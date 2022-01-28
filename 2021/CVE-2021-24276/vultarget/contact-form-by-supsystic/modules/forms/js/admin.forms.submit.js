var g_cfsFormsSubmit = {
	_$mainShell: null
,	init: function() {
		var self = this;
		this._$mainShell = jQuery('#cfsFormSubmitToListShell');
		jQuery('.cfsFormSubmitAddOpt').click(function(){
			self.addSubmitOpt();
			return false;
		});
	}
,	addSubmitOpt: function( params ) {
		params = params || {};
		var $shell = jQuery((params.$shellClone ? params.$shellClone : jQuery('#cfsFormSubmitToShellEx'))).clone().removeAttr('id');
		$shell.appendTo( this._$mainShell );
		$shell.find('input,textarea,select').removeAttr('disabled');
		if(params.data) {
			this.fillInSubmitOpts( params.data, $shell );
		}
		this._switchCc( $shell, true );
		this.updateSortOrder();
		this.initOptShellBtns( $shell );
		cfsInitTooltips( $shell );
		// Init rich text editor
		if(typeof(tinymce) !== 'undefined' && tinymce && tinymce.init) {
			var $editTxt = $shell.find('textarea[name*="[msg]"]')
			,	editTxtId = 'cfsMsgTxtEdit_'+ Math.floor(Math.random() * (99999999));
			$editTxt.attr('id', editTxtId);
			tinymce.init({
				selector: '#'+ editTxtId
			,	plugins: 'directionality fullscreen image link media charmap hr lists textcolor colorpicker'
			,	toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat'
			});
			setTimeout(function(){
				var editor = tinymce.get(editTxtId);
				if(editor) {
					editor.on('change', function(){
						jQuery('#'+ editTxtId).val( this.getContent() );
					});
				}
			}, 1000);
		}
	}
,	removeSubmitOptBtnClk: function( $shell ) {
		var self = this;
		$shell.animateRemoveCfs(g_cfsAnimationSpeed, function(){
			self.updateSortOrder();
		});
	}
,	initOptShellBtns: function( $shell ) {
		var self = this;
		$shell.find('.cfsFormSubmitToAddCcBtn').click(function(){
			self._switchCc( $shell );
			return false;
		});
		$shell.find('.cfsFormSubmitToRemoveBtn').click(function(){
			self.removeSubmitOptBtnClk( $shell );
			return false;
		});
	}
,	_switchCc: function( $shell, direct ) {
		var $ccShell = $shell.find('.cfsFormSubmitToCcShell')
		,	$enbCcInp = $shell.find('[name*="[enb_cc]"]')
		,	$enbCcBtn = $shell.find('.cfsFormSubmitToAddCcBtn')
		,	enbCc = parseInt($enbCcInp.val());
		if(direct) {
			enbCc = !enbCc;
		}
		if(enbCc) {
			$enbCcBtn.find('.cfsOnOffBtnLabel').html($enbCcBtn.data('on-txt'));
			$enbCcBtn.find('i.fa').removeClass('fa-minus').addClass('fa-plus');
			$ccShell.hide( g_cfsAnimationSpeed );
		} else {
			$enbCcBtn.find('.cfsOnOffBtnLabel').html($enbCcBtn.data('off-txt'));
			$enbCcBtn.find('i.fa').removeClass('fa-plus').addClass('fa-minus');
			$ccShell.show( g_cfsAnimationSpeed );
		}
		$enbCcInp.val(enbCc ? 0 : 1);
	}
,	fillInSubmitOpts: function( data, $shell ) {
		for(var key in data) {
			var $input = $shell.find('[name*="['+ key+ ']"]');
			if($input && $input.length) {
				var value = data[ key ];
				if(key == 'to' && value == 'admin@mail.com') continue;
				$input.val( value );
			}
		}
	}
,	updateSortOrder: function() {
		var $rows = this._$mainShell.find('.cfsFormSubmitToShell:not(#cfsFormSubmitToShellEx)')
		,	i = 0;
		$rows.each(function(){
			var $inputs = jQuery(this).find('[name^="params[submit]"]');
			$inputs.each(function(){
				var name = jQuery(this).attr('name');
				jQuery(this).attr('name', name.replace(/(\[submit\]\[\]|\[submit\]\[\d+\])/g, '[submit]['+ i+ ']'));
			});
			i++;
		});
	}
,	haveSubmitData: function() {
		return this._$mainShell.find('.cfsFormSubmitToShell:not(#cfsFormSubmitToShellEx)').length;
	}
};
jQuery(document).ready(function(){
	// Set all exampled inputs as disabled
	jQuery('#cfsFormSubmitToShellEx').find('input,textarea,selectbox').attr('disabled', 'disabled');
	g_cfsFormsSubmit.init();
	if(typeof(cfsForm) !== 'undefined'
		&& cfsForm.params
		&& cfsForm.params.submit
		&& cfsForm.params.submit.length
	) {
		var $shellClone = jQuery('#cfsFormSubmitToShellEx');

		for(var i = 0; i < cfsForm.params.submit.length; i++) {
			g_cfsFormsSubmit.addSubmitOpt({
				data: cfsForm.params.submit[ i ]
			,	$shellClone: $shellClone
			});
		}
	} else {	// Add at least one send option - because this is contact form in the end, right?:)
		g_cfsFormsSubmit.addSubmitOpt();
	}
	g_cfsFormsSubmit.updateSortOrder();
	// Test email functionality
	jQuery('.cfsTestEmailFuncBtn').click(function(){
		jQuery.sendFormCfs({
			btn: this
		,	data: {mod: 'mail', action: 'testEmail', test_email: jQuery('#cfsFormEditForm input[name="params[tpl][test_email]"]').val()}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('.cfsTestEmailWasSent').slideDown( g_cfsAnimationSpeed );
				}
			}
		});
		return false;
	});
	jQuery('.cfsContactsExportBtn').click(function(){
		var $this = jQuery(this)
		,	originalHref = $this.data('original-href');
		if(!originalHref) {
			originalHref = $this.attr('href');
			$this.data('original-href', originalHref);
		}
		$this.attr('href', originalHref+ '&delim='+ jQuery('#cfsContactsExportDelimTxt').val());
	});
});
