<?php
/**
 * Register the Admin pages and load the scripts action
 */

/**
 * Sub-menu pages
 */
function bnfw_admin_menu() {

	// New Notifications Sub-menu
	add_submenu_page(
		'edit.php?post_type=bnfw_notification',
		esc_html__( 'Notification Settings', 'bnfw' ),
		esc_html__( 'Settings', 'bnfw' ),
		'bnfw',
		'bnfw-settings',
		'bnfw_settings_page'
	);
}

// Add the Admin pages to the WordPress menu
add_action( 'admin_menu', 'bnfw_admin_menu' );
add_action( 'admin_menu', 'bnfw_menu_item_links', 12 );
add_action( 'admin_head', 'bnfw_menu_item_link_targets' );

/* ------------------------------------------------------------------------ *
 * Menu Pages
 * ------------------------------------------------------------------------ */

/**
 * Settings Page
 */
function bnfw_settings_page() {
	ob_start(); ?>

	<div class="wrap">
		<h2><?php esc_html_e( 'BNFW Settings', 'bnfw' ); ?></h2>

		<form method="post" action="options.php" class="bnfw-form">
			<?php
			settings_errors();
			settings_fields( 'bnfw-settings' );
			do_settings_sections( 'bnfw-settings' );

			submit_button( __( 'Save Settings', 'bnfw' ) );
			?>
		</form>
	</div>

	<?php echo ob_get_clean();
}

/**
 * External Menu Item Links
 */
function bnfw_menu_item_links() {
	global $submenu;

	if ( current_user_can( 'bnfw' ) ) {
		$doc_url = 'https://betternotificationsforwp.com/documentation/';
		$store_url = 'https://betternotificationsforwp.com/downloads/';
		$support_url = 'https://betternotificationsforwp.com/priority-support/';

		if ( bnfw_is_tracking_allowed() ) {
			$doc_url .= '?utm_source=WP%20Admin%20Submenu%20Item%20-%20"Documentation"&amp;utm_medium=referral';
			$store_url .= '?utm_source=WP%20Admin%20Submenu%20Item%20-%20"Add-on"&amp;utm_medium=referral';
			$support_url .= '?utm_source=WP%20Admin%20Submenu%20Item%20-%20"Priority%20Support"&amp;utm_medium=referral';
		}

		// Documentation Link
		$submenu['edit.php?post_type=bnfw_notification'][500] = array(
			'<div id="bnfw-menu-item-documentation" style="color: #73daeb;">' . __( 'Documentation', 'bnfw' ) . '</div>',
			'bnfw',
			$doc_url,
		);

		// Add-ons Link
		$submenu['edit.php?post_type=bnfw_notification'][600] = array(
			'<div id="bnfw-menu-item-addons" style="color: #ff6f59;">' . __( 'Premium Add-ons', 'bnfw' ) . '</div>',
			'bnfw',
			$store_url,
		);

		// Add-ons Link
		$submenu['edit.php?post_type=bnfw_notification'][700] = array(
			'<div id="bnfw-menu-item-support" style="color: #f00001;">' . __( 'Priority Support', 'bnfw' ) . '</div>',
			'bnfw',
			$support_url,
		);
	}
}

function bnfw_menu_item_link_targets() {
	?>
	<script type="text/javascript">
		jQuery( document ).ready( function ( $ ) {
			// Documentation Link
			$( '#bnfw-menu-item-documentation' ).parent().attr( 'target', '_blank' );
			$( '#bnfw-menu-item-documentation' ).hover( function () {
				$( this ).css( 'color', '#a0e6f1' );
			}, function () {
				$( this ).css( 'color', '#73daeb' );
			} );

			// Add-ons Link
			$( '#bnfw-menu-item-addons' ).parent().attr( 'target', '_blank' );
			$( '#bnfw-menu-item-addons' ).hover( function () {
				$( this ).css( 'color', '#ff9b8c' );
			}, function () {
				$( this ).css( 'color', '#ff6f59' );
			} );

			// Priority Support Link
			$( '#bnfw-menu-item-support' ).parent().attr( 'target', '_blank' );
			$( '#bnfw-menu-item-support' ).hover( function () {
				$( this ).css( 'color', '#ff3536' );
			}, function () {
				$( this ).css( 'color', '#f00001' );
			} );
		} );
	</script>
<?php }

/* ------------------------------------------------------------------------ *
 * Settings Page - Setting Registration
 * ------------------------------------------------------------------------ */

/**
 *
 */
