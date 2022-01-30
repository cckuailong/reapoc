<?php

add_action( 'wpcf7_init', 'wpcf7_constant_contact_register_service', 10, 0 );

function wpcf7_constant_contact_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$integration->add_category( 'email_marketing',
		__( 'Email marketing', 'contact-form-7' ) );

	$service = WPCF7_ConstantContact::get_instance();
	$integration->add_service( 'constant_contact', $service );
}

add_action( 'wpcf7_save_contact_form',
	'wpcf7_constant_contact_save_contact_form', 10, 1 );

function wpcf7_constant_contact_save_contact_form( $contact_form ) {
	$service = WPCF7_ConstantContact::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	$additional_settings = $contact_form->additional_setting(
		'constant_contact',
		false
	);

	$list_names = array();

	$pattern = '/[\t ]*('
		. "'[^']*'"
		. '|'
		. '"[^"]*"'
		. '|'
		. '[^,]*?'
		. ')[\t ]*(?:[,]+|$)/';

	foreach ( $additional_settings as $setting ) {
		if ( preg_match_all( $pattern, $setting, $matches ) ) {
			foreach ( $matches[1] as $match ) {
				$name = trim( wpcf7_strip_quote( $match ) );

				if ( '' !== $name ) {
					$list_names[] = $name;
				}
			}
		}
	}

	$list_names = array_unique( $list_names );

	$key = sprintf( 'wpcf7_contact_form:%d', $contact_form->id() );

	$service->update_contact_lists( array( $key => $list_names ) );
}

add_action( 'wpcf7_submit', 'wpcf7_constant_contact_submit', 10, 2 );

function wpcf7_constant_contact_submit( $contact_form, $result ) {
	$service = WPCF7_ConstantContact::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	if ( $contact_form->in_demo_mode() ) {
		return;
	}

	$do_submit = true;

	if ( empty( $result['status'] )
	or ! in_array( $result['status'], array( 'mail_sent' ) ) ) {
		$do_submit = false;
	}

	$additional_settings = $contact_form->additional_setting(
		'constant_contact',
		false
	);

	foreach ( $additional_settings as $setting ) {
		if ( in_array( $setting, array( 'off', 'false', '0' ), true ) ) {
			$do_submit = false;
			break;
		}
	}

	$do_submit = apply_filters( 'wpcf7_constant_contact_submit',
		$do_submit, $contact_form, $result );

	if ( ! $do_submit ) {
		return;
	}

	$submission = WPCF7_Submission::get_instance();

	$consented = true;

	foreach ( $contact_form->scan_form_tags( 'feature=name-attr' ) as $tag ) {
		if ( $tag->has_option( 'consent_for:constant_contact' )
		and null == $submission->get_posted_data( $tag->name ) ) {
			$consented = false;
			break;
		}
	}

	if ( ! $consented ) {
		return;
	}

	$request_builder_class_name = apply_filters(
		'wpcf7_constant_contact_contact_post_request_builder',
		'WPCF7_ConstantContact_ContactPostRequest'
	);

	if ( ! class_exists( $request_builder_class_name ) ) {
		return;
	}

	$request_builder = new $request_builder_class_name;
	$request_builder->build( $submission );

	if ( ! $request_builder->is_valid() ) {
		return;
	}

	if ( $email = $request_builder->get_email_address()
	and $service->email_exists( $email ) ) {
		return;
	}

	$service->create_contact( $request_builder->to_array() );
}

if ( ! class_exists( 'WPCF7_Service_OAuth2' ) ) {
	return;
}

class WPCF7_ConstantContact extends WPCF7_Service_OAuth2 {

	const service_name = 'constant_contact';
	const authorization_endpoint = 'https://api.cc.email/v3/idfed';
	const token_endpoint = 'https://idfed.constantcontact.com/as/token.oauth2';

	private static $instance;
	protected $contact_lists = array();

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		$this->authorization_endpoint = self::authorization_endpoint;
		$this->token_endpoint = self::token_endpoint;

		$option = (array) WPCF7::get_option( self::service_name );

		if ( isset( $option['client_id'] ) ) {
			$this->client_id = $option['client_id'];
		}

