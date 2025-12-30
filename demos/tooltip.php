<?php
/**
 * Tooltip Component Demo
 * Demonstrates all tooltip features
 */

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/gulp_workflow_template');

$pageTitle = 'Tooltip';
$showBreadcrumb = true;
$includeCss = ['tooltip.css'];
$includeJs = ['tooltip.iife.js'];

require_once BASE_PATH . '/includes/demo-header.php';
?>

<h1>Tooltip</h1>
<p class="subtitle">Lightweight, accessible tooltips with smart positioning and multiple trigger modes</p>

<div class="note">
    <strong>Try it!</strong> Hover, click, or focus on the elements below to see tooltips in action.
    Tooltips automatically flip position when near viewport edges.
</div>

<h2>Live Demos</h2>

<!-- Basic Tooltips -->
<div class="demo-section">
    <h3>Basic Tooltips (Hover)</h3>
    <p>Default trigger is hover. Just add <code>data-tooltip</code> attribute.</p>
    <div class="demo-row">
        <button class="btn btn-primary" data-tooltip="Hello! I'm a tooltip">Hover me</button>
        <button class="btn btn-secondary" data-tooltip="Another tooltip with more content that wraps nicely">Longer text</button>
        <span data-tooltip="Tooltips work on any element!" style="cursor: help; text-decoration: underline dotted;">Hover this text</span>
    </div>
    <div class="code-block"><code>&lt;button data-tooltip="Hello! I'm a tooltip"&gt;Hover me&lt;/button&gt;</code></div>
</div>

<!-- Positions -->
<div class="demo-section">
    <h3>Positions</h3>
    <p>Use <code>data-tooltip-position</code> to set the tooltip position. Auto-flips when near edges.</p>
    <div class="demo-row">
        <button class="btn btn-secondary" data-tooltip="Top tooltip" data-tooltip-position="top">Top</button>
        <button class="btn btn-secondary" data-tooltip="Bottom tooltip" data-tooltip-position="bottom">Bottom</button>
        <button class="btn btn-secondary" data-tooltip="Left tooltip" data-tooltip-position="left">Left</button>
        <button class="btn btn-secondary" data-tooltip="Right tooltip" data-tooltip-position="right">Right</button>
    </div>
    <div class="code-block"><code>&lt;button data-tooltip="Top tooltip" data-tooltip-position="top"&gt;Top&lt;/button&gt;
&lt;button data-tooltip="Bottom tooltip" data-tooltip-position="bottom"&gt;Bottom&lt;/button&gt;
&lt;button data-tooltip="Left tooltip" data-tooltip-position="left"&gt;Left&lt;/button&gt;
&lt;button data-tooltip="Right tooltip" data-tooltip-position="right"&gt;Right&lt;/button&gt;</code></div>
</div>

<!-- Trigger Modes -->
<div class="demo-section">
    <h3>Trigger Modes</h3>
    <p>Use <code>data-tooltip-trigger</code> to change how the tooltip is activated.</p>
    <div class="demo-row">
        <button class="btn btn-secondary" data-tooltip="Hover triggered (default)" data-tooltip-trigger="hover">Hover</button>
        <button class="btn btn-secondary" data-tooltip="Click to toggle! Click again or click outside to close." data-tooltip-trigger="click">Click</button>
        <button class="btn btn-secondary" data-tooltip="Focus triggered - Tab to me!" data-tooltip-trigger="focus">Focus (Tab to me)</button>
    </div>
    <div class="code-block"><code>&lt;button data-tooltip="Hover triggered" data-tooltip-trigger="hover"&gt;Hover&lt;/button&gt;
&lt;button data-tooltip="Click to toggle!" data-tooltip-trigger="click"&gt;Click&lt;/button&gt;
&lt;button data-tooltip="Focus triggered" data-tooltip-trigger="focus"&gt;Focus&lt;/button&gt;</code></div>
</div>

