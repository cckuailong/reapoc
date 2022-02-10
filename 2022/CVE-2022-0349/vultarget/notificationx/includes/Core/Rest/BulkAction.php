<?php

namespace NotificationX\Core\Rest;

use NotificationX\Admin\Admin;
use NotificationX\Core\PostType;
use NotificationX\GetInstance;
use WP_REST_Server;

class BulkAction {
    /**
     * Instance of BulkAction
     *
     * @var BulkAction
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
        $this->rest_base = 'bulk-action';
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
        register_rest_route($this->namespace, "/{$this->rest_base}/delete",
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'delete'),
                'permission_callback' => [$this, 'edit_permission'],
            )
        );
        register_rest_route($this->namespace, "/{$this->rest_base}/regenerate",
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'regenerate'),
                'permission_callback' => [$this, 'read_permission'],
            )
        );
        register_rest_route($this->namespace, "/{$this->rest_base}/enable",
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'enable'),
                'permission_callback' => [$this, 'edit_permission'],
            )
        );
        register_rest_route($this->namespace, "/{$this->rest_base}/disable",
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'disable'),
                'permission_callback' => [$this, 'edit_permission'],
            )
        );

    }

    public function read_permission( $request ) {
        return current_user_can('read_notificationx');
    }
    public function edit_permission( $request ) {
        return current_user_can('edit_notificationx');
    }

    public function delete($request){
        $count = [];
        $params = $request->get_params();
        if(!empty($params['ids']) && is_array($params['ids'])){
            foreach ($params['ids'] as $key => $nx_id) {
                $count[$nx_id] = PostType::get_instance()->delete_post($nx_id);
            }
        }
        return [
            'success' => true,
            'count'   => $count,
        ];
    }

    public function regenerate($request){
        $count = 0;
        $params = $request->get_params();
        if(!empty($params['ids']) && is_array($params['ids'])){
            foreach ($params['ids'] as $key => $nx_id) {
                $count += Admin::get_instance()->regenerate_notifications(['nx_id' => $nx_id]);
            }
        }
        return [
            'success' => true,
            'count'   => $count,
        ];
    }

    public function enable($request){
        $count = [];
        $params = $request->get_params();
        if(!empty($params['ids']) && is_array($params['ids'])){
            $ids = array_map( 'esc_sql', $params['ids'] );
            $posts = PostType::get_instance()->get_posts([
                'nx_id' => [
                    'IN',
                    '(' . implode( ', ', $ids ) . ')',
                ],
            ], 'nx_id, source' );
            if(is_array($posts)){
                foreach ($posts as $key => $post) {
                    $count[$post['nx_id']] = PostType::get_instance()->update_status([
                        'nx_id'   => $post['nx_id'],
                        'source'  => $post['source'],
                        'enabled' => true,
                    ]);
                }
            }
        }
        return [
            'success' => true,
            'count'   => $count,
        ];
    }

    public function disable($request){
        $count = [];
        $params = $request->get_params();
        if(!empty($params['ids']) && is_array($params['ids'])){
            $ids = array_map( 'esc_sql', $params['ids'] );
            $posts = PostType::get_instance()->get_posts([
                'nx_id' => [
                    'IN',
                    '(' . implode(', ', $ids) . ')',
                ],
            ], 'nx_id, source');
            if(is_array($posts)){
                foreach ($posts as $key => $post) {
                    $count[$post['nx_id']] = PostType::get_instance()->update_status([
                        'nx_id'   => $post['nx_id'],
                        'source'  => $post['source'],
                        'enabled' => false,
                    ]);
                }
            }
        }
        return [
            'success' => true,
            'count'   => $count,
        ];
    }
}