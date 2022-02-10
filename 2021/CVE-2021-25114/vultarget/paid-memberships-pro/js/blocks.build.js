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
/******/ 	return __webpack_require__(__webpack_require__.s = "./blocks/blocks.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./blocks/account-invoices-section/block.js":
/*!**************************************************!*\
  !*** ./blocks/account-invoices-section/block.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership Account: Invoices
 *
 * Displays the Membership Account > Invoices page section.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/account-invoices-section', {
  title: __('Membership Account: Invoices', 'paid-memberships-pro'),
  description: __('Displays the member\'s invoices.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'archive'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, " ", __('Membership Account: Invoices', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/account-links-section/block.js":
/*!***********************************************!*\
  !*** ./blocks/account-links-section/block.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership Account: Member Links
 *
 * Displays the Membership Account > Member Links page section.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/account-links-section', {
  title: __('Membership Account: Links', 'paid-memberships-pro'),
  description: __('Displays the member\'s member links. This block is only visible if other Add Ons or custom code have added links.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'external'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Account: Member Links', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/account-membership-section/block.js":
/*!****************************************************!*\
  !*** ./blocks/account-membership-section/block.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership Account: Memberships
 *
 * Displays the Membership Account > My Memberships page section.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/account-membership-section', {
  title: __('Membership Account: Memberships', 'paid-memberships-pro'),
  description: __('Displays the member\'s membership information.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'groups'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Account: My Memberships', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/account-page/block.js":
/*!**************************************!*\
  !*** ./blocks/account-page/block.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _inspector__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./inspector */ "./blocks/account-page/inspector.js");



function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Block: PMPro Membership Account
 *
 * Displays the Membership Account page.
 *
 */

/**
 * Block dependencies
 */

/**
 * Internal block libraries
 */

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/account-page', {
  title: __('Membership Account Page', 'paid-memberships-pro'),
  description: __('Displays the sections of the Membership Account page as selected below.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'admin-users'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {
    membership: {
      type: 'boolean',
      default: false
    },
    profile: {
      type: 'boolean',
      default: false
    },
    invoices: {
      type: 'boolean',
      default: false
    },
    links: {
      type: 'boolean',
      default: false
    }
  },
  edit: function edit(props) {
    var setAttributes = props.setAttributes,
        isSelected = props.isSelected;
    return [isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_inspector__WEBPACK_IMPORTED_MODULE_2__["default"], _objectSpread({
      setAttributes: setAttributes
    }, props)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Account Page', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/account-page/inspector.js":
/*!******************************************!*\
  !*** ./blocks/account-page/inspector.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Inspector; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);







function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, result); }; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var Component = wp.element.Component;
var _wp$components = wp.components,
    PanelBody = _wp$components.PanelBody,
    CheckboxControl = _wp$components.CheckboxControl;
var InspectorControls = wp.blockEditor.InspectorControls;
/**
 * Create an Inspector Controls wrapper Component
 */

var Inspector = /*#__PURE__*/function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default()(Inspector, _Component);

  var _super = _createSuper(Inspector);

  function Inspector() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, Inspector);

    return _super.apply(this, arguments);
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(Inspector, [{
    key: "render",
    value: function render() {
      var _this$props = this.props,
          _this$props$attribute = _this$props.attributes,
          membership = _this$props$attribute.membership,
          profile = _this$props$attribute.profile,
          invoices = _this$props$attribute.invoices,
          links = _this$props$attribute.links,
          setAttributes = _this$props.setAttributes;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(CheckboxControl, {
        label: __("Show 'My Memberships' Section", 'paid-memberships-pro'),
        checked: membership,
        onChange: function onChange(membership) {
          return setAttributes({
            membership: membership
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(CheckboxControl, {
        label: __("Show 'Profile' Section", 'paid-memberships-pro'),
        checked: profile,
        onChange: function onChange(profile) {
          return setAttributes({
            profile: profile
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(CheckboxControl, {
        label: __("Show 'Invoices' Section", 'paid-memberships-pro'),
        checked: invoices,
        onChange: function onChange(invoices) {
          return setAttributes({
            invoices: invoices
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(CheckboxControl, {
        label: __("Show 'Member Links' Section", 'paid-memberships-pro'),
        checked: links,
        onChange: function onChange(links) {
          return setAttributes({
            links: links
          });
        }
      })));
    }
  }]);

  return Inspector;
}(Component);



/***/ }),

/***/ "./blocks/account-profile-section/block.js":
/*!*************************************************!*\
  !*** ./blocks/account-profile-section/block.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Checkout Button
 *
 * Add a styled link to the PMPro checkout page for a
 * specific level.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/account-profile-section', {
  title: __('Membership Account: Profile', 'paid-memberships-pro'),
  description: __('Displays the member\'s profile information.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'admin-users'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Account: Profile', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/billing-page/block.js":
/*!**************************************!*\
  !*** ./blocks/billing-page/block.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership Billing
 *
 * Displays the Membership Billing page and form.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/billing-page', {
  title: __('Membership Billing Page', 'paid-memberships-pro'),
  description: __('Displays the member\'s billing information and allows them to update the payment method.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'list-view'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Billing Page', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/blocks.js":
/*!**************************!*\
  !*** ./blocks/blocks.js ***!
  \**************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _i18n_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./i18n.js */ "./blocks/i18n.js");
/* harmony import */ var _i18n_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_i18n_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _checkout_button_block_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./checkout-button/block.js */ "./blocks/checkout-button/block.js");
/* harmony import */ var _account_page_block_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./account-page/block.js */ "./blocks/account-page/block.js");
/* harmony import */ var _account_membership_section_block_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./account-membership-section/block.js */ "./blocks/account-membership-section/block.js");
/* harmony import */ var _account_profile_section_block_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./account-profile-section/block.js */ "./blocks/account-profile-section/block.js");
/* harmony import */ var _account_invoices_section_block_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./account-invoices-section/block.js */ "./blocks/account-invoices-section/block.js");
/* harmony import */ var _account_links_section_block_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./account-links-section/block.js */ "./blocks/account-links-section/block.js");
/* harmony import */ var _billing_page_block_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./billing-page/block.js */ "./blocks/billing-page/block.js");
/* harmony import */ var _cancel_page_block_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./cancel-page/block.js */ "./blocks/cancel-page/block.js");
/* harmony import */ var _checkout_page_block_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./checkout-page/block.js */ "./blocks/checkout-page/block.js");
/* harmony import */ var _confirmation_page_block_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./confirmation-page/block.js */ "./blocks/confirmation-page/block.js");
/* harmony import */ var _invoice_page_block_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./invoice-page/block.js */ "./blocks/invoice-page/block.js");
/* harmony import */ var _levels_page_block_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./levels-page/block.js */ "./blocks/levels-page/block.js");
/* harmony import */ var _membership_block_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./membership/block.js */ "./blocks/membership/block.js");
/* harmony import */ var _member_profile_edit_block_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./member-profile-edit/block.js */ "./blocks/member-profile-edit/block.js");
/* harmony import */ var _login_block_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./login/block.js */ "./blocks/login/block.js");


