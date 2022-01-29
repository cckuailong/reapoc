<?php

/**
 * Central dispatcher for HTTP request controllers. Dispatches to registered handlers for processing
 * a web request, providing convenient mapping and exception handling facilities.
 */
class RM_Main_Controller implements RM_Controller_Abstract
{

    public $request;
    // Attribute to load xml properties given in rm_config.xml
    public $xml;
    // Setting controller where request will be dispatched
    public $controller;
    // Attribute works as method name in handler class
    public $action;
    // Attribute to load global configuration values from xml
    public $params = array();
    public $model;
    public $service;

    public function __construct($params)
    {
        $this->params = $params;
        $this->set_request();
        //print_r($params); die;

        if ($this->request->isValid())
        {
            $this->set_xml();

            $this->set_controller($this->xml->controller);
            if (isset($this->xml->model))
                $this->set_model($this->xml->model);
            if (isset($this->xml->service))
                $this->set_service($this->xml->service);
            $this->set_action($this->xml->action);
        }
       
    }
    
  
    // Method to set request parameters with sanitized values
    public function set_request()
    {
        $this->request = $this->params['request'];
        unset($this->params['request']);
    }

    public function get_request()
    {
        return $this->request;
    }

    // Method to load request related properties from xml configuration file
    public function set_xml()
    {
        $xml_loader = $this->params['xml_loader'];
        $this->xml = $xml_loader->load_data($this->request->req['rm_slug']);
        //var_dump($this->xml);die;
    }

    public function get_xml()
    {
        return $this->xml;
    }

    public function set_controller($controller)
    {   
        if (!class_exists($controller))
        {
            throw new InvalidArgumentException(
            "The controller '$controller' has not been defined.");
        }
        $this->controller = $controller;
    }

    public function get_controller()
    {
        return $this->controller;
    }

    // Map request action to controller's method call with the help of Reflection classes
    public function set_action($action)
    {
        $reflector = new ReflectionClass($this->controller);
        //echo "<pre>";
        //var_dump($reflector->getMethods()); die;

        if ($reflector->hasMethod($action))
        {
            if (($this->xml->type == "post" && !empty($_POST)) || ($this->xml->type == "get" && empty($_POST)) || empty($this->xml->type))
            {
                $this->action = $this->xml->action;
            } else
                throw new InvalidArgumentException(
                "The controller action '$action' is not a valid request in $this->controller.");
        } else
            throw new InvalidArgumentException(
            "The controller action '$action' has not been defined in $this->controller.");
    }

    public function get_action()
    {
        return $this->action;
    }

    /**
     *
     */
    public function run()
    {
        
        $installed_rm_version = get_option('rm_option_rm_version', null);

        if ((!$installed_rm_version || version_compare($installed_rm_version, RM_PLUGIN_VERSION, 'lt')) && is_admin())
        {
            update_option('rm_option_rm_version', RM_PLUGIN_VERSION);
            
            if(RM_SHOW_WHATSNEW_SPLASH == true)
            {
                $screen = get_current_screen();
                if ($screen->base !== 'post')
                {
                    //User either intalled fresh or updated RM, show 'Whats New' page.
                    include plugin_dir_path(plugin_dir_path(__FILE__)) . 'admin/views/template_rm_whats_new.php';

                    return;
                }
            }    
            
        }

        if ($this->request->isValid())
        {
            return $command = call_user_func_array(array(new $this->controller(), $this->action), array($this->model, $this->service, $this->request, $this->params));
        }
    }

    /*
     * Set model instance from createModel method
     */

    public function set_model($class_name, $config = array())
    {
        if ($model = $this->create_model($class_name))
        {
            /* $reflector = new ReflectionClass($this->controller);
              if ($reflector->hasMethod('set')){
              $model->set($this->request->req);
              } */

            // $model->set($this->request->req);

            $this->model = $model;
        }
        $this->model = $model;
    }

    /*
     * Creation of model object
     */

    public function create_model($name)
    {
        if (class_exists($name, true))
        {
            return new $name();
        }

        return false;
    }

    /*
     * Get service object
     */

    public function set_service($class_name)
    {
        if ($service = $this->create_service($class_name))
        {
            $this->service = $service;
        }
    }

    /*
     * Create a service object
     */

    public function create_service($name)
    {
        if (class_exists($name, true))
        {
            return new $name($this->model);
        }

        return false;
    }

    public function set_params($params)
    {
        $this->params = $params;
    }

}
