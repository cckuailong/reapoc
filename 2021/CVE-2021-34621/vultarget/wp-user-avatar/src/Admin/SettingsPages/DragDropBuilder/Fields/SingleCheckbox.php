<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class SingleCheckbox extends FieldBase
{
    public function field_type()
    {
        return $this->tag_name . '-single-checkbox';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-yes"></span>';
    }

    public function field_title()
    {
        return esc_html__('Single Checkbox', 'wp-user-avatar');
    }

    public function category()
    {
        return parent::EXTRA_CATEGORY;
    }

    public function field_settings()
    {
        return apply_filters('ppress_form_builder_checkbox_field_settings', [
            parent::GENERAL_TAB  => [
                'key'                  => [
                    'label'       => esc_html__('Field Key', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => ppress_dnd_field_key_description()
                ],
                'checkbox_text' => [
                    'label' => esc_html__('Checkbox Text', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD
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