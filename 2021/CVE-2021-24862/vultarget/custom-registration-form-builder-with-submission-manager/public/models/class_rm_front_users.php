<?php

/*
 * 
 */

/**
 * Description of RM_Front_Users
 *
 * @author CMSHelplive
 */
class RM_Front_Users extends RM_Base_Model
{

    public $id;
    public $email;
    public $otp_code;
    public $last_activity_time;
    public $created_date;
    public $type;
    public $expiry;
    
    public function __construct($type=null)
    {
        $this->initialized = false;
        $this->id = NULL;
        if(defined('REGMAGIC_ADDON')) {
            if(empty($type)){
                $this->type='n';
            }
            else
            {
                 $this->type='f';
            }
        }
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_email()
    {
        return $this->email;
    }

    public function get_otp_code()
    {
        return $this->otp_code;
    }

    public function get_last_activity_time()
    {
        return $this->last_activity_time;
    }

    public function get_created_date()
    {
        return $this->created_date;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function set_email($email)
    {
        $this->email = $email;
    }

    public function set_otp_code($otp_code)
    {
        $this->otp_code = $otp_code;
    }

    public function set_last_activity_time($last_activity_time)
    {
        $this->last_activity_time = $last_activity_time;
    }

    public function set_created_date($created_date)
    {
        $this->created_date = $created_date;
    }
    
    public function get_type(){
        return $this->type;
    }

    public function insert_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }

        if ($this->id)
        {
            return false;
        }
        
        if(defined('REGMAGIC_ADDON')) {
            // Deleting any other OTP code from same user
            RM_DBManager::delete_rows('FRONT_USERS', array('email'=>$this->email));
        }

        $data = array(
            'email' => $this->email,
            'otp_code' => $this->otp_code,
            'last_activity_time' => RM_Utilities::get_current_time(),
            'created_date' => RM_Utilities::get_current_time(),
        );

        $data_specifiers = array(
            '%s',
            '%s',
            '%s',
            '%s'
        );
        
        if(defined('REGMAGIC_ADDON')) {
            $data['type'] = $this->type;
            $data['expiry'] = $this->expiry;
            array_push($data_specifiers,'%s','%s');
        }

        $result = RM_DBManager::insert_row('FRONT_USERS', $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        $this->id = $result;

        return $result;
    }

    public function update_into_db()
    {
        if (!$this->initialized)
        {
            return false;
        }
        if (!$this->id)
        {
            return false;
        }

        $data = array(
            'email' => $this->email,
            'otp_code' => $this->otp_code,
            'last_activity_time' => RM_Utilities::get_current_time(),
        );

        $data_specifiers = array(
            '%s',
            '%s',
            '%s'
        );

        $result = RM_DBManager::update_row('FRONT_USERS', $this->field_id, $data, $data_specifiers);

        if (!$result)
        {
            return false;
        }

        return true;
    }

    public function load_from_db($id, $should_set_id = true)
    {

        $result = RM_DBManager::get_row('FRONT_USERS', $field_id);

        if (null !== $result)
        {
            if ($should_set_id)
                $this->id = $id;
            else
                $this->field_id = null;
            $this->email = $result['email'];
            $this->otp_code = $result['otp_code'];
            $this->last_activity_time = $result['last_activity_time'];
            $this->created_date = $result['created_date'];
        } else
        {
            return false;
        }
        $this->initialized = true;
        return true;
    }

    public function remove_from_db()
    {
        return RM_DBManager::remove_row('FRONT_USERS', $this->field_id);
    }
    
    public function set(array $config){
        foreach($config as $key=>$c){
            if(property_exists($this,$key)){
                $this->$key= $c;   
            }
        }
        $this->initialized= true;
    }

}