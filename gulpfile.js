// Modern Gulpfile with async/await, modular tasks, and improved config
const gulp = require("gulp");
const fs = require("fs");
const { rimraf } = require("rimraf");
const gulpLoadPlugins = require("gulp-load-plugins");
const plugins = gulpLoadPlugins();
const noop = require("gulp-noop");
const uglify = require("gulp-uglify-es").default;
const rollupStream = require("@rollup/stream");
const rollupBabel = require("@rollup/plugin-babel").default;
const rollupResolve = require("@rollup/plugin-node-resolve").default;
const rollupCommonjs = require("@rollup/plugin-commonjs");
const rollupReplace = require("@rollup/plugin-replace");
const source = require("vinyl-source-stream");
const buffer = require("vinyl-buffer");
const sass = require("gulp-sass")(require("sass"));
const javascriptObfuscator = require("gulp-javascript-obfuscator");
const path = require("path");
const rev = require("gulp-rev");
const autoprefixer = require("autoprefixer");
const postcss = require("gulp-postcss");
const browserSync = require("browser-sync").create();

// Configurable options (adjusted for your structure)
const config = {
  // Library name (used for output filenames and IIFE global)
  libName: "LibraryName",
  libFileName: "library-name", // kebab-case for filenames

  // Source directories
  assetsCssDir: "src/assets/scss",
  assetsJsDir: "src/assets/js",
  assetsImagesDir: "src/assets/images",
  libDir: "src/library",
  libCssDir: "src/library/scss",
  libJsDir: "src/library/js",
  nodeDir: "node_modules",

  // Sass include paths (for clean @use imports)
  sassIncludePaths: [path.resolve(__dirname, "src/library")],

  // Output directories - Library (dist/)
  libCssOutDir: "dist/css",
  libJsOutDir: "dist/js",
  libManifestPath: "dist/rev/manifest.json",

  // Output directories - Assets (assets/)
  assetsCssOutDir: "assets/css",
  assetsJsOutDir: "assets/js",
  assetsManifestPath: "assets/rev/manifest.json",
  imagesOutDir: "assets/images",

  // Theme Switcher component
  themeSwitcherName: "ThemeSwitcher",
  themeSwitcherFileName: "theme-switcher",
  themeSwitcherCssDir: "src/library/theme-switcher/scss",
  themeSwitcherJsDir: "src/library/theme-switcher/js",

  // Tooltip component
  tooltipName: "Tooltip",
  tooltipFileName: "tooltip",
  tooltipCssDir: "src/library/tooltip/scss",
  tooltipJsDir: "src/library/tooltip/js",
};

// Utility: Remove old hashed files not in manifest
function cleanupOldFiles(dir, manifestPath, ext) {
  return async function cleanupTask(done) {
    try {
      if (!fs.existsSync(manifestPath)) {
        done && done();
        return;
      }

      // Read manifest with error handling for corrupt JSON
      let manifest;
      try {
        const content = fs.readFileSync(manifestPath, "utf8");
        manifest = JSON.parse(content);
      } catch (parseError) {
        // Manifest is corrupt, skip cleanup but don't fail the build
        console.warn("[Cleanup] Warning: Could not parse manifest, skipping cleanup");
        done && done();
        return;
      }

      const keepFiles = new Set(Object.values(manifest));
      if (!fs.existsSync(dir)) {
        done && done();
        return;
      }
      const files = fs.readdirSync(dir);
      for (const file of files) {
        if (file.endsWith(ext) && !keepFiles.has(file)) {
          fs.unlinkSync(path.join(dir, file));
          const mapFile = file + ".map";
          if (fs.existsSync(path.join(dir, mapFile))) {
            fs.unlinkSync(path.join(dir, mapFile));
          }
        }
      }
      done && done();
    } catch (e) {
      // Don't fail the build for cleanup errors
      console.warn("[Cleanup] Warning:", e.message);
      done && done();
    }
  };
}

// Detect production mode at runtime via NODE_ENV or --production
function isProduction() {
  return (
    process.env.NODE_ENV === "production" ||
    process.argv.includes("--production")
  );
}
function useSourceMaps() {
  return !isProduction();
}

let noVersionMode = false;
function useVersioning() {
  return !noVersionMode;
}

