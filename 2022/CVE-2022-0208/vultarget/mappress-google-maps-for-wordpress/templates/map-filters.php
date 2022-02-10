<?php if (Mappress::$pro) { ?>
	<div class='mapp-filters'>
		<div class='mapp-filter-wrapper'>
			<div class='mapp-button mapp-caret mapp-filter-toggle' data-mapp-action='filter-toggle'><?php _e('Filter', 'mappress-google-maps-for-wordpress');?></div>
			<div class='mapp-filter-body'>
				<div class='mapp-filter-list'>
					<?php foreach(Mappress::$options->filters as $atts) { ?>
						<?php $filter = new Mappress_Filter($atts); ?>
						<div class='mapp-filter-values'><?php echo $filter->get_html(); ?></div>
					<?php } ?>
				</div>
				<div class='mapp-filter-toolbar'>
					<div class='mapp-link-button mapp-filter-reset' data-mapp-action='filter-reset'><?php _e('Reset', 'mappress-google-maps-for-wordpress');?></div>
					<div class='mapp-filter-count'></div>
					<div class='mapp-submit-button mapp-filter-done' data-mapp-action='filter-toggle'><?php _e('Done', 'mappress-google-maps-for-wordpress');?></div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
