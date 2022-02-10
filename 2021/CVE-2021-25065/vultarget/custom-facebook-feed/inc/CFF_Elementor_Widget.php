<?php
namespace CustomFacebookFeed;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use CustomFacebookFeed\Builder\CFF_Db;
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class CFF_Elementor_Widget  extends Widget_Base {

	public function get_name() {
        return 'cff-widget';
    }
    public function get_title() {
        return esc_html__('Custom Facebook Feed', 'booster-addons');
    }
    public function get_icon() {
        return 'fa fa-facebook';
    }
    public function get_categories() {
        return array('smash-balloon');
    }
    public function get_script_depends() {
        return [
            'cffscripts',
            'elementor-preview'
        ];
    }

    protected function register_controls() {
    	/********************************************
                    CONTENT SECTION
        ********************************************/
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Facebook Feed Settings', 'custom-facebook-feed'),
            ]
        );
        $this->add_control(
            'feed_id',
            [
                'label' => esc_html__('Select a Feed', 'custom-facebook-feed'),
                'type' => 'cff_feed_control',
                'label_block' => true,
                'dynamic' => ['active' => true],
                'options' =>  CFF_Db::elementor_feeds_query(),
            ]
        );
        $this->end_controls_section();

    }
     protected function render() {
    	$settings = $this->get_settings_for_display();
    	if( isset($settings['feed_id']) ){
    		$output = do_shortcode( shortcode_unautop( '[custom-facebook-feed feed='.$settings['feed_id'].']' ) );

    	}else{
    		$output = __('Please choose a feed', 'custom-facebook-feed');
    	}

        echo apply_filters('cff_output', $output, $settings);

    }

}