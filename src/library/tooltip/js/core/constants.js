// Tooltip Constants
// ==================
// Positions, triggers, and default configuration

export const POSITIONS = {
  TOP: 'top',
  BOTTOM: 'bottom',
  LEFT: 'left',
  RIGHT: 'right',
};

export const TRIGGERS = {
  HOVER: 'hover',
  FOCUS: 'focus',
  CLICK: 'click',
};

export const DEFAULTS = {
  position: POSITIONS.TOP,
  trigger: TRIGGERS.HOVER,
  delay: {
    show: 0,
    hide: 0,
  },
  offset: 8,
  animation: true,
  animationDuration: 200,
  arrow: true,
  interactive: false,
  html: false, // Allow HTML content (use with caution - XSS risk if content is user-provided)
  shortcut: null, // Keyboard shortcut to display (e.g., "Ctrl+S")
  appendTo: null, // Will default to document.body at runtime
};

export const CLASSES = {
  tooltip: 'tooltip',
  arrow: 'tooltip__arrow',
  text: 'tooltip__text',
  shortcut: 'tooltip__shortcut',
  visible: 'tooltip--visible',
  interactive: 'tooltip--interactive',
  positionTop: 'tooltip--top',
  positionBottom: 'tooltip--bottom',
  positionLeft: 'tooltip--left',
  positionRight: 'tooltip--right',
};

export const DATA_ATTRIBUTES = {
  tooltip: 'data-tooltip',
  position: 'data-tooltip-position',
  trigger: 'data-tooltip-trigger',
  delay: 'data-tooltip-delay',
  interactive: 'data-tooltip-interactive',
  html: 'data-tooltip-html',
  shortcut: 'data-tooltip-shortcut',
};
