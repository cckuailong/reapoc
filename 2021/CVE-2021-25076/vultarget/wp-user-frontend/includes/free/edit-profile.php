<?php

class WPUF_Edit_Profile {

    public function __construct() {
        add_shortcode( 'wpuf_editprofile', [$this, 'shortcode'] );

        add_action( 'personal_options_update', [$this, 'post_lock_update'] );
        add_action( 'edit_user_profile_update', [$this, 'post_lock_update'] );

        add_action( 'show_user_profile', [$this, 'post_lock_form'] );
        add_action( 'edit_user_profile', [$this, 'post_lock_form'] );
    }

    /**
     * Hanldes the editprofile shortcode
     *
     * @author Tareq Hasan
     */
    public function shortcode() {
        // wpuf()->plugin_scripts();
        ?>
        <style>
            <?php //echo $custom_css = wpuf_get_option( 'custom_css', 'wpuf_general' );?>
        </style>
        <?php
        ob_start();

        if ( is_user_logged_in() ) {
            $this->show_form();
        } else {
            printf( esc_html( __( 'This page is restricted. Please %s to view this page.', 'wp-user-frontend' ) ), wp_loginout( '', false ) );
        }

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Shows the user profile form
     *
     * @global type $userdata
     *
     * @param type $user_id
     */
    public function show_form( $user_id = null ) {
        global $userdata, $wp_http_referer;
        wp_get_current_user();

        if ( !( function_exists( 'get_user_to_edit' ) ) ) {
            require_once ABSPATH . '/wp-admin/includes/user.php';
        }

        if ( !( function_exists( '_wp_get_user_contactmethods' ) ) ) {
            require_once ABSPATH . '/wp-includes/registration.php';
        }

        if ( !$user_id ) {
            $current_user = wp_get_current_user();
            $user_id      = $user_ID      = $current_user->ID;
        }

        if ( isset( $_POST['submit'] ) ) {
            check_admin_referer( 'update-profile_' . $user_id );
            $errors = edit_user( $user_id );

            if ( is_wp_error( $errors ) ) {
                $message = $errors->get_error_message();
                $style   = 'error';
            } else {
                $message = __( '<strong>Success</strong>: Profile updated', 'wp-user-frontend' );
                $style   = 'success';
                do_action( 'personal_options_update', $user_id );
            }
        }

        $profileuser = get_user_to_edit( $user_id );

        if ( isset( $message ) ) {
            echo wp_kses_post( '<div class="' . $style . '">' . $message . '</div>' );
        } ?>
        <div class="wpuf-profile">
            <form name="profile" id="your-profile" action="" method="post">
                <?php wp_nonce_field( 'update-profile_' . $user_id ); ?>
                <?php if ( $wp_http_referer ) { ?>
                    <input type="hidden" name="wp_http_referer" value="<?php echo esc_url( $wp_http_referer ); ?>" />
                <?php } ?>
                <input type="hidden" name="from" value="profile" />
                <input type="hidden" name="checkuser_id" value="<?php echo esc_attr( $user_id ); ?>" />
                <table class="wpuf-table">
                    <?php do_action( 'personal_options', $profileuser ); ?>
                </table>
                <?php do_action( 'profile_personal_options', $profileuser ); ?>

                <fieldset>
                    <legend><?php esc_html_e( 'Name', 'wp-user-frontend' ); ?></legend>

                    <table class="wpuf-table">
                        <tr>
                            <th><label for="user_login1"><?php esc_html_e( 'Username', 'wp-user-frontend' ); ?></label></th>
                            <td><input type="text" name="user_login" id="user_login1" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" class="regular-text" /><br /><em><span class="description"><?php esc_html_e( 'Usernames cannot be changed.', 'wp-user-frontend' ); ?></span></em></td>
                        </tr>
                        <tr>
                            <th><label for="first_name"><?php esc_html_e( 'First Name', 'wp-user-frontend' ); ?></label></th>
                            <td><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $profileuser->first_name ); ?>" class="regular-text" /></td>
                        </tr>

                        <tr>
                            <th><label for="last_name"><?php esc_html_e( 'Last Name', 'wp-user-frontend' ); ?></label></th>
                            <td><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $profileuser->last_name ); ?>" class="regular-text" /></td>
                        </tr>

