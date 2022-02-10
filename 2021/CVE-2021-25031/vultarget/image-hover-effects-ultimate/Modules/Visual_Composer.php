<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Visual_Composer
 *
 * @author $biplob018
 */
class Visual_Composer {

    /**
     * Define $wpdb
     *
     * @since 9.3.0
     */
    public $wpdb;

    /**
     * Database Parent Table
     *
     * @since 9.3.0
     */
    public $parent_table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->parent_table = $this->wpdb->prefix . 'image_hover_ultimate_style';
        add_action('vc_before_init', [$this, 'iheu_oxi_VC_extension']);
        add_shortcode('iheu_oxi_VC', [$this, 'iheu_oxi_VC_shortcode']);
    }

    public function iheu_oxi_VC_extension() {
        global $wpdb;
        $data = $wpdb->get_results('SELECT * FROM ' . $this->parent_table . ' ORDER BY id DESC', ARRAY_A);
        $vcdata = array();
        foreach ($data as $value) {
            $vcdata[] = $value['id'];
        }
        vc_map(array(
            "name" => __("Image Hover Ultimate"),
            "base" => "iheu_oxi_VC",
            "category" => __("Content"),
            "params" => array(
                array(
                    "type" => "dropdown",
                    "heading" => "Image Hover Select",
                    "param_name" => "id",
                    "value" => $vcdata,
                    'save_always' => true,
                    "description" => "Select your Image Hover ID",
                    "group" => 'Settings',
                ),
            )
        ));
    }

    public function iheu_oxi_VC_shortcode($atts) {
        extract(shortcode_atts(array(
            'id' => ''
                        ), $atts));
        $styleid = $atts['id'];
        ob_start();
        echo \OXI_IMAGE_HOVER_PLUGINS\Classes\Bootstrap::instance()->shortcode_render($styleid, 'user');
        return ob_get_clean();
    }

}
