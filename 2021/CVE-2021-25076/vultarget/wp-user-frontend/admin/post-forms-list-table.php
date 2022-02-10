<?php

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Post Forms list table class
 *
 * @since 2.5
 */
class WPUF_Admin_Post_Forms_List_Table extends WP_List_Table {

    /**
     * Class constructor
     *
     * @since 2.5
     *
     * @return void
     */
    public function __construct() {
        global $status, $page, $page_status;

        parent::__construct( [
            'singular' => 'post-form',
            'plural'   => 'post-forms',
            'ajax'     => false,
        ] );
    }

    /**
     * Top filters like All, Published, Trash etc
     *
     * @since 2.5
     *
     * @return array
     */
    public function get_views() {
        $status_links   = [];
        $base_link      = admin_url( 'admin.php?page=wpuf-post-forms' );

        $post_statuses  = apply_filters( 'wpuf_post_forms_list_table_post_statuses', [
            'all'       => __( 'All', 'wp-user-frontend' ),
            'publish'   => __( 'Published', 'wp-user-frontend' ),
            'trash'     => __( 'Trash', 'wp-user-frontend' ),
        ] );

        $current_status = isset( $_GET['post_status'] ) ? sanitize_text_field( wp_unslash( $_GET['post_status'] ) ) : 'all';

        $post_counts = (array) wp_count_posts( 'wpuf_forms' );

        if ( isset( $post_counts['auto-draft'] ) ) {
            unset( $post_counts['auto-draft'] );
        }

        foreach ( $post_statuses as $status => $status_title ) {
            $link = ( 'all' === $status ) ? $base_link : admin_url( 'admin.php?page=wpuf-post-forms&post_status=' . $status );

            if ( $status === $current_status ) {
                $class = 'current';
            } else {
                $class = '';
            }

            switch ( $status ) {
                case 'all':
                    $without_trash = $post_counts;
                    unset( $without_trash['trash'] );

                    $count = array_sum( $without_trash );
                    break;

                default:
                    $count = isset( $post_counts[ $status ] ) ? $post_counts[ $status ] : 0;
                    break;
            }

            $status_links[ $status ] = sprintf(
                '<a class="%s" href="%s">%s <span class="count">(%s)</span></a>',
                $class, $link, $status_title, $count
             );
        }

        return apply_filters( 'wpuf_post_forms_list_table_get_views', $status_links, $post_statuses, $current_status );
    }

    /**
     * Message to show if no item found
     *
     * @since 2.5
     *
     * @return void
     */
    public function no_items() {
        esc_html_e( 'No form found.', 'wp-user-frontend' );
    }

