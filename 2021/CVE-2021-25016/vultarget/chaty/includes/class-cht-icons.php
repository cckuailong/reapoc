<?php
/**
 * Class CHT_Icons *
 *
 * @since 1.0
 */

namespace CHT\includes;
if (!defined('ABSPATH')) {
    exit;
}
class CHT_Widget
{
    protected $plugin_slug = 'chaty-app';
    protected $friendly_name = 'Chaty Widget';

    protected static $instance = null;

    public function __construct()
    {
    }

    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function get_plugin_slug()
    {
        return $this->plugin_slug;
    }

    public function get_name()
    {
        return $this->friendly_name;
    }
}