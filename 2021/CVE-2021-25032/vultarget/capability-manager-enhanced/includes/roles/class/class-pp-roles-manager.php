<?php

class Pp_Roles_Manager
{

    /**
     * Pp_Roles_Manager constructor.
     */
    public function __construct()
    {

    }

    /**
     * Returns an array of all the available roles.
     * This method is used to show the roles list table.
     *
     * @return array[]
     */
    public function get_roles_for_list_table()
    {
        $roles = wp_roles()->roles; // get_editable_roles();
        $count = count_users();
        $res = [];
        foreach ($roles as $role => $detail) {
            $res[] = [
                'role' => $role,
                'name' => $detail['name'],
                'count' => isset($count['avail_roles'][$role]) ? $count['avail_roles'][$role] : 0,
                'is_system' => $this->is_system_role($role)
            ];
        }

        return $res;
    }

    /**
     * Array containing all default wordpress roles
     *
     * @return array
     */
    public function get_system_roles()
    {

        $roles = [
            'administrator',
            'editor',
            'author',
            'contributor',
            'subscriber',
            'revisor'
        ];

        $roles = apply_filters('pp-roles-get-system-roles', $roles);

        return $roles;
    }

    /**
     * Checks if the given role is a system role
     *
     * @param $role
     *
     * @return bool
     */
    public function is_system_role($role)
    {

        $is = in_array($role, $this->get_system_roles());

        $is = apply_filters('pp-roles-is-system-role', $is, $role);

        return $is;
    }

    /**
     * Checks if he provided role exist
     *
     * @param $role
     *
     * @return bool
     */
    public function is_role($role)
    {
        return wp_roles()->is_role($role);
    }

    /**
     * Get role object from role
     *
     * @param $role
     *
     * @return WP_Role|null
     */
    public function get_role($role)
    {
        return wp_roles()->get_role($role);
    }

    /**
     * Get role name string form a role
     *
     * @param $role
     *
     * @return string
     */
    public function get_role_name($role)
    {
        if ($this->is_role($role)) {
            return wp_roles()->role_names[$role];
        }

        return $role;
    }

    /**
     * Add role to the system
     *
     * @param $role
     * @param $name
     *
     * @return WP_Role|null
     */
    public function add_role($role, $name)
    {
        $result = add_role($role, $name);

        return $result;
    }

    /**
     * Deletes a role from the system
     *
     * @param $role
     *
     * @return bool
     */
    public function delete_role($role, $args = [])
    {
        global $wpdb, $wp_roles;

        $default = get_option('default_role');

        if ($default == $role) {
            return false;
        }

		$like = '%' . $wpdb->esc_like( $role ) . '%';

		$query = $wpdb->prepare( "SELECT ID FROM {$wpdb->usermeta} INNER JOIN {$wpdb->users} "
			. "ON {$wpdb->usermeta}.user_id = {$wpdb->users}.ID "
			. "WHERE meta_key='{$wpdb->prefix}capabilities' AND meta_value LIKE %s", $like );

		$users = $wpdb->get_results($query);

		// Array of all roles except the one being deleted, for use below
		$role_names = array_diff_key( $wp_roles->role_names, [$role => true] );
        
		$count = 0;
		foreach ( $users as $u ) {
			$skip_role_set = false;
		
			$user = new WP_User($u->ID);
			if ( $user->has_cap($role) ) {		// Check again the user has the deleting role
				// Role may have been assigned supplementally.  Don't move a user to default role if they still have one or more roles following the deletion.
				foreach( array_keys($role_names) as $_role_name ) {
					if ( $user->has_cap($_role_name) ) {
						$skip_role_set = true;
						break;
					}
				}
				
				if ( ! $skip_role_set ) {
					$user->set_role($default);
					$count++;
				}
			}
		}

		remove_role($role);

		if ( $customized_roles = get_option( 'pp_customized_roles' ) ) {
			if ( isset( $customized_roles[$role] ) ) {
				unset( $customized_roles[$role] );
				update_option( 'pp_customized_roles', $customized_roles );
			}
		}

		return $count;
    }
}