<!-- Delay -->
<div class="demo-section">
    <h3>Delay</h3>
    <p>Use <code>data-tooltip-delay</code> to add a delay (in milliseconds) before showing.</p>
    <div class="demo-row">
        <button class="btn btn-secondary" data-tooltip="No delay" data-tooltip-delay="0">No delay</button>
        <button class="btn btn-secondary" data-tooltip="200ms delay" data-tooltip-delay="200">200ms delay</button>
        <button class="btn btn-secondary" data-tooltip="500ms delay" data-tooltip-delay="500">500ms delay</button>
    </div>
    <div class="code-block"><code>&lt;button data-tooltip="200ms delay" data-tooltip-delay="200"&gt;Delayed&lt;/button&gt;</code></div>
</div>

<!-- Interactive -->
<div class="demo-section">
    <h3>Interactive Tooltips</h3>
    <p>Add <code>data-tooltip-interactive</code> to allow hovering over the tooltip itself.</p>
    <div class="demo-row">
        <button class="btn btn-secondary" data-tooltip="You can hover over me! I won't disappear when you move to the tooltip." data-tooltip-interactive>Interactive tooltip</button>
    </div>
    <div class="code-block"><code>&lt;button data-tooltip="Hover over me!" data-tooltip-interactive&gt;Interactive&lt;/button&gt;</code></div>
</div>

<!-- Keyboard Shortcuts -->
<div class="demo-section">
    <h3>Keyboard Shortcuts</h3>
    <p>Use <code>data-tooltip-shortcut</code> to display a keyboard shortcut badge alongside the tooltip text.</p>
    <div class="demo-row">
        <button class="btn btn-primary" data-tooltip="Save document" data-tooltip-shortcut="Ctrl+S">Save</button>
        <button class="btn btn-secondary" data-tooltip="Copy selection" data-tooltip-shortcut="Ctrl+C">Copy</button>
        <button class="btn btn-secondary" data-tooltip="Paste content" data-tooltip-shortcut="Ctrl+V">Paste</button>
        <button class="btn btn-secondary" data-tooltip="Undo last action" data-tooltip-shortcut="Ctrl+Z">Undo</button>
        <button class="btn btn-secondary" data-tooltip="Search in document" data-tooltip-shortcut="Ctrl+F">Find</button>
    </div>
    <div class="code-block"><code>&lt;button data-tooltip="Save document" data-tooltip-shortcut="Ctrl+S"&gt;Save&lt;/button&gt;
&lt;button data-tooltip="Copy selection" data-tooltip-shortcut="Ctrl+C"&gt;Copy&lt;/button&gt;</code></div>
</div>

<!-- HTML Content -->
<div class="demo-section">
    <h3>HTML Content</h3>
    <p>Add <code>data-tooltip-html</code> to enable HTML rendering in tooltips. <strong>Use with caution</strong> - only use with trusted content.</p>
    <div class="demo-row">
        <button class="btn btn-secondary" data-tooltip="<strong>Bold</strong> and <em>italic</em> text" data-tooltip-html>Formatted text</button>
        <button class="btn btn-secondary" data-tooltip="Status: <span style='color:#4ade80;'>●</span> Online" data-tooltip-html>With colored icon</button>
    </div>
    <div class="code-block"><code>&lt;button data-tooltip="&lt;strong&gt;Bold&lt;/strong&gt; text" data-tooltip-html&gt;HTML tooltip&lt;/button&gt;</code></div>
</div>

<!-- Form Elements -->
<div class="demo-section">
    <h3>On Form Elements</h3>
    <p>Tooltips work great with form elements, especially with focus trigger.</p>
    <div class="demo-row" style="flex-direction: column; align-items: flex-start; gap: 0.75rem;">
        <input type="text" placeholder="Hover for help" data-tooltip="Enter your username" style="padding: 0.5rem; border-radius: 4px; border: 1px solid rgba(128,128,128,0.3);">
        <input type="email" placeholder="Focus for help" data-tooltip="We'll never share your email" data-tooltip-trigger="focus" style="padding: 0.5rem; border-radius: 4px; border: 1px solid rgba(128,128,128,0.3);">
    </div>
