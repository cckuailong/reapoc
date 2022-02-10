<?php
/**
 * This template renders the summary ticket icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/ticket/icon.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @deprecated 4.11.0
 * @version 4.11.0
 *
 */
?>
<div class="tribe-tickets__registration__tickets__item__icon">
	<?php $this->template( 'registration/summary/ticket/icon-svg' ); ?>
</div>
