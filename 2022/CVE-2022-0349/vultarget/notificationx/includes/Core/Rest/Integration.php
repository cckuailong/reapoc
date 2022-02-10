<?php

namespace NotificationX\Core\Rest;

use NotificationX\Core\PostType;
use NotificationX\Core\REST;
use NotificationX\Extensions\ExtensionFactory;
use NotificationX\Extensions\GlobalFields;
use NotificationX\GetInstance;
use NotificationX\NotificationX;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;

class Integration {
    /**
     * Instance of NotificationX
     *
     * @var NotificationX
     */
    use GetInstance;

    /**
     * Constructor.
     *
     * @since 4.7.0
     *
     * @param string $post_type Post type.
     */
    public function __construct() {
        $this->namespace = 'notificationx/v1';
        $this->rest_base = 'notification';
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @since 4.7.0
     *
     * @see register_rest_route()
     */
    public function register_routes() {
        // Settings Integration
        register_rest_route( $this->namespace, '/api-connect', array(
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( $this, 'api_connect' ),
            'permission_callback' => array($this, 'settings_permission'),
        ));

        // calls from integration provider.
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_response'),
                    'permission_callback' => '__return_true',
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'save_response'),
                    'permission_callback' => '__return_true',
                ),
            )
        );
        // OLD Fallback for Zapier
        register_rest_route(
            "notificationx",
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_response'),
                    'permission_callback' => '__return_true',
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'save_response'),
                    'permission_callback' => '__return_true',
                ),
            )
        );
    }

    public function get_response( \WP_REST_Request $request ){
        $id        = $request['id'];
		$api_key   = $request['api_key'];
        $error     = [];

		if( $api_key === md5( home_url( '', 'http' ) ) || $api_key === md5( home_url( '', 'https' ) ) ) {
            $notificationx = PostType::get_instance()->get_post( $id );
            if( $notificationx ) {
                return wp_send_json( true );
            }
            $error['message'] = __( 'There is no notification created with this id:' . $id, 'notificationx' );
            return wp_send_json_error( $error, 401 );
		} else {
			$error['message'] = __( 'Error: API Key Invalid!', 'notificationx' );
			return wp_send_json_error( $error, 401 );
		}
    }

    /**
     * Undocumented function
     *
     * @param \WP_REST_Request $request
     * @return void
     */
    public function save_response( \WP_REST_Request $request ){
        $response_data = array(
            'data'      => '',
            'error'     => false
        );

        if ( ! isset( $request['api_key'] ) ) {
            $response_data['error'] = __('Error: You should provide an API key.', 'notificationx');
        } else {
            if( md5( home_url( '', 'http' ) ) != $request['api_key'] && md5( home_url( '', 'https' ) ) != $request['api_key'] ) {
                $response_data['error'] = __('Error: Invalid API key.', 'notificationx');
            }
        }

        if ( ! $response_data['error'] ) {
            $response_data['data'] = $request->get_params();
            if ( isset( $response_data['data']['api_key'] ) ) {
                unset( $response_data['data']['api_key'] );
            }
            do_action( 'nx_api_response_success', $response_data['data'] );
        }

        return apply_filters( 'nx_api_response', $response_data );
    }

    /**
     * Undocumented function
     *
     * @param \WP_REST_Request $request
     * @return
     */
    public function api_connect( \WP_REST_Request $request ){
        $params = $request->get_params();
        $source = !empty($params['source']) ? $params['source'] : '';
        /**
         * @var Extension
         */
        $ext = ExtensionFactory::get_instance()->get($source);
        if($ext && method_exists($ext, 'connect')){
            return $ext->connect($params);
        }
        else{
            $result = apply_filters("nx_api_connect_$source", null, $params);
            if($result){
                return $result;
            }
        }
        return REST::get_instance()->error();
    }

    public function settings_permission( $request ) {
        return current_user_can('edit_notificationx_settings');
    }
}