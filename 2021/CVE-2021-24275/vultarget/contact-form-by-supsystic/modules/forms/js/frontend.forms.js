var g_cfsIsPageCachedChecked = false
,	g_cfsIsPageCached = false;
var cfsForms = [];
// Make some fields types adaption for HTML5 input types support
var g_cfsFieldsAdapt = {
	date: function( $input ) {
		this._initDatePicker($input, {dateFormat: 'mm/dd/yy'});
	}
,	month: function( $input ) {
		this._initDatePicker($input, {dateFormat: 'MM, yy'});
	}
,	week: function( $input ) {
		this._initDatePicker($input, {
			dateFormat: 'mm/dd/yy'
		,	showWeek: true
		,	onSelect: function(dateText, inst) {
				var date = new Date(dateText);
				jQuery(this).val("Week " + jQuery.datepicker.iso8601Week( date )+ ', '+ date.getFullYear());
			}
		});
	}
,	time: function( $input, params ) {
		params = params || {};
		$input.timepicker( params );
	}
,	_initDatePicker: function( $input, params ) {
		params = params || {};
		$input.datepicker( params );
	}
};
function cfsForm(params) {
	params = params || {};
	this._data = params;
	this._$ = null;
	this._nonce_updated = false;
	this._beforeSubmitClbs = [];
	this.init();
}
cfsForm.prototype.init = function() {
	// Init base $shell object
	this.getShell();
	// Make HTML5 input types support
	this._bindHtml5Support();
	// Check custom error messages from form settings
	this._bindCustomErrorMsgs();
	// Make basic form preparations
	this._bindLove();
	this._checkUpdateRecaptcha();
	this._bindFieldsMatchValidation();
	this._bindReset();
	this._bindSubmit();
	// Check cached pages nonce updates
	this._checkNonceUpdate();
	// Chech if we need show custom time pickers
	this._checkTimePickers();
	// Remember that we showed this form
	this._setActionDone('show');
	// Trigger standard jquery action
	jQuery(document).trigger('cfsAfterFormInit', this);
};
cfsForm.prototype.getHtmlViewId = function() {
	return this._data.view_html_id;
};
cfsForm.prototype.getParam = function( key ) {
	var setVal = false
	,	keys = arguments;
	if(keys && keys.length) {
		for(var i = 0; i < keys.length; i++) {
			var k = keys[ i ];
			if(i) {
				setVal = (setVal && setVal[ k ]) ? setVal[ k ] : false;
			} else {
				setVal = this._data.params[ k ];
			}
		}
	}
	return setVal;
};
cfsForm.prototype.getParams = function() {
	return this.get('params');
};
cfsForm.prototype.get = function( key ) {
	return this._data && this._data[ key ] ? this._data[ key ] : false;
};
cfsForm.prototype.getData = function() {};
cfsForm.prototype.getFieldsByType = function( htmlType ) {
	return this.getFieldsBy('html', htmlType);
};
cfsForm.prototype.getFields = function() {
	return this._data.params.fields ? this._data.params.fields : false;
};
cfsForm.prototype.getFieldByName = function( name ) {
	var fields = this.getFieldsBy('name', name);
	return fields ? fields[ 0 ] : false;	//  getFieldsBy return array, here we will return only one record
};
cfsForm.prototype.getFieldsBy = function(getByKey, getByValue) {
	var res = [];
	if(this._data.params.fields) {
		for(var i = 0; i < this._data.params.fields.length; i++) {
			if(this._data.params.fields[ i ][ getByKey ] == getByValue) {
				res.push( this._data.params.fields[ i ] );
			}
		}
	}
	return res && res.length ? res : false;
};
cfsForm.prototype.getFieldInput = function( name ) {
	return this._$.find('[name^="fields['+ name+ ']"]');
};
cfsForm.prototype._bindFieldsMatchValidation = function() {
	var $bindToFields = this._$.find('[data-equal-to]');
	if($bindToFields && $bindToFields.length) {
		var self = this;
		$bindToFields.change(function(){	// Validate on field change itself
			var $this = jQuery(this)
			,	eqToName = $this.data('equal-to');
			cfsCheckFieldsMatchValidation( $this, self.getFieldInput( eqToName ), self );
		}).each(function(){	// Also repeat validation when binded-to field was changed: user enter incorrect value to field itself, but then set to binded field same value - and error should be gone
			var $this = jQuery( this )
			,	eqToName = $this.data('equal-to')
			,	$eqToField = self.getFieldInput( eqToName );
			$eqToField.data('eq-to-check', $this.data('name')).change(function(){
				var $this = jQuery(this);
				cfsCheckFieldsMatchValidation( self.getFieldInput( $this.data('eq-to-check') ), $this, self );
			});
		});
	}
};
cfsForm.prototype._checkUpdateRecaptcha = function() {
	var reCaptchFields = this.getFieldsByType('recaptcha');
	if(reCaptchFields && reCaptchFields.length) {	// if reCaptcha exists
		this._tryUpdateRecaptcha();
	}
};
cfsForm.prototype._tryUpdateRecaptcha = function() {
	cfsInitCaptcha( this._$.find('.g-recaptcha') );
};
cfsForm.prototype._bindHtml5Support = function() {
	var checkTypes = ['date', 'month', 'week', 'time'];
	for(var i = 0; i < checkTypes.length; i++) {
		var key = checkTypes[ i ];
		if(typeof(key) === 'string' && !ModernizrCfs.inputtypes[ key ]) {
			var $inputs = this._$.find('[type="'+ key+ '"]');
			if($inputs && $inputs.length) {
				g_cfsFieldsAdapt[ key ]( $inputs );
			}
		}
	}
};
cfsForm.prototype._checkTimePickers = function() {
	var fields = this.getFields();
	if( fields ) {
		for(var i = 0; i < fields.length; i++) {
			if(fields[ i ].html == 'time' 
				&& fields[ i ].time_format 
				&& fields[ i ].time_format != 'am_pm'
			) {
				var $input = this.getFieldInput( fields[ i ].name );
				if($input && $input.length) {
					if( $input.hasClass() ) {	// Time input was inited
						$input.timepicker('option', {timeFormat: 'H:i:s'});
					} else {
						$input.attr('type', 'text').timepicker({timeFormat: 'H:i'});
					}
				}
			}
		}
	}
};
cfsForm.prototype._bindCustomErrorMsgs = function() {
	var invalidError = this._data.params.tpl.field_error_invalid;
	if(invalidError && invalidError != '' && this._data.params.fields) {
		var self = this;
		for(var i = 0; i < this._data.params.fields.length; i++) {
			if(parseInt(this._data.params.fields[ i ].mandatory)) {
				var $field = this.getFieldHtml( this._data.params.fields[ i ].name );
				if($field 
					&& $field.get(0) 
					&& $field.get(0).validity	// check HTML5 validation methods existing
					&& $field.get(0).setCustomValidity
				) {
					var label = this._data.params.fields[ i ].label 
						? this._data.params.fields[ i ].label 
						: this._data.params.fields[ i ].placeholder
					,	msg = cfs_str_replace(invalidError, '[label]', label);
					$field.data('cfs-invalid-msg', msg);
					$field.get(0).oninvalid = function() {
						self._setFieldInvalidMsg( this );
					};
					$field.change(function(){
						this.setCustomValidity('');	// Clear validation error, if it need - it will be set in "oninvalid" clb
					});
				}
			}
		}
	}
};
cfsForm.prototype._setFieldInvalidMsg = function( fieldHtml ) {
	fieldHtml.setCustomValidity( jQuery(fieldHtml).data('cfs-invalid-msg') );
};
cfsForm.prototype.getFieldHtml = function( name ) {
	var $field = this._$.find('[name="fields['+ name+ ']"]');
	return $field && $field.length ? $field : false;
};
cfsForm.prototype._bindLove = function() {
	if(parseInt(toeOptionCfs('add_love_link'))) {
		this._$.append( toeOptionCfs('love_link_html') );
	}
};
cfsForm.prototype._addStat = function( action, isUnique ) {
	jQuery.sendFormCfs({
		msgElID: 'noMessages'
	,	data: {mod: 'statistics', action: 'add', id: this._data.id, type: action, is_unique: isUnique, 'connect_hash': this._data.connect_hash}
	});
};
cfsForm.prototype.getShell = function( checkExists ) {
	if(!this._$ || (checkExists && !this._$.length)) {
		this._$ = jQuery('#'+ this._data.view_html_id);
	}
	return this._$;
};
cfsForm.prototype.getStyle = function() {
	if(!this._$style) {
		this._$style = jQuery('#'+ this._data.view_html_id+ '_style');
	}
	return this._$style;
};
cfsForm.prototype.onBeforeSubmit = function(clb) {
	if(typeof(clb) === 'function')
		this._beforeSubmitClbs.push(clb);
};
cfsForm.prototype._beforeSubmit = function($form) {
	if(this._beforeSubmitClbs && this._beforeSubmitClbs.length) {
		for(var i = 0; i < this._beforeSubmitClbs.length; i++) {
			this._beforeSubmitClbs[i]($form, this);
		}
	}
};
cfsForm.prototype._bindSubmit = function() {
	var self = this;
	this._$.find('.csfForm:not(.cfsSubmitBinded)').submit(function(){
		var $submitBtn = jQuery(this).find('input[type=submit]')
		,	$form = jQuery(this)
		,	$msgEl = jQuery(this).find('.cfsContactMsg');
		self._beforeSubmit($form);
		$submitBtn.attr('disabled', 'disabled');
		self._setActionDone('submit', true);
		jQuery(this).sendFormCfs({
			msgElID: $msgEl
		,	appendData: {url: window.location.href}
		,	onSuccess: function(res){
				$form.find('input[type=submit]').removeAttr('disabled');
				if(!res.error) {
					var hideOnSubmit = self.getParam('tpl', 'hide_on_submit')
					,	$inPopup = $form.parents('.ppsPopupShell:first');
					if(hideOnSubmit === false || parseInt(hideOnSubmit)) {
						var afterRemoveClb = false;
						// If form is in PopUp - let's relocate it correctly after form html will be removed
						// so PopUp will be still in the center of the screen
						if($inPopup && $inPopup.length) {
							afterRemoveClb = function() {
								if(typeof(ppsGetPopupByViewId) === 'function') {
									_ppsPositionPopup({
										popup: ppsGetPopupByViewId( $inPopup.data('view-id') )
									});
								}
							};
						}
						self._setActionDone('submit_success', true);
						var $parentShell = jQuery($form).parents('.cfsFormShell');
						$msgEl.appendTo( $parentShell );
						var docScrollTop = jQuery('html,body').scrollTop()
						,	formShellTop = self._$.offset().top;
						if(docScrollTop > formShellTop) {	// If message will appear outside of user vision - let's scroll to it
							var scrollTo = formShellTop - $form.scrollTop() - 30;
							jQuery('html,body').animate({
								scrollTop: scrollTo
							}, g_cfsAnimationSpeed);
						}
						$form.animateRemoveCfs( g_cfsAnimationSpeed, afterRemoveClb );
					} else {
						$form.get(0).reset();	// Just clear form
					}
					if(res.data.redirect) {
						toeRedirect(res.data.redirect, parseInt(self._data.params.tpl.redirect_on_submit_new_wnd));
					}
					jQuery(document).trigger('cfsAfterFormSubmitSuccess', self);
					if($inPopup && $inPopup.length) {
						if(typeof(ppsGetPopupByViewId) === 'function') {
							jQuery(document).trigger('ppsAfterPopupsActionDone', {
								popup: ppsGetPopupByViewId( $inPopup.data('view-id') )
							,	action: 'contact'
							});
						}
					}
				} else {
					self._setActionDone('submit_error', true);
				}
			}
		});
		return false;
	}).addClass('cfsSubmitBinded');
};
cfsForm.prototype._bindReset = function() {
	var self = this;
	this._$.find('.csfForm input[type="reset"]').on('click', function(){
		var ratingWrap = self._$.find('.csfForm .cfsField_rating');

		if(ratingWrap.length) {
			ratingWrap.find('.cfsRateBtn').removeClass('active');
			ratingWrap.find('input[type="hidden"]').val('');
		}
	});
};
cfsForm.prototype._setActionDone = function( action, onlyClientSide ) {
	var actionsKey = 'cfs_actions_'+ this._data.id
	,	actions = getCookieCfs( actionsKey )
	,	isUnique = 0;
	if(!actions)
		actions = {};
	if(action == 'show' && !actions[ action ]) {
		isUnique = 1;
	}
	actions[ action ] = 1;
	var saveCookieTime = 30;
	saveCookieTime = isNaN(saveCookieTime) ? 30 : saveCookieTime;
	if(!saveCookieTime)
		saveCookieTime = null;	// Save for current session only
	setCookieCfs(actionsKey, actions, saveCookieTime);
	if(!onlyClientSide) {
		this._addStat( action, isUnique );
	}
	jQuery(document).trigger('cfsAfterFormsActionDone', this);
};
cfsForm.prototype.getId = function() {
	return this._data ? this._data.id : false;
};
// Form printing methods - maybe we will add this in future to print forms
cfsForm.prototype.printForm = function() {
	var title = 'Form Content';
	var printWnd = window.open('', title, 'height=400,width=600');
	printWnd.document.write('<html><head><title>'+ title+ '</title>');
	printWnd.document.write('</head><body >');
	printWnd.document.write( this.extractFormData() );
	printWnd.document.write('</body></html>');

	printWnd.document.close(); // necessary for IE >= 10
	printWnd.focus(); // necessary for IE >= 10

	printWnd.print();
	printWnd.close();
};
cfsForm.prototype.extractFormData = function() {
	var $chatBlock = this._$.find('.cfsForm').clone()
	,	$style = this.getStyle().clone()
	,	remove = ['.cfsInputShell', '.cfsFormFooter', '.cfsMessagesExShell', '.cfsOptBtnsShell'];
	for(var i = 0; i < remove.length; i++) {
		$chatBlock.find( remove[ i ] ).remove();
	}
	return jQuery('<div />').append( jQuery('<div id="'+ this._data.tpl.view_html_id+ '" />').append( $chatBlock ).append( $style ) ).html();
};
cfsForm.prototype.refresh = function() {
	this.getShell( true );
	this._bindSubmit();
	this._checkUpdateRecaptcha();
	jQuery(document).trigger('cfsAfterFormInit', this);
};
cfsForm.prototype._checkNonceUpdate = function() {
	if(_cfsCheckIsPageCached()) {
		this._updateNonce();
	}
};
cfsForm.prototype._updateNonce = function() {
	if(!this._nonce_updated) {
		var self = this;
		jQuery.sendFormCfs({
			msgElID: 'noMessages'
		,	data: {mod: 'forms', action: 'updateNonce', id: this._data.id, get_for: ['cfsContactForm']}
		,	onSuccess: function(res) {
				if(!res.error && res.data.update_for) {
					for(var className in res.data.update_for) {
						self._$.find('input[name="_wpnonce"]').val( res.data.update_for[ className ] );
					}
				}
			}
		});
		this._nonce_updated = true;
	}
};
// End of form printing methods
var g_cfsForms = {
	_list: []
,	add: function(params) {
		this._list.push( new cfsForm(params) );
	}
,	getById: function( id ) {
		if(this._list && this._list.length) {
			for(var i = 0; i < this._list.length; i++) {
				if(this._list[ i ].getId() == id) {
					return this._list[ i ];
				}
			}
		}
		return false;
	}
,	getByViewHtmlId: function( viewHtmlId ) {
		if(this._list && this._list.length) {
			for(var i = 0; i < this._list.length; i++) {
				if(this._list[ i ].getHtmlViewId() == viewHtmlId) {
					return this._list[ i ];
				}
			}
		}
		return false;
	}
,	getFormDataByViewHtmlId: function( viewHtmlId ) {
		if(typeof(cfsForms) !== 'undefined' && cfsForms && cfsForms.length) {
			for(var i = 0; i < cfsForms.length; i++) {
				if(cfsForms[ i ].view_html_id == viewHtmlId) {
					return cfsForms[ i ];
				}
			}
		}
		return false;
	}
,	getList: function() {
		return this._list;
	}
};
function cfsCheckInitForms(selector) {
	// New way to pass data - pass it from the form HTML to avoid multiple adding in JS
	var $formsData = selector ? jQuery(selector).find('.cfsFormDesc') : jQuery('.cfsFormDesc');
	if($formsData && $formsData.length) {
		var newFormsData = [];
		$formsData.each(function(){
			var formData = typeof(atob) === 'function' ? atob(jQuery(this).text()) : base64_decode(jQuery(this).text());
			if(formData) {
				formData = JSON.parse(formData);
				if(formData) {
					newFormsData.push( formData );
					//g_cfsForms.add( formData );
				}
			}
		});
		if(typeof(newFormsData) !== 'undefined' && newFormsData && newFormsData.length) {
			jQuery(document).trigger('cfsBeforeFormsInit', newFormsData);
			for(var i = 0; i < newFormsData.length; i++) {
				g_cfsForms.add( newFormsData[ i ] );
			}
			jQuery(document).trigger('cfsAfterFormsInit', newFormsData);
		}
	}
}
jQuery(document).ready(function(){
	// Old way - pass forms data from PHP - directly to JS with WP methods
	// It was multiple issues, on of them - compatibility with Yoast Seo that generated multiple forms data in JS
	if(typeof(cfsFormsRenderFormIter) !== 'undefined') {
		for(var i = 0; i <= cfsFormsRenderFormIter.lastIter; i++) {
			if(typeof(window['cfsForms_'+ i]) !== 'undefined') {
				cfsForms.push( window['cfsForms_'+ i] );
			}
		}
	}
	// New way to pass data - pass it from the form HTML to avoid multiple adding in JS
	cfsCheckInitForms();
	if(typeof(cfsForms) !== 'undefined' && cfsForms && cfsForms.length) {
		jQuery(document).trigger('cfsBeforeFormsInit', cfsForms);
		for(var i = 0; i < cfsForms.length; i++) {
			g_cfsForms.add( cfsForms[ i ] );
		}
		jQuery(document).trigger('cfsAfterFormsInit', cfsForms);
	}
});
/**
 * Check if page was cached by any cache plugin - by checking page comments.
 * Usually they add comments in body tag.
 */
