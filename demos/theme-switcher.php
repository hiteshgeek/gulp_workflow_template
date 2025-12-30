<?php
/**
 * Theme Switcher Component Demo
 * Demonstrates theme switching functionality
 */

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/gulp_workflow_template');

$pageTitle = 'Theme Switcher';
$showBreadcrumb = true;

require_once BASE_PATH . '/includes/demo-header.php';
?>

<h1>Theme Switcher</h1>
<p class="subtitle">A modular, standalone theme toggle with Light, Dark, and System modes</p>

<div class="note">
    <strong>Try it!</strong> Click the button in the bottom-right corner to cycle through modes:
    <br><strong>Light</strong> (sun icon) &rarr; <strong>Dark</strong> (moon icon) &rarr; <strong>System</strong> (monitor icon) &rarr; Light...
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

<h3>2. Prevent flash of wrong theme (optional but recommended)</h3>
<div class="code-block"><code>&lt;!-- Add this in &lt;head&gt; before CSS --&gt;
&lt;script&gt;
(function() {
    try {
        var mode = JSON.parse(localStorage.getItem('theme-mode')) || 'system';
        var theme = mode;
        if (mode === 'system') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-theme', theme);
    } catch (e) {
        document.documentElement.setAttribute('data-theme', 'light');
    }
})();
&lt;/script&gt;</code></div>

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

<h2>JavaScript API</h2>
<div class="code-block"><code>// If using ESM
import { initTheme, toggleTheme, getSavedMode, MODES } from './theme-switcher.js';

// Initialize (auto-called on page load)
initTheme();

// Toggle through modes: light &rarr; dark &rarr; system &rarr; light
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

<h2>Accessibility</h2>
<ul>
    <li><strong>Keyboard:</strong> Button is focusable and activates with Enter/Space</li>
    <li><strong>ARIA:</strong> Dynamic aria-label describes current mode and next action</li>
    <li><strong>Focus:</strong> Visible focus outline on keyboard navigation</li>
    <li><strong>Motion:</strong> Respects <code>prefers-reduced-motion</code> for animations</li>
</ul>

<?php require_once BASE_PATH . '/includes/demo-footer.php'; ?>
