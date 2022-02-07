<?php
/**
 * Setting up triggers
 *
 * @package     Wow_Plugin
 * @subpackage  Add-new/Targeting
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include targeting params
include_once( 'settings/targeting.php' );

$prefix = $this->plugin['prefix'];

// Counters
$option_name_view   = '_' . $prefix . '_view_counter_' . $tool_id;
$option_name_action = '_' . $prefix . '_action_counter_' . $tool_id;
$tool_view          = get_option( $option_name_view, '0' );
$tool_action        = get_option( $option_name_action, '0' );
if ( ! empty( $tool_view ) ) {
	$conversion = round( $tool_action / $tool_view * 100, 2 ) . '%';
} else {
	$conversion = '0%';
}

?>

	<div id="analytics" class="postbox wow-sidebar">
		<h2><?php esc_html_e( 'Analytics', $this->plugin['text'] ); ?></h2>
		<div class="inside">
			<div class="container">
				<div class="element">
					<span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'Views', $this->plugin['text'] ); ?> -
					<span
						id="tool_view"><?php echo $tool_view; ?></span>
					<p/>
					<span class="dashicons dashicons-external"></span> <?php esc_html_e( 'Actions', $this->plugin['text'] ); ?> -
					<span
						id="tool_action"><?php echo $tool_action; ?></span>
					<p/>
					<span class="dashicons dashicons-filter"></span> <?php esc_html_e( 'Conversion', $this->plugin['text'] ); ?> -
					<span id="conversion"><?php echo $conversion; ?></span>
					<p/>
					<span class="preview button" onclick="resetcounts(<?php echo $tool_id; ?>);"><?php esc_html_e( 'Reset',
				  $this->plugin['text'] ); ?></span>

				</div>
			</div>
		</div>
	</div>

	<div id="targeting" class="postbox wow-sidebar">

		<h2><?php esc_html_e( 'Targeting', $this->plugin['text'] ); ?></h2>
		<div class="inside">

			<!--Screen options-->
			<div class="container">
				<div class="element">
					<h4><?php esc_html_e( 'Show on devices', $this->plugin['text'] ); ?></h4>
			<?php echo self::option( $include_more_screen ); ?>
					<label for="include_more_screen"><?php esc_html_e( "Don't show on screens more",
				  $this->plugin['text'] ); ?></label>
			<?php echo self::tooltip( $show_screen_help ); ?>
					<p/>
			<?php echo self::option( $screen_more ); ?>
			<?php echo self::option( $include_mobile ); ?>
					<label for="include_mobile"><?php esc_html_e( "Don't show on screens less",
				  $this->plugin['text'] ); ?></label>
			<?php echo self::tooltip( $include_mobile_help ); ?>
					<p/>
			<?php echo self::option( $screen ); ?><p/>
				</div>
			</div>

			<!--    User's role-->
			<div class="container">
				<div class="element">
					<h4><?php _e( 'Show for users', $this->plugin['text'] ); ?><?php echo self::pro(); ?></h4>
					<input type="radio" checked disabled> All Users<br/>
					<input type="radio" disabled> Authorized Users<br/>
					<input type="radio" disabled> Unauthorized Users<br/>
					<p/>
				</div>
			</div>

			<!--    Language-->
			<div class="container">
				<div class="element">
					<h4><?php _e( 'Depending on the language', $this->plugin['text'] ); ?><?php echo self::pro(); ?></h4>
					<input type="checkbox" disabled>
					<label for="depending_language"><?php _e( "Enable", $this->plugin['text'] ); ?></label>
				</div>
			</div>

			<!--    FontAwesome-->
			<div class="container">
				<div class="element">
					<h4><?php esc_html_e( 'Font Awesome 5 style', $this->plugin['text'] ); ?></h4>
			<?php echo self::option( $disable_fontawesome ); ?> <label
						for="disable_fontawesome"><?php esc_html_e( "Disable", $this->plugin['text'] ); ?></label>
			<?php echo self::tooltip( $disable_fontawesome_help ); ?>
				</div>
			</div>


		</div>
	</div>
<?php
