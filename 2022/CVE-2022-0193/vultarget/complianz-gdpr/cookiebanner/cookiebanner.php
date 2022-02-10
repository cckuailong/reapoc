<?php
/**
 * Output category consent checkboxes html
 */
function cmplz_get_dynamic_categories_ajax()
{
	$cookiebanner = new CMPLZ_COOKIEBANNER( intval($_GET['id']) );
	$checkbox_style = isset($_GET['checkbox_style']) ? sanitize_title($_GET['checkbox_style']) : 'classic';
	$data = $cookiebanner->get_consent_checkboxes($context = 'banner', $consenttype = sanitize_title($_GET['consenttype']), $force_template = $checkbox_style );

	$response = json_encode( $data );
	header( "Content-Type: application/json" );
	echo $response;
	exit;
}

add_action('wp_ajax_cmplz_get_dynamic_categories_ajax', 'cmplz_get_dynamic_categories_ajax');
/**
 * When A/B testing is enabled, we should increase all banner versions to flush the users cache
 */

function cmplz_update_banner_version_all_banners() {
	$banners = cmplz_get_cookiebanners();
	if ( $banners ) {
		foreach ( $banners as $banner_item ) {
			$banner = new CMPLZ_COOKIEBANNER( $banner_item->ID );
			$banner->banner_version ++;
			$banner->save();
		}
	}
}

function cmplz_check_minimum_one_banner() {
	if ( ! cmplz_user_can_manage() ) {
		return;
	}

	if ( !isset($_GET['page'])){
		return;
	}

	if (strpos($_GET['page'], 'cmplz')===false && strpos($_GET['page'], 'complianz')===false ) {
		return;
	}

	//make sure there's at least one banner
	$cookiebanners = cmplz_get_cookiebanners();
	$added_banner = false;
	if ( count( $cookiebanners ) < 1 ) {
		$banner = new CMPLZ_COOKIEBANNER();
		$banner->save();
		$added_banner = true;
	}

	//if we have one (active) banner, but it's not default, make it default
	if ($added_banner) $cookiebanners = cmplz_get_cookiebanners();
	if ( count( $cookiebanners ) == 1 && ! $cookiebanners[0]->default ) {
		$banner = new CMPLZ_COOKIEBANNER( $cookiebanners[0]->ID );
		$banner->enable_default();
	}
}
add_action( 'admin_init', 'cmplz_check_minimum_one_banner' );

add_action( 'admin_init', 'cmplz_redirect_to_cookiebanner' );
function cmplz_redirect_to_cookiebanner() {
	//on cookiebanner page?
	if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'cmplz-cookiebanner' ) {
		return;
	}
	if ( ! apply_filters( 'cmplz_show_cookiebanner_list_view', false )
	     && ! isset( $_GET['id'] )
	) {
		wp_redirect( add_query_arg( 'id', cmplz_get_default_banner_id(),
			admin_url( 'admin.php?page=cmplz-cookiebanner' ) ) );
	}
}

add_action( 'cmplz_cookiebanner_menu', 'cmplz_cookiebanner_admin_menu' );
function cmplz_cookiebanner_admin_menu() {
	if ( ! cmplz_user_can_manage() ) {
		return;
	}

	add_submenu_page(
		'complianz',
		__( 'Cookie banner', 'complianz-gdpr' ),
		__( 'Cookie banner', 'complianz-gdpr' ),
		'manage_options',
		'cmplz-cookiebanner',
		'cmplz_cookiebanner_overview'
	);
}

add_action( 'wp_ajax_cmplz_delete_cookiebanner', 'cmplz_delete_cookiebanner' );
function cmplz_delete_cookiebanner() {
	if ( ! cmplz_user_can_manage() ) {
		return;
	}

	if ( isset( $_POST['cookiebanner_id'] ) ) {
		$banner   = new CMPLZ_COOKIEBANNER( intval( $_POST['cookiebanner_id'] ) );
		$success  = $banner->delete();
		$response = json_encode( array(
			'success' => $success,
		) );
		header( "Content-Type: application/json" );
		echo $response;
		exit;
	}
}

/**
 * This function is hooked to the plugins_loaded, prio 10 hook, as otherwise there is some escaping we don't want.
 *
 * @todo fix the escaping
 */
