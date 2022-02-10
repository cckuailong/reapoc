<?php
// Sanitize all PMPro email bodies. @since 2.6.1
add_filter( 'pmpro_email_body', 'pmpro_kses', 11 );

/**
 * The default name for WP emails is WordPress.
 * Use our setting instead.
 */
function pmpro_wp_mail_from_name($from_name)
{
	$default_from_name = 'WordPress';

	//make sure it's the default from name
	if($from_name == $default_from_name)
	{
		$pmpro_from_name = pmpro_getOption("from_name");
		if ($pmpro_from_name)
			$from_name = stripslashes($pmpro_from_name);
	}

	return $from_name;
}

/**
 * The default email address for WP emails is wordpress@sitename.
 * Use our setting instead.
 */
function pmpro_wp_mail_from($from_email)
{
	// default from email wordpress@sitename
	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}
	$default_from_email = 'wordpress@' . $sitename;

	//make sure it's the default email address
	if($from_email == $default_from_email)
	{
		$pmpro_from_email = pmpro_getOption("from_email");
		if ($pmpro_from_email && is_email( $pmpro_from_email ) )
			$from_email = $pmpro_from_email;
	}

	return $from_email;
}

// Are we filtering all WP emails or just PMPro ones?
$only_filter_pmpro_emails = pmpro_getOption("only_filter_pmpro_emails");
if($only_filter_pmpro_emails) {
	add_filter('pmpro_email_sender_name', 'pmpro_wp_mail_from_name');
	add_filter('pmpro_email_sender', 'pmpro_wp_mail_from');
} else {
	add_filter('wp_mail_from_name', 'pmpro_wp_mail_from_name');
	add_filter('wp_mail_from', 'pmpro_wp_mail_from');
}

/**
 * If the $email_member_notification option is empty, disable the wp_new_user_notification email at checkout.
 */
$email_member_notification = pmpro_getOption("email_member_notification");
if(empty($email_member_notification))
	add_filter("pmpro_wp_new_user_notification", "__return_false", 0);

/**
 * Adds template files and changes content type to html if using PHPMailer directly.
 */
function pmpro_send_html( $phpmailer ) {

	//to check if we should wpautop later
	$original_body = $phpmailer->Body;

	// Set the original plain text message
	$phpmailer->AltBody = wp_specialchars_decode($phpmailer->Body, ENT_QUOTES);
	// Clean < and > around text links in WP 3.1
	$phpmailer->Body = preg_replace('#<(https?://[^*]+)>#', '$1', $phpmailer->Body);

	// If there is no HTML, run through wpautop
	if($phpmailer->Body == strip_tags($phpmailer->Body))
		$phpmailer->Body = wpautop($phpmailer->Body);

	// Convert line breaks & make links clickable
	$phpmailer->Body = make_clickable ($phpmailer->Body);

	// Get header for message if found
	if(file_exists(get_stylesheet_directory() . "/email_header.html"))
		$header = file_get_contents(get_stylesheet_directory() . "/email_header.html");
	elseif(file_exists(get_template_directory() . "/email_header.html"))
		$header = file_get_contents(get_template_directory() . "/email_header.html");
	else
		$header = "";

	//wpautop header if needed
	if(!empty($header) && $header == strip_tags($header))
		$header = wpautop($header);

	// Get footer for message if found
	if(file_exists(get_stylesheet_directory() . "/email_footer.html"))
		$footer = file_get_contents(get_stylesheet_directory() . "/email_footer.html");
	elseif(file_exists(get_template_directory() . "/email_footer.html"))
		$footer =  file_get_contents(get_template_directory() . "/email_footer.html");
	else
		$footer = "";

	//wpautop header if needed
	if(!empty($footer) && $footer == strip_tags($footer))
		$footer = wpautop($footer);

	$header = apply_filters( 'pmpro_email_body_header', $header, $phpmailer );
	$footer = apply_filters( 'pmpro_email_body_footer', $footer, $phpmailer );

	// Add header/footer to the email
	if(!empty($header))
		$phpmailer->Body = $header . "\n" . $phpmailer->Body;
	if(!empty($footer))
		$phpmailer->Body = $phpmailer->Body . "\n" . $footer;

	// Replace variables in email
	global $current_user;
	$data = array(
				"name" => $current_user->display_name,
				"sitename" => get_option("blogname"),
				"login_link" => pmpro_url("account"),
				"login_url" => pmpro_url("account"),
				"display_name" => $current_user->display_name,
				"user_email" => $current_user->user_email,
				"subject" => $phpmailer->Subject
			);
	foreach($data as $key => $value)
	{
		$phpmailer->Body = str_replace("!!" . $key . "!!", $value, $phpmailer->Body);
	}

	do_action("pmpro_after_phpmailer_init", $phpmailer);
	do_action("pmpro_after_pmpmailer_init", $phpmailer);	//typo left in for backwards compatibility
}

