<?php

namespace NotificationX\Core\Rest;

use NotificationX\Core\Database;
use NotificationX\Core\PostType;
use NotificationX\Core\REST;
use NotificationX\Extensions\GlobalFields;
use NotificationX\GetInstance;
use NotificationX\NotificationX;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;

class Posts extends WP_REST_Controller {
    /**
     * Instance of NotificationX
     *
     * @var NotificationX
     */
    use GetInstance;

    /**
     * Post type.
     *
     * @since 4.7.0
     * @var string
     */
    protected $post_type;

    /**
     * Constructor.
     *
     * @since 4.7.0
     *
     * @param string $post_type Post type.
     */
    public function __construct() {
        $this->namespace = 'notificationx/v1';
        $this->rest_base = 'nx';
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
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    // 'args'                => $this->get_collection_params(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    // 'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
                ),
                // 'schema' => array($this, 'get_public_item_schema'),
            )
        );


        // $schema = $this->get_item_schema();
        $get_item_args = array(
            'context' => $this->get_context_param(array('default' => 'view')),
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __('Unique identifier for the object.', 'notificationx'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    // 'args'                => $get_item_args,
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array($this, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                    // 'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array($this, 'delete_item'),
                    'permission_callback' => array($this, 'delete_item_permissions_check'),
                    'args'                => array(
                        'force' => array(
                            'type'        => 'boolean',
                            'default'     => false,
                            'description' => __('Whether to bypass Trash and force deletion.', 'notificationx'),
                        ),
                    ),
                ),
                // 'schema' => array($this, 'get_public_item_schema'),
            )
        );
    }

    /**
     * Checks if a given request has access to read posts.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check($request) {
        return current_user_can('read_notificationx');
    }

    /**
     * Checks if a given request has access to read post.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_item_permissions_check($request) {
        return current_user_can('read_notificationx');
    }

    /**
     * Retrieves a collection of posts.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items($request) {
        $params     = $request->get_params();
        $status     = !empty($params['status']) ? $params['status'] : "all";
        $page       = !empty($params['page']) ? $params['page'] : 1;
        $per_page   = !empty($params['per_page']) ? $params['per_page'] : 20;
        $start_from = ($page - 1) * $per_page;
        $limit      = "ORDER BY a.updated_at DESC LIMIT $start_from, $per_page";
        $where      = [];

        if($status == 'enabled' || $status == 'disabled'){
            $where['enabled'] = $status == 'enabled' ? true : false;
        }
        $total_posts = Database::get_instance()->get_post(Database::$table_posts, [], 'count(*) AS total');
        $enabled     = Database::get_instance()->get_post(Database::$table_posts, ['enabled' => true], 'count(*) AS total');
        $disabled    = Database::get_instance()->get_post(Database::$table_posts, ['enabled' => false], 'count(*) AS total');

        return [
            'total'    => $total_posts['total'],
            'enabled'  => $enabled['total'],
            'disabled' => $disabled['total'],
            'posts'    => PostType::get_instance()->get_post_with_analytics($where, $limit),
        ];
    }

    /**
     * Retrieves a single post.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_item($request) {
        return PostType::get_instance()->get_post($request['id']);
    }

    /**
     * Checks if a given request has access to create a post.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function create_item_permissions_check($request) {
        if (!empty($request['id'])) {
            return new WP_Error(
                'rest_post_exists',
                __('Cannot create existing post.', 'notificationx'),
                array('status' => 400)
            );
        }

        return current_user_can('edit_notificationx');
    }

    /**
     * Creates a single post.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_item($request) {
        if (!empty($request['nx_id'])) {
            return new WP_Error(
                'rest_post_exists',
                __('Cannot create existing post.', 'notificationx'),
                array('status' => 400)
            );
        }

        // $prepared_post = $this->prepare_item_for_database($request);

        // if (is_wp_error($prepared_post)) {
        //     return $prepared_post;
        // }

        $params = $request->get_params();
        return PostType::get_instance()->save_post($params);
    }

    /**
     * Checks if a given request has access to update a post.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
     */
    public function update_item_permissions_check($request) {
        return current_user_can('edit_notificationx');
    }

    /**
     * Updates a single post.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_item($request) {
        $params = $request->get_params();
        return PostType::get_instance()->save_post($params);
    }

    /**
     * Checks if a given request has access to delete a post.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
     */
    public function delete_item_permissions_check($request) {
        // if ($post && !$this->check_delete_permission($post)) {
        //     return new WP_Error(
        //         'rest_cannot_delete',
        //         __('Sorry, you are not allowed to delete this post.'),
        //         array('status' => rest_authorization_required_code())
        //     );
        // }
        return current_user_can('edit_notificationx');
    }

    /**
     * Deletes a single post.
     *
     * @since 4.7.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_item($request) {
        if(PostType::get_instance()->delete_post($request['id'])){
            wp_send_json_success();
        }
        wp_send_json_error();
    }

}
