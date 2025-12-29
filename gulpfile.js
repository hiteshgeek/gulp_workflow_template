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
  libCssDir: "src/library/scss",
  libJsDir: "src/library/js",
  nodeDir: "node_modules",

  // Output directories - Library (dist/)
  libCssOutDir: "dist/css",
  libJsOutDir: "dist/js",
  libManifestPath: "dist/rev/manifest.json",

  // Output directories - Assets (assets/)
  assetsCssOutDir: "assets/css",
  assetsJsOutDir: "assets/js",
  assetsManifestPath: "assets/rev/manifest.json",
  imagesOutDir: "assets/images",
};

// Utility: Remove old hashed files not in manifest
function cleanupOldFiles(dir, manifestPath, ext) {
  return async function cleanupTask(done) {
    try {
      if (!fs.existsSync(manifestPath)) {
        done && done();
        return;
      }
      const manifest = JSON.parse(fs.readFileSync(manifestPath, "utf8"));
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
      console.error("[Cleanup] Error:", e);
      done && done(e);
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
    .pipe(sass())
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
    .pipe(sass())
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
  // Watch and rebuild
  gulp.watch(
    config.libCssDir + "/**/*.scss",
    gulp.series("lib:styles", reload)
  );
  gulp.watch(config.libJsDir + "/**/*.js", gulp.series("lib:scripts", reload));
  gulp.watch(
    config.assetsCssDir + "/**/*.scss",
    gulp.series("assets:styles", reload)
  );
  gulp.watch(
    config.assetsJsDir + "/**/*.js",
    gulp.series("assets:scripts", reload)
  );
  // Watch PHP/HTML files for reload
  gulp.watch("**/*.php").on("change", browserSync.reload);
  gulp.watch("**/*.html").on("change", browserSync.reload);
});

// =============================================================================
// MAIN TASKS
// =============================================================================

gulp.task("dev", gulp.series("clean", gulp.parallel("lib", "assets")));
gulp.task("dev-with-watch", gulp.series("dev", "watch"));
gulp.task("dev-serve", gulp.series("dev", "serve", "watch")); // Dev with BrowserSync
gulp.task(
  "prod",
  gulp.series(setProdEnv, "clean", gulp.parallel("lib", "assets"))
);
gulp.task(
  "dev-noversion",
  gulp.series(setNoVersionMode, "clean", gulp.parallel("lib", "assets"))
);

gulp.task("default", gulp.series("dev"));
