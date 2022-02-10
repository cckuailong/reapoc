<div class="wpuf-dashboard-container">

    <h2 class="page-head">
        <span class="colour"><?php printf( esc_attr( __( "%s's Dashboard", 'wp-user-frontend' ) ), esc_html( $userdata->display_name )); ?></span>
    </h2>

    <?php if ( wpuf_get_option( 'show_post_count', 'wpuf_dashboard', 'on' ) == 'on' ) { ?>
        <?php if ( !empty( $post_type_obj ) ) { ?>
            <div class="post_count">
                <?php
                $labels = [];

                foreach ( $post_type_obj as $key => $post_type_name ) {
                    if ( isset( $post_type_name->label ) ) {
                        $labels[] = $post_type_name->label;
                    }
                }

                printf(
                    wp_kses_post( __( 'You have created <span>%d</span> (%s)', 'wp-user-frontend' ) ),
                    wp_kses_post( $dashboard_query->found_posts ),
                    wp_kses_post( implode( ', ', $labels ) )
                );
                ?>
            </div>
        <?php } ?>
    <?php } ?>

    <?php
    if ( !empty( $post_type_obj ) ) {
        do_action( 'wpuf_dashboard_top', $userdata->ID, $post_type_obj );
    }

    $meta_label = [];
    $meta_name  = [];
    $meta_id    = [];
    $meta_key   = [];

    if ( !empty( $meta ) ) {
        $arr =  explode( ',', $meta );

        foreach ( $arr as $mkey ) {
            $meta_key[] = trim( $mkey );
        }
    }

    if ( $dashboard_query->have_posts() ) {
        $args = [
            'post_status' => 'publish',
            'post_type'   => [ 'wpuf_forms' ],
        ];

        $query = new WP_Query( $args );

        foreach ( $query->posts as $post ) {
            $postdata = get_object_vars( $post );
            unset( $postdata['ID'] );

            $data = [
                'meta_data' => [
                    'fields'    => wpuf_get_form_fields( $post->ID ),
                ],
            ];

            foreach ( $data['meta_data']['fields'] as $fields ) {
                foreach ( $fields as $key => $field_value ) {
                    if ( $key == 'is_meta' && $field_value == 'yes' ) {
                        $meta_label[]= $fields['label'];
                        $meta_name[] = $fields['name'];
                        $meta_id[]   = $fields['id'];
                    }
                }
            }
        }

        wp_reset_postdata();

        $len               = count( $meta_key );
        $len_label         = count( $meta_label );
        $len_id            = count( $meta_id );
        $featured_img      = wpuf_get_option( 'show_ft_image', 'wpuf_dashboard' );
        $featured_img_size = wpuf_get_option( 'ft_img_size', 'wpuf_dashboard' );
        $enable_payment    = wpuf_get_option( 'enable_payment', 'wpuf_payment' );
        $current_user      = wpuf_get_user();
        $user_subscription = new WPUF_User_Subscription( $current_user );
        $user_sub          = $user_subscription->current_pack();
        $sub_id            = $current_user->subscription()->current_pack_id();

        if ( $sub_id ) {
            $subs_expired = $user_subscription->expired();
        } else {
            $subs_expired = false;
        }
    ?>

    <?php include WPUF_ROOT . '/templates/dashboard/list.php'; ?>
        <div class="wpuf-pagination">
            <?php
                $pagination = paginate_links( [
                    'base'      => add_query_arg( 'pagenum', '%#%' ),
                    'format'    => '',
                    'prev_text' => __( '&laquo;', 'wp-user-frontend' ),
                    'next_text' => __( '&raquo;', 'wp-user-frontend' ),
                    'total'     => $dashboard_query->max_num_pages,
                    'current'   => $pagenum,
                    'add_args'  => false,
                ] );

                if ( $pagination ) {
                    echo wp_kses( $pagination, [
                        'span' => [
                            'aria-current' => [],
                            'class' => [],
                        ],
                        'a' => [
                            'href' => [],
                            'class' => [],
                        ]
                    ] );
                }
            ?>
        </div>
    <?php
        } else {
            if ( !empty( $post_type_obj ) && !empty( $labels ) ) {
                printf( '<div class="wpuf-message">' . wp_kses_post( __( 'No %s found', 'wp-user-frontend' ) ) . '</div>', esc_html( implode( ', ', $labels ) ) );
                do_action( 'wpuf_dashboard_nopost', $userdata->ID, $post_type_obj );
            }
        }

        if ( !empty( $post_type_obj ) ) {
            do_action( 'wpuf_dashboard_bottom', $userdata->ID, $post_type_obj );
        }
    ?>

</div>
