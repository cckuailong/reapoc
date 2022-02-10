<?php
/**
 * WPRMenu Admin Framework
 *
 * @package     WP Responsive Menu
 * @author      MagniGenie
 * @copyright   Copyright (c) 2019, WP Responsive Menu
 * @link        https://magnigenie.com/
 * @since       WP Responsive Menu 3.1.4
 */

defined( 'ABSPATH' ) || exit;

class WPRMenu_Framework_Admin {

  /**
  * Page hook for the options screen
  *
  * @since 1.7.0
  * @type string
  */
  protected $options_screen = null;

  /**
  * Hook in the scripts and styles
  *
  * @since 1.7.0
  */
  public function init() {

    //Gets options to load
    $options = & WPRMenu_Framework::_wpr_optionsframework_options();

    //Checks if options are available
    if ( $options ) {

      // Add the options page and menu item.
      add_action( 'admin_menu', array( $this, 'add_wprmenu_options_page' ) );

      // Add the required scripts and styles
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

      // Settings need to be registered after admin_init
      add_action( 'admin_init', array( $this, 'settings_init' ) );

      // Adds options menu to the admin bar
      add_action( 'wp_before_admin_bar_render', array( $this, 'wpr_optionsframework_admin_bar' ) );

      add_action( 'admin_menu', array( $this, 'wpr_demo_page' ), 30 );

      add_action( 'admin_enqueue_scripts', array( $this, 'wprm_demo_import_styles' ) );

      add_action( 'admin_enqueue_scripts', array( $this, 'wprm_demo_import_scripts' ) );
    }

  }

  /**
  * Registers the settings
  *
  * @since 1.7.0
  */
  function settings_init() {
    
    //Load WPRMenu Framework Settings
    $wpr_optionsframework_settings = get_option( 'wpr_optionsframework' );

    // Registers the settings fields and callback
    register_setting( 'wpr_optionsframework', $wpr_optionsframework_settings['id'],  array ( $this, 'validate_options' ) );

    // Displays notice after options save
    add_action( 'wpr_optionsframework_after_validate', array( $this, 'save_options_notice' ) );

  }

  static function menu_settings() {

    $menu = array(

      // Modes: submenu, menu
      'mode' => 'menu',

      // Submenu default settings
      'page_title'  => __( 'Theme Options', 'wprmenu'),
      'menu_title'  => __('Theme Options', 'wprmenu'),
      'capability'  => 'edit_theme_options',
      'menu_slug'   => 'wprmenu-framework',
      'parent_slug' => 'themes.php',

      // Menu default settings
      'icon_url' => 'dashicons-menu',
      'position' => '61'

    );

    return apply_filters( 'wpr_optionsframework_menu', $menu );
  }

  /**
  * Add a subpage called "Theme Options" to the appearance menu.
  *
  * @since 1.7.0
  */
  function add_wprmenu_options_page() {

    $menu = $this->menu_settings();

      switch( $menu['mode'] ) {

        case 'menu':
        $this->options_screen = add_menu_page(
        $menu['page_title'],
        $menu['menu_title'],
        $menu['capability'],
        $menu['menu_slug'],
        array( $this, 'wprmenu_render_options_page' ),
        $menu['icon_url'],
        $menu['position']
        );
        break;

        default:
        // http://codex.wordpress.org/Function_Reference/add_submenu_page
        $this->options_screen = add_submenu_page(
        $menu['parent_slug'],
        $menu['page_title'],
        $menu['menu_title'],
        $menu['capability'],
        $menu['menu_slug'],
        array( $this, 'wprmenu_render_options_page' ) );
        break;
      }  
  }

  public function wprmenu_admin_url() {
    return untrailingslashit( plugins_url( '/inc/', WPRMENU_FILE ) );
  }

  /**
  * Loads the required stylesheets
  *
  * @since 1.7.0
  */

