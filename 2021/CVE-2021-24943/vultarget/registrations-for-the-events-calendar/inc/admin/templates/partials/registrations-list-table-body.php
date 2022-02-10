<?php
$mvt = '';
$date_format = 'F jS, ' . rtec_get_time_format();
?>

<tr <?php echo $row_class; ?>>
	<td><a href="<?php $this->the_detailed_view_href( $event->ID, '' ); ?>"><?php echo esc_html( $event_meta['title'] ); ?></a></td>
	<td><?php echo date_i18n( $date_format, strtotime( $event_meta['start_date'] ) ); ?></td>
	<td><?php echo date_i18n( $date_format, strtotime( $event_meta['end_date'] ) ); ?></td>
	<td class="rtec-venue-highlight"><?php echo esc_html( $venue ); ?></td>
	<td class="rtec-list-attendance"><?php echo esc_html( $event_obj->get_registration_text( array(), $num_registered ) ); ?></td>
	<td><a href="<?php $this->the_detailed_view_href( $event->ID, $mvt ); ?>"><i class="fa fa-list" aria-hidden="true"></i> <?php _e( 'Details', 'registrations-for-the-events-calendar' ); ?></a></td>
</tr>
