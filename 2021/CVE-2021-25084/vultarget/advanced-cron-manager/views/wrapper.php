<?php
/**
 * Wrapper for admin page
 *
 * @package advanced-cron-manager
 */

/**
 * $this in this context is an instance of View class
 */
do_action( 'advanced-cron-manager/screen/wrap/before', $this );
?>

<div class="wrap">

	<h1 class="wp-heading-inline"><?php esc_html_e( 'Cron Manager', 'advanced-cron-manager' ); ?></h1>
	<?php $this->get_view( 'elements/add-event-button' ); ?>
	<?php $this->get_view( 'elements/add-schedule-button' ); ?>

	<hr class="wp-header-end">

	<div id="poststuff">

		<div id="post-body" class="columns-2">

			<div id="post-body-content">
				<?php
				/**
				 * $this in this context is an instance of View class
				 */
				do_action( 'advanced-cron-manager/screen/main', $this );
				?>
			</div>

			<div id="postbox-container-1" class="postbox-container">
				<?php
				/**
				 * $this in this context is an instance of View class
				 */
				do_action( 'advanced-cron-manager/screen/sidebar', $this );
				?>
			</div>

		</div>

	</div>

</div>

<?php
/**
 * $this in this context is an instance of View class
 */
do_action( 'advanced-cron-manager/screen/wrap/after', $this ); ?>
