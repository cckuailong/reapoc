<?php
?>

	<div class="rtec-single-event<?php $event_obj->the_single_event_classes(); ?>">

		<div class="rtec-event-meta">
			<?php do_action( 'rtec_registrations_tab_event_meta', $event_obj ); ?>

		</div>

		<?php do_action( 'rtec_registrations_tab_hidden_event_options', $event_obj ); ?>
		<?php include RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-reg-table.php'; ?>

	</div> <!-- rtec-single-event -->
