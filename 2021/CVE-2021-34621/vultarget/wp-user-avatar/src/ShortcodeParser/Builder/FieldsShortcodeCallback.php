<?php

namespace ProfilePress\Core\ShortcodeParser\Builder;

use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Classes\FormRepository;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;
use ProfilePress\Core\Classes\UserAvatar;

class FieldsShortcodeCallback
{
    protected $form_type;

    protected $form_name;

    protected $tag_name;

    /** @var \WP_User */
    private $current_user;

    public function __construct($form_type)
    {
        $this->form_type = $form_type;

        $this->form_name = $form_type == FormRepository::REGISTRATION_TYPE ? 'registration' : 'edit_profile';

        $this->tag_name = $form_type == FormRepository::REGISTRATION_TYPE ? 'reg' : 'eup';

        add_action('init', [$this, 'get_current_user']);
    }

    public function get_current_user()
    {
        $current_user = wp_get_current_user();
        if ($current_user instanceof \WP_User) {
            $this->current_user = $current_user;
        }
    }

    public function GET_POST()
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Is field a required field?
     *
     * @param array $atts
     *
     * @return bool
     */
    public function is_field_required($atts)
    {
        $atts = ppress_normalize_attributes($atts);

        return isset($atts['required']) && ($atts['required'] === true || $atts['required'] == 'true' || $atts['required'] == '1');
    }

    /**
     * Rewrite custom field key to something more human readable.
     *
     * @param string $key field key
     *
     * @return string
     */
    public function human_readable_field_key($key)
    {
        return ucfirst(str_replace('_', ' ', $key));
    }

    public function valid_field_atts($atts)
    {
        if ( ! is_array($atts)) return $atts;

        $invalid_atts = array('enforce', 'key', 'field_key', 'limit', 'options', 'checkbox_text', 'date_format', 'field_width', 'icon');

        $valid_atts = array();

        foreach ($atts as $key => $value) {
            if ( ! in_array($key, $invalid_atts)) {
                $valid_atts[$key] = $value;
            }
        }

        return $valid_atts;
    }

    public function field_attributes($field_name, $atts, $required = 'false')
    {
        $_POST = $this->GET_POST();

        if ($field_name !== $this->tag_name . '_submit') {
            $atts['required'] = isset($atts['required']) ? $atts['required'] : $required;
        }

        if ( ! in_array($field_name, ['ignore_value'])) {
            $atts['value'] = isset($_POST[$field_name]) ? esc_attr($_POST[$field_name]) : @esc_attr($atts['value']);
        }

        $output = [];

        foreach ($atts as $key => $value) {
            // add class to submit button.
            if ($field_name == $this->tag_name . '_submit' && $key == 'class') {
                $value = 'pp-submit-form ' . $value;
            }

            if ($key != 'required' && ! empty($value)) {
                $value    = esc_attr($value);
                $output[] = "$key=\"$value\"";
            }
        }

        $output = implode(' ', $output);

        if ($this->is_field_required($atts)) {
            $output .= ' required="required"';
        }

        return $output;
    }

    /**
     * @param array $atts
     *
     * @return string
     */
    public function username($atts)
    {
        $required = true;

        if (empty($atts)) $atts = [];

        // we are using + cos we dont want to override array value if it already exist.
        $atts = $atts + ['placeholder' => esc_html__('Username', 'wp-user-avatar')];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {

            $atts = $atts + ['disabled' => 'disabled'];

            $atts['value'] = esc_attr($this->current_user->user_login);

            $required = false;
        }

        $attributes = $this->field_attributes($this->tag_name . '_username', $this->valid_field_atts(ppress_normalize_attributes($atts)), $required);

        $html = "<input name='" . $this->tag_name . "_username' type='text' $attributes>";

        return apply_filters('ppress_' . $this->form_name . '_username_field', $html, $atts);
    }

