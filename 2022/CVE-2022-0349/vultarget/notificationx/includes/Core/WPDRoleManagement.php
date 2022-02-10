<?php

namespace NotificationX\Core;


/**
 * Role Management Class for WPDev
 */
class WPDRoleManagement {
    /**
     *  [
     *      'read_notificationx' => [
     *          'roles' => ['administrator'],
     *          'map'   => [],
     *      ],
     *      'edit_notificationx' => [
     *          'roles' => ['administrator'],
     *          'map'   => ['read_notificationx'],
     *      ],
     *      'edit_notificationx_settings' => [
     *          'roles' => ['administrator'],
     *          'map'   => ['read_notificationx'],
     *      ],
     *      'read_notificationx_analytics' => [
     *          'roles' => ['administrator'],
     *          'map'   => ['read_notificationx'],
     *      ],
     *  ];
     *
     * @var array
     */
    public $cap_roles = [];

    /**
     * Initial Invoked
     */
    public function __construct($cap_roles){
        $this->cap_roles = $cap_roles;
        add_filter('user_has_cap', array($this, 'allow_admin'), 10, 4);
        add_filter('map_meta_cap', array($this, 'map_meta_cap'), 10, 4);
        add_action('wpd_add_cap', array($this, 'add_role_caps'), 10);
    }

    /**
     * Add caps to selected roles on settings save.
     *
     * @param array $cap_roles
     * @return void
     */
    public function add_role_caps($cap_roles){
        global $wp_roles;
        $all_roles = $wp_roles->role_objects;

        if(is_array($all_roles)){
            foreach($all_roles as $role_name => $role){
                foreach ($cap_roles as $cap => $_role) {
                    if($role_name == 'administrator'){
                        $role->add_cap($cap);
                    }
                    elseif(in_array($role_name, $_role['roles'])){
                        $role->add_cap($cap);
                    }
                    else{
                        $role->remove_cap($cap);
                    }
                }
            }
        }

    }

    /**
     * Enable caps based on passed cap_roles.
     *
     *
     * @return array
     */
    public function allow_admin($allcaps, $caps, $args, $user) {
        foreach ($this->cap_roles as $cap => $_role) {
            // admin has all caps.
            if(!empty($allcaps['administrator'])){
                $allcaps[$cap] = true;
            }
            // if user has cap then also add the mapped(dependency) cap.
            elseif(!empty($allcaps[$cap]) && !empty($_role['map']) && is_array($_role['map'])){
                foreach ($_role['map'] as $key => $map) {
                    if(in_array($map, $caps)){
                        $allcaps[$map] = true;
                    }
                }
            }
        }
        return $allcaps;
    }

    /**
     * Add additional cap for specified cap, based on passed cap_roles.
     *
     *
     * @return array
     */
    public function map_meta_cap($caps, $cap, $user_id, $args) {
        foreach ($this->cap_roles as $_cap => $_role) {
            if($cap == $_cap && !empty($_role['map']) && is_array($_role['map'])){
                foreach ($_role['map'] as $key => $cap) {
                    $caps[] = $cap;
                }
            }
        }
        return $caps;
    }

}
