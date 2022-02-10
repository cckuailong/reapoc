<?php
/**
 * Forms header
 *
 * @package advanced-cron-manager
 */

?>

<?php if ( $this->get_var( 'heading' ) ) : ?>
	<h3><?php echo $this->get_var( 'heading' ); // phpcs:ignore ?></h3>
<?php endif ?>

<form class="<?php echo esc_attr( $this->get_var( 'form_class' ) ); ?>">
