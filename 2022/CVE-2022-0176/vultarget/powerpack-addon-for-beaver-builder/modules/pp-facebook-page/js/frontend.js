;(function($) {

	PPFacebookPage = function( settings ) {
		this.id			= settings.id;
		this.node		= $('.fl-node-' + settings.id)[0];
		this.settings 	= settings;

		this._init();
	};

	PPFacebookPage.prototype = {
		id: '',
		node: '',
		settings: {},

		_init: function()
		{
			this._initSDK();
			this._parse();

			$('body').delegate('.fl-builder-pp-facebook-page-settings #fl-field-layout select', 'change', $.proxy( this._change, this ));
		},

		_initSDK: function()
		{
			if ( $( '#fb-root' ).length === 0 ) {
				$('body').prepend('<div id="fb-root"></div>');
			}

			var d = document, s = 'script', id = 'facebook-jssdk';
			var js, fjs = d.getElementsByTagName(s)[0];
			
			if (d.getElementById(id)) return;
			
			js = d.createElement(s); js.id = id;
			js.src = this.settings.sdkUrl;
			fjs.parentNode.insertBefore(js, fjs);
		},

		_change: function(e)
		{
			e.stopPropagation();
			
			var node = this.node,
				tabs = $('.fl-builder-pp-facebook-page-settings #fl-field-layout select').val();

			$(node).find('.fb-page').attr({
				'data-tabs': tabs
			});

			this._parse();
		},

		_parse: function()
		{
			var node = this.node;

			// FB SDK is loaded, parse only current element
			if ('undefined' !== typeof FB) {
				FB.XFBML.parse( node );
			}
		}
	};

})(jQuery);