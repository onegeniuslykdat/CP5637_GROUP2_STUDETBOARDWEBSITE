/******/ (function(modules) { // webpackBootstrap
/******/ 	// install a JSONP callback for chunk loading
/******/ 	function webpackJsonpCallback(data) {
/******/ 		var chunkIds = data[0];
/******/ 		var moreModules = data[1];
/******/ 		var executeModules = data[2];
/******/
/******/ 		// add "moreModules" to the modules object,
/******/ 		// then flag all "chunkIds" as loaded and fire callback
/******/ 		var moduleId, chunkId, i = 0, resolves = [];
/******/ 		for(;i < chunkIds.length; i++) {
/******/ 			chunkId = chunkIds[i];
/******/ 			if(Object.prototype.hasOwnProperty.call(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 				resolves.push(installedChunks[chunkId][0]);
/******/ 			}
/******/ 			installedChunks[chunkId] = 0;
/******/ 		}
/******/ 		for(moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				modules[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(parentJsonpFunction) parentJsonpFunction(data);
/******/
/******/ 		while(resolves.length) {
/******/ 			resolves.shift()();
/******/ 		}
/******/
/******/ 		// add entry modules from loaded chunk to deferred list
/******/ 		deferredModules.push.apply(deferredModules, executeModules || []);
/******/
/******/ 		// run deferred modules when all chunks ready
/******/ 		return checkDeferredModules();
/******/ 	};
/******/ 	function checkDeferredModules() {
/******/ 		var result;
/******/ 		for(var i = 0; i < deferredModules.length; i++) {
/******/ 			var deferredModule = deferredModules[i];
/******/ 			var fulfilled = true;
/******/ 			for(var j = 1; j < deferredModule.length; j++) {
/******/ 				var depId = deferredModule[j];
/******/ 				if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 			}
/******/ 			if(fulfilled) {
/******/ 				deferredModules.splice(i--, 1);
/******/ 				result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 			}
/******/ 		}
/******/
/******/ 		return result;
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// object to store loaded and loading chunks
/******/ 	// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 	// Promise = chunk loading, 0 = chunk loaded
/******/ 	var installedChunks = {
/******/ 		3: 0
/******/ 	};
/******/
/******/ 	var deferredModules = [];
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
/******/ 	var jsonpArray = window["webpackJsonp"] = window["webpackJsonp"] || [];
/******/ 	var oldJsonpFunction = jsonpArray.push.bind(jsonpArray);
/******/ 	jsonpArray.push = webpackJsonpCallback;
/******/ 	jsonpArray = jsonpArray.slice();
/******/ 	for(var i = 0; i < jsonpArray.length; i++) webpackJsonpCallback(jsonpArray[i]);
/******/ 	var parentJsonpFunction = oldJsonpFunction;
/******/
/******/
/******/ 	// add entry module to deferred list
/******/ 	deferredModules.push(["YhH6",0]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "+9iv":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "/vq8":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "1HDl":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

module.exports = wp.data;

/***/ }),

/***/ "1fKG":
/***/ (function(module, exports) {

module.exports = tribe.modules.reduxSaga;

/***/ }),

/***/ "2LK8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* reexport */ LAYOUT; });

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__("cDcd");

// EXTERNAL MODULE: external "tribe.modules.propTypes"
var external_tribe_modules_propTypes_ = __webpack_require__("rf6O");
var external_tribe_modules_propTypes_default = /*#__PURE__*/__webpack_require__.n(external_tribe_modules_propTypes_);

// EXTERNAL MODULE: external "tribe.modules.classnames"
var external_tribe_modules_classnames_ = __webpack_require__("K2gz");
var external_tribe_modules_classnames_default = /*#__PURE__*/__webpack_require__.n(external_tribe_modules_classnames_);

// EXTERNAL MODULE: ./src/modules/elements/container-panel/style.pcss
var style = __webpack_require__("+9iv");

// CONCATENATED MODULE: ./src/modules/elements/container-panel/element.js
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */

const LAYOUT = {
  rsvp: 'rsvp',
  ticket: 'ticket'
};
const ContainerPanel = _ref => {
  let {
    className,
    content,
    header,
    layout
  } = _ref;
  const headerAndContent = wp.element.createElement(external_React_["Fragment"], null, wp.element.createElement("div", {
    className: "tribe-editor__container-panel__header"
  }, header), content && wp.element.createElement("div", {
    className: "tribe-editor__container-panel__content"
  }, content));
  const getHeaderAndContent = () => layout === LAYOUT.ticket ? headerAndContent : wp.element.createElement("div", {
    className: "tribe-editor__container-panel__header-content-wrapper"
  }, headerAndContent);
  return wp.element.createElement("div", {
    className: external_tribe_modules_classnames_default()('tribe-editor__container-panel', `tribe-editor__container-panel--${layout}`, className)
  }, getHeaderAndContent());
};
ContainerPanel.propTypes = {
  className: external_tribe_modules_propTypes_default.a.string,
  content: external_tribe_modules_propTypes_default.a.node,
  header: external_tribe_modules_propTypes_default.a.node,
  layout: external_tribe_modules_propTypes_default.a.oneOf(Object.keys(LAYOUT)).isRequired
};
/* harmony default export */ var container_panel_element = (ContainerPanel);
// CONCATENATED MODULE: ./src/modules/elements/container-panel/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var container_panel = __webpack_exports__["b"] = (container_panel_element);


/***/ }),

/***/ "2TDg":
/***/ (function(module, exports) {

module.exports = lodash.omit;

/***/ }),

/***/ "45Xz":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "4Qn9":
/***/ (function(module, exports) {

module.exports = lodash.isEmpty;

/***/ }),

/***/ "4Uf/":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "m", function() { return SET_MODAL_DATA; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "l", function() { return RESET_MODAL_DATA; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "o", function() { return SUBMIT_MODAL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "h", function() { return INITIALIZE_MODAL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "n", function() { return SHOW_MODAL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "g", function() { return HIDE_MODAL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return FETCH_POST_TYPES; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return FETCH_POST_TYPES_SUCCESS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return FETCH_POST_TYPES_ERROR; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return FETCH_POST_CHOICES; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return FETCH_POST_CHOICES_SUCCESS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return FETCH_POST_CHOICES_ERROR; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "i", function() { return MOVE_TICKET; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "k", function() { return MOVE_TICKET_SUCCESS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "j", function() { return MOVE_TICKET_ERROR; });
/* harmony import */ var _moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("BNqv");
/**
 * Internal dependencies
 */


//
// ─── MODAL DATA ─────────────────────────────────────────────────────────────────
//

const SET_MODAL_DATA = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_MODAL_DATA`;
const RESET_MODAL_DATA = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/RESET_MODAL_DATA`;
const SUBMIT_MODAL = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SUBMIT_MODAL`;

//
// ─── MODAL UI STATE ─────────────────────────────────────────────────────────────
//

const INITIALIZE_MODAL = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/INITIALIZE_MODAL`;
const SHOW_MODAL = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SHOW_MODAL`;
const HIDE_MODAL = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/HIDE_MODAL`;

//
// ─── API ───────────────────────────────────────────────────────────────────────-
//

const FETCH_POST_TYPES = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/FETCH_POST_TYPES`;
const FETCH_POST_TYPES_SUCCESS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/FETCH_POST_TYPES_SUCCESS`;
const FETCH_POST_TYPES_ERROR = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/FETCH_POST_TYPES_ERROR`;
const FETCH_POST_CHOICES = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/FETCH_POST_CHOICES`;
const FETCH_POST_CHOICES_SUCCESS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/FETCH_POST_CHOICES_SUCCESS`;
const FETCH_POST_CHOICES_ERROR = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/FETCH_POST_CHOICES_ERROR`;
const MOVE_TICKET = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/MOVE_TICKET`;
const MOVE_TICKET_SUCCESS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/MOVE_TICKET_SUCCESS`;
const MOVE_TICKET_ERROR = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/MOVE_TICKET_ERROR`;

/***/ }),

/***/ "4oMP":
/***/ (function(module, exports) {

module.exports = lodash.isObject;

/***/ }),

/***/ "56gU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return DEFAULT_STATE; });
/* harmony import */ var _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("n+fr");
/**
 * Internal dependencies
 */


/**
 * Full payload from gutenberg media upload is not used,
 * only id, alt, and src are used for this specific case.
 */
const DEFAULT_STATE = {
  id: 0,
  src: '',
  alt: ''
};
/* harmony default export */ __webpack_exports__["b"] = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_HEADER_IMAGE:
      return {
        id: action.payload.id,
        src: action.payload.src,
        alt: action.payload.alt
      };
    default:
      return state;
  }
});

/***/ }),

/***/ "68xo":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "6OzC":
/***/ (function(module, exports) {

module.exports = lodash.find;

/***/ }),

/***/ "6Ugf":
/***/ (function(module, exports) {

module.exports = tribe.common.elements;

/***/ }),

/***/ "6lOv":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "8C5M":
/***/ (function(module, exports) {

module.exports = tribe.common.utils.recurrence;

/***/ }),

/***/ "9lL/":
/***/ (function(module, exports) {

module.exports = tribe.common.data.plugins;

/***/ }),

/***/ "9mgW":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "AuWn":
/***/ (function(module, exports) {

module.exports = tribe.modules.reactInputAutosize;

/***/ }),

/***/ "B8vQ":
/***/ (function(module, exports) {

module.exports = tribe.common.utils;

/***/ }),

/***/ "BNqv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "n", function() { return PREFIX_TICKETS_STORE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "o", function() { return RSVP_POST_TYPE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return KEY_RSVP_FOR_EVENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "k", function() { return KEY_TICKET_SHOW_DESCRIPTION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return KEY_PRICE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return KEY_TICKET_CAPACITY; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "m", function() { return KEY_TICKET_START_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return KEY_TICKET_END_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "l", function() { return KEY_TICKET_SHOW_NOT_GOING; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "i", function() { return KEY_TICKET_HEADER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return KEY_TICKET_DEFAULT_PROVIDER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return KEY_TICKETS_LIST; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "g", function() { return KEY_TICKET_GOING_COUNT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "j", function() { return KEY_TICKET_NOT_GOING_COUNT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "h", function() { return KEY_TICKET_HAS_ATTENDEE_INFO_FIELDS; });
const PREFIX_TICKETS_STORE = '@@MT/TICKETS';
const RSVP_POST_TYPE = 'tribe_rsvp_tickets';

/**
 * @todo: these are expected to change based on BE changes
 */
const KEY_RSVP_FOR_EVENT = '_tribe_rsvp_for_event';
const KEY_TICKET_SHOW_DESCRIPTION = '_tribe_ticket_show_description';
const KEY_PRICE = '_price';
const KEY_TICKET_CAPACITY = '_tribe_ticket_capacity';
const KEY_TICKET_START_DATE = '_ticket_start_date';
const KEY_TICKET_END_DATE = '_ticket_end_date';
const KEY_TICKET_SHOW_NOT_GOING = '_tribe_ticket_show_not_going';
const KEY_TICKET_HEADER = '_tribe_ticket_header';
const KEY_TICKET_DEFAULT_PROVIDER = '_tribe_default_ticket_provider';
const KEY_TICKETS_LIST = '_tribe_tickets_list';
const KEY_TICKET_GOING_COUNT = '_tribe_ticket_going_count';
const KEY_TICKET_NOT_GOING_COUNT = '_tribe_ticket_not_going_count';
const KEY_TICKET_HAS_ATTENDEE_INFO_FIELDS = '_tribe_ticket_has_attendee_info_fields';

/***/ }),

/***/ "DOwB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TC", function() { return TC; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "EDD", function() { return EDD; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "WOO", function() { return WOO; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "RSVP", function() { return RSVP; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "RSVP_CLASS", function() { return RSVP_CLASS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TICKETS_COMMERCE_MODULE_CLASS", function() { return TICKETS_COMMERCE_MODULE_CLASS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TC_CLASS", function() { return TC_CLASS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "EDD_CLASS", function() { return EDD_CLASS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "WOO_CLASS", function() { return WOO_CLASS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PROVIDER_CLASS_TO_PROVIDER_MAPPING", function() { return PROVIDER_CLASS_TO_PROVIDER_MAPPING; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PROVIDER_TYPES", function() { return PROVIDER_TYPES; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "EDD_ORDERS", function() { return EDD_ORDERS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TC_ORDERS", function() { return TC_ORDERS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "WOO_ORDERS", function() { return WOO_ORDERS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TICKETS_COMMERCE_ORDERS", function() { return TICKETS_COMMERCE_ORDERS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TICKET_ORDERS_PAGE_SLUG", function() { return TICKET_ORDERS_PAGE_SLUG; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "UNLIMITED", function() { return UNLIMITED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SHARED", function() { return SHARED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "GLOBAL", function() { return GLOBAL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "INDEPENDENT", function() { return INDEPENDENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "CAPPED", function() { return CAPPED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "OWN", function() { return OWN; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TICKET_TYPES_VALUES", function() { return TICKET_TYPES_VALUES; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TICKET_TYPES", function() { return TICKET_TYPES; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PREFIX", function() { return PREFIX; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SUFFIX", function() { return SUFFIX; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PRICE_POSITIONS", function() { return PRICE_POSITIONS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TICKET_LABELS", function() { return TICKET_LABELS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SALE_PRICE_LABELS", function() { return SALE_PRICE_LABELS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "IS_FREE_TC_TICKET_ALLOWED", function() { return IS_FREE_TC_TICKET_ALLOWED; });
var _window, _window$tribe_editor_, _window$tribe_editor_2, _window2, _window2$tribe_editor, _window2$tribe_editor2, _window3, _window3$tribe_editor, _window3$tribe_editor2, _window3$tribe_editor3;
const TC = 'tribe-commerce';
const EDD = 'edd';
const WOO = 'woo';
const RSVP = 'rsvp';
const RSVP_CLASS = 'Tribe__Tickets__RSVP';
const TICKETS_COMMERCE_MODULE_CLASS = 'TEC\\Tickets\\Commerce\\Module';
const TC_CLASS = 'Tribe__Tickets__Commerce__PayPal__Main';
const EDD_CLASS = 'Tribe__Tickets_Plus__Commerce__EDD__Main';
const WOO_CLASS = 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main';
const PROVIDER_CLASS_TO_PROVIDER_MAPPING = {
  [TC_CLASS]: TC,
  [EDD_CLASS]: EDD,
  [WOO_CLASS]: WOO
};
const PROVIDER_TYPES = [TC, EDD, WOO];
const EDD_ORDERS = 'edd-orders';
const TC_ORDERS = 'tpp-orders';
const WOO_ORDERS = 'tickets-orders';
const TICKETS_COMMERCE_ORDERS = 'tickets-commerce-orders';
const TICKET_ORDERS_PAGE_SLUG = {
  [EDD_CLASS]: EDD_ORDERS,
  [TC_CLASS]: TC_ORDERS,
  [WOO_CLASS]: WOO_ORDERS,
  [TICKETS_COMMERCE_MODULE_CLASS]: TICKETS_COMMERCE_ORDERS
};
const UNLIMITED = 'unlimited';
const SHARED = 'shared';
const GLOBAL = 'global'; // The name used by the backend to indicate shared capacity.
const INDEPENDENT = 'independent';
const CAPPED = 'capped';
const OWN = 'own';
const TICKET_TYPES_VALUES = [UNLIMITED, CAPPED, OWN];
const TICKET_TYPES = {
  [UNLIMITED]: UNLIMITED,
  [SHARED]: CAPPED,
  [INDEPENDENT]: OWN
};
const PREFIX = 'prefix';
const SUFFIX = 'postfix';
const PRICE_POSITIONS = [PREFIX, SUFFIX];

// eslint-disable-next-line no-undef
const TICKET_LABELS = (_window = window) === null || _window === void 0 ? void 0 : (_window$tribe_editor_ = _window.tribe_editor_config) === null || _window$tribe_editor_ === void 0 ? void 0 : (_window$tribe_editor_2 = _window$tribe_editor_.tickets) === null || _window$tribe_editor_2 === void 0 ? void 0 : _window$tribe_editor_2.ticketLabels;
const SALE_PRICE_LABELS = (_window2 = window) === null || _window2 === void 0 ? void 0 : (_window2$tribe_editor = _window2.tribe_editor_config) === null || _window2$tribe_editor === void 0 ? void 0 : (_window2$tribe_editor2 = _window2$tribe_editor.tickets) === null || _window2$tribe_editor2 === void 0 ? void 0 : _window2$tribe_editor2.salePrice;

// eslint-disable-next-line max-len
const IS_FREE_TC_TICKET_ALLOWED = (_window3 = window) === null || _window3 === void 0 ? void 0 : (_window3$tribe_editor = _window3.tribe_editor_config) === null || _window3$tribe_editor === void 0 ? void 0 : (_window3$tribe_editor2 = _window3$tribe_editor.tickets) === null || _window3$tribe_editor2 === void 0 ? void 0 : (_window3$tribe_editor3 = _window3$tribe_editor2.commerce) === null || _window3$tribe_editor3 === void 0 ? void 0 : _window3$tribe_editor3.isFreeTicketAllowed;

/***/ }),

/***/ "EiNN":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "Etll":
/***/ (function(module, exports) {

module.exports = lodash.includes;

/***/ }),

/***/ "HAtF":
/***/ (function(module, exports) {

module.exports = lodash.keys;

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

module.exports = wp.blocks;

/***/ }),

/***/ "In0u":
/***/ (function(module, exports) {

module.exports = lodash.noop;

/***/ }),

/***/ "IwAG":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "J/pT":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "K2gz":
/***/ (function(module, exports) {

module.exports = tribe.modules.classnames;

/***/ }),

/***/ "LHhe":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "MWqi":
/***/ (function(module, exports) {

module.exports = tribe.modules.reselect;

/***/ }),

/***/ "NxMS":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "ClockActive", function() { return /* reexport */ clock; });
__webpack_require__.d(__webpack_exports__, "ClockInactive", function() { return /* reexport */ inactive_clock; });
__webpack_require__.d(__webpack_exports__, "ARF", function() { return /* reexport */ attendee_registration; });
__webpack_require__.d(__webpack_exports__, "SaleWindow", function() { return /* reexport */ sale_window; });
__webpack_require__.d(__webpack_exports__, "Tickets", function() { return /* reexport */ tickets; });
__webpack_require__.d(__webpack_exports__, "TicketActive", function() { return /* reexport */ ticket; });
__webpack_require__.d(__webpack_exports__, "TicketInactive", function() { return /* reexport */ inactive_ticket; });
__webpack_require__.d(__webpack_exports__, "RSVP", function() { return /* reexport */ rsvp; });
__webpack_require__.d(__webpack_exports__, "RSVPActive", function() { return /* reexport */ active_rsvp; });
__webpack_require__.d(__webpack_exports__, "RSVPInactive", function() { return /* reexport */ inactive_rsvp; });
__webpack_require__.d(__webpack_exports__, "AttendeesGravatar", function() { return /* reexport */ gravatar; });
__webpack_require__.d(__webpack_exports__, "Attendees", function() { return /* reexport */ attendees; });
__webpack_require__.d(__webpack_exports__, "Orders", function() { return /* reexport */ orders; });
__webpack_require__.d(__webpack_exports__, "Settings", function() { return /* reexport */ settings; });
__webpack_require__.d(__webpack_exports__, "Close", function() { return /* reexport */ icons_close; });
__webpack_require__.d(__webpack_exports__, "Ticket", function() { return /* reexport */ ticket_left; });
__webpack_require__.d(__webpack_exports__, "ECP", function() { return /* reexport */ ecp; });
__webpack_require__.d(__webpack_exports__, "Bulb", function() { return /* reexport */ bulb; });

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__("cDcd");
var external_React_default = /*#__PURE__*/__webpack_require__.n(external_React_);

// CONCATENATED MODULE: ./src/modules/icons/active/clock.svg
var _extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function _objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var clock = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = _objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", _extends({
    width: "60",
    height: "60",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("g", {
    fill: "none",
    fillRule: "evenodd"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M47.043 31.028c0 9.647-7.821 17.47-17.47 17.47-9.647 0-17.468-7.823-17.468-17.47 0-9.648 7.82-17.469 17.469-17.469 9.648 0 17.469 7.821 17.469 17.47",
    fill: "#FEFEFE"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M14.584 37.434c-2.236-.79-5.979-23.562 15.244-23.562 21.215 0 16.507 20.48 15.298 23.666 2.021-3.833-.896-18.888-15.298-18.888-14.382 0-16.39 13.972-15.244 18.784",
    fill: "#E6E6E6"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M46.01 31.241c0 8.937-7.244 16.182-16.182 16.182-8.936 0-16.181-7.245-16.181-16.182 0-8.937 7.245-16.182 16.18-16.182 8.939 0 16.183 7.245 16.183 16.182zm-.043-10.562c1.613-1.614 1.613-4.168 0-5.648-1.614-1.48-4.168-1.614-5.648 0l-.404.403c-1.884-1.211-3.901-2.017-6.051-2.554V9.16c0-.641-.52-1.16-1.161-1.16h-5.75c-.64 0-1.16.519-1.16 1.16v3.586c-9.04 2.01-15.631 10.448-14.706 20.256.815 8.656 7.689 15.749 16.314 16.843 11.426 1.452 21.256-7.518 21.256-18.673-.133-3.768-1.21-7.265-3.093-10.09l.403-.403z",
    fill: "#444"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M40.506 35.965l-9.578-5.257v-9.852a1.1 1.1 0 0 0-2.2 0v11.19l.57.279 10.149 5.57a1.107 1.107 0 0 0 1.495-.435l.01-.023a1.102 1.102 0 0 0-.446-1.472",
    fill: "#039ED3"
  })));
});
// CONCATENATED MODULE: ./src/modules/icons/inactive/clock.svg
var clock_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function clock_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var inactive_clock = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = clock_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", clock_extends({
    width: "60",
    height: "60",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("g", {
    fill: "none",
    fillRule: "evenodd"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M47.043 31.028c0 9.647-7.821 17.47-17.47 17.47-9.647 0-17.468-7.823-17.468-17.47 0-9.648 7.82-17.469 17.469-17.469 9.648 0 17.469 7.821 17.469 17.47",
    fill: "#FEFEFE"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M46.01 31.241c0 8.937-7.244 16.182-16.182 16.182-8.936 0-16.181-7.245-16.181-16.182 0-8.937 7.245-16.182 16.18-16.182 8.939 0 16.183 7.245 16.183 16.182zm-.043-10.562c1.613-1.614 1.613-4.168 0-5.648-1.614-1.48-4.168-1.614-5.648 0l-.404.403c-1.884-1.211-3.901-2.017-6.051-2.554V9.16c0-.641-.52-1.16-1.161-1.16h-5.75c-.64 0-1.16.519-1.16 1.16v3.586c-9.04 2.01-15.631 10.448-14.706 20.256.815 8.656 7.689 15.749 16.314 16.843 11.426 1.452 21.256-7.518 21.256-18.673-.133-3.768-1.21-7.265-3.093-10.09l.403-.403z",
    fill: "#AEB4BB"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M40.506 35.965l-9.578-5.257v-9.852a1.1 1.1 0 0 0-2.2 0v11.19l.57.279 10.149 5.57a1.107 1.107 0 0 0 1.495-.435l.01-.023a1.102 1.102 0 0 0-.446-1.472",
    fill: "#AEB4BB"
  })));
});
// CONCATENATED MODULE: ./src/modules/icons/attendee-registration.svg
var attendee_registration_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function attendee_registration_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var attendee_registration = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = attendee_registration_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", attendee_registration_extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: "17",
    height: "16",
    viewBox: "0 0 17 16",
    fill: "none"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M9.7 1.6v-.533C9.7.477 9.165 0 8.5 0c-.666 0-1.2.476-1.2 1.067V1.6c-.666 0-1.2.476-1.2 1.067V3.2h4.8v-.54c0-.584-.535-1.06-1.2-1.06z",
    fill: "#727272"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M5.194 1.6H3.3v12.8h10.4V1.6h-1.923M6.1 6.8h4.8M6.1 10.832h4.8",
    stroke: "#727272",
    strokeWidth: "1.5",
    strokeMiterlimit: "10",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/sale-window.svg
var sale_window_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function sale_window_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var sale_window = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = sale_window_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", sale_window_extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M8 14.5a6.5 6.5 0 1 0 0-13 6.5 6.5 0 0 0 0 13z",
    stroke: "#727272",
    strokeWidth: "1.5",
    strokeMiterlimit: "10"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M8 4v4.5l2.5 2",
    stroke: "#727272",
    strokeWidth: "1.5",
    strokeMiterlimit: "10",
    strokeLinecap: "round"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/tickets.svg
var tickets_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function tickets_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var tickets = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = tickets_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", tickets_extends({
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 47.85 38.44"
  }, props), /*#__PURE__*/external_React_default.a.createElement("defs", null), /*#__PURE__*/external_React_default.a.createElement("title", null, "block-icon-tickets"), /*#__PURE__*/external_React_default.a.createElement("g", {
    id: "Layer_2",
    "data-name": "Layer 2"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    className: styles["cls-1"] || "cls-1",
    d: "M47.15 24.14a3.66 3.66 0 0 1-1.71 0 3.7 3.7 0 0 1 .33-7.25l-.71-3.68A3.69 3.69 0 0 1 43.67 6l-.49-2.55a4.22 4.22 0 0 0-5-3.33l-34.8 7a4.23 4.23 0 0 0-3.3 4.93l.44 2.35a3.66 3.66 0 0 1 1.81 0 3.69 3.69 0 0 1-.43 7.25l.72 3.7A3.69 3.69 0 1 1 4 32.57l.62 3.3a3.18 3.18 0 0 0 3.71 2.51l36.95-7.15a3.18 3.18 0 0 0 2.51-3.71zm-31.46-9.86l14.18-2.82.81 3.86-14.17 2.81zm2.46 13l-.8-3.94 14.18-2.82.8 3.94z",
    id: "Layer_1-2",
    "data-name": "Layer 1"
  })));
});
// CONCATENATED MODULE: ./src/modules/icons/active/ticket.svg
var ticket_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function ticket_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var ticket = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = ticket_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", ticket_extends({
    width: "60",
    height: "60",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("g", {
    fill: "none",
    fillRule: "evenodd"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M10.238 43.288l.98 5.341c.098.538.6.894 1.123.8l38.995-6.636c.713-.13 1.223-.782 1.2-1.528l-1.132-5.123-38.54 10.777-2.39-3.977-.236.346z",
    fill: "#E6E6E6"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    fill: "#FEFEFE",
    d: "M6.161 24.997l1.583 5.417 1.792 1.375-.375 3.333.375 2.625 2.041 2-.583 3.292 1.25 4.833 41.292-12.417L52.37 30.1l-2.25-1.42-.375-1.933 1.083-2.333-.416-1.625-1.667-1.042-1.208-2.417 1.041-2-.916-4.708z"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M14.267 31.31l-.434-1.434a.25.25 0 0 1 .167-.311l28.5-8.614a.251.251 0 0 1 .312.167l.434 1.436a.25.25 0 0 1-.167.312l-28.5 8.613a.251.251 0 0 1-.312-.168m2.393 7.739l-.435-1.435a.25.25 0 0 1 .167-.311l28.5-8.613a.25.25 0 0 1 .312.167l.434 1.435a.25.25 0 0 1-.167.312l-28.5 8.613a.251.251 0 0 1-.312-.168",
    fill: "#039ED3"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M12.812 46.805l-.061-.23-.237-.91-.66-2.528a.416.416 0 0 1-.01-.061.305.305 0 0 1 .039-.186c.074-.112.115-.174.153-.237a4.164 4.164 0 0 0 .586-2.292 4.13 4.13 0 0 0-.27-1.297c-.367-.956-1.066-1.7-1.971-2.095a.307.307 0 0 1-.181-.21l-.32-1.223a.347.347 0 0 1 .054-.287 4.097 4.097 0 0 0 .438-3.777c-.37-.958-1.07-1.702-1.971-2.095a.314.314 0 0 1-.18-.206l-.877-3.372a8.538 8.538 0 0 0-.085-.292l.294-.1 39.046-11.925.28-.08.082.3.81 3.112a.336.336 0 0 1-.065.298 4.01 4.01 0 0 0-.861 1.622 4.065 4.065 0 0 0 .329 2.939c.476.896 1.25 1.537 2.186 1.808a.297.297 0 0 1 .2.217l.186.706a.32.32 0 0 1-.066.292c-.867.99-1.197 2.41-.862 3.704.339 1.292 1.304 2.313 2.517 2.666a.301.301 0 0 1 .203.217l.806 3.107.08.306s-.195.062-.29.094l-39.03 11.929-.292.086zM54.578 35.1l-1.364-5.24c-.115-.446-.5-.753-.931-.755-.725-.001-1.376-.537-1.57-1.287-.196-.747.098-1.562.717-1.982a1.1 1.1 0 0 0 .432-1.173l-.624-2.397c-.116-.444-.483-.753-.931-.754a1.645 1.645 0 0 1-1.372-.814 1.86 1.86 0 0 1-.216-1.338 1.79 1.79 0 0 1 .735-1.117 1.09 1.09 0 0 0 .43-1.173l-1.364-5.245-.035-.12-.037-.112c-.024-.065-.033-.098-.048-.134-.037-.086-.042-.098-.051-.114a.763.763 0 0 0-.098-.16.572.572 0 0 0-.126-.112.675.675 0 0 0-.171-.065c-.13-.008-.157-.01-.193-.008-.12.014-.145.019-.175.024l-.17.037c-.015.003-.048.012-41.605 12.709-.854.296-.932.446-.694 1.338l1.399 5.358c.102.389.408.68.779.742.378.069.723.274.974.577.296.359.439.82.408 1.296a1.845 1.845 0 0 1-.571 1.233 1.102 1.102 0 0 0-.31 1.075l.697 2.67c.1.387.407.68.779.743.376.065.722.273.974.577a1.823 1.823 0 0 1 .408 1.295 1.852 1.852 0 0 1-.107.517 1.897 1.897 0 0 1-.143.313c-.09.15-.198.286-.323.404a1.042 1.042 0 0 0-.299.489 1.13 1.13 0 0 0-.008.585l1.397 5.373c.104.345.173.562.287.693a.407.407 0 0 0 .295.149.801.801 0 0 0 .11 0c.05-.002.109-.01.174-.023.06-.011.128-.026.215-.048l.34-.103 4.59-1.4c.202-.06.407-.124.618-.186l12.233-3.732 1.79-.547 4.057-1.237c.3-.092.598-.183.897-.272l17.106-5.22c.826-.29.867-.48.695-1.33z",
    fill: "#444"
  })));
});
// CONCATENATED MODULE: ./src/modules/icons/inactive/ticket.svg
var inactive_ticket_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function inactive_ticket_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var inactive_ticket = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = inactive_ticket_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", inactive_ticket_extends({
    width: "60",
    height: "60",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("g", {
    fill: "#AEB4BB",
    fillRule: "evenodd"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M14.267 31.31l-.434-1.434a.25.25 0 0 1 .167-.311l28.5-8.614a.251.251 0 0 1 .312.167l.434 1.436a.25.25 0 0 1-.167.312l-28.5 8.613a.251.251 0 0 1-.312-.168m2.393 7.739l-.435-1.435a.25.25 0 0 1 .167-.311l28.5-8.613a.25.25 0 0 1 .312.167l.434 1.435a.25.25 0 0 1-.167.312l-28.5 8.613a.251.251 0 0 1-.312-.168"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M12.812 46.805l-.061-.23-.237-.91-.66-2.528a.416.416 0 0 1-.01-.061.305.305 0 0 1 .039-.186c.074-.112.115-.174.153-.237a4.164 4.164 0 0 0 .586-2.292 4.13 4.13 0 0 0-.27-1.297c-.367-.956-1.066-1.7-1.971-2.095a.307.307 0 0 1-.181-.21l-.32-1.223a.347.347 0 0 1 .054-.287 4.097 4.097 0 0 0 .438-3.777c-.37-.958-1.07-1.702-1.971-2.095a.314.314 0 0 1-.18-.206l-.877-3.372a8.538 8.538 0 0 0-.085-.292l.294-.1 39.046-11.925.28-.08.082.3.81 3.112a.336.336 0 0 1-.065.298 4.01 4.01 0 0 0-.861 1.622 4.065 4.065 0 0 0 .329 2.939c.476.896 1.25 1.537 2.186 1.808a.297.297 0 0 1 .2.217l.186.706a.32.32 0 0 1-.066.292c-.867.99-1.197 2.41-.862 3.704.339 1.292 1.304 2.313 2.517 2.666a.301.301 0 0 1 .203.217l.806 3.107.08.306s-.195.062-.29.094l-39.03 11.929-.292.086zM54.578 35.1l-1.364-5.24c-.115-.446-.5-.753-.931-.755-.725-.001-1.376-.537-1.57-1.287-.196-.747.098-1.562.717-1.982a1.1 1.1 0 0 0 .432-1.173l-.624-2.397c-.116-.444-.483-.753-.931-.754a1.645 1.645 0 0 1-1.372-.814 1.86 1.86 0 0 1-.216-1.338 1.79 1.79 0 0 1 .735-1.117 1.09 1.09 0 0 0 .43-1.173l-1.364-5.245-.035-.12-.037-.112c-.024-.065-.033-.098-.048-.134-.037-.086-.042-.098-.051-.114a.763.763 0 0 0-.098-.16.572.572 0 0 0-.126-.112.675.675 0 0 0-.171-.065c-.13-.008-.157-.01-.193-.008-.12.014-.145.019-.175.024l-.17.037c-.015.003-.048.012-41.605 12.709-.854.296-.932.446-.694 1.338l1.399 5.358c.102.389.408.68.779.742.378.069.723.274.974.577.296.359.439.82.408 1.296a1.845 1.845 0 0 1-.571 1.233 1.102 1.102 0 0 0-.31 1.075l.697 2.67c.1.387.407.68.779.743.376.065.722.273.974.577a1.823 1.823 0 0 1 .408 1.295 1.852 1.852 0 0 1-.107.517 1.897 1.897 0 0 1-.143.313c-.09.15-.198.286-.323.404a1.042 1.042 0 0 0-.299.489 1.13 1.13 0 0 0-.008.585l1.397 5.373c.104.345.173.562.287.693a.407.407 0 0 0 .295.149.801.801 0 0 0 .11 0c.05-.002.109-.01.174-.023.06-.011.128-.026.215-.048l.34-.103 4.59-1.4c.202-.06.407-.124.618-.186l12.233-3.732 1.79-.547 4.057-1.237c.3-.092.598-.183.897-.272l17.106-5.22c.826-.29.867-.48.695-1.33z"
  })));
});
// CONCATENATED MODULE: ./src/modules/icons/rsvp.svg
var rsvp_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function rsvp_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var rsvp = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = rsvp_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", rsvp_extends({
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 47.99 38.9"
  }, props), /*#__PURE__*/external_React_default.a.createElement("defs", null), /*#__PURE__*/external_React_default.a.createElement("title", null, "block-icon-rsvp"), /*#__PURE__*/external_React_default.a.createElement("g", {
    id: "Layer_2",
    "data-name": "Layer 2"
  }, /*#__PURE__*/external_React_default.a.createElement("g", {
    id: "Layer_1-2",
    "data-name": "Layer 1"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    className: styles["cls-1"] || "cls-1",
    d: "M47.93 28l-5-26.27L26.1 27a1.11 1.11 0 0 1-1.57.3L0 10.38l5 25.91a3.21 3.21 0 0 0 3.75 2.54l36.67-7A3.21 3.21 0 0 0 47.93 28z"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    className: styles["cls-1"] || "cls-1",
    d: "M23.64 21.23a.81.81 0 0 0 1-.11L39.93 0 1.65 7.34l20.84 13.17z"
  }))));
});
// CONCATENATED MODULE: ./src/modules/icons/active/rsvp.svg
var active_rsvp_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function active_rsvp_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var active_rsvp = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = active_rsvp_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", active_rsvp_extends({
    width: "60",
    height: "60",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("g", {
    fill: "none",
    fillRule: "evenodd"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M5.419 24.046l26.093 9.307 16.183-22.117.567.858-15.905 25.354a1.926 1.926 0 0 1-2.491.701L5.012 25.761l.407-1.715z",
    fill: "#E6E6E6"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    fill: "#039ED3",
    d: "M12.517 49.828l-1.762-.945 10.58-19.731 1.763.946zm42.792-12.271l-19.43-11.125.995-1.736 19.429 11.125z"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M3 9h54.017v41.354H3z"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M5.237 23.411l7.084 24.706L54.78 35.942l-7.084-24.706L5.237 23.411zm6.398 26.943a1.005 1.005 0 0 1-.961-.724L3.04 23.001a1 1 0 0 1 .685-1.237l44.38-12.726a1.002 1.002 0 0 1 1.237.685l7.635 26.63a1 1 0 0 1-.685 1.236l-44.38 12.726a.959.959 0 0 1-.277.04z",
    fill: "#444"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M31.1 34.48c-.118 0-.237-.02-.352-.063L3.83 24.295l.704-1.871 26.222 9.86 17.012-22.257 1.59 1.215-17.463 22.846a1 1 0 0 1-.795.393",
    fill: "#444"
  })));
});
// CONCATENATED MODULE: ./src/modules/icons/inactive/rsvp.svg
var inactive_rsvp_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function inactive_rsvp_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var inactive_rsvp = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = inactive_rsvp_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", inactive_rsvp_extends({
    width: "60",
    height: "60",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("g", {
    fill: "#AEB4BB",
    fillRule: "evenodd"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M12.517 49.828l-1.762-.945 10.58-19.731 1.763.946zm42.792-12.271l-19.43-11.125.995-1.736 19.429 11.125z"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M5.237 23.411l7.084 24.706L54.78 35.942l-7.084-24.706L5.237 23.411zm6.398 26.943a1.005 1.005 0 0 1-.961-.724L3.04 23.001a1 1 0 0 1 .685-1.237l44.38-12.726a1.002 1.002 0 0 1 1.237.685l7.635 26.63a1 1 0 0 1-.685 1.236l-44.38 12.726a.959.959 0 0 1-.277.04z"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M31.1 34.48c-.118 0-.237-.02-.352-.063L3.83 24.295l.704-1.871 26.222 9.86 17.012-22.257 1.59 1.215-17.463 22.846a1 1 0 0 1-.795.393"
  })));
});
// CONCATENATED MODULE: ./src/modules/icons/gravatar.svg
var gravatar_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function gravatar_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var gravatar = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = gravatar_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", gravatar_extends({
    version: "1",
    xmlns: "http://www.w3.org/2000/svg",
    width: "200",
    height: "200",
    viewBox: "0 0 200 200"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    fill: "#C5C5C5",
    d: "M0 0h200v200H0z"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    fill: "#FFF",
    d: "M23.511 200h152.977c-6.617-38.031-27.018-68.385-53.278-79.828 12.934-7.904 21.567-22.154 21.567-38.422 0-24.853-20.147-45-45-45s-45 20.147-45 45c0 16.345 8.715 30.652 21.751 38.534-26.134 11.53-46.421 41.811-53.017 79.716z"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/attendees.svg
var attendees_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function attendees_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var attendees = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = attendees_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", attendees_extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: "21",
    height: "21",
    viewBox: "0 0 21 21",
    fill: "none"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M18 18.25v-1.667c0-.884-.395-1.732-1.098-2.357-.704-.625-1.657-.976-2.652-.976h-7.5c-.995 0-1.948.351-2.652.976C3.395 14.851 3 15.7 3 16.583v1.667M10.5 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8z",
    stroke: "#727272",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/orders.svg
var orders_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function orders_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var orders = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = orders_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", orders_extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: "19",
    height: "19",
    viewBox: "0 0 19 19",
    fill: "none"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M9.5 17.5a8 8 0 1 0 0-16 8 8 0 0 0 0 16z",
    stroke: "#727373",
    strokeWidth: "1.5",
    strokeMiterlimit: "10"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M9.5 13.667v1M9.5 4.417v1M11.954 7.088s-.379-1.421-2.363-1.421-2.599 1.22-2.599 1.899c0 2.62 5.092.93 5.092 3.632 0 .956-.79 1.973-2.733 1.969-1.922-.005-2.517-1.746-2.517-1.746",
    stroke: "#727373",
    strokeWidth: "1.5",
    strokeMiterlimit: "10",
    strokeLinecap: "round"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/settings.svg
var settings_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function settings_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var settings = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = settings_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", settings_extends({
    width: "24",
    height: "26",
    viewBox: "0 0 24 26",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M12 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z",
    stroke: "#727272",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M19.4 16a1.65 1.65 0 0 0 .33 1.82l.06.06a1.998 1.998 0 0 1 0 2.83 1.998 1.998 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V22a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 20.4a1.65 1.65 0 0 0-1.82.33l-.06.06a1.998 1.998 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 10a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V4a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 3.417 1.415 2 2 0 0 1-.587 1.415l-.06.06a1.65 1.65 0 0 0-.33 1.82V10a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z",
    stroke: "#727272",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/close.svg
var close_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function close_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var icons_close = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = close_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", close_extends({
    xmlns: "http://www.w3.org/2000/svg",
    width: "15",
    height: "15",
    viewBox: "0 0 15 15",
    fill: "none"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M13 2L2 13M13 13L2 2",
    stroke: "#727272",
    strokeWidth: "2",
    strokeLinecap: "square"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/ticket-left.svg
var ticket_left_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function ticket_left_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var ticket_left = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = ticket_left_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", ticket_left_extends({
    width: "24",
    height: "25",
    viewBox: "0 0 24 25",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M15.697 14.482c.126.345-.025.719-.34.834l-.757.28c-.314.114-.672-.072-.799-.417s.025-.719.339-.835l.758-.278c.314-.116.672.07.799.416zM12.325 16.431c.314-.115.466-.489.339-.834s-.484-.532-.798-.416l-.759.278c-.314.116-.465.49-.338.835s.484.531.798.416l.758-.279z",
    fill: "#000"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M19.906 18.937a.872.872 0 0 0 .533-1.111L14.98 2.976a.872.872 0 0 0-1.126-.501l-2.833 1.041c.364 1.05-.202 2.21-1.278 2.605-1.076.396-2.259-.122-2.66-1.157L4.25 6.005a.872.872 0 0 0-.533 1.111l5.459 14.85a.871.871 0 0 0 1.125.501l2.834-1.041a1.92 1.92 0 0 1-.02-.054l-.005-.013c-.365-1.05.201-2.21 1.277-2.606 1.076-.395 2.26.123 2.661 1.158a2.602 2.602 0 0 1 .025.067l2.833-1.041zm-5.843-15.47l-1.903.699c-.006 1.248-.8 2.43-2.07 2.896-1.27.467-2.64.08-3.453-.866l-1.903.699 3.532 9.609.705-.26c.314-.115.671.071.798.417.127.345-.024.719-.338.834l-.705.26 1.368 3.72 1.878-.69c-.026-1.276.774-2.496 2.07-2.972 1.296-.476 2.695-.065 3.501.925l1.88-.691-1.369-3.722-.705.26c-.314.115-.671-.072-.798-.417s.025-.719.339-.834l.704-.26-3.531-9.608z",
    fill: "#000"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/ecp.svg
var ecp_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function ecp_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var ecp = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = ecp_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", ecp_extends({
    width: "12",
    height: "13",
    viewBox: "0 0 12 13",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M12 6.5a6 6 0 1 1-12 0 6 6 0 0 1 12 0z",
    fill: "#FFCF48"
  }), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M5.72 6.547l-2.098-.06 4.76-4.378-2.084 3.913 2.098.06-4.465 4.741L5.72 6.547z",
    fill: "#161B7D"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/bulb.svg
var bulb_extends = Object.assign || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];
    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }
  return target;
};
function bulb_objectWithoutProperties(obj, keys) {
  var target = {};
  for (var i in obj) {
    if (keys.indexOf(i) >= 0) continue;
    if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
    target[i] = obj[i];
  }
  return target;
}

/* harmony default export */ var bulb = (_ref => {
  let {
      styles = {}
    } = _ref,
    props = bulb_objectWithoutProperties(_ref, ["styles"]);
  return /*#__PURE__*/external_React_default.a.createElement("svg", bulb_extends({
    width: "24",
    height: "24",
    viewBox: "0 0 24 24",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, props), /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M16.5 9c0 2.45-1.607 2.399-2.204 5.775a.895.895 0 0 1-.896.763h-2.8a.895.895 0 0 1-.896-.763C9.107 11.399 7.5 11.45 7.5 9c0-2.474 2.043-4.5 4.5-4.5s4.5 2.026 4.5 4.5zm-2.502 7.624a.413.413 0 0 1-.414.416h-3.168a.413.413 0 0 1-.413-.416c0-.23.183-.416.413-.416h3.168c.23 0 .414.185.414.416zm-.414 1.48a.413.413 0 0 1-.413.416H10.75a.413.413 0 0 1-.414-.416c0-.232.184-.416.414-.416h2.422c.23 0 .413.184.413.416zM11 19h2l-.5.5h-1L11 19z",
    fill: "#334AFF"
  }));
});
// CONCATENATED MODULE: ./src/modules/icons/index.js



















/***/ }),

/***/ "OKIc":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "P9XJ":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "Q9xL":
/***/ (function(module, exports) {

module.exports = tribe.common.hoc;

/***/ }),

/***/ "QFGf":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "b", function() { return /* reexport */ constants; });
__webpack_require__.d(__webpack_exports__, "d", function() { return /* reexport */ options_namespaceObject; });
__webpack_require__.d(__webpack_exports__, "g", function() { return /* reexport */ types; });
__webpack_require__.d(__webpack_exports__, "a", function() { return /* reexport */ actions; });
__webpack_require__.d(__webpack_exports__, "f", function() { return /* reexport */ selectors_namespaceObject; });
__webpack_require__.d(__webpack_exports__, "e", function() { return /* reexport */ watchers; });

// UNUSED EXPORTS: utils

// NAMESPACE OBJECT: ./src/modules/data/blocks/ticket/options.js
var options_namespaceObject = {};
__webpack_require__.r(options_namespaceObject);
__webpack_require__.d(options_namespaceObject, "CAPACITY_TYPE_OPTIONS", function() { return CAPACITY_TYPE_OPTIONS; });

// NAMESPACE OBJECT: ./src/modules/data/blocks/ticket/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getState", function() { return getState; });
__webpack_require__.d(selectors_namespaceObject, "getBlock", function() { return getBlock; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsIsSelected", function() { return getTicketsIsSelected; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsIsSettingsOpen", function() { return getTicketsIsSettingsOpen; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsIsSettingsLoading", function() { return getTicketsIsSettingsLoading; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsProvider", function() { return getTicketsProvider; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsSharedCapacity", function() { return getTicketsSharedCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsSharedCapacityInt", function() { return getTicketsSharedCapacityInt; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsTempSharedCapacity", function() { return getTicketsTempSharedCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsTempSharedCapacityInt", function() { return getTicketsTempSharedCapacityInt; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsHeaderImage", function() { return getTicketsHeaderImage; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsHeaderImageId", function() { return getTicketsHeaderImageId; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsHeaderImageSrc", function() { return getTicketsHeaderImageSrc; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsHeaderImageAlt", function() { return getTicketsHeaderImageAlt; });
__webpack_require__.d(selectors_namespaceObject, "getTickets", function() { return getTickets; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsAllClientIds", function() { return getTicketsAllClientIds; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsByClientId", function() { return getTicketsByClientId; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsArray", function() { return getTicketsArray; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsCount", function() { return getTicketsCount; });
__webpack_require__.d(selectors_namespaceObject, "hasTickets", function() { return hasTickets; });
__webpack_require__.d(selectors_namespaceObject, "hasCreatedTickets", function() { return selectors_hasCreatedTickets; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentTickets", function() { return getIndependentTickets; });
__webpack_require__.d(selectors_namespaceObject, "getSharedTickets", function() { return getSharedTickets; });
__webpack_require__.d(selectors_namespaceObject, "getSharedTicketsCount", function() { return getSharedTicketsCount; });
__webpack_require__.d(selectors_namespaceObject, "getUnlimitedTickets", function() { return getUnlimitedTickets; });
__webpack_require__.d(selectors_namespaceObject, "hasATicketSelected", function() { return hasATicketSelected; });
__webpack_require__.d(selectors_namespaceObject, "getTicketsIdsInBlocks", function() { return getTicketsIdsInBlocks; });
__webpack_require__.d(selectors_namespaceObject, "getUneditableTickets", function() { return getUneditableTickets; });
__webpack_require__.d(selectors_namespaceObject, "getUneditableTicketsAreLoading", function() { return getUneditableTicketsAreLoading; });
__webpack_require__.d(selectors_namespaceObject, "getTicketClientId", function() { return getTicketClientId; });
__webpack_require__.d(selectors_namespaceObject, "getTicket", function() { return getTicket; });
__webpack_require__.d(selectors_namespaceObject, "getTicketSold", function() { return getTicketSold; });
__webpack_require__.d(selectors_namespaceObject, "getTicketAvailable", function() { return getTicketAvailable; });
__webpack_require__.d(selectors_namespaceObject, "getTicketId", function() { return getTicketId; });
__webpack_require__.d(selectors_namespaceObject, "getTicketCurrencySymbol", function() { return getTicketCurrencySymbol; });
__webpack_require__.d(selectors_namespaceObject, "getTicketCurrencyPosition", function() { return getTicketCurrencyPosition; });
__webpack_require__.d(selectors_namespaceObject, "getTicketCurrencyDecimalPoint", function() { return getTicketCurrencyDecimalPoint; });
__webpack_require__.d(selectors_namespaceObject, "getTicketCurrencyNumberOfDecimals", function() { return getTicketCurrencyNumberOfDecimals; });
__webpack_require__.d(selectors_namespaceObject, "getTicketCurrencyThousandsSep", function() { return getTicketCurrencyThousandsSep; });
__webpack_require__.d(selectors_namespaceObject, "getTicketProvider", function() { return getTicketProvider; });
__webpack_require__.d(selectors_namespaceObject, "getTicketHasAttendeeInfoFields", function() { return getTicketHasAttendeeInfoFields; });
__webpack_require__.d(selectors_namespaceObject, "getTicketIsLoading", function() { return getTicketIsLoading; });
__webpack_require__.d(selectors_namespaceObject, "getTicketIsModalOpen", function() { return getTicketIsModalOpen; });
__webpack_require__.d(selectors_namespaceObject, "getTicketHasBeenCreated", function() { return getTicketHasBeenCreated; });
__webpack_require__.d(selectors_namespaceObject, "getTicketHasChanges", function() { return getTicketHasChanges; });
__webpack_require__.d(selectors_namespaceObject, "getTicketHasDurationError", function() { return getTicketHasDurationError; });
__webpack_require__.d(selectors_namespaceObject, "getTicketIsSelected", function() { return getTicketIsSelected; });
__webpack_require__.d(selectors_namespaceObject, "isTicketDisabled", function() { return isTicketDisabled; });
__webpack_require__.d(selectors_namespaceObject, "getTicketDetails", function() { return getTicketDetails; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTitle", function() { return getTicketTitle; });
__webpack_require__.d(selectors_namespaceObject, "getTicketDescription", function() { return getTicketDescription; });
__webpack_require__.d(selectors_namespaceObject, "getTicketPrice", function() { return getTicketPrice; });
__webpack_require__.d(selectors_namespaceObject, "getTicketOnSale", function() { return getTicketOnSale; });
__webpack_require__.d(selectors_namespaceObject, "getTicketSku", function() { return getTicketSku; });
__webpack_require__.d(selectors_namespaceObject, "getTicketIACSetting", function() { return getTicketIACSetting; });
__webpack_require__.d(selectors_namespaceObject, "getTicketStartDate", function() { return getTicketStartDate; });
__webpack_require__.d(selectors_namespaceObject, "getTicketStartDateInput", function() { return getTicketStartDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketStartDateMoment", function() { return getTicketStartDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getTicketEndDate", function() { return getTicketEndDate; });
__webpack_require__.d(selectors_namespaceObject, "getTicketEndDateInput", function() { return getTicketEndDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketEndDateMoment", function() { return getTicketEndDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getTicketStartTime", function() { return getTicketStartTime; });
__webpack_require__.d(selectors_namespaceObject, "getTicketStartTimeNoSeconds", function() { return getTicketStartTimeNoSeconds; });
__webpack_require__.d(selectors_namespaceObject, "getTicketEndTime", function() { return getTicketEndTime; });
__webpack_require__.d(selectors_namespaceObject, "getTicketEndTimeNoSeconds", function() { return getTicketEndTimeNoSeconds; });
__webpack_require__.d(selectors_namespaceObject, "getTicketStartTimeInput", function() { return getTicketStartTimeInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketEndTimeInput", function() { return getTicketEndTimeInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketCapacityType", function() { return getTicketCapacityType; });
__webpack_require__.d(selectors_namespaceObject, "getTicketCapacity", function() { return getTicketCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getTicketCapacityInt", function() { return getTicketCapacityInt; });
__webpack_require__.d(selectors_namespaceObject, "getSalePriceChecked", function() { return getSalePriceChecked; });
__webpack_require__.d(selectors_namespaceObject, "getSalePrice", function() { return getSalePrice; });
__webpack_require__.d(selectors_namespaceObject, "getTicketSaleStartDate", function() { return getTicketSaleStartDate; });
__webpack_require__.d(selectors_namespaceObject, "getTicketSaleStartDateInput", function() { return getTicketSaleStartDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketSaleStartDateMoment", function() { return getTicketSaleStartDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getTicketSaleEndDate", function() { return getTicketSaleEndDate; });
__webpack_require__.d(selectors_namespaceObject, "getTicketSaleEndDateInput", function() { return getTicketSaleEndDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketSaleEndDateMoment", function() { return getTicketSaleEndDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "isUnlimitedTicket", function() { return isUnlimitedTicket; });
__webpack_require__.d(selectors_namespaceObject, "isSharedTicket", function() { return isSharedTicket; });
__webpack_require__.d(selectors_namespaceObject, "isIndependentTicket", function() { return isIndependentTicket; });
__webpack_require__.d(selectors_namespaceObject, "isTicketPast", function() { return isTicketPast; });
__webpack_require__.d(selectors_namespaceObject, "isTicketFuture", function() { return isTicketFuture; });
__webpack_require__.d(selectors_namespaceObject, "isTicketOnSale", function() { return isTicketOnSale; });
__webpack_require__.d(selectors_namespaceObject, "hasTicketOnSale", function() { return hasTicketOnSale; });
__webpack_require__.d(selectors_namespaceObject, "allTicketsPast", function() { return allTicketsPast; });
__webpack_require__.d(selectors_namespaceObject, "allTicketsFuture", function() { return allTicketsFuture; });
__webpack_require__.d(selectors_namespaceObject, "getTicketAttendeeInfoFields", function() { return getTicketAttendeeInfoFields; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempDetails", function() { return getTicketTempDetails; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempTitle", function() { return getTicketTempTitle; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempDescription", function() { return getTicketTempDescription; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempPrice", function() { return getTicketTempPrice; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempSku", function() { return getTicketTempSku; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempIACSetting", function() { return getTicketTempIACSetting; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempStartDate", function() { return getTicketTempStartDate; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempStartDateInput", function() { return getTicketTempStartDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempStartDateMoment", function() { return getTicketTempStartDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempEndDate", function() { return getTicketTempEndDate; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempEndDateInput", function() { return getTicketTempEndDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempEndDateMoment", function() { return getTicketTempEndDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempStartTime", function() { return getTicketTempStartTime; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempStartTimeNoSeconds", function() { return getTicketTempStartTimeNoSeconds; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempEndTime", function() { return getTicketTempEndTime; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempEndTimeNoSeconds", function() { return getTicketTempEndTimeNoSeconds; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempStartTimeInput", function() { return getTicketTempStartTimeInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempEndTimeInput", function() { return getTicketTempEndTimeInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempCapacityType", function() { return getTicketTempCapacityType; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempCapacity", function() { return getTicketTempCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempCapacityInt", function() { return getTicketTempCapacityInt; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempCapacityTypeOption", function() { return getTicketTempCapacityTypeOption; });
__webpack_require__.d(selectors_namespaceObject, "getTempSalePriceChecked", function() { return getTempSalePriceChecked; });
__webpack_require__.d(selectors_namespaceObject, "getTempSalePrice", function() { return getTempSalePrice; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempSaleStartDate", function() { return getTicketTempSaleStartDate; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempSaleStartDateInput", function() { return getTicketTempSaleStartDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempSaleStartDateMoment", function() { return getTicketTempSaleStartDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempSaleEndDate", function() { return getTicketTempSaleEndDate; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempSaleEndDateInput", function() { return getTicketTempSaleEndDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getTicketTempSaleEndDateMoment", function() { return getTicketTempSaleEndDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "showSalePrice", function() { return selectors_showSalePrice; });
__webpack_require__.d(selectors_namespaceObject, "isTicketSalePriceValid", function() { return isTicketSalePriceValid; });
__webpack_require__.d(selectors_namespaceObject, "isTempTitleValid", function() { return isTempTitleValid; });
__webpack_require__.d(selectors_namespaceObject, "isTempCapacityValid", function() { return isTempCapacityValid; });
__webpack_require__.d(selectors_namespaceObject, "isTempSharedCapacityValid", function() { return isTempSharedCapacityValid; });
__webpack_require__.d(selectors_namespaceObject, "isZeroPriceValid", function() { return isZeroPriceValid; });
__webpack_require__.d(selectors_namespaceObject, "isTicketValid", function() { return isTicketValid; });
__webpack_require__.d(selectors_namespaceObject, "_getTotalCapacity", function() { return _getTotalCapacity; });
__webpack_require__.d(selectors_namespaceObject, "_getTotalTempCapacity", function() { return _getTotalTempCapacity; });
__webpack_require__.d(selectors_namespaceObject, "_getTotalSold", function() { return _getTotalSold; });
__webpack_require__.d(selectors_namespaceObject, "_getTotalAvailable", function() { return _getTotalAvailable; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentTicketsCapacity", function() { return getIndependentTicketsCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentTicketsTempCapacity", function() { return getIndependentTicketsTempCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentTicketsSold", function() { return getIndependentTicketsSold; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentTicketsAvailable", function() { return getIndependentTicketsAvailable; });
__webpack_require__.d(selectors_namespaceObject, "getSharedTicketsSold", function() { return getSharedTicketsSold; });
__webpack_require__.d(selectors_namespaceObject, "getSharedTicketsAvailable", function() { return getSharedTicketsAvailable; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentAndSharedTicketsCapacity", function() { return getIndependentAndSharedTicketsCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentAndSharedTicketsTempCapacity", function() { return getIndependentAndSharedTicketsTempCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentAndSharedTicketsSold", function() { return getIndependentAndSharedTicketsSold; });
__webpack_require__.d(selectors_namespaceObject, "getIndependentAndSharedTicketsAvailable", function() { return getIndependentAndSharedTicketsAvailable; });
__webpack_require__.d(selectors_namespaceObject, "getTicketProviders", function() { return getTicketProviders; });
__webpack_require__.d(selectors_namespaceObject, "getDefaultTicketProvider", function() { return getDefaultTicketProvider; });
__webpack_require__.d(selectors_namespaceObject, "hasValidTicketProvider", function() { return hasValidTicketProvider; });
__webpack_require__.d(selectors_namespaceObject, "hasMultipleTicketProviders", function() { return hasMultipleTicketProviders; });
__webpack_require__.d(selectors_namespaceObject, "hasTicketProviders", function() { return hasTicketProviders; });
__webpack_require__.d(selectors_namespaceObject, "canCreateTickets", function() { return canCreateTickets; });
__webpack_require__.d(selectors_namespaceObject, "getCurrentPostTypeLabel", function() { return getCurrentPostTypeLabel; });
__webpack_require__.d(selectors_namespaceObject, "currentPostIsEvent", function() { return currentPostIsEvent; });
__webpack_require__.d(selectors_namespaceObject, "getNumericPrice", function() { return getNumericPrice; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/defineProperty.js
var defineProperty = __webpack_require__("lSNA");
var defineProperty_default = /*#__PURE__*/__webpack_require__.n(defineProperty);

// EXTERNAL MODULE: external "tribe.modules.redux"
var external_tribe_modules_redux_ = __webpack_require__("rKB8");

// EXTERNAL MODULE: external "lodash.omit"
var external_lodash_omit_ = __webpack_require__("2TDg");
var external_lodash_omit_default = /*#__PURE__*/__webpack_require__.n(external_lodash_omit_);

// EXTERNAL MODULE: ./src/modules/data/blocks/ticket/types.js
var types = __webpack_require__("enZp");

// EXTERNAL MODULE: external "tribe.common.utils"
var external_tribe_common_utils_ = __webpack_require__("B8vQ");

// EXTERNAL MODULE: external "lodash.trim"
var external_lodash_trim_ = __webpack_require__("XNrZ");
var external_lodash_trim_default = /*#__PURE__*/__webpack_require__.n(external_lodash_trim_);

// EXTERNAL MODULE: external "lodash.find"
var external_lodash_find_ = __webpack_require__("6OzC");
var external_lodash_find_default = /*#__PURE__*/__webpack_require__.n(external_lodash_find_);

// EXTERNAL MODULE: external "tribe.modules.reselect"
var external_tribe_modules_reselect_ = __webpack_require__("MWqi");

// EXTERNAL MODULE: external "moment"
var external_moment_ = __webpack_require__("wy2R");
var external_moment_default = /*#__PURE__*/__webpack_require__.n(external_moment_);

// EXTERNAL MODULE: ./src/modules/data/blocks/ticket/constants.js
var constants = __webpack_require__("DOwB");

// EXTERNAL MODULE: external "wp.i18n"
var external_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/options.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */

const CAPACITY_TYPE_OPTIONS = [{
  label: Object(external_wp_i18n_["__"])('Share capacity with other tickets', 'event-tickets'),
  value: constants["TICKET_TYPES"][constants["SHARED"]]
}, {
  label: Object(external_wp_i18n_["__"])('Set capacity for this ticket only', 'event-tickets'),
  value: constants["TICKET_TYPES"][constants["INDEPENDENT"]]
}, {
  label: Object(external_wp_i18n_["__"])('Unlimited', 'event-tickets'),
  value: constants["TICKET_TYPES"][constants["UNLIMITED"]]
}];
// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/selectors.js


/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const {
  UNLIMITED,
  INDEPENDENT,
  SHARED,
  TICKET_TYPES,
  IS_FREE_TC_TICKET_ALLOWED
} = constants;
const {
  tickets: ticketsConfig,
  post: postConfig
} = external_tribe_common_utils_["globals"];
const getState = state => state;
const getBlock = state => state.tickets.blocks.ticket;

//
// ─── BLOCK SELECTORS ────────────────────────────────────────────────────────────
//

const getTicketsIsSelected = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], block => block.isSelected);
const getTicketsIsSettingsOpen = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], block => block.isSettingsOpen);
const getTicketsIsSettingsLoading = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], block => block.isSettingsLoading);
const getTicketsProvider = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], block => block.provider);
const getTicketsSharedCapacity = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], block => block.sharedCapacity);
const getTicketsSharedCapacityInt = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsSharedCapacity], capacity => parseInt(capacity, 10) || 0);
const getTicketsTempSharedCapacity = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], block => block.tempSharedCapacity);
const getTicketsTempSharedCapacityInt = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsTempSharedCapacity], capacity => parseInt(capacity, 10) || 0);

//
// ─── HEADER IMAGE SELECTORS ─────────────────────────────────────────────────────
//

const getTicketsHeaderImage = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], block => block.headerImage);
const getTicketsHeaderImageId = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsHeaderImage], headerImage => headerImage.id);
const getTicketsHeaderImageSrc = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsHeaderImage], headerImage => headerImage.src);
const getTicketsHeaderImageAlt = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsHeaderImage], headerImage => headerImage.alt);

//
// ─── TICKETS SELECTORS ──────────────────────────────────────────────────────────
//

const getTickets = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], block => block.tickets);
const getTicketsAllClientIds = Object(external_tribe_modules_reselect_["createSelector"])([getTickets], tickets => [...new Set(tickets.allClientIds)]);
const getTicketsByClientId = Object(external_tribe_modules_reselect_["createSelector"])([getTickets], tickets => tickets.byClientId);
const getTicketsArray = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsAllClientIds, getTicketsByClientId], (ids, tickets) => ids.map(id => tickets[id]));
const getTicketsCount = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsAllClientIds], allClientIds => allClientIds.length);
const hasTickets = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsCount], count => count > 0);
const selectors_hasCreatedTickets = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsArray], tickets => tickets.reduce((hasCreated, ticket) => hasCreated || ticket.hasBeenCreated, false));
const getIndependentTickets = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsArray], tickets => tickets.filter(ticket => ticket.details.capacityType === TICKET_TYPES[INDEPENDENT]));
const getSharedTickets = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsArray], tickets => tickets.filter(ticket => ticket.details.capacityType === TICKET_TYPES[SHARED]));
const getSharedTicketsCount = Object(external_tribe_modules_reselect_["createSelector"])([getSharedTickets], tickets => tickets.length);
const getUnlimitedTickets = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsArray], tickets => tickets.filter(ticket => ticket.details.capacityType === TICKET_TYPES[UNLIMITED]));
const hasATicketSelected = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsArray], tickets => tickets.reduce((selected, ticket) => selected || ticket.isSelected, false));
const getTicketsIdsInBlocks = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsArray], tickets => tickets.reduce((accumulator, ticket) => {
  if (ticket.ticketId !== 0) {
    accumulator.push(ticket.ticketId);
  }
  return accumulator;
}, []));
const getUneditableTickets = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], function (block) {
  return block.uneditableTickets || [];
});
const getUneditableTicketsAreLoading = Object(external_tribe_modules_reselect_["createSelector"])([getBlock], function (block) {
  return block.uneditableTicketsLoading || false;
});

//
// ─── TICKET SELECTORS ───────────────────────────────────────────────────────────
//

const getTicketClientId = (state, ownProps) => ownProps.clientId;
const getTicket = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsByClientId, getTicketClientId], (tickets, clientId) => tickets[clientId] || {});
const getTicketSold = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.sold);
const getTicketAvailable = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.available);
const getTicketId = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.ticketId);
const getTicketCurrencySymbol = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.currencySymbol);
const getTicketCurrencyPosition = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.currencyPosition);
const getTicketCurrencyDecimalPoint = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.currencyDecimalPoint);
const getTicketCurrencyNumberOfDecimals = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.currencyNumberOfDecimals);
const getTicketCurrencyThousandsSep = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.currencyThousandsSep);
const getTicketProvider = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.provider);
const getTicketHasAttendeeInfoFields = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.hasAttendeeInfoFields);
const getTicketIsLoading = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.isLoading);
const getTicketIsModalOpen = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.isModalOpen);
const getTicketHasBeenCreated = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.hasBeenCreated);
const getTicketHasChanges = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.hasChanges);
const getTicketHasDurationError = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.hasDurationError);
const getTicketIsSelected = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.isSelected);
const isTicketDisabled = Object(external_tribe_modules_reselect_["createSelector"])([hasATicketSelected, getTicketIsSelected, getTicketIsLoading, getTicketsIsSettingsOpen], (hasSelected, isSelected, isLoading, isSettingsOpen) => hasSelected && !isSelected || isLoading || isSettingsOpen);

//
// ─── TICKET DETAILS SELECTORS ───────────────────────────────────────────────────
//

const getTicketDetails = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.details || {});
const getTicketTitle = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.title);
const getTicketDescription = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.description);
const getTicketPrice = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.price);
const getTicketOnSale = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.on_sale);
const getTicketSku = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.sku);
const getTicketIACSetting = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.iac);
const getTicketStartDate = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.startDate);
const getTicketStartDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.startDateInput);
const getTicketStartDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.startDateMoment);
const getTicketEndDate = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.endDate);
const getTicketEndDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.endDateInput);
const getTicketEndDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.endDateMoment);
const getTicketStartTime = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.startTime || '');
const getTicketStartTimeNoSeconds = Object(external_tribe_modules_reselect_["createSelector"])([getTicketStartTime], startTime => startTime.slice(0, -3));
const getTicketEndTime = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.endTime || '');
const getTicketEndTimeNoSeconds = Object(external_tribe_modules_reselect_["createSelector"])([getTicketEndTime], endTime => endTime.slice(0, -3));
const getTicketStartTimeInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.startTimeInput);
const getTicketEndTimeInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.endTimeInput);
const getTicketCapacityType = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.capacityType);
const getTicketCapacity = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.capacity);
const getTicketCapacityInt = Object(external_tribe_modules_reselect_["createSelector"])([getTicketCapacity], capacity => parseInt(capacity, 10) || 0);
const getSalePriceChecked = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.salePriceChecked);
const getSalePrice = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.salePrice);
const getTicketSaleStartDate = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.saleStartDate);
const getTicketSaleStartDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.saleStartDateInput);
const getTicketSaleStartDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.saleStartDateMoment);
const getTicketSaleEndDate = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.saleEndDate);
const getTicketSaleEndDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.saleEndDateInput);
const getTicketSaleEndDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.saleEndDateMoment);
const isUnlimitedTicket = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.capacityType === TICKET_TYPES[UNLIMITED]);
const isSharedTicket = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.capacityType === TICKET_TYPES[SHARED]);
const isIndependentTicket = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.capacityType === TICKET_TYPES[INDEPENDENT]);
const isTicketPast = Object(external_tribe_modules_reselect_["createSelector"])([getTicketEndDateMoment], endDate => external_moment_default()().isAfter(endDate));
const isTicketFuture = Object(external_tribe_modules_reselect_["createSelector"])([getTicketStartDateMoment], startDate => external_moment_default()().isBefore(startDate));
const isTicketOnSale = Object(external_tribe_modules_reselect_["createSelector"])([getTicketHasBeenCreated, isTicketPast, isTicketFuture], (hasBeenCreated, isPast, isFuture) => hasBeenCreated && !isPast && !isFuture);
const hasTicketOnSale = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsAllClientIds, getState], (allClientIds, state) => allClientIds.reduce((onSale, clientId) => onSale || isTicketOnSale(state, {
  clientId
}), false));
const allTicketsPast = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsAllClientIds, getState], (allClientIds, state) => allClientIds.reduce((isPast, clientId) => {
  const props = {
    clientId
  };
  return getTicketHasBeenCreated(state, props) ? isPast && isTicketPast(state, props) : isPast;
}, true));
const allTicketsFuture = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsAllClientIds, getState], (allClientIds, state) => allClientIds.reduce((isFuture, clientId) => {
  const props = {
    clientId
  };
  return getTicketHasBeenCreated(state, props) ? isFuture && isTicketFuture(state, props) : isFuture;
}, true));
const getTicketAttendeeInfoFields = Object(external_tribe_modules_reselect_["createSelector"])([getTicketDetails], details => details.attendeeInfoFields || []);

//
// ─── TICKET TEMP DETAILS SELECTORS ──────────────────────────────────────────────
//

const getTicketTempDetails = Object(external_tribe_modules_reselect_["createSelector"])([getTicket], ticket => ticket.tempDetails || {});
const getTicketTempTitle = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.title);
const getTicketTempDescription = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.description);
const getTicketTempPrice = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.price);
const getTicketTempSku = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.sku);
const getTicketTempIACSetting = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.iac);
const getTicketTempStartDate = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.startDate);
const getTicketTempStartDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.startDateInput);
const getTicketTempStartDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.startDateMoment);
const getTicketTempEndDate = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.endDate);
const getTicketTempEndDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.endDateInput);
const getTicketTempEndDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.endDateMoment);
const getTicketTempStartTime = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.startTime || '');
const getTicketTempStartTimeNoSeconds = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempStartTime], startTime => startTime.slice(0, -3));
const getTicketTempEndTime = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.endTime || '');
const getTicketTempEndTimeNoSeconds = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempEndTime], endTime => endTime.slice(0, -3));
const getTicketTempStartTimeInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.startTimeInput);
const getTicketTempEndTimeInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.endTimeInput);
const getTicketTempCapacityType = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.capacityType);
const getTicketTempCapacity = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.capacity);
const getTicketTempCapacityInt = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempCapacity], capacity => parseInt(capacity, 10) || 0);
const getTicketTempCapacityTypeOption = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempCapacityType], capacityType => external_lodash_find_default()(CAPACITY_TYPE_OPTIONS, {
  value: capacityType
}) || {});
const getTempSalePriceChecked = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.salePriceChecked);
const getTempSalePrice = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.salePrice);
const getTicketTempSaleStartDate = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.saleStartDate);
const getTicketTempSaleStartDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.saleStartDateInput);
const getTicketTempSaleStartDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.saleStartDateMoment);
const getTicketTempSaleEndDate = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.saleEndDate);
const getTicketTempSaleEndDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.saleEndDateInput);
const getTicketTempSaleEndDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempDetails], tempDetails => tempDetails.saleEndDateMoment);
const selectors_showSalePrice = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsProvider], provider => provider === constants["TICKETS_COMMERCE_MODULE_CLASS"]);
const isTicketSalePriceValid = Object(external_tribe_modules_reselect_["createSelector"])([getTempSalePrice, getTicketTempPrice, getTicketCurrencyDecimalPoint, getTicketCurrencyNumberOfDecimals, getTicketCurrencyThousandsSep], (salePrice, price, decimalPoint, decimalPlaces, thousandSep) => {
  if (salePrice === '' || price === '') {
    return true;
  }
  if (!decimalPoint || !decimalPlaces || !thousandSep) {
    return true;
  }

  // eslint-disable-next-line no-use-before-define
  const salePriceVal = getNumericPrice(salePrice, decimalPoint, decimalPlaces, thousandSep);
  // eslint-disable-next-line no-use-before-define
  const priceVal = getNumericPrice(price, decimalPoint, decimalPlaces, thousandSep);
  return salePriceVal < priceVal;
});
const isTempTitleValid = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempTitle], title => external_lodash_trim_default()(title) !== '');
const isTempCapacityValid = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempCapacity], capacity => external_lodash_trim_default()(capacity) !== '' && !isNaN(capacity) && capacity > 0);
const isTempSharedCapacityValid = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsTempSharedCapacity], capacity => external_lodash_trim_default()(capacity) !== '' && !isNaN(capacity) && capacity > 0);
const isZeroPriceValid = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempPrice, getTicketsProvider], (price, provider) => {
  if (0 < parseInt(price, 10)) {
    return true;
  }
  if (constants["TC_CLASS"] === provider) {
    return false;
  }
  if (constants["TICKETS_COMMERCE_MODULE_CLASS"] === provider) {
    return IS_FREE_TC_TICKET_ALLOWED;
  }
  return true;
});
const isTicketValid = Object(external_tribe_modules_reselect_["createSelector"])([getTicketTempCapacityType, isTempTitleValid, isTempCapacityValid, isTempSharedCapacityValid, isZeroPriceValid], (capacityType, titleValid, capacityValid, sharedCapacityValid, zeroPriceValid) => {
  if (capacityType === TICKET_TYPES[UNLIMITED]) {
    return titleValid && zeroPriceValid;
  } else if (capacityType === TICKET_TYPES[SHARED]) {
    return titleValid && sharedCapacityValid && zeroPriceValid;
  }
  return titleValid && capacityValid && zeroPriceValid;
});

//
// ─── AMOUNT REDUCERS ────────────────────────────────────────────────────────────
//

const _getTotalCapacity = tickets => tickets.reduce((total, ticket) => {
  const capacity = parseInt(ticket.details.capacity, 10) || 0;
  return total + capacity;
}, 0);
const _getTotalTempCapacity = tickets => tickets.reduce((total, ticket) => {
  const tempCapacity = parseInt(ticket.tempDetails.capacity, 10) || 0;
  return total + tempCapacity;
}, 0);
const _getTotalSold = tickets => tickets.reduce((total, ticket) => {
  const sold = parseInt(ticket.sold, 10) || 0;
  return total + sold;
}, 0);
const _getTotalAvailable = tickets => tickets.reduce((total, ticket) => {
  const available = parseInt(ticket.available, 10) || 0;
  return total + available;
}, 0);
const getIndependentTicketsCapacity = Object(external_tribe_modules_reselect_["createSelector"])(getIndependentTickets, _getTotalCapacity);
const getIndependentTicketsTempCapacity = Object(external_tribe_modules_reselect_["createSelector"])(getIndependentTickets, _getTotalTempCapacity);
const getIndependentTicketsSold = Object(external_tribe_modules_reselect_["createSelector"])(getIndependentTickets, _getTotalSold);
const getIndependentTicketsAvailable = Object(external_tribe_modules_reselect_["createSelector"])(getIndependentTickets, _getTotalAvailable);
const getSharedTicketsSold = Object(external_tribe_modules_reselect_["createSelector"])(getSharedTickets, _getTotalSold);
const getSharedTicketsAvailable = Object(external_tribe_modules_reselect_["createSelector"])([getTicketsSharedCapacityInt, getSharedTicketsSold], (sharedCapacity, sharedSold) => Math.max(sharedCapacity - sharedSold, 0));
const getIndependentAndSharedTicketsCapacity = Object(external_tribe_modules_reselect_["createSelector"])([getIndependentTicketsCapacity, getTicketsSharedCapacityInt], (independentCapacity, sharedCapacity) => independentCapacity + sharedCapacity);
const getIndependentAndSharedTicketsTempCapacity = Object(external_tribe_modules_reselect_["createSelector"])([getIndependentTicketsTempCapacity, getTicketsTempSharedCapacityInt], (independentTempCapacity, tempSharedCapacity) => independentTempCapacity + tempSharedCapacity);
const getIndependentAndSharedTicketsSold = Object(external_tribe_modules_reselect_["createSelector"])([getIndependentTicketsSold, getSharedTicketsSold], (independentSold, sharedSold) => independentSold + sharedSold);
const getIndependentAndSharedTicketsAvailable = Object(external_tribe_modules_reselect_["createSelector"])([getIndependentTicketsAvailable, getSharedTicketsAvailable], (independentAvailable, sharedAvailable) => independentAvailable + sharedAvailable);

//
// ─── MISC SELECTORS ─────────────────────────────────────────────────────────────
//

const getTicketProviders = () => {
  const tickets = ticketsConfig();
  return tickets.providers || [];
};
const getDefaultTicketProvider = () => {
  const tickets = ticketsConfig();
  return tickets.default_provider || '';
};
const hasValidTicketProvider = () => {
  const provider = getDefaultTicketProvider();
  return provider !== '' && provider !== constants["RSVP_CLASS"];
};
const hasMultipleTicketProviders = Object(external_tribe_modules_reselect_["createSelector"])([getTicketProviders], providers => providers.length > 1);
const hasTicketProviders = Object(external_tribe_modules_reselect_["createSelector"])([getTicketProviders], providers => providers.length > 0);
const canCreateTickets = Object(external_tribe_modules_reselect_["createSelector"])([hasTicketProviders, hasValidTicketProvider], (providers, validDefaultProvider) => providers && validDefaultProvider);
const getCurrentPostTypeLabel = function () {
  var _post$labels;
  let key = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'singular_name';
  const post = postConfig();
  return (post === null || post === void 0 ? void 0 : (_post$labels = post.labels) === null || _post$labels === void 0 ? void 0 : _post$labels[key]) || 'Post';
};
const currentPostIsEvent = () => {
  const post = postConfig();
  return (post === null || post === void 0 ? void 0 : post.type) === 'tribe_events';
};
const getNumericPrice = (price, decimalPoint, decimalPlaces, thousandSep) => {
  // Remove thousand separators.
  let newValue = price.replace(new RegExp('\\' + thousandSep, 'g'), '');

  // Replace decimal separator with period.
  newValue = newValue.replace(decimalPoint, '.');

  // Round to specified number of decimal places.
  newValue = parseFloat(newValue).toFixed(decimalPlaces);
  newValue = parseInt(newValue.replace('.', ''));
  return newValue;
};
// EXTERNAL MODULE: external "wp.hooks"
var external_wp_hooks_ = __webpack_require__("g56x");

// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/utils.js
/**
 * External dependencies
 */

const {
  settings,
  priceSettings,
  tickets: utils_ticketsConfig
} = external_tribe_common_utils_["globals"];
/**
 * Internal dependencies
 */



/**
 * Get currency symbol by provider
 *
 * @param provider The tickets provider class
 */
const getProviderCurrency = provider => {
  const tickets = utils_ticketsConfig();
  const providers = getTicketProviders();

  // if we don't get the provider, return the default one
  if ('' === provider) {
    return tickets.default_currency;
  }
  const [result] = providers.filter(el => el.class === provider);
  return result ? result.currency : tickets.default_currency;
};

/**
 * Get currency decimal point by provider
 *
 * @param provider The tickets provider class
 */
const getProviderCurrencyDecimalPoint = provider => {
  const providers = getTicketProviders();
  const defaultCurrencyDecimalPoint = '.';

  // if we don't get the provider, return the default one
  if ('' === provider) {
    return defaultCurrencyDecimalPoint;
  }
  const [result] = providers.filter(el => el.class === provider);
  return result ? result.currency_decimal_point : defaultCurrencyDecimalPoint;
};

/**
 * Get currency number of decimals by provider
 *
 * @param provider The tickets provider class
 */
const getProviderCurrencyNumberOfDecimals = provider => {
  const providers = getTicketProviders();
  const defaultCurrencyNumberOfDecimals = 2;

  // if we don't get the provider, return the default one
  if ('' === provider) {
    return defaultCurrencyNumberOfDecimals;
  }
  const [result] = providers.filter(el => el.class === provider);
  return result ? result.currency_number_of_decimals : defaultCurrencyNumberOfDecimals;
};

/**
 * Get currency thousands separator by provider
 *
 * @param provider The tickets provider class
 */
const getProviderCurrencyThousandsSep = provider => {
  const providers = getTicketProviders();
  const defaultCurrencyThousandsSep = ',';

  // if we don't get the provider, return the default one
  if ('' === provider) {
    return defaultCurrencyThousandsSep;
  }
  const [result] = providers.filter(el => el.class === provider);
  return result ? result.currency_thousands_sep : defaultCurrencyThousandsSep;
};

/**
 * Get the default provider's currency symbol
 */
const getDefaultProviderCurrency = () => {
  return getProviderCurrency(getDefaultTicketProvider());
};

/**
 * Get the default provider's currency decimal point
 */
const getDefaultProviderCurrencyDecimalPoint = () => {
  return getProviderCurrencyDecimalPoint(getDefaultTicketProvider());
};

/**
 * Get the default provider's currency number of decimals
 */
const getDefaultProviderCurrencyNumberOfDecimals = () => {
  return getProviderCurrencyNumberOfDecimals(getDefaultTicketProvider());
};

/**
 * Get the default provider's currency thousands separator
 */
const getDefaultProviderCurrencyThousandsSep = () => {
  return getProviderCurrencyThousandsSep(getDefaultTicketProvider());
};

/**
 * Get currency position
 */
const getDefaultCurrencyPosition = () => {
  const position = external_tribe_common_utils_["string"].isTruthy(settings() && settings().reverseCurrencyPosition) ? 'suffix' : 'prefix';
  return priceSettings() && priceSettings().defaultCurrencyPosition ? priceSettings().defaultCurrencyPosition : position;
};

/**
 * Returns whether a Ticket is editable in the context of the current post.
 *
 * @param {number} ticketId The ticket ID.
 * @param {string} ticketType The ticket types, e.g. `default`, `series_pass`, etc.
 * @param {Object} post The post object.
 */
const isTicketEditableFromPost = (ticketId, ticketType, post) => {
  /**
   * Filters whether a ticket can be edited from a post.
   *
   * @since 5.8.0
   * @param {boolean} isEditable Whether or not the ticket can be edited from the post.
   * @param {Object} context The context of the filter.
   * @param {number} context.ticketId The ticket ID.
   * @param {string} context.ticketType The ticket types, e.g. `default`, `series_pass`, etc.
   * @param {Object} context.post The post object.
   */
  return Object(external_wp_hooks_["applyFilters"])('tec.tickets.blocks.editTicketFromPost', true, {
    ticketId,
    ticketType,
    post
  });
};
// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/reducers/tickets/ticket/details.js

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



const details_datePickerFormat = external_tribe_common_utils_["globals"].tecDateSettings().datepickerFormat;
const currentMoment = external_moment_default()();
const bufferDuration = external_tribe_common_utils_["globals"].tickets().end_sale_buffer_duration ? external_tribe_common_utils_["globals"].tickets().end_sale_buffer_duration : 2;
const bufferYears = external_tribe_common_utils_["globals"].tickets().end_sale_buffer_years ? external_tribe_common_utils_["globals"].tickets().end_sale_buffer_years : 1;
const details_endMoment = currentMoment.clone().add(bufferDuration, 'hours').add(bufferYears, 'years');
const details_startDateInput = details_datePickerFormat ? currentMoment.format(external_tribe_common_utils_["moment"].toFormat(details_datePickerFormat)) : external_tribe_common_utils_["moment"].toDate(currentMoment);
const details_endDateInput = details_datePickerFormat ? details_endMoment.format(external_tribe_common_utils_["moment"].toFormat(details_datePickerFormat)) : external_tribe_common_utils_["moment"].toDate(details_endMoment);
const details_iac = external_tribe_common_utils_["globals"].iacVars().iacDefault ? external_tribe_common_utils_["globals"].iacVars().iacDefault : 'none';
const DEFAULT_STATE = {
  attendeeInfoFields: [],
  title: '',
  description: '',
  price: '',
  on_sale: false,
  sku: '',
  iac: details_iac,
  startDate: external_tribe_common_utils_["moment"].toDatabaseDate(currentMoment),
  startDateInput: details_startDateInput,
  startDateMoment: currentMoment,
  endDate: external_tribe_common_utils_["moment"].toDatabaseDate(details_endMoment),
  endDateInput: details_endDateInput,
  endDateMoment: details_endMoment,
  startTime: external_tribe_common_utils_["moment"].toDatabaseTime(currentMoment),
  endTime: external_tribe_common_utils_["moment"].toDatabaseTime(details_endMoment),
  startTimeInput: external_tribe_common_utils_["moment"].toTime(currentMoment),
  endTimeInput: external_tribe_common_utils_["moment"].toTime(details_endMoment),
  capacityType: constants["TICKET_TYPES"][constants["UNLIMITED"]],
  capacity: '',
  type: 'default',
  salePriceChecked: false,
  salePrice: '',
  saleStartDate: '',
  saleStartDateInput: '',
  saleStartDateMoment: '',
  saleEndDate: '',
  saleEndDateInput: '',
  saleEndDateMoment: ''
};
/* harmony default export */ var ticket_details = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["SET_TICKET_TITLE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        title: action.payload.title
      });
    case types["SET_TICKET_DESCRIPTION"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        description: action.payload.description
      });
    case types["SET_TICKET_PRICE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        price: action.payload.price
      });
    case types["SET_TICKET_ON_SALE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        on_sale: action.payload.onSale
      });
    case types["SET_TICKET_SKU"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        sku: action.payload.sku
      });
    case types["SET_TICKET_IAC_SETTING"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        iac: action.payload.iac
      });
    case types["SET_TICKET_START_DATE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        startDate: action.payload.startDate
      });
    case types["SET_TICKET_START_DATE_INPUT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        startDateInput: action.payload.startDateInput
      });
    case types["SET_TICKET_START_DATE_MOMENT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        startDateMoment: action.payload.startDateMoment
      });
    case types["SET_TICKET_END_DATE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        endDate: action.payload.endDate
      });
    case types["SET_TICKET_END_DATE_INPUT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        endDateInput: action.payload.endDateInput
      });
    case types["SET_TICKET_END_DATE_MOMENT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        endDateMoment: action.payload.endDateMoment
      });
    case types["SET_TICKET_START_TIME"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        startTime: action.payload.startTime
      });
    case types["SET_TICKET_END_TIME"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        endTime: action.payload.endTime
      });
    case types["SET_TICKET_START_TIME_INPUT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        startTimeInput: action.payload.startTimeInput
      });
    case types["SET_TICKET_END_TIME_INPUT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        endTimeInput: action.payload.endTimeInput
      });
    case types["SET_TICKET_CAPACITY_TYPE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        capacityType: action.payload.capacityType
      });
    case types["SET_TICKET_CAPACITY"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        capacity: action.payload.capacity
      });
    case types["SET_TICKET_ATTENDEE_INFO_FIELDS"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        attendeeInfoFields: action.payload.attendeeInfoFields
      });
    case types["SET_TICKET_TYPE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        type: action.payload.type
      });
    case types["SET_TICKET_SALE_PRICE_CHECK"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        salePriceChecked: action.payload.checked
      });
    case types["SET_TICKET_SALE_PRICE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        salePrice: action.payload.salePrice
      });
    case types["SET_TICKET_SALE_START_DATE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        saleStartDate: action.payload.startDate
      });
    case types["SET_TICKET_SALE_START_DATE_INPUT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        saleStartDateInput: action.payload.startDateInput
      });
    case types["SET_TICKET_SALE_START_DATE_MOMENT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        saleStartDateMoment: action.payload.startDateMoment
      });
    case types["SET_TICKET_SALE_END_DATE"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        saleEndDate: action.payload.endDate
      });
    case types["SET_TICKET_SALE_END_DATE_INPUT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        saleEndDateInput: action.payload.endDateInput
      });
    case types["SET_TICKET_SALE_END_DATE_MOMENT"]:
      return _objectSpread(_objectSpread({}, state), {}, {
        saleEndDateMoment: action.payload.endDateMoment
      });
    default:
      return state;
  }
});
// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/reducers/tickets/ticket/temp-details.js

function temp_details_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function temp_details_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? temp_details_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : temp_details_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



const temp_details_datePickerFormat = external_tribe_common_utils_["globals"].tecDateSettings().datepickerFormat;
const temp_details_currentMoment = external_moment_default()();
const temp_details_bufferDuration = external_tribe_common_utils_["globals"].tickets().end_sale_buffer_duration ? external_tribe_common_utils_["globals"].tickets().end_sale_buffer_duration : 2;
const temp_details_bufferYears = external_tribe_common_utils_["globals"].tickets().end_sale_buffer_years ? external_tribe_common_utils_["globals"].tickets().end_sale_buffer_years : 1;
const temp_details_endMoment = temp_details_currentMoment.clone().add(temp_details_bufferDuration, 'hours').add(temp_details_bufferYears, 'years');
const temp_details_startDateInput = temp_details_datePickerFormat ? temp_details_currentMoment.format(external_tribe_common_utils_["moment"].toFormat(temp_details_datePickerFormat)) : external_tribe_common_utils_["moment"].toDate(temp_details_currentMoment);
const temp_details_endDateInput = temp_details_datePickerFormat ? temp_details_endMoment.format(external_tribe_common_utils_["moment"].toFormat(temp_details_datePickerFormat)) : external_tribe_common_utils_["moment"].toDate(temp_details_endMoment);
const temp_details_iac = external_tribe_common_utils_["globals"].iacVars().iacDefault ? external_tribe_common_utils_["globals"].iacVars().iacDefault : 'none';
const temp_details_DEFAULT_STATE = {
  title: '',
  description: '',
  price: '',
  sku: '',
  iac: temp_details_iac,
  startDate: external_tribe_common_utils_["moment"].toDatabaseDate(temp_details_currentMoment),
  startDateInput: temp_details_startDateInput,
  startDateMoment: temp_details_currentMoment,
  endDate: external_tribe_common_utils_["moment"].toDatabaseDate(temp_details_endMoment),
  endDateInput: temp_details_endDateInput,
  endDateMoment: temp_details_endMoment,
  startTime: external_tribe_common_utils_["moment"].toDatabaseTime(temp_details_currentMoment),
  endTime: external_tribe_common_utils_["moment"].toDatabaseTime(temp_details_endMoment),
  startTimeInput: external_tribe_common_utils_["moment"].toTime(temp_details_currentMoment),
  endTimeInput: external_tribe_common_utils_["moment"].toTime(temp_details_endMoment),
  capacityType: constants["TICKET_TYPES"][constants["UNLIMITED"]],
  capacity: '',
  salePriceChecked: false,
  salePrice: '',
  saleStartDate: '',
  saleStartDateInput: '',
  saleStartDateMoment: '',
  saleEndDate: '',
  saleEndDateInput: '',
  saleEndDateMoment: ''
};
/* harmony default export */ var temp_details = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : temp_details_DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["SET_TICKET_TEMP_TITLE"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        title: action.payload.title
      });
    case types["SET_TICKET_TEMP_DESCRIPTION"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        description: action.payload.description
      });
    case types["SET_TICKET_TEMP_PRICE"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        price: action.payload.price
      });
    case types["SET_TICKET_TEMP_SKU"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        sku: action.payload.sku
      });
    case types["SET_TICKET_TEMP_IAC_SETTING"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        iac: action.payload.iac
      });
    case types["SET_TICKET_TEMP_START_DATE"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startDate: action.payload.startDate
      });
    case types["SET_TICKET_TEMP_START_DATE_INPUT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startDateInput: action.payload.startDateInput
      });
    case types["SET_TICKET_TEMP_START_DATE_MOMENT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startDateMoment: action.payload.startDateMoment
      });
    case types["SET_TICKET_TEMP_END_DATE"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endDate: action.payload.endDate
      });
    case types["SET_TICKET_TEMP_END_DATE_INPUT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endDateInput: action.payload.endDateInput
      });
    case types["SET_TICKET_TEMP_END_DATE_MOMENT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endDateMoment: action.payload.endDateMoment
      });
    case types["SET_TICKET_TEMP_START_TIME"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startTime: action.payload.startTime
      });
    case types["SET_TICKET_TEMP_END_TIME"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endTime: action.payload.endTime
      });
    case types["SET_TICKET_TEMP_START_TIME_INPUT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startTimeInput: action.payload.startTimeInput
      });
    case types["SET_TICKET_TEMP_END_TIME_INPUT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endTimeInput: action.payload.endTimeInput
      });
    case types["SET_TICKET_TEMP_CAPACITY_TYPE"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        capacityType: action.payload.capacityType
      });
    case types["SET_TICKET_TEMP_CAPACITY"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        capacity: action.payload.capacity
      });
    case types["SET_TICKET_TEMP_SALE_PRICE_CHECK"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        salePriceChecked: action.payload.checked
      });
    case types["SET_TICKET_TEMP_SALE_PRICE"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        salePrice: action.payload.salePrice
      });
    case types["SET_TICKET_TEMP_SALE_START_DATE"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        saleStartDate: action.payload.startDate
      });
    case types["SET_TICKET_TEMP_SALE_START_DATE_INPUT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        saleStartDateInput: action.payload.startDateInput
      });
    case types["SET_TICKET_TEMP_SALE_START_DATE_MOMENT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        saleStartDateMoment: action.payload.startDateMoment
      });
    case types["SET_TICKET_TEMP_SALE_END_DATE"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        saleEndDate: action.payload.endDate
      });
    case types["SET_TICKET_TEMP_SALE_END_DATE_INPUT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        saleEndDateInput: action.payload.endDateInput
      });
    case types["SET_TICKET_TEMP_SALE_END_DATE_MOMENT"]:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        saleEndDateMoment: action.payload.endDateMoment
      });
    default:
      return state;
  }
});
// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/reducers/tickets/ticket.js

function ticket_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function ticket_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ticket_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ticket_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Internal dependencies
 */




const ticket_DEFAULT_STATE = {
  details: DEFAULT_STATE,
  tempDetails: temp_details_DEFAULT_STATE,
  sold: 0,
  available: 0,
  ticketId: 0,
  currencyDecimalPoint: getDefaultProviderCurrencyDecimalPoint(),
  currencyNumberOfDecimals: getDefaultProviderCurrencyNumberOfDecimals(),
  currencyPosition: getDefaultCurrencyPosition(),
  currencySymbol: getDefaultProviderCurrency(),
  currencyThousandsSep: getDefaultProviderCurrencyThousandsSep(),
  provider: '',
  hasAttendeeInfoFields: false,
  isLoading: false,
  isModalOpen: false,
  hasBeenCreated: false,
  hasChanges: false,
  hasDurationError: false,
  isSelected: false
};
/* harmony default export */ var tickets_ticket = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : ticket_DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["SET_TICKET_ATTENDEE_INFO_FIELDS"]:
    case types["SET_TICKET_TITLE"]:
    case types["SET_TICKET_DESCRIPTION"]:
    case types["SET_TICKET_PRICE"]:
    case types["SET_TICKET_ON_SALE"]:
    case types["SET_TICKET_SKU"]:
    case types["SET_TICKET_IAC_SETTING"]:
    case types["SET_TICKET_START_DATE"]:
    case types["SET_TICKET_START_DATE_INPUT"]:
    case types["SET_TICKET_START_DATE_MOMENT"]:
    case types["SET_TICKET_END_DATE"]:
    case types["SET_TICKET_END_DATE_INPUT"]:
    case types["SET_TICKET_END_DATE_MOMENT"]:
    case types["SET_TICKET_START_TIME"]:
    case types["SET_TICKET_END_TIME"]:
    case types["SET_TICKET_START_TIME_INPUT"]:
    case types["SET_TICKET_END_TIME_INPUT"]:
    case types["SET_TICKET_CAPACITY_TYPE"]:
    case types["SET_TICKET_CAPACITY"]:
    case types["SET_TICKET_TYPE"]:
    case types["SET_TICKET_TYPE_DESCRIPTION"]:
    case types["SET_TICKET_TYPE_ICON_URL"]:
    case types["SET_TICKET_TYPE_NAME"]:
    case types["SET_TICKET_SALE_PRICE_CHECK"]:
    case types["SET_TICKET_SALE_PRICE"]:
    case types["SET_TICKET_SALE_START_DATE"]:
    case types["SET_TICKET_SALE_START_DATE_INPUT"]:
    case types["SET_TICKET_SALE_START_DATE_MOMENT"]:
    case types["SET_TICKET_SALE_END_DATE"]:
    case types["SET_TICKET_SALE_END_DATE_INPUT"]:
    case types["SET_TICKET_SALE_END_DATE_MOMENT"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        details: ticket_details(state.details, action)
      });
    case types["SET_TICKET_TEMP_TITLE"]:
    case types["SET_TICKET_TEMP_DESCRIPTION"]:
    case types["SET_TICKET_TEMP_PRICE"]:
    case types["SET_TICKET_TEMP_SKU"]:
    case types["SET_TICKET_TEMP_IAC_SETTING"]:
    case types["SET_TICKET_TEMP_START_DATE"]:
    case types["SET_TICKET_TEMP_START_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_START_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_END_DATE"]:
    case types["SET_TICKET_TEMP_END_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_END_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_START_TIME"]:
    case types["SET_TICKET_TEMP_END_TIME"]:
    case types["SET_TICKET_TEMP_START_TIME_INPUT"]:
    case types["SET_TICKET_TEMP_END_TIME_INPUT"]:
    case types["SET_TICKET_TEMP_CAPACITY_TYPE"]:
    case types["SET_TICKET_TEMP_CAPACITY"]:
    case types["SET_TICKET_TEMP_SALE_PRICE_CHECK"]:
    case types["SET_TICKET_TEMP_SALE_PRICE"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE_MOMENT"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        tempDetails: temp_details(state.tempDetails, action)
      });
    case types["SET_TICKET_SOLD"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        sold: action.payload.sold
      });
    case types["SET_TICKET_AVAILABLE"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        available: action.payload.available
      });
    case types["SET_TICKET_ID"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        ticketId: action.payload.ticketId
      });
    case types["SET_TICKET_CURRENCY_SYMBOL"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        currencySymbol: action.payload.currencySymbol
      });
    case types["SET_TICKET_CURRENCY_POSITION"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        currencyPosition: action.payload.currencyPosition
      });
    case types["SET_TICKET_PROVIDER"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        provider: action.payload.provider
      });
    case types["SET_TICKET_HAS_ATTENDEE_INFO_FIELDS"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        hasAttendeeInfoFields: action.payload.hasAttendeeInfoFields
      });
    case types["SET_TICKET_IS_LOADING"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        isLoading: action.payload.isLoading
      });
    case types["SET_TICKET_IS_MODAL_OPEN"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        isModalOpen: action.payload.isModalOpen
      });
    case types["SET_TICKET_HAS_BEEN_CREATED"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        hasBeenCreated: action.payload.hasBeenCreated
      });
    case types["SET_TICKET_HAS_CHANGES"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        hasChanges: action.payload.hasChanges
      });
    case types["SET_TICKET_HAS_DURATION_ERROR"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        hasDurationError: action.payload.hasDurationError
      });
    case types["SET_TICKET_IS_SELECTED"]:
      return ticket_objectSpread(ticket_objectSpread({}, state), {}, {
        isSelected: action.payload.isSelected
      });
    default:
      return state;
  }
});
// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/reducers/tickets.js

function tickets_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function tickets_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? tickets_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : tickets_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


const byClientId = function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["SET_TICKET_TITLE"]:
    case types["SET_TICKET_DESCRIPTION"]:
    case types["SET_TICKET_PRICE"]:
    case types["SET_TICKET_ON_SALE"]:
    case types["SET_TICKET_SKU"]:
    case types["SET_TICKET_IAC_SETTING"]:
    case types["SET_TICKET_START_DATE"]:
    case types["SET_TICKET_START_DATE_INPUT"]:
    case types["SET_TICKET_START_DATE_MOMENT"]:
    case types["SET_TICKET_END_DATE"]:
    case types["SET_TICKET_END_DATE_INPUT"]:
    case types["SET_TICKET_END_DATE_MOMENT"]:
    case types["SET_TICKET_START_TIME"]:
    case types["SET_TICKET_END_TIME"]:
    case types["SET_TICKET_START_TIME_INPUT"]:
    case types["SET_TICKET_END_TIME_INPUT"]:
    case types["SET_TICKET_CAPACITY_TYPE"]:
    case types["SET_TICKET_CAPACITY"]:
    case types["SET_TICKET_TEMP_TITLE"]:
    case types["SET_TICKET_TEMP_DESCRIPTION"]:
    case types["SET_TICKET_TEMP_PRICE"]:
    case types["SET_TICKET_TEMP_SKU"]:
    case types["SET_TICKET_TEMP_IAC_SETTING"]:
    case types["SET_TICKET_TEMP_START_DATE"]:
    case types["SET_TICKET_TEMP_START_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_START_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_END_DATE"]:
    case types["SET_TICKET_TEMP_END_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_END_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_START_TIME"]:
    case types["SET_TICKET_TEMP_END_TIME"]:
    case types["SET_TICKET_TEMP_START_TIME_INPUT"]:
    case types["SET_TICKET_TEMP_END_TIME_INPUT"]:
    case types["SET_TICKET_TEMP_CAPACITY_TYPE"]:
    case types["SET_TICKET_TEMP_CAPACITY"]:
    case types["SET_TICKET_SOLD"]:
    case types["SET_TICKET_AVAILABLE"]:
    case types["SET_TICKET_ID"]:
    case types["SET_TICKET_CURRENCY_SYMBOL"]:
    case types["SET_TICKET_CURRENCY_POSITION"]:
    case types["SET_TICKET_PROVIDER"]:
    case types["SET_TICKET_HAS_ATTENDEE_INFO_FIELDS"]:
    case types["SET_TICKET_ATTENDEE_INFO_FIELDS"]:
    case types["SET_TICKET_IS_LOADING"]:
    case types["SET_TICKET_IS_MODAL_OPEN"]:
    case types["SET_TICKET_HAS_BEEN_CREATED"]:
    case types["SET_TICKET_HAS_CHANGES"]:
    case types["SET_TICKET_HAS_DURATION_ERROR"]:
    case types["SET_TICKET_IS_SELECTED"]:
    case types["SET_TICKET_TYPE"]:
    case types["SET_TICKET_TYPE_DESCRIPTION"]:
    case types["SET_TICKET_TYPE_ICON_URL"]:
    case types["SET_TICKET_TYPE_NAME"]:
    case types["REGISTER_TICKET_BLOCK"]:
    case types["SET_TICKET_SALE_PRICE_CHECK"]:
    case types["SET_TICKET_TEMP_SALE_PRICE_CHECK"]:
    case types["SET_TICKET_SALE_PRICE"]:
    case types["SET_TICKET_TEMP_SALE_PRICE"]:
    case types["SET_TICKET_SALE_START_DATE"]:
    case types["SET_TICKET_SALE_START_DATE_INPUT"]:
    case types["SET_TICKET_SALE_START_DATE_MOMENT"]:
    case types["SET_TICKET_SALE_END_DATE"]:
    case types["SET_TICKET_SALE_END_DATE_INPUT"]:
    case types["SET_TICKET_SALE_END_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE_MOMENT"]:
      return tickets_objectSpread(tickets_objectSpread({}, state), {}, {
        [action.payload.clientId]: tickets_ticket(state[action.payload.clientId], action)
      });
    case types["REMOVE_TICKET_BLOCK"]:
      return external_lodash_omit_default()(state, [action.payload.clientId]);
    case types["REMOVE_TICKET_BLOCKS"]:
      return {};
    default:
      return state;
  }
};
const allClientIds = function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["REGISTER_TICKET_BLOCK"]:
      return [...state, action.payload.clientId];
    case types["REMOVE_TICKET_BLOCK"]:
      return state.filter(clientId => action.payload.clientId !== clientId);
    case types["REMOVE_TICKET_BLOCKS"]:
      return [];
    default:
      return state;
  }
};
/* harmony default export */ var reducers_tickets = (Object(external_tribe_modules_redux_["combineReducers"])({
  byClientId,
  allClientIds
}));
// EXTERNAL MODULE: ./src/modules/data/blocks/ticket/reducers/header-image.js
var header_image = __webpack_require__("YEbw");

// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/reducer.js

function reducer_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function reducer_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? reducer_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : reducer_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Internal dependencies
 */



const reducer_DEFAULT_STATE = {
  headerImage: header_image["a" /* DEFAULT_STATE */],
  isSelected: false,
  isSettingsOpen: false,
  isSettingsLoading: false,
  provider: '',
  sharedCapacity: '',
  tempSharedCapacity: '',
  tickets: reducers_tickets(undefined, {})
};
/* harmony default export */ var reducer = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : reducer_DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["SET_TICKETS_HEADER_IMAGE"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        headerImage: Object(header_image["b" /* default */])(state.headerImage, action)
      });
    case types["SET_TICKETS_IS_SELECTED"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        isSelected: action.payload.isSelected
      });
    case types["SET_TICKETS_IS_SETTINGS_OPEN"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        isSettingsOpen: action.payload.isSettingsOpen
      });
    case types["SET_TICKETS_IS_SETTINGS_LOADING"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        isSettingsLoading: action.payload.isSettingsLoading
      });
    case types["SET_TICKETS_PROVIDER"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        provider: action.payload.provider
      });
    case types["SET_TICKETS_SHARED_CAPACITY"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        sharedCapacity: action.payload.sharedCapacity
      });
    case types["SET_TICKETS_TEMP_SHARED_CAPACITY"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        tempSharedCapacity: action.payload.tempSharedCapacity
      });
    case types["SET_TICKET_TITLE"]:
    case types["SET_TICKET_DESCRIPTION"]:
    case types["SET_TICKET_PRICE"]:
    case types["SET_TICKET_ON_SALE"]:
    case types["SET_TICKET_SKU"]:
    case types["SET_TICKET_IAC_SETTING"]:
    case types["SET_TICKET_START_DATE"]:
    case types["SET_TICKET_START_DATE_INPUT"]:
    case types["SET_TICKET_START_DATE_MOMENT"]:
    case types["SET_TICKET_END_DATE"]:
    case types["SET_TICKET_END_DATE_INPUT"]:
    case types["SET_TICKET_END_DATE_MOMENT"]:
    case types["SET_TICKET_START_TIME"]:
    case types["SET_TICKET_END_TIME"]:
    case types["SET_TICKET_START_TIME_INPUT"]:
    case types["SET_TICKET_END_TIME_INPUT"]:
    case types["SET_TICKET_CAPACITY_TYPE"]:
    case types["SET_TICKET_CAPACITY"]:
    case types["SET_TICKET_SALE_PRICE_CHECK"]:
    case types["SET_TICKET_SALE_PRICE"]:
    case types["SET_TICKET_SALE_START_DATE"]:
    case types["SET_TICKET_SALE_START_DATE_INPUT"]:
    case types["SET_TICKET_SALE_START_DATE_MOMENT"]:
    case types["SET_TICKET_SALE_END_DATE"]:
    case types["SET_TICKET_SALE_END_DATE_INPUT"]:
    case types["SET_TICKET_SALE_END_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_TITLE"]:
    case types["SET_TICKET_TEMP_DESCRIPTION"]:
    case types["SET_TICKET_TEMP_PRICE"]:
    case types["SET_TICKET_TEMP_SKU"]:
    case types["SET_TICKET_TEMP_IAC_SETTING"]:
    case types["SET_TICKET_TEMP_START_DATE"]:
    case types["SET_TICKET_TEMP_START_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_START_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_END_DATE"]:
    case types["SET_TICKET_TEMP_END_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_END_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_START_TIME"]:
    case types["SET_TICKET_TEMP_END_TIME"]:
    case types["SET_TICKET_TEMP_START_TIME_INPUT"]:
    case types["SET_TICKET_TEMP_END_TIME_INPUT"]:
    case types["SET_TICKET_TEMP_CAPACITY_TYPE"]:
    case types["SET_TICKET_TEMP_CAPACITY"]:
    case types["SET_TICKET_TEMP_SALE_PRICE_CHECK"]:
    case types["SET_TICKET_TEMP_SALE_PRICE"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_SALE_START_DATE_MOMENT"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE_INPUT"]:
    case types["SET_TICKET_TEMP_SALE_END_DATE_MOMENT"]:
    case types["SET_TICKET_SOLD"]:
    case types["SET_TICKET_AVAILABLE"]:
    case types["SET_TICKET_ID"]:
    case types["SET_TICKET_CURRENCY_SYMBOL"]:
    case types["SET_TICKET_CURRENCY_POSITION"]:
    case types["SET_TICKET_PROVIDER"]:
    case types["SET_TICKET_HAS_ATTENDEE_INFO_FIELDS"]:
    case types["SET_TICKET_ATTENDEE_INFO_FIELDS"]:
    case types["SET_TICKET_IS_LOADING"]:
    case types["SET_TICKET_IS_MODAL_OPEN"]:
    case types["SET_TICKET_HAS_BEEN_CREATED"]:
    case types["SET_TICKET_HAS_CHANGES"]:
    case types["SET_TICKET_HAS_DURATION_ERROR"]:
    case types["SET_TICKET_IS_SELECTED"]:
    case types["SET_TICKET_TYPE"]:
    case types["SET_TICKET_TYPE_DESCRIPTION"]:
    case types["SET_TICKET_TYPE_ICON_URL"]:
    case types["SET_TICKET_TYPE_NAME"]:
    case types["REGISTER_TICKET_BLOCK"]:
    case types["REMOVE_TICKET_BLOCK"]:
    case types["REMOVE_TICKET_BLOCKS"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        tickets: reducers_tickets(state.tickets, action)
      });
    case types["SET_UNEDITABLE_TICKETS"]:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        uneditableTickets: action.payload.uneditableTickets,
        uneditableTicketsLoading: false
      });
    case types["SET_UNEDITABLE_TICKETS_LOADING"]:
      if (action.loading) {
        return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
          uneditableTickets: [],
          uneditableTicketsLoading: true
        });
      }
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        uneditableTicketsLoading: false
      });
    default:
      return state;
  }
});
// EXTERNAL MODULE: ./src/modules/data/blocks/ticket/actions.js
var actions = __webpack_require__("hImw");

// EXTERNAL MODULE: external "lodash.includes"
var external_lodash_includes_ = __webpack_require__("Etll");
var external_lodash_includes_default = /*#__PURE__*/__webpack_require__.n(external_lodash_includes_);

// EXTERNAL MODULE: external "tribe.modules.reduxSaga.effects"
var external_tribe_modules_reduxSaga_effects_ = __webpack_require__("RmXt");

// EXTERNAL MODULE: external "wp.data"
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external "wp.blocks"
var external_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: ./src/modules/data/blocks/rsvp/actions.js
var rsvp_actions = __webpack_require__("qkor");

// EXTERNAL MODULE: ./src/modules/data/blocks/rsvp/reducers/header-image.js
var reducers_header_image = __webpack_require__("56gU");

// EXTERNAL MODULE: ./src/modules/data/utils.js
var utils = __webpack_require__("BNqv");

// EXTERNAL MODULE: external "tribe.common.data"
var external_tribe_common_data_ = __webpack_require__("ZNLL");

// EXTERNAL MODULE: ./src/modules/data/shared/move/types.js
var move_types = __webpack_require__("4Uf/");

// EXTERNAL MODULE: ./src/modules/data/shared/move/selectors.js
var selectors = __webpack_require__("ihba");

// EXTERNAL MODULE: ./src/modules/data/shared/sagas.js
var sagas = __webpack_require__("ghtj");

// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/sagas.js


function sagas_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function sagas_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? sagas_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : sagas_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External Dependencies
 */

/**
 * Wordpress dependencies
 */





/**
 * Internal dependencies
 */
















const {
  UNLIMITED: sagas_UNLIMITED,
  SHARED: sagas_SHARED,
  TICKET_TYPES: sagas_TICKET_TYPES,
  PROVIDER_CLASS_TO_PROVIDER_MAPPING
} = constants;
const {
  restNonce,
  tecDateSettings
} = external_tribe_common_utils_["globals"];
const {
  wpREST
} = external_tribe_common_utils_["api"];
function* createMissingTicketBlocks(tickets) {
  const {
    insertBlock,
    updateBlockListSettings
  } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_wp_data_["dispatch"], 'core/block-editor');
  const {
    getBlockCount,
    getBlocks
  } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_wp_data_["select"], 'core/editor');
  const ticketsBlocks = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([getBlocks(), 'filter'], block => block.name === 'tribe/tickets');
  ticketsBlocks.forEach(_ref => {
    let {
      clientId
    } = _ref;
    // Since we're not using the store provided by WordPress, we need to update the block list
    // settings for the Tickets block here to allow the tickets-item block to be inserted.
    // If the WP store did not initialize yet when the `insertBlock` function is called, the
    // block will not be inserted and there will be a silent failure.
    updateBlockListSettings(clientId, {
      allowedBlocks: ['tribe/tickets-item']
    });
    tickets.forEach(ticketId => {
      const attributes = {
        hasBeenCreated: true,
        ticketId
      };
      const nextChildPosition = getBlockCount(clientId);
      const block = Object(external_wp_blocks_["createBlock"])('tribe/tickets-item', attributes);
      insertBlock(block, nextChildPosition, clientId, false);
    });
  });
}
function formatTicketFromRestToAttributeFormat(ticket) {
  var _ticket$capacity_deta, _ticket$capacity_deta2, _ticket$capacity_deta3, _ticket$capacity_deta4, _ticket$cost_details, _ticket$cost_details2, _ticket$cost_details3, _ticket$cost_details4;
  const capacity = (ticket === null || ticket === void 0 ? void 0 : (_ticket$capacity_deta = ticket.capacity_details) === null || _ticket$capacity_deta === void 0 ? void 0 : _ticket$capacity_deta.max) || 0;
  const available = (ticket === null || ticket === void 0 ? void 0 : (_ticket$capacity_deta2 = ticket.capacity_details) === null || _ticket$capacity_deta2 === void 0 ? void 0 : _ticket$capacity_deta2.available) || 0;
  const capacityType = (ticket === null || ticket === void 0 ? void 0 : (_ticket$capacity_deta3 = ticket.capacity_details) === null || _ticket$capacity_deta3 === void 0 ? void 0 : _ticket$capacity_deta3.global_stock_mode) || constants["UNLIMITED"];
  const sold = (ticket === null || ticket === void 0 ? void 0 : (_ticket$capacity_deta4 = ticket.capacity_details) === null || _ticket$capacity_deta4 === void 0 ? void 0 : _ticket$capacity_deta4.sold) || 0;
  const isShared = capacityType === constants["SHARED"] || capacityType === constants["CAPPED"] || capacityType === constants["GLOBAL"];
  return {
    id: ticket.id,
    type: ticket.type,
    title: ticket.title,
    description: ticket.description,
    capacityType: capacityType,
    price: (ticket === null || ticket === void 0 ? void 0 : ticket.cost) || '0.00',
    capacity: capacity,
    available: available,
    sharedCapacity: capacity,
    sold: sold,
    shareSold: sold,
    isShared: isShared,
    currencyDecimalPoint: (ticket === null || ticket === void 0 ? void 0 : (_ticket$cost_details = ticket.cost_details) === null || _ticket$cost_details === void 0 ? void 0 : _ticket$cost_details.currency_decimal_separator) || '.',
    currencyNumberOfDecimals: (ticket === null || ticket === void 0 ? void 0 : (_ticket$cost_details2 = ticket.cost_details) === null || _ticket$cost_details2 === void 0 ? void 0 : _ticket$cost_details2.currency_decimal_numbers) || 2,
    currencyPosition: (ticket === null || ticket === void 0 ? void 0 : (_ticket$cost_details3 = ticket.cost_details) === null || _ticket$cost_details3 === void 0 ? void 0 : _ticket$cost_details3.currency_position) || 'prefix',
    currencySymbol: (ticket === null || ticket === void 0 ? void 0 : ticket.cost_details.currency_symbol) || '$',
    currencyThousandsSep: (ticket === null || ticket === void 0 ? void 0 : (_ticket$cost_details4 = ticket.cost_details) === null || _ticket$cost_details4 === void 0 ? void 0 : _ticket$cost_details4.currency_thousand_separator) || ','
  };
}
function* updateUneditableTickets() {
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setUneditableTicketsLoading"](true));
  const post = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(() => Object(external_wp_data_["select"])('core/editor').getCurrentPost());
  if (!(post !== null && post !== void 0 && post.id)) {
    return;
  }

  // Get **all** the tickets, not just the uneditable ones. Filtering will take care of removing the editable ones.
  const {
    response,
    data = {
      tickets: []
    }
  } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(wpREST, {
    namespace: 'tribe/tickets/v1',
    path: `tickets/?include_post=${post.id}&per_page=30`,
    initParams: {
      method: 'GET'
    }
  });
  if ((response === null || response === void 0 ? void 0 : response.status) !== 200 || !Array.isArray(data === null || data === void 0 ? void 0 : data.tickets)) {
    // Something went wrong, bail out.
    return null;
  }
  const restFormatUneditableTickets = data.tickets
  // Remove the editable tickets.
  .filter(ticket => !isTicketEditableFromPost(ticket.id, ticket.type, post));
  const uneditableTickets = [];
  if (restFormatUneditableTickets.length >= 1) {
    for (const ticket of restFormatUneditableTickets) {
      const formattedUneditableTicket = yield formatTicketFromRestToAttributeFormat(ticket);
      uneditableTickets.push(formattedUneditableTicket);
    }
  }

  /**
   * Fires after the uneditable tickets have been updated from the backend.
   *
   * @since 5.8.0
   * @param {Object[]} uneditableTickets The uneditable tickets just fetched from the backend.
   */
  Object(external_wp_hooks_["doAction"])('tec.tickets.blocks.uneditableTicketsUpdated', uneditableTickets);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setUneditableTickets"](uneditableTickets));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setUneditableTicketsLoading"](false));
}
function* setTicketsInitialState(action) {
  const {
    get
  } = action.payload;
  const currentPost = yield Object(external_wp_data_["select"])('core/editor').getCurrentPost();
  const header = parseInt(get('header', header_image["a" /* DEFAULT_STATE */].id), 10);
  const sharedCapacity = get('sharedCapacity');
  // Shape: [ {id: int, type: string}, ... ].
  const allTickets = JSON.parse(get('tickets', '[]'));
  const {
    editableTickets,
    uneditableTickets
  } = allTickets.reduce((acc, ticket) => {
    if (isTicketEditableFromPost(ticket.id, ticket.type, currentPost)) {
      acc.editableTickets.push(ticket);
    } else {
      acc.uneditableTickets.push(ticket);
    }
    return acc;
  }, {
    editableTickets: [],
    uneditableTickets: []
  });

  // Get only the IDs of the tickets that are not in the block list already.
  const ticketsInBlock = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketsIdsInBlocks);
  const ticketsDiff = editableTickets.filter(item => !external_lodash_includes_default()(ticketsInBlock, item.id));
  if (ticketsDiff.length >= 1) {
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])(createMissingTicketBlocks, ticketsDiff.map(ticket => ticket.id));
  }
  if (uneditableTickets.length >= 1) {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setUneditableTickets"](uneditableTickets));
  }

  // Meta value is '0' however fields use empty string as default
  if (sharedCapacity !== '0') {
    yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsSharedCapacity"](sharedCapacity)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsTempSharedCapacity"](sharedCapacity))]);
  }
  if (!isNaN(header) && header !== 0) {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["fetchTicketsHeaderImage"](header));
  }
  let provider = get('provider', reducer_DEFAULT_STATE.provider);
  if (provider === constants["RSVP_CLASS"] || !provider) {
    const defaultProvider = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getDefaultTicketProvider);
    provider = defaultProvider === constants["RSVP_CLASS"] ? '' : defaultProvider;
  }
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsProvider"](provider));
}
function* resetTicketsBlock() {
  const hasCreatedTickets = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors_hasCreatedTickets);
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["removeTicketBlocks"]()), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsIsSettingsOpen"](false))]);
  if (!hasCreatedTickets) {
    const currentMeta = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getEditedPostAttribute'], 'meta');
    const newMeta = sagas_objectSpread(sagas_objectSpread({}, currentMeta), {}, {
      [utils["d" /* KEY_TICKET_CAPACITY */]]: ''
    });
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["dispatch"])('core/editor'), 'editPost'], {
      meta: newMeta
    });
    yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsSharedCapacity"]('')), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsTempSharedCapacity"](''))]);
  }
}
function* setTicketInitialState(action) {
  const {
    clientId,
    get
  } = action.payload;
  const ticketId = get('ticketId', ticket_DEFAULT_STATE.ticketId);
  const hasBeenCreated = get('hasBeenCreated', ticket_DEFAULT_STATE.hasBeenCreated);
  const datePickerFormat = tecDateSettings().datepickerFormat;
  const publishDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getEditedPostAttribute'], 'date');
  const startMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, publishDate);
  const startDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, startMoment);
  const startDateInput = yield datePickerFormat ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, startMoment, datePickerFormat) : Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, startMoment);
  const startTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseTime, startMoment);
  const startTimeInput = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toTime, startMoment);
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartDate"](clientId, startDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartDateInput"](clientId, startDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartDateMoment"](clientId, startMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartTime"](clientId, startTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartTimeInput"](clientId, startTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDate"](clientId, startDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDateInput"](clientId, startDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDateMoment"](clientId, startMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartTime"](clientId, startTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartTimeInput"](clientId, startTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasBeenCreated"](clientId, hasBeenCreated))]);
  const isEvent = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["e" /* isTribeEventPostType */]);

  // Only run this on events post type.
  if (isEvent && window.tribe.events) {
    // This try-catch may be redundant given the above if statement.
    try {
      // NOTE: This requires TEC to be installed, if not installed, do not set an end date
      // Ticket purchase window should end when event starts
      const eventStart = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(tribe.events.data.blocks.datetime.selectors.getStart);
      const endMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, eventStart);
      const endDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, endMoment);
      const endDateInput = yield datePickerFormat ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, endMoment, datePickerFormat) : Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, endMoment);
      const endTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseTime, endMoment);
      const endTimeInput = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toTime, endMoment);
      yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDate"](clientId, endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDateInput"](clientId, endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDateMoment"](clientId, endMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndTime"](clientId, endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndTimeInput"](clientId, endTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDate"](clientId, endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDateInput"](clientId, endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDateMoment"](clientId, endMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndTime"](clientId, endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndTimeInput"](clientId, endTimeInput))]);
    } catch (err) {
      console.error(err);
      // ¯\_(ツ)_/¯
    }
  }

  const hasTicketsPlus = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(external_tribe_common_data_["plugins"].selectors.hasPlugin, external_tribe_common_data_["plugins"].constants.TICKETS_PLUS);
  if (hasTicketsPlus) {
    yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketCapacityType"](clientId, constants["TICKET_TYPES"][constants["SHARED"]])), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempCapacityType"](clientId, constants["TICKET_TYPES"][constants["SHARED"]]))]);
  }
  const sharedCapacity = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketsSharedCapacity);
  if (sharedCapacity) {
    yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketCapacity"](clientId, sharedCapacity)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempCapacity"](clientId, sharedCapacity))]);
  }
  if (ticketId !== 0) {
    yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketId"](clientId, ticketId)), Object(external_tribe_modules_reduxSaga_effects_["call"])(fetchTicket, {
      payload: {
        clientId,
        ticketId
      }
    })]);
  }
  yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketDurationError, clientId);
  yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(saveTicketWithPostSave, clientId);
}
function* setBodyDetails(clientId) {
  const body = new FormData();
  const props = {
    clientId
  };
  const rootClientId = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getBlockRootClientId'], clientId);
  const ticketProvider = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketProvider, props);
  const ticketsProvider = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketsProvider);
  body.append('post_id', yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getCurrentPostId']));
  body.append('provider', ticketProvider || ticketsProvider);
  body.append('name', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempTitle, props));
  body.append('description', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempDescription, props));
  body.append('price', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempPrice, props));
  body.append('start_date', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartDate, props));
  body.append('start_time', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartTime, props));
  body.append('end_date', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDate, props));
  body.append('end_time', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndTime, props));
  body.append('sku', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSku, props));
  body.append('iac', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempIACSetting, props));
  body.append('menu_order', yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getBlockIndex'], clientId, rootClientId));
  const capacityType = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempCapacityType, props);
  const capacity = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempCapacity, props);
  const isUnlimited = capacityType === sagas_TICKET_TYPES[sagas_UNLIMITED];
  body.append('ticket[mode]', isUnlimited ? '' : capacityType);
  body.append('ticket[capacity]', isUnlimited ? '' : capacity);
  if (capacityType === sagas_TICKET_TYPES[sagas_SHARED]) {
    body.append('ticket[event_capacity]', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketsTempSharedCapacity));
  }
  const showSalePrice = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors_showSalePrice, props);
  if (showSalePrice) {
    body.append('ticket[sale_price][checked]', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTempSalePriceChecked, props));
    body.append('ticket[sale_price][price]', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTempSalePrice, props));
    body.append('ticket[sale_price][start_date]', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSaleStartDate, props));
    body.append('ticket[sale_price][end_date]', yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSaleEndDate, props));
  }
  return body;
}
function* removeTicketBlock(clientId) {
  const {
    removeBlock
  } = Object(external_wp_data_["dispatch"])('core/editor');
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["removeTicketBlock"](clientId)), Object(external_tribe_modules_reduxSaga_effects_["call"])(removeBlock, clientId)]);
}
function* fetchTicket(action) {
  const {
    ticketId,
    clientId
  } = action.payload;
  if (ticketId === 0) {
    return;
  }
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIsLoading"](clientId, true));
  try {
    const {
      response,
      data: ticket
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(wpREST, {
      path: `tickets/${ticketId}`,
      namespace: 'tribe/tickets/v1'
    });
    const {
      status = '',
      provider
    } = ticket;
    if (response.status === 404 || status === 'trash' || provider === constants["RSVP"]) {
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(removeTicketBlock, clientId);
      return;
    }
    if (response.ok) {
      /* eslint-disable camelcase */

      const {
        totals = {},
        available_from,
        available_until,
        cost_details,
        title,
        description,
        sku,
        iac,
        capacity_type,
        capacity,
        supports_attendee_information,
        attendee_information_fields,
        type,
        sale_price_data,
        on_sale
      } = ticket;
      /* eslint-enable camelcase */

      const datePickerFormat = tecDateSettings().datepickerFormat;
      const startMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, available_from);
      const startDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, startMoment);
      const startDateInput = yield datePickerFormat ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, startMoment, datePickerFormat) : Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, startMoment);
      const startTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseTime, startMoment);
      const startTimeInput = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toTime, startMoment);
      let endMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, '');
      let endDate = '';
      let endDateInput = '';
      let endTime = '';
      let endTimeInput = '';
      if (available_until) {
        // eslint-disable-line camelcase
        endMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, available_until);
        endDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, endMoment);
        endDateInput = yield datePickerFormat ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, endMoment, datePickerFormat) : Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, endMoment);
        endTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseTime, endMoment);
        endTimeInput = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toTime, endMoment);
      }
      const salePriceChecked = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.enabled) || false;
      const salePrice = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.sale_price) || '';
      const sale_start_date = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.start_date) || ''; // eslint-disable-line camelcase
      const saleStartDateMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, sale_start_date);
      const saleStartDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, saleStartDateMoment);
      const saleStartDateInput = yield datePickerFormat ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, saleStartDateMoment, datePickerFormat) : Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, saleStartDateMoment);
      const sale_end_date = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.end_date) || ''; // eslint-disable-line camelcase
      const saleEndDateMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, sale_end_date);
      const saleEndDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, saleEndDateMoment);
      const saleEndDateInput = yield datePickerFormat ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, saleEndDateMoment, datePickerFormat) : Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, saleEndDateMoment);
      const details = {
        attendeeInfoFields: attendee_information_fields,
        title,
        description,
        price: cost_details.values[0],
        on_sale,
        sku,
        iac,
        startDate,
        startDateInput,
        startDateMoment: startMoment,
        endDate,
        endDateInput,
        endDateMoment: endMoment,
        startTime,
        endTime,
        startTimeInput,
        endTimeInput,
        capacityType: capacity_type,
        capacity,
        type,
        salePriceChecked,
        salePrice,
        saleStartDate,
        saleStartDateInput,
        saleStartDateMoment,
        saleEndDate,
        saleEndDateInput,
        saleEndDateMoment
      };
      yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketDetails"](clientId, details)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempDetails"](clientId, details)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSold"](clientId, totals.sold)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketAvailable"](clientId, totals.stock)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketCurrencySymbol"](clientId, cost_details.currency_symbol)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketCurrencyPosition"](clientId, cost_details.currency_position)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketProvider"](clientId, provider)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasAttendeeInfoFields"](clientId, supports_attendee_information)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasBeenCreated"](clientId, true))]);
    }
  } catch (e) {
    console.error(e);
    /**
     * @todo handle error scenario
     */
  }

  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIsLoading"](clientId, false));
}
function* createNewTicket(action) {
  const {
    clientId
  } = action.payload;
  const props = {
    clientId
  };
  const {
    add_ticket_nonce = ''
  } = restNonce(); // eslint-disable-line camelcase
  const body = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(setBodyDetails, clientId);
  body.append('add_ticket_nonce', add_ticket_nonce);
  try {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIsLoading"](clientId, true));
    const {
      response,
      data: ticket
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(wpREST, {
      path: 'tickets/',
      namespace: 'tribe/tickets/v1',
      initParams: {
        method: 'POST',
        body
      }
    });
    if (response.ok) {
      const sharedCapacity = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketsSharedCapacity);
      const tempSharedCapacity = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketsTempSharedCapacity);
      if (sharedCapacity === '' && !isNaN(tempSharedCapacity) && tempSharedCapacity > 0) {
        yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsSharedCapacity"](tempSharedCapacity));
      }
      const available = ticket.capacity_details.available === -1 ? 0 : ticket.capacity_details.available;
      const {
        sale_price_data
      } = ticket; // eslint-disable-line camelcase
      const salePriceChecked = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.enabled) || false;
      const salePrice = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.sale_price) || '';
      const [title, description, price, sku, iac, startDate, startDateInput, startDateMoment, endDate, endDateInput, endDateMoment, startTime, endTime, startTimeInput, endTimeInput, capacityType, capacity, saleStartDate, saleStartDateInput, saleStartDateMoment, saleEndDate, saleEndDateInput, saleEndDateMoment] = yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempTitle, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempDescription, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempPrice, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSku, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempIACSetting, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartDate, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartDateInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartDateMoment, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDate, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDateInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDateMoment, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartTime, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndTime, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartTimeInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndTimeInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempCapacityType, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempCapacity, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSaleStartDate, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSaleStartDateInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSaleStartDateMoment, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSaleEndDate, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSaleEndDateInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSaleEndDateMoment, props)]);
      yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketDetails"](clientId, {
        title,
        description,
        price,
        sku,
        iac,
        startDate,
        startDateInput,
        startDateMoment,
        endDate,
        endDateInput,
        endDateMoment,
        startTime,
        endTime,
        startTimeInput,
        endTimeInput,
        capacityType,
        capacity,
        salePriceChecked,
        salePrice,
        saleStartDate,
        saleStartDateInput,
        saleStartDateMoment,
        saleEndDate,
        saleEndDateInput,
        saleEndDateMoment
      })), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTempSalePriceChecked"](clientId, salePriceChecked)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTempSalePrice"](clientId, salePrice)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketId"](clientId, ticket.id)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasBeenCreated"](clientId, true)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketAvailable"](clientId, available)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketProvider"](clientId, PROVIDER_CLASS_TO_PROVIDER_MAPPING[ticket.provider_class])), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](clientId, false))]);
      yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(saveTicketWithPostSave, clientId);
    }
  } catch (e) {
    console.error(e);
    /**
     * @todo: handle error scenario
     */
  } finally {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIsLoading"](clientId, false));
  }
}
function* updateTicket(action) {
  const {
    clientId
  } = action.payload;
  const props = {
    clientId
  };
  const {
    edit_ticket_nonce = ''
  } = restNonce(); // eslint-disable-line camelcase
  const body = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(setBodyDetails, clientId);
  body.append('edit_ticket_nonce', edit_ticket_nonce);
  const ticketId = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketId, props);
  try {
    const data = [];
    for (const [key, value] of body.entries()) {
      data.push(`${encodeURIComponent(key)}=${encodeURIComponent(value)}`);
    }
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIsLoading"](clientId, true));
    const {
      response,
      data: ticket
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(wpREST, {
      path: `tickets/${ticketId}`,
      namespace: 'tribe/tickets/v1',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
      },
      initParams: {
        method: 'PUT',
        body: data.join('&')
      }
    });
    if (response.ok) {
      const {
        capacity_details,
        sale_price_data,
        on_sale
      } = ticket; // eslint-disable-line camelcase, max-len
      const available = capacity_details.available === -1 ? 0 : capacity_details.available;
      const salePriceChecked = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.enabled) || false;
      const salePrice = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.sale_price) || '';
      const datePickerFormat = tecDateSettings().datepickerFormat;
      const sale_start_date = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.start_date) || ''; // eslint-disable-line camelcase
      const saleStartDateMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, sale_start_date);
      const saleStartDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, saleStartDateMoment);
      const saleStartDateInput = yield datePickerFormat ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, saleStartDateMoment, datePickerFormat) : Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, saleStartDateMoment);
      const sale_end_date = (sale_price_data === null || sale_price_data === void 0 ? void 0 : sale_price_data.end_date) || ''; // eslint-disable-line camelcase
      const saleEndDateMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, sale_end_date);
      const saleEndDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, saleEndDateMoment);
      const saleEndDateInput = yield datePickerFormat ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, saleEndDateMoment, datePickerFormat) : Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDate, saleEndDateMoment);
      const [title, description, price, sku, iac, startDate, startDateInput, startDateMoment, endDate, endDateInput, endDateMoment, startTime, endTime, startTimeInput, endTimeInput, capacityType, capacity] = yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempTitle, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempDescription, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempPrice, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempSku, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempIACSetting, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartDate, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartDateInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartDateMoment, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDate, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDateInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDateMoment, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartTime, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndTime, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartTimeInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndTimeInput, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempCapacityType, props), Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempCapacity, props)]);
      yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketDetails"](clientId, {
        title,
        description,
        price,
        on_sale,
        sku,
        iac,
        startDate,
        startDateInput,
        startDateMoment,
        endDate,
        endDateInput,
        endDateMoment,
        startTime,
        endTime,
        startTimeInput,
        endTimeInput,
        capacityType,
        capacity,
        salePriceChecked,
        salePrice,
        saleStartDate,
        saleStartDateInput,
        saleStartDateMoment,
        saleEndDate,
        saleEndDateInput,
        saleEndDateMoment
      })), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSold"](clientId, capacity_details.sold)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketAvailable"](clientId, available)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](clientId, false)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTempSalePrice"](clientId, salePrice)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTempSalePriceChecked"](clientId, salePriceChecked)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDate"](clientId, saleStartDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDateInput"](clientId, saleStartDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDateMoment"](clientId, saleStartDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDate"](clientId, saleEndDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDateInput"](clientId, saleEndDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDateMoment"](clientId, saleEndDateMoment))]);
    }
  } catch (e) {
    console.error(e);
    /**
     * @todo: handle error scenario
     */
  } finally {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIsLoading"](clientId, false));
  }
}
function* deleteTicket(action) {
  const {
    clientId,
    askForDeletion = true
  } = action.payload;
  const props = {
    clientId
  };
  let shouldDelete = false;
  if (askForDeletion) {
    shouldDelete = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([window, 'confirm'], Object(external_wp_i18n_["__"])('Are you sure you want to delete this ticket? It cannot be undone.', 'event-tickets'));
  } else {
    shouldDelete = true;
  }
  if (shouldDelete) {
    const ticketId = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketId, props);
    const hasBeenCreated = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketHasBeenCreated, props);
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIsSelected"](clientId, false));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["removeTicketBlock"](clientId));
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["dispatch"])('core/editor'), 'clearSelectedBlock']);
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["dispatch"])('core/editor'), 'removeBlocks'], [clientId]);
    if (hasBeenCreated) {
      const {
        remove_ticket_nonce = ''
      } = restNonce(); // eslint-disable-line camelcase
      const postId = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getCurrentPostId']);

      /**
       * Encode params to be passed into the DELETE request as PHP doesn’t transform the request body
       * of a DELETE request into a super global.
       */
      const body = [`${encodeURIComponent('post_id')}=${encodeURIComponent(postId)}`, `${encodeURIComponent('remove_ticket_nonce')}=${encodeURIComponent(remove_ticket_nonce)}` // eslint-disable-line max-len
      ];

      try {
        yield Object(external_tribe_modules_reduxSaga_effects_["call"])(wpREST, {
          path: `tickets/${ticketId}`,
          namespace: 'tribe/tickets/v1',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
          },
          initParams: {
            method: 'DELETE',
            body: body.join('&')
          }
        });
      } catch (e) {
        /**
         * @todo handle error on removal
         */
      }
    }
  }
}
function* fetchTicketsHeaderImage(action) {
  const {
    id
  } = action.payload;
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsIsSettingsLoading"](true));
  try {
    const {
      response,
      data: media
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(wpREST, {
      path: `media/${id}`
    });
    if (response.ok) {
      const headerImage = {
        id: media.id,
        alt: media.alt_text,
        src: media.media_details.sizes.medium.source_url
      };
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsHeaderImage"](headerImage));
    }
  } catch (e) {
    console.error(e);
    /**
     * @todo: handle error scenario
     */
  } finally {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsIsSettingsLoading"](false));
  }
}
function* updateTicketsHeaderImage(action) {
  const {
    image
  } = action.payload;
  const postId = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getCurrentPostId']);
  const body = {
    meta: {
      [utils["i" /* KEY_TICKET_HEADER */]]: `${image.id}`
    }
  };
  try {
    /**
     * @todo: until rsvp and tickets header image can be separated, they need to be linked
     */
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsIsSettingsLoading"](true));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(rsvp_actions["setRSVPIsSettingsLoading"](true));
    const slug = Object(external_wp_data_["select"])('core/editor').getCurrentPostType();
    const postType = Object(external_wp_data_["select"])('core').getPostType(slug);
    const restBase = postType.rest_base;
    const {
      response
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(wpREST, {
      path: `${restBase}/${postId}`,
      headers: {
        'Content-Type': 'application/json'
      },
      initParams: {
        method: 'PUT',
        body: JSON.stringify(body)
      }
    });
    if (response.ok) {
      const headerImage = {
        id: image.id,
        alt: image.alt,
        src: image.sizes.medium.url
      };
      /**
       * @todo: until rsvp and tickets header image can be separated, they need to be linked
       */
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsHeaderImage"](headerImage));
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(rsvp_actions["setRSVPHeaderImage"](headerImage));
    }
  } catch (e) {
    /**
     * @todo: handle error scenario
     */
  } finally {
    /**
     * @todo: until rsvp and tickets header image can be separated, they need to be linked
     */
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsIsSettingsLoading"](false));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(rsvp_actions["setRSVPIsSettingsLoading"](false));
  }
}
function* deleteTicketsHeaderImage() {
  const postId = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getCurrentPostId']);
  const body = {
    meta: {
      [utils["i" /* KEY_TICKET_HEADER */]]: null
    }
  };
  try {
    /**
     * @todo: until rsvp and tickets header image can be separated, they need to be linked
     */
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsIsSettingsLoading"](true));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(rsvp_actions["setRSVPIsSettingsLoading"](true));
    const slug = Object(external_wp_data_["select"])('core/editor').getCurrentPostType();
    const postType = Object(external_wp_data_["select"])('core').getPostType(slug);
    const restBase = postType.rest_base;
    const {
      response
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["api"].wpREST, {
      path: `${restBase}/${postId}`,
      headers: {
        'Content-Type': 'application/json'
      },
      initParams: {
        method: 'PUT',
        body: JSON.stringify(body)
      }
    });
    if (response.ok) {
      /**
       * @todo: until rsvp and tickets header image can be separated, they need to be linked
       */
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsHeaderImage"](header_image["a" /* DEFAULT_STATE */]));
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(rsvp_actions["setRSVPHeaderImage"](reducers_header_image["a" /* DEFAULT_STATE */]));
    }
  } catch (e) {
    /**
     * @todo: handle error scenario
     */
  } finally {
    /**
     * @todo: until rsvp and tickets header image can be separated, they need to be linked
     */
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketsIsSettingsLoading"](false));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(rsvp_actions["setRSVPIsSettingsLoading"](false));
  }
}
function* setTicketDetails(action) {
  const {
    clientId,
    details
  } = action.payload;
  const {
    attendeeInfoFields,
    title,
    description,
    price,
    on_sale,
    // eslint-disable-line camelcase
    sku,
    iac,
    startDate,
    startDateInput,
    startDateMoment,
    endDate,
    endDateInput,
    endDateMoment,
    startTime,
    endTime,
    startTimeInput,
    endTimeInput,
    capacityType,
    capacity,
    type,
    salePriceChecked,
    salePrice,
    saleStartDate,
    saleStartDateInput,
    saleStartDateMoment,
    saleEndDate,
    saleEndDateInput,
    saleEndDateMoment
  } = details;
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketAttendeeInfoFields"](clientId, attendeeInfoFields)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTitle"](clientId, title)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketDescription"](clientId, description)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketPrice"](clientId, price)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketOnSale"](clientId, on_sale)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSku"](clientId, sku)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIACSetting"](clientId, iac)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartDate"](clientId, startDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartDateInput"](clientId, startDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartDateMoment"](clientId, startDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDate"](clientId, endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDateInput"](clientId, endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDateMoment"](clientId, endDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartTime"](clientId, startTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndTime"](clientId, endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketStartTimeInput"](clientId, startTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndTimeInput"](clientId, endTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketCapacityType"](clientId, capacityType)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketCapacity"](clientId, capacity)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketType"](clientId, type)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setSalePriceChecked"](clientId, salePriceChecked)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setSalePrice"](clientId, salePrice)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSaleStartDate"](clientId, saleStartDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSaleStartDateInput"](clientId, saleStartDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSaleStartDateMoment"](clientId, saleStartDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSaleEndDate"](clientId, saleEndDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSaleEndDateInput"](clientId, saleEndDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketSaleEndDateMoment"](clientId, saleEndDateMoment))]);
}
function* setTicketTempDetails(action) {
  const {
    clientId,
    tempDetails
  } = action.payload;
  const {
    title,
    description,
    price,
    sku,
    iac,
    startDate,
    startDateInput,
    startDateMoment,
    endDate,
    endDateInput,
    endDateMoment,
    startTime,
    endTime,
    startTimeInput,
    endTimeInput,
    capacityType,
    capacity,
    salePriceChecked,
    salePrice,
    saleStartDate,
    saleStartDateInput,
    saleStartDateMoment,
    saleEndDate,
    saleEndDateInput,
    saleEndDateMoment
  } = tempDetails;
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempTitle"](clientId, title)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempDescription"](clientId, description)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempPrice"](clientId, price)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSku"](clientId, sku)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempIACSetting"](clientId, iac)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDate"](clientId, startDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDateInput"](clientId, startDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDateMoment"](clientId, startDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDate"](clientId, endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDateInput"](clientId, endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDateMoment"](clientId, endDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartTime"](clientId, startTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndTime"](clientId, endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartTimeInput"](clientId, startTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndTimeInput"](clientId, endTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempCapacityType"](clientId, capacityType)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempCapacity"](clientId, capacity)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTempSalePriceChecked"](clientId, salePriceChecked)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTempSalePrice"](clientId, salePrice)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDate"](clientId, saleStartDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDateInput"](clientId, saleStartDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDateMoment"](clientId, saleStartDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDate"](clientId, saleEndDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDateInput"](clientId, saleEndDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDateMoment"](clientId, saleEndDateMoment))]);
}

/**
 * Allows the Ticket to be saved at the same time a post is being saved.
 * Avoids the user having to open up the Ticket block, and then click update again there,
 * when changing the event start date.
 *
 * @param {string} clientId Client ID of ticket block
 * @export
 * @yields
 */
function* saveTicketWithPostSave(clientId) {
  let savingChannel, notSavingChannel;
  try {
    // Do nothing when not already created
    if (yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketHasBeenCreated, {
      clientId
    })) {
      // Create channels for use
      savingChannel = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["c" /* createWPEditorSavingChannel */]);
      notSavingChannel = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["b" /* createWPEditorNotSavingChannel */]);
      while (true) {
        // Wait for channel to save
        yield Object(external_tribe_modules_reduxSaga_effects_["take"])(savingChannel);

        // Update when saving
        yield Object(external_tribe_modules_reduxSaga_effects_["call"])(updateTicket, {
          payload: {
            clientId
          }
        });

        // Wait for channel to finish saving
        yield Object(external_tribe_modules_reduxSaga_effects_["take"])(notSavingChannel);
      }
    }
  } catch (error) {
    console.error(error);
  } finally {
    // Close save channel if exists
    if (savingChannel) {
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])([savingChannel, 'close']);
    }
    // Close not saving channel if exists
    if (notSavingChannel) {
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])([notSavingChannel, 'close']);
    }
  }
}

/**
 * Will sync all tickets
 *
 * @param {string} prevStartDate Previous start date before latest set date time changes
 * @export
 * @yields
 */
function* syncTicketsSaleEndWithEventStart(prevStartDate) {
  const ticketIds = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketsAllClientIds);
  for (let index = 0; index < ticketIds.length; index++) {
    const clientId = ticketIds[index];
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])(syncTicketSaleEndWithEventStart, prevStartDate, clientId);
  }
}

/**
 * Will sync Tickets sale end to be the same as event start date and time, if field has not been manually edited
 *
 * @borrows TEC - Functionality requires TEC to be enabled
 * @param {string} prevStartDate Previous start date before latest set date time changes
 * @param {string} clientId Client ID of ticket block
 * @export
 * @yields
 */
function* syncTicketSaleEndWithEventStart(prevStartDate, clientId) {
  try {
    const tempEndMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDateMoment, {
      clientId
    });
    const endMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketEndDateMoment, {
      clientId
    });
    const {
      moment: prevEventStartMoment
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["a" /* createDates */], prevStartDate);

    // NOTE: Mutation
    // Convert to use local timezone
    yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["call"])([tempEndMoment, 'local']), Object(external_tribe_modules_reduxSaga_effects_["call"])([endMoment, 'local']), Object(external_tribe_modules_reduxSaga_effects_["call"])([prevEventStartMoment, 'local'])]);

    // If initial end and current end are the same, the RSVP has not been modified
    const isNotManuallyEdited = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([tempEndMoment, 'isSame'], endMoment, 'minute');
    const isSyncedToEventStart = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([tempEndMoment, 'isSame'], prevEventStartMoment, 'minute');
    const isEvent = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["e" /* isTribeEventPostType */]);

    // This if statement may be redundant given the try-catch statement above.
    // Only run this on events post type.
    if (isEvent && window.tribe.events && isNotManuallyEdited && isSyncedToEventStart) {
      const eventStart = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(window.tribe.events.data.blocks.datetime.selectors.getStart);
      const {
        moment: endDateMoment,
        date: endDate,
        dateInput: endDateInput,
        time: endTime,
        timeInput: endTimeInput
      } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["a" /* createDates */], eventStart);
      yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDate"](clientId, endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDateInput"](clientId, endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDateMoment"](clientId, endDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndTime"](clientId, endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndTimeInput"](clientId, endTimeInput)),
      // Sync Ticket end items as well so as not to make state 'manually edited'
      Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDate"](clientId, endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDateInput"](clientId, endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndDateMoment"](clientId, endDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndTime"](clientId, endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketEndTimeInput"](clientId, endTimeInput)),
      // Trigger UI button
      Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](clientId, true)),
      // Handle ticket duration error
      Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketDurationError, clientId)]);
    }
  } catch (error) {
    // ¯\_(ツ)_/¯
    console.error(error);
  }
}

/**
 * Listens for event start date and time changes after RSVP block is loaded.
 *
 * @borrows TEC - Functionality requires TEC to be enabled and post type to be event
 * @export
 * @yields
 */
function* handleEventStartDateChanges() {
  try {
    // Ensure we have a postType set before proceeding
    const postTypeChannel = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["d" /* hasPostTypeChannel */]);
    yield Object(external_tribe_modules_reduxSaga_effects_["take"])(postTypeChannel);
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])([postTypeChannel, 'close']);
    const isEvent = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["e" /* isTribeEventPostType */]);
    if (isEvent && window.tribe.events) {
      const {
        SET_START_DATE_TIME,
        SET_START_TIME
      } = window.tribe.events.data.blocks.datetime.types;
      let syncTask;
      while (true) {
        // Cache current event start date for comparison
        const eventStart = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(window.tribe.events.data.blocks.datetime.selectors.getStart);

        // Wait til use changes date or time on TEC datetime block
        yield Object(external_tribe_modules_reduxSaga_effects_["take"])([SET_START_DATE_TIME, SET_START_TIME]);

        // Important to cancel any pre-existing forks to prevent bad data from being sent
        if (syncTask) {
          yield Object(external_tribe_modules_reduxSaga_effects_["cancel"])(syncTask);
        }
        syncTask = yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(syncTicketsSaleEndWithEventStart, eventStart);
      }
    }
  } catch (error) {
    // ¯\_(ツ)_/¯
    console.error(error);
  }
}
function* handleTicketDurationError(clientId) {
  let hasDurationError = false;
  const startDateMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartDateMoment, {
    clientId
  });
  const endDateMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndDateMoment, {
    clientId
  });
  if (!startDateMoment || !endDateMoment) {
    hasDurationError = true;
  } else {
    const startTime = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempStartTime, {
      clientId
    });
    const endTime = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketTempEndTime, {
      clientId
    });
    const startTimeSeconds = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].toSeconds, startTime, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM_SS);
    const endTimeSeconds = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].toSeconds, endTime, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM_SS);
    const startDateTimeMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].setTimeInSeconds, startDateMoment.clone(), startTimeSeconds);
    const endDateTimeMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].setTimeInSeconds, endDateMoment.clone(), endTimeSeconds);
    const durationHasError = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([startDateTimeMoment, 'isSameOrAfter'], endDateTimeMoment);
    if (durationHasError) {
      hasDurationError = true;
    }
  }
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasDurationError"](clientId, hasDurationError));
}
function* handleTicketStartDate(action) {
  const {
    clientId,
    date,
    dayPickerInput
  } = action.payload;
  const startDateMoment = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, date) : undefined;
  const startDate = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, startDateMoment) : '';
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDate"](clientId, startDate));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDateInput"](clientId, dayPickerInput.state.value));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartDateMoment"](clientId, startDateMoment));
}
function* handleTicketEndDate(action) {
  const {
    clientId,
    date,
    dayPickerInput
  } = action.payload;
  const endDateMoment = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, date) : undefined;
  const endDate = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, endDateMoment) : '';
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDate"](clientId, endDate));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDateInput"](clientId, dayPickerInput.state.value));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndDateMoment"](clientId, endDateMoment));
}
function* handleTicketSaleStartDate(action) {
  const {
    clientId,
    date,
    dayPickerInput
  } = action.payload;
  const startDateMoment = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, date) : undefined;
  const startDate = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, startDateMoment) : '';
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDate"](clientId, startDate));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDateInput"](clientId, dayPickerInput.state.value));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleStartDateMoment"](clientId, startDateMoment));
}
function* handleTicketSaleEndDate(action) {
  const {
    clientId,
    date,
    dayPickerInput
  } = action.payload;
  const endDateMoment = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, date) : undefined;
  const endDate = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, endDateMoment) : '';
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDate"](clientId, endDate));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDateInput"](clientId, dayPickerInput.state.value));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempSaleEndDateMoment"](clientId, endDateMoment));
}
function* handleTicketStartTime(action) {
  const {
    clientId,
    seconds
  } = action.payload;
  const startTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].fromSeconds, seconds, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartTime"](clientId, `${startTime}:00`));
}
function* handleTicketStartTimeInput(action) {
  const {
    clientId,
    seconds
  } = action.payload;
  const startTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].fromSeconds, seconds, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM);
  const startTimeMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, startTime, external_tribe_common_utils_["moment"].TIME_FORMAT, false);
  const startTimeInput = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toTime, startTimeMoment);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempStartTimeInput"](clientId, startTimeInput));
}
function* handleTicketEndTime(action) {
  const {
    clientId,
    seconds
  } = action.payload;
  const endTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].fromSeconds, seconds, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndTime"](clientId, `${endTime}:00`));
}
function* handleTicketEndTimeInput(action) {
  const {
    clientId,
    seconds
  } = action.payload;
  const endTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].fromSeconds, seconds, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM);
  const endTimeMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, endTime, external_tribe_common_utils_["moment"].TIME_FORMAT, false);
  const endTimeInput = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toTime, endTimeMoment);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketTempEndTimeInput"](clientId, endTimeInput));
}
function* handleTicketMove() {
  const ticketClientIds = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getTicketsAllClientIds);
  const modalClientId = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors["a" /* getModalClientId */]);
  if (ticketClientIds.includes(modalClientId)) {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketIsSelected"](modalClientId, false));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["removeTicketBlock"](modalClientId));
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["dispatch"])('core/editor'), 'removeBlocks'], [modalClientId]);
  }
}
function* handler(action) {
  switch (action.type) {
    case types["SET_TICKETS_INITIAL_STATE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(setTicketsInitialState, action);
      break;
    case types["RESET_TICKETS_BLOCK"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(resetTicketsBlock);
      break;
    case types["SET_TICKET_INITIAL_STATE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(setTicketInitialState, action);
      break;
    case types["FETCH_TICKET"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(fetchTicket, action);
      break;
    case types["CREATE_NEW_TICKET"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(createNewTicket, action);
      break;
    case types["UPDATE_TICKET"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(updateTicket, action);
      break;
    case types["DELETE_TICKET"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(deleteTicket, action);
      break;
    case types["FETCH_TICKETS_HEADER_IMAGE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(fetchTicketsHeaderImage, action);
      break;
    case types["UPDATE_TICKETS_HEADER_IMAGE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(updateTicketsHeaderImage, action);
      break;
    case types["DELETE_TICKETS_HEADER_IMAGE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(deleteTicketsHeaderImage);
      break;
    case types["SET_TICKET_DETAILS"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(setTicketDetails, action);
      break;
    case types["SET_TICKET_TEMP_DETAILS"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(setTicketTempDetails, action);
      break;
    case types["HANDLE_TICKET_START_DATE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketStartDate, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketDurationError, action.payload.clientId);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](action.payload.clientId, true));
      break;
    case types["HANDLE_TICKET_END_DATE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketEndDate, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketDurationError, action.payload.clientId);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](action.payload.clientId, true));
      break;
    case types["HANDLE_TICKET_SALE_START_DATE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketSaleStartDate, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](action.payload.clientId, true));
      break;
    case types["HANDLE_TICKET_SALE_END_DATE"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketSaleEndDate, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](action.payload.clientId, true));
      break;
    case types["HANDLE_TICKET_START_TIME"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketStartTime, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketStartTimeInput, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketDurationError, action.payload.clientId);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](action.payload.clientId, true));
      break;
    case types["HANDLE_TICKET_END_TIME"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketEndTime, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketEndTimeInput, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketDurationError, action.payload.clientId);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setTicketHasChanges"](action.payload.clientId, true));
      break;
    case move_types["k" /* MOVE_TICKET_SUCCESS */]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleTicketMove);
      break;
    case types["UPDATE_UNEDITABLE_TICKETS"]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(updateUneditableTickets);
    default:
      break;
  }
}
function* watchers() {
  yield Object(external_tribe_modules_reduxSaga_effects_["takeEvery"])([types["SET_TICKETS_INITIAL_STATE"], types["RESET_TICKETS_BLOCK"], types["SET_TICKET_INITIAL_STATE"], types["FETCH_TICKET"], types["CREATE_NEW_TICKET"], types["UPDATE_TICKET"], types["DELETE_TICKET"], types["FETCH_TICKETS_HEADER_IMAGE"], types["UPDATE_TICKETS_HEADER_IMAGE"], types["DELETE_TICKETS_HEADER_IMAGE"], types["SET_TICKET_DETAILS"], types["SET_TICKET_TEMP_DETAILS"], types["HANDLE_TICKET_START_DATE"], types["HANDLE_TICKET_END_DATE"], types["HANDLE_TICKET_START_TIME"], types["HANDLE_TICKET_END_TIME"], types["HANDLE_TICKET_SALE_START_DATE"], types["HANDLE_TICKET_SALE_END_DATE"], move_types["k" /* MOVE_TICKET_SUCCESS */], types["UPDATE_UNEDITABLE_TICKETS"]], handler);
  yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(handleEventStartDateChanges);
}
// CONCATENATED MODULE: ./src/modules/data/blocks/ticket/index.js
/**
 * Internal dependencies
 */








/* harmony default export */ var blocks_ticket = __webpack_exports__["c"] = (reducer);


/***/ }),

/***/ "RmXt":
/***/ (function(module, exports) {

module.exports = tribe.modules.reduxSaga.effects;

/***/ }),

/***/ "U/eK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return showModal; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return hideModal; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return setModalData; });
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("4Uf/");
/* eslint-disable camelcase */
/**
 * Internal Dependencies
 */

const showModal = (ticketId, clientId) => ({
  type: _types__WEBPACK_IMPORTED_MODULE_0__[/* SHOW_MODAL */ "n"],
  payload: {
    ticketId,
    clientId
  }
});
const hideModal = () => ({
  type: _types__WEBPACK_IMPORTED_MODULE_0__[/* HIDE_MODAL */ "g"]
});
const setModalData = payload => ({
  type: _types__WEBPACK_IMPORTED_MODULE_0__[/* SET_MODAL_DATA */ "m"],
  payload
});

/***/ }),

/***/ "XNR4":
/***/ (function(module, exports) {

module.exports = lodash.some;

/***/ }),

/***/ "XNrZ":
/***/ (function(module, exports) {

module.exports = lodash.trim;

/***/ }),

/***/ "YEbw":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return DEFAULT_STATE; });
/* harmony import */ var _moderntribe_tickets_data_blocks_ticket_types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("enZp");
/**
 * Internal dependencies
 */


/**
 * Full payload from gutenberg media upload is not used,
 * only id, alt, and src are used for this specific case.
 */
const DEFAULT_STATE = {
  id: 0,
  src: '',
  alt: ''
};
/* harmony default export */ __webpack_exports__["b"] = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case _moderntribe_tickets_data_blocks_ticket_types__WEBPACK_IMPORTED_MODULE_0__["SET_TICKETS_HEADER_IMAGE"]:
      return {
        id: action.payload.id,
        src: action.payload.src,
        alt: action.payload.alt
      };
    default:
      return state;
  }
});

/***/ }),

/***/ "YhH6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "blocks", function() { return /* reexport */ modules_blocks_namespaceObject; });
__webpack_require__.d(__webpack_exports__, "data", function() { return /* reexport */ data_namespaceObject; });
__webpack_require__.d(__webpack_exports__, "elements", function() { return /* reexport */ modules_elements; });
__webpack_require__.d(__webpack_exports__, "icons", function() { return /* reexport */ icons; });

// NAMESPACE OBJECT: ./src/modules/data/index.js
var data_namespaceObject = {};
__webpack_require__.r(data_namespaceObject);
__webpack_require__.d(data_namespaceObject, "initStore", function() { return initStore; });
__webpack_require__.d(data_namespaceObject, "getStore", function() { return getStore; });

// NAMESPACE OBJECT: ./src/modules/blocks/index.js
var modules_blocks_namespaceObject = {};
__webpack_require__.r(modules_blocks_namespaceObject);
__webpack_require__.d(modules_blocks_namespaceObject, "default", function() { return modules_blocks; });

// EXTERNAL MODULE: external "wp.blocks"
var external_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external "tribe.modules.redux"
var external_tribe_modules_redux_ = __webpack_require__("rKB8");

// EXTERNAL MODULE: ./src/modules/data/blocks/rsvp/index.js + 7 modules
var rsvp = __webpack_require__("n+fr");

// EXTERNAL MODULE: ./src/modules/data/blocks/ticket/index.js + 9 modules
var ticket = __webpack_require__("QFGf");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/defineProperty.js
var defineProperty = __webpack_require__("lSNA");
var defineProperty_default = /*#__PURE__*/__webpack_require__.n(defineProperty);

// EXTERNAL MODULE: ./src/modules/data/utils.js
var utils = __webpack_require__("BNqv");

// CONCATENATED MODULE: ./src/modules/data/blocks/attendees/types.js
/* eslint-disable max-len */
/**
 * Internal dependencies
 */

const SET_ATTENDEES_INITIAL_STATE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_ATTENDEES_INITIAL_STATE`;
const SET_ATTENDEES_TITLE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_ATTENDEES_TITLE`;
const SET_ATTENDEES_DISPLAY_TITLE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_ATTENDEES_DISPLAY_TITLE`;
const SET_ATTENDEES_DISPLAY_SUBTITLE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_ATTENDEES_DISPLAY_SUBTITLE`;
// CONCATENATED MODULE: ./src/modules/data/blocks/attendees/reducer.js

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Internal dependencies
 */

const DEFAULT_STATE = {
  title: '',
  displayTitle: true,
  displaySubtitle: true
};
/* harmony default export */ var reducer = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case SET_ATTENDEES_TITLE:
      return _objectSpread(_objectSpread({}, state), {}, {
        title: action.payload.title
      });
    case SET_ATTENDEES_DISPLAY_TITLE:
      return _objectSpread(_objectSpread({}, state), {}, {
        displayTitle: action.payload.displayTitle
      });
    case SET_ATTENDEES_DISPLAY_SUBTITLE:
      return _objectSpread(_objectSpread({}, state), {}, {
        displaySubtitle: action.payload.displaySubtitle
      });
    default:
      return state;
  }
});
// EXTERNAL MODULE: external "tribe.modules.reselect"
var external_tribe_modules_reselect_ = __webpack_require__("MWqi");

// CONCATENATED MODULE: ./src/modules/data/blocks/attendees/selectors.js
/**
 * External dependencies
 */

const getAttendeesBlock = state => state.tickets.blocks.attendees;
const getTitle = Object(external_tribe_modules_reselect_["createSelector"])([getAttendeesBlock], attendees => attendees.title);
const getDisplayTitle = Object(external_tribe_modules_reselect_["createSelector"])([getAttendeesBlock], attendees => attendees.displayTitle);
const getDisplaySubtitle = Object(external_tribe_modules_reselect_["createSelector"])([getAttendeesBlock], attendees => attendees.displaySubtitle);
// CONCATENATED MODULE: ./src/modules/data/blocks/attendees/actions.js
/**
 * Internal dependencies
 */

const actions_setTitle = title => ({
  type: SET_ATTENDEES_TITLE,
  payload: {
    title
  }
});
const setDisplayTitle = displayTitle => ({
  type: SET_ATTENDEES_DISPLAY_TITLE,
  payload: {
    displayTitle
  }
});
const setDisplaySubtitle = displaySubtitle => ({
  type: SET_ATTENDEES_DISPLAY_SUBTITLE,
  payload: {
    displaySubtitle
  }
});
const setInitialState = payload => ({
  type: SET_ATTENDEES_INITIAL_STATE,
  payload
});
// EXTERNAL MODULE: external "tribe.modules.reduxSaga.effects"
var external_tribe_modules_reduxSaga_effects_ = __webpack_require__("RmXt");

// CONCATENATED MODULE: ./src/modules/data/blocks/attendees/sagas.js
/**
 * External Dependencies
 */


/**
 * Internal dependencies
 */



function* sagas_setInitialState(action) {
  const {
    get
  } = action.payload;
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions_setTitle(get('title', DEFAULT_STATE.title))), Object(external_tribe_modules_reduxSaga_effects_["put"])(setDisplayTitle(get('displayTitle', DEFAULT_STATE.displayTitle))), Object(external_tribe_modules_reduxSaga_effects_["put"])(setDisplaySubtitle(get('displaySubtitle', DEFAULT_STATE.displaySubtitle)))]);
}
function* watchers() {
  yield Object(external_tribe_modules_reduxSaga_effects_["takeEvery"])(SET_ATTENDEES_INITIAL_STATE, sagas_setInitialState);
}
// CONCATENATED MODULE: ./src/modules/data/blocks/attendees/index.js
/**
 * Internal dependencies
 */





/* harmony default export */ var attendees = (reducer);

// CONCATENATED MODULE: ./src/modules/data/blocks/reducer.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



/* harmony default export */ var blocks_reducer = (Object(external_tribe_modules_redux_["combineReducers"])({
  rsvp: rsvp["b" /* default */],
  ticket: ticket["c" /* default */],
  attendees: attendees
}));
// CONCATENATED MODULE: ./src/modules/data/blocks/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var blocks = (blocks_reducer);
// EXTERNAL MODULE: ./src/modules/data/shared/move/types.js
var types = __webpack_require__("4Uf/");

// CONCATENATED MODULE: ./src/modules/data/shared/move/reducers/posts.js

function posts_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function posts_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? posts_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : posts_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Internal dependencies
 */

const posts_DEFAULT_STATE = {
  isFetching: false,
  posts: {}
};
function posts() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : posts_DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["a" /* FETCH_POST_CHOICES */]:
      return posts_objectSpread(posts_objectSpread({}, state), {}, {
        isFetching: true
      });
    case types["c" /* FETCH_POST_CHOICES_SUCCESS */]:
      return posts_objectSpread(posts_objectSpread(posts_objectSpread({}, state), action.data), {}, {
        isFetching: false
      });
    case types["b" /* FETCH_POST_CHOICES_ERROR */]:
      return posts_objectSpread(posts_objectSpread({}, state), {}, {
        isFetching: false
      });
    default:
      return state;
  }
}
// CONCATENATED MODULE: ./src/modules/data/shared/move/reducers/postTypes.js

function postTypes_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function postTypes_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? postTypes_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : postTypes_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Internal dependencies
 */

const postTypes_DEFAULT_STATE = {
  isFetching: false,
  posts: {}
};
function postTypes() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : postTypes_DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["d" /* FETCH_POST_TYPES */]:
      return postTypes_objectSpread(postTypes_objectSpread({}, state), {}, {
        isFetching: true
      });
    case types["f" /* FETCH_POST_TYPES_SUCCESS */]:
      return postTypes_objectSpread(postTypes_objectSpread(postTypes_objectSpread({}, state), action.data), {}, {
        isFetching: false
      });
    case types["e" /* FETCH_POST_TYPES_ERROR */]:
      return postTypes_objectSpread(postTypes_objectSpread({}, state), {}, {
        isFetching: false
      });
    default:
      return state;
  }
}
// CONCATENATED MODULE: ./src/modules/data/shared/move/reducers/ui.js

function ui_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function ui_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ui_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ui_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Internal dependencies
 */

const ui_DEFAULT_STATE = {
  showModal: false
};
function ui() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : ui_DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["n" /* SHOW_MODAL */]:
      return ui_objectSpread(ui_objectSpread({}, state), {}, {
        showModal: true
      });
    case types["g" /* HIDE_MODAL */]:
      return ui_objectSpread(ui_objectSpread({}, state), {}, {
        showModal: false
      });
    default:
      return state;
  }
}
// CONCATENATED MODULE: ./src/modules/data/shared/move/reducers/modal.js

function modal_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function modal_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? modal_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : modal_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/* eslint-disable camelcase */
/**
 * Internal dependencies
 */

const modal_DEFAULT_STATE = {
  post_type: 'all',
  search_terms: '',
  target_post_id: null,
  ticketId: null,
  clientId: null,
  isSubmitting: false
};
function modal() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : modal_DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types["m" /* SET_MODAL_DATA */]:
      return modal_objectSpread(modal_objectSpread({}, state), action.payload);
    case types["i" /* MOVE_TICKET */]:
      return modal_objectSpread(modal_objectSpread({}, state), {}, {
        isSubmitting: true
      });
    case types["j" /* MOVE_TICKET_ERROR */]:
    case types["k" /* MOVE_TICKET_SUCCESS */]:
      return modal_objectSpread(modal_objectSpread({}, state), {}, {
        isSubmitting: false
      });
    case types["l" /* RESET_MODAL_DATA */]:
      return modal_DEFAULT_STATE;
    default:
      return state;
  }
}
// CONCATENATED MODULE: ./src/modules/data/shared/move/reducers/index.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */




/* harmony default export */ var reducers = (Object(external_tribe_modules_redux_["combineReducers"])({
  posts: posts,
  postTypes: postTypes,
  ui: ui,
  modal: modal
}));
// CONCATENATED MODULE: ./src/modules/data/reducers.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/* harmony default export */ var data_reducers = (Object(external_tribe_modules_redux_["combineReducers"])({
  blocks: blocks,
  move: reducers
}));
// EXTERNAL MODULE: external "tribe.common.data.plugins"
var external_tribe_common_data_plugins_ = __webpack_require__("9lL/");

// EXTERNAL MODULE: external "tribe.common.store"
var external_tribe_common_store_ = __webpack_require__("g8L8");

// EXTERNAL MODULE: external "tribe.modules.reduxSaga"
var external_tribe_modules_reduxSaga_ = __webpack_require__("1fKG");

// EXTERNAL MODULE: external "wp.data"
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external "tribe.common.utils"
var external_tribe_common_utils_ = __webpack_require__("B8vQ");

// EXTERNAL MODULE: ./src/modules/data/shared/move/selectors.js
var selectors = __webpack_require__("ihba");

// EXTERNAL MODULE: ./src/modules/data/shared/move/actions.js
var move_actions = __webpack_require__("U/eK");

// CONCATENATED MODULE: ./src/modules/data/shared/move/sagas.js

function sagas_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function sagas_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? sagas_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : sagas_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/* eslint-disable camelcase */
/**
 * External Dependencies
 */



/**
 * Wordpress dependencies
 */


/**
 * Internal dependencies
 */




function createBody(params) {
  return Object.entries(params).map(_ref => {
    let [key, value] = _ref;
    return `${key}=${encodeURIComponent(value)}`;
  }).join('&');
}
function* _fetch(params) {
  try {
    const body = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(createBody, sagas_objectSpread(sagas_objectSpread({}, params), {}, {
      check: external_tribe_common_utils_["globals"].restNonce().move_tickets
    }));
    const response = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(fetch, window.ajaxurl, {
      method: 'POST',
      body,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
      },
      credentials: 'include'
    });
    return yield Object(external_tribe_modules_reduxSaga_effects_["call"])([response, 'json']);
  } catch (error) {
    console.error(error);
  }
}

/**
 * Fetches usable oost types
 *
 * @yields
 */
function* fetchPostTypes() {
  try {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
      type: types["d" /* FETCH_POST_TYPES */]
    });
    const {
      data
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(_fetch, {
      action: 'move_tickets_get_post_types'
    });
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
      type: types["f" /* FETCH_POST_TYPES_SUCCESS */],
      data
    });
    return data;
  } catch (error) {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
      type: types["e" /* FETCH_POST_TYPES_ERROR */],
      error
    });
  }
}

/**
 * Fetches filtered posts based on criteria
 *
 * @export
 * @yields
 * @param {*} {
 * 	ignore,
 * 	post_type,
 * 	search_terms = '',
 * }
 */
function fetchPostChoices(_ref2) {
  let {
    ignore,
    post_type,
    search_terms = ''
  } = _ref2;
  return function* () {
    try {
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
        type: types["a" /* FETCH_POST_CHOICES */]
      });
      const {
        data
      } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(_fetch, {
        action: 'move_tickets_get_post_choices',
        ignore,
        post_type,
        search_terms
      });
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
        type: types["c" /* FETCH_POST_CHOICES_SUCCESS */],
        data
      });
      return data;
    } catch (error) {
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
        type: types["b" /* FETCH_POST_CHOICES_ERROR */],
        error
      });
    }
  }();
}

/**
 * Moves ticket/RSVP from one post to another
 *
 * @export
 * @yields
 * @param {*} {
 * 	src_post_id,
 * 	ticket_type_id,
 * 	target_post_id,
 * }
 */
function moveTicket(_ref3) {
  let {
    src_post_id,
    ticket_type_id,
    target_post_id
  } = _ref3;
  return function* () {
    try {
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
        type: types["i" /* MOVE_TICKET */]
      });
      const {
        data
      } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(_fetch, {
        action: 'move_ticket_type',
        src_post_id,
        ticket_type_id,
        target_post_id
      });
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
        type: types["k" /* MOVE_TICKET_SUCCESS */],
        data
      });
      return data;
    } catch (error) {
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
        type: types["j" /* MOVE_TICKET_ERROR */],
        error
      });
    }
  }();
}
function* getCurrentPostId() {
  return yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getCurrentPostId']);
}
function* getPostChoices() {
  const params = yield Object(external_tribe_modules_reduxSaga_effects_["all"])({
    post_type: Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors["b" /* getModalPostType */]),
    search_terms: Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors["c" /* getModalSearch */]),
    ignore: Object(external_tribe_modules_reduxSaga_effects_["call"])(getCurrentPostId)
  });
  yield Object(external_tribe_modules_reduxSaga_effects_["call"])(fetchPostChoices, params);
}
function* onModalChange(action) {
  if (!action.payload.hasOwnProperty('target_post_id') && !action.payload.hasOwnProperty('ticketId')) {
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_modules_reduxSaga_["delay"], 500);
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])(getPostChoices);
  }
}
function* onModalSubmit() {
  const params = yield Object(external_tribe_modules_reduxSaga_effects_["all"])({
    src_post_id: Object(external_tribe_modules_reduxSaga_effects_["call"])(getCurrentPostId),
    target_post_id: Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors["d" /* getModalTarget */]),
    ticket_type_id: Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors["e" /* getModalTicketId */])
  });
  yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(moveTicket, params);
  const action = yield Object(external_tribe_modules_reduxSaga_effects_["take"])([types["k" /* MOVE_TICKET_SUCCESS */], types["j" /* MOVE_TICKET_ERROR */]]);
  if (action.type === types["k" /* MOVE_TICKET_SUCCESS */]) {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(move_actions["a" /* hideModal */]());
  }
}
function* onModalShow(action) {
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
    type: types["m" /* SET_MODAL_DATA */],
    payload: action.payload
  });
}
function* onModalHide() {
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])({
    type: types["l" /* RESET_MODAL_DATA */]
  });
}
function* initialize() {
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["call"])(fetchPostTypes), Object(external_tribe_modules_reduxSaga_effects_["call"])(getPostChoices)]);
}
function* sagas_watchers() {
  yield Object(external_tribe_modules_reduxSaga_effects_["takeLatest"])([types["h" /* INITIALIZE_MODAL */]], initialize);
  yield Object(external_tribe_modules_reduxSaga_effects_["takeLatest"])([types["m" /* SET_MODAL_DATA */]], onModalChange);
  yield Object(external_tribe_modules_reduxSaga_effects_["takeLatest"])([types["o" /* SUBMIT_MODAL */]], onModalSubmit);
  yield Object(external_tribe_modules_reduxSaga_effects_["takeLatest"])([types["n" /* SHOW_MODAL */]], onModalShow);
  yield Object(external_tribe_modules_reduxSaga_effects_["takeLatest"])([types["g" /* HIDE_MODAL */]], onModalHide);
}
// CONCATENATED MODULE: ./src/modules/data/sagas.js
/**
 * Internal dependencies
 */





/* harmony default export */ var data_sagas = (() => {
  [rsvp["c" /* sagas */], ticket["e" /* sagas */], watchers, sagas_watchers].forEach(sagas => external_tribe_common_store_["store"].run(sagas));
});
// CONCATENATED MODULE: ./src/modules/data/index.js
/**
 * External dependencies
 */




const initStore = () => {
  data_sagas();
  const {
    dispatch,
    injectReducers
  } = external_tribe_common_store_["store"];
  const {
    TICKETS
  } = external_tribe_common_data_plugins_["constants"];
  dispatch(external_tribe_common_data_plugins_["actions"].addPlugin(TICKETS));
  injectReducers({
    tickets: data_reducers
  });
};
const getStore = () => external_tribe_common_store_["store"];
// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__("cDcd");
var external_React_default = /*#__PURE__*/__webpack_require__.n(external_React_);

// EXTERNAL MODULE: external "wp.i18n"
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: ./src/modules/icons/index.js + 18 modules
var icons = __webpack_require__("NxMS");

// EXTERNAL MODULE: external "tribe.modules.reactRedux"
var external_tribe_modules_reactRedux_ = __webpack_require__("h74D");

// EXTERNAL MODULE: external "moment"
var external_moment_ = __webpack_require__("wy2R");
var external_moment_default = /*#__PURE__*/__webpack_require__.n(external_moment_);

// EXTERNAL MODULE: external "tribe.modules.propTypes"
var external_tribe_modules_propTypes_ = __webpack_require__("rf6O");
var external_tribe_modules_propTypes_default = /*#__PURE__*/__webpack_require__.n(external_tribe_modules_propTypes_);

// EXTERNAL MODULE: external "tribe.modules.classnames"
var external_tribe_modules_classnames_ = __webpack_require__("K2gz");
var external_tribe_modules_classnames_default = /*#__PURE__*/__webpack_require__.n(external_tribe_modules_classnames_);

// EXTERNAL MODULE: external "wp.components"
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: ./src/modules/elements/index.js + 26 modules
var modules_elements = __webpack_require__("jHzm");

// EXTERNAL MODULE: ./src/modules/elements/container-panel/index.js + 1 modules
var container_panel = __webpack_require__("2LK8");

// EXTERNAL MODULE: external "tribe.common.elements"
var external_tribe_common_elements_ = __webpack_require__("6Ugf");

// EXTERNAL MODULE: ./src/modules/blocks/rsvp/counters/style.pcss
var style = __webpack_require__("apLV");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/counters/template.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const RSVPCounters = _ref => {
  let {
    goingCount,
    notGoingCount,
    showNotGoing
  } = _ref;
  return wp.element.createElement("div", {
    className: "tribe-editor__rsvp-container-header__counters"
  }, wp.element.createElement(external_tribe_common_elements_["Counter"], {
    className: "tribe-editor__rsvp-container-header__going-counter",
    count: goingCount,
    label: Object(external_wp_i18n_["__"])('Going', 'event-tickets')
  }), showNotGoing && wp.element.createElement(external_tribe_common_elements_["Counter"], {
    className: "tribe-editor__rsvp-container-header__not-going-counter",
    count: notGoingCount,
    label: Object(external_wp_i18n_["__"])('Not going', 'event-tickets')
  }));
};
RSVPCounters.propTypes = {
  goingCount: external_tribe_modules_propTypes_default.a.number,
  notGoingCount: external_tribe_modules_propTypes_default.a.number,
  showNotGoing: external_tribe_modules_propTypes_default.a.bool
};
/* harmony default export */ var template = (RSVPCounters);
// EXTERNAL MODULE: external "tribe.common.hoc"
var external_tribe_common_hoc_ = __webpack_require__("Q9xL");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/counters/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const mapStateToProps = state => ({
  goingCount: rsvp["d" /* selectors */].getRSVPGoingCount(state),
  notGoingCount: rsvp["d" /* selectors */].getRSVPNotGoingCount(state),
  showNotGoing: rsvp["d" /* selectors */].getRSVPNotGoingResponses(state)
});
/* harmony default export */ var container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(mapStateToProps))(template));
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/action-buttons/attendees-action-button/template.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const AttendeesActionButton = _ref => {
  let {
    href,
    isDisabled
  } = _ref;
  return wp.element.createElement(modules_elements["ActionButton"], {
    asLink: true,
    className: "tribe-editor__rsvp__action-button tribe-editor__rsvp__action-button--attendees",
    disabled: isDisabled,
    id: "attendees-rsvp",
    href: href,
    icon: wp.element.createElement(icons["Attendees"], null),
    target: "_blank"
  }, Object(external_wp_i18n_["__"])('View Attendees', 'event-tickets'));
};
AttendeesActionButton.propTypes = {
  href: external_tribe_modules_propTypes_default.a.string,
  isDisabled: external_tribe_modules_propTypes_default.a.bool
};
/* harmony default export */ var attendees_action_button_template = (AttendeesActionButton);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/action-buttons/attendees-action-button/container.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const container_mapStateToProps = state => {
  const adminURL = external_tribe_common_utils_["globals"].adminUrl();
  const postType = Object(external_wp_data_["select"])('core/editor').getCurrentPostType();
  const postId = Object(external_wp_data_["select"])('core/editor').getCurrentPostId();
  return {
    href: `${adminURL}edit.php?post_type=${postType}&page=tickets-attendees&event_id=${postId}`,
    // eslint-disable-line max-len
    isDisabled: rsvp["d" /* selectors */].getRSVPIsLoading(state)
  };
};
/* harmony default export */ var attendees_action_button_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(container_mapStateToProps))(attendees_action_button_template));
// EXTERNAL MODULE: external "lodash.noop"
var external_lodash_noop_ = __webpack_require__("In0u");
var external_lodash_noop_default = /*#__PURE__*/__webpack_require__.n(external_lodash_noop_);

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/action-buttons/settings-action-button/template.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const SettingsActionButton = _ref => {
  let {
    isDisabled,
    onClick
  } = _ref;
  return wp.element.createElement(modules_elements["ActionButton"], {
    className: "tribe-editor__rsvp__action-button tribe-editor__rsvp__action-button--settings",
    disabled: isDisabled,
    id: "settings-rsvp",
    icon: wp.element.createElement(icons["Settings"], null),
    onClick: onClick
  }, Object(external_wp_i18n_["__"])('Settings', 'event-tickets'));
};
SettingsActionButton.defaultProps = {
  onClick: external_lodash_noop_default.a
};
SettingsActionButton.propTypes = {
  isDisabled: external_tribe_modules_propTypes_default.a.bool,
  onClick: external_tribe_modules_propTypes_default.a.func
};
/* harmony default export */ var settings_action_button_template = (SettingsActionButton);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/action-buttons/settings-action-button/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const settings_action_button_container_mapStateToProps = state => ({
  isDisabled: rsvp["d" /* selectors */].getRSVPIsLoading(state)
});
const mapDispatchToProps = dispatch => ({
  onClick: () => dispatch(rsvp["a" /* actions */].setRSVPSettingsOpen(true))
});
/* harmony default export */ var settings_action_button_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(settings_action_button_container_mapStateToProps, mapDispatchToProps))(settings_action_button_template));
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/action-buttons/index.js


// EXTERNAL MODULE: ./src/modules/blocks/rsvp/container-header/style.pcss
var container_header_style = __webpack_require__("b+3r");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/container-header/template.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const getCapacityLabel = capacity => {
  // todo: should use _n to be translator friendly
  const singular = Object(external_wp_i18n_["__"])('%d Remaining', 'event-tickets');
  const plural = singular;
  const fallback = wp.element.createElement("span", {
    className: "tribe-editor__rsvp-container-header__capacity-label-fallback"
  }, Object(external_wp_i18n_["__"])('Unlimited', 'event-tickets'));
  return wp.element.createElement(modules_elements["NumericLabel"], {
    className: "tribe-editor__rsvp-container-header__capacity-label",
    count: capacity,
    includeZero: true,
    singular: singular,
    plural: plural,
    fallback: fallback
  });
};
const RSVPContainerHeader = _ref => {
  let {
    description,
    isAddEditOpen,
    isCreated,
    title,
    available,
    setAddEditOpen
  } = _ref;
  if (isAddEditOpen) {
    return null;
  }

  /* eslint-disable max-len */
  const leftColumn = wp.element.createElement(external_React_default.a.Fragment, null, wp.element.createElement("h3", {
    className: "tribe-editor__rsvp-title tribe-common-h2 tribe-common-h4--min-medium"
  }, title), wp.element.createElement("div", {
    className: "tribe-editor__rsvp-description tribe-common-h6 tribe-common-h--alt tribe-common-b3--min-medium"
  }, description, wp.element.createElement(container, null)), isCreated && getCapacityLabel(available));
  const rightColumn = wp.element.createElement(external_React_default.a.Fragment, null, wp.element.createElement("button", {
    id: "edit-rsvp",
    className: "tribe-common-c-btn tribe-common-b1 tribe-common-b2--min-medium",
    onClick: setAddEditOpen
  }, Object(external_wp_i18n_["__"])('Edit RSVP', 'event-tickets')), wp.element.createElement(settings_action_button_container, null), wp.element.createElement(attendees_action_button_container, null));
  return wp.element.createElement(external_React_default.a.Fragment, null, wp.element.createElement("div", {
    className: "tribe-common tribe-editor__inactive-block--rsvp tribe-editor__rsvp-container-header"
  }, wp.element.createElement(modules_elements["SplitContainer"], {
    leftColumn: leftColumn,
    rightColumn: rightColumn
  })));
  /* eslint-enable max-len */
};

RSVPContainerHeader.propTypes = {
  available: external_tribe_modules_propTypes_default.a.number,
  description: external_tribe_modules_propTypes_default.a.string,
  isAddEditOpen: external_tribe_modules_propTypes_default.a.bool,
  isCreated: external_tribe_modules_propTypes_default.a.bool,
  setAddEditOpen: external_tribe_modules_propTypes_default.a.func,
  title: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var container_header_template = (RSVPContainerHeader);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/container-header/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const container_header_container_mapStateToProps = state => ({
  available: rsvp["d" /* selectors */].getRSVPAvailable(state),
  description: rsvp["d" /* selectors */].getRSVPDescription(state),
  isAddEditOpen: rsvp["d" /* selectors */].getRSVPIsAddEditOpen(state),
  isCreated: rsvp["d" /* selectors */].getRSVPCreated(state),
  title: rsvp["d" /* selectors */].getRSVPTitle(state)
});
const container_mapDispatchToProps = dispatch => ({
  setAddEditOpen: () => dispatch(rsvp["a" /* actions */].setRSVPIsAddEditOpen(true))
});
/* harmony default export */ var container_header_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(container_header_container_mapStateToProps, container_mapDispatchToProps))(container_header_template));
// EXTERNAL MODULE: ./node_modules/uniqid/index.js
var uniqid = __webpack_require__("zJgK");
var uniqid_default = /*#__PURE__*/__webpack_require__.n(uniqid);

// EXTERNAL MODULE: ./src/modules/blocks/rsvp/capacity/styles.pcss
var styles = __webpack_require__("LHhe");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/capacity/template.js
/**
 * External dependencies
 */





/**
 * Wordpress dependencies
 */


/**
 * Internal dependencies
 */


const RSVPCapacity = _ref => {
  let {
    isDisabled,
    onTempCapacityChange,
    tempCapacity
  } = _ref;
  const capacityId = uniqid_default()();
  return wp.element.createElement("div", {
    className: external_tribe_modules_classnames_default()('tribe-editor__ticket__capacity', 'tribe-editor__ticket__content-row', 'tribe-editor__ticket__content-row--capacity')
  }, wp.element.createElement(external_tribe_common_elements_["LabeledItem"], {
    className: "tribe-editor__ticket__capacity-label",
    forId: capacityId,
    isLabel: true,
    label: Object(external_wp_i18n_["__"])('RSVP Capacity', 'event-tickets')
  }), wp.element.createElement("div", {
    className: "tribe-editor__rsvp-container-content__capacity"
  }, wp.element.createElement(external_tribe_common_elements_["NumberInput"], {
    className: "tribe-editor__rsvp-container-content__capacity-input",
    disabled: isDisabled,
    id: capacityId,
    min: 0,
    onChange: onTempCapacityChange,
    value: tempCapacity
  }), wp.element.createElement("span", {
    className: "tribe-editor__rsvp-container-content__capacity-label-help"
  }, Object(external_wp_i18n_["__"])('Leave blank if unlimited', 'event-tickets'))));
};
RSVPCapacity.propTypes = {
  isDisabled: external_tribe_modules_propTypes_default.a.bool,
  onTempCapacityChange: external_tribe_modules_propTypes_default.a.func.isRequired,
  tempCapacity: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var capacity_template = (RSVPCapacity);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/capacity/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const capacity_container_mapStateToProps = state => ({
  isDisabled: rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPSettingsOpen(state),
  tempCapacity: rsvp["d" /* selectors */].getRSVPTempCapacity(state)
});
const capacity_container_mapDispatchToProps = dispatch => ({
  onTempCapacityChange: e => {
    dispatch(rsvp["a" /* actions */].setRSVPTempCapacity(e.target.value));
    dispatch(rsvp["a" /* actions */].setRSVPHasChanges(true));
  }
});
/* harmony default export */ var capacity_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(capacity_container_mapStateToProps, capacity_container_mapDispatchToProps))(capacity_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/description/styles.pcss
var description_styles = __webpack_require__("J/pT");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/description/template.js
/**
 * External dependencies
 */





/**
 * Wordpress dependencies
 */


/**
 * Internal dependencies
 */


const RSVPDescription = _ref => {
  let {
    isDisabled,
    onTempDescriptionChange,
    tempDescription
  } = _ref;
  const descriptionId = uniqid_default()();
  return wp.element.createElement("div", {
    className: external_tribe_modules_classnames_default()('tribe-editor__ticket__description', 'tribe-editor__ticket__content-row', 'tribe-editor__ticket__content-row--description')
  }, wp.element.createElement(external_tribe_common_elements_["LabeledItem"], {
    className: "tribe-editor__ticket__description-label",
    forId: descriptionId,
    isLabel: true,
    label: Object(external_wp_i18n_["__"])('Description', 'event-tickets')
  }), wp.element.createElement(external_tribe_common_elements_["Input"], {
    className: "tribe-editor__ticket__description-input",
    id: descriptionId,
    disabled: isDisabled,
    type: "text",
    value: tempDescription,
    onChange: onTempDescriptionChange
  }));
};
RSVPDescription.propTypes = {
  isDisabled: external_tribe_modules_propTypes_default.a.bool,
  onTempDescriptionChange: external_tribe_modules_propTypes_default.a.func.isRequired,
  tempDescription: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var description_template = (RSVPDescription);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/description/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const description_container_mapStateToProps = state => ({
  isDisabled: rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPSettingsOpen(state),
  tempDescription: rsvp["d" /* selectors */].getRSVPTempDescription(state)
});
const description_container_mapDispatchToProps = dispatch => ({
  onTempDescriptionChange: e => {
    dispatch(rsvp["a" /* actions */].setRSVPTempDescription(e.target.value));
    dispatch(rsvp["a" /* actions */].setRSVPHasChanges(true));
  }
});
/* harmony default export */ var description_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(description_container_mapStateToProps, description_container_mapDispatchToProps))(description_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/not-going/styles.pcss
var not_going_styles = __webpack_require__("45Xz");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/not-going/template.js
/**
 * External dependencies
 */





/**
 * Wordpress dependencies
 */


/**
 * Internal dependencies
 */


const RSVPNotGoingResponses = _ref => {
  let {
    isDisabled,
    onTempNotGoingResponsesChange,
    tempNotGoingResponses
  } = _ref;
  const notGoingId = uniqid_default()();
  return wp.element.createElement("div", {
    className: external_tribe_modules_classnames_default()('tribe-editor__ticket__not-going-responses', 'tribe-editor__ticket__content-row', 'tribe-editor__ticket__content-row--not-going-responses')
  }, wp.element.createElement(external_tribe_common_elements_["LabeledItem"], {
    className: "tribe-editor__ticket__not-going-responses-label",
    forId: notGoingId,
    isLabel: true,
    label: Object(external_wp_i18n_["__"])('Not going', 'event-tickets')
  }), wp.element.createElement("div", {
    className: "tribe-editor__rsvp-container-content__not-going-responses"
  }, wp.element.createElement(external_tribe_common_elements_["Checkbox"], {
    checked: tempNotGoingResponses,
    className: "tribe-editor__rsvp-container-content__not-going-responses",
    disabled: isDisabled,
    id: notGoingId,
    label: Object(external_wp_i18n_["__"])('Enable "Not Going" responses', 'event-tickets'),
    onChange: onTempNotGoingResponsesChange
  }), wp.element.createElement("span", {
    className: "tribe-editor__rsvp-container-content__not-going-responses-label-help"
  }, Object(external_wp_i18n_["__"])('Receive notification of people who will not attend.', 'event-tickets'))));
};
RSVPNotGoingResponses.propTypes = {
  isDisabled: external_tribe_modules_propTypes_default.a.bool,
  onTempNotGoingResponsesChange: external_tribe_modules_propTypes_default.a.func.isRequired,
  tempNotGoingResponses: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var not_going_template = (RSVPNotGoingResponses);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/not-going/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const not_going_container_mapStateToProps = state => ({
  tempNotGoingResponses: rsvp["d" /* selectors */].getRSVPTempNotGoingResponses(state)
});
const not_going_container_mapDispatchToProps = dispatch => ({
  onTempNotGoingResponsesChange: e => {
    dispatch(rsvp["a" /* actions */].setRSVPTempNotGoingResponses(e.target.checked));
    dispatch(rsvp["a" /* actions */].setRSVPHasChanges(true));
  }
});
/* harmony default export */ var not_going_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(not_going_container_mapStateToProps, not_going_container_mapDispatchToProps))(not_going_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/title/styles.pcss
var title_styles = __webpack_require__("OKIc");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/title/template.js
/**
 * External dependencies
 */





/**
 * Wordpress dependencies
 */


/**
 * Internal dependencies
 */


const RSVPTitle = _ref => {
  let {
    isDisabled,
    onTempTitleChange,
    tempTitle
  } = _ref;
  const titleId = uniqid_default()();
  const inputRef = Object(external_React_["useRef"])(null);
  Object(external_React_["useEffect"])(() => {
    var _inputRef$current;
    inputRef === null || inputRef === void 0 ? void 0 : (_inputRef$current = inputRef.current) === null || _inputRef$current === void 0 ? void 0 : _inputRef$current.focus();
  }, [inputRef]);
  return wp.element.createElement("div", {
    className: external_tribe_modules_classnames_default()('tribe-editor__ticket__title', 'tribe-editor__ticket__content-row', 'tribe-editor__ticket__content-row--title')
  }, wp.element.createElement(external_tribe_common_elements_["LabeledItem"], {
    className: "tribe-editor__ticket__title-label",
    forId: titleId,
    isLabel: true,
    label: Object(external_wp_i18n_["__"])('RSVP name', 'event-tickets')
  }), wp.element.createElement("input", {
    className: "tribe-editor__input tribe-editor__ticket__title-input",
    id: titleId,
    disabled: isDisabled,
    type: "text",
    value: tempTitle,
    onChange: onTempTitleChange,
    ref: inputRef
  }));
};
RSVPTitle.propTypes = {
  isDisabled: external_tribe_modules_propTypes_default.a.bool,
  onTempTitleChange: external_tribe_modules_propTypes_default.a.func.isRequired,
  tempTitle: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var title_template = (RSVPTitle);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/title/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const title_container_mapStateToProps = state => ({
  isDisabled: rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPSettingsOpen(state),
  tempTitle: rsvp["d" /* selectors */].getRSVPTempTitle(state),
  title: rsvp["d" /* selectors */].getRSVPTitle(state)
});
const title_container_mapDispatchToProps = dispatch => ({
  onTempTitleChange: e => {
    dispatch(rsvp["a" /* actions */].setRSVPTempTitle(e.target.value));
    dispatch(rsvp["a" /* actions */].setRSVPHasChanges(true));
  }
});
/* harmony default export */ var title_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(title_container_mapStateToProps, title_container_mapDispatchToProps))(title_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/duration-label/style.pcss
var duration_label_style = __webpack_require__("vLzK");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/duration-label/template.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


const tooltipLabel = wp.element.createElement(external_wp_components_["Dashicon"], {
  className: "tribe-editor__rsvp-duration__duration-tooltip-label",
  icon: "info-outline"
});
const RSVPDurationLabel = _ref => {
  let {
    tooltipDisabled
  } = _ref;
  return wp.element.createElement(modules_elements["LabelWithTooltip"], {
    className: "tribe-editor__rsvp-duration__duration-label",
    label: Object(external_wp_i18n_["__"])('RSVP Duration', 'event-tickets'),
    tooltipDisabled: tooltipDisabled,
    tooltipLabel: tooltipLabel
    // @TODO: get tooltip text based on post type
    ,
    tooltipText: Object(external_wp_i18n_["__"])('By default, sales will begin as soon as you save the ticket and end when the event begins', 'event-tickets')
  });
};
RSVPDurationLabel.propTypes = {
  tooltipDisabled: external_tribe_modules_propTypes_default.a.bool
};
/* harmony default export */ var duration_label_template = (RSVPDurationLabel);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/duration-label/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const getIsDisabled = state => rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPSettingsOpen(state);
const duration_label_container_mapStateToProps = state => ({
  isDisabled: getIsDisabled(state)
});
/* harmony default export */ var duration_label_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(duration_label_container_mapStateToProps))(duration_label_template));
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/objectWithoutProperties.js
var objectWithoutProperties = __webpack_require__("QILm");
var objectWithoutProperties_default = /*#__PURE__*/__webpack_require__.n(objectWithoutProperties);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/extends.js
var helpers_extends = __webpack_require__("pVnL");
var extends_default = /*#__PURE__*/__webpack_require__.n(helpers_extends);

// EXTERNAL MODULE: ./src/modules/blocks/rsvp/duration-picker/style.pcss
var duration_picker_style = __webpack_require__("IwAG");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/duration-picker/template.js

/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


const RSVPDurationPicker = props => wp.element.createElement(modules_elements["DateTimeRangePicker"], extends_default()({
  className: "tribe-editor__rsvp-duration__duration-picker"
}, props));
RSVPDurationPicker.propTypes = {
  fromDate: external_tribe_modules_propTypes_default.a.instanceOf(Date),
  fromDateInput: external_tribe_modules_propTypes_default.a.string,
  fromDateDisabled: external_tribe_modules_propTypes_default.a.bool,
  fromTime: external_tribe_modules_propTypes_default.a.string,
  fromTimeDisabled: external_tribe_modules_propTypes_default.a.bool,
  onFromDateChange: external_tribe_modules_propTypes_default.a.func,
  onFromTimePickerBlur: external_tribe_modules_propTypes_default.a.func,
  onFromTimePickerChange: external_tribe_modules_propTypes_default.a.func,
  onFromTimePickerClick: external_tribe_modules_propTypes_default.a.func,
  onToDateChange: external_tribe_modules_propTypes_default.a.func,
  onToTimePickerBlur: external_tribe_modules_propTypes_default.a.func,
  onToTimePickerChange: external_tribe_modules_propTypes_default.a.func,
  onToTimePickerClick: external_tribe_modules_propTypes_default.a.func,
  toDate: external_tribe_modules_propTypes_default.a.instanceOf(Date),
  toDateInput: external_tribe_modules_propTypes_default.a.string,
  toDateDisabled: external_tribe_modules_propTypes_default.a.bool,
  toTime: external_tribe_modules_propTypes_default.a.string,
  toTimeDisabled: external_tribe_modules_propTypes_default.a.bool
};
/* harmony default export */ var duration_picker_template = (RSVPDurationPicker);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/duration-picker/container.js


const _excluded = ["state"],
  _excluded2 = ["dispatch"];
function container_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function container_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? container_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : container_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */




const onFromDateChange = dispatch => (date, modifiers, dayPickerInput) => {
  const payload = {
    date,
    dayPickerInput
  };
  dispatch(rsvp["a" /* actions */].handleRSVPStartDate(payload));
};
const onFromTimePickerChange = dispatch => e => dispatch(rsvp["a" /* actions */].setRSVPTempStartTimeInput(e.target.value));
const onFromTimePickerClick = dispatch => (value, onClose) => {
  dispatch(rsvp["a" /* actions */].handleRSVPStartTime(value));
  onClose();
};
const onToDateChange = dispatch => (date, modifiers, dayPickerInput) => {
  const payload = {
    date,
    dayPickerInput
  };
  dispatch(rsvp["a" /* actions */].handleRSVPEndDate(payload));
};
const onToTimePickerChange = dispatch => e => dispatch(rsvp["a" /* actions */].setRSVPTempEndTimeInput(e.target.value));
const onToTimePickerClick = dispatch => (value, onClose) => {
  dispatch(rsvp["a" /* actions */].handleRSVPEndTime(value));
  onClose();
};
const onFromTimePickerBlur = (state, dispatch) => e => {
  let startTimeMoment = external_tribe_common_utils_["moment"].toMoment(e.target.value, external_tribe_common_utils_["moment"].TIME_FORMAT, false);
  if (!startTimeMoment.isValid()) {
    const startTimeInput = rsvp["d" /* selectors */].getRSVPStartTimeInput(state);
    startTimeMoment = external_tribe_common_utils_["moment"].toMoment(startTimeInput, external_tribe_common_utils_["moment"].TIME_FORMAT, false);
  }
  const seconds = external_tribe_common_utils_["moment"].totalSeconds(startTimeMoment);
  dispatch(rsvp["a" /* actions */].handleRSVPStartTime(seconds));
};
const onToTimePickerBlur = (state, dispatch) => e => {
  let endTimeMoment = external_tribe_common_utils_["moment"].toMoment(e.target.value, external_tribe_common_utils_["moment"].TIME_FORMAT, false);
  if (!endTimeMoment.isValid()) {
    const endTimeInput = rsvp["d" /* selectors */].getRSVPEndTimeInput(state);
    endTimeMoment = external_tribe_common_utils_["moment"].toMoment(endTimeInput, external_tribe_common_utils_["moment"].TIME_FORMAT, false);
  }
  const seconds = external_tribe_common_utils_["moment"].totalSeconds(endTimeMoment);
  dispatch(rsvp["a" /* actions */].handleRSVPEndTime(seconds));
};
const duration_picker_container_mapStateToProps = state => {
  const datePickerFormat = external_tribe_common_utils_["globals"].tecDateSettings().datepickerFormat ? external_tribe_common_utils_["moment"].toFormat(external_tribe_common_utils_["globals"].tecDateSettings().datepickerFormat) : 'LL';
  const isDisabled = rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPSettingsOpen(state);
  const startDateMoment = rsvp["d" /* selectors */].getRSVPTempStartDateMoment(state);
  const endDateMoment = rsvp["d" /* selectors */].getRSVPTempEndDateMoment(state);
  const fromDate = startDateMoment && startDateMoment.toDate();
  const toDate = endDateMoment && endDateMoment.toDate();
  return {
    fromDate,
    fromDateInput: rsvp["d" /* selectors */].getRSVPTempStartDateInput(state),
    fromDateDisabled: isDisabled,
    fromDateFormat: datePickerFormat,
    fromTime: rsvp["d" /* selectors */].getRSVPTempStartTimeInput(state),
    fromTimeDisabled: isDisabled,
    toDate,
    toDateInput: rsvp["d" /* selectors */].getRSVPTempEndDateInput(state),
    toDateDisabled: isDisabled,
    toDateFormat: datePickerFormat,
    toTime: rsvp["d" /* selectors */].getRSVPTempEndTimeInput(state),
    toTimeDisabled: isDisabled,
    state
  };
};
const duration_picker_container_mapDispatchToProps = dispatch => ({
  onFromDateChange: onFromDateChange(dispatch),
  onFromTimePickerChange: onFromTimePickerChange(dispatch),
  onFromTimePickerClick: onFromTimePickerClick(dispatch),
  onToDateChange: onToDateChange(dispatch),
  onToTimePickerChange: onToTimePickerChange(dispatch),
  onToTimePickerClick: onToTimePickerClick(dispatch),
  dispatch
});
const mergeProps = (stateProps, dispatchProps, ownProps) => {
  const {
      state
    } = stateProps,
    restStateProps = objectWithoutProperties_default()(stateProps, _excluded);
  const {
      dispatch
    } = dispatchProps,
    restDispatchProps = objectWithoutProperties_default()(dispatchProps, _excluded2);
  return container_objectSpread(container_objectSpread(container_objectSpread(container_objectSpread({}, ownProps), restStateProps), restDispatchProps), {}, {
    onFromTimePickerBlur: onFromTimePickerBlur(state, dispatch),
    onToTimePickerBlur: onToTimePickerBlur(state, dispatch)
  });
};
/* harmony default export */ var duration_picker_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(duration_picker_container_mapStateToProps, duration_picker_container_mapDispatchToProps, mergeProps))(duration_picker_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/duration/style.pcss
var duration_style = __webpack_require__("6lOv");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/duration/template.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const RSVPDuration = _ref => {
  let {
    hasDurationError
  } = _ref;
  return wp.element.createElement("div", {
    className: "tribe-editor__rsvp-duration"
  }, wp.element.createElement(duration_label_container, null), wp.element.createElement(duration_picker_container, null), hasDurationError && wp.element.createElement("span", {
    className: "tribe-editor__rsvp-duration__error"
  }, Object(external_wp_i18n_["__"])('There is an error with the selected sales duration. Please fix the issue before saving.',
  // eslint-disable-line max-len
  'event-tickets')));
};
RSVPDuration.propTypes = {
  hasDurationError: external_tribe_modules_propTypes_default.a.bool
};
/* harmony default export */ var duration_template = (RSVPDuration);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/duration/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const duration_container_mapStateToProps = state => ({
  hasDurationError: rsvp["d" /* selectors */].getRSVPHasDurationError(state)
});
/* harmony default export */ var duration_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(duration_container_mapStateToProps))(duration_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/attendee-registration/style.pcss
var attendee_registration_style = __webpack_require__("Zz++");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/attendee-registration/template.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const linkTextAdd = Object(external_wp_i18n_["__"])('+ Add', 'event-tickets');
const linkTextEdit = Object(external_wp_i18n_["__"])('Edit', 'event-tickets');
const RSVPAttendeeRegistration = _ref => {
  let {
    attendeeRegistrationURL,
    hasAttendeeInfoFields,
    isCreated,
    isDisabled,
    isModalOpen,
    onClick,
    onClose,
    onIframeLoad
  } = _ref;
  const linkText = hasAttendeeInfoFields ? linkTextEdit : linkTextAdd;
  return wp.element.createElement(modules_elements["AttendeesRegistration"], {
    helperText: Object(external_wp_i18n_["__"])('Save your RSVP to enable attendee information fields', 'event-tickets'),
    iframeURL: attendeeRegistrationURL,
    isDisabled: isDisabled,
    isModalOpen: isModalOpen,
    label: Object(external_wp_i18n_["__"])('Attendee Information', 'event-tickets'),
    linkText: linkText,
    modalTitle: Object(external_wp_i18n_["__"])('Attendee Information', 'event-tickets'),
    onClick: onClick,
    onClose: onClose,
    onIframeLoad: onIframeLoad,
    showHelperText: !isCreated
    // @todo: @paulmskim shouldCloseOnClickOutside is a fix until we can figure out modal closing issue in WP 5.5.
    ,
    shouldCloseOnClickOutside: false
  });
};
RSVPAttendeeRegistration.propTypes = {
  attendeeRegistrationURL: external_tribe_modules_propTypes_default.a.string.isRequired,
  hasAttendeeInfoFields: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isCreated: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isDisabled: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isModalOpen: external_tribe_modules_propTypes_default.a.bool.isRequired,
  onClick: external_tribe_modules_propTypes_default.a.func.isRequired,
  onClose: external_tribe_modules_propTypes_default.a.func.isRequired,
  onIframeLoad: external_tribe_modules_propTypes_default.a.func.isRequired
};
/* harmony default export */ var attendee_registration_template = (RSVPAttendeeRegistration);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/attendee-registration/container.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const getAttendeeRegistrationUrl = state => {
  const adminURL = external_tribe_common_utils_["globals"].adminUrl();
  const postType = Object(external_wp_data_["select"])('core/editor').getCurrentPostType();
  const rsvpId = rsvp["d" /* selectors */].getRSVPId(state);
  return `${adminURL}edit.php?post_type=${postType}&page=attendee-registration&ticket_id=${rsvpId}&tribe_events_modal=1`; // eslint-disable-line max-len
};

const container_getIsDisabled = state => rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPSettingsOpen(state) || !rsvp["d" /* selectors */].getRSVPCreated(state);
const attendee_registration_container_mapStateToProps = state => ({
  attendeeRegistrationURL: getAttendeeRegistrationUrl(state),
  hasAttendeeInfoFields: rsvp["d" /* selectors */].getRSVPHasAttendeeInfoFields(state),
  isCreated: rsvp["d" /* selectors */].getRSVPCreated(state),
  isDisabled: container_getIsDisabled(state),
  isModalOpen: rsvp["d" /* selectors */].getRSVPIsModalOpen(state)
});
const attendee_registration_container_mapDispatchToProps = (dispatch, ownProps) => {
  return {
    onClick: () => {
      dispatch(rsvp["a" /* actions */].setRSVPIsModalOpen(true));
    },
    onClose: e => {
      if (!e.target.classList.contains('components-modal__content')) {
        dispatch(rsvp["a" /* actions */].setRSVPIsModalOpen(ownProps.clientId, false));
      }
    },
    onIframeLoad: iframe => {
      const iframeWindow = iframe.contentWindow;

      // show overlay
      const showOverlay = () => {
        iframe.nextSibling.classList.add('tribe-editor__attendee-registration__modal-overlay--show');
      };

      // add event listener for form submit
      const form = iframeWindow.document.querySelector('#event-tickets-attendee-information');
      form.addEventListener('submit', showOverlay);

      // remove listeners
      const removeListeners = () => {
        iframeWindow.removeEventListener('unload', handleUnload); // eslint-disable-line no-use-before-define,max-len
        form.removeEventListener('submit', showOverlay);
      };

      // handle unload on iframe unload
      const handleUnload = () => {
        // remove listeners
        removeListeners(iframeWindow);

        // check if there are meta fields
        const metaFields = iframeWindow.document.querySelector('#tribe-tickets-attendee-sortables');
        const hasFields = Boolean(metaFields.firstElementChild);

        // dispatch actions
        dispatch(rsvp["a" /* actions */].setRSVPHasAttendeeInfoFields(hasFields));
        dispatch(rsvp["a" /* actions */].setRSVPIsModalOpen(false));
      };

      // add handler to iframe window unload
      iframeWindow.addEventListener('unload', handleUnload);

      // add target blank to "Learn more" link
      const introLink = iframeWindow.document.querySelector('.tribe-intro > a');
      if (introLink) {
        introLink.setAttribute('target', '_blank');
      }
    }
  };
};
/* harmony default export */ var attendee_registration_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(attendee_registration_container_mapStateToProps, attendee_registration_container_mapDispatchToProps))(attendee_registration_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/container-content/style.pcss
var container_content_style = __webpack_require__("/vq8");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/container-content/template.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */







const RSVPContainerContent = _ref => {
  let {
    isAddEditOpen,
    hasTicketsPlus
  } = _ref;
  if (!isAddEditOpen) {
    return null;
  }
  return wp.element.createElement(external_React_default.a.Fragment, null, wp.element.createElement(title_container, null), wp.element.createElement(description_container, null), wp.element.createElement(capacity_container, null), wp.element.createElement(duration_container, null), wp.element.createElement(not_going_container, null), hasTicketsPlus && wp.element.createElement(attendee_registration_container, null));
};
RSVPContainerContent.propTypes = {
  isAddEditOpen: external_tribe_modules_propTypes_default.a.bool,
  hasTicketsPlus: external_tribe_modules_propTypes_default.a.bool
};
/* harmony default export */ var container_content_template = (RSVPContainerContent);
// EXTERNAL MODULE: external "tribe.common.data"
var external_tribe_common_data_ = __webpack_require__("ZNLL");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/container-content/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */




const container_content_container_mapStateToProps = state => ({
  hasTicketsPlus: external_tribe_common_data_["plugins"].selectors.hasPlugin(state)(external_tribe_common_data_["plugins"].constants.TICKETS_PLUS),
  isAddEditOpen: rsvp["d" /* selectors */].getRSVPIsAddEditOpen(state)
});
/* harmony default export */ var container_content_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(container_content_container_mapStateToProps))(container_content_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/container/style.pcss
var container_style = __webpack_require__("p/3v");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/container/template.js
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */





const RSVPContainer = _ref => {
  let {
    isDisabled,
    isSelected,
    clientId
  } = _ref;
  return wp.element.createElement(modules_elements["ContainerPanel"], {
    className: external_tribe_modules_classnames_default()('tribe-editor__rsvp-container', {
      'tribe-editor__rsvp-container--disabled': isDisabled
    }),
    layout: container_panel["a" /* LAYOUT */].rsvp,
    header: wp.element.createElement(container_header_container, {
      isSelected: isSelected
    }),
    content: wp.element.createElement(container_content_container, {
      clientId: clientId
    })
  });
};
RSVPContainer.propTypes = {
  isDisabled: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isSelected: external_tribe_modules_propTypes_default.a.bool.isRequired,
  clientId: external_tribe_modules_propTypes_default.a.string.isRequired
};
/* harmony default export */ var container_template = (RSVPContainer);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/container/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const container_container_getIsDisabled = state => rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPSettingsOpen(state);
const container_container_mapStateToProps = state => ({
  isAddEditOpen: rsvp["d" /* selectors */].getRSVPIsAddEditOpen(state),
  isDisabled: container_container_getIsDisabled(state)
});
/* harmony default export */ var container_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(container_container_mapStateToProps))(container_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/move-delete/style.pcss
var move_delete_style = __webpack_require__("EiNN");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/move-delete/template.js
/**
 * External Dependencies
 */





const MoveDelete = _ref => {
  let {
    moveRSVP,
    removeRSVP,
    isDisabled
  } = _ref;
  return wp.element.createElement("div", {
    className: "tribe-editor__rsvp__content-row--move-delete"
  }, wp.element.createElement(external_tribe_common_elements_["Button"], {
    type: "button",
    onClick: removeRSVP,
    disabled: isDisabled
  }, Object(external_wp_i18n_["__"])('Remove RSVP', 'event-tickets')), wp.element.createElement(external_tribe_common_elements_["Button"], {
    type: "button",
    onClick: moveRSVP,
    disabled: isDisabled
  }, Object(external_wp_i18n_["__"])('Move RSVP', 'event-tickets')));
};
MoveDelete.propTypes = {
  moveRSVP: external_tribe_modules_propTypes_default.a.func.isRequired,
  removeRSVP: external_tribe_modules_propTypes_default.a.func.isRequired,
  isDisabled: external_tribe_modules_propTypes_default.a.bool.isRequired
};
/* harmony default export */ var move_delete_template = (MoveDelete);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/move-delete/container.js


const container_excluded = ["dispatch"];
function move_delete_container_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function move_delete_container_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? move_delete_container_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : move_delete_container_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */





/**
 * Internal dependencies
 */




const move_delete_container_mapStateToProps = state => ({
  created: rsvp["d" /* selectors */].getRSVPCreated(state),
  rsvpId: rsvp["d" /* selectors */].getRSVPId(state),
  isDisabled: rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPSettingsOpen(state)
});
const move_delete_container_mapDispatchToProps = (dispatch, ownProps) => ({
  moveRSVP: rsvpId => dispatch(Object(move_actions["c" /* showModal */])(rsvpId, ownProps.clientId)),
  dispatch
});
const container_mergeProps = (stateProps, dispatchProps, ownProps) => {
  const {
      dispatch
    } = dispatchProps,
    restDispatchProps = objectWithoutProperties_default()(dispatchProps, container_excluded);
  return move_delete_container_objectSpread(move_delete_container_objectSpread(move_delete_container_objectSpread(move_delete_container_objectSpread({}, ownProps), stateProps), restDispatchProps), {}, {
    removeRSVP: () => {
      if (window.confirm(
      // eslint-disable-line no-alert
      Object(external_wp_i18n_["__"])('Are you sure you want to delete this RSVP? It cannot be undone.', 'event-tickets'))) {
        dispatch(rsvp["a" /* actions */].deleteRSVP());
        if (stateProps.created && stateProps.rsvpId) {
          dispatch(rsvp["e" /* thunks */].deleteRSVP(stateProps.rsvpId));
        }
        Object(external_wp_data_["dispatch"])('core/editor').removeBlocks([ownProps.clientId]);
      }
    },
    moveRSVP: () => dispatchProps.moveRSVP(stateProps.rsvpId)
  });
};
/* harmony default export */ var move_delete_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(move_delete_container_mapStateToProps, move_delete_container_mapDispatchToProps, container_mergeProps))(move_delete_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/action-dashboard/style.pcss
var action_dashboard_style = __webpack_require__("i9sy");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/action-dashboard/template.js

/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const confirmLabel = created => created ? Object(external_wp_i18n_["__"])('Update RSVP', 'event-tickets') : Object(external_wp_i18n_["__"])('Create RSVP', 'event-tickets');
const cancelLabel = Object(external_wp_i18n_["__"])('Cancel', 'event-tickets');
class template_RSVPActionDashboard extends external_React_["PureComponent"] {
  constructor(props) {
    super(props);
    defineProperty_default()(this, "onWarningClick", () => {
      this.setState({
        isWarningOpen: !this.state.isWarningOpen
      });
    });
    defineProperty_default()(this, "getActions", () => {
      const actions = [];
      if (this.props.created) {
        actions.push(wp.element.createElement(move_delete_container, {
          clientId: this.props.clientId
        }));
      }
      return actions;
    });
    this.state = {
      isWarningOpen: false
    };
  }
  render() {
    const {
      created,
      hasRecurrenceRules,
      isConfirmDisabled,
      onCancelClick,
      onConfirmClick
    } = this.props;

    /* eslint-disable max-len */
    return wp.element.createElement(external_React_["Fragment"], null, wp.element.createElement(modules_elements["ActionDashboard"], {
      className: "tribe-editor__rsvp__action-dashboard tribe-common",
      actions: this.getActions(),
      cancelLabel: cancelLabel,
      confirmLabel: confirmLabel(created),
      isConfirmDisabled: isConfirmDisabled,
      onCancelClick: onCancelClick,
      onConfirmClick: onConfirmClick,
      showCancel: true
    }), hasRecurrenceRules && wp.element.createElement("div", {
      className: "tribe-editor__rsvp__warning"
    }, Object(external_wp_i18n_["__"])('This is a recurring event. If you add tickets they will only show up on the next upcoming event in the recurrence pattern. The same ticket form will appear across all events in the series. Please configure your events accordingly.', 'event-tickets')));
    /* eslint-enable max-len */
  }
}
defineProperty_default()(template_RSVPActionDashboard, "propTypes", {
  clientId: external_tribe_modules_propTypes_default.a.string.isRequired,
  created: external_tribe_modules_propTypes_default.a.bool.isRequired,
  hasRecurrenceRules: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isCancelDisabled: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isConfirmDisabled: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isLoading: external_tribe_modules_propTypes_default.a.bool.isRequired,
  onCancelClick: external_tribe_modules_propTypes_default.a.func.isRequired,
  onConfirmClick: external_tribe_modules_propTypes_default.a.func.isRequired,
  showCancel: external_tribe_modules_propTypes_default.a.bool.isRequired
});
/* harmony default export */ var action_dashboard_template = (template_RSVPActionDashboard);
// EXTERNAL MODULE: external "tribe.common.utils.recurrence"
var external_tribe_common_utils_recurrence_ = __webpack_require__("8C5M");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/action-dashboard/container.js


const action_dashboard_container_excluded = ["state"];
function action_dashboard_container_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function action_dashboard_container_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? action_dashboard_container_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : action_dashboard_container_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const getIsConfirmDisabled = state => !rsvp["d" /* selectors */].getRSVPTempTitle(state) || !rsvp["d" /* selectors */].getRSVPHasChanges(state) || rsvp["d" /* selectors */].getRSVPIsLoading(state) || rsvp["d" /* selectors */].getRSVPHasDurationError(state);
const container_onCancelClick = (state, dispatch) => () => {
  dispatch(rsvp["a" /* actions */].setRSVPTempDetails({
    tempTitle: rsvp["d" /* selectors */].getRSVPTitle(state),
    tempDescription: rsvp["d" /* selectors */].getRSVPDescription(state),
    tempCapacity: rsvp["d" /* selectors */].getRSVPCapacity(state),
    tempNotGoingResponses: rsvp["d" /* selectors */].getRSVPNotGoingResponses(state),
    tempStartDate: rsvp["d" /* selectors */].getRSVPStartDate(state),
    tempStartDateInput: rsvp["d" /* selectors */].getRSVPStartDateInput(state),
    tempStartDateMoment: rsvp["d" /* selectors */].getRSVPStartDateMoment(state),
    tempEndDate: rsvp["d" /* selectors */].getRSVPEndDate(state),
    tempEndDateInput: rsvp["d" /* selectors */].getRSVPEndDateInput(state),
    tempEndDateMoment: rsvp["d" /* selectors */].getRSVPEndDateMoment(state),
    tempStartTime: rsvp["d" /* selectors */].getRSVPStartTime(state),
    tempEndTime: rsvp["d" /* selectors */].getRSVPEndTime(state),
    tempStartTimeInput: rsvp["d" /* selectors */].getRSVPStartTimeInput(state),
    tempEndTimeInput: rsvp["d" /* selectors */].getRSVPEndTimeInput(state)
  }));
  dispatch(rsvp["a" /* actions */].setRSVPHasChanges(false));
  dispatch(rsvp["a" /* actions */].setRSVPIsAddEditOpen(false));
  Object(external_wp_data_["dispatch"])('core/editor').clearSelectedBlock();
};
const container_onConfirmClick = (state, dispatch) => () => {
  const payload = {
    title: rsvp["d" /* selectors */].getRSVPTempTitle(state),
    description: rsvp["d" /* selectors */].getRSVPTempDescription(state),
    capacity: rsvp["d" /* selectors */].getRSVPTempCapacity(state),
    notGoingResponses: rsvp["d" /* selectors */].getRSVPTempNotGoingResponses(state),
    startDate: rsvp["d" /* selectors */].getRSVPTempStartDate(state),
    startDateInput: rsvp["d" /* selectors */].getRSVPTempStartDateInput(state),
    startDateMoment: rsvp["d" /* selectors */].getRSVPTempStartDateMoment(state),
    endDate: rsvp["d" /* selectors */].getRSVPTempEndDate(state),
    endDateInput: rsvp["d" /* selectors */].getRSVPTempEndDateInput(state),
    endDateMoment: rsvp["d" /* selectors */].getRSVPTempEndDateMoment(state),
    startTime: rsvp["d" /* selectors */].getRSVPTempStartTime(state),
    endTime: rsvp["d" /* selectors */].getRSVPTempEndTime(state),
    startTimeInput: rsvp["d" /* selectors */].getRSVPTempStartTimeInput(state),
    endTimeInput: rsvp["d" /* selectors */].getRSVPTempEndTimeInput(state)
  };
  if (!rsvp["d" /* selectors */].getRSVPCreated(state)) {
    dispatch(rsvp["e" /* thunks */].createRSVP(action_dashboard_container_objectSpread(action_dashboard_container_objectSpread({}, payload), {}, {
      postId: Object(external_wp_data_["select"])('core/editor').getCurrentPostId()
    })));
  } else {
    dispatch(rsvp["e" /* thunks */].updateRSVP(action_dashboard_container_objectSpread(action_dashboard_container_objectSpread({}, payload), {}, {
      id: rsvp["d" /* selectors */].getRSVPId(state)
    })));
  }
};
const action_dashboard_container_mapStateToProps = state => ({
  created: rsvp["d" /* selectors */].getRSVPCreated(state),
  hasRecurrenceRules: Object(external_tribe_common_utils_recurrence_["hasRecurrenceRules"])(state),
  noTicketsOnRecurring: Object(external_tribe_common_utils_recurrence_["noTicketsOnRecurring"])(),
  isCancelDisabled: rsvp["d" /* selectors */].getRSVPIsLoading(state),
  isConfirmDisabled: getIsConfirmDisabled(state),
  isLoading: rsvp["d" /* selectors */].getRSVPIsLoading(state),
  showCancel: rsvp["d" /* selectors */].getRSVPCreated(state),
  state
});
const action_dashboard_container_mergeProps = (stateProps, dispatchProps, ownProps) => {
  const {
      state
    } = stateProps,
    restStateProps = objectWithoutProperties_default()(stateProps, action_dashboard_container_excluded);
  const {
    dispatch
  } = dispatchProps;
  return action_dashboard_container_objectSpread(action_dashboard_container_objectSpread(action_dashboard_container_objectSpread({}, ownProps), restStateProps), {}, {
    onCancelClick: container_onCancelClick(state, dispatch),
    onConfirmClick: container_onConfirmClick(state, dispatch, ownProps)
  });
};
/* harmony default export */ var action_dashboard_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(action_dashboard_container_mapStateToProps, null, action_dashboard_container_mergeProps))(action_dashboard_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/header-image/style.pcss
var header_image_style = __webpack_require__("sMOv");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/header-image/template.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const RSVPHeaderImage = _ref => {
  let {
    image,
    isSettingsLoading,
    onRemove,
    onSelect
  } = _ref;
  const description = !(image !== null && image !== void 0 && image.src) && Object(external_wp_i18n_["__"])( /* eslint-disable-next-line max-len */
  'Select an image from your Media Library to display on emailed tickets and RSVPs. For best results, use a .jpg, .png, or .gif at least 1160px wide.', 'event-tickets');
  const imageUploadProps = {
    title: Object(external_wp_i18n_["__"])('Ticket Header Image', 'event-tickets'),
    description,
    className: 'tribe-editor__rsvp__image-upload',
    buttonDisabled: isSettingsLoading,
    buttonLabel: Object(external_wp_i18n_["__"])('Upload Image', 'event-tickets'),
    image,
    onRemove,
    onSelect,
    removeButtonDisabled: isSettingsLoading
  };
  return wp.element.createElement(external_tribe_common_elements_["ImageUpload"], imageUploadProps);
};
RSVPHeaderImage.propTypes = {
  image: external_tribe_modules_propTypes_default.a.shape({
    alt: external_tribe_modules_propTypes_default.a.string.isRequired,
    id: external_tribe_modules_propTypes_default.a.number.isRequired,
    src: external_tribe_modules_propTypes_default.a.string.isRequired
  }).isRequired,
  isSettingsLoading: external_tribe_modules_propTypes_default.a.bool.isRequired,
  onRemove: external_tribe_modules_propTypes_default.a.func.isRequired,
  onSelect: external_tribe_modules_propTypes_default.a.func.isRequired
};
/* harmony default export */ var header_image_template = (RSVPHeaderImage);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/header-image/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */




/**
 * Full payload from gutenberg media upload is not used,
 * only id, alt, and src are used for this specific case.
 */

const header_image_container_mapStateToProps = state => ({
  image: {
    id: rsvp["d" /* selectors */].getRSVPHeaderImageId(state),
    alt: rsvp["d" /* selectors */].getRSVPHeaderImageAlt(state),
    src: rsvp["d" /* selectors */].getRSVPHeaderImageSrc(state)
  },
  isSettingsLoading: rsvp["d" /* selectors */].getRSVPIsSettingsLoading(state)
});
const header_image_container_mapDispatchToProps = dispatch => ({
  /**
   * Full payload from gutenberg media upload is not used,
   * only id, alt, and medium src are used for this specific case.
   */

  onSelect: image => dispatch(rsvp["a" /* actions */].updateRSVPHeaderImage(image)),
  onRemove: () => dispatch(rsvp["a" /* actions */].deleteRSVPHeaderImage())
});
/* harmony default export */ var header_image_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(header_image_container_mapStateToProps, header_image_container_mapDispatchToProps))(header_image_template));
// EXTERNAL MODULE: ./src/modules/blocks/rsvp/settings-dashboard/style.pcss
var settings_dashboard_style = __webpack_require__("dm1+");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/settings-dashboard/template.js
/**
 * External dependencies
 */




/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




const RSVPSettingsDashboard = _ref => {
  let {
    isSettingsLoading,
    onCloseClick
  } = _ref;
  return wp.element.createElement(modules_elements["SettingsDashboard"], {
    className: external_tribe_modules_classnames_default()('tribe-editor__rsvp__settings-dashboard', {
      'tribe-editor__rsvp__settings-dashboard--loading': isSettingsLoading
    }),
    closeButtonDisabled: isSettingsLoading,
    content: wp.element.createElement(external_React_["Fragment"], null, wp.element.createElement(header_image_container, null), isSettingsLoading && wp.element.createElement(external_wp_components_["Spinner"], null)),
    headerLeft: wp.element.createElement(external_React_["Fragment"], null, wp.element.createElement(icons["Settings"], null), wp.element.createElement("span", {
      className: "tribe-editor__settings-dashboard__header-left-text"
    }, Object(external_wp_i18n_["__"])('RSVP Settings', 'event-tickets'))),
    onCloseClick: onCloseClick
  });
};
RSVPSettingsDashboard.propTypes = {
  isSettingsLoading: external_tribe_modules_propTypes_default.a.bool.isRequired,
  onCloseClick: external_tribe_modules_propTypes_default.a.func.isRequired
};
/* harmony default export */ var settings_dashboard_template = (RSVPSettingsDashboard);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/settings-dashboard/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const settings_dashboard_container_mapStateToProps = state => ({
  isSettingsLoading: rsvp["d" /* selectors */].getRSVPIsSettingsLoading(state)
});
const settings_dashboard_container_mapDispatchToProps = dispatch => ({
  onCloseClick: () => dispatch(rsvp["a" /* actions */].setRSVPSettingsOpen(false))
});
/* harmony default export */ var settings_dashboard_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(settings_dashboard_container_mapStateToProps, settings_dashboard_container_mapDispatchToProps))(settings_dashboard_template));
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/inactive-block/template.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

const RSVPInactiveBlock = _ref => {
  let {
    created,
    setAddEditOpen
  } = _ref;
  const title = created ? Object(external_wp_i18n_["__"])('RSVP is not currently active', 'event-tickets') : Object(external_wp_i18n_["__"])('Add an RSVP', 'event-tickets');
  const description = created ? Object(external_wp_i18n_["__"])('Edit this block to change RSVP settings.', 'event-tickets') : Object(external_wp_i18n_["__"])('Allow users to confirm their attendance.', 'event-tickets');

  /* eslint-disable max-len */
  const leftColumn = wp.element.createElement(external_React_default.a.Fragment, null, wp.element.createElement("h3", {
    className: "tribe-editor__rsvp-title tribe-common-h2 tribe-common-h4--min-medium"
  }, title), wp.element.createElement("div", {
    className: "tribe-editor__rsvp-description tribe-common-h6 tribe-common-h--alt tribe-common-b3--min-medium"
  }, description));
  const rightColumn = wp.element.createElement(external_React_default.a.Fragment, null, wp.element.createElement("button", {
    id: "add-rsvp",
    className: "tribe-common-c-btn tribe-common-b1 tribe-common-b2--min-medium",
    onClick: setAddEditOpen
  }, Object(external_wp_i18n_["__"])('Add RSVP', 'event-tickets')));
  /* eslint-enable max-len */

  return wp.element.createElement(modules_elements["Card"], {
    className: "tribe-common tribe-editor__inactive-block--rsvp"
  }, wp.element.createElement(modules_elements["SplitContainer"], {
    leftColumn: leftColumn,
    rightColumn: rightColumn
  }));
};
RSVPInactiveBlock.propTypes = {
  created: external_tribe_modules_propTypes_default.a.bool.isRequired,
  setAddEditOpen: external_tribe_modules_propTypes_default.a.func.isRequired
};
/* harmony default export */ var inactive_block_template = (RSVPInactiveBlock);
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/inactive-block/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



const inactive_block_container_mapStateToProps = state => ({
  created: rsvp["d" /* selectors */].getRSVPCreated(state)
});
const inactive_block_container_mapDispatchToProps = dispatch => ({
  setAddEditOpen: () => dispatch(rsvp["a" /* actions */].setRSVPIsAddEditOpen(true))
});
/* harmony default export */ var inactive_block_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(inactive_block_container_mapStateToProps, inactive_block_container_mapDispatchToProps))(inactive_block_template));
// EXTERNAL MODULE: ./src/modules/elements/move-modal/index.js + 2 modules
var move_modal = __webpack_require__("mzTd");

// EXTERNAL MODULE: ./src/modules/blocks/rsvp/style.pcss
var rsvp_style = __webpack_require__("ocgc");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/template.js
/**
 * External dependencies
 */




/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */







const RSVP = _ref => {
  let {
    clientId,
    created,
    hasRecurrenceRules,
    initializeRSVP,
    isAddEditOpen,
    isInactive,
    isLoading,
    isModalShowing,
    isSelected,
    isSettingsOpen,
    noRsvpsOnRecurring,
    rsvpId,
    setAddEditClosed
  } = _ref;
  const rsvpBlockRef = Object(external_React_["useRef"])(null);
  const handleAddEditClose = Object(external_React_["useCallback"])(event => {
    const rsvpButtons = ['add-rsvp', 'edit-rsvp', 'attendees-rsvp', 'settings-rsvp'];
    if (rsvpBlockRef.current && !rsvpBlockRef.current.contains(event.target) && !rsvpButtons.includes(event.target.id)) {
      setAddEditClosed();
    }
  }, [setAddEditClosed]);
  Object(external_React_["useEffect"])(() => {
    !rsvpId && initializeRSVP();
    document.addEventListener('click', handleAddEditClose);
    return () => document.removeEventListener('click', handleAddEditClose);
  }, [handleAddEditClose, initializeRSVP, rsvpId]);
  const renderBlock = () => {
    const displayInactive = !isAddEditOpen && (created && isInactive || !created);
    return wp.element.createElement("div", {
      ref: rsvpBlockRef
    }, displayInactive ? wp.element.createElement(inactive_block_container, null) : !isSettingsOpen && wp.element.createElement(modules_elements["Card"], {
      className: external_tribe_modules_classnames_default()('tribe-editor__rsvp', {
        'tribe-editor__rsvp--add-edit-open': isAddEditOpen
      }, {
        'tribe-editor__rsvp--selected': isSelected
      }, {
        'tribe-editor__rsvp--loading': isLoading
      })
    }, wp.element.createElement(container_container, {
      isSelected: isSelected,
      clientId: clientId
    }), isAddEditOpen && wp.element.createElement(action_dashboard_container, {
      clientId: clientId
    }), isLoading && wp.element.createElement(external_wp_components_["Spinner"], null)), isSettingsOpen && wp.element.createElement(settings_dashboard_container, null), isModalShowing && wp.element.createElement(move_modal["a" /* default */], null));
  };
  const renderBlockNotSupported = () => {
    return wp.element.createElement("div", {
      className: "tribe-editor__not-supported-message"
    }, wp.element.createElement("p", {
      className: "tribe-editor__not-supported-message-text"
    }, Object(external_wp_i18n_["__"])('RSVPs are not yet supported on recurring events.', 'event-tickets'), wp.element.createElement("br", null), wp.element.createElement("a", {
      className: "tribe-editor__not-supported-message-link",
      href: "https://evnt.is/1b7a",
      target: "_blank",
      rel: "noopener noreferrer"
    }, Object(external_wp_i18n_["__"])('Read about our plans for future features.', 'event-tickets')), wp.element.createElement("br", null), wp.element.createElement(external_wp_components_["Button"], {
      variant: "secondary",
      onClick: () => wp.data.dispatch('core/block-editor').removeBlock(clientId)
    }, Object(external_wp_i18n_["__"])('Remove block', 'event-tickets'))));
  };
  if (hasRecurrenceRules && noRsvpsOnRecurring) {
    return renderBlockNotSupported();
  }
  return renderBlock();
};
RSVP.propTypes = {
  clientId: external_tribe_modules_propTypes_default.a.string.isRequired,
  created: external_tribe_modules_propTypes_default.a.bool.isRequired,
  hasRecurrenceRules: external_tribe_modules_propTypes_default.a.bool.isRequired,
  initializeRSVP: external_tribe_modules_propTypes_default.a.func.isRequired,
  isAddEditOpen: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isInactive: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isLoading: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isModalShowing: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isSelected: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isSettingsOpen: external_tribe_modules_propTypes_default.a.bool.isRequired,
  noRsvpsOnRecurring: external_tribe_modules_propTypes_default.a.bool.isRequired,
  rsvpId: external_tribe_modules_propTypes_default.a.number.isRequired,
  setAddEditClosed: external_tribe_modules_propTypes_default.a.func.isRequired
};
/* harmony default export */ var rsvp_template = (RSVP);
// EXTERNAL MODULE: ./src/modules/blocks/hoc/with-save-data.js
var with_save_data = __webpack_require__("fIBd");

// CONCATENATED MODULE: ./src/modules/blocks/rsvp/container.js
/**
 * External dependencies
 */




/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */







const getIsInactive = state => {
  const startDateMoment = rsvp["d" /* selectors */].getRSVPStartDateMoment(state);
  const startTime = rsvp["d" /* selectors */].getRSVPStartTimeNoSeconds(state);
  const endDateMoment = rsvp["d" /* selectors */].getRSVPEndDateMoment(state);
  const endTime = rsvp["d" /* selectors */].getRSVPEndTimeNoSeconds(state);
  if (!startDateMoment || !endDateMoment) {
    return false;
  }
  const startMoment = external_tribe_common_utils_["moment"].setTimeInSeconds(startDateMoment.clone(), external_tribe_common_utils_["time"].toSeconds(startTime, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM));
  const endMoment = external_tribe_common_utils_["moment"].setTimeInSeconds(endDateMoment.clone(), external_tribe_common_utils_["time"].toSeconds(endTime, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM));
  const currentMoment = external_moment_default()();
  return !(currentMoment.isAfter(startMoment) && currentMoment.isBefore(endMoment));
};
const container_setInitialState = (dispatch, ownProps) => () => {
  const postId = Object(external_wp_data_["select"])('core/editor').getCurrentPostId();
  dispatch(rsvp["e" /* thunks */].getRSVP(postId));
  const {
    attributes = {}
  } = ownProps;
  if (parseInt(attributes.headerImageId, 10)) {
    dispatch(rsvp["a" /* actions */].fetchRSVPHeaderImage(attributes.headerImageId));
  }
  if (attributes.goingCount) {
    dispatch(rsvp["a" /* actions */].setRSVPGoingCount(parseInt(attributes.goingCount, 10)));
  }
  if (attributes.notGoingCount) {
    dispatch(rsvp["a" /* actions */].setRSVPNotGoingCount(parseInt(attributes.notGoingCount, 10)));
  }
};
const rsvp_container_mapStateToProps = state => {
  const rsvpId = rsvp["d" /* selectors */].getRSVPId(state);
  return {
    created: rsvp["d" /* selectors */].getRSVPCreated(state),
    isAddEditOpen: rsvp["d" /* selectors */].getRSVPIsAddEditOpen(state),
    isInactive: getIsInactive(state),
    isLoading: rsvp["d" /* selectors */].getRSVPIsLoading(state),
    isModalShowing: Object(selectors["l" /* isModalShowing */])(state) && Object(selectors["e" /* getModalTicketId */])(state) === rsvpId,
    isSettingsOpen: rsvp["d" /* selectors */].getRSVPSettingsOpen(state),
    hasRecurrenceRules: Object(external_tribe_common_utils_recurrence_["hasRecurrenceRules"])(state),
    noRsvpsOnRecurring: Object(external_tribe_common_utils_recurrence_["noRsvpsOnRecurring"])(),
    rsvpId
  };
};
const rsvp_container_mapDispatchToProps = (dispatch, ownProps) => ({
  initializeRSVP: () => dispatch(rsvp["a" /* actions */].initializeRSVP()),
  onBlockRemoved: () => dispatch(rsvp["a" /* actions */].deleteRSVP()),
  setInitialState: container_setInitialState(dispatch, ownProps),
  setAddEditClosed: () => dispatch(rsvp["a" /* actions */].setRSVPIsAddEditOpen(false))
});
/* harmony default export */ var rsvp_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(rsvp_container_mapStateToProps, rsvp_container_mapDispatchToProps), Object(with_save_data["a" /* default */])())(rsvp_template));
// CONCATENATED MODULE: ./src/modules/blocks/rsvp/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




/**
 * Module Code
 */
/* harmony default export */ var blocks_rsvp = ({
  id: 'rsvp',
  title: Object(external_wp_i18n_["__"])('RSVP', 'event-tickets'),
  description: Object(external_wp_i18n_["__"])('Find out who is planning to attend!', 'event-tickets'),
  icon: wp.element.createElement(icons["RSVP"], null),
  category: 'tribe-tickets',
  keywords: ['event', 'events-gutenberg', 'tribe'],
  supports: {
    html: false,
    multiple: false,
    customClassName: false
  },
  attributes: {
    goingCount: {
      type: 'integer',
      source: 'meta',
      meta: utils["g" /* KEY_TICKET_GOING_COUNT */]
    },
    notGoingCount: {
      type: 'integer',
      source: 'meta',
      meta: utils["j" /* KEY_TICKET_NOT_GOING_COUNT */]
    },
    headerImageId: {
      type: 'integer',
      source: 'meta',
      meta: utils["i" /* KEY_TICKET_HEADER */]
    }
  },
  edit: rsvp_container,
  save: () => null
});
// EXTERNAL MODULE: external "tribe.modules.reactInputAutosize"
var external_tribe_modules_reactInputAutosize_ = __webpack_require__("AuWn");
var external_tribe_modules_reactInputAutosize_default = /*#__PURE__*/__webpack_require__.n(external_tribe_modules_reactInputAutosize_);

// EXTERNAL MODULE: external "wp.editor"
var external_wp_editor_ = __webpack_require__("jSdM");

// EXTERNAL MODULE: ./src/modules/blocks/attendees/style.pcss
var attendees_style = __webpack_require__("k3Up");

// CONCATENATED MODULE: ./src/modules/blocks/attendees/template.js
/**
 * External dependencies
 */





/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



/**
 * Module Code
 */

const placeholder = Object(external_wp_i18n_["__"])('Who\'s Attending?', 'event-tickets');
const subtitle = Object(external_wp_i18n_["__"])('(X) people are attending this event', 'event-tickets');
const renderLabelInput = _ref => {
  let {
    isSelected,
    isEmpty,
    title,
    setTitle
  } = _ref;
  const containerClassNames = external_tribe_modules_classnames_default()({
    'tribe-editor__event-attendees__title': true,
    'tribe-editor__event-attendees__title--selected': isSelected
  });
  const inputClassNames = external_tribe_modules_classnames_default()({
    'tribe-editor__event-attendees__title-text': true,
    'tribe-editor__event-attendees__title-text--empty': isEmpty && isSelected
  });
  return wp.element.createElement("div", {
    key: "tribe-events-attendees-label",
    className: containerClassNames
  }, wp.element.createElement(external_tribe_modules_reactInputAutosize_default.a, {
    id: "tribe-events-attendees-link",
    className: inputClassNames,
    value: title,
    placeholder: placeholder,
    onChange: setTitle
  }));
};
const renderPlaceholder = () => {
  const classes = ['tribe-editor__event-attendees__title', 'tribe-editor__event-attendees__title--placeholder'];
  return wp.element.createElement("span", {
    className: external_tribe_modules_classnames_default()(classes)
  }, placeholder);
};
const RenderGravatars = () => wp.element.createElement("div", {
  className: "tribe-editor__event-attendees__gravatars"
}, wp.element.createElement(icons["AttendeesGravatar"], null), wp.element.createElement(icons["AttendeesGravatar"], null), wp.element.createElement(icons["AttendeesGravatar"], null), wp.element.createElement(icons["AttendeesGravatar"], null), wp.element.createElement(icons["AttendeesGravatar"], null));
const RenderSubtitle = () => wp.element.createElement("div", {
  className: "tribe-editor__event-attendees__subtitle"
}, wp.element.createElement("p", null, subtitle));
const UI = props => {
  const {
    isSelected,
    title,
    displayTitle,
    displaySubtitle
  } = props;
  const blockTitle = !(isSelected || title) ? renderPlaceholder() : renderLabelInput(props);
  return wp.element.createElement("div", {
    className: "tribe-editor__block tribe-editor__event-attendees"
  }, displayTitle ? blockTitle : '', displaySubtitle ? wp.element.createElement(RenderSubtitle, null) : '', wp.element.createElement(RenderGravatars, null));
};
UI.propTypes = {
  displaySubtitle: external_tribe_modules_propTypes_default.a.bool,
  displayTitle: external_tribe_modules_propTypes_default.a.bool,
  isSelected: external_tribe_modules_propTypes_default.a.bool,
  title: external_tribe_modules_propTypes_default.a.string
};
const Controls = _ref2 => {
  let {
    isSelected,
    displayTitle,
    displaySubtitle,
    onSetDisplayTitleChange,
    onSetDisplaySubtitleChange
  } = _ref2;
  return isSelected && wp.element.createElement(external_wp_editor_["InspectorControls"], {
    key: "inspector"
  }, wp.element.createElement(external_wp_components_["PanelBody"], {
    title: Object(external_wp_i18n_["__"])('Attendees Settings', 'event-tickets')
  }, wp.element.createElement(external_wp_components_["ToggleControl"], {
    label: Object(external_wp_i18n_["__"])('Display Title', 'event-tickets'),
    checked: displayTitle,
    onChange: onSetDisplayTitleChange
  }), wp.element.createElement(external_wp_components_["ToggleControl"], {
    label: Object(external_wp_i18n_["__"])('Display Subtitle', 'event-tickets'),
    checked: displaySubtitle,
    onChange: onSetDisplaySubtitleChange
  })));
};
Controls.propTypes = {
  displaySubtitle: external_tribe_modules_propTypes_default.a.bool,
  displayTitle: external_tribe_modules_propTypes_default.a.bool,
  isSelected: external_tribe_modules_propTypes_default.a.bool,
  onSetDisplaySubtitleChange: external_tribe_modules_propTypes_default.a.func,
  onSetDisplayTitleChange: external_tribe_modules_propTypes_default.a.func
};
const Attendees = props => wp.element.createElement(external_React_["Fragment"], null, wp.element.createElement(UI, props), wp.element.createElement(Controls, props));
Attendees.propTypes = {
  setTitle: external_tribe_modules_propTypes_default.a.func,
  title: external_tribe_modules_propTypes_default.a.string,
  isSelected: external_tribe_modules_propTypes_default.a.bool,
  isEmpty: external_tribe_modules_propTypes_default.a.bool,
  displayTitle: external_tribe_modules_propTypes_default.a.bool,
  displaySubtitle: external_tribe_modules_propTypes_default.a.bool,
  onSetDisplaySubtitleChange: external_tribe_modules_propTypes_default.a.func,
  onSetDisplayTitleChange: external_tribe_modules_propTypes_default.a.func
};
/* harmony default export */ var attendees_template = (Attendees);
// CONCATENATED MODULE: ./src/modules/blocks/attendees/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */






/**
 * Module Code
 */

const attendees_container_mapStateToProps = state => ({
  title: getTitle(state),
  displayTitle: getDisplayTitle(state),
  displaySubtitle: getDisplaySubtitle(state)
});
const attendees_container_mapDispatchToProps = dispatch => ({
  setInitialState: props => dispatch(setInitialState(props)),
  setTitle: e => dispatch(actions_setTitle(e.target.value)),
  onSetDisplayTitleChange: checked => dispatch(setDisplayTitle(checked)),
  onSetDisplaySubtitleChange: checked => dispatch(setDisplaySubtitle(checked))
});
/* harmony default export */ var attendees_container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(attendees_container_mapStateToProps, attendees_container_mapDispatchToProps), Object(with_save_data["a" /* default */])())(attendees_template));
// CONCATENATED MODULE: ./src/modules/blocks/attendees/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Module Code
 */
/* harmony default export */ var blocks_attendees = ({
  id: 'attendees',
  title: Object(external_wp_i18n_["__"])('Attendee List', 'event-tickets'),
  description: Object(external_wp_i18n_["__"])('Show the gravatars of people coming to this event.', 'event-tickets'),
  icon: wp.element.createElement(icons["Attendees"], null),
  category: 'tribe-tickets',
  keywords: ['event', 'events-gutenberg', 'tribe'],
  supports: {
    html: false,
    customClassName: false
  },
  attributes: {
    title: {
      type: 'html',
      default: Object(external_wp_i18n_["__"])('Who\'s Attending?', 'event-tickets')
    },
    displayTitle: {
      type: 'boolean',
      default: true
    },
    displaySubtitle: {
      type: 'boolean',
      default: true
    }
  },
  edit: attendees_container,
  save: () => null
});
// CONCATENATED MODULE: ./src/modules/blocks/index.js
/**
 * Wordpress dependencies
 */

const {
  applyFilters,
  doAction
} = wp.hooks;

/**
 * Internal dependencies
 */



let blocks_blocks = [blocks_rsvp, blocks_attendees];

/**
 * Allows filtering the list of blocks registered by Event Tickets.
 *
 * the
 *
 * @since 5.8.0
 * @param {Object[]} blocks The blocks that will be registered.
 */
blocks_blocks = applyFilters('tec.tickets.blocks.beforeRegistration', blocks_blocks);
blocks_blocks.forEach(block => Object(external_wp_blocks_["registerBlockType"])(`tribe/${block.id}`, block));

/**
 * Fires an action after Event Tickets blocks are registered.
 *
 * @since 5.8.0
 * @param {Object[]} blocks The blocks that were registered.
 */
doAction('tec.tickets.blocks.afterRegistration', blocks_blocks);

// Initialize AFTER blocks are registered
// to avoid plugin shown as available in reducer
// but not having block available for use
initStore();
/* harmony default export */ var modules_blocks = (blocks_blocks);
// CONCATENATED MODULE: ./src/modules/index.js
/**
 * Internal dependencies
 */






/***/ }),

/***/ "ZNLL":
/***/ (function(module, exports) {

module.exports = tribe.common.data;

/***/ }),

/***/ "Zz++":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "aAk0":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "apLV":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "b+3r":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "cDcd":
/***/ (function(module, exports) {

module.exports = React;

/***/ }),

/***/ "dm1+":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "e5yv":
/***/ (function(module, exports) {

module.exports = lodash.isArray;

/***/ }),

/***/ "enZp":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKETS_INITIAL_STATE", function() { return SET_TICKETS_INITIAL_STATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "RESET_TICKETS_BLOCK", function() { return RESET_TICKETS_BLOCK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKETS_HEADER_IMAGE", function() { return SET_TICKETS_HEADER_IMAGE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKETS_IS_SELECTED", function() { return SET_TICKETS_IS_SELECTED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKETS_IS_SETTINGS_OPEN", function() { return SET_TICKETS_IS_SETTINGS_OPEN; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKETS_IS_SETTINGS_LOADING", function() { return SET_TICKETS_IS_SETTINGS_LOADING; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKETS_PROVIDER", function() { return SET_TICKETS_PROVIDER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKETS_SHARED_CAPACITY", function() { return SET_TICKETS_SHARED_CAPACITY; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKETS_TEMP_SHARED_CAPACITY", function() { return SET_TICKETS_TEMP_SHARED_CAPACITY; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "FETCH_TICKETS_HEADER_IMAGE", function() { return FETCH_TICKETS_HEADER_IMAGE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "UPDATE_TICKETS_HEADER_IMAGE", function() { return UPDATE_TICKETS_HEADER_IMAGE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "DELETE_TICKETS_HEADER_IMAGE", function() { return DELETE_TICKETS_HEADER_IMAGE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "REGISTER_TICKET_BLOCK", function() { return REGISTER_TICKET_BLOCK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "REMOVE_TICKET_BLOCK", function() { return REMOVE_TICKET_BLOCK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "REMOVE_TICKET_BLOCKS", function() { return REMOVE_TICKET_BLOCKS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TITLE", function() { return SET_TICKET_TITLE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_DESCRIPTION", function() { return SET_TICKET_DESCRIPTION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_PRICE", function() { return SET_TICKET_PRICE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_ON_SALE", function() { return SET_TICKET_ON_SALE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SKU", function() { return SET_TICKET_SKU; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_IAC_SETTING", function() { return SET_TICKET_IAC_SETTING; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_START_DATE", function() { return SET_TICKET_START_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_START_DATE_INPUT", function() { return SET_TICKET_START_DATE_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_START_DATE_MOMENT", function() { return SET_TICKET_START_DATE_MOMENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_END_DATE", function() { return SET_TICKET_END_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_END_DATE_INPUT", function() { return SET_TICKET_END_DATE_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_END_DATE_MOMENT", function() { return SET_TICKET_END_DATE_MOMENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_START_TIME", function() { return SET_TICKET_START_TIME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_END_TIME", function() { return SET_TICKET_END_TIME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_START_TIME_INPUT", function() { return SET_TICKET_START_TIME_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_END_TIME_INPUT", function() { return SET_TICKET_END_TIME_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_CAPACITY_TYPE", function() { return SET_TICKET_CAPACITY_TYPE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_CAPACITY", function() { return SET_TICKET_CAPACITY; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SALE_PRICE_CHECK", function() { return SET_TICKET_SALE_PRICE_CHECK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SALE_PRICE", function() { return SET_TICKET_SALE_PRICE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SALE_START_DATE", function() { return SET_TICKET_SALE_START_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SALE_START_DATE_INPUT", function() { return SET_TICKET_SALE_START_DATE_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SALE_START_DATE_MOMENT", function() { return SET_TICKET_SALE_START_DATE_MOMENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SALE_END_DATE", function() { return SET_TICKET_SALE_END_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SALE_END_DATE_INPUT", function() { return SET_TICKET_SALE_END_DATE_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SALE_END_DATE_MOMENT", function() { return SET_TICKET_SALE_END_DATE_MOMENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_TITLE", function() { return SET_TICKET_TEMP_TITLE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_DESCRIPTION", function() { return SET_TICKET_TEMP_DESCRIPTION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_PRICE", function() { return SET_TICKET_TEMP_PRICE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SKU", function() { return SET_TICKET_TEMP_SKU; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_IAC_SETTING", function() { return SET_TICKET_TEMP_IAC_SETTING; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_START_DATE", function() { return SET_TICKET_TEMP_START_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_START_DATE_INPUT", function() { return SET_TICKET_TEMP_START_DATE_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_START_DATE_MOMENT", function() { return SET_TICKET_TEMP_START_DATE_MOMENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_END_DATE", function() { return SET_TICKET_TEMP_END_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_END_DATE_INPUT", function() { return SET_TICKET_TEMP_END_DATE_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_END_DATE_MOMENT", function() { return SET_TICKET_TEMP_END_DATE_MOMENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_START_TIME", function() { return SET_TICKET_TEMP_START_TIME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_END_TIME", function() { return SET_TICKET_TEMP_END_TIME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_START_TIME_INPUT", function() { return SET_TICKET_TEMP_START_TIME_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_END_TIME_INPUT", function() { return SET_TICKET_TEMP_END_TIME_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_CAPACITY_TYPE", function() { return SET_TICKET_TEMP_CAPACITY_TYPE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_CAPACITY", function() { return SET_TICKET_TEMP_CAPACITY; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SALE_PRICE_CHECK", function() { return SET_TICKET_TEMP_SALE_PRICE_CHECK; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SALE_PRICE", function() { return SET_TICKET_TEMP_SALE_PRICE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SALE_START_DATE", function() { return SET_TICKET_TEMP_SALE_START_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SALE_START_DATE_INPUT", function() { return SET_TICKET_TEMP_SALE_START_DATE_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SALE_START_DATE_MOMENT", function() { return SET_TICKET_TEMP_SALE_START_DATE_MOMENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SALE_END_DATE", function() { return SET_TICKET_TEMP_SALE_END_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SALE_END_DATE_INPUT", function() { return SET_TICKET_TEMP_SALE_END_DATE_INPUT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_SALE_END_DATE_MOMENT", function() { return SET_TICKET_TEMP_SALE_END_DATE_MOMENT; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_SOLD", function() { return SET_TICKET_SOLD; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_AVAILABLE", function() { return SET_TICKET_AVAILABLE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_ID", function() { return SET_TICKET_ID; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_CURRENCY_SYMBOL", function() { return SET_TICKET_CURRENCY_SYMBOL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_CURRENCY_POSITION", function() { return SET_TICKET_CURRENCY_POSITION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_PROVIDER", function() { return SET_TICKET_PROVIDER; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_HAS_ATTENDEE_INFO_FIELDS", function() { return SET_TICKET_HAS_ATTENDEE_INFO_FIELDS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_ATTENDEE_INFO_FIELDS", function() { return SET_TICKET_ATTENDEE_INFO_FIELDS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_IS_LOADING", function() { return SET_TICKET_IS_LOADING; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_IS_MODAL_OPEN", function() { return SET_TICKET_IS_MODAL_OPEN; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_HAS_BEEN_CREATED", function() { return SET_TICKET_HAS_BEEN_CREATED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_HAS_CHANGES", function() { return SET_TICKET_HAS_CHANGES; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_HAS_DURATION_ERROR", function() { return SET_TICKET_HAS_DURATION_ERROR; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_IS_SELECTED", function() { return SET_TICKET_IS_SELECTED; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TYPE", function() { return SET_TICKET_TYPE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TYPE_DESCRIPTION", function() { return SET_TICKET_TYPE_DESCRIPTION; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TYPE_ICON_URL", function() { return SET_TICKET_TYPE_ICON_URL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TYPE_NAME", function() { return SET_TICKET_TYPE_NAME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_UNEDITABLE_TICKETS", function() { return SET_UNEDITABLE_TICKETS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_UNEDITABLE_TICKETS_LOADING", function() { return SET_UNEDITABLE_TICKETS_LOADING; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "UPDATE_UNEDITABLE_TICKETS", function() { return UPDATE_UNEDITABLE_TICKETS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_DETAILS", function() { return SET_TICKET_DETAILS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_TEMP_DETAILS", function() { return SET_TICKET_TEMP_DETAILS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "HANDLE_TICKET_START_DATE", function() { return HANDLE_TICKET_START_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "HANDLE_TICKET_END_DATE", function() { return HANDLE_TICKET_END_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "HANDLE_TICKET_START_TIME", function() { return HANDLE_TICKET_START_TIME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "HANDLE_TICKET_END_TIME", function() { return HANDLE_TICKET_END_TIME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "HANDLE_TICKET_SALE_START_DATE", function() { return HANDLE_TICKET_SALE_START_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "HANDLE_TICKET_SALE_END_DATE", function() { return HANDLE_TICKET_SALE_END_DATE; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "FETCH_TICKET", function() { return FETCH_TICKET; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "CREATE_NEW_TICKET", function() { return CREATE_NEW_TICKET; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "UPDATE_TICKET", function() { return UPDATE_TICKET; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "DELETE_TICKET", function() { return DELETE_TICKET; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SET_TICKET_INITIAL_STATE", function() { return SET_TICKET_INITIAL_STATE; });
/* harmony import */ var _moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("BNqv");
/* eslint-disable max-len */
/**
 * Internal dependencies
 */


//
// ─── TICKETS TYPES ──────────────────────────────────────────────────────────────
//

const SET_TICKETS_INITIAL_STATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKETS_INITIAL_STATE`;
const RESET_TICKETS_BLOCK = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/RESET_TICKETS_BLOCK`;
const SET_TICKETS_HEADER_IMAGE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKETS_HEADER_IMAGE`;
const SET_TICKETS_IS_SELECTED = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKETS_IS_SELECTED`;
const SET_TICKETS_IS_SETTINGS_OPEN = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKETS_IS_SETTINGS_OPEN`;
const SET_TICKETS_IS_SETTINGS_LOADING = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKETS_IS_SETTINGS_LOADING`;
const SET_TICKETS_PROVIDER = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKETS_PROVIDER`;
const SET_TICKETS_SHARED_CAPACITY = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKETS_SHARED_CAPACITY`;
const SET_TICKETS_TEMP_SHARED_CAPACITY = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKETS_TEMP_SHARED_CAPACITY`;

//
// ─── HEADER IMAGE SAGA TYPES ────────────────────────────────────────────────────
//

const FETCH_TICKETS_HEADER_IMAGE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/FETCH_TICKETS_HEADER_IMAGE`;
const UPDATE_TICKETS_HEADER_IMAGE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/UPDATE_TICKETS_HEADER_IMAGE`;
const DELETE_TICKETS_HEADER_IMAGE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/DELETE_TICKETS_HEADER_IMAGE`;

//
// ─── CHILD TICKET TYPES ─────────────────────────────────────────────────────────
//

const REGISTER_TICKET_BLOCK = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/REGISTER_TICKET_BLOCK`;
const REMOVE_TICKET_BLOCK = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/REMOVE_TICKET_BLOCK`;
const REMOVE_TICKET_BLOCKS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/REMOVE_TICKET_BLOCKS`;
const SET_TICKET_TITLE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TITLE`;
const SET_TICKET_DESCRIPTION = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_DESCRIPTION`;
const SET_TICKET_PRICE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_PRICE`;
const SET_TICKET_ON_SALE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_ON_SALE`;
const SET_TICKET_SKU = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SKU`;
const SET_TICKET_IAC_SETTING = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_IAC_SETTING`;
const SET_TICKET_START_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_START_DATE`;
const SET_TICKET_START_DATE_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_START_DATE_INPUT`;
const SET_TICKET_START_DATE_MOMENT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_START_DATE_MOMENT`;
const SET_TICKET_END_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_END_DATE`;
const SET_TICKET_END_DATE_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_END_DATE_INPUT`;
const SET_TICKET_END_DATE_MOMENT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_END_DATE_MOMENT`;
const SET_TICKET_START_TIME = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_START_TIME`;
const SET_TICKET_END_TIME = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_END_TIME`;
const SET_TICKET_START_TIME_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_START_TIME_INPUT`;
const SET_TICKET_END_TIME_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_END_TIME_INPUT`;
const SET_TICKET_CAPACITY_TYPE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_CAPACITY_TYPE`;
const SET_TICKET_CAPACITY = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_CAPACITY`;
const SET_TICKET_SALE_PRICE_CHECK = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SALE_PRICE_CHECK`;
const SET_TICKET_SALE_PRICE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SALE_PRICE`;
const SET_TICKET_SALE_START_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SALE_START_DATE`;
const SET_TICKET_SALE_START_DATE_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SALE_START_DATE_INPUT`;
const SET_TICKET_SALE_START_DATE_MOMENT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SALE_START_DATE_MOMENT`;
const SET_TICKET_SALE_END_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SALE_END_DATE`;
const SET_TICKET_SALE_END_DATE_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SALE_END_DATE_INPUT`;
const SET_TICKET_SALE_END_DATE_MOMENT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SALE_END_DATE_MOMENT`;
const SET_TICKET_TEMP_TITLE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_TITLE`;
const SET_TICKET_TEMP_DESCRIPTION = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_DESCRIPTION`;
const SET_TICKET_TEMP_PRICE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_PRICE`;
const SET_TICKET_TEMP_SKU = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SKU`;
const SET_TICKET_TEMP_IAC_SETTING = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_IAC_SETTING`;
const SET_TICKET_TEMP_START_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_START_DATE`;
const SET_TICKET_TEMP_START_DATE_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_START_DATE_INPUT`;
const SET_TICKET_TEMP_START_DATE_MOMENT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_START_DATE_MOMENT`;
const SET_TICKET_TEMP_END_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_END_DATE`;
const SET_TICKET_TEMP_END_DATE_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_END_DATE_INPUT`;
const SET_TICKET_TEMP_END_DATE_MOMENT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_END_DATE_MOMENT`;
const SET_TICKET_TEMP_START_TIME = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_START_TIME`;
const SET_TICKET_TEMP_END_TIME = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_END_TIME`;
const SET_TICKET_TEMP_START_TIME_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_START_TIME_INPUT`;
const SET_TICKET_TEMP_END_TIME_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_END_TIME_INPUT`;
const SET_TICKET_TEMP_CAPACITY_TYPE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_CAPACITY_TYPE`;
const SET_TICKET_TEMP_CAPACITY = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_CAPACITY`;
const SET_TICKET_TEMP_SALE_PRICE_CHECK = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SALE_PRICE_CHECK`;
const SET_TICKET_TEMP_SALE_PRICE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SALE_PRICE`;
const SET_TICKET_TEMP_SALE_START_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SALE_START_DATE`;
const SET_TICKET_TEMP_SALE_START_DATE_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SALE_START_DATE_INPUT`;
const SET_TICKET_TEMP_SALE_START_DATE_MOMENT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SALE_START_DATE_MOMENT`;
const SET_TICKET_TEMP_SALE_END_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SALE_END_DATE`;
const SET_TICKET_TEMP_SALE_END_DATE_INPUT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SALE_END_DATE_INPUT`;
const SET_TICKET_TEMP_SALE_END_DATE_MOMENT = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_SALE_END_DATE_MOMENT`;
const SET_TICKET_SOLD = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_SOLD`;
const SET_TICKET_AVAILABLE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_AVAILABLE`;
const SET_TICKET_ID = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_ID`;
const SET_TICKET_CURRENCY_SYMBOL = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_CURRENCY_SYMBOL`;
const SET_TICKET_CURRENCY_POSITION = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_CURRENCY_POSITION`;
const SET_TICKET_PROVIDER = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_PROVIDER`;
const SET_TICKET_HAS_ATTENDEE_INFO_FIELDS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_HAS_ATTENDEE_INFO_FIELDS`;
const SET_TICKET_ATTENDEE_INFO_FIELDS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_ATTENDEE_INFO_FIELDS`;
const SET_TICKET_IS_LOADING = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_IS_LOADING`;
const SET_TICKET_IS_MODAL_OPEN = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_IS_MODAL_OPEN`;
const SET_TICKET_HAS_BEEN_CREATED = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_HAS_BEEN_CREATED`;
const SET_TICKET_HAS_CHANGES = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_HAS_CHANGES`;
const SET_TICKET_HAS_DURATION_ERROR = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_HAS_DURATION_ERROR`;
const SET_TICKET_IS_SELECTED = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_IS_SELECTED`;
const SET_TICKET_TYPE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TYPE`;
const SET_TICKET_TYPE_DESCRIPTION = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TYPE_DESCRIPTION`;
const SET_TICKET_TYPE_ICON_URL = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TYPE_ICON_URL`;
const SET_TICKET_TYPE_NAME = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TYPE_NAME`;
const SET_UNEDITABLE_TICKETS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_UNEDITABLE_TICKETS`;
const SET_UNEDITABLE_TICKETS_LOADING = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_UNEDITABLE_TICKETS_LOADING`;
const UPDATE_UNEDITABLE_TICKETS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/UPDATE_UNEDITABLE_TICKETS`;

//
// ─── CHILD TICKET SAGA TYPES ────────────────────────────────────────────────────
//

const SET_TICKET_DETAILS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_DETAILS`;
const SET_TICKET_TEMP_DETAILS = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_TEMP_DETAILS`;
const HANDLE_TICKET_START_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/HANDLE_TICKET_START_DATE`;
const HANDLE_TICKET_END_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/HANDLE_TICKET_END_DATE`;
const HANDLE_TICKET_START_TIME = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/HANDLE_TICKET_START_TIME`;
const HANDLE_TICKET_END_TIME = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/HANDLE_TICKET_END_TIME`;
const HANDLE_TICKET_SALE_START_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/HANDLE_TICKET_SALE_START_DATE`;
const HANDLE_TICKET_SALE_END_DATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/HANDLE_TICKET_SALE_END_DATE`;
const FETCH_TICKET = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/FETCH_TICKET`;
const CREATE_NEW_TICKET = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/CREATE_NEW_TICKET`;
const UPDATE_TICKET = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/UPDATE_TICKET`;
const DELETE_TICKET = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/DELETE_TICKET`;
const SET_TICKET_INITIAL_STATE = `${_moderntribe_tickets_data_utils__WEBPACK_IMPORTED_MODULE_0__[/* PREFIX_TICKETS_STORE */ "n"]}/SET_TICKET_INITIAL_STATE`;

/***/ }),

/***/ "fIBd":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("lSNA");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var lodash_keys__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("HAtF");
/* harmony import */ var lodash_keys__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash_keys__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash_isObject__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("4oMP");
/* harmony import */ var lodash_isObject__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash_isObject__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var lodash_isArray__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__("e5yv");
/* harmony import */ var lodash_isArray__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash_isArray__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var lodash_isEmpty__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__("4Qn9");
/* harmony import */ var lodash_isEmpty__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(lodash_isEmpty__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var lodash_noop__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__("In0u");
/* harmony import */ var lodash_noop__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(lodash_noop__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__("cDcd");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__("rf6O");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(prop_types__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__("rl8x");
/* harmony import */ var _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_8__);






function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * @todo: This is just a holder for ET blocks until the block editor UX work.
 *        The `withSaveData()` HOC needs to be removed from common. Until the
 *        block editor UX work, this will live here.
 */

/**
 * External dependencies
 */



const blockRegister = {};

/**
 * Higher order component that updates the attributes of a component if any of the properties of the
 * attributes changes.
 *
 * Only updates the attributes that has changed with the new updates into the properties and only
 * the ones specified as attributes params otherwise will fallback to the property attributes of the
 * component to extract the keys of those to do the comparision.
 *
 * @param {Object} selectedAttributes Set of attributes to only update fallback to this.props.attributes
 * @returns {Function} Return a new HOC
 */
/* harmony default export */ __webpack_exports__["a"] = (function () {
  let selectedAttributes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  return WrappedComponent => {
    class WithSaveData extends react__WEBPACK_IMPORTED_MODULE_6__["Component"] {
      constructor(props) {
        super(props);
        _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(this, "keys", []);
        _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(this, "saving", null);
        this.keys = this.generateKeys();
      }
      generateKeys() {
        if (lodash_isArray__WEBPACK_IMPORTED_MODULE_3___default()(this.attrs)) {
          return this.attrs;
        }
        if (lodash_isObject__WEBPACK_IMPORTED_MODULE_2___default()(this.attrs)) {
          return lodash_keys__WEBPACK_IMPORTED_MODULE_1___default()(this.attrs);
        }
        console.warn('Make sure attributes is from a valid type: Array or Object');
        return [];
      }

      // At this point attributes has been set so no need to be set the initial state into the store here.
      componentDidMount() {
        const {
          setInitialState,
          attributes = {},
          isolated,
          onBlockCreated
        } = this.props;
        onBlockCreated(this.props);
        this.registerBlock();

        // Prevent to set the initial state for blocks that are copies from others
        // overwrite this with the isolated property of the block to `true`
        if (this.blockCount() > 1 && !isolated) {
          return;
        }
        setInitialState(_objectSpread(_objectSpread({}, this.props), {}, {
          get(key, defaultValue) {
            return key in attributes ? attributes[key] : defaultValue;
          }
        }));
      }
      componentWillUnmount() {
        const {
          onBlockRemoved
        } = this.props;
        this.unregisterBlock();
        onBlockRemoved(this.props);
      }
      registerBlock() {
        const {
          name
        } = this.props;
        blockRegister[name] = name in blockRegister ? blockRegister[name] + 1 : 1;
      }
      unregisterBlock() {
        const {
          name
        } = this.props;
        blockRegister[name] -= 1;
      }
      blockCount() {
        const {
          name
        } = this.props;
        return blockRegister[name];
      }
      componentDidUpdate() {
        const diff = this.calculateDiff();
        if (_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_8___default()(this.saving, diff)) {
          return;
        }
        this.saving = diff;
        if (lodash_isEmpty__WEBPACK_IMPORTED_MODULE_4___default()(diff)) {
          return;
        }
        this.props.setAttributes(diff);
      }
      calculateDiff() {
        const attributes = this.attrs;
        return this.keys.reduce((diff, key) => {
          if (key in this.props && !_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_8___default()(attributes[key], this.props[key])) {
            diff[key] = this.props[key];
          }
          return diff;
        }, {});
      }
      get attrs() {
        return selectedAttributes || this.props.attributes || {};
      }
      render() {
        return wp.element.createElement(WrappedComponent, this.props);
      }
    }
    _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(WithSaveData, "defaultProps", {
      attributes: {},
      setInitialState: lodash_noop__WEBPACK_IMPORTED_MODULE_5___default.a,
      setAttributes: lodash_noop__WEBPACK_IMPORTED_MODULE_5___default.a,
      name: '',
      isolated: false,
      onBlockCreated: lodash_noop__WEBPACK_IMPORTED_MODULE_5___default.a,
      onBlockRemoved: lodash_noop__WEBPACK_IMPORTED_MODULE_5___default.a
    });
    _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(WithSaveData, "propTypes", {
      setAttributes: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.func,
      setInitialState: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.func,
      attributes: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.object,
      name: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.string,
      isolated: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.bool,
      increaseRegister: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.func,
      decreaseRegister: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.func,
      onBlockCreated: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.func,
      onBlockRemoved: prop_types__WEBPACK_IMPORTED_MODULE_7___default.a.func
    });
    WithSaveData.displayName = `WithSaveData( ${WrappedComponent.displayName || WrappedComponent.name || 'Component '}`; // eslint-disable-line max-len

    return WithSaveData;
  };
});

/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

module.exports = wp.hooks;

/***/ }),

/***/ "g8L8":
/***/ (function(module, exports) {

module.exports = tribe.common.store;

/***/ }),

/***/ "ghtj":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return isTribeEventPostType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return hasPostTypeChannel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return createWPEditorSavingChannel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return createWPEditorNotSavingChannel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return createDates; });
/* harmony import */ var lodash_some__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("XNR4");
/* harmony import */ var lodash_some__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash_some__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("1ZqX");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("RmXt");
/* harmony import */ var redux_saga_effects__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var redux_saga__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__("1fKG");
/* harmony import */ var redux_saga__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(redux_saga__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _moderntribe_common_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__("ZNLL");
/* harmony import */ var _moderntribe_common_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_moderntribe_common_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__("B8vQ");
/* harmony import */ var _moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__);

/* eslint-disable max-len */

/**
 * External Dependencies
 */



/**
 * Internal dependencies
 */



/*
 * Determines if current post is a tribe event
 * @export
 * @returns {Boolean} bool
 */
function* isTribeEventPostType() {
  const postType = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__["call"])([Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["select"])('core/editor'), 'getEditedPostAttribute'], 'type');
  return postType === _moderntribe_common_data__WEBPACK_IMPORTED_MODULE_4__["editor"].EVENT;
}

/**
 * Creates event channel subscribing to WP editor state when post type is loaded.
 * Used as post type is not available upon load in some cases, so some false negatives
 *
 * @returns {Function} Channel
 */
function hasPostTypeChannel() {
  return Object(redux_saga__WEBPACK_IMPORTED_MODULE_3__["eventChannel"])(emit => {
    const wpEditor = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["select"])('core/editor');
    const predicates = [() => !!wpEditor.getEditedPostAttribute('type')];

    // Returns unsubscribe function
    return Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["subscribe"])(() => {
      // Only emit when truthy
      if (lodash_some__WEBPACK_IMPORTED_MODULE_0___default()(predicates, fn => fn())) {
        emit(true); // Emitted value is insignificant here, but cannot be left undefined
      }
    });
  });
}

/**
 * Creates event channel subscribing to WP editor state when saving post
 *
 * @returns {Function} Channel
 */
function createWPEditorSavingChannel() {
  return Object(redux_saga__WEBPACK_IMPORTED_MODULE_3__["eventChannel"])(emit => {
    const wpEditor = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["select"])('core/editor');
    const predicates = [() => wpEditor.isSavingPost() && !wpEditor.isAutosavingPost()];

    // Returns unsubscribe function
    return Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["subscribe"])(() => {
      // Only emit when truthy
      if (lodash_some__WEBPACK_IMPORTED_MODULE_0___default()(predicates, fn => fn())) {
        emit(true); // Emitted value is insignificant here, but cannot be left undefined
      }
    });
  });
}

/**
 * Creates event channel subscribing to WP editor state when not saving post
 *
 * @returns {Function} Channel
 */
function createWPEditorNotSavingChannel() {
  return Object(redux_saga__WEBPACK_IMPORTED_MODULE_3__["eventChannel"])(emit => {
    const wpEditor = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["select"])('core/editor');
    const predicates = [() => !(wpEditor.isSavingPost() && !wpEditor.isAutosavingPost())];

    // Returns unsubscribe function
    return Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__["subscribe"])(() => {
      // Only emit when truthy
      if (lodash_some__WEBPACK_IMPORTED_MODULE_0___default()(predicates, fn => fn())) {
        emit(true); // Emitted value is insignificant here, but cannot be left undefined
      }
    });
  });
}

/**
 * Create date objects used throughout sagas
 *
 * @export
 * @yields
 * @param {string} date datetime string
 * @returns {Object} Object of dates/moments
 */
function* createDates(date) {
  const {
    datepickerFormat
  } = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__["call"])([_moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__["globals"], 'tecDateSettings']);
  const moment = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__["call"])(_moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__["moment"].toMoment, date);
  const currentDate = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__["call"])(_moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__["moment"].toDatabaseDate, moment);
  const dateInput = yield datepickerFormat ? Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__["call"])(_moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__["moment"].toDate, moment, datepickerFormat) : Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__["call"])(_moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__["moment"].toDate, moment);
  const time = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__["call"])(_moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__["moment"].toDatabaseTime, moment);
  const timeInput = yield Object(redux_saga_effects__WEBPACK_IMPORTED_MODULE_2__["call"])(_moderntribe_common_utils__WEBPACK_IMPORTED_MODULE_5__["moment"].toTime, moment);
  return {
    moment,
    date: currentDate,
    dateInput,
    time,
    timeInput
  };
}

/***/ }),

/***/ "h74D":
/***/ (function(module, exports) {

module.exports = tribe.modules.reactRedux;

/***/ }),

/***/ "hImw":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketsInitialState", function() { return setTicketsInitialState; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "resetTicketsBlock", function() { return resetTicketsBlock; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketsHeaderImage", function() { return setTicketsHeaderImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketsIsSelected", function() { return setTicketsIsSelected; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketsIsSettingsOpen", function() { return setTicketsIsSettingsOpen; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketsIsSettingsLoading", function() { return setTicketsIsSettingsLoading; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "openSettings", function() { return openSettings; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "closeSettings", function() { return closeSettings; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketsProvider", function() { return setTicketsProvider; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketsSharedCapacity", function() { return setTicketsSharedCapacity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketsTempSharedCapacity", function() { return setTicketsTempSharedCapacity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "updateUneditableTickets", function() { return updateUneditableTickets; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "fetchTicketsHeaderImage", function() { return fetchTicketsHeaderImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "updateTicketsHeaderImage", function() { return updateTicketsHeaderImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "deleteTicketsHeaderImage", function() { return deleteTicketsHeaderImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTitle", function() { return setTicketTitle; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketDescription", function() { return setTicketDescription; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketPrice", function() { return setTicketPrice; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketOnSale", function() { return setTicketOnSale; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketSku", function() { return setTicketSku; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketIACSetting", function() { return setTicketIACSetting; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketStartDate", function() { return setTicketStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketStartDateInput", function() { return setTicketStartDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketStartDateMoment", function() { return setTicketStartDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketEndDate", function() { return setTicketEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketEndDateInput", function() { return setTicketEndDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketEndDateMoment", function() { return setTicketEndDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketStartTime", function() { return setTicketStartTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketEndTime", function() { return setTicketEndTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketStartTimeInput", function() { return setTicketStartTimeInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketEndTimeInput", function() { return setTicketEndTimeInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketCapacityType", function() { return setTicketCapacityType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketCapacity", function() { return setTicketCapacity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketType", function() { return setTicketType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempTitle", function() { return setTicketTempTitle; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempDescription", function() { return setTicketTempDescription; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempPrice", function() { return setTicketTempPrice; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setSalePriceChecked", function() { return setSalePriceChecked; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setSalePrice", function() { return setSalePrice; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketSaleStartDate", function() { return setTicketSaleStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketSaleStartDateInput", function() { return setTicketSaleStartDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketSaleStartDateMoment", function() { return setTicketSaleStartDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketSaleEndDate", function() { return setTicketSaleEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketSaleEndDateInput", function() { return setTicketSaleEndDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketSaleEndDateMoment", function() { return setTicketSaleEndDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTempSalePriceChecked", function() { return setTempSalePriceChecked; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTempSalePrice", function() { return setTempSalePrice; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempSaleStartDate", function() { return setTicketTempSaleStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempSaleStartDateInput", function() { return setTicketTempSaleStartDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempSaleStartDateMoment", function() { return setTicketTempSaleStartDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempSaleEndDate", function() { return setTicketTempSaleEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempSaleEndDateInput", function() { return setTicketTempSaleEndDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempSaleEndDateMoment", function() { return setTicketTempSaleEndDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempSku", function() { return setTicketTempSku; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempIACSetting", function() { return setTicketTempIACSetting; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempStartDate", function() { return setTicketTempStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempStartDateInput", function() { return setTicketTempStartDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempStartDateMoment", function() { return setTicketTempStartDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempEndDate", function() { return setTicketTempEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempEndDateInput", function() { return setTicketTempEndDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempEndDateMoment", function() { return setTicketTempEndDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempStartTime", function() { return setTicketTempStartTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempEndTime", function() { return setTicketTempEndTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempStartTimeInput", function() { return setTicketTempStartTimeInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempEndTimeInput", function() { return setTicketTempEndTimeInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempCapacityType", function() { return setTicketTempCapacityType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempCapacity", function() { return setTicketTempCapacity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "registerTicketBlock", function() { return registerTicketBlock; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "removeTicketBlock", function() { return removeTicketBlock; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "removeTicketBlocks", function() { return removeTicketBlocks; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketSold", function() { return setTicketSold; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketAvailable", function() { return setTicketAvailable; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketId", function() { return setTicketId; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketCurrencySymbol", function() { return setTicketCurrencySymbol; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketCurrencyPosition", function() { return setTicketCurrencyPosition; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketProvider", function() { return setTicketProvider; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketHasAttendeeInfoFields", function() { return setTicketHasAttendeeInfoFields; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketAttendeeInfoFields", function() { return setTicketAttendeeInfoFields; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketIsLoading", function() { return setTicketIsLoading; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketIsModalOpen", function() { return setTicketIsModalOpen; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketHasBeenCreated", function() { return setTicketHasBeenCreated; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketHasChanges", function() { return setTicketHasChanges; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketHasDurationError", function() { return setTicketHasDurationError; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketIsSelected", function() { return setTicketIsSelected; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setUneditableTickets", function() { return setUneditableTickets; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setUneditableTicketsLoading", function() { return setUneditableTicketsLoading; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketDetails", function() { return setTicketDetails; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketTempDetails", function() { return setTicketTempDetails; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "handleTicketStartDate", function() { return handleTicketStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "handleTicketEndDate", function() { return handleTicketEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "processTicketSaleStartDate", function() { return processTicketSaleStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "processTicketSaleEndDate", function() { return processTicketSaleEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "handleTicketStartTime", function() { return handleTicketStartTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "handleTicketEndTime", function() { return handleTicketEndTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "fetchTicket", function() { return fetchTicket; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createNewTicket", function() { return createNewTicket; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "updateTicket", function() { return updateTicket; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "deleteTicket", function() { return deleteTicket; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setTicketInitialState", function() { return setTicketInitialState; });
/* harmony import */ var _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("QFGf");
/**
 * Internal dependencies
 */


//
// ─── TICKETS ACTIONS ────────────────────────────────────────────────────────────
//

const setTicketsInitialState = props => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKETS_INITIAL_STATE,
  payload: props
});
const resetTicketsBlock = () => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].RESET_TICKETS_BLOCK
});
const setTicketsHeaderImage = payload => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKETS_HEADER_IMAGE,
  payload
});
const setTicketsIsSelected = isSelected => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKETS_IS_SELECTED,
  payload: {
    isSelected
  }
});
const setTicketsIsSettingsOpen = isSettingsOpen => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKETS_IS_SETTINGS_OPEN,
  payload: {
    isSettingsOpen
  }
});
const setTicketsIsSettingsLoading = isSettingsLoading => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKETS_IS_SETTINGS_LOADING,
  payload: {
    isSettingsLoading
  }
});
const openSettings = () => setTicketsIsSettingsOpen(true);
const closeSettings = () => setTicketsIsSettingsOpen(false);
const setTicketsProvider = provider => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKETS_PROVIDER,
  payload: {
    provider
  }
});
const setTicketsSharedCapacity = sharedCapacity => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKETS_SHARED_CAPACITY,
  payload: {
    sharedCapacity
  }
});
const setTicketsTempSharedCapacity = tempSharedCapacity => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKETS_TEMP_SHARED_CAPACITY,
  payload: {
    tempSharedCapacity
  }
});

//
// ─── TICKETS SAGA ACTIONS ────────────────────────────────────────────────────────────
//

const updateUneditableTickets = () => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].UPDATE_UNEDITABLE_TICKETS
});

//
// ─── HEADER IMAGE SAGA ACTIONS ──────────────────────────────────────────────────
//

const fetchTicketsHeaderImage = id => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].FETCH_TICKETS_HEADER_IMAGE,
  payload: {
    id
  }
});
const updateTicketsHeaderImage = image => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].UPDATE_TICKETS_HEADER_IMAGE,
  payload: {
    image
  }
});
const deleteTicketsHeaderImage = () => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].DELETE_TICKETS_HEADER_IMAGE
});

//
// ─── TICKET DETAILS ACTIONS ─────────────────────────────────────────────────────
//

const setTicketTitle = (clientId, title) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TITLE,
  payload: {
    clientId,
    title
  }
});
const setTicketDescription = (clientId, description) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_DESCRIPTION,
  payload: {
    clientId,
    description
  }
});
const setTicketPrice = (clientId, price) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_PRICE,
  payload: {
    clientId,
    price
  }
});
const setTicketOnSale = (clientId, onSale) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_ON_SALE,
  payload: {
    clientId,
    onSale
  }
});
const setTicketSku = (clientId, sku) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SKU,
  payload: {
    clientId,
    sku
  }
});
const setTicketIACSetting = (clientId, iac) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_IAC_SETTING,
  payload: {
    clientId,
    iac
  }
});
const setTicketStartDate = (clientId, startDate) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_START_DATE,
  payload: {
    clientId,
    startDate
  }
});
const setTicketStartDateInput = (clientId, startDateInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_START_DATE_INPUT,
  payload: {
    clientId,
    startDateInput
  }
});
const setTicketStartDateMoment = (clientId, startDateMoment) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_START_DATE_MOMENT,
  payload: {
    clientId,
    startDateMoment
  }
});
const setTicketEndDate = (clientId, endDate) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_END_DATE,
  payload: {
    clientId,
    endDate
  }
});
const setTicketEndDateInput = (clientId, endDateInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_END_DATE_INPUT,
  payload: {
    clientId,
    endDateInput
  }
});
const setTicketEndDateMoment = (clientId, endDateMoment) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_END_DATE_MOMENT,
  payload: {
    clientId,
    endDateMoment
  }
});
const setTicketStartTime = (clientId, startTime) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_START_TIME,
  payload: {
    clientId,
    startTime
  }
});
const setTicketEndTime = (clientId, endTime) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_END_TIME,
  payload: {
    clientId,
    endTime
  }
});
const setTicketStartTimeInput = (clientId, startTimeInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_START_TIME_INPUT,
  payload: {
    clientId,
    startTimeInput
  }
});
const setTicketEndTimeInput = (clientId, endTimeInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_END_TIME_INPUT,
  payload: {
    clientId,
    endTimeInput
  }
});
const setTicketCapacityType = (clientId, capacityType) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_CAPACITY_TYPE,
  payload: {
    clientId,
    capacityType
  }
});
const setTicketCapacity = (clientId, capacity) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_CAPACITY,
  payload: {
    clientId,
    capacity
  }
});
const setTicketType = (clientId, type) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TYPE,
  payload: {
    clientId,
    type
  }
});

//
// ─── TICKET TEMP DETAILS ACTIONS ────────────────────────────────────────────────
//

const setTicketTempTitle = (clientId, title) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_TITLE,
  payload: {
    clientId,
    title
  }
});
const setTicketTempDescription = (clientId, description) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_DESCRIPTION,
  payload: {
    clientId,
    description
  }
});
const setTicketTempPrice = (clientId, price) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_PRICE,
  payload: {
    clientId,
    price
  }
});

/**
 * Set the sale price checked status for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {boolean} checked Whether the sale price is checked.
 * @returns {{payload: {clientId, checked}, type: string}} The action.
 */
const setSalePriceChecked = (clientId, checked) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SALE_PRICE_CHECK,
  payload: {
    clientId,
    checked
  }
});

/**
 * Set the sale price for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} salePrice The sale price.
 * @returns {{payload: {clientId, salePrice}, type: string}} The action.
 */
const setSalePrice = (clientId, salePrice) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SALE_PRICE,
  payload: {
    clientId,
    salePrice
  }
});

/**
 * Set the sale start date for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} startDate The start date.
 * @returns {{payload: {clientId, startDate}, type: string}} The action.
 */
const setTicketSaleStartDate = (clientId, startDate) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SALE_START_DATE,
  payload: {
    clientId,
    startDate
  }
});

/**
 * Set the sale start date input for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} startDateInput The start date input.
 * @returns {{payload: {clientId, startDateInput}, type: string}} The action.
 */
const setTicketSaleStartDateInput = (clientId, startDateInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SALE_START_DATE_INPUT,
  payload: {
    clientId,
    startDateInput
  }
});

/**
 * Set the sale start date moment for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {Object} startDateMoment The start date moment.
 * @returns {{payload: {clientId, startDateMoment}, type: string}} The action.
 */
const setTicketSaleStartDateMoment = (clientId, startDateMoment) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SALE_START_DATE_MOMENT,
  payload: {
    clientId,
    startDateMoment
  }
});

/**
 * Set the sale end date for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} endDate The end date.
 * @returns {{payload: {clientId, endDate}, type: string}} The action.
 */
const setTicketSaleEndDate = (clientId, endDate) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SALE_END_DATE,
  payload: {
    clientId,
    endDate
  }
});

/**
 * Set the sale end date input for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} endDateInput The end date input.
 * @returns {{payload: {clientId, endDateInput}, type: string}} The action.
 */
const setTicketSaleEndDateInput = (clientId, endDateInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SALE_END_DATE_INPUT,
  payload: {
    clientId,
    endDateInput
  }
});

/**
 * Set the sale end date moment for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {Object} endDateMoment The end date moment.
 * @returns {{payload: {clientId, endDateMoment}, type: string}} The action.
 */
const setTicketSaleEndDateMoment = (clientId, endDateMoment) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SALE_END_DATE_MOMENT,
  payload: {
    clientId,
    endDateMoment
  }
});

/**
 * Set the Temp Sale Price Checked status for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {boolean} checked Whether the sale price is checked.
 * @returns {{payload: {clientId, checked}, type: string}} The action.
 */
const setTempSalePriceChecked = (clientId, checked) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SALE_PRICE_CHECK,
  payload: {
    clientId,
    checked
  }
});

/**
 * Set the Temp Sale Price for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} salePrice The sale price.
 * @returns {{payload: {clientId, salePrice}, type: string}} The action.
 */
const setTempSalePrice = (clientId, salePrice) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SALE_PRICE,
  payload: {
    clientId,
    salePrice
  }
});

/**
 * Set the Temp Sale Start Date for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} startDate The start date.
 * @returns {{payload: {clientId, startDate}, type: string}} The action.
 */
const setTicketTempSaleStartDate = (clientId, startDate) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SALE_START_DATE,
  payload: {
    clientId,
    startDate
  }
});

/**
 * Set the Temp Sale Start Date input for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} startDateInput The start date input.
 * @returns {{payload: {clientId, startDateInput}, type: string}} The action.
 */
const setTicketTempSaleStartDateInput = (clientId, startDateInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SALE_START_DATE_INPUT,
  payload: {
    clientId,
    startDateInput
  }
});

/**
 * Set the Temp Sale Start Date moment for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {Object} startDateMoment The start date moment.
 * @returns {{payload: {clientId, startDateMoment}, type: string}} The action.
 */
const setTicketTempSaleStartDateMoment = (clientId, startDateMoment) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SALE_START_DATE_MOMENT,
  payload: {
    clientId,
    startDateMoment
  }
});

/**
 * Set the Temp Sale End Date for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} endDate The end date.
 * @returns {{payload: {clientId, endDate}, type: string}} The action.
 */
const setTicketTempSaleEndDate = (clientId, endDate) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SALE_END_DATE,
  payload: {
    clientId,
    endDate
  }
});

/**
 * Set the Temp Sale End Date input for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} endDateInput The end date input.
 * @returns {{payload: {clientId, endDateInput}, type: string}} The action.
 */
const setTicketTempSaleEndDateInput = (clientId, endDateInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SALE_END_DATE_INPUT,
  payload: {
    clientId,
    endDateInput
  }
});

/**
 * Set the Temp Sale End Date moment for a ticket.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {Object} endDateMoment The end date moment.
 * @returns {{payload: {clientId, endDateMoment}, type: string}} The action.
 */
const setTicketTempSaleEndDateMoment = (clientId, endDateMoment) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SALE_END_DATE_MOMENT,
  payload: {
    clientId,
    endDateMoment
  }
});
const setTicketTempSku = (clientId, sku) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_SKU,
  payload: {
    clientId,
    sku
  }
});
const setTicketTempIACSetting = (clientId, iac) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_IAC_SETTING,
  payload: {
    clientId,
    iac
  }
});
const setTicketTempStartDate = (clientId, startDate) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_START_DATE,
  payload: {
    clientId,
    startDate
  }
});
const setTicketTempStartDateInput = (clientId, startDateInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_START_DATE_INPUT,
  payload: {
    clientId,
    startDateInput
  }
});
const setTicketTempStartDateMoment = (clientId, startDateMoment) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_START_DATE_MOMENT,
  payload: {
    clientId,
    startDateMoment
  }
});
const setTicketTempEndDate = (clientId, endDate) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_END_DATE,
  payload: {
    clientId,
    endDate
  }
});
const setTicketTempEndDateInput = (clientId, endDateInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_END_DATE_INPUT,
  payload: {
    clientId,
    endDateInput
  }
});
const setTicketTempEndDateMoment = (clientId, endDateMoment) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_END_DATE_MOMENT,
  payload: {
    clientId,
    endDateMoment
  }
});
const setTicketTempStartTime = (clientId, startTime) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_START_TIME,
  payload: {
    clientId,
    startTime
  }
});
const setTicketTempEndTime = (clientId, endTime) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_END_TIME,
  payload: {
    clientId,
    endTime
  }
});
const setTicketTempStartTimeInput = (clientId, startTimeInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_START_TIME_INPUT,
  payload: {
    clientId,
    startTimeInput
  }
});
const setTicketTempEndTimeInput = (clientId, endTimeInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_END_TIME_INPUT,
  payload: {
    clientId,
    endTimeInput
  }
});
const setTicketTempCapacityType = (clientId, capacityType) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_CAPACITY_TYPE,
  payload: {
    clientId,
    capacityType
  }
});
const setTicketTempCapacity = (clientId, capacity) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_CAPACITY,
  payload: {
    clientId,
    capacity
  }
});

//
// ─── TICKET ACTIONS ─────────────────────────────────────────────────────────────
//

const registerTicketBlock = clientId => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].REGISTER_TICKET_BLOCK,
  payload: {
    clientId
  }
});
const removeTicketBlock = clientId => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].REMOVE_TICKET_BLOCK,
  payload: {
    clientId
  }
});
const removeTicketBlocks = () => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].REMOVE_TICKET_BLOCKS
});
const setTicketSold = (clientId, sold) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_SOLD,
  payload: {
    clientId,
    sold
  }
});
const setTicketAvailable = (clientId, available) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_AVAILABLE,
  payload: {
    clientId,
    available
  }
});
const setTicketId = (clientId, ticketId) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_ID,
  payload: {
    clientId,
    ticketId
  }
});
const setTicketCurrencySymbol = (clientId, currencySymbol) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_CURRENCY_SYMBOL,
  payload: {
    clientId,
    currencySymbol
  }
});
const setTicketCurrencyPosition = (clientId, currencyPosition) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_CURRENCY_POSITION,
  payload: {
    clientId,
    currencyPosition
  }
});
const setTicketProvider = (clientId, provider) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_PROVIDER,
  payload: {
    clientId,
    provider
  }
});
const setTicketHasAttendeeInfoFields = (clientId, hasAttendeeInfoFields) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_HAS_ATTENDEE_INFO_FIELDS,
  payload: {
    clientId,
    hasAttendeeInfoFields
  }
});
const setTicketAttendeeInfoFields = (clientId, attendeeInfoFields) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_ATTENDEE_INFO_FIELDS,
  payload: {
    clientId,
    attendeeInfoFields
  }
});
const setTicketIsLoading = (clientId, isLoading) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_IS_LOADING,
  payload: {
    clientId,
    isLoading
  }
});
const setTicketIsModalOpen = (clientId, isModalOpen) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_IS_MODAL_OPEN,
  payload: {
    clientId,
    isModalOpen
  }
});
const setTicketHasBeenCreated = (clientId, hasBeenCreated) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_HAS_BEEN_CREATED,
  payload: {
    clientId,
    hasBeenCreated
  }
});
const setTicketHasChanges = (clientId, hasChanges) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_HAS_CHANGES,
  payload: {
    clientId,
    hasChanges
  }
});
const setTicketHasDurationError = (clientId, hasDurationError) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_HAS_DURATION_ERROR,
  payload: {
    clientId,
    hasDurationError
  }
});
const setTicketIsSelected = (clientId, isSelected) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_IS_SELECTED,
  payload: {
    clientId,
    isSelected
  }
});
const setUneditableTickets = uneditableTickets => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_UNEDITABLE_TICKETS,
  payload: {
    uneditableTickets
  }
});
const setUneditableTicketsLoading = loading => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_UNEDITABLE_TICKETS_LOADING,
  loading
});

//
// ─── TICKET SAGA ACTIONS ────────────────────────────────────────────────────────
//

const setTicketDetails = (clientId, details) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_DETAILS,
  payload: {
    clientId,
    details
  }
});
const setTicketTempDetails = (clientId, tempDetails) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_TEMP_DETAILS,
  payload: {
    clientId,
    tempDetails
  }
});
const handleTicketStartDate = (clientId, date, dayPickerInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].HANDLE_TICKET_START_DATE,
  payload: {
    clientId,
    date,
    dayPickerInput
  }
});
const handleTicketEndDate = (clientId, date, dayPickerInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].HANDLE_TICKET_END_DATE,
  payload: {
    clientId,
    date,
    dayPickerInput
  }
});

/**
 * Process the ticket sale start date.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} date The date.
 * @param {string} dayPickerInput The day picker input.
 * @returns {{payload: {date, dayPickerInput, clientId}, type: string}} The action.
 */
const processTicketSaleStartDate = (clientId, date, dayPickerInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].HANDLE_TICKET_SALE_START_DATE,
  payload: {
    clientId,
    date,
    dayPickerInput
  }
});

/**
 * Process the ticket sale end date.
 *
 * @since 5.9.0
 * @param {string} clientId The client ID of the ticket.
 * @param {string} date The date.
 * @param {string} dayPickerInput The day picker input.
 * @returns {{payload: {date, dayPickerInput, clientId}, type: string}} The action.
 */
const processTicketSaleEndDate = (clientId, date, dayPickerInput) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].HANDLE_TICKET_SALE_END_DATE,
  payload: {
    clientId,
    date,
    dayPickerInput
  }
});
const handleTicketStartTime = (clientId, seconds) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].HANDLE_TICKET_START_TIME,
  payload: {
    clientId,
    seconds
  }
});
const handleTicketEndTime = (clientId, seconds) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].HANDLE_TICKET_END_TIME,
  payload: {
    clientId,
    seconds
  }
});
const fetchTicket = (clientId, ticketId) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].FETCH_TICKET,
  payload: {
    clientId,
    ticketId
  }
});
const createNewTicket = clientId => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].CREATE_NEW_TICKET,
  payload: {
    clientId
  }
});
const updateTicket = clientId => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].UPDATE_TICKET,
  payload: {
    clientId
  }
});
const deleteTicket = (clientId, askForDeletion) => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].DELETE_TICKET,
  payload: {
    clientId,
    askForDeletion
  }
});
const setTicketInitialState = props => ({
  type: _moderntribe_tickets_data_blocks_ticket__WEBPACK_IMPORTED_MODULE_0__[/* types */ "g"].SET_TICKET_INITIAL_STATE,
  payload: props
});

/***/ }),

/***/ "i9sy":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "ihba":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export getMove */
/* unused harmony export _getUI */
/* unused harmony export _getPostTypes */
/* unused harmony export _getPosts */
/* unused harmony export _getModal */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "l", function() { return isModalShowing; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "j", function() { return isFetchingPostTypes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "k", function() { return isFetchingPosts; });
/* unused harmony export getPostTypes */
/* unused harmony export getPosts */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "h", function() { return getPostTypeOptions; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return getPostOptions; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return getModalPostType; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return getModalSearch; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return getModalTarget; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return getModalTicketId; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return getModalClientId; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "m", function() { return isModalSubmitting; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "g", function() { return getPostTypeOptionValue; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "i", function() { return hasSelectedPost; });
/* harmony import */ var lodash_find__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("6OzC");
/* harmony import */ var lodash_find__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash_find__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var reselect__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("MWqi");
/* harmony import */ var reselect__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(reselect__WEBPACK_IMPORTED_MODULE_1__);

/**
 * External dependencies
 */

const getMove = state => state.tickets.move;
const _getUI = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(getMove, move => move.ui);
const _getPostTypes = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(getMove, move => move.postTypes);
const _getPosts = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(getMove, move => move.posts);
const _getModal = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(getMove, move => move.modal);
const isModalShowing = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getUI, ui => ui.showModal);
const isFetchingPostTypes = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getPostTypes, postTypes => postTypes.isFetching);
const isFetchingPosts = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getPosts, posts => posts.isFetching);
const getPostTypes = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getPostTypes, postTypes => postTypes.posts);
const getPosts = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getPosts, posts => posts.posts);
const getPostTypeOptions = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(getPostTypes, types => Object.keys(types).map(type => ({
  value: type,
  label: types[type]
})));
const getPostOptions = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(getPosts, posts => Object.keys(posts).map(post => ({
  value: post,
  label: posts[post]
})));
const getModalPostType = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getModal, modal => modal.post_type);
const getModalSearch = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getModal, modal => modal.search_terms);
const getModalTarget = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getModal, modal => modal.target_post_id);
const getModalTicketId = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getModal, modal => modal.ticketId);
const getModalClientId = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getModal, modal => modal.clientId);
const isModalSubmitting = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])(_getModal, modal => modal.isSubmitting);
const getPostTypeOptionValue = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])([getPostTypeOptions, getModalPostType], (postTypeOptions, postType) => lodash_find__WEBPACK_IMPORTED_MODULE_0___default()(postTypeOptions, ['value', postType]));
const hasSelectedPost = Object(reselect__WEBPACK_IMPORTED_MODULE_1__["createSelector"])([getPostOptions, getModalTarget], (posts, target) => !!(target && lodash_find__WEBPACK_IMPORTED_MODULE_0___default()(posts, ['value', target])));

/***/ }),

/***/ "jHzm":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "ActionButton", function() { return /* reexport */ action_button; });
__webpack_require__.d(__webpack_exports__, "ActionDashboard", function() { return /* reexport */ action_dashboard; });
__webpack_require__.d(__webpack_exports__, "AttendeesRegistration", function() { return /* reexport */ attendees_registration; });
__webpack_require__.d(__webpack_exports__, "Card", function() { return /* reexport */ card; });
__webpack_require__.d(__webpack_exports__, "SplitContainer", function() { return /* reexport */ split_container; });
__webpack_require__.d(__webpack_exports__, "ContainerPanel", function() { return /* reexport */ container_panel["b" /* default */]; });
__webpack_require__.d(__webpack_exports__, "DateTimeRangePicker", function() { return /* reexport */ date_time_range_picker; });
__webpack_require__.d(__webpack_exports__, "IconWithTooltip", function() { return /* reexport */ icon_with_tooltip; });
__webpack_require__.d(__webpack_exports__, "LabelWithTooltip", function() { return /* reexport */ label_with_tooltip; });
__webpack_require__.d(__webpack_exports__, "NumericLabel", function() { return /* reexport */ numeric_label; });
__webpack_require__.d(__webpack_exports__, "InactiveBlock", function() { return /* reexport */ inactive_block; });
__webpack_require__.d(__webpack_exports__, "SettingsDashboard", function() { return /* reexport */ settings_dashboard; });
__webpack_require__.d(__webpack_exports__, "WarningButton", function() { return /* reexport */ warning_button; });
__webpack_require__.d(__webpack_exports__, "Notice", function() { return /* reexport */ notice; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/extends.js
var helpers_extends = __webpack_require__("pVnL");
var extends_default = /*#__PURE__*/__webpack_require__.n(helpers_extends);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/defineProperty.js
var defineProperty = __webpack_require__("lSNA");
var defineProperty_default = /*#__PURE__*/__webpack_require__.n(defineProperty);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/objectWithoutProperties.js
var objectWithoutProperties = __webpack_require__("QILm");
var objectWithoutProperties_default = /*#__PURE__*/__webpack_require__.n(objectWithoutProperties);

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__("cDcd");
var external_React_default = /*#__PURE__*/__webpack_require__.n(external_React_);

// EXTERNAL MODULE: external "tribe.modules.propTypes"
var external_tribe_modules_propTypes_ = __webpack_require__("rf6O");
var external_tribe_modules_propTypes_default = /*#__PURE__*/__webpack_require__.n(external_tribe_modules_propTypes_);

// EXTERNAL MODULE: external "tribe.modules.classnames"
var external_tribe_modules_classnames_ = __webpack_require__("K2gz");
var external_tribe_modules_classnames_default = /*#__PURE__*/__webpack_require__.n(external_tribe_modules_classnames_);

// EXTERNAL MODULE: external "tribe.common.elements"
var external_tribe_common_elements_ = __webpack_require__("6Ugf");

// EXTERNAL MODULE: ./src/modules/elements/action-button/style.pcss
var style = __webpack_require__("unXf");

// CONCATENATED MODULE: ./src/modules/elements/action-button/element.js



const _excluded = ["asLink", "children", "className", "disabled", "href", "icon", "onClick", "position", "target"];
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */


const positions = {
  right: 'right',
  left: 'left'
};
const components = {
  button: external_tribe_common_elements_["Button"],
  link: external_tribe_common_elements_["Link"]
};
const ActionButton = _ref => {
  let {
      asLink,
      children,
      className,
      disabled,
      href,
      icon,
      onClick,
      position,
      target
    } = _ref,
    props = objectWithoutProperties_default()(_ref, _excluded);
  const containerClass = external_tribe_modules_classnames_default()('tribe-editor__action-button', `tribe-editor__action-button--icon-${position}`, className);
  const Element = asLink && !disabled ? components.link : components.button;
  const getProps = () => {
    const elemProps = _objectSpread({}, props);
    if (asLink && !disabled) {
      elemProps.onMouseDown = () => {
        window.open(href, target);
      };
    } else {
      elemProps.disabled = disabled;
      elemProps.onMouseDown = onClick;
    }
    return elemProps;
  };
  return wp.element.createElement(Element, extends_default()({
    className: containerClass
  }, getProps()), icon, wp.element.createElement("span", {
    className: "tribe-editor__action-button__label"
  }, children));
};
ActionButton.propTypes = {
  asLink: external_tribe_modules_propTypes_default.a.bool,
  children: external_tribe_modules_propTypes_default.a.node,
  className: external_tribe_modules_propTypes_default.a.string,
  disabled: external_tribe_modules_propTypes_default.a.bool,
  href: external_tribe_modules_propTypes_default.a.string,
  icon: external_tribe_modules_propTypes_default.a.node.isRequired,
  onClick: external_tribe_modules_propTypes_default.a.func,
  position: external_tribe_modules_propTypes_default.a.oneOf(Object.keys(positions)),
  target: external_tribe_modules_propTypes_default.a.string
};
ActionButton.defaultProps = {
  asLink: false,
  position: positions.left
};
/* harmony default export */ var action_button_element = (ActionButton);
// CONCATENATED MODULE: ./src/modules/elements/action-button/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var action_button = (action_button_element);
// EXTERNAL MODULE: external "lodash.noop"
var external_lodash_noop_ = __webpack_require__("In0u");
var external_lodash_noop_default = /*#__PURE__*/__webpack_require__.n(external_lodash_noop_);

// EXTERNAL MODULE: ./src/modules/elements/action-dashboard/style.pcss
var action_dashboard_style = __webpack_require__("P9XJ");

// CONCATENATED MODULE: ./src/modules/elements/action-dashboard/element.js

/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


const ActionDashboard = _ref => {
  let {
    actions,
    cancelLabel,
    className,
    confirmLabel,
    isCancelDisabled,
    isConfirmDisabled,
    onCancelClick,
    onConfirmClick,
    showCancel,
    showConfirm
  } = _ref;
  const actionsList = actions && !!actions.length && wp.element.createElement("div", {
    className: "tribe-editor__action-dashboard__group-left"
  }, actions.map((action, index) => wp.element.createElement("span", {
    key: `action-${index}`,
    className: "tribe-editor__action-dashboard__action-wrapper"
  }, action)));
  const cancelButton = showCancel && wp.element.createElement(external_tribe_common_elements_["Button"], {
    className: "tribe-editor__action-dashboard__cancel-button",
    isDisabled: isCancelDisabled,
    onClick: onCancelClick
  }, cancelLabel);

  /* eslint-disable max-len */
  const confirmButton = showConfirm && wp.element.createElement(external_tribe_common_elements_["Button"], {
    className: "tribe-editor__action-dashboard__confirm-button tribe-editor__button--sm tribe-common-c-btn",
    isDisabled: isConfirmDisabled,
    onMouseDown: onConfirmClick
  }, confirmLabel);
  /* eslint-enable max-len */

  const groupRight = (showCancel || showConfirm) && wp.element.createElement("div", {
    className: "tribe-editor__action-dashboard__group-right"
  }, cancelButton, confirmButton);
  return wp.element.createElement("section", {
    className: external_tribe_modules_classnames_default()('tribe-editor__action-dashboard', {
      'tribe-editor__action-dashboard__no-top-bottom-paddings': !actionsList && !groupRight
    }, className)
  }, actionsList, groupRight);
};
ActionDashboard.defaultProps = {
  showCancel: true,
  showConfirm: true,
  onCancelClick: external_lodash_noop_default.a,
  onConfirmClick: external_lodash_noop_default.a
};
ActionDashboard.propTypes = {
  actions: external_tribe_modules_propTypes_default.a.arrayOf(external_tribe_modules_propTypes_default.a.node),
  cancelLabel: external_tribe_modules_propTypes_default.a.string,
  className: external_tribe_modules_propTypes_default.a.string,
  confirmLabel: external_tribe_modules_propTypes_default.a.string,
  isCancelDisabled: external_tribe_modules_propTypes_default.a.bool,
  isConfirmDisabled: external_tribe_modules_propTypes_default.a.bool,
  onCancelClick: external_tribe_modules_propTypes_default.a.func,
  onConfirmClick: external_tribe_modules_propTypes_default.a.func,
  showCancel: external_tribe_modules_propTypes_default.a.bool,
  showConfirm: external_tribe_modules_propTypes_default.a.bool
};
/* harmony default export */ var action_dashboard_element = (ActionDashboard);
// CONCATENATED MODULE: ./src/modules/elements/action-dashboard/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var action_dashboard = (action_dashboard_element);
// EXTERNAL MODULE: external "wp.components"
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external "wp.i18n"
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: ./src/modules/elements/attendees-registration/style.pcss
var attendees_registration_style = __webpack_require__("ph4m");

// CONCATENATED MODULE: ./src/modules/elements/attendees-registration/element.js



const element_excluded = ["helperText", "iframeURL", "isDisabled", "isModalOpen", "label", "linkText", "modalTitle", "onClick", "onClose", "onIframeLoad", "showHelperText"];
/**
 * External dependencies
 */



/**
 * Wordpress dependencies
 */



/**
 * Internal dependencies
 */


class element_AttendeesRegistration extends external_React_["PureComponent"] {
  constructor(props) {
    super(props);
    this.iframe = /*#__PURE__*/Object(external_React_["createRef"])();
  }
  render() {
    const _this$props = this.props,
      {
        helperText,
        iframeURL,
        isDisabled,
        isModalOpen,
        label,
        linkText,
        modalTitle,
        onClick,
        onClose,
        onIframeLoad,
        showHelperText
      } = _this$props,
      restProps = objectWithoutProperties_default()(_this$props, element_excluded);
    const modalContent = wp.element.createElement("div", {
      className: "tribe-editor__attendee-registration__modal-content"
    }, wp.element.createElement("iframe", {
      className: "tribe-editor__attendee-registration__modal-iframe",
      onLoad: () => onIframeLoad(this.iframe.current),
      ref: this.iframe,
      src: iframeURL,
      title: Object(external_wp_i18n_["__"])('Attendee registration', 'event-tickets')
    }), wp.element.createElement("div", {
      className: "tribe-editor__attendee-registration__modal-overlay"
    }, wp.element.createElement(external_wp_components_["Spinner"], null)));
    return wp.element.createElement("div", {
      className: "tribe-editor__attendee-registration"
    }, wp.element.createElement(external_tribe_common_elements_["LabelWithModal"], extends_default()({
      className: "tribe-editor__attendee-registration__label-with-modal",
      isOpen: isModalOpen,
      label: label,
      modalButtonDisabled: isDisabled,
      modalButtonLabel: linkText,
      modalClassName: "tribe-editor__attendee-registration__modal",
      modalContent: modalContent,
      modalTitle: modalTitle,
      onClick: onClick,
      onClose: onClose
    }, restProps)), showHelperText && wp.element.createElement("span", {
      className: "tribe-editor__attendee-registration__helper-text"
    }, helperText));
  }
}
defineProperty_default()(element_AttendeesRegistration, "propTypes", {
  helperText: external_tribe_modules_propTypes_default.a.string.isRequired,
  iframeURL: external_tribe_modules_propTypes_default.a.string.isRequired,
  isDisabled: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isModalOpen: external_tribe_modules_propTypes_default.a.bool.isRequired,
  label: external_tribe_modules_propTypes_default.a.string.isRequired,
  linkText: external_tribe_modules_propTypes_default.a.string.isRequired,
  modalTitle: external_tribe_modules_propTypes_default.a.string.isRequired,
  onClick: external_tribe_modules_propTypes_default.a.func.isRequired,
  onClose: external_tribe_modules_propTypes_default.a.func.isRequired,
  onIframeLoad: external_tribe_modules_propTypes_default.a.func.isRequired,
  showHelperText: external_tribe_modules_propTypes_default.a.bool.isRequired
});
/* harmony default export */ var attendees_registration_element = (element_AttendeesRegistration);
// CONCATENATED MODULE: ./src/modules/elements/attendees-registration/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var attendees_registration = (attendees_registration_element);
// EXTERNAL MODULE: ./src/modules/elements/card/style.pcss
var card_style = __webpack_require__("68xo");

// CONCATENATED MODULE: ./src/modules/elements/card/element.js
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */

const getHeaderElement = (header, description) => {
  if (!description) {
    return wp.element.createElement("div", {
      className: "tickets-heading tickets-row-line"
    }, header);
  }
  return wp.element.createElement("div", {
    className: "tickets-heading__wrapper tickets-row-line"
  }, wp.element.createElement("div", {
    className: "tickets-heading tickets-heading__title"
  }, header), wp.element.createElement("div", {
    className: "tickets-heading__description"
  }, description));
};
const Card = _ref => {
  let {
    className,
    children,
    header,
    description
  } = _ref;
  return wp.element.createElement("div", {
    className: external_tribe_modules_classnames_default()('tribe-editor__card', className)
  }, header && getHeaderElement(header, description), children);
};
Card.propTypes = {
  className: external_tribe_modules_propTypes_default.a.string,
  children: external_tribe_modules_propTypes_default.a.node,
  header: external_tribe_modules_propTypes_default.a.string,
  description: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var card_element = (Card);
// CONCATENATED MODULE: ./src/modules/elements/card/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var card = (card_element);
// EXTERNAL MODULE: ./src/modules/elements/split-container/style.pcss
var split_container_style = __webpack_require__("9mgW");

// CONCATENATED MODULE: ./src/modules/elements/split-container/element.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */

const SplitContainer = _ref => {
  let {
    leftColumn,
    rightColumn
  } = _ref;
  return wp.element.createElement(external_React_default.a.Fragment, null, wp.element.createElement("div", {
    className: "tribe-editor__rsvp-details-wrapper"
  }, wp.element.createElement("div", {
    className: "tribe-editor__rsvp-details"
  }, leftColumn)), wp.element.createElement("div", {
    className: "tribe-editor__rsvp-actions-wrapper"
  }, wp.element.createElement("div", {
    className: "tribe-editor__rsvp-actions"
  }, wp.element.createElement("div", {
    className: "tribe-editor__rsvp-actions-rsvp"
  }, wp.element.createElement("div", {
    className: "tribe-editor__rsvp-actions-rsvp-create"
  }, rightColumn)))));
};
SplitContainer.propTypes = {
  leftColumn: external_tribe_modules_propTypes_default.a.node,
  rightColumn: external_tribe_modules_propTypes_default.a.node
};
/* harmony default export */ var split_container_element = (SplitContainer);
// CONCATENATED MODULE: ./src/modules/elements/split-container/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var split_container = (split_container_element);
// EXTERNAL MODULE: ./src/modules/elements/container-panel/index.js + 1 modules
var container_panel = __webpack_require__("2LK8");

// EXTERNAL MODULE: ./node_modules/react-day-picker/moment/index.js
var moment = __webpack_require__("1rrs");

// EXTERNAL MODULE: external "tribe.common.utils"
var external_tribe_common_utils_ = __webpack_require__("B8vQ");

// EXTERNAL MODULE: ./src/modules/elements/date-time-range-picker/style.pcss
var date_time_range_picker_style = __webpack_require__("kd3S");

// CONCATENATED MODULE: ./src/modules/elements/date-time-range-picker/element.js


/**
 * External dependencies
 */





/**
 * Internal dependencies
 */



class element_DateTimeRangePicker extends external_React_["Component"] {
  constructor(_props) {
    super(_props);
    defineProperty_default()(this, "getFromDayPickerInputProps", () => {
      const {
        fromDate,
        fromDateInput,
        fromDateDisabled,
        fromDateFormat,
        onFromDateChange,
        shiftFocus,
        toDate
      } = this.props;
      const props = {
        value: fromDateInput,
        format: fromDateFormat,
        formatDate: moment["formatDate"],
        parseDate: moment["parseDate"],
        dayPickerProps: {
          selectedDays: [fromDate, {
            from: fromDate,
            to: toDate
          }],
          disabledDays: {
            after: toDate
          },
          modifiers: {
            start: fromDate,
            end: toDate
          },
          toMonth: toDate
        },
        onDayChange: onFromDateChange,
        inputProps: {
          disabled: fromDateDisabled
        }
      };

      /**
       * If shiftFocus is true, selection of date on fromDayPickerInput
       * automatically focuses on toDayPickerInput
       */
      if (shiftFocus) {
        props.dayPickerProps.onDayClick = () => this.toDayPickerInput.current.focus();
      }
      return props;
    });
    defineProperty_default()(this, "getToDayPickerInputProps", () => {
      const {
        fromDate,
        onToDateChange,
        shiftFocus,
        toDate,
        toDateInput,
        toDateDisabled,
        toDateFormat
      } = this.props;
      const props = {
        value: toDateInput,
        format: toDateFormat,
        formatDate: moment["formatDate"],
        parseDate: moment["parseDate"],
        dayPickerProps: {
          selectedDays: [fromDate, {
            from: fromDate,
            to: toDate
          }],
          disabledDays: {
            before: fromDate
          },
          modifiers: {
            start: fromDate,
            end: toDate
          },
          month: fromDate,
          fromMonth: fromDate
        },
        onDayChange: onToDateChange,
        inputProps: {
          disabled: toDateDisabled
        }
      };

      /**
       * If shiftFocus is true, selection of date on fromDayPickerInput
       * automatically focuses on toDayPickerInput
       */
      if (shiftFocus) {
        props.ref = this.toDayPickerInput;
      }
      return props;
    });
    defineProperty_default()(this, "getFromTimePickerProps", () => {
      const {
        fromTime,
        fromTimeDisabled,
        onFromTimePickerBlur,
        onFromTimePickerChange,
        onFromTimePickerClick,
        onFromTimePickerFocus
      } = this.props;
      const props = {
        current: fromTime,
        start: external_tribe_common_utils_["time"].START_OF_DAY,
        end: external_tribe_common_utils_["time"].END_OF_DAY,
        onBlur: onFromTimePickerBlur,
        onChange: onFromTimePickerChange,
        onClick: onFromTimePickerClick,
        onFocus: onFromTimePickerFocus,
        timeFormat: external_tribe_common_utils_["date"].FORMATS.WP.time,
        disabled: fromTimeDisabled
      };
      return props;
    });
    defineProperty_default()(this, "getToTimePickerProps", () => {
      const {
        onToTimePickerBlur,
        onToTimePickerChange,
        onToTimePickerClick,
        onToTimePickerFocus,
        toTime,
        toTimeDisabled
      } = this.props;
      const props = {
        current: toTime,
        start: external_tribe_common_utils_["time"].START_OF_DAY,
        end: external_tribe_common_utils_["time"].END_OF_DAY,
        onBlur: onToTimePickerBlur,
        onChange: onToTimePickerChange,
        onClick: onToTimePickerClick,
        onFocus: onToTimePickerFocus,
        timeFormat: external_tribe_common_utils_["date"].FORMATS.WP.time,
        disabled: toTimeDisabled
      };
      return props;
    });
    this.toDayPickerInput = /*#__PURE__*/Object(external_React_["createRef"])();
  }
  render() {
    const {
      className,
      separatorDateTime,
      separatorTimeRange
    } = this.props;
    return wp.element.createElement("div", {
      className: external_tribe_modules_classnames_default()('tribe-editor__date-time-range-picker', className)
    }, wp.element.createElement("div", {
      className: "tribe-editor__date-time-range-picker__start"
    }, wp.element.createElement(external_tribe_common_elements_["DayPickerInput"], this.getFromDayPickerInputProps()), wp.element.createElement("span", {
      className: external_tribe_modules_classnames_default()('tribe-editor__date-time-range-picker__separator', 'tribe-editor__date-time-range-picker__separator--date-time')
    }, separatorDateTime), wp.element.createElement(external_tribe_common_elements_["TimePicker"], this.getFromTimePickerProps())), wp.element.createElement("div", {
      className: "tribe-editor__date-time-range-picker__end"
    }, wp.element.createElement("span", {
      className: external_tribe_modules_classnames_default()('tribe-editor__date-time-range-picker__separator', 'tribe-editor__date-time-range-picker__separator--time-range')
    }, separatorTimeRange), wp.element.createElement(external_tribe_common_elements_["DayPickerInput"], this.getToDayPickerInputProps()), wp.element.createElement("span", {
      className: external_tribe_modules_classnames_default()('tribe-editor__date-time-range-picker__separator', 'tribe-editor__date-time-range-picker__separator--date-time')
    }, separatorDateTime), wp.element.createElement(external_tribe_common_elements_["TimePicker"], this.getToTimePickerProps())));
  }
}
defineProperty_default()(element_DateTimeRangePicker, "defaultProps", {
  fromDateFormat: 'LL',
  onFromDateChange: external_lodash_noop_default.a,
  onToDateChange: external_lodash_noop_default.a,
  separatorDateTime: 'at',
  separatorTimeRange: 'to',
  toDateFormat: 'LL'
});
defineProperty_default()(element_DateTimeRangePicker, "propTypes", {
  className: external_tribe_modules_propTypes_default.a.string,
  fromDate: external_tribe_modules_propTypes_default.a.instanceOf(Date),
  fromDateInput: external_tribe_modules_propTypes_default.a.string,
  fromDateDisabled: external_tribe_modules_propTypes_default.a.bool,
  fromDateFormat: external_tribe_modules_propTypes_default.a.string,
  fromTime: external_tribe_modules_propTypes_default.a.string,
  fromTimeDisabled: external_tribe_modules_propTypes_default.a.bool,
  onFromDateChange: external_tribe_modules_propTypes_default.a.func,
  onFromTimePickerBlur: external_tribe_modules_propTypes_default.a.func,
  onFromTimePickerChange: external_tribe_modules_propTypes_default.a.func,
  onFromTimePickerClick: external_tribe_modules_propTypes_default.a.func,
  onFromTimePickerFocus: external_tribe_modules_propTypes_default.a.func,
  onToDateChange: external_tribe_modules_propTypes_default.a.func,
  onToTimePickerBlur: external_tribe_modules_propTypes_default.a.func,
  onToTimePickerChange: external_tribe_modules_propTypes_default.a.func,
  onToTimePickerClick: external_tribe_modules_propTypes_default.a.func,
  onToTimePickerFocus: external_tribe_modules_propTypes_default.a.func,
  separatorDateTime: external_tribe_modules_propTypes_default.a.string,
  separatorTimeRange: external_tribe_modules_propTypes_default.a.string,
  shiftFocus: external_tribe_modules_propTypes_default.a.bool,
  toDate: external_tribe_modules_propTypes_default.a.instanceOf(Date),
  toDateInput: external_tribe_modules_propTypes_default.a.string,
  toDateDisabled: external_tribe_modules_propTypes_default.a.bool,
  toDateFormat: external_tribe_modules_propTypes_default.a.string,
  toTime: external_tribe_modules_propTypes_default.a.string,
  toTimeDisabled: external_tribe_modules_propTypes_default.a.bool
});
/* harmony default export */ var date_time_range_picker_element = (element_DateTimeRangePicker);
// CONCATENATED MODULE: ./src/modules/elements/date-time-range-picker/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var date_time_range_picker = (date_time_range_picker_element);
// CONCATENATED MODULE: ./src/modules/elements/icon-with-tooltip/element.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */

const IconWithTooltip = _ref => {
  let {
    description,
    icon,
    position,
    propertyName
  } = _ref;
  const text = wp.element.createElement("div", null, propertyName, description && ': ', description && wp.element.createElement("em", null, description));
  return wp.element.createElement(external_wp_components_["Tooltip"], {
    text: text,
    position: position
  }, wp.element.createElement("span", null, icon));
};
IconWithTooltip.defaultProps = {
  description: '',
  position: 'top right'
};
IconWithTooltip.propTypes = {
  description: external_tribe_modules_propTypes_default.a.string,
  icon: external_tribe_modules_propTypes_default.a.node,
  position: external_tribe_modules_propTypes_default.a.oneOf(['top left', 'top center', 'top right', 'bottom left', 'bottom center', 'bottom right']),
  propertyName: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var icon_with_tooltip_element = (IconWithTooltip);
// CONCATENATED MODULE: ./src/modules/elements/icon-with-tooltip/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var icon_with_tooltip = (icon_with_tooltip_element);
// EXTERNAL MODULE: ./src/modules/elements/label-with-tooltip/style.pcss
var label_with_tooltip_style = __webpack_require__("s3Q2");

// CONCATENATED MODULE: ./src/modules/elements/label-with-tooltip/element.js
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */




/* eslint-disable max-len */
const LabelWithTooltip = _ref => {
  let {
    className,
    forId,
    isLabel,
    label,
    tooltipDisabled,
    tooltipLabel,
    tooltipPosition,
    tooltipText
  } = _ref;
  return wp.element.createElement(external_tribe_common_elements_["LabeledItem"], {
    className: external_tribe_modules_classnames_default()('tribe-editor__label-with-tooltip', className),
    forId: forId,
    isLabel: isLabel,
    label: label
  }, wp.element.createElement(external_wp_components_["Tooltip"], {
    text: tooltipText,
    position: tooltipPosition
  }, wp.element.createElement("button", {
    "aria-label": tooltipText,
    className: external_tribe_modules_classnames_default()('tribe-editor__tooltip-label', 'tribe-editor__label-with-tooltip__tooltip-label'),
    disabled: tooltipDisabled
  }, tooltipLabel)));
};
/* eslint-enable max-len */

LabelWithTooltip.defaultProps = {
  label: '',
  tooltipPosition: 'top right'
};
LabelWithTooltip.propTypes = {
  className: external_tribe_modules_propTypes_default.a.string,
  forId: external_tribe_modules_propTypes_default.a.string,
  isLabel: external_tribe_modules_propTypes_default.a.bool,
  label: external_tribe_modules_propTypes_default.a.node,
  tooltipDisabled: external_tribe_modules_propTypes_default.a.bool,
  tooltipLabel: external_tribe_modules_propTypes_default.a.node,
  tooltipPosition: external_tribe_modules_propTypes_default.a.oneOf(['top left', 'top center', 'top right', 'bottom left', 'bottom center', 'bottom right']),
  tooltipText: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var label_with_tooltip_element = (LabelWithTooltip);
// CONCATENATED MODULE: ./src/modules/elements/label-with-tooltip/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var label_with_tooltip = (label_with_tooltip_element);
// CONCATENATED MODULE: ./src/modules/elements/numeric-label/element.js
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */

/**
 * Generate a label with singular, plural values based on the count provided, the function
 * returns a fallback value (`undefined`) by default when the value is either 0 or lower.
 *
 * Labels need to have a %d on it where the number will be replaced
 *
 * @param {Object} props The props passed to this component
 * @param {string | Array | Object} props.className The class of the element
 * @param {number} props.count The amount to be compared
 * @param {boolean} props.includeZero If true, zero is included in count
 * @param {string} props.singular The label for the singular case
 * @param {string} props.plural The label for the plural case
 * @param {*} props.fallback The value to be returned if count is zero or negative
 * @param {boolean} props.useFallback If true, fallback is used.
 * @returns {*} return fallback if count is zero or negative otherwise singular or plural
 */
const NumericLabel = _ref => {
  let {
    className,
    count,
    includeZero,
    singular,
    plural,
    fallback,
    useFallback
  } = _ref;
  if (useFallback && (includeZero && !(count >= 0) || !includeZero && !(count > 0))) {
    return fallback;
  }
  const targetStr = count === 1 ? singular : plural;
  const [before, after] = targetStr.split('%d');
  return wp.element.createElement("span", {
    className: external_tribe_modules_classnames_default()('tribe-editor__numeric-label', className)
  }, before && wp.element.createElement("span", {
    className: "tribe-editor__numeric-label--before"
  }, before), wp.element.createElement("span", {
    className: "tribe-editor__numeric-label--count"
  }, count), after && wp.element.createElement("span", {
    className: "tribe-editor__numeric-label--after"
  }, after));
};
NumericLabel.propTypes = {
  className: external_tribe_modules_propTypes_default.a.oneOfType([external_tribe_modules_propTypes_default.a.string, external_tribe_modules_propTypes_default.a.arrayOf(external_tribe_modules_propTypes_default.a.string), external_tribe_modules_propTypes_default.a.object]),
  count: external_tribe_modules_propTypes_default.a.number.isRequired,
  includeZero: external_tribe_modules_propTypes_default.a.bool,
  singular: external_tribe_modules_propTypes_default.a.string,
  plural: external_tribe_modules_propTypes_default.a.string,
  useFallback: external_tribe_modules_propTypes_default.a.any,
  fallback: external_tribe_modules_propTypes_default.a.any
};
NumericLabel.defaultProps = {
  count: 0,
  includeZero: false,
  singular: '',
  plural: '',
  className: '',
  fallback: null,
  useFallback: true
};
/* harmony default export */ var numeric_label_element = (NumericLabel);
// CONCATENATED MODULE: ./src/modules/elements/numeric-label/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var numeric_label = (numeric_label_element);
// EXTERNAL MODULE: ./src/modules/elements/inactive-block/style.pcss
var inactive_block_style = __webpack_require__("trUg");

// CONCATENATED MODULE: ./src/modules/elements/inactive-block/element.js
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */

const LAYOUT = {
  rsvp: 'rsvp',
  ticket: 'ticket'
};
const InactiveBlock = _ref => {
  let {
    className,
    description,
    icon,
    layout,
    title
  } = _ref;
  return wp.element.createElement("section", {
    className: external_tribe_modules_classnames_default()('tribe-editor__inactive-block', `tribe-editor__inactive-block--${layout}`, className)
  }, wp.element.createElement("div", {
    className: "tribe-editor__inactive-block__icon"
  }, icon), (title || description) && wp.element.createElement("div", {
    className: "tribe-editor__inactive-block__content"
  }, title && wp.element.createElement("h2", {
    className: "tribe-editor__inactive-block__title"
  }, title), description && wp.element.createElement("p", {
    className: "tribe-editor__inactive-block__description"
  }, description)));
};
InactiveBlock.propTypes = {
  className: external_tribe_modules_propTypes_default.a.string,
  description: external_tribe_modules_propTypes_default.a.string,
  icon: external_tribe_modules_propTypes_default.a.node,
  layout: external_tribe_modules_propTypes_default.a.oneOf(Object.keys(LAYOUT)).isRequired,
  title: external_tribe_modules_propTypes_default.a.string
};
/* harmony default export */ var inactive_block_element = (InactiveBlock);
// CONCATENATED MODULE: ./src/modules/elements/inactive-block/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var inactive_block = (inactive_block_element);

// EXTERNAL MODULE: ./src/modules/icons/index.js + 18 modules
var icons = __webpack_require__("NxMS");

// EXTERNAL MODULE: ./src/modules/elements/settings-dashboard/style.pcss
var settings_dashboard_style = __webpack_require__("oe2g");

// CONCATENATED MODULE: ./src/modules/elements/settings-dashboard/element.js

/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const SettingsDashboard = _ref => {
  let {
    className,
    closeButtonDisabled,
    closeButtonLabel,
    content,
    headerLeft,
    onCloseClick
  } = _ref;
  return wp.element.createElement(card, {
    className: external_tribe_modules_classnames_default()('tribe-editor__settings-dashboard', className)
  }, wp.element.createElement("header", {
    className: "tribe-editor__settings-dashboard__header"
  }, wp.element.createElement("span", {
    className: "tribe-editor__settings-dashboard__header-left"
  }, headerLeft), wp.element.createElement(external_tribe_common_elements_["Button"], {
    className: "tribe-editor__settings-dashboard__close-button",
    onClick: onCloseClick,
    disabled: closeButtonDisabled
  }, closeButtonLabel)), wp.element.createElement("div", {
    className: "tribe-editor__settings-dashboard__content"
  }, content));
};
SettingsDashboard.defaultProps = {
  closeButtonLabel: wp.element.createElement(external_React_["Fragment"], null, wp.element.createElement(icons["Close"], null), wp.element.createElement("span", {
    className: "tribe-editor__settings-dashboard__close-button-text"
  }, Object(external_wp_i18n_["__"])('close', 'event-tickets'))),
  headerLeft: wp.element.createElement(external_React_["Fragment"], null, wp.element.createElement(icons["Settings"], null), wp.element.createElement("span", {
    className: "tribe-editor__settings-dashboard__header-left-text"
  }, Object(external_wp_i18n_["__"])('Ticket Settings', 'event-tickets'))),
  onCloseClick: external_lodash_noop_default.a
};
SettingsDashboard.propTypes = {
  className: external_tribe_modules_propTypes_default.a.string,
  closeButtonDisabled: external_tribe_modules_propTypes_default.a.bool,
  closeButtonLabel: external_tribe_modules_propTypes_default.a.node,
  headerLeft: external_tribe_modules_propTypes_default.a.node,
  onCloseClick: external_tribe_modules_propTypes_default.a.func,
  content: external_tribe_modules_propTypes_default.a.node
};
/* harmony default export */ var settings_dashboard_element = (SettingsDashboard);
// CONCATENATED MODULE: ./src/modules/elements/settings-dashboard/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var settings_dashboard = (settings_dashboard_element);
// EXTERNAL MODULE: ./src/modules/elements/warning-button/style.pcss
var warning_button_style = __webpack_require__("1HDl");

// CONCATENATED MODULE: ./src/modules/elements/warning-button/element.js


const warning_button_element_excluded = ["children", "className", "icon"];
/**
 * External dependencies
 */




/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const WarningButton = _ref => {
  let {
      children,
      className,
      icon
    } = _ref,
    props = objectWithoutProperties_default()(_ref, warning_button_element_excluded);
  return wp.element.createElement(external_tribe_common_elements_["Button"], extends_default()({
    className: external_tribe_modules_classnames_default()('tribe-editor__warning-button', className)
  }, props), wp.element.createElement(external_wp_components_["Dashicon"], {
    className: "tribe-editor__warning-button-icon",
    icon: icon
  }), wp.element.createElement("span", {
    className: "tribe-editor__warning-button-text"
  }, children));
};
WarningButton.propTypes = {
  children: external_tribe_modules_propTypes_default.a.node,
  className: external_tribe_modules_propTypes_default.a.string,
  icon: external_tribe_modules_propTypes_default.a.string.isRequired
};
/* harmony default export */ var warning_button_element = (WarningButton);
// CONCATENATED MODULE: ./src/modules/elements/warning-button/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var warning_button = (warning_button_element);
// EXTERNAL MODULE: ./src/modules/elements/notice/style.pcss
var notice_style = __webpack_require__("t/7v");

// CONCATENATED MODULE: ./src/modules/elements/notice/element.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


const Notice = _ref => {
  let {
    description
  } = _ref;
  return wp.element.createElement("div", {
    className: "tribe-editor__notice"
  }, wp.element.createElement(icons["Bulb"], null), description);
};
Notice.propTypes = {
  description: external_tribe_modules_propTypes_default.a.node
};
/* harmony default export */ var notice_element = (Notice);
// CONCATENATED MODULE: ./src/modules/elements/notice/index.js
/**
 * Internal dependencies
 */

/* harmony default export */ var notice = (notice_element);
// CONCATENATED MODULE: ./src/modules/elements/index.js















/***/ }),

/***/ "jSdM":
/***/ (function(module, exports) {

module.exports = wp.editor;

/***/ }),

/***/ "k3Up":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "kd3S":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

module.exports = wp.i18n;

/***/ }),

/***/ "mzTd":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* reexport */ container; });

// EXTERNAL MODULE: external "tribe.modules.reactRedux"
var external_tribe_modules_reactRedux_ = __webpack_require__("h74D");

// EXTERNAL MODULE: external "tribe.modules.redux"
var external_tribe_modules_redux_ = __webpack_require__("rKB8");

// EXTERNAL MODULE: external "tribe.common.hoc"
var external_tribe_common_hoc_ = __webpack_require__("Q9xL");

// EXTERNAL MODULE: ./src/modules/data/shared/move/selectors.js
var selectors = __webpack_require__("ihba");

// EXTERNAL MODULE: ./src/modules/data/shared/move/types.js
var types = __webpack_require__("4Uf/");

// EXTERNAL MODULE: ./src/modules/data/shared/move/actions.js
var actions = __webpack_require__("U/eK");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/defineProperty.js
var defineProperty = __webpack_require__("lSNA");
var defineProperty_default = /*#__PURE__*/__webpack_require__.n(defineProperty);

// EXTERNAL MODULE: external "wp.components"
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external "wp.i18n"
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: external "tribe.common.elements"
var external_tribe_common_elements_ = __webpack_require__("6Ugf");

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__("cDcd");

// EXTERNAL MODULE: external "tribe.modules.propTypes"
var external_tribe_modules_propTypes_ = __webpack_require__("rf6O");
var external_tribe_modules_propTypes_default = /*#__PURE__*/__webpack_require__.n(external_tribe_modules_propTypes_);

// EXTERNAL MODULE: ./src/modules/elements/move-modal/style.pcss
var style = __webpack_require__("aAk0");

// CONCATENATED MODULE: ./src/modules/elements/move-modal/template.js

/**
 * External Dependencies
 */






class template_MoveModal extends external_React_["PureComponent"] {
  constructor() {
    super(...arguments);
    defineProperty_default()(this, "renderPostTypes", () => {
      if (this.props.isFetchingPosts) {
        return wp.element.createElement(external_wp_components_["Spinner"], null);
      }
      return this.props.postOptions.length ? wp.element.createElement(external_wp_components_["MenuGroup"], null, wp.element.createElement(external_wp_components_["MenuItemsChoice"], {
        choices: this.props.postOptions,
        value: this.props.postValue,
        onSelect: this.props.onPostSelect
      })) : wp.element.createElement(external_wp_components_["Notice"], {
        isDismissible: false,
        status: "warning"
      }, Object(external_wp_i18n_["__"])('No posts found', 'event-tickets'));
    });
  }
  componentDidMount() {
    this.props.initialize();
  }
  render() {
    return wp.element.createElement(external_wp_components_["Modal"], {
      title: this.props.title,
      onRequestClose: this.props.hideModal,
      className: "tribe-editor__tickets__move-modal"
    }, wp.element.createElement("label", {
      htmlFor: "post_type"
    }, Object(external_wp_i18n_["__"])('You can optionally focus on a specific post type:', 'event-tickets')), wp.element.createElement(external_tribe_common_elements_["Select"], {
      id: "post_type",
      options: this.props.postTypeOptions,
      onChange: this.props.onPostTypeChange,
      value: this.props.postTypeOptionValue
    }), wp.element.createElement("label", {
      htmlFor: "search"
    }, Object(external_wp_i18n_["__"])('You can also enter keywords to help find the target event by title or description', 'event-tickets')), wp.element.createElement(external_tribe_common_elements_["Input"], {
      id: "search",
      type: "text",
      onChange: this.props.onSearchChange,
      value: this.props.search
    }), wp.element.createElement("label", null, Object(external_wp_i18n_["__"])('Select the post you wish to move the ticket type to:', 'event-tickets')), this.renderPostTypes(), wp.element.createElement("footer", null, wp.element.createElement(external_wp_components_["Button"], {
      isLarge: true,
      isPrimary: true,
      isBusy: this.props.isModalSubmitting,
      disabled: !this.props.hasSelectedPost || this.props.isFetchingPosts,
      onClick: this.props.onSubmit
    }, Object(external_wp_i18n_["__"])('Finish!', 'event-tickets'))));
  }
}
defineProperty_default()(template_MoveModal, "propTypes", {
  hasSelectedPost: external_tribe_modules_propTypes_default.a.bool.isRequired,
  hideModal: external_tribe_modules_propTypes_default.a.func.isRequired,
  initialize: external_tribe_modules_propTypes_default.a.func.isRequired,
  isFetchingPosts: external_tribe_modules_propTypes_default.a.bool.isRequired,
  isModalSubmitting: external_tribe_modules_propTypes_default.a.bool.isRequired,
  onPostSelect: external_tribe_modules_propTypes_default.a.func.isRequired,
  onPostTypeChange: external_tribe_modules_propTypes_default.a.func.isRequired,
  onSearchChange: external_tribe_modules_propTypes_default.a.func.isRequired,
  onSubmit: external_tribe_modules_propTypes_default.a.func.isRequired,
  postOptions: external_tribe_modules_propTypes_default.a.arrayOf(external_tribe_modules_propTypes_default.a.object),
  postTypeOptions: external_tribe_modules_propTypes_default.a.arrayOf(external_tribe_modules_propTypes_default.a.object),
  postTypeOptionValue: external_tribe_modules_propTypes_default.a.object,
  postValue: external_tribe_modules_propTypes_default.a.string.isRequired,
  search: external_tribe_modules_propTypes_default.a.string.isRequired,
  title: external_tribe_modules_propTypes_default.a.string.isRequired
});
defineProperty_default()(template_MoveModal, "defaultProps", {
  title: Object(external_wp_i18n_["__"])('Move Ticket Types', 'event-tickets')
});
// CONCATENATED MODULE: ./src/modules/elements/move-modal/container.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */





const mapStateToProps = state => ({
  hasSelectedPost: selectors["i" /* hasSelectedPost */](state),
  isFetchingPosts: selectors["k" /* isFetchingPosts */](state),
  isFetchingPostTypes: selectors["j" /* isFetchingPostTypes */](state),
  isModalSubmitting: selectors["m" /* isModalSubmitting */](state),
  postOptions: selectors["f" /* getPostOptions */](state),
  postTypeOptions: selectors["h" /* getPostTypeOptions */](state),
  postTypeOptionValue: selectors["g" /* getPostTypeOptionValue */](state),
  postValue: selectors["d" /* getModalTarget */](state),
  search: selectors["c" /* getModalSearch */](state)
});
const mapDispatchToProps = dispatch => ({
  initialize: () => dispatch({
    type: types["h" /* INITIALIZE_MODAL */]
  }),
  hideModal: () => dispatch(Object(actions["a" /* hideModal */])()),
  onSearchChange: e => dispatch(Object(actions["b" /* setModalData */])({
    search_terms: e.target.value
  })),
  onPostTypeChange: option => dispatch(Object(actions["b" /* setModalData */])({
    post_type: option.value
  })),
  onPostSelect: value => dispatch(Object(actions["b" /* setModalData */])({
    target_post_id: value
  })),
  onSubmit: () => dispatch({
    type: types["o" /* SUBMIT_MODAL */]
  })
});
/* harmony default export */ var container = (Object(external_tribe_modules_redux_["compose"])(Object(external_tribe_common_hoc_["withStore"])(), Object(external_tribe_modules_reactRedux_["connect"])(mapStateToProps, mapDispatchToProps))(template_MoveModal));
// CONCATENATED MODULE: ./src/modules/elements/move-modal/index.js


/***/ }),

/***/ "n+fr":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "f", function() { return /* reexport */ types_namespaceObject; });
__webpack_require__.d(__webpack_exports__, "a", function() { return /* reexport */ actions; });
__webpack_require__.d(__webpack_exports__, "c", function() { return /* reexport */ watchers; });
__webpack_require__.d(__webpack_exports__, "d", function() { return /* reexport */ selectors_namespaceObject; });
__webpack_require__.d(__webpack_exports__, "e", function() { return /* reexport */ thunks_namespaceObject; });

// NAMESPACE OBJECT: ./src/modules/data/blocks/rsvp/types.js
var types_namespaceObject = {};
__webpack_require__.r(types_namespaceObject);
__webpack_require__.d(types_namespaceObject, "SET_RSVP_ID", function() { return SET_RSVP_ID; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_SETTINGS_OPEN", function() { return SET_RSVP_SETTINGS_OPEN; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_HAS_CHANGES", function() { return SET_RSVP_HAS_CHANGES; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_IS_LOADING", function() { return SET_RSVP_IS_LOADING; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_IS_SETTINGS_LOADING", function() { return SET_RSVP_IS_SETTINGS_LOADING; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_IS_MODAL_OPEN", function() { return SET_RSVP_IS_MODAL_OPEN; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_GOING_COUNT", function() { return SET_RSVP_GOING_COUNT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_NOT_GOING_COUNT", function() { return SET_RSVP_NOT_GOING_COUNT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_HAS_ATTENDEE_INFO_FIELDS", function() { return SET_RSVP_HAS_ATTENDEE_INFO_FIELDS; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_HAS_DURATION_ERROR", function() { return SET_RSVP_HAS_DURATION_ERROR; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_IS_ADD_EDIT_OPEN", function() { return SET_RSVP_IS_ADD_EDIT_OPEN; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_DETAILS", function() { return SET_RSVP_DETAILS; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_DETAILS", function() { return SET_RSVP_TEMP_DETAILS; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_HEADER_IMAGE", function() { return SET_RSVP_HEADER_IMAGE; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TITLE", function() { return SET_RSVP_TITLE; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_DESCRIPTION", function() { return SET_RSVP_DESCRIPTION; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_CAPACITY", function() { return SET_RSVP_CAPACITY; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_NOT_GOING_RESPONSES", function() { return SET_RSVP_NOT_GOING_RESPONSES; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_START_DATE", function() { return SET_RSVP_START_DATE; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_START_DATE_INPUT", function() { return SET_RSVP_START_DATE_INPUT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_START_DATE_MOMENT", function() { return SET_RSVP_START_DATE_MOMENT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_END_DATE", function() { return SET_RSVP_END_DATE; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_END_DATE_INPUT", function() { return SET_RSVP_END_DATE_INPUT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_END_DATE_MOMENT", function() { return SET_RSVP_END_DATE_MOMENT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_START_TIME", function() { return SET_RSVP_START_TIME; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_END_TIME", function() { return SET_RSVP_END_TIME; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_START_TIME_INPUT", function() { return SET_RSVP_START_TIME_INPUT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_END_TIME_INPUT", function() { return SET_RSVP_END_TIME_INPUT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_TITLE", function() { return SET_RSVP_TEMP_TITLE; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_DESCRIPTION", function() { return SET_RSVP_TEMP_DESCRIPTION; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_CAPACITY", function() { return SET_RSVP_TEMP_CAPACITY; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_NOT_GOING_RESPONSES", function() { return SET_RSVP_TEMP_NOT_GOING_RESPONSES; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_START_DATE", function() { return SET_RSVP_TEMP_START_DATE; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_START_DATE_INPUT", function() { return SET_RSVP_TEMP_START_DATE_INPUT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_START_DATE_MOMENT", function() { return SET_RSVP_TEMP_START_DATE_MOMENT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_END_DATE", function() { return SET_RSVP_TEMP_END_DATE; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_END_DATE_INPUT", function() { return SET_RSVP_TEMP_END_DATE_INPUT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_END_DATE_MOMENT", function() { return SET_RSVP_TEMP_END_DATE_MOMENT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_START_TIME", function() { return SET_RSVP_TEMP_START_TIME; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_END_TIME", function() { return SET_RSVP_TEMP_END_TIME; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_START_TIME_INPUT", function() { return SET_RSVP_TEMP_START_TIME_INPUT; });
__webpack_require__.d(types_namespaceObject, "SET_RSVP_TEMP_END_TIME_INPUT", function() { return SET_RSVP_TEMP_END_TIME_INPUT; });
__webpack_require__.d(types_namespaceObject, "CREATE_RSVP", function() { return CREATE_RSVP; });
__webpack_require__.d(types_namespaceObject, "INITIALIZE_RSVP", function() { return INITIALIZE_RSVP; });
__webpack_require__.d(types_namespaceObject, "DELETE_RSVP", function() { return DELETE_RSVP; });
__webpack_require__.d(types_namespaceObject, "HANDLE_RSVP_START_DATE", function() { return HANDLE_RSVP_START_DATE; });
__webpack_require__.d(types_namespaceObject, "HANDLE_RSVP_END_DATE", function() { return HANDLE_RSVP_END_DATE; });
__webpack_require__.d(types_namespaceObject, "HANDLE_RSVP_START_TIME", function() { return HANDLE_RSVP_START_TIME; });
__webpack_require__.d(types_namespaceObject, "HANDLE_RSVP_END_TIME", function() { return HANDLE_RSVP_END_TIME; });
__webpack_require__.d(types_namespaceObject, "FETCH_RSVP_HEADER_IMAGE", function() { return FETCH_RSVP_HEADER_IMAGE; });
__webpack_require__.d(types_namespaceObject, "UPDATE_RSVP_HEADER_IMAGE", function() { return UPDATE_RSVP_HEADER_IMAGE; });
__webpack_require__.d(types_namespaceObject, "DELETE_RSVP_HEADER_IMAGE", function() { return DELETE_RSVP_HEADER_IMAGE; });

// NAMESPACE OBJECT: ./src/modules/data/blocks/rsvp/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getRSVPBlock", function() { return getRSVPBlock; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPId", function() { return getRSVPId; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPCreated", function() { return getRSVPCreated; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPSettingsOpen", function() { return getRSVPSettingsOpen; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPIsAddEditOpen", function() { return getRSVPIsAddEditOpen; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPHasChanges", function() { return getRSVPHasChanges; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPIsLoading", function() { return getRSVPIsLoading; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPIsSettingsLoading", function() { return getRSVPIsSettingsLoading; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPIsModalOpen", function() { return getRSVPIsModalOpen; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPGoingCount", function() { return getRSVPGoingCount; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPNotGoingCount", function() { return getRSVPNotGoingCount; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPHasAttendeeInfoFields", function() { return getRSVPHasAttendeeInfoFields; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPHasDurationError", function() { return getRSVPHasDurationError; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPDetails", function() { return getRSVPDetails; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTitle", function() { return getRSVPTitle; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPDescription", function() { return getRSVPDescription; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPCapacity", function() { return getRSVPCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPAvailable", function() { return getRSVPAvailable; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPNotGoingResponses", function() { return getRSVPNotGoingResponses; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPStartDate", function() { return getRSVPStartDate; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPStartDateInput", function() { return getRSVPStartDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPStartDateMoment", function() { return getRSVPStartDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPStartTime", function() { return getRSVPStartTime; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPStartTimeNoSeconds", function() { return getRSVPStartTimeNoSeconds; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPEndDate", function() { return getRSVPEndDate; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPEndDateInput", function() { return getRSVPEndDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPEndDateMoment", function() { return getRSVPEndDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPEndTime", function() { return getRSVPEndTime; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPEndTimeNoSeconds", function() { return getRSVPEndTimeNoSeconds; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPStartTimeInput", function() { return getRSVPStartTimeInput; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPEndTimeInput", function() { return getRSVPEndTimeInput; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempDetails", function() { return getRSVPTempDetails; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempTitle", function() { return getRSVPTempTitle; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempDescription", function() { return getRSVPTempDescription; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempCapacity", function() { return getRSVPTempCapacity; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempNotGoingResponses", function() { return getRSVPTempNotGoingResponses; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempStartDate", function() { return getRSVPTempStartDate; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempStartDateInput", function() { return getRSVPTempStartDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempStartDateMoment", function() { return getRSVPTempStartDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempStartTime", function() { return getRSVPTempStartTime; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempStartTimeNoSeconds", function() { return getRSVPTempStartTimeNoSeconds; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempEndDate", function() { return getRSVPTempEndDate; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempEndDateInput", function() { return getRSVPTempEndDateInput; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempEndDateMoment", function() { return getRSVPTempEndDateMoment; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempEndTime", function() { return getRSVPTempEndTime; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempEndTimeNoSeconds", function() { return getRSVPTempEndTimeNoSeconds; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempStartTimeInput", function() { return getRSVPTempStartTimeInput; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPTempEndTimeInput", function() { return getRSVPTempEndTimeInput; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPHeaderImage", function() { return getRSVPHeaderImage; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPHeaderImageId", function() { return getRSVPHeaderImageId; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPHeaderImageSrc", function() { return getRSVPHeaderImageSrc; });
__webpack_require__.d(selectors_namespaceObject, "getRSVPHeaderImageAlt", function() { return getRSVPHeaderImageAlt; });

// NAMESPACE OBJECT: ./src/modules/data/blocks/rsvp/thunks.js
var thunks_namespaceObject = {};
__webpack_require__.r(thunks_namespaceObject);
__webpack_require__.d(thunks_namespaceObject, "createRSVP", function() { return createRSVP; });
__webpack_require__.d(thunks_namespaceObject, "updateRSVP", function() { return updateRSVP; });
__webpack_require__.d(thunks_namespaceObject, "deleteRSVP", function() { return deleteRSVP; });
__webpack_require__.d(thunks_namespaceObject, "getRSVP", function() { return getRSVP; });

// EXTERNAL MODULE: ./src/modules/data/utils.js
var utils = __webpack_require__("BNqv");

// CONCATENATED MODULE: ./src/modules/data/blocks/rsvp/types.js
/* eslint-disable max-len */
/**
 * Internal dependencies
 */


//
// ─── RSVP TYPES ─────────────────────────────────────────────────────────────────
//

const SET_RSVP_ID = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_ID`;
const SET_RSVP_SETTINGS_OPEN = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_SETTINGS_OPEN`;
const SET_RSVP_HAS_CHANGES = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_HAS_CHANGES`;
const SET_RSVP_IS_LOADING = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_IS_LOADING`;
const SET_RSVP_IS_SETTINGS_LOADING = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_IS_SETTINGS_LOADING`;
const SET_RSVP_IS_MODAL_OPEN = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_IS_MODAL_OPEN`;
const SET_RSVP_GOING_COUNT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_GOING_COUNT`;
const SET_RSVP_NOT_GOING_COUNT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_NOT_GOING_COUNT`;
const SET_RSVP_HAS_ATTENDEE_INFO_FIELDS = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_HAS_ATTENDEE_INFO_FIELDS`;
const SET_RSVP_HAS_DURATION_ERROR = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_HAS_DURATION_ERROR`;
const SET_RSVP_IS_ADD_EDIT_OPEN = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_IS_ADD_EDIT_OPEN`;
const SET_RSVP_DETAILS = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_DETAILS`;
const SET_RSVP_TEMP_DETAILS = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_DETAILS`;
const SET_RSVP_HEADER_IMAGE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_HEADER_IMAGE`;

//
// ─── RSVP DETAILS TYPES ─────────────────────────────────────────────────────────
//

const SET_RSVP_TITLE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TITLE`;
const SET_RSVP_DESCRIPTION = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_DESCRIPTION`;
const SET_RSVP_CAPACITY = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_CAPACITY`;
const SET_RSVP_NOT_GOING_RESPONSES = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_NOT_GOING_RESPONSES`;
const SET_RSVP_START_DATE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_START_DATE`;
const SET_RSVP_START_DATE_INPUT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_START_DATE_INPUT`;
const SET_RSVP_START_DATE_MOMENT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_START_DATE_MOMENT`;
const SET_RSVP_END_DATE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_END_DATE`;
const SET_RSVP_END_DATE_INPUT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_END_DATE_INPUT`;
const SET_RSVP_END_DATE_MOMENT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_END_DATE_MOMENT`;
const SET_RSVP_START_TIME = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_START_TIME`;
const SET_RSVP_END_TIME = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_END_TIME`;
const SET_RSVP_START_TIME_INPUT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_START_TIME_INPUT`;
const SET_RSVP_END_TIME_INPUT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_END_TIME_INPUT`;

//
// ─── RSVP TEMP DETAILS TYPES ────────────────────────────────────────────────────
//

const SET_RSVP_TEMP_TITLE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_TITLE`;
const SET_RSVP_TEMP_DESCRIPTION = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_DESCRIPTION`;
const SET_RSVP_TEMP_CAPACITY = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_CAPACITY`;
const SET_RSVP_TEMP_NOT_GOING_RESPONSES = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_NOT_GOING_RESPONSES`;
const SET_RSVP_TEMP_START_DATE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_START_DATE`;
const SET_RSVP_TEMP_START_DATE_INPUT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_START_DATE_INPUT`;
const SET_RSVP_TEMP_START_DATE_MOMENT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_START_DATE_MOMENT`;
const SET_RSVP_TEMP_END_DATE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_END_DATE`;
const SET_RSVP_TEMP_END_DATE_INPUT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_END_DATE_INPUT`;
const SET_RSVP_TEMP_END_DATE_MOMENT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_END_DATE_MOMENT`;
const SET_RSVP_TEMP_START_TIME = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_START_TIME`;
const SET_RSVP_TEMP_END_TIME = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_END_TIME`;
const SET_RSVP_TEMP_START_TIME_INPUT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_START_TIME_INPUT`;
const SET_RSVP_TEMP_END_TIME_INPUT = `${utils["n" /* PREFIX_TICKETS_STORE */]}/SET_RSVP_TEMP_END_TIME_INPUT`;

//
// ─── RSVP THUNK & SAGA TYPES ────────────────────────────────────────────────────
//

const CREATE_RSVP = `${utils["n" /* PREFIX_TICKETS_STORE */]}/CREATE_RSVP`;
const INITIALIZE_RSVP = `${utils["n" /* PREFIX_TICKETS_STORE */]}/INITIALIZE_RSVP`;
const DELETE_RSVP = `${utils["n" /* PREFIX_TICKETS_STORE */]}/DELETE_RSVP`;
const HANDLE_RSVP_START_DATE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/HANDLE_RSVP_START_DATE`;
const HANDLE_RSVP_END_DATE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/HANDLE_RSVP_END_DATE`;
const HANDLE_RSVP_START_TIME = `${utils["n" /* PREFIX_TICKETS_STORE */]}/HANDLE_RSVP_START_TIME`;
const HANDLE_RSVP_END_TIME = `${utils["n" /* PREFIX_TICKETS_STORE */]}/HANDLE_RSVP_END_TIME`;
const FETCH_RSVP_HEADER_IMAGE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/FETCH_RSVP_HEADER_IMAGE`;
const UPDATE_RSVP_HEADER_IMAGE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/UPDATE_RSVP_HEADER_IMAGE`;
const DELETE_RSVP_HEADER_IMAGE = `${utils["n" /* PREFIX_TICKETS_STORE */]}/DELETE_RSVP_HEADER_IMAGE`;
// EXTERNAL MODULE: ./src/modules/data/blocks/rsvp/actions.js
var actions = __webpack_require__("qkor");

// EXTERNAL MODULE: external "tribe.modules.reselect"
var external_tribe_modules_reselect_ = __webpack_require__("MWqi");

// CONCATENATED MODULE: ./src/modules/data/blocks/rsvp/selectors.js
/**
 * External dependencies
 */


/**
 * ------------------------------------------------------------
 * RSVP State
 * ------------------------------------------------------------
 */

const getRSVPBlock = state => state.tickets.blocks.rsvp;
const getRSVPId = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.id);
const getRSVPCreated = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.created);
const getRSVPSettingsOpen = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.settingsOpen);
const getRSVPIsAddEditOpen = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.isAddEditOpen);
const getRSVPHasChanges = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.hasChanges);
const getRSVPIsLoading = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.isLoading);
const getRSVPIsSettingsLoading = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.isSettingsLoading);
const getRSVPIsModalOpen = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.isModalOpen);
const getRSVPGoingCount = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.goingCount);
const getRSVPNotGoingCount = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.notGoingCount);
const getRSVPHasAttendeeInfoFields = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.hasAttendeeInfoFields);
const getRSVPHasDurationError = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.hasDurationError);

/**
 * ------------------------------------------------------------
 * RSVP Details
 * ------------------------------------------------------------
 */
const getRSVPDetails = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.details);
const getRSVPTitle = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.title);
const getRSVPDescription = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.description);
const getRSVPCapacity = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.capacity);
const getRSVPAvailable = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPCapacity, getRSVPGoingCount], (capacity, goingCount) => {
  if (capacity === '') {
    return -1;
  }
  const total = parseInt(capacity, 10) || 0;
  const going = parseInt(goingCount, 10) || 0;
  /**
   * Prevent to have negative values when subtracting the going amount from total amount, so it takes the max value
   * of the substraction operation or zero if the operation is lower than zero it will return zero insted.
   */
  return Math.max(total - going, 0);
});
const getRSVPNotGoingResponses = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.notGoingResponses);
const getRSVPStartDate = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.startDate);
const getRSVPStartDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.startDateInput);
const getRSVPStartDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.startDateMoment);
const getRSVPStartTime = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.startTime);
const getRSVPStartTimeNoSeconds = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPStartTime], startTime => startTime.slice(0, -3));
const getRSVPEndDate = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.endDate);
const getRSVPEndDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.endDateInput);
const getRSVPEndDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.endDateMoment);
const getRSVPEndTime = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.endTime);
const getRSVPEndTimeNoSeconds = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPEndTime], endTime => endTime.slice(0, -3));
const getRSVPStartTimeInput = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.startTimeInput);
const getRSVPEndTimeInput = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPDetails], details => details.endTimeInput);

/**
 * ------------------------------------------------------------
 * RSVP Temp Details
 * ------------------------------------------------------------
 */
const getRSVPTempDetails = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.tempDetails);
const getRSVPTempTitle = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.title);
const getRSVPTempDescription = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.description);
const getRSVPTempCapacity = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.capacity);
const getRSVPTempNotGoingResponses = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.notGoingResponses);
const getRSVPTempStartDate = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.startDate);
const getRSVPTempStartDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.startDateInput);
const getRSVPTempStartDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.startDateMoment);
const getRSVPTempStartTime = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.startTime);
const getRSVPTempStartTimeNoSeconds = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempStartTime], startTime => startTime.slice(0, -3));
const getRSVPTempEndDate = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.endDate);
const getRSVPTempEndDateInput = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.endDateInput);
const getRSVPTempEndDateMoment = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.endDateMoment);
const getRSVPTempEndTime = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.endTime);
const getRSVPTempEndTimeNoSeconds = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempEndTime], endTime => endTime.slice(0, -3));
const getRSVPTempStartTimeInput = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.startTimeInput);
const getRSVPTempEndTimeInput = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPTempDetails], tempDetails => tempDetails.endTimeInput);

/**
 * ------------------------------------------------------------
 * RSVP Header Image
 * ------------------------------------------------------------
 */
const getRSVPHeaderImage = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPBlock], rsvp => rsvp.headerImage);
const getRSVPHeaderImageId = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPHeaderImage], headerImage => headerImage.id);
const getRSVPHeaderImageSrc = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPHeaderImage], headerImage => headerImage.src);
const getRSVPHeaderImageAlt = Object(external_tribe_modules_reselect_["createSelector"])([getRSVPHeaderImage], headerImage => headerImage.alt);
// EXTERNAL MODULE: external "tribe.common.store"
var external_tribe_common_store_ = __webpack_require__("g8L8");

// EXTERNAL MODULE: external "tribe.common.utils"
var external_tribe_common_utils_ = __webpack_require__("B8vQ");

// CONCATENATED MODULE: ./src/modules/data/blocks/rsvp/thunks.js
/**
 * Internal dependencies
 */




const {
  request: {
    actions: wpRequestActions
  }
} = external_tribe_common_store_["middlewares"];

/**
 * @todo: until we can abstract out wpRequest() better, these should remain as a thunk
 */
const METHODS = {
  DELETE: 'DELETE',
  GET: 'GET',
  POST: 'POST',
  PUT: 'PUT'
};
const createOrUpdateRSVP = method => payload => dispatch => {
  const {
    title,
    description,
    capacity,
    notGoingResponses,
    startDateMoment,
    startTime,
    endDateMoment,
    endTime
  } = payload;
  const startMoment = startDateMoment.clone().startOf('day').seconds(external_tribe_common_utils_["time"].toSeconds(startTime, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM_SS));
  const endMoment = endDateMoment.clone().startOf('day').seconds(external_tribe_common_utils_["time"].toSeconds(endTime, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM_SS));
  let path = `${utils["o" /* RSVP_POST_TYPE */]}`;
  const reqBody = {
    title,
    excerpt: description,
    meta: {
      [utils["d" /* KEY_TICKET_CAPACITY */]]: capacity,
      [utils["m" /* KEY_TICKET_START_DATE */]]: external_tribe_common_utils_["moment"].toDateTime(startMoment),
      [utils["f" /* KEY_TICKET_END_DATE */]]: external_tribe_common_utils_["moment"].toDateTime(endMoment),
      [utils["l" /* KEY_TICKET_SHOW_NOT_GOING */]]: notGoingResponses
    }
  };
  if (method === METHODS.POST) {
    reqBody.status = 'publish';
    reqBody.meta[utils["b" /* KEY_RSVP_FOR_EVENT */]] = `${payload.postId}`;
    /* This is hardcoded value until we can sort out BE */
    reqBody.meta[utils["k" /* KEY_TICKET_SHOW_DESCRIPTION */]] = 'yes';
    /* This is hardcoded value until we can sort out BE */
    reqBody.meta[utils["a" /* KEY_PRICE */]] = '0';
  } else if (method === METHODS.PUT) {
    path += `/${payload.id}`;
  }
  const options = {
    path,
    params: {
      method,
      body: JSON.stringify(reqBody)
    },
    actions: {
      start: () => dispatch(actions["setRSVPIsLoading"](true)),
      success: _ref => {
        let {
          body
        } = _ref;
        if (method === METHODS.POST) {
          dispatch(actions["createRSVP"]());
          dispatch(actions["setRSVPId"](body.id));
        }
        dispatch(actions["setRSVPDetails"](payload));
        dispatch(actions["setRSVPHasChanges"](false));
        dispatch(actions["setRSVPIsLoading"](false));
      },
      error: () => dispatch(actions["setRSVPIsLoading"](false))
    }
  };
  dispatch(wpRequestActions.wpRequest(options));
};
const createRSVP = createOrUpdateRSVP(METHODS.POST);
const updateRSVP = createOrUpdateRSVP(METHODS.PUT);
const deleteRSVP = id => dispatch => {
  const path = `${utils["o" /* RSVP_POST_TYPE */]}/${id}`;
  const options = {
    path,
    params: {
      method: METHODS.DELETE
    }
  };
  dispatch(wpRequestActions.wpRequest(options));
};
const getRSVP = function (postId) {
  let page = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
  return dispatch => {
    const path = `${utils["o" /* RSVP_POST_TYPE */]}?per_page=100&page=${page}&context=edit`;
    const options = {
      path,
      params: {
        method: METHODS.GET
      },
      actions: {
        start: () => dispatch(actions["setRSVPIsLoading"](true)),
        success: _ref2 => {
          let {
            body,
            headers
          } = _ref2;
          const filteredRSVPs = body.filter(rsvp => rsvp.meta[utils["b" /* KEY_RSVP_FOR_EVENT */]] == postId // eslint-disable-line eqeqeq
          );

          const totalPages = headers.get('x-wp-totalpages');
          if (filteredRSVPs.length) {
            /* If RSVP for event exists in fetched data */
            /**
             * @todo We are currently only fetching the first RSVP.
             *       If an event has more than 1 RSVP set up from
             *       the classic editor, only one will be displayed.
             *       The strategy to handle this is is being worked on.
             */
            const datePickerFormat = external_tribe_common_utils_["globals"].tecDateSettings().datepickerFormat;
            const rsvp = filteredRSVPs[0];
            const {
              meta = {}
            } = rsvp;
            const startMoment = external_tribe_common_utils_["moment"].toMoment(meta[utils["m" /* KEY_TICKET_START_DATE */]]);
            const endMoment = external_tribe_common_utils_["moment"].toMoment(meta[utils["f" /* KEY_TICKET_END_DATE */]]);
            const startDateInput = datePickerFormat ? startMoment.format(external_tribe_common_utils_["moment"].toFormat(datePickerFormat)) : external_tribe_common_utils_["moment"].toDate(startMoment);
            const endDateInput = datePickerFormat ? endMoment.format(external_tribe_common_utils_["moment"].toFormat(datePickerFormat)) : external_tribe_common_utils_["moment"].toDate(endMoment);
            const capacity = meta[utils["d" /* KEY_TICKET_CAPACITY */]] >= 0 ? meta[utils["d" /* KEY_TICKET_CAPACITY */]] : '';
            const notGoingResponses = meta[utils["l" /* KEY_TICKET_SHOW_NOT_GOING */]];
            dispatch(actions["createRSVP"]());
            dispatch(actions["setRSVPId"](rsvp.id));
            dispatch(actions["setRSVPGoingCount"](parseInt(meta[utils["g" /* KEY_TICKET_GOING_COUNT */]], 10) || 0));
            dispatch(actions["setRSVPNotGoingCount"](parseInt(meta[utils["j" /* KEY_TICKET_NOT_GOING_COUNT */]], 10) || 0));
            dispatch(actions["setRSVPHasAttendeeInfoFields"](meta[utils["h" /* KEY_TICKET_HAS_ATTENDEE_INFO_FIELDS */]]));
            dispatch(actions["setRSVPDetails"]({
              title: rsvp.title.raw,
              description: rsvp.excerpt.raw,
              capacity,
              notGoingResponses,
              startDate: external_tribe_common_utils_["moment"].toDate(startMoment),
              startDateInput,
              startDateMoment: startMoment.clone().startOf('day'),
              endDate: external_tribe_common_utils_["moment"].toDate(endMoment),
              endDateInput,
              endDateMoment: endMoment.clone().seconds(0),
              startTime: external_tribe_common_utils_["moment"].toDatabaseTime(startMoment),
              endTime: external_tribe_common_utils_["moment"].toDatabaseTime(endMoment),
              startTimeInput: external_tribe_common_utils_["moment"].toTime(startMoment),
              endTimeInput: external_tribe_common_utils_["moment"].toTime(endMoment)
            }));
            dispatch(actions["setRSVPTempDetails"]({
              tempTitle: rsvp.title.raw,
              tempDescription: rsvp.excerpt.raw,
              tempCapacity: capacity,
              tempNotGoingResponses: notGoingResponses,
              tempStartDate: external_tribe_common_utils_["moment"].toDate(startMoment),
              tempStartDateInput: startDateInput,
              tempStartDateMoment: startMoment.clone().startOf('day'),
              tempEndDate: external_tribe_common_utils_["moment"].toDate(endMoment),
              tempEndDateInput: endDateInput,
              tempEndDateMoment: endMoment.clone().seconds(0),
              tempStartTime: external_tribe_common_utils_["moment"].toDatabaseTime(startMoment),
              tempEndTime: external_tribe_common_utils_["moment"].toDatabaseTime(endMoment),
              tempStartTimeInput: external_tribe_common_utils_["moment"].toTime(startMoment),
              tempEndTimeInput: external_tribe_common_utils_["moment"].toTime(endMoment)
            }));
            dispatch(actions["setRSVPIsLoading"](false));
          } else if (page < totalPages) {
            /* If there are more pages */
            dispatch(getRSVP(postId, page + 1));
          } else {
            /* Did not find RSVP */
            dispatch(actions["setRSVPIsLoading"](false));
          }
        },
        error: () => dispatch(actions["setRSVPIsLoading"](false))
      }
    };
    dispatch(wpRequestActions.wpRequest(options));
  };
};
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/defineProperty.js
var defineProperty = __webpack_require__("lSNA");
var defineProperty_default = /*#__PURE__*/__webpack_require__.n(defineProperty);

// EXTERNAL MODULE: external "moment"
var external_moment_ = __webpack_require__("wy2R");
var external_moment_default = /*#__PURE__*/__webpack_require__.n(external_moment_);

// CONCATENATED MODULE: ./src/modules/data/blocks/rsvp/reducers/details.js

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


const details_datePickerFormat = external_tribe_common_utils_["globals"].tecDateSettings().datepickerFormat;
const currentMoment = external_moment_default()();
const bufferDuration = external_tribe_common_utils_["globals"].tickets().end_sale_buffer_duration ? external_tribe_common_utils_["globals"].tickets().end_sale_buffer_duration : 2;
const bufferYears = external_tribe_common_utils_["globals"].tickets().end_sale_buffer_years ? external_tribe_common_utils_["globals"].tickets().end_sale_buffer_years : 1;
const details_endMoment = currentMoment.clone().add(bufferDuration, 'hours').add(bufferYears, 'years');
const details_startDateInput = details_datePickerFormat ? currentMoment.format(external_tribe_common_utils_["moment"].toFormat(details_datePickerFormat)) : external_tribe_common_utils_["moment"].toDate(currentMoment);
const details_endDateInput = details_datePickerFormat ? details_endMoment.format(external_tribe_common_utils_["moment"].toFormat(details_datePickerFormat)) : external_tribe_common_utils_["moment"].toDate(details_endMoment);
const DEFAULT_STATE = {
  title: '',
  description: '',
  capacity: '',
  notGoingResponses: false,
  startDate: external_tribe_common_utils_["moment"].toDatabaseDate(currentMoment),
  startDateInput: details_startDateInput,
  startDateMoment: currentMoment,
  endDate: external_tribe_common_utils_["moment"].toDatabaseDate(details_endMoment),
  endDateInput: details_endDateInput,
  endDateMoment: details_endMoment,
  startTime: external_tribe_common_utils_["moment"].toDatabaseTime(currentMoment),
  endTime: external_tribe_common_utils_["moment"].toDatabaseTime(details_endMoment),
  startTimeInput: external_tribe_common_utils_["moment"].toTime(currentMoment),
  endTimeInput: external_tribe_common_utils_["moment"].toTime(details_endMoment)
};
/* harmony default export */ var details = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types_namespaceObject.SET_RSVP_TITLE:
      return _objectSpread(_objectSpread({}, state), {}, {
        title: action.payload.title
      });
    case types_namespaceObject.SET_RSVP_DESCRIPTION:
      return _objectSpread(_objectSpread({}, state), {}, {
        description: action.payload.description
      });
    case types_namespaceObject.SET_RSVP_CAPACITY:
      return _objectSpread(_objectSpread({}, state), {}, {
        capacity: action.payload.capacity
      });
    case types_namespaceObject.SET_RSVP_NOT_GOING_RESPONSES:
      return _objectSpread(_objectSpread({}, state), {}, {
        notGoingResponses: action.payload.notGoingResponses
      });
    case types_namespaceObject.SET_RSVP_START_DATE:
      return _objectSpread(_objectSpread({}, state), {}, {
        startDate: action.payload.startDate
      });
    case types_namespaceObject.SET_RSVP_START_DATE_INPUT:
      return _objectSpread(_objectSpread({}, state), {}, {
        startDateInput: action.payload.startDateInput
      });
    case types_namespaceObject.SET_RSVP_START_DATE_MOMENT:
      return _objectSpread(_objectSpread({}, state), {}, {
        startDateMoment: action.payload.startDateMoment
      });
    case types_namespaceObject.SET_RSVP_END_DATE:
      return _objectSpread(_objectSpread({}, state), {}, {
        endDate: action.payload.endDate
      });
    case types_namespaceObject.SET_RSVP_END_DATE_INPUT:
      return _objectSpread(_objectSpread({}, state), {}, {
        endDateInput: action.payload.endDateInput
      });
    case types_namespaceObject.SET_RSVP_END_DATE_MOMENT:
      return _objectSpread(_objectSpread({}, state), {}, {
        endDateMoment: action.payload.endDateMoment
      });
    case types_namespaceObject.SET_RSVP_START_TIME:
      return _objectSpread(_objectSpread({}, state), {}, {
        startTime: action.payload.startTime
      });
    case types_namespaceObject.SET_RSVP_END_TIME:
      return _objectSpread(_objectSpread({}, state), {}, {
        endTime: action.payload.endTime
      });
    case types_namespaceObject.SET_RSVP_START_TIME_INPUT:
      return _objectSpread(_objectSpread({}, state), {}, {
        startTimeInput: action.payload.startTimeInput
      });
    case types_namespaceObject.SET_RSVP_END_TIME_INPUT:
      return _objectSpread(_objectSpread({}, state), {}, {
        endTimeInput: action.payload.endTimeInput
      });
    default:
      return state;
  }
});
// CONCATENATED MODULE: ./src/modules/data/blocks/rsvp/reducers/temp-details.js

function temp_details_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function temp_details_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? temp_details_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : temp_details_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Internal dependencies
 */


/* harmony default export */ var temp_details = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types_namespaceObject.SET_RSVP_TEMP_TITLE:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        title: action.payload.title
      });
    case types_namespaceObject.SET_RSVP_TEMP_DESCRIPTION:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        description: action.payload.description
      });
    case types_namespaceObject.SET_RSVP_TEMP_CAPACITY:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        capacity: action.payload.capacity
      });
    case types_namespaceObject.SET_RSVP_TEMP_NOT_GOING_RESPONSES:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        notGoingResponses: action.payload.notGoingResponses
      });
    case types_namespaceObject.SET_RSVP_TEMP_START_DATE:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startDate: action.payload.startDate
      });
    case types_namespaceObject.SET_RSVP_TEMP_START_DATE_INPUT:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startDateInput: action.payload.startDateInput
      });
    case types_namespaceObject.SET_RSVP_TEMP_START_DATE_MOMENT:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startDateMoment: action.payload.startDateMoment
      });
    case types_namespaceObject.SET_RSVP_TEMP_END_DATE:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endDate: action.payload.endDate
      });
    case types_namespaceObject.SET_RSVP_TEMP_END_DATE_INPUT:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endDateInput: action.payload.endDateInput
      });
    case types_namespaceObject.SET_RSVP_TEMP_END_DATE_MOMENT:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endDateMoment: action.payload.endDateMoment
      });
    case types_namespaceObject.SET_RSVP_TEMP_START_TIME:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startTime: action.payload.startTime
      });
    case types_namespaceObject.SET_RSVP_TEMP_END_TIME:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endTime: action.payload.endTime
      });
    case types_namespaceObject.SET_RSVP_TEMP_START_TIME_INPUT:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        startTimeInput: action.payload.startTimeInput
      });
    case types_namespaceObject.SET_RSVP_TEMP_END_TIME_INPUT:
      return temp_details_objectSpread(temp_details_objectSpread({}, state), {}, {
        endTimeInput: action.payload.endTimeInput
      });
    default:
      return state;
  }
});
// EXTERNAL MODULE: ./src/modules/data/blocks/rsvp/reducers/header-image.js
var header_image = __webpack_require__("56gU");

// CONCATENATED MODULE: ./src/modules/data/blocks/rsvp/reducer.js

function reducer_ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function reducer_objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? reducer_ownKeys(Object(source), !0).forEach(function (key) { defineProperty_default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : reducer_ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Internal dependencies
 */




const reducer_DEFAULT_STATE = {
  id: 0,
  created: false,
  settingsOpen: false,
  hasChanges: false,
  isAddEditOpen: false,
  isLoading: false,
  isSettingsLoading: false,
  isModalOpen: false,
  goingCount: 0,
  notGoingCount: 0,
  hasAttendeeInfoFields: false,
  details: DEFAULT_STATE,
  tempDetails: DEFAULT_STATE,
  headerImage: header_image["a" /* DEFAULT_STATE */]
};
/* harmony default export */ var reducer = (function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : reducer_DEFAULT_STATE;
  let action = arguments.length > 1 ? arguments[1] : undefined;
  switch (action.type) {
    case types_namespaceObject.CREATE_RSVP:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        created: true
      });
    case types_namespaceObject.DELETE_RSVP:
      return reducer_DEFAULT_STATE;
    case types_namespaceObject.SET_RSVP_ID:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        id: action.payload.id
      });
    case types_namespaceObject.SET_RSVP_SETTINGS_OPEN:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        settingsOpen: action.payload.settingsOpen
      });
    case types_namespaceObject.SET_RSVP_IS_ADD_EDIT_OPEN:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        isAddEditOpen: action.payload.isAddEditOpen
      });
    case types_namespaceObject.SET_RSVP_HAS_CHANGES:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        hasChanges: action.payload.hasChanges
      });
    case types_namespaceObject.SET_RSVP_IS_LOADING:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        isLoading: action.payload.isLoading
      });
    case types_namespaceObject.SET_RSVP_IS_SETTINGS_LOADING:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        isSettingsLoading: action.payload.isSettingsLoading
      });
    case types_namespaceObject.SET_RSVP_IS_MODAL_OPEN:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        isModalOpen: action.payload.isModalOpen
      });
    case types_namespaceObject.SET_RSVP_GOING_COUNT:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        goingCount: action.payload.goingCount
      });
    case types_namespaceObject.SET_RSVP_NOT_GOING_COUNT:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        notGoingCount: action.payload.notGoingCount
      });
    case types_namespaceObject.SET_RSVP_HAS_ATTENDEE_INFO_FIELDS:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        hasAttendeeInfoFields: action.payload.hasAttendeeInfoFields
      });
    case types_namespaceObject.SET_RSVP_HAS_DURATION_ERROR:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        hasDurationError: action.payload.hasDurationError
      });
    case types_namespaceObject.SET_RSVP_TITLE:
    case types_namespaceObject.SET_RSVP_DESCRIPTION:
    case types_namespaceObject.SET_RSVP_CAPACITY:
    case types_namespaceObject.SET_RSVP_NOT_GOING_RESPONSES:
    case types_namespaceObject.SET_RSVP_START_DATE:
    case types_namespaceObject.SET_RSVP_START_DATE_INPUT:
    case types_namespaceObject.SET_RSVP_START_DATE_MOMENT:
    case types_namespaceObject.SET_RSVP_END_DATE:
    case types_namespaceObject.SET_RSVP_END_DATE_INPUT:
    case types_namespaceObject.SET_RSVP_END_DATE_MOMENT:
    case types_namespaceObject.SET_RSVP_START_TIME:
    case types_namespaceObject.SET_RSVP_END_TIME:
    case types_namespaceObject.SET_RSVP_START_TIME_INPUT:
    case types_namespaceObject.SET_RSVP_END_TIME_INPUT:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        details: details(state.details, action)
      });
    case types_namespaceObject.SET_RSVP_TEMP_TITLE:
    case types_namespaceObject.SET_RSVP_TEMP_DESCRIPTION:
    case types_namespaceObject.SET_RSVP_TEMP_CAPACITY:
    case types_namespaceObject.SET_RSVP_TEMP_NOT_GOING_RESPONSES:
    case types_namespaceObject.SET_RSVP_TEMP_START_DATE:
    case types_namespaceObject.SET_RSVP_TEMP_START_DATE_INPUT:
    case types_namespaceObject.SET_RSVP_TEMP_START_DATE_MOMENT:
    case types_namespaceObject.SET_RSVP_TEMP_END_DATE:
    case types_namespaceObject.SET_RSVP_TEMP_END_DATE_INPUT:
    case types_namespaceObject.SET_RSVP_TEMP_END_DATE_MOMENT:
    case types_namespaceObject.SET_RSVP_TEMP_START_TIME:
    case types_namespaceObject.SET_RSVP_TEMP_END_TIME:
    case types_namespaceObject.SET_RSVP_TEMP_START_TIME_INPUT:
    case types_namespaceObject.SET_RSVP_TEMP_END_TIME_INPUT:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        tempDetails: temp_details(state.tempDetails, action)
      });
    case types_namespaceObject.SET_RSVP_HEADER_IMAGE:
      return reducer_objectSpread(reducer_objectSpread({}, state), {}, {
        headerImage: Object(header_image["b" /* default */])(state.headerImage, action)
      });
    default:
      return state;
  }
});
// EXTERNAL MODULE: external "wp.data"
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external "tribe.modules.reduxSaga.effects"
var external_tribe_modules_reduxSaga_effects_ = __webpack_require__("RmXt");

// EXTERNAL MODULE: ./src/modules/data/blocks/ticket/actions.js
var ticket_actions = __webpack_require__("hImw");

// EXTERNAL MODULE: ./src/modules/data/blocks/ticket/reducers/header-image.js
var reducers_header_image = __webpack_require__("YEbw");

// EXTERNAL MODULE: ./src/modules/data/shared/move/types.js
var types = __webpack_require__("4Uf/");

// EXTERNAL MODULE: ./src/modules/data/shared/move/selectors.js
var selectors = __webpack_require__("ihba");

// EXTERNAL MODULE: ./src/modules/data/shared/sagas.js
var sagas = __webpack_require__("ghtj");

// CONCATENATED MODULE: ./src/modules/data/blocks/rsvp/sagas.js
/* eslint-disable max-len */

/**
 * External Dependencies
 */



/**
 * Internal dependencies
 */













//
// ─── RSVP DETAILS ───────────────────────────────────────────────────────────────
//

/**
 * Set details for current RSVP
 *
 * @export
 * @yields
 * @param {Object} action redux action
 */
function* setRSVPDetails(action) {
  const {
    title,
    description,
    capacity,
    notGoingResponses,
    startDate,
    startDateInput,
    startDateMoment,
    startTime,
    endDate,
    endDateInput,
    endDateMoment,
    endTime,
    startTimeInput,
    endTimeInput
  } = action.payload;
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTitle"](title)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPDescription"](description)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPCapacity"](capacity)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPNotGoingResponses"](notGoingResponses)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartDate"](startDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartDateInput"](startDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartDateMoment"](startDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartTime"](startTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDate"](endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDateInput"](endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDateMoment"](endDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndTime"](endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartTimeInput"](startTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndTimeInput"](endTimeInput))]);
}

/**
 * Set details for current temp RSVP
 *
 * @export
 * @yields
 * @param {Object} action redux action
 */
function* setRSVPTempDetails(action) {
  const {
    tempTitle,
    tempDescription,
    tempCapacity,
    tempNotGoingResponses,
    tempStartDate,
    tempStartDateInput,
    tempStartDateMoment,
    tempStartTime,
    tempEndDate,
    tempEndDateInput,
    tempEndDateMoment,
    tempEndTime,
    tempStartTimeInput,
    tempEndTimeInput
  } = action.payload;
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempTitle"](tempTitle)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempDescription"](tempDescription)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempCapacity"](tempCapacity)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempNotGoingResponses"](tempNotGoingResponses)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDate"](tempStartDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDateInput"](tempStartDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDateMoment"](tempStartDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartTime"](tempStartTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDate"](tempEndDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateInput"](tempEndDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateMoment"](tempEndDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTime"](tempEndTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartTimeInput"](tempStartTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTimeInput"](tempEndTimeInput))]);
}

//
// ─── INITIALIZE ─────────────────────────────────────────────────────────────────
//

/**
 * Initializes RSVP that has not been created
 *
 * @borrows TEC - Optional functionality requires TEC to be enabled and post type to be event
 * @export
 * @yields
 */
function* initializeRSVP() {
  const publishDate = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getEditedPostAttribute'], 'date');
  const {
    moment: startMoment,
    date: startDate,
    dateInput: startDateInput,
    time: startTime,
    timeInput: startTimeInput
  } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["a" /* createDates */], publishDate);
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartDate"](startDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartDateInput"](startDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartDateMoment"](startMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartTime"](startTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPStartTimeInput"](startTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDate"](startDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDateInput"](startDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDateMoment"](startMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartTime"](startTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartTimeInput"](startTimeInput))]);
  try {
    if (yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["e" /* isTribeEventPostType */])) {
      // NOTE: This requires TEC to be installed, if not installed, do not set an end date
      const eventStart = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(window.tribe.events.data.blocks.datetime.selectors.getStart); // RSVP window should end when event starts... ideally
      const {
        moment: endMoment,
        date: endDate,
        dateInput: endDateInput,
        time: endTime,
        timeInput: endTimeInput
      } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["a" /* createDates */], eventStart);
      yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDate"](endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDateInput"](endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDateMoment"](endMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndTime"](endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndTimeInput"](endTimeInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDate"](endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateInput"](endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateMoment"](endMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTime"](endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTimeInput"](endTimeInput))]);
    }
  } catch (error) {
    // ¯\_(ツ)_/¯
    console.error(error);
  }
  yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPDurationError);
}

/**
 * Will sync RSVP sale end to be the same as event start date and time, if field has not been manually edited
 *
 * @borrows TEC - Functionality requires TEC to be enabled
 * @param {string} prevStartDate Previous start date before latest set date time changes
 * @export
 * @yields
 */
function* syncRSVPSaleEndWithEventStart(prevStartDate) {
  try {
    const tempEndMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempEndDateMoment);
    const endMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPEndDateMoment);
    const {
      moment: prevEventStartMoment
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["a" /* createDates */], prevStartDate);

    // NOTE: Mutation
    // Convert to use local timezone
    yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["call"])([tempEndMoment, 'local']), Object(external_tribe_modules_reduxSaga_effects_["call"])([endMoment, 'local']), Object(external_tribe_modules_reduxSaga_effects_["call"])([prevEventStartMoment, 'local'])]);

    // If initial end and current end are the same, the RSVP has not been modified
    const isNotManuallyEdited = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([tempEndMoment, 'isSame'], endMoment, 'minute');
    const isSyncedToEventStart = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([tempEndMoment, 'isSame'], prevEventStartMoment, 'minute');
    if (isNotManuallyEdited && isSyncedToEventStart) {
      const eventStart = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(window.tribe.events.data.blocks.datetime.selectors.getStart);
      const {
        moment: endDateMoment,
        date: endDate,
        dateInput: endDateInput,
        time: endTime,
        timeInput: endTimeInput
      } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["a" /* createDates */], eventStart);
      yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDate"](endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateInput"](endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateMoment"](endDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTime"](endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTimeInput"](endTimeInput)),
      // Sync RSVP end items as well so as not to make state 'manually edited'
      Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDate"](endDate)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDateInput"](endDateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDateMoment"](endDateMoment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndTime"](endTime)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndTimeInput"](endTimeInput)),
      // Trigger UI button
      Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHasChanges"](true)),
      // Handle RSVP duration error
      Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPDurationError)]);

      // Sub fork which will wait to sync RSVP when post saves
      yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(saveRSVPWithPostSave);
    }
  } catch (error) {
    // ¯\_(ツ)_/¯
    console.error(error);
  }
}

/**
 * Allows the RSVP to be saved at the same time a post is being saved.
 * Avoids the user having to open up the RSVP block, and then click update again there, when changing the event start date.
 *
 * @export
 * @yields
 */
function* saveRSVPWithPostSave() {
  let saveChannel;
  try {
    // Do nothing when not already created
    if (yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPCreated)) {
      // Create channel for use
      saveChannel = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["c" /* createWPEditorSavingChannel */]);

      // Wait for channel to save
      yield Object(external_tribe_modules_reduxSaga_effects_["take"])(saveChannel);
      const payload = yield Object(external_tribe_modules_reduxSaga_effects_["all"])({
        id: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPId),
        title: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempTitle),
        description: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempDescription),
        capacity: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempCapacity),
        notGoingResponses: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempNotGoingResponses),
        startDate: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempStartDate),
        startDateInput: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempStartDateInput),
        startDateMoment: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempStartDateMoment),
        endDate: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempEndDate),
        endDateInput: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempEndDateInput),
        endDateMoment: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempEndDateMoment),
        startTime: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempStartTime),
        endTime: Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempEndTime)
      });

      // Use update thunk to submit
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(updateRSVP(payload));
    }
  } catch (error) {
    console.error(error);
  } finally {
    // Close channel if exists
    if (saveChannel) {
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])([saveChannel, 'close']);
    }
  }
}

/**
 * Listens for event start date and time changes after RSVP block is loaded.
 *
 * @borrows TEC - Functionality requires TEC to be enabled and post type to be event
 * @export
 * @yields
 */
function* handleEventStartDateChanges() {
  try {
    // Proceed after creating dummy RSVP or after fetching
    yield Object(external_tribe_modules_reduxSaga_effects_["take"])([INITIALIZE_RSVP, SET_RSVP_DETAILS]);
    const isEvent = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["e" /* isTribeEventPostType */]);
    if (isEvent && window.tribe.events) {
      const {
        SET_START_DATE_TIME,
        SET_START_TIME
      } = window.tribe.events.data.blocks.datetime.types;
      let syncTask;
      while (true) {
        // Cache current event start date for comparison
        const eventStart = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(window.tribe.events.data.blocks.datetime.selectors.getStart);

        // Wait til use changes date or time on TEC datetime block
        yield Object(external_tribe_modules_reduxSaga_effects_["take"])([SET_START_DATE_TIME, SET_START_TIME]);

        // Important to cancel any pre-existing forks to prevent bad data from being sent
        if (syncTask) {
          yield Object(external_tribe_modules_reduxSaga_effects_["cancel"])(syncTask);
        }
        syncTask = yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(syncRSVPSaleEndWithEventStart, eventStart);
      }
    }
  } catch (error) {
    // ¯\_(ツ)_/¯
    console.error(error);
  }
}

//
// ─── DATE & TIME ────────────────────────────────────────────────────────────────
//

function* handleRSVPDurationError() {
  let hasDurationError = false;
  const startDateMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempStartDateMoment);
  const endDateMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempEndDateMoment);
  if (!startDateMoment || !endDateMoment) {
    hasDurationError = true;
  } else {
    const startTime = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempStartTime);
    const endTime = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempEndTime);
    const startTimeSeconds = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].toSeconds, startTime, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM_SS);
    const endTimeSeconds = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].toSeconds, endTime, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM_SS);
    const startDateTimeMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].setTimeInSeconds, startDateMoment.clone(), startTimeSeconds);
    const endDateTimeMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].setTimeInSeconds, endDateMoment.clone(), endTimeSeconds);
    const durationHasError = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([startDateTimeMoment, 'isSameOrAfter'], endDateTimeMoment);
    if (durationHasError) {
      hasDurationError = true;
    }
  }
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHasDurationError"](hasDurationError));
}
function* handleRSVPStartDate(action) {
  const {
    date,
    dayPickerInput
  } = action.payload;
  const startDateMoment = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, date) : undefined;
  const startDate = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, startDateMoment) : '';
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDate"](startDate));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDateInput"](dayPickerInput.state.value));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartDateMoment"](startDateMoment));
}
function* handleRSVPEndDate(action) {
  const {
    date,
    dayPickerInput
  } = action.payload;
  const endDateMoment = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, date) : undefined;
  const endDate = yield date ? Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toDatabaseDate, endDateMoment) : '';
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDate"](endDate));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateInput"](dayPickerInput.state.value));
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateMoment"](endDateMoment));
}
function* handleRSVPStartTime(action) {
  const startTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].fromSeconds, action.payload.seconds, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartTime"](`${startTime}:00`));
}
function* handleRSVPStartTimeInput(action) {
  const startTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].fromSeconds, action.payload.seconds, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM);
  const startTimeMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, startTime, external_tribe_common_utils_["moment"].TIME_FORMAT, false);
  const startTimeInput = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toTime, startTimeMoment);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempStartTimeInput"](startTimeInput));
}
function* handleRSVPEndTime(action) {
  const endTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].fromSeconds, action.payload.seconds, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTime"](`${endTime}:00`));
}
function* handleRSVPEndTimeInput(action) {
  const endTime = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["time"].fromSeconds, action.payload.seconds, external_tribe_common_utils_["time"].TIME_FORMAT_HH_MM);
  const endTimeMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toMoment, endTime, external_tribe_common_utils_["moment"].TIME_FORMAT, false);
  const endTimeInput = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["moment"].toTime, endTimeMoment);
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTimeInput"](endTimeInput));
}

/**
 * Handles proper RSVP deletion and RSVP block removal upon moving RSVP
 *
 * @export
 * @yields
 */
function* handleRSVPMove() {
  const rsvpId = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPId);
  const modalTicketId = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors["e" /* getModalTicketId */]);
  if (rsvpId === modalTicketId) {
    const clientId = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(selectors["a" /* getModalClientId */]);
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["deleteRSVP"]());
    yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["dispatch"])('core/editor'), 'removeBlocks'], [clientId]);
  }
}

//
// ─── HEADER IMAGE ───────────────────────────────────────────────────────────────
//

function* fetchRSVPHeaderImage(action) {
  const {
    id
  } = action.payload;
  yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPIsSettingsLoading"](true));
  try {
    const {
      response,
      data: media
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["api"].wpREST, {
      path: `media/${id}`
    });
    if (response.ok) {
      const headerImage = {
        id: media.id,
        alt: media.alt_text,
        src: media.media_details.sizes.medium.source_url
      };
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHeaderImage"](headerImage));
    }
  } catch (e) {
    console.error(e);
    /**
     * @todo: handle error scenario
     */
  } finally {
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPIsSettingsLoading"](false));
  }
}
function* updateRSVPHeaderImage(action) {
  const {
    image
  } = action.payload;
  const postId = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getCurrentPostId']);
  const body = {
    meta: {
      [utils["i" /* KEY_TICKET_HEADER */]]: `${image.id}`
    }
  };
  try {
    /**
     * @todo: until rsvp and tickets header image can be separated, they need to be linked
     */
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPIsSettingsLoading"](true));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(ticket_actions["setTicketsIsSettingsLoading"](true));
    const slug = Object(external_wp_data_["select"])('core/editor').getCurrentPostType();
    const postType = Object(external_wp_data_["select"])('core').getPostType(slug);
    const restBase = postType.rest_base;
    const {
      response
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["api"].wpREST, {
      path: `${restBase}/${postId}`,
      headers: {
        'Content-Type': 'application/json'
      },
      initParams: {
        method: 'PUT',
        body: JSON.stringify(body)
      }
    });
    if (response.ok) {
      const headerImage = {
        id: image.id,
        alt: image.alt,
        src: image.sizes.medium.url
      };
      /**
       * @todo: until rsvp and tickets header image can be separated, they need to be linked
       */
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHeaderImage"](headerImage));
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(ticket_actions["setTicketsHeaderImage"](headerImage));
    }
  } catch (e) {
    /**
     * @todo: handle error scenario
     */
  } finally {
    /**
     * @todo: until rsvp and tickets header image can be separated, they need to be linked
     */
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPIsSettingsLoading"](false));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(ticket_actions["setTicketsIsSettingsLoading"](false));
  }
}
function* deleteRSVPHeaderImage() {
  const postId = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([Object(external_wp_data_["select"])('core/editor'), 'getCurrentPostId']);
  const body = {
    meta: {
      [utils["i" /* KEY_TICKET_HEADER */]]: null
    }
  };
  try {
    /**
     * @todo: until rsvp and tickets header image can be separated, they need to be linked
     */
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPIsSettingsLoading"](true));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(ticket_actions["setTicketsIsSettingsLoading"](true));
    const slug = Object(external_wp_data_["select"])('core/editor').getCurrentPostType();
    const postType = Object(external_wp_data_["select"])('core').getPostType(slug);
    const restBase = postType.rest_base;
    const {
      response
    } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(external_tribe_common_utils_["api"].wpREST, {
      path: `${restBase}/${postId}`,
      headers: {
        'Content-Type': 'application/json'
      },
      initParams: {
        method: 'PUT',
        body: JSON.stringify(body)
      }
    });
    if (response.ok) {
      /**
       * @todo: until rsvp and tickets header image can be separated, they need to be linked
       */
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHeaderImage"](header_image["a" /* DEFAULT_STATE */]));
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(ticket_actions["setTicketsHeaderImage"](reducers_header_image["a" /* DEFAULT_STATE */]));
    }
  } catch (e) {
    /**
     * @todo: handle error scenario
     */
  } finally {
    /**
     * @todo: until rsvp and tickets header image can be separated, they need to be linked
     */
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPIsSettingsLoading"](false));
    yield Object(external_tribe_modules_reduxSaga_effects_["put"])(ticket_actions["setTicketsIsSettingsLoading"](false));
  }
}

//
// ─── HANDLERS ───────────────────────────────────────────────────────────────────
//

function* handler(action) {
  switch (action.type) {
    case SET_RSVP_DETAILS:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(setRSVPDetails, action);
      break;
    case SET_RSVP_TEMP_DETAILS:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(setRSVPTempDetails, action);
      break;
    case INITIALIZE_RSVP:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(initializeRSVP);
      break;
    case HANDLE_RSVP_START_DATE:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPStartDate, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPDurationError);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHasChanges"](true));
      break;
    case HANDLE_RSVP_END_DATE:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPEndDate, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPDurationError);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHasChanges"](true));
      break;
    case HANDLE_RSVP_START_TIME:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPStartTime, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPStartTimeInput, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPDurationError);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHasChanges"](true));
      break;
    case HANDLE_RSVP_END_TIME:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPEndTime, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPEndTimeInput, action);
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPDurationError);
      yield Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPHasChanges"](true));
      break;
    case FETCH_RSVP_HEADER_IMAGE:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(fetchRSVPHeaderImage, action);
      break;
    case UPDATE_RSVP_HEADER_IMAGE:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(updateRSVPHeaderImage, action);
      break;
    case DELETE_RSVP_HEADER_IMAGE:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(deleteRSVPHeaderImage);
      break;
    case types["k" /* MOVE_TICKET_SUCCESS */]:
      yield Object(external_tribe_modules_reduxSaga_effects_["call"])(handleRSVPMove);
      break;
    default:
      break;
  }
}

/**
 * Temporary bandaid until datepickers allow blank state
 *
 * @export
 * @yields
 */
function* setNonEventPostTypeEndDate() {
  yield Object(external_tribe_modules_reduxSaga_effects_["take"])([INITIALIZE_RSVP]);
  if (yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["e" /* isTribeEventPostType */])) {
    return;
  }
  const tempEndMoment = yield Object(external_tribe_modules_reduxSaga_effects_["select"])(getRSVPTempEndDateMoment);
  const endMoment = yield Object(external_tribe_modules_reduxSaga_effects_["call"])([tempEndMoment, 'clone']);
  const {
    date,
    dateInput,
    moment,
    time
  } = yield Object(external_tribe_modules_reduxSaga_effects_["call"])(sagas["a" /* createDates */], endMoment.toDate());
  yield Object(external_tribe_modules_reduxSaga_effects_["all"])([Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDate"](date)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateInput"](dateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndDateMoment"](moment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPTempEndTime"](time)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDate"](date)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDateInput"](dateInput)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndDateMoment"](moment)), Object(external_tribe_modules_reduxSaga_effects_["put"])(actions["setRSVPEndTime"](time))]);
}

//
// ─── WATCHERS ───────────────────────────────────────────────────────────────────
//

function* watchers() {
  yield Object(external_tribe_modules_reduxSaga_effects_["takeEvery"])([SET_RSVP_DETAILS, SET_RSVP_TEMP_DETAILS, INITIALIZE_RSVP, HANDLE_RSVP_START_DATE, HANDLE_RSVP_END_DATE, HANDLE_RSVP_START_TIME, HANDLE_RSVP_END_TIME, FETCH_RSVP_HEADER_IMAGE, UPDATE_RSVP_HEADER_IMAGE, DELETE_RSVP_HEADER_IMAGE, types["k" /* MOVE_TICKET_SUCCESS */]], handler);
  yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(handleEventStartDateChanges);
  yield Object(external_tribe_modules_reduxSaga_effects_["fork"])(setNonEventPostTypeEndDate);
}
// CONCATENATED MODULE: ./src/modules/data/blocks/rsvp/index.js
/**
 * Internal dependencies
 */






/* harmony default export */ var blocks_rsvp = __webpack_exports__["b"] = (reducer);


/***/ }),

/***/ "ocgc":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "oe2g":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "p/3v":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "ph4m":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "qkor":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPId", function() { return setRSVPId; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPSettingsOpen", function() { return setRSVPSettingsOpen; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPIsAddEditOpen", function() { return setRSVPIsAddEditOpen; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPHasChanges", function() { return setRSVPHasChanges; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPIsLoading", function() { return setRSVPIsLoading; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPIsSettingsLoading", function() { return setRSVPIsSettingsLoading; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPIsModalOpen", function() { return setRSVPIsModalOpen; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPGoingCount", function() { return setRSVPGoingCount; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPNotGoingCount", function() { return setRSVPNotGoingCount; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPHasAttendeeInfoFields", function() { return setRSVPHasAttendeeInfoFields; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPHasDurationError", function() { return setRSVPHasDurationError; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPDetails", function() { return setRSVPDetails; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempDetails", function() { return setRSVPTempDetails; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPHeaderImage", function() { return setRSVPHeaderImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTitle", function() { return setRSVPTitle; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPDescription", function() { return setRSVPDescription; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPCapacity", function() { return setRSVPCapacity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPNotGoingResponses", function() { return setRSVPNotGoingResponses; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPStartDate", function() { return setRSVPStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPStartDateInput", function() { return setRSVPStartDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPStartDateMoment", function() { return setRSVPStartDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPEndDate", function() { return setRSVPEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPEndDateInput", function() { return setRSVPEndDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPEndDateMoment", function() { return setRSVPEndDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPStartTime", function() { return setRSVPStartTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPEndTime", function() { return setRSVPEndTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPStartTimeInput", function() { return setRSVPStartTimeInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPEndTimeInput", function() { return setRSVPEndTimeInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempTitle", function() { return setRSVPTempTitle; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempDescription", function() { return setRSVPTempDescription; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempCapacity", function() { return setRSVPTempCapacity; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempNotGoingResponses", function() { return setRSVPTempNotGoingResponses; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempStartDate", function() { return setRSVPTempStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempStartDateInput", function() { return setRSVPTempStartDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempStartDateMoment", function() { return setRSVPTempStartDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempEndDate", function() { return setRSVPTempEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempEndDateInput", function() { return setRSVPTempEndDateInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempEndDateMoment", function() { return setRSVPTempEndDateMoment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempStartTime", function() { return setRSVPTempStartTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempEndTime", function() { return setRSVPTempEndTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempStartTimeInput", function() { return setRSVPTempStartTimeInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "setRSVPTempEndTimeInput", function() { return setRSVPTempEndTimeInput; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createRSVP", function() { return createRSVP; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "initializeRSVP", function() { return initializeRSVP; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "deleteRSVP", function() { return deleteRSVP; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "handleRSVPStartDate", function() { return handleRSVPStartDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "handleRSVPEndDate", function() { return handleRSVPEndDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "handleRSVPStartTime", function() { return handleRSVPStartTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "handleRSVPEndTime", function() { return handleRSVPEndTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "fetchRSVPHeaderImage", function() { return fetchRSVPHeaderImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "updateRSVPHeaderImage", function() { return updateRSVPHeaderImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "deleteRSVPHeaderImage", function() { return deleteRSVPHeaderImage; });
/* harmony import */ var _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("n+fr");
/**
 * Internal dependencies
 */


//
// ─── RSVP ACTIONS ───────────────────────────────────────────────────────────────
//

const setRSVPId = id => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_ID,
  payload: {
    id
  }
});
const setRSVPSettingsOpen = settingsOpen => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_SETTINGS_OPEN,
  payload: {
    settingsOpen
  }
});
const setRSVPIsAddEditOpen = isAddEditOpen => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_IS_ADD_EDIT_OPEN,
  payload: {
    isAddEditOpen
  }
});
const setRSVPHasChanges = hasChanges => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_HAS_CHANGES,
  payload: {
    hasChanges
  }
});
const setRSVPIsLoading = isLoading => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_IS_LOADING,
  payload: {
    isLoading
  }
});
const setRSVPIsSettingsLoading = isSettingsLoading => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_IS_SETTINGS_LOADING,
  payload: {
    isSettingsLoading
  }
});
const setRSVPIsModalOpen = isModalOpen => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_IS_MODAL_OPEN,
  payload: {
    isModalOpen
  }
});
const setRSVPGoingCount = goingCount => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_GOING_COUNT,
  payload: {
    goingCount
  }
});
const setRSVPNotGoingCount = notGoingCount => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_NOT_GOING_COUNT,
  payload: {
    notGoingCount
  }
});
const setRSVPHasAttendeeInfoFields = hasAttendeeInfoFields => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_HAS_ATTENDEE_INFO_FIELDS,
  payload: {
    hasAttendeeInfoFields
  }
});
const setRSVPHasDurationError = hasDurationError => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_HAS_DURATION_ERROR,
  payload: {
    hasDurationError
  }
});
const setRSVPDetails = payload => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_DETAILS,
  payload
});
const setRSVPTempDetails = payload => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_DETAILS,
  payload
});
const setRSVPHeaderImage = payload => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_HEADER_IMAGE,
  payload
});

//
// ─── RSVP DETAILS ACTIONS ───────────────────────────────────────────────────────
//

const setRSVPTitle = title => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TITLE,
  payload: {
    title
  }
});
const setRSVPDescription = description => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_DESCRIPTION,
  payload: {
    description
  }
});
const setRSVPCapacity = capacity => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_CAPACITY,
  payload: {
    capacity
  }
});
const setRSVPNotGoingResponses = notGoingResponses => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_NOT_GOING_RESPONSES,
  payload: {
    notGoingResponses
  }
});
const setRSVPStartDate = startDate => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_START_DATE,
  payload: {
    startDate
  }
});
const setRSVPStartDateInput = startDateInput => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_START_DATE_INPUT,
  payload: {
    startDateInput
  }
});
const setRSVPStartDateMoment = startDateMoment => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_START_DATE_MOMENT,
  payload: {
    startDateMoment
  }
});
const setRSVPEndDate = endDate => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_END_DATE,
  payload: {
    endDate
  }
});
const setRSVPEndDateInput = endDateInput => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_END_DATE_INPUT,
  payload: {
    endDateInput
  }
});
const setRSVPEndDateMoment = endDateMoment => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_END_DATE_MOMENT,
  payload: {
    endDateMoment
  }
});
const setRSVPStartTime = startTime => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_START_TIME,
  payload: {
    startTime
  }
});
const setRSVPEndTime = endTime => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_END_TIME,
  payload: {
    endTime
  }
});
const setRSVPStartTimeInput = startTimeInput => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_START_TIME_INPUT,
  payload: {
    startTimeInput
  }
});
const setRSVPEndTimeInput = endTimeInput => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_END_TIME_INPUT,
  payload: {
    endTimeInput
  }
});

//
// ─── RSVP TEMP DETAILS ACTIONS ──────────────────────────────────────────────────
//

const setRSVPTempTitle = title => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_TITLE,
  payload: {
    title
  }
});
const setRSVPTempDescription = description => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_DESCRIPTION,
  payload: {
    description
  }
});
const setRSVPTempCapacity = capacity => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_CAPACITY,
  payload: {
    capacity
  }
});
const setRSVPTempNotGoingResponses = notGoingResponses => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_NOT_GOING_RESPONSES,
  payload: {
    notGoingResponses
  }
});
const setRSVPTempStartDate = startDate => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_START_DATE,
  payload: {
    startDate
  }
});
const setRSVPTempStartDateInput = startDateInput => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_START_DATE_INPUT,
  payload: {
    startDateInput
  }
});
const setRSVPTempStartDateMoment = startDateMoment => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_START_DATE_MOMENT,
  payload: {
    startDateMoment
  }
});
const setRSVPTempEndDate = endDate => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_END_DATE,
  payload: {
    endDate
  }
});
const setRSVPTempEndDateInput = endDateInput => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_END_DATE_INPUT,
  payload: {
    endDateInput
  }
});
const setRSVPTempEndDateMoment = endDateMoment => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_END_DATE_MOMENT,
  payload: {
    endDateMoment
  }
});
const setRSVPTempStartTime = startTime => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_START_TIME,
  payload: {
    startTime
  }
});
const setRSVPTempEndTime = endTime => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_END_TIME,
  payload: {
    endTime
  }
});
const setRSVPTempStartTimeInput = startTimeInput => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_START_TIME_INPUT,
  payload: {
    startTimeInput
  }
});
const setRSVPTempEndTimeInput = endTimeInput => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].SET_RSVP_TEMP_END_TIME_INPUT,
  payload: {
    endTimeInput
  }
});

//
// ─── RSVP THUNK & SAGA ACTIONS ──────────────────────────────────────────────────
//

const createRSVP = () => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].CREATE_RSVP
});
const initializeRSVP = () => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].INITIALIZE_RSVP
});
const deleteRSVP = () => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].DELETE_RSVP
});
const handleRSVPStartDate = payload => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].HANDLE_RSVP_START_DATE,
  payload
});
const handleRSVPEndDate = payload => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].HANDLE_RSVP_END_DATE,
  payload
});
const handleRSVPStartTime = seconds => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].HANDLE_RSVP_START_TIME,
  payload: {
    seconds
  }
});
const handleRSVPEndTime = seconds => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].HANDLE_RSVP_END_TIME,
  payload: {
    seconds
  }
});
const fetchRSVPHeaderImage = id => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].FETCH_RSVP_HEADER_IMAGE,
  payload: {
    id
  }
});
const updateRSVPHeaderImage = image => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].UPDATE_RSVP_HEADER_IMAGE,
  payload: {
    image
  }
});
const deleteRSVPHeaderImage = () => ({
  type: _moderntribe_tickets_data_blocks_rsvp__WEBPACK_IMPORTED_MODULE_0__[/* types */ "f"].DELETE_RSVP_HEADER_IMAGE
});

/***/ }),

/***/ "rKB8":
/***/ (function(module, exports) {

module.exports = tribe.modules.redux;

/***/ }),

/***/ "rf6O":
/***/ (function(module, exports) {

module.exports = tribe.modules.propTypes;

/***/ }),

/***/ "rl8x":
/***/ (function(module, exports) {

module.exports = wp.isShallowEqual;

/***/ }),

/***/ "s3Q2":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "sMOv":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "t/7v":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

module.exports = wp.components;

/***/ }),

/***/ "trUg":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "unXf":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "vLzK":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "wy2R":
/***/ (function(module, exports) {

module.exports = moment;

/***/ })

/******/ });