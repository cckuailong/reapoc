<?php

class PP_Capabilities_Roles
{

    /**
     * The loader that's responsible for maintaining and registering all roles hooks
     *
     * @access   protected
     * @var      Pp_Roles_Loader $loader Maintains and registers all roles hooks
     */
    protected $loader;

    /**
     * Singleton instance
     *
     * @var null
     */
    protected static $instance = null;

    /**
     * The Singleton method
     *
     * @return self
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {

    }

    /**
     * Load the required dependencies.
     * 
     * - Pp_Roles_Loader. Orchestrates the hooks.
     * - Pp_Roles_Admin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once 'class-pp-roles-loader.php';

        if (is_admin()) {
            /**
             * The class responsible for manage roles and capabilities.
             */
            require_once 'class-pp-roles-manager.php';
            /**
             * The class responsible for defining all actions that occur in the admin area.
             */
            require_once 'class-pp-roles-admin.php';
            /**
             * The class responsible for handling form submissions
             */
            require_once 'class-pp-roles-actions.php';
        }

        $this->loader = new Pp_Roles_Loader();

    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @access   private
     */
    private function define_admin_hooks()
    {

        if (is_admin()) {
            /**
             * Roles manager
             */
            $manager = new Pp_Roles_Manager();
            $this->loader->set('manager', $manager);

            /**
             * Roles admin
             */
            $role_admin = new Pp_Roles_Admin();
            $this->loader->set('admin', $role_admin);

            /**
             * Actions to handle
             */
            $actions = new Pp_Roles_Actions();
            $this->loader->set('actions', $actions);

            /**
             * Notifications
             */
            $notifications = new PP_Capabilities_Notices();
            $this->loader->set('notify', $notifications);
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     */
    public function run()
    {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks.
     *
     * @return    Pp_Roles_Loader    Orchestrates the hooks.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Gets an instance from the loader
     *
     * @param string $key
     *
     * @return mixed|null The instance
     *
     * @access    public
     *
     */
    public function __get($key)
    {
        return $this->get_loader()->get($key);
    }

    /**
     * Sets an instance in the loader
     *
     * @param string $key
     * @param mixed $value
     *
     * @access    public
     *
     */
    public function __set($key, $value)
    {
        $this->get_loader()->set($key, $value);
    }

}
