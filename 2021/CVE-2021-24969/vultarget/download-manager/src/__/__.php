<?php


namespace WPDM\__;


class __
{
    /**
     * Get the client's IP address
     *
     */
    static function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        $ipaddress = explode(",", $ipaddress);

        return $ipaddress[0];
    }



    static function media_field($data)
    {
        ob_start();
        ?>
        <div class="input-group">
            <input placeholder="<?php echo $data['placeholder']; ?>" type="url" name="<?php echo $data['name']; ?>"
                   id="<?php echo isset($data['id']) ? $data['id'] : ($id = uniqid()); ?>" class="form-control"
                   value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>"/>
            <span class="input-group-btn">
                        <button class="btn btn-default btn-media-upload" type="button"
                                rel="#<?php echo isset($data['id']) ? $data['id'] : $id; ?>"><i
                                    class="fa fa-picture-o"></i></button>
                    </span>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * @usage Genrate option fields
     * @param $data
     * @return mixed|string
     */
    static function option_field($data, $fieldprefix)
    {
        $desc = isset($data['description']) ? "<em class='note'>{$data['description']}</em>" : "";
        $class = isset($data['class']) ? $data['class'] : "";
        $data['placeholder'] = isset($data['placeholder']) ? $data['placeholder'] : '';
        switch ($data['type']):
            case 'text':
                return "<input type='text' name='{$fieldprefix}[{$data['name']}]' class='form-control {$class}' id='$data[id]' value='$data[value]' placeholder='{$data['placeholder']}'  />$desc";
                break;
            case 'select':
            case 'dropdown':
                $html = "<select name='{$fieldprefix}[{$data['name']}]'  id='{$data['id']}' class='form-control {$class}' style='width:100%;min-width:150px;' >";
                foreach ($data['options'] as $value => $label) {

                    $html .= "<option value='{$value}' " . selected($data['selected'], $value, false) . ">$label</option>";
                }
                $html .= "</select>";
                return $html . $desc;
                break;
            case 'notice':
                return "<div class='alert alert-info' style='margin: 0'>$data[notice]</div>" . $desc;
            case 'textarea':
                return "<textarea name='{$fieldprefix}[{$data['name']}]' id='$data[id]' class='form-control {$class}' style='min-height: 100px'>$data[value]</textarea>$desc";
                break;
            case 'checkbox':
                return "<input type='hidden' name='{$fieldprefix}[{$data['name']}]' value='0' /><input type='checkbox' class='{$class}' name='$data[name]' id='$data[id]' value='$data[value]' " . checked($data['checked'], $data['value'], false) . " />" . $desc;
                break;
            case 'callback':
                return call_user_func($data['dom_callback'], $data['dom_callback_params']) . $desc;
                break;
            case 'heading':
                return "<h3>" . $data['label'] . "</h3>";
                break;
            case 'media':
                return __::media_field($data);
                break;
            default:
                return "<input type='{$data['type']}' name='{$fieldprefix}[{$data['name']}]' class='form-control {$class}' id='$data[id]' value='$data[value]' placeholder='{$data['placeholder']}'  />$desc";
                break;
        endswitch;
    }

    /**
     * @param $options
     * @return string
     */
    static function option_page($options, $fieldprefix = '')
    {
        $html = "<div class='wpdm-settings-fields'>";
        foreach ($options as $id => $option) {
            $option['name'] = $id;
            if (!isset($option['id'])) $option['id'] = $id;
            if (in_array($option['type'], array('checkbox', 'radio')))
                $html .= "<div class='form-group'><label>" . option_field($option) . " {$option['label']}</label></div>";
            else if ($option['type'] == 'heading')
                $html .= "<h3>{$option['label']}</h3>";
            else
                $html .= "<div class='form-group'><label>{$option['label']}</label>" . option_field($option, $fieldprefix) . "</div>";
        }
        $html .= "</div>";
        return $html;
    }


    /**
     * @param $name
     * @param $options
     * @return string
     */
    static function settings_section($name, $options)
    {
        return "<div class='panel panel-default'><div class='panel-heading'>{$name}</div><div class='panel-body'>" . option_page($options) . "</div></div>";
    }


    /**
     * @param $var
     * @param $index
     * @param array $params
     * @return array|bool|float|int|mixed|string|string[]|null
     */

    static function valueof($var, $index, $params = [])
    {
        $index = explode("/", $index);
        $default = is_string($params) ? $params : '';
        if (is_object($var)) $var = (array)$var;
        $default = is_array($params) && isset($params['default']) ? $params['default'] : $default;
        if (count($index) > 1) {
            $val = $var;
            foreach ($index as $key) {
                $val = is_array($val) && isset($val[$key]) ? $val[$key] : '__not__set__';
                if ($val === '__not__set__') return $default;
            }
        } else
            $val = isset($var[$index[0]]) ? $var[$index[0]] : $default;

        if (is_array($params) && isset($params['validate'])) {
            if (!is_array($val))
                $val = __::sanitize_var($val, $params['validate']);
            else
                $val = __::sanitize_array($val, $params['validate']);
        }

        return $val;
    }

    /**
     * @usage Validate and sanitize input data
     * @param $var
     * @param array $params
     * @return int|null|string
     */
    static function query_var($var, $params = array(), $default = null)
    {
        global $wp_query;

        $_var = explode("/", $var);
        if (count($_var) > 1) {
            $val = $_REQUEST;
            foreach ($_var as $key) {
                $val = is_array($val) && isset($val[$key]) ? $val[$key] : false;
            }
        } else {
            $default = $default ? $default : (isset($params['default']) ? $params['default'] : null);
            $val = isset($_REQUEST[$var]) ? $_REQUEST[$var] : null;
            if(!$val)
                $val = isset($wp_query->query_vars[$var]) ? $wp_query->query_vars[$var] : $default;
        }
        $validate = is_string($params) ? $params : '';
        $validate = is_array($params) && isset($params['validate']) ? $params['validate'] : $validate;

        if (!is_array($val))
            $val = __::sanitize_var($val, $validate);
        else
            $val = __::sanitize_array($val, $validate);

        return $val;
    }

    /**
     * Sanitize an array or any single value
     * @param $array
     * @return mixed
     */
    static function sanitize_array($array, $sanitize = 'kses')
    {
        if (!is_array($array)) return esc_attr($array);
        foreach ($array as $key => &$value) {
            $validate = is_array($sanitize) && isset($sanitize[$key]) ? $sanitize[$key] : $sanitize;
            if (is_array($value))
                __::sanitize_array($value, $validate);
            else {
                $value = __::sanitize_var($value, $validate);
            }
            $array[$key] = &$value;
        }
        return $array;
    }

    /**
     * @param $value
     * @param string $sanitize
     * @return array|float|int|mixed|string|string[]|null
     */
    static function sanitize_var($value, $sanitize = 'kses')
    {
        if (is_array($value))
            return __::sanitize_array($value, $sanitize);
        else {
            switch ($sanitize) {
                case 'int':
                case 'num':
                    return (int)$value;
                    break;
                case 'double':
                case 'float':
                    return (double)($value);
                    break;
                case 'txt':
                case 'str':
                    $value = esc_attr($value);
                    break;
                case 'kses':
                    $allowedtags = wp_kses_allowed_html();
                    $allowedtags['div'] = array('class' => true);
                    $allowedtags['strong'] = array('class' => true);
                    $allowedtags['b'] = array('class' => true);
                    $allowedtags['i'] = array('class' => true);
                    $allowedtags['a'] = array('class' => true, 'href' => true);
                    $value = wp_kses($value, $allowedtags);
                    break;
                case 'serverpath':
                    $value = realpath($value);
                    $value = str_replace("\\", "/", $value);
                    break;
                case 'txts':
                    $value = sanitize_textarea_field($value);
                    break;
                case 'esc_html':
                    $value = esc_html($value);
                    break;
                case 'url':
                    $value = esc_url($value);
                    break;
                case 'noscript':
                case 'escs':
                    $value = wpdm_escs($value);
                    break;
                case 'filename':
                    $value = preg_replace("/([^\w\s\d\-_~,;\[\]\(\).])/", '_', $value);
                    $value = preg_replace("/([\.]+)/", '_', $value);
                    break;
                case 'alpha':
                    $value = preg_replace("/([^a-zA-Z])/", '', $value);
                    break;
                case 'alphanum':
                    $value = preg_replace("/([^a-zA-Z0-9])/", '', $value);
                    break;
                case 'html':

                    break;
                default:
                    $value = esc_sql(esc_attr($value));
                    break;
            }
            $value = __::escs($value);
        }
        return $value;
    }

    /**
     * @usage Escape script tag
     * @param $html
     * @return null|string|string[]
     */
    static function escs($html)
    {
        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    }

    /**
     * @return bool
     */
    static function is_ajax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Validate URL
     * @param $url
     * @return mixed|void
     */
    static function is_url( $url ) {
        $result = ( false !== filter_var( $url, FILTER_VALIDATE_URL ) );
        return apply_filters( '__is_url', $result, $url );
    }

    /**
     * @usage Post with cURL
     * @param $url
     * @param $data (array)
     * @param $headers (array)
     * @return bool|mixed|string
     */
    static function remote_post($url, $data, $headers = [])
    {

        $response = wp_remote_post($url, array(
                'method' => 'POST',
                'sslverify' => false,
                'timeout' => 5,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'body' => $data,
                'cookies' => array()
            )
        );
        $body = wp_remote_retrieve_body($response);
        return $body;
    }

    /**
     * @usage Get with cURL
     * @param $url
     * @param $headers (array)
     * @return bool|mixed|string
     */
    static function remote_get($url, $headers = [])
    {
        $content = "";
        $response = wp_remote_get($url, array('timeout' => 5, 'sslverify' => false, 'headers' => $headers));
        if (is_array($response)) {
            $content = $response['body'];
        } else
            $content = Messages::error($response->get_error_message(), -1);
        return $content;
    }

    static function hex2rgb($hex){
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return "$r, $g, $b";
    }

    /**
     * @usage Quote all elements in an array
     * @param $values
     * @return mixed
     */
    static function quote_all_array($values)
    {
        foreach ($values as $key => $value)
            if (is_array($value))
                $values[$key] = self::quote_all_array($value);
            else
                $values[$key] =self:: quote_it($value);
        return $values;
    }

    /**
     * @usage Quoate a value
     * @param $value
     * @return array|string
     */
    static function quote_it($value)
    {
        if (is_null($value))
            return "NULL";
        $value = '"' . esc_sql($value) . '"';
        return $value;
    }


    /**
     * Splice associative array
     * @param $input
     * @param $offset
     * @param $length
     * @param $replacement
     */
    static function array_splice_assoc(&$input, $offset, $length, $replacement)
    {
        $replacement = (array)$replacement;
        $key_indices = array_flip(array_keys($input));
        if (isset($input[$offset]) && is_string($offset)) {
            $offset = $key_indices[$offset];
        }
        if (isset($input[$length]) && is_string($length)) {
            $length = $key_indices[$length] - $offset;
        }

        $input = array_slice($input, 0, $offset, TRUE)
            + $replacement
            + array_slice($input, $offset + $length, NULL, TRUE);
    }

    static function mnemonicNumberFormat($number, $plus = true){
        if($number > 1000000){
            $number = number_format(($number/1000000), 1);
            $number = $number > (int)$number && $plus ? (int)$number.'M+':(int)$number.'M';
            return $number;
        }
        if($number > 1000){
            $number = number_format(($number/1000), 1);
            $number = $number > (int)$number && $plus ? (int)$number.'K+':(int)$number.'K';
            return $number;
        }
        return $number;
    }

    static function isAuthentic($nonce_var, $nonce_key, $access_level, $is_ajax = true){
        $nonce_var = __::sanitize_var($nonce_var, 'txt');
        if($is_ajax) {
            if(!check_ajax_referer($nonce_key, $nonce_var, false))
                wp_send_json(['success' => false, 'message' => esc_attr__( 'Referer verification failed', WPDM_TEXT_DOMAIN )]);
        }
        if(!wp_verify_nonce(wpdm_query_var($nonce_var), $nonce_key)) wp_send_json(['success' => false, 'message' => __('Security token is expired! Refresh the page and try again.', 'download-manager')]);
        if(!current_user_can($access_level)) wp_send_json(['success' => false, 'message' => __( "You are not allowed to change settings!", "download-manager" )]);
    }

    static function p(...$args)
    {
        foreach ($args as $arg) {
            echo "<pre>".print_r($arg, 1)."</pre>";
        }
    }

    static function d(...$args)
    {
        foreach ($args as $arg) {
            echo "<pre>".print_r($arg, 1)."</pre>";
        }
        die();
    }
}
