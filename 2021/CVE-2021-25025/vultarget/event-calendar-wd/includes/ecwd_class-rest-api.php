<?php
class EcwdRestApi
  {

    private static $instance = null;

    private $namespace = ECWD_REST_NAMESPACE;

    private $bases = array(
      'excluded_event' => array('/excluded_event', true, false, false),//get,post,delete
      'past_event' => array('/past_event', true, false, false),//get,post,delete
      'add_event' => array('/add_event', false, true, false),//get,post,delete
      'delete_event' => array('/delete_event', false, true, false),//get,post,delete
    );

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
    {
      foreach ($this->bases as $key => $route_config) {
        $endpoint = $this->get_endpoint($key);

        // readable
        if ($route_config[1]) {

          register_rest_route($this->namespace, $endpoint,
            array(
              'methods' => \WP_REST_Server::READABLE,
              'callback' => array($this, 'get_item'),
              'permission_callback' => array($this, 'get_items_permissions_check'),
              'args' => array(),
            )
          );

        }

        // writable
        if ($route_config[2]) {

          register_rest_route($this->namespace, $endpoint,
            array(
              'methods' => \WP_REST_Server::CREATABLE,
              'callback' => array($this, 'update_item'),
              'permission_callback' => array($this, 'create_item_permissions_check'),
              'args' => array(),
            )
          );

        }

        // deletable
        if ($route_config[3]) {

          register_rest_route($this->namespace, $endpoint,
            array(
              'methods' => \WP_REST_Server::DELETABLE,
              'callback' => array($this, 'delete_item'),
              'permission_callback' => array($this, 'delete_item_permissions_check'),
              'args' => array(),
            )
          );

        }
      }

    }

    /**
     * get endpoint route by its key(string identificator )
     *
     */
    private function get_endpoint($key)
    {
      if (array_key_exists($key, $this->bases)) {
        return $this->bases[$key][0];
      }
      return false;

    }

    /**
     * get endpoint key by its route
     *
     */
    private function parse_endpoint($route)
    {
      $route_url = substr($route, 6);

      foreach ($this->bases as $key => $value) {
        $route_regex = '/' . substr($value[0], 1) . '/';

        if (preg_match($route_regex, substr($route_url, 1))) {
          return $key;
        }
      }

      return null;

    }
    /**
     * Get a collection of items
     *
     * @param \WP_REST_Request $request Full data about the request.
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_item($request)
    {
      $parameters = self::wp_unslash_conditional($request->get_query_params());
      if(wp_verify_nonce($parameters["nonce"] , 'ecwd_rest_nonce')){
        $route = $request->get_route();
        $endpoint = $this->parse_endpoint($route);
        switch ($endpoint) {
          case 'excluded_event':
            $status = 200;
            $data_for_response = array(
              "code" => "ok",
              "data" => self::excluded_events($parameters)
            );
            break;
          case 'past_event':
            $status = 200;
            $data_for_response = array(
              "code" => "ok",
              "data" => self::all_past_events()
            );
            break;
        }
        return new \WP_REST_Response($data_for_response, $status);
      }
    }

    public function update_item($request) {
      $parameters = self::wp_unslash_conditional($request->get_body_params());
      if(wp_verify_nonce($parameters["nonce"] , 'ecwd_rest_nonce')) {
        $route    = $request->get_route();
        $endpoint = $this->parse_endpoint($route);
        include_once 'events/ecwd-events-controller.php';
        switch ($endpoint) {
          case 'add_event':
            $status            = 200;
            $data_for_response = array(
              "success" => self::add_events($parameters),
              "free_events_count" => self::free_events_count($parameters)
            );

            if(isset($parameters['calendar_id'])){
              ecwd_events_controller::clear_recurring_events_cache(array($parameters['calendar_id']));
            }

            break;
            case "delete_event";
              $events_id = array();
              if(isset($parameters['events_id']) && is_array($parameters['events_id'])){
                $events_id = $parameters['events_id'];
              }
              $status            = 200;
              $data_for_response = array(
                "success" => self::delete_events($events_id)
              );

            break;
        }
        return new \WP_REST_Response($data_for_response, $status);
      }
    }

    private static function excluded_events($parameters){
      if(isset($parameters['calendar_id'])){
        $calendar_id = intval($parameters['calendar_id']);
      }else{
        return false;
      }
      include_once 'events/ecwd-events-controller.php';
      $controller = new ecwd_events_controller();
      $excluded_events = $controller->get_excluded_events($calendar_id);
      return self::convert_data($excluded_events);
    }

    private static function all_past_events(){
      include_once 'events/ecwd-events-controller.php';
      $controller = new ecwd_events_controller();
      $past_events = $controller->get_past_events();
      return self::convert_data($past_events);
    }

    private static function convert_data($events){
      $return_data = array();
      foreach ($events as $event){
        $event_data = array(
          'id' => $event->id,
          'title' => esc_html($event->title),
          'from' => $event->get_start_date(),
          'end' => $event->get_end_date(),
        );
        array_push($return_data, $event_data);
      }
      return $return_data;
    }

    private static function delete_events($ids){
      include_once 'events/ecwd-events-controller.php';
      $cotroller = new ecwd_events_controller();
      $cotroller->delete_events($ids);
      return true;
    }


    private static function add_events($parameters){
      $status = false;
      if (isset($parameters["ecwd_data"])) {
        $ecwd_events_list  = $parameters["ecwd_data"];
        $calendar_id = esc_attr($parameters['calendar_id']);
        foreach ($ecwd_events_list as $val){
          if(isset($val['event_id'])){
            $event_id = esc_attr($val['event_id']);
            $event_calendars = get_post_meta($event_id, ECWD_PLUGIN_PREFIX . '_event_calendars', true);
            if (!$event_calendars) {
              $event_calendars = array();
            }

            if (is_array($event_calendars) && !in_array($calendar_id, $event_calendars)) {
              $event_calendars[] = $calendar_id;
              $status = true;
            }
            update_post_meta($event_id, ECWD_PLUGIN_PREFIX . '_event_calendars', $event_calendars);
          }
        }


      }
      return $status;
    }


    private static function free_events_count($parameters){
      if(isset($parameters['calendar_id'])){
        $calendar_id = intval($parameters['calendar_id']);
      }else{
        return false;
      }
      include_once 'events/ecwd-events-controller.php';
      $controller = new ecwd_events_controller();
      $excluded_events = $controller->get_excluded_events($calendar_id);
      return count($excluded_events);
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request)
    {
      //return true; <--use to make readable by all
      return true; //current_user_can( 'edit_something' );
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check($request)
    {
      return $this->get_items_permissions_check($request);
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function create_item_permissions_check($request)
    {
      return true; //current_user_can( 'edit_something' );
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function update_item_permissions_check($request)
    {
      return $this->create_item_permissions_check($request);
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function delete_item_permissions_check($request)
    {
      return true;
    }

    /**
     * Prepare the item for create or update operation
     *
     * @param WP_REST_Request $request Request object
     * @return WP_Error|object $prepared_item
     */
    protected function prepare_item_for_database($request)
    {
      return array();
    }

    /**
     * Prepare the item for the REST response
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepare_item_for_response($item, $request)
    {
      return array();
    }



    /*
     * wp 4.4 adds slashes, removes them
     *
     * https://core.trac.wordpress.org/ticket/36419
     **/
    private static function wp_unslash_conditional($data)
    {

      global $wp_version;
      if ($wp_version < 4.5) {
        $data = wp_unslash($data);
      }

      return $data;
    }

    public static function get_instance()
    {
      if (null == self::$instance) {
        self::$instance = new self;
        self::$instance->register_routes();
      }
      return self::$instance;
    }

  }
