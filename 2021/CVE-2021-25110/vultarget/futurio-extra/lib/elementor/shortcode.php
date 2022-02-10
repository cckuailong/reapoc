<?php

/**
 * Shortcode for elementor
 *
 * Based on plugin https://wordpress.org/plugins/anywhere-elementor/
 *
 * @since 1.0.0
 */

namespace Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Futurio_Extra_Shortcode {

    const SHORTCODE = 'elementor-template';

    public function __construct() {
        $this->add_actions();
    }

    public function admin_columns_headers($defaults) {
        $defaults['shortcode'] = esc_html__('Shortcode', 'futurio-extra');

        return $defaults;
    }

    public function admin_columns_content($column_name, $post_id) {
        if ('shortcode' === $column_name) {
            // %s = shortcode, %d = post_id
            $shortcode = esc_attr(sprintf('[%s id="%d"]', self::SHORTCODE, $post_id));
            printf('<input class="widefat" type="text" readonly onfocus="this.select()" value="%s" />', $shortcode);
        }
    }

    public function shortcode($attributes = []) {
        if (!class_exists('Elementor\Plugin')) {
            return '';
        }
        if (empty($attributes['id'])) {
            return '';
        }

        $response = Plugin::instance()->frontend->get_builder_content_for_display($attributes['id']);
        return $response;
    }

    public function css_head() {

        if (class_exists('\Elementor\Core\Files\CSS\Post')) {
            $blog_id = get_theme_mod('custom_blog_feed', '');
            $error_id = get_theme_mod('custom_404_page', '');
            $header_id = get_theme_mod('custom_header', '');
            $footer_id = get_theme_mod('custom_footer', '');

            if ($blog_id != '') {
                $blog_css = new \Elementor\Core\Files\CSS\Post($blog_id);
                $blog_css->enqueue();
            }
            if ($error_id != '') {
                $error_css = new \Elementor\Core\Files\CSS\Post($error_id);
                $error_css->enqueue();
            }
            if ($header_id != '') {
                $header_css = new \Elementor\Core\Files\CSS\Post($header_id);
                $header_css->enqueue();
            }
            if ($footer_id != '') {
                $footer_css = new \Elementor\Core\Files\CSS\Post($footer_id);
                $footer_css->enqueue();
            }
        }
    }

    private function add_actions() {
        if (is_admin()) {
            add_action('manage_elementor_library_posts_columns', [$this, 'admin_columns_headers']);
            add_action('manage_elementor_library_posts_custom_column', [$this, 'admin_columns_content'], 10, 2);
        }

        add_shortcode(self::SHORTCODE, [$this, 'shortcode']);

        add_action('wp_enqueue_scripts', [$this, 'css_head']);
    }

}

new Futurio_Extra_Shortcode();
