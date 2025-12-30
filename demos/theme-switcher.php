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

<hr>

<h2>Folder Structure</h2>
<p>The theme switcher component follows a modular architecture. Here's how the source files are organized:</p>

<div class="code-block"><code>src/library/theme-switcher/
├── js/
│   ├── core/
│   │   └── theme.js        # Core theme logic
│   └── index.js            # Entry point + button UI
└── scss/
    ├── base/
    │   └── _themes.scss    # CSS custom properties
    ├── components/
    │   └── _button.scss    # Toggle button styles
    └── main.scss           # Entry point

src/library/common/           # Shared utilities (bundled automatically)
├── js/
│   └── storage.js          # localStorage helpers
└── scss/
    ├── _index.scss         # Forwards all modules
    ├── _variables.scss     # Design tokens
    └── _mixins.scss        # Reusable mixins</code></div>

<hr>

<h2>Source Files Explained</h2>

<h3>JavaScript Files</h3>

<div class="demo-box">
    <h4><code>js/index.js</code> - Entry Point + Button UI</h4>
    <p>Creates the floating toggle button and handles UI interactions.</p>
    <ul class="file-list">
        <li><strong>SVG Icons:</strong> Embeds sun, moon, and monitor (system) icons as inline SVG</li>
        <li><strong>createButton():</strong> Creates the button element with all three icons</li>
        <li><strong>updateButtonIcon():</strong> Updates <code>data-mode</code> attribute and aria-label based on current mode</li>
        <li><strong>init(options):</strong> Initializes theme, creates button, attaches click handler</li>
        <li><strong>Auto-initialization:</strong> Automatically runs init() on DOMContentLoaded</li>
        <li><strong>Exports:</strong> Re-exports initTheme, toggleTheme, getSavedMode, MODES from core</li>
    </ul>
</div>

<div class="demo-box">
    <h4><code>js/core/theme.js</code> - Core Theme Logic</h4>
    <p>Handles all theme switching logic, storage, and system preference detection.</p>
    <ul class="file-list">
        <li><strong>STORAGE_KEY:</strong> <code>'theme-mode'</code> - localStorage key for persistence</li>
        <li><strong>MODES:</strong> <code>{ LIGHT: 'light', DARK: 'dark', SYSTEM: 'system' }</code></li>
        <li><strong>getSystemTheme():</strong> Detects OS preference using <code>matchMedia('prefers-color-scheme: dark')</code></li>
        <li><strong>getSavedMode():</strong> Retrieves saved mode from localStorage (defaults to 'system')</li>
        <li><strong>getCurrentTheme():</strong> Returns actual applied theme from <code>data-theme</code> attribute</li>
        <li><strong>setMode(mode):</strong> Applies theme, saves to storage, sets up system listener if needed</li>
        <li><strong>toggleTheme():</strong> Cycles through modes: light &rarr; dark &rarr; system &rarr; light</li>
        <li><strong>initTheme():</strong> Reads saved mode and applies it (call on page load)</li>
        <li><strong>onSystemThemeChange(callback):</strong> Listens for OS theme changes, returns cleanup function</li>
    </ul>
</div>

<div class="demo-box">
    <h4><code>common/js/storage.js</code> - localStorage Helpers</h4>
    <p>Safe localStorage utilities with error handling. <strong>Bundled into output automatically.</strong></p>
    <ul class="file-list">
        <li><strong>getItem(key, defaultValue):</strong> Gets and JSON.parses value, returns default on error</li>
        <li><strong>setItem(key, value):</strong> JSON.stringifies and stores value, returns success boolean</li>
        <li><strong>removeItem(key):</strong> Removes item from storage (silent fail on error)</li>
    </ul>
    <p><em>Note: This is bundled into the dist file - you don't need to include it separately.</em></p>
</div>

<h3>SCSS Files</h3>

<div class="demo-box">
    <h4><code>scss/main.scss</code> - Entry Point</h4>
    <p>Simple entry point that imports theme definitions and button styles.</p>
    <div class="code-block"><code>@use 'base/themes';
@use 'components/button';</code></div>
</div>

<div class="demo-box">
    <h4><code>scss/base/_themes.scss</code> - CSS Custom Properties</h4>
    <p>Defines theme colors as CSS variables based on <code>data-theme</code> attribute.</p>
    <ul class="file-list">
        <li><strong>[data-theme='light']:</strong>
            <ul>
                <li><code>--theme-bg: #ffffff</code> - Light background</li>
                <li><code>--theme-text: #1a1a1a</code> - Dark text</li>
                <li><code>--theme-primary: #3b82f6</code> - Blue accent</li>
                <li><code>--theme-primary-hover: #2563eb</code> - Darker blue on hover</li>
            </ul>
        </li>
        <li><strong>[data-theme='dark']:</strong>
            <ul>
                <li><code>--theme-bg: #1a1a1a</code> - Dark background</li>
                <li><code>--theme-text: #f5f5f5</code> - Light text</li>
                <li><code>--theme-primary: #3b82f6</code> - Same blue accent</li>
                <li><code>--theme-primary-hover: #2563eb</code> - Same hover state</li>
            </ul>
        </li>
    </ul>
    <p><em>Uses variables from common/scss/_variables.scss (configurable with !default)</em></p>
</div>