  function enqueue_admin_styles( $hook ) {

    if ( $this->options_screen != $hook ){
      return;
    }


    //Register styles.
    wp_register_style( 'wprmenu_settings_framework_styles', $this->wprmenu_admin_url() . '/assets/css/wprmenu-settings-framework.css', array(), WPRMENU_VERSION );
    wp_register_style( 'wprmenu_icons', $this->wprmenu_admin_url() . '/assets/icons/wpr-icons.css', array(),  WPRMENU_VERSION );
    wp_register_style( 'wprmenu_iconpicker', $this->wprmenu_admin_url() . '/assets/css/jquery.fonticonpicker.min.css', array(),  WPRMENU_VERSION );
    wp_register_style( 'wprmenu_bootflat', $this->wprmenu_admin_url() . '/assets/css/site.min.css', array(),  WPRMENU_VERSION );
    
    //Enqueue styles.
    wp_enqueue_style( 'wprmenu_settings_framework_styles' );
    wp_enqueue_style( 'wprmenu_icons' );
    wp_enqueue_style( 'wprmenu_iconpicker' );
    wp_enqueue_style( 'wprmenu_bootflat' );
    wp_enqueue_style( 'wp-color-picker' );
  }

  public function wprm_demo_import_styles( $hook ) {
    $screen = get_current_screen();

    $current_page = 'wpr-menu_page_wprmenu-demo-import';

    wp_register_style( 'Sweetalert2-css', $this->wprmenu_admin_url() . '/assets/css/sweetalert2.min.css', array(),  WPRMENU_VERSION );
    wp_register_style( 'wpr_import_demo', $this->wprmenu_admin_url() . '/assets/css/wpr_import_demo.css', array(),  WPRMENU_VERSION );

    if( $screen->id !== $current_page )
      return;

    // Enqueue SweetAlert2 Style
    wp_enqueue_style( 'Sweetalert2-css' );
    wp_enqueue_style( 'wpr_import_demo' );
  }

  function wprm_demo_import_scripts( $hook ) {
    $screen = get_current_screen();

    $current_page = 'wpr-menu_page_wprmenu-demo-import';

    if( $screen->id !== $current_page ){
      return;
    }

    $options = get_option( 'wprmenu_options' );

    //Exit Intent
    wp_register_script( 'wpr-exit-intent', $this->wprmenu_admin_url() . '/assets/js/wpr-exit-intent.js', array( 'jquery' ), WPRMENU_VERSION );

    // Enqueue SweetAlert2 JS
    wp_register_script( 'Sweetalert2-js', $this->wprmenu_admin_url() . '/assets/js/sweetalert2.all.min.js', array( 'jquery'), WPRMENU_VERSION );

    wp_register_script( 'wprmenu-import-demo', $this->wprmenu_admin_url() . '/assets/js/wprmenu-import-demo.js', array('jquery', 'wpr-exit-intent', 'Sweetalert2-js'),  WPRMENU_VERSION );

    $enable_preview = !empty( $options ) ? $options['wpr_live_preview'] : 0;

    $params = array(
      'ajax_url'            =>  admin_url( 'admin-ajax.php' ),
      'please_wait'         => __('Please Wait !', 'wprmenu'),
      'import_done'         => __('Import Done', 'wprmenu'),
      'please_reload'       => __('Please reload the page by doing click the button below', 'wprmenu'),
      'navigating_away'     => __('Seems like navigating away', 'wprmenu'),
      'confirm_message'     => __('Are you sure to navigate away? Please save all the changes otherwise the recent changes will be reverted back', 'wprmenu'),
      'options_saved'       => __( 'Options Saved!', 'wprmenu'),
      'options_not_saved'   => __( 'Options Not Saved!', 'wprmenu'),
      'options_saved_msg'   => __( 'The options has been saved. Please reload this page by doing click on the button below.', 'wprmenu'),
      'import_error_title'  => __('Oops...', 'wprmenu'),
      'pro_version_error'   => __('Please upgrade to PRO Version to import this demo.', 'wprmenu'),
      'preview_url'         => home_url(),
      'enable_preview'      => $enable_preview,
      'import_error'        => __('Something went wrong', 'wprmenu'),
    );

    wp_localize_script( 'wprmenu-import-demo' , 'wprmenu_params', $params );

    wp_enqueue_script( 'wpr-exit-intent' );
    wp_enqueue_script( 'Sweetalert2-js' );
    wp_enqueue_script( 'wprmenu-import-demo' );
  }

