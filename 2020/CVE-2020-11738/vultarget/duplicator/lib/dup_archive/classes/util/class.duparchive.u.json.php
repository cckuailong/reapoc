<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
if(!class_exists('DupArchiveJsonU')) {
class DupArchiveJsonU
{
    protected static $_messages = array(
        JSON_ERROR_NONE => 'No error has occurred',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded. To resolve see https://snapcreek.com/duplicator/docs/faqs-tech/#faq-package-170-q'
    );

    public static function customEncode($value, $iteration = 1)
    {
        $encoded = DupLiteSnapJsonU::wp_json_encode($value);
    
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                throw new RuntimeException('Maximum stack depth exceeded'); // or trigger_error() or throw new Exception()
            case JSON_ERROR_STATE_MISMATCH:
                throw new RuntimeException('Underflow or the modes mismatch'); // or trigger_error() or throw new Exception()
            case JSON_ERROR_CTRL_CHAR:
                throw new RuntimeException('Unexpected control character found');
            case JSON_ERROR_SYNTAX:
                throw new RuntimeException('Syntax error, malformed JSON'); // or trigger_error() or throw new Exception()
            case JSON_ERROR_UTF8:
                if ($iteration == 1) {
                    $clean = self::makeUTF8($value);
                    return self::customEncode($clean, $iteration + 1);
                } else {
                    throw new RuntimeException('UTF-8 error loop');
                }
            default:
                throw new RuntimeException('Unknown error'); // or trigger_error() or throw new Exception()
        }
    }

    public static function encode($value, $options = 0)
    {
        $result = DupLiteSnapJsonU::wp_json_encode($value, $options);

        if ($result !== FALSE) {

            return $result;
        }

        if (function_exists('json_last_error')) {
            $message = self::$_messages[json_last_error()];
        } else {
            $message = 'One or more filenames isn\'t compatible with JSON encoding';
        }

        throw new RuntimeException($message);
    }

    public static function decode($json, $assoc = false)
    {
        $result = json_decode($json, $assoc);

        if ($result) {
            return $result;
        }

        throw new RuntimeException(self::$_messages[json_last_error()]);
    }


    /** ========================================================
	 * PRIVATE METHODS
     * =====================================================  */


    private static function makeUTF8($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::makeUTF8($value);
            }
        } else if (is_string($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

    private static function escapeString($str)
    {
        return addcslashes($str, "\v\t\n\r\f\"\\/");
    }

    private static function oldCustomEncode($in)
    {
        $out = "";

        if (is_object($in)) {
            //$class_vars = get_object_vars(($in));
            //$arr = array();
            //foreach ($class_vars as $key => $val)
            //{
            $arr[$key] = "\"".self::escapeString($key)."\":\"{$val}\"";
            //}
            //$val = implode(',', $arr);
            //$out .= "{{$val}}";
            $in = get_object_vars($in);
        }
        //else
        if (is_array($in)) {
            $obj = false;
            $arr = array();

            foreach ($in AS $key => $val) {
                if (!is_numeric($key)) {
                    $obj = true;
                }
                $arr[$key] = self::oldCustomEncode($val);
            }

            if ($obj) {
                foreach ($arr AS $key => $val) {
                    $arr[$key] = "\"".self::escapeString($key)."\":{$val}";
                }
                $val = implode(',', $arr);
                $out .= "{{$val}}";
            } else {
                $val = implode(',', $arr);
                $out .= "[{$val}]";
            }
        } elseif (is_bool($in)) {
            $out .= $in ? 'true' : 'false';
        } elseif (is_null($in)) {
            $out .= 'null';
        } elseif (is_string($in)) {
            $out .= "\"".self::escapeString($in)."\"";
        } else {
            $out .= $in;
        }

        return "{$out}";
    }

    private static function oldMakeUTF8($val)
    {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = self::oldMakeUTF8($v);
            }
        } else if (is_object($val)) {
            foreach ($val as $k => $v) {
                $val->$k = self::oldMakeUTF8($v);
            }
        } else {
            if (mb_detect_encoding($val, 'UTF-8', true)) {
                return $val;
            } else {
                return utf8_encode($val);
            }
        }

        return $val;
    }
}
}