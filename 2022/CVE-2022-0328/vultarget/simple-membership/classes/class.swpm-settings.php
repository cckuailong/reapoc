<?php

class SwpmSettings {

	private static $_this;
	private $settings;
	public $current_tab;
	private $tabs;

	private function __construct() {
		$this->settings = (array) get_option( 'swpm-settings' );
	}

	public function init_config_hooks() {
		//This function is called from "admin_init"
		//It sets up the various tabs and the fields for the settings admin page.

		if ( is_admin() ) { // for frontend just load settings but dont try to render settings page.
			//Read the value of tab query arg.
			$tab               = isset( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : 1;
			$this->current_tab = empty( $tab ) ? 1 : $tab;

			//Setup the available settings tabs array.
			$this->tabs = array(
				1 => SwpmUtils::_( 'General Settings' ),
				2 => SwpmUtils::_( 'Payment Settings' ),
				3 => SwpmUtils::_( 'Email Settings' ),
				4 => SwpmUtils::_( 'Tools' ),
				5 => SwpmUtils::_( 'Advanced Settings' ),
				6 => SwpmUtils::_( 'Addons Settings' ),
			);

			//Register the draw tab action hook. It will be triggered using do_action("swpm-draw-settings-nav-tabs")
			add_action( 'swpm-draw-settings-nav-tabs', array( &$this, 'draw_tabs' ) );

			//Register the various settings fields for the current tab.
			$method = 'tab_' . $this->current_tab;
			if ( method_exists( $this, $method ) ) {
				$this->$method();
			}
		}
	}

	private function tab_1() {
		//Register settings sections and fileds for the general settings tab.

		register_setting( 'swpm-settings-tab-1', 'swpm-settings', array( &$this, 'sanitize_tab_1' ) );

		//This settings section has no heading
		add_settings_section( 'swpm-general-post-submission-check', '', array( &$this, 'swpm_general_post_submit_check_callback' ), 'simple_wp_membership_settings' );

		add_settings_section( 'swpm-documentation', SwpmUtils::_( 'Plugin Documentation' ), array( &$this, 'swpm_documentation_callback' ), 'simple_wp_membership_settings' );
		add_settings_section( 'general-settings', SwpmUtils::_( 'General Settings' ), array( &$this, 'general_settings_callback' ), 'simple_wp_membership_settings' );
		add_settings_field(
			'enable-free-membership',
			SwpmUtils::_( 'Enable Free Membership' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'general-settings',
			array(
				'item'    => 'enable-free-membership',
				'message' => SwpmUtils::_( 'Enable/disable registration for free membership level. When you enable this option, make sure to specify a free membership level ID in the field below.' ),
			)
		);
		add_settings_field(
			'free-membership-id',
			SwpmUtils::_( 'Free Membership Level ID' ),
			array( &$this, 'textfield_small_callback' ),
			'simple_wp_membership_settings',
			'general-settings',
			array(
				'item'    => 'free-membership-id',
				'message' => SwpmUtils::_( 'Assign free membership level ID' ),
			)
		);
		add_settings_field(
			'enable-moretag',
			SwpmUtils::_( 'Enable More Tag Protection' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'general-settings',
			array(
				'item'    => 'enable-moretag',
				'message' => SwpmUtils::_( 'Enables or disables "more" tag protection in the posts and pages. Anything after the More tag is protected. Anything before the more tag is teaser content.' ),
			)
		);
		add_settings_field(
			'hide-adminbar',
			SwpmUtils::_( 'Hide Adminbar' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'general-settings',
			array(
				'item'    => 'hide-adminbar',
				'message' => SwpmUtils::_( 'WordPress shows an admin toolbar to the logged in users of the site. Check this if you want to hide that admin toolbar in the frontend of your site.' ),
			)
		);
		add_settings_field(
			'show-adminbar-admin-only',
			SwpmUtils::_( 'Show Adminbar to Admin' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'general-settings',
			array(
				'item'    => 'show-adminbar-admin-only',
				'message' => SwpmUtils::_( 'Use this option if you want to show the admin toolbar to admin users only. The admin toolbar will be hidden for all other users.' ),
			)
		);
		add_settings_field(
			'disable-access-to-wp-dashboard',
			SwpmUtils::_( 'Disable Access to WP Dashboard' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'general-settings',
			array(
				'item'    => 'disable-access-to-wp-dashboard',
				'message' => SwpmUtils::_( 'WordPress allows a standard wp user to be able to go to the wp-admin URL and access his profile from the wp dashboard. Using this option will prevent any non-admin users from going to the wp dashboard.' ),
			)
		);

		add_settings_field(
			'default-account-status',
			SwpmUtils::_( 'Default Account Status' ),
			array( &$this, 'selectbox_callback' ),
			'simple_wp_membership_settings',
			'general-settings',
			array(
				'item'    => 'default-account-status',
				'options' => SwpmUtils::get_account_state_options(),
				'default' => 'active',
				'message' => SwpmUtils::_( 'Select the default account status for newly registered users. If you want to manually approve the members then you can set the status to "Pending".' ),
			)
		);

		add_settings_field(
			'members-login-to-comment',
			SwpmUtils::_( 'Members Must be Logged in to Comment' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'general-settings',
			array(
				'item'    => 'members-login-to-comment',
				'message' => SwpmUtils::_( 'Enable this option if you only want the members of the site to be able to post a comment.' ),
			)
		);

		/*
		  add_settings_field('protect-everything',  SwpmUtils::_('Protect Everything'),
		  array(&$this, 'checkbox_callback'), 'simple_wp_membership_settings', 'general-settings',
		  array('item' => 'protect-everything',
		  'message'=>SwpmUtils::_('Check this box if you want to protect all posts/pages by default.')));
		 */

		add_settings_section( 'pages-settings', SwpmUtils::_( 'Pages Settings' ), array( &$this, 'pages_settings_callback' ), 'simple_wp_membership_settings' );
		add_settings_field(
			'login-page-url',
			SwpmUtils::_( 'Login Page URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'pages-settings',
			array(
				'item'    => 'login-page-url',
				'message' => '',
			)
		);
		add_settings_field(
			'registration-page-url',
			SwpmUtils::_( 'Registration Page URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'pages-settings',
			array(
				'item'    => 'registration-page-url',
				'message' => '',
			)
		);
		add_settings_field(
			'join-us-page-url',
			SwpmUtils::_( 'Join Us Page URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'pages-settings',
			array(
				'item'    => 'join-us-page-url',
				'message' => '',
			)
		);
		add_settings_field(
			'profile-page-url',
			SwpmUtils::_( 'Edit Profile Page URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'pages-settings',
			array(
				'item'    => 'profile-page-url',
				'message' => '',
			)
		);
		add_settings_field(
			'reset-page-url',
			SwpmUtils::_( 'Password Reset Page URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'pages-settings',
			array(
				'item'    => 'reset-page-url',
				'message' => '',
			)
		);

		add_settings_section( 'debug-settings', SwpmUtils::_( 'Test & Debug Settings' ), array( &$this, 'testndebug_settings_callback' ), 'simple_wp_membership_settings' );

		$debug_field_help_text  = SwpmUtils::_( 'Check this option to enable debug logging.' );
		$debug_field_help_text .= SwpmUtils::_( ' This can be useful when troubleshooting an issue. Turn it off and reset the log files after the troubleshooting is complete.' );
		$debug_field_help_text .= '<br />';
		$debug_field_help_text .= '<br />- ' . SwpmUtils::_( 'View general debug log file by clicking ' ) . '<a href="' . SIMPLE_WP_MEMBERSHIP_URL . '/log.txt" target="_blank">' . SwpmUtils::_( 'here' ) . '</a>.';
		$debug_field_help_text .= '<br />- ' . SwpmUtils::_( 'View login related debug log file by clicking ' ) . '<a href="' . SIMPLE_WP_MEMBERSHIP_URL . '/log-auth.txt" target="_blank">' . SwpmUtils::_( 'here' ) . '</a>.';
		$debug_field_help_text .= '<br />- ' . SwpmUtils::_( 'Reset debug log files by clicking ' ) . '<a href="admin.php?page=simple_wp_membership_settings&swmp_reset_log=1" target="_blank">' . SwpmUtils::_( 'here' ) . '</a>.';
		add_settings_field(
			'enable-debug',
			SwpmUtils::_( 'Enable Debug' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'debug-settings',
			array(
				'item'    => 'enable-debug',
				'message' => $debug_field_help_text,
			)
		);
		add_settings_field(
			'enable-sandbox-testing',
			SwpmUtils::_( 'Enable Sandbox Testing' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'debug-settings',
			array(
				'item'    => 'enable-sandbox-testing',
				'message' => SwpmUtils::_( 'Enable this option if you want to do sandbox payment testing.' ),
			)
		);
	}

	private function tab_2() {
		//Register settings sections and fileds for the payment settings tab.
		register_setting( 'swpm-settings-tab-2', 'swpm-settings', array( $this, 'sanitize_tab_2' ) );
		add_settings_section( 'stripe-global-settings', SwpmUtils::_( 'Stripe Global Settings' ), null, 'simple_wp_membership_settings' );
		add_settings_field(
			'stripe-prefill-member-email',
			SwpmUtils::_( 'Pre-fill Member Email Address' ),
			array( $this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'stripe-global-settings',
			array(
				'item'    => 'stripe-prefill-member-email',
				'message' => SwpmUtils::_( 'Pre-fills the email address of the logged-in member on the Stripe checkout form when possible' ),
			)
		);
		add_settings_field(
			'stripe-test-public-key',
			SwpmUtils::_( 'Test Publishable Key' ),
			array( $this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'stripe-global-settings',
			array(
				'item'    => 'stripe-test-public-key',
				'message' => SwpmUtils::_( 'Stripe API Test publishable key' ),
			)
		);
		add_settings_field(
			'stripe-test-secret-key',
			SwpmUtils::_( 'Test Secret Key' ),
			array( $this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'stripe-global-settings',
			array(
				'item'    => 'stripe-test-secret-key',
				'message' => SwpmUtils::_( 'Stripe API Test secret key' ),
			)
		);
		add_settings_field(
			'stripe-live-public-key',
			SwpmUtils::_( 'Live Publishable Key' ),
			array( $this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'stripe-global-settings',
			array(
				'item'    => 'stripe-live-public-key',
				'message' => SwpmUtils::_( 'Stripe API Live publishable key' ),
			)
		);
		add_settings_field(
			'stripe-live-secret-key',
			SwpmUtils::_( 'Live Secret Key' ),
			array( $this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'stripe-global-settings',
			array(
				'item'    => 'stripe-live-secret-key',
				'message' => SwpmUtils::_( 'Stripe API Live secret key' ),
			)
		);
	}

	private function tab_3() {
		//Register settings sections and fileds for the email settings tab.

		register_setting( 'swpm-settings-tab-3', 'swpm-settings', array( &$this, 'sanitize_tab_3' ) );

		add_settings_section( 'email-settings-overview', SwpmUtils::_( 'Email Settings Overview' ), array( &$this, 'email_settings_overview_callback' ), 'simple_wp_membership_settings' );
		add_settings_section( 'email-misc-settings', SwpmUtils::_( 'Email Misc. Settings' ), array( &$this, 'email_misc_settings_callback' ), 'simple_wp_membership_settings' );

		add_settings_field(
			'email-misc-from',
			SwpmUtils::_( 'From Email Address' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'email-misc-settings',
			array(
				'item'    => 'email-from',
				'message' => 'This value will be used as the sender\'s address for the emails. Example value: Your Name &lt;sales@your-domain.com&gt;',
			)
		);

		add_settings_field(
			'email-enable-html',
			SwpmUtils::_( 'Allow HTML in Emails' ),
			array( $this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'email-misc-settings',
			array(
				'item'    => 'email-enable-html',
				'message' => 'Enables HTML support in emails. We recommend using plain text (non HTML) email as it has better email delivery rate.',
			)
		);

		//Prompt to complete registration email settings
		add_settings_section( 'reg-prompt-email-settings', SwpmUtils::_( 'Email Settings (Prompt to Complete Registration )' ), array( &$this, 'reg_prompt_email_settings_callback' ), 'simple_wp_membership_settings' );
		add_settings_field(
			'reg-prompt-complete-mail-subject',
			SwpmUtils::_( 'Email Subject' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'reg-prompt-email-settings',
			array(
				'item'    => 'reg-prompt-complete-mail-subject',
				'message' => '',
			)
		);
		add_settings_field(
			'reg-prompt-complete-mail-body',
			SwpmUtils::_( 'Email Body' ),
			array( &$this, 'wp_editor_callback' ),
			'simple_wp_membership_settings',
			'reg-prompt-email-settings',
			array(
				'item'    => 'reg-prompt-complete-mail-body',
				'message' => '',
			)
		);

		//Registration complete email settings
		$msg_for_admin_notify_email_field  = SwpmUtils::_( 'Enter the email address where you want the admin notification email to be sent to.' );
		$msg_for_admin_notify_email_field .= SwpmUtils::_( ' You can put multiple email addresses separated by comma (,) in the above field to send the notification to multiple email addresses.' );

		$msg_for_admin_notify_email_subj = SwpmUtils::_( 'Enter the subject for the admin notification email.' );
		$admin_notify_email_body_msg     = SwpmUtils::_( 'This email will be sent to the admin when a new user completes the membership registration. Only works if you have enabled the "Send Notification to Admin" option above.' );

		add_settings_section( 'reg-email-settings', SwpmUtils::_( 'Email Settings (Registration Complete)' ), array( &$this, 'reg_email_settings_callback' ), 'simple_wp_membership_settings' );
		add_settings_field(
			'reg-complete-mail-subject',
			SwpmUtils::_( 'Email Subject' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'reg-email-settings',
			array(
				'item'    => 'reg-complete-mail-subject',
				'message' => '',
			)
		);
		add_settings_field(
			'reg-complete-mail-body',
			SwpmUtils::_( 'Email Body' ),
			array( &$this, 'wp_editor_callback' ),
			'simple_wp_membership_settings',
			'reg-email-settings',
			array(
				'item'    => 'reg-complete-mail-body',
				'message' => '',
			)
		);
		add_settings_field(
			'enable-admin-notification-after-reg',
			SwpmUtils::_( 'Send Notification to Admin' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'reg-email-settings',
			array(
				'item'    => 'enable-admin-notification-after-reg',
				'message' => SwpmUtils::_( 'Enable this option if you want the admin to receive a notification when a member registers.' ),
			)
		);
		add_settings_field(
			'admin-notification-email',
			SwpmUtils::_( 'Admin Email Address' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'reg-email-settings',
			array(
				'item'    => 'admin-notification-email',
				'message' => $msg_for_admin_notify_email_field,
			)
		);
		add_settings_field(
			'reg-complete-mail-subject-admin',
			SwpmUtils::_( 'Admin Notification Email Subject' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'reg-email-settings',
			array(
				'item'    => 'reg-complete-mail-subject-admin',
				'message' => $msg_for_admin_notify_email_subj,
			)
		);
		add_settings_field(
			'reg-complete-mail-body-admin',
			SwpmUtils::_( 'Admin Notification Email Body' ),
			array( &$this, 'wp_editor_callback' ),
			'simple_wp_membership_settings',
			'reg-email-settings',
			array(
				'item'    => 'reg-complete-mail-body-admin',
				'message' => $admin_notify_email_body_msg,
			)
		);

		add_settings_field(
			'enable-notification-after-manual-user-add',
			SwpmUtils::_( 'Send Email to Member When Added via Admin Dashboard' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'reg-email-settings',
			array(
				'item'    => 'enable-notification-after-manual-user-add',
				'message' => '',
			)
		);

		//Password reset email settings
		add_settings_section( 'reset-password-settings', SwpmUtils::_( 'Email Settings (Password Reset)' ), array( &$this, 'reset_password_settings_callback' ), 'simple_wp_membership_settings' );
		add_settings_field(
			'reset-mail-subject',
			SwpmUtils::_( 'Email Subject' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'reset-password-settings',
			array(
				'item'    => 'reset-mail-subject',
				'message' => '',
			)
		);
		add_settings_field(
			'reset-mail-body',
			SwpmUtils::_( 'Email Body' ),
			array( &$this, 'wp_editor_callback' ),
			'simple_wp_membership_settings',
			'reset-password-settings',
			array(
				'item'    => 'reset-mail-body',
				'message' => '',
			)
		);

		//Account upgrade email settings
		add_settings_section( 'upgrade-email-settings', SwpmUtils::_( ' Email Settings (Account Upgrade Notification)' ), array( &$this, 'upgrade_email_settings_callback' ), 'simple_wp_membership_settings' );
		add_settings_field(
			'upgrade-complete-mail-subject',
			SwpmUtils::_( 'Email Subject' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'upgrade-email-settings',
			array(
				'item'    => 'upgrade-complete-mail-subject',
				'message' => '',
			)
		);
		add_settings_field(
			'upgrade-complete-mail-body',
			SwpmUtils::_( 'Email Body' ),
			array( &$this, 'wp_editor_callback' ),
			'simple_wp_membership_settings',
			'upgrade-email-settings',
			array(
				'item'    => 'upgrade-complete-mail-body',
				'message' => '',
			)
		);
		add_settings_field(
			'disable-email-after-upgrade',
			SwpmUtils::_( 'Disable Email Notification After Upgrade' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'upgrade-email-settings',
			array(
				'item'    => 'disable-email-after-upgrade',
				'message' => SwpmUtils::_( 'You can use this option to disable the email notification that gets sent to the members when they make a payment for upgrade or renewal.' ),
			)
		);

		//Bulk account activate and notify email settings.
		add_settings_section( 'bulk-activate-email-settings', SwpmUtils::_( ' Email Settings (Bulk Account Activate Notification)' ), array( &$this, 'bulk_activate_email_settings_callback' ), 'simple_wp_membership_settings' );
		add_settings_field(
			'bulk-activate-notify-mail-subject',
			SwpmUtils::_( 'Email Subject' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'bulk-activate-email-settings',
			array(
				'item'    => 'bulk-activate-notify-mail-subject',
				'message' => '',
			)
		);
		add_settings_field(
			'bulk-activate-notify-mail-body',
			SwpmUtils::_( 'Email Body' ),
			array( &$this, 'wp_editor_callback' ),
			'simple_wp_membership_settings',
			'bulk-activate-email-settings',
			array(
				'item'    => 'bulk-activate-notify-mail-body',
				'message' => '',
			)
		);

		//Email activation email settings.
		add_settings_section( 'email-activation-email-settings', SwpmUtils::_( ' Email Settings (Email Activation)' ), array( &$this, 'email_activation_email_settings_callback' ), 'simple_wp_membership_settings' );
		add_settings_field(
			'email-activation-mail-subject',
			SwpmUtils::_( 'Email Subject' ),
			array( &$this, 'textfield_callback' ),
			'simple_wp_membership_settings',
			'email-activation-email-settings',
			array(
				'item'    => 'email-activation-mail-subject',
				'message' => '',
			)
		);
		add_settings_field(
			'email-activation-mail-body',
			SwpmUtils::_( 'Email Body' ),
			array( &$this, 'wp_editor_callback' ),
			'simple_wp_membership_settings',
			'email-activation-email-settings',
			array(
				'item'    => 'email-activation-mail-body',
				'message' => '',
			)
		);
	}

	private function tab_4() {
		//Register settings sections and fileds for the tools tab.
	}

	private function tab_5() {
		//Register settings sections and fileds for the advanced settings tab.

		register_setting( 'swpm-settings-tab-5', 'swpm-settings', array( &$this, 'sanitize_tab_5' ) );

		add_settings_section( 'advanced-settings', SwpmUtils::_( 'Advanced Settings' ), array( &$this, 'advanced_settings_callback' ), 'simple_wp_membership_settings' );

		add_settings_field(
			'enable-expired-account-login',
			SwpmUtils::_( 'Enable Expired Account Login' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'enable-expired-account-login',
				'message' => SwpmUtils::_( "When enabled, expired members will be able to log into the system but won't be able to view any protected content. This allows them to easily renew their account by making another payment." ),
			)
		);

		add_settings_field(
			'renewal-page-url',
			SwpmUtils::_( 'Membership Renewal URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'renewal-page-url',
				'message' => SwpmUtils::_( 'You can create a renewal page for your site. Read <a href="https://simple-membership-plugin.com/creating-membership-renewal-button/" target="_blank">this documentation</a> to learn how to create a renewal page.' ),
			)
		);

		add_settings_field(
			'after-rego-redirect-page-url',
			SwpmUtils::_( 'After Registration Redirect URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'after-rego-redirect-page-url',
				'message' => SwpmUtils::_( 'You can enter an URL here to redirect the members to this page after they submit the registration form. Read <a href="https://simple-membership-plugin.com/configure-after-registration-redirect-for-members/" target="_blank">this documentation</a> to learn how to setup after registration redirect.' ),
			)
		);

		add_settings_field(
			'auto-login-after-rego',
			SwpmUtils::_( 'Enable Auto Login After Registration' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'auto-login-after-rego',
				'message' => SwpmUtils::_( 'Use this option if you want the members to be automatically logged into your site right after they complete the registration. This option will override any after registration redirection and instead it will trigger the after login redirection. Read <a href="https://simple-membership-plugin.com/configure-auto-login-after-registration-members/" target="_blank">this documentation</a> to learn more.' ),
			)
		);

		add_settings_field(
			'hide-rego-form-to-logged-users',
			SwpmUtils::_( 'Hide Registration Form to Logged Users' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'hide-rego-form-to-logged-users',
				'message' => SwpmUtils::_( 'Use this option if you want to hide the registration form to the logged-in members. If logged-in members visit the registration page, they will see a message instead of the registration form.' ),
			)
		);

		add_settings_field(
			'after-logout-redirection-url',
			SwpmUtils::_( 'After Logout Redirect URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'after-logout-redirection-url',
				'message' => SwpmUtils::_( 'You can enter an URL here to redirect the members to this page after they click the logout link to logout from your site.' ),
			)
		);

		add_settings_field(
			'logout-member-on-browser-close',
			SwpmUtils::_( 'Logout Member on Browser Close' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'logout-member-on-browser-close',
				'message' => SwpmUtils::_( 'Enable this option if you want the member to be logged out of the account when he closes the browser.' ),
			)
		);

		add_settings_field(
			'allow-account-deletion',
			SwpmUtils::_( 'Allow Account Deletion' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'allow-account-deletion',
				'message' => SwpmUtils::_( 'Allow users to delete their accounts.' ),
			)
		);

		add_settings_field(
			'force-strong-passwords',
			SwpmUtils::_( 'Force Strong Password for Members' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'force-strong-passwords',
				'message' => SwpmUtils::_( 'Enable this if you want the users to be forced to use a strong password for their accounts.' ),
			)
		);

		add_settings_field(
			'delete-pending-account',
			SwpmUtils::_( 'Auto Delete Pending Account' ),
			array( &$this, 'selectbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'delete-pending-account',
				'options' => array(
					0 => 'Do not delete',
					1 => 'Older than 1 month',
					2 => 'Older than 2 months',
				),
				'default' => '0',
				'message' => SwpmUtils::_( 'Select how long you want to keep "pending" account.' ),
			)
		);

		add_settings_field(
			'admin-dashboard-access-permission',
			SwpmUtils::_( 'Admin Dashboard Access Permission' ),
			array( &$this, 'selectbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'admin-dashboard-access-permission',
				'options' => array(
					'manage_options'       => translate_user_role( 'Administrator' ),
					'edit_pages'           => translate_user_role( 'Editor' ),
					'edit_published_posts' => translate_user_role( 'Author' ),
					'edit_posts'           => translate_user_role( 'Contributor' ),
				),
				'default' => 'manage_options',
				'message' => SwpmUtils::_( 'SWPM admin dashboard is accessible to admin users only (just like any other plugin). You can allow users with other WP user roles to access the SWPM admin dashboard by selecting a value here. Note that this option cannot work if you enabled the "Disable Access to WP Dashboard" option in General Settings.' ),
			)
		);

		add_settings_field(
			'force-wp-user-sync',
			SwpmUtils::_( 'Force WP User Synchronization' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'force-wp-user-sync',
				'message' => SwpmUtils::_( 'Enable this option if you want to force the member login to be synchronized with WP user account. This can be useful if you are using another plugin that uses WP user records. For example: bbPress plugin.' ),
			)
		);

		//Auto create SWPM user related settings section
		add_settings_section( 'auto-create-swpm-user-settings', SwpmUtils::_( 'Create Member Accounts for New WP Users' ), array( &$this, 'advanced_settings_auto_create_swpm_uses_settings_callback' ), 'simple_wp_membership_settings' );

		add_settings_field(
			'enable-auto-create-swpm-members',
			SwpmUtils::_( 'Enable Auto Create Member Accounts' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'auto-create-swpm-user-settings',
			array(
				'item'    => 'enable-auto-create-swpm-members',
				'message' => SwpmUtils::_( 'Enable this option to automatically create member accounts for any new WP user that is created by another plugin.' ),
			)
		);

		$levels_array = SwpmMembershipLevelUtils::get_all_membership_levels_in_array();
		add_settings_field(
			'auto-create-default-membership-level',
			SwpmUtils::_( 'Default Membership Level' ),
			array( &$this, 'selectbox_callback' ),
			'simple_wp_membership_settings',
			'auto-create-swpm-user-settings',
			array(
				'item'    => 'auto-create-default-membership-level',
				'options' => $levels_array,
				'default' => '',
				'message' => SwpmUtils::_( 'When automatically creating a member account using this feature, the membership level of the user will be set to the one you specify here.' ),
			)
		);

		$status_array = SwpmUtils::get_account_state_options();
		add_settings_field(
			'auto-create-default-account-status',
			SwpmUtils::_( 'Default Account Status' ),
			array( &$this, 'selectbox_callback' ),
			'simple_wp_membership_settings',
			'auto-create-swpm-user-settings',
			array(
				'item'    => 'auto-create-default-account-status',
				'options' => $status_array,
				'default' => '',
				'message' => SwpmUtils::_( 'When automatically creating a member account using this feature, the membership account status of the user will be set to the one you specify here.' ),
			)
		);

		add_settings_field(
			'payment-notification-forward-url',
			SwpmUtils::_( 'Payment Notification Forward URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'advanced-settings',
			array(
				'item'    => 'payment-notification-forward-url',
				'message' => SwpmUtils::_( 'You can enter an URL here to forward the payment notification after the membership payment has been processed by this plugin. Useful if you want to forward the payment notification to an external script for further processing.' ),
			)
		);

		//Terms and conditions section
		add_settings_section( 'terms-and-conditions', SwpmUtils::_( 'Terms and Conditions' ), array( &$this, 'advanced_settings_terms_and_conditions_callback' ), 'simple_wp_membership_settings' );

		add_settings_field(
			'enable-terms-and-conditions',
			SwpmUtils::_( 'Enable Terms and Conditions' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'terms-and-conditions',
			array(
				'item'    => 'enable-terms-and-conditions',
				'message' => SwpmUtils::_( 'Users must accept the terms before they can complete the registration.' ),
			)
		);
		add_settings_field(
			'terms-and-conditions-page-url',
			SwpmUtils::_( 'Terms and Conditions Page URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'terms-and-conditions',
			array(
				'item'    => 'terms-and-conditions-page-url',
				'message' => SwpmUtils::_( 'Enter the URL of your terms and conditions page. You can create a WordPress page and specify your terms in there then specify the URL of that page in the above field.' ),
			)
		);
		add_settings_field(
			'enable-privacy-policy',
			SwpmUtils::_( 'Enable Privacy Policy' ),
			array( &$this, 'checkbox_callback' ),
			'simple_wp_membership_settings',
			'terms-and-conditions',
			array(
				'item'    => 'enable-privacy-policy',
				'message' => SwpmUtils::_( 'Users must accept it before they can complete the registration.' ),
			)
		);
		add_settings_field(
			'privacy-policy-page-url',
			SwpmUtils::_( 'Privacy Policy Page URL' ),
			array( &$this, 'textfield_long_callback' ),
			'simple_wp_membership_settings',
			'terms-and-conditions',
			array(
				'item'    => 'privacy-policy-page-url',
				'message' => SwpmUtils::_( 'Enter the URL of your privacy policy page.' ),
			)
		);
	}

	private function tab_6() {
		//Register settings sections and fileds for the addon settings tab.
	}

	public static function get_instance() {
		self::$_this = empty( self::$_this ) ? new SwpmSettings() : self::$_this;
		return self::$_this;
	}

	public function selectbox_callback( $args ) {
		$item     = $args['item'];
		$options  = $args['options'];
		$default  = $args['default'];
		$msg      = isset( $args['message'] ) ? $args['message'] : '';
		$selected = esc_attr( $this->get_value( $item, $default ) );
		echo "<select name='swpm-settings[" . $item . "]' >";
		foreach ( $options as $key => $value ) {
			$is_selected = ( $key == $selected ) ? 'selected="selected"' : '';
			echo '<option ' . $is_selected . ' value="' . esc_attr( $key ) . '">' . esc_attr( $value ) . '</option>';
		}
		echo '</select>';
		echo '<br/><i>' . $msg . '</i>';
	}

	public function checkbox_callback( $args ) {
		$item = $args['item'];
		$msg  = isset( $args['message'] ) ? $args['message'] : '';
		$is   = esc_attr( $this->get_value( $item ) );
		echo "<input type='checkbox' $is name='swpm-settings[" . $item . "]' value=\"checked='checked'\" />";
		echo '<br/><i>' . $msg . '</i>';
	}

	public function textarea_callback( $args ) {
		$item = $args['item'];
		$msg  = isset( $args['message'] ) ? $args['message'] : '';
		$text = esc_attr( $this->get_value( $item ) );
		echo "<textarea name='swpm-settings[" . $item . "]'  rows='6' cols='60' >" . $text . '</textarea>';
		echo '<br/><i>' . $msg . '</i>';
	}

	public function textfield_small_callback( $args ) {
		$item = $args['item'];
		$msg  = isset( $args['message'] ) ? $args['message'] : '';
		$text = esc_attr( $this->get_value( $item ) );
		echo "<input type='text' name='swpm-settings[" . $item . "]'  size='5' value='" . $text . "' />";
		echo '<br/><i>' . $msg . '</i>';
	}

	public function textfield_callback( $args ) {
		$item = $args['item'];
		$msg  = isset( $args['message'] ) ? $args['message'] : '';
		$text = $this->get_value( $item );
		echo sprintf( '<input type="text" name="swpm-settings[%s]" size="50" value="%s" />', esc_attr( $item ), esc_attr( $text ) );
		echo sprintf( '<p class="description">%s</p>', $msg );
	}

	public function textfield_long_callback( $args ) {
		$item = $args['item'];
		$msg  = isset( $args['message'] ) ? $args['message'] : '';
		$text = esc_attr( $this->get_value( $item ) );
		echo "<input type='text' name='swpm-settings[" . $item . "]'  size='100' value='" . $text . "' />";
		echo '<br/><i>' . $msg . '</i>';
	}

	public function set_default_editor( $r ) {
		$r = 'html';
		return $r;
	}

	public function wp_editor_callback( $args ) {
		$item         = $args['item'];
		$msg          = isset( $args['message'] ) ? $args['message'] : '';
		$text         = $this->get_value( $item );
		$html_enabled = $this->get_value( 'email-enable-html' );
		add_filter( 'wp_default_editor', array( $this, 'set_default_editor' ) );
		echo '<style>#wp-' . esc_attr( sprintf( '%s', $item ) ) . '-wrap{max-width:40em;}</style>';
		wp_editor(
			html_entity_decode( $text ),
			$item,
			array(
				'textarea_name'  => 'swpm-settings[' . $item . ']',
				'teeny'          => true,
				'default_editor' => ! empty( $html_enabled ) ? 'QuickTags' : '',
				'textarea_rows'  => 15,
			)
		);
		remove_filter( 'wp_default_editor', array( $this, 'set_default_editor' ) );
		echo "<p class=\"description\">{$msg}</p>";
	}

	public function swpm_documentation_callback() {
		?>
		<div class="swpm-orange-box">
			<?php printf( SwpmUtils::_( 'Visit the %s to read setup and configuration documentation.' ), '<a target="_blank" href="https://simple-membership-plugin.com/">' . SwpmUtils::_( 'Simple Membership Plugin Site' ) . '</a>' ); ?>
			<?php printf( SwpmUtils::_( 'Please %s if you like the plugin.' ), '<a href="https://wordpress.org/support/view/plugin-reviews/simple-membership?filter=5" target="_blank">' . SwpmUtils::_( 'give us a rating' ) . '</a>' ); ?>
		</div>
		<?php
	}

	public function swpm_general_post_submit_check_callback() {
		//Log file reset handler
		if ( isset( $_REQUEST['swmp_reset_log'] ) ) {
			if ( SwpmLog::reset_swmp_log_files() ) {
				echo '<div id="message" class="updated fade"><p>Debug log files have been reset!</p></div>';
			} else {
				echo '<div id="message" class="updated fade"><p>Debug log files could not be reset!</p></div>';
			}
		}

		//Show settings updated message
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			echo '<div id="message" class="updated fade"><p>' . SwpmUtils::_( 'Settings updated!' ) . '</p></div>';
		}
	}

	public function general_settings_callback() {
		SwpmUtils::e( 'General Plugin Settings.' );
	}

	public function pages_settings_callback() {
		SwpmUtils::e( 'Page Setup and URL Related settings.' );

		echo '<p>';
		SwpmUtils::e( 'The following pages are required for the plugin to function correctly. These pages were automatically created by the plugin at install time.' );
		echo '</p>';
	}

	public function testndebug_settings_callback() {
		SwpmUtils::e( 'Testing and Debug Related Settings.' );
	}

	public function reg_email_settings_callback() {
		SwpmUtils::e( 'This email will be sent to your users when they complete the registration and become a member.' );
	}

	public function reset_password_settings_callback() {
		SwpmUtils::e( 'This email will be sent to your users when they use the password reset functionality.' );
	}

	public function email_settings_overview_callback() {
		echo '<div class="swpm-grey-box">';
		echo '<p>';
		SwpmUtils::e( 'This interface lets you customize the various emails that get sent to your members for various actions. The default settings should be good to get your started.' );
		echo '</p>';

		echo '<p>';
		echo '<a href="https://simple-membership-plugin.com/email-merge-tags-email-shortcodes-for-email-customization/" target="_blank">' . SwpmUtils::_( 'This documentation' ) . '</a>';
		SwpmUtils::e( ' explains what email merge tags you can use in the email body field to customize it (if you want to).' );
		echo '</p>';
		echo '</div>';
	}

	public function email_misc_settings_callback() {

		//Show settings updated message when it is updated
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			//This status message need to be in the callback function to prevent header sent warning
			echo '<div id="message" class="updated fade"><p>' . SwpmUtils::_( 'Settings updated!' ) . '</p></div>';
		}

		SwpmUtils::e( 'Settings in this section apply to all emails.' );
	}

	public function upgrade_email_settings_callback() {
		SwpmUtils::e( 'This email will be sent to your users after account upgrade (when an existing member pays for a new membership level).' );
	}

	public function bulk_activate_email_settings_callback() {
		SwpmUtils::e( 'This email will be sent to your members when you use the bulk account activate and notify action.' );
		SwpmUtils::e( ' You cannot use email merge tags in this email. You can only use generic text.' );
	}

	public function email_activation_email_settings_callback() {
		SwpmUtils::e( 'This email will be sent if Email Activation is enabled for a Membership Level.' );
	}

	public function reg_prompt_email_settings_callback() {
		SwpmUtils::e( 'This email will be sent to prompt users to complete registration after the payment.' );
	}

	public function advanced_settings_callback() {

		//Show settings updated message when it is updated
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			//This status message need to be in the callback function to prevent header sent warning
			echo '<div id="message" class="updated fade"><p>' . SwpmUtils::_( 'Settings updated!' ) . '</p></div>';

                        /* Check if any conflicting setting options have been enabled together. */
                        $disable_wp_dashboard_for_non_admins = $this->get_value('disable-access-to-wp-dashboard');
                        if ($disable_wp_dashboard_for_non_admins) {
                            //The disable wp dashboard option is enabled.
                            //Check to make sure the "Admin Dashboard Access Permission" option is not being used for other roles.
                            $admin_dashboard_permission = $this->get_value( 'admin-dashboard-access-permission' );
                            if ( empty( $admin_dashboard_permission ) || $admin_dashboard_permission == 'manage_options' ) {
                                //This is fine.
                            } else {
                                //Conflicting options enabled.
                                //Show warning and reset the option value to default.
                                $this->set_value('admin-dashboard-access-permission', 'manage_options');
                                $this->save();
                                echo '<div id="message" class="error"><p>' . SwpmUtils::_( 'Note: You cannot enable both the "Disable Access to WP Dashboard" and "Admin Dashboard Access Permission" options at the same time. Only use one of those options.' ) . '</p></div>';
                            }
                        }
                        /* End of conflicting options check */

		}

		SwpmUtils::e( 'This page allows you to configure some advanced features of the plugin.' );
	}

	public function advanced_settings_auto_create_swpm_uses_settings_callback() {
		SwpmUtils::e( 'This section allows you to configure automatic creation of member accounts when new WP User records are created by another plugin. It can be useful if you are using another plugin that creates WP user records and you want them to be recognized in the membership plugin.' );
	}

	public function advanced_settings_terms_and_conditions_callback() {
		SwpmUtils::e( 'This section allows you to configure terms and conditions and privacy policy that users must accept at registration time.' );
	}

	public function sanitize_tab_1( $input ) {
		if ( empty( $this->settings ) ) {
			$this->settings = (array) get_option( 'swpm-settings' );
		}
		$output = $this->settings;
		//general settings block

		$output['hide-adminbar']                  = isset( $input['hide-adminbar'] ) ? esc_attr( $input['hide-adminbar'] ) : '';
		$output['show-adminbar-admin-only']       = isset( $input['show-adminbar-admin-only'] ) ? esc_attr( $input['show-adminbar-admin-only'] ) : '';
		$output['disable-access-to-wp-dashboard'] = isset( $input['disable-access-to-wp-dashboard'] ) ? esc_attr( $input['disable-access-to-wp-dashboard'] ) : '';

		$output['protect-everything']     = isset( $input['protect-everything'] ) ? esc_attr( $input['protect-everything'] ) : '';
		$output['enable-free-membership'] = isset( $input['enable-free-membership'] ) ? esc_attr( $input['enable-free-membership'] ) : '';
		$output['enable-moretag']         = isset( $input['enable-moretag'] ) ? esc_attr( $input['enable-moretag'] ) : '';
		$output['enable-debug']           = isset( $input['enable-debug'] ) ? esc_attr( $input['enable-debug'] ) : '';
		$output['enable-sandbox-testing'] = isset( $input['enable-sandbox-testing'] ) ? esc_attr( $input['enable-sandbox-testing'] ) : '';

		$output['free-membership-id']       = ( $input['free-membership-id'] != 1 ) ? absint( $input['free-membership-id'] ) : '';
		$output['login-page-url']           = esc_url( $input['login-page-url'] );
		$output['registration-page-url']    = esc_url( $input['registration-page-url'] );
		$output['profile-page-url']         = esc_url( $input['profile-page-url'] );
		$output['reset-page-url']           = esc_url( $input['reset-page-url'] );
		$output['join-us-page-url']         = esc_url( $input['join-us-page-url'] );
		$output['default-account-status']   = esc_attr( $input['default-account-status'] );
		$output['members-login-to-comment'] = isset( $input['members-login-to-comment'] ) ? esc_attr( $input['members-login-to-comment'] ) : '';

		return $output;
	}

	public function sanitize_tab_2( $input ) {
		if ( empty( $this->settings ) ) {
			$this->settings = (array) get_option( 'swpm-settings' );
		}
		$output = $this->settings;

		$output['stripe-test-public-key'] = sanitize_text_field( $input['stripe-test-public-key'] );
		$output['stripe-test-secret-key'] = sanitize_text_field( $input['stripe-test-secret-key'] );
		$output['stripe-live-public-key'] = sanitize_text_field( $input['stripe-live-public-key'] );
		$output['stripe-live-secret-key'] = sanitize_text_field( $input['stripe-live-secret-key'] );

		$output['stripe-prefill-member-email'] = isset( $input['stripe-prefill-member-email'] ) ? esc_attr( $input['stripe-prefill-member-email'] ) : 0;

		return $output;
	}

	public function sanitize_tab_3( $input ) {
		if ( empty( $this->settings ) ) {
			$this->settings = (array) get_option( 'swpm-settings' );
		}
		$output                              = $this->settings;
		$output['reg-complete-mail-subject'] = sanitize_text_field( $input['reg-complete-mail-subject'] );

		$output['reg-complete-mail-body']          = $input['reg-complete-mail-body'];
		$output['reg-complete-mail-subject-admin'] = sanitize_text_field( $input['reg-complete-mail-subject-admin'] );
		$output['reg-complete-mail-body-admin']    = $input['reg-complete-mail-body-admin'];

		$output['reset-mail-subject'] = sanitize_text_field( $input['reset-mail-subject'] );
		$output['reset-mail-body']    = $input['reset-mail-body'];

		$output['upgrade-complete-mail-subject'] = sanitize_text_field( $input['upgrade-complete-mail-subject'] );
		$output['upgrade-complete-mail-body']    = $input['upgrade-complete-mail-body'];
		$output['disable-email-after-upgrade']   = isset( $input['disable-email-after-upgrade'] ) ? esc_attr( $input['disable-email-after-upgrade'] ) : '';

		$output['bulk-activate-notify-mail-subject'] = sanitize_text_field( $input['bulk-activate-notify-mail-subject'] );
		$output['bulk-activate-notify-mail-body']    = $input['bulk-activate-notify-mail-body'];

		$output['email-activation-mail-subject'] = sanitize_text_field( $input['email-activation-mail-subject'] );
		$output['email-activation-mail-body']    = $input['email-activation-mail-body'];

		$output['reg-prompt-complete-mail-subject'] = sanitize_text_field( $input['reg-prompt-complete-mail-subject'] );
		$output['reg-prompt-complete-mail-body']    = $input['reg-prompt-complete-mail-body'];
		$output['email-from']                       = trim( $input['email-from'] );
		$output['email-enable-html']                = isset( $input['email-enable-html'] ) ? esc_attr( $input['email-enable-html'] ) : '';

		$output['enable-admin-notification-after-reg']       = isset( $input['enable-admin-notification-after-reg'] ) ? esc_attr( $input['enable-admin-notification-after-reg'] ) : '';
		$output['admin-notification-email']                  = sanitize_text_field( $input['admin-notification-email'] );
		$output['enable-notification-after-manual-user-add'] = isset( $input['enable-notification-after-manual-user-add'] ) ? esc_attr( $input['enable-notification-after-manual-user-add'] ) : '';

		return $output;
	}

	public function sanitize_tab_5( $input ) {
		if ( empty( $this->settings ) ) {
			$this->settings = (array) get_option( 'swpm-settings' );
		}
		$output                                      = $this->settings;
		$output['enable-expired-account-login']      = isset( $input['enable-expired-account-login'] ) ? esc_attr( $input['enable-expired-account-login'] ) : '';
		$output['logout-member-on-browser-close']    = isset( $input['logout-member-on-browser-close'] ) ? esc_attr( $input['logout-member-on-browser-close'] ) : '';
		$output['allow-account-deletion']            = isset( $input['allow-account-deletion'] ) ? esc_attr( $input['allow-account-deletion'] ) : '';
		$output['delete-pending-account']            = isset( $input['delete-pending-account'] ) ? esc_attr( $input['delete-pending-account'] ) : 0;
		$output['admin-dashboard-access-permission'] = isset( $input['admin-dashboard-access-permission'] ) ? esc_attr( $input['admin-dashboard-access-permission'] ) : '';
		$output['renewal-page-url']                  = esc_url( $input['renewal-page-url'] );
		$output['after-rego-redirect-page-url']      = esc_url( $input['after-rego-redirect-page-url'] );
		$output['after-logout-redirection-url']      = esc_url( $input['after-logout-redirection-url'] );
		$output['force-strong-passwords']            = isset( $input['force-strong-passwords'] ) ? esc_attr( $input['force-strong-passwords'] ) : '';
		$output['auto-login-after-rego']             = isset( $input['auto-login-after-rego'] ) ? esc_attr( $input['auto-login-after-rego'] ) : '';
                $output['hide-rego-form-to-logged-users']    = isset( $input['hide-rego-form-to-logged-users'] ) ? esc_attr( $input['hide-rego-form-to-logged-users'] ) : '';
		$output['force-wp-user-sync']                = isset( $input['force-wp-user-sync'] ) ? esc_attr( $input['force-wp-user-sync'] ) : '';
		$output['payment-notification-forward-url']  = esc_url( $input['payment-notification-forward-url'] );

		//Auto create swpm user related settings
		$output['enable-auto-create-swpm-members']      = isset( $input['enable-auto-create-swpm-members'] ) ? esc_attr( $input['enable-auto-create-swpm-members'] ) : '';
		$output['auto-create-default-membership-level'] = isset( $input['auto-create-default-membership-level'] ) ? esc_attr( $input['auto-create-default-membership-level'] ) : '';
		$output['auto-create-default-account-status']   = isset( $input['auto-create-default-account-status'] ) ? esc_attr( $input['auto-create-default-account-status'] ) : '';
		//Terms and conditions related settings
		$output['enable-terms-and-conditions']   = isset( $input['enable-terms-and-conditions'] ) ? esc_attr( $input['enable-terms-and-conditions'] ) : '';
		$output['terms-and-conditions-page-url'] = esc_url( $input['terms-and-conditions-page-url'] );
		$output['enable-privacy-policy']         = isset( $input['enable-privacy-policy'] ) ? esc_attr( $input['enable-privacy-policy'] ) : '';
		$output['privacy-policy-page-url']       = esc_url( $input['privacy-policy-page-url'] );
		return $output;
	}

	public function get_value( $key, $default = '' ) {
		if ( isset( $this->settings[ $key ] ) ) {
			return $this->settings[ $key ];
		}
		return $default;
	}

	public function set_value( $key, $value ) {
		$this->settings[ $key ] = $value;
		return $this;
	}

	public function save() {
		update_option( 'swpm-settings', $this->settings );
	}

	public function draw_tabs() {
		$current = $this->current_tab;
		?>
		<h2 class="nav-tab-wrapper">
			<?php foreach ( $this->tabs as $id => $label ) { ?>
				<a class="nav-tab <?php echo ( $current == $id ) ? 'nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_settings&tab=<?php echo $id; ?>"><?php echo $label; ?></a>
			<?php } ?>
		</h2>
		<?php
	}

	public function handle_main_settings_admin_menu() {
		do_action( 'swpm_settings_menu_start' );

		//Check current_user_can() or die.
		SwpmMiscUtils::check_user_permission_and_is_admin( 'Main Settings Menu' );

		?>
		<div class="wrap swpm-admin-menu-wrap"><!-- start wrap -->

			<h1><?php echo SwpmUtils::_( 'Simple WP Membership::Settings' ); ?></h1><!-- page title -->

			<!-- start nav menu tabs -->
			<?php do_action( 'swpm-draw-settings-nav-tabs' ); ?>
			<!-- end nav menu tabs -->
			<?php
			do_action( 'swpm_settings_menu_after_nav_tabs' );

			//Switch to handle the body of each of the various settings pages based on the currently selected tab
			$current_tab = $this->current_tab;
			switch ( $current_tab ) {
				case 1:
					//General settings
					include SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_settings.php';
					break;
				case 2:
					//Payment settings
					include SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/admin_payment_settings.php';
					break;
				case 3:
					//Email settings
					include SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_settings.php';
					break;
				case 4:
					//Tools
					include SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_tools_settings.php';
					break;
				case 5:
					//Advanced settings
					include SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_settings.php';
					break;
				case 6:
					//Addon settings
					include SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_addon_settings.php';
					break;
				default:
					//The default fallback (general settings)
					include SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_settings.php';
					break;
			}

			echo '</div>'; //<!-- end of wrap -->
	}

}

