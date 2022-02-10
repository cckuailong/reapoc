<?php
require_once ABSPATH . '/wp-admin/includes/user.php';

function wpuf_edit_users() {
    ob_start();

    // if user is logged in
    if ( is_user_logged_in() ) {

        //this user can edit the users
        if ( current_user_can( 'edit_users' ) ) {
            $action   = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'show';
            $user_id  = isset( $_GET['user_id'] ) ? intval( wp_unslash( $_GET['user_id'] ) ) : 0;
            $userdata = get_userdata( $user_id );

            switch ( $action ) {
                case 'edit':
                    //if user exists
                    if ( $user_id && $userdata ) {
                        if ( ! empty( wpuf()->free_loader->edit_profile ) ) {
                            wpuf()->free_loader->edit_profile->show_form( $user_id );
                        } else {
                            printf( esc_html( __( "User doesn't exists", 'wp-user-frontend' ) ) );
                        }
                    } else {
                        printf( esc_html( __( "User doesn't exists", 'wp-user-frontend' ) ) );
                    }
                    break;

                case 'wpuf_add_user':
                    wpuf_add_user();
                    break;

                default: wpuf_show_users();
            }
        } else { // user don't have any permission
            printf( esc_html( __( "You don't have permission for this purpose", 'wp-user-frontend' ) ) );
        }
    } else { //user is not logged in
        printf( esc_html( __( 'This page is restricted. Please %s to view this page.', 'wp-user-frontend' ) ), wp_loginout( '', false ) );
    }

    return ob_get_clean();
}

add_shortcode( 'wpuf-edit-users', 'wpuf_edit_users' );

function wpuf_show_users() {
    global $wpdb, $userdata;

    //delete user
    $action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';

    if ( $action == 'del' ) {
        $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

        if ( isset( $nonce ) && !wp_verify_nonce( $nonce, 'wpuf_del_user' ) ) {
            return ;
        }

        $delete_flag = false;

        //get users info
        $cur_user      = $userdata->ID;
        $to_be_deleted = ( isset( $_GET['user_id'] ) ) ? intval( wp_unslash( $_GET['user_id'] ) ) : 0;

        //user can't delete himself and not the admin, whose id is 1
        if ( $cur_user != $to_be_deleted && $to_be_deleted != 1 ) {

            //check that user exists
            $get_user = get_userdata( $to_be_deleted );

            if ( $get_user ) {
                $delete_flag = true;
            }
        }

        //delete the user
        if ( current_user_can( 'delete_users' ) && $delete_flag == true ) {
            wp_delete_user( $to_be_deleted );
            echo wp_kses_post( '<div class="success">' . __( 'User Deleted', 'wp-user-frontend' ) . '</div>' );
        } else {
            echo wp_kses_post( '<div class="error">Cheatin&#8217; uh?</div>' );
        }
    }

    //$sql = "SELECT ID, display_name FROM $wpdb->users ORDER BY user_registered ASC";
    //$users = $wpdb->get_results( $sql );
    $users          = get_users( apply_filters( 'wpuf_show_users', [] ) );
    $delete_message = __( 'Are you sure to delete this user?', 'wp-user-frontend' ); ?>

    <a class="wpuf-button" href="<?php the_permalink(); ?>?action=wpuf_add_user">Add New User</a>

    <?php if ( $users ) { ?>
        <table class="wpuf-table" cellpadding="0" cellspacing="0">
            <tr>
                <th><?php esc_html_e( 'Username', 'wp-user-frontend' ); ?></th>
                <th><?php esc_html_e( 'Action', 'wp-user-frontend' ); ?></th>
            </tr>
            <?php foreach ( $users as $user ) { ?>
                <tr>
                    <td><a href="<?php echo esc_attr( get_author_posts_url( $user->ID ) ); ?>"><?php printf( esc_attr__( '%s', 'wp-user-frontend' ), esc_attr( $user->display_name ) ); ?></td>
                    <td>
                        <a href="<?php echo esc_url( wp_nonce_url( get_permalink() . '?action=edit&user_id=' . $user->ID, 'wpuf_edit_user' ) ); ?>"><?php esc_html_e( 'Edit', 'wp-user-frontend' ); ?></a>
                        <a href="<?php echo esc_url( wp_nonce_url( the_permalink( 'echo=false' ) . '?action=del&user_id=' . $user->ID, 'wpuf_del_user' ) ); ?>" onclick="return confirm('<?php echo esc_html( $delete_message ); ?>');"><span style="color: red;"><?php esc_html_e( 'Delete', 'wp-user-frontend' ); ?></span></a>
                    </td>
                </tr>

            <?php } ?>
        </table>

    <?php } ?>

    <?php
}

