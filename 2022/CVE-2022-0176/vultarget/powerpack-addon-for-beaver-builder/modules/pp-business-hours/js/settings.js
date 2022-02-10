(function($){

    /**
     * Use this file to register a module helper that
     * adds additional logic to the settings form. The
     * method 'FLBuilder._registerModuleHelper' accepts
     * two parameters, the module slug (same as the folder name)
     * and an object containing the helper methods and properties.
     */
    FLBuilder._registerModuleHelper('pp-business-hours', {

        /**
         * The 'rules' property is where you setup
         * validation rules that are passed to the jQuery
         * validate plugin (http://jqueryvalidation.org).
         *
         * @property rules
         * @type object
         */
        rules: {
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

    /**
     * Validate a nested form created with 'type' => 'form'.
     */
    FLBuilder._registerModuleHelper('bh_settings_form', {

        rules: {
            bh_title: {
                required: true
            }
        },

        init: function()
        {

        },

    });

})(jQuery);
