<?php
/**
 * Single row of schedule
 * Needs `schedule` var set which is instance of underDEV\AdvancedCronManager\Cron\Object\Schedule
 *
 * @package advanced-cron-manager
 */

$schedule = $this->get_var( 'schedule' );

?>

<div class="single-schedule <?php echo $schedule->protected ? 'protected' : ''; ?>">
	<div class="column label"><?php echo esc_html( $schedule->label ); ?></div>
	<div class="column slug"><?php echo esc_html( $schedule->slug ); ?></div>
	<div class="column interval">
		<?php // Translators: numer of seconds. ?>
		<span title="<?php printf( esc_attr( _n( '%d second', '%d seconds', $schedule->interval, 'advanced-cron-manager' ) ), intval( $schedule->interval ) ); ?>">
			<?php echo esc_html( $schedule->get_human_interval() ); ?>
		</span>
	</div>
	<div class="column actions">
		<?php if ( $schedule->protected ) : ?>
			<span class="dashicons dashicons-edit disabled" title="<?php esc_attr_e( 'This schedule is protected and you cannot edit it', 'advanced-cron-manager' ); ?>">
				<span><?php esc_html_e( 'Edit', 'advanced-cron-manager' ); ?></span>
			</span>
			<span class="dashicons dashicons-trash disabled" title="<?php esc_attr_e( 'This schedule is protected and you cannot remove it', 'advanced-cron-manager' ); ?>">
				<span><?php esc_html_e( 'Trash', 'advanced-cron-manager' ); ?></span>
			</span>
		<?php else : ?>
			<a href="#" data-nonce="<?php echo esc_attr( $schedule->nonce( 'edit' ) ); ?>" data-schedule="<?php echo esc_attr( $schedule->slug ); ?>" class="edit-schedule dashicons dashicons-edit" title="<?php esc_attr_e( 'Edit', 'advanced-cron-manager' ); ?>">
				<span><?php esc_html_e( 'Edit', 'advanced-cron-manager' ); ?></span>
			</a>
			<a href="#" data-nonce="<?php echo esc_attr( $schedule->nonce( 'remove' ) ); ?>" data-schedule="<?php echo esc_attr( $schedule->slug ); ?>" class="remove-schedule dashicons dashicons-trash" title="<?php esc_attr_e( 'Remove', 'advanced-cron-manager' ); ?>">
				<span><?php esc_html_e( 'Remove', 'advanced-cron-manager' ); ?></span>
			</a>
		<?php endif ?>
	</div>
</div>
