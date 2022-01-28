<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\EditProfile;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class ShowProfilePicture extends FieldBase
{
    public function field_type()
    {
        return 'pp-user-avatar';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-id"></span>';
    }

    public function field_title()
    {
        return esc_html__('Show Profile Picture', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return [
            parent::GENERAL_TAB => [
                'size' => [
                    'label' => esc_html__('Size', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD,
                    'type'  => 'number'
                ],
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