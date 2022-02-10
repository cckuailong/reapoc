<?php
/*
Plugin Name: myStickyElements
Plugin URI: https://premio.io/
Description: myStickyElements is simple yet very effective plugin. It is perfect to fill out usually unused side space on webpages with some additional messages, videos, social widgets ...
Version: 2.0.3
Author: Premio
Author URI: https://premio.io/
Domain Path: /languages
License: GPLv2 or later
*/

defined('ABSPATH') or die("Cannot access pages directly.");

define('MYSTICKYELEMENTS_URL', plugins_url('/', __FILE__));  // Define Plugin URL
define('MYSTICKYELEMENTS_PATH', plugin_dir_path(__FILE__));  // Define Plugin Directory Path
define("MY_STICKY_ELEMENT_VERSION", "2.0.3");
/*
 * redirect my sticky element setting page after plugin activated
 */
add_action( 'activated_plugin', 'mystickyelement_activation_redirect' );
function mystickyelement_activation_redirect($plugin){

	if( $plugin == plugin_basename( __FILE__ ) ) {
        $is_shown = get_option("mysticky_element_update_message");
        $mystickyelements_contact_form = get_option("mystickyelements-contact-form");
        $mystickyelements_widgets = get_option("mystickyelements-widgets");
        if($is_shown === false) {
            add_option("mysticky_element_update_message", 1);
        }
		
		if ( $mystickyelements_widgets && $mystickyelements_contact_form ) {
			wp_redirect( admin_url( 'admin.php?page=my-sticky-elements' ) ) ;
		} else {
			wp_redirect( admin_url( 'admin.php?page=my-sticky-elements&widget=0' ) ) ;
		}
		exit;
	}
}


