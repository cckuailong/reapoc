<?php
/* "Copyright 2012 a3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\PageViewsCount\FrameWork {

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------
A3rev Plugin Admin UI

TABLE OF CONTENTS

- var plugin_name
- var admin_plugin_url
- var admin_plugin_dir
- var admin_pages
- admin_plugin_url()
- admin_plugin_dir()
- admin_pages()
- plugin_extension_start()
- plugin_extension_end()
- pro_fields_before()
- pro_fields_after()
- blue_message_box()

-----------------------------------------------------------------------------------*/

class Admin_UI
{
	/**
	 * @var string
	 * You must change to correct plugin name that you are working
	 */

	public $framework_version      = '2.5.0';
	public $plugin_name            = A3_PVC_KEY;
	public $plugin_path            = A3_PVC_PLUGIN_NAME;
	public $google_api_key_option  = '';
	public $google_map_api_key_option = '';
	public $toggle_box_open_option = '';
	public $version_transient      = '';
	public $is_free_plugin         = true;
	public $is_load_google_fonts   = true;
	
	public $support_url            = '';


	/**
	 * @var string
	 * You must change to correct class name that you are working
	 */
	public $class_name = 'A3_PVC';

	public $plugin_prefix = A3_PVC_PREFIX;

	/**
	 * @var string
	 * You must change to correct pro plugin page url on a3rev site
	 */
	public $pro_plugin_page_url = 'https://a3rev.com/shop/page-view-count/';

	/**
	 * @var string
	 */
	public $admin_plugin_url;

	/**
	 * @var string
	 */
	public $admin_plugin_dir;

	/**
	 * @var array
	 * You must change to correct page you want to include scripts & styles, if you have many pages then use array() : array( 'quotes-orders-mode', 'quotes-orders-rule' )
	 */
	public $admin_pages = array();