</div>

<!-- Auto-Flip Demo -->
<div class="demo-section">
    <h3>Smart Positioning (Auto-Flip)</h3>
    <p>Tooltips automatically reposition when they would overflow the viewport. Try scrolling to the edge!</p>
    <div class="demo-row" style="justify-content: space-between;">
        <button class="btn btn-secondary" data-tooltip="I flip right when too close to left edge" data-tooltip-position="left">Left edge</button>
        <button class="btn btn-secondary" data-tooltip="I flip left when too close to right edge" data-tooltip-position="right">Right edge</button>
    </div>
</div>

<!-- Events Demo -->
<div class="demo-section">
    <h3>Events Demo (All 8 Events)</h3>
    <p>Interact with the controls below to see all tooltip events fire in real-time.</p>
    <div class="demo-row" style="flex-wrap: wrap; gap: 0.5rem;">
        <button id="event-demo-btn" class="btn btn-primary" data-tooltip="Hover me to see show/hide events!">Hover for Events</button>
        <button id="disable-btn" class="btn btn-secondary">Disable Tooltip</button>
        <button id="enable-btn" class="btn btn-secondary">Enable Tooltip</button>
        <button id="destroy-btn" class="btn btn-secondary" style="background: #dc2626;">Destroy Tooltip</button>
        <button id="recreate-btn" class="btn btn-secondary" style="background: #059669;">Recreate Tooltip</button>
        <button id="clear-log-btn" class="btn btn-secondary" style="opacity: 0.7;">Clear Log</button>
    </div>
    <div id="event-log" style="margin-top: 1rem; padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 4px; font-family: monospace; font-size: 0.8rem; max-height: 200px; overflow-y: auto;">
        <div style="opacity: 0.5;">Event log will appear here...</div>
    </div>
    <div style="margin-top: 0.75rem; font-size: 0.85rem; opacity: 0.8;">
        <strong>Events:</strong>
        <span style="color: #4ade80;">tooltip:show</span>,
        <span style="color: #4ade80;">tooltip:shown</span>,
        <span style="color: #60a5fa;">tooltip:inserted</span>,
        <span style="color: #f87171;">tooltip:hide</span>,
        <span style="color: #f87171;">tooltip:hidden</span>,
        <span style="color: #a78bfa;">tooltip:enabled</span>,
        <span style="color: #fb923c;">tooltip:disabled</span>,
        <span style="color: #f472b6;">tooltip:disposed</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('event-demo-btn');
    const log = document.getElementById('event-log');
    const disableBtn = document.getElementById('disable-btn');
    const enableBtn = document.getElementById('enable-btn');
    const destroyBtn = document.getElementById('destroy-btn');
    const recreateBtn = document.getElementById('recreate-btn');
    const clearLogBtn = document.getElementById('clear-log-btn');

    // All 8 tooltip events with color coding
    const eventColors = {
        'tooltip:show': '#4ade80',      // green
        'tooltip:shown': '#4ade80',     // green
        'tooltip:inserted': '#60a5fa', // blue
        'tooltip:hide': '#f87171',      // red
        'tooltip:hidden': '#f87171',    // red
        'tooltip:enabled': '#a78bfa',   // purple
        'tooltip:disabled': '#fb923c',  // orange
        'tooltip:disposed': '#f472b6'   // pink
    };

    function logEvent(eventName) {
        const time = new Date().toLocaleTimeString();
        const div = document.createElement('div');
        div.textContent = '[' + time + '] ' + eventName;
        div.style.color = eventColors[eventName] || '#ffffff';
        log.appendChild(div);
        log.scrollTop = log.scrollHeight;
    }

    function attachEventListeners(element) {
        Object.keys(eventColors).forEach(function(eventName) {
            element.addEventListener(eventName, function() {
                logEvent(eventName);
            });
        });
    }

    // Attach listeners to main button
    attachEventListeners(btn);

    // Disable tooltip
    disableBtn.addEventListener('click', function() {
        const instance = Tooltip.Tooltip.getInstance(btn);
        if (instance) {
            instance.disable();
        } else {
            logEvent('(No tooltip instance found)');
        }
    });

    // Enable tooltip
    enableBtn.addEventListener('click', function() {
        const instance = Tooltip.Tooltip.getInstance(btn);
        if (instance) {
            instance.enable();
        } else {
            logEvent('(No tooltip instance found)');
        }
    });

    // Destroy tooltip
    destroyBtn.addEventListener('click', function() {
        const instance = Tooltip.Tooltip.getInstance(btn);
        if (instance) {
            instance.destroy();
        } else {
            logEvent('(No tooltip instance found)');
        }
    });

    // Recreate tooltip
    recreateBtn.addEventListener('click', function() {
        const existing = Tooltip.Tooltip.getInstance(btn);
        if (existing) {
            logEvent('(Tooltip already exists)');
            return;
        }
        new Tooltip.Tooltip(btn, {
            content: 'Hover me to see show/hide events!'
        });
        attachEventListeners(btn);
        logEvent('(Tooltip recreated - listeners attached)');
    });

    // Clear log
    clearLogBtn.addEventListener('click', function() {
        log.innerHTML = '<div style="opacity: 0.5;">Event log cleared...</div>';
    });
});
</script>