add_action( 'plugins_loaded', 'cmplz_cookiebanner_form_submit', 20 );
function cmplz_cookiebanner_form_submit() {
	if ( ! cmplz_user_can_manage() ) {
		return;
	}

	if ( ! isset( $_GET['id'] ) && ! isset( $_POST['cmplz_add_new'] ) ) {
		return;
	}

	if ( ! isset( $_POST['cmplz_nonce'] ) ) {
		return;
	}

	if ( isset( $_POST['cmplz_add_new'] ) ) {
		$banner = new CMPLZ_COOKIEBANNER();
	} else {
		$id     = intval( $_GET['id'] );
		$banner = new CMPLZ_COOKIEBANNER( $id );
	}
	$banner->process_form( $_POST );

	if ( isset( $_POST['cmplz_add_new'] ) ) {
		wp_redirect( admin_url( 'admin.php?page=cmplz-cookiebanner&id=' . $banner->id ) );
		exit;
	}
}


function cmplz_cookiebanner_overview() {

	if ( ! cmplz_user_can_manage() ) {
		return;
	}

	/**
	 * Restart the statistics
	 * */
	if ( class_exists( 'cmplz_statistics' ) && ( isset( $_GET['action'] ) && $_GET['action'] == 'reset_statistics' )) {
		COMPLIANZ::$statistics->init_statistics();
	}

	$id = false;
	if ( isset( $_GET['id'] ) ) {
		$id = intval( $_GET['id'] );
	}

	if ( $id || ( isset( $_GET['action'] ) && $_GET['action'] == 'new' ) ) {
		include( dirname( __FILE__ ) . "/edit.php" );
	} else {

        ob_start();

		include( dirname( __FILE__ ) . '/class-cookiebanner-table.php' );

		$customers_table = new cmplz_CookieBanner_Table();
		$customers_table->prepare_items();

		?>
		<script>
			jQuery(document).ready(function ($) {
				$(document).on('click', '.cmplz-delete-banner', function (e) {

					e.preventDefault();
					var btn = $(this);
					btn.closest('tr').css('background-color', 'red');
					var delete_banner_id = btn.data('id');
					$.ajax({
						type: "POST",
						url: '<?php echo admin_url( 'admin-ajax.php' )?>',
						dataType: 'json',
						data: ({
							action: 'cmplz_delete_cookiebanner',
							cookiebanner_id: delete_banner_id
						}),
						success: function (response) {
							if (response.success) {
								btn.closest('tr').remove();
							}
						}
					});

				});
			});
		</script>

		<div class="wrap cookie-warning">
			<h1><?php _e( "Cookie banner settings", 'complianz-gdpr' ) ?>
				<?php do_action( 'cmplz_after_cookiebanner_title' ); ?>
			</h1>
			<?php
			if ( ! COMPLIANZ::$wizard->wizard_completed_once() ) {
				cmplz_notice( __( 'Please complete the wizard to check if you need a cookie banner.', 'complianz-gdpr' ), 'warning' );
			} else {
				if ( ! COMPLIANZ::$cookie_admin->site_needs_cookie_warning() ) {
					cmplz_notice( __( 'Your website does not require a cookie banner, so these settings do not apply.', 'complianz-gdpr' ) );
				} else {
					cmplz_notice( __( 'Your website requires a cookie banner, click edit below the cookie banner title to determine how the popup will look.', 'complianz-gdpr' ) );
				}
			}

			do_action( 'cmplz_before_cookiebanner_list' );
			?>

			<form id="cmplz-cookiebanner-filter" method="get" action="">
				<?php
				$customers_table->search_box( __( 'Filter', 'complianz-gdpr' ), 'cmplz-cookiebanner' );
				$customers_table->display();
				?>
				<input type="hidden" name="page" value="cmplz-cookiebanner"/>
			</form>
			<?php do_action( 'cmplz_after_cookiebanner_list' ); ?>
		</div>
		<?php

        $content = ob_get_clean();
        $args = array(
            'page' => 'cookie-banner',
            'content' => $content,
        );
        echo cmplz_get_template('admin_wrap.php', $args );
	}
}

/*
 *
 *
 * Here we add scripts and styles for the wysywig editor on the backend
 *
 * */
add_action( 'admin_enqueue_scripts',
	'cmplz_enqueue_cookiebanner_wysiwyg_assets' );
