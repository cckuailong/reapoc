jQuery(document).ready(function() {
	jQuery('.bpfwp-schema-defaults-helper').on('click', function() {
		jQuery('.bpfwp-schema-defaults-helper-box').removeClass('bpfwp-hidden');
		jQuery('.bpfwp-schema-defaults-helper-background').removeClass('bpfwp-hidden');

		jQuery('.bpfwp-schema-defaults-helper-box').data('field_id', jQuery(this).data('field_id'));
	});

	jQuery('.bpfwp-schema-defaults-helper-box-exit').on('click', function() {
		jQuery('.bpfwp-schema-defaults-helper-box').addClass('bpfwp-hidden');
		jQuery('.bpfwp-schema-defaults-helper-background').addClass('bpfwp-hidden');
	});

	jQuery('.bpfwp-schema-defaults-helper-background').on('click', function() {
		jQuery('.bpfwp-schema-defaults-helper-box').addClass('bpfwp-hidden');
		jQuery('.bpfwp-schema-defaults-helper-background').addClass('bpfwp-hidden');
	});
	jQuery('.bpfwp-schema-defaults-helper-box-exit').on('click', function() {
		jQuery('.bpfwp-schema-defaults-helper-box').addClass('bpfwp-hidden');
		jQuery('.bpfwp-schema-defaults-helper-background').addClass('bpfwp-hidden');
	});

	jQuery('.bpfwp-schema-defaults-helper-option').on('click', function() {
		var field_id = jQuery(this).parent().parent().parent().parent().data('field_id');
		jQuery('input[data-field_id="' + field_id + '"].bpfwp-schema-defaults-field').val( jQuery(this).data('helper_value') );

		jQuery('.bpfwp-schema-defaults-helper-box').addClass('bpfwp-hidden');
		jQuery('.bpfwp-schema-defaults-helper-background').addClass('bpfwp-hidden');

		jQuery('.bpfwp-schema-defaults-helper-box').removeData('field_id');
	});

	jQuery('.bpfwp-add-repeatable-field').on('click', function() {
		var schema_type = jQuery(this).data('schema_type');
		var field_name = jQuery(this).data('field_name');
		var field_prefix = jQuery(this).data('field_prefix');
		var field_slug = jQuery(this).data('field_slug');

		var count_input_name = 'count_' + schema_type + '[' + field_prefix + ']';
		
		var count = parseInt(jQuery('input[name="' + count_input_name + '"]').val()) + 1;

		// up the count by 1
		jQuery('input[name="' + count_input_name + '"]').val(count);

		// add in the new set of inputs to add another of this field
		jQuery(this).siblings('h4[data-field_name="' + field_name + '"]').first().clone().insertAfter('div[data-field_name="' + field_name + '"]:last');
		jQuery(this).siblings('div[data-field_name="' + field_name + '"]').first().clone().insertAfter('h4[data-field_name="' + field_name + '"]:last');

		// update the input names so that the count parameter is correct
		jQuery(this).siblings('div[data-field_name="' + field_name + '"]').last().find('input').each(function() {
			var input_name = jQuery(this).attr('name');
			input_name = input_name.substring(0, input_name.lastIndexOf('['));
			input_name += '[' + count + ']';

			jQuery(this).attr('name', input_name);
			jQuery(this).val('');
		});

		// update the label for names so that the count parameter is correct
		jQuery(this).siblings('div[data-field_name="' + field_name + '"]').last().find('label').each(function() {
			var label_for = jQuery(this).attr('for');
			label_for = label_for.substring(0, label_for.lastIndexOf('['));
			label_for += '[' + count + ']';

			jQuery(this).attr('for', label_for);
		});

	});
});