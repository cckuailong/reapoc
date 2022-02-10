jQuery(document).ready( function($) {

        $('#FormfieldsTableContainer').jtable({
            title: emeformfields.translate_formfields,
            paging: true,
            sorting: true,
            jqueryuiTheme: true,
            defaultSorting: 'name ASC',
            selecting: true, //Enable selecting
            multiselect: true, //Allow multiple selecting
            selectingCheckboxes: true, //Show checkboxes on first column
            selectOnRowClick: true,
            deleteConfirmation: function(data) {
               data.deleteConfirmMessage = emeformfields.translate_pressdeletetoremove + ' "' + data.record.field_name + '"';
            },
            actions: {
                listAction: ajaxurl+'?action=eme_formfields_list',
                deleteAction: ajaxurl+'?action=eme_manage_formfields&do_action=deleteFormfield&eme_admin_nonce='+emeformfields.translate_adminnonce
            },
            fields: {
                field_id: {
                    key: true,
		    title: emeformfields.translate_id,
                    visibility: 'hidden'
                },
                field_name: {
		    title: emeformfields.translate_name,
                    visibility: 'fixed',
                },
                field_type: {
		    title: emeformfields.translate_type
                },
                field_required: {
		    title: emeformfields.translate_required,
                    width: '2%'
                },
                field_purpose: {
		    title: emeformfields.translate_purpose
                },
                extra_charge: {
		    title: emeformfields.translate_extracharge,
                    visibility: 'hidden'
                },
                searchable: {
		    title: emeformfields.translate_searchable,
                    visibility: 'hidden'
                },
                used: {
		    title: emeformfields.translate_used,
                    sorting: false,
                    width: '2%'
                }
            }
        });
 
        if ($('#FormfieldsTableContainer').length) {
           $('#FormfieldsTableContainer').jtable('load', {
               search_name: $('#search_name').val(),
               search_type: $('#search_type').val(),
               search_purpose: $('#search_purpose').val()
	   });
        }
 
        // Actions button
        $('#FormfieldsActionsButton').on("click",function (e) {
	   e.preventDefault();
           var selectedRows = $('#FormfieldsTableContainer').jtable('selectedRows');
           var do_action = $('#eme_admin_action').val();
           var action_ok=1;
           if (selectedRows.length > 0) {
              if ((do_action=='deleteFormfields') && !confirm(emeformfields.translate_areyousuretodeleteselected)) {
                 action_ok=0;
              }
              if (action_ok==1) {
                 $('#FormfieldsActionsButton').text(emeformfields.translate_pleasewait);
                 var ids = [];
                 selectedRows.each(function () {
                   ids.push($(this).data('record')['field_id']);
                 });

                 var idsjoined = ids.join(); //will be such a string '2,5,7'
                 $.post(ajaxurl, {'field_id': idsjoined, 'action': 'eme_manage_formfields', 'do_action': do_action, 'eme_admin_nonce': emeformfields.translate_adminnonce }, function(data) {
			 $('#FormfieldsTableContainer').jtable('reload');
			 $('#FormfieldsActionsButton').text(emeformfields.translate_apply);
			 $('div#formfields-message').html(data.htmlmessage);
			 $('div#formfields-message').show();
			 $('div#formfields-message').delay(3000).fadeOut('slow');
                 },'json');
              }
           }
           // return false to make sure the real form doesn't submit
           return false;
        });
	//
        // Re-load records when user click 'load records' button.
        $('#FormfieldsLoadRecordsButton').on("click",function (e) {
           e.preventDefault();
           $('#FormfieldsTableContainer').jtable('load', {
               search_name: $('#search_name').val(),
               search_type: $('#search_type').val(),
               search_purpose: $('#search_purpose').val()
           });
           // return false to make sure the real form doesn't submit
           return false;
        });

	function updateShowHideFormfields_type () {
		if (jQuery('select#field_type').val() == 'file') {
			$('tr#tr_extra_charge').hide();
			$('tr#tr_field_tags').hide();
			$('tr#tr_admin_tags').hide();
		} else {
			// field_purpose can be a select or hidden field
			if (jQuery('#field_purpose').val() == 'people') {
				$('tr#tr_extra_charge').hide();
			} else {
				$('tr#tr_extra_charge').show();
			}
			$('tr#tr_field_tags').show();
			$('tr#tr_admin_tags').show();
		}
	}
	function updateShowHideFormfields_purpose () {
		// field_purpose can be a select or hidden field
		if (jQuery('#field_purpose').val() == 'people') {
			$('tr#tr_extra_charge').hide();
			$('tr#tr_field_condition').show();
			$('tr#tr_export').show();
		} else {
			if (jQuery('select#field_type').val() == 'file') {
				$('tr#tr_extra_charge').hide();
			} else {
				$('tr#tr_extra_charge').show();
			}
			$('tr#tr_field_condition').hide();
			$('tr#tr_export').hide();
		}
	}
	$('select#field_purpose').on("change",updateShowHideFormfields_purpose);
	$('select#field_type').on("change",updateShowHideFormfields_type);
	updateShowHideFormfields_purpose();
	updateShowHideFormfields_type();
});
