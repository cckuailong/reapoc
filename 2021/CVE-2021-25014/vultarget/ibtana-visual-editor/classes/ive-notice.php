<?php
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'IVE_Admin_Notice' ) ) {


	class IVE_Admin_Notice {


		function __construct() {

			add_action( 'admin_notices', array( $this, 'ive_admin_notice' ) );

			add_action( 'wp_ajax_ive_get_admin_notices', array( $this, 'ive_get_admin_notices' ) );
      add_action( 'wp_ajax_ive_admin_notice_ignore', array( $this, 'ive_admin_notice_ignore' ) );
			add_action( 'wp_ajax_ive_get_theme_license_activation_duration', array( $this, 'ive_get_theme_license_activation_duration' ) );
			add_action( 'wp_ajax_ive_get_client_meta_box_info', array( $this, 'ive_get_client_meta_box_info' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'ive_notice_scripts' ) );


			$is_sirat_theme_installed = false;
			if ( count( wp_get_themes() ) && ( gettype( wp_get_themes() ) == 'array' ) ) {
				if ( isset( wp_get_themes()['sirat'] ) ) {
					$is_sirat_theme_installed = true;
				}
			}
			// $ive_vw_theme_admin_notice_dismiss	= get_option( 'ive-vw-theme-admin-notice-dismiss', false );
			$ive_vw_theme_admin_notice_dismiss = false;
			if ( ( wp_get_theme()->get( 'TextDomain' ) != 'sirat' ) && $is_sirat_theme_installed && !$ive_vw_theme_admin_notice_dismiss ) {
				add_action( 'admin_notices', array( $this, 'ive_admin_notice_sirat' ) );
				add_action( 'admin_init', array( $this, 'ive_vw_theme_admin_notice_dismiss' ) );
			}
		}



		function ive_get_client_meta_box_info() {

			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_notice_nonce' ) ) {
				exit;
			}

			$endpoint 			= IBTANA_LICENSE_API_ENDPOINT . 'get_client_meta_box_info';
			$args 		= [
				'theme_text_domain'	=> wp_get_theme()->get( 'TextDomain' )
			];
			$body			= wp_json_encode( $args );
			$options	= [
				'body'        => $body,
				'headers'     => [
					'Content-Type' => 'application/json',
				],
			];
			$request_data			= wp_remote_post( $endpoint, $options );
		}



		function ive_get_theme_license_activation_duration() {

			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_notice_nonce' ) ) {
				exit;
			}

			$endpoint 			= IBTANA_LICENSE_API_ENDPOINT . 'get_theme_license_activation_duration';
			$args 		= [
				'site_url'							=>	site_url(),
				'vw_pro_theme_key'			=>	sanitize_text_field( $_POST['vw_pro_theme_key'] ),
				'pro_theme_text_domain'	=>	sanitize_text_field( $_POST['pro_theme_text_domain'] )
			];
			$body			= wp_json_encode( $args );
			$options	= [
				'body'        => $body,
				'headers'     => [
					'Content-Type' => 'application/json',
				],
			];
			$request_data			= wp_remote_post( $endpoint, $options );

			if ( is_wp_error( $request_data ) ) {
				$response = array(
					'success'	=>	false,
					'msg'			=>	__( 'Something Went Wrong!', 'ibtana-visual-editor' )
				);
				wp_send_json( $response );
				exit;
			}

			$response_json		= json_decode( wp_remote_retrieve_body( $request_data ) );

			if ( !$response_json ) {
				$response = array(
					'success'	=>	false,
					'msg'			=>	__( 'Something Went Wrong!', 'ibtana-visual-editor' )
				);
				wp_send_json( $response );
				exit;
			}

			wp_send_json( $response_json );
		}


		function ive_vw_theme_admin_notice_dismiss() {
			if ( isset( $_GET['ive-vw-theme-admin-notice-dismiss'] ) ) {
				update_option( 'ive-vw-theme-admin-notice-dismiss', true );
			}
		}


		function ive_admin_notice_sirat() {
			$ive_sirat_theme_activate_action_url = wp_nonce_url(
				'themes.php?action=activate&amp;template=sirat&amp;stylesheet=sirat',
				'switch-theme_sirat'
			);
			?>
			<div id="ive-admin-notice-sirat" class="notice notice-info">
				<p>
					<?php
					printf(
						'<strong>%s</strong> %s <strong>%s</strong> %s',
						__( 'Ibtana - WordPress Website Builder', 'ibtana-visual-editor' ),
						__( 'better works with', 'ibtana-visual-editor' ),
						__( 'Sirat', 'ibtana-visual-editor' ),
						__( 'theme.', 'ibtana-visual-editor' )
					);
					?>

				</p>
				<p>
					<a href="<?php echo $ive_sirat_theme_activate_action_url; ?>" class="button-primary">
						<?php esc_html_e( 'Activate Sirat Theme', 'ibtana-visual-editor' ); ?>
					</a>
				</p>
				<!-- <a class="notice-dismiss" href="?ive-vw-theme-admin-notice-dismiss" type="button"></a> -->
			</div>
			<?php
		}


		function ive_get_admin_notices() {

			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_notice_nonce' ) ) {
				exit;
			}

			$endpoint 			=	IBTANA_LICENSE_API_ENDPOINT . 'get_client_admin_notices_for_client';
			$response 		 	=	wp_remote_post( $endpoint );

			if ( is_wp_error( $response ) ) {
				$response = array( 'status' => false, 'msg' => 'Something Went Wrong!' );
				wp_send_json( $response );
				exit;
			}

			$response_json	= json_decode( wp_remote_retrieve_body( $response ) );

			$notices_data		= $response_json->data;
			wp_send_json_success( $notices_data );
		}


		function ive_notice_scripts() {

			wp_enqueue_style( 'ive-notice-style', IBTANA_PLUGIN_DIR_URL . 'dist/css/ive-notice.css' );
			wp_enqueue_script( 'ive-notice-script', IBTANA_PLUGIN_DIR_URL . 'dist/js/ive-notice.js' );

			$ive_admin_notices = get_option( 'ive_admin_notices', [] );

			// Theme Text Domain START
			$theme_text_domain = '';
			if ( defined( 'CUSTOM_TEXT_DOMAIN' ) ) {
				$theme_text_domain = CUSTOM_TEXT_DOMAIN;
			} else {
				$theme_text_domain = wp_get_theme()->get( 'TextDomain' );
	  		if ( is_child_theme() ) {
	  			$theme_text_domain = wp_get_theme()->get( 'Template' );
	  		}
			}
			// Theme Text Domain END


			// Check if vw theme license is activated START
			$free_theme_text_domain	=	'';
			$pro_theme_text_domain	=	'';
			if ( defined( 'CUSTOM_TEXT_DOMAIN' ) ) {
				$pro_theme_text_domain		= wp_get_theme()->get( 'TextDomain' );
				if ( is_child_theme() ) {
					$pro_theme_text_domain	= wp_get_theme()->get( 'Template' );
				}
			} else {
				$free_theme_text_domain		=	wp_get_theme()->get( 'TextDomain' );
				if ( is_child_theme() ) {
					$free_theme_text_domain = wp_get_theme()->get( 'Template' );
				}
			}
			$theme_validation_status_option_key	=	str_replace( "-", "_", $pro_theme_text_domain ) . '_theme_validation_status';
			$theme_validation_status						=	get_option( $theme_validation_status_option_key, false );
			$vw_pro_theme_key										= get_option( 'vw_pro_theme_key', '' );
			// Check if vw theme license is activated END


			$ive_notice_params = array(
        'IBTANA_LICENSE_API_ENDPOINT'	=>	IBTANA_LICENSE_API_ENDPOINT,
        'ajax_url' 	 									=>	esc_url( admin_url( 'admin-ajax.php' ) ),
				'ive_admin_notices'						=>	$ive_admin_notices,

				'free_theme_text_domain'			=>	$free_theme_text_domain,
				'theme_text_domain'						=>	$theme_text_domain,
				'admin_url'										=>	esc_url( admin_url() ),

				'pro_theme_text_domain'				=>	$pro_theme_text_domain,
				'theme_validation_status'			=>	$theme_validation_status,
				'vw_pro_theme_key'						=>	$vw_pro_theme_key,
				'date'												=>	date( "Y-m-d H:i:s" ),
				'wpnonce'											=>	wp_create_nonce( 'ive_notice_nonce' )
      );
      wp_localize_script( 'ive-notice-script', 'ive_notice_params', $ive_notice_params );

		}


		function ive_admin_notice() {
			?>
        <div id="ive-admin-notice" class="notice">
        </div>
			<?php
		}


		function ive_admin_notice_ignore() {
			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_notice_nonce' ) ) {
				exit;
			}
      $ive_admin_notice_id	=	sanitize_text_field( $_POST['ive_admin_notice_id'] );
			$ive_admin_notices		=	get_option( 'ive_admin_notices', [] );

			array_push( $ive_admin_notices, $ive_admin_notice_id );

			if ( count( $ive_admin_notices ) > 50 ) {
				array_shift( $ive_admin_notices );
			}

      update_option( 'ive_admin_notices', $ive_admin_notices );
      wp_send_json_success();
		}


	}

	new IVE_Admin_Notice();

}
