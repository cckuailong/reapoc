<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class SelectRole extends FieldBase
{
    public function field_type()
    {
        return $this->tag_name . '-select-role';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-slides"></span>';
    }

    public function field_title()
    {
        return esc_html__('Select Role', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return apply_filters('ppress_form_builder_select_role_field_settings', [
            parent::GENERAL_TAB  => [
                'label'   => [
                    'label' => esc_html__('Label', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD,
                ],
                'options' => [
                    'label'       => esc_html__('Options', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => esc_html__('Enter comma separated list of roles to include in the select dropdown.', 'wp-user-avatar')
                ]
            ],
            parent::SETTINGS_TAB => [
                'required' => [
                    'type'        => 'checkbox',
                    'label'       => esc_html__('Required', 'wp-user-avatar'),
                    'description' => esc_html__('Force users to fill out this field, otherwise it will be optional.', 'wp-user-avatar'),
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