    /**
     * Bulk actions dropdown
     *
     * @since 2.5
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [];

        if ( !isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) {
            $actions['trash'] = __( 'Move to Trash', 'wp-user-frontend' );
        }

        if ( isset( $_GET['post_status'] ) && 'trash' === $_GET['post_status'] ) {
            $actions['restore'] = __( 'Restore', 'wp-user-frontend' );
            $actions['delete']  = __( 'Delete Permanently', 'wp-user-frontend' );
        }

        return apply_filters( 'wpuf_post_forms_list_table_get_bulk_actions', $actions );
    }

    /**
     * List table search box
     *
     * @since 2.5
     *
     * @param string $text
     * @param string $input_id
     *
     * @return void
     */
    public function search_box( $text, $input_id ) {
        if ( empty( $_GET['s'] ) && !$this->has_items() ) {
            return;
        }

        if ( !empty( $_GET['orderby'] ) ) {
            $orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
            echo wp_kses('<input type="hidden" name="orderby" value="' . esc_attr( $orderby ) . '" />', [
                'input' => [
                    'type' => [],
                    'name' => [],
                    'value' => [],
                ]
            ]);
        }

        if ( !empty( $_GET['order'] ) ) {
            $order = sanitize_text_field( wp_unslash( $_GET['order'] ) );
            echo wp_kses( '<input type="hidden" name="order" value="' . esc_attr( $order ) . '" />', [
               'input' => [
                    'type' => [],
                    'name' => [],
                    'value' => [],
                ]
            ] );
        }

        if ( !empty( $_GET['post_status'] ) ) {
            $post_status = sanitize_text_field( wp_unslash( $_GET['post_status'] ) );
            echo wp_kses( '<input type="hidden" name="post_status" value="' . esc_attr( $post_status ) . '" />', [
                'input' => [
                    'type' => [],
                    'name' => [],
                    'value' => [],
                ]
            ] );
        }

        do_action( 'wpuf_post_forms_list_table_search_box', $text, $input_id );

        $input_id = $input_id . '-search-input'; ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_attr( $text ); ?>:</label>
            <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, 'button', 'post_form_search', false, [ 'id' => 'search-submit' ] ); ?>
        </p>
        <?php
    }

    /**
     * Decide which action is currently performing
     *
     * @since 2.5
     *
     * @return string
     */
    public function current_action() {
        if ( isset( $_GET['post_form_search'] ) ) {
            return 'post_form_search';
        }

        return parent::current_action();
    }

    /**
     * Prepare table data
     *
     * @since 2.5
     *
     * @return void
     */
    public function prepare_items() {
        $columns               = $this->get_columns();
        $hidden                = [];
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = [ $columns, $hidden, $sortable ];

        $per_page              = get_option( 'posts_per_page', 20 );
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page - 1 ) * $per_page;

        $args = [
            'offset'         => $offset,
            'posts_per_page' => $per_page,
        ];

        if ( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ) {
            $args['s'] = sanitize_text_field( wp_unslash( $_GET['s'] ) );
        }

        if ( isset( $_GET['post_status'] ) && !empty( $_GET['post_status'] ) ) {
            $args['post_status'] = sanitize_text_field( wp_unslash( $_GET['post_status'] ) );
        }

        if ( isset( $_GET['orderby'] ) && !empty( $_GET['orderby'] ) ) {
            $args['orderby'] = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
        }

        if ( isset( $_GET['order'] ) && !empty( $_GET['order'] ) ) {
            $args['order'] = sanitize_text_field( wp_unslash( $_GET['order'] ) );
        }

        $items  = $this->item_query( $args );

        $this->counts = $items['count'];
        $this->items  = $items['forms'];

        $this->set_pagination_args( [
            'total_items' => $items['count'],
            'per_page'    => $per_page,
        ] );
    }

    /**
     * WP_Query for post forms
     *
     * @since 2.5
     *
     * @param array $args
     *
     * @return array
     */
    public function item_query( $args ) {
        $defauls = [
            'post_status' => 'any',
            'orderby'     => 'DESC',
            'order'       => 'ID',
        ];

        $args = wp_parse_args( $args, $defauls );

        $args['post_type'] = 'wpuf_forms';

        $query = new WP_Query( $args );

        $forms = [];

        if ( $query->have_posts() ) {
            $i = 0;

            while ( $query->have_posts() ) {
                $query->the_post();

                $form = $query->posts[ $i ];

                $settings = get_post_meta( get_the_ID(), 'wpuf_form_settings', true );

                $forms[ $i ] = [
                    'ID'                    => $form->ID,
                    'post_title'            => $form->post_title,
                    'post_status'           => $form->post_status,
                    'settings_post_type'    => isset( $settings['post_type'] ) ? $settings['post_type'] : '',
                    'settings_post_status'  => isset( $settings['post_status'] ) ? $settings['post_status'] : '',
                    'settings_guest_post'   => isset( $settings['guest_post'] ) ? $settings['guest_post'] : '',
                ];

                $i++;
            }
        }

        $forms = apply_filters( 'wpuf_post_forms_list_table_query_results', $forms, $query, $args );
        $count = $query->found_posts;

        wp_reset_postdata();

        return [
            'forms' => $forms,
            'count' => $count,
        ];
    }

    /**
     * Get the column names
     *
     * @since 2.5
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
            'cb'            => '<input type="checkbox" />',
            'form_name'     => __( 'Form Name', 'wp-user-frontend' ),
            'post_type'     => __( 'Post Type', 'wp-user-frontend' ),
            'post_status'   => __( 'Post Status', 'wp-user-frontend' ),
            'guest_post'    => __( 'Guest Post', 'wp-user-frontend' ),
            'shortcode'     => __( 'Shortcode', 'wp-user-frontend' ),
        ];

        return apply_filters( 'wpuf_post_forms_list_table_cols', $columns );
    }

    /**
     * Get sortable columns
     *
     * @since 2.5
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = [
            'form_name' => [ 'form_name', false ],
        ];

        return apply_filters( 'wpuf_post_forms_list_table_sortable_columns', $sortable_columns );
    }

    /**
     * Column values
     *
     * @since 2.5
     *
     * @param array  $item
     * @param string $column_name
     *
     * @return string
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'post_type':
                return $item['settings_post_type'];

            case 'post_status':
                return wpuf_admin_post_status( $item['settings_post_status'] );

            case 'guest_post':
                $is_guest_post  = $item['settings_guest_post'];
                $url            = WPUF_ASSET_URI . '/images/';
                $image          = '<img src="%s" alt="%s">';

                return filter_var( $is_guest_post, FILTER_VALIDATE_BOOLEAN ) ?
                            sprintf( $image, $url . 'tick.png', __( 'Yes', 'wp-user-frontend' ) ) :
                            sprintf( $image, $url . 'cross.png', __( 'No', 'wp-user-frontend' ) );

            case 'shortcode':
                return '<code>[wpuf_form id="' . $item['ID'] . '"]</code>';

            default:
                return apply_filter( 'wpuf_post_forms_list_table_column_default', $item, $column_name );
        }
    }

    /**
     * Checkbox column value
     *
     * @since 2.5
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="post[]" value="%d" />', $item['ID'] );
    }

    /**
     * Form name column value
     *
     * @since 2.5
     *
     * @param array $item
     *
     * @return string
     */
    public function column_form_name( $item ) {
        $actions = [];

        $edit_url       = admin_url( 'admin.php?page=wpuf-post-forms&action=edit&id=' . $item['ID'] );
        $wpnonce        = wp_create_nonce( 'bulk-post-forms' );
        $admin_url      = admin_url( 'admin.php?page=wpuf-post-forms&id=' . $item['ID'] . '&_wpnonce=' . $wpnonce );

        $duplicate_url  = $admin_url . '&action=duplicate';
        $trash_url      = $admin_url . '&action=trash';
        $restore_url    = $admin_url . '&action=restore';
        $delete_url     = $admin_url . '&action=delete';

        if ( ( !isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) && current_user_can( wpuf_admin_role() ) ) {
            $actions['edit']        = sprintf( '<a href="%s">%s</a>', $edit_url, __( 'Edit', 'wp-user-frontend' ) );
            $actions['trash']       = sprintf( '<a href="%s" class="submitdelete">%s</a>', $trash_url, __( 'Trash', 'wp-user-frontend' ) );
            $actions['duplicate']   = sprintf( '<a href="%s">%s</a>', $duplicate_url, __( 'Duplicate', 'wp-user-frontend' ) );

            $title = sprintf(
                '<a class="row-title" href="%1s" aria-label="%2s">%3s</a>',
                $edit_url,
                '"' . $item['post_title'] . '" (Edit)',
                $item['post_title']
            );
        }

        if ( ( isset( $_GET['post_status'] ) && 'trash' === $_GET['post_status'] ) && current_user_can( wpuf_admin_role() ) ) {
            $actions['restore'] = sprintf( '<a href="%s">%s</a>', $restore_url, __( 'Restore', 'wp-user-frontend' ) );
            $actions['delete']  = sprintf( '<a href="%s" class="submitdelete">%s</a>', $delete_url, __( 'Delete Permanently', 'wp-user-frontend' ) );

            $title = sprintf(
                '<strong>%1s</strong>',
                $item['post_title']
            );
        }

        $draft_marker = ( 'draft' === $item['post_status'] ) ?
                            '<strong> — <span class="post-state">' . __( 'Draft', 'wp-user-frontend' ) . '</span></strong>' :
                            '';

        $form_name = sprintf( '%1s %2s %3s', $title, $draft_marker, $this->row_actions( $actions ) );

        return apply_filters( 'wpuf_post_forms_list_table_column_form_name', $form_name, $item );
    }
}
