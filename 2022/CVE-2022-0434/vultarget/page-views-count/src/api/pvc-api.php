<?php
namespace A3Rev\PageViewsCount;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class API
{

    public $namespace = 'pvc/v1';

    public $rest_bases = array( 
            '/increase',
            '/view'
        );

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
    }

    public function rest_api_init() {

        foreach ( $this->rest_bases as $rest_base ) {
            register_rest_route( $this->namespace, $rest_base . '/(?P<post_ids>([a-zA-Z0-9_]\,?)+)', array(
                'methods'  => 'GET',
                'callback' => array( $this, str_replace( '/', '', $rest_base ) . '_stats' ),
                'permission_callback' => '__return_true',
            ) );
        }
    }

    public function get_stats( $ids = array() ) {
        $items_data = array();

        if ( empty( $ids ) ) {
            return $items_data;
        }

        $results = A3_PVC::pvc_fetch_posts_stats( $ids );
        if ( $results ) {
            foreach( $results as $result ) {
                $items_data[$result->post_id] = array (
                    'post_id'       => (int) $result->post_id,
                    'total_view'    => (int) $result->total,
                    'today_view'    => (int) $result->today
                );
                $ids = array_diff( $ids, array( $result->post_id ) );
            }
        }

        foreach ( $ids as $post_id ) {
            $total = A3_PVC::pvc_fetch_post_total( $post_id );
            $items_data[$post_id] = array (
                'post_id'       => (int) $post_id,
                'total_view'    => (int) $total,
                'today_view'    => 0
            );
        }

        return $items_data;
    }

    public function increase_stats( \WP_REST_Request $request ) {
        @ini_set( 'display_errors', false );

        $post_ids_text = $request->get_param( 'post_ids' );

        if ( '' == trim( $post_ids_text ) ) {
           return wp_send_json( array(
                'success' => false,
                'message' => __( "Post Ids is empty", 'page-views-count' ),
            ) ); 
        }

        $post_ids = explode( ',', $post_ids_text );

        if ( empty ( $post_ids ) ) {
            return wp_send_json( array(
                'success' => false,
                'message' => __( "Post Ids is empty", 'page-views-count' ),
            ) );
        }

        $post_ids = array_map( 'trim', $post_ids );

        $ids = array();
        foreach ( $post_ids as $post_id ) {
            if ( ! in_array( $post_id, $ids ) ) {
                $ids[] = $post_id;
                A3_PVC::pvc_stats_update( $post_id );
            }
        }

        $json_data = array(
            'success' => true,
            'items'   => $this->get_stats( $post_ids ),
        );

        return wp_send_json( $json_data );
    }

    public function view_stats( \WP_REST_Request $request ) {
        @ini_set( 'display_errors', false );

        $post_ids_text = $request->get_param( 'post_ids' );

        if ( '' == trim( $post_ids_text ) ) {
           return wp_send_json( array(
                'success' => false,
                'message' => __( "Post Ids is empty", 'page-views-count' ),
            ) ); 
        }

        $post_ids = explode( ',', $post_ids_text );

        if ( empty ( $post_ids ) ) {
            return wp_send_json( array(
                'success' => false,
                'message' => __( "Post Ids is empty", 'page-views-count' ),
            ) );
        }

        $post_ids = array_map( 'trim', $post_ids );

        $json_data = array(
            'success' => true,
            'items'   => $this->get_stats( $post_ids ),
        );

        return wp_send_json( $json_data );
    }
}

?>