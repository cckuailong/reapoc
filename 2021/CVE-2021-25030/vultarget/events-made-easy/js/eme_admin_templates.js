jQuery(document).ready( function($) {

        $('#TemplatesTableContainer').jtable({
            title: emetemplates.translate_templates,
            paging: true,
            sorting: true,
            jqueryuiTheme: true,
            defaultSorting: 'name ASC',
            selecting: true, //Enable selecting
            multiselect: true, //Allow multiple selecting
            selectingCheckboxes: true, //Show checkboxes on first column
            selectOnRowClick: true,
            deleteConfirmation: function(data) {
               data.deleteConfirmMessage = emetemplates.translate_pressdeletetoremove + ' "' + data.record.name + '"';
            },
            actions: {
                listAction: ajaxurl+'?action=eme_templates_list',
                deleteAction: ajaxurl+'?action=eme_manage_templates&do_action=deleteTemplates&eme_admin_nonce='+emetemplates.translate_adminnonce
            },
            fields: {
                id: {
                    key: true,
		    title: emetemplates.translate_id
                },
                name: {
                    visibility: 'fixed',
		    title: emetemplates.translate_name
                },
                description: {
		    title: emetemplates.translate_description
                },
                type: {
		    title: emetemplates.translate_type
                }
            }
        });
 
        if ($('#TemplatesTableContainer').length) {
           $('#TemplatesTableContainer').jtable('load', {
               search_name: $('#search_name').val(),
               search_type: $('#search_type').val()
	   });
        }
 
        // Actions button
        $('#TemplatesActionsButton').on("click",function (e) {
	   e.preventDefault();
           var selectedRows = $('#TemplatesTableContainer').jtable('selectedRows');
           var do_action = $('#eme_admin_action').val();
           var action_ok=1;
           if (selectedRows.length > 0) {
              if ((do_action=='deleteTemplates') && !confirm(emetemplates.translate_areyousuretodeleteselected)) {
                 action_ok=0;
              }
              if (action_ok==1) {
                 $('#TemplatesActionsButton').text(emetemplates.translate_pleasewait);
                 var ids = [];
                 selectedRows.each(function () {
                   ids.push($(this).data('record')['id']);
                 });

                 var idsjoined = ids.join(); //will be such a string '2,5,7'
                 $.post(ajaxurl, {'id': idsjoined, 'action': 'eme_manage_templates', 'do_action': do_action, 'eme_admin_nonce': emetemplates.translate_adminnonce }, function() {
			 $('#TemplatesTableContainer').jtable('reload');
			 $('#TemplatesActionsButton').text(emetemplates.translate_apply);
			 if (do_action=='deleteTemplates') {
				 $('div#templates-message').html(emetemplates.translate_deleted);
				 $('div#templates-message').show();
				 $('div#templates-message').delay(3000).fadeOut('slow');
			 }
                 });
              }
           }
           // return false to make sure the real form doesn't submit
           return false;
        });
	//
        // Re-load records when user click 'load records' button.
        $('#TemplatesLoadRecordsButton').on("click",function (e) {
           e.preventDefault();
           $('#TemplatesTableContainer').jtable('load', {
               search_name: $('#search_name').val(),
               search_type: $('#search_type').val()
           });
           // return false to make sure the real form doesn't submit
           return false;
        });

	// because the fieldname contains a '[' we do it a bit differently
	var pdfsize_name='properties[pdf_size]';
	function updateShowHideStuff () {
		if ($('select#type').val() == 'pdf') {
			$('table#pdf_properties').show();
		} else {
			$('table#pdf_properties').hide();
		}
		if ($('select[name="'+pdfsize_name+'"]').val() == 'custom') {
			$('tr.template-pdf-custom').show();
		} else {
			$('tr.template-pdf-custom').hide();
		}
	}
	$('select#type').on("change",updateShowHideStuff);
	$('select[name="'+pdfsize_name+'"]').on("change",updateShowHideStuff);
	updateShowHideStuff();
});