                        <tr>
                            <th><label for="nickname"><?php esc_html_e( 'Nickname', 'wp-user-frontend' ); ?> <span class="description"><?php esc_html_e( '(required)', 'wp-user-frontend' ); ?></span></label></th>
                            <td><input type="text" name="nickname" id="nickname" value="<?php echo esc_attr( $profileuser->nickname ); ?>" class="regular-text" /></td>
                        </tr>

                        <tr>
                            <th><label for="display_name"><?php esc_html_e( 'Display to Public as', 'wp-user-frontend' ); ?></label></th>
                            <td>
                                <select name="display_name" id="display_name">
                                    <?php
                                    $public_display = [];
        $public_display['display_username']         = $profileuser->user_login;
        $public_display['display_nickname']         = $profileuser->nickname;

        if ( !empty( $profileuser->first_name ) ) {
            $public_display['display_firstname'] = $profileuser->first_name;
        }

        if ( !empty( $profileuser->last_name ) ) {
            $public_display['display_lastname'] = $profileuser->last_name;
        }

        if ( !empty( $profileuser->first_name ) && !empty( $profileuser->last_name ) ) {
            $public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
            $public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
        }

        if ( !in_array( $profileuser->display_name, $public_display ) ) { // Only add this if it isn't duplicated elsewhere
            $public_display = ['display_displayname' => $profileuser->display_name] + $public_display;
        }
        $public_display = array_map( 'trim', $public_display );
        $public_display = array_unique( $public_display );
        foreach ( $public_display as $id => $item ) {
            ?>
                                        <option id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $item ); ?>"<?php selected( $profileuser->display_name, $item ); ?>><?php echo esc_html( $item ); ?></option>
                                        <?php
        } ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset>
                    <legend><?php esc_html_e( 'Contact Info', 'wp-user-frontend' ); ?></legend>

                    <table class="wpuf-table">
                        <tr>
                            <th><label for="email"><?php esc_html_e( 'E-mail', 'wp-user-frontend' ); ?> <span class="description"><?php esc_html_e( '(required)', 'wp-user-frontend' ); ?></span></label></th>
                            <td><input type="text" name="email" id="email" value="<?php echo esc_attr( $profileuser->user_email ); ?>" class="regular-text" /> </td>
                        </tr>

                        <tr>
                            <th><label for="url"><?php esc_html_e( 'Website', 'wp-user-frontend' ); ?></label></th>
                            <td><input type="text" name="url" id="url" value="<?php echo esc_attr( $profileuser->user_url ); ?>" class="regular-text code" /></td>
                        </tr>

                        <?php
                        foreach ( _wp_get_user_contactmethods() as $name => $desc ) {
                            ?>
                            <tr>
                                <th><label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( apply_filters( 'user_' . $name . '_label', $desc ) ); ?></label></th>
                                <td><input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $profileuser->$name ); ?>" class="regular-text" /></td>
                            </tr>
                            <?php
                        } ?>
                    </table>
                </fieldset>

                <fieldset>
                    <legend><?php esc_html_e( 'About Yourself', 'wp-user-frontend' ); ?></legend>

                    <table class="wpuf-table">
                        <tr>
                            <th><label for="description"><?php esc_html_e( 'Biographical Info', 'wp-user-frontend' ); ?></label></th>
                            <td><textarea name="description" id="description" rows="5" cols="30"><?php echo esc_html( $profileuser->description ); ?></textarea><br />
                                <span class="description"><?php esc_html_e( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'wp-user-frontend' ); ?></span></td>
                        </tr>
                        <tr id="password">
                            <th><label for="pass1"><?php esc_html_e( 'New Password', 'wp-user-frontend' ); ?></label></th>
                            <td>
                                <input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" /><br /><br />
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php esc_html_e( 'Confirm Password', 'wp-user-frontend' ); ?></label></th>
                            <td>
                                <input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" />&nbsp;<em><span class="description"><?php esc_html_e( 'Type your new password again.', 'wp-user-frontend' ); ?></span></em>
                            </td>
                        </tr>
                        <tr>

                            <th><label><?php esc_html_e( 'Password Strength', 'wp-user-frontend' ); ?></label></th>
                            <td>
                                <div id="pass-strength-result"><?php esc_html_e( 'Strength indicator', 'wp-user-frontend' ); ?></div>
                                <script src="<?php echo esc_url( site_url() ); ?>/wp-includes/js/zxcvbn.min.js"></script>
                                <script src="<?php echo esc_url( admin_url() ); ?>/js/password-strength-meter.js"></script>
                                <script type="text/javascript">
                                    var pwsL10n = {
                                        empty: "Strength indicator",
                                        short: "Very weak",
                                        bad: "Weak",
                                        good: "Medium",
                                        strong: "Strong",
                                        mismatch: "Mismatch"
                                    };
                                    try{convertEntities(pwsL10n);}catch(e){};
                                </script>
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <?php do_action( 'show_user_profile', $profileuser ); ?>

                <p class="submit">
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $user_id ); ?>" />
                    <input type="submit" class="wpuf-submit" value="<?php esc_html_e( 'Update Profile', 'wp-user-frontend' ); ?>" name="submit" />
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * Adds the postlock form in users profile
     *
     * @param object $profileuser
     */
    public function post_lock_form( $profileuser ) {
        $current_user      = new WPUF_User( $profileuser );
        $wpuf_subscription = $current_user->subscription();
        $post_locked       = $current_user->post_locked();
        $lock_reason       = $current_user->lock_reason();
        $edit_post_locked  = $current_user->edit_post_locked();
        $edit_lock_reason  = $current_user->edit_post_lock_reason();

        if ( is_admin() && current_user_can( 'edit_users' ) ) {
            $select           = ( $post_locked == true ) ? 'yes' : 'no';
            $edit_post_select = ( $edit_post_locked == true ) ? 'yes' : 'no'; ?>
            <div class="wpuf-user-post-lock">
                <h3><?php esc_html_e( 'WPUF Post Lock', 'wp-user-frontend' ); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="post-lock"><?php esc_html_e( 'Lock Post:', 'wp-user-frontend' ); ?> </label></th>
                        <td>
                            <select name="wpuf_postlock" id="post-lock">
                                <option value="no"<?php selected( $select, 'no' ); ?>>No</option>
                                <option value="yes"<?php selected( $select, 'yes' ); ?>>Yes</option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Lock user from creating new post.', 'wp-user-frontend' ); ?></span></em>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="post-lock"><?php esc_html_e( 'Lock Reason:', 'wp-user-frontend' ); ?> </label></th>
                        <td>
                            <input type="text" name="wpuf_lock_cause" id="wpuf_lock_cause" class="regular-text" value="<?php echo esc_attr( $lock_reason ); ?>" />
                        </td>
                    </tr>

                    <tr>
                        <th><label for="post-lock"><?php esc_html_e( 'Lock Edit Post:', 'wp-user-frontend' ); ?> </label></th>
                        <td>
                            <select name="wpuf_edit_postlock" id="edit-post-lock">
                                <option value="no"<?php selected( $edit_post_select, 'no' ); ?>>No</option>
                                <option value="yes"<?php selected( $edit_post_select, 'yes' ); ?>>Yes</option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Lock user from editing post.', 'wp-user-frontend' ); ?></span></em>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="post-lock"><?php esc_html_e( 'Edit Post Lock Reason:', 'wp-user-frontend' ); ?> </label></th>
                        <td>
                            <input type="text" name="wpuf_edit_post_lock_cause" id="wpuf_edit_post_lock_cause" class="regular-text" value="<?php echo esc_attr( $edit_lock_reason ); ?>" />
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        }
    }

    /**
     * Update user profile lock
     *
     * @param int $user_id
     */
    public function post_lock_update( $user_id ) {
        $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'update-profile_' . $user_id ) ) {
            return ;
        }

        $wpuf_postlock             = isset( $_POST['wpuf_postlock'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_postlock'] ) ) : '';
        $wpuf_lock_cause           = isset( $_POST['wpuf_lock_cause'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_lock_cause'] ) ) : '';
        $wpuf_edit_postlock        = isset( $_POST['wpuf_edit_postlock'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_edit_postlock'] ) ) : '';
        $wpuf_edit_post_lock_cause = isset( $_POST['wpuf_edit_post_lock_cause'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_edit_post_lock_cause'] ) ) : '';

        if ( is_admin() && current_user_can( 'edit_users' ) ) {
            update_user_meta( $user_id, 'wpuf_postlock', $wpuf_postlock );
            update_user_meta( $user_id, 'wpuf_lock_cause', $wpuf_lock_cause );
            update_user_meta( $user_id, 'wpuf_edit_postlock', $wpuf_edit_postlock );
            update_user_meta( $user_id, 'wpuf_edit_post_lock_cause', $wpuf_edit_post_lock_cause );
        }
    }
}
