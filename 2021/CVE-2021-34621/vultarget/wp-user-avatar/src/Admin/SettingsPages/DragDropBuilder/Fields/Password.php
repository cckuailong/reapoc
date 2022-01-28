<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class Password extends FieldBase
{
    public function field_type()
    {
        return $this->tag_name . '-password';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-lock"></span>';
    }

    public function field_title()
    {
        return esc_html__('Password', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return apply_filters('ppress_form_builder_password_field_settings', [
            parent::GENERAL_TAB => [
                'placeholder' => [
                    'label' => esc_html__('Placeholder', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD
                ]
            ],
            parent::STYLE_TAB   => [
                'class' => [
                    'label'       => esc_html__('CSS Classes', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => esc_html__('Enter the CSS class names you would like to add to this field.', 'wp-user-avatar')
                ]
            ],
        ], $this);
    }
}