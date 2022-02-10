<script type='text/template' id='mapp-tmpl-edit-popup'>
	<div class='mapp-poi-header'>
		<input class='mapp-poi-title' type='text' value='{{poi.title}}'>
		<# if (mappl10n.options.pro && !poi.type) { #>
			<div id='mapp-poi-iconpicker'></div>
		<# } else if (poi.isPoly()) { #>
			<div id='mapp-poi-colorpicker'></div>
		<# } #>
	</div>

	<# if (poi.type == 'kml') { #>
		<div class='mapp-poi-kml'>
			<input class='mapp-poi-url' type='text' readonly='readonly' value='<# print( (poi.kml) ? poi.kml.url : '' );#>'/>
		</div>
	<# } #>

	<div class='mapp-poi-editor-toolbar'>
		<div class='mapp-poi-editor-tabs'>
			<a class='mapp-poi-visual'><?php _e('Visual', 'mappress-google-maps-for-wordpress'); ?></a> | <a class='mapp-poi-html'><?php _e('HTML', 'mappress-google-maps-for-wordpress');?></a>
			</div>
			<a href='#' class='insert-media add_media' data-editor='mapp-poi-body'><?php _e('Add Media', 'mappress-google-maps-for-wordpress');?></a>
		</div>
	</div>

	<div class='mapp-poi-main'>
		<textarea id='mapp-poi-body' class='mapp-poi-body' rows='10'>{{ poi.body }}</textarea>
	</div>

	<div class='mapp-poi-toolbar'>
		<button data-mapp-poi='save' class='button button-primary'><?php _e('Save', 'mappress-google-maps-for-wordpress'); ?></button>
		<button data-mapp-poi='cancel' class='button'><?php _e('Cancel', 'mappress-google-maps-for-wordpress'); ?></button>
		<a href='#' data-mapp-poi='remove'><?php _e('Delete', 'mappress-google-maps-for-wordpress');?></a>
	</div>
</script>