function wpuf_add_user() {
    global $wp_error;
    //get admin template file. wp_dropdown_role is there :(
    require_once ABSPATH . '/wp-admin/includes/template.php'; ?>
    <?php if ( current_user_can( 'create_users' ) ) { ?>

        <h3><?php esc_html_e( 'Add New User', 'wp-user-frontend' ); ?></h3>

        <?php

        if ( isset( $_POST['wpuf_new_user_submit'] ) ) {

            $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

            if ( isset( $nonce ) &&  ! wp_verify_nonce(  $nonce, 'wpuf_add_user' ) )  {
                return ;
            }

            $errors = [];

            $username =  isset( $_POST['user_login'] ) ? sanitize_user( wp_unslash( $_POST['user_login'] ) ) : '';
            $email    = isset( $_POST['user_email'] ) ? sanitize_text_field( wp_unslash( $_POST['user_email'] ) ) : '';
            $role     = isset( $_POST['role'] ) ? sanitize_text_field( wp_unslash( $_POST['role'] ) ) : '';

            $error = null;
            $error = wpuf_register_new_user( $username, $email, $role );

            if ( !is_wp_error( $error ) ) {
                echo wp_kses_post( '<div class="success">' . __( 'User Added', 'wp-user-frontend' ) . '</div>' );
            } else {
                echo wp_kses_post( '<div class="error">' . $error->get_error_message() . '</div>' );
            }
        }
        ?>

        <form action="" method="post">
            <?php wp_nonce_field( 'wpuf_add_user' ); ?>
            <ul class="wpuf-post-form">
                <li>
                    <label for="user_login">
                        <?php esc_html_e( 'Username', 'wp-user-frontend' ); ?> <span class="required">*</span>
                    </label>
                    <input type="text" name="user_login" id="user_login" minlength="2" value="<?php if ( isset( $_POST['user_login'] ) ) {
                            $u_login = sanitize_text_field( wp_unslash( $_POST['user_login'] ) );
                            echo $u_login;
                    } ?>">
                    <div class="clear"></div>
                </li>

                <li>
                    <label for="user_email">
                        <?php esc_html_e( 'Email', 'wp-user-frontend' ); ?> <span class="required">*</span>
                    </label>
                    <input type="text" name="user_email" id="user_email" minlength="2" value="<?php if ( isset( $_POST['user_email'] ) ) {
                            $u_email = sanitize_email( wp_unslash( $_POST['user_email'] ) );
                            echo $u_email;
                    } ?>">
                    <div class="clear"></div>
                </li>

                <li>
                    <label for="role">
                        <?php esc_html_e( 'Role', 'wp-user-frontend' ); ?>
                    </label>

                    <select name="role" id="role">
                        <?php
                        if ( !$new_user_role ) {
                            $new_user_role = !empty( $current_role ) ? $current_role : get_option( 'default_role' );
                        }
                        wp_dropdown_roles( $new_user_role );
                        ?>
                    </select>

                    <div class="clear"></div>
                </li>

                <li>
                    <label>&nbsp;</label>
                    <input class="wpuf_submit" type="submit" name="wpuf_new_user_submit" value="<?php echo esc_attr( __( 'Add New User', 'wp-user-frontend' ) ); ?>">
                </li>

            </ul>

        </form>

    <?php } ?>

    <?php
}

/**
 * Handles registering a new user.
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 *
 * @return int|WP_Error either user's ID or error on failure
 */
function wpuf_register_new_user( $user_login, $user_email, $role ) {
    $errors = new WP_Error();

    $sanitized_user_login = sanitize_user( $user_login );
    $user_email           = apply_filters( 'user_registration_email', $user_email );

    // Check the username
    if ( $sanitized_user_login == '' ) {
        $errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username', 'wp-user-frontend' ) );
    } elseif ( !validate_username( $user_login ) ) {
        $errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username', 'wp-user-frontend' ) );
        $sanitized_user_login = '';
    } elseif ( username_exists( $sanitized_user_login ) ) {
        $errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one', 'wp-user-frontend' ) );
    }

    // Check the e-mail address
    if ( $user_email == '' ) {
        $errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address', 'wp-user-frontend' ) );
    } elseif ( !is_email( $user_email ) ) {
        $errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct', 'wp-user-frontend' ) );
        $user_email = '';
    } elseif ( email_exists( $user_email ) ) {
        $errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one', 'wp-user-frontend' ) );
    }

    do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

    $errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

    if ( $errors->get_error_code() ) {
        return $errors;
    }

    $user_pass = wp_generate_password( 12, false );
    //$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );

    $userdata = [
        'user_login' => $sanitized_user_login,
        'user_email' => $user_email,
        'user_pas'   => $user_pass,
        'role'       => $role,
    ];

    $user_id = wp_insert_user( $userdata );

    if ( !$user_id ) {
        $errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'wp-user-frontend' ), get_option( 'admin_email' ) ) );

        return $errors;
    }

    update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

    wp_send_new_user_notifications( $user_id );

    return $user_id;
}
