<?php

namespace MEC\Notifications;

class DisplayNotificationSettings{

    public function output($atts){

        $default = array(
            'class' => '',
            'group_id' => '',
            'base_field_name' => '',
            'section_title' => '',
            'enable_options_text' => '',
            'enable_options_description' => '',
            'placeholders' => [],
            'options' => '',
        );

        $atts = wp_parse_args( $atts, $default );

        $atts = apply_filters( 'mec_display_notification_settings_atts', $atts );

        include __DIR__ .'/template-notification-settings.php';
    }
}