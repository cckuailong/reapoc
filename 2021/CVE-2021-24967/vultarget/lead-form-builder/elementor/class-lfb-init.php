<?php 
if ( ! defined( 'ABSPATH' )) {
   exit; // Exit if accessed directly.
}

/**
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class LFB_Addon_Init {

   /**
    * Plugin Version
    *
    * @since 1.0.0
    *
    * @var string The plugin version.
    */
   const VERSION = '1.0.0';

   /**
    * Minimum Elementor Version
    *
    * @since 1.0.0
    *
    * @var string Minimum Elementor version required to run the plugin.
    */
   const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

   /**
    * Minimum PHP Version
    *
    * @since 1.0.0
    *
    * @var string Minimum PHP version required to run the plugin.
    */
   const MINIMUM_PHP_VERSION = '5.4';

   /**
    * Instance
    *
    * @since 1.0.0
    *
    * @access private
    * @static
    *
    * @var LFB_Addon_Init The single instance of the class.
    */
   private static $_instance = null;

   /**
    * Instance
    *
    * Ensures only one instance of the class is loaded or can be loaded.
    *
    * @since 1.0.0
    *
    * @access public
    * @static
    *
    * @return LFB_Addon_Init An instance of the class.
    */
   public static function instance() {

      if ( is_null( self::$_instance ) ) {
         self::$_instance = new self();
      }
      return self::$_instance;

   }

   /**
    * Constructor
    *
    * @since 1.0.0
    *
    * @access public
    */
   public function __construct() {

      add_action( 'init', [ $this, 'i18n' ] );
      add_action( 'plugins_loaded', [ $this, 'init' ] );
   }

   /**
    * Load Textdomain
    *
    * Load plugin localization files.
    *
    * Fired by `init` action hook.
    *
    * @since 1.0.0
    *
    * @access public
    */
   public function i18n() {

      load_plugin_textdomain( 'lead-form-builder' );

   }

   /**
    * Initialize the plugin
    *
    * Load the plugin only after Elementor (and other plugins) are loaded.
    * Checks for basic plugin requirements, if one check fail don't continue,
    * if all check have passed load the files required to run the plugin.
    *
    * Fired by `plugins_loaded` action hook.
    *
    * @since 1.0.0
    *
    * @access public
    */
   public function init() {
  
    // Register Widget Styles
    add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_style' ] );

   }



  public function widget_style() {      

    wp_register_style( 'lfb-style', LFB_UN_PLUGIN_URL. '/css/lfb-styler.css' );

    wp_enqueue_style( 'lfb-style' ); 

  }

}

LFB_Addon_Init::instance();

/*****  Creating a New Category*********
*******************************************/
function lfb_elementor_widget_categories( $elements_manager ) {

  $elements_manager->add_category(
    'lfb-category',
    [
      'title' => __( 'Lead Form Styler', 'lead-form-builder' ),
      'icon' => 'eicon-pro-icon',
    ]
  );
}
add_action( 'elementor/elements/categories_registered', 'lfb_elementor_widget_categories' );

function lfb_add_new_elements(){


  require_once LFB_EXT_DIR.'modules/lfb-styler.php';

}

add_action('elementor/widgets/widgets_registered','lfb_add_new_elements');
