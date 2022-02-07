<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Admin class.
 *
 * TODO Future: Look into making this class smaller
 */
class DLM_Admin {

	/**
	 * Variable indicating if rewrites need a flush
	 *
	 * @var bool
	 */
	private $need_rewrite_flush = false;

	/**
	 * Setup actions etc.
	 */
	public function setup() {

		// Directory protection
		add_filter( 'mod_rewrite_rules', array( $this, 'ms_files_protection' ) );
		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Admin menus
		add_action( 'admin_menu', array( $this, 'admin_menu_extensions' ), 20 );

		// setup settings
		$settings = new DLM_Admin_Settings();
		add_action( 'admin_init', array( $settings, 'register_settings' ) );
		add_filter( 'dlm_settings', array( $settings, 'backwards_compatibility_settings' ), 99, 1 );
		$settings->register_lazy_load_callbacks();

		// setup settings page
		$settings_page = new DLM_Settings_Page();
		$settings_page->setup();

		// setup logs
		$log_page = new DLM_Log_Page();
		$log_page->setup();

		// setup report
		$reports_page = new DLM_Reports_Page();
		$reports_page->setup();

		// Dashboard
		add_action( 'wp_dashboard_setup', array( $this, 'admin_dashboard' ) );

		// Admin Footer Text
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );

		// flush rewrite rules on shutdown
		add_action( 'shutdown', array( $this, 'maybe_flush_rewrites' ) );

