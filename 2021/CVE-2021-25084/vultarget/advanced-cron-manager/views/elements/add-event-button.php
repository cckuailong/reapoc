<?php
/**
 * Add task button
 *
 * @package advanced-cron-manager
 */

?>

<a href="#" class="add-event page-title-action" data-nonce="<?php echo wp_create_nonce( 'acm/event/add' ); // phpcs:ignore ?>"><?php esc_html_e( 'Add new event', 'advanced-cron-manager' ); ?></a>
