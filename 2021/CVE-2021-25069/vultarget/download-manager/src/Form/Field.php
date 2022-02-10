<?php


namespace WPDM\Form;


use WPDM\__\__;

class Field
{

    static function heading($attrs)
    {
        $_attrs = "";
        $text = $attrs['text'];
        unset($attrs['text']);
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<div class=''>{$text}</div>";
    }

    static function hidden($attrs)
    {
        $_attrs = "";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='hidden' $_attrs />";
        return $text;
    }

    static function text($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='text' $_attrs />";
        return $text;
    }

    static function textarea($attrs, $value = '')
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $_value) {
            $_attrs .= "{$key}='{$_value}' ";
        }
        $value = stripslashes_deep($value);
        $text = "<textarea $_attrs>{$value}</textarea>";
        return $text;
    }

    static function number($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='number' $_attrs />";
        return $text;
    }

    static function email($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<input type='email' $_attrs />";
    }

    static function password($attrs)
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        foreach ($attrs as $key => $value) {
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<input type='password' $_attrs />";
    }

    function checkbox($attrs){
        $_attrs = "";
        if(isset($attrs['class'])) unset($attrs['class']);
        $options = $attrs['options'];
        unset($attrs['options']);
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        $_options = "";
        foreach ($options as $value => $label){
            $_options .= "<div><label class='d-block option-label'><input type='checkbox' $_attrs value='{$value}'> {$label}</label></div>\r\n";
        }
        return $_options;
    }

    function radio($attrs){
        $_attrs = "";
        if(isset($attrs['class'])) unset($attrs['class']);
        $options = $attrs['options'];
        unset($attrs['options']);
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        $_options = "";
        foreach ($options as $value => $label){
            $_options .= "<div><label class='d-block option-label'><input type='radio' $_attrs value='{$value}'> {$label}</label></div>\r\n";
        }
        return $_options;
    }

    static function select($attrs, $value = '')
    {
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control " . $attrs['class'] : "form-control";
        $options = $attrs['options'];
        unset($attrs['options']);
        foreach ($attrs as $key => $_value) {
            $_attrs .= "{$key}='{$_value}' ";
        }
        $_options = "";
        foreach ($options as $_value => $label) {
            $_options .= "<option value='{$_value}' " . selected($_value, $value, false) . ">{$label}</option>\r\n";
        }
        return "<select $_attrs>\r\n{$_options}\r\n</select>";
    }

    static function meidapicker($attrs, $value = '')
    {
        ob_start();
        $_attrs = '';
        if (is_array($attrs)) {
            foreach ($attrs as $attr => $value) {
                $_attrs .= "$attr='$value' ";
            }
        }
        ?>
        <div class="input-group">
            <input <?php echo $_attrs; ?> type="url" value="<?php echo $value; ?>"/>
            <span class="input-group-append">
                <button class="btn btn-secondary btn-media-upload" type="button" rel="#<?php echo $attrs['id']; ?>"><i
                            class="far fa-image"></i></button>
            </span>
        </div>
        <?php
        return ob_get_clean();
    }

    static function reCaptcha($attrs){
        ob_start();
        ?>
        <div class="form-group row">
            <div class="col-sm-12">
                <input type="hidden" id="<?php echo $attrs['id'] ?>" name="<?php echo $attrs['name'] ?>" value=""/>
                <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
                        async defer></script>
                <div id="<?php echo $attrs['id'] ?>_field"></div>
                <style>
                    #<?php echo $attrs['id'] ?>_field iframe{ transform: scale(1.16); margin-left: 24px; margin-top: 5px; margin-bottom: 5px; }
                    #<?php echo $attrs['id'] ?>_field{ padding-bottom: 10px !important; }
                </style>
                <script type="text/javascript">
                    var verifyCallback = function (response) {
                        jQuery('#<?php echo $attrs['id'] ?>').val(response);
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

    static function custom($field, $attrs)
    {
        return call_user_func($field, $attrs);
    }

}
