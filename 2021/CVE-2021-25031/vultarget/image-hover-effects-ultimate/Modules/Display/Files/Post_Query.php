<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Display\Files;

if (!defined('ABSPATH')) {
    exit;
}

trait Post_Query {

    public function post_type() {
        return get_post_types(array('public' => true, 'show_in_nav_menus' => true), 'names');
    }

    public function post_author() {
        $us = [];
        $users = get_users();
        if ($users) {
            foreach ($users as $user) {
                $us[$user->ID] = ucfirst($user->display_name);
            }
        }
        return $us;
    }

    public function post_category($type) {
        $cat = [];
        $categories = get_terms(array(
            'taxonomy' => $type == 'post' ? 'category' : $type . '_category',
            'hide_empty' => true,
        ));
        if (empty($categories) || is_wp_error($categories)):
            return [];
        endif;

        foreach ($categories as $categorie) {
            $cat[$categorie->term_id] = ucfirst($categorie->name);
        }
        return $cat;
    }

    public function post_tags($type) {
        $tg = [];
        $tags = get_terms(array(
            'taxonomy' => $type . '_tag',
            'hide_empty' => true,
        ));
        if (empty($tags) || is_wp_error($tags)):
            return [];
        endif;

        foreach ($tags as $tag) {
            $tg[$tag->term_id] = ucfirst($tag->name);
        }

        return $tg;
    }

    public function post_include($type) {
        $post_list = get_posts(array(
            'post_type' => $type,
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1,
        ));
        if (empty($post_list) && is_wp_error($post_list)):
            return [];
        endif;
        $posts = array();
        foreach ($post_list as $post) {
            $posts[$post->ID] = ucfirst($post->post_title);
        }
        return $posts;
    }

    public function post_exclude($type) {
        $post_list = get_posts(array(
            'post_type' => $type,
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1,
        ));
        if (empty($post_list) && is_wp_error($post_list)):
            return [];
        endif;
        $posts = array();
        foreach ($post_list as $post) {
            $posts[$post->ID] = ucfirst($post->post_title);
        }
        return $posts;
    }

    public function thumbnail_sizes() {
        $default_image_sizes = get_intermediate_image_sizes();
        $thumbnail_sizes = array();
        foreach ($default_image_sizes as $size) {
            $image_sizes[$size] = $size . ' - ' . intval(get_option("{$size}_size_w")) . ' x ' . intval(get_option("{$size}_size_h"));
            $thumbnail_sizes[$size] = str_replace('_', ' ', ucfirst($image_sizes[$size]));
        }
        return $thumbnail_sizes;
    }

    public function post_style() {
        $g = 'general%';
        $c = 'general%';
        $s = 'square%';
        $alldata = $this->wpdb->get_results($this->wpdb->prepare("SELECT id, name FROM $this->parent_table WHERE style_name LIKE %s OR style_name LIKE %s OR style_name LIKE %s ORDER by id ASC", $g, $s, $c), ARRAY_A);
        $st = [];
        foreach ($alldata as $k => $value) {
            $st[$value['id']] = $value['name'] != '' ? $value['name'] : 'Shortcode ID ' . $value['id'];
        }

        return $st;
    }

}