<div class="demo-box">
    <h4><code>scss/components/_button.scss</code> - Toggle Button Styles</h4>
    <p>Styles for the floating theme toggle button.</p>
    <ul class="file-list">
        <li><strong>.theme-switcher:</strong> Fixed position button (bottom-right corner)
            <ul>
                <li>44px circular button</li>
                <li>Inverted colors (uses --theme-text as background, --theme-bg as color)</li>
                <li>Box shadow for elevation</li>
                <li>Scale transform on hover/active</li>
            </ul>
        </li>
        <li><strong>.theme-switcher__icon:</strong> 24px SVG icons, hidden by default</li>
        <li><strong>[data-mode='light'] .theme-switcher__icon--sun:</strong> Shows sun when mode is light</li>
        <li><strong>[data-mode='dark'] .theme-switcher__icon--moon:</strong> Shows moon when mode is dark</li>
        <li><strong>[data-mode='system'] .theme-switcher__icon--system:</strong> Shows monitor when mode is system</li>
    </ul>
    <p><em>Uses mixins from common/scss/_mixins.scss (button-reset, flex-center, size, transition)</em></p>
</div>

<div class="demo-box">
    <h4><code>common/scss/_variables.scss</code> - Design Tokens</h4>
    <p>Shared variables used across components. All use <code>!default</code> so you can override them.</p>
    <ul class="file-list">
        <li><strong>Colors:</strong> $color-light-bg, $color-light-text, $color-dark-bg, $color-dark-text, $color-primary, $color-primary-hover</li>
        <li><strong>Transitions:</strong> $transition-duration (0.3s), $transition-easing (ease-in-out)</li>
        <li><strong>Spacing:</strong> $spacing-xs (4px) through $spacing-xl (32px)</li>
        <li><strong>Border Radius:</strong> $border-radius-sm (4px) through $border-radius-full (50%)</li>
        <li><strong>Z-Index:</strong> $z-index-dropdown (100) through $z-index-tooltip (500)</li>
        <li><strong>Icons:</strong> $icon-size (24px)</li>
    </ul>
</div>

<div class="demo-box">
    <h4><code>common/scss/_mixins.scss</code> - Reusable Mixins</h4>
    <p>SCSS mixins used by component styles.</p>
    <ul class="file-list">
        <li><strong>transition($properties...):</strong> Generates transition with configured duration/easing</li>
        <li><strong>flex-center:</strong> <code>display: flex; align-items: center; justify-content: center;</code></li>
        <li><strong>flex-between:</strong> Same but with <code>justify-content: space-between;</code></li>
        <li><strong>button-reset:</strong> Removes default button styles, adds focus-visible outline</li>
        <li><strong>visually-hidden:</strong> Hides element visually but keeps it accessible</li>
        <li><strong>size($width, $height):</strong> Sets width and height (height defaults to width)</li>
    </ul>
</div>

<hr>

<h2>Output Files (dist/)</h2>
<p>After building, these files are generated in the <code>dist/</code> folder:</p>

<div class="demo-box">
    <ul class="file-list">
        <li>
            <code>dist/css/theme-switcher-[hash].css</code>
            <span class="badge badge--css">CSS</span>
            <br><small>Compiled CSS with theme variables and button styles. Include in &lt;head&gt;.</small>
        </li>
        <li>
            <code>dist/js/theme-switcher-[hash].js</code>
            <span class="badge badge--js">ESM</span>
            <br><small>ES Module format. Use with <code>import { toggleTheme } from './theme-switcher.js'</code></small>
        </li>
        <li>
            <code>dist/js/theme-switcher-[hash].iife.js</code>
            <span class="badge badge--js">IIFE</span>
            <br><small>Browser bundle. Creates global <code>window.ThemeSwitcher</code>. <strong>Includes storage.js bundled inside.</strong></small>
        </li>
        <li>
            <code>dist/css/theme-switcher-[hash].css.map</code>
            <br><small>Source map for CSS debugging.</small>
        </li>
        <li>
            <code>dist/js/theme-switcher-[hash].js.map</code>
            <br><small>Source map for JS debugging.</small>
        </li>
    </ul>
</div>

<hr>

<h2>Plug &amp; Play Usage</h2>
<p>The theme switcher is <strong>fully standalone</strong> when using dist files (storage.js is bundled in).</p>

<div class="code-block"><code>&lt;!-- 1. OPTIONAL: Prevent flash of wrong theme (add in &lt;head&gt; BEFORE css) --&gt;
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
&lt;/script&gt;

&lt;!-- 2. Include CSS in &lt;head&gt; --&gt;
&lt;link rel="stylesheet" href="path/to/theme-switcher.css"&gt;

&lt;!-- 3. Include JS before &lt;/body&gt; --&gt;
&lt;script src="path/to/theme-switcher.iife.js"&gt;&lt;/script&gt;

&lt;!-- 4. Use CSS variables in your styles --&gt;
&lt;style&gt;
body {
    background: var(--theme-bg);
    color: var(--theme-text);
}
&lt;/style&gt;

&lt;!-- That's it! Button appears automatically in bottom-right corner. --&gt;</code></div>

<hr>

<h2>How Theme Switching Works</h2>
<p>Understanding the flow helps when debugging or customizing:</p>

<div class="demo-box">
    <ol>
        <li><strong>Page Load:</strong> Flash prevention script reads localStorage and sets <code>data-theme</code> immediately</li>
        <li><strong>JS Init:</strong> <code>initTheme()</code> reads saved mode, applies theme, sets up system listener if needed</li>
        <li><strong>Button Click:</strong> <code>toggleTheme()</code> cycles mode (light &rarr; dark &rarr; system &rarr; light)</li>
        <li><strong>Mode Change:</strong> <code>setMode()</code> saves to localStorage and updates <code>&lt;html data-theme="..."&gt;</code></li>
        <li><strong>CSS Responds:</strong> <code>[data-theme='light']</code> or <code>[data-theme='dark']</code> selectors apply correct variables</li>
        <li><strong>System Mode:</strong> When mode is 'system', listens for OS changes via <code>matchMedia</code></li>
    </ol>
</div>

<?php require_once BASE_PATH . '/includes/demo-footer.php'; ?>
