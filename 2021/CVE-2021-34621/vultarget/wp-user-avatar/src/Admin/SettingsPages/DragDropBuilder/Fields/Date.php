<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;


use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;

class Date extends FieldBase
{
    public function field_type()
    {
        return $this->tag_name . '-date-field';
    }

    public static function field_icon()
    {
        return '<span class="dashicons dashicons-calendar"></span>';
    }

    public function field_title()
    {
        return esc_html__('Date / Time', 'wp-user-avatar');
    }

    public function category()
    {
        return parent::EXTRA_CATEGORY;
    }

    public function field_settings()
    {
        return apply_filters('ppress_form_builder_date_field_settings', [
            parent::GENERAL_TAB  => [
                'key'         => [
                    'label'       => esc_html__('Field Key', 'wp-user-avatar'),
                    'field'       => self::INPUT_FIELD,
                    'description' => ppress_dnd_field_key_description()
                ],
                'date_format' => [
                    'label'       => esc_html__('Date/Time Format', 'wp-user-avatar'),
                    'field'       => self::SELECT_FIELD,
                    'options'     => [
                        'Y-m-d'       => 'Y-m-d - (Ex: 2018-04-28)',
                        'd-M-y'       => 'd-M-y - (Ex: 28-Apr-18)',
                        'm/d/Y'       => 'm/d/Y - (Ex: 04/28/2018)', // USA
                        'd/m/Y'       => 'd/m/Y - (Ex: 28/04/2018)', // Canada, UK
                        'd.m.Y'       => 'd.m.Y - (Ex: 28.04.2019)', // Germany
                        'n/j/y'       => 'n/j/y - (Ex: 4/28/18)',
                        'm/d/y'       => 'm/d/y - (Ex: 04/28/18)',
                        'M/d/Y'       => 'M/d/Y - (Ex: Apr/28/2018)',
                        'y/m/d'       => 'y/m/d - (Ex: 18/04/28)',
                        'm/d/Y h:i K' => 'm/d/Y h:i K - (Ex: 04/28/2018 08:55 PM)', // USA
                        'm/d/Y H:i'   => 'm/d/Y H:i - (Ex: 04/28/2018 20:55)', // USA
                        'd/m/Y h:i K' => 'd/m/Y h:i K - (Ex: 28/04/2018 08:55 PM)', // Canada, UK
                        'd/m/Y H:i'   => 'd/m/Y H:i - (Ex: 28/04/2018 20:55)', // Canada, UK
                        'd.m.Y h:i K' => 'd.m.Y h:i K - (Ex: 28.04.2019 08:55 PM)', // Germany
                        'd.m.Y H:i'   => 'd.m.Y H:i - (Ex: 28.04.2019 20:55)', // Germany
                        'h:i K'       => sprintf('h:i K (%s Ex: 08:55 PM)', esc_html__('Only Time', 'wp-user-avatar')),
                        'H:i'         => sprintf('H:i (%s Ex: 20:55)', esc_html__('Only Time', 'wp-user-avatar'))
                    ],
                    'description' => esc_html__('Select a date and time format that users will be able to choose a date in.', 'wp-user-avatar')
                ],
                'placeholder' => [
                    'label' => esc_html__('Placeholder', 'wp-user-avatar'),
                    'field' => self::INPUT_FIELD,
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