		// filter attachment thumbnails in media library for files in dlm_uploads
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'filter_thumbnails_protected_files_grid' ), 10, 1 );
		add_filter( 'wp_get_attachment_image_src', array( $this, 'filter_thumbnails_protected_files_list' ), 10, 1 );

		// Legacy Upgrader
		$lu_check = new DLM_LU_Checker();
		if ( $lu_check->needs_upgrading() ) {
			$lu_message = new DLM_LU_Message();
			$lu_message->display();
		}

	}

	/**
	 * ms_files_protection function.
	 *
	 * @access public
	 *
	 * @param mixed $rewrite
	 *
	 * @return string
	 */
	public function ms_files_protection( $rewrite ) {

		if ( ! is_multisite() ) {
			return $rewrite;
		}

		$rule = "\n# DLM Rules - Protect Files from ms-files.php\n\n";
		$rule .= "<IfModule mod_rewrite.c>\n";
		$rule .= "RewriteEngine On\n";
		$rule .= "RewriteCond %{QUERY_STRING} file=dlm_uploads/ [NC]\n";
		$rule .= "RewriteRule /ms-files.php$ - [F]\n";
		$rule .= "</IfModule>\n\n";

		return $rule . $rewrite;
	}

	/**
	 * upload_dir function.
	 *
	 * @access public
	 *
	 * @param mixed $pathdata
	 *
	 * @return array
	 */
	public function upload_dir( $pathdata ) {

		if ( isset( $_POST['type'] ) && 'dlm_download' === $_POST['type'] ) {
			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/dlm_uploads';
				$pathdata['url']    = $pathdata['url'] . '/dlm_uploads';
				$pathdata['subdir'] = '/dlm_uploads';
			} else {
				$new_subdir = '/dlm_uploads' . $pathdata['subdir'];

				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}
		}

		return $pathdata;
	}

	/**
	 * filter attachment thumbnails in media library grid view for files in dlm_uploads
	 *
	 * @param array $response
	 *
	 * @return array
	 */
	public function filter_thumbnails_protected_files_grid( $response ) {

		if ( apply_filters( 'dlm_filter_thumbnails_protected_files', true ) ) {
			$upload_dir = wp_upload_dir();

			if ( strpos( $response['url'], $upload_dir['baseurl'] . '/dlm_uploads' ) !== false ) {
				if ( ! empty( $response['sizes'] ) ) {
					$dlm_protected_thumb = download_monitor()->get_plugin_url() . '/assets/images/protected-file-thumbnail.png';
					foreach ( $response['sizes'] as $rs_key => $rs_val ) {
						$rs_val['url']                = $dlm_protected_thumb;
						$response['sizes'][ $rs_key ] = $rs_val;
					}
				}
			}
		}

		return $response;
	}

	/**
	 * filter attachment thumbnails in media library list view for files in dlm_uploads
	 *
	 * @param bool|array $image
	 *
	 * @return bool|array
	 */
	public function filter_thumbnails_protected_files_list( $image ) {
		if ( apply_filters( 'dlm_filter_thumbnails_protected_files', true ) ) {
			if ( $image ) {

				$upload_dir = wp_upload_dir();

				if ( strpos( $image[0], $upload_dir['baseurl'] . '/dlm_uploads' ) !== false ) {
					$image[0] = $dlm_protected_thumb = download_monitor()->get_plugin_url() . '/assets/images/protected-file-thumbnail.png';
					$image[1] = 60;
					$image[2] = 60;
				}
			}

		}

		return $image;
	}

	/**
	 * admin_enqueue_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		global $post;

		wp_enqueue_style( 'download_monitor_menu_css', download_monitor()->get_plugin_url() . '/assets/css/menu.css', array(), DLM_VERSION );

		if ( $hook == 'index.php' ) {
			wp_enqueue_style( 'download_monitor_dashboard_css', download_monitor()->get_plugin_url() . '/assets/css/dashboard.css', array(), DLM_VERSION );
		}

		$enqueue = false;

		if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'edit.php' ) {
			if (
				( ! empty( $_GET['post_type'] ) && in_array( $_GET['post_type'], array(
						'dlm_download',
						\Never5\DownloadMonitor\Shop\Util\PostType::KEY
					) ) )
				||
				( ! empty( $post->post_type ) && in_array( $post->post_type, array(
						'dlm_download',
						\Never5\DownloadMonitor\Shop\Util\PostType::KEY
					) ) )
			) {
				$enqueue = true;
			}
		}

		if ( strstr( $hook, 'dlm_download_page' ) ) {
			$enqueue = true;
		}

		if ( $hook == 'edit-tags.php' && strstr( $_GET['taxonomy'], 'dlm_download' ) ) {
			$enqueue = true;
		}

		if ( isset( $_GET['page'] ) && 'download-monitor-orders' === $_GET['page'] ) {
			$enqueue = true;
		}

		if ( ! $enqueue ) {
			return;
		}

		wp_enqueue_script( 'jquery-blockui', download_monitor()->get_plugin_url() . '/assets/js/blockui.min.js', array( 'jquery' ), '2.61' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-style', download_monitor()->get_plugin_url() . '/assets/css/jquery-ui.css', array(), DLM_VERSION );
		wp_enqueue_style( 'download_monitor_admin_css', download_monitor()->get_plugin_url() . '/assets/css/admin.css', array( 'dashicons' ), DLM_VERSION );
	}

	/**
	 * Add the admin menu on later hook so extensions can be add before this menu item
	 */
	public function admin_menu_extensions() {
		// Extensions page
		add_submenu_page( 'edit.php?post_type=dlm_download', __( 'Download Monitor Extensions', 'download-monitor' ), '<span style="color:#419CCB;font-weight:bold;">' . __( 'Extensions', 'download-monitor' ) . '</span>', 'manage_options', 'dlm-extensions', array(
			$this,
			'extensions_page'
		) );
	}

	/**
	 * Output extensions page
	 */
	public function extensions_page() {
		$admin_extensions = new DLM_Admin_Extensions();
		$admin_extensions->output();
	}

	/**
	 * admin_dashboard function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_dashboard() {
		new DLM_Admin_Dashboard();
	}

	/**
	 * Change the admin footer text on Download Monitor admin pages
	 *
	 * @since  1.7
	 *
	 * @param  string $footer_text
	 *
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();

		$dlm_page_ids = array(
			'edit-dlm_download',
			'dlm_download',
			'edit-dlm_download_category',
			'edit-dlm_download_tag',
			'dlm_download_page_download-monitor-logs',
			'dlm_download_page_download-monitor-settings',
			'dlm_download_page_download-monitor-reports',
			'dlm_download_page_dlm-extensions'
		);

		// Check to make sure we're on a Download Monitor admin page
		if ( isset( $current_screen->id ) && apply_filters( 'dlm_display_admin_footer_text', in_array( $current_screen->id, $dlm_page_ids ) ) ) {
			// Change the footer text
			$footer_text = sprintf( __( 'If you like %sDownload Monitor%s please leave us a %s★★★★★%s rating. A huge thank you from us in advance!', 'download-monitor' ), '<strong>', '</strong>', '<a href="https://wordpress.org/support/view/plugin-reviews/download-monitor?filter=5#postform" target="_blank">', '</a>' );
		}

		return $footer_text;
	}

	/**
	 * Maybe flush rewrite rules
	 */
	public function maybe_flush_rewrites() {
		if ( true == $this->need_rewrite_flush ) {
			flush_rewrite_rules();
		}
	}
}