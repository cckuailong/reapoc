<?php

class Pp_Roles_Actions
{

    /**
     * @var string
     */
    protected $capability = 'manage_options';

    /**
     * @var Pp_Roles_Manager
     */
    protected $manager = null;

    /**
     * @var array
     */
    protected $actions = [
        'pp-roles-add-role',
        'pp-roles-delete-role',
        'pp-roles-hide-role',
        'pp-roles-unhide-role',
    ];

    /**
     * Pp_Roles_Actions constructor.
     */
    public function __construct()
    {
        $this->manager = pp_capabilities_roles()->manager;

        if (did_action('wp_ajax_pp-roles-add-role') || did_action('wp_ajax_pp-roles-delete-role')) {
            $this->handle();
        }
    }

    /**
     * Is ajax request
     *
     * @return bool
     */
    protected function is_ajax()
    {
        return (defined('DOING_AJAX') && DOING_AJAX);
    }

    /**
     * Handle post actions
     */
    public function handle()
    {
        $current_action = $this->current_action();

        if (in_array($current_action, $this->actions)) {
            $current_action = str_replace('pp-roles-', '', $current_action);
            $current_action = str_replace('-', '_', $current_action);
            $this->$current_action();
        }
    }

    /**
     * Get the current action selected from the bulk actions dropdown.
     *
     * @return string|false The action name or False if no action was selected
     */
    protected function current_action()
    {
        if (isset($_REQUEST['filter_action']) && !empty($_REQUEST['filter_action'])) {
            return false;
        }

        if (isset($_REQUEST['action']) && -1 != $_REQUEST['action']) {
            return $_REQUEST['action'];
        }

        if (isset($_REQUEST['action2']) && -1 != $_REQUEST['action2']) {
            return $_REQUEST['action2'];
        }

        return false;
    }

    protected function notify_success($message) {
        $this->notify($message, 'success', false);
    }

    protected function notify_error($message) {
        $this->notify($message, 'error', false);
    }

    /**
     * Notify the user with a message. Handles ajax and post requests
     *
     * @param string $message The message to show to the user
     * @param string $type The type of message to show [error|success|warning\info]
     * @param bool $redirect If we should redirect to referrer
     */
    protected function notify($message, $type = 'error', $redirect = true)
    {
        if (!in_array($type, ['error', 'success', 'warning'])) {
            $type = 'error';
        }

        if ($this->is_ajax()) {
            //$format = '<div class="notice notice-%s is-dismissible"><p>%s</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'capsman-enhanced') . '</span></button></div>';
            $format = '<div class="notice notice-%s is-dismissible"><p>%s</p></div>';
            wp_send_json_error(sprintf($format, $type, $message));
            exit;
        } else {
            //enqueue message
            pp_capabilities_roles()->notify->add($type, $message);

            // @todo: migrate Capabilities screen notice display method
            if (!empty($_REQUEST['page']) && ('pp-capabilities' == $_REQUEST['page'])) {
                $redirect = false;
            }

            if ($redirect) {
                $redirect_url = wp_get_referer();
                $redirect_url = wp_get_raw_referer();
	            
                if (empty($redirect_url)) {
                    $params = [
                        'page' => 'pp-capabilities-roles',
                    ];
                    $redirect_url = add_query_arg($params, admin_url('admin.php'));
                }

                wp_safe_redirect($redirect_url);
                die();
            }
        }
    }

    /**
     * Check if the user is able to access this page
     */
    protected function check_permissions()
    {

        if (!current_user_can($this->capability)) {
            $out = __('You do not have sufficient permissions to perform this action.', 'capsman-enhanced');
            $this->notify($out);
        }
    }