/**
 * Change the content type of emails to HTML.
 */
function pmpro_wp_mail_content_type( $content_type ) {
	add_action('phpmailer_init', 'pmpro_send_html');

	// Change to html if not already.
	if( $content_type == 'text/plain') {
		$content_type = 'text/html';
	}

	return $content_type;
}
add_filter('wp_mail_content_type', 'pmpro_wp_mail_content_type');

/**
 * Filter the password reset email for compatibility with the HTML format.
 * We double check the wp_mail_content_type filter hasn't been disabled.
 * We check if there are already <br /> tags before running nl2br.
 * Running make_clickable() multiple times has no effect.
 */
function pmpro_retrieve_password_message( $message ) {
	if ( has_filter( 'wp_mail_content_type', 'pmpro_wp_mail_content_type' ) ) {
		$message = make_clickable( $message );

		if ( strpos( '<br', strtolower( $message ) ) === false ) {
			$message = nl2br( $message );
		}
	}

	return $message;
}
add_filter( 'retrieve_password_message', 'pmpro_retrieve_password_message', 10, 1 );

//get template data
function pmpro_email_templates_get_template_data() {

	check_ajax_referer('pmproet', 'security');

	if ( ! current_user_can( 'pmpro_emailtemplates' ) ) {
		die( __( 'You do not have permissions to perform this action.', 'paid-memberships-pro' ) );
	}

	global $pmpro_email_templates_defaults;

	$template = sanitize_text_field( $_REQUEST['template'] );

	//get template data
	$template_data['body'] = pmpro_getOption('email_' . $template . '_body');
	$template_data['subject'] = pmpro_getOption('email_' . $template . '_subject');
	$template_data['disabled'] = pmpro_getOption('email_' . $template . '_disabled');

	if (empty($template_data['body'])) {
		//if not found, load template
		$template_data['body'] = pmpro_email_templates_get_template_body($template);
	}

	if (empty($template_data['subject']) && $template != "header" && $template != "footer") {
		$template_data['subject'] = $pmpro_email_templates_defaults[$template]['subject'];
	}

	// Get template help text from defaults.
	$template_data['help_text'] = $pmpro_email_templates_defaults[$template]['help_text'];

	echo json_encode($template_data);

	exit;
}
add_action('wp_ajax_pmpro_email_templates_get_template_data', 'pmpro_email_templates_get_template_data');

//save template data
function pmpro_email_templates_save_template_data() {

	check_ajax_referer('pmproet', 'security');

	if ( ! current_user_can( 'pmpro_emailtemplates' ) ) {
		die( __( 'You do not have permissions to perform this action.', 'paid-memberships-pro' ) );
	}

	$template = sanitize_text_field( $_REQUEST['template'] );
	$subject = sanitize_text_field( wp_unslash( $_REQUEST['subject'] ) );
	$body = pmpro_kses( wp_unslash( $_REQUEST['body'] ), 'email' );

	//update this template's settings
	pmpro_setOption( 'email_' . $template . '_subject', $subject );
	pmpro_setOption( 'email_' . $template . '_body', $body );
	delete_transient( 'pmproet_' . $template );
	esc_html_e( 'Template Saved', 'paid-memberships-pro' );

	exit;
}
add_action('wp_ajax_pmpro_email_templates_save_template_data', 'pmpro_email_templates_save_template_data');

//reset template data
function pmpro_email_templates_reset_template_data() {

	check_ajax_referer('pmproet', 'security');

	if ( ! current_user_can( 'pmpro_emailtemplates' ) ) {
		die( __( 'You do not have permissions to perform this action.', 'paid-memberships-pro' ) );
	}

	global $pmpro_email_templates_defaults;

	$template = sanitize_text_field( $_REQUEST['template'] );

	delete_option('pmpro_email_' . $template . '_subject');
	delete_option('pmpro_email_' . $template . '_body');
	delete_transient( 'pmproet_' . $template );

	$template_data['subject'] = $pmpro_email_templates_defaults[$template]['subject'];
	$template_data['body'] = pmpro_email_templates_get_template_body($template);

	echo json_encode($template_data);
	exit;
}
add_action('wp_ajax_pmpro_email_templates_reset_template_data', 'pmpro_email_templates_reset_template_data');

