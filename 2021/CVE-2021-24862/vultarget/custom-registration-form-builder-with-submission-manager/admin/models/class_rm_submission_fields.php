<?php

/**
 * Model for field submissions on a form
 * 
 * This model has the properties for submission fields and basic DB methods 
 * for the entity
 *
 * @author cmshelplive
 */
class RM_Submission_Fields extends RM_Base_Model
{

    public $sub_field_id;
    public $submission_id;
    public $form_id;
    public $field_id;
    public $value;
    //public $initialized;
    //submission field validation errors
    public $errors;

    public function __construct()
    {

        $this->initialized = false;
        $this->sub_field_id = NULL;
    }

    /*     * *Getters** */

    public static function get_identifier()
    {
        return 'SUBMISSION_FIELDS';
    }

    public function get_sub_field_id()
    {
        return $this->sub_field_id;
    }

    public function get_submission_id()
    {
        return $this->submission_id;
    }

    public function get_field_id()
    {
        return $this->field_id;
    }

    public function get_value()
    {
        return maybe_unserialize($this->value);
    }

    public function get_form_id()
    {
        return $this->form_id;
    }

    /*     * *Setters** */

    public function set_form_id($form_id)
    {
        $this->form_id = $form_id;
    }

    public function set_id($sub_field_id)
    {
        $this->sub_field_id = $sub_field_id;
    }

    public function set_submission_id($submission_id)
    {
        $this->submission_id = $submission_id;
    }

    public function set_field_id($field_id)
    {
        $this->field_id = $field_id;
    }

    public function set_value($value)
    {
        $this->value = maybe_serialize($value);
    }

//        public function set($request)
//    {
//
//        foreach ($request as $property => $value)
//        {
//            if (property_exists ($this ,$property ))
//            {
//                $this->$property = $value;
//            }
//        }
//    }


    /*     * *Validations** */

    public function validate_submission_id()
    {
        if (empty($this->submission_id))
        {
            $this->errors['SUBMISSION_ID'] = __("Submission ID must not be empty.",'custom-registration-form-builder-with-submission-manager');
        }
    }

    public function validate_field_id()
    {
        if (empty($this->field_id))
        {
            $this->errors['FIELD_ID'] = __("Field ID must not be empty.",'custom-registration-form-builder-with-submission-manager');
        }
    }

    public function validate_value()
    {
        //to validate field value
    }

    public function is_valid()
    {
        $this->validate_submission_id();
        $this->validate_field_id();
        $this->validate_value();

        return count($this->errors) === 0;
    }

    public function errors()
    {
        return $this->errors;
    }

    /*     * **Database Operations*** */

    public function insert_into_db($force = false)
    {
        if (!$this->initialized)
        {
            return false;
        }

        if (!$force && $this->sub_field_id)
        {
            return false;
        }

        $data = array(
            'submission_id' => $this->submission_id,
            'field_id' => $this->field_id,
            'form_id' => $this->form_id,
            'value' => $this->value,
        );

        $data_specifiers = array(
            '%d',
            '%d',
            '%d',
            '%s'
        );

        $result = RM_DBManager::insert_row('SUBMISSION_FIELDS', $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        $this->sub_field_id = $result;

        return $result;
    }

    public function update_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }
        if (!$this->sub_field_id)
        {
            return false;
        }

        $data = array(
            'submission_id' => $this->submission_id,
            'field_id' => $this->field_id,
            'form_id' => $this->form_id,
            'value' => $this->value,
        );

        $data_specifiers = array(
            '%d',
            '%d',
            '%d',
            '%s'
        );

        $result = RM_DBManager::update_row('SUBMISSION_FIELDS', $this->sub_field_id, $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        return true;
    }

    public function load_from_db($sub_field_id, $should_set_id = true)
    {

        $result = RM_DBManager::get_row('SUBMISSION_FIELDS', $sub_field_id);

        if (null !== $result)
        {
            if ($should_set_id)
                $this->sub_field_id = $sub_field_id;
            $this->submission_id = $result->submission_id;
            $this->field_id = $result->field_id;
            $this->value = $result->value;
            $this->form_id = $result->form_id;
        } else
        {
            return false;
        }

        return true;
    }

    public function remove_from_db()
    {
        return RM_DBManager::remove_row('SUBMISSION_FIELDS', $this->sub_field_id);
    }

}
