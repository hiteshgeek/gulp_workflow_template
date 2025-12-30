// Tooltip Core
// =============
// Main Tooltip class with full feature support

import { POSITIONS, TRIGGERS, DEFAULTS, CLASSES } from './constants.js';
import {
  calculatePosition,
  getOptimalPosition,
  applyPosition,
  clampToViewport,
} from './position.js';

// Store all tooltip instances
const instances = new WeakMap();

// Unique ID counter
let idCounter = 0;

/**
 * Generate unique tooltip ID
 * @returns {string}
 */
function generateId() {
  return `tooltip-${++idCounter}`;
}

/**
 * Merge options with defaults
 * @param {Object} options
 * @returns {Object}
 */
function mergeOptions(options = {}) {
  return {
    ...DEFAULTS,
    ...options,
    delay: {
      ...DEFAULTS.delay,
      ...(typeof options.delay === 'number'
        ? { show: options.delay, hide: options.delay }
        : options.delay),
    },
  };
}

/**
 * Tooltip Class
 */
export class Tooltip {
  /**
   * Create a tooltip instance
   * @param {HTMLElement} trigger - The trigger element
   * @param {Object} options - Configuration options
   */
  constructor(trigger, options = {}) {
    if (!trigger || !(trigger instanceof HTMLElement)) {
      throw new Error('Tooltip: trigger element is required');
    }

    // Check for existing instance
    if (instances.has(trigger)) {
      return instances.get(trigger);
    }

    this.trigger = trigger;
    this.options = mergeOptions(options);
    this.id = generateId();
    this.isVisible = false;
    this.tooltipElement = null;
    this.showTimeout = null;
    this.hideTimeout = null;

    this._bindEvents = this._bindEvents.bind(this);
    this._handleMouseEnter = this._handleMouseEnter.bind(this);
    this._handleMouseLeave = this._handleMouseLeave.bind(this);
    this._handleFocus = this._handleFocus.bind(this);
    this._handleBlur = this._handleBlur.bind(this);
    this._handleClick = this._handleClick.bind(this);
    this._handleKeydown = this._handleKeydown.bind(this);
    this._handleOutsideClick = this._handleOutsideClick.bind(this);

    this._init();
    instances.set(trigger, this);
  }

  /**
   * Initialize tooltip
   * @private
   */
  _init() {
    this._createTooltipElement();
    this._setupAccessibility();
    this._bindEvents();
  }

  /**
   * Create the tooltip DOM element
   * @private
   */
  _createTooltipElement() {
    const tooltip = document.createElement('div');
    tooltip.id = this.id;
    tooltip.className = CLASSES.tooltip;
    tooltip.setAttribute('role', 'tooltip');
    tooltip.setAttribute('aria-hidden', 'true');

    if (this.options.interactive) {
      tooltip.classList.add(CLASSES.interactive);
    }

    // Set content directly on tooltip element (simpler structure)
    this._setTooltipContent(tooltip, this.options.content);

    // Arrow
    if (this.options.arrow) {
      const arrow = document.createElement('div');
      arrow.className = CLASSES.arrow;
      tooltip.appendChild(arrow);
    }

    this.tooltipElement = tooltip;
  }

  /**
   * Set tooltip content (supports text or HTML based on options.html)
   * @private
   */
  _setTooltipContent(element, content) {
    // Store arrow element if it exists
    const arrow = element.querySelector('.' + CLASSES.arrow);

    // Clear content
    element.innerHTML = '';

    // Add text content
    const textSpan = document.createElement('span');
    textSpan.className = CLASSES.text;
    if (this.options.html) {
      textSpan.innerHTML = content || '';
    } else {
      textSpan.textContent = content || '';
    }
    element.appendChild(textSpan);

    // Add shortcut badge if provided
    if (this.options.shortcut) {
      const shortcutSpan = document.createElement('span');
      shortcutSpan.className = CLASSES.shortcut;
      shortcutSpan.textContent = this.options.shortcut;
      element.appendChild(shortcutSpan);
    }

    // Re-add arrow if it existed
    if (arrow) {
      element.appendChild(arrow);
    }
  }

  /**
   * Setup ARIA attributes for accessibility
   * @private
   */
  _setupAccessibility() {
    this.trigger.setAttribute('aria-describedby', this.id);

    // Make trigger focusable if it isn't already
    if (!this.trigger.hasAttribute('tabindex') && this.trigger.tabIndex < 0) {
      this.trigger.setAttribute('tabindex', '0');
    }
  }