<hr>

<h2>Quick Start</h2>

<h3>1. Include the files</h3>
<div class="code-block"><code>&lt;!-- CSS in &lt;head&gt; --&gt;
&lt;link rel="stylesheet" href="tooltip.css"&gt;

&lt;!-- JS before &lt;/body&gt; --&gt;
&lt;script src="tooltip.iife.js"&gt;&lt;/script&gt;</code></div>

<h3>2. Add data attributes</h3>
<div class="code-block"><code>&lt;!-- Basic tooltip --&gt;
&lt;button data-tooltip="Hello World"&gt;Hover me&lt;/button&gt;

&lt;!-- With options --&gt;
&lt;button
    data-tooltip="Click me!"
    data-tooltip-position="bottom"
    data-tooltip-trigger="click"
    data-tooltip-delay="200"
&gt;Click tooltip&lt;/button&gt;</code></div>

<hr>

<h2>Data Attributes</h2>
<div class="demo-box">
    <ul class="file-list">
        <li><code>data-tooltip</code> - Tooltip content (required)</li>
        <li><code>data-tooltip-position</code> - Position: <code>top</code>, <code>bottom</code>, <code>left</code>, <code>right</code> (default: top)</li>
        <li><code>data-tooltip-trigger</code> - Trigger: <code>hover</code>, <code>click</code>, <code>focus</code> (default: hover)</li>
        <li><code>data-tooltip-delay</code> - Show delay in milliseconds (default: 0)</li>
        <li><code>data-tooltip-interactive</code> - Allow hovering over tooltip (boolean)</li>
        <li><code>data-tooltip-html</code> - Enable HTML content rendering (boolean, use with caution)</li>
        <li><code>data-tooltip-shortcut</code> - Keyboard shortcut to display (e.g., "Ctrl+S")</li>
    </ul>
</div>

<hr>

<h2>JavaScript API</h2>

<h3>Programmatic Usage</h3>
<div class="code-block"><code>// Create tooltip programmatically
const tooltip = new Tooltip.Tooltip(element, {
    content: 'Hello World',
    position: 'top',      // 'top', 'bottom', 'left', 'right'
    trigger: 'hover',     // 'hover', 'click', 'focus' or array
    delay: { show: 200, hide: 100 },
    animation: true,
    arrow: true,
    interactive: false,
    html: false,          // Enable HTML content (use with trusted content only)
    shortcut: 'Ctrl+S',   // Keyboard shortcut badge
    offset: 8
});

