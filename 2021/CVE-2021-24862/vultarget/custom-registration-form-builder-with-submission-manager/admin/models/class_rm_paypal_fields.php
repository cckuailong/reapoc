<?php

/**
 * Form field model
 * 
 * This class represents the model for a form's fields and has the properties 
 * of a field and also have the DB operations for the model 
 *
 * @author cmshelplive
 */
class RM_PayPal_Fields extends RM_Base_Model
{

    public $field_id;
    public $type;
    public $name;
    public $value;
    public $class;
    public $option_label;
    public $option_price;
    public $option_value;
    public $description;
    public $require;
    public $order;
    public $extra_options;
    //Note: Do not set default value as null. Performance is better with isset which fails for null valued keys.
    public $def_ex_opts = array('show_on_form' => 'yes', 'allow_quantity' => 'no');
    //public $initialized;
    //errors of field data validation
    public $errors;

    public function __construct()
    {
        $this->initialized = false;
        $this->field_id = NULL;
    }

    /*     * *getters** */

    public static function get_identifier()
    {
        return 'PAYPAL_FIELDS';
    }

    public function get_field_id()
    {
        return $this->field_id;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function get_class()
    {
        return $this->class;
    }

    public function get_option_label()
    {
        return $this->option_label;
    }

    public function get_option_price()
    {
        return $this->option_price;
    }

    public function get_option_value()
    {
        return $this->option_value;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_require()
    {
        return $this->require;
    }

    public function get_order()
    {
        return $this->order;
    }
    
    //Pass null as argument to fetch whole array of extra options.
    public function get_extra_options($option_name = null)
    {
        if(!$option_name)
            return $this->extra_options;
        
        return isset($this->extra_options[$option_name]) ? $this->extra_options[$option_name] : null;            
    }

    /*     * *setters** */

    public function set_field_id($field_id)
    {
        $this->field_id = $field_id;
    }

    public function set_type($type)
    {
        $this->type = $type;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function set_value($value)
    {
        $this->value = $value;
    }

    public function set_class($class)
    {
        $this->class = $class;
    }

    public function set_option_label($option_label)
    {
        $option_label = maybe_unserialize($option_label);
        $option_label_sanitized = array();
        foreach($option_label as $label)
        {
            if($label == null || trim($label) == '')
                continue;
            else
                $option_label_sanitized[] = $label ;                
        }
        
        if(count($option_label_sanitized)===0)
            $this->option_label = null;
        else
            $this->option_label = maybe_serialize($option_label_sanitized);
    }

    public function set_option_price($option_price)
    {
        $option_price = maybe_unserialize($option_price);
        $option_price_sanitized = array();
        foreach($option_price as $price)
        {
            if($price == null || trim($price) == '')
                continue;
            else
                $option_price_sanitized[] = $price ;                
        }
        
        if(count($option_price_sanitized)===0)
            $this->option_price = null;
        else
            $this->option_price = maybe_serialize($option_price_sanitized);
    }

    public function set_option_value($option_value)
    {
        $this->option_value = $option_value;
    }

    public function set_description($description)
    {
        $this->description = $description;
    }

    public function set_require($require)
    {
        $this->require = $require;
    }

    public function set_order($order)
    {
        $this->order = $order;
    }

    //Batch set up whole array of extra options, do not use for updating individual options
    public function set_extra_options(array $extra_options)
    {
        $extra_options = array_intersect_key(array_merge($this->def_ex_opts, $extra_options), $this->def_ex_opts);
        $this->extra_options = $extra_options;
    }
    
    //Update extra options.
    public function update_extra_option($ex_opt_name , $ex_opt_val)
    {
        //It must be a valid option
        if(isset($this->def_ex_opts[$ex_opt_name]))
            $this->extra_options[$ex_opt_name]= $ex_opt_val;
    }

    public function set_errors($errors)
    {
        $this->errors = $errors;
    }

    /*
      public function set($request)
      {

      foreach ($request as $property => $value)
      {
      if (property_exists($this, $property))
      {
      $set_property_method = 'set_' . $property;
      if(method_exists($this, $set_property_method))
      $this->$set_property_method($value);
      } elseif (in_array($property, $this->valid_options, true))
      {
      if (is_array($value))
      $value = count($value);
      $this->field_options->$property = $value;
      }
      }

      return $this->initialized = true;
      } */

    /*     * **Validations*** */

    public function validate_label()
    {
        if (empty($this->field_label))
        {
            $this->errors['LABEL'] = __('Label can not be empty.', 'custom-registration-form-builder-with-submission-manager');
        }
        if (!is_string($this->field_label))
        {
            $this->errors['LABEL'] = __('Label must be a string.', 'custom-registration-form-builder-with-submission-manager');
        }
        if (preg_match('/[^a-zA-Z0-9_\-\.]/', $this->field_label))
        {
            $this->errors['LABEL'] = __('Label can not contain special characters.', 'custom-registration-form-builder-with-submission-manager');
        }
    }

    public function validate_type()
    {

        if (empty($this->field_type))
        {
            $this->errors['TYPE'] = __('Field can not be empty.', 'custom-registration-form-builder-with-submission-manager');
        }

        //validation of field_type data
    }

    public function validate_value()
    {
        //validations for value of field; 
    }

    public function validate_default_value()
    {
        //validations for fields default value
    }

    public function validate_order()
    {
        if (empty($this->field_order))
        {
            $this->errors['ORDER'] = __('Field order can not be empty.', 'custom-registration-form-builder-with-submission-manager');
        }
        if (is_int($this->field_order))
        {
            $this->errors['ORDER'] = __('Invalid order.', 'custom-registration-form-builder-with-submission-manager');
        }
    }

    public function is_valid()
    {
        $this->validate_label();
        $this->validate_type();

        return count($this->errors) === 0;
    }

    public function errors()
    {
        return $this->errors;
    }

    /*     * **Database Operations*** */

    public function insert_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }

        if ($this->field_id)
        {
            return false;
        }

        $data = array(
            'type' => $this->type,
            'name' => $this->name,
            'value' => $this->value,
            'class' => $this->class,
            'option_label' => $this->option_label,
            'option_price' => $this->option_price,
            'option_value' => $this->option_value,
            'description' => $this->description,
            'require' => $this->require,
            'order' => $this->order,
            'extra_options' => maybe_serialize($this->extra_options)
        );

        $data_specifiers = array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
        );

        $result = RM_DBManager::insert_row('PAYPAL_FIELDS', $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        $this->field_id = $result;

        return $result;
    }

    public function update_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }
        if (!$this->field_id)
        {
            return false;
        }