// disable template
function pmpro_email_templates_disable_template() {

	check_ajax_referer('pmproet', 'security');

	if ( ! current_user_can( 'pmpro_emailtemplates' ) ) {
		die( __( 'You do not have permissions to perform this action.', 'paid-memberships-pro' ) );
	}

	$template = sanitize_text_field( $_REQUEST['template'] );
	$disabled = sanitize_text_field( $_REQUEST['disabled'] );
	$response['result'] = update_option('pmpro_email_' . $template . '_disabled', $disabled );
	$response['status'] = $disabled;
	echo json_encode($response);
	exit;
}
add_action('wp_ajax_pmpro_email_templates_disable_template', 'pmpro_email_templates_disable_template');

//send test email
function pmpro_email_templates_send_test() {

	check_ajax_referer('pmproet', 'security');

	if ( ! current_user_can( 'pmpro_emailtemplates' ) ) {
		die( __( 'You do not have permissions to perform this action.', 'paid-memberships-pro' ) );
	}

	global $current_user;

	//setup test email
	$test_email = new PMProEmail();
	$test_email->to = sanitize_email( $_REQUEST['email'] );
	$test_email->template = sanitize_text_field( str_replace('email_', '', $_REQUEST['template']) );

	//add filter to change recipient
	add_filter('pmpro_email_recipient', 'pmpro_email_templates_test_recipient', 10, 2);

	//load test order
	$test_order = new MemberOrder();
	$test_order->get_test_order();

	$test_user = $current_user;

	// Grab the first membership level defined as a "test level" to use
	$all_levels = pmpro_getAllLevels( true);
	$test_user->membership_level = array_pop( $all_levels );

	//add notice to email body
	add_filter('pmpro_email_body', 'pmpro_email_templates_test_body', 10, 2);

	//force the template
	add_filter('pmpro_email_filter', 'pmpro_email_templates_test_template', 5, 1);

	//figure out how to send the email
	switch($test_email->template) {
		case 'cancel':
			$send_email = 'sendCancelEmail';
			$params = array($test_user);
			break;
		case 'cancel_admin':
			$send_email = 'sendCancelAdminEmail';
			$params = array($current_user, $current_user->membership_level->id);
			break;
		case 'checkout_check':
		case 'checkout_express':
		case 'checkout_free':
		case 'checkout_freetrial':
		case 'checkout_paid':
		case 'checkout_trial':
			$send_email = 'sendCheckoutEmail';
			$params = array($test_user, $test_order);
			break;
		case 'checkout_check_admin':
		case 'checkout_express_admin':
		case 'checkout_free_admin':
		case 'checkout_freetrial_admin':
		case 'checkout_paid_admin':
		case 'checkout_trial_admin':
			$send_email = 'sendCheckoutAdminEmail';
			$params = array($test_user, $test_order);
			break;
		case 'billing':
			$send_email = 'sendBillingEmail';
			$params = array($test_user, $test_order);
			break;
		case 'billing_admin':
			$send_email = 'sendBillingAdminEmail';
			$params = array($test_user, $test_order);
			break;
		case 'billing_failure':
			$send_email = 'sendBillingFailureEmail';
			$params = array($test_user, $test_order);
			break;
		case 'billing_failure_admin':
			$send_email = 'sendBillingFailureAdminEmail';
			$params = array($test_user->user_email, $test_order);
			break;
		case 'credit_card_expiring':
			$send_email = 'sendCreditCardExpiringEmail';
			$params = array($test_user, $test_order);
			break;
		case 'invoice':
			$send_email = 'sendInvoiceEmail';
			$params = array($test_user, $test_order);
			break;
		case 'trial_ending':
			$send_email = 'sendTrialEndingEmail';
			$params = array($test_user);
			break;
		case 'membership_expired';
			$send_email = 'sendMembershipExpiredEmail';
			$params = array($test_user);
			break;
		case 'membership_expiring';
			$send_email = 'sendMembershipExpiringEmail';
			$params = array($test_user);
			break;
		case 'payment_action':
			$send_email = 'sendPaymentActionRequiredEmail';
			$params = array($test_user, $test_order, "http://www.example-notification-url.com/not-a-real-site");
			break;
		case 'payment_action_admin':
			$send_email = 'sendPaymentActionRequiredAdminEmail';
			$params = array($test_user, $test_order, "http://www.example-notification-url.com/not-a-real-site");
			break;
		default:
			$send_email = 'sendEmail';
			$params = array();
	}

	//send the email
	$response = call_user_func_array(array($test_email, $send_email), $params);

	//return the response
	echo $response;
	exit;
}
add_action('wp_ajax_pmpro_email_templates_send_test', 'pmpro_email_templates_send_test');