  /**
  * Loads the required javascript
  *
  * @since 1.7.0
  */
  function enqueue_admin_scripts( $hook ) {

    if ( $this->options_screen != $hook ){
      return;
    }
          
    // Register scripts.
    wp_register_script( 'wpr-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery'),  WPRMENU_VERSION );
    wp_register_script( 'wpr-ace', WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY . 'assets/js/ace-min-noconflict/ace.js', array( 'jquery' ), WPRMENU_VERSION );
    wp_register_script( 'wpr-ace-theme-chrome', WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY . 'assets/js/ace-min-noconflict/theme-chrome.js', array( 'jquery' ), WPRMENU_VERSION );
    wp_register_script( 'wpr-ace-mode-css', WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY . 'assets/js/ace-min-noconflict/mode-css.js', array( 'jquery' ), WPRMENU_VERSION );
    wp_register_script( 'icon-picker', WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY . 'assets/js/jquery.fonticonpicker.min.js', array( 'jquery' ), WPRMENU_VERSION );
    wp_enqueue_style( 'wpr_select2_style', $this->wprmenu_admin_url() . '/assets/css/select2.min.css', WPRMENU_VERSION );

    wp_register_script( 'Select2-js', $this->wprmenu_admin_url() . '/assets/js/select2.full.js', array( 'jquery'), WPRMENU_VERSION );
    wp_register_script( 'wpr-exit-intent', WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY . 'assets/js/wpr-exit-intent.js', array( 'jquery' ), WPRMENU_VERSION );
    wp_enqueue_style( 'Sweetalert2-css', WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY . 'assets/css/sweetalert2.min.css', WPRMENU_VERSION );
    wp_register_script( 'Sweetalert2-js', WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY . 'assets/js/sweetalert2.all.min.js', array( 'jquery'), WPRMENU_VERSION );
    wp_register_script( 'wprmenu-options', WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY . 'assets/js/wprmenu-options.js', array( 'jquery','wp-color-picker', 'Select2-js', 'wpr-ace', 'wpr-exit-intent', 'Sweetalert2-js' ), WPRMENU_VERSION );

    $params = array( 
      'options_path'        => WPRMENU_OPTIONS_FRAMEWORK_DIRECTORY,
      'ajax_url'            =>  admin_url( 'admin-ajax.php' ),
      'view_demo'           => __('View Demo', 'wprmenu'),
      'preview_done'        => __('Preview Done', 'wprmenu'),
      'loading_preview'     => __('Loading Preview', 'wprmenu'),
      'import_demo'         => __('Import Demo', 'wprmenu'),
      'import_error'        => __('Something went wrong', 'wprmenu'),
      'please_wait'         => __('Please Wait !', 'wprmenu'),
      'please_reload'       => __('Please reload the page by doing click the button below', 'wprmenu'),
      'reload'              => __('Reload', 'wprmenu'),
      'import_error_title'  => __('Oops...', 'wprmenu'),
      'import_error'        => __('Something went wrong', 'wprmenu'),
      'import_done'         => __('Import Done', 'wprmenu'),
      'update_license_key'  => __('Please Update Your License Key To Import Demo', 'wprmenu'),
      'pro_message'         => __('Import requires PRO version', 'wprmenu'),
      'site_url'            => get_site_url(),
      'please_reload'       => __('Please reload the page by doing click the button below', 'wprmenu'),
      'reload'              => __('Reload', 'wprmenu'),
      'navigating_away'     => __('Seems like navigating away', 'wprmenu'),
      'confirm_message'     => __('Are you sure to navigate away? Please save all the changes otherwise the recent changes will be reverted back', 'wprmenu'),
      'pro_version_text'    => __('Pro Version', 'wprmenu'),
      'pro_version_upgrade_error' => __('This demo requires pro version to be activated', 'wprmenu'),
      'upgrade_to_pro'      => __('Upgrade to PRO to use this option.', 'wprmenu'),
      'ugrade_pro_link'     => WPRMENU_PRO_LINK,
      'preview_url'         => get_home_url(),
      'social_link_remove_confirmation' => __( 'Do you really want to remove this social link?', 'wprmenu' ),
    );

    wp_localize_script( 'wprmenu-options', 'wprmenu_params' , $params );

