<?php

namespace ProfilePress\Core\Admin\SettingsPages;

// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

class AdminFooter
{
    public function __construct()
    {
        add_filter('admin_footer_text', [$this, 'admin_rate_us']);
    }

    /**
     * Add rating links to the admin dashboard
     *
     * @param       string $footer_text The existing footer text
     *
     * @return      string
     */
    public function admin_rate_us($footer_text)
    {
        if (ppress_is_admin_page()) {
            $rate_text = sprintf(__('Thank you for using <a href="%1$s" target="_blank">ProfilePress</a>! Please <a href="%2$s" target="_blank">rate us ★★★★★</a> on <a href="%2$s" target="_blank">WordPress.org</a> to help us spread the word.', 'wp-user-avatar'),
                'https://profilepress.net',
                'https://wordpress.org/support/view/plugin-reviews/wp-user-avatar?filter=5#postform'
            );

            $footer_text = '<span>' . $rate_text . '</span>';
        }

        return $footer_text;
    }

    /**
     * @return AdminFooter
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
