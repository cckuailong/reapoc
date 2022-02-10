<?php
/**
 * Single row of event
 * Needs `event` var set which is instance of underDEV\AdvancedCronManager\Cron\Object\Event
 *
 * @package advanced-cron-manager
 */

$event                 = $this->get_var( 'event' );
$schedules             = $this->get_var( 'schedules' );
$single_event_schedule = $schedules->get_single_event_schedule();

$time_offset = get_option( 'gmt_offset' ) * 3600;
$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );

$css_class = '';

if ( $event->paused ) {
	$css_class .= 'paused ';
}

?>

<div class="single-event row <?php echo esc_attr( $css_class ); ?>" data-schedule="<?php echo esc_attr( $event->schedule ? $event->schedule : $single_event_schedule->slug ); ?>">
	<div class="columns">
		<div class="column cb">
			<input type="checkbox" name="bulk-actions" value="<?php echo esc_attr( $event->hash ); ?>">
			<span class="dashicons dashicons-admin-generic"></span>
		</div>
		<div class="column event">
			<a href="#" class="event-name">
				<?php echo esc_html( $event->hook ); ?>
				<?php if ( $event->paused ) : ?>
					<span class="dashicons dashicons-controls-pause"></span>
				<?php endif ?>
			</a>
			<div class="row-actions">
				<span class="details">
					<a href="#"><?php esc_html_e( 'Details', 'advanced-cron-manager' ); ?></a> |
				</span>
				<span class="run">
					<a href="#" data-nonce="<?php echo esc_attr( $event->nonce( 'run' ) ); ?>" data-event="<?php echo esc_attr( $event->hash ); ?>" class="run-event"><?php esc_html_e( 'Execute now', 'advanced-cron-manager' ); ?></a> |
				</span>
				<?php if ( ! $event->protected ) : ?>
					<span class="pause">
						<?php if ( $event->paused ) : ?>
							<a href="#" data-nonce="<?php echo esc_attr( $event->nonce( 'unpause' ) ); ?>" data-event="<?php echo esc_attr( $event->hash ); ?>" class="unpause-event"><?php esc_html_e( 'Unpause', 'advanced-cron-manager' ); ?></a> |
						<?php else : ?>
							<a href="#" data-nonce="<?php echo esc_attr( $event->nonce( 'pause' ) ); ?>" data-event="<?php echo esc_attr( $event->hash ); ?>" class="pause-event"><?php esc_html_e( 'Pause', 'advanced-cron-manager' ); ?></a> |
						<?php endif ?>
					</span>
				<?php endif ?>
				<?php do_action( 'advanced-cron-manager/screen/event/row/actions', $event, $this ); ?>
				<span class="trash">
					<?php if ( $event->protected ) : ?>
						<?php esc_html_e( 'Protected', 'advanced-cron-manager' ); ?>
					<?php else : ?>
						<a href="#" data-nonce="<?php echo esc_attr( $event->nonce( 'remove' ) ); ?>" data-event="<?php echo esc_attr( $event->hash ); ?>" class="remove-event"><?php esc_html_e( 'Remove', 'advanced-cron-manager' ); ?></a>
					<?php endif ?>
				</span>
			</div>
		</div>
		<div class="column schedule" data-interval="<?php echo esc_attr( $event->interval ); ?>"><?php echo esc_html( $schedules->get_schedule( $event->schedule )->label ); ?></div>
		<div class="column arguments">
			<?php foreach ( $event->args as $arg ) : ?>
				<span>
					<?php if ( is_array( $arg ) ) : ?>
						<?php esc_html_e( 'Array', 'advanced-cron-manager' ); ?>
					<?php elseif ( is_object( $arg ) ) : ?>
						<?php echo esc_html( get_class( $arg ) ); ?>
					<?php else : ?>
						<?php echo esc_html( $arg ); ?>
					<?php endif ?>
				</span>
			<?php endforeach ?>
		</div>
		<div class="column next-execution" data-time="<?php echo esc_attr( $event->next_call ); ?>">
			<?php if ( $event->next_call <= time() ) : ?>
				<?php esc_html_e( 'In queue', 'advanced-cron-manager' ); ?>
			<?php else : ?>
				<?php
				// Translators: human friendly diff time.
				echo esc_html( sprintf( __( 'In %s', 'advanced-cron-manager' ), human_time_diff( time(), $event->next_call ) ) );
				?>
				<br>
				<span title="<?php echo esc_attr( 'UTC: ' . date_i18n( $date_format . ' ' . $time_format, $event->next_call ) ); ?>">
					<?php echo date_i18n( $date_format . ' ' . $time_format, $event->next_call + $time_offset ); // phpcs:ignore ?>
				</span>
			<?php endif ?>
		</div>
	</div>
	<div class="details">
		<ul class="tabs">
			<?php $active = 'active'; ?>
			<?php foreach ( $this->get_var( 'details_tabs' ) as $tab_slug => $tab_name ) : ?>
				<li class="<?php echo esc_attr( $active ); ?> <?php echo esc_attr( $tab_slug ); ?>">
					<a href="#" data-section="<?php echo esc_attr( $tab_slug ); ?>"><?php echo esc_html( $tab_name ); ?></a>
				</li>
				<?php $active = ''; ?>
			<?php endforeach ?>
		</ul>
		<?php $active = 'active'; ?>
		<?php foreach ( $this->get_var( 'details_tabs' ) as $tab_slug => $tab_name ) : ?>
			<div class="content <?php echo esc_attr( $tab_slug ); ?> <?php echo esc_attr( $active ); ?>">
				<?php do_action( 'advanced-cron-manager/screen/event/details/tab/' . $tab_slug, $this ); ?>
			</div>
			<?php $active = ''; ?>
		<?php endforeach ?>
	</div>
</div>
