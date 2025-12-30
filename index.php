<?php

// Define base path and URL (adjust for your setup)
define('BASE_PATH', __DIR__);
define('BASE_URL', '/gulp_workflow_template');  // e.g., '/my-project' or '' for root

require_once __DIR__ . '/includes/Asset.php';

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Switcher Demo - Gulp Workflow Template</title>

    <!-- Theme Switcher CSS -->
    <?= Asset::css('theme-switcher.css', 'lib') ?>

    <!-- Demo page styles (inline) -->
    <style>
        /* Base styles using theme variables */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: var(--theme-bg);
            color: var(--theme-text);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        h2 {
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
            color: var(--theme-primary);
        }

        h3 {
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem;
        }

        p {
            margin-bottom: 1rem;
        }

        .subtitle {
            opacity: 0.7;
            margin-bottom: 2rem;
        }

        .demo-box {
            background: rgba(128, 128, 128, 0.1);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
        }

        .code-block {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            padding: 1rem;
            overflow-x: auto;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
            margin: 0.5rem 0 1rem;
        }

        [data-theme='dark'] .code-block {
            background: rgba(255, 255, 255, 0.1);
        }

        .code-block code {
            white-space: pre;
        }

        .highlight {
            color: var(--theme-primary);
        }

        .file-list {
            list-style: none;
            margin: 1rem 0;
        }

        .file-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(128, 128, 128, 0.2);
        }

        .file-list li:last-child {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .badge--css {
            background: #264de4;
            color: white;
        }

        .badge--js {
            background: #f7df1e;
            color: black;
        }

        .note {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid var(--theme-primary);
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 6px 6px 0;
        }

        hr {
            border: none;
            border-top: 1px solid rgba(128, 128, 128, 0.2);
            margin: 2rem 0;
        }

        a {
            color: var(--theme-primary);
        }

        footer {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(128, 128, 128, 0.2);
            opacity: 0.7;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Theme Switcher</h1>
        <p class="subtitle">A modular, standalone theme toggle with Light, Dark, and System modes</p>

        <div class="note">
            <strong>Try it!</strong> Click the button in the bottom-right corner to cycle through modes:
            <br><strong>Light</strong> (sun icon) → <strong>Dark</strong> (moon icon) → <strong>System</strong> (monitor icon) → Light...
            <br>In System mode, the theme automatically follows your OS preference.
        </div>

        <h2>Features</h2>
        <ul>
            <li><strong>Three modes:</strong> Light, Dark, and System (follows OS preference)</li>
            <li>Automatically responds to system theme changes in real-time</li>
            <li>Saves user choice to localStorage</li>
            <li>Smooth transitions between themes</li>
            <li>Accessible (keyboard navigable, focus states, aria-labels)</li>
            <li>Standalone - works with any project</li>
            <li>Modular SCSS and JS architecture</li>
        </ul>

        <hr>

        <h2>Quick Start</h2>

        <h3>1. Include the files</h3>
        <div class="code-block"><code>&lt;!-- CSS in &lt;head&gt; --&gt;
&lt;link rel="stylesheet" href="theme-switcher.css"&gt;

&lt;!-- JS before &lt;/body&gt; --&gt;
&lt;script src="theme-switcher.iife.js"&gt;&lt;/script&gt;</code></div>

        <h3>2. Add data-theme to html</h3>
        <div class="code-block"><code>&lt;html lang="en" data-theme="light"&gt;</code></div>

        <h3>3. Use CSS variables in your styles</h3>
        <div class="code-block"><code>body {
    background-color: var(--theme-bg);
    color: var(--theme-text);
}

a {
    color: var(--theme-primary);
}

a:hover {
    color: var(--theme-primary-hover);
}</code></div>

        <hr>

        <h2>Available CSS Variables</h2>
        <div class="demo-box">
            <ul class="file-list">
                <li><code>--theme-bg</code> - Background color (light: #ffffff, dark: #1a1a1a)</li>
                <li><code>--theme-text</code> - Text color (light: #1a1a1a, dark: #f5f5f5)</li>
                <li><code>--theme-primary</code> - Primary accent color (#3b82f6)</li>
                <li><code>--theme-primary-hover</code> - Primary hover state (#2563eb)</li>
            </ul>
        </div>

        <hr>

        <h2>Output Files</h2>
        <div class="demo-box">
            <ul class="file-list">
                <li>
                    <code><?= Asset::getFile('theme-switcher.css', 'lib') ?></code>
                    <span class="badge badge--css">CSS</span>
                </li>
                <li>
                    <code><?= Asset::getFile('theme-switcher.js', 'lib') ?></code>
                    <span class="badge badge--js">ESM</span>
                </li>
                <li>
                    <code><?= Asset::getFile('theme-switcher.iife.js', 'lib') ?></code>
                    <span class="badge badge--js">IIFE</span>
                </li>
            </ul>
        </div>

        <hr>

        <h2>Using with PHP Asset Helper</h2>
        <div class="code-block"><code>&lt;?= Asset::css('theme-switcher.css', 'lib') ?&gt;
&lt;?= Asset::js('theme-switcher.iife.js', 'lib') ?&gt;

// Or with ES modules
&lt;?= Asset::js('theme-switcher.js', 'lib', ['type' => 'module']) ?&gt;</code></div>

        <hr>

        <h2>Build Commands</h2>
        <div class="demo-box">
            <ul class="file-list">
                <li><code>gulp theme-switcher</code> - Build CSS and JS</li>
                <li><code>gulp theme-switcher:styles</code> - Build CSS only</li>
                <li><code>gulp theme-switcher:scripts</code> - Build JS only</li>
                <li><code>gulp theme-switcher --production</code> - Minified build</li>
            </ul>
        </div>

        <hr>

        <h2>Customization</h2>
        <p>Override SCSS variables before importing to customize colors:</p>
        <div class="code-block"><code>// In your SCSS file
@use 'common/scss' with (
    $color-light-bg: #f8f9fa,
    $color-dark-bg: #212529,
    $color-primary: #6366f1
);

@use 'theme-switcher/scss/main';</code></div>

        <hr>

        <h2>JavaScript API</h2>
        <div class="code-block"><code>// If using ESM
import { initTheme, toggleTheme, getSavedMode, MODES } from './theme-switcher.js';

// Initialize (auto-called on page load)
initTheme();

// Toggle through modes: light → dark → system → light
toggleTheme();

// Get current mode
const mode = getSavedMode(); // 'light', 'dark', or 'system'

// Available modes
MODES.LIGHT   // 'light'
MODES.DARK    // 'dark'
MODES.SYSTEM  // 'system'

// If using IIFE, access via global
ThemeSwitcher.toggleTheme();
ThemeSwitcher.getSavedMode();</code></div>

        <footer>
            <p>Built with the Gulp Workflow Template</p>
        </footer>
    </div>

    <!-- Theme Switcher JS (IIFE for browser) -->
    <?= Asset::js('theme-switcher.iife.js', 'lib') ?>

</body>
</html>
