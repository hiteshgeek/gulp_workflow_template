// Theme Switcher
// ===============
// A standalone theme switcher component with system preference support

import { initTheme, toggleTheme, getSavedMode, MODES } from './core/theme.js';

// SVG Icons (Feather Icons)
const SUN_ICON = `<svg class="theme-switcher__icon theme-switcher__icon--sun" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>`;

const MOON_ICON = `<svg class="theme-switcher__icon theme-switcher__icon--moon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>`;

const SYSTEM_ICON = `<svg class="theme-switcher__icon theme-switcher__icon--system" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`;

/**
 * Update button icon based on current mode
 * @param {HTMLButtonElement} button
 */
function updateButtonIcon(button) {
  const mode = getSavedMode();
  button.setAttribute('data-mode', mode);

  // Update aria-label
  const labels = {
    [MODES.LIGHT]: 'Current: Light mode. Click to switch to Dark mode',
    [MODES.DARK]: 'Current: Dark mode. Click to switch to System mode',
    [MODES.SYSTEM]: 'Current: System mode. Click to switch to Light mode',
  };
  button.setAttribute('aria-label', labels[mode] || 'Toggle theme');
}

/**
 * Create the theme switcher button element
 * @returns {HTMLButtonElement}
 */
function createButton() {
  const button = document.createElement('button');
  button.className = 'theme-switcher';
  button.innerHTML = SUN_ICON + MOON_ICON + SYSTEM_ICON;
  updateButtonIcon(button);
  return button;
}

/**
 * Initialize the theme switcher
 * @param {Object} options - Configuration options
 * @param {string|HTMLElement} options.container - Container selector or element (default: body)
 */
export function init(options = {}) {
  const { container = document.body } = options;

  // Initialize theme from saved/system preference
  initTheme();

  // Create and append button
  const target = typeof container === 'string'
    ? document.querySelector(container)
    : container;

  if (!target) {
    console.warn('Theme Switcher: Container not found');
    return;
  }

  const button = createButton();

  button.addEventListener('click', () => {
    toggleTheme();
    updateButtonIcon(button);
  });

  target.appendChild(button);
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => init());
} else {
  init();
}

// Export for manual usage
export { initTheme, toggleTheme, getSavedMode, MODES } from './core/theme.js';