  /**
   * Bind event listeners based on trigger type
   * @private
   */
  _bindEvents() {
    const { trigger } = this.options;
    const triggers = Array.isArray(trigger) ? trigger : [trigger];

    triggers.forEach((t) => {
      switch (t) {
        case TRIGGERS.HOVER:
          this.trigger.addEventListener('mouseenter', this._handleMouseEnter);
          this.trigger.addEventListener('mouseleave', this._handleMouseLeave);
          if (this.options.interactive) {
            // Keep tooltip open when hovering over it
            this.tooltipElement.addEventListener('mouseenter', this._handleMouseEnter);
            this.tooltipElement.addEventListener('mouseleave', this._handleMouseLeave);
          }
          break;

        case TRIGGERS.FOCUS:
          this.trigger.addEventListener('focus', this._handleFocus);
          this.trigger.addEventListener('blur', this._handleBlur);
          break;

        case TRIGGERS.CLICK:
          this.trigger.addEventListener('click', this._handleClick);
          break;
      }
    });

    // Keyboard support
    this.trigger.addEventListener('keydown', this._handleKeydown);
  }

  /**
   * Unbind event listeners
   * @private
   */
  _unbindEvents() {
    this.trigger.removeEventListener('mouseenter', this._handleMouseEnter);
    this.trigger.removeEventListener('mouseleave', this._handleMouseLeave);
    this.trigger.removeEventListener('focus', this._handleFocus);
    this.trigger.removeEventListener('blur', this._handleBlur);
    this.trigger.removeEventListener('click', this._handleClick);
    this.trigger.removeEventListener('keydown', this._handleKeydown);

    if (this.tooltipElement) {
      this.tooltipElement.removeEventListener('mouseenter', this._handleMouseEnter);
      this.tooltipElement.removeEventListener('mouseleave', this._handleMouseLeave);
    }

    document.removeEventListener('click', this._handleOutsideClick);
  }

  /**
   * Handle mouse enter event
   * @private
   */
  _handleMouseEnter() {
    this._clearTimeouts();
    this.showTimeout = setTimeout(() => this.show(), this.options.delay.show);
  }

  /**
   * Handle mouse leave event
   * @private
   */
  _handleMouseLeave() {
    this._clearTimeouts();
    this.hideTimeout = setTimeout(() => this.hide(), this.options.delay.hide);
  }

  /**
   * Handle focus event
   * @private
   */
  _handleFocus() {
    this.show();
  }

  /**
   * Handle blur event
   * @private
   */
  _handleBlur() {
    this.hide();
  }

  /**
   * Handle click event
   * @private
   */
  _handleClick(e) {
    e.preventDefault();
    this.toggle();

    // Add outside click listener when showing
    if (this.isVisible) {
      setTimeout(() => {
        document.addEventListener('click', this._handleOutsideClick);
      }, 0);
    }
  }

  /**
   * Handle keydown event
   * @private
   */
  _handleKeydown(e) {
    if (e.key === 'Escape' && this.isVisible) {
      this.hide();
      e.preventDefault();
    }
  }

  /**
   * Handle clicks outside tooltip
   * @private
   */
  _handleOutsideClick(e) {
    if (
      !this.trigger.contains(e.target) &&
      !this.tooltipElement.contains(e.target)
    ) {
      this.hide();
      document.removeEventListener('click', this._handleOutsideClick);
    }
  }

  /**
   * Clear show/hide timeouts
   * @private
   */
  _clearTimeouts() {
    if (this.showTimeout) {
      clearTimeout(this.showTimeout);
      this.showTimeout = null;
    }
    if (this.hideTimeout) {
      clearTimeout(this.hideTimeout);
      this.hideTimeout = null;
    }
  }

  /**
   * Update tooltip position
   * @private
   * @param {boolean} force - Force update even if not visible (used during show)
   */
  _updatePosition(force = false) {
    if (!this.tooltipElement) return;
    if (!force && !this.isVisible) return;

    const optimalPosition = getOptimalPosition(
      this.trigger,
      this.tooltipElement,
      this.options.position,
      this.options.offset
    );

    let coords = calculatePosition(
      this.trigger,
      this.tooltipElement,
      optimalPosition,
      this.options.offset
    );

    // Clamp to viewport
    coords = clampToViewport(coords, this.tooltipElement);

    applyPosition(this.tooltipElement, coords, optimalPosition);
  }

  /**
   * Dispatch a custom event
   * @private
   * @param {string} eventName - Event name
   * @param {Object} detail - Event detail
   */
  _dispatchEvent(eventName, detail = {}) {
    const event = new CustomEvent(eventName, {
      bubbles: true,
      cancelable: true,
      detail: { tooltip: this, ...detail },
    });
    return this.trigger.dispatchEvent(event);
  }

