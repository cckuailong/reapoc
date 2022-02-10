<?php
/**
 * Implementation tab
 * View scope is the same as in the events/section view
 *
 * @package advanced-cron-manager
 */

?>

<code>
	<?php echo $this->get_var( 'event' )->get_implementation(); // phpcs:ignore ?>
</code>
<p><?php esc_html_e( 'You can paste this anywhere between <?php and ?> tags.', 'advanced-cron-manager' ); ?></p>
