<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class SelectDropdown extends FieldBase
{
    public function field_type()
    {
        return $this->tag_name . '-select-dropdown';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-arrow-down"></span>';
    }

    public function field_title()
    {
        return esc_html__('Dropdown', 'wp-user-avatar');
    }

    public function category()
    {
        return parent::EXTRA_CATEGORY;
    }

    public function field_settings()
    {
        return apply_filters('ppress_form_builder_select_dropdown_field_settings', [
            parent::GENERAL_TAB  => [
                'label' => [
                    'label' => esc_html__('Label', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD,
                ],
                'key'         => [
                    'label'       => esc_html__('Field Key', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => ppress_dnd_field_key_description()
                ],
                'is_multiple' => [
                    'label'       => esc_html__('Multiple Selection', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'type'        => 'checkbox',
                    'description' => esc_html__('Check to allow multiple options to be selected.', 'wp-user-avatar')
                ],
                'options'     => [
                    'label'       => esc_html__('Options', 'wp-user-avatar'),
                    'field'       => self::TEXTAREA_FIELD,
                    'description' => esc_html__('Enter one choice per line. This will be the options available for user to select.', 'wp-user-avatar')
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