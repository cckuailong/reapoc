<?php

namespace ProfilePress\Core\Widgets;

class Init
{
    public static function init()
    {
        add_action('widgets_init', function () {
            register_widget(__NAMESPACE__ . '\Form');
            register_widget(__NAMESPACE__ . '\TabbedWidget');
            register_widget(__NAMESPACE__ . '\UserPanel');
        });
    }
}