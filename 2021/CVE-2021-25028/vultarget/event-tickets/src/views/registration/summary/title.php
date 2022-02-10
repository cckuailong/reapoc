<?php
/**
 * This template renders the summary Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/summary/title.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
?>
<div class="tribe-tickets__registration__title">
	<header>
		<h2 class="tribe-common-h4 tribe-common-h3--min-medium">
			<a href="<?php the_permalink( $event_id ); ?>">
				<?php echo get_the_title( $event_id ); ?>
			</a>
		</h2>
	</header>
</div>
