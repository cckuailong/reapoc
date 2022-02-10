<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC User class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_user extends MEC_base
{
    /**
     * @var MEC_main
     */
    public $main;

    /**
     * @var MEC_db
     */
    public $db;

    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // MEC Main library
        $this->main = $this->getMain();

        // MEC DB Library
        $this->db = $this->getDB();

        // MEC settings
        $this->settings = $this->main->get_settings();
    }

    public function register($attendee, $args)
    {
        $name = isset($attendee['name']) ? $attendee['name'] : '';
        $raw = (isset($attendee['reg']) and is_array($attendee['reg'])) ? $attendee['reg'] : array();

        $email = isset($attendee['email']) ? $attendee['email'] : '';
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

        $reg = array();
        foreach($raw as $k => $v) $reg[$k] = (is_array($v) ? $v : stripslashes($v));

        $existed_user_id = $this->main->email_exists($email);

        // User already exist
        if($existed_user_id !== false) return $existed_user_id;

        // Update WordPress user first name and last name
        if(strpos($name, ',') !== false)
        {
            $ex = explode(',', $name);
            $first_name = isset($ex[0]) ? $ex[0] : '';
            $last_name = '';
        }
        else
        {
            $ex = explode(' ', $name);
            $first_name = isset($ex[0]) ? $ex[0] : '';
            $last_name = '';
        }

        if(isset($ex[1]))
        {
            unset($ex[0]);
            $last_name = implode(' ', $ex);
        }

        // Registration is disabled
        if(isset($this->settings['booking_registration']) and !$this->settings['booking_registration'])
        {
            $existed_user_id = $this->db->select("SELECT `id` FROM `#__mec_users` WHERE `email`='".$this->db->escape($email)."'", 'loadResult');

            // User already exist
            if($existed_user_id) return $existed_user_id;

            $now = date('Y-m-d H:i:s');
            $user_id = $this->db->q("INSERT INTO `#__mec_users` (`first_name`,`last_name`,`email`,`reg`,`created_at`,`updated_at`) VALUES ('".$this->db->escape($first_name)."','".$this->db->escape($last_name)."','".$this->db->escape($email)."','".$this->db->escape(json_encode($reg))."','".$now."','".$now."')", "INSERT");
        }
        else
        {
            $username = $email;
            $password = wp_generate_password(12, true, true);

            if(isset($args['username']) and trim($args['username'])) $username = $args['username'];
            if(isset($args['password']) and trim($args['password'])) $password = $args['password'];

            $user_id = $this->main->register_user($username, $email, $password);

            $user = new stdClass();
            $user->ID = $user_id;
            $user->first_name = $first_name;
            $user->last_name = $last_name;

            wp_update_user($user);
            update_user_meta($user_id, 'mec_name', $name);
            update_user_meta($user_id, 'mec_reg', $reg);
            update_user_meta($user_id, 'nickname', $name);

            // Map Data
            $event_id = (isset($args['event_id']) ? $args['event_id'] : 0);
            if($event_id)
            {
                $reg_fields = $this->main->get_reg_fields($event_id);

                foreach($reg as $reg_id => $reg_value)
                {
                    $reg_field = (isset($reg_fields[$reg_id]) ? $reg_fields[$reg_id] : array());
                    if(isset($reg_field['mapping']) and trim($reg_field['mapping']))
                    {
                        update_user_meta($user_id, $reg_field['mapping'], (is_array($reg_value) ? implode(',', $reg_value) : $reg_value));
                    }
                }
            }

            // Set the User Role
            $role = (isset($this->settings['booking_user_role']) and trim($this->settings['booking_user_role'])) ? $this->settings['booking_user_role'] : 'subscriber';

            $wpuser = new WP_User($user_id);
            $wpuser->set_role($role);
        }

        return $user_id;
    }

    public function assign($booking_id, $user_id)
    {
        // Registration is disabled
        if(isset($this->settings['booking_registration']) and !$this->settings['booking_registration'] and !get_user_by('ID', $user_id)) update_post_meta($booking_id, 'mec_user_id', $user_id);
        else update_post_meta($booking_id, 'mec_user_id', 'wp');
    }

    public function get($id)
    {
        // Registration is disabled
        if(isset($this->settings['booking_registration']) and !$this->settings['booking_registration'])
        {
            $user = $this->mec($id);
            if(!$user) $user = $this->wp($id);
        }
        else
        {
            $user = $this->wp($id);
            if(!$user) $user = $this->mec($id);
        }

        return $user;
    }

    public function mec($id)
    {
        $data = $this->db->select("SELECT * FROM `#__mec_users` WHERE `id`=".((int) $id), 'loadObject');
        if(!$data) return NULL;

        $user = new stdClass();
        $user->ID = $data->id;
        $user->first_name = stripslashes($data->first_name);
        $user->last_name = stripslashes($data->last_name);
        $user->display_name = trim(stripslashes($data->first_name).' '.stripslashes($data->last_name));
        $user->email = $data->email;
        $user->user_email = $data->email;
        $user->user_registered = $data->created_at;
        $user->data = $user;

        return $user;
    }

    public function wp($id)
    {
        return get_userdata($id);
    }

    public function booking($id)
    {
        $mec_user_id = get_post_meta($id, 'mec_user_id', true);
        if(trim($mec_user_id) and is_numeric($mec_user_id)) return $this->mec($mec_user_id);

        return $this->wp(get_post($id)->post_author);
    }

    public function by_email($email)
    {
        return $this->get($this->id('email', $email));
    }

    public function id($field, $value)
    {
        $id = NULL;

        // Registration is disabled
        if(isset($this->settings['booking_registration']) and !$this->settings['booking_registration'])
        {
            $id = $this->db->select("SELECT `id` FROM `#__mec_users` WHERE `".$field."`='".$this->db->escape($value)."'", 'loadResult');
            if(!$id)
            {
                $user = get_user_by($field, $value);
                if(isset($user->ID)) $id = $user->ID;
            }
        }
        else
        {
            $user = get_user_by($field, $value);
            if(isset($user->ID)) $id = $user->ID;

            if(!$id) $id = $this->db->select("SELECT `id` FROM `#__mec_users` WHERE `".$field."`='".$this->db->escape($value)."'", 'loadResult');
        }

        return $id;
    }
}