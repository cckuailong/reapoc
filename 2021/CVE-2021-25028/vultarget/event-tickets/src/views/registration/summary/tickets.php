<?php
/**
 * This template renders the summary tickets
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/tickets.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
?>
<div class="tribe-tickets__registration__tickets">

	<?php $this->template( 'registration/summary/tickets-header' ); ?>

	<?php foreach ( $tickets as $key => $ticket ) : ?>

		<?php $this->template( 'registration/summary/ticket/content', array( 'ticket' => $ticket, 'key' => $key ) ); ?>

	<?php endforeach; ?>

</div>
