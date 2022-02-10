<?php
/**
 * Server Scheduler settings part
 *
 * @package advanced-cron-manager
 */

$settings = $this->get_var( 'settings' );

if ( ! empty( $settings['server_enable'] ) ) {
	$display_dependants = '';
} else {
	$display_dependants = 'style="display: none;"';
}

?>

<div id="server-scheduler-settings">

	<div class="tile">

		<h3 class="tile-header"><?php esc_html_e( 'Server Scheduler', 'advanced-cron-manager' ); ?></h3>

		<div class="tile-content">

			<form id="server-settings-form">

				<label class="master-setting">
					<input type="checkbox" name="server_enable" value="1" <?php checked( $settings['server_enable'], 1 ); ?>>
					<?php esc_html_e( 'Enable Server Scheduler', 'advanced-cron-manager' ); ?>
					<p class="description"><?php esc_html_e( 'When enabled WordPress will not spawn Cron anymore. You have to set the Cron on your server', 'advanced-cron-manager' ); ?></p>
				</label>

				<div class="dependants" <?php echo esc_attr( $display_dependants ); ?>>
					<p><?php _e( 'Check <a href="https://www.google.com/search?q=how+to+setup+cron+job" target="_blank">how to setup the Cron job</a> or read more about <a href="https://developer.wordpress.org/plugins/cron/hooking-into-the-system-task-scheduler/" target="_blank">Hooking WP-Cron Into the System Task Scheduler</a>', 'advanced-cron-manager' ); // phpcs:ignore ?>.</p>
					<p>
						<?php esc_html_e( 'The command you want to use is:', 'advanced-cron-manager' ); ?>
						<code>wget -qO- <?php echo site_url( '/wp-cron.php' ); // phpcs:ignore ?> &> /dev/null</code>
					</p>
					<p><?php esc_html_e( 'The reasonable time interval is 5-15 minutes. That is */5 * * * * or */15 * * * * for Cron interval setting', 'advanced-cron-manager' ); ?>.</p>
				</div>

				<input type="submit" class="button-secondary" data-nonce="<?php echo esc_attr( wp_create_nonce( 'acm/server/settings/save' ) ); ?>" value="<?php esc_attr_e( 'Save settings', 'advanced-cron-manager' ); ?>">

			</form>

		</div>

	</div>

</div>
