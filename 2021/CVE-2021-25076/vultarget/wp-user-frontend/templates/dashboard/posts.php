<?php

$post_type = $_GET['section'];

global $userdata;

$userdata = get_userdata( $userdata->ID ); //wp 3.3 fix

global $post;

$pagenum = isset( $_GET['pagenum'] ) ? intval( wp_unslash( $_GET['pagenum'] ) ) : 1;
$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';
// delete post
if ( $action == 'del' ) {
    $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

    if ( isset( $nonce ) && !wp_verify_nonce( $nonce, 'wpuf_del' ) ) {
        return ;
    }

    //check, if the requested user is the post author
    $pid  = isset( $_REQUEST['pid'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['pid'] ) ) : '';
    $type = isset( $_REQUEST['section'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['section'] ) ) : '';
    $maybe_delete = get_post( $pid );

    if ( ( $maybe_delete->post_author == $userdata->ID ) || current_user_can( 'delete_others_pages' ) ) {
        wp_trash_post( $pid );

        //redirect
        $redirect = add_query_arg( [ 'section' => $type, 'msg' => 'deleted'], get_permalink() );
        wp_redirect( $redirect );
        exit;
    } else {
        echo wp_kses_post( '<div class="error">' . __( 'You are not the post author. Cheating huh!', 'wp-user-frontend' ) . '</div>' );
    }
}

// show delete success message
$msg = isset( $_GET['msg'] ) ? sanitize_text_field( wp_unslash( $_GET['msg'] ) ) : '';
if ( $msg == 'deleted' ) {?>
    <div id="wpuf-delete-msg">
        <p><?php esc_attr_e( 'Item Deleted successfully !', 'wp-user-frontend' ); ?></p>
        <span class="dashicons-before dashicons-dismiss"></span>
    </div>
    <script>
        (function ($) {
            var delete_div = $("#wpuf-delete-msg");
            if ((location.search.split('msg' + '=')[1] || '').split('&')[0]==='deleted'){
                delete_div.css({display:'flex'});
                if (delete_div.is(':visible')){
                    setTimeout(function (e) {
                        delete_div.css({display:'none'});
                    },5000)
                }
            }

            $("#wpuf-delete-msg span").on('click',function (e) {
                delete_div.toggle('slow',function () {
                    delete_div.css({display:'none'});
                });
            })
        })(jQuery);
    </script>
<?php
}

$args = [
    'author'         => get_current_user_id(),
    'post_status'    => ['draft', 'future', 'pending', 'publish', 'private'],
    'post_type'      => $post_type,
    'posts_per_page' => wpuf_get_option( 'per_page', 'wpuf_dashboard', 5 ),
    'paged'          => $pagenum,
];

$original_post   = $post;
$dashboard_query = new WP_Query( apply_filters( 'wpuf_dashboard_query', $args ) );
$post_type_obj   = get_post_type_object( $post_type );

?>

<?php do_action( 'wpuf_account_posts_top', $userdata->ID, $post_type_obj ); ?>

