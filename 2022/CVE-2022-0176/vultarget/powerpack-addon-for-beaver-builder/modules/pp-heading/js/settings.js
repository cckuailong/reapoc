(function($){

    /**
     * Use this file to register a module helper that
     * adds additional logic to the settings form. The
     * method 'FLBuilder._registerModuleHelper' accepts
     * two parameters, the module slug (same as the folder name)
     * and an object containing the helper methods and properties.
     */
    FLBuilder._registerModuleHelper('pp-heading', {

        /**
         * The 'rules' property is where you setup
         * validation rules that are passed to the jQuery
         * validate plugin (http://jqueryvalidation.org).
         *
         * @property rules
         * @type object
         */
        rules: {
            'font_icon_line_space': {
                number: true,
            },
            'heading_font_size': {
                number: true
            },
            'heading_line_height': {
                number: true
            },
            'heading_top_margin': {
                number: true
            },
            'sub_heading_font_size': {
                number: true
            },
            'sub_heading_line_height': {
                number: true
            },
            'sub_heading_top_margin': {
                number: true
            },
            'sub_heading_bottom_margin': {
                number: true
            },
            'separator_heading_top_margin': {
                number: true
            },
            'separator_heading_bottom_margin': {
                number: true
            },
            'line_width': {
                number: true
            },
            'line_height': {
                number: true
            },
            'font_icon_font_size': {
                number: true
            },
            'font_icon_border_width': {
                number: true
            },
            'font_icon_border_radius': {
                number: true
            },
            'font_icon_padding_top': {
                number: true
            },
            'font_icon_padding_bottom': {
                number: true
            },
            'font_icon_padding_left': {
                number: true
            },
            'font_icon_padding_right': {
                number: true
            },
            'font_icon_line_space': {
                number: true
            }
        },

        /**
         * The 'init' method is called by the builder when
         * the settings form is opened.
         *
         * @method init
         */
        init: function()
        {

        },

    });

})(jQuery);
