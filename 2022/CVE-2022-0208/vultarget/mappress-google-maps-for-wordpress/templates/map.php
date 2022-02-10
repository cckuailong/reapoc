<script type='text/template' id='mapp-tmpl-map'>
	<div class='mapp-wrapper'>
		<div class='mapp-content'>
			<# print(mapp.lib.template('map-header', { map : map })); #>
			<div class='mapp-main'>
				<# if (map.poiList && layout != 'inline') { print(mapp.lib.template('map-sidebar', { map : map })); } #>
				<div class='mapp-directions'></div>
				<div class='mapp-canvas-panel'>
					<div class='mapp-canvas'></div>
					<# print(mapp.lib.template('map-menu', { map : map })); #>
					<# if (mappl10n.options.ssl) { #>
						<div class='mapp-geolocate-control-wrapper'>
							<div class='mapp-geolocate-control' data-mapp-action='geolocate' title='<?php _e('Your Location', 'mappress-google-maps-for-wordpress');?>'></div>
						</div>
					<# } #>
					<div class='mapp-spinner-overlay'></div>
					<div class='mapp-spinner'></div>
				</div>
			</div>
			<# print(mapp.lib.template('map-footer', { map : map })); #>
		</div>
	</div>
	<# if (map.poiList && layout == 'inline') { print(mapp.lib.template('map-sidebar', { map : map })); } #>
</script>

<script type='text/template' id='mapp-tmpl-map-header'>
	<# const isFilterable = map.isFilterable(); #>
	<# const isSearchable = map.isSearchable(); #>
	<# if (isSearchable || isFilterable) { #>
		<div class='mapp-header'>
			<# if (isSearchable) { #>
				<div class='mapp-search'>
					<input class='mapp-places' type='text' placeholder='<?php _e('Search', 'mappress-google-maps-for-wordpress'); ?>' title='<?php _e('Enter an address, place, KML file URL, or lat,lng', 'mappress-google-maps-for-wordpress');?>'/>
					<div class='mapp-submit-button mapp-search-button'>
						<# if (map.editable) { #>
							+ <?php _e('Add to map', 'mappress-google-maps-for-wordpress'); ?>
						<# } else { #>
							<img src='<# print(mappl10n.options.baseurl + "/images/search.png"); #>'>
						<# } #>
					</div>
				</div>
			<# } #>
			<# if (isFilterable && mappl10n.options.filtersPos == 'top') { #>
				<# print(mapp.lib.template('map-filters', { map : map })); #>
			<# } #>
		</div>
	<# } #>
</script>

<script type='text/template' id='mapp-tmpl-map-sidebar'>
	<div class='mapp-sidebar'>
		<div class='mapp-sidebar-toggle' data-mapp-action='sidebar-toggle'></div>
		<# if (map.isFilterable() && mappl10n.options.filtersPos == 'list') { #>
			<# print(mapp.lib.template('map-filters', { map : map })); #>
		<# } #>
		<div class='mapp-list'></div>
	</div>
</script>

<script type='text/template' id='mapp-tmpl-map-menu'>
	<# if (!map.editable && mappl10n.options.engine != 'leaflet') { #>
		<div class='mapp-controls'>
			<div class='mapp-menu-toggle' data-mapp-action='menu-toggle' title='<?php _e('Menu', 'mappress-google-maps-for-wordpress');?>'></div>
			<div class='mapp-menu'>
				<div class='mapp-menu-item' data-mapp-action='center'><?php _e('Center map', 'mappress-google-maps-for-wordpress');?></div>
				<?php if (Mappress::$options->engine != 'leaflet') { ?>
					<div class='mapp-menu-separator'></div>
					<div class='mapp-menu-item' data-mapp-action='layer' data-mapp-layer='traffic'><?php _e('Traffic', 'mappress-google-maps-for-wordpress');?></div>
					<div class='mapp-menu-item' data-mapp-action='layer' data-mapp-layer='bicycling'><?php _e('Bicycling', 'mappress-google-maps-for-wordpress');?></div>
					<div class='mapp-menu-item' data-mapp-action='layer' data-mapp-layer='transit'><?php _e('Transit', 'mappress-google-maps-for-wordpress');?></div>
				<?php } ?>
				<div class='mapp-menu-footer' title='<?php _e('Get help', 'mappress-google-maps-for-wordpress');?>'><a href='https://mappresspro.com/mappress-documentation' target='_blank'><div class='mapp-menu-help'>?</div></div></a>
			</div>
		</div>
	<# } #>
</script>

<script type='text/template' id='mapp-tmpl-map-footer'>
	<# if ( (map.poiList || mappl10n.options.poiList) && map.layout != 'inline') { #>
		<div class='mapp-footer'>
			<div class='mapp-button mapp-view-list' data-mapp-action='view-list'><img src='<# print(mappl10n.options.baseurl + "/images/outline_format_list_bulleted_black_18dp.png"); #>'><?php _e('List', 'mappress-google-maps-for-wordpress'); ?></div>
			<div class='mapp-button mapp-view-map' data-mapp-action='view-map'><img src='<# print(mappl10n.options.baseurl + "/images/outline_map_black_18dp.png"); #>'><?php _e('Map', 'mappress-google-maps-for-wordpress'); ?></div>
		</div>
	<# } #>
</script>
