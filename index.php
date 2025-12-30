<?php
/**
 * Component Library - Index
 * Lists all available components
 */

define('BASE_PATH', __DIR__);
define('BASE_URL', '/gulp_workflow_template');

$pageTitle = 'Component Library';
$showBreadcrumb = false;

require_once __DIR__ . '/includes/demo-header.php';
?>

<h1>Component Library</h1>
<p class="subtitle">Modular, standalone UI components built with modern SCSS and JavaScript</p>

<div class="note">
    <strong>About this library:</strong> Each component is self-contained with its own CSS and JS files.
    Components can be used independently in any project - just include the files and follow the usage guide.
</div>

<h2>Available Components</h2>

<div class="component-grid">
    <a href="<?= BASE_URL ?>/demos/theme-switcher.php" class="component-card">
        <h3>Theme Switcher</h3>
        <p>A floating toggle button with Light, Dark, and System theme modes. Automatically follows OS preference.</p>
        <div class="card-footer">
            CSS + JS | Auto-init | Accessible
        </div>
    </a>

    <a href="<?= BASE_URL ?>/demos/tooltip.php" class="component-card">
        <h3>Tooltip</h3>
        <p>Lightweight tooltips with smart positioning, multiple triggers (hover, click, focus), and smooth animations.</p>
        <div class="card-footer">
            CSS + JS | Auto-init | Accessible
        </div>
    </a>
</div>

<hr>

<h2>Getting Started</h2>

<h3>1. Build the components</h3>
<div class="code-block"><code># Development build
gulp dev

# Production build (minified)
gulp prod

# Build specific component
gulp theme-switcher
gulp tooltip</code></div>

<h3>2. Include in your project</h3>
<div class="code-block"><code>&lt;!-- CSS in &lt;head&gt; --&gt;
&lt;link rel="stylesheet" href="dist/css/theme-switcher.css"&gt;
&lt;link rel="stylesheet" href="dist/css/tooltip.css"&gt;

&lt;!-- JS before &lt;/body&gt; --&gt;
&lt;script src="dist/js/theme-switcher.iife.js"&gt;&lt;/script&gt;
&lt;script src="dist/js/tooltip.iife.js"&gt;&lt;/script&gt;</code></div>

<h3>3. With PHP Asset Helper (versioned files)</h3>
<div class="code-block"><code>&lt;?php
require_once 'includes/Asset.php';
?&gt;

&lt;?= Asset::css('theme-switcher.css', 'lib') ?&gt;
&lt;?= Asset::css('tooltip.css', 'lib') ?&gt;

&lt;?= Asset::js('theme-switcher.iife.js', 'lib') ?&gt;
&lt;?= Asset::js('tooltip.iife.js', 'lib') ?&gt;</code></div>

<hr>

<h2>Project Structure</h2>
<div class="demo-box">
    <ul class="file-list">
        <li><code>src/library/common/</code> - Shared SCSS variables, mixins, and JS utilities</li>
        <li><code>src/library/theme-switcher/</code> - Theme Switcher component source</li>
        <li><code>src/library/tooltip/</code> - Tooltip component source</li>
        <li><code>dist/css/</code> - Compiled CSS files</li>
        <li><code>dist/js/</code> - Compiled JS files (ESM + IIFE)</li>
        <li><code>includes/Asset.php</code> - PHP helper for versioned assets</li>
    </ul>
</div>

<hr>

<h2>Features</h2>
<ul>
    <li><strong>Modular Architecture:</strong> Each component is self-contained</li>
    <li><strong>Modern SCSS:</strong> Uses @use/@forward, CSS custom properties, and shared design tokens</li>
    <li><strong>ES Modules + IIFE:</strong> Both module and browser-ready builds</li>
    <li><strong>Auto-initialization:</strong> Components work with data attributes out of the box</li>
    <li><strong>Accessible:</strong> ARIA attributes, keyboard navigation, focus management</li>
    <li><strong>Theme Support:</strong> Light/dark themes with CSS custom properties</li>
    <li><strong>Versioned Assets:</strong> Cache-busting with revision hashes</li>
</ul>

<?php require_once __DIR__ . '/includes/demo-footer.php'; ?>