function pmpro_email_templates_test_recipient($email) {
	if(!empty($_REQUEST['email']))
		$email = sanitize_email( $_REQUEST['email'] );
	return $email;
}

//for test emails
function pmpro_email_templates_test_body($body, $email = null) {
	$body .= '<br /><br /><b>-- ' . __('THIS IS A TEST EMAIL', 'paid-memberships-pro') . ' --</b>';
	return $body;
}

function pmpro_email_templates_test_template($email)
{
	if( ! empty( $_REQUEST['template'] ) ) {
		$email->template = sanitize_text_field( str_replace('email_', '', $_REQUEST['template']) );
	}

	return $email;
}

/* Filter for Variables */
function pmpro_email_templates_email_data($data, $email) {

	global $pmpro_currency_symbol;

	if ( ! empty( $data ) && ! empty( $data['user_login'] ) ) {
		$user = get_user_by( 'login', $data['user_login'] );
	} elseif ( ! empty( $email ) ) {
		$user = get_user_by( 'email', $email->email );
	} else {
		$user = wp_get_current_user();
	}

	// Make sure we have the current membership level data.
	if ( $user instanceof WP_User ) {
		$user->membership_level = pmpro_getMembershipLevelForUser(
			$user->ID,
			true
		);
	}

	//make sure data is an array
	if(!is_array($data))
		$data = array();

	//general data
	$new_data['sitename'] = get_option("blogname");
	$new_data['siteemail'] = pmpro_getOption("from_email");
	if(empty($new_data['login_link'])) {
		$new_data['login_link'] = wp_login_url();
		$new_data['login_url'] = wp_login_url();
	}
	$new_data['levels_link'] = pmpro_url("levels");

	// User Data.
	if ( ! empty( $user ) ) {
		$new_data['name'] = $user->display_name;
		$new_data['user_login'] = $user->user_login;
		$new_data['display_name'] = $user->display_name;
		$new_data['user_email'] = $user->user_email;

		// Membership Information.
		$new_data['membership_expiration'] = '';
		$new_data["membership_change"] = __("Your membership has been cancelled.", "paid-memberships-pro");
		if ( empty( $user->membership_level ) ) {
			$user->membership_level = pmpro_getMembershipLevelForUser($user->ID, true);
		}
		if ( ! empty( $user->membership_level ) ) {
			if ( ! empty( $user->membership_level->name ) ) {
				$new_data["membership_change"] = sprintf(__("The new level is %s.", "paid-memberships-pro"), $user->membership_level->name);
			}
			if ( ! empty($user->membership_level->startdate) ) {
				$new_data['startdate'] = date_i18n( get_option( 'date_format' ), $user->membership_level->startdate );
			}
			if ( ! empty($user->membership_level->enddate) ) {
				$new_data['enddate'] = date_i18n( get_option( 'date_format' ), $user->membership_level->enddate );
				$new_data['membership_expiration'] = "<p>" . sprintf( __("This membership will expire on %s.", "paid-memberships-pro"), date_i18n( get_option( 'date_format' ), $user->membership_level->enddate ) ) . "</p>\n";
				$new_data["membership_change"] .= " " . sprintf(__("This membership will expire on %s.", "paid-memberships-pro"), date_i18n( get_option( 'date_format' ), $user->membership_level->enddate ) );
			} else if ( ! empty( $email->expiration_changed ) ) {
				$new_data["membership_change"] .= " " . __("This membership does not expire.", "paid-memberships-pro");
			}
		}
	}

	//invoice data
	if(!empty($data['invoice_id']))
	{
		$invoice = new MemberOrder($data['invoice_id']);
		if(!empty($invoice) && !empty($invoice->code))
		{
			$new_data['billing_name'] = $invoice->billing->name;
			$new_data['billing_street'] = $invoice->billing->street;
			$new_data['billing_city'] = $invoice->billing->city;
			$new_data['billing_state'] = $invoice->billing->state;
			$new_data['billing_zip'] = $invoice->billing->zip;
			$new_data['billing_country'] = $invoice->billing->country;
			$new_data['billing_phone'] = $invoice->billing->phone;
			$new_data['cardtype'] = $invoice->cardtype;
			$new_data['accountnumber'] = hideCardNumber($invoice->accountnumber);
			$new_data['expirationmonth'] = $invoice->expirationmonth;
			$new_data['expirationyear'] = $invoice->expirationyear;
			$new_data['instructions'] = wpautop(pmpro_getOption('instructions'));
			$new_data['invoice_id'] = $invoice->code;
			$new_data['invoice_total'] = $pmpro_currency_symbol . number_format($invoice->total, 2);
			$new_data['invoice_date'] = date_i18n( get_option( 'date_format' ), $invoice->getTimestamp() );
			$new_data['invoice_link'] = pmpro_url('invoice', '?invoice=' . $invoice->code);

				//billing address
			$new_data["billing_address"] = pmpro_formatAddress($invoice->billing->name,
				$invoice->billing->street,
				"", //address 2
				$invoice->billing->city,
				$invoice->billing->state,
				$invoice->billing->zip,
				$invoice->billing->country,
				$invoice->billing->phone);
		}
	}

	//if others are used in the email look in usermeta
	$et_body = pmpro_getOption('email_' . $email->template . '_body');
	$templates_in_email = preg_match_all("/!!([^!]+)!!/", $et_body, $matches);
	if ( ! empty( $templates_in_email ) && ! empty( $user->ID ) ) {
		$matches = $matches[1];
		foreach($matches as $match) {
			if ( empty( $new_data[ $match ] ) ) {
				$usermeta = get_user_meta($user->ID, $match, true);
				if ( ! empty( $usermeta ) ) {
					if( is_array( $usermeta ) && ! empty( $usermeta['fullurl'] ) ) {
						$new_data[$match] = $usermeta['fullurl'];
					} elseif( is_array($usermeta ) ) {
						$new_data[$match] = implode(", ", $usermeta);
					} else {
						$new_data[$match] = $usermeta;
					}
				}
			}
		}
	}

	//now replace any new_data not already in data
	foreach($new_data as $key => $value)
	{
		if(!isset($data[$key]))
			$data[$key] = $value;
	}

	return $data;
}
add_filter('pmpro_email_data', 'pmpro_email_templates_email_data', 10, 2);


