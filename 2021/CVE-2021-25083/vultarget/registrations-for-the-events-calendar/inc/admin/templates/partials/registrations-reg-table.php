<table class="widefat striped rtec-registrations-data">
	<thead>
	<tr>
		<?php foreach ( $event_obj->labels as $label ) : ?>
			<th><?php esc_html_e( stripslashes( $label ), 'registrations-for-the-events-calendar' ); ?></th>
		<?php endforeach; ?>
	</tr>
	</thead>
	<tbody>
	<?php if ( ! empty( $event_obj->registrants_data ) ) : foreach( $event_obj->registrants_data as $registration ) :
		$is_user = isset( $registration['user_id'] ) && (int)$registration['user_id'] > 0 ? true : false;
		?>

		<tr class="rtec-reg-row<?php echo $this->get_registrant_tr_classes( $registration['status'], $is_user ); ?>" >
			<td class="rtec-first-data">
				<?php echo $this->get_registrant_icons( $registration['status'], $is_user ) . esc_html( date_i18n( 'm/d ' . rtec_get_time_format(), strtotime( $registration['registration_date'] ) + rtec_get_time_zone_offset() ) ); ?>
			</td>
			<?php foreach ( $event_obj->column_label as $column => $label ) : ?>
				<td><?php
					if ( isset( $registration[$column] ) ) {
						echo esc_html( stripslashes( $registration[$column] ) );
					} else if ( isset( $registration[$column.'_name'] ) ) {
						echo esc_html( stripslashes( $registration[$column.'_name'] ) );
					} else if ( isset( $registration['custom'][$label] ) ) {
						echo esc_html( stripslashes( $registration['custom'][$label] ) );
					} else if ( isset( $registration['custom'][$column] ) ) {
						echo esc_html( stripslashes( $registration['custom'][$column]['value'] ) );
					}
					?></td>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; // registration ?>

	<?php else: ?>

		<tr>
			<td colspan="4" align="center"><?php _e( 'No Registrations Yet', 'registrations-for-the-events-calendar' ); ?></td>
		</tr>

	<?php endif; // registrations not empty ?>

	<?php if ( $event_obj->pagination_needed ) : ?>
		<tr><td colspan="4"><a href="<?php $this->the_detailed_view_href( $event->ID, '' ); ?>" class="button rtec-wide rtec-view-all"><i class="fa fa-list" aria-hidden="true"></i> <?php _e( 'View all', 'registrations-for-the-events-calendar' ); ?></a></td></tr>
	<?php endif; ?>

	</tbody>
</table>