function cmplz_enqueue_cookiebanner_wysiwyg_assets( $hook ) {

	if ( ! cmplz_user_can_manage() ) {
		return;
	}

	if ( strpos( $hook, 'cmplz-cookiebanner' ) === false ) {
		return;
	}

	if ( ! isset( $_GET['id'] ) && ! ( isset( $_GET['action'] ) && $_GET['action'] == 'new' ) ) {
		return;
	}

	$minified = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_register_style( 'cmplz-cookie',
		cmplz_url . "assets/css/cookieconsent$minified.css", "",
		cmplz_version );
	wp_enqueue_style( 'cmplz-cookie' );

	$cookiebanner_id = isset( $_GET['id'] ) ? intval( $_GET['id'] )
		: cmplz_get_default_banner_id();
	if ( cmplz_get_value( 'use_custom_cookie_css' . $cookiebanner_id ) ) {
		$custom_css = cmplz_get_value( 'custom_css' . $cookiebanner_id );
		if ( ! empty( $custom_css ) ) {
			wp_add_inline_style( 'cmplz-cookie', $custom_css );
		}
	}

	$cookiesettings
		= COMPLIANZ::$cookie_admin->get_cookiebanner_settings( $cookiebanner_id );

	wp_enqueue_script( 'cmplz-cookie', cmplz_url . "assets/js/cookieconsent$minified.js",
		array( 'jquery', 'cmplz-admin' ), cmplz_version, true );
	wp_localize_script(
		'cmplz-cookie',
		'complianz',
		$cookiesettings
	);
	$deps =  array( 'jquery', 'cmplz-cookie' );

	wp_enqueue_script( 'cmplz-cookie-config-styling',
		cmplz_url . "assets/js/cookieconfig-styling$minified.js",
		$deps, cmplz_version, true );
		wp_localize_script(
				'cmplz-cookie-config-styling',
				'complianz_admin',
				array(
						'url' => add_query_arg('lang',  substr( get_locale(), 0, 2 ), admin_url( 'admin-ajax.php' ) ),
				)
	);
	do_action("cmplz_enqueue_cookiebanner_settings");
}

/**
 * Show meta box on edit pages
 * @param $post_type
 */

function cmplz_add_hide_cookiebanner_meta_box($post_type)
{
	if ( !is_post_type_viewable( $post_type )) return;
	if ( !cmplz_user_can_manage() ) return;

	add_meta_box('cmplz_edit_meta_box', __('Cookiebanner', 'complianz-gdpr'), 'cmplz_hide_cookiebanner_metabox', null, 'side', 'default', array());
}
add_action('add_meta_boxes', 'cmplz_add_hide_cookiebanner_meta_box');

/**
 * Render meta box
 */

function cmplz_hide_cookiebanner_metabox(){

	if ( !cmplz_user_can_manage() ) return;

	wp_nonce_field('cmplz_cookiebanner_hide_nonce', 'cmplz_cookiebanner_hide_nonce');
	global $post;

	if (!$post) return;

	//if there's no slug, don't offer this option
	if ( strlen($post->post_name)==0 ) return;

	$option_label = __("Disable Complianz on this page", "complianz-gdpr");
	$checked = get_post_meta($post->ID, 'cmplz_hide_cookiebanner', true) ? 'checked' : '';
	echo '<label><input type="checkbox" ' . $checked . ' name="cmplz_hide_cookiebanner" value="1" />' . $option_label . '</label>';
}

/**
 * Save the chosen selection to hide the cookiebanner on a page
 *
 * @param int $post_ID
 * @param WP_POST $post
 * @param bool $update
 */

function cmplz_save_hide_page_cookiebanner_option($post_ID, $post, $update)
{
	if ( !$update ) return;

	if ( !cmplz_user_can_manage() ) return;

	// check if this isn't an auto save
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	// security check
	if (!isset($_POST['cmplz_cookiebanner_hide_nonce']) || !wp_verify_nonce($_POST['cmplz_cookiebanner_hide_nonce'], 'cmplz_cookiebanner_hide_nonce'))
		return;

	$hide = isset($_POST['cmplz_hide_cookiebanner']) ? true : false;
	update_post_meta($post_ID, "cmplz_hide_cookiebanner", $hide);
	$excluded_posts_array = get_option('cmplz_excluded_posts_array', array());
	$slug = $post->post_name;

	if ($hide) {
		if ( !in_array( $slug, $excluded_posts_array) ) {
			$excluded_posts_array[] = $slug;
		}
	} else {
		$key = array_search( $slug, $excluded_posts_array );
		if ( $key !== FALSE ) unset($excluded_posts_array[$key]);
	}

	update_option( 'cmplz_excluded_posts_array', array_filter( $excluded_posts_array) );
}
add_action('save_post', 'cmplz_save_hide_page_cookiebanner_option', 10, 3 );