/**
 * Import internationalization
 */

/**
 * Import registerBlockType blocks
 */

















(function () {
  var PMProSVG = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    version: "1.1",
    id: "Layer_1",
    x: "0px",
    y: "0px",
    viewBox: "0 0 18 18"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M17.99,4.53c-0.35,0.12-0.7,0.26-1.06,0.4c-0.35,0.14-0.7,0.3-1.05,0.46c-0.35,0.16-0.69,0.33-1.03,0.51 c-0.34,0.18-0.68,0.37-1.02,0.56c-0.15,0.09-0.31,0.18-0.46,0.27c-0.15,0.09-0.3,0.19-0.45,0.28c-0.15,0.1-0.3,0.19-0.45,0.29 c-0.15,0.1-0.3,0.2-0.44,0.3c-0.08,0.05-0.16,0.11-0.23,0.16c-0.08,0.05-0.16,0.11-0.23,0.17c-0.08,0.06-0.15,0.11-0.23,0.17 c-0.08,0.06-0.15,0.11-0.23,0.17c-0.07,0.05-0.13,0.1-0.2,0.15c-0.07,0.05-0.13,0.1-0.2,0.15c-0.07,0.05-0.13,0.1-0.2,0.15 c-0.07,0.05-0.13,0.1-0.2,0.16c-0.04,0.03-0.09,0.07-0.13,0.1c-0.04,0.03-0.09,0.07-0.13,0.1C10,9.13,9.95,9.17,9.91,9.2 C9.87,9.24,9.83,9.27,9.79,9.31C9.77,9.32,9.75,9.33,9.74,9.35C9.72,9.36,9.71,9.37,9.69,9.39C9.67,9.4,9.66,9.42,9.64,9.43 C9.63,9.44,9.61,9.46,9.59,9.47C9.54,9.52,9.49,9.56,9.43,9.61C9.38,9.65,9.33,9.7,9.27,9.74C9.22,9.79,9.17,9.84,9.11,9.88 c-0.05,0.05-0.11,0.09-0.16,0.14c-0.27,0.24-0.54,0.49-0.81,0.75c-0.26,0.25-0.53,0.51-0.78,0.78c-0.26,0.26-0.51,0.53-0.76,0.81 c-0.25,0.27-0.49,0.55-0.73,0.84c-0.03,0.04-0.07,0.08-0.1,0.12c-0.03,0.04-0.07,0.08-0.1,0.12c-0.03,0.04-0.07,0.08-0.1,0.12 c-0.03,0.04-0.07,0.08-0.1,0.12c-0.03,0.04-0.07,0.08-0.1,0.12c-0.03,0.04-0.06,0.08-0.1,0.12c-0.03,0.04-0.06,0.08-0.1,0.12 c-0.03,0.04-0.06,0.08-0.1,0.12c0,0.01-0.01,0.01-0.01,0.02c0,0.01-0.01,0.01-0.01,0.02c0,0.01-0.01,0.01-0.01,0.02 c0,0.01-0.01,0.01-0.01,0.02c-0.03,0.03-0.05,0.07-0.08,0.1c-0.03,0.03-0.05,0.07-0.08,0.1c-0.03,0.03-0.05,0.07-0.08,0.11 c-0.03,0.03-0.05,0.07-0.08,0.11c-0.03,0.04-0.06,0.08-0.09,0.12c-0.03,0.04-0.06,0.08-0.09,0.12C4.5,14.96,4.47,15,4.44,15.05 c-0.03,0.04-0.06,0.08-0.09,0.13c0,0-0.01,0.01-0.01,0.01c0,0-0.01,0.01-0.01,0.01c0,0-0.01,0.01-0.01,0.01c0,0-0.01,0.01-0.01,0.01 c-0.15,0.22-0.31,0.44-0.46,0.67c-0.15,0.22-0.3,0.45-0.44,0.68c-0.14,0.23-0.29,0.46-0.43,0.7C2.85,17.52,2.71,17.76,2.58,18 c-0.08-0.19-0.16-0.38-0.23-0.56c-0.07-0.18-0.14-0.35-0.21-0.51c-0.07-0.16-0.13-0.32-0.19-0.47c-0.06-0.15-0.12-0.3-0.18-0.45 l-0.01,0.01l0.01-0.03c-0.01-0.03-0.02-0.05-0.03-0.08c-0.01-0.02-0.02-0.05-0.03-0.07c-0.01-0.02-0.02-0.05-0.03-0.07 c-0.01-0.02-0.02-0.05-0.03-0.07c0-0.01-0.01-0.02-0.01-0.02c0-0.01-0.01-0.02-0.01-0.02c0-0.01-0.01-0.02-0.01-0.02 c0-0.01-0.01-0.02-0.01-0.02c-0.01-0.02-0.01-0.04-0.02-0.05c-0.01-0.02-0.01-0.04-0.02-0.05c-0.01-0.02-0.01-0.04-0.02-0.05 c-0.01-0.02-0.01-0.04-0.02-0.05c-0.01-0.03-0.02-0.05-0.03-0.07c-0.01-0.02-0.02-0.05-0.03-0.07c-0.01-0.02-0.02-0.05-0.03-0.07 c-0.01-0.02-0.02-0.05-0.03-0.07c-0.01-0.02-0.02-0.05-0.03-0.07c-0.01-0.02-0.02-0.05-0.03-0.07c-0.01-0.02-0.02-0.05-0.03-0.07 c-0.01-0.02-0.02-0.05-0.03-0.07c-0.02-0.05-0.04-0.1-0.06-0.16c-0.02-0.05-0.04-0.1-0.06-0.16c-0.02-0.05-0.04-0.11-0.06-0.16 c-0.02-0.05-0.04-0.11-0.06-0.16c-0.08-0.23-0.16-0.47-0.25-0.72c-0.08-0.25-0.17-0.5-0.26-0.77c-0.09-0.27-0.18-0.55-0.27-0.84 c-0.09-0.29-0.19-0.6-0.29-0.93c0.05,0.07,0.1,0.15,0.15,0.22c0.05,0.07,0.1,0.14,0.14,0.2c0.05,0.07,0.09,0.13,0.14,0.19 c0.04,0.06,0.09,0.12,0.13,0.18c0.09,0.13,0.18,0.24,0.27,0.35c0.09,0.11,0.17,0.21,0.24,0.3c0.08,0.09,0.15,0.18,0.23,0.27 c0.07,0.09,0.15,0.17,0.22,0.25c0.02,0.02,0.03,0.04,0.05,0.06c0.02,0.02,0.03,0.04,0.05,0.06c0.02,0.02,0.03,0.04,0.05,0.06 c0.02,0.02,0.03,0.04,0.05,0.06c0.07,0.07,0.13,0.14,0.2,0.22c0.07,0.08,0.14,0.16,0.22,0.24c0.08,0.08,0.16,0.17,0.24,0.27 c0.09,0.1,0.18,0.2,0.27,0.31c0.01,0.01,0.02,0.02,0.03,0.03c0.01,0.01,0.02,0.02,0.03,0.03c0.01,0.01,0.02,0.02,0.03,0.04 c0.01,0.01,0.02,0.02,0.03,0.04c0.02-0.02,0.04-0.05,0.06-0.07c0.02-0.02,0.04-0.05,0.06-0.07c0.02-0.02,0.04-0.05,0.06-0.07 C2.96,14.03,2.98,14,3,13.98c0.03-0.03,0.05-0.06,0.08-0.09c0.03-0.03,0.05-0.06,0.08-0.09c0.03-0.03,0.05-0.06,0.08-0.09 c0.03-0.03,0.05-0.06,0.08-0.09c0.28-0.33,0.58-0.65,0.88-0.97c0.31-0.32,0.63-0.62,0.95-0.92c0.33-0.3,0.67-0.6,1.02-0.88 c0.35-0.29,0.72-0.57,1.09-0.84c0.06-0.04,0.11-0.08,0.17-0.12C7.49,9.83,7.55,9.79,7.6,9.75c0.06-0.04,0.11-0.08,0.17-0.12 c0.06-0.04,0.12-0.08,0.17-0.12C7.97,9.5,7.98,9.49,8,9.48c0.02-0.01,0.03-0.02,0.05-0.03C8.06,9.43,8.08,9.42,8.1,9.41 C8.11,9.4,8.13,9.38,8.14,9.37c0.05-0.03,0.1-0.06,0.14-0.1c0.05-0.03,0.1-0.06,0.14-0.1c0.05-0.03,0.1-0.06,0.14-0.1 c0.05-0.03,0.1-0.06,0.15-0.09C8.79,8.94,8.87,8.9,8.94,8.85C9.01,8.8,9.09,8.76,9.16,8.71c0.07-0.05,0.15-0.09,0.22-0.14 c0.07-0.05,0.15-0.09,0.22-0.14c0.09-0.05,0.17-0.11,0.26-0.16c0.09-0.05,0.17-0.1,0.26-0.16c0.09-0.05,0.18-0.1,0.27-0.15 c0.09-0.05,0.18-0.1,0.27-0.15c0.25-0.14,0.51-0.28,0.76-0.42c0.26-0.14,0.52-0.27,0.78-0.41c0.26-0.13,0.53-0.27,0.79-0.4 c0.27-0.13,0.54-0.26,0.81-0.38c0.01,0,0.02-0.01,0.03-0.01c0.01,0,0.02-0.01,0.03-0.01c0.01,0,0.02-0.01,0.03-0.01 c0.01,0,0.02-0.01,0.03-0.01c0.33-0.15,0.67-0.3,1-0.44c0.34-0.15,0.68-0.29,1.02-0.42c0.34-0.14,0.69-0.27,1.03-0.4 C17.31,4.77,17.65,4.64,17.99,4.53z M15.73,9.59l0.65,4.56l-10.4-0.05c-0.02,0.02-0.04,0.04-0.05,0.07 c-0.02,0.02-0.04,0.04-0.05,0.07c-0.02,0.02-0.04,0.04-0.05,0.07c-0.02,0.02-0.04,0.04-0.05,0.07c-0.02,0.02-0.03,0.04-0.05,0.06 c-0.02,0.02-0.03,0.04-0.05,0.06c-0.02,0.02-0.03,0.04-0.05,0.06c-0.02,0.02-0.03,0.04-0.05,0.06l11.23,0.2l-0.78-5.24L15.73,9.59z M6.75,13.2c-0.04,0.04-0.08,0.09-0.11,0.13c-0.04,0.04-0.08,0.09-0.11,0.13c-0.04,0.04-0.07,0.09-0.11,0.13l9.22-0.07L15.04,9.1 l-0.07-0.53l-0.39,0.04l0.55,4.3l-8.27,0.17C6.83,13.12,6.79,13.16,6.75,13.2z M13.78,7.66l-0.59,0.08 c-0.06,0.04-0.12,0.08-0.18,0.12c-0.06,0.04-0.12,0.08-0.18,0.12c-0.06,0.04-0.12,0.08-0.18,0.12c-0.06,0.04-0.12,0.08-0.18,0.12 c-0.08,0.05-0.16,0.11-0.24,0.16c-0.08,0.06-0.16,0.11-0.24,0.17c-0.08,0.06-0.16,0.11-0.24,0.17c-0.08,0.06-0.16,0.11-0.24,0.17 c-0.07,0.05-0.14,0.1-0.21,0.15c-0.07,0.05-0.14,0.1-0.21,0.15c-0.07,0.05-0.14,0.1-0.2,0.16c-0.07,0.05-0.14,0.11-0.2,0.16 c-0.04,0.03-0.09,0.07-0.13,0.1c-0.04,0.03-0.09,0.07-0.13,0.1c-0.04,0.04-0.09,0.07-0.13,0.11c-0.04,0.04-0.09,0.07-0.13,0.11 c-0.02,0.01-0.03,0.03-0.05,0.04c-0.02,0.01-0.03,0.03-0.05,0.04c-0.02,0.01-0.03,0.03-0.05,0.04c-0.02,0.01-0.03,0.03-0.05,0.04 c-0.06,0.05-0.11,0.09-0.16,0.14c-0.05,0.05-0.11,0.09-0.16,0.14c-0.05,0.05-0.11,0.09-0.16,0.14c-0.05,0.05-0.11,0.09-0.16,0.14 c-0.17,0.15-0.34,0.3-0.51,0.46c-0.17,0.16-0.33,0.31-0.5,0.47c-0.16,0.16-0.33,0.32-0.49,0.48c-0.16,0.16-0.32,0.33-0.48,0.49 l6.98-0.23l-0.48-4.16L13.78,7.66z M13.32,5.73c-0.06,0.03-0.11,0.05-0.17,0.08c-0.06,0.03-0.12,0.06-0.17,0.09 c-0.03,0.01-0.06,0.03-0.08,0.04c0,0,0,0,0,0c-0.02-0.01-0.04-0.03-0.06-0.04c-0.06-0.04-0.13-0.07-0.21-0.09 c-0.07-0.02-0.15-0.04-0.23-0.04c-0.08,0-0.16,0-0.24,0.01l-0.14,0.02c0.07-0.04,0.13-0.08,0.18-0.14c0.05-0.05,0.1-0.11,0.14-0.18 c0.04-0.06,0.06-0.13,0.08-0.2c0.02-0.07,0.02-0.15,0.01-0.22c-0.01-0.1-0.04-0.18-0.08-0.26c-0.05-0.08-0.11-0.14-0.18-0.19 c-0.07-0.05-0.16-0.08-0.25-0.1c-0.09-0.02-0.19-0.02-0.29,0c-0.1,0.02-0.19,0.06-0.27,0.11c-0.08,0.05-0.15,0.11-0.21,0.19 C11.08,4.9,11.03,4.98,11,5.07c-0.03,0.09-0.04,0.18-0.03,0.27c0.01,0.07,0.02,0.14,0.05,0.2c0.03,0.06,0.06,0.12,0.11,0.17 c0.05,0.05,0.1,0.09,0.16,0.12c0.06,0.03,0.13,0.06,0.2,0.07l-0.17,0.03C11.18,5.96,11.06,6,10.94,6.07 c-0.11,0.07-0.21,0.15-0.29,0.25c-0.08,0.1-0.14,0.21-0.19,0.33c-0.04,0.12-0.06,0.25-0.05,0.38l0.02,0.33 c-0.09,0.05-0.17,0.1-0.26,0.16c-0.02,0-0.05,0-0.07,0c0.02-0.01,0.04-0.02,0.06-0.03c-0.06-0.06-0.13-0.11-0.21-0.16 c-0.07-0.04-0.15-0.08-0.24-0.1C9.63,7.2,9.54,7.18,9.45,7.18c-0.09-0.01-0.18,0-0.27,0.01L9.01,7.21c0.08-0.05,0.16-0.1,0.23-0.17 C9.3,6.97,9.36,6.9,9.41,6.81C9.46,6.73,9.5,6.64,9.52,6.55c0.02-0.09,0.03-0.19,0.03-0.29C9.54,6.13,9.51,6.02,9.46,5.92 c-0.05-0.1-0.12-0.18-0.21-0.25C9.17,5.6,9.07,5.56,8.96,5.53c-0.11-0.02-0.22-0.03-0.34,0C8.5,5.55,8.39,5.6,8.29,5.66 C8.19,5.72,8.1,5.81,8.02,5.9C7.95,5.99,7.89,6.1,7.85,6.21C7.81,6.32,7.79,6.44,7.79,6.56c0,0.09,0.02,0.18,0.05,0.26 c0.03,0.08,0.07,0.16,0.12,0.22c0.05,0.07,0.11,0.12,0.18,0.17c0.07,0.04,0.15,0.08,0.23,0.1l-0.2,0.03 C8.01,7.37,7.85,7.42,7.72,7.51C7.58,7.59,7.46,7.7,7.35,7.82C7.25,7.95,7.17,8.1,7.11,8.25c-0.06,0.16-0.09,0.33-0.08,0.5 l0.01,0.74C6.98,9.53,6.93,9.58,6.88,9.62C6.81,9.49,6.74,9.38,6.65,9.28c-0.1-0.11-0.21-0.2-0.33-0.27 C6.2,8.94,6.07,8.89,5.93,8.87C5.8,8.84,5.66,8.83,5.51,8.85L5.3,8.88c0.1-0.06,0.2-0.13,0.29-0.22c0.09-0.09,0.16-0.19,0.23-0.3 c0.06-0.11,0.12-0.23,0.15-0.35C6,7.88,6.02,7.75,6.02,7.62c0-0.17-0.03-0.32-0.08-0.46C5.88,7.03,5.8,6.91,5.71,6.82 C5.61,6.73,5.5,6.67,5.37,6.63c-0.12-0.04-0.26-0.04-0.4-0.02c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0 c-0.14,0.03-0.28,0.08-0.4,0.16c-0.12,0.08-0.23,0.18-0.33,0.3C4.14,7.2,4.07,7.33,4.01,7.48c-0.06,0.15-0.09,0.3-0.1,0.46 c0,0.12,0.01,0.24,0.03,0.35c0.03,0.11,0.07,0.21,0.12,0.3c0.05,0.09,0.12,0.17,0.2,0.23c0.08,0.06,0.17,0.11,0.27,0.14L4.3,9 C4.1,9.03,3.92,9.09,3.75,9.2C3.58,9.3,3.43,9.44,3.3,9.6c-0.13,0.16-0.24,0.35-0.32,0.56c-0.08,0.21-0.13,0.43-0.14,0.67 l-0.12,2.26l-0.53-0.6l0.49-6.3C2.68,6.09,2.71,6,2.74,5.91c0.04-0.09,0.08-0.17,0.14-0.24c0.06-0.07,0.12-0.14,0.2-0.19 C3.15,5.44,3.23,5.4,3.32,5.38l0.71-0.17l0-0.02l0.18-0.04l0.06-1.19C4.3,3.56,4.39,3.15,4.55,2.77c0.16-0.38,0.37-0.75,0.64-1.08 C5.45,1.35,5.76,1.05,6.11,0.8c0.35-0.26,0.74-0.47,1.16-0.61C7.7,0.05,8.12-0.01,8.51,0c0.4,0.02,0.77,0.12,1.1,0.29 c0.33,0.18,0.62,0.43,0.83,0.75c0.21,0.33,0.35,0.73,0.38,1.19l0.1,1.36l0.3-0.07l0,0.02l0.89-0.21c0.13-0.03,0.25-0.03,0.36-0.02 c0.12,0.02,0.22,0.05,0.32,0.11c0.09,0.05,0.17,0.13,0.23,0.21c0.06,0.09,0.1,0.19,0.11,0.31L13.32,5.73z M9.46,3.96L9.4,2.61 C9.39,2.33,9.31,2.09,9.19,1.88C9.07,1.68,8.91,1.51,8.71,1.4C8.52,1.28,8.29,1.21,8.05,1.19C7.81,1.17,7.55,1.2,7.28,1.28 C7.01,1.37,6.76,1.49,6.53,1.65c-0.22,0.16-0.43,0.35-0.6,0.57C5.77,2.43,5.63,2.67,5.53,2.91c-0.1,0.25-0.16,0.5-0.17,0.76 L5.33,4.91L9.46,3.96z"
  }));
  wp.blocks.updateCategory('pmpro', {
    icon: PMProSVG
  });
})();

