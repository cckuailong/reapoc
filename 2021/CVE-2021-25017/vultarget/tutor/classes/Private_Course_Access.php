<?php
namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;


class Private_Course_Access {
    
    private $allow_empty=false;

    public function __construct() {
        add_action('pre_get_posts', array($this, 'enable_private_access'));
    }

    public function enable_private_access($query=null){

        if(!is_admin() && is_user_logged_in() ){

            global $wpdb;
            $p_name = isset($query->query['name']) ? $query->query['name'] : '';
            $p_name = esc_sql($p_name);

            if($this->allow_empty && empty($p_name)){
                $query->set('post_status', array('private', 'publish'));
                return;
            }

            // Get using raw query to speed up
            $private_query = "SELECT ID, post_parent FROM {$wpdb->posts} WHERE post_type='courses' AND post_name='{$p_name}' AND post_status='private'";
            $result = $wpdb->get_results($private_query);
            $private_course_id = (is_array($result) && isset($result[0])) ? $result[0]->ID : 0;
            
            if($private_course_id>0 && tutils()->is_enrolled($private_course_id)){
                $this->allow_empty = true;
                $query->set('post_status', array('private', 'publish'));
            }
        }
    }
}