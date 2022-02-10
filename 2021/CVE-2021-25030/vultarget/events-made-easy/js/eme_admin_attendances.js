jQuery(document).ready( function($) {

        $('#AttendancesTableContainer').jtable({
            title: emeattendances.translate_attendance_reports,
            paging: true,
            sorting: true,
            jqueryuiTheme: true,
            defaultSorting: 'name ASC',
            selecting: false, //Enable selecting
            multiselect: false, //Allow multiple selecting
            selectingCheckboxes: false, //Show checkboxes on first column
            selectOnRowClick: true,
            deleteConfirmation: function(data) {
               data.deleteConfirmMessage = emeattendances.translate_areyousuretodeletethis;
            },
            actions: {
                listAction: ajaxurl+'?action=eme_attendances_list',
                deleteAction: ajaxurl+'?action=eme_manage_attendances&do_action=deleteAttendances&eme_admin_nonce='+emeattendances.translate_adminnonce
            },
            fields: {
                id: {
                    key: true,
		    visibility: 'hidden',
		    title: emeattendances.translate_id
                },
		creation_date: {
                    title: emeattendances.translate_attendancedate
                },
                type: {
		    title: emeattendances.translate_type
                },
                person: {
                    sorting: false,
		    title: emeattendances.translate_personinfo
                },
                related_name: {
		    title: ''
                },
            },
            toolbar: {
                items: [{
			text: emeattendances.translate_csv,
			click: function () {
				jtable_csv('#AttendancesTableContainer');
			}
		},
		{
			text: emeattendances.translate_print,
			click: function () {
				$('#AttendancesTableContainer').printElement();
			}
		}
		]
            },

        });
 
        if ($('#AttendancesTableContainer').length) {
           $('#AttendancesTableContainer').jtable('load', {
                'search_type': $('#search_type').val(),
	        'search_start_date': $('#search_start_date').val(),
                'search_end_date': $('#search_end_date').val(),

	   });
        }
 
        // Re-load records when user click 'load records' button.
        $('#AttendancesLoadRecordsButton').on("click",function (e) {
           e.preventDefault();
           $('#AttendancesTableContainer').jtable('load', {
                'search_type': $('#search_type').val(),
	        'search_start_date': $('#search_start_date').val(),
                'search_end_date': $('#search_end_date').val(),
           });
           // return false to make sure the real form doesn't submit
           return false;
        });

	// for autocomplete to work, the element needs to exist, otherwise JS errors occur
	// we check for that using length
	if ($('input[name=chooseperson]').length) {
		$('input[name=chooseperson]').autocomplete({
			source: function(request, response) {
				$.post(ajaxurl,
					{ q: request.term,
						action: 'eme_autocomplete_people',
						eme_searchlimit: 'people'
					},
					function(data){
						response($.map(data, function(item) {
							return {
								lastname: eme_htmlDecode(item.lastname),
								firstname: eme_htmlDecode(item.firstname),
								email: eme_htmlDecode(item.email),
								person_id: eme_htmlDecode(item.person_id)
							};
						}));
					}, 'json');
			},
			change: function (event, ui) {
				if(!ui.item){
					$(event.target).val("");
				}
			},
			response: function (event, ui) {
				if (!ui.content.length) {
					ui.content.push({ person_id: 0 });
					$(event.target).val("");
				}
			},
			select:function(event, ui) {
				// when a person is selected, populate related fields in this form
				if (ui.item.person_id>0) {
					$('input[name=person_id]').val(ui.item.person_id);
					$(event.target).val(ui.item.lastname+' '+ui.item.firstname+' ('+ui.item.person_id+')').attr('readonly', true).addClass('clearable x');

				}
				return false;
			},
			minLength: 2
		}).data( 'ui-autocomplete' )._renderItem = function( ul, item ) {
			if (item.person_id==0) {
				return $( '<li></li>' )
					.append('<strong>'+emeattendances.translate_nomatchperson+'</strong>')
					.appendTo( ul );
			} else {
				return $( '<li></li>' )
					.append('<a><strong>'+item.lastname+' '+item.firstname+' ('+item.person_id+')'+'</strong><br /><small>'+item.email+ '</small></a>')
					.appendTo( ul );
			}
		};

		// if manual input: set the hidden field empty again
		$('input[name=chooseperson]').on("keyup",function() {
			$('input[name=person_id]').val('');
		}).on("change",function() {
			if ($('input[name=chooseperson]').val()=='') {
				$('input[name=person_id]').val('');
				$('input[name=chooseperson]').attr('readonly', false).removeClass('clearable');
			}
		});
	}
});
