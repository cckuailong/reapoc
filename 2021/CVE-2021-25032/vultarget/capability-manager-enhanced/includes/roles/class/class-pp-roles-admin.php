<?php

class Pp_Roles_Admin
{

    /**
     * The capability
     *
     * @access   protected
     * @var string
     */
    protected $capability = 'manage_options';

    /**
     * Roles list table instance
     *
     * @var null
     */
    protected $roles_list_table;

    /**
     * Initialize the class and set its properties.
     *
     */
    public function __construct()
    {
        global $hook_suffix;    //avoid warning outputs
        if (!isset($hook_suffix)) {
            $hook_suffix = '';
        }

        $this->roles_list_table = null;
        $this->get_roles_list_table();
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

    /**
     * Returns the admin list table instance
     *
     * @return PP_Capabilities_Roles_List_Table
     */
    public function get_roles_list_table()
    {
        if ($this->roles_list_table === null) {
            if (!class_exists('PP_Capabilities_Roles_List_Table')) {
                require_once plugin_dir_path(__FILE__) . 'class-pp-roles-list-table.php';
            }

            $this->roles_list_table = new PP_Capabilities_Roles_List_Table([
                'screen' => get_current_screen()
            ]);
        }

        return $this->roles_list_table;
    }

    /**
     * Display admin flash notices
     */
    public function admin_notices()
    {
        echo pp_capabilities_roles()->notify->display();
    }

    /**
     * Handle post actions
     */
    public function handle_actions()
    {

        $actions = pp_capabilities_roles()->actions;

        $actions->handle();
    }

}