    /**
     * Check nonce and notify if error
     *
     * @param string $action
     * @param string $query_arg
     */
    protected function check_nonce($action = '-1', $query_arg = '_wpnonce')
    {

        $checked = isset($_REQUEST[$query_arg]) && wp_verify_nonce($_REQUEST[$query_arg], $action);
        if (!$checked) {
            $out = __('Your link has expired, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }
    }

    /**
     * Handles add role action
     */
    public function add_role()
    {
        /**
         * Check capabilities
         */
        $this->check_permissions();

        /**
         * Check nonce
         */
        $this->check_nonce('add-role');

        if (empty($_REQUEST['name'])) {
            $out = __('Missing parameters, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }

        /**
         * Validate input data
         */
        require_once(dirname(CME_FILE).'/includes/handler.php');
        $capsman_handler = new CapsmanHandler();
        $role = $capsman_handler->createNewName($_REQUEST['name']);
        
        /**
         * Check for invalid name entry
         */
        if (!empty($role['error']) && ('invalid_name' == $role['error'])) {
            $out = __(sprintf('Invalid role name entry: %s', "<strong>" . esc_attr($role['name']) . "</strong>"), 'capsman-enhanced');
            $this->notify($out);
        }

        /**
         * Check role doesn't exist
         */
        if (!empty($role['error']) && ('role_exists' == $role['error'])) {
            //this role already exist
            $out = __(sprintf('The role "%s" already exists. Please choose a different name.', "<strong>" . esc_attr($role['name']) . "</strong>"), 'capsman-enhanced');
            $this->notify($out);
        }

        /**
         * Add role
         */
        $result = add_role($role['name'], $role['display'], []);

        if (!$result instanceof WP_Role) {
            $out = __('Something went wrong, the system wasn\'t able to create the role, refresh the page and try again.', 'capsman-enhanced');
            if ($this->notify($out)) {
                return;
            }
        }

        if ($this->is_ajax()) {
            /**
             * The role row
             */
            $count_users = count_users();
            ob_start();

            global $hook_suffix;    //avoid warning outputs
            if (!isset($hook_suffix)) {
                $hook_suffix = '';
            }
            
            pp_capabilities_roles()->admin->get_roles_list_table()->single_row([
                'role' => $result->name,
                'name' => $this->manager->get_role_name($result->name),
                'count' => isset($count_users['avail_roles'][$result->name]) ? $count_users['avail_roles'][$result->name] : 0,
                'is_system' => $this->manager->is_system_role($result->name)
            ]);
            $out = ob_get_clean();

            wp_send_json_success($out);
        } else {
            /**
             * Notify user and redirect
             */
            $out = __(sprintf('The new role %s was created successfully.', '<strong>' . $role . '</strong>'), 'capsman-enhanced');
            $this->notify($out, 'success');
        }
    }

    /**
     * Delete role action
     */
    public function delete_role($role = '', $args = [])
    {
        $defaults = ['allow_system_role_deletion' => false, 'nonce_check' => 'bulk-roles'];
        $args = array_merge($defaults, $args);
        foreach (array_keys($defaults) as $var) {
            $$var = $args[$var];
        }

        if (empty($role)) {
            $role = (isset($_REQUEST['role'])) ? $_REQUEST['role'] : '';
        }

        /**
         * Check capabilities
         */
        $this->check_permissions();

        /**
         * Check nonce
         */
        if ($nonce_check) {
            $this->check_nonce($nonce_check);
        }

        /**
         * Validate input data
         */
        $roles = [];
        if ($role) {
            if (is_string($role)) {
                $input = sanitize_text_field($role);
                $roles[] = $input;
            } else if (is_array($role)) {
                foreach ($role as $key => $id) {
                    $roles[] = sanitize_text_field($id);
                }
            }
        } else {
            return;
        }

        /**
         * If no roles provided return
         */
        if (empty($roles)) {
            $out = __('Missing parameters, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }

        $default = get_option('default_role');
        
		if ( $default == $role ) {
            //ak_admin_error(sprintf(__('Cannot delete default role. You <a href="%s">have to change it first</a>.', 'capsman-enhanced'), 'options-general.php'));
            $this->notify(__('Cannot delete default role. You <a href="%s">have to change it first</a>.', 'capsman-enhanced'), 'options-general.php');
			return;
		}

        /**
         * Check if is a system role
         */
        if (!$allow_system_role_deletion) {
            foreach ($roles as $key => $role) {
	
                if ($this->manager->is_system_role($role)) {
                    unset($roles[$key]);
                }
            }

            if (empty($roles)) {
                $out = __('Deleting a system role is not allowed.', 'capsman-enhanced');
                $this->notify($out);
            }
        }

        /**
         * Delete roles
         */
        $deleted = 0;
        $user_count = 0;

        foreach ($roles as $role) {
            if (pp_capabilities_is_editable_role($role)) {
                $moved_users = $this->manager->delete_role($role);
                if (false !== $moved_users) {
                    $deleted++;
                    $user_count = $user_count + $moved_users;
                }
            }
        }

        if ($deleted) {
            $default_name = (wp_roles()->is_role($default)) ? wp_roles()->role_names[$default] : $default;
            $users_message = ($user_count) ? sprintf(__('%1$d users moved to default role %2$s.', 'capsman-enhanced'), $user_count, $default_name) : '';
            
            $role_name = (wp_roles()->is_role($roles[0])) ? wp_roles()->role_names[$roles[0]] : $roles[0];

            $single = sprintf(
                __('The role %1$s was successfully deleted. %2$s', 'capsman-enhanced'), 
                '<strong>' . $roles[0] . '</strong>',
                $users_message
            );
            
            $plural = sprintf(
                __('The selected %1$s roles were successfully deleted. %2$s', 'capsman-enhanced'), 
                '<strong>' . $deleted . '</strong>',
                $users_message
            );
            
            $out = _n($single, $plural, $deleted, 'capsman-enhanced');

            if ($this->is_ajax()) {
                wp_send_json_success($out);
            } else {
                $this->notify($out, 'success');
            }
        } else {
            $this->notify(_('The role could not be deleted.', 'capsman-enhanced'));
        }
    }

    /**
     * Hide role action
     */
    public function hide_role($role = '', $args = [])
    {
        if (!defined('PRESSPERMIT_ACTIVE')) {
            return;
        }

        if (empty($role)) {
            $role = (isset($_REQUEST['role'])) ? $_REQUEST['role'] : '';
        }

        /**
         * Check capabilities
         */
        $this->check_permissions();

        /**
         * Validate input data
         */
        $roles = [];
        if ($role) {
            if (is_string($role)) {
                $input = sanitize_text_field($role);
                $roles[] = $input;
            } else if (is_array($role)) {
                foreach ($role as $key => $id) {
                    $roles[] = sanitize_text_field($id);
                }
            }
        } else {
            return;
        }

        /**
         * If no roles provided return
         */
        if (empty($roles)) {
            $out = __('Missing parameters, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }

        $pp_only = (array) pp_capabilities_get_permissions_option( 'supplemental_role_defs' );
        $pp_only = array_merge($pp_only, (array) $roles);
        pp_capabilities_update_permissions_option('supplemental_role_defs', $pp_only);

        $role_name = (wp_roles()->is_role($roles[0])) ? wp_roles()->role_names[$roles[0]] : $roles[0];

        $out = sprintf(
            __('The role %1$s was successfully hidden.', 'capsman-enhanced'), 
            '<strong>' . $roles[0] . '</strong>'
        );
        
        if ($this->is_ajax()) {
            wp_send_json_success($out);
        } else {
            $this->notify($out, 'success');
        }
    }

    /**
     * Unhide role action
     */
    public function unhide_role($role = '', $args = [])
    {
        if (!defined('PRESSPERMIT_ACTIVE')) {
            return;
        }

        if (empty($role)) {
            $role = (isset($_REQUEST['role'])) ? $_REQUEST['role'] : '';
        }

        /**
         * Check capabilities
         */
        $this->check_permissions();

        /**
         * Validate input data
         */
        $roles = [];
        if ($role) {
            if (is_string($role)) {
                $input = sanitize_text_field($role);
                $roles[] = $input;
            } else if (is_array($role)) {
                foreach ($role as $key => $id) {
                    $roles[] = sanitize_text_field($id);
                }
            }
        } else {
            return;
        }

        /**
         * If no roles provided return
         */
        if (empty($roles)) {
            $out = __('Missing parameters, refresh the page and try again.', 'capsman-enhanced');
            $this->notify($out);
        }

        $pp_only = (array) pp_capabilities_get_permissions_option( 'supplemental_role_defs' );
        $pp_only = array_diff($pp_only, (array) $roles);
        pp_capabilities_update_permissions_option('supplemental_role_defs', $pp_only);

        $role_name = (wp_roles()->is_role($roles[0])) ? wp_roles()->role_names[$roles[0]] : $roles[0];

        $out = sprintf(
            __('The role %1$s was successfully unhidden.', 'capsman-enhanced'), 
            '<strong>' . $roles[0] . '</strong>'
        );
        
        if ($this->is_ajax()) {
            wp_send_json_success($out);
        } else {
            $this->notify($out, 'success');
        }
    }
}