/**
 * Load the default email template.
 *
 * Checks theme, then template, then PMPro directory.
 *
 * @since 0.6
 *
 * @param $template string
 *
 * @return string
 */
function pmpro_email_templates_get_template_body($template) {

	global $pmpro_email_templates_defaults;

	// Defaults
	$body = "";
	$file = false;


	// Load the template.
	if ( get_transient( 'pmproet_' . $template ) === false ) {
		// Load template
		if ( ! empty( pmpro_getOption('email_' . $template . '_body') ) ) {
			$body = pmpro_getOption('email_' . $template . '_body');
		}elseif( ! empty($pmpro_email_templates_defaults[$template]['body'])) {
			$body = $pmpro_email_templates_defaults[$template]['body'];
		} elseif ( file_exists( get_stylesheet_directory() . '/paid-memberships-pro/email/' . $template . '.html' ) ) {
			$file = get_stylesheet_directory() . '/paid-memberships-pro/email/' . $template . '.html';
		} elseif ( file_exists( get_template_directory() . '/paid-memberships-pro/email/' . $template . '.html') ) {
			$file = get_template_directory() . '/paid-memberships-pro/email/' . $template . '.html';
		}

		if( $file && ! $body ) {
			ob_start();
			require_once( $file );
			$body = ob_get_contents();
			ob_end_clean();
		}

		if ( ! empty( $body ) ) {
			set_transient( 'pmproet_' . $template, $body, 300 );
		}
	} else {
		$body = get_transient( 'pmproet_' . $template );
	}


	return $body;
}