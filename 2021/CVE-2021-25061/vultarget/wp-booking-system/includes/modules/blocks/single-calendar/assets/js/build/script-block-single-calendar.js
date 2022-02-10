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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _wp$components = wp.components,
    ServerSideRender = _wp$components.ServerSideRender,
    PanelBody = _wp$components.PanelBody,
    SelectControl = _wp$components.SelectControl;
var registerBlockType = wp.blocks.registerBlockType;
var InspectorControls = wp.editor.InspectorControls;
var __ = wp.i18n.__;

/**
 * Block inspector controls options
 *
 */

// The options for the Calendars dropdown

var calendars = [];

calendars[0] = { value: 0, label: __('Select Calendar...', 'wp-booking-system') };

for (var i = 0; i < wpbs_calendars.length; i++) {

    calendars.push({ value: wpbs_calendars[i].id, label: wpbs_calendars[i].name });
}

// The options for the Calendars dropdown
var forms = [];

forms[0] = { value: 0, label: __('Select Form...', 'wp-booking-system') };

for (var i = 0; i < wpbs_forms.length; i++) {

    forms.push({ value: wpbs_forms[i].id, label: wpbs_forms[i].name });
}


// The option for the Language dropdown
var languages = [];

languages[0] = { value: 'auto', label: __('Auto', 'wp-booking-system') };

for (var i = 0; i < wpbs_languages.length; i++) {

    languages.push({ value: wpbs_languages[i].code, label: wpbs_languages[i].name });
}

// Register the block
registerBlockType('wp-booking-system/single-calendar', {

    // The block's title
    title: 'Single Calendar',

    // The block's icon
    icon: 'calendar-alt',

    // The block category the block should be added to
    category: 'wp-booking-system',

    // The block's attributes, needed to save the data
    attributes: {

        id: {
            type: 'string'
        },

        form_id: {
            type: 'string'
        },

        title: {
            type: 'string'
        },

        legend: {
            type: 'string'
        },

        language: {
            type: 'string',
            default: 'auto'
        }

    },

    edit: function edit(props) {

        return [wp.element.createElement(ServerSideRender, {
            block: 'wp-booking-system/single-calendar',
            attributes: props.attributes }), wp.element.createElement(
            InspectorControls,
            { key: 'inspector' },
            wp.element.createElement(
                PanelBody,
                {
                    title: __('Calendar', 'wp-booking-system'),
                    initialOpen: true },
                wp.element.createElement(SelectControl, {
                    value: props.attributes.id,
                    options: calendars,
                    onChange: function onChange(new_value) {
                        return props.setAttributes({ id: new_value });
                    } })
            ),
            wp.element.createElement(
                PanelBody,
                {
                    title: __('Form', 'wp-booking-system'),
                    initialOpen: true },
                wp.element.createElement(SelectControl, {
                    value: props.attributes.form_id,
                    options: forms,
                    onChange: function onChange(new_value) {
                        return props.setAttributes({ form_id: new_value });
                    } })
            ),
            wp.element.createElement(
                PanelBody,
                {
                    title: __('Calendar Options', 'wp-booking-system'),
                    initialOpen: true },
                wp.element.createElement(SelectControl, {
                    label: __('Display Calendar Title', 'wp-booking-system'),
                    value: props.attributes.title,
                    options: [{ value: 'yes', label: __('Yes', 'wp-booking-system') }, { value: 'no', label: __('No', 'wp-booking-system') }],
                    onChange: function onChange(new_value) {
                        return props.setAttributes({ title: new_value });
                    } }),
                wp.element.createElement(SelectControl, {
                    label: __('Display Legend', 'wp-booking-system'),
                    value: props.attributes.legend,
                    options: [{ value: 'yes', label: __('Yes', 'wp-booking-system') }, { value: 'no', label: __('No', 'wp-booking-system') }],
                    onChange: function onChange(new_value) {
                        return props.setAttributes({ legend: new_value });
                    } }),
                
                wp.element.createElement(SelectControl, {
                    label: __('Language', 'wp-booking-system'),
                    value: props.attributes.language,
                    options: languages,
                    onChange: function onChange(new_value) {
                        return props.setAttributes({ language: new_value });
                    } })
            )
        )];
    },

    save: function save() {
        return null;
    }

});

jQuery(function ($) {

    /**
     * Runs every 250 milliseconds to check if a calendar was just loaded
     * and if it was, trigger the window resize to show it
     *
     */
    setInterval(function () {

        $('.wpbs-container-loaded').each(function () {

            if ($(this).attr('data-just-loaded') == '1') {
                $(window).trigger('resize');
                $(this).attr('data-just-loaded', '0');
            }
        });
    }, 250);
});

/***/ })
/******/ ]);