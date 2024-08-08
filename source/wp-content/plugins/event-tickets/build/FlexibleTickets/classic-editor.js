/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "Wo0u");
/******/ })
/************************************************************************/
/******/ ({

/***/ "Wo0u":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: ./src/Tickets/Blocks/app/flexible-tickets/series-relationship.js
var series_relationship = __webpack_require__("YRH8");

// CONCATENATED MODULE: ./src/Tickets/Blocks/app/flexible-tickets/classic-editor/utils.js
/**
 * Run a callback when the DOM is ready.
 *
 * @param {Function} domReadyCallback The callback function to be called when the DOM is ready.
 */
const onReady = domReadyCallback => {
  if (document.readyState !== 'loading') {
    domReadyCallback();
  } else {
    document.addEventListener('DOMContentLoaded', domReadyCallback);
  }
};

// CONCATENATED MODULE: ./src/Tickets/Blocks/app/flexible-tickets/classic-editor/post-update-control.js



/**
 * Subscribe to Series relationship and ticket provider changes to lock/unlock the post publish button and
 * show/hide the notice.
 */
function init() {
  // Localized data is required to run this script.
  if (window.TECFtEditorData === undefined || window.TECFtEditorData.seriesRelationship === undefined || window.TECFtEditorData.classic === undefined) {
    return;
  }
  const {
    ticketPanelEditSelector,
    ticketPanelEditDefaultProviderAttribute,
    ticketsMetaboxSelector
  } = window.TECFtEditorData.classic;
  const ticketsMetabox = jQuery(ticketsMetaboxSelector);

  /**
   * Get the event ticket provider from the ticket panel attribute.
   *
   * @return {string} The event ticket provider.
   */
  function getEventProviderFromPanel() {
    return document.getElementById(ticketPanelEditSelector.substring(1)).getAttribute(ticketPanelEditDefaultProviderAttribute);
  }

  /**
   * Get the event title from the post title input.
   *
   * @return {string} The event title.
   */
  function getEventTitle() {
    return document.getElementById('title').value;
  }

  /**
   * Lock the post publish  and "Save Draft" buttons.
   */
  function lockPostPublish() {
    Array.from(document.querySelectorAll('#publish,#save-post')).forEach(el => el.disabled = true);
  }

  /**
   * Unlock the post publish and "Save Draft" buttons.
   */
  function unlockPostPublish() {
    Array.from(document.querySelectorAll('#publish,#save-post')).forEach(el => el.disabled = false);
  }

  /**
   * Toggle the publish lock based on the event and series providers.
   *
   * @param {string|null} eventProvider  The current event ticket provider.
   * @param {string|null} seriesProvider The current series ticket provider.
   * @param {string}      seriesTitle    Thte title of the currently selected series.
   */
  function togglePublishLock(eventProvider, seriesProvider, seriesTitle) {
    if (eventProvider === seriesProvider || eventProvider === null || seriesProvider === null) {
      unlockPostPublish();
      Object(series_relationship["h" /* removeDiscordantProviderNotice */])();
      return;
    }
    lockPostPublish();
    Object(series_relationship["i" /* showDiscordantProviderNotice */])(getEventTitle(), seriesTitle);
  }

  /**
   * Toggle the publish lock when the event ticket provider is changed in the ticket panel.
   */
  function onTicketProviderChange() {
    const seriesProvider = Object(series_relationship["d" /* getSeriesProviderFromSelection */])();
    const eventProvider = getEventProviderFromPanel();
    const seriesTitle = Object(series_relationship["f" /* getSeriesTitleFromSelection */])();
    togglePublishLock(eventProvider, seriesProvider, seriesTitle);
  }

  /**
   * Toggle the publish lock when the series is changed in the metabox dropdown.
   *
   * @param {Event} event The 'change' event dispatched by Select2.
   */
  function onSeriesChange(event) {
    const seriesProvider = Object(series_relationship["c" /* getSeriesProviderFromEvent */])(event);
    const eventProvider = getEventProviderFromPanel();
    const seriesTitle = Object(series_relationship["e" /* getSeriesTitleFromEvent */])(event);
    togglePublishLock(eventProvider, seriesProvider, seriesTitle);
  }

  /**
   * Subscribe to the event dispatched after any ticket panel is swapped.
   *
   * @param {Function} onChange The callback function to be called when the ticket panel is swapped.
   */
  function subscribeToTicketProviderChange(onChange) {
    ticketsMetabox.on('after_panel_swap.tickets', onChange);
  }
  Object(series_relationship["j" /* subscribeToSeriesChange */])(onSeriesChange);
  subscribeToTicketProviderChange(onTicketProviderChange);
}
onReady(init);
// CONCATENATED MODULE: ./src/Tickets/Blocks/app/flexible-tickets/classic-editor/tickets-on-recurring-control.js
var _window, _window$TECFtEditorDa, _window$TECFtEditorDa2, _window2, _window2$TECFtEditorD, _window2$TECFtEditorD2;


// The selectors that will be used to interact with the DOM.
const recurrenceRowSelector = '.recurrence-row';
const newRecurrenceRowSelector = '.recurrence-row.tribe-datetime-block:not(.tribe-recurrence-exclusion-row)';
const existingRecurrenceRowSelector = '.recurrence-row.tribe-recurrence-description,' + ' .recurrence-row.tribe-recurrence-exclusion-row';
const recurrenceNotSupportedRowSelector = '.recurrence-row.tec-events-pro-recurrence-not-supported';
const recurrenceControls = '.recurrence-container';
const recurrenceRule = '.recurrence-container .tribe-event-recurrence-rule';
const ticketTablesSelector = '.tribe-tickets-editor-table-tickets-body';
const rsvpTicketsSelector = ticketTablesSelector + ' [data-ticket-type="rsvp"]';
const defaultTicketsSelector = ticketTablesSelector + ' [data-ticket-type="default"]';
const ticketsMetaboxId = 'tribetickets';
const ticketWarningSelector = '.tec_ticket-panel__recurring-unsupported-warning';
const ticketControlsSelector = '#ticket_form_toggle, #rsvp_form_toggle, #settings_form_toggle, .tec_ticket-panel__helper_text__wrap';
const ticketEditPanelActiveSelector = '#tribe_panel_edit[aria-hidden="false"]';

// Init the control state from the localized data.
let state = {
  hasRecurrenceRules: ((_window = window) === null || _window === void 0 ? void 0 : (_window$TECFtEditorDa = _window.TECFtEditorData) === null || _window$TECFtEditorDa === void 0 ? void 0 : (_window$TECFtEditorDa2 = _window$TECFtEditorDa.event) === null || _window$TECFtEditorDa2 === void 0 ? void 0 : _window$TECFtEditorDa2.isRecurring) || false,
  hasOwnTickets: ((_window2 = window) === null || _window2 === void 0 ? void 0 : (_window2$TECFtEditorD = _window2.TECFtEditorData) === null || _window2$TECFtEditorD === void 0 ? void 0 : (_window2$TECFtEditorD2 = _window2$TECFtEditorD.event) === null || _window2$TECFtEditorD2 === void 0 ? void 0 : _window2$TECFtEditorD2.hasOwnTickets) || false
};

// Clone and keep track of the previous state.
let prevState = Object.assign({}, state);

/**
 * Update the state and call the callback if the state has changed.
 *
 * @since 5.8.0
 *
 * @param {Object} newState The updates to the state.
 */
function updateState(newState) {
  prevState = Object.assign({}, state);
  state = Object.assign({}, state, newState);
  if (prevState.hasRecurrenceRules === state.hasRecurrenceRules && prevState.hasOwnTickets === state.hasOwnTickets) {
    // No changes, do nothing.
    return;
  }
  handleControls(state);
}

/**
 * Hide the recurrence controls.
 *
 * The method will take care of hiding the recurrence controls and showing the recurrence not supported message.
 *
 * @since 5.8.0
 */
function hideRecurrenceControls() {
  document.querySelectorAll(recurrenceRowSelector).forEach(el => {
    el.style.display = 'none';
  });
  document.querySelectorAll(recurrenceNotSupportedRowSelector).forEach(el => {
    el.style.display = 'contents';
    el.style.visibility = 'visible';
  });
}

/**
 * Show the recurrence controls.
 *
 * The method will take care of showing the recurrence controls and hiding the recurrence not supported message.
 * If the Events has not recurrence rules, the method will show just the button to add recurrence rules.
 *
 * @since 5.8.0
 */
function showRecurrenceControls() {
  if (state.hasRecurrenceRules) {
    document.querySelectorAll(recurrenceRowSelector).forEach(el => {
      el.style.display = '';
    });
  } else {
    document.querySelectorAll(existingRecurrenceRowSelector).forEach(el => {
      el.style.display = 'none';
    });
    document.querySelectorAll(newRecurrenceRowSelector).forEach(el => {
      el.style.display = '';
    });
  }
  document.querySelectorAll(recurrenceNotSupportedRowSelector).forEach(el => {
    el.style.display = 'none';
  });
}

/**
 * Show the ticket controls.
 *
 * The method will take care of showing the ticket controls and hiding the ticket warning.
 *
 * @since 5.8.0
 */
function showTicketControls() {
  document.querySelectorAll(ticketWarningSelector).forEach(el => {
    el.style.display = 'none';
  });
  document.querySelectorAll(ticketControlsSelector).forEach(el => {
    el.style.display = '';
  });
}

/**
 * Hide the ticket controls.
 *
 * The method will take care of hiding the ticket controls and showing the ticket warning.
 *
 * @since 5.8.0
 */
function hideTicketControls() {
  document.querySelectorAll(ticketWarningSelector).forEach(el => {
    el.style.display = '';
  });
  document.querySelectorAll(ticketControlsSelector).forEach(el => {
    el.style.display = 'none';
  });
}

/**
 * Handle the controls visibility based on the state.
 *
 * @since 5.8.0
 *
 * @param {Object} newState The new state to hide/show controls based on.
 */
function handleControls(newState) {
  if (!newState.hasRecurrenceRules && !newState.hasOwnTickets) {
    // The potential state where both recurrence rules and tickets are still possible.
    showRecurrenceControls();
    showTicketControls();
    return;
  }
  if (newState.hasOwnTickets && newState.hasRecurrenceRules) {
    // This newState should not exist; we'll be conservative and hide everything.
    hideRecurrenceControls();
    hideTicketControls();
    return;
  }
  if (newState.hasOwnTickets) {
    // If an event has own tickets, it cannot have recurrence rules.
    hideRecurrenceControls();
    showTicketControls();
    return;
  }

  // Finally, if an event has recurrence rules, it cannot have own tickets.
  showRecurrenceControls();
  hideTicketControls();
}

// Initialize the controls visibility based on the initial state.
onReady(() => handleControls(state));

// Set up a mutation observer to detect when the recurrence rule is added or removed from the recurrence container.
const recurrenceControlsObserver = new MutationObserver(() => {
  const recurrenceRulesCount = document.querySelectorAll(recurrenceRule).length;
  updateState({
    hasRecurrenceRules: recurrenceRulesCount > 0
  });
});
recurrenceControlsObserver.observe(document.querySelector(recurrenceControls), {
  childList: true
});

/*
 * Set up a mutation observer to detect when tickets or RSVPs are added or removed from the tickets metabox.
 * Also: detect when the user is editing or creating a ticket.
 */
const ticketsObserver = new MutationObserver(() => {
  // Run the DOM queries only if required.
  const hasOwnTickets = document.querySelectorAll(rsvpTicketsSelector).length ||
  // Has RSVP tickets or...
  document.querySelectorAll(defaultTicketsSelector).length ||
  // ...has default tickets or...
  document.querySelectorAll(ticketEditPanelActiveSelector).length; // ...is editing a ticket.
  updateState({
    hasOwnTickets
  });
});
ticketsObserver.observe(document.getElementById(ticketsMetaboxId), {
  childList: true,
  subtree: true,
  attributes: true
});
// CONCATENATED MODULE: ./src/Tickets/Blocks/app/flexible-tickets/classic-editor/index.js



/***/ }),