/***/ }),

/***/ "./blocks/cancel-page/block.js":
/*!*************************************!*\
  !*** ./blocks/cancel-page/block.js ***!
  \*************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership Cancel
 *
 * Displays the Membership Cancel page.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/cancel-page', {
  title: __('Membership Cancel Page', 'paid-memberships-pro'),
  description: __('Generates the Membership Cancel page.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'no'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Cancel Page', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/checkout-button/block.js":
/*!*****************************************!*\
  !*** ./blocks/checkout-button/block.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _inspector__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./inspector */ "./blocks/checkout-button/inspector.js");



function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Block: PMPro Checkout Button
 *
 * Add a styled link to the PMPro checkout page for a specific level.
 *
 */

/**
 * Block dependencies
 */

/**
 * Internal block libraries
 */

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var _wp$components = wp.components,
    TextControl = _wp$components.TextControl,
    SelectControl = _wp$components.SelectControl;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/checkout-button', {
  title: __('Membership Checkout Button', 'paid-memberships-pro'),
  description: __('Displays a button-styled link to Membership Checkout for the specified level.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'migrate'
  },
  keywords: [__('pmpro', 'paid-memberships-pro'), __('buy', 'paid-memberships-pro'), __('level', 'paid-memberships-pro')],
  supports: {},
  attributes: {
    text: {
      type: 'string',
      default: 'Buy Now'
    },
    css_class: {
      type: 'string',
      default: 'pmpro_btn'
    },
    level: {
      type: 'string'
    }
  },
  edit: function edit(props) {
    var _props$attributes = props.attributes,
        text = _props$attributes.text,
        level = _props$attributes.level,
        css_class = _props$attributes.css_class,
        className = props.className,
        setAttributes = props.setAttributes,
        isSelected = props.isSelected;
    return [isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_inspector__WEBPACK_IMPORTED_MODULE_2__["default"], _objectSpread({
      setAttributes: setAttributes
    }, props)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: className
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("a", {
      class: css_class
    }, text)), isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(TextControl, {
      label: __('Button Text', 'paid-memberships-pro'),
      value: text,
      onChange: function onChange(text) {
        return setAttributes({
          text: text
        });
      }
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(SelectControl, {
      label: __('Membership Level', 'paid-memberships-pro'),
      value: level,
      onChange: function onChange(level) {
        return setAttributes({
          level: level
        });
      },
      options: window.pmpro.all_level_values_and_labels
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(TextControl, {
      label: __('CSS Class', 'paid-memberships-pro'),
      value: css_class,
      onChange: function onChange(css_class) {
        return setAttributes({
          css_class: css_class
        });
      }
    }))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/checkout-button/inspector.js":
/*!*********************************************!*\
  !*** ./blocks/checkout-button/inspector.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Inspector; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);







function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, result); }; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var Component = wp.element.Component;
var _wp$components = wp.components,
    PanelBody = _wp$components.PanelBody,
    TextControl = _wp$components.TextControl,
    SelectControl = _wp$components.SelectControl;
var InspectorControls = wp.blockEditor.InspectorControls;
/**
 * Create an Inspector Controls wrapper Component
 */

var Inspector = /*#__PURE__*/function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default()(Inspector, _Component);

  var _super = _createSuper(Inspector);

  function Inspector() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, Inspector);

    return _super.apply(this, arguments);
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(Inspector, [{
    key: "render",
    value: function render() {
      var _this$props = this.props,
          _this$props$attribute = _this$props.attributes,
          text = _this$props$attribute.text,
          level = _this$props$attribute.level,
          css_class = _this$props$attribute.css_class,
          setAttributes = _this$props.setAttributes;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(TextControl, {
        label: __('Button Text', 'paid-memberships-pro'),
        help: __('Text for checkout button', 'paid-memberships-pro'),
        value: text,
        onChange: function onChange(text) {
          return setAttributes({
            text: text
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(SelectControl, {
        label: __('Level', 'paid-memberships-pro'),
        help: __('The level to link to for checkout button', 'paid-memberships-pro'),
        value: level,
        onChange: function onChange(level) {
          return setAttributes({
            level: level
          });
        },
        options: window.pmpro.all_level_values_and_labels
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(TextControl, {
        label: __('CSS Class', 'paid-memberships-pro'),
        help: __('Additional styling for checkout button', 'paid-memberships-pro'),
        value: css_class,
        onChange: function onChange(css_class) {
          return setAttributes({
            css_class: css_class
          });
        }
      })));
    }
  }]);

  return Inspector;
}(Component);



/***/ }),

/***/ "./blocks/checkout-page/block.js":
/*!***************************************!*\
  !*** ./blocks/checkout-page/block.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _inspector__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./inspector */ "./blocks/checkout-page/inspector.js");



function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

/**
 * Block: PMPro Membership Checkout
 *
 * Displays the Membership Checkout form.
 *
 */

/**
 * Block dependencies
 */

/**
 * Internal block libraries
 */

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var SelectControl = wp.components.SelectControl;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/checkout-page', {
  title: __('Membership Checkout Form', 'paid-memberships-pro'),
  description: __('Displays the Membership Checkout form.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'list-view'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {
    pmpro_default_level: {
      type: 'string',
      source: 'meta',
      meta: 'pmpro_default_level'
    }
  },
  edit: function edit(props) {
    var pmpro_default_level = props.attributes.pmpro_default_level,
        className = props.className,
        setAttributes = props.setAttributes,
        isSelected = props.isSelected;
    return [isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_inspector__WEBPACK_IMPORTED_MODULE_2__["default"], _objectSpread({
      setAttributes: setAttributes
    }, props)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Checkout Form', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("hr", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(SelectControl, {
      label: __('Membership Level', 'paid-memberships-pro'),
      value: pmpro_default_level,
      onChange: function onChange(pmpro_default_level) {
        return setAttributes({
          pmpro_default_level: pmpro_default_level
        });
      },
      options: window.pmpro.all_level_values_and_labels
    }))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/checkout-page/inspector.js":
/*!*******************************************!*\
  !*** ./blocks/checkout-page/inspector.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Inspector; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);







function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, result); }; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var Component = wp.element.Component;
var _wp$components = wp.components,
    PanelBody = _wp$components.PanelBody,
    PanelRow = _wp$components.PanelRow,
    SelectControl = _wp$components.SelectControl;
var InspectorControls = wp.blockEditor.InspectorControls;
/**
 * Create an Inspector Controls wrapper Component
 */

var Inspector = /*#__PURE__*/function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default()(Inspector, _Component);

  var _super = _createSuper(Inspector);

  function Inspector() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, Inspector);

    return _super.apply(this, arguments);
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(Inspector, [{
    key: "render",
    value: function render() {
      var _this$props = this.props,
          pmpro_default_level = _this$props.attributes.pmpro_default_level,
          setAttributes = _this$props.setAttributes;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(SelectControl, {
        label: __('Membership Level', 'paid-memberships-pro'),
        help: __('Choose a default level for Membership Checkout.', 'paid-memberships-pro'),
        value: pmpro_default_level,
        onChange: function onChange(pmpro_default_level) {
          return setAttributes({
            pmpro_default_level: pmpro_default_level
          });
        },
        options: [''].concat(window.pmpro.all_level_values_and_labels)
      })));
    }
  }]);

  return Inspector;
}(Component);



