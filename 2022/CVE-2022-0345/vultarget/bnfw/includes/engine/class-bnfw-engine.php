<?php

/**
 * BNFW Engine
 *
 * @since 1.0
 */
class BNFW_Engine {

    /**
     * Send test email.
     *
     * @since 1.2
     *
     * @param array $setting
     */
    public function send_test_email( $setting ) {
        $subject = __( 'Test Email:', 'bnfw' ) . ' ' . $setting[ 'subject' ];
        $message = '<p><strong>' . __( 'This is a test email. All shortcodes below will show in place but not be replaced with content.', 'bnfw' ) . '</strong></p>' . stripslashes( $setting[ 'message' ] );

        if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
            $message = wpautop( $message );
        }

        $current_user = wp_get_current_user();
        $email        = $current_user->user_email;

        $headers = array();
        if ( 'html' == $setting[ 'email-formatting' ] ) {
            $headers[] = 'Content-type: text/html';
            $message   = apply_filters( 'bnfw_test_email_message', $message, $setting );
        } elseif ( 'text' == $setting[ 'email-formatting' ] ) {
            $message = strip_tags( $message );
        }

        wp_mail( $email, stripslashes( $subject ), $message, $headers );
    }

    /**
     * Send the notification email.
     *
     * @since 1.0
     * @param array $setting
     * @param int $id
     */
    public function send_notification( $setting, $id ) {
        /**
         * BNFW - Whether notification is disabled?
         *
         * @since 1.3.6
         */

        $notification_disabled = apply_filters( 'bnfw_notification_disabled', ( 'true' === $setting[ 'disabled' ] ), $id, $setting );

        if ( ! $notification_disabled ) {

            $subject = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $id );
            $message = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $id );
            $emails  = $this->get_emails( $setting, $id );
            $headers = $this->get_headers( $emails );

            if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
                $message = wpautop( $message );
            }

            if ( 'html' == $setting[ 'email-formatting' ] ) {
                $headers[] = 'Content-type: text/html';
                $message   = apply_filters( 'bnfw_notification_message', $message, $setting );
            } else {
                $headers[] = 'Content-type: text/plain';
                if ( 'text' == $setting[ 'email-formatting' ] ) {
                    $message = strip_tags( $message );
                }
            }

            $emails = apply_filters( 'bnfw_emails', $emails, $setting, $id );

            $send = apply_filters( 'bnfw_can_send_email', true, $setting, $emails, $subject, $message, $headers );

            if ( ! $send ) {
                return;
            }

            if ( isset( $emails[ 'to' ] ) && is_array( $emails[ 'to' ] ) ) {
                foreach ( $emails[ 'to' ] as $email ) {
                    wp_mail( $email, stripslashes( $this->handle_global_user_shortcodes( $subject, $email ) ), $this->handle_global_user_shortcodes( $message, $email ), $headers );
                }
            }
        }
    }

    /**
     * Send new user registration notification email.
     *
     * @since 1.1
     * @param array  $setting  Notification setting
     * @param object $user     User object
     * @param string $password_url Plain text password in WP < 4.3 and password url in WP > 4.3
     */
    public function send_registration_email( $setting, $user, $password_url = '' ) {
        /**
         * Whether to trigger welcome email notification or not.
         *
         * @since 1.7
         */
        $trigger_notification = apply_filters( 'bnfw_trigger_welcome-email_notification', true, $setting, $user );

        if ( ! $trigger_notification ) {
            return;
        }

        $user_id = $user->ID;

        $subject = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $user_id );
        $message = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $user_id );

        $subject = str_replace( '[password]', $password_url, $subject );
        $message = str_replace( '[password]', $password_url, $message );

        $subject = str_replace( '[password_url]', $password_url, $subject );
        $message = str_replace( '[password_url]', $password_url, $message );

        $subject = str_replace( '[login_url]', wp_login_url(), $subject );
        $message = str_replace( '[login_url]', wp_login_url(), $message );

        if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
            $message = wpautop( $message );
        }

        $headers = array();
        if ( 'html' == $setting[ 'email-formatting' ] ) {
            $headers[] = 'Content-type: text/html';
            $message   = apply_filters( 'bnfw_registration_email_message', $message, $setting );
        } elseif ( 'text' == $setting[ 'email-formatting' ] ) {
            $message = strip_tags( $message );
        }

        $subject = $this->handle_global_user_shortcodes( $subject, $user->user_email );
        $message = $this->handle_global_user_shortcodes( $message, $user->user_email );
        wp_mail( $user->user_email, stripslashes( $subject ), $message, $headers );
    }

    /**
     * Send user login notification email.
     *
     * @since 1.1
     * @param array  $setting  Notification setting
     * @param object $user     User object
     */
    public function send_user_login_email( $setting, $user ) {

        $trigger_notification = apply_filters( 'bnfw_trigger_user-login_notification', true, $setting, $user );

        if ( ! $trigger_notification ) {
            return;
        }

        $user_id = $user->ID;

        $subject = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $user_id );
        $message = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $user_id );
        $emails  = $this->get_emails( $setting, $user_id );
        $headers = $this->get_headers( $emails );

        if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
            $message = wpautop( $message );
        }

        if ( 'html' == $setting[ 'email-formatting' ] ) {
            $headers[] = 'Content-type: text/html';
            $message   = apply_filters( 'bnfw_notification_message', $message, $setting );
        } elseif ( 'text' == $setting[ 'email-formatting' ] ) {
            $message = strip_tags( $message );
        }

        $subject = $this->handle_global_user_shortcodes( $subject, $user->user_email );
        $message = $this->handle_global_user_shortcodes( $message, $user->user_email );

        wp_mail( $user->user_email, stripslashes( $subject ), $message, $headers );
    }

    /**
     * Send user login notification email for admin.
     *
     * @since 1.1
     * @param array  $setting  Notification setting
     * @param object $user     User object
     */
    public function send_user_login_email_for_admin( $setting, $user ) {

        $trigger_notification = apply_filters( 'bnfw_trigger_user-login_notification', true, $setting, $user );

        if ( ! $trigger_notification ) {
            return;
        }
        $user_id = $user->ID;

        $this->send_notification( $setting, $user_id );
    }

    /**
     * Send comment reply notification email.
     *
     * @since 1.3
     * @param array  $setting        Notification setting
     * @param object $comment        Comment object
     * @param object $parent_comment Parent comment object
     */
    public function send_comment_reply_email( $setting, $comment,
                                              $parent_comment ) {
        $comment_id = $comment->comment_ID;

        /**
         * BNFW - Whether notification is disabled?
         *
         * @since 1.3.6
         */
        $notification_disabled = apply_filters( 'bnfw_notification_disabled', false, $comment_id, $setting );

        if ( ! $notification_disabled ) {
            $subject = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $comment_id );
            $message = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $comment_id );

            $headers = array();
            if ( 'html' == $setting[ 'email-formatting' ] ) {
                $headers[] = 'Content-type: text/html';
            } elseif ( 'text' == $setting[ 'email-formatting' ] ) {
                $message = strip_tags( $message );
            }

            if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
                $message = wpautop( $message );
                $message = apply_filters( 'bnfw_comment_reply_email_message', $message, $setting );
            }

            $subject = $this->handle_global_user_shortcodes( $subject, $parent_comment->comment_author_email );
            $message = $this->handle_global_user_shortcodes( $message, $parent_comment->comment_author_email );
            wp_mail( $parent_comment->comment_author_email, stripslashes( $subject ), $message, $headers );
        }
    }

    /**
     * Send user role changed email.
     *
     * @since 1.3.9
     *
     * @param array $setting Notification setting
     * @param int   $user_id User ID
     * @param array $old_role Old User Role.
     * @param array $new_role New User Role.
     */
    public function send_user_role_changed_email( $setting, $user_id, $old_role,
                                                  $new_role ) {
        $subject = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $user_id );
        $message = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $user_id );

        $subject = $this->handle_user_role_shortcodes( $subject, $old_role, $new_role );
        $message = $this->handle_user_role_shortcodes( $message, $old_role, $new_role );

        $headers = array();
        if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
            $message = wpautop( $message );
        }

        if ( 'html' == $setting[ 'email-formatting' ] ) {
            $headers[] = 'Content-type: text/html';
            $message   = apply_filters( 'bnfw_user_role_changed_email_message', $message, $setting );
        } elseif ( 'text' == $setting[ 'email-formatting' ] ) {
            $message = strip_tags( $message );
        }

        $user = get_user_by( 'id', $user_id );

        $subject = $this->handle_global_user_shortcodes( $subject, $user->user_email );
        $message = $this->handle_global_user_shortcodes( $message, $user->user_email );
        wp_mail( $user->user_email, stripslashes( $subject ), $message, $headers );
    }

    /**
     * Send user role added support User Role Editor by Members Plugin.
     *
     * @since 1.3.9
     *
     * @param array $setting Notification setting
     * @param int   $user_id User ID
     * @param array $old_role Old User Role.
     * @param array $new_role New User Role.
     */
    public function send_user_role_added_email( $setting, $user_id, $old_role,
                                                $new_role ) {
        $subject = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $user_id );
        $message = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $user_id );

        $subject = $this->handle_user_added_role_shortcodes( $subject, $old_role, $new_role );
        $message = $this->handle_user_added_role_shortcodes( $message, $old_role, $new_role );

        $headers = array();
        if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
            $message = wpautop( $message );
        }

        if ( 'html' == $setting[ 'email-formatting' ] ) {
            $headers[] = 'Content-type: text/html';
            $message   = apply_filters( 'bnfw_user_role_changed_email_message', $message, $setting );
        } elseif ( 'text' == $setting[ 'email-formatting' ] ) {
            $message = strip_tags( $message );
        }

        $user = get_user_by( 'id', $user_id );

        $subject = $this->handle_global_user_shortcodes( $subject, $user->user_email );
        $message = $this->handle_global_user_shortcodes( $message, $user->user_email );
        wp_mail( $user->user_email, stripslashes( $subject ), $message, $headers );
    }

    /**
     * Handle User Role shortcodes.
     *
     * @param string $message  String that needs shortcode processing.
     * @param array  $old_role Old User Role.
     * @param array  $new_role New User Role.
     *
     * @return string Processed string.
     */
    public function handle_user_role_shortcodes( $message, $old_role, $new_role ) {
        $roles = wp_roles();

        $old_role_name = '';
        $new_role_name = '';

        if ( isset( $roles->role_names[ $old_role ] ) ) {
            $old_role_name = $roles->role_names[ $old_role ];
        }

        if ( isset( $roles->role_names[ $new_role ] ) ) {
            $new_role_name = $roles->role_names[ $new_role ];
        }

        $message = str_replace( '[user_role_old]', $old_role_name, $message );
        $message = str_replace( '[user_role_new]', $new_role_name, $message );

        return $message;
    }

    /**
     * Handle User Added Role shortcodes.
     *
     * @param string $message  String that needs shortcode processing.
     * @param array  $old_role Old User Role.
     * @param array  $new_role New User Role.
     *
     * @return string Processed string.
     */
    public function handle_user_added_role_shortcodes( $message, $old_roles,
                                                       $new_roles ) {
        $roles = wp_roles();

        $old_role_name = array();
        $new_role_name = array();

        foreach ( $old_roles as $key => $old_role ) {
            if ( isset( $roles->role_names[ $old_role ] ) ) {
                $old_role_name[] = $roles->role_names[ $old_role ];
            }
        }
        foreach ( $new_roles as $key => $new_role ) {
            if ( isset( $roles->role_names[ $new_role ] ) ) {
                $new_role_name[] = $roles->role_names[ $new_role ];
            }
        }

        $message = str_replace( '[user_role_old]', implode( ',', $old_role_name ), $message );
        $message = str_replace( '[user_role_new]', implode( ',', $new_role_name ), $message );

        return $message;
    }

    /**
     * Handle shortcodes for filtered data notifications like `password_changed` and `email_changed`.
     *
     * @since 1.6
     *
     * @param array      $email_data Email data.
     * @param array      $setting    Notification settings.
     * @param string|int $extra_data Extra data.
     *
     * @return array Modified email data.
     */
    public function handle_filtered_data_notification( $email_data, $setting,
                                                       $extra_data ) {
        $email_data[ 'message' ] = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $extra_data );
        $email_data[ 'subject' ] = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $extra_data );

        $email_data[ 'message' ] = $this->handle_global_user_shortcodes( $email_data[ 'message' ], $email_data[ 'to' ] );
        $email_data[ 'subject' ] = $this->handle_global_user_shortcodes( $email_data[ 'subject' ], $email_data[ 'to' ] );

        if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
            $email_data[ 'message' ] = wpautop( $email_data[ 'message' ] );
        }

        if ( 'html' == $setting[ 'email-formatting' ] ) {
            $headers[] = 'Content-type: text/html';
        } else {
            $headers[] = 'Content-type: text/plain';
            if ( 'text' == $setting[ 'email-formatting' ] ) {
                $message = strip_tags( $message );
            }
        }

        $email_data[ 'headers' ] = $headers;

        return $email_data;
    }

    /**
     * Handle shortcodes for core updated notification.
     *
     * @since    1.6
     *
     * @param array  $email_data Email data.
     * @param array  $setting    Notification settings.
     * @param string $type       Result of update.
     *
     * @return array Modified email data.
     */
    public function handle_core_updated_notification( $email_data, $setting,
                                                      $type ) {
        $email_data[ 'body' ]    = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $type );
        $email_data[ 'subject' ] = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $type );

        $emails  = $this->get_emails( $setting, $type );
        $headers = $this->get_headers( $emails );

        $email_data[ 'body' ]    = $this->handle_global_user_shortcodes( $email_data[ 'body' ], $emails[ 'to' ][ 0 ] );
        $email_data[ 'subject' ] = $this->handle_global_user_shortcodes( $email_data[ 'subject' ], $emails[ 'to' ][ 0 ] );

        if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
            $email_data[ 'body' ] = wpautop( $email_data[ 'body' ] );
        }

        if ( 'html' == $setting[ 'email-formatting' ] ) {
            $headers[] = 'Content-type: text/html';
        } else {
            $headers[] = 'Content-type: text/plain';
            if ( 'text' == $setting[ 'email-formatting' ] ) {
                $message = strip_tags( $message );
            }
        }

        $email_data[ 'headers' ] = $headers;

        return $email_data;
    }

    /**
     * Handle shortcode for password reset email message.
     *
     * @since 1.1
     *
     * @param $setting
     * @param $key
     * @param $user_login
     * @param $user_data
     *
     * @return mixed|string
     */
    public function handle_password_reset_shortcodes( $setting, $key,
                                                      $user_login, $user_data ) {
        $message = '';

        if ( '' != $user_login ) {
            // For WordPress version 4.1.0 or less, we could have empty user_login
            $message = $this->handle_shortcodes( $setting[ 'message' ], 'user-password', $user_data->ID );
            $message = $this->handle_global_user_shortcodes( $message, $user_data->user_email );

            $reset_link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );
            $message    = str_replace( '[password_reset_link]', $reset_link, $message );
        }

        return $message;
    }

    /**
     * Send Password Changed email.
     *
     * @param array   $setting Notification Setting.
     * @param WP_User $user    User for whom the password has changed.
     */
    public function send_password_changed_email( $setting, $user ) {
        $user_id = $user->ID;

        $subject = $this->handle_shortcodes( $setting[ 'subject' ], $setting[ 'notification' ], $user_id );
        $message = $this->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $user_id );

        if ( 'true' != $setting[ 'disable-autop' ] && 'html' == $setting[ 'email-formatting' ] ) {
            $message = wpautop( $message );
        }

        $headers = array();
        if ( 'html' == $setting[ 'email-formatting' ] ) {
            $headers[] = 'Content-type: text/html';
        } elseif ( 'text' == $setting[ 'email-formatting' ] ) {
            $message = strip_tags( $message );
        }

        $subject = $this->handle_global_user_shortcodes( $subject, $user->user_email );
        $message = $this->handle_global_user_shortcodes( $message, $user->user_email );
        wp_mail( $user->user_email, stripslashes( $subject ), $message, $headers );
    }

    /**
     * Generate message for notification.
     *
     * @since 1.0
     * public since @since 1.6
     *
     * @param string     $message      String may have shortcode.
     * @param string     $notification Notification name.
     * @param string|int $extra_data   Additional data for shortcode.
     *
     * @return string Processed string.
     */
    public function handle_shortcodes( $message, $notification, $extra_data ) {

        switch ( $notification ) {
            case 'new-comment':
            case 'new-trackback':
            case 'new-pingback':
            case 'reply-comment':
                // handle new comments, trackbacks and pingbacks
                $message = $this->comment_shortcodes( $message, $extra_data );
                $comment = get_comment( $extra_data );
                $message = $this->post_shortcodes( $message, $comment->comment_post_ID );
                if ( 0 != $comment->user_id ) {
                    $message = $this->user_shortcodes( $message, $comment->user_id );
                }
                break;

            case 'admin-password':
            case 'admin-password-changed':
            case 'admin-email-changed':
            case 'admin-user':
            case 'welcome-email':
            case 'user-login':
                $message = $this->user_shortcodes( $message, $extra_data );
                break;
            case 'admin-user-login':
                $message = $this->user_shortcodes( $message, $extra_data );
                break;
            case 'new-user':
            case 'user-role':
            case 'admin-role':
            case 'password-changed':
                // handle users (lost password and new user registration)
                $message = $this->user_shortcodes( $message, $extra_data );
                break;

            case 'email-changed':
            case 'user-password':
                // handle users (lost password and new user registration)
                $message = $this->user_shortcodes( $message, $extra_data, 'email_' );
                break;

            case 'new-category':
                // handle new category
                $message = $this->taxonomy_shortcodes( $message, 'category', $extra_data );
                break;

            case 'new-post_tag':
                // handle new tag
                $message = $this->taxonomy_shortcodes( $message, 'post_tag', $extra_data );
                break;

            case 'core-updated':
                // handle core updated type
                $message = $this->core_updated_shortcodes( $message, $extra_data );
                break;

            case 'data-export':
                // handle data export email
                $message = $this->data_export_shortcodes( $message, $extra_data );
                break;

            case 'data-erased':
                // handle data export email
                $message = $this->data_erased_shortcodes( $message, $extra_data );
                break;

            case 'new-media':
            case 'update-media':
                $message = $this->post_shortcodes( $message, $extra_data );
                $post    = get_post( $extra_data );
                if ( $post instanceof WP_Post ) {
                    $message = $this->user_shortcodes( $message, $post->post_author );
                }
                break;

            default:
                $type = explode( '-', $notification, 2 );
                if ( 'newterm' == $type[ 0 ] ) {
                    // handle new terms
                    $message = $this->taxonomy_shortcodes( $message, $type[ 1 ], $extra_data );
                } elseif ( 'new' == $type[ 0 ] || 'update' == $type[ 0 ] || 'pending' == $type[ 0 ] || 'future' == $type[ 0 ] || 'private' == $type[ 0 ] || 'trash' == $type[ 0 ] ) {
                    // handle new, update and pending posts
                    $post_types = get_post_types( array( 'public' => true ), 'names' );
                    $post_types = array_diff( $post_types, array( BNFW_Notification::POST_TYPE ) );

                    if ( in_array( $type[ 1 ], $post_types ) ) {
                        $message = $this->post_shortcodes( $message, $extra_data );
                        $post    = get_post( $extra_data );
                        if ( $post instanceof WP_Post ) {
                            $message = $this->user_shortcodes( $message, $post->post_author );
                        }
                    }
                } elseif ( 'comment' == $type[ 0 ] || 'moderate' == $type[ 0 ] || 'commentreply' == $type[ 0 ] ) {
                    $message = $this->comment_shortcodes( $message, $extra_data );
                    $comment = get_comment( $extra_data );
                    $message = $this->post_shortcodes( $message, $comment->comment_post_ID );
                    if ( 0 != $comment->user_id ) {
                        $message = $this->user_shortcodes( $message, $comment->user_id );
                    }
                } elseif ( 'approve' === $type[ 0 ] ) {
                    // handle Approve comments notification
                    $message = $this->comment_shortcodes( $message, $extra_data );
                    $comment = get_comment( $extra_data );
                    $message = $this->post_shortcodes( $message, $comment->comment_post_ID );
                    if ( 0 != $comment->user_id ) {
                        $message = $this->user_shortcodes( $message, $comment->user_id );
                    }
                    break;
                } elseif ( 'ca' === $type[ 0 ] ) {
                    $message = $this->confirm_action_shortcodes( $message, $extra_data );
                    $message = $this->handle_global_user_shortcodes( $message, $extra_data[ 'email' ] );
                } elseif ( 'uc' === $type[ 0 ] ) {
                    $message = $this->confirmed_action_shortcodes( $message, $extra_data );
                    $message = $this->handle_global_user_shortcodes( $message, $extra_data[ 'admin_email' ] );
                }
                break;
        }

        $message = $this->global_shortcodes( $message );

        $message = apply_filters( 'bnfw_shortcodes', $message, $notification, $extra_data, $this );
        return $message;
    }

    /**
     * Handle Global shortcodes.
     *
     * @since 1.5
     *
     * @param string $message String with shortcodes.
     *
     * @return string String after processing global shortcodes.
     */
    private function global_shortcodes( $message ) {
        $message = str_replace( '[global_site_title]', get_bloginfo( 'name' ), $message );
        $message = str_replace( '[global_site_tagline]', get_bloginfo( 'description' ), $message );
        $message = str_replace( '[global_site_url]', get_bloginfo( 'url' ), $message );

        $message = str_replace( '[current_time]', current_time( get_option( 'time_format' ) ), $message );
        $message = str_replace( '[current_date]', date_i18n( get_option( 'date_format' ), current_time( 'timestamp' ) ), $message );
        $message = str_replace( '[admin_email]', get_option( 'admin_email' ), $message );

        return $message;
    }

    /**
     * Handle Global shortcodes.
     *
     * @param string $message Message.
     * @param string $email   Email.
     *
     * @return string
     */
    public function handle_global_shortcodes( $message, $email ) {
        $message = $this->global_shortcodes( $message );

        return $this->handle_global_user_shortcodes( $message, $email );
    }

    /**
     * Handle Global User Shortcodes.
     *
     * @param string $message String to be processed.
     * @param string $email   Email of the user.
     *
     * @return string Processed string.
     */
    public function handle_global_user_shortcodes( $message, $email ) {
        $user = get_user_by( 'email', $email );

        if ( false === $user ) {
            $message = str_replace( '[global_user_firstname]', $email, $message );
            $message = str_replace( '[global_user_lastname]', $email, $message );
            $message = str_replace( '[global_user_username]', $email, $message );
        } else {
            $message = str_replace( '[global_user_firstname]', $user->first_name, $message );
            $message = str_replace( '[global_user_lastname]', $user->last_name, $message );
            $message = str_replace( '[global_user_username]', $user->user_login, $message );

            $message = $this->user_shortcodes( $message, $user->ID, 'email_' );
        }

        $message = str_replace( '[privacy_policy_url]', get_privacy_policy_url(), $message );

        $message = str_replace( array('[global_user_email]','[user_email]'), $email, $message );

        return $message;
    }

    /**
     * Handle media post shortcodes.
     *
     * @since 1.0
     * @param string $message
     * @param int $post_id
     * @return string
     */
    public function media_post_shortcodes( $message, $post ) {
        $post_content     = $this->may_be_strip_shortcode( $post->post_content );
        $post_content     = apply_filters( 'the_content', $post_content );
        $post_content     = str_replace( ']]>', ']]&gt;', $post_content );
        $message          = str_replace( '[ID]', $post->ID, $message );
        $message          = str_replace( '[media_date]', bnfw_format_date( $post->post_date ), $message );
        $message          = str_replace( '[media_date_gmt]', bnfw_format_date( $post->post_date_gmt ), $message );
        $message          = str_replace( '[media_description]', $post_content, $message );
        $message          = str_replace( '[media_title]', $post->post_title, $message );
        $message          = str_replace( '[media_alt_text]', get_post_meta( $post->ID, '_wp_attachment_image_alt', true ), $message );
        $message          = str_replace( '[media_caption]', $this->may_be_strip_shortcode( get_the_excerpt( $post ) ), $message );
        $message          = str_replace( '[media_status]', $post->post_status, $message );
        $message          = str_replace( '[media_modified]', bnfw_format_date( $post->post_modified ), $message );
        $message          = str_replace( '[media_modified_gmt]', bnfw_format_date( $post->post_modified_gmt ), $message );
        $message          = str_replace( '[media_content_filtered]', $post->post_content_filtered, $message );
        $message          = str_replace( '[media_type]', $post->post_type, $message );
        $message          = str_replace( '[media_mime_type]', $post->post_mime_type, $message );
        $message          = str_replace( '[media_slug]', $post->post_name, $message );
        $dimensions       = get_post_meta( $post->ID, '_wp_attachment_metadata', true );
        $media_dimensions = $dimensions[ 'width' ] . ' x ' . $dimensions[ 'height' ];
        $message          = str_replace( '[media_dimensions]', $media_dimensions, $message );
        $user_info        = get_userdata( $post->post_author );
        $message          = str_replace( '[media_author]', $user_info->display_name, $message );

        return $message;
    }

    /**
     * Handle post shortcodes.
     *
     * @since 1.0
     * @param string $message
     * @param int $post_id
     * @return string
     */
    public function post_shortcodes( $message, $post_id ) {
        $post = get_post( $post_id );

        if ( ! $post instanceof WP_Post ) {
            return $message;
        }

        if ( $post->post_type == 'attachment' ) {
            $message = $this->media_post_shortcodes( $message, $post );
        }

        $post_content = $this->may_be_strip_shortcode( $post->post_content );
        $post_content = apply_filters( 'the_content', $post_content );
        $post_content = str_replace( ']]>', ']]&gt;', $post_content );

        $message = str_replace( '[ID]', $post->ID, $message );
        $message = str_replace( '[post_date]', bnfw_format_date( $post->post_date ), $message );
        $message = str_replace( '[post_date_gmt]', bnfw_format_date( $post->post_date_gmt ), $message );
        $message = str_replace( '[post_content]', $post_content, $message );
        $message = str_replace( '[post_title]', $post->post_title, $message );
        $message = str_replace( '[post_excerpt]', $this->may_be_strip_shortcode( get_the_excerpt( $post ) ), $message );
        $message = str_replace( '[post_status]', $post->post_status, $message );
        $message = str_replace( '[comment_status]', $post->comment_status, $message );
        $message = str_replace( '[ping_status]', $post->ping_status, $message );
        $message = str_replace( '[post_password]', $post->post_password, $message );
        $message = str_replace( '[post_name]', $post->post_name, $message );
        $message = str_replace( '[post_slug]', $post->post_name, $message );
        $message = str_replace( '[to_ping]', $post->to_ping, $message );
        $message = str_replace( '[pinged]', $post->pinged, $message );
        $message = str_replace( '[post_modified]', bnfw_format_date( $post->post_modified ), $message );
        $message = str_replace( '[post_modified_gmt]', bnfw_format_date( $post->post_modified_gmt ), $message );
        $message = str_replace( '[post_content_filtered]', $post->post_content_filtered, $message );
        $message = str_replace( '[post_parent]', $post->post_parent, $message );
        $message = str_replace( '[post_parent_permalink]', get_permalink( $post->post_parent ), $message );
        $message = str_replace( '[guid]', $post->guid, $message );
        $message = str_replace( '[menu_order]', $post->menu_order, $message );
        $message = str_replace( '[post_type]', $post->post_type, $message );
        $message = str_replace( '[post_mime_type]', $post->post_mime_type, $message );
        $message = str_replace( '[comment_count]', $post->comment_count, $message );
        $message = str_replace( '[permalink]', get_permalink( $post->ID ), $message );
        $message = str_replace( '[post_type_archive]', get_post_type_archive_link( $post->post_type ), $message );

        $message = str_replace( '[edit_post]', $this->get_edit_post_link( $post->ID, 'return' ), $message );

        $featured_image = '';
        if ( has_post_thumbnail( $post->ID ) ) {
            $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
            if ( is_array( $image_url ) ) {
                $featured_image = $image_url[ 0 ];
            }
        }
        $message = str_replace( '[featured_image]', $featured_image, $message );

        $message = str_replace( '[first_image]', $this->get_first_image( $post->post_content ), $message );

        if ( 'future' == $post->post_status ) {
            $message = str_replace( '[post_scheduled_date]', bnfw_format_date( $post->post_date ), $message );
            $message = str_replace( '[post_scheduled_date_gmt]', bnfw_format_date( $post->post_date_gmt ), $message );
        } else {
            $message = str_replace( '[post_scheduled_date]', 'Published', $message );
            $message = str_replace( '[post_scheduled_date_gmt]', 'Published', $message );
        }

        $categories = wp_get_post_categories( $post_id, array( 'fields' => 'all' ) );

        $message = str_replace( '[post_category]', implode( ', ', wp_list_pluck( $categories, 'name' ) ), $message );

        if ( count( $categories ) > 0 ) {
            $message = str_replace(
            array(
                '[post_category_slug]',
                '[post_category_description]',
            ),
            array(
                $categories[ 0 ]->slug,
                $categories[ 0 ]->description,
            ),
            $message
            );
        }

        $tag_list = implode( ', ', wp_get_post_tags( $post_id, array( 'fields' => 'names' ) ) );
        $message  = str_replace( '[post_tag]', $tag_list, $message );

        $user_info = get_userdata( $post->post_author );
        $message   = str_replace( '[post_author]', $user_info->display_name, $message );

        $message = str_replace( '[author_link]', get_author_posts_url( $post->post_author ), $message );

        if ( $last_id = get_post_meta( $post->ID, '_edit_lock', true ) ) {
            
            $last_id = explode(':',$last_id);
            if(count($last_id) > 1){
                $last_id = end($last_id);
            }

            if ( $post->post_author != $last_id ) {
                $last_user_info = get_userdata( $last_id );
            } else {
                $last_user_info = $user_info;
            }

            $message = str_replace( '[post_update_author]', $last_user_info->display_name, $message );
        }

        $message = str_replace( '[post_term', '[post_term id="' . $post_id . '"', $message );
        add_shortcode( 'post_term', array( $this, 'post_term_shortcode_handler' ) );
        $message = do_shortcode( $message );
        remove_shortcode( 'post_term', array( $this, 'post_term_shortcode_handler' ) );

        return apply_filters( 'bnfw_shortcodes_post', $message, $post_id );
    }

    /**
     * Retrieves the edit post link for post.
     *
     * This is a copy of the built-in function without the user check.
     *
     * Can be used within the WordPress loop or outside of it. Can be used with
     * pages, posts, attachments, and revisions.
     *
     * @param int|WP_Post $id      Optional. Post ID or post object. Default is the global `$post`.
     * @param string      $context Optional. How to output the '&' character. Default '&amp;'.
     * @return string|null The edit post link for the given post. null if the post type is invalid or does
     *                     not allow an editing UI.
     */
    public function get_edit_post_link( $id = 0, $context = 'display' ) {
        if ( ! $post = get_post( $id ) )
            return;

        if ( 'revision' === $post->post_type )
            $action = '';
        elseif ( 'display' == $context )
            $action = '&amp;action=edit';
        else
            $action = '&action=edit';

        $post_type_object = get_post_type_object( $post->post_type );
        if ( ! $post_type_object )
            return;

        if ( $post_type_object->_edit_link ) {
            $link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
        } else {
            $link = '';
        }

        /**
         * Filters the post edit link.
         *
         * @since 2.3.0
         *
         * @param string $link    The edit link.
         * @param int    $post_id Post ID.
         * @param string $context The link context. If set to 'display' then ampersands
         *                        are encoded.
         */
        return apply_filters( 'get_edit_post_link', $link, $post->ID, $context );
    }

    /**
     * Handle post term shortcode.
     *
     * @param array $atts Shortocde attributes.
     *
     * @return string Processed string.
     */
    public function post_term_shortcode_handler( $atts ) {
        $atts = shortcode_atts( array(
            'taxonomy' => '',
            'id'       => 0,
        ), $atts );

        $terms = wp_get_post_terms( $atts[ 'id' ], $atts[ 'taxonomy' ], array( 'fields' => 'names' ) );

        if ( ! is_wp_error( $terms ) ) {
            return implode( ', ', $terms );
        }

        return '';
    }

    /**
     * Strip shortcodes, unless disabled.
     *
     * @param string $content Content who's shortcodes should be stripped.
     *
     * @return string Processed content.
     */
    private function may_be_strip_shortcode( $content ) {
        $enable_shortcode = get_option( 'bnfw_enable_shortcodes' );

        if ( '1' == $enable_shortcode ) {
            return $content;
        }

        return strip_shortcodes( $content );
    }

    /**
     * Handle comment shortcodes.
     *
     * @since 1.0
     *
     * @param string $message String to be processed.
     * @param int $comment_id Comment id.
     *
     * @return string Processed string.
     */
    private function comment_shortcodes( $message, $comment_id ) {
        $comment = get_comment( $comment_id );

        $message = str_replace( '[comment_ID]', $comment->comment_ID, $message );
        $message = str_replace( '[comment_post_ID]', $comment->comment_post_ID, $message );
        $message = str_replace( '[comment_author]', $comment->comment_author, $message );
        $message = str_replace( '[comment_author_email]', $comment->comment_author_email, $message );
        $message = str_replace( '[comment_author_url]', $comment->comment_author_url, $message );
        $message = str_replace( '[comment_author_IP]', $comment->comment_author_IP, $message );
        $message = str_replace( '[comment_date]', bnfw_format_date( $comment->comment_date ), $message );
        $message = str_replace( '[comment_date_gmt]', bnfw_format_date( $comment->comment_date_gmt ), $message );
        $message = str_replace( '[comment_content]', get_comment_text( $comment->comment_ID ), $message );
        $message = str_replace( '[comment_karma]', $comment->comment_karma, $message );
        $message = str_replace( '[comment_approved]', str_replace( array( '0', '1', 'spam' ), array( 'Awaiting Moderation', 'Approved', 'Spam' ), $comment->comment_approved ), $message );
        $message = str_replace( '[comment_agent]', $comment->comment_agent, $message );
        $message = str_replace( '[comment_type]', $comment->comment_type, $message );
        $message = str_replace( '[comment_parent]', $comment->comment_parent, $message );
        $message = str_replace( '[user_id]', $comment->user_id, $message );
        $message = str_replace( '[permalink]', get_comment_link( $comment->comment_ID ), $message );
        $message = str_replace( '[comment_moderation_link]', admin_url( 'comment.php?action=editcomment&c=' ) . $comment->comment_ID, $message );
        $message = str_replace( '[comment_moderation_approve]', '<a href="' . wp_nonce_url( admin_url( "comment.php?action=approve&c={$comment->comment_ID}#wpbody-content" ) ) . '">Approve</a>', $message );
        $message = str_replace( '[comment_moderation_spam]', '<a href="' . wp_nonce_url( admin_url( "comment.php?action=spam&c={$comment->comment_ID}#wpbody-content" ) ) . '">Spam</a>', $message );
        $message = str_replace( '[comment_moderation_delete]', '<a href="' . wp_nonce_url( admin_url( "comment.php?action=trash&c={$comment->comment_ID}#wpbody-content" ) ) . '">Delete</a>', $message );

        $parent_comment = get_comment( $comment->comment_parent );
        if ( $parent_comment instanceof WP_Comment ) {
            $message = str_replace( '[comment_parent_content]', $parent_comment->comment_content, $message );
        }

        return $message;
    }

    /**
     * Handle user shortcodes.
     *
     * @since 1.0
     *
     * @param string $message String to be processed.
     * @param int $user_id User id.
     *
     * @return string Processed string.
     */
    public function user_shortcodes( $message, $user_id, $prefix = '' ) {
        global $wp_roles;

        $user_info = get_userdata( $user_id );

        if ( ! $user_info instanceof WP_User ) {
            return $message;
        }

        // deprecated
        $message = str_replace( '[ID]', $user_info->ID, $message );
        $message = str_replace( '[display_name]', $user_info->display_name, $message );
        $message = str_replace( '[nickname]', $user_info->nickname, $message );
        $message = str_replace( '[commenter_avatar]', get_avatar_url( $user_id ), $message );

        $message = str_replace( '[' . $prefix . 'user_id]', $user_info->ID, $message );
        $message = str_replace( '[' . $prefix . 'user_login]', $user_info->user_login, $message );
        $message = str_replace( '[' . $prefix . 'user_nicename]', $user_info->user_nicename, $message );
        $message = str_replace( '[' . $prefix . 'user_email]', $user_info->user_email, $message );
        $message = str_replace( '[' . $prefix . 'user_url]', $user_info->user_url, $message );
        $message = str_replace( '[' . $prefix . 'user_registered]', $user_info->user_registered, $message );
        $message = str_replace( '[' . $prefix . 'user_display_name]', $user_info->display_name, $message );
        $message = str_replace( '[' . $prefix . 'user_firstname]', $user_info->user_firstname, $message );
        $message = str_replace( '[' . $prefix . 'user_lastname]', $user_info->user_lastname, $message );
        $message = str_replace( '[' . $prefix . 'user_nickname]', $user_info->nickname, $message );
        $message = str_replace( '[' . $prefix . 'user_description]', $user_info->user_description, $message );
        $message = str_replace( '[' . $prefix . 'user_avatar]', get_avatar_url( $user_id ), $message );

        $roles   = array_map( array( $this, 'get_role_label_by_name' ), $user_info->roles );
        $message = str_replace( '[' . $prefix . 'user_role]', implode( ', ', $roles ), $message );

        $user_capabilities = bnfw_format_user_capabilities( $user_info->wp_capabilities );
        if ( ! empty( $user_capabilities ) ) {
            $message = str_replace( '[wp_capabilities]', $user_capabilities, $message );
            $message = str_replace( '[' . $prefix . 'user_wp_capabilities]', $user_capabilities, $message );
        }

        $message = apply_filters( 'bnfw_shortcodes_user', $message, $user_id, $prefix );
        return $message;
    }

    /**
     * Handle taxonomy shortcodes.
     *
     * @access private
     * @since 1.1
     *
     * @param string $message
     * @param string $taxonomy
     * @param int $term_id
     * @return string
     */
    private function taxonomy_shortcodes( $message, $taxonomy, $term_id ) {
        $term_info = get_term( $term_id, $taxonomy );

        $message = str_replace( '[slug]', $term_info->slug, $message );
        $message = str_replace( '[name]', $term_info->name, $message );
        $message = str_replace( '[description]', $term_info->description, $message );

        return $message;
    }

    /**
     * Handle Core Updated Shortcodes.
     *
     * @since 1.6
     *
     * @param string $message Original message with shortcodes.
     * @param string $type    The type of email being sent. Can be one of
     *                        'success', 'fail', 'manual', 'critical'.
     *
     * @return string Modified content.
     */
    private function core_updated_shortcodes( $message, $type ) {
        $message = str_replace( '[core_update_status]', $type, $message );

        return $message;
    }

    /**
     * Get the list of emails from the notification settings.
     *
     * @since 1.0
     *
     * @param array $setting Notification settings
     * @param int   $id
     * @param bool  $process_post_authors
     * @param bool  $process_exclude_current_user
     *
     * @return array Emails
     */
    public function get_emails( $setting, $id, $process_post_authors = true,
                                $process_exclude_current_user = true ) {
        global $current_user;

        $emails = array();

        $exclude = null;
        if ( $process_exclude_current_user && 'true' == $setting[ 'disable-current-user' ] ) {
            if ( isset( $current_user->ID ) ) {
                $exclude = $current_user->ID;
            }
        }

        $emails[ 'to' ] = array();

        if ( ! empty( $setting[ 'users' ] ) ) {
            $emails[ 'to' ] = $this->get_emails_from_users( $setting[ 'users' ], $exclude, $id, $setting );
        }

        /**
         * BNFW get to emails.
         */
        if ( $process_post_authors && 'true' === $setting[ 'only-post-author' ] ) {
            $post_id = $id;

            if ( bnfw_is_comment_notification( $setting[ 'notification' ] ) ) {
                $comment = get_comment( $id );
                $post_id = $comment->comment_post_ID;
            }

            $type = explode( '-', $setting[ 'notification' ], 2 );
            if ( 'approve' == $type[ 0 ] ) {
                if ( ! in_array( $comment->comment_author_email, $emails[ 'to' ] ) ) {
                    $emails[ 'to' ][] = $comment->comment_author_email;
                }
            } else {
                if ( $setting[ 'notification' ] == 'user-customfield' || $setting[ 'notification' ] == 'user-customfieldvalue' ) {
                    $post_author = $post_id;
                } else {
                    $post_author = get_post_field( 'post_author', $post_id );
                }
                $author = get_user_by( 'id', $post_author );
                if ( false !== $author && $post_author != $exclude ) {
                    if ( ! in_array( $author->user_email, $emails[ 'to' ] ) ) {
                        $emails[ 'to' ][] = $author->user_email;
                    }
                }
            }
        }

        if ( 'true' == $setting[ 'show-fields' ] ) {
            $default_from_field = get_option( 'blogname' ) . ' <' . get_option( 'admin_email' ) . '>';

            if ( ! empty( $setting[ 'from-name' ] ) && ! empty( $setting[ 'from-email' ] ) && is_email( $setting[ 'from-email' ] ) ) {
                $default_from_field = $setting[ 'from-name' ] . ' <' . $setting[ 'from-email' ] . '>';
            }

            /**
             * Filter Email From Field.
             */
            $emails[ 'from' ] = apply_filters( 'bnfw_from_field', $default_from_field, $setting, $id, $emails[ 'to' ] );

            /**
             * Filter Reply Name Field.
             */
            $emails[ 'reply-name' ] = apply_filters( 'bnfw_reply_name_field', $setting[ 'reply-name' ], $setting, $id, $emails[ 'to' ] );

            /**
             * Filter Reply Email Field.
             */
            $emails[ 'reply-email' ] = apply_filters( 'bnfw_reply_email_field', $setting[ 'reply-email' ], $setting, $id, $emails[ 'to' ] );

            if ( ! empty( $setting[ 'cc' ] ) ) {
                $emails[ 'cc' ] = $this->get_emails_from_users( $setting[ 'cc' ], $exclude, $id, $setting );
            }

            if ( ! empty( $setting[ 'bcc' ] ) ) {
                $emails[ 'bcc' ] = $this->get_emails_from_users( $setting[ 'bcc' ], $exclude, $id, $setting );
            }
        }

        $excluded_emails = array();

        if ( ! empty( $setting[ 'exclude-users' ] ) ) {
            $excluded_emails = $this->get_emails_from_users( $setting[ 'exclude-users' ] );
        }

        if ( ! empty( $excluded_emails ) ) {
            $emails[ 'to' ] = array_diff( $emails[ 'to' ], $excluded_emails );

            if ( ! empty( $emails[ 'cc' ] ) ) {
                $emails[ 'cc' ] = array_diff( $emails[ 'cc' ], $excluded_emails );
            }

            if ( ! empty( $emails[ 'bcc' ] ) ) {
                $emails[ 'bcc' ] = array_diff( $emails[ 'bcc' ], $excluded_emails );
            }
        }
        $emails[ 'to' ] = apply_filters( 'bnfw_to_emails', $emails[ 'to' ], $setting, $id );

        return $emails;
    }

    /**
     * Get emails from users.
     *
     * @since    1.2
     *
     * @param array $users   Users Array
     * @param int   $exclude User id to exclude
     * @param int   $post_id Post id.
     * @param array $setting Notification setting.
     *
     * @return array
     */
    public function get_emails_from_users( $users, $exclude = null,
                                           $post_id = 0, $setting = array() ) {
        $user_ids     = array();
        $user_roles   = array();
        $non_wp_users = array();

        if ( empty( $users ) ) {
            return array();
        }

        foreach ( $users as $user ) {
            if ( $this->starts_with( $user, 'role-' ) ) {
                $user_roles[] = str_replace( 'role-', '', $user );
            } elseif ( strpos( $user, '@' ) !== false ) {
                $non_wp_users[] = $user;
                continue;
            } elseif ( absint( $user ) > 0 ) {
                $user_ids[] = absint( $user );
            } else {
                $non_wp_users[] = $user;
            }
        }

        if ( null != $exclude ) {
            $user_ids = array_diff( $user_ids, array( $exclude ) );
        }

        $emails_from_user_ids   = $this->get_emails_from_id( $user_ids );
        $emails_from_user_roles = $this->get_emails_from_role( $user_roles, $exclude );

        if ( ! empty( $setting ) ) {
            // for new comment notifications, we need to use post id instead of comment id.
            if ( bnfw_is_comment_notification( $setting[ 'notification' ] ) && $post_id ) {
                $comment = get_comment( $post_id );
                $post_id = $comment->comment_post_ID;
            }
        }

        $non_wp_emails = apply_filters( 'bnfw_non_wp_emails', array(), $non_wp_users, $post_id );

        return array_merge( $emails_from_user_roles, $emails_from_user_ids, $non_wp_emails );
    }

    /**
     * Get user emails by user ids.
     *
     * @since 1.0
     *
     * @param array $user_ids.
     *
     * @return array Emails.
     */
    private function get_emails_from_id( $user_ids ) {
        $email_list = array();
        if ( is_array( $user_ids ) && count( $user_ids ) > 0 ) {
            $user_query = new WP_User_Query( array( 'include' => $user_ids ) );
            foreach ( $user_query->results as $user ) {
                $email_list[] = $user->user_email;
            }
        }
        return $email_list;
    }

    /**
     * Get emails of users based on role.
     *
     * @since 1.0
     * @param array $roles User Roles
     * @param int $exclude User id to exclude
     * @return array Email ids
     */
    private function get_emails_from_role( $roles, $exclude = null ) {
        if ( ! is_array( $roles ) ) {
            $roles = array( $roles );
        }

        $email_list = array();
        foreach ( $roles as $role ) {
            $role_name = $this->get_role_name_by_label( $role );
            $users     = get_users(
            array(
                'role'   => $role_name,
                'fields' => array( 'user_email', 'ID' ),
            )
            );

            foreach ( $users as $user ) {
                if ( null != $exclude ) {
                    if ( $user->ID == $exclude ) {
                        continue;
                    }
                }

                if ( ! in_array( $user->user_email, $email_list ) ) {
                    $email_list[] = $user->user_email;
                }
            }
        }

        return $email_list;
    }

    /**
     * Find if a string starts with another string.
     *
     * @since 1.2
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    private function starts_with( $haystack, $needle ) {
        // search backwards starting from haystack length characters from the end
        return '' === $needle || strrpos( $haystack, $needle, -strlen( $haystack ) ) !== false;
    }

    /**
     * Get User role name by label.
     *
     * @param mixed $role_label
     *
     * @return int|string
     */
    protected function get_role_name_by_label( $role_label ) {
        global $wp_roles;
        foreach ( $wp_roles->roles as $role_name => $role_info ) {
            if ( $role_label == $role_info[ 'name' ] || $role_name == $role_label ) {
                return $role_name;
            }
        }

        // There is something wrong
        return '';
    }

    /**
     * Get the lable for a user role from name.
     *
     * @param string $role_name Role name
     *
     * @return string Role Label.
     */
    public function get_role_label_by_name( $role_name ) {
        global $wp_roles;

        if ( ! isset( $wp_roles->roles[ $role_name ] ) ) {
            return '';
        }

        return translate_user_role( $wp_roles->roles[ $role_name ][ 'name' ] );
    }

    /**
     * Get first image in post.
     *
     * @param mixed $post_content
     *
     * @return string
     */
    protected function get_first_image( $post_content ) {
        if ( preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches ) ) {
            return $matches[ 1 ][ 0 ];
        }
    }

    /**
     * Generate email headers based on the emails.
     *
     * @since 1.0
     * @param array $emails
     * @return array
     */
    public function get_headers( $emails ) {
        $headers = array();

        if ( ! empty( $emails[ 'from' ] ) ) {
            $headers[] = 'From:' . $emails[ 'from' ];
        }

        if ( ! empty( $emails[ 'reply-email' ] ) && is_email( $emails[ 'reply-email' ] ) ) {
            $headers[] = 'Reply-To:' . $emails[ 'reply-name' ] . '<' . $emails[ 'reply-email' ] . '>';
        }

        if ( ! empty( $emails[ 'cc' ] ) ) {
            $headers[] = 'Cc:' . implode( ',', $emails[ 'cc' ] );
        }
        if ( ! empty( $emails[ 'bcc' ] ) ) {
            $headers[] = 'Bcc:' . implode( ',', $emails[ 'bcc' ] );
        }

        /**
         * Filter out mail headers.
         *
         * @param array $headers Headers.
         * @param array $emails Emails.
         */
        return apply_filters( 'bnfw_mail_headers', $headers, $emails );
    }

    public function handle_user_request_email_shortcodes( $message, $setting,
                                                          $email_data ) {
        $message = $this->handle_shortcodes( $message, $setting[ 'notification' ], $email_data );

        return $message;
    }

    public function handle_user_confirmed_action_email_shortcodes( $message,
                                                                   $setting,
                                                                   $email_data ) {
        $message = $this->handle_shortcodes( $message, $setting[ 'notification' ], $email_data );

        return $message;
    }

    public function handle_data_export_email_shortcodes( $message, $setting,
                                                         $request_id ) {
        $message = $this->handle_shortcodes( $message, $setting[ 'notification' ], $request_id );

        return $message;
    }

    protected function confirm_action_shortcodes( $message, $extra_data ) {
        $message = $this->data_request_shortcodes( $message, $extra_data );
        $message = str_replace( '[request_confirmation_link]', $extra_data[ 'confirm_url' ], $message );
        if ( isset( $extra_data[ 'email' ] ) ) {
            $message = str_replace( '[request_email]', $extra_data[ 'email' ], $message );
        }

        if ( isset( $extra_data[ 'user_email' ] ) ) {
            $message = str_replace( '[request_email]', $extra_data[ 'user_email' ], $message );
        }

        return $message;
    }

    protected function confirmed_action_shortcodes( $message, $extra_data ) {
        $message = $this->data_request_shortcodes( $message, $extra_data );
        $message = str_replace( '[data_privacy_requests_url]', $extra_data[ 'manage_url' ], $message );
        $message = str_replace( '[request_email]', $extra_data[ 'user_email' ], $message );

        return $message;
    }

    protected function data_request_shortcodes( $message, $extra_data ) {
        $message = str_replace( '[data_request_type]', $extra_data[ 'description' ], $message );

        return $message;
    }

    protected function data_export_shortcodes( $message, $request_id ) {
        $export_file_url = get_post_meta( $request_id, '_export_file_url', true );

        $export_file_url = 'Download File: '.$this->get_export_downloadable_url($request_id);

        $message         = str_replace( '[data_privacy_download_url]', $export_file_url, $message );

        $expiration      = apply_filters( 'wp_privacy_export_expiration', 3 * DAY_IN_SECONDS );
        $expiration_date = date_i18n( get_option( 'date_format' ), time() + $expiration );
        $message         = str_replace( '[data_privacy_download_expiry]', $expiration_date, $message );

        return $message;
    }

    protected function data_erased_shortcodes( $message, $extra_data ) {
        $privacy_policy_url = (!isset($extra_data[ 'privacy_policy_url' ]))? get_privacy_policy_url() : $extra_data[ 'privacy_policy_url' ];

        $message = str_replace( '[privacy_policy_url]', $privacy_policy_url, $message );
        $message = str_replace( '[sitename]', $extra_data[ 'sitename' ], $message );

        return $message;
    }

    /**
     * Process shortcodes in email.
     *
     * @param $email
     * @param $post_id
     * @param $setting
     *
     * @return string
     */
    public function process_shortcodes_in_email( $email, $post_id, $setting,
                                                 $to_emails ) {
        if ( ! empty( $setting ) ) {
            if ( $this->starts_with( $setting[ 'notification' ], 'comment-' ) || $this->starts_with( $setting[ 'notification' ], 'moderate-' ) ) {
                // for new comment notifications, we need to use post id instead of comment id.
                $post_id = bnfw_get_post_id_from_comment( $post_id );
            }
        }

        $email = $this->handle_shortcodes( $email, $setting[ 'notification' ], $post_id );

        if ( is_array( $to_emails ) && ! empty( $to_emails ) ) {
            $to_email = $to_emails[ 0 ];

            $email = $this->handle_global_user_shortcodes( $email, $to_email );
        }

        $processed_emails = array();
        if ( is_email( $email ) ) {
            $processed_emails[] = $email;
        }

        $emails = apply_filters( 'bnfw_non_wp_emails', $processed_emails, array( $email ), $post_id );

        if ( empty( $emails ) ) {
            return '';
        }

        return $emails[ 0 ];
    }

    /**
     * Check email content type.
     *
     * @param string $setting   Setting.
     * @param string $content   Content.
     *
     * @return string Content .
     */
    public function check_email_content_type( $setting, $content ) {

        if ( 'html' == $setting[ 'email-formatting' ] ) {
            add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
            if ( 'true' !== $setting[ 'disable-autop' ] ) {
                $content = wpautop( $content );
            }
        } else {
            add_filter( 'wp_mail_content_type', array( $this, 'set_text_content_type' ) );
            $content = strip_tags( $content );
        }

        return $content;
    }

    /**
     * Set the email formatting to HTML.
     *
     * @since 1.4
     */
    public function set_html_content_type() {
        return 'text/html';
    }

    /**
     * Set the email formatting to text.
     *
     * @since 1.4
     */
    public function set_text_content_type() {
        return 'text/plain';
    }

    /**
     * Get user's download URL from data export request
     *
     * @since 1.8.4
     * @param int $user_email
     * @return string $download_url | string error message
     */
    public function get_export_downloadable_url($request_id = null){
        if(!$request_id)
            return;

        global $wpdb;
        $table = $wpdb->prefix.'posts';
        $query = 'SELECT ID FROM '.$table.' WHERE  `post_type` =  "user_request" AND `ID` = '.$request_id;
        
        $query = apply_filters('export_downloadable_url_query',$query,$request_id);

        $get_id = $wpdb->get_var($query);

        $file = get_post_meta($get_id,'_export_file_name',true);
        $upload_url = wp_upload_dir();
        $dl_url = $upload_url['baseurl'].'/wp-personal-data-exports/'.$file;

        $dl_url = apply_filters('export_downloadable_url_return',$dl_url);

        if($dl_url)
            return $dl_url;
        else
            return __('Error: Download link is not available please contact support');

    }

}
