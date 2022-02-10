<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Form_Outputter
{

    /**
     * The arguments for the form outputter
     *
     * @access protected
     * @var    array
     *
     */
    protected $args;

    /**
     * The WPBS_Form
     *
     * @access protected
     * @var    WPBS_Form
     *
     */
    protected $form = null;

    /**
     * The Calendar ID attached to the form
     *
     * @access protected
     * @var    int
     *
     */
    protected $calendar_id = null;

    /**
     * The form fields
     *
     * @access protected
     * @var    array
     *
     */
    protected $form_fields = null;

    /**
     * The plugin general settings
     *
     * @access protected
     * @var    array
     *
     */
    protected $plugin_settings = array();

    /**
     * A unique string
     *
     * @access protected
     * @var    array
     *
     */
    protected $unique;

    /**
     * Constructor
     *
     * @param WPBS_Form $form
     * @param array     $args
     *
     */
    public function __construct($form, $args = array(), $form_fields = array(), $calendar_id = null)
    {

        /**
         * Set arguments
         *
         */
        $this->args = wp_parse_args($args, wpbs_get_form_output_default_args());

        /**
         * Set the form
         *
         */
        $this->form = $form;

        
        /**
         * Set the form
         *
         */
        $this->calendar_id = $calendar_id;


        /**
         * Set plugin settings
         *
         */
        $this->plugin_settings = get_option('wpbs_settings', array());

        /**
         * Set the form fields
         *
         */
        if (empty($form_fields)) {
            $this->form_fields = $form->get('fields');
        } else {
            $this->form_fields = $form_fields;
        }

        /**
         * Set the unique string to prevent conflicts if the same form is embedded twice on the same page
         *
         */
        $this->unique = hash('crc32', microtime(), false);
    }

    /**
     * Constructs and returns the HTML for the entire form
     *
     * @return string
     *
     */
    public function get_display()
    {

        /**
         * Return nothing if form is in Trash
         *
         */
        if ($this->form->get('status') == 'trash') {
            return '';
        }

        /**
         * Prepare needed data
         *
         */

        $form_html_data = 'data-id="' . $this->form->get('id') . '" ';

        foreach ($this->args as $arg => $val) {
            $form_html_data .= 'data-' . $arg . '="' . esc_attr($val) . '" ';
        }

        /**
         * Handle output for existing form
         *
         */
        $output = '<form method="post" action="#" id="wpbs-form-' . (int) $this->form->get('id') . '" class="wpbs-form-container wpbs-form-' . (int) $this->form->get('id') . '" ' . $form_html_data . '>';

        $output .= $this->get_form_errors();

        $output .= apply_filters('wpbs_form_outputter_form_fields_before', '', $this->form);

        $output .= $this->get_form_fields();

        $output .= apply_filters('wpbs_form_outputter_form_fields_after', '', $this->form);

        $output .= $this->get_form_button();

        $output .= '</form>';

        return $output;
    }

    /**
     * Check if we have form errors, not field errors
     *
     * @return mixed
     *
     */
    protected function get_form_errors()
    {

        if (!isset($this->form_fields['form_error'])) {
            return false;
        }

        $output = '<div class="wpbs-form-general-error">';
        $output .= $this->form_fields['form_error'];
        $output .= '</div>';

        return $output;

    }

    /**
     * Constructs and returns the HTML for form fields
     *
     * @return string
     *
     */
    protected function get_form_fields()
    {

        $output = '<div class="wpbs-form-fields">';

        foreach ($this->form_fields as $field) {

            if (!isset($field['type'])) {
                continue;
            }

            $field_output_method = 'get_form_field_type__' . $field['type'];

            if (method_exists($this, $field_output_method)) {
                $output .= $this->{$field_output_method}($field);
            }

        }

        $output .= '</div>';

        return $output;

    }

    /**
     * Constructs and returns the HTML the TEXT field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__text($field)
    {
        //$required_attribute = $this->form_field_is_required($field) ? ' required' : '';
        $required_attribute = '';

        $output = $this->get_form_field_header($field);
        $output .= '<input
            type="text"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . $this->get_form_field_value($field) . '"
            placeholder="' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '"
            ' . $required_attribute . '
        />';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the EMAIL field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__email($field)
    {
        //$required_attribute = $this->form_field_is_required($field) ? ' required' : '';
        $required_attribute = '';

        $output = $this->get_form_field_header($field);
        $output .= '<input
            type="email"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . $this->get_form_field_value($field) . '"
            placeholder="' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '"
            ' . $required_attribute . '
        />';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the TEXTAREA field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__textarea($field)
    {
        //$required_attribute = $this->form_field_is_required($field) ? ' required' : '';
        $required_attribute = '';

        $output = $this->get_form_field_header($field);
        $output .= '<textarea
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            placeholder="' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '"
            ' . $required_attribute . '
        >' . $this->get_form_field_value($field) . '</textarea>';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the DROPDOWN field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__dropdown($field)
    {
        //$required_attribute = $this->form_field_is_required($field) ? ' required' : '';
        $required_attribute = '';

        $output = $this->get_form_field_header($field);
        $output .= '<select
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            ' . $required_attribute . '
        >';

        $value = $this->get_form_field_value($field);

        if ($this->get_form_field_translation($field['values'], 'placeholder')) {
            $default_selected = (empty($value)) ? 'selected' : '';
            $output .= '<option value="" ' . $default_selected . ' disabled>' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '</option>';
        }

        $options = $this->get_form_field_translation($field['values'], 'options');

        if ($options) {
            foreach ($options as $i => $option) {
                if (is_null($option)) {
                    continue;
                }
                $selected = ($value == $option) ? 'selected' : '';
                $output .= '<option ' . $selected . ' value="' . esc_attr($option) . '">' . esc_attr($option) . '</option>';
            }
        }

        $output .= '</select>';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the CHECKBOX field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__checkbox($field)
    {
        //$required_attribute = $this->form_field_is_required($field) ? ' required' : '';
        $required_attribute = '';

        $output = $this->get_form_field_header($field);

        $values = $this->get_form_field_value($field);

        $options = $this->get_form_field_translation($field['values'], 'options');

        if ($options) {
            foreach ($options as $i => $option) {
                if (is_null($option)) {
                    continue;
                }

                $selected = (!empty($values) && in_array($option, $values)) ? ' checked' : '';

                $output .= '<label for="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '">';
                $output .= '<input
                    type="checkbox"
                    name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '[]"
                    id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '"
                    value="' . esc_attr($option) . '"
                    ' . $required_attribute . '
                    ' . $selected . '
                >';
                $output .= ' ' . esc_attr($option) . '<span></span></label>';
            }
        }

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the RADIO field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__radio($field)
    {

        //$required_attribute = $this->form_field_is_required($field) ? ' required' : '';
        $required_attribute = '';

        $output = $this->get_form_field_header($field);

        $value = $this->get_form_field_value($field);

        $options = $this->get_form_field_translation($field['values'], 'options');

        if ($options) {
            foreach ($options as $i => $option) {
                if (is_null($option)) {
                    continue;
                }

                $selected = ($value == $option) ? ' checked' : '';

                $output .= '<label for="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '">';
                $output .= '<input
                    type="radio"
                    name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
                    id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '"
                    value="' . esc_attr($option) . '"
                    ' . $required_attribute . '
                    ' . $selected . '
                >';
                $output .= ' ' . esc_attr($option) . '<span></span></label>';
            }
        }

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the HTML field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__html($field)
    {

        $output = $this->get_form_field_header($field);

        $output .= html_entity_decode(esc_html($this->get_form_field_translation($field['values'], 'value')));

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the HIDDEN field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__hidden($field)
    {

        $output = $this->get_form_field_header($field);

        $output .= '<input
            type="hidden"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            value="' . $this->get_form_field_value($field) . '"
        />';

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the CAPTCHA field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__captcha($field)
    {

        $recaptcha_site_key = (isset($this->plugin_settings['recaptcha_v2_site_key'])) ? $this->plugin_settings['recaptcha_v2_site_key'] : '';

        $output = $this->get_form_field_header($field);

        $output .= '<div class="wpbs-google-recaptcha" id="wpbs-google-recaptcha-' . $this->form->get('id') . '-' . $this->unique . '" data-sitekey="' . $recaptcha_site_key . '"></div>';

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Helper function to output field header
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_header($field)
    {
        $custom_class = isset($field['values']['default']['class']) ? $field['values']['default']['class'] : '';
        $error_class = isset($field['error']) ? 'wpbs-form-field-has-error' : '';
        $required_class = $this->form_field_is_required($field) ? 'wpbs-field-required' : '';

        $output = '<div class="wpbs-form-field wpbs-form-field-' . $field['type'] . ' wpbs-form-field-' . $this->form->get('id') . '-' . $field['id'] . ' ' . $custom_class . ' ' . $required_class . ' ' . $error_class . '">';
        $output .= $this->get_form_field_label($field);

        $output .= '<div class="wpbs-form-field-input">';

        return $output;
    }

    /**
     * Helper function to output field footer
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_footer($field)
    {

        $output = $this->get_form_field_description($field);
        $output .= $this->get_form_field_error($field);

        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Helper function to get field labels
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_label($field)
    {
        if (isset($field['values']['default']['hide_label']) && $field['values']['default']['hide_label'] == 'on') {
            return false;
        }

        if (in_array($field['type'], array('html', 'hidden'))) {
            return false;
        }

        $for = !in_array($field['type'], array('dropdown', 'radio', 'checkbox', 'product_dropdown', 'product_radio', 'product_checkbox')) ? 'for="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"' : '';

        $output = '<div class="wpbs-form-field-label">';
        $output .= '<label ' . $for . '>';
        $output .= $this->get_form_field_translation($field['values'], 'label');
        $output .= ($this->form_field_is_required($field)) ? '<sup class="wpbs-field-required-asterisk">*</sup>' : '';
        $output .= '</label>';
        $output .= '</div>';
        return $output;
    }

    /**
     * Helper function to get field value
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_value($field)
    {
        if (isset($field['user_value'])) {
            if (is_array($field['user_value'])) {
                return $field['user_value'];
            }

            return esc_attr($field['user_value']);
        }

        return esc_attr($this->get_form_field_translation($field['values'], 'value'));
    }

    /**
     * Helper function to get field descriptions
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_description($field)
    {
        if (empty($this->get_form_field_translation($field['values'], 'description'))) {
            return false;
        }

        $output = '<div class="wpbs-form-field-description">';
        $output .= '<small>' . $this->get_form_field_translation($field['values'], 'description') . '</small>';
        $output .= '</div>';
        return $output;
    }

    /**
     * Helper function to get field errors
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_error($field)
    {
        $error = isset($field['error']) ? $field['error'] : '';

        $output = '';

        if ($error) {
            $output .= '<div class="wpbs-form-field-error"><small>' . $error . '</small></div>';
        }
        return $output;
    }

    /**
     * Helper function to get translations
     *
     * @param array $values
     * @param string $key
     *
     * @return string
     *
     */
    protected function get_form_field_translation($values, $key)
    {
        $language = $this->args['language'];

        if (array_key_exists($language, $values) && !empty($values[$language][$key])) {
            return $values[$language][$key];
        }

        if (isset($values['default'][$key]) && !empty($values['default'][$key])) {

            return $values['default'][$key];
        }
        return null;
    }

    /**
     * Checks if a field is required
     *
     * @param array $field
     *
     * @return bool
     *
     */
    protected function form_field_is_required($field)
    {
        return (isset($field['values']['default']['required']) && $field['values']['default']['required'] == 'on') ? true : false;
    }

    /**
     * Constructs and returns the HTML the submit button
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_button()
    {

        $language = $this->args['language'];
        $label = wpbs_get_translated_form_meta($this->form->get('id'), 'submit_button_label', $language);

        if (empty($label)) {
            $label = __('Submit', 'wp-booking-system');
        }

        $output = '<div class="wpbs-form-field wpbs-form-submit-button">';

        $output .= '<button type="submit" id="wpbs-form-submit-' . $this->form->get('id') . '">' . esc_attr($label) . '</button>';

        $output .= '</div>';

        return $output;
    }

    /**
     * Get the unique code to diferentiate forms
     *
     */
    public function get_unique()
    {
        return $this->unique;
    }

    /**
     * Get the language
     *
     */
    public function get_language()
    {
        return $this->args['language'];
    }

}