/***/ }),

/***/ "./blocks/confirmation-page/block.js":
/*!*******************************************!*\
  !*** ./blocks/confirmation-page/block.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership Confirmation
 *
 * Displays the Membership Confirmation template.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/confirmation-page', {
  title: __('Membership Confirmation Page', 'paid-memberships-pro'),
  description: __('Displays the member\'s Membership Confirmation after Membership Checkout.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'yes'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Confirmation Page', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/i18n.js":
/*!************************!*\
  !*** ./blocks/i18n.js ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports) {

wp.i18n.setLocaleData({
  '': {}
}, 'paid-memberships-pro');

/***/ }),

/***/ "./blocks/invoice-page/block.js":
/*!**************************************!*\
  !*** ./blocks/invoice-page/block.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership Invoices
 *
 * Displays the Membership Invoices template.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/invoice-page', {
  title: __('Membership Invoice Page', 'paid-memberships-pro'),
  description: __('Displays the member\'s  Membership Invoices.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'archive'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Invoices', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/levels-page/block.js":
/*!*************************************!*\
  !*** ./blocks/levels-page/block.js ***!
  \*************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership Levels
 *
 * Displays the Membership Levels template.
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/levels-page', {
  title: __('Membership Levels List', 'paid-memberships-pro'),
  description: __('Displays a list of Membership Levels. To change the order, go to Memberships > Settings > Levels.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'list-view'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  supports: {},
  attributes: {},
  edit: function edit() {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Paid Memberships Pro', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __('Membership Levels List', 'paid-memberships-pro')))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/login/block.js":
/*!*******************************!*\
  !*** ./blocks/login/block.js ***!
  \*******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _inspector__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./inspector */ "./blocks/login/inspector.js");


