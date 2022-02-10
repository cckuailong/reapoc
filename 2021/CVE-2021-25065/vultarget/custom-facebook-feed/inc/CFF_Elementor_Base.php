<?php
namespace CustomFacebookFeed;
class CFF_Elementor_Base{
	const VERSION = CFFVER;
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '5.6';
    private static $instance;


	public static function instance() {
		if ( !isset( self::$instance ) && !self::$instance instanceof CFF_Elementor_Base ) {
			self::$instance = new CFF_Elementor_Base();
            self::$instance->apply_hooks();
		}
		return self::$instance;
	}
	 private function apply_hooks(){
        add_action( 'elementor/frontend/after_register_scripts', [$this, 'register_frontend_scripts'] );
        add_action( 'elementor/frontend/after_register_styles', [$this, 'register_frontend_styles'], 10 );
        add_action( 'elementor/frontend/after_enqueue_styles', [$this, 'enqueue_frontend_styles'], 10 );
        add_action( 'elementor/controls/controls_registered', [$this, 'register_controls']);
        add_action( 'elementor/widgets/widgets_registered', [$this,'register_widgets']);
        add_action( 'elementor/init', [$this, 'add_smashballon_categories']);

    }

    public function register_controls() {
        $controls_manager = \Elementor\Plugin::$instance->controls_manager;
        $controls_manager->register_control('cff_feed_control', new CFF_Feed_Elementor_Control());

    }


	public function register_widgets() {
        $instance_manager = \Elementor\Plugin::instance()->widgets_manager;
        $instance_manager->register_widget_type(new CFF_Elementor_Widget());
    }


    public function register_frontend_scripts(){
    	$data = array(
			'placeholder' => CFF_PLUGIN_URL. 'assets/img/placeholder.png',
			#'resized_url' => Cff_Utils::cff_get_resized_uploads_url(),
		);

    	wp_register_script(
			'cffscripts',
			CFF_PLUGIN_URL . 'assets/js/cff-scripts.js' ,
			array('jquery'),
			CFFVER,
			true
		);
		wp_localize_script( 'cffscripts', 'cffOptions', $data );

        wp_register_script(
            'elementor-preview',
            CFF_PLUGIN_URL . 'assets/js/elementor-preview.js' ,
            array('jquery'),
            CFFVER,
            true
        );
    }

    public function register_frontend_styles(){
        wp_register_style(
        	'cffstyles',
			CFF_PLUGIN_URL . 'assets/css/cff-style.min.css' ,
			array(),
			CFFVER
        );
    }

    public function enqueue_frontend_styles(){
        wp_enqueue_style( 'cffstyles' );
    }

     public function add_smashballon_categories() {
        \Elementor\Plugin::instance()->elements_manager->add_category('smash-balloon',[
            'title' => __( 'Smash Balloon', 'custom-facebook-feed' ),
            'icon' => 'fa fa-plug',
        ]);
    }

}

