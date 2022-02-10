(function ($) {

	/**
     * Use this file to register a module helper that
     * adds additional logic to the settings form. The
     * method 'FLBuilder._registerModuleHelper' accepts
     * two parameters, the module slug (same as the folder name)
     * and an object containing the helper methods and properties.
     */
	FLBuilder.registerModuleHelper('pp-facebook-page', {

		 /**
         * The 'rules' property is where you setup
         * validation rules that are passed to the jQuery
         * validate plugin (http://jqueryvalidation.org).
         *
         * @property rules
         * @type object
         */
		rules: {
			page_url: {
				required: true,
			},
		},

	});

})(jQuery);
