<# if (!map.editable && mappl10n.options.directions != 'google') { #>
	<div class='mapp-directions'>
		<span class='mapp-close' data-mapp-action='dir-cancel'></span>
		<div>
			<input class='mapp-dir-saddr' tabindex='1' placeholder='<?php _e("My location", 'mappress-google-maps-for-wordpress');?>' />
			<span data-mapp-action='dir-swap' class='mapp-dir-arrows'></span>
		</div>

		<div>
			<input class='mapp-dir-daddr' tabindex='2'/>
		</div>

		<div class='mapp-dir-toolbar'>
			<span class='mapp-submit-button' data-mapp-action='dir-get'><?php _e('Get Directions', 'mappress-google-maps-for-wordpress'); ?></span>
		</div>
		<div class='mapp-dir-renderer'></div>
	</div>
<# } #>