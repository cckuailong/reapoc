<?php
/**
 * Accesibe Extension Class
 *
 * @category Ext_Accessibe
 * @package  Ext_Accessibe
 * @author   Author <info@querysol.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://querysol.com/
 */
class Ext_Accessibe extends WPCF7R_Action {
	public $ver       = '1.0';
	private $api_url  = ACCESSIBE_API_URI;
	private $ext_name = 'accessibe';

	/**
	 * Class constructor
	 */
	public function __construct() {
		if ( isset( $_GET['scan'] ) ) {
			$this->reset_scan_results();
		}
		if ( is_admin() ) {
			$this->admin_init();
		}
	}

	/**
	 * Get the extension name
	 */
	public function get_name() {
		$name = $this->ext_name;
		return $name;
	}

	/**
	 * Get the option key that will store the widget settings
	 */
	public function get_widget_option_key() {
		$key = 'accesibe_widget_options';
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$key .= '_' . ICL_LANGUAGE_CODE;
		}
		return $key;
	}

	/**
	 * Initiate admin scripts
	 */
	public function admin_init() {
		if ( isset( $_GET['page'] ) && qs_get_plugin_display_name() === $_GET['page'] ) {
			$response = $this->acctivate_acccesbe_menu();
		}
		if ( isset( $_REQUEST['start-free-trial'] ) && qs_get_plugin_display_name() === $_REQUEST['start-free-trial'] ) {
			$response = $this->mark_scan_completed();
		}
		if ( isset( $_POST['activate-accesibe'] ) && $_POST['activate-accesibe'] ) {
			$response = $this->activate();
			if ( is_wp_error( $response ) ) {
				WPCF7r_Utils::add_admin_notice( 'alert', $response->get_error_message() );
			} else {
				WPCF7r_Utils::add_admin_notice( 'notice', __( 'Successfully activated' ) );
			}
		}
		if ( isset( $_GET['deactivate'] ) && qs_get_plugin_display_name() === $_GET['deactivate'] ) {
			$this->deactivate();
		}
		if ( isset( $_REQUEST['save_ext_settings'] ) && qs_get_plugin_display_name() === $_REQUEST['save_ext_settings'] ) {
			$this->save();
		}
		$this->init();
	}

	/**
	 * Save widget settings
	 */
	private function save() {
		$name = $this->get_name();
		$key  = $this->get_widget_option_key();
		if ( isset( $_REQUEST['wpcf7-redirect'][ $key ][ $name ] ) && $_REQUEST['wpcf7-redirect'][ $key ][ $name ] ) {
			$data = $_REQUEST['wpcf7-redirect'][ $key ][ $name ];
			update_option( $key, $data );
		} elseif ( isset( $_REQUEST['save_ext_settings'] ) ) {
			update_option( $key, array() );
		}
		$this->init();
	}

	/**
	 * Get this extension settings page url
	 */
	public function get_settings_url() {
		return admin_url( '/admin.php?page=' . qs_get_plugin_display_name() );
	}

	/**
	 * The link will reset the scan data to allow a new scan to process
	 */
	public function get_scan_link() {
		$url = $this->get_settings_url();
		$url = add_query_arg(
			array(
				'scan' => true,
			)
		);
		return $url;
	}

	/**
	 * Check if scan was commited and saved
	 */
	public function get_scan_results() {
		return get_option( 'accesibe_scan_results' );
	}

	/**
	 * Get the widget settings
	 */
	public function get_widget_settings() {
		$key = $this->get_widget_option_key();
		return get_option( $key );
	}

	/**
	 * Check if the plugin is active
	 */
	public function is_active() {
		$settings = $this->get_settings();
		return $settings ? true : false;
	}

	/**
	 * Check if the current screen is a registration form
	 * @return boolean
	 */
	public function is_registration_form() {
		return ! $this->is_scan() && ! $this->is_active();
	}

	/**
	 * Check if the plugin is active
	 *
	 * @return boolean
	 */
	public function is_scan() {
		$results = $this->get_scan_results();
		return $results ? false : true;
	}

	/**
	 * Set a flag that a scan was completed
	 */
	public function mark_scan_completed() {
		update_option( 'accesibe_scan_results', true );
	}

	/**
	 * Allow the user to rescan the website
	 */
	public function reset_scan_results() {
		delete_option( 'accesibe_scan_results' );
	}

	/**
	 * Init extension
	 */
	public function init() {
		if ( ! get_option( 'hide_accessibie_menu' ) && get_option( 'show_accessibie_menu' ) ) {
			add_action( 'admin_menu', array( $this, 'accessibie_menu' ) );
		}
		$this->accesibe_widget_options = $this->get_widget_settings();
	}

	/**
	 * Deactivate the extension
	 */
	public function deactivate() {
		delete_option( 'show_accessibie_menu' );
		delete_option( 'accesibe_options' );
		delete_option( 'accesibe_scan_results' );
		update_option( 'hide_accessibie_menu', true );
	}

	/**
	 * Get the value of a specific field
	 */
	public function get_field_value( $field ) {
		if ( $this->accesibe_widget_options ) {
			return isset( $this->accesibe_widget_options[ $field ] ) ? $this->accesibe_widget_options[ $field ] : '';
		}
	}

	/**
	 * Get the template to display on the admin field
	 *
	 * @param $template
	 */
	public function get_settings_template( $template ) {
		$name   = $this->get_name();
		$prefix = "[accesibe_widget_options][{$name}]";
		include WPCF7_PRO_REDIRECT_ACTIONS_TEMPLATE_PATH . $template;
	}

	/**
	 * General function to retrieve meta
	 *
	 * @param $key
	 */
	public function get( $key ) {
		return isset( $this->accesibe_widget_options[ $key ] ) ? $this->accesibe_widget_options[ $key ] : '';
	}

	/**
	 * Get the accessibe settings form
	 */
	public function get_settings_form() {
		$this->get_settings_template( 'html-page-settings.php' );
	}

	/**
	 * Display settings fields for active users
	 */
	public function get_accesibe_settings() {
		$this->html = new WPCF7R_html( '' );
		include( WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'settings.php' );
	}

	/**
	* Get the fields relevant for this action
	*/
	public function get_action_fields() {
		return
			array(
				array(
					'name'        => 'hideMobile',
					'type'        => 'checkbox',
					'label'       => __( 'Hide On Mobile', 'wpcf7-redirect' ),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'hideMobile' ),
				),
				array(
					'name'        => 'hideTrigger',
					'type'        => 'checkbox',
					'label'       => __( 'Hide Trigger', 'wpcf7-redirect' ),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'hideTrigger' ),
				),
				array(
					'name'        => 'leadColor',
					'type'        => 'text',
					'input_class' => 'colorpicker',
					'label'       => __( 'Main Color', 'wpcf7-redirect' ),
					'class'       => 'qs-col qs-col-6',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'leadColor' ) ? $this->get( 'leadColor' ) : '#146FF8',
				),
				array(
					'name'        => 'triggerColor',
					'type'        => 'text',
					'input_class' => 'colorpicker',
					'label'       => __( 'Trigger Color', 'wpcf7-redirect' ),
					'class'       => 'qs-col qs-col-6',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'triggerColor' ) ? $this->get( 'triggerColor' ) : '#146FF8',
				),
				array(
					'name'        => 'triggerIcon',
					'type'        => 'media',
					'label'       => __( 'Trigger Icon', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'triggerIcon' ) ? $this->get( 'triggerIcon' ) : 'default',
				),
				array(
					'name'        => 'triggerSize',
					'type'        => 'select',
					'label'       => __( 'Trigger Size', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'options'     => array(
						'small'  => __( 'Small' ),
						'medium' => __( 'Medium' ),
						'large'  => __( 'Large' ),
					),
					'value'       => $this->get( 'triggerSize' ) ? $this->get( 'triggerSize' ) : 'medium',
				),
				array(
					'name'        => 'statementLink',
					'type'        => 'url',
					'label'       => __( 'Link To Statment', 'wpcf7-redirect' ),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'statementLink' ),
				),
				array(
					'name'        => 'feedbackLink',
					'type'        => 'url',
					'label'       => __( 'Link To Feedback', 'wpcf7-redirect' ),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'feedbackLink' ),
				),
				array(
					'name'        => 'language',
					'type'        => 'select',
					'label'       => __( 'Widget Language', 'wpcf7-redirect' ),
					'class'       => 'select2-field',
					'sub_title'   => '',
					'placeholder' => '',
					'options'     => $this->get_available_languages(),
					'value'       => $this->get( 'language' ),
				),
				array(
					'name'        => 'position',
					'type'        => 'select',
					'label'       => __( 'Widget Position', 'wpcf7-redirect' ),
					'class'       => '',
					'options'     => array(
						'right' => __( 'Right' ),
						'left'  => __( 'Left' ),
					),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'position' ),
				),
				array(
					'name'        => 'triggerRadius',
					'type'        => 'number',
					'label'       => __( 'Trigger Border Radius (%)', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'triggerRadius' ) ? $this->get( 'triggerRadius' ) : '50%',
				),
				array(
					'name'        => 'triggerPositionX',
					'type'        => 'select',
					'label'       => __( 'Trigger Position (X)', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'options'     => array(
						'right' => __( 'Right' ),
						'left'  => __( 'Left' ),
					),
					'value'       => $this->get( 'triggerPositionX' ) ? $this->get( 'triggerPositionX' ) : 'left',
				),
				array(
					'name'        => 'triggerOffsetX',
					'type'        => 'number',
					'label'       => __( 'Trigger Offset X (Pixels)', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'triggerOffsetX' ) ? $this->get( 'triggerOffsetX' ) : 0,
				),
				array(
					'name'        => 'triggerPositionY',
					'type'        => 'select',
					'label'       => __( 'Trigger Position (X)', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'options'     => array(
						'top'    => __( 'Top' ),
						'bottom' => __( 'Bottom' ),
					),
					'value'       => $this->get( 'triggerPositionY' ) ? $this->get( 'triggerPositionY' ) : 'bottom',
				),
				array(
					'name'        => 'triggerOffsetY',
					'type'        => 'number',
					'label'       => __( 'Trigger Offset Y (Pixels)', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'triggerOffsetY' ) ? $this->get( 'triggerOffsetY' ) : 0,
				),
				array(
					'name'        => 'triggerSizeMobile',
					'type'        => 'select',
					'label'       => __( 'Trigger Size Mobile', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'options'     => array(
						'small'  => __( 'Small' ),
						'medium' => __( 'Medium' ),
						'large'  => __( 'Large' ),
					),
					'value'       => $this->get( 'triggerSizeMobile' ) ? $this->get( 'triggerSizeMobile' ) : 'medium',
				),
				array(
					'name'        => 'triggerPositionXMobile',
					'type'        => 'select',
					'label'       => __( 'Trigger Position (X) Mobile', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'options'     => array(
						'right' => __( 'Right' ),
						'left'  => __( 'Left' ),
					),
					'value'       => $this->get( 'triggerPositionXMobile' ) ? $this->get( 'triggerPositionXMobile' ) : 'left',
				),
				array(
					'name'        => 'triggerOffsetXMobile',
					'type'        => 'number',
					'label'       => __( 'Trigger Offset X (Pixels) Mobile', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'triggerOffsetXMobile' ) ? $this->get( 'triggerOffsetXMobile' ) : 0,
				),
				array(
					'name'        => 'triggerPositionYMobile',
					'type'        => 'select',
					'label'       => __( 'Trigger Position (X) Mobile', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'options'     => array(
						'top'    => __( 'Top' ),
						'bottom' => __( 'Bottom' ),
					),
					'value'       => $this->get( 'triggerPositionYMobile' ) ? $this->get( 'triggerPositionYMobile' ) : 'bottom',
				),
				array(
					'name'        => 'triggerOffsetYMobile',
					'type'        => 'number',
					'label'       => __( 'Trigger Offset Y (Pixels)', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'triggerOffsetYMobile' ) ? $this->get( 'triggerOffsetYMobile' ) : 0,
				),
				array(
					'name'        => 'triggerRadiusMobile',
					'type'        => 'number',
					'label'       => __( 'Trigger Border Radius (%) Mobile', 'wpcf7-redirect' ),
					'class'       => '',
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'triggerRadiusMobile' ) ? $this->get( 'triggerRadiusMobile' ) : '50%',
				),
				array(
					'name'        => 'footerHtml',
					'type'        => 'editor',
					'label'       => __( 'Footer Html', 'wpcf7-redirect' ),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'footerHtml' ),
				),
			);
	}

	/**
	 * Get a list of available widget languages
	 */
	public function get_available_languages() {
		return wpcf7_get_languages_list();
	}

	/**
	 * Display accesibe menu
	 */
	public function acctivate_acccesbe_menu() {
		update_option( 'show_accessibie_menu', true );
		delete_option( 'hide_accessibie_menu' );
	}

	/**
	 * Activate the extension
	 */
	public function activate() {
		$args      = array(
			'email'          => sanitize_email( $_POST['email'] ),
			'name'           => sanitize_text_field( $_POST['fullname'] ),
			'password'       => sanitize_text_field( $_POST['password'] ),
			'domain'         => str_replace( array( 'http://', 'https://' ), '', home_url() ),
			'ip_address'     => $_SERVER['SERVER_ADDR'],
			'contactCountry' => isset( $_POST['user-country'] ) ? sanitize_text_field( $_POST['user-country'] ) : '',
			'contactPhone'   => isset( $_POST['phone-number'] ) ? sanitize_text_field( $_POST['phone-number'] ) : '',
		);
		$post_args = array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'headers'     => array(
				'Content-Type' => 'application/json',
			),
			'body'        => wp_json_encode( $args ),
			'cookies'     => array(),
		);
		$response  = wp_remote_post( $this->api_url, $post_args );
		
		if ( ! is_wp_error( $response ) ) {
			$response = wp_remote_retrieve_body( $response );
			$response = (object) json_decode( $response, true );
			if ( 1 === $response->status ) {
				$options_args = array(
					'accessibe_plugin_active'          => $this->ver,
					'accessibe_plugin_domain'          => $args['domain'],
					'accessibe_plugin_dativation_date' => current_time( 'Ymd' ),
				);
				update_option( 'accesibe_options', $options_args );
			} else {
				$options_args = array(
					'accessibe_plugin_active'          => $this->ver,
					'accessibe_plugin_domain'          => $args['domain'],
					'accessibe_plugin_dativation_date' => current_time( 'Ymd' ),
				);
				update_option( 'accesibe_options', $options_args );
				$response = new WP_Error( 'activate', __( 'This Domain Is Already Registered' ) );
			}
		}
		return $response;
	}

	/**
	 * Get accesibe options
	 */
	public function get_settings() {
		return get_option( 'accesibe_options' );
	}

	/**
	 * Create Accesibie Menu
	 */
	public function accessibie_menu() {
		// Add the menu item and page!
		$page_title = qs_get_plugin_display_name();
		$capability = 'manage_options';
		$callback   = array( $this, 'accesibie_settings_page_content' );
		$icon       = WPCF7_PRO_REDIRECT_BUILD_PATH . '/images/accesibie-logo.png';
		add_menu_page(
			$page_title,
			$page_title,
			$capability,
			$page_title,
			$callback,
			$icon
		);
	}

	/**
	 * Extension page Content
	 */
	public function accesibie_settings_page_content() {
		do_action( 'before_settings_fields' );
		?>

		<div class="wrap wrap-accesibe">
			<div class="postbox">
				<div class="padbox">
					<div class="content">
						<?php include 'templates/accesibie-content.php'; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Echo the script in the footer
	 */
	public function footer_script() {
		$params  = array(
			'statementLink'    => $this->get( 'statementLink' ),
			'feedbackLink'     => $this->get( 'feedbackLink' ),
			'footerHtml'       => $this->get( 'footerHtml' ),
			'hideMobile'       => $this->get( 'hideMobile' ),
			'hideTrigger'      => $this->get( 'hideTrigger' ),
			'language'         => $this->get( 'language' ) ? $this->get( 'language' ) : 'en',
			'position'         => $this->get( 'position' ) ? $this->get( 'position' ) : 'right',
			'leadColor'        => $this->get( 'leadColor' ) ? $this->get( 'leadColor' ) : '#146FF8',
			'triggerColor'     => $this->get( 'triggerColor' ) ? $this->get( 'triggerColor' ) : '#146FF8',
			'triggerRadius'    => $this->get( 'triggerRadius' ) ? $this->get( 'triggerRadius' ) : '50%',
			'triggerPositionX' => $this->get( 'triggerPositionX' ) ? $this->get( 'triggerPositionX' ) : 'right',
			'triggerPositionY' => $this->get( 'triggerPositionY' ) ? $this->get( 'triggerPositionY' ) : 'bottom',
			'triggerIcon'      => $this->get( 'triggerIcon' ) ? $this->get( 'triggerIcon' ) : 'default',
			'triggerSize'      => $this->get( 'triggerSize' ) ? $this->get( 'triggerSize' ) : 'medium',
			'triggerOffsetX'   => $this->get( 'triggerOffsetX' ) ? $this->get( 'triggerOffsetX' ) : 20,
			'triggerOffsetY'   => $this->get( 'triggerOffsetY' ) ? $this->get( 'triggerOffsetY' ) : 20,
			'mobile'           => (object) array(
				'triggerSize'      => $this->get( 'triggerSizeMobile' ) ? $this->get( 'triggerSizeMobile' ) : 'small',
				'triggerPositionX' => $this->get( 'triggerPositionXMobile' ) ? $this->get( 'triggerPositionXMobile' ) : 'right',
				'triggerPositionY' => $this->get( 'triggerPositionYMobile' ) ? $this->get( 'triggerPositionYMobile' ) : 'center',
				'triggerOffsetX'   => $this->get( 'triggerOffsetXMobile' ) ? $this->get( 'triggerOffsetXMobile' ) : 0,
				'triggerOffsetY'   => $this->get( 'triggerOffsetYMobile' ) ? $this->get( 'triggerOffsetYMobile' ) : 0,
				'triggerRadius'    => $this->get( 'triggerRadiusMobile' ) ? $this->get( 'triggerRadiusMobile' ) : '50%',
			),
		);
		$options = json_encode( $params, JSON_UNESCAPED_UNICODE );
		?>
		<script>(function(){var s = document.createElement('script'),e = ! document.body ? document.querySelector('head') : document.body;s.src = 'https://acsbapp.com/apps/app/assets/js/acsb.js';s.async = s.defer = true;s.onload = function(){acsbJS.init(<?php echo $options; ?>);};e.appendChild(s);}());</script>
		<?php
	}

	/**
	 * Enqueue script if the plugin is active
	 */
	public static function enqueue_script() {
		if ( ! is_admin() ) {
			$instance = new self();
			$instance->init();
			if ( $instance->is_active() ) {
				add_action( 'wp_footer', array( $instance, 'footer_script' ) );
			}
		}
	}
}

function qs_get_plugin_display_name() {
	return apply_filters( 'qs_get_plugin_display_name', 'Accessibility' );
}

Ext_Accessibe::enqueue_script();
