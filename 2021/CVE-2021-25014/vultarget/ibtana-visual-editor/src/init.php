<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( !defined('ABSPATH') ) {
  exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */

/** this code help to activate gutunberg on woocomerce product page **/
/** On off settings for gutunberg page editor **/
/**
 *
 */


class Ibtana_Visual_Editor_Init_Class {

  /* Constructor method for the class. */
	function __construct() {
    $this->ibtana_visual_editor_init_enqueues();
    do_action( 'ibtana-visual-editor/loaded' );
  }

  function ibtana_visual_editor_init_enqueues() {
    add_action( 'enqueue_block_editor_assets', array( $this, 'ibtana_visual_editor_cgb_editor_assets' ) );
    add_action( 'wp_enqueue_scripts', array( $this,'register_frontend_script' ) );
    add_filter( 'block_categories_all', function( $categories, $post ) {
      return array_merge(
        array(
          array(
            'slug'  =>  'Ibtana Blocks',
            'title' =>  'Ibtana Blocks',
            'icon'  =>  '<svg className="components-panel__icon" width="24" height="24" viewBox="0 0 20 20" aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg">
                          <rect fill="#ffffff" x="0" y="0" width="20" height="20"/>
                          <rect fill="#1163EB" x="2" y="2" width="16" height="16" rx="16"/>
                        </svg>'
          ),
        ),
        $categories
      );
    }, 99999, 2 );

  }
  /**
   * Enqueue Gutenberg block assets for backend editor.
   *
   * @uses {wp-blocks} for block type registration & related functions.
   * @uses {wp-element} for WP Element abstraction — structure of blocks.
   * @uses {wp-i18n} to internationalize the block's text.
   * @uses {wp-editor} for WP editor styles.
   * @since 1.0.0
   */
  function ibtana_visual_editor_cgb_editor_assets() {
    wp_enqueue_script(
      'owl-js',
      plugins_url('/dist/owl.carousel.js', dirname(__FILE__)),
      array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
      false
    );
    wp_enqueue_script(
      'ibtana-visual-editor-admin-script',
      plugins_url('/dist/adminScript.js', dirname(__FILE__)),
      array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
      1.0,
      true
    );
    wp_enqueue_style(
      'owl-css',
      plugins_url( 'dist/assets/owl.carousel.css', dirname(__FILE__) ),
      array( 'wp-edit-blocks' )
    );

    wp_enqueue_style(
      'ibtana-bootstrap-backend',
      plugins_url('dist/css/bootstrap/css/bootstrap.min.css', dirname(__FILE__)),
      array( 'wp-edit-blocks' )
    );

    wp_enqueue_style(
      'ibtana-fontawesome-backend',
      'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/fontawesome.min.css',
      array( 'wp-edit-blocks' )
    );

    wp_enqueue_style(
      'ibtana-fontawesome-solid-css-backend',
      'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/solid.css',
      array( 'wp-edit-blocks' )
    );

    wp_enqueue_style(
      'ibtana-fontawesome-brans-css-backend',
      'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/brands.min.css',
      array( 'wp-edit-blocks' )
    );

    wp_enqueue_style(
      'ibtana-free-templates-editor-style',
      'https://vwthemesdemo.com/ibtana_json/free_theme/css/ive-editor-style.css',
      array( 'wp-edit-blocks' )
    );

    wp_enqueue_style(
      'ibtana-premium-templates-editor-style',
      'https://vwthemesdemo.com/ibtana_json/premium_theme/css/ive-editor-style.css',
      array( 'wp-edit-blocks' )
    );

    wp_enqueue_style(
      'ibtana-woocommerce-templates-editor-style',
      'https://vwthemesdemo.com/ibtana_json/woocommerce_templates/css/iepa-editor.css',
      array( 'wp-edit-blocks' )
    );

    $is_iepa_active = false;
    $ibtana_ecommerce_product_addons_license_key	=	get_option( 'ibtana_ecommerce_product_addons_license_key' );
    if ( $ibtana_ecommerce_product_addons_license_key ) {
      if ( isset( $ibtana_ecommerce_product_addons_license_key['license_key'] ) ) {
        if ( ( $ibtana_ecommerce_product_addons_license_key['license_key'] != '' ) ) {
          $is_iepa_active		=	true;
        }
      }
    }
    if ( $is_iepa_active ) {
      wp_enqueue_style(
        'ibtana-woocommerce-premium-templates-editor-style',
        'https://vwthemesdemo.com/ibtana_json/woocommerce_templates/css/premium/iepa-editor.css',
        array( 'wp-edit-blocks' )
      );
    }

    wp_enqueue_script( 'updates' );
    wp_register_script(
      'ibtana-visual-editor-modal-js',
      plugins_url( 'dist/modal.js', dirname(__FILE__) ),
      array( 'jquery' ),
      IVE_VER,
      true
    );

    $theme_text_domain = wp_get_theme()->get( 'TextDomain' );
		if ( is_child_theme() ) {
			$theme_text_domain = wp_get_theme()->get( 'Template' );
		}

    $ive_active_vw_theme_text_domain = get_option( 'ive_active_vw_theme_text_domain' ) ? get_option( 'ive_active_vw_theme_text_domain' ) : 'sirat';

    wp_localize_script(
      'ibtana-visual-editor-modal-js',
			'ibtana_visual_editor_modal_js',
			array(
				'active_theme_text_domain'            =>  $theme_text_domain,
				'page_id'                             =>  get_the_ID(),
				'site_url'                            =>  site_url(),
				'rest_url'                            =>  get_rest_url(),
				'themedomain'                         =>  get_template(),
				'adminUrl'                            =>  admin_url(),
        'adminAjax'                           =>  admin_url( 'admin-ajax.php' ),
				'admin_user_ibtana_license_key'       =>  get_option('vw_pro_theme_key'),
        'googleReCaptchaAPISiteKey'           =>  get_option('ive_googleReCaptchaAPISiteKey'),
        'googleReCaptchaAPISecretKey'         =>  get_option('ive_googleReCaptchaAPISecretKey'),
				'get_template_directory_uri_image'    =>  get_template_directory_uri() . "/screenshot.png",
				'path'                                =>  get_site_url(),
				'current_theme_name'                  =>  wp_get_theme()->get( 'Name' ),
        'IBTANA_LICENSE_API_ENDPOINT'         =>  IBTANA_LICENSE_API_ENDPOINT,
        'IBTANA_THEME_URL'                    =>  IBTANA_THEME_URL,
        'plugin_url'                          =>  plugins_url('ibtana-visual-editor'),
        'placeholder_image'                   =>  plugins_url('ibtana-visual-editor') . '/dist/images/placeholder.png',
        'custom_text_domain'                  =>  defined( 'CUSTOM_TEXT_DOMAIN' ) ? CUSTOM_TEXT_DOMAIN : '',
        'ive_add_on_keys'                     =>  apply_filters( 'ive_add_on_license_info', [] ),
        'wpnonce' 										        =>  wp_create_nonce( 'ive_whizzie_nonce' ),
        'is_woocommerce_available'            =>  class_exists( 'woocommerce' ) ? true : false,
        'post_type'                           =>  get_post_type(),
        'save_templates_limit_info'           =>  IVE_Ibtana_CPT::get_template_limit_info(),
        'ive_general_settings'                =>  get_option( 'ive_general_settings' ),
        'ive_active_vw_theme_text_domain'     =>  $ive_active_vw_theme_text_domain,
			)
		);
    wp_enqueue_script( 'ibtana-visual-editor-modal-js' );

    wp_enqueue_style(
      'ibtana-ive-modal-editor-css',
      plugins_url( 'dist/css/ibtana-modal-view.css', dirname(__FILE__) ),
      array( 'wp-edit-blocks' )
    );

    // Styles.
    wp_enqueue_style(
      'ibtana-visual-editor-cgb-block-editor-css', // Handle.
      plugins_url('dist/blocks.editor.build.css', dirname(__FILE__)), // Block editor CSS.
      array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
    );
    wp_enqueue_style(
      'ibtana-visual-editor-cgb-style-editor-css', // Handle.
      plugins_url('public/style.css', dirname(__FILE__)), // Block editor CSS.
      array( 'wp-editor' ) // Dependency to include the CSS after it.
    );
    wp_enqueue_script(
      'ibtana-visual-editor-cgb-block-js', // Handle.
      plugins_url('/dist/blocks.build.js', dirname(__FILE__)), // Block.build.js: We register the block here. Built with Webpack.
      array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-plugins', 'wp-edit-post',  ), // Dependencies, defined above.
      true // Enqueue the script in the footer.
    );

    $ibtana_visual_editor_editor_widths = get_option('ibtana_visual_editor_editor_width', array());
    $ibtana_visual_editor_sidebar_size = 750;
    $ibtana_visual_editor_nosidebar_size = 1140;
    $ibtana_visual_editor_jssize = 2000;
    if (! isset($ibtana_visual_editor_editor_widths['enable_editor_width']) || 'true' === $ibtana_visual_editor_editor_widths['enable_editor_width']) {
        if (isset($ibtana_visual_editor_editor_widths['limited_margins']) && 'true' === $ibtana_visual_editor_editor_widths['limited_margins']) {
            $ibtana_visual_editor_add_size = 10;
        } else {
            $ibtana_visual_editor_add_size = 30;
        }
        $ibtana_visual_editor_post_type = get_post_type();
        if (isset($ibtana_visual_editor_editor_widths['page_default']) && ! empty($ibtana_visual_editor_editor_widths['page_default']) && isset($ibtana_visual_editor_editor_widths['post_default']) && ! empty($ibtana_visual_editor_editor_widths['post_default'])) {
            if (isset($post_type) && 'page' === $post_type) {
                $ibtana_visual_editor_defualt_size_type = $ibtana_visual_editor_editor_widths['page_default'];
            } else {
                $ibtana_visual_editor_defualt_size_type = $ibtana_visual_editor_editor_widths['post_default'];
            }
        } else {
            $ibtana_visual_editor_defualt_size_type = 'sidebar';
        }
        if (isset($ibtana_visual_editor_editor_widths['sidebar']) && ! empty($ibtana_visual_editor_editor_widths['sidebar'])) {
            $ibtana_visual_editor_sidebar_size = $ibtana_visual_editor_editor_widths['sidebar'] + $ibtana_visual_editor_add_size;
        } else {
            $ibtana_visual_editor_sidebar_size = 750;
        }
        if (isset($ibtana_visual_editor_editor_widths['nosidebar']) && ! empty($ibtana_visual_editor_editor_widths['nosidebar'])) {
            $ibtana_visual_editor_nosidebar_size = $ibtana_visual_editor_editor_widths['nosidebar'] + $ibtana_visual_editor_add_size;
        } else {
            $ibtana_visual_editor_nosidebar_size = 1140 + $ibtana_visual_editor_add_size;
        }
        if ('sidebar' == $ibtana_visual_editor_defualt_size_type) {
            $ibtana_visual_editor_default_size = $ibtana_visual_editor_sidebar_size;
        } elseif ('fullwidth' == $ibtana_visual_editor_defualt_size_type) {
            $ibtana_visual_editor_default_size = 'none';
        } else {
            $ibtana_visual_editor_default_size = $ibtana_visual_editor_nosidebar_size;
        }
        if ('none' === $ibtana_visual_editor_default_size) {
            $ibtana_visual_editor_jssize = 2000;
        } else {
            $ibtana_visual_editor_jssize = $ibtana_visual_editor_default_size;
        }
    }
    wp_localize_script(
      'ibtana-visual-editor-cgb-block-js',
      'ive_blocks_params',
      array(
        'sidebar_size'          =>  $ibtana_visual_editor_sidebar_size,
        'nosidebar_size'        =>  $ibtana_visual_editor_nosidebar_size,
        'default_size'          =>  $ibtana_visual_editor_jssize,
        'config'                =>  get_option( 'config_blocks' ),
        'settings'              =>  get_option( 'settings_blocks' ),
        'ive_general_settings'  =>  get_option( 'ive_general_settings' ),
        'ive_add_on_keys'       =>  apply_filters( 'ive_add_on_license_info', [] ),
        'google_map_pro'        =>  IVE_URL
      )
    );

    $ibtana_visual_editor_editor_widths = get_option('ibtana_visual_editor_editor_width', array());
    if (isset($ibtana_visual_editor_editor_widths['limited_margins']) && 'true' === $ibtana_visual_editor_editor_widths['limited_margins']) {
      wp_enqueue_style(
        'ibtana-visual-editor-limited-margins-css',
        plugins_url() . 'dist/limited-margins.css',
        array( 'wp-edit-blocks' ),
        1.0
      );
    }
    wp_enqueue_style(
      'ibtana-custom-css-backend',
      plugins_url('dist/css/ive-custom-css.css', dirname(__FILE__)),
      array( 'wp-edit-blocks' )
    );
  }


  /**
   * Register frontend css
   */
  function register_frontend_script() {
    $post = get_post();
    if ( !is_object( $post ) ) {
      return false;
    }

    $is_ibtana_block_exists = false;

    if ( has_blocks( $post->post_content ) ) {
      $blocks = parse_blocks( $post->post_content );
      foreach ( $blocks as $key => $block_single ) {
        $block_single_blockName  = $block_single['blockName'];
        $block_single_blockName_arr = explode( '/', $block_single_blockName );
        if ( $block_single_blockName_arr[0] === 'ive' || $block_single_blockName_arr[0] === 'iepa' ) {
          $is_ibtana_block_exists = true;
          break;
        }
      }
    }

    if ( !$is_ibtana_block_exists ) {
      return;
    }

    if (function_exists( 'is_checkout' )) {
      if (is_checkout()) {
        return;
      }
    }

    wp_enqueue_style(
        'ibtana-visual-editor-cgb-style-css',
        plugins_url('dist/blocks.style.build.css', dirname(__FILE__)),
        array( 'wp-editor' )
    );

    wp_localize_script(
      'ibtana-visual-editor-frontend-form-js',
			'ive_form_captcha',
			array(
        'googleReCaptchaAPISiteKey' => get_option('ive_googleReCaptchaAPISiteKey')
      )
    );

    wp_enqueue_script(
        'ibtana-visual-editor-parsley-js',
        plugins_url('/dist/js/parsley.js', dirname(__FILE__)),
        array( 'jquery' ),
        true
    );

    wp_enqueue_script(
        'ibtana-visual-editor-frontend-form-js',
        plugins_url('/src/blocks/form/frontend.js', dirname(__FILE__)),
        array( 'jquery' ),
        true
    );

    if ( apply_filters( 'gkt_enqueue_google_recaptcha', true ) ) {
      $recaptcha_site_key   = get_option( 'ive_googleReCaptchaAPISiteKey' );
      $recaptcha_secret_key = get_option( 'ive_googleReCaptchaAPISecretKey' );

      if ( $recaptcha_site_key && $recaptcha_secret_key ) {
        wp_register_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . esc_attr( $recaptcha_site_key ), array(), '3.0.0', true );
      }
    }
    wp_enqueue_script('google-recaptcha');

    wp_enqueue_style('ibtana-visual-editor-owl-css',plugins_url('dist/assets/owl.carousel.css', dirname(__FILE__)));
    wp_enqueue_script('ibtana-visual-editor-owl', plugins_url('/dist/owl.carousel.js', dirname(__FILE__)), array( 'jquery'), '1.0', true);
    wp_enqueue_script('ibtana-visual-editor-scripts', plugins_url('/dist/scripts.js', dirname(__FILE__)), array( 'jquery'), '1.0', true);

    $style = 'bootstrap';
    if( ( ! wp_style_is( $style, 'queue' ) ) && ( ! wp_style_is( $style, 'done' ) ) ) {
      wp_enqueue_style(
          'ibtana-bootstrap-frontend',
          plugins_url('dist/css/bootstrap/css/bootstrap.min.css', dirname(__FILE__))
      );
    }



    wp_enqueue_style('ibtana-fontawesome-frontend','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/fontawesome.min.css');

    wp_enqueue_style('ibtana-fontawesome-solid-css-frontend','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/solid.css');

    wp_enqueue_style('ibtana-fontawesome-brand-css-frontend','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/brands.min.css');

    wp_enqueue_script(
      'ibtana-gallery',
      plugins_url('dist/lightgallery-all.min.js', dirname(__FILE__))
    );

    wp_enqueue_style(
      'ibtana-gallery-frontend',
      plugins_url('dist/css/lightgallery.css',
      dirname(__FILE__))
    );

    wp_enqueue_style(
      'ibtana-animate-frontend',
      plugins_url('dist/css/animate.min.css',
      dirname(__FILE__))
    );

    wp_enqueue_style(
        'ibtana-custom-css-frontend',
        plugins_url('dist/css/ive-custom-css.css', dirname(__FILE__))
    );

    wp_enqueue_script( 'ive-block-map-frontend',
      plugins_url('dist/js/map.js', dirname(__FILE__)),
      array( 'jquery' ),
      IVE_VER,
      true
    );
  }

}
new Ibtana_Visual_Editor_Init_Class;
