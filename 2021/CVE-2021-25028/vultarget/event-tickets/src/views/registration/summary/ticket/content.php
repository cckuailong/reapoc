<?php
/**
 * This template renders the summary tickets
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/ticket/content.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
?>
<div class="tribe-tickets__registration__tickets__item">

	<?php $this->template( 'registration/summary/ticket/icon', array( 'ticket' => $ticket, 'key' => $key ) ); ?>

	<?php $this->template( 'registration/summary/ticket/quantity', array( 'ticket' => $ticket, 'key' => $key ) ); ?>

	<?php $this->template( 'registration/summary/ticket/title', array( 'ticket' => $ticket, 'key' => $key ) ); ?>

	<?php $this->template( 'registration/summary/ticket/price', array( 'ticket' => $ticket, 'key' => $key ) ); ?>

</div>
