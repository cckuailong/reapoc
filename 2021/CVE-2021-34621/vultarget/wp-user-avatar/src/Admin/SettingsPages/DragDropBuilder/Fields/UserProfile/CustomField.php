<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields\UserProfile;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class CustomField extends FieldBase
{
    public function field_type()
    {
        return 'profile-cpf';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-admin-users"></span>';
    }

    public function field_title()
    {
        return esc_html__('Custom Field', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return [
            parent::GENERAL_TAB => [
                'custom_field' => [
                    'label'       => esc_html__('Custom Field', 'wp-user-avatar'),
                    'field'       => self::SELECT_FIELD,
                    'options'     => ppress_custom_fields_key_value_pair(),
                    'description' => sprintf(
                        esc_html__('Select a custom field. Only use the %1$sTitle%2$s and %1$sUser Meta / Field Key%2$s below if you don\'t have it %3$sdefined in ProfilePress%4$s.', 'wp-user-avatar'),
                        '<em>', '</em>',
                        '<a target="_blank" href="' . PPRESS_CUSTOM_FIELDS_SETTINGS_PAGE . '">', '</a>'
                    )
                ],
                'label'        => [
                    'label' => esc_html__('Title', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD
                ],
                'field_key'    => [
                    'label'       => esc_html__('User Meta / Field Key', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => esc_html__('Enter a custom field or user meta key here.', 'wp-user-avatar')
                ]
            ]
        ];
    }
}