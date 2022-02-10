<?php

?>

<table class="widefat rtec-registrations-data">
	<thead>
	<tr>
		<th><?php _e( 'Title', 'registrations-for-the-events-calendar' ); ?></th>
		<th><?php _e( 'Start Date', 'registrations-for-the-events-calendar' ); ?></th>
		<th><?php _e( 'Start Date', 'registrations-for-the-events-calendar' ); ?></th>
		<th><?php _e( 'Venue', 'registrations-for-the-events-calendar' ); ?></th>
		<th><?php _e( 'Attendance', 'registrations-for-the-events-calendar' ); ?></th>
		<th><?php _e( 'Link', 'registrations-for-the-events-calendar' ); ?></th>
	</tr>
	</thead>
	<tbody>
		<?php do_action( 'rtec_registrations_tab_list_table_body' ); ?>
	</tbody>
</table>
