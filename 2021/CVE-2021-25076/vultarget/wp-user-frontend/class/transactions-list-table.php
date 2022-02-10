<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WPUF_Transactions_List_Table extends WP_List_Table {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(
            [
                'singular' => __( 'transaction', 'wp-user-frontend' ),
                'plural'   => __( 'transactions', 'wp-user-frontend' ),
                'ajax'     => false,
            ]
        );
    }

    /**
     * Render the bulk edit checkbox.
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-items[]" value="%s" />', $item->id
        );
    }

    /**
     * Get a list of columns.
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
            'cb'             => '<input type="checkbox" />',
            'id'             => __( 'ID', 'wp-user-frontend' ),
            'status'         => __( 'Status', 'wp-user-frontend' ),
            'user'           => __( 'User', 'wp-user-frontend' ),
            'subtotal'       => __( 'Subtotal', 'wp-user-frontend' ),
            'cost'           => __( 'Cost', 'wp-user-frontend' ),
            'tax'            => __( 'Tax', 'wp-user-frontend' ),
            'post_id'        => __( 'Post ID', 'wp-user-frontend' ),
            'pack_id'        => __( 'Pack ID', 'wp-user-frontend' ),
            'payment_type'   => __( 'Gateway', 'wp-user-frontend' ),
            'payer'          => __( 'Payer', 'wp-user-frontend' ),
            'payer_email'    => __( 'Email', 'wp-user-frontend' ),
            'transaction_id' => __( 'Trans ID', 'wp-user-frontend' ),
            'created'        => __( 'Date', 'wp-user-frontend' ),
        ];

        return $columns;
    }

    /**
     * Get a list of sortable columns.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = [
            'id'      => [ 'id', false ],
            'status'  => [ 'status', false ],
            'created' => [ 'created', false ],
        ];

        return $sortable_columns;
    }

    /**
     * Set the views
     *
     * @return array
     */
    public function get_views() {
        $status_links = [];
        $base_link    = admin_url( 'admin.php?page=wpuf_transaction' );

        $transactions_count         = wpuf_get_transactions( [ 'count' => true ] );
        $transactions_pending_count = wpuf_get_pending_transactions( [ 'count' => true ] );

        $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : 'all';

        $status_links['all']     = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( [ 'status' => 'all' ], $base_link ), ( $status === 'all' ) ? 'current' : '', __( 'All', 'wp-user-frontend' ), $transactions_count );
        $status_links['pending'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( [ 'status' => 'pending' ], $base_link ), ( $status === 'pending' ) ? 'current' : '', __( 'Pending', 'wp-user-frontend' ), $transactions_pending_count );

        return $status_links;
    }

    /**
     * Method for id column.
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_id( $item ) {
        $id = $item->id;

        $delete_nonce = wp_create_nonce( 'wpuf-delete-transaction' );
        $title        = '<strong>#' . $id . '</strong>';

        $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : '';
        $page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';

        if ( $status === 'pending' ) {
            $accept_nonce = wp_create_nonce( 'wpuf-accept-transaction' );
            $reject_nonce = wp_create_nonce( 'wpuf-reject-transaction' );

            $actions = [
                'accept' => sprintf( '<a href="?page=%s&action=%s&id=%d&_wpnonce=%s">%s</a>', esc_attr( $page ), 'accept', absint( $id ), $accept_nonce, __( 'Accept', 'wp-user-frontend' ) ),
                'reject' => sprintf( '<a href="?page=%s&action=%s&id=%d&_wpnonce=%s">%s</a>', esc_attr( $page ), 'reject', absint( $id ), $reject_nonce, __( 'Reject', 'wp-user-frontend' ) ),
            ];
        } else {
            $actions = [
                'delete' => sprintf( '<a href="?page=%s&action=%s&id=%d&_wpnonce=%s">%s</a>', esc_attr( $page ), 'delete', absint( $id ), $delete_nonce, __( 'Delete', 'wp-user-frontend' ) ),
            ];
        }

        return $title . $this->row_actions( $actions );
    }

    /**
     * Define each column of the table.
     *
     * @param array  $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'status':
                return ( $item->status === 'completed' ) ? '<span class="wpuf-status-completed" title="Completed"></span>' : '<span class="wpuf-status-processing" title="Processing"></span>';

            case 'user':
                $user           = get_user_by( 'id', $item->user_id );
                $post_author_id = get_post_field( 'post_author', $item->post_id );
                $post_author    = get_the_author_meta( 'nickname', $post_author_id );

                return ! empty( $user ) ? sprintf( '<a href="%s">%s</a>', admin_url( 'user-edit.php?user_id=' . $item->user_id ), $user->user_nicename ) : $post_author;

            case 'subtotal':
                return wpuf_format_price( ! empty( $item->subtotal ) ? $item->subtotal : $item->cost );

            case 'cost':
                return wpuf_format_price( $item->cost );

            case 'tax':
                return wpuf_format_price( $item->tax );

            case 'post_id':
                return ! empty( $item->post_id ) ? sprintf( '<a href="%s">%s</a>', admin_url( 'post.php?post=' . $item->post_id . '&action=edit' ), $item->post_id ) : '-';

            case 'pack_id':
                return ! empty( $item->pack_id ) ? sprintf( '<a href="%s">%s</a>', admin_url( 'post.php?post=' . $item->pack_id . '&action=edit' ), $item->pack_id ) : '-';

            case 'payer':
                return ! empty( $item->payer_first_name ) ? $item->payer_first_name . ' ' . $item->payer_last_name : '-';

            case 'created':
                return ! empty( $item->created ) ? gmdate( 'd-m-Y', strtotime( $item->created ) ) : '-';
            default:
                return ! empty( $item->{$column_name} ) ? $item->{$column_name} : '-';
                break;
        }
    }

    /**
     * Message to be displayed when there are no items.
     *
     * @return void
     */
    public function no_items() {
        esc_html_e( 'No transactions found.', 'wp-user-frontend' );
    }

    /**
     * Set the bulk actions.
     *
     * @return array
     */
    public function get_bulk_actions() {
        $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : '';

        if ( $status === 'pending' ) {
            $actions = [
                'bulk-accept' => __( 'Accept', 'wp-user-frontend' ),
                'bulk-reject' => __( 'Reject', 'wp-user-frontend' ),
            ];
        } else {
            $actions = [
                'bulk-delete' => __( 'Delete', 'wp-user-frontend' ),
            ];
        }

        return $actions;
    }

    /**
     * Prepares the list of items for displaying.
     *
     * @return void
     */
    public function prepare_items() {
        $per_page     = $this->get_items_per_page( 'transactions_per_page', 20 );
        $current_page = $this->get_pagenum();

        $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : 'all';

        if ( $status === 'pending' ) {
            $total_items = wpuf_get_pending_transactions( [ 'count' => true ] );
        } else {
            $total_items = wpuf_get_transactions( [ 'count' => true ] );
        }

        $this->set_pagination_args(
            [
                'total_items' => $total_items,
                'per_page'    => $per_page,
            ]
        );

        $this->_column_headers = $this->get_column_info();

        $this->process_actions();

        $offset = ( $current_page - 1 ) * $per_page;

        $args = [
            'offset' => $offset,
            'number' => $per_page,
        ];

        if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
            $args['orderby'] = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) );
            $args['order']   = sanitize_text_field( wp_unslash( $_REQUEST['order'] ) );
        }

        if ( $status === 'pending' ) {
            $this->items = wpuf_get_pending_transactions( $args );
        } else {
            $this->items = wpuf_get_transactions( $args );
        }
    }

    /**
     * Process the actions
     *
     * @return void
     */
    private function process_actions() {
        global $wpdb;

        $page_url = menu_page_url( 'wpuf_transaction', false );

        // Delete Transaction
        $action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';
        $action2 = isset( $_REQUEST['action2'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action2'] ) ) : '';
        $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';
        $id = isset( $_REQUEST['id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) : '';

        if ( $action === 'delete' || $action2 === 'delete' ) {
            if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf-delete-transaction' ) ) {
                return false;
            }

            $id = absint( esc_sql( $id ) );

            $wpdb->delete( $wpdb->prefix . 'wpuf_transaction', [ 'id' => $id ], [ '%d' ] );

            // Redirect
            wp_redirect( $page_url );
            exit;
        }

        // Delete Transactions
        if ( $action === 'bulk-delete' || $action2 === 'bulk-delete' ) {
            if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'bulk-transactions' ) ) {
                return false;
            }

            $bulk_items = isset( $_REQUEST['bulk-items'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['bulk-items'] ) ) : [];

            $ids = esc_sql( $bulk_items );

            foreach ( $ids as $id ) {
                $id = absint( $id );

                $wpdb->delete( $wpdb->prefix . 'wpuf_transaction', [ 'id' => $id ], [ '%d' ] );
            }

            // Redirect
            wp_redirect( $page_url );
            exit;
        }

        // Reject Transaction
        if ( $action === 'reject' || $action2 === 'reject' ) {
            if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf-reject-transaction' ) ) {
                return false;
            }

            $id      = isset( $_REQUEST['id'] ) ? intval( wp_unslash( $_REQUEST['id'] ) ) : 0;
            $info    = get_post_meta( $id, '_data', true );
            $gateway = $info['post_data']['wpuf_payment_method'];

            do_action( "wpuf_{$gateway}_bank_order_reject", $id );
            wp_delete_post( $id, true );

            // Redirect
            wp_redirect( $page_url );
            exit;
        }

        // Reject Transactions
        if ( $action === 'bulk-reject' || $action2 === 'bulk-reject' ) {
            if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'bulk-transactions' ) ) {
                return false;
            }
            $bulk_items = isset( $_REQUEST['bulk-items'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['bulk-items'] ) ) : [];
            $ids = esc_sql( $bulk_items );

            foreach ( $ids as $id ) {
                $id      = absint( $id );
                $info    = get_post_meta( $id, '_data', true );
                $gateway = $info['post_data']['wpuf_payment_method'];

                do_action( "wpuf_{$gateway}_bank_order_reject", $id );

                wp_delete_post( $id, true );
            }

            // Redirect
            wp_redirect( $page_url );
            exit;
        }

        // Accept Transaction
        if ( $action === 'accept' || $action2 === 'accept' ) {
            if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf-accept-transaction' ) ) {
                return false;
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $id   = isset( $_REQUEST['id'] ) ? intval( wp_unslash( $_REQUEST['id'] ) ) : 0;
            $info = get_post_meta( $id, '_data', true );

            if ( $info ) {
                switch ( $info['type'] ) {
                    case 'post':
                        $post_id = $info['item_number'];
                        $pack_id = 0;
                        break;

                    case 'pack':
                        $post_id = 0;
                        $pack_id = $info['item_number'];
                        break;
                }

                $payer_address = '';

                if ( wpuf_get_option( 'show_address', 'wpuf_address_options', false ) ) {
                    $payer_address = wpuf_get_user_address();
                }

                $transaction = [
                    'user_id'          => $info['user_info']['id'],
                    'status'           => 'completed',
                    'subtotal'         => $info['subtotal'],
                    'tax'              => $info['tax'],
                    'cost'             => $info['price'],
                    'post_id'          => $post_id,
                    'pack_id'          => $pack_id,
                    'payer_first_name' => $info['user_info']['first_name'],
                    'payer_last_name'  => $info['user_info']['last_name'],
                    'payer_address'    => $payer_address,
                    'payer_email'      => $info['user_info']['email'],
                    'payment_type'     => 'Bank/Manual',
                    'transaction_id'   => $id,
                    'created'          => current_time( 'mysql' ),
                ];

                do_action( 'wpuf_gateway_bank_order_complete', $transaction, $id );

                WPUF_Payment::insert_payment( $transaction );

                $coupon_id = $info['post_data']['coupon_id'];

                if ( $coupon_id ) {
                    $pre_usage = get_post_meta( $coupon_id, '_coupon_used', true );
                    $pre_usage = ( empty( $pre_usage ) ) ? 0 : $pre_usage;
                    $new_use   = $pre_usage + 1;

                    update_post_meta( $coupon_id, '_coupon_used', $new_use );
                }

                wp_delete_post( $id, true );
            }

            wp_redirect( $page_url );
            exit;
        }

        // Bulk Accept Transaction
        if ( $action === 'bulk-accept' || $action2 === 'bulk-accept' ) {
            if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'bulk-transactions' ) ) {
                return false;
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            $bulk_items = isset( $_REQUEST['bulk-items'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['bulk-items'] ) ) : [];
            $ids = esc_sql( $bulk_items );

            foreach ( $ids as $id ) {
                $id = absint( $id );

                $info = get_post_meta( $id, '_data', true );

                if ( $info ) {
                    switch ( $info['type'] ) {
                        case 'post':
                            $post_id = $info['item_number'];
                            $pack_id = 0;
                            break;

                        case 'pack':
                            $post_id = 0;
                            $pack_id = $info['item_number'];
                            break;
                    }

                    $transaction = [
                        'user_id'          => $info['user_info']['id'],
                        'status'           => 'completed',
                        'subtotal'         => $info['subtotal'],
                        'tax'              => $info['tax'],
                        'cost'             => $info['price'],
                        'post_id'          => $post_id,
                        'pack_id'          => $pack_id,
                        'payer_first_name' => $info['user_info']['first_name'],
                        'payer_last_name'  => $info['user_info']['last_name'],
                        'payer_email'      => $info['user_info']['email'],
                        'payment_type'     => 'Bank/Manual',
                        'transaction_id'   => $id,
                        'created'          => current_time( 'mysql' ),
                    ];

                    do_action( 'wpuf_gateway_bank_order_complete', $transaction, $id );

                    WPUF_Payment::insert_payment( $transaction );
                    wp_delete_post( $id, true );
                }
            }

            wp_redirect( $page_url );
            exit;
        }
    }
}
