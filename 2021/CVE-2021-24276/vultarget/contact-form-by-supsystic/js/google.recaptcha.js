jQuery(document).ready(function(){
	// Can be uncommented - to simply init all recaptcha fields on page once document is ready, but we commented this - because we need more flexible controll in this particular plugin
	//cfsInitCaptcha();
});
function cfsInitCaptcha( $elements ) {
	if(typeof(grecaptcha) === 'undefined' || typeof(grecaptcha.render) === 'undefined') {
		// We set this function to be returned when google api loaded - onload=cfsInitCaptcha
		/*setTimeout(function(){
			cfsInitCaptcha( $elements );
		}, 500);*/
		return;
	}
	$elements = $elements ? $elements : jQuery(document).find('.g-recaptcha');
	if($elements && $elements.length) {
		$elements.each(function(){
			var $this = jQuery(this);
			if(typeof $this.data('recaptcha-widget-id') == 'undefined') {
				var dataForInit = {}
				,	elementData = $this.data()
				,	elementId = $this.attr('id');
				if(!elementId) {
					elementId = 'cfsRecaptcha_'+ (Math.floor(Math.random() * 100000));
					$this.attr('id', elementId);
				}
				if(elementData) {
					for(var key in elementData) {
						if(typeof(elementData[ key ]) === 'string') {
							dataForInit[ key ] = elementData[ key ];
						}
					}
				}
				$this.data('recaptcha-widget-id', grecaptcha.render(elementId, dataForInit));
			}
		});
	}
}
