<?php

namespace ProfilePress\Core\Themes\DragDrop;

use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Core\Classes\UserAvatar;
use ProfilePress\Libsodium\Recaptcha\Recaptcha;

class FieldListing
{
    private $form_id;
    private $form_type;
    private $is_buildscratch;

    private $field_settings = [];

    private $defaults;

    private $shortcode_field_wrap_start = '';

    private $shortcode_field_wrap_end = '';

    public function __construct($form_id, $form_type, $is_buildscratch = false)
    {
        $this->form_id         = $form_id;
        $this->form_type       = $form_type;
        $this->is_buildscratch = $is_buildscratch;

        $field_settings = FR::get_form_meta($form_id, $form_type, FR::FORM_BUILDER_FIELDS_SETTINGS);
        if ( ! empty($field_settings)) {
            $this->field_settings = json_decode($field_settings, true);
        }
    }

    public function shortcode_field_wrap_start($wrapper = '')
    {
        $this->shortcode_field_wrap_start = $wrapper;

        return $this;
    }

    public function shortcode_field_wrap_end($wrapper = '')
    {
        $this->shortcode_field_wrap_end = $wrapper;

        return $this;
    }

    public function defaults($defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    protected function is_field_required($field_setting)
    {
        $field_type = $field_setting['fieldType'];

        // the str_replace sorcery checks if field type contains any of the strings https://stackoverflow.com/a/42311760/2648410
        // we are using edit-profile-password and reg-password because we have other password custom fields that ends with -password
        if (str_replace(['-username', 'reg-password', 'edit-profile-password', '-email'], '', $field_type) != $field_type) return true;

        return isset($field_setting['required']) && ($field_setting['required'] === true || $field_setting['required'] == 'true' || $field_setting['required'] == '1');
    }

    protected function label_structure($field_setting, $field_id)
    {
        $field_type = $field_setting['fieldType'];

        if ( ! in_array($field_type, ['login-remember']) && strpos($field_type, 'agreeable') === false && ! empty($field_setting['label'])) {
            $output = sprintf('<div class="pp-form-label-wrap"><label for="%s" class="pp-form-label">', $field_id);
            $output .= $field_setting['label'];

            if ($this->is_field_required($field_setting)) {
                $output .= ' <span class="pp-form-required-label">*</span>';
            }

            if (isset($field_setting['description_appearance']) && $field_setting['description_appearance'] == 'tooltip' && ! empty($field_setting['description'])) {
                $output .= sprintf(
                    ' <span class="ppress-hint-tooltip hint--top hint--medium hint--bounce" aria-label="%s"><i class="pp-form-material-icons">help</i></span>',
                    sanitize_text_field($field_setting['description'])
                );
            }

            $output .= sprintf('</label></div>');

            return $output;
        }
    }

    public function is_avatar($field_type)
    {
        return $field_type == 'pp-user-avatar';
    }

    public function is_cover_image($field_type)
    {
        return $field_type == 'pp-user-cover-image';
    }

    public function is_recaptcha($field_type)
    {
        return $field_type == 'pp-recaptcha';
    }

    public function is_recaptcha_v3($field_type)
    {
        return $this->is_recaptcha($field_type) && class_exists('ProfilePress\Libsodium\Recaptcha\Recaptcha') && Recaptcha::$type == 'v3';
    }

    public function forge()
    {
        $output = '';

        foreach ($this->field_settings as $field_setting) {

            $field_type = $field_setting['fieldType'];

            if ($this->is_recaptcha($field_type) && ! EM::is_enabled(EM::RECAPTCHA)) continue;

            if (isset($this->defaults, $this->defaults[$field_type])) {
                $field_setting = wp_parse_args($field_setting, $this->defaults[$field_type]);
            }

            $raw_field_setting = $field_setting;

            $pp_form_field_wrap_classes = '';

            $field_with_class = 'full';
            if ( ! empty($field_setting['field_width'])) {
                $field_with_class = $field_setting['field_width'];
                unset($field_setting['field_width']);
            }

            $pp_form_field_wrap_classes .= ' fw-' . $field_with_class;

            if ( ! empty($field_setting['icon'])) {
                $pp_form_field_wrap_classes .= ' field-has-icon';
                unset($field_setting['icon']);
            }

            if (isset($field_setting['password_visibility_icon']) && $field_setting['password_visibility_icon'] === true) {
                $pp_form_field_wrap_classes .= ' has-password-visibility-icon';
                unset($field_setting['password_visibility_icon']);
            }

            $description_appearance = 'standard';
            if ( ! empty($field_setting['description_appearance'])) {
                $description_appearance = $field_setting['description_appearance'];
                unset($field_setting['description_appearance']);
            }
            $pp_form_field_wrap_classes .= ' fda-' . $description_appearance;

            $label_display = 'above';
            if ( ! empty($field_setting['label_display'])) {
                $label_display = $field_setting['label_display'];
                unset($field_setting['label_display']);
            }

            $pp_form_field_wrap_classes .= ' fld-' . $label_display;

            if ( ! $this->is_buildscratch) {
                $pp_form_field_wrap_classes = '';
            }

            if ( ! $this->is_recaptcha_v3($field_type)) {
                $output .= sprintf('<div class="pp-form-field-wrap %s%s">', str_replace('_', '-', $field_type), $pp_form_field_wrap_classes);
            }

            $field_setting = apply_filters("ppress_form_field_listing_setting_{$field_type}", $field_setting);

            if (strpos($field_type, 'reg-cpf') !== false) {
                $field_type = sprintf('reg-cpf key="%s"', $field_setting['definedFieldKey']);
                unset($field_setting['definedFieldKey']);
                unset($field_setting['definedFieldType']);
            }

            if (strpos($field_type, 'edit-profile-cpf') !== false) {
                $field_type = sprintf('edit-profile-cpf key="%s"', $field_setting['definedFieldKey']);
                unset($field_setting['definedFieldKey']);
                unset($field_setting['definedFieldType']);
            }

            $field_id = $field_type;
            if (isset($field_setting['key'])) {
                $field_id .= '_' . $field_setting['key'];
            }

            $field_id = ppress_md5($field_id);

            $output .= $this->label_structure($raw_field_setting, $field_id);

            $field_description = isset($field_setting['description']) ? $field_setting['description'] : '';

            unset($field_setting['label']);
            unset($field_setting['description']);
            unset($field_setting['fieldBarTitle']);
            unset($field_setting['fieldTitle']);
            unset($field_setting['fieldType']);
            unset($field_setting['fieldIcon']);
            unset($field_setting['sortID']);

            // add field class
            $field_type_class = strpos($field_type, 'reg-cpf') !== false ? 'reg-cpf' : $field_type;
            $field_type_class = strpos($field_type_class, 'edit-profile-cpf') !== false ? 'edit-profile-cpf' : $field_type_class;
            $saved_classes    = ' ' . $field_type_class;
            if ( ! empty($field_setting['class'])) {
                $saved_classes .= ' ' . $field_setting['class'];
            }

            $field_setting['class'] = "pp-form-field{$saved_classes}";

            if ($this->is_recaptcha($field_type) || $this->is_avatar($field_type) || $this->is_cover_image($field_type)) {
                $field_setting['class'] = $saved_classes;
            }

            $attributes = sprintf('id="%s" ', $field_id);

            foreach ($field_setting as $key => $value) {
                // the str_replace sorcery/ritual checks if field type contains any of the strings https://stackoverflow.com/a/42311760/2648410
                if ($key == 'options' && str_replace(['-select-dropdown', '-checkbox-list', '-radio-buttons'], '', $field_type) != $field_type) {
                    /** @source https://stackoverflow.com/a/3997367/2648410 */
                    $value = array_map('trim', preg_split('/\r\n|\r|\n/', $value, -1, PREG_SPLIT_NO_EMPTY));
                    $value = implode(',', $value);
                }

                $val = esc_attr($value);
                //do not escape custom HTML
                if ($key == 'custom_html') {
                    $val = addslashes($value);
                }

                $attributes .= $key . "='" . $val . "' ";
            }

            $output .= $this->shortcode_field_wrap_start;

            if ($this->is_avatar($field_type)) {
                $output .= sprintf('<div class="pp-field-user-avatar-picture-wrap" style="width:%s">', $field_setting['size'] . 'px');
            }

            if ($this->is_cover_image($field_type)) {
                $output .= '<div class="pp-field-user-cover-image-wrap">';
            }

            $output .= apply_filters("ppress_form_field_listing_$field_type", '[' . $field_type . ' ' . $attributes . ']', $raw_field_setting, $this->form_id, $this->form_type);

            if ($this->is_avatar($field_type) && UserAvatar::user_has_pp_avatar(get_current_user_id())) {
                $output .= '<span class="pp-profile-avatar-overlay-wrap"><span class="pp-profile-avatar-overlay"><ins><i class="pp-form-material-icons pp-del-profile-avatar">delete</i></ins></span></span>';
            }

            if ($this->is_cover_image($field_type) && ppress_user_has_cover_image()) {
                $output .= '<span class="pp-cover-image-overlay-wrap"><span class="pp-cover-image-overlay"><ins><i class="pp-form-material-icons pp-del-cover-image">delete</i></ins></span></span>';
            }

            if ($this->is_avatar($field_type) || $this->is_cover_image($field_type)) {
                $output .= '</div>';
            }

            $output .= apply_filters('ppress_form_after_field_listing', '', $raw_field_setting, $this->form_id, $this->form_type);
            if ( ! empty($field_description)) {
                $output .= '<div class="pp-form-field-description">';
                $output .= $field_description;
                $output .= '</div>';
            }

            $output .= $this->shortcode_field_wrap_end;

            if ( ! $this->is_recaptcha_v3($field_type)) {
                $output .= '</div>';
            }
        }

        return $output;
    }
}