/**
 * Block: PMPro Login Form
 *
 * Add a login form to any page or post.
 *
 */

/**
 * Block dependencies
 */

/**
 * Internal block libraries
 */

var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var Fragment = wp.element.Fragment;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType("pmpro/login-form", {
  title: __("Log in Form", "paid-memberships-pro"),
  description: __("Displays a Log In Form for Paid Memberships Pro.", "paid-memberships-pro"),
  category: "pmpro",
  icon: {
    background: "#2997c8",
    foreground: "#ffffff",
    src: "unlock"
  },
  keywords: [__("pmpro", "paid-memberships-pro"), __("login", "paid-memberships-pro"), __("form", "paid-memberships-pro"), __("log in", "paid-memberships-pro")],
  supports: {},
  edit: function edit(props) {
    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Fragment, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_inspector__WEBPACK_IMPORTED_MODULE_1__["default"], props), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __("Paid Memberships Pro", "paid-memberships-pro")), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __("Log in Form", "paid-memberships-pro"))))];
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/login/inspector.js":
/*!***********************************!*\
  !*** ./blocks/login/inspector.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Inspector; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);







function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_4___default()(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_3___default()(this, result); }; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var Component = wp.element.Component;
var _wp$components = wp.components,
    PanelBody = _wp$components.PanelBody,
    SelectControl = _wp$components.SelectControl,
    ToggleControl = _wp$components.ToggleControl;
var InspectorControls = wp.blockEditor.InspectorControls;
/**
 * Create an Inspector Controls wrapper Component
 */

