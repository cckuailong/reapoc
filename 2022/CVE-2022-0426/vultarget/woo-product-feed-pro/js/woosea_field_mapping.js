jQuery(document).ready(function($) {

	// Dialog opener for information on mapping attributes
    	jQuery( "#dialog" ).dialog({
      		autoOpen: false,
      		show: {
        		effect: "blind",
        		duration: 1000
      		},
      		hide: {
        		effect: "explode",
        		duration: 1000
      		}
    	});

	// Add a mapping row to the table for field mappings
	jQuery(".add-field-mapping").on('click', function(){
		var nonce = $('#nonce_field_mapping').val();	
		var channel_hash = $('#channel_hash').val();
		var prevRow = $("tr.rowCount:last input[type=hidden]").val();
		var addrow_value = $('#addrow').val();

		// When user deletes all default fields
		if (prevRow === undefined){
			prevRow = 0;
		}

		var rowCount = Number(prevRow) + Number(addrow_value);
		var newrow_value = Number(addrow_value) + Number(1);
		$('#addrow').val(newrow_value);

                jQuery.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: { 
				'action': 'woosea_fieldmapping_dropdown', 
				'rowCount': rowCount, 
				'security': nonce,
				'channel_hash': channel_hash 
			}
                })
                .done(function( data ) {
                        data = JSON.parse( data );
			
			$( '#woosea-fieldmapping-table' ).append('<tr><td><input type="hidden" name="attributes[' + rowCount + '][rowCount]" value="' + rowCount + '"><input type="checkbox" name="record" class="checkbox-field"></td><td><select name="attributes[' + rowCount + '][attribute]" class="select-field">' + data.field_options + '</select></td><td><input type="text" name="attributes[' + rowCount + '][prefix]" class="input-field-medium"></td><td><select name="attributes[' + rowCount + '][mapfrom]" class="select-field">' + data.attribute_options + '</select></td><td><input type="text" name="attributes[' + rowCount + '][suffix]" class="input-field-medium"></td></tr>');

			$('.select-field').on('change', function(){
				if ($(this).val() == "static_value") {
					var rownr = $(this).closest("tr").prevAll("tr").length;
					$(this).replaceWith('<input type="text" name="attributes[' + rowCount + '][mapfrom]" class="input-field-midsmall"><input type="hidden" name="attributes[' + rowCount + '][static_value]" value="true">');
				}
			});
                })
                .fail(function( data ) {
                        console.log('Failed AJAX Call :( /// Return Data: ' + data);
                });
        });


	// Add a mapping row to the table for own mappings
	jQuery(".add-own-mapping").on('click', function(){
		var nonce = $('#nonce_field_mapping').val();
		var channel_hash = $('#channel_hash').val();
                var prevRow = $("tr.rowCount:last input[type=hidden]").val();
		var addrow_value = $('#addrow').val();

		// When user deletes all default fields
		if (prevRow === undefined){
			prevRow = 0;
		}
		
		var rowCount = Number(prevRow) + Number(addrow_value);
		var newrow_value = Number(addrow_value) + Number(1);
		$('#addrow').val(newrow_value);

                jQuery.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: { 
				'action': 'woosea_fieldmapping_dropdown', 
				'security': nonce,
				'rowCount': rowCount, 
				'channel_hash': channel_hash 
			}
                })
                .done(function( data ) {
                        data = JSON.parse( data );
			$( '#woosea-fieldmapping-table' ).append('<tr><td><input type="hidden" name="attributes[' + rowCount + '][rowCount]" value="' + rowCount + '"><input type="checkbox" name="record" class="checkbox-field"></td><td><input name="attributes[' + rowCount + '][attribute]" id="own-input-field" class="input-field"></td><td><input type="text" name="attributes[' + rowCount + '][prefix]" class="input-field-medium"></td><td><select name="attributes[' + rowCount + '][mapfrom]" class="select-field">' + data.attribute_options + '</select></td><td><input type="text" name="attributes[' + rowCount + '][suffix]" class="input-field-medium"></td></tr>');
       
			$('.select-field').on('change', function(){
				if ($(this).val() == "static_value") {
					var rownr = $(this).closest("tr").prevAll("tr").length;
					$(this).replaceWith('<input type="text" name="attributes[' + rowCount + '][mapfrom]" class="input-field-midsmall"><input type="hidden" name="attributes[' + rowCount + '][static_value]" value="true">');
				}
			});
	         })
                .fail(function( data ) {
                        console.log('Failed AJAX Call :( /// Return Data: ' + data);
                });
        });

	jQuery("#savebutton").on('click', function(){

  		$("#own-input-field").each(function() {
			var input=$(this).val();
			var re = /^[a-zA-Zа-яА-Я_-]*$/;
			if (input.indexOf("PARAM_") >= 0){
				// For Yandex, Zbozi and Heureka also accept Cyrillic characters
				var re = /.*/;
			}
                	var minLength = 2;
                	var maxLength = 50;
			
			var is_input=re.test(input);
                	// Check for allowed characters
                	if (!is_input){
				$('form').submit(function(){
      					return false;
				});	
				$('.notice').replaceWith("<div class='notice notice-error is-dismissible'><p>Sorry, when creating new custom fields only letters are allowed (so no white spaces, numbers or any other character are allowed).</p></div>");
                	} else {
				// Check for length of fieldname
				if (input.length < minLength){
					$('form').submit(function(){
      						return false;
					});	
                                	$('.notice').replaceWith("<div class='notice notice-error is-dismissible'><p>Sorry, your custom field name needs to be at least 2 letters long.</p></div>");
                        	} else if (input.length > maxLength){
        				$('form').submit(function(){
      						return false;
					});	
					$('.notice').replaceWith("<div class='notice notice-error is-dismissible'><p>Sorry, your custom field name cannot be over 20 letters long.</p></div>");
                        	} else {
					$("#fieldmapping")[0].submit();
				}
			}
		});
	});

	jQuery('.select-field').on('change', function(){
		if ($(this).val() == "static_value") {
//			var rownr = $(this).closest("tr").prevAll("tr").length;
			var rownr = $(this).closest('tr').attr("class").split(' ')[1];
			$(this).replaceWith('<input type="text" name="attributes[' + rownr + '][mapfrom]" class="input-field-midsmall"><input type="hidden" name="attributes[' + rownr + '][static_value]" value="true">');
		}
	});

	// Find and remove selected table rows
        jQuery(".delete-field-mapping").on('click', function(){
            $("table tbody").find('input[name="record"]').each(function(){
		
		if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
        });
});
