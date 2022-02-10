<div class="rtec-mvt-list-item" data-rtec-event-id="<?php echo esc_attr( stripslashes( $event_obj->event_meta['post_id'] ) ); ?>" data-rtec-mvt-id="<?php echo esc_attr( stripslashes( $mvt_field['id'] ) ); ?>">
	<a href="<?php $this->the_detailed_view_href( $event_obj->event_meta['post_id'], $mvt_field['id'] ); ?>"><h3><?php echo esc_html( stripslashes( $mvt_field['label'] ) ); ?></h3></a>
	<div class="rtec-reg-info">
		<p><?php echo $event_obj->get_registration_text( $mvt_field, $num_registered_mvt_this ); ?></p>
	</div>
	<div class="rtecbox closed">
		<button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">Toggle panel: Information</span><span class="toggle-indicator" aria-hidden="true"></span></button>
	</div>
	<div class="rtec-hidden-reg-table">
	<?php include RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-reg-table.php'; ?>
	</div>
</div>
