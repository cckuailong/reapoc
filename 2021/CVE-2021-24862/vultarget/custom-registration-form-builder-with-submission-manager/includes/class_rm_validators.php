<?php

/**
 * Validator class of the plugin.
 * 
 * @link http://www.registrationmagic.com
 * @since      1.0.0
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/includes
 */

/**
 * Validator class of the plugin
 * 
 * It conatains all the validations specific to the field type.
 * Singleton as it doesnt hold any data.
 * 
 * @author cmshelplive
 */
class RM_Validators
{

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_text($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //error reporting method 
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_dropdown($data)
    {
        if (true)
        {
            echo "error!!"; //error reporting method ;
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_radio($data)
    {
        if (true)
        {
            echo "error!!"; //report error 
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_textarea($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_checkbox($data)
    {
        if (true)
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_heading($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_paragraph($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_date($data)
    {

        if (!is_string($data))
        {
            echo "error!!"; //report error
        }

        if (true/* some regex for date */)
        {
            echo "error!!"; //report error
        }
    }
  
    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_email($data)
    {
        if (!is_email($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_number($data)
    {
        if (!is_numeric($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_country($data)
    {
        if (true)
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_timezone($data)
    {
        if (true)
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_terms($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_file($data)
    {
        if (!is_file($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_pricing($data)
    {
        if (true)
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_repeatable($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_fname($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_lname($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //report error
        }
    }

    /**
     * type validator function
     * 
     * @var   $data    mixed    field value     
     */
    public static function validate_type_bio($data)
    {
        if (!is_string($data))
        {
            echo "error!!"; //report error 
        }
    }

    /**
     * This function will be used to validate a field 
     * it dynamically loads the validator function for corresponding field type
     * 
     * @access public
     * 
     * @param   $type    string     type of the field to validate
     * @param   $data    mixed      value of the field
     * 
     * @return  void  
     */
    public static function validate($type, $data)
    {
        if ($data)
        {
            $validator_method = 'validate_type_' . $type;
            self::$validator_method($data);
        }
    }

}
