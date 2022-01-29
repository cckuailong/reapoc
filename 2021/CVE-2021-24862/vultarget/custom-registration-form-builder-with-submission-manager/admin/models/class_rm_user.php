<?php

/**
 * User class for the plugin
 * 
 * This class has all the methods for user related functionality to be used through 
 * out the plugin.
 * @link       http://registration_magic.com
 * @since      1.0.0
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/admin
 * @author CMSHelplive
 */
class RM_User
{

    /**
     * WP_user object
     *
     * @since    1.0.0
     * @access   public
     * @var      object     $user
     */
    public $user;

    
    public function __construct($user_id=0)
    {   
        $user_id = absint($user_id);
        if($user_id)
            $this->user = get_userdata($user_id);
    }


    /**
     * Get user's property
     * 
     * This function is used to get the user property of this class if no argument is supplied 
     * or returns the user_data object with the values of user fields specified or false if fails
     * 
     * @param   array|string     $field_name    name of the user data field 
     * @return  mixed     $userdata|false                                                
     */
    public function get($field_name = null)
    {
        if (!$field_name)
            return $this->user;
        
        elseif(is_array($field_name)){
            $user_data = new stdClass;
            foreach($field_name as $field_label){
                $user_data->$field_label = (string)$this->user->$field_label;
            }
        }
        else{
           $user_data = (string)$this->user->$field_name;
        }
        
        if(!$user_data)
            return false;
        
        return $user_data;
    }

    /**
     * Deactivates the user by simply upadting its meta information
     * 
     * @return boolean
     */
    public function deactivate_user($user_id)
    {
        $user_id= empty($user_id) ? $this->user->ID : $user_id;
        $result = update_user_meta($user_id, 'rm_user_status', 1);
        if ($result)
        {
          do_action('rm_user_deactivated',$user_id);
          return true;
        } 
        else
        {
            return false;
        }
    }
    
    public function activate_user($user_id)
    {
        return update_user_meta($user_id,'rm_user_status',0);
    }		  

}