    wp_enqueue_script( 'wpr-bootstrap' );
    wp_enqueue_script( 'wpr-ace' );
    wp_enqueue_script( 'wpr-ace-theme-chrome' );
    wp_enqueue_script( 'wpr-ace-mode-css' );
    wp_enqueue_script( 'icon-picker' );
    wp_enqueue_script( 'Select2-js' );
    wp_enqueue_script( 'wpr-exit-intent' );
    wp_enqueue_script( 'Sweetalert2-js' );
    wp_enqueue_script( 'wprmenu-options' );

    // Inline scripts from options-interface.php
    add_action( 'admin_head', array( $this, 'wpr_of_admin_head' ) );
  }

  function wpr_of_admin_head() {
    // Hook to add custom scripts
    do_action( 'wpr_optionsframework_custom_scripts' );
  }

  /**
     * Builds out the options panel.
     *
   * If we were using the Settings API as it was intended we would use
   * do_settings_sections here.  But as we don't want the settings wrapped in a table,
   * we'll call our own custom wpr_optionsframework_fields.  See options-interface.php
   * for specifics on how each individual field is generated.
   *
   * Nonces are provided using the settings_fields()
   *
     * @since 1.7.0
     */
  function wprmenu_render_options_page() { ?>

    <div id="wpr_optionsframework-wrap" class="wrap">
      <?php $menu = $this->menu_settings(); ?>
      <h2><?php echo esc_html( $menu['page_title'] ); ?></h2>

      <div class="clear"></div>

      <div class="wpr-options-settings-wrapper">
        <?php settings_errors( 'wprmenu-framework' ); ?>

        <!--Navigation Tabs Starts Here -->
        <div class="mg-navtabs-wrapper">
          <?php echo WPRMenu_Framework_Interface::wpr_optionsframework_tabs(); ?>
        </div>
        <!-- Navigation Tabs Ends Here -->

        <!-- Settings Panel Starts Here -->
        <div class="mg-settings-panel-right">
          <div id="wpr_optionsframework-metabox" class="metabox-holder">
            
            <div id="wpr_optionsframework" class="postbox">
              <form id="wpr_form_settings" action="options.php" method="post">
                <?php settings_fields( 'wpr_optionsframework' ); ?>
                <?php WPRMenu_Framework_Interface::wpr_optionsframework_fields(); /* Settings */ ?>
                <input type="submit" class="reset-button wpr-reset-button button-secondary" style="display: none;" name="reset" value="<?php esc_attr_e( 'Restore Defaults', 'wpr' ); ?>" />
              </form>
            </div> <!-- / #container -->

            <div class="mg-options-submit-wrap">
            
              <!-- Submit Settings -->
              <div id="wpr_optionsframework-submit">
                <?php WPRMenu_Framework_Interface::wpr_render_form_button(); /* Submit/Reset Buttons */ ?>
              
                <?php WPRMenu_Framework_Interface::wpr_render_floating_buttons(); /* Floating Buttons */ ?>
              </div> <!-- / Submit Settings -->

            </div>

          </div>

        </div>

      </div> <!--  / .wpr-options-settings-wrapper-->
      <?php do_action( 'wpr_optionsframework_after' ); ?>

    </div> <!-- / .wrap -->
    <div class="clear"></div> 

    <?php 
  }

  /**
   * Validate Options.
   *
   * This runs after the submit/reset button has been clicked and
   * validates the inputs.
   *
   * @uses $_POST['reset'] to restore default options
   */
  function validate_options( $input ) {

    /*
     * Restore Defaults.
     *
     * In the event that the user clicked the "Restore Defaults"
     * button, the options defined in the theme's options.php
     * file will be added to the option for the active theme.
     */

    if ( isset( $_POST['reset'] ) ) {
      add_settings_error( 'wprmenu-framework', 'restore_defaults', __( 'Default options restored.', 'wprmenu' ), 'updated fade' );
      return $this->get_default_values();
    }

    /*
     * Update Settings
     *
     * This used to check for $_POST['update'], but has been updated
     * to be compatible with the theme customizer introduced in WordPress 3.4
     */

    $clean = array();
    $options = & WPRMenu_Framework::_wpr_optionsframework_options();
    foreach ( $options as $option ) {

      if ( ! isset( $option['id'] ) ) {
        continue;
      }

      if ( ! isset( $option['type'] ) ) {
        continue;
      }

      $id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

      // Set checkbox to false if it wasn't sent in the $_POST
      if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
        $input[$id] = false;
      }

      // Set each item in the multicheck to false if it wasn't sent in the $_POST
      if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
        foreach ( $option['options'] as $key => $value ) {
          $input[$id][$key] = false;
        }
      }

      // For a value to be submitted to database it must pass through a sanitization filter
      if ( has_filter( 'wpr_of_sanitize_' . $option['type'] ) && !empty( $input[$id] ) ) {
        $clean[$id] = apply_filters( 'wpr_of_sanitize_' . $option['type'], $input[$id], $option );
      }
    }

    // Hook to run after validation
    do_action( 'wpr_optionsframework_after_validate', $clean );
    
    if (isset($_COOKIE['wprmenu_live_preview']) && $_COOKIE['wprmenu_live_preview'] == 'yes' ) {
      unset($_COOKIE['wprmenu_live_preview']);
      setcookie('wprmenu_live_preview', null, -1, '/');
    } 


    return $clean;
  }

  /**
   * Display message when options have been saved
   */

  function save_options_notice() {
    add_settings_error( 'wprmenu-framework', 'save_options', __( 'WP Responsive Menu Options Saved.', 'wprmenu' ), 'updated fade in' );
  }

  /**
   * Get the default values for all the theme options
   *
   * Get an array of all default values as set in
   * options.php. The 'id','std' and 'type' keys need
   * to be defined in the configuration array. In the
   * event that these keys are not present the option
   * will not be included in this function's output.
   *
   * @return array Re-keyed options configuration array.
   *
   */

  function get_default_values() {
    $output = array();
    $config = & WPRMenu_Framework::_wpr_optionsframework_options();
    foreach ( (array) $config as $option ) {
      if ( ! isset( $option['id'] ) ) {
        continue;
      }
      if ( ! isset( $option['std'] ) ) {
        continue;
      }
      if ( ! isset( $option['type'] ) ) {
        continue;
      }
      if ( has_filter( 'wpr_of_sanitize_' . $option['type'] ) ) {
        $output[$option['id']] = apply_filters( 'wpr_of_sanitize_' . $option['type'], $option['std'], $option );
      }
    }
    return $output;
  }

  /**
   * Add options menu item to admin bar
   */

  function wpr_optionsframework_admin_bar() {

    $menu = $this->menu_settings();

    global $wp_admin_bar;

    if ( 'menu' == $menu['mode'] ) {
      $href = admin_url( 'admin.php?page=' . $menu['menu_slug'] );
    } else {
      $href = admin_url( 'themes.php?page=' . $menu['menu_slug'] );
    }

    $args = array(
      'parent' => 'appearance',
      'id' => 'wpr_of_theme_options',
      'title' => $menu['menu_title'],
      'href' => $href
    );

    $wp_admin_bar->add_menu( apply_filters( 'wpr_optionsframework_admin_bar', $args ) );
  }

  public function wpr_demo_page() {
      add_submenu_page(
        'wp-responsive-menu',
        'Import Demo',
        'Import Demo',
        'manage_options',
        'wprmenu-demo-import',
        array( $this, 'wpr_import_demo_settings_page' )
      );
    }

  public function wpr_import_demo_settings_page() {
    ?>
    <div class="wrap wprmenu-import-wrap">
    <h2><?php esc_attr_e('Import Demo', 'wprmenu'); ?></h2>

    <div class="mg-reset mg-wrap">
      <div class="mg-content">
        <div class="mg-main">
          <div class="mg-row">
            <div class="mg-column mg-full-width">
              <div class="mg-box" style="background-color: transparent; box-shadow: none;">

                <!-- Demo Import Heading Here-->
                <div class="mg-box-content" style="margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);">
                  <h3><span><?php _e( 'Easy Setup With Our Predefined Demos
', 'wprmenu' ); ?>  :)</span></h3>
                </div>
                <!-- Demo Import Heading Here -->
                
                <div class="mg-box-content">
                  <?php echo WPRMenu_Framework_Interface::wprmenu_get_demodata('Free'); ?>
                </div>

                <div class="mg-box-content">
                  <?php echo WPRMenu_Framework_Interface::wprmenu_get_demodata('Pro'); ?>
                </div>

              </div>
            </div>
            <div class="clear"></div>
          </div>
        </div>
      </div>
    </div>

  </div>
    <?php
  }

}
