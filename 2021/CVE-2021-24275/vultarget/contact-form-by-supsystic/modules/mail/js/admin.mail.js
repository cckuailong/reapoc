jQuery(document).ready(function(){
	jQuery('#cfsMailTestForm').submit(function(){
		jQuery(this).sendFormCfs({
			btn: jQuery(this).find('button:first')
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#cfsMailTestForm').slideUp( 300 );
					jQuery('#cfsMailTestResShell').slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('.cfsMailTestResBtn').click(function(){
		var result = parseInt(jQuery(this).data('res'));
		jQuery.sendFormCfs({
			btn: this
		,	data: {mod: 'mail', action: 'saveMailTestRes', result: result}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#cfsMailTestResShell').slideUp( 300 );
					jQuery('#'+ (result ? 'cfsMailTestResSuccess' : 'cfsMailTestResFail')).slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('#cfsMailSettingsForm').submit(function(){
		jQuery(this).sendFormCfs({
			btn: jQuery(this).find('button:first')
		});
		return false; 
	});
});