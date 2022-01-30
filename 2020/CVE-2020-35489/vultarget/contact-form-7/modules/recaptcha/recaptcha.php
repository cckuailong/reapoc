<?php

add_action( 'wpcf7_init', 'wpcf7_recaptcha_register_service', 10, 0 );

function wpcf7_recaptcha_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$integration->add_category( 'captcha',
		__( 'CAPTCHA', 'contact-form-7' )
	);

	$integration->add_service( 'recaptcha',
		WPCF7_RECAPTCHA::get_instance()
	);
}

add_action( 'wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 10, 0 );

function wpcf7_recaptcha_enqueue_scripts() {
	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	wp_enqueue_script( 'google-recaptcha',
		add_query_arg(
			array(
				'render' => $service->get_sitekey(),
			),
			'https://www.google.com/recaptcha/api.js'
		),
		array(),
		'3.0',
		true
	);

	wp_enqueue_script( 'wpcf7-recaptcha',
		wpcf7_plugin_url( 'modules/recaptcha/script.js' ),
		array( 'google-recaptcha' ),
		WPCF7_VERSION,
		true
	);

	wp_localize_script( 'wpcf7-recaptcha',
		'wpcf7_recaptcha',
		array(
			'sitekey' => $service->get_sitekey(),
			'actions' => apply_filters( 'wpcf7_recaptcha_actions', array(
				'homepage' => 'homepage',
				'contactform' => 'contactform',
			) ),
		)
	);
}

add_filter( 'wpcf7_form_hidden_fields',
	'wpcf7_recaptcha_add_hidden_fields', 100, 1
);

function wpcf7_recaptcha_add_hidden_fields( $fields ) {
	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() ) {
		return $fields;
	}

	return array_merge( $fields, array(
		'_wpcf7_recaptcha_response' => '',
	) );
}

add_filter( 'wpcf7_spam', 'wpcf7_recaptcha_verify_response', 9, 2 );

function wpcf7_recaptcha_verify_response( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() ) {
		return $spam;
	}

	$token = isset( $_POST['_wpcf7_recaptcha_response'] )
		? trim( $_POST['_wpcf7_recaptcha_response'] ) : '';

	if ( $service->verify( $token ) ) { // Human
		$spam = false;
	} else { // Bot
		$spam = true;

		if ( '' === $token ) {
			$submission->add_spam_log( array(
				'agent' => 'recaptcha',
				'reason' => __( 'reCAPTCHA response token is empty.', 'contact-form-7' ),
			) );
		} else {
			$submission->add_spam_log( array(
				'agent' => 'recaptcha',
				'reason' => sprintf(
					__( 'reCAPTCHA score (%1$.2f) is lower than the threshold (%2$.2f).', 'contact-form-7' ),
					$service->get_last_score(),
					$service->get_threshold()
				),
			) );
		}
	}

	return $spam;
}

add_action( 'wpcf7_init', 'wpcf7_recaptcha_add_form_tag_recaptcha', 10, 0 );

function wpcf7_recaptcha_add_form_tag_recaptcha() {
	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() ) {
		return;
	}

	wpcf7_add_form_tag( 'recaptcha',
		'__return_empty_string', // no output
		array( 'display-block' => true )
	);
}

add_action( 'wpcf7_upgrade', 'wpcf7_upgrade_recaptcha_v2_v3', 10, 2 );

function wpcf7_upgrade_recaptcha_v2_v3( $new_ver, $old_ver ) {
	if ( version_compare( '5.1-dev', $old_ver, '<=' ) ) {
		return;
	}

	$service = WPCF7_RECAPTCHA::get_instance();

	if ( ! $service->is_active() or $service->get_global_sitekey() ) {
		return;
	}

	// Maybe v2 keys are used now. Warning necessary.
	WPCF7::update_option( 'recaptcha_v2_v3_warning', true );
	WPCF7::update_option( 'recaptcha', null );
}

add_action( 'wpcf7_admin_menu', 'wpcf7_admin_init_recaptcha_v2_v3', 10, 0 );

function wpcf7_admin_init_recaptcha_v2_v3() {
	if ( ! WPCF7::get_option( 'recaptcha_v2_v3_warning' ) ) {
		return;
	}

	add_filter( 'wpcf7_admin_menu_change_notice',
		'wpcf7_admin_menu_change_notice_recaptcha_v2_v3', 10, 1 );

	add_action( 'wpcf7_admin_warnings',
		'wpcf7_admin_warnings_recaptcha_v2_v3', 5, 3 );
}