// Methods
tooltip.show();           // Show the tooltip
tooltip.hide();           // Hide the tooltip
tooltip.toggle();         // Toggle visibility
tooltip.setContent('New content');  // Update content
tooltip.setOptions({ position: 'bottom' }); // Update options
tooltip.update();         // Update position
tooltip.enable();         // Enable tooltip
tooltip.disable();        // Disable tooltip
tooltip.destroy();        // Remove tooltip completely</code></div>

<h3>Events</h3>
<p>All events are dispatched on the trigger element, bubble up the DOM, and are cancelable.</p>
<div class="demo-box">
    <ul class="file-list">
        <li><code>tooltip:show</code> - Fires before tooltip is shown (cancelable)</li>
        <li><code>tooltip:shown</code> - Fires after tooltip is fully visible (after animation)</li>
        <li><code>tooltip:inserted</code> - Fires when tooltip element is added to DOM</li>
        <li><code>tooltip:hide</code> - Fires before tooltip is hidden (cancelable)</li>
        <li><code>tooltip:hidden</code> - Fires after tooltip is fully hidden (after animation)</li>
        <li><code>tooltip:enabled</code> - Fires when tooltip is enabled</li>
        <li><code>tooltip:disabled</code> - Fires when tooltip is disabled</li>
        <li><code>tooltip:disposed</code> - Fires when tooltip is destroyed</li>
    </ul>
</div>

<div class="code-block"><code>// Listen for events
element.addEventListener('tooltip:show', (e) => {
    console.log('Tooltip is about to show', e.detail.tooltip);
    // e.preventDefault(); // Cancel showing
});

element.addEventListener('tooltip:shown', (e) => {
    console.log('Tooltip is now visible');
});

element.addEventListener('tooltip:hide', (e) => {
    console.log('Tooltip is about to hide');
    // e.preventDefault(); // Cancel hiding
});

element.addEventListener('tooltip:hidden', (e) => {
    console.log('Tooltip is now hidden');
});</code></div>

<h3>Manual Initialization</h3>
<div class="code-block"><code>// Re-initialize tooltips in a container (e.g., after adding new elements)
Tooltip.init(document.getElementById('my-container'));

// Destroy all tooltips in a container
Tooltip.destroyAll(document.getElementById('my-container'));

// Get existing tooltip instance
const instance = Tooltip.Tooltip.getInstance(element);</code></div>

<hr>

<h2>Output Files</h2>
<div class="demo-box">
    <ul class="file-list">
        <li>
            <code><?= Asset::getFile('tooltip.css', 'lib') ?></code>
            <span class="badge badge--css">CSS</span>
        </li>
        <li>
            <code><?= Asset::getFile('tooltip.js', 'lib') ?></code>
            <span class="badge badge--js">ESM</span>
        </li>
        <li>
            <code><?= Asset::getFile('tooltip.iife.js', 'lib') ?></code>
            <span class="badge badge--js">IIFE</span>
        </li>
    </ul>
</div>

<hr>

<h2>Build Commands</h2>
<div class="demo-box">
    <ul class="file-list">
        <li><code>gulp tooltip</code> - Build CSS and JS</li>
        <li><code>gulp tooltip:styles</code> - Build CSS only</li>
        <li><code>gulp tooltip:scripts</code> - Build JS only</li>
        <li><code>gulp tooltip --production</code> - Minified build</li>
    </ul>
</div>

<hr>

<h2>Accessibility</h2>
<ul>
    <li><strong>ARIA:</strong> Tooltips use <code>role="tooltip"</code> and <code>aria-describedby</code></li>
    <li><strong>Keyboard:</strong> Press <code>Escape</code> to close any visible tooltip</li>
    <li><strong>Focus:</strong> Elements without tabindex automatically receive <code>tabindex="0"</code></li>
    <li><strong>Screen readers:</strong> Tooltip content is announced when focus trigger is used</li>
</ul>

<?php require_once BASE_PATH . '/includes/demo-footer.php'; ?>
