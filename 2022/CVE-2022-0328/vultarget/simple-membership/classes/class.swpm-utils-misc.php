<?php

class SwpmMiscUtils {

	public static $stripe_sca_frontend_scripts_printed = false;

	public static function create_mandatory_wp_pages() {
		$settings = SwpmSettings::get_instance();

		//Create join us page
		$swpm_join_page_content  = '<p style="color:red;font-weight:bold;">This page and the content has been automatically generated for you to give you a basic idea of how a "Join Us" page should look like. You can customize this page however you like it by editing this page from your WordPress page editor.</p>';
		$swpm_join_page_content .= '<p style="font-weight:bold;">If you end up changing the URL of this page then make sure to update the URL value in the settings menu of the plugin.</p>';
		$swpm_join_page_content .= '<p style="border-top:1px solid #ccc;padding-top:10px;margin-top:10px;"></p>
			<strong>Free Membership</strong>
			<br />
			You get unlimited access to free membership content
			<br />
			<em><strong>Price: Free!</strong></em>
			<br /><br />Link the following image to go to the Registration Page if you want your visitors to be able to create a free membership account<br /><br />
			<img title="Join Now" src="' . SIMPLE_WP_MEMBERSHIP_URL . '/images/join-now-button-image.gif" alt="Join Now Button" width="277" height="82" />
			<p style="border-bottom:1px solid #ccc;padding-bottom:10px;margin-bottom:10px;"></p>';
		$swpm_join_page_content .= '<p><strong>You can register for a Free Membership or pay for one of the following membership options</strong></p>';
		$swpm_join_page_content .= '<p style="border-top:1px solid #ccc;padding-top:10px;margin-top:10px;"></p>
			[ ==> Insert Payment Button For Your Paid Membership Levels Here <== ]
			<p style="border-bottom:1px solid #ccc;padding-bottom:10px;margin-bottom:10px;"></p>';

		$swpm_join_page = array(
			'post_title'     => 'Join Us',
			'post_name'      => 'membership-join',
			'post_content'   => $swpm_join_page_content,
			'post_parent'    => 0,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);

		$join_page_obj = get_page_by_path( 'membership-join' );
		if ( ! $join_page_obj ) {
			$join_page_id = wp_insert_post( $swpm_join_page );
		} else {
			$join_page_id = $join_page_obj->ID;
			if ( $join_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $join_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$swpm_join_page_permalink = get_permalink( $join_page_id );
		$settings->set_value( 'join-us-page-url', $swpm_join_page_permalink );

		//Create registration page
		$swpm_rego_page = array(
			'post_title'     => SwpmUtils::_( 'Registration' ),
			'post_name'      => 'membership-registration',
			'post_content'   => '[swpm_registration_form]',
			'post_parent'    => $join_page_id,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		$rego_page_obj  = get_page_by_path( 'membership-registration' );
		if ( ! $rego_page_obj ) {
			$rego_page_id = wp_insert_post( $swpm_rego_page );
		} else {
			$rego_page_id = $rego_page_obj->ID;
			if ( $rego_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $rego_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$swpm_rego_page_permalink = get_permalink( $rego_page_id );
		$settings->set_value( 'registration-page-url', $swpm_rego_page_permalink );

		//Create login page
		$swpm_login_page = array(
			'post_title'     => SwpmUtils::_( 'Member Login' ),
			'post_name'      => 'membership-login',
			'post_content'   => '[swpm_login_form]',
			'post_parent'    => 0,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		$login_page_obj  = get_page_by_path( 'membership-login' );
		if ( ! $login_page_obj ) {
			$login_page_id = wp_insert_post( $swpm_login_page );
		} else {
			$login_page_id = $login_page_obj->ID;
			if ( $login_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $login_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$swpm_login_page_permalink = get_permalink( $login_page_id );
		$settings->set_value( 'login-page-url', $swpm_login_page_permalink );

		//Create profile page
		$swpm_profile_page = array(
			'post_title'     => SwpmUtils::_( 'Profile' ),
			'post_name'      => 'membership-profile',
			'post_content'   => '[swpm_profile_form]',
			'post_parent'    => $login_page_id,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		$profile_page_obj  = get_page_by_path( 'membership-profile' );
		if ( ! $profile_page_obj ) {
			$profile_page_id = wp_insert_post( $swpm_profile_page );
		} else {
			$profile_page_id = $profile_page_obj->ID;
			if ( $profile_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $profile_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$swpm_profile_page_permalink = get_permalink( $profile_page_id );
		$settings->set_value( 'profile-page-url', $swpm_profile_page_permalink );

		//Create reset page
		$swpm_reset_page = array(
			'post_title'     => SwpmUtils::_( 'Password Reset' ),
			'post_name'      => 'password-reset',
			'post_content'   => '[swpm_reset_form]',
			'post_parent'    => $login_page_id,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		$reset_page_obj  = get_page_by_path( 'password-reset' );
		if ( ! $profile_page_obj ) {
			$reset_page_id = wp_insert_post( $swpm_reset_page );
		} else {
			$reset_page_id = $reset_page_obj->ID;
			if ( $reset_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $reset_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$swpm_reset_page_permalink = get_permalink( $reset_page_id );
		$settings->set_value( 'reset-page-url', $swpm_reset_page_permalink );

		$settings->save(); //Save all settings object changes
	}

	public static function redirect_to_url( $url ) {
		if ( empty( $url ) ) {
			return;
		}
		$url = apply_filters( 'swpm_redirect_to_url', $url );

		if ( ! preg_match( '/http/', $url ) ) {//URL value is incorrect
			echo '<p>Error! The URL value you entered in the plugin configuration is incorrect.</p>';
			echo '<p>A URL must always have the "http" keyword in it.</p>';
			echo '<p style="font-weight: bold;">The URL value you currently configured is: <br />' . $url . '</p>';
			echo '<p>Here are some examples of correctly formatted URL values for your reference: <br />http://www.example.com<br/>http://example.com<br />https://www.example.com</p>';
			echo '<p>Find the field where you entered this incorrect URL value and correct the mistake then try again.</p>';
			exit;
		}
		if ( ! headers_sent() ) {
			header( 'Location: ' . $url );
		} else {
			echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
		}
		exit;
	}

	public static function show_temporary_message_then_redirect( $msg, $redirect_url, $timeout = 5 ) {
		$timeout       = absint( $timeout );
		$redirect_html = sprintf( '<meta http-equiv="refresh" content="%d; url=\'%s\'" />', $timeout, $redirect_url );
		$redir_msg     = SwpmUtils::_( 'You will be automatically redirected in a few seconds. If not, please %s.' );
		$redir_msg     = sprintf( $redir_msg, '<a href="' . $redirect_url . '">' . SwpmUtils::_( 'click here' ) . '</a>' );

		$msg   = $msg . '<br/><br/>' . $redir_msg . $redirect_html;
		$title = SwpmUtils::_( 'Action Status' );
		wp_die( $msg, $title );
	}

	public static function get_current_page_url() {
		$pageURL = 'http';

                if ( isset( $_SERVER['SCRIPT_URI'] ) && ! empty( $_SERVER['SCRIPT_URI'] ) ) {
			$pageURL = $_SERVER['SCRIPT_URI'];
                        $pageURL = str_replace(':443', '', $pageURL);//remove any port number from the URL value (some hosts include the port number with this).
			$pageURL = apply_filters( 'swpm_get_current_page_url_filter', $pageURL );
			return $pageURL;
		}

		if ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' ) ) {
			$pageURL .= 's';
		}
		$pageURL .= '://';
		if ( isset( $_SERVER['SERVER_PORT'] ) && ( $_SERVER['SERVER_PORT'] != '80' ) && ( $_SERVER['SERVER_PORT'] != '443' ) ) {
			$pageURL .= ltrim( $_SERVER['SERVER_NAME'], '.*' ) . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= ltrim( $_SERVER['SERVER_NAME'], '.*' ) . $_SERVER['REQUEST_URI'];
		}

		$pageURL = apply_filters( 'swpm_get_current_page_url_filter', $pageURL );

		return $pageURL;
	}

	/*
	 * This is an alternative to the get_current_page_url() function. It needs to be tested on many different server conditions before it can be utilized
	 */
	public static function get_current_page_url_alt() {
		$url_parts          = array();
		$url_parts['proto'] = 'http';

		if ( isset( $_SERVER['SCRIPT_URI'] ) && ! empty( $_SERVER['SCRIPT_URI'] ) ) {
			return $_SERVER['SCRIPT_URI'];
		}

		if ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' ) ) {
			$url_parts['proto'] = 'https';
		}

		$url_parts['port'] = '';
		if ( isset( $_SERVER['SERVER_PORT'] ) && ( $_SERVER['SERVER_PORT'] != '80' ) && ( $_SERVER['SERVER_PORT'] != '443' ) ) {
			$url_parts['port'] = $_SERVER['SERVER_PORT'];
		}

		$url_parts['domain'] = ltrim( $_SERVER['SERVER_NAME'], '.*' );
		$url_parts['uri']    = $_SERVER['REQUEST_URI'];

		$url_parts = apply_filters( 'swpm_get_current_page_url_alt_filter', $url_parts );

		$pageURL = sprintf( '%s://%s%s%s', $url_parts['proto'], $url_parts['domain'], ! empty( $url_parts['port'] ) ? ':' . $url_parts['port'] : '', $url_parts['uri'] );

		return $pageURL;
	}

	/*
	 * Returns just the domain name. Something like example.com
	 */

	public static function get_home_url_without_http_and_www() {
		$site_url = get_site_url();
		$parse    = parse_url( $site_url );
		$site_url = $parse['host'];
		$site_url = str_replace( 'https://', '', $site_url );
		$site_url = str_replace( 'http://', '', $site_url );
		if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $site_url, $regs ) ) {
			$site_url = $regs['domain'];
		}
		return $site_url;
	}

	public static function replace_dynamic_tags( $msg_body, $member_id, $additional_args = '' ) {
		$settings    = SwpmSettings::get_instance();
		$user_record = SwpmMemberUtils::get_user_by_id( $member_id );

		$password = '';
		$reg_link = '';
		if ( ! empty( $additional_args ) ) {
			$password = isset( $additional_args['password'] ) ? $additional_args['password'] : $password;
			$reg_link = isset( $additional_args['reg_link'] ) ? $additional_args['reg_link'] : $reg_link;
		}
		$login_link = $settings->get_value( 'login-page-url' );

		//Construct the primary address value
		$primary_address = '';
		if ( ! empty( $user_record->address_street ) && ! empty( $user_record->address_city ) ) {
			//An address value is present.
			$primary_address .= $user_record->address_street;
			$primary_address .= "\n" . $user_record->address_city;
			if ( ! empty( $user_record->address_state ) ) {
				$primary_address .= ' ' . $user_record->address_state;
			}
			if ( ! empty( $user_record->address_zipcode ) ) {
				$primary_address .= ' ' . $user_record->address_zipcode;
			}
			if ( ! empty( $user_record->country ) ) {
				$primary_address .= "\n" . $user_record->country;
			}
		}

		$membership_level_name = SwpmMembershipLevelUtils::get_membership_level_name_of_a_member( $member_id );
		//Format some field values
		$member_since_formatted = SwpmUtils::get_formatted_date_according_to_wp_settings( $user_record->member_since );
		$subsc_starts_formatted = SwpmUtils::get_formatted_date_according_to_wp_settings( $user_record->subscription_starts );

		//Define the replacable tags
		$tags = array(
			'{member_id}',
			'{user_name}',
			'{first_name}',
			'{last_name}',
			'{membership_level}',
			'{membership_level_name}',
			'{account_state}',
			'{email}',
			'{phone}',
			'{member_since}',
			'{subscription_starts}',
			'{company_name}',
			'{password}',
			'{login_link}',
			'{reg_link}',
			'{primary_address}',
		);

		//Define the values
		$vals = array(
			$member_id,
			$user_record->user_name,
			$user_record->first_name,
			$user_record->last_name,
			$user_record->membership_level,
			$membership_level_name,
			$user_record->account_state,
			$user_record->email,
			$user_record->phone,
			$member_since_formatted,
			$subsc_starts_formatted,
			$user_record->company_name,
			$password,
			$login_link,
			$reg_link,
			$primary_address,
		);

		$msg_body = str_replace( $tags, $vals, $msg_body );
		return $msg_body;
	}

	public static function get_login_link() {
		$login_url  = SwpmSettings::get_instance()->get_value( 'login-page-url' );
		$joinus_url = SwpmSettings::get_instance()->get_value( 'join-us-page-url' );
		if ( empty( $login_url ) || empty( $joinus_url ) ) {
			return '<span style="color:red;">Simple Membership is not configured correctly. The login page or the join us page URL is missing in the settings configuration. '
					. 'Please contact <a href="mailto:' . get_option( 'admin_email' ) . '">Admin</a>';
		}

		//Create the login/protection message
		$filtered_login_url = apply_filters( 'swpm_get_login_link_url', $login_url ); //Addons can override the login URL value using this filter.
		$login_msg          = '';
		$login_msg         .= SwpmUtils::_( 'Please' ) . ' <a class="swpm-login-link" href="' . $filtered_login_url . '">' . SwpmUtils::_( 'Login' ) . '</a>. ';
		$login_msg         .= SwpmUtils::_( 'Not a Member?' ) . ' <a href="' . $joinus_url . '">' . SwpmUtils::_( 'Join Us' ) . '</a>';

		return $login_msg;
	}

	public static function get_renewal_link() {
		$renewal = SwpmSettings::get_instance()->get_value( 'renewal-page-url' );
		if ( empty( $renewal ) ) {
			//No renewal page is configured so don't show any renewal page link. It is okay to have no renewal page configured.
			return '';
		}
		return SwpmUtils::_( 'Please' ) . ' <a class="swpm-renewal-link" href="' . $renewal . '">' . SwpmUtils::_( 'renew' ) . '</a> ' . SwpmUtils::_( ' your account to gain access to this content.' );
	}

	public static function compare_url( $url1, $url2 ) {
		$url1 = trailingslashit( strtolower( $url1 ) );
		$url2 = trailingslashit( strtolower( $url2 ) );
		if ( $url1 == $url2 ) {
			return true;
		}

		$url1 = parse_url( $url1 );
		$url2 = parse_url( $url2 );

		$components = array( 'scheme', 'host', 'port', 'path' );

		foreach ( $components as $key => $value ) {
			if ( ! isset( $url1[ $value ] ) && ! isset( $url2[ $value ] ) ) {
				continue;
			}

			if ( ! isset( $url2[ $value ] ) ) {
				return false;
			}
			if ( ! isset( $url1[ $value ] ) ) {
				return false;
			}

			if ( $url1[ $value ] != $url2[ $value ] ) {
				return false;
			}
		}

		if ( ! isset( $url1['query'] ) && ! isset( $url2['query'] ) ) {
			return true;
		}

		if ( ! isset( $url2['query'] ) ) {
			return false;
		}
		if ( ! isset( $url1['query'] ) ) {
			return false;
		}

		return strpos( $url1['query'], $url2['query'] ) || strpos( $url2['query'], $url1['query'] );
	}

	public static function is_swpm_admin_page() {
		if ( isset( $_GET['page'] ) && ( stripos( $_GET['page'], 'simple_wp_membership' ) !== false ) ) {
			//This is an admin page of the SWPM plugin
			return true;
		}
		return false;
	}

	public static function check_user_permission_and_is_admin( $action_name ) {
		//Check we are on the admin end
		if ( ! is_admin() ) {
			//Error! This is not on the admin end. This can only be done from the admin side
			wp_die( SwpmUtils::_( 'Error! This action (' . $action_name . ') can only be done from admin end.' ) );
		}

		//Check user has management permission
		if ( ! current_user_can( SWPM_MANAGEMENT_PERMISSION ) ) {
			//Error! Only management users can do this
			wp_die( SwpmUtils::_( 'Error! This action (' . $action_name . ') can only be done by an user with management permission.' ) );
		}
	}

	public static function format_raw_content_for_front_end_display( $raw_content ) {
		$formatted_content = wptexturize( $raw_content );
		$formatted_content = convert_smilies( $formatted_content );
		$formatted_content = convert_chars( $formatted_content );
		$formatted_content = wpautop( $formatted_content );
		$formatted_content = shortcode_unautop( $formatted_content );
		$formatted_content = prepend_attachment( $formatted_content );
		$formatted_content = capital_P_dangit( $formatted_content );
		$formatted_content = do_shortcode( $formatted_content );

		return $formatted_content;
	}

	public static function get_countries_dropdown( $country = '' ) {
		$countries = array(
			'Afghanistan',
			'Albania',
			'Algeria',
			'Andorra',
			'Angola',
			'Antigua and Barbuda',
			'Argentina',
			'Armenia',
			'Aruba',
			'Australia',
			'Austria',
			'Azerbaijan',
			'Bahamas',
			'Bahrain',
			'Bangladesh',
			'Barbados',
			'Belarus',
			'Belgium',
			'Belize',
			'Benin',
			'Bhutan',
			'Bolivia',
			'Bonaire',
			'Bosnia and Herzegovina',
			'Botswana',
			'Brazil',
			'Brunei',
			'Bulgaria',
			'Burkina Faso',
			'Burundi',
			'Cambodia',
			'Cameroon',
			'Canada',
			'Cape Verde',
			'Central African Republic',
			'Chad',
			'Chile',
			'China',
			'Colombia',
			'Comoros',
			'Congo (Brazzaville)',
			'Congo',
			'Costa Rica',
			"Cote d\'Ivoire",
			'Croatia',
			'Cuba',
			'Curacao',
			'Cyprus',
			'Czech Republic',
			'Denmark',
			'Djibouti',
			'Dominica',
			'Dominican Republic',
			'East Timor (Timor Timur)',
			'Ecuador',
			'Egypt',
			'El Salvador',
			'Equatorial Guinea',
			'Eritrea',
			'Estonia',
                        'Eswatini',
			'Ethiopia',
			'Fiji',
			'Finland',
			'France',
			'Gabon',
			'Gambia, The',
			'Georgia',
			'Germany',
			'Ghana',
			'Greece',
			'Grenada',
			'Guatemala',
			'Guinea',
			'Guinea-Bissau',
			'Guyana',
			'Haiti',
			'Honduras',
			'Hong Kong',
			'Hungary',
			'Iceland',
			'India',
			'Indonesia',
			'Iran',
			'Iraq',
			'Ireland',
			'Israel',
			'Italy',
			'Jamaica',
			'Japan',
			'Jordan',
			'Kazakhstan',
			'Kenya',
			'Kiribati',
			'Korea, North',
			'Korea, South',
			'Kuwait',
			'Kyrgyzstan',
			'Laos',
			'Latvia',
			'Lebanon',
			'Lesotho',
			'Liberia',
			'Libya',
			'Liechtenstein',
			'Lithuania',
			'Luxembourg',
			'Macedonia',
			'Madagascar',
			'Malawi',
			'Malaysia',
			'Maldives',
			'Mali',
			'Malta',
			'Marshall Islands',
			'Mauritania',
			'Mauritius',
			'Mexico',
			'Micronesia',
			'Moldova',
			'Monaco',
			'Mongolia',
			'Montenegro',
			'Morocco',
			'Mozambique',
			'Myanmar',
			'Namibia',
			'Nauru',
			'Nepa',
			'Netherlands',
			'New Zealand',
			'Nicaragua',
			'Niger',
			'Nigeria',
			'Norway',
			'Oman',
			'Pakistan',
			'Palau',
                        'Palestine',
			'Panama',
			'Papua New Guinea',
			'Paraguay',
			'Peru',
			'Philippines',
			'Poland',
			'Portugal',
			'Qatar',
			'Romania',
			'Russia',
			'Rwanda',
			'Saint Kitts and Nevis',
			'Saint Lucia',
			'Saint Vincent',
			'Samoa',
			'San Marino',
			'Sao Tome and Principe',
			'Saudi Arabia',
			'Senegal',
			'Serbia',
			'Seychelles',
			'Sierra Leone',
			'Singapore',
			'Slovakia',
			'Slovenia',
			'Solomon Islands',
			'Somalia',
			'South Africa',
			'Spain',
			'Sri Lanka',
			'Sudan',
			'Suriname',
			'Swaziland',
			'Sweden',
			'Switzerland',
			'Syria',
			'Taiwan',
			'Tajikistan',
			'Tanzania',
			'Thailand',
			'Togo',
			'Tonga',
			'Trinidad and Tobago',
			'Tunisia',
			'Turkey',
			'Turkmenistan',
			'Tuvalu',
			'Uganda',
			'Ukraine',
			'United Arab Emirates',
			'United Kingdom',
			'United States of America',
			'Uruguay',
			'Uzbekistan',
			'Vanuatu',
			'Vatican City',
			'Venezuela',
			'Vietnam',
			'Yemen',
			'Zambia',
			'Zimbabwe',
		);
		//let's try to "guess" country name
		$curr_lev      = -1;
		$guess_country = '';
		foreach ( $countries as $country_name ) {
			similar_text( strtolower( $country ), strtolower( $country_name ), $lev );
			if ( $lev >= $curr_lev ) {
				//this is closest match so far
				$curr_lev      = $lev;
				$guess_country = $country_name;
			}
			if ( $curr_lev == 100 ) {
				//exact match
				break;
			}
		}
		if ( $curr_lev <= 80 ) {
			// probably bad guess
			$guess_country = '';
		}
		$countries_dropdown = '';
		//let's add "(Please select)" option
		$countries_dropdown .= "\r\n" . '<option value=""' . ( $country == '' ? ' selected' : '' ) . '>' . SwpmUtils::_( '(Please Select)' ) . '</option>';
		if ( $guess_country == '' && $country != '' ) {
			//since we haven't guessed the country name, let's add current value to the options
			$countries_dropdown .= "\r\n" . '<option value="' . $country . '" selected>' . $country . '</option>';
		}
		if ( $guess_country != '' ) {
			$country = $guess_country;
		}
		foreach ( $countries as $country_name ) {
			$countries_dropdown .= "\r\n" . '<option value="' . $country_name . '"' . ( strtolower( $country_name ) == strtolower( $country ) ? ' selected' : '' ) . '>' . $country_name . '</option>';
		}
		return $countries_dropdown;
	}

	public static function get_button_type_name( $button_type ) {
		$btnTypesNames = array(
			'pp_buy_now'              => SwpmUtils::_( 'PayPal Buy Now' ),
			'pp_subscription'         => SwpmUtils::_( 'PayPal Subscription' ),
			'pp_smart_checkout'       => SwpmUtils::_( 'PayPal Smart Checkout' ),
			'stripe_buy_now'          => SwpmUtils::_( 'Stripe Buy Now' ),
			'stripe_subscription'     => SwpmUtils::_( 'Stripe Subscription' ),
			'stripe_sca_buy_now'      => SwpmUtils::_( 'Stripe SCA Buy Now' ),
			'stripe_sca_subscription' => SwpmUtils::_( 'Stripe SCA Subscription' ),
			'braintree_buy_now'       => SwpmUtils::_( 'Braintree Buy Now' ),
		);

		$button_type_name = $button_type;

		if ( array_key_exists( $button_type, $btnTypesNames ) ) {
			$button_type_name = $btnTypesNames[ $button_type ];
		}

		return $button_type_name;
	}

	public static function format_money( $amount, $currency = false ) {
		$formatted = number_format( $amount, 2 );
		if ( $currency ) {
			$formatted .= ' ' . $currency;
		}
		return $formatted;
	}

	public static function load_stripe_lib() {
		//this function loads Stripe PHP SDK and ensures only once instance is loaded
		if ( ! class_exists( '\Stripe\Stripe' ) ) {
			require_once SIMPLE_WP_MEMBERSHIP_PATH . 'lib/stripe-gateway/init.php';
			\Stripe\Stripe::setAppInfo( 'Simple Membership', SIMPLE_WP_MEMBERSHIP_VER, 'https://simple-membership-plugin.com/', 'pp_partner_Fvas9OJ0jQ2oNQ' );
		}
	}

	public static function get_stripe_api_keys_from_payment_button( $button_id, $live = false ) {
		$keys   = array(
			'public' => '',
			'secret' => '',
		);
		$button = get_post( $button_id );
		if ( $button ) {
			$opts            = get_option( 'swpm-settings' );
			$use_global_keys = get_post_meta( $button_id, 'stripe_use_global_keys', true );

			if ( $use_global_keys ) {
				if ( $live ) {
					$keys['public'] = isset( $opts['stripe-live-public-key'] ) ? $opts['stripe-live-public-key'] : '';
					$keys['secret'] = isset( $opts['stripe-live-secret-key'] ) ? $opts['stripe-live-secret-key'] : '';
				} else {
					$keys['public'] = isset( $opts['stripe-test-public-key'] ) ? $opts['stripe-test-public-key'] : '';
					$keys['secret'] = isset( $opts['stripe-test-secret-key'] ) ? $opts['stripe-test-secret-key'] : '';
				}
			} else {
				if ( $live ) {
					$stripe_live_secret_key      = get_post_meta( $button_id, 'stripe_live_secret_key', true );
					$stripe_live_publishable_key = get_post_meta( $button_id, 'stripe_live_publishable_key', true );

					$keys['public'] = $stripe_live_publishable_key;
					$keys['secret'] = $stripe_live_secret_key;
				} else {
					$stripe_test_secret_key      = get_post_meta( $button_id, 'stripe_test_secret_key', true );
					$stripe_test_publishable_key = get_post_meta( $button_id, 'stripe_test_publishable_key', true );

					$keys['public'] = $stripe_test_publishable_key;
					$keys['secret'] = $stripe_test_secret_key;
				}
			}
		}
		return $keys;
	}

	public static function mail( $email, $subject, $email_body, $headers ) {
		$settings     = SwpmSettings::get_instance();
		$html_enabled = $settings->get_value( 'email-enable-html' );
		if ( ! empty( $html_enabled ) ) {
			$headers   .= "Content-Type: text/html; charset=UTF-8\r\n";
			$email_body = nl2br( $email_body );
		}
		wp_mail( $email, $subject, $email_body, $headers );
	}

	/**
	* Outputs Stripe SCA frontend scripts and styles once
	*/
	public static function output_stripe_sca_frontend_scripts_once() {
		$out = '';
		if ( ! self::$stripe_sca_frontend_scripts_printed ) {
			$out                                      .= '<script src="https://js.stripe.com/v3/"></script>';
			$out                                      .= "<link rel='stylesheet' href='https://checkout.stripe.com/v3/checkout/button.css' type='text/css' media='all' />";
			self::$stripe_sca_frontend_scripts_printed = true;
		}
		return $out;
	}

}
