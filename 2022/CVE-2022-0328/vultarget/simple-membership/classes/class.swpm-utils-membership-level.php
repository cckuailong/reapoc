<?php

/**
 * This class will contain various utility functions for the membership access level.
 */

class SwpmMembershipLevelUtils {

    public static function get_membership_level_name_of_a_member($member_id){
        $user_row = SwpmMemberUtils::get_user_by_id($member_id);
        $level_id = $user_row->membership_level;
        
        $level_row = SwpmUtils::get_membership_level_row_by_id($level_id);
        $level_name = $level_row->alias;
        return $level_name;
    }
    
    public static function get_membership_level_name_by_level_id($level_id){        
        $level_row = SwpmUtils::get_membership_level_row_by_id($level_id);
        $level_name = $level_row->alias;
        return $level_name;
    }
    
    public static function get_all_membership_levels_in_array(){
        //Creates an array like the following with all the available levels.
        //Array ( [2] => Free Level, [3] => Silver Level, [4] => Gold Level )
        
        global $wpdb;
        $levels_array = array();
        $query = "SELECT alias, id FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE id != 1";
        $levels = $wpdb->get_results($query);
        foreach ($levels as $level) {
            if(isset($level->id)){
                $levels_array[$level->id] = $level->alias;
            }
        }
        return $levels_array; 
    }
}
