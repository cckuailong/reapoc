var g_cfsPromoTplSelected = false;
jQuery(document).ready(function(){
	if(typeof(cfsOriginalForm) !== 'undefined') {	// Just changing template - for existing forms
		cfsInitChangeFormDialog();
	} else {			// Creating new forms
		cfsInitCreateFormDialog();
	}
	if(jQuery('.cfsTplPrevImg').length) {	// If on creation page
		cfsAdjustPreviewSize();
		jQuery(window).resize(function(){
			cfsAdjustPreviewSize();
		});
	}
});

function cfsAdjustPreviewSize() {
	var shellWidth = parseInt(jQuery('.forms-list').width())
	,	initialMaxWidth = 400
	,	startFrom = 860
	,	endFrom = 500;
	if(shellWidth < startFrom && shellWidth > endFrom) {
		jQuery('.cfsTplPrevImg').css('max-width', initialMaxWidth - Math.floor((startFrom - shellWidth) / 2));
	} else if(shellWidth < endFrom || shellWidth > startFrom) {
		jQuery('.cfsTplPrevImg').css('max-width', initialMaxWidth);
	}
}
function cfsInitChangeFormDialog() {
	// Pre-select current Form template
	jQuery('.forms-list-item[data-id="'+ cfsOriginalForm.original_id+ '"]').addClass('active');
	var $container = jQuery('#cfsChangeTplWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#cfsChangeTplForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.forms-list-item').click(function(){
		var id = parseInt(jQuery(this).data('id'));
		if(!id) {
			g_cfsPromoTplSelected = true;
			_cfsShowPromoFormForTpl( this );
			return;
		}
		g_cfsPromoTplSelected = false;
		if(cfsOriginalForm.original_id == id) {
			var dialog = jQuery('<div />').html(toeLangCfs('This is the same template that was used for Form before.')).dialog({
				modal:    true
			,	width: 480
			,	height: 180
			,	buttons: {
					OK: function() {
						dialog.dialog('close');
					}
				}
			,	close: function() {
					dialog.remove();
				}
			});
			return false;
		}
		jQuery('#cfsChangeTplForm').find('[name=id]').val( cfsOriginalForm.id );
		jQuery('#cfsChangeTplForm').find('[name=new_tpl_id]').val( id );
		jQuery('#cfsChangeTplNewLabel').html( jQuery(this).find('.cfsTplLabel').html() )
		jQuery('#cfsChangeTplMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#cfsChangeTplForm').submit(function(){
		jQuery(this).sendFormCfs({
			msgElID: 'cfsChangeTplMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function cfsInitCreateFormDialog() {
	jQuery('.forms-list-item').click(function(){
		var id = parseInt(jQuery(this).data('id'));
		jQuery('.forms-list-item').removeClass('active');
		jQuery(this).addClass('active');
		if(id) {
			jQuery('#cfsCreateFormFrm').find('[name=original_id]').val( jQuery(this).data('id') );
		}
		if(id) {
			g_cfsPromoTplSelected = false;
			return false;
		} else {
			g_cfsPromoTplSelected = true;
		}
	});
	jQuery('#cfsCreateFormFrm').submit(function(){
		if(g_cfsPromoTplSelected) {
			_cfsShowPromoFormForTpl();
			return false;
		}
		jQuery(this).sendFormCfs({
			btn: jQuery(this).find('button')
		,	msgElID: 'cfsCreateFormMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function _cfsShowPromoFormForTpl( $tplItem ) {
	var $proOptWnd = cfsGetMainPromoForm()
	,	selectedTplHref = $tplItem 
			? jQuery($tplItem).find('a.cfsPromoTplBtn').attr('href') 
			: jQuery('.forms-list-item.active a.cfsPromoTplBtn').attr('href');
	jQuery('#cfsOptInProWnd a').attr('href', selectedTplHref);
	$proOptWnd.dialog('open');
	jQuery('#cfsOptWndTemplateTxt').show();
	jQuery('#cfsOptWndOptionTxt').hide();
}
function cfsFormRemoveRow(id, link) {
	var tblId = jQuery(link).parents('table.ui-jqgrid-btable:first').attr('id');
	if(confirm(toeLangCfs('Are you sure want to remove "'+ cfsGetGridColDataById(id, 'label', tblId)+ '" Pop-Up?'))) {
		jQuery.sendFormCfs({
			btn: link
		,	data: {mod: 'forms', action: 'remove', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#'+ tblId).trigger( 'reloadGrid' );
				}
			}
		});
	}
}