// Tooltip
// ========
// A modular, accessible tooltip component with auto-positioning

import { Tooltip, POSITIONS, TRIGGERS, DEFAULTS } from './core/tooltip.js';
import { DATA_ATTRIBUTES } from './core/constants.js';

/**
 * Parse data attributes from an element
 * @param {HTMLElement} element
 * @returns {Object}
 */
function parseDataAttributes(element) {
  const options = {};

  // Content from data-tooltip attribute
  const content = element.getAttribute(DATA_ATTRIBUTES.tooltip);
  if (content) {
    options.content = content;
  }

  // Position
  const position = element.getAttribute(DATA_ATTRIBUTES.position);
  if (position && Object.values(POSITIONS).includes(position)) {
    options.position = position;
  }

  // Trigger
  const trigger = element.getAttribute(DATA_ATTRIBUTES.trigger);
  if (trigger) {
    // Support comma-separated triggers
    const triggers = trigger.split(',').map((t) => t.trim());
    options.trigger = triggers.length === 1 ? triggers[0] : triggers;
  }

  // Delay
  const delay = element.getAttribute(DATA_ATTRIBUTES.delay);
  if (delay) {
    const parsed = parseInt(delay, 10);
    if (!isNaN(parsed)) {
      options.delay = parsed;
    }
  }

  // Interactive
  if (element.hasAttribute(DATA_ATTRIBUTES.interactive)) {
    options.interactive = true;
  }

  // HTML content
  if (element.hasAttribute(DATA_ATTRIBUTES.html)) {
    options.html = true;
  }

  // Keyboard shortcut
  const shortcut = element.getAttribute(DATA_ATTRIBUTES.shortcut);
  if (shortcut) {
    options.shortcut = shortcut;
  }

  return options;
}

/**
 * Initialize tooltips from data attributes
 * @param {string|HTMLElement} container - Container to search in
 * @returns {Tooltip[]} Array of created tooltip instances
 */
export function init(container = document) {
  const root = typeof container === 'string'
    ? document.querySelector(container)
    : container;

  if (!root) {
    console.warn('Tooltip: Container not found');
    return [];
  }

  const elements = root.querySelectorAll(`[${DATA_ATTRIBUTES.tooltip}]`);
  const instances = [];

  elements.forEach((element) => {
    // Skip if already initialized
    if (Tooltip.getInstance(element)) {
      instances.push(Tooltip.getInstance(element));
      return;
    }

    const options = parseDataAttributes(element);
    const tooltip = new Tooltip(element, options);
    instances.push(tooltip);
  });

  return instances;
}

/**
 * Destroy all tooltip instances in a container
 * @param {string|HTMLElement} container
 */
export function destroyAll(container = document) {
  const root = typeof container === 'string'
    ? document.querySelector(container)
    : container;

  if (!root) return;

  const elements = root.querySelectorAll(`[${DATA_ATTRIBUTES.tooltip}]`);

  elements.forEach((element) => {
    const instance = Tooltip.getInstance(element);
    if (instance) {
      instance.destroy();
    }
  });
}

// Auto-initialize when DOM is ready
if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => init());
  } else {
    init();
  }
}

// Export for manual usage
export { Tooltip, POSITIONS, TRIGGERS, DEFAULTS };
