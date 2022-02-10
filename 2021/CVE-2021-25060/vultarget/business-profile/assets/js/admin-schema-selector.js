jQuery(document).ready(function() {
	jQuery('select[name="schema_target_type"]').on('change', function() {
		if ( jQuery(this).val() == 'post_type' ) {
			jQuery('select[name="schema_target_value"]').find('option').remove();

			jQuery.each(schema_option_data.post_types, function(key, post_type) { 
				jQuery('select[name="schema_target_value"]').append('<option value="' + post_type.name + '">' + post_type.label + '</option>');
			});
		}
		else if ( jQuery(this).val() == 'post' ) {
			jQuery('select[name="schema_target_value"]').find('option').remove();

			jQuery.each(schema_option_data.posts, function(key, post) {
				jQuery('select[name="schema_target_value"]').append('<option value="' + post.ID + '">' + post.post_title + '</option>');
			});
		}
		else if ( jQuery(this).val() == 'page' ) {
			jQuery('select[name="schema_target_value"]').find('option').remove();

			jQuery.each(schema_option_data.pages, function(key, page) {
				jQuery('select[name="schema_target_value"]').append('<option value="' + page.ID + '">' + page.post_title + '</option>');
			});
		}
		else if ( jQuery(this).val() == 'post_category' ) {
			jQuery('select[name="schema_target_value"]').find('option').remove();

			jQuery.each(schema_option_data.post_categories, function(key, post_category) {
				jQuery('select[name="schema_target_value"]').append('<option value="' + post_category.term_id + '">' + post_category.name + '</option>');
			});
		}
		else if ( jQuery(this).val() == 'taxonomy' ) {
			jQuery('select[name="schema_target_value"]').find('option').remove();

			jQuery.each(schema_option_data.taxonomies, function(key, taxonomy) {
				jQuery('select[name="schema_target_value"]').append('<option value="' + taxonomy.name + '">' + taxonomy.label + '</option>');
			});
		}
		else if ( jQuery(this).val() == 'page_template' ) {
			jQuery('select[name="schema_target_value"]').find('option').remove();

			jQuery.each(schema_option_data.page_templates, function(name, file) {
				jQuery('select[name="schema_target_value"]').append('<option value="' + file + '">' + name + '</option>');
			});
		}
		else if ( jQuery(this).val() == 'global' ) {
			jQuery('select[name="schema_target_value"]').find('option').remove();
		}
	});
});