<?php

namespace WPDM\Form;

use WPDM\__\__;

class Form
{
    private $fieldGroups = [];
    private $formFields;
    private $method = 'post';
    private $action = '';
    private $name = 'form';
    private $id = 'form';
    private $class = 'form';
    public $submit_button = [];
    public $error = '';
    public $noForm = false;

    function __construct($formFields, $attrs = array())
    {
        if (!isset($attrs['id'])) {
            $this->error = '<div class="alert alert-danger">Form ID is require, field id is missing in $attrs<br/><pre style="border-radius: 0;margin-top: 10px;margin-bottom: 5px">' . print_r($attrs, 1) . '</pre></div>';
            return;
        }
        if (isset($attrs['groups']))
            $this->fieldGroups = $attrs['groups'];
        $this->formFields = apply_filters("{$attrs['id']}_fields", $formFields, $attrs);
        foreach ($attrs as $name => $value) {
            $this->$name = $value;
        }
    }

    function div($class = '', $id = '')
    {
        return "<div class='{$class}' id='row_{$id}'>";
    }

    function divClose()
    {
        return "</div>";
    }

    function label($label, $for = '')
    {
        return "<label form='{$for}'>{$label}</label>";
    }

    function row($id, $fields)
    {
        $row = $this->div("form-row", $id);
        if (isset($fields['label']))
            $this->label($fields['label'], $id);

        foreach ($fields['cols'] as $id => $field) {
            $row .= $this->formGroup($id, $field);
        }

        $row .= $this->divClose();
        return $row;
    }

    function fieldGroup($id, $name, $fields_html)
    {
        $group = $this->div('card', $id);
        $group .= $this->div('card-header', $id) . $name . $this->divClose();
        $group .= $this->div('card-body') . $fields_html . $this->divClose();
        $group .= $this->divClose();
        return $group;
    }

    function formGroup($id, $field)
    {
        $grid_class = isset($field['grid_class']) ? $field['grid_class'] : '';
        $field_html = $this->div("form-group {$grid_class}", $id);
        $type = $field['type'];
        $field_html .= $this->div("input-wrapper {$type}-input-wrapper", $id . "_wrapper");
        if (isset($field['label']))
            $field_html .= $this->label($field['label'], $id);

        $input = $type === 'custom' ? Field::custom($field['custom_control'], $field['attrs']) : Field::$type($field['attrs'], __::valueof($field, 'value'));
        if (in_array($type, ['reCaptcha', 'hidden'])) return $input;

        $prepend = isset($field['prepend']) ? $field['prepend'] : null;
        $append = isset($field['append']) ? $field['append'] : null;
        $input = $this->inputGroup($input, $prepend, $append);
        $field_html .= $this->note(__::valueof($field, 'note_before'));
        $field_html .= $input;
        $field_html .= $this->note(__::valueof($field, 'note'));
        $field_html .= $this->note(__::valueof($field, 'note_after'));
        $field_html .= $this->divClose();
        $field_html .= $this->divClose();
        return $field_html;
    }

    function note($note)
    {
        if ($note) return "<em class='note'>{$note}</em>";
    }

    function inputGroup($input, $prepend = null, $append = null)
    {
        if (!$prepend && !$prepend) return $input;
        $input_group = "<div class='input-group'>";
        $input_group .= $prepend ? "<div class='input-group-prepend'><span class='input-group-text'>{$prepend}</span></div>" : "";
        $input_group .= $input;
        $input_group .= $append ? "<div class='input-group-append'><span class='input-group-text'>{$append}</span></div>" : "";
        $input_group .= "</div>";
        return $input_group;
    }

    function heading($attrs)
    {
        $_attrs = "";
        $text = $attrs['text'];
        unset($attrs['text']);
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<div class=''>{$text}</div>";
    }

    function reCaptcha($attrs)
    {
        ob_start();
        ?>
        <div class="form-group row">
            <div class="col-sm-12">
                <input type="hidden" id="<?php echo $attrs['id'] ?>" name="<?php echo $attrs['name'] ?>" value=""/>
                <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
                        async defer></script>
                <div id="<?php echo $attrs['id'] ?>_field"></div>
                <style>
                    #<?php echo $attrs['id'] ?>_field iframe {
                        transform: scale(1.16);
                        margin-left: 24px;
                        margin-top: 5px;
                        margin-bottom: 5px;
                    }

                    #<?php echo $attrs['id'] ?>_field {
                        padding-bottom: 10px !important;
                    }
                </style>
                <script type="text/javascript">
                    var verifyCallback = function (response) {
                        jQuery('#<?php echo esc_attr($attrs['id']) ?>').val(response);
                    };
                    var widgetId2;
                    var onloadCallback = function () {
                        grecaptcha.render('<?php echo $attrs['id'] ?>_field', {
                            'sitekey': '<?php echo get_option('_wpdm_recaptcha_site_key'); ?>',
                            'callback': verifyCallback,
                            'theme': 'light'
                        });
                    };
                </script>
            </div>

        </div>
        <?php
        $captcha = ob_get_clean();
        return $captcha;
    }

    function render()
    {
        if ($this->error) return $this->error;
        $form_html = $this->noForm ? "" : "<form method='{$this->method}' action='{$this->action}' name='{$this->name}' id='{$this->id}' class='{$this->class}'>";
        $before_form_fields = "";
        $form_html .= apply_filters("wpdm_form_{$this->id}_before_fields", $before_form_fields, $this);

        //Initial field groups
        $_field_group_html = [];
        foreach ($this->fieldGroups as $group_id => $group_name){
            $_field_group_html[$group_id] = '';
        }

        //Generate form fields
        foreach ($this->formFields as $id => $field) {
            $field_html = '';
            if (isset($field['cols']))
                $field_html = $this->row($id, $field);
            else
                $field_html = $this->formGroup($id, $field);
            if(isset($field['group']) && isset($this->fieldGroups[$field['group']]))
                $_field_group_html[$field['group']] .= $field_html;
            else
                $form_html .= $field_html;
        }

        foreach ($this->fieldGroups as $group_id => $group_name){
            $form_html .= $this->fieldGroup($group_id, $group_name, $_field_group_html[$group_id]);
        }

        if ($this->submit_button) {
            $form_html .= "<button class='{$this->submit_button['class']}'>{$this->submit_button['label']}</button>";
        }
        $after_form_fields = "";
        $form_html .= apply_filters("wpdm_form_{$this->id}_before_fields", $after_form_fields, $this);
        $form_html .= $this->noForm ? "" : "</form>";
        return $form_html;
    }
}