        $data = array(
            'type' => $this->type,
            'name' => $this->name,
            'value' => $this->value,
            'class' => $this->class,
            'option_label' => $this->option_label,
            'option_price' => $this->option_price,
            'option_value' => $this->option_value,
            'description' => $this->description,
            'require' => $this->require,
            'order' => $this->order,
            'extra_options' => maybe_serialize($this->extra_options)
        );

        $data_specifiers = array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
        );

        $result = RM_DBManager::update_row('PAYPAL_FIELDS', $this->field_id, $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        return true;
    }

    public function load_from_db($field_id, $should_set_id = true)
    {

        $result = RM_DBManager::get_row('PAYPAL_FIELDS', $field_id);
        //var_dump($result);die;
        if (null !== $result)
        {
            if ($should_set_id)
                $this->field_id = $field_id;
            else
                $this->field_id = null;

            $this->type = $result->type;
            $this->name = $result->name;
            $this->value = $result->value;
            $this->class = $result->class;
            $this->option_label = $result->option_label;
            $this->option_price = $result->option_price;
            $this->option_value = $result->option_value;
            $this->description = $result->description;
            $this->require = $result->require;
            $this->order = $result->order;
            $this->extra_options = maybe_unserialize($result->extra_options);
            
            //Support for old structure
            if(!is_array($this->extra_options) && ($this->extra_options == 'yes' || $this->extra_options == 'no'))
                $this->extra_options = array('show_on_form' => $this->extra_options);
            
            //If still not an array than it is an invalid config, reset to default.
            if(!is_array($this->extra_options))
                $this->extra_options = $this->def_ex_opts;
            else //Otherwise, just merge with default values.
                $this->extra_options = array_merge($this->def_ex_opts, $this->extra_options);
        } else
        {
            return false;
        }
        $this->initialized = true;
        return true;
    }

    public function remove_from_db()
    {
        return RM_DBManager::remove_row('PAYPAL_FIELDS', $this->field_id);
    }

}
