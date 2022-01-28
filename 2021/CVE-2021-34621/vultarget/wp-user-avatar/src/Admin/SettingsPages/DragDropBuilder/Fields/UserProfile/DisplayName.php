<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\UserProfile;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class DisplayName extends FieldBase
{
    public function field_type()
    {
        return 'profile-display-name';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-admin-users"></span>';
    }

    public function field_title()
    {
        return esc_html__('Display Name', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return [
            parent::GENERAL_TAB => [
                'label' => [
                    'label' => esc_html__('Title', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD
                ],
                'format' => [
                    'label' => esc_html__('Format', 'wp-user-avatar'),
                    'field' => self::SELECT_FIELD,
                    'options' => [
                        'display-name'     => esc_html__('Default Display Name', 'wp-user-avatar'),
                        'first_last_names' => esc_html__('First & Last Name', 'wp-user-avatar'),
                        'last_first_names' => esc_html__('Last & First Name', 'wp-user-avatar'),
                        'first_name_initial_l'       => esc_html__('First Name & First Initial of Last Name', 'wp-user-avatar'),
                        'f_initial_last_name'       => esc_html__('First Initial of First Name & Last Name', 'wp-user-avatar')
                    ]
                ]
            ]
        ];
    }
}