class MyStickyElementsPage
{
    private $options;
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'admin_init', array( $this, 'mystickysideelement_load_transl') );
		add_action( 'admin_enqueue_scripts',  array( $this, 'mw_enqueue_color_picker' ) );

    }

		public function mystickysideelement_load_transl()
	{
		load_plugin_textdomain('mystickyelements', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
	}


    public function add_plugin_page()
    {
        add_options_page(
            'Settings Admin',
            'myStickyelements',
            'manage_options',
            'my-sticky-elements-settings',
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page() {


   // Set class property
   // $all_options = array (
	$this->options = get_option( 'mysticky_elements_options');
	$this->options0 = get_option( 'mysticky_elements_options0');
	$this->options1 = get_option( 'mysticky_elements_options1');
	$this->options2 = get_option( 'mysticky_elements_options2');
	$this->options3 = get_option( 'mysticky_elements_options3');
	$this->options4 = get_option( 'mysticky_elements_options4');
	$this->options5 = get_option( 'mysticky_elements_options5');
	$this->options6 = get_option( 'mysticky_elements_options6');
	$this->options7 = get_option( 'mysticky_elements_options7');
	$this->options8 = get_option( 'mysticky_elements_options8');
	$this->options9 = get_option( 'mysticky_elements_options9');
	?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e( 'myStickylements Settings', 'mystickyelements' ); ?></h2>

           <?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';  ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=my-sticky-elements-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General Settings</a>
                <a href="?page=my-sticky-elements-settings&tab=element_1" class="nav-tab <?php echo $active_tab == 'element_1' ? 'nav-tab-active' : ''; ?>">E1</a>
                <a href="?page=my-sticky-elements-settings&tab=element_2" class="nav-tab <?php echo $active_tab == 'element_2' ? 'nav-tab-active' : ''; ?>">E2</a>
                <a href="?page=my-sticky-elements-settings&tab=element_3" class="nav-tab <?php echo $active_tab == 'element_3' ? 'nav-tab-active' : ''; ?>">E3</a>
                <a href="?page=my-sticky-elements-settings&tab=element_4" class="nav-tab <?php echo $active_tab == 'element_4' ? 'nav-tab-active' : ''; ?>">E4</a>
                <a href="?page=my-sticky-elements-settings&tab=element_5" class="nav-tab <?php echo $active_tab == 'element_5' ? 'nav-tab-active' : ''; ?>">E5</a>
                <a href="?page=my-sticky-elements-settings&tab=element_6" class="nav-tab <?php echo $active_tab == 'element_6' ? 'nav-tab-active' : ''; ?>">E6</a>
                <a href="?page=my-sticky-elements-settings&tab=element_7" class="nav-tab <?php echo $active_tab == 'element_7' ? 'nav-tab-active' : ''; ?>">E7</a>
                <a href="?page=my-sticky-elements-settings&tab=element_8" class="nav-tab <?php echo $active_tab == 'element_8' ? 'nav-tab-active' : ''; ?>">E8</a>
                <a href="?page=my-sticky-elements-settings&tab=element_9" class="nav-tab <?php echo $active_tab == 'element_9' ? 'nav-tab-active' : ''; ?>">E9</a>
                <a href="?page=my-sticky-elements-settings&tab=element_10" class="nav-tab <?php echo $active_tab == 'element_10' ? 'nav-tab-active' : ''; ?>">E10</a>
            </h2>
            <form method="post" action="options.php">
             <?php

                if( $active_tab == 'general' ) {

                    settings_fields( 'mysticky_elements_option_group' );
                    do_settings_sections( 'my-sticky-elements-settings' );

                } else if( $active_tab == 'element_1' )  {

                    settings_fields( 'mysticky_elements_option_group9' );
                    do_settings_sections( 'my-sticky-elements-settings9' );


				 } else if( $active_tab == 'element_2' )  {

                    settings_fields( 'mysticky_elements_option_group8' );
                    do_settings_sections( 'my-sticky-elements-settings8' );

                } else if( $active_tab == 'element_3' )  {

                    settings_fields( 'mysticky_elements_option_group7' );
                    do_settings_sections( 'my-sticky-elements-settings7' );

				} else if( $active_tab == 'element_4' )  {

                    settings_fields( 'mysticky_elements_option_group6' );
                    do_settings_sections( 'my-sticky-elements-settings6' );

				} else if( $active_tab == 'element_5' )  {

                    settings_fields( 'mysticky_elements_option_group5' );
                    do_settings_sections( 'my-sticky-elements-settings5' );

				} else if( $active_tab == 'element_6' )  {

                    settings_fields( 'mysticky_elements_option_group4' );
                    do_settings_sections( 'my-sticky-elements-settings4' );

				} else if( $active_tab == 'element_7' )  {

                    settings_fields( 'mysticky_elements_option_group3' );
                    do_settings_sections( 'my-sticky-elements-settings3' );

				} else if( $active_tab == 'element_8' )  {

                    settings_fields( 'mysticky_elements_option_group2' );
                    do_settings_sections( 'my-sticky-elements-settings2' );

				} else if( $active_tab == 'element_9' )  {

                    settings_fields( 'mysticky_elements_option_group1' );
                    do_settings_sections( 'my-sticky-elements-settings1' );

				} else if( $active_tab == 'element_10' )  {

                    settings_fields( 'mysticky_elements_option_group0' );
                    do_settings_sections( 'my-sticky-elements-settings0' );

                }

                // This prints out all hidden setting fields
           //     settings_fields( 'mysticky_elements_option_group' );
             //   do_settings_sections( 'my-sticky-elements-settings' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
		global $id, $title, $callback, $page;
        register_setting(
            'mysticky_elements_option_group', // Option group
            'mysticky_elements_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
		register_setting(
            'mysticky_elements_option_group9',
            'mysticky_elements_options9',
            array( $this, 'sanitize' )
        );

		register_setting(
            'mysticky_elements_option_group8',
            'mysticky_elements_options8',
            array( $this, 'sanitize' )
        );
		register_setting(
            'mysticky_elements_option_group7',
            'mysticky_elements_options7',
            array( $this, 'sanitize' )
        );
		register_setting(
            'mysticky_elements_option_group6',
            'mysticky_elements_options6',
            array( $this, 'sanitize' )
        );
		register_setting(
            'mysticky_elements_option_group5',
            'mysticky_elements_options5',
            array( $this, 'sanitize' )
        );
		register_setting(
            'mysticky_elements_option_group4',
            'mysticky_elements_options4',
            array( $this, 'sanitize' )
        );
		register_setting(
            'mysticky_elements_option_group3',
            'mysticky_elements_options3',
            array( $this, 'sanitize' )
        );
		register_setting(
            'mysticky_elements_option_group2',
            'mysticky_elements_options2',
            array( $this, 'sanitize' )
        );
		register_setting(
            'mysticky_elements_option_group1',
            'mysticky_elements_options1',
            array( $this, 'sanitize' )
        );
		register_setting(
            'mysticky_elements_option_group0',
            'mysticky_elements_options0',
            array( $this, 'sanitize' )
        );

		add_settings_field( $id, $title, $callback, $page, $section = 'default', $args = array() );

	    //** General Settings **//
	    add_settings_section(
            'setting_section_id', // ID
            __("myStickyElements Options", 'mystickyelements'), // Title
            array( $this, 'print_section_info' ), // Callback
            'my-sticky-elements-settings' // Page
        );

		add_settings_field(
            'myfixed_disable_small_screen',
            __("Disable at Small Screen Sizes", 'mystickyelements'),
            array( $this, 'myfixed_disable_small_screen_callback' ),
            'my-sticky-elements-settings',
            'setting_section_id'
        );

	/*	add_settings_field(
            'mysticky_active_on_height',
            'Make visible when scroled',
            array( $this, 'mysticky_active_on_height_callback' ),
            'my-sticky-elements-settings',
            'setting_section_id'
        );*/

		add_settings_field(
            'myfixed_click',
            __("Change on Event", 'mystickyelements'),
            array( $this, 'myfixed_click_callback' ),
            'my-sticky-elements-settings',
            'setting_section_id'
        );

		add_settings_field(
            'myfixed_cssstyle',
            __("CSS style", 'mystickyelements'),
            array( $this, 'myfixed_cssstyle_callback' ),
            'my-sticky-elements-settings',
            'setting_section_id'

        );


		   //** First element 9  **//

		 add_settings_section(
            'setting_section_id', // ID
            __("myStickyElements Options", 'mystickyelements'), // Title
            array( $this, 'print_section_info9' ), // Callback
            'my-sticky-elements-settings9' // Page
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element9_enable_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
        add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element9_side_position_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element9_top_position_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element9_icon_bg_color_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element9_icon_bg_img_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element9_content_bg_color_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element9_content_txt_color_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element9_content_width_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element9_content_padding_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element9_content_callback' ),
            'my-sticky-elements-settings9',
            'setting_section_id'
        );


		 //** Second element 8  **//
		 add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info8' ),
            'my-sticky-elements-settings8'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element8_enable_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );

		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element8_side_position_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element8_top_position_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element8_icon_bg_color_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element8_icon_bg_img_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element8_content_bg_color_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element8_content_txt_color_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element8_content_width_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element8_content_padding_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element8_content_callback' ),
            'my-sticky-elements-settings8',
            'setting_section_id'
        );

		//** Third element 7  **//

		add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info7' ),
            'my-sticky-elements-settings7'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element7_enable_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element7_side_position_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element7_top_position_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element7_icon_bg_color_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element7_icon_bg_img_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element7_content_bg_color_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element7_content_txt_color_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element7_content_width_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element7_content_padding_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element7_content_callback' ),
            'my-sticky-elements-settings7',
            'setting_section_id'
        );

		//** Fourth element 6  **//

		add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info6' ),
            'my-sticky-elements-settings6'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element6_enable_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element6_side_position_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element6_top_position_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element6_icon_bg_color_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element6_icon_bg_img_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element6_content_bg_color_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element6_content_txt_color_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element6_content_width_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element6_content_padding_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element6_content_callback' ),
            'my-sticky-elements-settings6',
            'setting_section_id'
        );

		//** Fifth element 5  **//

		add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info5' ),
            'my-sticky-elements-settings5'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element5_enable_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );


		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element5_side_position_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element5_top_position_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element5_icon_bg_color_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element5_icon_bg_img_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element5_content_bg_color_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element5_content_txt_color_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element5_content_width_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element5_content_padding_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element5_content_callback' ),
            'my-sticky-elements-settings5',
            'setting_section_id'
        );


		//** Sixth element 4  **//

		add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info4' ),
            'my-sticky-elements-settings4'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element4_enable_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );


		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element4_side_position_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element4_top_position_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element4_icon_bg_color_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element4_icon_bg_img_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element4_content_bg_color_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element4_content_txt_color_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element4_content_width_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element4_content_padding_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element4_content_callback' ),
            'my-sticky-elements-settings4',
            'setting_section_id'
        );





		//** Sevnth element 3  **//

		add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info3' ),
            'my-sticky-elements-settings3'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element3_enable_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );


		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element3_side_position_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element3_top_position_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element3_icon_bg_color_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element3_icon_bg_img_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element3_content_bg_color_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element3_content_txt_color_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element3_content_width_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element3_content_padding_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element3_content_callback' ),
            'my-sticky-elements-settings3',
            'setting_section_id'
        );


		//** Eight element 2  **//

		add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info2' ),
            'my-sticky-elements-settings2'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element2_enable_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );


		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element2_side_position_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element2_top_position_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element2_icon_bg_color_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element2_icon_bg_img_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element2_content_bg_color_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element2_content_txt_color_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element2_content_width_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element2_content_padding_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element2_content_callback' ),
            'my-sticky-elements-settings2',
            'setting_section_id'
        );


		//** Ninth element 1  **//

		add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info1' ),
            'my-sticky-elements-settings1'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element1_enable_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );


		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element1_side_position_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element1_top_position_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element1_icon_bg_color_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element1_icon_bg_img_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element1_content_bg_color_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element1_content_txt_color_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element1_content_width_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element1_content_padding_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element1_content_callback' ),
            'my-sticky-elements-settings1',
            'setting_section_id'
        );


		//** Tenth element 0  **//

		add_settings_section(
            'setting_section_id',
            __("myStickyElements Options", 'mystickyelements'),
            array( $this, 'print_section_info0' ),
            'my-sticky-elements-settings0'
        );
		add_settings_field(
            'myfixed_element_enable',
            __("Enable Element", 'mystickyelements'),
            array( $this, 'myfixed_element0_enable_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );


		add_settings_field(
            'myfixed_element_side_position',
            __("Horizontal Position", 'mystickyelements'),
            array( $this, 'myfixed_element0_side_position_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_top_position',
            __("Vertical Position", 'mystickyelements'),
            array( $this, 'myfixed_element0_top_position_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_color',
            __("Icon bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element0_icon_bg_color_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_icon_bg_img',
            __("Icon bg Image", 'mystickyelements'),
            array( $this, 'myfixed_element0_icon_bg_img_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_bg_color',
            __("Content bg Color", 'mystickyelements'),
            array( $this, 'myfixed_element0_content_bg_color_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_txt_color',
            __("Content Text Color", 'mystickyelements'),
            array( $this, 'myfixed_element0_content_txt_color_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_width',
            __("Content Width", 'mystickyelements'),
            array( $this, 'myfixed_element0_content_width_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content_padding',
            __("Content Padding", 'mystickyelements'),
            array( $this, 'myfixed_element0_content_padding_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );
		add_settings_field(
            'myfixed_element_content',
            __("Content", 'mystickyelements'),
            array( $this, 'myfixed_element0_content_callback' ),
            'my-sticky-elements-settings0',
            'setting_section_id'
        );

    }



    /**
     * Sanitize each setting field as needed
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {

        $new_input = array();
        if( isset( $input['myfixed_element_enable'] ) )
            $new_input['myfixed_element_enable'] = sanitize_text_field( $input['myfixed_element_enable'] );

		if( isset( $input['myfixed_element_side_position'] ) )
            $new_input['myfixed_element_side_position'] = sanitize_text_field( $input['myfixed_element_side_position'] );

		if( isset( $input['myfixed_element_top_position'] ) )
            $new_input['myfixed_element_top_position'] = absint( $input['myfixed_element_top_position'] );

		if( isset( $input['myfixed_element_icon_bg_color'] ) )
            $new_input['myfixed_element_icon_bg_color'] = sanitize_text_field( $input['myfixed_element_icon_bg_color'] );

		if( isset( $input['myfixed_element_icon_bg_img'] ) )
		    $new_input['myfixed_element_icon_bg_img'] = sanitize_text_field( $input['myfixed_element_icon_bg_img'] );

		if( isset( $input['myfixed_element_content_bg_color'] ) )
            $new_input['myfixed_element_content_bg_color'] =  sanitize_text_field( $input['myfixed_element_content_bg_color'] );

		if( isset( $input['myfixed_element_content_txt_color'] ) )
            $new_input['myfixed_element_content_txt_color'] =  sanitize_text_field( $input['myfixed_element_content_txt_color'] );

		if( isset( $input['myfixed_element_content_width'] ) )
            $new_input['myfixed_element_content_width'] =  absint( $input['myfixed_element_content_width'] );

		if( isset( $input['myfixed_element_content_padding'] ) )
            $new_input['myfixed_element_content_padding'] =  absint( $input['myfixed_element_content_padding'] );

		if( isset( $input['myfixed_element_content'] ) )
			$new_input['myfixed_element_content'] =  wp_kses($input['myfixed_element_content'],

			array(
				'a' => array(
						'href' => array(),
						'title' => array(),
						'target' => array(),
						'rel' => array()
						),
				'br' => array(),
				'h1' => array(),
				'h2' => array(),
				'h3' => array(),
				'h4' => array(),
				'h5' => array(),
				'h6' => array(),
				'&nbsp;' => array(),
				'hr' => array(),
				'p' => array(),
				'em' => array(),
				'strong' => array(),
				'ul' => array(),
				'ol' => array(),
				'li' => array(),
				'blockquote' => array(),
				'iframe' => array(
						'src' => array(),
						'width' => array(),
						'height' => array(),
						'frameborder' => array(),
						'allowfullscreen' => array(),
						'scrolling' => array(),
						'style' => array(),
						'allowtransparency' => array()
						),
				'img' => array(
						'src' => array(),
						'alt' => array(),
						'class' => array(),
						'width' => array(),
						'height' => array(),
						'rel' => array()
						),
				'span' => array(
						'style' => array(),
						'class' => array()
						)

				)
			);


        if( isset( $input['myfixed_zindex'] ) )
            $new_input['myfixed_zindex'] = absint( $input['myfixed_zindex'] );

		if( isset( $input['myfixed_disable_small_screen'] ) )
            $new_input['myfixed_disable_small_screen'] = absint( $input['myfixed_disable_small_screen'] );


		if( isset( $input['myfixed_click'] ) )
            $new_input['myfixed_click'] = sanitize_text_field( $input['myfixed_click'] );

		if( isset( $input['myfixed_cssstyle'] ) )
             $new_input['myfixed_cssstyle'] = sanitize_text_field( $input['myfixed_cssstyle'] );


        return $new_input;
    }

	public  function mw_enqueue_color_picker(  )
	{
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'my-script-handle', plugins_url('js/iris-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	}



	 /* ***** GENERAL ***** */

    public function print_section_info()
    {
        print __('Add sticky elements to your blog.', 'mystickyelements');

    }
    /**
     * Get the settings option array and print one of its values
     */

	public function myfixed_disable_small_screen_callback()
	{
		printf(
		'<p class="description">'. __('less than ', 'mystickyelements') .'<input type="text" size="4" id="myfixed_disable_small_screen" name="mysticky_elements_options[myfixed_disable_small_screen]" value="%s" />'. __('px width, 0 to disable.', 'mystickyelements') .'</p>',
            isset( $this->options['myfixed_disable_small_screen'] ) ? esc_attr( $this->options['myfixed_disable_small_screen']) : ''
		);
	}

	/*public function mysticky_active_on_height_callback()
	{
		printf(
		'<p class="description">after <input type="text" size="4" id="mysticky_active_on_height" name="mysticky_elements_options[mysticky_active_on_height]" value="%s" />px</p>',
            isset( $this->options['mysticky_active_on_height'] ) ? esc_attr( $this->options['mysticky_active_on_height']) : ''
		);
	}*/


	public function myfixed_click_callback()
	{
		printf(
		'<select id="myfixed_click" name="mysticky_elements_options[myfixed_click]" selected="%s">',
			isset( $this->options['myfixed_click'] ) ? esc_attr( $this->options['myfixed_click']) : ''
		);
		if ($this->options['myfixed_click'] == 'click') {
		printf(
		'<option name="click" value="click" selected>'. __('click', 'mystickyelements') .'</option>
		<option name="hover" value="hover">'. __('hover', 'mystickyelements') .'</option>
		</select>'
		);
		}
		if ($this->options['myfixed_click'] == 'hover') {
		printf(
		'<option name="click" value="click">'. __('click', 'mystickyelements') .'</option>
		<option name="hover" value="hover" selected >'. __('hover', 'mystickyelements') .'</option>
		</select>'
		);
		}
		if ($this->options['myfixed_click'] == '') {
		printf(
		'<option name="click" value="click" selected>'. __('click', 'mystickyelements') .'</option>
		<option name="hover" value="hover">'. __('hover', 'mystickyelements') .'</option>
		</select>'
		);
		}
	}

   public function myfixed_cssstyle_callback()

    {
        printf(
            '
			<p class="description">'. __('Add/Edit css to manage advanced elements style.', 'mystickyelements') .'</p>  <textarea type="text" rows="4" cols="60" id="myfixed_cssstyle" name="mysticky_elements_options[myfixed_cssstyle]">%s</textarea> <br />
		' ,
            isset( $this->options['myfixed_cssstyle'] ) ? esc_attr( $this->options['myfixed_cssstyle']) : ''
        );
		echo '</p>';
    }


	/* ***** ELEMENT 1 ***** */

	public function print_section_info9()
    {
        print __('Element 1 Settings.', 'mystickyelements');
    }


	 public function myfixed_element9_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options9[myfixed_element_enable]" selected="%s">',
			isset( $this->options9['myfixed_element_enable'] ) ? esc_attr( $this->options9['myfixed_element_enable']) : ''
		);
		if ($this->options9['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options9['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options9['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }





	public function myfixed_element9_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options9[myfixed_element_side_position]" selected="%s">',
			isset( $this->options9['myfixed_element_side_position'] ) ? esc_attr( $this->options9['myfixed_element_side_position']) : ''
		);
		if ($this->options9['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options9['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options9['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }

	public function myfixed_element9_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options9[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options9['myfixed_element_top_position'] ) ? esc_attr( $this->options9['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element9_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options9[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options9['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options9['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element9_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options9[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options9['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options9['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element9_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options9[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options9['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options9['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element9_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options9[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options9['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options9['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element9_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options9[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options9['myfixed_element_content_width'] ) ? esc_attr( $this->options9['myfixed_element_content_width']) : ''
        );
    }


	public function myfixed_element9_content_padding_callback()
    {
		printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options9[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options9['myfixed_element_content_padding'] ) ? esc_attr( $this->options9['myfixed_element_content_padding']) : ''
        );
	}



   public function myfixed_element9_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options9['myfixed_element_content'] ) ? esc_textarea( $this->options9['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options9[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }


    /* ***** ELEMENT 2 ***** */

	public function print_section_info8()
    {
        print __('Element 2 Settings.', 'mystickyelements');
    }

     public function myfixed_element8_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options8[myfixed_element_enable]" selected="%s">',
			isset( $this->options8['myfixed_element_enable'] ) ? esc_attr( $this->options8['myfixed_element_enable']) : ''
		);
		if ($this->options8['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options8['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options8['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }


	public function myfixed_element8_side_position_callback(){
		
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options8[myfixed_element_side_position]" selected="%s">',
			isset( $this->options8['myfixed_element_side_position'] ) ? esc_attr( $this->options8['myfixed_element_side_position']) : ''
		);
		if ($this->options8['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options8['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options8['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }

	public function myfixed_element8_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options8[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options8['myfixed_element_top_position'] ) ? esc_attr( $this->options8['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element8_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options8[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options8['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options8['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element8_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options8[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options8['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options8['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element8_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options8[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options8['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options8['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element8_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options8[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options8['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options8['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element8_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options8[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options8['myfixed_element_content_width'] ) ? esc_attr( $this->options8['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element8_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options8[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options8['myfixed_element_content_padding'] ) ? esc_attr( $this->options8['myfixed_element_content_padding']) : ''
        );
    }


	 public function myfixed_element8_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options8['myfixed_element_content'] ) ? esc_textarea( $this->options8['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options8[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }


    /* ***** ELEMENT 3 ***** */


		public function print_section_info7()
    {
        print __('Element 3 Settings.', 'mystickyelements');
    }


	 public function myfixed_element7_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options7[myfixed_element_enable]" selected="%s">',
			isset( $this->options7['myfixed_element_enable'] ) ? esc_attr( $this->options7['myfixed_element_enable']) : ''
		);
		if ($this->options7['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options7['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options7['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }



	public function myfixed_element7_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options7[myfixed_element_side_position]" selected="%s">',
			isset( $this->options7['myfixed_element_side_position'] ) ? esc_attr( $this->options7['myfixed_element_side_position']) : ''
		);
		if ($this->options7['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options7['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options7['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }
	public function myfixed_element7_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options7[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options7['myfixed_element_top_position'] ) ? esc_attr( $this->options7['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element7_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options7[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options7['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options7['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element7_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options7[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options7['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options7['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element7_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options7[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options7['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options7['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element7_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options7[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options7['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options7['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element7_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options7[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options7['myfixed_element_content_width'] ) ? esc_attr( $this->options7['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element7_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options7[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options7['myfixed_element_content_padding'] ) ? esc_attr( $this->options7['myfixed_element_content_padding']) : ''
        );
    }


   public function myfixed_element7_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options7['myfixed_element_content'] ) ? esc_textarea( $this->options7['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options7[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }




	/* ***** ELEMENT 4 ***** */


		public function print_section_info6()
    {
       print __('Element 4 Settings.', 'mystickyelements');
    }


	 public function myfixed_element6_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options6[myfixed_element_enable]" selected="%s">',
			isset( $this->options6['myfixed_element_enable'] ) ? esc_attr( $this->options6['myfixed_element_enable']) : ''
		);
		if ($this->options6['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options6['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options6['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }



	public function myfixed_element6_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options6[myfixed_element_side_position]" selected="%s">',
			isset( $this->options6['myfixed_element_side_position'] ) ? esc_attr( $this->options6['myfixed_element_side_position']) : ''
		);
		if ($this->options6['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options6['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options6['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }
	public function myfixed_element6_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options6[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options6['myfixed_element_top_position'] ) ? esc_attr( $this->options6['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element6_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options6[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options6['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options6['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element6_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options6[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options6['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options6['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element6_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options6[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options6['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options6['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element6_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options6[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options6['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options6['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element6_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options6[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options6['myfixed_element_content_width'] ) ? esc_attr( $this->options6['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element6_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options6[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options6['myfixed_element_content_padding'] ) ? esc_attr( $this->options6['myfixed_element_content_padding']) : ''
        );
    }


   public function myfixed_element6_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options6['myfixed_element_content'] ) ? esc_textarea( $this->options6['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options6[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }



	/* ***** ELEMENT 5 ***** */


		public function print_section_info5()
    {
        print __('Element 5 Settings.', 'mystickyelements');
    }


	 public function myfixed_element5_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options5[myfixed_element_enable]" selected="%s">',
			isset( $this->options5['myfixed_element_enable'] ) ? esc_attr( $this->options5['myfixed_element_enable']) : ''
		);
		if ($this->options5['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options5['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options5['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }



	public function myfixed_element5_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options5[myfixed_element_side_position]" selected="%s">',
			isset( $this->options5['myfixed_element_side_position'] ) ? esc_attr( $this->options5['myfixed_element_side_position']) : ''
		);
		if ($this->options5['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options5['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options5['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }
	public function myfixed_element5_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options5[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options5['myfixed_element_top_position'] ) ? esc_attr( $this->options5['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element5_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options5[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options5['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options5['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element5_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options5[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options5['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options5['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element5_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options5[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options5['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options5['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element5_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options5[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options5['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options5['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element5_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options5[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options5['myfixed_element_content_width'] ) ? esc_attr( $this->options5['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element5_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options5[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options5['myfixed_element_content_padding'] ) ? esc_attr( $this->options5['myfixed_element_content_padding']) : ''
        );
    }


   public function myfixed_element5_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options5['myfixed_element_content'] ) ? esc_textarea( $this->options5['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options5[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }




	/* ***** ELEMENT 6 ***** */


		public function print_section_info4()
    {
        print __('Element 6 Settings.', 'mystickyelements');
    }


	 public function myfixed_element4_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options4[myfixed_element_enable]" selected="%s">',
			isset( $this->options4['myfixed_element_enable'] ) ? esc_attr( $this->options4['myfixed_element_enable']) : ''
		);
		if ($this->options4['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options4['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options4['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }



	public function myfixed_element4_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options4[myfixed_element_side_position]" selected="%s">',
			isset( $this->options4['myfixed_element_side_position'] ) ? esc_attr( $this->options4['myfixed_element_side_position']) : ''
		);
		if ($this->options4['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options4['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options4['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }
	public function myfixed_element4_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options4[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options4['myfixed_element_top_position'] ) ? esc_attr( $this->options4['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element4_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options4[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options4['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options4['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element4_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options4[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options4['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options4['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element4_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options4[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options4['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options4['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element4_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options4[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options4['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options4['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element4_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options4[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options4['myfixed_element_content_width'] ) ? esc_attr( $this->options4['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element4_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options4[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options4['myfixed_element_content_padding'] ) ? esc_attr( $this->options4['myfixed_element_content_padding']) : ''
        );
    }


   public function myfixed_element4_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options4['myfixed_element_content'] ) ? esc_textarea( $this->options4['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options4[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }


	/* ***** ELEMENT 7 ***** */


	public function print_section_info3()
    {
        print __('Element 7 Settings.', 'mystickyelements');
    }


	 public function myfixed_element3_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options3[myfixed_element_enable]" selected="%s">',
			isset( $this->options3['myfixed_element_enable'] ) ? esc_attr( $this->options3['myfixed_element_enable']) : ''
		);
		if ($this->options3['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options3['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options3['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }



	public function myfixed_element3_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options3[myfixed_element_side_position]" selected="%s">',
			isset( $this->options3['myfixed_element_side_position'] ) ? esc_attr( $this->options3['myfixed_element_side_position']) : ''
		);
		if ($this->options3['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options3['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options3['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }
	public function myfixed_element3_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options3[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options3['myfixed_element_top_position'] ) ? esc_attr( $this->options3['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element3_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options3[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options3['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options3['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element3_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options3[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options3['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options3['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element3_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options3[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options3['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options3['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element3_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options3[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options3['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options3['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element3_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options3[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options3['myfixed_element_content_width'] ) ? esc_attr( $this->options3['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element3_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options3[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options3['myfixed_element_content_padding'] ) ? esc_attr( $this->options3['myfixed_element_content_padding']) : ''
        );
    }


   public function myfixed_element3_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options3['myfixed_element_content'] ) ? esc_textarea( $this->options3['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options3[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }


	/* ***** ELEMENT 8 ***** */


		public function print_section_info2()
    {
        print __('Element 8 Settings.', 'mystickyelements');
    }


	 public function myfixed_element2_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options2[myfixed_element_enable]" selected="%s">',
			isset( $this->options2['myfixed_element_enable'] ) ? esc_attr( $this->options2['myfixed_element_enable']) : ''
		);
		if ($this->options2['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options2['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options2['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }



	public function myfixed_element2_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options2[myfixed_element_side_position]" selected="%s">',
			isset( $this->options2['myfixed_element_side_position'] ) ? esc_attr( $this->options2['myfixed_element_side_position']) : ''
		);
		if ($this->options2['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options2['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options2['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }
	public function myfixed_element2_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options2[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options2['myfixed_element_top_position'] ) ? esc_attr( $this->options2['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element2_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options2[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options2['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options2['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element2_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options2[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options2['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options2['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element2_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options2[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options2['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options2['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element2_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options2[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options2['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options2['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element2_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options2[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options2['myfixed_element_content_width'] ) ? esc_attr( $this->options2['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element2_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options2[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options2['myfixed_element_content_padding'] ) ? esc_attr( $this->options2['myfixed_element_content_padding']) : ''
        );
    }


   public function myfixed_element2_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options2['myfixed_element_content'] ) ? esc_textarea( $this->options2['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options2[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }




	/* ***** ELEMENT 9 ***** */


		public function print_section_info1()
    {
        print __('Element 9 Settings.', 'mystickyelements');
    }


	 public function myfixed_element1_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options1[myfixed_element_enable]" selected="%s">',
			isset( $this->options1['myfixed_element_enable'] ) ? esc_attr( $this->options1['myfixed_element_enable']) : ''
		);
		if ($this->options1['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options1['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options1['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }



	public function myfixed_element1_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options1[myfixed_element_side_position]" selected="%s">',
			isset( $this->options1['myfixed_element_side_position'] ) ? esc_attr( $this->options1['myfixed_element_side_position']) : ''
		);
		if ($this->options1['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options1['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options1['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }
	public function myfixed_element1_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options1[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options1['myfixed_element_top_position'] ) ? esc_attr( $this->options1['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element1_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options1[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options1['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options1['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element1_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options1[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options1['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options1['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element1_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options1[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options1['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options1['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element1_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options1[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options1['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options1['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element1_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options1[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options1['myfixed_element_content_width'] ) ? esc_attr( $this->options1['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element1_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options1[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options1['myfixed_element_content_padding'] ) ? esc_attr( $this->options1['myfixed_element_content_padding']) : ''
        );
    }


   public function myfixed_element1_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options1['myfixed_element_content'] ) ? esc_textarea( $this->options1['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options1[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }



	/* ***** ELEMENT 10 ***** */


		public function print_section_info0()
    {
        print __('Element 10 Settings.', 'mystickyelements');
    }


	 public function myfixed_element0_enable_callback()
	{
		printf(
		'<select id="myfixed_element_enable" name="mysticky_elements_options0[myfixed_element_enable]" selected="%s">',
			isset( $this->options0['myfixed_element_enable'] ) ? esc_attr( $this->options0['myfixed_element_enable']) : ''
		);
		if ($this->options0['myfixed_element_enable'] == 'enable') {
			printf(
		'<option name="enable" value="enable" selected>enable</option>
		<option name="disable" value="disable">disable</option>
		</select>'
		);
		}
		if ($this->options0['myfixed_element_enable'] == 'disable') {
			printf(
		'<option name="enable" value="enable">enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
		if ($this->options0['myfixed_element_enable'] == '') {
			printf(
		'<option name="enable" value="enable" >enable</option>
		<option name="disable" value="disable" selected >disable</option>
		</select>'
		);
		}
    }



	public function myfixed_element0_side_position_callback()
	{
		printf(
		'<select id="myfixed_element_side_position" name="mysticky_elements_options0[myfixed_element_side_position]" selected="%s">',
			isset( $this->options0['myfixed_element_side_position'] ) ? esc_attr( $this->options0['myfixed_element_side_position']) : ''
		);
		if ($this->options0['myfixed_element_side_position'] == 'left') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
		if ($this->options0['myfixed_element_side_position'] == 'right') {
			printf(
		'<option name="left" value="left">left</option>
		<option name="right" value="right" selected >right</option>
		</select>'
		);
		}
		if ($this->options0['myfixed_element_side_position'] == '') {
			printf(
		'<option name="left" value="left" selected>left</option>
		<option name="right" value="right">right</option>
		</select>'
		);
		}
    }
	public function myfixed_element0_top_position_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_top_position" name="mysticky_elements_options0[myfixed_element_top_position]" value="%s" /> px (top position of an element). Please note that Element 1 must be on top of element 2 and so on, otherwise elements will overlap...</p>',
            isset( $this->options0['myfixed_element_top_position'] ) ? esc_attr( $this->options0['myfixed_element_top_position']) : ''
        );
    }
	public function myfixed_element0_icon_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_icon_bg_color" name="mysticky_elements_options0[myfixed_element_icon_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options0['myfixed_element_icon_bg_color'] ) ? esc_attr( $this->options0['myfixed_element_icon_bg_color']) : ''
        );
    }
	public function myfixed_element0_icon_bg_img_callback()
    {
        printf(
            '<p class="description"><input type="text" size="28" id="myfixed_element_icon_bg_img" name="mysticky_elements_options0[myfixed_element_icon_bg_img]" value="%s" />  Icon Image.</p>',
            isset( $this->options0['myfixed_element_icon_bg_img'] ) ? esc_attr( $this->options0['myfixed_element_icon_bg_img']) : ''
        );
    }
	public function myfixed_element0_content_bg_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_bg_color" name="mysticky_elements_options0[myfixed_element_content_bg_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options0['myfixed_element_content_bg_color'] ) ? esc_attr( $this->options0['myfixed_element_content_bg_color']) : ''
        );
    }
	public function myfixed_element0_content_txt_color_callback()
    {
        printf(
            '<p class="description"><input type="text" size="8" id="myfixed_element_content_txt_color" name="mysticky_elements_options0[myfixed_element_content_txt_color]" value="%s" class="my-color-field" /></p>',
            isset( $this->options0['myfixed_element_content_txt_color'] ) ? esc_attr( $this->options0['myfixed_element_content_txt_color']) : ''
        );
    }
	public function myfixed_element0_content_width_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_width" name="mysticky_elements_options0[myfixed_element_content_width]" value="%s" />px</p>',
            isset( $this->options0['myfixed_element_content_width'] ) ? esc_attr( $this->options0['myfixed_element_content_width']) : ''
        );
    }
	public function myfixed_element0_content_padding_callback()
    {
        printf(
            '<p class="description"><input type="text" size="4" id="myfixed_element_content_padding" name="mysticky_elements_options0[myfixed_element_content_padding]" value="%s" />px</p>',
            isset( $this->options0['myfixed_element_content_padding'] ) ? esc_attr( $this->options0['myfixed_element_content_padding']) : ''
        );
    }


   public function myfixed_element0_content_callback()

    {
		$content_id = 'myfixed_element_content';
		$content =  (isset( $this->options0['myfixed_element_content'] ) ? esc_textarea( $this->options0['myfixed_element_content']) : '');

		wp_editor( html_entity_decode($content), $content_id,
			 array(
			'textarea_name' => 'mysticky_elements_options0[myfixed_element_content]',
			//'teeny' => true,
			//'tinymce' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 8)
			)
		);
    }







}


	// end plugin admin settings





	// Create style from options

	function mysticky_element_build_stylesheet_content() {

		$mysticky_options = get_option( 'mysticky_elements_options' );


    echo
'<style type="text/css">';
	if (  $mysticky_options['myfixed_cssstyle'] == "" )  {
	echo '';
	}
	echo
	  $mysticky_options ['myfixed_cssstyle'] ;

	if  ($mysticky_options ['myfixed_disable_small_screen'] > 0 ){
    echo
		'
		@media only screen and (max-width: ' . $mysticky_options ['myfixed_disable_small_screen'] . 'px) { .mysticky-block-left, .mysticky-block-right { display: none; } }
	';

	}
	echo
'</style>
	';
	}




	function insert_my_footerrr() {

		$mysticky_options0 = get_option( 'mysticky_elements_options0' );
		$mysticky_options1 = get_option( 'mysticky_elements_options1' );
		$mysticky_options2 = get_option( 'mysticky_elements_options2' );
		$mysticky_options3 = get_option( 'mysticky_elements_options3' );
		$mysticky_options4 = get_option( 'mysticky_elements_options4' );
		$mysticky_options5 = get_option( 'mysticky_elements_options5' );
		$mysticky_options6 = get_option( 'mysticky_elements_options6' );
		$mysticky_options7 = get_option( 'mysticky_elements_options7' );
		$mysticky_options8 = get_option( 'mysticky_elements_options8' );
		$mysticky_options9 = get_option( 'mysticky_elements_options9' );

		$mysticky_options_arr = array($mysticky_options0, $mysticky_options1, $mysticky_options2, $mysticky_options3, $mysticky_options4, $mysticky_options5, $mysticky_options6, $mysticky_options7, $mysticky_options8, $mysticky_options9);

	foreach ($mysticky_options_arr as &$mysticky_option) {

		//$mysticky_option
	if ($mysticky_option ['myfixed_element_enable'] == "enable") {

	$plugins_url = plugins_url();

    echo '
	<div class="mysticky-block-' . $mysticky_option ['myfixed_element_side_position'] . '" style="width: ' . $mysticky_option ['myfixed_element_content_width'] . 'px; ' . $mysticky_option ['myfixed_element_side_position'] . ': -' . $mysticky_option ['myfixed_element_content_width'] . 'px; top: ' . $mysticky_option ['myfixed_element_top_position'] . 'px; position: fixed; background:' . $mysticky_option ['myfixed_element_content_bg_color'] . ';">
	<div class="mysticky-block-icon" style="background-color:' . $mysticky_option ['myfixed_element_icon_bg_color'] . '; background-image: url(' . plugins_url( ''. $mysticky_option ['myfixed_element_icon_bg_img'] . '', __FILE__ ) . ');"></div>
	 <div class="mysticky-block-content" style="min-height: 50px; color:' . $mysticky_option ['myfixed_element_content_txt_color'] . '; padding: ' . $mysticky_option ['myfixed_element_content_padding'] . 'px;">' . do_shortcode ($mysticky_option ['myfixed_element_content'] ) . '</div>

	</div>
';
       }; // endif


	  if ($mysticky_option ['myfixed_element_enable'] == "disable") {

    echo '';
       }; // endif


		}

	unset($mysticky_options_arr); // break the reference with the last element

}



function mystickyelements_script() {

	$mysticky_options = get_option( 'mysticky_elements_options' );

	if( wp_script_is( 'jquery' ) ) {
	// do nothing
	} else {
	wp_enqueue_script( 'jquery' );
	}
    //wp_enqueue_script( 'mystickyelements', 'https://code.jquery.com/jquery-3.5.0.js',false,'1.0.0', true );
	wp_register_script('mystickyelements', WP_PLUGIN_URL. '/mystickyelements/js/mystickyelements.js', false,'1.0.0', true);
	wp_enqueue_script( 'mystickyelements' );

	// Localize mystickyelements.js script with myStickyElements options
	$mysticky_translation_array = array(
		'myfixed_click_string' => $mysticky_options['myfixed_click'] ,
	/*	'mysticky_active_on_height_string' => $mysticky_options['mysticky_active_on_height'],*/
		'mysticky_disable_at_width_string' => $mysticky_options['myfixed_disable_small_screen'],

	);

		wp_localize_script( 'mystickyelements', 'mysticky_element', $mysticky_translation_array );
}




if ( !function_exists( 'mystickyelement_activate' )) {
	function mystickyelement_activate() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

		$contact_lists_table = $wpdb->prefix . 'mystickyelement_contact_lists';
		if ($wpdb->get_var("show tables like '$contact_lists_table'") != $contact_lists_table) {

			$contact_lists_table_sql = "CREATE TABLE $contact_lists_table (
				ID int(11) NOT NULL AUTO_INCREMENT,
				contact_name varchar(255) NULL,
				contact_phone varchar(255) NULL,
				contact_email varchar(255) NULL,
				contact_message text NULL,
				contact_option varchar(255) NULL,
				message_date DATETIME NOT NULL default '0000-00-00 00:00:00',
				PRIMARY KEY  (ID)
			) $charset_collate;";
			dbDelta($contact_lists_table_sql);
		}

		if ( get_option('mystickyelements-contact-form') == false ) {
			$contact_form = array(
								'enable' 		=> 1,
								'name' 			=> 1,
								'name_require' 	=> '',
								'name_value' 	=> '',
								'phone' 		=> 1,
								'phone_require' => 1,
								'phone_value' 	=> '',
								'email' 		=> 1,
								'email_require' => 1,
								'email_value' 	=> '',
								'message' 		=> 1,
								'message_value' => '',
								'dropdown'		=> '',
								'dropdown_require' => '',
								'submit_button_background_color'=> '#7761DF',
								'submit_button_text_color' 		=> '#FFFFFF',
								'submit_button_text' 	=> 'Submit',
								'desktop' 	=> 1,
								'mobile' 	=> 1,
								'direction' 	=> 'LTR',
								'tab_background_color' 	=> '#7761DF',
								'tab_text_color' 		=> '#FFFFFF',
								'headine_text_color' 	=> '#7761DF',
								'text_in_tab' 			=> 'Contact Us',
								'send_leads' 			=> 'database',
								'sent_to_mail' 			=> '',
								'form_css' 				=> '' ,
							);

			update_option( 'mystickyelements-contact-form', $contact_form);
		}

		if ( get_option('mystickyelements-social-channels') == false ) {
			$social_channels = array(
									'enable' 			=> 1,
									'whatsapp' 			=> 1,
									//'facebook_messenger'=> 1,
								);

			update_option( 'mystickyelements-social-channels', $social_channels);
		}
		if ( get_option('mystickyelements-social-channels-tabs') == false ) {
			$social_channels_tabs['whatsapp'] = array(
													'text' => "",
													'hover_text' => "WhatsApp",
													'bg_color' => "#26D367",
													'desktop' => 1,
													'mobile' => 1,
												);
			/*
			$social_channels_tabs['facebook_messenger'] = array(
													'text' => "",
													'hover_text' => "Facebook Messenger",
													'bg_color' => "#007FF7",
													'desktop' => 1,
													'mobile' => 1,
												);
			*/
			update_option( 'mystickyelements-social-channels-tabs', $social_channels_tabs);
		}

		if ( get_option('mystickyelements-general-settings') == false ) {
			$general_settings = array(
									'position' 			=> 'left',
									'open_tabs_when' 	=> 'hover',
									'mobile_behavior' 	=> 'disable',
									'flyout' 			=> 'disable',
									'custom_position' 	=> '',
									'tabs_css' 			=> '',
									'minimize_tab'		=> '1',
									'minimize_tab_background_color'	=> '#000000',
								);

			update_option( 'mystickyelements-general-settings', $general_settings);
		}
		
		$mystickyelements_popup_status = get_option( 'mystickyelements_intro_popup' );
		if( ( $mystickyelements_popup_status === false || empty( $mystickyelements_popup_status ) ) && !isset($_GET['update_version']) ) {
			add_option( 'mystickyelements_intro_popup', 'show' );
		}
	}
}

//register_activation_hook( __FILE__, 'mystickyelement_activate' );


if ( !function_exists('mystickyelements_social_channels')) {

	function mystickyelements_social_channels() {
		$social_channels = array(
							'whatsapp'	=> array(
											'text' => "WhatsApp",
											'icon_text' => "",
											'hover_text' => "WhatsApp",
											'background_color' => "#26D367",
											'placeholder'	=> 'Example: +18006927753',
											'class' => "fab fa-whatsapp",
											'tooltip'	=> 'Add your full WhatsApp number with country code. E.g., +18006927753',
											'is_pre_set_message' => 1,
											'number_validation' => 1,
											'icon_color' => 1
										),
							'facebook_messenger'	=> array(
											'text' => "Facebook Messenger",
											'icon_text' => "",
											'hover_text' => "Facebook Messenger",
											'background_color' => "#007FF7",
											'placeholder'	=> 'Example: Coca-Cola',
											'class' => "fab fa-facebook-messenger",
											'tooltip'	=> '<ul><li>1. Go to <a href="" target="_blank">Facebook.com</a></li><li>2. Click on your name tab</li><li>3. Copy the last part of the URL <img src="'.MYSTICKYELEMENTS_URL.'images/facebook-image.png" /></li><li>4. Add your Messenger username. If your page\'s username is "cocacola" add only the username part. E.g., cocacola</li></ul>',
											'icon_color' => 1
										),
							'facebook' => array(
											'text' => "Facebook",
											'icon_text' => "",
											'hover_text' => "Facebook",
											'background_color' => "#4267B2",
											'placeholder'	=> 'Example: https://facebook.com/coca-cola/',
											'class' => "fab fa-facebook-f",
											'tooltip'	=> 'Add the link of of your Facebook page or URL. E.g., <a href="https://facebook.com/cocacola" target="_blank">https://facebook.com/cocacola</a>',
											'icon_color' => 1
										),
							'SMS'	=> array(
											'text' => "SMS",
											'icon_text' => "",
											'hover_text' => "SMS",
											'background_color' => "#ff549c",
											'placeholder'	=> 'Example: +1507854875',
											'class' => "fas fa-sms",
											'tooltip'	=> 'Add your full phone number with country code. E.g., +18006927753',
											'number_validation' => 1,
											'icon_color' => 1
										),
							'phone'		=> array(
											'text' => "Phone",
											'icon_text' => "",
											'hover_text' => "Phone",
											'background_color' => "#26D37C",
											'placeholder'	=> 'Example: +18006927753',
											'class' => "fa fa-phone",
											'tooltip'	=> 'Add your full phone number with country code. E.g., +18006927753',
											'number_validation' => 1,
											'icon_color' => 1
										),							
							'email'		=> array(
											'text' => "Email",
											'icon_text' => "",
											'hover_text' => "Email",
											'background_color' => "#DC483C",
											'placeholder'	=> 'Example: john@example.com',
											'class' => "far fa-envelope",
											'tooltip'	=> 'Add your email address. E.g., support@premio.io',
											'icon_color' => 1
										),
							'insagram'	=> array(
											'text' => "Instagram",
											'icon_text' => "",
											'hover_text' => "Instagram",
											'background_color' => "",
											'placeholder'	=> 'Example: https://instagram.com/cocacola',
											'class' => "fab fa-instagram",
											'tooltip'	=> 'Add the link of of your Instagram profile E.g., <a href="https://instagram.com/cocacola" target="_blank">https://instagram.com/cocacola</a>',
											'icon_color' => 1
										),
							'skype'	=> array(
											'text' => "Skype",
											'icon_text' => "",
											'hover_text' => "Skype",
											'background_color' => "#00aff0",
											'placeholder'	=> 'Example: Enter your Skype Username',
											'class' => "fab fa-skype",
											'tooltip'	=> 'Enter your Skype username. E.g., username',
											'icon_color' => 1
										),
							'telegram'	=> array(
											'text' => "Telegram",
											'icon_text' => "",
											'hover_text' => "Telegram",
											'background_color' => "#2CA5E0",
											'placeholder'	=> 'Enter Telegram username of channel or personal profile',
											'class' => "fab fa-telegram-plane",
											'tooltip'	=> 'Enter the username of your Telegram profile or  channel. You can find your username by going into the Telegram profile. E.g., TelegramTips',
											'icon_color' => 1
										),
							'address'	=> array(
											'text' => "Address",
											'icon_text' => "",
											'icon_label' => "Address",
											'icon_new_tab' => "0",
											'hover_text' => "Address",
											'background_color' => "#23D28C",
											'placeholder'	=> 'Example: 3229, Royalway, Houston, TX 77058, US',
											'class' => "fas fa-map-marker-alt",
											'tooltip'	=> 'Add your full address. E.g., 3229, Royalway, Houston, TX 77058, US',
											'icon_color' => 1
										),
							'business_hours'	=> array(
											'text' => "Open Hours",
											'icon_text' => "",
											'icon_label' => "Opening Hours",
											'icon_new_tab' => "0",
											'hover_text' => "Open Hours",
											'background_color' => "#E85F65",
											'placeholder'	=> 'Example: 9:00 - 5:00',
											'class' => "fas fa-calendar-alt",
											'tooltip'	=> 'Write your opening hours. E.g, 9:00am - 5:00pm or 9:00 - 5:00 or 9:00 - 15:30',
											'icon_color' => 1
										),
							'youtube'	=> array(
											'text' => "YouTube",
											'icon_text' => "",
											'hover_text' => "YouTube",
											'background_color' => "#F54E4E",
											'placeholder'	=> 'Example: https://youtube.com/username',
											'class' => "fab fa-youtube",
											'tooltip'	=> 'Add your YouTube channel link. E.g., <a href="https://youtube.com/username" target="_blank">https://youtube.com/username</a>',
											'icon_color' => 1
										),
							'poptin_popups'	=> array(
											'text' => "Poptin Popups",
											'icon_text' => "",
											"icon_label"=>"Popup link",
											'hover_text' => "Poptin Popups",
											'background_color' => "#47a2b1",
											'placeholder'	=> 'Example: https://app.popt.in/APIRequest/click/96Y4a02XXa15e',
											'class' => "mystickyelement_poptin_icon",
											'tooltip'	=> 'Copy your Poptin popup link from "On-click", from Display Rules. Check the <a href="https://premio.io/help/mystickyelements/how-to-launch-a-poptin-pop-up-in-my-sticky-elements/" target="_blank">documentation</a> for more. E.g., <a href="https://app.popt.in/APIRequest/click/96Y4a02XXa15e" target="_blank">https://app.popt.in/APIRequest/click/96Y4a02XXa15e</a>',
											'icon_color' => 1
										),
							'wechat'	=> array(
											'text' => "WeChat",
											'icon_text' => "",
											'hover_text' => "WeChat",
											'background_color' => "#00AD19",
											'placeholder'	=> 'Enter weChat ID. E.g., cocacola',
											'class' => "fab fa-weixin",
											'tooltip'	=> "Enter the weChat ID of the profile you want to add. You will usually find the 'WeChat ID' written next to the avatar photo of the profile",
											'icon_color' => 1
										),	
							'snapchat'	=> array(
											'text' => "Snapchat",
											'icon_text' => "",
											'hover_text' => "Snapchat",
											'background_color' => "#fffc00",
											'placeholder'	=> 'Example: Enter your Snapchat Username',
											'class' => "fab fa-snapchat-ghost",
											'tooltip'	=> 'Enter your Snapchat username. E.g., username',
											'icon_color' => 1
										),
							
							'twitter'	=> array(
											'text' => "Twitter",
											'icon_text' => "",
											'hover_text' => "Twitter",
											'background_color' => "#1C9DEB",
											'placeholder'	=> 'Example: https://twitter.com/cocacola',
											'class' => "fab fa-twitter",
											'tooltip'	=> 'Add the link of of your Twitter profile E.g., <a href="https://twitter.com/cocacola" target="_blank">https://twitter.com/cocacola</a>',
											'icon_color' => 1
										),
							'linkedin'	=> array(
											'text' => "Linkedin",
											'icon_text' => "",
											'hover_text' => "Linkedin",
											'background_color' => "#0077b5",
											'placeholder'	=> 'Example: https://linkedin.com/in/username',
											'class' => "fab fa-linkedin-in",
											'tooltip'	=> 'Enter the full link of your LinkedIn profile. E.g., <a href="https://linkedin.com/in/username" target="_blank">https://linkedin.com/in/username</a>',
											'icon_color' => 1
										),
							'viber'	=> array(
											'text' => "Viber",
											'icon_text' => "",
											'hover_text' => "Viber",
											'background_color' => "#59267c",
											'placeholder'	=> 'Example: +1507854875',
											'class' => "fab fa-viber",
											'tooltip'	=> 'Enter your full phone number that you registered with Viber. E.g., +1507854875',
											'number_validation' => 1,
											'icon_color' => 1
										),
							'tiktok'	=> array(
											'text' => "Tiktok",
											'icon_text' => "",
											'hover_text' => "Tiktok",
											'background_color' => "#000",
											'placeholder'	=> 'Example: @TikTok_username',
											'class' => "fab fa-tiktok",
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'vimeo'	=> array(
											'text' => "Vimeo",
											'icon_text' => "",
											'hover_text' => "Vimeo",
											'background_color' => "#1ab7ea",
											'placeholder'	=> 'Example: https://vimeo.com/channel-name',
											'class' => "fab fa-vimeo-v",
											'tooltip'	=> 'Add your Vimeo channel link. E.g., <a href="https://vimeo.com/channel-name" target="_blank">https://vimeo.com/channel-name</a>',
											'icon_color' => 1,
										),
							'pinterest'	=> array(
											'text' => "Pinterest",
											'icon_text' => "",
											'hover_text' => "Pinterest",
											'background_color' => "#E85F65",
											'placeholder'	=> 'Example: https://pinterest/username',
											'class' => "fab fa-pinterest-p",
											'tooltip'	=> 'Add the link of of your Pinterest profile E.g., <a href="https://pinterest.com/username" target="_blank">https://pinterest.com/username</a>',
											'icon_color' => 1
										),
							'line'	=> array(
											'text' => "Line",
											'icon_text' => "",
											'hover_text' => "Line",
											'background_color' => "#00c300",
											'placeholder'	=> 'Example: http://line.me/ti/p/2a-s5A2B8B',
											'class' => "mystickyelement_line_icon",
											'tooltip'	=> 'Add your full profile link of Line. E.g., <a href="http://line.me/ti/p/2a-s5A2B8B" target="_blank">http://line.me/ti/p/2a-s5A2B8B</a>',
											'icon_color' => 1,
											'custom_svg_icon'	=> file_get_contents( MYSTICKYELEMENTS_PATH . '/images/line-logo.svg')
										),
							'itunes'	=> array(
											'text' => "iTunes",
											'icon_text' => "",
											'hover_text' => "iTunes",
											'background_color' => "#495057",
											'placeholder'	=> 'Example: https://www.apple.com/us/itunes/channel-link',
											'class' => "fab fa-itunes-note",
											'tooltip'	=> 'Add your iTunes channel link. E.g., <a href="https://www.apple.com/us/itunes/channel-link" target="_blank">https://www.apple.com/us/itunes/channel-link</a>',
											'icon_color' => 1
										),
							'SoundCloud'	=> array(
											'text' => "SoundCloud",
											'icon_text' => "",
											'hover_text' => "SoundCloud",
											'background_color' => "#ff5500",
											'placeholder'	=> 'Example: https://soundcloud.com/channel-link',
											'class' => "fab fa-soundcloud",
											'tooltip'	=> 'Add your SoundCloud channel link. E.g., <a href="https://soundcloud.com/channel-link" target="_blank">https://soundcloud.com/channel-link</a>',
											'icon_color' => 1
										),
							'vk'	=> array(
											'text' => "Vkontakte",
											'icon_text' => "",
											'hover_text' => "Vkontakte",
											'background_color' => "#4a76a8",
											'placeholder'	=> 'Enter your Vk username. If "vk.com/example" is the URL, username is "example"',
											'class' => "fab fa-vk",
											'tooltip'	=> 'Username for the VK account part of the web page address, for "vk.com/example" the username is "example". Only enter the username',
											'icon_color' => 1
										),
							'spotify'	=> array(
											'text' => "Spotify",
											'icon_text' => "",
											'hover_text' => "Spotify",
											'background_color' => "#ff5500",
											'placeholder'	=> 'Example: https://www.spotify.com/channel-link',
											'class' => "fab fa-spotify",
											'tooltip'	=> 'Add your Spotify channel link. E.g., <a href="https://www.spotify.com/channel-link" target="_blank">https://www.spotify.com/channel-link</a>',
											'icon_color' => 1
										),			
							'tumblr'	=> array(
											'text' => "Tumblr",
											'icon_text' => "",
											'hover_text' => "Tumblr",
											'background_color' => "#35465d",
											'placeholder'	=> 'Example: https://www.tumblr.com/channel-link',
											'class' => "fab fa-tumblr",
											'tooltip'	=> 'Add your full profile link of Tumblr. E.g, <a href="https://www.tumblr.com/channel-link" target="_blank">https://www.tumblr.com/channel-link</a>',
											'icon_color' => 1
										),
							'qzone'		=> array(
											'text' => "Qzone",
											'icon_text' => "",
											'hover_text' => "Qzone",
											'background_color' => "#1a87da",
											'placeholder'	=> 'Example: https://qzone.qq.com/channel-link',
											'class' => "mystickyelement_qzone_icon",
											'tooltip'	=> 'Add your full profile link of Qzone. E.g, <a href="https://qzone.qq.com/channel-link" target="_blank">https://qzone.qq.com/channel-link</a>',
											'icon_color' => 1,
											'custom_svg_icon'	=> file_get_contents( MYSTICKYELEMENTS_PATH . '/images/qzone-logo.svg')
										),
							'qq'		=> array(
											'text' => "QQ",
											'icon_text' => "",
											'hover_text' => "QQ",
											'background_color' => "#212529",
											'placeholder'	=> 'Example: Enter your QQ Username',
											'class' => "fab fa-qq",
											'tooltip'	=> 'Enter your QQ username. E.g., username',
											'icon_color' => 1
										),
							'behance'	=> array(
											'text' => "Behance",
											'icon_text' => "",
											'hover_text' => "Behance",
											'background_color' => "#131418",
											'placeholder'	=> 'Example: https://www.behance.net/channel-link',
											'class' => "fab fa-behance",
											'tooltip'	=> 'Add your full profile link of Behance. E.g, <a href="https://www.behance.net/channel-link" target="_blank">https://www.behance.net/channel-link</a>',
											'icon_color' => 1
										),
							'dribbble'	=> array(
											'text' => "Dribbble",
											'icon_text' => "",
											'hover_text' => "Dribbble",
											'background_color' => "#ea4c89",
											'placeholder'	=> 'Example: https://dribbble.com/channel-link',
											'class' => "fab fa-dribbble",
											'tooltip'	=> 'Add your full profile link of Dribble. E.g, <a href="https://dribbble.com/channel-link" target="_blank">https://dribbble.com/channel-link</a>',
											'icon_color' => 1
										),
							'quora'	=> array(
											'text' => "Quora",
											'icon_text' => "",
											'hover_text' => "Quora",
											'background_color' => "#aa2200",
											'placeholder'	=> 'Example: https://www.quora.com/channel-link',
											'class' => "fab fa-quora",
											'tooltip'	=> 'Add your full profile link of Quora. E.g, <a href="https://www.quora.com/channel-link" target="_blank">https://www.quora.com/channel-link</a>',
											'icon_color' => 1
										),
							'yelp'	=> array(
											'text' => "yelp",
											'icon_text' => "",
											'hover_text' => "yelp",
											'background_color' => "#c41200",
											'placeholder'	=> 'Example: https://www.yelp.com/biz/your_business_here',
											'class' => "fab fa-yelp",
											'tooltip'	=> 'Add your Yelp business link. E.g, <a href="https://www.yelp.com/biz/your_business_here" target="_blank">https://www.yelp.com/biz/your_business_here</a>',
											'icon_color' => 1
										),
							'amazon'	=> array(
											'text' => "Amazon",
											'icon_text' => "",
											'hover_text' => "Amazon",
											'background_color' => "#3b7a57",
											'placeholder'	=> 'Example: https://www.amazon.com/your_store_or_product',
											'class' => "mystickyelement_amazon_icon",
											'tooltip'	=> 'Add your Amazon product link. E.g, <a href="https://www.amazon.com/your_store_or_product" target="_blank">https://www.amazon.com/your_store_or_product</a>',
											'icon_color' => 1
										),
							'reddit'	=> array(
											'text' => "Reddit",
											'icon_text' => "",
											'hover_text' => "Reddit",
											'background_color' => "#FF4301",
											'placeholder'	=> 'Example: https://www.reddit.com/r/your_community',
											'class' => "fab fa-reddit-alien",
											'tooltip'	=> 'Add your Reddit community or profile link. E.g, <a href="https://www.reddit.com/r/your_community" target="_blank">https://www.reddit.com/r/your_community</a>',
											'icon_color' => 1
										),
							'RSS'	=> array(
											'text' => "RSS",
											'icon_text' => "",
											'hover_text' => "RSS",
											'background_color' => "#ee802f",
											'placeholder'	=> 'Example: https://www.example.com/your_rss_feed',
											'class' => "fa fa-rss",
											'tooltip'	=> 'Add your RSS feed link. E.g, <a href="https://www.example.com/your_rss_feed" target="_blank">https://www.example.com/your_rss_feed</a>',
											'icon_color' => 1
										),
							'flickr'	=> array(
											'text' => "Flickr",
											'icon_text' => "",
											'hover_text' => "Flickr",
											'background_color' => "#ff0084",
											'placeholder'	=> 'Example: https://www.flickr.com/photos/your_profile',
											'class' => "mystickyelement_flickr_icon",
											'tooltip'	=> 'Add your full profile link of Flickr E.g, <a href="https://www.flickr.com/photos/your_profile" target="_blank">https://www.flickr.com/photos/your_profile</a>',
											'icon_color' => 1
										),
							'ebay'	=> array(
											'text' => "eBay",
											'icon_text' => "",
											'hover_text' => "eBay",
											'background_color' => "#000000",
											'placeholder'	=> 'Example: https://www.ebay.com/str/your_store',
											'class' => "mystickyelement_ebay_icon",
											'tooltip'	=> 'Add your eBay profile/product link. E.g, <a href="https://www.ebay.com/str/your_store" target="_blank">https://www.ebay.com/str/your_store</a>',
											'icon_color' => 1
										),
							'etsy'	=> array(
											'text' => "Etsy",
											'icon_text' => "",
											'hover_text' => "Etsy",
											'background_color' => "#eb6d20",
											'placeholder'	=> 'Example: https://www.etsy.com/shop/your_shop',
											'class' => "fab fa-etsy",
											'tooltip'	=> 'Add your full shop link of Etsy. E.g, <a href="https://www.etsy.com/shop/your_shop" target="_blank">https://www.etsy.com/shop/your_shop</a>',
											'icon_color' => 1
										),
							'slack'	=> array(
											'text' => "Slack",
											'icon_text' => "",
											'hover_text' => "Slack",
											'background_color' => "#3f0e40",
											'placeholder'	=> 'Example: https://your_workspace.slack.com/',
											'class' => "mystickyelement_slack_icon",
											'tooltip'	=> 'Add your Slack workspace link. E.g, <a href="https://your_workspace.slack.com/" target="">https://your_workspace.slack.com/</a>',
											'icon_color' => 1
										),
							'trip_advisor'	=> array(
											'text' => "Trip Advisor",
											'icon_text' => "",
											'hover_text' => "Trip Advisor",
											'background_color' => "#00af87",
											'placeholder'	=> 'Example: https://www.tripadvisor.com/your_place',
											'class' => "fab fa-tripadvisor",
											'tooltip'	=> 'Add your place link of TripAdvisor E.g, <a href="https://www.tripadvisor.com/your_place" target="_blank">https://www.tripadvisor.com/your_place</a>',
											'icon_color' => 1
										),
							'medium'	=> array(
											'text' => "Medium",
											'icon_text' => "",
											'hover_text' => "Medium",
											'background_color' => "#0000cd",
											'placeholder'	=> 'Example: https://medium.com/your_publication',
											'class' => "fab fa-medium",
											'tooltip'	=> 'Add your full profile link of Medium. E.g, <a href="https://medium.com/your_publication" target="">https://medium.com/your_publication</a>',
											'icon_color' => 1
										),
							'google_play'	=> array(
											'text' => "Google Play",
											'icon_text' => "",
											'hover_text' => "Google Play",
											'background_color' => "#747474",
											'placeholder'	=> 'Example: https://play.google.com/store/apps/details?id=your_app',
											'class' => "mystickyelement_google_play_icon",
											'tooltip'	=> 'Add your Google Play link. E.g, <a href="https://play.google.com/store/apps/details?id=your_app" target="_blank">https://play.google.com/store/apps/details?id=your_app</a>',
											'icon_color' => 1
										),
							'app_store'	=> array(
											'text' => "App Store (apple)",
											'icon_text' => "",
											'hover_text' => "App Store (apple)",
											'background_color' => "#1d77f2",
											'placeholder'	=> 'Example: https://apps.apple.com/app/your_app',
											'class' => "fab fa-app-store",
											'tooltip'	=> 'Add your Apple Appstore link. E.g, <a href="https://apps.apple.com/app/your_app" target="_blank">https://apps.apple.com/app/your_app</a>',
											'icon_color' => 1
										),
							'fiverr'	=> array(
											'text' => "Fiverr",
											'icon_text' => "",
											'hover_text' => "Fiverr",
											'background_color' => "#00b22d",
											'placeholder'	=> 'Example: https://www.fiverr.com/your_profile',
											'class' => "mystickyelement_fiverr_icon",
											'tooltip'	=> 'Add your Fiverr profile link. E.g, <a href="https://www.fiverr.com/your_profile" target="_blank">https://www.fiverr.com/your_profile</a>',
											'icon_color' => 1
										),
							'shopify'	=> array(
											'text' => "Shopify",
											'icon_text' => "",
											'hover_text' => "Shopify",
											'background_color' => "#96BF47",
											'placeholder'	=> 'Example: http://your_storemyshopify.com/',
											'class' => "mystickyelement_shopify_icon",
											'tooltip'	=> 'Add your Shopify store or product link. E.g, <a href="http://your_storemyshopify.com/" target="_blank">http://your_storemyshopify.com/</a>',
											'icon_color' => 1
										),
							'printful'	=> array(
											'text' => "Printful",
											'icon_text' => "",
											'hover_text' => "Printful",
											'background_color' => "#000",
											'placeholder'	=> 'Example: https://www.printful.com/your_prudct',
											'class' => "mystickyelement_printful_icon",
											'tooltip'	=> 'Add your Printful product link. E.g, <a href="https://www.printful.com/your_prudct" target="_blank">https://www.printful.com/your_prudct</a>',
											'icon_color' => 1
										),
							'gumroad'	=> array(
											'text' => "Gumroad",
											'icon_text' => "",
											'hover_text' => "Gumroad",
											'background_color' => "#36a9ae",
											'placeholder'	=> 'Example: https://gumroad.com/your_profile',
											'class' => "mystickyelement_gumroad_icon",
											'tooltip'	=> 'Add your Gumroad product link. E.g, <a href="https://gumroad.com/your_profile" target="_blank">https://gumroad.com/your_profile</a>',
											'icon_color' => 1
										),
							'ok'		=> array(
											'text' => "OK.ru",
											'icon_text' => "",
											'hover_text' => "OK.ru",
											'background_color' => "#F6902C",
											'placeholder'	=> 'Example: https://ok.ru/your_proflie',
											'class' => "fab fa-odnoklassniki",
											'tooltip'	=> 'Add your full profile link of Ok.ru. E.g, <a href="https://ok.ru/your_proflie" target="_blank">https://ok.ru/your_proflie</a>',
											'icon_color' => 1
										),
							
							'custom_one'	=> array(
											'text' => "Custom Link 1",
											'custom_tooltip' => "Custom Link 1",
											'icon_text' => "",
											'hover_text' => "Custom Link 1",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your custom social link',
											'class' => "fas fa-cloud-upload-alt",
											'custom'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_two'	=> array(
											'text' => "Custom Link 2",
											'custom_tooltip' => "Custom Link 2",
											'hover_text' => "Custom Link 2",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your custom social link',
											'class' => "fas fa-cloud-upload-alt",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_three'	=> array(
											'text' => "Custom Link 3",
											'custom_tooltip' => "Custom Link 3",
											'icon_text' => "",
											'hover_text' => "Custom Link 3",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your custom social link',
											'class' => "fas fa-cloud-upload-alt",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_four'	=> array(
											'text' => "Custom Link 4",
											'custom_tooltip' => "Custom Link 4",
											'icon_text' => "",
											'hover_text' => "Custom Link 4",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your custom social link',
											'class' => "fas fa-cloud-upload-alt",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_five'	=> array(
											'text' => "Custom Link 5",
											'custom_tooltip' => "Custom Link 5",
											'icon_text' => "",
											'hover_text' => "Custom Link 5",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your custom social link',
											'class' => "fas fa-cloud-upload-alt",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_six'	=> array(
											'text' => "Custom Link 6",
											'custom_tooltip' => "Custom Link 6",
											'icon_text' => "",
											'hover_text' => "Custom Link 6",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your custom social link',
											'class' => "fas fa-cloud-upload-alt",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_seven'	=> array(
											'text' => "Custom Shortcode/HTML 1",
											'custom_tooltip' => "Custom Shortcode/HTML 1",
											'icon_text' => "",
											'hover_text' => "Custom Shortcode/HTML 1",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your shortcode or custom IFRAME/HTML code',
											'class' => "fas fa-code",
											'custom'	=> 1,
											'custom_html'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_eight'	=> array(
											'text' => "Custom Shortcode/HTML 2",
											'custom_tooltip' => "Custom Shortcode/HTML 2",
											'icon_text' => "",
											'hover_text' => "Custom Shortcode/HTML 2",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your shortcode or custom IFRAME/HTML code',
											'class' => "fas fa-code",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'custom_html'	=> 1,
											'icon_color' => 1,
											'tooltip'	=> ''
										),
							'custom_nine'	=> array(
											'text' => "Custom Shortcode/HTML 3",
											'custom_tooltip' => "Custom Shortcode/HTML 3",
											'icon_text' => "",
											'hover_text' => "Custom Shortcode/HTML 3",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your shortcode or custom IFRAME/HTML code',
											'class' => "fas fa-code",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'custom_html'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_ten'	=> array(
											'text' => "Custom Shortcode/HTML 4",
											'custom_tooltip' => "Custom Shortcode/HTML 4",
											'icon_text' => "",
											'hover_text' => "Custom Shortcode/HTML 4",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your shortcode or custom IFRAME/HTML code',
											'class' => "fas fa-code",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'custom_html'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_eleven'	=> array(
											'text' => "Custom Shortcode/HTML 5",
											'custom_tooltip' => "Custom Shortcode/HTML 5",
											'icon_text' => "",
											'hover_text' => "Custom Shortcode/HTML 5",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your shortcode or custom IFRAME/HTML code',
											'class' => "fas fa-code",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'custom_html'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_twelve'	=> array(
											'text' => "Custom Shortcode/HTML 6",
											'custom_tooltip' => "Custom Shortcode/HTML 6",
											'icon_text' => "",
											'hover_text' => "Custom Shortcode/HTML 6",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your shortcode or custom IFRAME/HTML code',
											'class' => "fas fa-code",
											'is_locked'	=> 1,
											'custom'	=> 1,
											'custom_html'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
						);

		return apply_filters( 'mystickyelements_social_channels_info',  $social_channels);
	}
}




function mystickyelements_custom_social_channels(){
	$social_channels = array(
							'custom_channel' => array(
											'text' => "Custom Link",
											'custom_tooltip' => "Custom Link",
											'icon_text' => "",
											'hover_text' => "Custom Link",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your custom social link',
											'class' => "fas fa-cloud-upload-alt",
											'is_locked'	=> 0,
											'custom'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
							'custom_shortcode' => array(
											'text' => "Custom Shortcode/HTML",
											'custom_tooltip' => "Custom Shortcode/HTML",
											'icon_text' => "",
											'hover_text' => "Custom Shortcode/HTML",
											'background_color' => "#7761DF",
											'placeholder'	=> 'Enter your shortcode or custom IFRAME/HTML code',
											'class' => "fas fa-code",
											'custom'	=> 1,
											'custom_html'	=> 1,
											'tooltip'	=> '',
											'icon_color' => 1
										),
										
						);
	return $social_channels;
}
function mystickyelements_admin_notices(){

	if ( isset($_GET['page']) && $_GET['page'] == 'my-sticky-elements-settings' ) {


		$activation_url = wp_nonce_url( 'options-general.php?page=my-sticky-elements-settings&update_version=1', 'mystickyelement_new_version', 'update_nonce');
		$admin_message = '<p>' . sprintf( '<a href="%s" id="mystickyelement-update" class="button-primary">%s</a>', $activation_url, esc_html__( 'Move to the new version', 'mystickyelements' ) ) . '</p>';

		echo '<div class="updated settings-error notice is-dismissible">' . $admin_message . '</div>';
	}
}

function mystickyelements_admin_init_update_version(){

	if (isset($_GET['update_version']) && $_GET['update_version'] == 1) {
		if (isset($_GET['update_nonce']) && wp_verify_nonce($_GET['update_nonce'], 'mystickyelement_new_version')) {
			delete_option( 'mysticky_elements_options');
			delete_option( 'mysticky_elements_options0');
			delete_option( 'mysticky_elements_options1');
			delete_option( 'mysticky_elements_options2');
			delete_option( 'mysticky_elements_options3');
			delete_option( 'mysticky_elements_options4');
			delete_option( 'mysticky_elements_options5');
			delete_option( 'mysticky_elements_options6');
			delete_option( 'mysticky_elements_options7');
			delete_option( 'mysticky_elements_options8');
			delete_option( 'mysticky_elements_options9');
			mystickyelement_activate();
			wp_redirect( admin_url( 'admin.php?page=my-sticky-elements-settings' ) ) ;
			exit;
		}
	}
}
add_action( 'admin_init', 'mystickyelements_admin_init_update_version');



/*
 * Check condition for older user
 *
 */
$mysticky_elements_options = get_option( 'mysticky_elements_options' );
if ( !empty($mysticky_elements_options)) {
	if( is_admin() ) {
		$my_settings_page = new MyStickyElementsPage();
	}

	add_action('wp_head', 'mysticky_element_build_stylesheet_content');
	add_action( 'wp_enqueue_scripts', 'mystickyelements_script' );
	add_action('wp_footer', 'insert_my_footerrr');

	add_action('admin_notices', 'mystickyelements_admin_notices');

} else {

	add_action( 'admin_init' , 'mystickyelements_admin_init' );

	require_once MYSTICKYELEMENTS_PATH . 'mystickyelements-fonts.php';
	require_once MYSTICKYELEMENTS_PATH . 'mystickyelements-fontawesome-icons.php';
	require_once MYSTICKYELEMENTS_PATH . 'mystickyelements-admin.php';
	require_once MYSTICKYELEMENTS_PATH . 'mystickyelements-front.php';
	require_once MYSTICKYELEMENTS_PATH . 'includes/class-affiliate.php';
}



function mystickyelements_admin_init() {
	global $wpdb, $pagenow;

	if ( $pagenow == 'plugins.php'  || ( isset($_GET['page']) && $_GET['page'] == 'my-sticky-elements' ) ) {

		$field_check = $wpdb->get_var( "SHOW COLUMNS FROM {$wpdb->prefix}mystickyelement_contact_lists LIKE 'contact_option'" );
		if ( 'contact_option' != $field_check ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}mystickyelement_contact_lists ADD contact_option VARCHAR(255) NULL DEFAULT NULL" );
		}

		$field_check = $wpdb->get_var( "SHOW COLUMNS FROM {$wpdb->prefix}mystickyelement_contact_lists LIKE 'message_date'" );
		if ( 'message_date' != $field_check ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}mystickyelement_contact_lists ADD message_date DATETIME NOT NULL default '0000-00-00 00:00:00'" );
		}
		
		$field_check = $wpdb->get_var( "SHOW COLUMNS FROM {$wpdb->prefix}mystickyelement_contact_lists LIKE 'page_link'" );
		if ( 'page_link' != $field_check ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}mystickyelement_contact_lists ADD page_link TEXT NULL DEFAULT NULL" );
		}
	}
	
	if ( !get_option( 'mystickyelements-widgets' ) && !get_option( 'mystickyelements-update_2_0')) {		
		update_option( 'mystickyelements-widgets', array('MyStickyElements #1'));
		update_option( 'mystickyelements-update_2_0', true);
	}
}