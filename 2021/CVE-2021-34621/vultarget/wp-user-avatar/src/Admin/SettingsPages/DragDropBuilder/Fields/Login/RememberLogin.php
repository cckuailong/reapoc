<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\Login;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class RememberLogin extends FieldBase
{
    public function field_type()
    {
        return 'login-remember';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-clock"></span>';
    }

    public function field_title()
    {
        return esc_html__('Remember Login', 'wp-user-avatar');
    }

    public function field_bar_title()
    {
        return esc_html__('Remember Me', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return apply_filters('ppress_form_builder_login_remember_login_field_settings', [
            parent::GENERAL_TAB => [
                'label' => [
                    'label' => esc_html__('Label', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD,

                ]
            ],
            parent::STYLE_TAB   => [
                'class' => [
                    'label'       => esc_html__('CSS Classes', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => esc_html__('Enter the CSS class names you would like to add to this field.', 'wp-user-avatar')
                ]
            ]
        ], $this);
    }
}