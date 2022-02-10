<?php

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class WPUF_List_Table_Subscribers extends WP_List_Table {

    public function __construct() {
        parent::__construct( [
            'singular' => 'subscriber',
            'plural'   => 'subscribers',
            'ajax'     => false,
        ] );
    }

    public function get_table_classes() {
        return [ 'widefat', 'fixed', 'striped', $this->_args['plural'] ];
    }

    /**
     * Message to show if no designation found
     *
     * @return void
     */
    public function no_items() {
        esc_html_e( 'No subscribers found', 'wp-user-frontend' );
    }

    /**
     * Get the column names
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
            'cb'                    => '<input type="checkbox" />',
            'id'                    => __( 'User ID', 'wp-user-frontend' ),
            'name'                  => __( 'User Name', 'wp-user-frontend' ),
            'subscription_id'       => __( 'Subscription ID', 'wp-user-frontend' ),
            'status'                => __( 'Status', 'wp-user-frontend' ),
            'gateway'               => __( 'Gateway', 'wp-user-frontend' ),
            'transaction_id'        => __( 'Transaction ID', 'wp-user-frontend' ),
            'starts_from'           => __( 'Starts from', 'wp-user-frontend' ),
            'expire'                => __( 'Expire date', 'wp-user-frontend' ),
        ];

        return $columns;
    }

    /**
     * Default column values if no callback found
     *
     * @param object $item
     * @param string $column_name
     *
     * @return string
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
                return $item->user_id;

            case 'name':
                return $item->name;

            case 'subscription_id':
                return $item->subscribtion_id;

            case 'status':
                return $item->subscribtion_status;

            case 'gateway':
                return $item->gateway;

            case 'transaction_id':
                return $item->transaction_id;

            case 'starts_from':
                return $item->starts_from;

            case 'expire':
                return $item->expire;

            default:
                return isset( $item->$column_name ) ? $item->$column_name : '';
        }
    }

    /**
     * Get sortable columns
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = [
            'id'        => [ 'id', true ],
        ];

        return $sortable_columns;
    }

    /**
     * Render the checkbox column
     *
     * @param object $item
     *
     * @return string
     */
    public function column_cb( $item ) {
        $post_ID = isset( $_REQUEST['post_ID'] ) ? intval( wp_unslash( $_REQUEST['post_ID'] ) ) : 0;
        return sprintf(
            '<input type="checkbox" name="subscriber_id[]" value="%d" />', $post_ID
         );
    }

    /**
     * Set the views
     *
     * @return array
     */
    public function get_views() {
        $status_links = [];
        $post_ID = isset( $_REQUEST['post_ID'] ) ? intval( wp_unslash( $_REQUEST['post_ID'] ) ) : 0;
        $base_link    = admin_url( 'admin.php?page=wpuf_subscribers&pack=' . $post_ID );

        $subscribers_count          = count( $users = WPUF_Subscription::init()->subscription_pack_users( $post_ID ) );
        $subscriptions_active_count = count( $users = WPUF_Subscription::init()->subscription_pack_users( $post_ID ) );
        $subscriptions_cancle_count = count( $users = WPUF_Subscription::init()->subscription_pack_users( $post_ID ) );

        $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : 'all';

        $status_links['all']       = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( [ 'status' => 'all' ], $base_link ), ( $status == 'all' ) ? 'current' : '', __( 'All', 'wp-user-frontend' ), $subscribers_count );
        $status_links['Completed'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( [ 'status' => 'Completed' ], $base_link ), ( $status == 'pending' ) ? 'current' : '', __( 'Completed', 'wp-user-frontend' ), $subscriptions_active_count );
        $status_links['Cancel']    = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( [ 'status' => 'Cancel' ], $base_link ), ( $status == 'Cancel' ) ? 'current' : '', __( 'Cancel', 'wp-user-frontend' ), $subscriptions_cancle_count );

        return $status_links;
    }

    /**
     * Prepare the class items
     *
     * @return void
     */
    public function prepare_items() {
        global $wpdb;

        $columns               = $this->get_columns();
        $hidden                = [ ];
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = [ $columns, $hidden, $sortable ];

        $per_page              = 20;
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page - 1 ) * $per_page;
        $this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '2';

        // only ncessary because we have sample data
        $args = [
            'offset' => $offset,
            'number' => $per_page,
        ];

        if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
            $args['orderby'] = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) ;
            $args['order']   = sanitize_text_field( wp_unslash( $_REQUEST['order'] ) );
        }

        $post_ID = isset( $_REQUEST['post_ID'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_ID'] ) ) : '';
        $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : '';


        $sql = 'SELECT * FROM ' . $wpdb->prefix . 'wpuf_subscribers';
        $sql .= isset( $_REQUEST['post_ID'] ) ? ' WHERE subscribtion_id = ' . intval( sanitize_text_field( wp_unslash( $_REQUEST['post_ID'] ) ) ): '';

        $sql .= isset( $_REQUEST['status'] ) ? ' AND subscribtion_status = "' . sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) . '"' : '';

        $this->items  = $wpdb->get_results( $sql, OBJECT );

        $this->set_pagination_args( [
            'total_items' => count( $this->items ),
            'per_page'    => $per_page,
        ] );
    }
}
