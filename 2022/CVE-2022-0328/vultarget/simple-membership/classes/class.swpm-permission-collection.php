<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bPermissionCollection
 *
 * @author nur
 */
class SwpmPermissionCollection {
    protected $permissions;
    protected static $instance;
    
    protected function __construct() {
        $this->permissions = array();
    }
    
    public static function get_instance(){
        self::$_this = empty(self::$_this)? new SwpmPermissionCollection():self::$_this;
        return self::$_this;
    }    
    
    public function load($level_ids = array()){
        if (empty($level_ids)){
            global $wpdb;
            $level_ids = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}swpm_membership_tbl WHERE id != 1");
        }
        
        foreach($level_ids as $id){
            $this->permissions[] = SwpmPermission::get_instance($id);
        }
    }
    
    public function get_permitted_levels($post_id){
        $levels = array();
        
        foreach($this->permissions as $permission){
            if ($permission->is_permitted($post_id)){
                $levels[$permission->get($id)] = $permission->get('alias');
            }
        }
        
        return $levels;
    }
}
