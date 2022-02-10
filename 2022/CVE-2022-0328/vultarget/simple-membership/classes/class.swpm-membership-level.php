<?php

/**
 * Description of SwpmMembershipLevel
 */
class SwpmMembershipLevel {

    const NO_EXPIRY = 0;
    const DAYS = 1;
    const WEEKS = 2;
    const MONTHS = 3;
    const YEARS = 4;
    const FIXED_DATE = 5;

    private static $_instance = null;

    private function __construct() {
        //NOP
    }

    public static function get_instance() {
        self::$_instance = empty(self::$_instance) ? new SwpmMembershipLevel() : self::$_instance;
        return self::$_instance;
    }

    public static function get_level_duration_type_string($type){
        $type_string = '';
        switch($type){
            case '0': $type_string = 'No Expiry or Until Cancelled';
                break;
            case '1': $type_string = 'Days';
                break;
            case '2': $type_string = 'Weeks';
                break;
            case '3': $type_string = 'Months';
                break;
            case '4': $type_string = 'Years';
                break;
            case '5': $type_string = 'Fixed Date';
                break;
        }
        return $type_string;
    }
    
    public function create_level() {
        //Check we are on the admin end and user has management permission 
        SwpmMiscUtils::check_user_permission_and_is_admin('membership level creation');
        
        //Check nonce
        if ( !isset($_POST['_wpnonce_create_swpmlevel_admin_end']) || !wp_verify_nonce($_POST['_wpnonce_create_swpmlevel_admin_end'], 'create_swpmlevel_admin_end' )){
            //Nonce check failed.
            wp_die(SwpmUtils::_("Error! Nonce verification failed for membership level creation from admin end."));
        }
        
        global $wpdb;
        $level = SwpmTransfer::$default_level_fields;
        $form = new SwpmLevelForm($level);
        if ($form->is_valid()) {
            $level_info = $form->get_sanitized();
            $wpdb->insert($wpdb->prefix . "swpm_membership_tbl", $level_info);
            $id = $wpdb->insert_id;
            //save email_activation option
            $email_activation=filter_input(INPUT_POST,'email_activation',FILTER_SANITIZE_NUMBER_INT);
            update_option('swpm_email_activation_lvl_'.$id, $email_activation, false);

            $custom = apply_filters('swpm_admin_add_membership_level', array());
            $this->save_custom_fields($id, $custom);
            $message = array('succeeded' => true, 'message' => '<p>' . SwpmUtils::_('Membership Level Creation Successful.') . '</p>');
            SwpmTransfer::get_instance()->set('status', $message);
            wp_redirect('admin.php?page=simple_wp_membership_levels');
            exit(0);
        }
        $message = array('succeeded' => false, 'message' => SwpmUtils::_('Please correct the following:'), 'extra' => $form->get_errors());
        SwpmTransfer::get_instance()->set('status', $message);
    }

    public function edit_level($id) {
        //Check we are on the admin end and user has management permission 
        SwpmMiscUtils::check_user_permission_and_is_admin('membership level edit');
        
        //Check nonce
        if ( !isset($_POST['_wpnonce_edit_swpmlevel_admin_end']) || !wp_verify_nonce($_POST['_wpnonce_edit_swpmlevel_admin_end'], 'edit_swpmlevel_admin_end' )){
            //Nonce check failed.
            wp_die(SwpmUtils::_("Error! Nonce verification failed for membership level edit from admin end."));
        }
        
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id = %d", $id);
        $level = $wpdb->get_row($query, ARRAY_A);
        $form = new SwpmLevelForm($level);
        if ($form->is_valid()) {
            $wpdb->update($wpdb->prefix . "swpm_membership_tbl", $form->get_sanitized(), array('id' => $id));
            //@todo meta table and collect all relevant info and pass as argument
            //save email_activation option
            $email_activation=filter_input(INPUT_POST,'email_activation',FILTER_SANITIZE_NUMBER_INT);
            update_option('swpm_email_activation_lvl_'.$id, $email_activation, false);

            $custom = apply_filters('swpm_admin_edit_membership_level', array(), $id);
            $this->save_custom_fields($id, $custom);
            $message = array('succeeded' => true, 'message' => '<p>'. SwpmUtils::_('Membership Level Updated Successfully.') . '</p>');
            SwpmTransfer::get_instance()->set('status', $message);
            wp_redirect('admin.php?page=simple_wp_membership_levels');
            exit(0);
        }
        $message = array('succeeded' => false, 'message' => SwpmUtils::_('Please correct the following:'), 'extra' => $form->get_errors());
        SwpmTransfer::get_instance()->set('status', $message);
    }

    private function save_custom_fields($level_id, $data) {
        $custom_obj = SwpmMembershipLevelCustom::get_instance_by_id($level_id);
        foreach ($data as $item) {
            $custom_obj->set($item);
        }
    }

}
