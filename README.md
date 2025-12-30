# Theme Switcher

A modular, standalone theme toggle component with **Light**, **Dark**, and **System** modes.

## Features

- **Three modes:** Light, Dark, and System (follows OS preference)
- Automatically responds to system theme changes in real-time
- Saves user choice to localStorage
- Smooth transitions between themes
- Accessible (keyboard navigable, focus states, aria-labels)
- Standalone - works with any project
- Modular SCSS and JS architecture

## Installation

### Build the component

```bash
gulp theme-switcher
```

### Output files

| File | Description |
|------|-------------|
| `dist/css/theme-switcher-*.css` | Compiled CSS |
| `dist/js/theme-switcher-*.js` | ES Module |
| `dist/js/theme-switcher-*.iife.js` | Browser script (global `ThemeSwitcher`) |

## Quick Start

### 1. Include the files

```html
<!-- CSS in <head> -->
<link rel="stylesheet" href="theme-switcher.css">

<!-- JS before </body> -->
<script src="theme-switcher.iife.js"></script>
```

### 2. Add data-theme to html

```html
<html lang="en" data-theme="light">
```

### 3. Use CSS variables in your styles

```css
body {
    background-color: var(--theme-bg);
    color: var(--theme-text);
}

a {
    color: var(--theme-primary);
}

a:hover {
    color: var(--theme-primary-hover);
}
```

## How It Works

Click the floating button (bottom-right corner) to cycle through modes:

1. **Light** (sun icon) - Forces light theme
2. **Dark** (moon icon) - Forces dark theme
3. **System** (monitor icon) - Follows OS preference automatically

The button displays the **current mode** icon.

## CSS Variables

| Variable | Light | Dark |
|----------|-------|------|
| `--theme-bg` | #ffffff | #1a1a1a |
| `--theme-text` | #1a1a1a | #f5f5f5 |
| `--theme-primary` | #3b82f6 | #3b82f6 |
| `--theme-primary-hover` | #2563eb | #2563eb |

## JavaScript API

### ES Module

```javascript
import { initTheme, toggleTheme, getSavedMode, setMode, MODES } from './theme-switcher.js';

// Initialize (auto-called on page load)
initTheme();

// Toggle through modes: light -> dark -> system -> light
toggleTheme();

// Get current mode
const mode = getSavedMode(); // 'light', 'dark', or 'system'

// Set specific mode
setMode(MODES.DARK);
setMode(MODES.SYSTEM);

// Available modes
MODES.LIGHT   // 'light'
MODES.DARK    // 'dark'
MODES.SYSTEM  // 'system'
```

### IIFE (Browser)

```javascript
// Access via global ThemeSwitcher object
ThemeSwitcher.toggleTheme();
ThemeSwitcher.getSavedMode();
ThemeSwitcher.setMode('dark');
```

## PHP Asset Helper

```php
<?= Asset::css('theme-switcher.css', 'lib') ?>
<?= Asset::js('theme-switcher.iife.js', 'lib') ?>

// Or with ES modules
<?= Asset::js('theme-switcher.js', 'lib', ['type' => 'module']) ?>
```

## Customization

Override SCSS variables before importing to customize colors:

```scss
// In your SCSS file
@use 'common/scss' with (
    $color-light-bg: #f8f9fa,
    $color-light-text: #212529,
    $color-dark-bg: #212529,
    $color-dark-text: #f8f9fa,
    $color-primary: #6366f1,
    $color-primary-hover: #4f46e5
);

@use 'theme-switcher/scss/main';
```

## File Structure

```
src/library/theme-switcher/
├── scss/
│   ├── main.scss           # Entry point
│   ├── base/
│   │   └── _themes.scss    # CSS custom properties for themes
│   └── components/
│       └── _button.scss    # Toggle button styles
├── js/
│   ├── index.js            # Entry point
│   └── core/
│       └── theme.js        # Theme switching logic
└── README.md
```

## Browser Support

- Chrome/Edge 88+
- Firefox 78+
- Safari 14+

Requires support for:
- CSS Custom Properties
- `prefers-color-scheme` media query
- `localStorage`

## Build Commands

| Command | Description |
|---------|-------------|
| `gulp theme-switcher` | Build CSS and JS |
| `gulp theme-switcher:styles` | Build CSS only |
| `gulp theme-switcher:scripts` | Build JS only |
| `gulp theme-switcher --production` | Minified production build |

## License

MIT