/***/ "YRH8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export getSeriesDataFromElement */
/* unused harmony export getSeriesDataFromEvent */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return getSeriesTitleFromEvent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return getSeriesProviderFromEvent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return getSeriesProviderFromSelection; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return getSeriesTitleFromSelection; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return getSeriesEditLinkFromMetaBox; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "j", function() { return subscribeToSeriesChange; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "h", function() { return removeDiscordantProviderNotice; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "i", function() { return showDiscordantProviderNotice; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "g", function() { return hasSelectedSeries; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return getSeriesPostIdFromSelection; });
const {
  fieldSelector,
  containerSelector,
  differentProviderNoticeSelector,
  differentProviderNoticeTemplate
} = window.TECFtEditorData.seriesRelationship;
const noticeSelector = containerSelector + ' ' + differentProviderNoticeSelector;

/**
 * Get the series data from the metabox dropdown element's value attribute.
 *
 * @since 5.8.0
 *
 * @param {Element|null} element The metabox dropdown element.
 * @param {string}       key     The key of the series data to retrieve.
 *
 * @return {string|null} The series data read from the element's value attribute, `null` if not found.
 */
function getSeriesDataFromElement(element, key) {
  if (!(element && element.value)) {
    return null;
  }
  const seriesJsonData = element.value;
  try {
    return JSON.parse(seriesJsonData)[key] || null;
  } catch (e) {
    return null;
  }
}

/**
 * Get the series data from the `change` event dispatched by Select2 when the series is changed
 *
 * @since 5.8.0
 *
 * @param {Event}  event The `change` event dispatched by Select2.
 * @param {string} key   The key of the series data to retrieve.
 *
 * @return {string|null} The series data read from the selected option data, `null` if not found.
 */
function getSeriesDataFromEvent(event, key) {
  if (!event.currentTarget) {
    return null;
  }
  return getSeriesDataFromElement(event.currentTarget, key);
}

/**
 * Get the series title from the `change` event dispatched by Select2 when the series is changed
 * by the user in the metabox dropdown.
 *
 * @since 5.8.0
 *
 * @param {Event} event The `change` event dispatched by Select2.
 *
 * @return {string} The title of the series read from the selected option data.
 */
function getSeriesTitleFromEvent(event) {
  return getSeriesDataFromEvent(event, 'title') || '';
}

/**
 * Get the series ticket provider from the `change` event dispatched by Select2 when the series is changed
 *
 * @since 5.8.0
 *
 * @param {Event} event The `change` event dispatched by Select2.
 *
 * @return {string|null} The ticket provider of the series read from the selected option data, `null` if not found.
 */
function getSeriesProviderFromEvent(event) {
  return getSeriesDataFromEvent(event, 'ticket_provider');
}

/**
 * Get the series ticket provider from the currently selected series in the metabox dropdown.
 *
 * @since 5.8.0
 *
 * @return {string|null} The ticket provider of the series read from the selected option data, `null` if not found.
 */
function getSeriesProviderFromSelection() {
  const seriesSelect = document.getElementById(fieldSelector.substring(1));
  return getSeriesDataFromElement(seriesSelect, 'ticket_provider');
}

/**
 * Get the series title from the currently selected series in the metabox dropdown.
 *
 * @since 5.8.0
 *
 * @return {string|null} The title of the series read from the selected option data, `null` if not found.
 */
function getSeriesTitleFromSelection() {
  const seriesSelect = document.getElementById(fieldSelector.substring(1));
  return getSeriesDataFromElement(seriesSelect, 'title');
}

/**
 * Get the series edit link from the metabox dropdown.
 *
 * @since 5.8.0
 *
 * @param {string|null} append The string to append to the edit link.
 *
 * @return {string } The edit link of the series read from the selected option data, or an empty string if not found.
 */
function getSeriesEditLinkFromMetaBox() {
  let append = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '#tribetickets';
  const editLinkElement = document.querySelector(containerSelector + ' a.tec-events-pro-series__edit-link');
  const editLink = (editLinkElement === null || editLinkElement === void 0 ? void 0 : editLinkElement.getAttribute('href')) || '';
  return editLink + (append ? append : '');
}

/**
 * Subscribe to the series change event.
 *
 * @since 5.8.0
 *
 * This is the event triggered by the user selecting a series in the metabox dropdown.
 *
 * @param {Function} onChange The callback function to be called when the series is changed.
 */
function subscribeToSeriesChange(onChange) {
  jQuery(fieldSelector).on('change', onChange);
}

/**
 * Remove the notice that the event and series have different ticket providers.
 *
 * @since 5.8.0
 */
function removeDiscordantProviderNotice() {
  Array.from(document.querySelectorAll(noticeSelector)).map(el => el.remove(true));
}

/**
 * Show a notice that the event and series have different ticket providers.
 *
 * @since 5.8.0
 *
 * @param {string} eventTitle  The title of the event.
 * @param {string} seriesTitle The title of the series.
 */
function showDiscordantProviderNotice(eventTitle, seriesTitle) {
  removeDiscordantProviderNotice();
  const noticeElement = document.createElement('div');
  noticeElement.classList.add(differentProviderNoticeSelector.substring(1));
  noticeElement.style['margin-top'] = 'var(--tec-spacer-1)';
  noticeElement.textContent = differentProviderNoticeTemplate.replace('%1$s', eventTitle).replace('%2$s', seriesTitle);
  document.querySelector(containerSelector).append(noticeElement);
}

/**
 * Check if the user has selected a series in the metabox dropdown.
 *
 * @since 5.8.0
 *
 * @return {boolean}
 */
function hasSelectedSeries() {
  const seriesSelect = document.getElementById(fieldSelector.substring(1));
  return (seriesSelect === null || seriesSelect === void 0 ? void 0 : seriesSelect.value) !== '' && (seriesSelect === null || seriesSelect === void 0 ? void 0 : seriesSelect.value) !== '-1';
}

/**
 * Get the post ID of the currently selected series in the metabox dropdown.
 *
 * @since 5.8.0
 *
 * @return {number|null} The post ID of the selected series, `null` if not found.
 */
function getSeriesPostIdFromSelection() {
  const seriesSelect = document.getElementById(fieldSelector.substring(1));
  return getSeriesDataFromElement(seriesSelect, 'id');
}

/***/ })

/******/ });