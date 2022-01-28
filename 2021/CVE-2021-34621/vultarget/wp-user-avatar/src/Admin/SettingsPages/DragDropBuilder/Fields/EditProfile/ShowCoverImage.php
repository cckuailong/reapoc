<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\EditProfile;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class ShowCoverImage extends FieldBase
{
    public function field_type()
    {
        return 'pp-user-cover-image';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-cover-image"></span>';
    }

    public function field_title()
    {
        return esc_html__('Show Cover Image', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return [
            parent::GENERAL_TAB => [
                'alt'  => [
                    'label' => esc_html__('Alt Text', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD
                ]
            ],
            parent::STYLE_TAB   => [
                'class' => [
                    'label'       => esc_html__('CSS Classes', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => esc_html__('Enter the CSS class names you would like to add to this field.', 'wp-user-avatar')
                ]
            ]
        ];
    }
}