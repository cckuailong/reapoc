<?php

class Pp_Roles_Loader
{

    /**
     * The array of actions registered with WordPress.
     *
     * @access   protected
     * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @access   protected
     * @var      array $filters The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters;

    /**
     * All instances registered in the Loader
     *
     * @var array
     */
    protected $instances;

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     */
    public function __construct()
    {

        $this->actions = [];
        $this->filters = [];
        $this->instances = [];

    }

    /**
     * Gets the value of an instance registered in the loader
     *
     * @param $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        return isset($this->instances[$key]) ? $this->instances[$key] : null;
    }

    /**
     * Sets the value of a instance registered in the loader
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        if (is_string($key)) {
            $this->instances[$key] = $value;
        }

    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @param string $hook The name of the WordPress action that is being registered.
     * @param object $component A reference to the instance of the object on which the action is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default
     *                              is 1.
     *
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @param string $hook The name of the WordPress filter that is being registered.
     * @param object $component A reference to the instance of the object on which the filter is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default
     *                              is 1
     *
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @param array $hooks The collection of hooks that is being registered (that is, actions or filters).
     * @param string $hook The name of the WordPress filter that is being registered.
     * @param object $component A reference to the instance of the object on which the filter is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority The priority at which the function should be fired.
     * @param int $accepted_args The number of arguments that should be passed to the $callback.
     *
     * @return   array                                  The collection of actions and filters registered with WordPress.
     * @access   private
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {

        $hooks[] = [
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        ];

        return $hooks;

    }

    /**
     * Register the filters and actions with WordPress.
     *
     */
    public function run()
    {

        foreach ($this->filters as $hook) {
            add_filter( 
                $hook['hook'], 
                [$hook['component'], $hook['callback']], 
                $hook['priority'], 
                $hook['accepted_args']
            );
        }

        foreach ($this->actions as $hook) {
            add_action(
                $hook['hook'], 
                [$hook['component'], $hook['callback']], 
                $hook['priority'], 
                $hook['accepted_args']
            );
        }

    }

}