<?php if ( $dashboard_query->have_posts() ) { ?>

    <?php
    $featured_img      = wpuf_get_option( 'show_ft_image', 'wpuf_dashboard' );
    $featured_img_size = wpuf_get_option( 'ft_img_size', 'wpuf_dashboard' );
    $payment_column    = wpuf_get_option( 'show_payment_column', 'wpuf_dashboard', 'on' );
    $enable_payment    = wpuf_get_option( 'enable_payment', 'wpuf_payment', 'on' );
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
    <div class="items-table-container">
        <table class="items-table <?php echo esc_attr( $post_type ); ?>" cellpadding="0" cellspacing="0">
            <thead>
                <tr class="items-list-header">
                    <?php
                    if ( 'on' == $featured_img ) {
                        echo wp_kses_post( '<th>' . __( 'Featured Image', 'wp-user-frontend' ) . '</th>' );
                    }
                    ?>
                    <th><?php esc_html_e( 'Title', 'wp-user-frontend' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'wp-user-frontend' ); ?></th>

                    <?php do_action( 'wpuf_account_posts_head_col', $args ); ?>

                    <?php if ( 'on' == $enable_payment && 'off' != $payment_column ) { ?>
                        <th><?php esc_html_e( 'Payment', 'wp-user-frontend' ); ?></th>
                    <?php } ?>

                    <th><?php esc_html_e( 'Options', 'wp-user-frontend' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $post;
                $stickies      = get_option( 'sticky_posts' );
                while ( $dashboard_query->have_posts() ) {
                    $dashboard_query->the_post();
                    $show_link        = !in_array( $post->post_status, ['draft', 'future', 'pending'] );
                    $payment_status   = get_post_meta( $post->ID, '_wpuf_payment_status', true );
                    $is_featured      = in_array( intval( $post->ID ), $stickies, true ) ? ' - ' . esc_html__( 'Featured', 'wp-user-frontend' ) . ucfirst( $post_type ) : '';
                    $title            = wp_trim_words( get_the_title(), 5 ) . $is_featured;
                    ?>
                    <tr>
                        <?php if ( 'on' == $featured_img ) { ?>
                            <td data-label="<?php esc_attr_e( 'Featured Image: ', 'wp-user-frontend' ); ?>">
                                <?php
                                echo $show_link ? wp_kses_post( '<a href="' . get_permalink( $post->ID ) . '">' ) : '';

                                if ( has_post_thumbnail() ) {
                                    the_post_thumbnail( $featured_img_size );
                                } else {
                                    printf( '<img src="%1$s" class="attachment-thumbnail wp-post-image" alt="%2$s" title="%2$s" />', esc_attr( apply_filters( 'wpuf_no_image', plugins_url( '../assets/images/no-image.png', __DIR__ ) ) ), esc_html( __( 'No Image', 'wp-user-frontend' ) ) );
                                }

                                echo $show_link ? '</a>' : '';
                                ?>
                                <span class="post-edit-icon">
                                    &#x25BE;
                                </span>
                            </td>
                        <?php } ?>
                        <td data-label="<?php esc_attr_e( 'Title: ', 'wp-user-frontend' ); ?>" class="<?php echo 'on' === $featured_img ? 'data-column' : '' ; ?>">
                            <?php if ( ! $show_link ) { ?>

                                <?php echo $title ?>

                            <?php } else { ?>

                                <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wp-user-frontend' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php echo $title ?></a>

                            <?php } ?>
                            <?php if ( 'on' !== $featured_img ){?>
                                <span class="post-edit-icon">
                                    &#x25BE;
                                </span>
                            <?php }?>
                        </td>
                        <td data-label="<?php esc_attr_e( 'Status: ', 'wp-user-frontend' ); ?>" class="data-column">
                            <?php wpuf_show_post_status( $post->post_status ); ?>
                        </td>

                        <?php do_action( 'wpuf_account_posts_row_col', $args, $post ); ?>

                        <?php if ( 'on' == $enable_payment && 'off' != $payment_column ) { ?>
                            <td data-label="<?php esc_attr_e( 'Payment: ', 'wp-user-frontend' ); ?>" class="data-column">
                                <?php if ( empty( $payment_status ) ) { ?>
                                    <?php esc_html_e( 'Not Applicable', 'wp-user-frontend' ); ?>
                                    <?php } elseif ( $payment_status != 'completed' ) { ?>
                                        <a href="<?php echo esc_attr( trailingslashit( get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) ) ) ); ?>?action=wpuf_pay&type=post&post_id=<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Pay Now', 'wp-user-frontend' ); ?></a>
                                        <?php } elseif ( $payment_status == 'completed' ) { ?>
                                            <?php esc_html_e( 'Completed', 'wp-user-frontend' ); ?>
                                        <?php } ?>
                                    </td>
                                <?php } ?>

                                <td data-label="<?php esc_attr_e( 'Options: ', 'wp-user-frontend' ); ?>" class="data-column">
                                    <?php
                                    if ( wpuf_get_option( 'enable_post_edit', 'wpuf_dashboard', 'yes' ) == 'yes' ) {
                                        $disable_pending_edit = wpuf_get_option( 'disable_pending_edit', 'wpuf_dashboard', 'on' );
                                        $edit_page            = (int) wpuf_get_option( 'edit_page_id', 'wpuf_frontend_posting' );
                                        $url                  = add_query_arg( ['pid' => $post->ID], get_permalink( $edit_page ) );

                                        $show_edit = true;

                                        if ( $post->post_status == 'pending' && $disable_pending_edit == 'on' ) {
                                            $show_edit  = false;
                                        }

                                        if ( ( $post->post_status == 'draft' || $post->post_status == 'pending' ) && ( !empty( $payment_status ) && $payment_status != 'completed' ) ) {
                                            $show_edit  = false;
                                        }

                                        if ( $subs_expired ) {
                                            $show_edit  = false;
                                        }

                                        if ( $show_edit ) {
                                            ?>
                                            <a class="wpuf-posts-options wpuf-posts-edit" href="<?php echo esc_url( wp_nonce_url( $url, 'wpuf_edit' ) ); ?>"><svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.2175 0.232507L14.0736 2.08857C14.3836 2.39858 14.3836 2.90335 14.0736 3.21336L12.6189 4.66802L9.63808 1.68716L11.0927 0.232507C11.4027 -0.0775022 11.9075 -0.0775022 12.2175 0.232507ZM0 14.3061V11.3253L8.7955 2.52974L11.7764 5.5106L2.98086 14.3061H0Z" fill="#B7C4E7"/></svg></a>
                                            <?php
                                        }
                                    } ?>

                                    <?php
                                    if ( wpuf_get_option( 'enable_post_del', 'wpuf_dashboard', 'yes' ) == 'yes' ) {
                                        $del_url = add_query_arg( ['action' => 'del', 'pid' => $post->ID] );
                                        $message = __( 'Are you sure to delete?', 'wp-user-frontend' ); ?>
                                        <a class="wpuf-posts-options wpuf-posts-delete" style="color: red;" href="<?php echo esc_url_raw( wp_nonce_url( $del_url, 'wpuf_del' ) ); ?>" onclick="return confirm('<?php echo esc_attr( $message ); ?>');"><svg width="15" height="15" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.8082 1.9102H7.98776C7.73445 1.9102 7.49152 1.80958 7.3124 1.63046C7.13328 1.45134 7.03266 1.20841 7.03266 0.955102C7.03266 0.701793 7.13328 0.458859 7.3124 0.279743C7.49152 0.100626 7.73445 0 7.98776 0H11.8082C12.0615 0 12.3044 0.100626 12.4835 0.279743C12.6626 0.458859 12.7633 0.701793 12.7633 0.955102C12.7633 1.20841 12.6626 1.45134 12.4835 1.63046C12.3044 1.80958 12.0615 1.9102 11.8082 1.9102ZM1.30203 2.86529H18.4939C18.7472 2.86529 18.9901 2.96591 19.1692 3.14503C19.3483 3.32415 19.449 3.56708 19.449 3.82039C19.449 4.0737 19.3483 4.31663 19.1692 4.49575C18.9901 4.67486 18.7472 4.77549 18.4939 4.77549H16.5837V16.2367C16.5835 16.9966 16.2815 17.7253 15.7442 18.2626C15.2069 18.7999 14.4782 19.1018 13.7184 19.102H6.07754C5.31768 19.1018 4.58901 18.7998 4.05171 18.2625C3.51441 17.7252 3.21246 16.9966 3.21223 16.2367V4.77549H1.30203C1.04872 4.77549 0.805783 4.67486 0.626667 4.49575C0.44755 4.31663 0.346924 4.0737 0.346924 3.82039C0.346924 3.56708 0.44755 3.32415 0.626667 3.14503C0.805783 2.96591 1.04872 2.86529 1.30203 2.86529ZM8.6631 14.0468C8.84222 13.8677 8.94284 13.6247 8.94284 13.3714V8.5959C8.94284 8.34259 8.84222 8.09966 8.6631 7.92054C8.48398 7.74142 8.24105 7.6408 7.98774 7.6408C7.73443 7.6408 7.4915 7.74142 7.31238 7.92054C7.13327 8.09966 7.03264 8.34259 7.03264 8.5959V13.3714C7.03264 13.6247 7.13327 13.8677 7.31238 14.0468C7.4915 14.2259 7.73443 14.3265 7.98774 14.3265C8.24105 14.3265 8.48398 14.2259 8.6631 14.0468ZM12.4835 14.0468C12.6626 13.8677 12.7633 13.6247 12.7633 13.3714V8.5959C12.7633 8.34259 12.6626 8.09966 12.4835 7.92054C12.3044 7.74142 12.0615 7.6408 11.8081 7.6408C11.5548 7.6408 11.3119 7.74142 11.1328 7.92054C10.9537 8.09966 10.853 8.34259 10.853 8.5959V13.3714C10.853 13.6247 10.9537 13.8677 11.1328 14.0468C11.3119 14.2259 11.5548 14.3265 11.8081 14.3265C12.0615 14.3265 12.3044 14.2259 12.4835 14.0468Z" fill="#B7C4E7"/></svg></a>
                                    <?php
                                    } ?>
                                </td>
                            </tr>
                            <?php
                }

                        wp_reset_postdata();
                        ?>

                    </tbody>
            </table>
            </div>

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
            printf( '<div class="wpuf-message">' . esc_attr( __( 'No %s found', 'wp-user-frontend' ) ) . '</div>', esc_html( $post_type_obj->label ) );
            do_action( 'wpuf_account_posts_nopost', $userdata->ID, $post_type_obj );
        }

        wp_reset_postdata();
