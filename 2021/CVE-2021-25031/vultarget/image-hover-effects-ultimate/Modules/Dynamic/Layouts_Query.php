<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Layouts_Query
 *
 * @author biplo
 */
class Layouts_Query {

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

    /**
     * Database Import Table
     *
     * @since 9.3.0
     */
    public $import_table;

    /**
     * Database Import Table
     *
     * @since 9.3.0
     */
    public $child_table;

    public function __construct($function = '', $rawdata = '', $args = '', $optional = '') {
        if (!empty($function) && !empty($rawdata)):
            global $wpdb;
            $this->wpdb = $wpdb;
            $this->parent_table = $this->wpdb->prefix . 'image_hover_ultimate_style';
            $this->child_table = $this->wpdb->prefix . 'image_hover_ultimate_list';
            return $this->$function($rawdata, $args, $optional);
        endif;
    }

    public function __rest_api_post($style, $args, $optional) {

        if (!is_array($args)):
            $args = json_decode(stripslashes($args), true);
        endif;
        $args ['offset'] = (int) $args['offset'] + (((int) $optional - 1) * (int) $args['posts_per_page']);

        if (!is_array($style)):
            $style = json_decode(stripslashes($style), true);
        endif;
        $rawdata = $this->wpdb->get_row($this->wpdb->prepare('SELECT * FROM ' . $this->parent_table . ' WHERE id = %d ', $style['display_post_id']), ARRAY_A);

        return $this->layouts_query($rawdata, $args, $style);
    }

    public function layouts_query($dbdata, $args, $style) {
        $postdata = $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM $this->child_table WHERE styleid = %d LIMIT %d, %d", $dbdata['id'], $args['offset'], $args['posts_per_page']), ARRAY_A);

        if (count($postdata) != $args['posts_per_page']):
            echo 'Image Hover Empty Data';
        elseif (count($postdata) == 0):
            echo 'Image Hover Empty Data';
        endif;

        $StyleName = explode('-', ucfirst($dbdata['style_name']));
        $cls = '\OXI_IMAGE_HOVER_PLUGINS\Modules\\' . $StyleName[0] . '\Render\Effects' . $StyleName[1];
        new $cls($dbdata, $postdata, 'request');
    }

}