var Inspector = /*#__PURE__*/function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_2___default()(Inspector, _Component);

  var _super = _createSuper(Inspector);

  function Inspector() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, Inspector);

    return _super.apply(this, arguments);
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(Inspector, [{
    key: "render",
    value: function render() {
      var _this = this;

      var _this$props = this.props,
          attributes = _this$props.attributes,
          setAttributes = _this$props.setAttributes;
      var display_if_logged_in = attributes.display_if_logged_in,
          show_menu = attributes.show_menu,
          show_logout_link = attributes.show_logout_link,
          location = attributes.location;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(ToggleControl, {
        label: __("Display 'Welcome' content when logged in.", "paid-memberships-pro"),
        checked: display_if_logged_in,
        onChange: function onChange(value) {
          _this.props.setAttributes({
            display_if_logged_in: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(ToggleControl, {
        label: __("Display the 'Log In Widget' menu.", "paid-memberships-pro"),
        help: __("Assign the menu under Appearance > Menus."),
        checked: show_menu,
        onChange: function onChange(value) {
          _this.props.setAttributes({
            show_menu: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__["createElement"])(ToggleControl, {
        label: __("Display a 'Log Out' link.", "paid-memberships-pro"),
        checked: show_logout_link,
        onChange: function onChange(value) {
          _this.props.setAttributes({
            show_logout_link: value
          });
        }
      })));
    }
  }]);

  return Inspector;
}(Component);



/***/ }),

/***/ "./blocks/member-profile-edit/block.js":
/*!*********************************************!*\
  !*** ./blocks/member-profile-edit/block.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Member Profile Edit
 *
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType("pmpro/member-profile-edit", {
  title: __("Member Profile Edit", "paid-memberships-pro"),
  description: __("Allow member profile editing.", "paid-memberships-pro"),
  category: "pmpro",
  icon: {
    background: "#2997c8",
    foreground: "#ffffff",
    src: "admin-users"
  },
  keywords: [__("pmpro", "paid-memberships-pro"), __("member", "paid-memberships-pro"), __("profile", "paid-memberships-pro")],
  edit: function edit(props) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __("Paid Memberships Pro", "paid-memberships-pro")), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-subtitle"
    }, __("Member Profile Edit", "paid-memberships-pro")));
  },
  save: function save() {
    return null;
  }
}));

/***/ }),

/***/ "./blocks/membership/block.js":
/*!************************************!*\
  !*** ./blocks/membership/block.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);


/**
 * Block: PMPro Membership
 *
 *
 */

/**
 * Internal block libraries
 */
var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;
var _wp$components = wp.components,
    PanelBody = _wp$components.PanelBody,
    CheckboxControl = _wp$components.CheckboxControl;
var _wp$blockEditor = wp.blockEditor,
    InspectorControls = _wp$blockEditor.InspectorControls,
    InnerBlocks = _wp$blockEditor.InnerBlocks;
var all_levels = [{
  value: 0,
  label: "Non-Members"
}].concat(pmpro.all_level_values_and_labels);
/**
 * Register block
 */

/* harmony default export */ __webpack_exports__["default"] = (registerBlockType('pmpro/membership', {
  title: __('Require Membership Block', 'paid-memberships-pro'),
  description: __('Control the visibility of nested blocks for members or non-members.', 'paid-memberships-pro'),
  category: 'pmpro',
  icon: {
    background: '#2997c8',
    foreground: '#ffffff',
    src: 'visibility'
  },
  keywords: [__('pmpro', 'paid-memberships-pro')],
  attributes: {
    levels: {
      type: 'array',
      default: []
    },
    uid: {
      type: 'string',
      default: ''
    }
  },
  edit: function edit(props) {
    var _props$attributes = props.attributes,
        levels = _props$attributes.levels,
        uid = _props$attributes.uid,
        setAttributes = props.setAttributes,
        isSelected = props.isSelected;

    if (uid == '') {
      var rand = Math.random() + "";
      setAttributes({
        uid: rand
      });
    } // Build an array of checkboxes for each level.


    var checkboxes = all_levels.map(function (level) {
      function setLevelsAttribute(nowChecked) {
        if (nowChecked && !levels.some(function (levelID) {
          return levelID == level.value;
        })) {
          // Add the level.
          var newLevels = levels.slice();
          newLevels.push(level.value + '');
          setAttributes({
            levels: newLevels
          });
        } else if (!nowChecked && levels.some(function (levelID) {
          return levelID == level.value;
        })) {
          // Remove the level.
          var _newLevels = levels.filter(function (levelID) {
            return levelID != level.value;
          });

          setAttributes({
            levels: _newLevels
          });
        }
      }

      return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(CheckboxControl, {
        label: level.label,
        checked: levels.some(function (levelID) {
          return levelID == level.value;
        }),
        onChange: setLevelsAttribute
      })];
    });
    return [isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      class: "pmpro-block-inspector-scrollable"
    }, checkboxes))), isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-require-membership-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Require Membership', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, null, checkboxes), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InnerBlocks, {
      renderAppender: function renderAppender() {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InnerBlocks.ButtonBlockAppender, null);
      },
      templateLock: false
    })), !isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "pmpro-block-require-membership-element"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
      className: "pmpro-block-title"
    }, __('Require Membership', 'paid-memberships-pro')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InnerBlocks, {
      renderAppender: function renderAppender() {
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InnerBlocks.ButtonBlockAppender, null);
      },
      templateLock: false
    }))];
  },
  save: function save(props) {
    var className = props.className;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: className
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InnerBlocks.Content, null));
  }
}));

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