		if ( isset( $option['client_secret'] ) ) {
			$this->client_secret = $option['client_secret'];
		}

		if ( isset( $option['access_token'] ) ) {
			$this->access_token = $option['access_token'];
		}

		if ( isset( $option['refresh_token'] ) ) {
			$this->refresh_token = $option['refresh_token'];
		}

		if ( $this->is_active() ) {
			if ( isset( $option['contact_lists'] ) ) {
				$this->contact_lists = $option['contact_lists'];
			}
		}

		add_action( 'wpcf7_admin_init', array( $this, 'auth_redirect' ) );
	}

	public function auth_redirect() {
		$auth = isset( $_GET['auth'] ) ? trim( $_GET['auth'] ) : '';
		$code = isset( $_GET['code'] ) ? trim( $_GET['code'] ) : '';

		if ( self::service_name === $auth and $code
		and current_user_can( 'wpcf7_manage_integration' ) ) {
			$redirect_to = add_query_arg(
				array(
					'service' => self::service_name,
					'action' => 'auth_redirect',
					'code' => $code,
				),
				menu_page_url( 'wpcf7-integration', false )
			);

			wp_safe_redirect( $redirect_to );
			exit();
		}
	}

	protected function save_data() {
		$option = array_merge(
			(array) WPCF7::get_option( self::service_name ),
			array(
				'client_id' => $this->client_id,
				'client_secret' => $this->client_secret,
				'access_token' => $this->access_token,
				'refresh_token' => $this->refresh_token,
				'contact_lists' => $this->contact_lists,
			)
		);

		WPCF7::update_option( self::service_name, $option );
	}

	protected function reset_data() {
		$this->client_id = '';
		$this->client_secret = '';
		$this->access_token = '';
		$this->refresh_token = '';
		$this->contact_lists = array();

		$this->save_data();
	}

	public function get_title() {
		return __( 'Constant Contact', 'contact-form-7' );
	}

	public function get_categories() {
		return array( 'email_marketing' );
	}

	public function icon() {
	}

	public function link() {
		echo sprintf( '<a href="%1$s">%2$s</a>',
			'https://constant-contact.evyy.net/c/1293104/205991/3411',
			'constantcontact.com'
		);
	}

	protected function get_redirect_uri() {
		return admin_url( '/?auth=' . self::service_name );
	}

	protected function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$url = menu_page_url( 'wpcf7-integration', false );
		$url = add_query_arg( array( 'service' => self::service_name ), $url );

		if ( ! empty( $args) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}

	public function load( $action = '' ) {
		parent::load( $action );

		if ( 'setup' == $action and 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'wpcf7-constant-contact-setup' );

			if ( ! empty( $_POST['reset'] ) ) {
				$this->reset_data();
			} else {
				$this->client_id = isset( $_POST['client_id'] )
					? trim( $_POST['client_id'] ) : '';

				$this->client_secret = isset( $_POST['client_secret'] )
					? trim( $_POST['client_secret'] ) : '';

				$this->save_data();
				$this->authorize( 'contact_data' );
			}

			wp_safe_redirect( $this->menu_page_url( 'action=setup' ) );
			exit();
		}

		if ( 'edit' == $action and 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'wpcf7-constant-contact-edit' );

			$list_ids = isset( $_POST['contact_lists'] )
				? (array) $_POST['contact_lists']
				: array();

			$this->update_contact_lists( array( 'default' => $list_ids ) );

			wp_safe_redirect( $this->menu_page_url(
				array(
					'action' => 'setup',
					'message' => 'updated',
				)
			) );

			exit();
		}

		if ( $this->is_active() ) {
			$this->update_contact_lists();
		}
	}

	public function email_exists( $email ) {
		$endpoint = add_query_arg(
			array(
				'email' => $email,
				'status' => 'all',
			),
			'https://api.cc.email/v3/contacts'
		);

		$request = array(
			'method' => 'GET',
			'headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
			),
		);

		$response = $this->remote_request( $endpoint, $request );

		if ( 400 <= (int) wp_remote_retrieve_response_code( $response ) ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}

			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );

		if ( empty( $response_body ) ) {
			return false;
		}

		$response_body = json_decode( $response_body, true );

		return ! empty( $response_body['contacts'] );
	}

	public function create_contact( $properties ) {
		$endpoint = 'https://api.cc.email/v3/contacts';

		$request = array(
			'method' => 'POST',
			'headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
			),
			'body' => json_encode( $properties ),
		);

		$response = $this->remote_request( $endpoint, $request );

		if ( 400 <= (int) wp_remote_retrieve_response_code( $response ) ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}

			return false;
		}
	}

	public function get_contact_lists() {
		$endpoint = 'https://api.cc.email/v3/contact_lists';

		$request = array(
			'method' => 'GET',
			'headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
			),
		);

		$response = $this->remote_request( $endpoint, $request );

		if ( 400 <= (int) wp_remote_retrieve_response_code( $response ) ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}

			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );

		if ( empty( $response_body ) ) {
			return false;
		}

		$response_body = json_decode( $response_body, true );

		if ( ! empty( $response_body['lists'] ) ) {
			return (array) $response_body['lists'];
		} else {
			return array();
		}
	}

	public function update_contact_lists( $selection = array() ) {
		$contact_lists = array();
		$contact_lists_on_api = $this->get_contact_lists();

		if ( false !== $contact_lists_on_api ) {
			foreach ( (array) $contact_lists_on_api as $list ) {
				if ( isset( $list['list_id'] ) ) {
					$list_id = trim( $list['list_id'] );
				} else {
					continue;
				}

				if ( isset( $this->contact_lists[$list_id]['selected'] ) ) {
					$list['selected'] = $this->contact_lists[$list_id]['selected'];
				} else {
					$list['selected'] = array();
				}

				$contact_lists[$list_id] = $list;
			}
		} else {
			$contact_lists = $this->contact_lists;
		}

		foreach ( (array) $selection as $key => $ids_or_names ) {
			foreach( $contact_lists as $list_id => $list ) {
				if ( in_array( $list['list_id'], (array) $ids_or_names, true )
				or in_array( $list['name'], (array) $ids_or_names, true ) ) {
					$contact_lists[$list_id]['selected'][$key] = true;
				} else {
					unset( $contact_lists[$list_id]['selected'][$key] );
				}
			}
		}

		$this->contact_lists = $contact_lists;

		if ( $selection ) {
			$this->save_data();
		}

		return $this->contact_lists;
	}

	public function admin_notice( $message = '' ) {
		switch ( $message ) {
			case 'success':
				echo sprintf(
					'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
					esc_html( __( "Connection established.", 'contact-form-7' ) )
				);
				break;
			case 'failed':
				echo sprintf(
					'<div class="notice notice-error is-dismissible"><p><strong>%1$s</strong>: %2$s</p></div>',
					esc_html( __( "Error", 'contact-form-7' ) ),
					esc_html( __( "Failed to establish connection. Please double-check your configuration.", 'contact-form-7' ) )
				);
				break;
			case 'updated':
				echo sprintf(
					'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
					esc_html( __( "Configuration updated.", 'contact-form-7' ) )
				);
				break;
		}
	}

	public function display( $action = '' ) {
		echo '<p>' . sprintf(
			esc_html( __( 'The Constant Contact integration module allows you to send contact data collected through your contact forms to the Constant Contact API. You can create reliable email subscription services in a few easy steps. For details, see %s.', 'contact-form-7' ) ),
			wpcf7_link(
				__(
					'https://contactform7.com/constant-contact-integration/',
					'contact-form-7'
				),
				__( 'Constant Contact integration', 'contact-form-7' )
			)
		) . '</p>';

		if ( $this->is_active() ) {
			echo sprintf(
				'<p class="dashicons-before dashicons-yes">%s</p>',
				esc_html( __( "This site is connected to the Constant Contact API.", 'contact-form-7' ) )
			);
		}

		if ( 'setup' == $action ) {
			$this->display_setup();
		} else {
			echo sprintf(
				'<p><a href="%1$s" class="button">%2$s</a></p>',
				esc_url( $this->menu_page_url( 'action=setup' ) ),
				esc_html( __( 'Setup Integration', 'contact-form-7' ) )
			);
		}
	}

	private function display_setup() {
?>
<form method="post" action="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>">
<?php wp_nonce_field( 'wpcf7-constant-contact-setup' ); ?>
<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="client_id"><?php echo esc_html( __( 'API Key', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( $this->client_id );
			echo sprintf(
				'<input type="hidden" value="%1$s" id="client_id" name="client_id" />',
				esc_attr( $this->client_id )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%1$s" id="client_id" name="client_id" class="regular-text code" />',
				esc_attr( $this->client_id )
			);
		}
	?></td>
</tr>
<tr>
	<th scope="row"><label for="client_secret"><?php echo esc_html( __( 'App Secret', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( wpcf7_mask_password( $this->client_secret ) );
			echo sprintf(
				'<input type="hidden" value="%1$s" id="client_secret" name="client_secret" />',
				esc_attr( $this->client_secret )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%1$s" id="client_secret" name="client_secret" class="regular-text code" />',
				esc_attr( $this->client_secret )
			);
		}
	?></td>
</tr>
<tr>
	<th scope="row"><label for="redirect_uri"><?php echo esc_html( __( 'Redirect URI', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		echo sprintf(
			'<input type="text" value="%1$s" id="redirect_uri" name="redirect_uri" class="large-text code" readonly="readonly" onfocus="this.select();" style="font-size: 11px;" />',
			$this->get_redirect_uri()
		);
	?>
	<p class="description"><?php echo esc_html( __( "Set this URL as the redirect URI.", 'contact-form-7' ) ); ?></p>
	</td>
</tr>
</tbody>
</table>
<?php
		if ( $this->is_active() ) {
			submit_button(
				_x( 'Reset Keys', 'API keys', 'contact-form-7' ),
				'small', 'reset'
			);
		} else {
			submit_button(
				__( 'Connect to the Constant Contact API', 'contact-form-7' )
			);
		}
?>
</form>

<?php
		if ( $this->is_active() and ! empty( $this->contact_lists ) ) {
?>
<form method="post" action="<?php echo esc_url( $this->menu_page_url( 'action=edit' ) ); ?>">
<?php wp_nonce_field( 'wpcf7-constant-contact-edit' ); ?>
<table class="form-table">
<tbody>
<tr>
	<th scope="row"><?php echo esc_html( _x( 'Contact Lists', 'Constant Contact', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><span><?php echo esc_html( _x( "Contact Lists: Select lists to which newly added contacts are to belong.", 'Constant Contact', 'contact-form-7' ) ); ?></span></legend>
		<p class="description"><?php echo esc_html( __( "Select lists to which newly added contacts are to belong.", 'contact-form-7' ) ); ?></p>
		<ul class="checkboxes"><?php
			foreach ( $this->contact_lists as $list ) {
				echo sprintf(
					'<li><input %1$s /> <label for="%2$s">%3$s</label></li>',
					wpcf7_format_atts( array(
						'type' => 'checkbox',
						'name' => 'contact_lists[]',
						'value' => $list['list_id'],
						'id' => 'contact_list_' . $list['list_id'],
						'checked' => empty( $list['selected']['default'] )
							? ''
							: 'checked',
					) ),
					esc_attr( 'contact_list_' . $list['list_id'] ),
					esc_html( $list['name'] )
				);
			}
		?></ul>
		</fieldset>
	</td>
</tr>
</tbody>
</table>
<?php
			submit_button();
		}
?>
</form>
<?php
	}

}

class WPCF7_ConstantContact_ContactPostRequest {

	private $email_address;
	private $first_name;
	private $last_name;
	private $job_title;
	private $company_name;
	private $create_source;
	private $birthday_month;
	private $birthday_day;
	private $anniversary;
	private $custom_fields = array();
	private $phone_numbers = array();
	private $street_addresses = array();
	private $list_memberships = array();

	public function __construct() {
	}

	public function build( WPCF7_Submission $submission ) {
		$this->set_create_source( 'Contact' );

		$posted_data = (array) $submission->get_posted_data();

		if ( isset( $posted_data['your-first-name'] ) ) {
			$this->set_first_name( $posted_data['your-first-name'] );
		}

		if ( isset( $posted_data['your-last-name'] ) ) {
			$this->set_last_name( $posted_data['your-last-name'] );
		}

		if ( ! ( $this->first_name || $this->last_name )
		and isset( $posted_data['your-name'] ) ) {
			$your_name = preg_split( '/[\s]+/', $posted_data['your-name'], 2 );
			$this->set_first_name( array_shift( $your_name ) );
			$this->set_last_name( array_shift( $your_name ) );
		}

		if ( isset( $posted_data['your-email'] ) ) {
			$this->set_email_address( $posted_data['your-email'], 'implicit' );
		}

		if ( isset( $posted_data['your-job-title'] ) ) {
			$this->set_job_title( $posted_data['your-job-title'] );
		}

		if ( isset( $posted_data['your-company-name'] ) ) {
			$this->set_company_name( $posted_data['your-company-name'] );
		}

		if ( isset( $posted_data['your-birthday-month'] )
		and isset( $posted_data['your-birthday-day'] ) ) {
			$this->set_birthday(
				$posted_data['your-birthday-month'],
				$posted_data['your-birthday-day']
			);
		} elseif ( isset( $posted_data['your-birthday'] ) ) {
			$date = trim( $posted_data['your-birthday'] );

			if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches ) ) {
				$this->set_birthday( $matches[2], $matches[3] );
			}
		}

		if ( isset( $posted_data['your-anniversary'] ) ) {
			$this->set_anniversary( $posted_data['your-anniversary'] );
		}

		if ( isset( $posted_data['your-phone-number'] ) ) {
			$this->add_phone_number( $posted_data['your-phone-number'] );
		}

		$this->add_street_address(
			isset( $posted_data['your-address-street'] )
				? $posted_data['your-address-street'] : '',
			isset( $posted_data['your-address-city'] )
				? $posted_data['your-address-city'] : '',
			isset( $posted_data['your-address-state'] )
				? $posted_data['your-address-state'] : '',
			isset( $posted_data['your-address-postal-code'] )
				? $posted_data['your-address-postal-code'] : '',
			isset( $posted_data['your-address-country'] )
				? $posted_data['your-address-country'] : ''
		);

		$service_option = (array) WPCF7::get_option( 'constant_contact' );

		$contact_lists = isset( $service_option['contact_lists'] )
			? $service_option['contact_lists'] : array();

		$contact_form = $submission->get_contact_form();

		if ( $contact_form->additional_setting( 'constant_contact' ) ) {
			$key = sprintf( 'wpcf7_contact_form:%d', $contact_form->id() );
		} else {
			$key = 'default';
		}

		foreach ( (array) $contact_lists as $list ) {
			if ( ! empty( $list['selected'][$key] ) ) {
				$this->add_list_membership( $list['list_id'] );
			}
		}
	}

	public function is_valid() {
		return $this->create_source
			&& ( $this->email_address || $this->first_name || $this->last_name );
	}

	public function to_array() {
		$output = array(
			'email_address' => $this->email_address,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'job_title' => $this->job_title,
			'company_name' => $this->company_name,
			'create_source' => $this->create_source,
			'birthday_month' => $this->birthday_month,
			'birthday_day' => $this->birthday_day,
			'anniversary' => $this->anniversary,
			'custom_fields' => $this->custom_fields,
			'phone_numbers' => $this->phone_numbers,
			'street_addresses' => $this->street_addresses,
			'list_memberships' => $this->list_memberships,
		);

		return array_filter( $output );
	}

	public function get_email_address() {
		if ( isset( $this->email_address['address'] ) ) {
			return $this->email_address['address'];
		}

		return '';
	}

	public function set_email_address( $address, $permission_to_send = '' ) {
		if ( ! wpcf7_is_email( $address )
		or 80 < $this->strlen( $address ) ) {
			return false;
		}

		$types_of_permission = array(
			'implicit', 'explicit', 'deprecate', 'pending',
			'unsubscribe', 'temp_hold', 'not_set',
		);

		if ( ! in_array( $permission_to_send, $types_of_permission ) ) {
			$permission_to_send = 'implicit';
		}

		return $this->email_address = array(
			'address' => $address,
			'permission_to_send' => $permission_to_send,
		);
	}

	public function set_first_name( $first_name ) {
		$first_name = trim( $first_name );

		if ( empty( $first_name )
		or 50 < $this->strlen( $first_name ) ) {
			return false;
		}

		return $this->first_name = $first_name;
	}

	public function set_last_name( $last_name ) {
		$last_name = trim( $last_name );

		if ( empty( $last_name )
		or 50 < $this->strlen( $last_name ) ) {
			return false;
		}

		return $this->last_name = $last_name;
	}

	public function set_job_title( $job_title ) {
		$job_title = trim( $job_title );

		if ( empty( $job_title )
		or 50 < $this->strlen( $job_title ) ) {
			return false;
		}

		return $this->job_title = $job_title;
	}

	public function set_company_name( $company_name ) {
		$company_name = trim( $company_name );

		if ( empty( $company_name )
		or 50 < $this->strlen( $company_name ) ) {
			return false;
		}

		return $this->company_name = $company_name;
	}

	public function set_create_source( $create_source ) {
		if ( ! in_array( $create_source, array( 'Contact', 'Account' ) ) ) {
			return false;
		}

		return $this->create_source = $create_source;
	}

	public function set_birthday( $month, $day ) {
		$month = (int) $month;
		$day = (int) $day;

		if ( $month < 1 || 12 < $month
		or $day < 1 || 31 < $day ) {
			return false;
		}

		$this->birthday_month = $month;
		$this->birthday_day = $day;

		return array( $this->birthday_month, $this->birthday_day );
	}

	public function set_anniversary( $anniversary ) {
		$pattern = sprintf(
			'#^(%s)$#',
			implode( '|', array(
				'\d{1,2}/\d{1,2}/\d{4}',
				'\d{4}/\d{1,2}/\d{1,2}',
				'\d{4}-\d{1,2}-\d{1,2}',
				'\d{1,2}-\d{1,2}-\d{4}',
			) )
		);

		if ( ! preg_match( $pattern, $anniversary ) ) {
			return false;
		}

		return $this->anniversary = $anniversary;
	}

	public function add_custom_field( $custom_field_id, $value ) {
		$uuid_pattern = '/^[0-9a-f-]+$/i';

		$value = trim( $value );

		if ( 25 <= count( $this->custom_fields )
		or ! preg_match( $uuid_pattern, $custom_field_id )
		or 255 < $this->strlen( $value ) ) {
			return false;
		}

		return $this->custom_fields[] = array(
			'custom_field_id' => $custom_field_id,
			'value' => $value,
		);
	}

	public function add_phone_number( $phone_number, $kind = 'home' ) {
		$phone_number = trim( $phone_number );

		if ( empty( $phone_number )
		or ! wpcf7_is_tel( $phone_number )
		or 25 < $this->strlen( $phone_number )
		or 2 <= count( $this->phone_numbers )
		or ! in_array( $kind, array( 'home', 'work', 'other' ) ) ) {
			return false;
		}

		return $this->phone_numbers[] = array(
			'phone_number' => $phone_number,
			'kind' => $kind,
		);
	}

	public function add_street_address( $street, $city, $state, $postal_code, $country, $kind = 'home' ) {
		$street = trim( $street );
		$city = trim( $city );
		$state = trim( $state );
		$postal_code = trim( $postal_code );
		$country = trim( $country );

		if ( ! ( $street || $city || $state || $postal_code || $country )
		or 1 <= count( $this->street_addresses )
		or ! in_array( $kind, array( 'home', 'work', 'other' ) )
		or 255 < $this->strlen( $street )
		or 50 < $this->strlen( $city )
		or 50 < $this->strlen( $state )
		or 50 < $this->strlen( $postal_code )
		or 50 < $this->strlen( $country ) ) {
			return false;
		}

		return $this->street_addresses[] = array(
			'kind' => $kind,
			'street' => $street,
			'city' => $city,
			'state' => $state,
			'postal_code' => $postal_code,
			'country' => $country,
		);
	}

	public function add_list_membership( $list_id ) {
		$uuid_pattern = '/^[0-9a-f-]+$/i';

		if ( 50 <= count( $this->list_memberships )
		or ! preg_match( $uuid_pattern, $list_id ) ) {
			return false;
		}

		return $this->list_memberships[] = $list_id;
	}

	protected function strlen( $string ) {
		return wpcf7_count_code_units( stripslashes( $string ) );
	}

}
