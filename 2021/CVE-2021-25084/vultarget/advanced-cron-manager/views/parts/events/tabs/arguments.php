<?php
/**
 * Logs tab
 * View scope is the same as in the events/section view
 *
 * @package advanced-cron-manager
 */

?>

<?php foreach ( $this->get_var( 'event' )->args as $arg ) : ?>
	<?php if ( is_array( $arg ) ) : ?>
		<span><?php esc_html_e( 'Array', 'advanced-cron-manager' ); ?></span>
	<?php elseif ( is_object( $arg ) ) : ?>
		<?php $class_name = get_class( $arg ); ?>
		<?php if ( empty( $class_name ) ) : ?>
			<span><?php esc_html_e( 'Object', 'advanced-cron-manager' ); ?></span>
		<?php else : ?>
			<span><?php echo esc_html( $class_name ); ?></span>
		<?php endif ?>
	<?php else : ?>
		<span><?php echo esc_html( $arg ); ?></span>
	<?php endif ?>
<?php endforeach ?>
