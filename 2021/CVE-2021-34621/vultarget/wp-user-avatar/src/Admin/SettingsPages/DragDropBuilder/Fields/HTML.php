<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class HTML extends FieldBase
{
    public function field_type()
    {
        return 'pp-custom-html';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-editor-code"></span>';
    }

    public function field_title()
    {
        return esc_html__('Custom HTML', 'wp-user-avatar');
    }

    public function field_bar_title()
    {
        return esc_html__('Custom HTML', 'wp-user-avatar');
    }

    public function field_settings()
    {
        return apply_filters('ppress_form_builder_email_field_settings', [
            parent::GENERAL_TAB => [
                'custom_html' => [
                    'label' => esc_html__('Content', 'wp-user-avatar'),
                    'field' => self::WPEDITOR_FIELD,
                ]
            ]
        ], $this);
    }
}