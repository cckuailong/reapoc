jQuery(document).ready(function($){
	$(document).on('click', '.post-grid-layout-editor .css-editor .remove', function(){
		id = $(this).parent().parent().attr('id');
		if(confirm('Do you really want to remove ?')){
			$(this).parent().parent().remove();
			$('#layout-container #item-'+id).remove();
		}
	})
	$(document).on('click', '.post-grid-layout-editor .layout-items .add-element', function(){
		var item_key = $(this).attr('item_key');
		var item_group = $(this).attr('item_group');
		var layout = $(this).attr('layout');
		var unique_id = $.now();
		$.ajax({
			type: 'POST',
			context: this,
			url:post_grid_ajax.post_grid_ajaxurl,
			data: {"action": "post_grid_layout_add_elements","item_group":item_group, "item_key":item_key,"unique_id":unique_id,"layout":layout},
			success: function(data){
				var html = JSON.parse(data)
				$('.post-grid-layout-editor #layout-container').append(html['item']);
				$('.post-grid-layout-editor .css-editor').append(html['options']);
			}
		});
	})
	$(document).on('keyup', '.post-grid-layout-editor .custom_css', function(){
		var css_style = $(this).val();
		var item_id = $(this).attr('item_id');
		$('.post-grid-layout-editor .layer-content #item-'+item_id).attr('style',css_style);
	})
});