	public function __construct() {
		$this->google_api_key_option     = A3_PVC_KEY . '_google_api_key';
		$this->google_map_api_key_option = A3_PVC_KEY . '_google_map_api_key';
		$this->toggle_box_open_option    = A3_PVC_KEY . '_toggle_box_open';
		$this->version_transient         = A3_PVC_KEY . '_licinfo';

		if ( defined( 'A3_PVC_G_FONTS' ) ) {
			$this->is_load_google_fonts = (boolean) A3_PVC_G_FONTS;
		}

		$this->support_url = 'https://wordpress.org/support/plugin/page-views-count/';
		$this->update_google_map_api_key();
	}
	
	
	/*-----------------------------------------------------------------------------------*/
	/* admin_plugin_url() */
	/*-----------------------------------------------------------------------------------*/
	public function admin_plugin_url() {
		if ( $this->admin_plugin_url ) return $this->admin_plugin_url;
		return $this->admin_plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* admin_plugin_dir() */
	/*-----------------------------------------------------------------------------------*/
	public function admin_plugin_dir() {
		if ( $this->admin_plugin_dir ) return $this->admin_plugin_dir;
		return $this->admin_plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* admin_pages() */
	/*-----------------------------------------------------------------------------------*/
	public function admin_pages() {
		$admin_pages = apply_filters( $this->plugin_name . '_admin_pages', $this->admin_pages );

		return (array)$admin_pages;
	}

	public function is_valid_google_map_api_key( $cache=true ) {
		$is_valid = false;

		$this->google_map_api_key  = get_option( $this->google_map_api_key_option, '' );
		$google_map_api_key_enable = get_option( $this->google_map_api_key_option . '_enable', 0 );

		if ( '' != trim( $this->google_map_api_key ) && 1 == $google_map_api_key_enable ) {

			$google_map_api_key_status = get_transient( $this->google_map_api_key_option . '_status' );

			if ( ! $cache ) {
				$google_map_api_key_status = null;
			}

			if ( ! $google_map_api_key_status ) {
				$is_valid = $this->validate_google_map_api_key( $this->google_map_api_key );
			} elseif ( 'valid' == $google_map_api_key_status ) {
				$is_valid = true;
			}

		}

		return $is_valid;
	}

	public function validate_google_map_api_key( $g_key = '' ) {
		$g_key = trim( $g_key );
		$is_valid = false;

		if ( ! empty( $g_key ) ) {
			$respone_api = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?address=Australia&key=" . $g_key,
				array(
					'sslverify' => false,
					'timeout'   => 45
				)
			);

			$response_map = array();

			// Check it is a valid request
			if ( ! is_wp_error( $respone_api ) ) {

				$json_string = version_compare( PHP_VERSION, '7.4', '>=' ) || get_magic_quotes_gpc() ? stripslashes( $respone_api['body'] ) : $respone_api['body']; // @codingStandardsIgnoreLine // phpcs:ignore
				$response_map = json_decode( $json_string, true );

				// Make sure that the valid response from google is not an error message
				if ( ! isset( $response_map['error_message'] ) ) {
					$is_valid = true;
				}
			}

			if ( $is_valid ) {
				$google_map_api_key_status = 'valid';
			} else {
				$google_map_api_key_status = 'invalid';
			}

			//caching google map api status for 24 hours
			set_transient( $this->google_map_api_key_option . '_status', $google_map_api_key_status, 86400 );
		}

		return $is_valid;
	}

	public function update_google_map_api_key() {
		// Enable Google Map API Key
		if ( isset( $_POST[ $this->google_map_api_key_option . '_enable' ] ) ) {
			$old_google_map_api_key_enable = get_option( $this->google_map_api_key_option . '_enable', 0 );

			update_option( $this->google_map_api_key_option . '_enable', 1 );

			$option_value = trim( sanitize_text_field( $_POST[ $this->google_map_api_key_option ] ) );
			update_option( $this->google_map_api_key_option, $option_value );

			if ( 1 != $old_google_map_api_key_enable ) {

				// Clear cached of google map api key status
				delete_transient( $this->google_map_api_key_option . '_status' );
			}

		// Disable Google Map API Key
		} elseif ( isset( $_POST[ $this->google_map_api_key_option ] ) ) {
			$old_google_map_api_key_enable = get_option( $this->google_map_api_key_option . '_enable', 0 );

			update_option( $this->google_map_api_key_option . '_enable', 0 );

			$option_value = trim( sanitize_text_field( $_POST[ $this->google_map_api_key_option ] ) );
			update_option( $this->google_map_api_key_option, $option_value );

			if ( 0 != $old_google_map_api_key_enable ) {
				// Clear cached of google map api key status
				delete_transient( $this->google_map_api_key_option . '_status' );
			}
		}
	} 

	/**
	 * get_premium_video_data()
	 * return array
	 * Data is used for Premium Video Box
	 */
	public function get_premium_video_data() {
		$premium_video_data = array(
				'box_title'    => __( 'Premium Version Enhanced Features', 'page-views-count' ),
				'image_url'    => A3_PVC_IMAGES_URL. '/video.jpg',
				'video_url'    => 'https://www.youtube.com/embed/9dGw-ORfMIk?version=3&autoplay=1',
				'left_title'   => __( 'Premium Version Enhanced Features', 'page-views-count' ),
				'left_text'    => __( 'Page View Count Premium', 'page-views-count' )
									. "\n\n" . __( 'Quick Video showing the main (not all) enhanced features that are built into the Page View Count Premium version', 'page-views-count' ),
				'right_title'  => __( 'Developer Support and Premium Features', 'page-views-count' ),
				'right_text'   => __( 'Limited Time Offer. Purchase the Premium Version Lifetime License. That is a Lifetime of maintenance updates, feature upgrades and developer support for a once only fee. Offer ending soon.', 'page-views-count' )
									. "\n\n" . '<a target="_blank" href="'.$this->pro_plugin_page_url.'" class="button-primary">' . __( 'Get Premium Features and Support', '' ) . '</a>',
			);

		return $premium_video_data;
	}

	public function plugin_premium_video_box( $echo = true ) {
		$premium_video_data = apply_filters( $this->plugin_name . '_plugin_premium_video_data', $this->get_premium_video_data() );

		$output = '<div id="a3_plugin_premium_video_container">';
		$output .= '<div class="a3rev_panel_container">';
		$output .= '<div class="a3rev_panel_box">';
		$output .= '<div class="a3rev_panel_box_handle">';
		$output .= '<h3 class="a3-plugin-ui-panel-box">'.$premium_video_data['box_title'].'</h3>';
		$output .= '</div>';
		$output .= '<div class="a3rev_panel_video_box">';
		$output .= $this->plugin_premium_video();
		$output .= $this->plugin_premium_video_text();
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		$output = apply_filters( $this->plugin_name . '_plugin_premium_video', $output );

		if ( $echo )
			echo $output;
		else
			return $output;
	}

	public function plugin_premium_video( $echo = false ) {
		$premium_video_data = apply_filters( $this->plugin_name . '_plugin_premium_video_data', $this->get_premium_video_data() );

		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');

		$output = '<div class="a3rev_panel_box_left_inside">';
		$output .= '<h2>' . $premium_video_data['left_title'] . '</h2>';
		$output .= '<a class="a3-plugin-premium-image thickbox" title="'.esc_attr( $premium_video_data['left_title'] ).'" href="'.esc_url( add_query_arg( array( 'TB_iframe' => 'true', 'width' => 640, 'height' => 360 ), $premium_video_data['video_url'] )  ).'">';
		$output .= '<img src="'.esc_url( $premium_video_data['image_url'] ).'" />';
		$output .= '<div class="a3-plugin-premium-video-play"></div>';
		$output .= '</a>';
		$output .= wpautop( $premium_video_data['left_text'] );
		$output .= '</div>';

		if ( $echo )
			echo $output;
		else
			return $output;
	}

	public function plugin_premium_video_text( $echo = false ) {
		$premium_video_data = apply_filters( $this->plugin_name . '_plugin_premium_video_data', $this->get_premium_video_data() );

		$output = '';
		if ( '' != trim( $premium_video_data['right_text'] ) ) {
			$output .= '<div class="a3rev_panel_box_separate"></div>';
			$output .= '<div class="a3rev_panel_box_right_inside">';
			$output .= '<h2>' . $premium_video_data['right_title'] . '</h2>';
			$output .= wpautop( $premium_video_data['right_text'] );
			$output .= '</div>';
		}

		if ( $echo )
			echo $output;
		else
			return $output;
	}

	public function plugin_extension_boxes( $echo = false ) {

		/**
		 * extension_boxes
		 * =============================================
		 * array (
		 *		'id'				=> 'box_id'						: Enter unique your box id
		 *		'content'			=> 'html_content' 				: (required) Enter the html content to show inside the box
		 * 		'css'				=> 'custom style'				: custom style for the box container
		 * )
		 *
		 */
		$extension_boxes = apply_filters( $this->plugin_name . '_plugin_extension_boxes', array() );

		$output = '';
		if ( is_array( $extension_boxes ) && count( $extension_boxes ) > 0 ) {
			foreach ( $extension_boxes as $box ) {
				if ( ! isset( $box['id'] ) ) $box['id'] = '';
				if ( ! isset( $box['class'] ) ) $box['class'] = '';
				if ( ! isset( $box['css'] ) ) $box['css'] = '';
				if ( ! isset( $box['content'] ) ) $box['content'] = '';

				$output .= '<div id="'. esc_attr( $box['id'] ) .'" class="'. esc_attr( $box['class'] ) .' a3_plugin_panel_extension_box" style="'. esc_attr( $box['css'] ) .'">';
				$output .= $box['content'];
				$output .= '</div>';
			}
		}

		if ( $echo )
			echo $output;
		else
			return $output;
	}

	/*-----------------------------------------------------------------------------------*/
	/* plugin_extension_start() */
	/* Start of yellow box on right for pro fields
	/*-----------------------------------------------------------------------------------*/
	public function plugin_extension_start( $echo = true ) {
		$output = '<div id="a3_plugin_panel_container">';
		$output .= '<div id="a3_plugin_panel_upgrade_area">';
		$output .= '<div id="a3_plugin_panel_extensions">';
		$output .= $this->plugin_extension_boxes( false );
		$output .= '</div>';
		$output .= '</div>';
		$output .= '<div id="a3_plugin_panel_fields">';

		$output = apply_filters( $this->plugin_name . '_plugin_extension_start', $output );

		if ( $echo )
			echo $output;
		else
			return $output;
	}

	/*-----------------------------------------------------------------------------------*/
	/* plugin_extension_start() */
	/* End of yellow box on right for pro fields
	/*-----------------------------------------------------------------------------------*/
	public function plugin_extension_end( $echo = true ) {
		$output = '</div>';
		$output .= '</div>';

		$output = apply_filters( $this->plugin_name . '_plugin_extension_end', $output );

		if ( $echo )
			echo $output;
		else
			return $output;

	}

	/*-----------------------------------------------------------------------------------*/
	/* upgrade_top_message() */
	/* Show upgrade top message for pro fields
	/*-----------------------------------------------------------------------------------*/
	public function upgrade_top_message( $echo = false, $setting_id = '' ) {
		$upgrade_top_message = sprintf( '<div class="pro_feature_top_message">'
			. __( 'Advanced Settings - Upgrade to the <a href="%s" target="_blank">%s License</a> to activate these settings.', 'page-views-count' )
			. '</div>'
			, apply_filters( $this->plugin_name . '_' . $setting_id . '_pro_plugin_page_url', apply_filters( $this->plugin_name . '_pro_plugin_page_url', $this->pro_plugin_page_url ) )
			, apply_filters( $this->plugin_name . '_' . $setting_id . '_pro_version_name', apply_filters( $this->plugin_name . '_pro_version_name', __( 'Pro Version', 'page-views-count' ) ) )
		);

		$upgrade_top_message = apply_filters( $this->plugin_name . '_upgrade_top_message', $upgrade_top_message, $setting_id );

		if ( $echo ) echo $upgrade_top_message;
		else return $upgrade_top_message;

	}

	/*-----------------------------------------------------------------------------------*/
	/* pro_fields_before() */
	/* Start of yellow box on right for pro fields
	/*-----------------------------------------------------------------------------------*/
	public function pro_fields_before( $echo = true ) {
		echo apply_filters( $this->plugin_name . '_pro_fields_before', '<div class="pro_feature_fields">'. $this->upgrade_top_message() );
	}

	/*-----------------------------------------------------------------------------------*/
	/* pro_fields_after() */
	/* End of yellow border for pro fields
	/*-----------------------------------------------------------------------------------*/
	public function pro_fields_after( $echo = true ) {
		echo apply_filters( $this->plugin_name . '_pro_fields_after', '</div>' );
	}

	/*-----------------------------------------------------------------------------------*/
	/* blue_message_box() */
	/* Blue Message Box
	/*-----------------------------------------------------------------------------------*/
	public function blue_message_box( $message = '', $width = '600px' ) {
		$message = '<div class="a3rev_blue_message_box_container" style="width:'.$width.'"><div class="a3rev_blue_message_box">' . $message . '</div></div>';
		$message = apply_filters( $this->plugin_name . '_blue_message_box', $message );

		return $message;
	}

	/*-----------------------------------------------------------------------------------*/
	/* get_version_message() */
	/* Get new version message, also include error connect
	/*-----------------------------------------------------------------------------------*/
	public function get_version_message() {
		$version_message = '';

		//Getting version number
		$version_transient = get_transient( $this->version_transient );
		if ( false !== $version_transient ) {
			$transient_timeout = '_transient_timeout_' . $this->version_transient;
			$timeout = get_option( $transient_timeout, false );
			if ( false === $timeout ) {
				$version_message = __( 'You should check now to see if have any new version is available', 'page-views-count' );
			} elseif ( 'cannot_connect_api' == $version_transient ) {
				$version_message = sprintf( __( 'Connection Failure! Please try again. If this issue persists please create a support request on the plugin <a href="%s" target="_blank">a3rev support forum</a>.', 'page-views-count' ), $this->support_url );
			} else {
				$version_info = explode( '||', $version_transient );
				if ( FALSE !== stristr( $version_transient, '||' )
					&& is_array( $version_info )
					&& isset( $version_info[1] ) && $version_info[1] == 'valid'
					&& version_compare( A3_PVC_VERSION , $version_info[0], '<' ) ) {

						$version_message = sprintf( __( 'There is a new version <span class="a3rev-ui-new-plugin-version">%s</span> available, <a href="%s" target="_blank">update now</a> or download direct from <a href="%s" target="_blank">My Account</a> on a3rev.com', 'page-views-count' ),
							$version_info[0],
							wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . $this->plugin_path ), 'upgrade-plugin_' . $this->plugin_path ),
							'https://a3rev.com/my-account/downloads/'
						);
				}
			}

		} else {
			$version_message = __( 'You should check now to see if have any new version is available', 'page-views-count' );
		}

		return $version_message;
	}

}

}
