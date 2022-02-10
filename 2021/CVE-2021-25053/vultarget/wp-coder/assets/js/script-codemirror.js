/* ========= INFORMATION ============================
	- author:    Dmytro Lobov
	- url:       https://wow-estore.com
	- email:     givememoney1982@gmail.com
==================================================== */

'use strict';
(function ($) {
    $(function () {
        const editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
        const codemirror_gen =
            {
                "indentUnit": 2,
                "indentWithTabs": true,
                "inputStyle": "contenteditable",
                "lineNumbers": true,
                "lineWrapping": true,
                "styleActiveLine": true,
                "continueComments": true,
                "extraKeys": {
                    "Ctrl-Space": "autocomplete",
                    "Ctrl-\/": "toggleComment",
                    "Cmd-\/": "toggleComment",
                    "Alt-F": "findPersistent",
                    "Ctrl-F": "findPersistent",
                    "Cmd-F": "findPersistent"
                },
                "direction": "ltr",
                "gutters": ["CodeMirror-lint-markers"],
                "lint": true,
                "autoCloseBrackets": true,
                "autoCloseTags": true,
                "matchTags": {
                    "bothTags": true
                },
                "tabSize": 2,

            };

        if ($('#wow_html_code').length) {
            let codemirror_el =
                {
                    "tagname-lowercase": true,
                    "attr-lowercase": true,
                    "attr-value-double-quotes": false,
                    "doctype-first": false,
                    "tag-pair": true,
                    "spec-char-escape": true,
                    "id-unique": true,
                    "src-not-empty": true,
                    "attr-no-duplication": true,
                    "alt-require": true,
                    "space-tab-mixed-disabled": "tab",
                    "attr-unsafe-chars": true,
                    "mode": 'htmlmixed',

                };

            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                codemirror_gen,
                codemirror_el,
            );

            let editor = wp.codeEditor.initialize($('#wow_html_code'), editorSettings);
        }

        if ($('#wow_css_code').length) {
            let codemirror_el =
                {


                    "mode": 'css',
                };

            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                codemirror_gen,
                codemirror_el,
            );

            let editor = wp.codeEditor.initialize($('#wow_css_code'), editorSettings);
        }
        if ($('#wow_js_code').length) {
            let codemirror_el =
                {

                    "boss": true,
                    "curly": true,
                    "eqeqeq": true,
                    "eqnull": true,
                    "es3": true,
                    "expr": true,
                    "immed": true,
                    "noarg": true,
                    "nonbsp": true,
                    "onevar": true,
                    "quotmark": "single",
                    "trailing": true,
                    "undef": true,
                    "unused": true,
                    "browser": true,
                    "globals": {
                        "_": false,
                        "Backbone": false,
                        "jQuery": true,
                        "JSON": false,
                        "wp": false
                    },
                    "mode": 'javascript',
                };

            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                codemirror_gen,
                codemirror_el,
            );

            let editor = wp.codeEditor.initialize($('#wow_js_code'), editorSettings);
        }
    });
})(jQuery);