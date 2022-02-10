<# if (map.query) { #>
	<div class='mapp-list-header'>
		<div class='mapp-list-count'>{{{pagination.count}}} <?php _e("Results", 'mappress-google-maps-for-wordpress'); ?></div>
	</div>
<# } #>

<div class='mapp-items'>
	<# _.forEach(pois, function(poi, i) { #>
		<# if (poi.visible) { #>
			<div class="mapp-item {{ (map.selected==poi) ? 'mapp-selected' : ''}}" data-mapp-action="open" data-mapp-poi="{{{i}}}">
				<# print(poi.render('item')); #>
			</div>
		<# } #>
	<# }); #>
</div>

<# if (pagination.count > pageSize) { // Only show pagination if >1 page #>
<div class='mapp-list-footer'>
	<div class='mapp-paginate'>
		<#
			const pages = Math.ceil(pagination.count / pageSize);
			let prevClass = (pagination.page <= 1) ? 'mapp-paginate-button mapp-disabled' : 'mapp-paginate-button';
			let prevAction = (pagination.page <= 1) ? '' : 'page';
			let nextClass = (pagination.page >= pages) ? 'mapp-paginate-button mapp-disabled' : 'mapp-paginate-button';
			let nextAction = (pagination.page >= pages) ? '' : 'page';
		#>
		<div class='{{prevClass}}' data-mapp-action='{{prevAction}}' data-mapp-page='1' >&laquo;</div>
		<div class='{{prevClass}}' data-mapp-action='{{prevAction}}' data-mapp-page='{{ pagination.page - 1}}'>&lsaquo;</div>
		<div class='mapp-paginate-count'><# print('<?php _e('%d of %d', 'mappress-google-maps-for-wordpress');?>'.replace('%d', pagination.page).replace('%d', pages)); #></div>
		<div class='{{nextClass}}' data-mapp-action='{{nextAction}}' data-mapp-page='{{ pagination.page + 1}}'>&rsaquo;</div>
		<div class='{{nextClass}}' data-mapp-action='{{nextAction}}' data-mapp-page='{{ pages }}'>&raquo;</div>
	</div>
</div>
<# } #>