function setProdEnv(done) {
  process.env.NODE_ENV = "production";
  done && done();
}

function setNoVersionMode(done) {
  noVersionMode = true;
  done && done();
}

function onError(err) {
  console.error("[Error]", err.toString());
  if (this && typeof this.emit === "function") this.emit("end");
}

// External dependencies (loaded via CDN, not bundled)
const externalDeps = ["echarts"];
const externalGlobals = { echarts: "echarts" };

// =============================================================================
// LIBRARY STYLES (src/library/scss -> dist/css)
// =============================================================================

function libStyles() {
  return gulp
    .src(config.libCssDir + "/main.scss")
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(useSourceMaps() ? plugins.sourcemaps.init() : noop())
    .pipe(sass({ includePaths: config.sassIncludePaths }))
    .pipe(postcss([autoprefixer()]))
    .pipe(plugins.concat(config.libFileName + ".css"))
    .pipe(isProduction() ? plugins.cleanCss() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libCssOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

// =============================================================================
// LIBRARY SCRIPTS (src/library/js -> dist/js)
// =============================================================================

function libScriptsESM() {
  return rollupStream({
    input: config.libJsDir + "/index.js",
    external: externalDeps,
    plugins: [
      rollupReplace({
        preventAssignment: true,
        "process.env.NODE_ENV": JSON.stringify(
          isProduction() ? "production" : "development"
        ),
      }),
      rollupResolve({ browser: true }),
      rollupCommonjs(),
      rollupBabel({
        babelHelpers: "bundled",
        babelrc: false,
        exclude: "node_modules/**",
      }),
    ],
    output: { format: "esm", inlineDynamicImports: true },
  })
    .pipe(source(config.libFileName + ".js"))
    .pipe(buffer())
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(
      useSourceMaps() ? plugins.sourcemaps.init({ loadMaps: true }) : noop()
    )
    .pipe(isProduction() ? uglify() : noop())
    .pipe(isProduction() ? javascriptObfuscator() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libJsOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

function libScriptsIIFE() {
  return rollupStream({
    input: config.libJsDir + "/index.js",
    external: externalDeps,
    plugins: [
      rollupReplace({
        preventAssignment: true,
        "process.env.NODE_ENV": JSON.stringify(
          isProduction() ? "production" : "development"
        ),
      }),
      rollupResolve({ browser: true }),
      rollupCommonjs(),
      rollupBabel({
        babelHelpers: "bundled",
        babelrc: false,
        exclude: "node_modules/**",
      }),
    ],
    output: {
      format: "iife",
      name: config.libName,
      globals: externalGlobals,
      exports: "named",
      inlineDynamicImports: true,
    },
  })
    .pipe(source(config.libFileName + ".iife.js"))
    .pipe(buffer())
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(
      useSourceMaps() ? plugins.sourcemaps.init({ loadMaps: true }) : noop()
    )
    .pipe(isProduction() ? uglify() : noop())
    .pipe(isProduction() ? javascriptObfuscator() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libJsOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

// =============================================================================
// ASSETS STYLES (src/assets/scss -> assets/css)
// =============================================================================

function assetsStyles() {
  return gulp
    .src(config.assetsCssDir + "/main.scss")
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(useSourceMaps() ? plugins.sourcemaps.init() : noop())
    .pipe(sass({ includePaths: config.sassIncludePaths }))
    .pipe(postcss([autoprefixer()]))
    .pipe(plugins.concat("main.css"))
    .pipe(isProduction() ? plugins.cleanCss() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.assetsCssOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.assetsManifestPath, {
            base: "assets/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("assets/rev") : noop());
}

// =============================================================================
// ASSETS SCRIPTS (src/assets/js -> assets/js)
// =============================================================================

function assetsScriptsESM() {
  return rollupStream({
    input: config.assetsJsDir + "/index.js",
    external: externalDeps,
    plugins: [
      rollupReplace({
        preventAssignment: true,
        "process.env.NODE_ENV": JSON.stringify(
          isProduction() ? "production" : "development"
        ),
      }),
      rollupResolve({ browser: true }),
      rollupCommonjs(),
      rollupBabel({
        babelHelpers: "bundled",
        babelrc: false,
        exclude: "node_modules/**",
      }),
    ],
    output: { format: "esm", inlineDynamicImports: true },
  })
    .pipe(source("main.js"))
    .pipe(buffer())
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(
      useSourceMaps() ? plugins.sourcemaps.init({ loadMaps: true }) : noop()
    )
    .pipe(isProduction() ? uglify() : noop())
    .pipe(isProduction() ? javascriptObfuscator() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.assetsJsOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.assetsManifestPath, {
            base: "assets/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("assets/rev") : noop());
}

function assetsScriptsIIFE() {
  return rollupStream({
    input: config.assetsJsDir + "/index.js",
    external: externalDeps,
    plugins: [
      rollupReplace({
        preventAssignment: true,
        "process.env.NODE_ENV": JSON.stringify(
          isProduction() ? "production" : "development"
        ),
      }),
      rollupResolve({ browser: true }),
      rollupCommonjs(),
      rollupBabel({
        babelHelpers: "bundled",
        babelrc: false,
        exclude: "node_modules/**",
      }),
    ],
    output: {
      format: "iife",
      name: "App",
      globals: externalGlobals,
      exports: "named",
      inlineDynamicImports: true,
    },
  })
    .pipe(source("main.iife.js"))
    .pipe(buffer())
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(
      useSourceMaps() ? plugins.sourcemaps.init({ loadMaps: true }) : noop()
    )
    .pipe(isProduction() ? uglify() : noop())
    .pipe(isProduction() ? javascriptObfuscator() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.assetsJsOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.assetsManifestPath, {
            base: "assets/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("assets/rev") : noop());
}

// =============================================================================
// THEME SWITCHER (src/library/theme-switcher -> dist/)
// =============================================================================

function themeSwitcherStyles() {
  return gulp
    .src(config.themeSwitcherCssDir + "/main.scss")
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(useSourceMaps() ? plugins.sourcemaps.init() : noop())
    .pipe(sass({ includePaths: config.sassIncludePaths }))
    .pipe(postcss([autoprefixer()]))
    .pipe(plugins.concat(config.themeSwitcherFileName + ".css"))
    .pipe(isProduction() ? plugins.cleanCss() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libCssOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

function themeSwitcherScriptsESM() {
  return rollupStream({
    input: config.themeSwitcherJsDir + "/index.js",
    plugins: [
      rollupReplace({
        preventAssignment: true,
        "process.env.NODE_ENV": JSON.stringify(
          isProduction() ? "production" : "development"
        ),
      }),
      rollupResolve({ browser: true }),
      rollupCommonjs(),
      rollupBabel({
        babelHelpers: "bundled",
        babelrc: false,
        exclude: "node_modules/**",
      }),
    ],
    output: { format: "esm", inlineDynamicImports: true },
  })
    .pipe(source(config.themeSwitcherFileName + ".js"))
    .pipe(buffer())
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(
      useSourceMaps() ? plugins.sourcemaps.init({ loadMaps: true }) : noop()
    )
    .pipe(isProduction() ? uglify() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libJsOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

function themeSwitcherScriptsIIFE() {
  return rollupStream({
    input: config.themeSwitcherJsDir + "/index.js",
    plugins: [
      rollupReplace({
        preventAssignment: true,
        "process.env.NODE_ENV": JSON.stringify(
          isProduction() ? "production" : "development"
        ),
      }),
      rollupResolve({ browser: true }),
      rollupCommonjs(),
      rollupBabel({
        babelHelpers: "bundled",
        babelrc: false,
        exclude: "node_modules/**",
      }),
    ],
    output: {
      format: "iife",
      name: config.themeSwitcherName,
      exports: "named",
      inlineDynamicImports: true,
    },
  })
    .pipe(source(config.themeSwitcherFileName + ".iife.js"))
    .pipe(buffer())
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(
      useSourceMaps() ? plugins.sourcemaps.init({ loadMaps: true }) : noop()
    )
    .pipe(isProduction() ? uglify() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libJsOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

// =============================================================================
// TOOLTIP (src/library/tooltip -> dist/)
// =============================================================================

function tooltipStyles() {
  return gulp
    .src(config.tooltipCssDir + "/main.scss")
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(useSourceMaps() ? plugins.sourcemaps.init() : noop())
    .pipe(sass({ includePaths: config.sassIncludePaths }))
    .pipe(postcss([autoprefixer()]))
    .pipe(plugins.concat(config.tooltipFileName + ".css"))
    .pipe(isProduction() ? plugins.cleanCss() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libCssOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

function tooltipScriptsESM() {
  return rollupStream({
    input: config.tooltipJsDir + "/index.js",
    plugins: [
      rollupReplace({
        preventAssignment: true,
        "process.env.NODE_ENV": JSON.stringify(
          isProduction() ? "production" : "development"
        ),
      }),
      rollupResolve({ browser: true }),
      rollupCommonjs(),
      rollupBabel({
        babelHelpers: "bundled",
        babelrc: false,
        exclude: "node_modules/**",
      }),
    ],
    output: { format: "esm", inlineDynamicImports: true },
  })
    .pipe(source(config.tooltipFileName + ".js"))
    .pipe(buffer())
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(
      useSourceMaps() ? plugins.sourcemaps.init({ loadMaps: true }) : noop()
    )
    .pipe(isProduction() ? uglify() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libJsOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

function tooltipScriptsIIFE() {
  return rollupStream({
    input: config.tooltipJsDir + "/index.js",
    plugins: [
      rollupReplace({
        preventAssignment: true,
        "process.env.NODE_ENV": JSON.stringify(
          isProduction() ? "production" : "development"
        ),
      }),
      rollupResolve({ browser: true }),
      rollupCommonjs(),
      rollupBabel({
        babelHelpers: "bundled",
        babelrc: false,
        exclude: "node_modules/**",
      }),
    ],
    output: {
      format: "iife",
      name: config.tooltipName,
      exports: "named",
      inlineDynamicImports: true,
    },
  })
    .pipe(source(config.tooltipFileName + ".iife.js"))
    .pipe(buffer())
    .pipe(plugins.plumber({ errorHandler: onError }))
    .pipe(
      useSourceMaps() ? plugins.sourcemaps.init({ loadMaps: true }) : noop()
    )
    .pipe(isProduction() ? uglify() : noop())
    .pipe(useVersioning() ? rev() : noop())
    .pipe(useSourceMaps() ? plugins.sourcemaps.write(".") : noop())
    .pipe(gulp.dest(config.libJsOutDir))
    .pipe(
      useVersioning()
        ? rev.manifest(config.libManifestPath, {
            base: "dist/rev",
            merge: true,
          })
        : noop()
    )
    .pipe(useVersioning() ? gulp.dest("dist/rev") : noop());
}

// =============================================================================
// CLEAN TASKS
// =============================================================================

gulp.task("clean-lib", async function () {
  await rimraf("dist/**", { glob: true });
});

gulp.task("clean-assets", async function () {
  await rimraf("assets/{css,js,rev}/**", { glob: true });
});

gulp.task("clean", gulp.parallel("clean-lib", "clean-assets"));

// =============================================================================
// CLEANUP OLD VERSIONED FILES
// =============================================================================

gulp.task(
  "clean-old-lib-css",
  cleanupOldFiles(config.libCssOutDir, config.libManifestPath, ".css")
);
gulp.task(
  "clean-old-lib-js",
  cleanupOldFiles(config.libJsOutDir, config.libManifestPath, ".js")
);
gulp.task(
  "clean-old-assets-css",
  cleanupOldFiles(config.assetsCssOutDir, config.assetsManifestPath, ".css")
);
gulp.task(
  "clean-old-assets-js",
  cleanupOldFiles(config.assetsJsOutDir, config.assetsManifestPath, ".js")
);

// =============================================================================
// IMAGES
// =============================================================================

gulp.task("images", function () {
  return gulp
    .src(config.assetsImagesDir + "/**/*")
    .pipe(gulp.dest(config.imagesOutDir));
});

// =============================================================================
// COMPOSITE TASKS
// =============================================================================

// Library tasks
gulp.task("lib:styles", gulp.series(libStyles, "clean-old-lib-css"));
// Note: ESM and IIFE must run sequentially to avoid manifest merge race condition
gulp.task(
  "lib:scripts",
  gulp.series(libScriptsESM, libScriptsIIFE, "clean-old-lib-js")
);
gulp.task("lib", gulp.parallel("lib:styles", "lib:scripts"));

// Assets tasks
gulp.task("assets:styles", gulp.series(assetsStyles, "clean-old-assets-css"));
// Note: ESM and IIFE must run sequentially to avoid manifest merge race condition
gulp.task(
  "assets:scripts",
  gulp.series(assetsScriptsESM, assetsScriptsIIFE, "clean-old-assets-js")
);
gulp.task("assets", gulp.parallel("assets:styles", "assets:scripts", "images"));

// Theme Switcher tasks
gulp.task("theme-switcher:styles", gulp.series(themeSwitcherStyles, "clean-old-lib-css"));
gulp.task(
  "theme-switcher:scripts",
  gulp.series(themeSwitcherScriptsESM, themeSwitcherScriptsIIFE, "clean-old-lib-js")
);
// Note: styles and scripts run sequentially to avoid manifest race conditions
gulp.task("theme-switcher", gulp.series("theme-switcher:styles", "theme-switcher:scripts"));

// Tooltip tasks
gulp.task("tooltip:styles", gulp.series(tooltipStyles, "clean-old-lib-css"));
gulp.task(
  "tooltip:scripts",
  gulp.series(tooltipScriptsESM, tooltipScriptsIIFE, "clean-old-lib-js")
);
// Note: styles and scripts run sequentially to avoid manifest race conditions
gulp.task("tooltip", gulp.series("tooltip:styles", "tooltip:scripts"));

// =============================================================================
// BROWSERSYNC
// =============================================================================

gulp.task("serve", function (done) {
  browserSync.init({
    proxy: "localhost/gulp_workflow_template", // Change this to your local dev URL
    notify: false,
    open: false, // Set to true to auto-open browser
  });
  done();
});

function reload(done) {
  browserSync.reload();
  done();
}

// =============================================================================
// WATCH TASK
// =============================================================================

gulp.task("watch", function () {
  // Watch and rebuild - Library
  gulp.watch(
    config.libCssDir + "/**/*.scss",
    gulp.series("lib:styles", reload)
  );
  gulp.watch(config.libJsDir + "/**/*.js", gulp.series("lib:scripts", reload));

  // Watch and rebuild - Assets
  gulp.watch(
    config.assetsCssDir + "/**/*.scss",
    gulp.series("assets:styles", reload)
  );
  gulp.watch(
    config.assetsJsDir + "/**/*.js",
    gulp.series("assets:scripts", reload)
  );

  // Watch and rebuild - Theme Switcher (including common folder)
  gulp.watch(
    [config.themeSwitcherCssDir + "/**/*.scss", "src/library/common/scss/**/*.scss"],
    gulp.series("theme-switcher:styles", reload)
  );
  gulp.watch(
    [config.themeSwitcherJsDir + "/**/*.js", "src/library/common/js/**/*.js"],
    gulp.series("theme-switcher:scripts", reload)
  );

  // Watch and rebuild - Tooltip (including common folder)
  gulp.watch(
    [config.tooltipCssDir + "/**/*.scss", "src/library/common/scss/**/*.scss"],
    gulp.series("tooltip:styles", reload)
  );
  gulp.watch(
    [config.tooltipJsDir + "/**/*.js", "src/library/common/js/**/*.js"],
    gulp.series("tooltip:scripts", reload)
  );

  // Watch PHP/HTML files for reload
  gulp.watch("**/*.php").on("change", browserSync.reload);
  gulp.watch("**/*.html").on("change", browserSync.reload);
});

// =============================================================================
// MAIN TASKS
// =============================================================================

// Note: Library components (theme-switcher, tooltip) run sequentially to avoid manifest race conditions
// Assets can run in parallel since they use a separate manifest
gulp.task("dev", gulp.series("clean", gulp.parallel("assets", gulp.series("theme-switcher", "tooltip"))));
gulp.task("dev-with-watch", gulp.series("dev", "watch"));
gulp.task("dev-serve", gulp.series("dev", "serve", "watch")); // Dev with BrowserSync
gulp.task(
  "prod",
  gulp.series(setProdEnv, "clean", gulp.parallel("assets", gulp.series("theme-switcher", "tooltip")))
);
gulp.task(
  "dev-noversion",
  gulp.series(setNoVersionMode, "clean", gulp.parallel("assets", gulp.series("theme-switcher", "tooltip")))
);

gulp.task("default", gulp.series("dev"));