function wpcf7_admin_menu_change_notice_recaptcha_v2_v3( $counts ) {
	$counts['wpcf7-integration'] += 1;
	return $counts;
}

function wpcf7_admin_warnings_recaptcha_v2_v3( $page, $action, $object ) {
	if ( 'wpcf7-integration' !== $page ) {
		return;
	}

	$message = sprintf(
		esc_html( __( "API keys for reCAPTCHA v3 are different from those for v2; keys for v2 don&#8217;t work with the v3 API. You need to register your sites again to get new keys for v3. For details, see %s.", 'contact-form-7' ) ),
		wpcf7_link(
			__( 'https://contactform7.com/recaptcha/', 'contact-form-7' ),
			__( 'reCAPTCHA (v3)', 'contact-form-7' )
		)
	);

	echo sprintf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		$message
	);
}

if ( ! class_exists( 'WPCF7_Service' ) ) {
	return;
}

class WPCF7_RECAPTCHA extends WPCF7_Service {

	private static $instance;
	private $sitekeys;
	private $last_score;

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		$this->sitekeys = WPCF7::get_option( 'recaptcha' );
	}

	public function get_title() {
		return __( 'reCAPTCHA', 'contact-form-7' );
	}

	public function is_active() {
		$sitekey = $this->get_sitekey();
		$secret = $this->get_secret( $sitekey );
		return $sitekey && $secret;
	}

	public function get_categories() {
		return array( 'captcha' );
	}

	public function icon() {
	}

	public function link() {
		echo wpcf7_link(
			'https://www.google.com/recaptcha/intro/index.html',
			'google.com/recaptcha'
		);
	}

	public function get_global_sitekey() {
		static $sitekey = '';

		if ( $sitekey ) {
			return $sitekey;
		}

		if ( defined( 'WPCF7_RECAPTCHA_SITEKEY' ) ) {
			$sitekey = WPCF7_RECAPTCHA_SITEKEY;
		}

		$sitekey = apply_filters( 'wpcf7_recaptcha_sitekey', $sitekey );

		return $sitekey;
	}

	public function get_global_secret() {
		static $secret = '';

		if ( $secret ) {
			return $secret;
		}

		if ( defined( 'WPCF7_RECAPTCHA_SECRET' ) ) {
			$secret = WPCF7_RECAPTCHA_SECRET;
		}

		$secret = apply_filters( 'wpcf7_recaptcha_secret', $secret );

		return $secret;
	}

	public function get_sitekey() {
		if ( $this->get_global_sitekey() && $this->get_global_secret() ) {
			return $this->get_global_sitekey();
		}

		if ( empty( $this->sitekeys )
		or ! is_array( $this->sitekeys ) ) {
			return false;
		}

		$sitekeys = array_keys( $this->sitekeys );

		return $sitekeys[0];
	}

	public function get_secret( $sitekey ) {
		if ( $this->get_global_sitekey() && $this->get_global_secret() ) {
			return $this->get_global_secret();
		}

		$sitekeys = (array) $this->sitekeys;

		if ( isset( $sitekeys[$sitekey] ) ) {
			return $sitekeys[$sitekey];
		} else {
			return false;
		}
	}

	protected function log( $url, $request, $response ) {
		wpcf7_log_remote_request( $url, $request, $response );
	}

	public function verify( $token ) {
		$is_human = false;

		if ( empty( $token ) or ! $this->is_active() ) {
			return $is_human;
		}

		$endpoint = 'https://www.google.com/recaptcha/api/siteverify';

		$sitekey = $this->get_sitekey();
		$secret = $this->get_secret( $sitekey );

		$request = array(
			'body' => array(
				'secret' => $secret,
				'response' => $token,
			),
		);

		$response = wp_remote_post( esc_url_raw( $endpoint ), $request );

		if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}

			return $is_human;
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_body = json_decode( $response_body, true );

		$this->last_score = $score = isset( $response_body['score'] )
			? $response_body['score']
			: 0;

		$threshold = $this->get_threshold();
		$is_human = $threshold < $score;

		$is_human = apply_filters( 'wpcf7_recaptcha_verify_response',
			$is_human, $response_body );

		if ( $submission = WPCF7_Submission::get_instance() ) {
			$submission->recaptcha = array(
				'version' => '3.0',
				'threshold' => $threshold,
				'response' => $response_body,
			);
		}

		return $is_human;
	}

	public function get_threshold() {
		return apply_filters( 'wpcf7_recaptcha_threshold', 0.50 );
	}

	public function get_last_score() {
		return $this->last_score;
	}

	protected function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$url = menu_page_url( 'wpcf7-integration', false );
		$url = add_query_arg( array( 'service' => 'recaptcha' ), $url );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}

	protected function save_data() {
		WPCF7::update_option( 'recaptcha', $this->sitekeys );
	}

	protected function reset_data() {
		$this->sitekeys = null;
		$this->save_data();
	}

	public function load( $action = '' ) {
		if ( 'setup' == $action and 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer( 'wpcf7-recaptcha-setup' );

			if ( ! empty( $_POST['reset'] ) ) {
				$this->reset_data();
				$redirect_to = $this->menu_page_url( 'action=setup' );
			} else {
				$sitekey = isset( $_POST['sitekey'] ) ? trim( $_POST['sitekey'] ) : '';
				$secret = isset( $_POST['secret'] ) ? trim( $_POST['secret'] ) : '';

				if ( $sitekey and $secret ) {
					$this->sitekeys = array( $sitekey => $secret );
					$this->save_data();

					$redirect_to = $this->menu_page_url( array(
						'message' => 'success',
					) );
				} else {
					$redirect_to = $this->menu_page_url( array(
						'action' => 'setup',
						'message' => 'invalid',
					) );
				}
			}

			if ( WPCF7::get_option( 'recaptcha_v2_v3_warning' ) ) {
				WPCF7::update_option( 'recaptcha_v2_v3_warning', false );
			}

			wp_safe_redirect( $redirect_to );
			exit();
		}
	}

	public function admin_notice( $message = '' ) {
		if ( 'invalid' == $message ) {
			echo sprintf(
				'<div class="notice notice-error is-dismissible"><p><strong>%1$s</strong>: %2$s</p></div>',
				esc_html( __( "Error", 'contact-form-7' ) ),
				esc_html( __( "Invalid key values.", 'contact-form-7' ) ) );
		}

		if ( 'success' == $message ) {
			echo sprintf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html( __( 'Settings saved.', 'contact-form-7' ) ) );
		}
	}

	public function display( $action = '' ) {
		echo '<p>' . sprintf(
			esc_html( __( 'reCAPTCHA protects you against spam and other types of automated abuse. With Contact Form 7&#8217;s reCAPTCHA integration module, you can block abusive form submissions by spam bots. For details, see %s.', 'contact-form-7' ) ),
			wpcf7_link(
				__( 'https://contactform7.com/recaptcha/', 'contact-form-7' ),
				__( 'reCAPTCHA (v3)', 'contact-form-7' )
			)
		) . '</p>';

		if ( $this->is_active() ) {
			echo sprintf(
				'<p class="dashicons-before dashicons-yes">%s</p>',
				esc_html( __( "reCAPTCHA is active on this site.", 'contact-form-7' ) )
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
		$sitekey = $this->is_active() ? $this->get_sitekey() : '';
		$secret = $this->is_active() ? $this->get_secret( $sitekey ) : '';

?>
<form method="post" action="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>">
<?php wp_nonce_field( 'wpcf7-recaptcha-setup' ); ?>
<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="sitekey"><?php echo esc_html( __( 'Site Key', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( $sitekey );
			echo sprintf(
				'<input type="hidden" value="%1$s" id="sitekey" name="sitekey" />',
				esc_attr( $sitekey )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%1$s" id="sitekey" name="sitekey" class="regular-text code" />',
				esc_attr( $sitekey )
			);
		}
	?></td>
</tr>
<tr>
	<th scope="row"><label for="secret"><?php echo esc_html( __( 'Secret Key', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( wpcf7_mask_password( $secret ) );
			echo sprintf(
				'<input type="hidden" value="%1$s" id="secret" name="secret" />',
				esc_attr( $secret )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%1$s" id="secret" name="secret" class="regular-text code" />',
				esc_attr( $secret )
			);
		}
	?></td>
</tr>
</tbody>
</table>
<?php
		if ( $this->is_active() ) {
			if ( $this->get_global_sitekey() && $this->get_global_secret() ) {
				// nothing
			} else {
				submit_button(
					_x( 'Remove Keys', 'API keys', 'contact-form-7' ),
					'small', 'reset'
				);
			}
		} else {
			submit_button( __( 'Save Changes', 'contact-form-7' ) );
		}
?>
</form>
<?php
	}
}
