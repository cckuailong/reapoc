<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\UserProfile;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class Email extends FieldBase
{
    public function field_type()
    {
        return 'profile-email';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-email"></span>';
    }

    public function field_title()
    {
        return esc_html__('Email Address', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return [
            parent::GENERAL_TAB => [
                'label' => [
                    'label' => esc_html__('Title', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD
                ]
            ]
        ];
    }
}