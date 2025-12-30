// Theme Core
// ===========
// Core logic for theme switching with system preference support

import { getItem, setItem } from '../../../common/js/storage.js';

const STORAGE_KEY = 'theme-mode';
const MODES = {
  LIGHT: 'light',
  DARK: 'dark',
  SYSTEM: 'system',
};

let systemThemeCleanup = null;

/**
 * Get the system's preferred color scheme
 * @returns {string} 'dark' or 'light'
 */
export function getSystemTheme() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches
    ? MODES.DARK
    : MODES.LIGHT;
}

/**
 * Get the saved mode from localStorage
 * @returns {string} Saved mode ('light', 'dark', 'system') or 'system' as default
 */
export function getSavedMode() {
  return getItem(STORAGE_KEY, MODES.SYSTEM);
}

/**
 * Get the current active theme (what's actually displayed)
 * @returns {string} Current theme ('light' or 'dark')
 */
export function getCurrentTheme() {
  return document.documentElement.getAttribute('data-theme') || MODES.LIGHT;
}

/**
 * Get the current mode (user's preference)
 * @returns {string} Current mode ('light', 'dark', or 'system')
 */
export function getCurrentMode() {
  return getSavedMode();
}

/**
 * Apply a theme to the document
 * @param {string} theme - 'light' or 'dark'
 */
function applyTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
}

/**
 * Set the theme mode
 * @param {string} mode - 'light', 'dark', or 'system'
 */
export function setMode(mode) {
  // Clean up previous system listener
  if (systemThemeCleanup) {
    systemThemeCleanup();
    systemThemeCleanup = null;
  }

  // Save the mode preference
  setItem(STORAGE_KEY, mode);

  if (mode === MODES.SYSTEM) {
    // Apply current system theme
    applyTheme(getSystemTheme());

    // Listen for system theme changes
    systemThemeCleanup = onSystemThemeChange((theme) => {
      applyTheme(theme);
    });
  } else {
    // Apply the explicit theme
    applyTheme(mode);
  }
}

/**
 * Cycle through modes: light -> dark -> system -> light
 * @returns {string} The new mode
 */
export function toggleTheme() {
  const currentMode = getSavedMode();
  let newMode;

  switch (currentMode) {
    case MODES.LIGHT:
      newMode = MODES.DARK;
      break;
    case MODES.DARK:
      newMode = MODES.SYSTEM;
      break;
    case MODES.SYSTEM:
    default:
      newMode = MODES.LIGHT;
      break;
  }

  setMode(newMode);
  return newMode;
}

/**
 * Initialize theme based on saved preference
 */
export function initTheme() {
  const savedMode = getSavedMode();
  setMode(savedMode);
}

/**
 * Listen for system theme changes
 * @param {Function} callback - Called when system theme changes
 * @returns {Function} Cleanup function
 */
export function onSystemThemeChange(callback) {
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

  const handler = (e) => {
    const theme = e.matches ? MODES.DARK : MODES.LIGHT;
    callback(theme);
  };

  mediaQuery.addEventListener('change', handler);

  return () => mediaQuery.removeEventListener('change', handler);
}

export { MODES, STORAGE_KEY };
