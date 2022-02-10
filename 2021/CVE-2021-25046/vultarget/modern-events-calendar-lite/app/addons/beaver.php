<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Beaver Builder addon class
 * @author Webnus <info@webnus.biz>
 */
class MEC_addon_beaver extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // MEC Factory class
        $this->factory = $this->getFactory();
        
        // MEC Main class
        $this->main = $this->getMain();
    }
    
    /**
     * Initialize the Elementor addon
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Beaver Builder is not installed
        if(!class_exists( 'FLBuilder' ) ) return false;
        define( 'MEC_BEAVER_DIR', plugin_dir_path( __FILE__ ) );
        define( 'MEC_BEAVER_URL', plugins_url( '/', __FILE__ ) );
        add_action( 'init', array($this,'mec_beaver_builder_shortcode') );
        return true;
    }

    public function mec_beaver_builder_shortcode() {
        if ( class_exists( 'FLBuilder' ) ) {
            require_once MEC_ABSPATH.'app/addons/mec-beaver-builder/mec-beaver-builder.php';
        }
    }

}