    /**
     * @param array $atts
     *
     * @return string
     */
    public function password($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Password', 'wp-user-avatar')];

        $attributes = $this->field_attributes($this->tag_name . '_password', $this->valid_field_atts(ppress_normalize_attributes($atts)), true);

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $attributes = $this->field_attributes('ignore_value', $this->valid_field_atts(ppress_normalize_attributes($atts)), false);
        }

        $html = "<input name='" . $this->tag_name . "_password' type='password' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE) {
            $html .= '<input name="' . $this->tag_name . '_password_present" type="hidden" value="true">';
        }

        return apply_filters('ppress_' . $this->form_name . '_password_field', $html, $atts);
    }

    /**
     * @param array $atts
     *
     * @return string
     */
    public function confirm_password($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Confirm Password', 'wp-user-avatar')];

        $attributes = $this->field_attributes($this->tag_name . '_password2', $this->valid_field_atts(ppress_normalize_attributes($atts)), true);

        $html = "<input name='" . $this->tag_name . "_password2' type='password' $attributes>";

        return apply_filters('ppress_' . $this->form_name . '_confirm_password_field', $html, $atts);
    }

    /**
     * Callback function for email
     *
     * @param $atts
     *
     * @return string
     */
    public function email($atts)
    {
        $required = true;

        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Email Address', 'wp-user-avatar')];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $required = false;
            // default username saved in DB
            $atts['value'] = esc_attr($this->current_user->user_email);
        }

        $attributes = $this->field_attributes($this->tag_name . '_email', $this->valid_field_atts(ppress_normalize_attributes($atts)), $required);

        $html = "<input name='" . $this->tag_name . "_email' type='text' $attributes>";

        return apply_filters('ppress_' . $this->form_name . '_email_field', $html, $atts);
    }

    /**
     * @param array $atts
     *
     * @return string
     */
    public function confirm_email($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Confirm Email Address', 'wp-user-avatar')];

        $attributes = $this->field_attributes($this->tag_name . '_email2', $this->valid_field_atts(ppress_normalize_attributes($atts)), true);

        $html = "<input name='" . $this->tag_name . "_email2' type='text' $attributes>";

        return apply_filters('ppress_' . $this->form_name . '_confirm_email_field', $html, $atts);

    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function website($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Website', 'wp-user-avatar')];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            // default username saved in DB
            $atts['value'] = esc_attr($this->current_user->user_url);
        }

        $field_name = $this->tag_name . '_website';

        $attributes = $this->field_attributes($field_name, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<input name='$field_name' type='text' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_website_required_field', esc_html__('Website', 'wp-user-avatar'));
            $html  .= "<input name='required-fields[$field_name]' type='hidden' value='$value'>";
        }

        return apply_filters('ppress_' . $this->form_name . '_website_field', $html, $atts);
    }


    /**
     * Callback function for nickname
     *
     * @param $atts
     *
     * @return string
     */
    public function nickname($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Nickname', 'wp-user-avatar')];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            // default username saved in DB
            $atts['value'] = esc_attr($this->current_user->nickname);
        }

        $field_name = $this->tag_name . '_nickname';

        $attributes = $this->field_attributes($field_name, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<input name='$field_name' type='text' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_nickname_required_field', esc_html__('Nickname', 'wp-user-avatar'));
            $html  .= "<input name='required-fields[$field_name]' type='hidden' value='$value'>";
        }

        return apply_filters('ppress_' . $this->form_name . '_nickname_field', $html, $atts);
    }

    /**
     * Callback function for nickname
     *
     * @param $atts
     *
     * @return string
     */
    public function display_name($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Display Name', 'wp-user-avatar')];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            // default username saved in DB
            $atts['value'] = esc_attr($this->current_user->display_name);
        }

        $field_name = $this->tag_name . '_display_name';

        $attributes = $this->field_attributes($field_name, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<input name='$field_name' type='text' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_display_name_required_field', esc_html__('Display name', 'wp-user-avatar'));
            $html  .= "<input name='required-fields[$field_name]' type='hidden' value='$value'>";
        }

        return apply_filters('ppress_' . $this->form_name . '_display_name_field', $html, $atts);
    }

    /**
     * Callback function for first name
     *
     * @param $atts
     *
     * @return string
     */
    public function first_name($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('First Name', 'wp-user-avatar')];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            // default username saved in DB
            $atts['value'] = esc_attr($this->current_user->first_name);
        }

        $field_name = $this->tag_name . '_first_name';

        $attributes = $this->field_attributes($field_name, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<input name='$field_name' type='text' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_first_name_required_field', esc_html__('First name', 'wp-user-avatar'));
            $html  .= "<input name='required-fields[$field_name]' type='hidden' value='$value'>";
        }

        return apply_filters('ppress_' . $this->form_name . '_first_name_field', $html, $atts);
    }


    /**
     * Callback for last name
     *
     * @param $atts
     *
     * @return string
     */
    public function last_name($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Last Name', 'wp-user-avatar')];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            // default username saved in DB
            $atts['value'] = esc_attr($this->current_user->last_name);
        }

        $field_name = $this->tag_name . '_last_name';

        $attributes = $this->field_attributes($field_name, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<input name='$field_name' type='text' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_last_name_required_field', esc_html__('Last name', 'wp-user-avatar'));
            $html  .= "<input name='required-fields[$field_name]' type='hidden' value='$value'>";
        }

        return apply_filters('ppress_' . $this->form_name . '_last_name_field', $html, $atts);
    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function bio($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = $atts + ['placeholder' => esc_html__('Biographical Info', 'wp-user-avatar')];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            // default username saved in DB
            $atts['value'] = esc_attr($this->current_user->description);
        }

        $field_name = $this->tag_name . '_bio';

        $attributes = $this->field_attributes('ignore_value', $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $value = isset($_POST[$field_name]) ? wp_kses_post($_POST[$field_name]) : @wp_kses_post($atts['value']);

        $html = "<textarea name=\"$field_name\" $attributes>$value</textarea>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_bio_required_field', esc_html__('Bio description', 'wp-user-avatar'));
            $html  .= "<input name='required-fields[$field_name]' type='hidden' value='$value'>";
        }

        return apply_filters('ppress_' . $this->form_name . '_bio_field', $html, $atts);
    }

    /**
     * Upload avatar field
     */
    public function avatar($atts)
    {
        if (empty($atts)) $atts = [];

        $field_name = $this->tag_name . '_avatar';

        $attributes = $this->field_attributes($field_name, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<input name='$field_name' type='file' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_avatar_required_field', esc_html__('Profile picture', 'wp-user-avatar'));
            $html  .= "<input name='required-fields[$field_name]' type='hidden' value='$value'>";
        }

        return apply_filters('ppress_' . $this->form_name . '_avatar_field', $html, $atts);
    }

    /**
     * Upload cover image field
     */
    public function cover_image($atts)
    {
        if (empty($atts)) $atts = [];

        $field_name = $this->tag_name . '_cover_image';

        $attributes = $this->field_attributes($field_name, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<input name='$field_name' type='file' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_cover_image_required_field', esc_html__('Cover image', 'wp-user-avatar'));
            $html  .= "<input name='required-fields[$field_name]' type='hidden' value='$value'>";
        }

        return apply_filters('ppress_' . $this->form_name . '_cover_image_field', $html, $atts);
    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function textbox_field($atts)
    {
        if (empty($atts)) $atts = [];

        $atts = array_replace(['type' => 'text'], $atts);

        if ( ! isset($atts['key']) || empty($atts['key'])) {
            return esc_html__('Field key is missing', 'wp-user-avatar');
        }

        $key = ppress_sanitize_key($atts['key']);

        $type = sanitize_text_field($atts['type']);

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $db_data       = ! empty($atts['value']) ? sanitize_text_field($atts['value']) : $this->current_user->$key;
            $atts['value'] = isset($_POST[$key]) ? esc_attr($_POST[$key]) : $db_data;
        }

        $attributes = $this->field_attributes($key, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<input name='" . $key . "' type='$type' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_custom_required_field', $this->human_readable_field_key($key), $key);
            $html  .= "<input name='required-fields[$key]' type='hidden' value='$value'>";
        }

        return $html;
    }

    public function number_field($atts)
    {
        if (empty($atts)) $atts = [];

        $atts['type'] = 'number';

        return $this->textbox_field($atts);
    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function cf_password_field($atts)
    {
        if (empty($atts)) $atts = [];

        $atts['type'] = 'password';

        return $this->textbox_field($atts);
    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function country_field($atts)
    {
        if (empty($atts)) $atts = [];

        if (empty($atts['key'])) {
            return esc_html__('Field key is missing', 'wp-user-avatar');
        }

        $key = ppress_sanitize_key($atts['key']);

        $value = isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : @sanitize_text_field($atts['value']);

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $db_data = ! empty($atts['value']) ? sanitize_text_field($atts['value']) : $this->current_user->$key;
            $value   = isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : $db_data;
        }

        $countries = ppress_array_of_world_countries();

        $attributes = $this->field_attributes('ignore_value', $this->valid_field_atts($atts));

        $html = "<select name='$key' $attributes>";
        $html .= '<option value="">' . esc_html__('Select country', 'wp-user-avatar') . '&hellip;</option>';

        foreach ($countries as $ckey => $cvalue) {
            $html .= '<option value="' . esc_attr($ckey) . '" ' . selected($value, $ckey, false) . '>' . $cvalue . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    public static function hasTime($string)
    {
        $timeStrings = ['H', 'h', 'G', 'i', 'S', 's', 'K'];
        foreach ($timeStrings as $timeString) {
            if (strpos($string, $timeString) != false) {
                return true;
            }
        }

        return false;
    }

    public static function hasDate($string)
    {
        $dateStrings = ['d', 'D', 'l', 'j', 'J', 'w', 'W', 'F', 'm', 'n', 'M', 'U', 'Y', 'y', 'Z'];
        foreach ($dateStrings as $dateString) {
            if (strpos($string, $dateString) != false) {
                return 'true';
            }
        }

        return false;
    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function date_field($atts)
    {
        if (empty($atts)) $atts = [];

        if (empty($atts['key'])) return esc_html__('Field key is missing', 'wp-user-avatar');

        $key = ppress_sanitize_key($atts['key']);

        $atts['class'] = "pp_datepicker $key " . @$atts['class'];

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $atts['value'] = isset($_POST[$key]) ? esc_attr($_POST[$key]) : $this->current_user->$key;
        }

        $attributes = $this->field_attributes($key, $this->valid_field_atts($atts));

        $html = "<input name='" . $key . "' type='text' $attributes>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_custom_required_field', $this->human_readable_field_key($key), $key);
            $html  .= "<input name='required-fields[$key]' type='hidden' value='$value'>";
        }

        $dateFormat = ! empty($atts['date_format']) ? $atts['date_format'] : 'Y-m-d';
        // defined fields in custom fields settings page has date format saved in options $atts
        if ( ! empty($atts['options'])) {
            $dateFormat = $atts['options'];
        }

        $hasTime = self::hasTime($dateFormat);
        $time24  = false;

        if ($hasTime && strpos($dateFormat, 'H') !== false) {
            $time24 = true;
        }

        $config = apply_filters('ppress_frontend_flatpickr_date_config', [
            'dateFormat'    => $dateFormat,
            'enableTime'    => $hasTime,
            'noCalendar'    => ! self::hasDate($dateFormat),
            'disableMobile' => true,
            'time_24hr'     => $time24
        ]);

        $html .= sprintf(
            '<script type="text/javascript">jQuery(function() {jQuery( ".pp_datepicker.%s" ).flatpickr(%s);});</script>',
            $key, json_encode($config)
        );

        return $html;
    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function textarea_field($atts)
    {
        if (empty($atts)) $atts = [];

        if (empty($atts['key'])) return esc_html__('Field key is missing', 'wp-user-avatar');

        $key = ppress_sanitize_key($atts['key']);

        $value = isset($_POST[$key]) ? esc_textarea($_POST[$key]) : @esc_textarea($atts['value']);

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $value = isset($_POST[$key]) ? esc_textarea($_POST[$key]) : $this->current_user->$key;
        }

        $attributes = $this->field_attributes($key, $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $html = "<textarea name=\"$key\" $attributes>$value</textarea>";

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_custom_required_field', $this->human_readable_field_key($key), $key);
            $html  .= "<input name='required-fields[$key]' type='hidden' value='$value'>";
        }

        return $html;
    }

    public function select_dropdown_field($atts)
    {
        if (empty($atts)) $atts = [];

        if (empty($atts['key'])) return esc_html__('Field key is missing', 'wp-user-avatar');

        $key = ppress_sanitize_key($atts['key']);

        if ( ! isset($atts['options']) || empty($atts['options'])) return esc_html__('No dropdown option found.', 'wp-user-avatar');

        $is_multiple           = isset($atts['is_multiple']) && $atts['is_multiple'] == '1' ? 'multiple' : '';
        $select2_class_name    = $is_multiple == 'multiple' ? 'ppress-select2 ' : '';
        $data_placeholder_attr = $is_multiple == 'multiple' ? ' data-placeholder="' . @esc_attr($atts['placeholder']) . '"' : '';

        $atts['class'] = $select2_class_name . @esc_attr($atts['class']);

        $attributes = $this->field_attributes('ignore_value', $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $select_tag_key = $is_multiple == 'multiple' ? "{$key}[]" : $key;
        $html           = "<input type='hidden' name=\"$select_tag_key\" value=''>";
        $html           .= "<select name=\"$select_tag_key\"$data_placeholder_attr $attributes $is_multiple>";

        $option_values = is_array($atts['options']) ? implode(',', $atts['options']) : $atts['options'];

        if ( ! empty($option_values)) {

            $_POST = $this->GET_POST();

            $option_values = explode(',', $option_values);

            foreach ($option_values as $value) {
                $value = trim($value);

                $selected = is_array(@$_POST[$key]) && in_array($value, @$_POST[$key]) ? 'selected="selected"' : @selected(@$_POST[$key], $value, false);

                if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
                    $selected = '';
                    if (is_array(@$_POST[$key]) && in_array($value, @$_POST[$key])) $selected = 'selected="selected"';
                    // !isset($_POST[ $key ] is called to not run the succeeding code if the form is submitted.
                    // to enable the select dropdown retain the submitted options when an error occur/ prevent the form from saving.
                    elseif ( ! isset($_POST[$key]) && is_array($this->current_user->$key) && in_array($value, $this->current_user->$key)) {
                        $selected = 'selected="selected"';
                    } elseif ( ! isset($_POST[$key]) && ! is_array($this->current_user->$key) && $value == $this->current_user->$key) {
                        $selected = 'selected="selected"';
                    }
                }

                $html .= "<option value=\"$value\" $selected>$value</option>";
            }
        }

        $html .= '</select>';
        // if field is required, add an hidden field
        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_custom_required_field', $this->human_readable_field_key($key), $key);
            $html  .= "<input name='required-fields[$key]' type='hidden' value='$value'>";
        }

        if ($is_multiple) {
            $limit = absint(@$atts['limit']);
            $html  .= $this->select2_js_script($key, $limit);
        }

        return $html;
    }

    public function radio_buttons_field($atts)
    {
        if (empty($atts)) $atts = [];

        if (empty($atts['key'])) {
            return esc_html__('Field key is missing', 'wp-user-avatar');
        }

        $key = ppress_sanitize_key($atts['key']);

        if ( ! isset($atts['options']) || empty($atts['options'])) {
            return esc_html__('No radio choice found.', 'wp-user-avatar');
        }

        $attributes = $this->field_attributes('ignore_value', $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $option_values = explode(',', $atts['options']);

        $_POST = $this->GET_POST();

        $html = '<div class="pp-radios-container">';

        foreach ($option_values as $value) {
            $value = trim($value);

            $checked = @checked($_POST[$key], $value, false);

            if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
                $db_data = ! empty($atts['value']) ? sanitize_text_field($atts['value']) : $this->current_user->$key;
                $checked = @checked(
                    isset($_POST[$key]) && ! empty($_POST[$key]) ? $_POST[$key] : $db_data,
                    $value,
                    false
                );
            }

            $backward_compat_class = '';
            if ($this->form_type == FormRepository::REGISTRATION_TYPE) {
                $backward_compat_class = ' profilepress-reg-label';
            }

            $html .= '<div class="pp-radio-wrap">';
            $html .= "<input type='radio' name=\"$key\" value=\"$value\" id=\"$value\" $checked $attributes>";
            $html .= "<label class=\"pp-form-label{$backward_compat_class}\" for=\"$value\">$value</label>";
            $html .= '</div>';
        }

        $html .= '</div>';

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_custom_required_field', $this->human_readable_field_key($key), $key);
            $html  .= "<input name='required-fields[$key]' type='hidden' value='$value'>";
        }

        return $html;
    }

    public function checkbox_list_field($atts)
    {
        if (empty($atts)) $atts = [];

        if (empty($atts['key'])) return esc_html__('Field key is missing', 'wp-user-avatar');

        $key = ppress_sanitize_key($atts['key']);

        if ( ! isset($atts['options']) || empty($atts['options'])) {
            return esc_html__('No checkbox choice found.', 'wp-user-avatar');
        }

        $attributes = $this->field_attributes('ignore_value', $this->valid_field_atts(ppress_normalize_attributes($atts)));

        $checkbox_tag_key = "{$key}[]";

        $option_values = explode(',', $atts['options']);

        $html = '<div class="pp-checkboxes-container">';

        $_POST = $this->GET_POST();

        foreach ($option_values as $value) {

            $value = trim($value);

            $checked = is_array(@$_POST[$key]) && in_array($value, @$_POST[$key]) ? 'checked="checked"' : @checked(@$_POST[$key], $value, false);

            if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
                $checked = '';
                if (isset($_POST[$key]) && is_array(@$_POST[$key]) && in_array($value, @$_POST[$key])) {
                    $checked = 'checked="checked"';
                } elseif ( ! isset($_POST[$key]) && is_array($this->current_user->$key) && in_array($value, $this->current_user->$key)) {
                    $checked = 'checked="checked"';
                } elseif ( ! isset($_POST[$key]) && ! is_array($this->current_user->$key) && $value == $this->current_user->$key) {
                    $checked = 'checked="checked"';
                }
            }

            $html .= '<div class="pp-checkbox-wrap pp-multi-checkbox">';
            $html .= "<input type='checkbox' name=\"$checkbox_tag_key\" value=\"$value\" id=\"$value\" $attributes $checked>";
            $html .= "<label class='pp-form-label' for=\"$value\">$value</label>";
            $html .= '</div>';
        }

        $html .= '</div>';

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_custom_required_field', $this->human_readable_field_key($key), $key);
            $html  .= "<input name='required-fields[$key]' type='hidden' value='$value'>";
        }

        return $html;
    }

    public function single_checkbox_field($atts)
    {
        if (empty($atts)) $atts = [];

        $_POST = $this->GET_POST();

        $attributes = $this->field_attributes('ignore_value', $this->valid_field_atts(ppress_normalize_attributes($atts)));

        if (empty($atts['key'])) return esc_html__('Field key is missing', 'wp-user-avatar');

        $key = ppress_sanitize_key($atts['key']);

        $html        = '<div class="pp-checkbox-wrap pp-single-checkbox">';
        $field_label = isset($atts['checkbox_text']) ? html_entity_decode($atts['checkbox_text']) : '';

        // checked for checkbox
        $checked = checked(isset($_POST[$key]) ? isset($_POST[$key]) : '', 'true', false);

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $db_data = ! empty($atts['value']) ? sanitize_text_field($atts['value']) : $this->current_user->$key;
            $checked = @checked(
                ! empty($_POST[$key]) ? $_POST[$key] : $db_data,
                'true',
                false
            );
        }

        $html .= "<input type='hidden' name=\"$key\" value=\"false\" style='display: none'>";
        $html .= "<input type='checkbox' name=\"$key\" value=\"true\" id=\"$key\" $checked $attributes>";
        $html .= "<label class='pp-form-label' for=\"$key\">$field_label</label>";
        $html .= '</div>';

        if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
            $value = apply_filters('ppress_custom_required_field', $this->human_readable_field_key($key), $key);
            $html  .= "<input name='required-fields[$key]' type='hidden' value='$value'>";
        }

        return $html;
    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function custom_profile_field($atts)
    {
        if ( ! EM::is_enabled(EM::CUSTOM_FIELDS)) return '';

        if (empty($atts)) $atts = [];

        $_POST = $this->GET_POST();

        $atts = ppress_normalize_attributes($atts);

        $key = ppress_sanitize_key($atts['key']);

        if (empty($key)) return esc_html__('Field key is missing', 'wp-user-avatar');

        $type = ! empty($atts['type']) ? $atts['type'] : PROFILEPRESS_sql::get_field_type($key);

        if (empty($type) || ! $type) {
            $type = 'text';
        }

        $html = esc_html__('custom field not defined', 'wp-user-avatar');

        if ($type == 'select') {
            $atts['options']     = PROFILEPRESS_sql::get_field_option_values($key);
            $atts['is_multiple'] = ppress_is_select_field_multi_selectable($key) ? '1' : '';
            $html                = $this->select_dropdown_field($atts);
        }

        if ($type == 'radio') {
            $atts['key']     = $key;
            $atts['options'] = PROFILEPRESS_sql::get_field_option_values($key);
            $html            = $this->radio_buttons_field($atts);
        }

        if ($type == 'agreeable') {
            $atts['key']           = $key;
            $atts['checkbox_text'] = $field_label = html_entity_decode(PROFILEPRESS_sql::get_field_label($key));
            $html                  = $this->single_checkbox_field($atts);
        }

        if ($type == 'checkbox') {
            $atts['key']     = $key;
            $atts['options'] = PROFILEPRESS_sql::get_field_option_values($key);

            $html = $this->checkbox_list_field($atts);
        }

        if ($type == 'textarea') {
            $html = $this->textarea_field($atts);
        }

        if ($type == 'country') {
            $html = $this->country_field($atts);
        }

        if ($type == 'date') {
            $atts['options'] = PROFILEPRESS_sql::get_field_option_values($key);
            $html            = $this->date_field($atts);
        }

        if ('file' == $type) {

            $attributes = $this->field_attributes($key, $this->valid_field_atts($atts));

            $html = "<input name='" . $key . "' type='file' $attributes>";
            // if field is required, add an hidden field
            if ($this->form_type == FormRepository::REGISTRATION_TYPE && $this->is_field_required($atts)) {
                $html .= "<input name='required-" . $key . "' type='hidden' value='true' style='display:none'>";
            }
        }

        if (in_array($type, ['text', 'password', 'email', 'tel', 'number', 'hidden'])) {
            $atts['type'] = $type;
            $html         = $this->textbox_field($atts);
        }

        return apply_filters('ppress_' . $this->form_name . '_cpf_field', $html, $atts);
    }

    /**
     * Callback function for submit button
     *
     * @param $atts
     *
     * @return string
     */
    public function submit($atts)
    {
        if (empty($atts)) $atts = [];

        $field_name = isset($atts['name']) ? esc_attr($atts['name']) : $this->tag_name . '_submit';
        $value      = esc_html__('Sign Up', 'wp-user-avatar');

        if ($this->form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $value = esc_html__('Save Changes', 'wp-user-avatar');;
        }

        $atts          = apply_filters('ppress_' . $this->form_name . '_submit_field_atts', $this->valid_field_atts(ppress_normalize_attributes($atts)));
        $atts['value'] = isset($atts['value']) ? esc_attr($atts['value']) : $value;


        $form_type = $this->form_type;
        $form_id   = isset($GLOBALS['pp_registration_form_id']) ? $GLOBALS['pp_registration_form_id'] : 0;
        if ($form_type == FormRepository::EDIT_PROFILE_TYPE) {
            $form_id = isset($GLOBALS['pp_edit_profile_form_id']) ? $GLOBALS['pp_edit_profile_form_id'] : 0;
        }

        if (isset($GLOBALS['pp_melange_form_id'])) {
            $form_id   = $GLOBALS['pp_melange_form_id'];
            $form_type = FormRepository::MELANGE_TYPE;
        }

        $processing_label = ! empty($atts['processing_label']) ? $atts['processing_label'] : FormRepository::get_processing_label($form_id, $form_type);


        $attributes = $this->field_attributes($field_name, $atts);

        $html = sprintf(
            '<input data-pp-submit-label="%1$s" data-pp-processing-label="%2$s" name="%3$s" type="submit" %4$s>',
            $atts['value'],
            esc_attr($processing_label),
            $field_name,
            $attributes
        );

        $html .= ppress_nonce_field();

        return apply_filters('ppress_' . $this->form_name . '_submit_field', $html, $atts);
    }

    public function select2_js_script($key, $limit = 0)
    {
        $limit = absint($limit);

        return <<<SCRIPT
<script type='text/javascript'>
jQuery(function() {
    var selector = jQuery('select[name^="$key"].ppress-select2');
    selector.select2({width: '100%', maximumSelectionLength: $limit});
});
</script>
SCRIPT;
    }

    /**
     * Remove a user avatar
     *
     * @param $atts
     *
     * @return string
     */
    public function remove_user_avatar($atts)
    {
        if (empty($atts)) $atts = [];

        $other_atts_html = ppress_other_field_atts($atts);

        $atts = shortcode_atts([
            'class' => '',
            'id'    => '',
            'title' => '',
            'label' => esc_html__('Delete Avatar', 'wp-user-avatar'),
        ], $atts
        );

        $atts = apply_filters('ppress_edit_profile_remove_avatar_button_atts', $atts);

        $class = 'class="pp-del-profile-avatar ' . $atts['class'] . '"';
        $label = ! empty($atts['label']) ? $atts['label'] : null;
        $id    = ! empty($atts['id']) ? 'id="' . $atts['id'] . '"' : null;
        $title = 'title="' . $atts['title'] . '"';

        // ensure a profile avatar for the user is available before the remove button gets displayed
        if (UserAvatar::user_has_pp_avatar($this->current_user->ID)) {
            $button = "<button type=\"submit\" name=\"eup_remove_avatar\" value=\"removed\" $class $id $title $other_atts_html>$label</button>";

            return apply_filters('ppress_edit_profile_remove_avatar_button', $button, $atts);
        }
    }

    /**
     * Remove a user cover image
     *
     * @param $atts
     *
     * @return string
     */
    public function remove_cover_image($atts)
    {
        if (empty($atts)) $atts = [];

        $other_atts_html = ppress_other_field_atts($atts);

        $atts = shortcode_atts([
            'class' => '',
            'id'    => '',
            'title' => '',
            'label' => esc_html__('Delete Cover Image', 'wp-user-avatar'),
        ], $atts
        );

        $atts = apply_filters('ppress_edit_profile_remove_cover_image_button_atts', $atts);

        $class = 'class="pp-del-cover-image ' . $atts['class'] . '"';
        $label = ! empty($atts['label']) ? $atts['label'] : null;
        $id    = ! empty($atts['id']) ? 'id="' . $atts['id'] . '"' : null;
        $title = 'title="' . $atts['title'] . '"';

        if (ppress_user_has_cover_image($this->current_user->ID)) {
            $button = "<button type=\"submit\" name=\"eup_remove_cover_image\" value=\"removed\" $class $id $title $other_atts_html>$label</button>";

            return apply_filters('ppress_edit_profile_remove_cover_image_button', $button, $atts);
        }
    }
}