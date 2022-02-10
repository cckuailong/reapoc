"use strict";
window.cffmetatrans = false;
(function () {
    var _wp = wp,
        _wp$serverSideRender = _wp.serverSideRender,
        createElement = wp.element.createElement,
        ServerSideRender = _wp$serverSideRender === void 0 ? wp.components.ServerSideRender : _wp$serverSideRender,
        _ref = wp.blockEditor || wp.editor,
        InspectorControls = _ref.InspectorControls,
        _wp$components = wp.components,
        TextareaControl = _wp$components.TextareaControl,
        Button = _wp$components.Button,
        PanelBody = _wp$components.PanelBody,
        Placeholder = _wp$components.Placeholder,
        registerBlockType = wp.blocks.registerBlockType;

    var cffIcon = createElement('svg', {
        width: 20,
        height: 20,
        viewBox: '0 0 448 512',
        className: 'dashicon'
    }, createElement('path', {
        fill: 'currentColor',
        d: 'M400 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h137.25V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.27c-30.81 0-40.42 19.12-40.42 38.73V256h68.78l-11 71.69h-57.78V480H400a48 48 0 0 0 48-48V80a48 48 0 0 0-48-48z'
    }));

    registerBlockType('cff/cff-feed-block', {
        title: 'Custom Facebook Feed',
        icon: cffIcon,
        category: 'widgets',
        attributes: {
            noNewChanges: {
                type: 'boolean',
            },
            shortcodeSettings: {
                type: 'string',
            },
            executed: {
                type: 'boolean'
            }
        },
        edit: function edit(props) {
            var _props = props,
                setAttributes = _props.setAttributes,
                _props$attributes = _props.attributes,
                _props$attributes$sho = _props$attributes.shortcodeSettings,
                shortcodeSettings = _props$attributes$sho === void 0 ? cff_block_editor.shortcodeSettings : _props$attributes$sho,
                _props$attributes$cli = _props$attributes.noNewChanges,
                noNewChanges = _props$attributes$cli === void 0 ? true : _props$attributes$cli;

            props.attributes.shortcodeSettings = shortcodeSettings;

            function setState(shortcodeSettingsContent) {
                setAttributes({
                    noNewChanges: false,
                    shortcodeSettings: shortcodeSettingsContent
                })
            }

            function previewClick(content) {
                setAttributes({
                    noNewChanges: true,
                })
            }

            function cffGutenbergSizeVisualHeader() {
                jQuery('.cff-visual-header.cff-has-cover').each(function() {
                    var wrapperHeight = jQuery(this).find('.cff-header-hero').innerHeight(),
                        imageHeight = jQuery(this).find('.cff-header-hero img').innerHeight(),
                        wrapperWidth = jQuery(this).find('.cff-header-hero').innerWidth(),
                        imageWidth = jQuery(this).find('.cff-header-hero img').innerWidth(),
                        wrapperAspect = wrapperWidth/wrapperHeight,
                        imageAspect = imageWidth/imageHeight,
                        width = wrapperAspect < imageAspect ? wrapperHeight * imageAspect + 'px' : '100%',
                        difference = imageHeight - wrapperHeight,
                        topMargin = Math.max(0,Math.round(difference/2)),
                        leftMargin = width !== '100%' ? Math.max(0,Math.round(((wrapperHeight * imageAspect)-wrapperWidth)/2)) : 0;
                    jQuery(this).find('.cff-header-hero img').css({
                        'opacity' : 1,
                        'display' : 'block',
                        'visibility' : 'visible',
                        'max-width' : 'none',
                        'max-height' : 'none',
                        'margin-top' : - topMargin + 'px',
                        'margin-left' : - leftMargin + 'px',
                        'width' : width,
                    });
                });
            }

            function afterRender() {
                jQuery(window).resize(function () {
                    setTimeout(function(){
                        cffGutenbergSizeVisualHeader();
                    }, 500);
                });
                var executed = false;
                // no way to run a script after AJAX call to get feed so we just try to execute it on a few intervals
                setTimeout(function() { if (typeof cffGutenbergSizeVisualHeader !== 'undefined' && !executed) {cffGutenbergSizeVisualHeader();}},1000);
                setTimeout(function() { if (typeof cffGutenbergSizeVisualHeader !== 'undefined' && !executed) {cffGutenbergSizeVisualHeader();}},2000);
                setTimeout(function() { if (typeof cffGutenbergSizeVisualHeader !== 'undefined' && !executed) {cffGutenbergSizeVisualHeader();}},3000);
                setTimeout(function() { if (typeof cffGutenbergSizeVisualHeader !== 'undefined' && !executed) {cffGutenbergSizeVisualHeader();}},5000);
                setTimeout(function() { if (typeof cffGutenbergSizeVisualHeader !== 'undefined' && !executed) {cffGutenbergSizeVisualHeader();}},10000);
            }

            var jsx = [React.createElement(InspectorControls, {
                key: "cff-gutenberg-setting-selector-inspector-controls"
            }, React.createElement(PanelBody, {
                title: cff_block_editor.i18n.addSettings
            }, React.createElement(TextareaControl, {
                key: "cff-gutenberg-settings",
                className: "cff-gutenberg-settings",
                label: cff_block_editor.i18n.shortcodeSettings,
                help: cff_block_editor.i18n.example + ": 'feed=\"1\"'",
                value: shortcodeSettings,
                onChange: setState
            }), React.createElement(Button, {
                key: "cff-gutenberg-preview",
                className: "cff-gutenberg-preview",
                onClick: previewClick,
                isDefault: true
            }, cff_block_editor.i18n.preview)))];

            if (noNewChanges) {
                afterRender();
                jsx.push(React.createElement(ServerSideRender, {
                    key: "custom-facebook-feeds/custom-facebook-feeds",
                    block: "cff/cff-feed-block",
                    attributes: props.attributes,
                }));
            } else {
                props.attributes.noNewChanges = false;
                jsx.push(React.createElement(Placeholder, {
                    key: "cff-gutenberg-setting-selector-select-wrap",
                    className: "cff-gutenberg-setting-selector-select-wrap"
                }, React.createElement(Button, {
                    key: "cff-gutenberg-preview",
                    className: "cff-gutenberg-preview",
                    onClick: previewClick,
                    isDefault: true
                }, cff_block_editor.i18n.preview)));
            }

            return jsx;
        },
        save: function save() {
            return null;
        }
    });
})();