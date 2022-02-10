"use strict";

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
        SelectControl = _wp$components.SelectControl,
        registerBlockType = wp.blocks.registerBlockType;

    registerBlockType('rtec/rtec-form-block', {
        title: rtec_block_editor.i18n.registration,
        icon: 'calendar',
        category: 'widgets',
        attributes: {
            noNewChanges: {
                type: 'boolean',
            },
            isTribeEvent: {
                type: 'boolean',
            },
            eventID: {
                type: 'string',
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
                shortcodeSettings = _props$attributes$sho === void 0 ? rtec_block_editor.shortcodeSettings : _props$attributes$sho,
                _props$attributes$eve= _props$attributes.eventID,
                eventID = _props$attributes$eve === void 0 ? 0 : _props$attributes$eve,
                _props$attributes$cli = _props$attributes.noNewChanges,
                noNewChanges = _props$attributes$cli === void 0 ? true : _props$attributes$cli,
                _props$attributes$exe = _props$attributes.executed,
                executed = _props$attributes$exe === void 0 ? false : _props$attributes$exe,
                isTribeEvent = (typeof TEC !== 'undefined'),
                eventOptions = rtec_block_editor.upcoming.map( value => (
                    { value: value.id, label: value.title }
                    ) );

            setAttributes({
                isTribeEvent: isTribeEvent,
            });

            function selectEvent( value ) {
                setAttributes( { eventID: value } );
            }

            function setState(shortcodeSettingsContent) {
                setAttributes({
                    noNewChanges: false,
                    shortcodeSettings: shortcodeSettingsContent
                });
            }

            function previewClick(content) {
                setAttributes({
                    noNewChanges: true,
                    executed: false,
                });
            }
            function afterRender() {
                // no way to run a script after AJAX call to get feed so we just try to execute it on a few intervals
                if (! executed
                    || typeof window.rtecIsInit === 'undefined') {
                    window.rtecIsInit = true;
                    setTimeout(function() { if (typeof rtecInit !== 'undefined') {rtecInit();}},1000);
                    setTimeout(function() { if (typeof rtecInit !== 'undefined') {rtecInit();}},2000);
                    setTimeout(function() { if (typeof rtecInit !== 'undefined') {rtecInit();}},3000);
                    setTimeout(function() { if (typeof rtecInit !== 'undefined') {rtecInit();}},5000);
                    setTimeout(function() { if (typeof rtecInit !== 'undefined') {rtecInit();}},10000);
                }
                setAttributes({
                    executed: true,
                });
            }

            var jsx;
            if (!isTribeEvent && parseInt(eventID) > 0) {
                var eventTitle = '';
                for (var i = 0; i < rtec_block_editor.upcoming.length; i++) {
                    if (parseInt(rtec_block_editor.upcoming[i].id) === parseInt(eventID)) {
                        eventTitle = rtec_block_editor.upcoming[i].title
                    }
                }
                jsx = [React.createElement(InspectorControls, {
                    key: "rtec-gutenberg-setting-selector-inspector-controls"
                }, React.createElement(PanelBody, {
                    title: rtec_block_editor.i18n.addSettings
                }, React.createElement("p", {
                    className: "rtec-gb-event-title-wrap"
                }, React.createElement("strong", {
                    className: "rtec-gb-event-title"
                }, eventTitle)), React.createElement(TextareaControl, {
                    key: "rtec-gutenberg-settings",
                    className: "rtec-gutenberg-settings",
                    label: rtec_block_editor.i18n.shortcodeSettings,
                    help: rtec_block_editor.i18n.example + ": 'attendeelist=\"false\" showheader=\"true\"'",
                    value: shortcodeSettings,
                    onChange: setState
                }), React.createElement(Button, {
                    key: "rtec-gutenberg-preview",
                    className: "rtec-gutenberg-preview",
                    onClick: previewClick,
                    isDefault: true
                }, rtec_block_editor.i18n.preview)))];
            } else {
                jsx = [React.createElement(InspectorControls, {
                    key: "rtec-gutenberg-setting-selector-inspector-controls"
                }, React.createElement(PanelBody, {
                    title: rtec_block_editor.i18n.addSettings
                }, React.createElement(TextareaControl, {
                    key: "rtec-gutenberg-settings",
                    className: "rtec-gutenberg-settings",
                    label: rtec_block_editor.i18n.shortcodeSettings,
                    help: rtec_block_editor.i18n.example + ": 'attendeelist=\"false\" showheader=\"true\"'",
                    value: shortcodeSettings,
                    onChange: setState
                }), React.createElement(Button, {
                    key: "rtec-gutenberg-preview",
                    className: "rtec-gutenberg-preview",
                    onClick: previewClick,
                    isDefault: true
                }, rtec_block_editor.i18n.preview)))];
            }

            if ( !isTribeEvent && parseInt(eventID) === 0) {
                jsx.push(React.createElement(Placeholder, {
                    key: "rtec-gutenberg-setting-selector-select-wrap",
                    className: "rtec-gutenberg-setting-selector-select-wrap"
                    },React.createElement(SelectControl, {
                        label: rtec_block_editor.i18n.whichevent,
                        value: eventID,
                        options: eventOptions,
                        onChange: selectEvent
                })));
            } else if (noNewChanges) {
                afterRender();
                jsx.push(React.createElement(ServerSideRender, {
                    key: "rtec-registration-form/rtec-registration-form",
                    block: "rtec/rtec-form-block",
                    attributes: props.attributes,
                }));
            } else {
                props.attributes.noNewChanges = false;
                jsx.push(React.createElement(Placeholder, {
                    key: "rtec-gutenberg-setting-selector-select-wrap",
                    className: "rtec-gutenberg-setting-selector-select-wrap"
                }, React.createElement(Button, {
                    key: "rtec-gutenberg-preview",
                    className: "rtec-gutenberg-preview",
                    onClick: previewClick,
                    isDefault: true
                }, rtec_block_editor.i18n.preview)));
            }

            return jsx;
        },
        save: function save() {
            return null;
        }
    });
})();