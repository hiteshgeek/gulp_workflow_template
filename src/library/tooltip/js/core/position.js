// Tooltip Positioning
// ====================
// Calculate and apply tooltip positions with auto-flip support

import { POSITIONS, CLASSES } from './constants.js';

/**
 * Get viewport dimensions
 * @returns {{ width: number, height: number, scrollX: number, scrollY: number }}
 */
function getViewport() {
  return {
    width: window.innerWidth,
    height: window.innerHeight,
    scrollX: window.scrollX || window.pageXOffset,
    scrollY: window.scrollY || window.pageYOffset,
  };
}

/**
 * Get element's bounding rectangle relative to viewport
 * @param {HTMLElement} element
 * @returns {DOMRect}
 */
function getRect(element) {
  return element.getBoundingClientRect();
}

/**
 * Calculate tooltip position coordinates
 * Uses fixed positioning (viewport-relative coordinates from getBoundingClientRect)
 * @param {HTMLElement} trigger - The trigger element
 * @param {HTMLElement} tooltip - The tooltip element
 * @param {string} position - Desired position (top, bottom, left, right)
 * @param {number} offset - Distance from trigger
 * @returns {{ top: number, left: number }}
 */
export function calculatePosition(trigger, tooltip, position, offset = 8) {
  const triggerRect = getRect(trigger);
  const tooltipRect = getRect(tooltip);

  let top = 0;
  let left = 0;

  switch (position) {
    case POSITIONS.TOP:
      top = triggerRect.top - tooltipRect.height - offset;
      left = triggerRect.left + (triggerRect.width - tooltipRect.width) / 2;
      break;

    case POSITIONS.BOTTOM:
      top = triggerRect.bottom + offset;
      left = triggerRect.left + (triggerRect.width - tooltipRect.width) / 2;
      break;

    case POSITIONS.LEFT:
      top = triggerRect.top + (triggerRect.height - tooltipRect.height) / 2;
      left = triggerRect.left - tooltipRect.width - offset;
      break;

    case POSITIONS.RIGHT:
      top = triggerRect.top + (triggerRect.height - tooltipRect.height) / 2;
      left = triggerRect.right + offset;
      break;

    default:
      // Default to top
      top = triggerRect.top - tooltipRect.height - offset;
      left = triggerRect.left + (triggerRect.width - tooltipRect.width) / 2;
  }

  return { top, left };
}

/**
 * Check if tooltip fits in viewport at given position
 * @param {HTMLElement} trigger
 * @param {HTMLElement} tooltip
 * @param {string} position
 * @param {number} offset
 * @returns {boolean}
 */
function fitsInViewport(trigger, tooltip, position, offset = 8) {
  const triggerRect = getRect(trigger);
  const tooltipRect = getRect(tooltip);
  const viewport = getViewport();
  const padding = 10; // Minimum distance from viewport edge

  switch (position) {
    case POSITIONS.TOP:
      return triggerRect.top - tooltipRect.height - offset >= padding;

    case POSITIONS.BOTTOM:
      return triggerRect.bottom + tooltipRect.height + offset <= viewport.height - padding;

    case POSITIONS.LEFT:
      return triggerRect.left - tooltipRect.width - offset >= padding;

    case POSITIONS.RIGHT:
      return triggerRect.right + tooltipRect.width + offset <= viewport.width - padding;

    default:
      return true;
  }
}

/**
 * Get the opposite position for flip fallback
 * @param {string} position
 * @returns {string}
 */
function getOppositePosition(position) {
  const opposites = {
    [POSITIONS.TOP]: POSITIONS.BOTTOM,
    [POSITIONS.BOTTOM]: POSITIONS.TOP,
    [POSITIONS.LEFT]: POSITIONS.RIGHT,
    [POSITIONS.RIGHT]: POSITIONS.LEFT,
  };
  return opposites[position] || POSITIONS.TOP;
}

/**
 * Get optimal position with auto-flip if preferred position doesn't fit
 * @param {HTMLElement} trigger
 * @param {HTMLElement} tooltip
 * @param {string} preferredPosition
 * @param {number} offset
 * @returns {string}
 */
export function getOptimalPosition(trigger, tooltip, preferredPosition, offset = 8) {
  // First, try the preferred position
  if (fitsInViewport(trigger, tooltip, preferredPosition, offset)) {
    return preferredPosition;
  }

  // Try the opposite position
  const opposite = getOppositePosition(preferredPosition);
  if (fitsInViewport(trigger, tooltip, opposite, offset)) {
    return opposite;
  }

  // Try other positions
  const allPositions = [POSITIONS.TOP, POSITIONS.BOTTOM, POSITIONS.LEFT, POSITIONS.RIGHT];
  for (const pos of allPositions) {
    if (pos !== preferredPosition && pos !== opposite && fitsInViewport(trigger, tooltip, pos, offset)) {
      return pos;
    }
  }

  // Fallback to preferred position if nothing fits
  return preferredPosition;
}

/**
 * Apply position styles to tooltip element
 * @param {HTMLElement} tooltip
 * @param {{ top: number, left: number }} coords
 * @param {string} position
 */
export function applyPosition(tooltip, coords, position) {
  // Remove all position classes
  tooltip.classList.remove(
    CLASSES.positionTop,
    CLASSES.positionBottom,
    CLASSES.positionLeft,
    CLASSES.positionRight
  );

  // Add current position class
  const positionClass = {
    [POSITIONS.TOP]: CLASSES.positionTop,
    [POSITIONS.BOTTOM]: CLASSES.positionBottom,
    [POSITIONS.LEFT]: CLASSES.positionLeft,
    [POSITIONS.RIGHT]: CLASSES.positionRight,
  }[position];

  if (positionClass) {
    tooltip.classList.add(positionClass);
  }

  // Apply coordinates
  tooltip.style.top = `${coords.top}px`;
  tooltip.style.left = `${coords.left}px`;
}

/**
 * Clamp tooltip position to stay within viewport
 * Uses fixed positioning (viewport-relative)
 * @param {{ top: number, left: number }} coords
 * @param {HTMLElement} tooltip
 * @returns {{ top: number, left: number }}
 */
export function clampToViewport(coords, tooltip) {
  const tooltipRect = getRect(tooltip);
  const viewport = getViewport();
  const padding = 10;

  let { top, left } = coords;

  // Clamp horizontal (viewport-relative)
  const maxLeft = viewport.width - tooltipRect.width - padding;
  const minLeft = padding;
  left = Math.max(minLeft, Math.min(left, maxLeft));

  // Clamp vertical (viewport-relative)
  const maxTop = viewport.height - tooltipRect.height - padding;
  const minTop = padding;
  top = Math.max(minTop, Math.min(top, maxTop));

  return { top, left };
}