  /**
   * Show the tooltip
   */
  show() {
    if (this.isVisible) return;

    // Dispatch show event (cancelable)
    const showEvent = this._dispatchEvent('tooltip:show');
    if (!showEvent) return; // Event was cancelled

    // Append to DOM if not already
    const appendTarget = this.options.appendTo || document.body;
    if (!this.tooltipElement.parentNode) {
      appendTarget.appendChild(this.tooltipElement);
      // Dispatch inserted event
      this._dispatchEvent('tooltip:inserted');
    }

    // Make visible for position calculation (but visually hidden)
    this.tooltipElement.style.display = 'block';
    this.tooltipElement.style.visibility = 'hidden';

    // Calculate and apply position (force=true since isVisible is still false)
    this._updatePosition(true);

    // Force reflow to ensure transition works
    void this.tooltipElement.offsetHeight;

    // Show tooltip with animation
    this.tooltipElement.style.visibility = '';
    this.tooltipElement.style.display = '';
    this.tooltipElement.classList.add(CLASSES.visible);
    this.tooltipElement.setAttribute('aria-hidden', 'false');

    this.isVisible = true;

    // Dispatch shown event after animation
    if (this.options.animation) {
      setTimeout(() => {
        if (this.isVisible) {
          this._dispatchEvent('tooltip:shown');
        }
      }, this.options.animationDuration);
    } else {
      this._dispatchEvent('tooltip:shown');
    }
  }

  /**
   * Hide the tooltip
   */
  hide() {
    if (!this.isVisible) return;

    // Dispatch hide event (cancelable)
    const hideEvent = this._dispatchEvent('tooltip:hide');
    if (!hideEvent) return; // Event was cancelled

    this._clearTimeouts();

    this.tooltipElement.classList.remove(CLASSES.visible);
    this.tooltipElement.setAttribute('aria-hidden', 'true');

    this.isVisible = false;

    document.removeEventListener('click', this._handleOutsideClick);

    // Remove from DOM after animation and dispatch hidden event
    const cleanupTooltip = () => {
      if (!this.isVisible && this.tooltipElement) {
        this.tooltipElement.style.display = 'none';
        if (this.tooltipElement.parentNode) {
          this.tooltipElement.parentNode.removeChild(this.tooltipElement);
        }
      }
      this._dispatchEvent('tooltip:hidden');
    };

    if (this.options.animation) {
      setTimeout(cleanupTooltip, this.options.animationDuration);
    } else {
      cleanupTooltip();
    }
  }

  /**
   * Toggle tooltip visibility
   */
  toggle() {
    if (this.isVisible) {
      this.hide();
    } else {
      this.show();
    }
  }

  /**
   * Update tooltip content
   * @param {string} content - New content (text or HTML)
   */
  setContent(content) {
    this.options.content = content;
    if (this.tooltipElement) {
      this._setTooltipContent(this.tooltipElement, content);
    }
    if (this.isVisible) {
      this._updatePosition();
    }
  }

  /**
   * Update tooltip options
   * @param {Object} options - New options to merge
   */
  setOptions(options) {
    this.options = mergeOptions({ ...this.options, ...options });
    if (this.isVisible) {
      this._updatePosition();
    }
  }

  /**
   * Destroy the tooltip instance
   */
  destroy() {
    this._clearTimeouts();
    this._unbindEvents();

    if (this.tooltipElement && this.tooltipElement.parentNode) {
      this.tooltipElement.parentNode.removeChild(this.tooltipElement);
    }

    this.trigger.removeAttribute('aria-describedby');
    instances.delete(this.trigger);

    // Dispatch disposed event
    this._dispatchEvent('tooltip:disposed');
  }

  /**
   * Enable the tooltip
   */
  enable() {
    this._bindEvents();
    this._dispatchEvent('tooltip:enabled');
  }

  /**
   * Disable the tooltip (prevents showing)
   */
  disable() {
    this._unbindEvents();
    if (this.isVisible) {
      this.hide();
    }
    this._dispatchEvent('tooltip:disabled');
  }

  /**
   * Update position (useful after content change or scroll)
   */
  update() {
    if (this.isVisible) {
      this._updatePosition();
    }
  }

  /**
   * Get tooltip instance for an element
   * @param {HTMLElement} element
   * @returns {Tooltip|null}
   */
  static getInstance(element) {
    return instances.get(element) || null;
  }
}

export { POSITIONS, TRIGGERS, DEFAULTS };
