<?php

namespace ProfilePress\Core\Themes\DragDrop;

use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;

class MemberDirectoryListing
{
    private $directory_id;

    private $user_id;

    private $field_settings = [];

    private $output = '';

    private $defaults;

    public function __construct($directory_id, $user_id = false)
    {
        $this->directory_id = $directory_id;

        $this->user_id = $user_id;

        $this->field_settings = FR::form_builder_fields_settings($directory_id, FR::MEMBERS_DIRECTORY_TYPE);
    }

    public function defaults($defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function forge()
    {
        $output = '';

        foreach ($this->field_settings as $field_setting) {

            $field_type = $raw_field_type = $field_setting['fieldType'];

            if (isset($this->defaults, $this->defaults[$field_type])) {
                $field_setting = wp_parse_args($field_setting, $this->defaults[$field_type]);
            }

            $field_title = isset($field_setting['label']) ? $field_setting['label'] : '';

            if ($field_type == 'profile-cpf') {

                $field_key = '';

                if ( ! empty($field_setting['field_key']) && ! empty($field_setting['label'])) {

                    $field_key = $field_setting['field_key'];

                } elseif ( ! empty($field_setting['custom_field'])) {

                    $field_key = $field_setting['custom_field'];

                    $field_title = PROFILEPRESS_sql::get_field_label($field_key);

                    if ( ! $field_title) {
                        $field_title = PROFILEPRESS_sql::get_contact_info_field_label($field_key);
                    }
                }

                $field_type = $field_type . ' key="' . $field_key . '"';
            }

            if ($field_type == 'profile-display-name') {

                if ( ! empty($field_setting['format'])) {

                    $format = $field_setting['format'];

                    $field_type = $field_type . ' format="' . $format . '"';
                }
            }

            if ($field_type == 'pp-custom-html') {

                if ( ! empty($field_setting['custom_html'])) {

                    $field_type = $field_type . ' custom_html="' . $field_setting['custom_html'] . '"';
                }
            }

            // it's important the shortcode is parsed because FrontendProfileBuilder class is instantiated for each user iteration
            $parsed_shortcode = do_shortcode('[' . $field_type . ']', true);

            if ( ! empty($parsed_shortcode)) {

                if ( ! empty($field_key) && $raw_field_type == 'profile-cpf' && in_array($field_key, array_keys(ppress_social_network_fields()))) {
                    $parsed_shortcode = sprintf('<a href="%s">%s</a>', $parsed_shortcode, ppress_var(ppress_social_network_fields(), $field_key));
                }

                if ($raw_field_type == 'profile-display-name') {

                    $parsed_shortcode = sprintf(
                        '<a href="%s">%s</a>',
                        ppress_get_frontend_profile_url($this->user_id),
                        $parsed_shortcode
                    );
                }

                if ($raw_field_type == 'profile-website') {

                    $parsed_shortcode = sprintf(
                        '<a href="%s">%s</a>',
                        $parsed_shortcode,
                        ! empty($field_title) ? $field_title : esc_html__('Website', 'wp-user-avatar')
                    );
                }

                if ($raw_field_type == 'profile-website') {
                    $parsed_shortcode = make_clickable($parsed_shortcode);
                }

                $output .= sprintf('<div class="ppress-md-profile-item-wrap %s">', $raw_field_type);

                if ( ! empty($field_title) && $raw_field_type != 'profile-website') {
                    $output .= sprintf('<span class="ppress-md-profile-item-title">%s:</span> ', $field_title);
                }

                $output .= sprintf('%s', $parsed_shortcode);

                $output .= '</div>';
            }
        }

        $this->output = $output;

        return $this;
    }

    public function output()
    {
        return $this->output;
    }
}