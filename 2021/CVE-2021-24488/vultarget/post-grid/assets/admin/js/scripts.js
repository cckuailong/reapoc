jQuery(document).ready(function($){
	$('.post-grid-meta-box .select2, #post_grid_post_settings .select2').select2({
		width: '360px',
		allowClear: true
	});
	$(document).on('change', '#post-grid .select-layout-content', function(){
			var layout = $(this).val();
			jQuery.ajax({
				type: 'POST',
				url: post_grid_ajax.post_grid_ajaxurl,
				data: {"action": "post_grid_layout_content_ajax","layout":layout},
				success: function(data){
							//jQuery(".layout-content").html(data);
							jQuery("#post-grid .layer-content").html(data);

				}

			});
	})


	$(document).on('change', '#post-grid #post_types', function(){
		post_types = $(this).val();
		grid_id = $(this).attr('grid_id');
		html = '<i class="fas fa-spin fa-spinner"></i>';
		$('#taxonomies-terms').html(html);
		jQuery.ajax(
			{
				type: 'POST',
				context: this,
				url: post_grid_ajax.post_grid_ajaxurl,
				data: {"action": "post_grid_update_taxonomies_terms_by_posttypes","post_types": post_types,"grid_id": grid_id },
				success: function(response) {
					var data = JSON.parse( response );
					html = data['html'];
					$('#taxonomies-terms').html(html);
					$('#taxonomies-terms .select2').select2({
						width: '320px',
						allowClear: true
					});
				}
			});
	})




});







