<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class PasswordStrengthMeter extends FieldBase
{
    public function field_type()
    {
        return $this->tag_name . '-password-meter';
    }

    public static function field_icon()
    {
        return '<img src="' . PPRESS_ASSETS_URL . '/images/admin/car.svg">';
    }

    public function field_title()
    {
        return esc_html__('Password Strength', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return apply_filters('ppress_form_builder_password_strength_meter_field_settings', [
            parent::SETTINGS_TAB => [
                'enforce' => [
                    'type'        => 'checkbox',
                    'label'       => esc_html__('Enforce', 'wp-user-avatar'),
                    'description' => esc_html__('Prevent registration unless users entered password is strong.', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                ]
            ],
            parent::STYLE_TAB    => [
                'class' => [
                    'label'       => esc_html__('CSS Classes', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => esc_html__('Enter the CSS class names you would like to add to this field.', 'wp-user-avatar')
                ]
            ],
        ], $this);
    }
}