function bnfw_general_options() {
	// Set-up - General Options Section
	add_settings_section(
		'bnfw_general_options_section',     // Section ID
		'',                                 // Title above settings section
		'bnfw_general_options_callback',    // Name of function that renders a description of the settings section
		'bnfw-settings'                     // Page to show on
	);

	// Register - Suppress SPAM Checkbox
	register_setting(
		'bnfw-settings',
		'bnfw_suppress_spam'
	);

	// Suppress notifications for SPAM comments
	add_settings_field(
		'bnfw_suppress_spam',           // Field ID
		esc_html__( 'Suppress SPAM comment notification', 'bnfw' ) . '<div class="bnfw-help-tip"><p>' . esc_html__( 'Comments that are correctly marked as SPAM by a 3rd party plugin (such as Akismet) will not generate a notification if this is ticked.', 'bnfw' ) . '</p></div>',  // Label to the left
		'bnfw_suppress_spam_checkbox',  // Name of function that renders options on the page
		'bnfw-settings',                // Page to show on
		'bnfw_general_options_section', // Associate with which settings section?
		array(
			esc_html__( 'Don\'t send notifications for comments marked as SPAM', 'bnfw' )
		)
	);

	// Register - Email Format setting
	register_setting(
		'bnfw-settings',
		'bnfw_email_format'
	);

	add_settings_field(
		'bnfw_email_format',           // Field ID
		esc_html__( 'Default Email Format', 'bnfw' ) . '<div class="bnfw-help-tip"><p>' . esc_html__( 'This will apply to all emails sent out via WordPress, even those from other plugins. For more details, please see the ', 'bnfw' ) . '<a href="https://wordpress.org/plugins/bnfw/faq/" target="_blank">FAQ</a>.</p></div>',  // Label to the left
		'bnfw_email_format_radio',  // Name of function that renders options on the page
		'bnfw-settings',                // Page to show on
		'bnfw_general_options_section' // Associate with which settings section?
	);

	// Register - Email Format setting
	register_setting(
		'bnfw-settings',
		'bnfw_enable_shortcodes'
	);

	add_settings_field(
		'bnfw_enable_shortcodes',           // Field ID
		esc_html__( 'Enable Content Shortcodes?', 'bnfw' ) . '<div class="bnfw-help-tip"><p>' . esc_html__( 'Shortcodes in the post/page content are disabled by default.', 'bnfw' ) . '</p></div>',  // Label to the left
		'bnfw_enable_shortcodes_checkbox',  // Name of function that renders options on the page
		'bnfw-settings',                // Page to show on
		'bnfw_general_options_section', // Associate with which settings section?
		array(
			esc_html__( 'Enable shortcode output in the page/post content', 'bnfw' ),
		)
	);

	// Register - Allow tracking setting
	register_setting(
		'bnfw-settings',
		'bnfw_allow_tracking'
	);

	add_settings_field(
		'bnfw_allow_tracking',           // Field ID
		esc_html__( 'Allow Usage Tracking?', 'bnfw' ),  // Label to the left
		'bnfw_render_allow_tracking',  // Name of function that renders options on the page
		'bnfw-settings',                // Page to show on
		'bnfw_general_options_section', // Associate with which settings section?
		array(
			esc_html__( 'Allow Better Notifications for WP to anonymously track how this plugin is used and help make the plugin better.', 'bnfw' )
		)
	);
}

add_action( 'admin_init', 'bnfw_general_options', 10 );

/* ------------------------------------------------------------------------ *
 * Settings Page - Settings Section Callbacks
 * ------------------------------------------------------------------------ */

/**
 *
 */
function bnfw_general_options_callback() {
}

/* ------------------------------------------------------------------------ *
 * Settings Page - Settings Field Callbacks
 * ------------------------------------------------------------------------ */

/**
 * Suppress SPAM checkbox.
 *
 * @since 1.0
 *
 * @param $args
 */
function bnfw_suppress_spam_checkbox( $args ) {
	?>
	<input type="checkbox" id="bnfw_suppress_spam" name="bnfw_suppress_spam"
	       value="1" <?php checked( 1, get_option( 'bnfw_suppress_spam' ), true ); ?>>
	<label for="bnfw_suppress_spam"><?php echo esc_html( $args[0] ); ?></label>
	<?php
}

/**
 * Show email format radio
 *
 * @since 1.4
 *
 * @param array $args
 */
function bnfw_email_format_radio( $args ) {
	$email_format = get_option( 'bnfw_email_format', 'html' );
	?>
	<label>
		<input type="radio" value="html"
		       name="bnfw_email_format" <?php checked( $email_format, 'html', true ); ?>><?php esc_html_e( 'HTML Formatting', 'bnfw' ); ?>
	</label>
	<br/>
	<label>
		<input type="radio" value="text"
		       name="bnfw_email_format" <?php checked( $email_format, 'text', true ); ?>><?php esc_html_e( 'Plain Text', 'bnfw' ); ?>
	</label>
	<?php
}

/**
 * Render allow tracking checkbox.
 *
 * @since 1.6
 *
 * @param array $args
 */
function bnfw_render_allow_tracking( $args ) {
	?>
	<input type="checkbox" id="bnfw_allow_tracking" name="bnfw_allow_tracking"
	       value="on" <?php checked( 'on', get_option( 'bnfw_allow_tracking' ), true ); ?>>
	<label for="bnfw_allow_tracking"><?php echo esc_html( $args[0] ); ?></label>
	<?php
}

/**
 * Render Enable shortcode checkbox.
 *
 * @param array $args
 */
function bnfw_enable_shortcodes_checkbox( $args ) {
	?>
	<input type="checkbox" id="bnfw_enable_shortcodes" name="bnfw_enable_shortcodes"
	       value="1" <?php checked( 1, get_option( 'bnfw_enable_shortcodes' ), true ); ?>>
	<label for="bnfw_enable_shortcodes"><?php echo esc_html( $args[0] ); ?></label>
	<?php
}
