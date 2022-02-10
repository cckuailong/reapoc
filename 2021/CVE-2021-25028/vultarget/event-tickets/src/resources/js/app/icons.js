var tribe = typeof tribe === "object" ? tribe : {}; tribe["tickets"] = tribe["tickets"] || {}; tribe["tickets"]["icons"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 28);
/******/ })
/************************************************************************/
/******/ ({

/***/ 10:
/***/ (function(module, exports) {

module.exports = React;

/***/ }),

/***/ 28:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "ClockActive", function() { return /* reexport */ clock; });
__webpack_require__.d(__webpack_exports__, "ClockInactive", function() { return /* reexport */ inactive_clock; });
__webpack_require__.d(__webpack_exports__, "Tickets", function() { return /* reexport */ tickets; });
__webpack_require__.d(__webpack_exports__, "TicketActive", function() { return /* reexport */ ticket; });
__webpack_require__.d(__webpack_exports__, "TicketInactive", function() { return /* reexport */ inactive_ticket; });
__webpack_require__.d(__webpack_exports__, "RSVP", function() { return /* reexport */ rsvp; });
__webpack_require__.d(__webpack_exports__, "RSVPActive", function() { return /* reexport */ active_rsvp; });
__webpack_require__.d(__webpack_exports__, "RSVPInactive", function() { return /* reexport */ inactive_rsvp; });
__webpack_require__.d(__webpack_exports__, "AttendeesGravatar", function() { return /* reexport */ gravatar; });
__webpack_require__.d(__webpack_exports__, "Attendees", function() { return /* reexport */ attendees; });
__webpack_require__.d(__webpack_exports__, "Orders", function() { return /* reexport */ orders; });

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__(10);
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
    viewBox: "0 0 36 47.98"
  }, props), /*#__PURE__*/external_React_default.a.createElement("title", null, "block-icon-attendees_1"), /*#__PURE__*/external_React_default.a.createElement("g", {
    "data-name": "Layer 2"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M32 6.08h-9.17V3.5a3.51 3.51 0 0 0-3.5-3.5h-3a3.51 3.51 0 0 0-3.5 3.5v2.58H4a4 4 0 0 0-4 4V44a4 4 0 0 0 4 4h28a4 4 0 0 0 4-4V10.08a4 4 0 0 0-4-4zm-14.08 5.63a7.3 7.3 0 1 1-7.3 7.3 7.3 7.3 0 0 1 7.3-7.3zM31.24 40v.22a2.67 2.67 0 0 1-.07.32v.07a2.25 2.25 0 0 1-2.1 1.7H6.81a2.36 2.36 0 0 1-2.22-2.48c0-4.46 6.5-10.47 13.33-10.47 6.41 0 12.53 5.57 13.33 9.88a2.77 2.77 0 0 1 0 .51v.13c0 .05 0 .12-.01.12z",
    "data-name": "Layer 1"
  })));
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
    viewBox: "0 0 36 47.99"
  }, props), /*#__PURE__*/external_React_default.a.createElement("title", null, "block-orders-icon"), /*#__PURE__*/external_React_default.a.createElement("g", {
    "data-name": "Layer 2"
  }, /*#__PURE__*/external_React_default.a.createElement("path", {
    d: "M32 0H4a4 4 0 0 0-4 4v40a4 4 0 0 0 4 4h28a4 4 0 0 0 4-4V4a4 4 0 0 0-4-4zM20.78 39.58h-.08l-.3.06-.83.14-.08 3.51h-3.25l-.08-3.52a7.52 7.52 0 0 1-4.28-1.93 7.93 7.93 0 0 1-2.13-5.14h4a3.71 3.71 0 0 0 .92 2.64 4.2 4.2 0 0 0 3.24 1.34 4.07 4.07 0 0 0 .88-.06 5.46 5.46 0 0 0 1.13-.26 3.58 3.58 0 0 0 1.08-.54 3 3 0 0 0 .78-.9 2.55 2.55 0 0 0 .31-1.28 2.19 2.19 0 0 0-.76-1.81 7.3 7.3 0 0 0-2.51-1.07l-3.52-1h-.12c-3-1-4.54-2.47-4.85-4.9a3 3 0 0 1-.07-.63v-.26-.12c0-3.07 2.77-5.56 5.93-5.79l.08-3.51h3.25l.08 3.57a10.4 10.4 0 0 1 1.66.43 6.68 6.68 0 0 1 2 1.17 5.84 5.84 0 0 1 1.43 1.85 6.67 6.67 0 0 1 .63 2.43h-4a3.21 3.21 0 0 0-.76-2 3.34 3.34 0 0 0-1.78-.86 4.91 4.91 0 0 0-1.08-.09 7.1 7.1 0 0 0-.84 0 8.06 8.06 0 0 0-.85.19 2.7 2.7 0 0 0-.89.48 2.32 2.32 0 0 0-.64.81 2.57 2.57 0 0 0-.23 1.13 2 2 0 0 0 .66 1.59 5 5 0 0 0 2.09.88l1.75.41.73.18.39.1c.47.12.92.25 1.36.4l.26.1.42.16a8.83 8.83 0 0 1 1.21.59 5.8 5.8 0 0 1 3.19 5.33c-.03 3.04-2.53 5.51-5.53 6.18zM28.86 10H7.51a2.5 2.5 0 0 1 0-5h21.35a2.5 2.5 0 0 1 0 5z",
    "data-name": "Layer 1"
  })));
});
// CONCATENATED MODULE: ./src/modules/icons/index.js












/***/ })

/******/ });