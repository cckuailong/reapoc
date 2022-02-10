<?php

namespace NotificationX\Core\Rest;

use NotificationX\Core\Analytics as CoreAnalytics;
use NotificationX\GetInstance;
use NotificationX\NotificationX;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;

class QuickBuild {
    /**
     * Instance of QuickBuild
     *
     * @var QuickBuild
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
        $this->rest_base = 'quickbuild';
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
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_config'),
                    // maybe use
                    'permission_callback' => [$this, 'update_permissions_check'],
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array($this, 'create_post'),
                    'permission_callback' => [$this, 'update_permissions_check'],
                ),
            )
        );

    }
    public function update_permissions_check($request) {
        return current_user_can('edit_notificationx');
    }

    public function get_config($request){
        $params = $request->get_params();
        $result = QuickBuild::get_instance()->tabs($params);
        wp_send_json($result);
    }

    public function create_post($request){
        $params = $request->get_params();
        $result = CoreAnalytics::get_instance()->insert_analytics($params['nx_id'], 'clicks');
        wp_send_json_success($result);
    }
}