function _cfsCheckIsPageCached() {
	if(g_cfsIsPageCachedChecked)	// It was computed before - ignore one more compilation
		return g_cfsIsPageCached;
	jQuery('*:not(iframe,video,object)').contents().filter(function(){
        return this.nodeType == 8;
    }).each(function(i, e){
		if(e.nodeValue 
			&& (e.nodeValue.indexOf('Performance optimized by W3 Total Cache') !== -1
				|| e.nodeValue.indexOf('Cached page generated by WP-Super-Cache') !== -1)
		) {
			g_cfsIsPageCached = true;
			return false;
		}
    });
	g_cfsIsPageCachedChecked = true;
	return g_cfsIsPageCached;
}
function cfsCheckFieldsMatchValidation( $fieldCheck, $fieldEqTo, form ) {
	if(typeof($fieldCheck.get(0).setCustomValidity) === 'function') {
		var val = $fieldCheck.val()
		,	eqToName = $fieldCheck.data('equal-to')
		,	eqToVal = $fieldEqTo.val();
		if(val != eqToVal) {
			var fieldEqTo = form.getFieldByName( eqToName );
			if(fieldEqTo) {
				var eqToLabel = fieldEqTo.label ? fieldEqTo.label : fieldEqTo.placeholder;
				$fieldCheck.get(0).setCustomValidity(toeLangCfs('Does not match '+ eqToLabel));
			}
		} else {
			$fieldCheck.get(0).setCustomValidity('');
		}
	}
}