module.exports = _assertThisInitialized;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

module.exports = _classCallCheck;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/createClass.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/createClass.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

module.exports = _createClass;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

module.exports = _defineProperty;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _getPrototypeOf(o) {
  module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  module.exports["default"] = module.exports, module.exports.__esModule = true;
  return _getPrototypeOf(o);
}

module.exports = _getPrototypeOf;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/inherits.js":
/*!*********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/inherits.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js");

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) setPrototypeOf(subClass, superClass);
}

module.exports = _inherits;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "./node_modules/@babel/runtime/helpers/typeof.js")["default"];

var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized.js */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");

function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return assertThisInitialized(self);
}

module.exports = _possibleConstructorReturn;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _setPrototypeOf(o, p) {
  module.exports = _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  module.exports["default"] = module.exports, module.exports.__esModule = true;
  return _setPrototypeOf(o, p);
}

module.exports = _setPrototypeOf;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/typeof.js":
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    module.exports = _typeof = function _typeof(obj) {
      return typeof obj;
    };

    module.exports["default"] = module.exports, module.exports.__esModule = true;
  } else {
    module.exports = _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };

    module.exports["default"] = module.exports, module.exports.__esModule = true;
  }

  return _typeof(obj);
}

module.exports = _typeof;
module.exports["default"] = module.exports, module.exports.__esModule = true;

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ })

/******/ });
//# sourceMappingURL=blocks.build.js.map