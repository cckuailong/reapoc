<?php

/**
 * Plugin Name: Better Notifications for WP
 * Plugin URI: https://wordpress.org/plugins/bnfw/
 * Description: Supercharge your WordPress notifications using a WYSIWYG editor and shortcodes. Default and new notifications available. Add more power with Add-ons.
 * Version: 1.8.6
 * Requires at least: 4.8
 * Requires PHP: 5.6
 * Author: Made with Fuel
 * Author URI: https://madewithfuel.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bnfw
 * Domain Path: /languages
 */

/**
 * Copyright © 2021 Made with Fuel Ltd. (hello@betternotificationsforwp.com)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
class BNFW {

    /**
     * Constructor.
     *
     * @since 1.0
     */
    function __construct() {
        $this->load_textdomain();
        $this->includes();
        $this->hooks();

        /**
         * BNFW Notification.
         *
         * @var \BNFW_Notification
         */
        $this->notifier = new BNFW_Notification;

        /**
         * BNFW Engine.
         *
         * @var \BNFW_Engine
         */
        $this->engine = new BNFW_Engine;
    }

    /**
     * Factory method to return the instance of the class.
     *
     * Makes sure that only one instance is created.
     *
     * @return \BNFW Instance of the class.
     */
    public static function factory() {
        static $instance = false;
        if ( ! $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Loads the plugin language files
     *
     * @since  1.0
     */
    public function load_textdomain() {
        // Load localization domain
        $this->translations = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
        load_plugin_textdomain( 'bnfw', false, $this->translations );
    }

    /**
     * Include required files.
     *
     * @since 1.0
     */
    public function includes() {

        // Load license related classes
        if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            require_once 'includes/libraries/EDD_SL_Plugin_Updater.php';
        }

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        require_once 'vendor/persist-admin-notices-dismissal/persist-admin-notices-dismissal.php';

        require_once 'includes/license/class-bnfw-license.php';
        require_once 'includes/license/class-bnfw-license-setting.php';

        // Load Engine
        require_once 'includes/engine/class-bnfw-engine.php';
        require_once 'includes/overrides.php';

        // Load notification post type and notification helpers
        require_once 'includes/admin/class-bnfw-notification.php';
        require_once 'includes/notification/post-notification.php';

        // Helpers
        require_once 'includes/helpers/helpers.php';
        require_once 'includes/helpers/ajax-helpers.php';

        // Load Admin Pages
        if ( is_admin() ) {
            require_once 'includes/admin/bnfw-settings.php';
        }
    }

    /**
     * Register Hooks.
     *
     * @since 1.0
     */
    public function hooks() {
        global $wp_version;

        register_activation_hook( __FILE__, array( $this, 'activate' ) );

        add_action( 'admin_init', array( 'PAnD', 'init' ) );
        add_action( 'admin_init', array( $this, 'add_capability_to_admin' ) );

        add_action( 'draft_to_private', array( $this, 'private_post' ) );
        add_action( 'future_to_private', array( $this, 'private_post' ) );
        add_action( 'pending_to_private', array( $this, 'private_post' ) );
        add_action( 'publish_to_private', array( $this, 'private_post' ) );

        add_action( 'wp_insert_post', array( $this, 'insert_post' ), 10, 3 );

        add_action( 'publish_to_trash', array( $this, 'trash_post' ) );

        add_action( 'auto-draft_to_publish', array( $this, 'publish_post' ) );
        add_action( 'draft_to_publish', array( $this, 'publish_post' ) );
        add_action( 'future_to_publish', array( $this, 'publish_post' ) );
        add_action( 'pending_to_publish', array( $this, 'publish_post' ) );
        add_action( 'private_to_publish', array( $this, 'publish_post' ) );
//		add_action( 'acf/submit_form'           , array( $this, 'acf_submit_form' ), 10, 2 );

        add_action( 'publish_to_publish', array( $this, 'update_post' ) );
        add_action( 'private_to_private', array( $this, 'update_post' ) );

        add_action( 'add_attachment', array( $this, 'new_publish_media_notification' ), 10, 1 );
        add_action( 'edit_attachment', array( $this, 'media_attachment_data_update_notification' ), 10 );

        add_action( 'transition_post_status', array( $this, 'on_post_transition' ), 10, 3 );

        add_action( 'init', array( $this, 'custom_post_type_hooks' ), 100 );
        add_action( 'create_term', array( $this, 'create_term' ), 10, 3 );

        add_action( 'transition_comment_status', array( $this, 'on_comment_status_change' ), 10, 3 );
        add_action( 'comment_post', array( $this, 'comment_post' ) );
        add_action( 'trackback_post', array( $this, 'trackback_post' ) );
        add_action( 'pingback_post', array( $this, 'pingback_post' ) );

        add_action( 'user_register', array( $this, 'user_register' ) );

        add_action( 'user_register', array( $this, 'welcome_email' ) );

        if ( is_plugin_active( 'members/members.php' ) ) {

            add_action('add_user_role', array($this,'user_role_added_from_member_plugin'), 10, 2);
            add_action('remove_user_role', array($this,'user_role_removed_from_member_plugin'), 10, 2);
            add_action('set_user_role', array( $this, 'user_role_changed' ), 10, 3 );

            add_action( 'profile_update', array( $this, 'user_role_added' ), 10, 2 );
        } else {
            add_action( 'set_user_role', array( $this, 'user_role_changed' ), 10, 3 );
        }



        add_action( 'wp_login', array( $this, 'user_login' ), 10, 2 );

        if ( version_compare( $wp_version, '4.4', '>=' ) ) {
            add_filter( 'retrieve_password_title', array( $this, 'change_password_email_title' ), 10, 3 );
        } else {
            add_filter( 'retrieve_password_title', array( $this, 'change_password_email_title' ) );
        }
        add_action( 'lostpassword_post', array( $this, 'on_lost_password' ) );
        add_filter( 'retrieve_password_message', array( $this, 'change_password_email_message' ), 10, 4 );

        add_action( 'after_password_reset', array( $this, 'on_password_reset' ) );

        add_filter( 'send_password_change_email', array( $this, 'should_password_changed_email_be_sent' ), 10, 3 );
        add_filter( 'password_change_email', array( $this, 'on_password_changed' ), 10, 2 );

        add_filter( 'send_email_change_email', array( $this, 'should_email_changed_email_be_sent' ), 10, 3 );
        add_filter( 'email_change_email', array( $this, 'on_email_changed' ), 10, 3 );
        add_filter( 'new_user_email_content', array( $this, 'on_email_changing' ), 10, 2 );

        add_filter( 'auto_core_update_email', array( $this, 'on_core_updated' ), 10, 4 );

        add_filter( 'user_request_action_email_content', array( $this, 'handle_user_request_email_content' ), 10, 2 );
        add_filter( 'user_request_action_email_subject', array( $this, 'handle_user_request_email_subject' ), 10, 3 );

        add_filter( 'user_confirmed_action_email_content', array( $this, 'handle_user_confirmed_action_email_content' ), 10, 2 );

        add_filter( 'wp_privacy_personal_data_email_content', array( $this, 'handle_data_export_email_content' ), 10, 3 );

        add_filter( 'user_erasure_complete_email_subject', array( $this, 'handle_erasure_complete_email_subject' ), 10, 3 );
        add_filter( 'user_confirmed_action_email_content', array( $this, 'handle_erasure_complete_email_content' ), 10, 2 );

        add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 4 );
        add_action( 'shutdown', array( $this, 'on_shutdown' ) );
    }

    /**
     * Add 'bnfw' capability to admin.
     */
    public function add_capability_to_admin() {
        $admins = get_role( 'administrator' );

        if ( is_null( $admins ) ) {
            return;
        }

        if ( ! $admins->has_cap( 'bnfw' ) ) {
            $admins->add_cap( 'bnfw' );
        }
    }

    /**
     * On post transition.
     *
     * @param string   $new_status New post status.
     * @param string   $old_status Old post status.
     * @param \WP_Post $post       Post object.
     */
    public function on_post_transition( $new_status, $old_status, $post ) {
        if ( ! is_a( $post, 'WP_Post' ) ) {
            return;
        }

        if ( 'pending' === $old_status ) {
            return;
        }

        if ( 'pending' !== $new_status ) {
            return;
        }

        $this->on_post_pending( $post->ID, $post );
    }

    /**
     * Setup hooks for custom post types.
     *
     * @since 1.2
     */
    function custom_post_type_hooks() {
        $post_types = get_post_types( array( 'public' => true ), 'names' );
        $post_types = array_diff( $post_types, array( BNFW_Notification::POST_TYPE ) );

        foreach ( $post_types as $post_type ) {
            add_action( 'future_' . $post_type, array( $this, 'on_post_scheduled' ), 10, 2 );
        }
    }

    /**
     * importer
     */
    public function activate() {
        require_once dirname( __FILE__ ) . '/includes/import.php';
        $importer = new BNFW_Import;
        $importer->import();
    }

    /**
     * Add 'Settings' link below BNFW in Plugins list.
     *
     * @since 1.0
     * @param unknown $links
     * @param unknown $file
     * @return unknown
     */
    public function plugin_action_links( $links, $file ) {
        $plugin_file = 'bnfw/bnfw.php';
        if ( $file == $plugin_file ) {
            $settings_link = '<a href="' . esc_url( admin_url( 'edit.php?post_type=bnfw_notification&page=bnfw-settings' ) ) . '">' . esc_html__( 'Settings', 'bnfw' ) . '</a>';
            array_unshift( $links, $settings_link );
        }
        return $links;
    }

    /**
     * When a new term is created.
     *
     * @since 1.0
     * @param int $term_id
     * @param int $tt_id
     * @param string $taxonomy
     */
    public function create_term( $term_id, $tt_id, $taxonomy ) {
        $this->send_notification( 'newterm-' . $taxonomy, $term_id );
    }

    /**
     * Fires when a post is created for the first time.
     *
     * @param int    $post_id Post ID
     * @param object $post    Post object
     * @param bool   $update  Whether this is an existing post being updated or not.
     *
     * @since 1.3.1
     */
    public function insert_post( $post_id, $post, $update ) {
        // Some themes like P2, directly insert posts into DB.
        $insert_post_themes = apply_filters( 'bnfw_insert_post_themes', array( 'P2', 'Syncope' ) );
        $current_theme      = wp_get_theme();

        /**
         * Whether to trigger insert post hook.
         *
         * @since 1.4
         */
        $trigger_insert_post = apply_filters( 'bnfw_trigger_insert_post', false, $post_id, $update );

        if ( in_array( $current_theme->get( 'Name' ), $insert_post_themes ) || $trigger_insert_post ) {
            $this->handle_inserted_post( $post_id, $update );
        }
    }

    /**
     * Trigger New Post published notification for ACF forms.
     *
     * @param string $form ACF Form.
     * @param int    $post_id Post ID.
     */
    public function acf_submit_form( $form, $post_id ) {
        $this->handle_inserted_post( $post_id );
    }

    /**
     * Trigger correct notifications for inserted posts.
     *
     * @param int $post_id Post id.
     * @param bool $update Whether the post was updated.
     *
     * @since 1.6.7
     */
    private function handle_inserted_post( $post_id, $update ) {
        $post = get_post( $post_id );

        if ( ! is_a( $post, 'WP_Post' ) ) {
            return;
        }

        switch ( $post->post_status ) {
            case 'publish':
                if ( $update ) {
                    $this->update_post( $post );
                } else {
                    $this->publish_post( $post );
                }
                break;

            case 'private':
                $this->private_post( $post );
                break;

            case 'pending':
                $this->on_post_pending( $post_id, $post );
                break;

            case 'future':
                $this->on_post_scheduled( $post_id, $post );
                break;
        }
    }

    /**
     * Fires when a post is created for the first time.
     *
     * @since 1.0
     * @param object $post Post Object
     */
    function publish_post( $post ) {
        $post_id   = $post->ID;
        $post_type = $post->post_type;

        if ( BNFW_Notification::POST_TYPE != $post_type ) {
            $this->send_notification_async( 'new-' . $post_type, $post_id );
        }
    }

    /**
     * Fires when a private post is created.
     *
     * @since 1.6
     * @param object $post Post Object
     */
    public function private_post( $post ) {
        $post_id   = $post->ID;
        $post_type = $post->post_type;

        if ( BNFW_Notification::POST_TYPE != $post_type ) {
            $this->send_notification_async( 'private-' . $post_type, $post_id );
        }
    }

    /**
     * Fires when a post is updated.
     *
     * @since 1.0
     * @param unknown $post
     */
    public function update_post( $post ) {
        if ( $this->is_metabox_request() ) {
            return;
        }

        $post_id   = $post->ID;
        $post_type = $post->post_type;

        if ( BNFW_Notification::POST_TYPE != $post_type ) {
            $this->send_notification_async( 'update-' . $post_type, $post_id );
        }
    }

    /**
     * Fires when a post is moved publish to trash.
     *
     */
    public function trash_post( $post ) {
        if ( $this->is_metabox_request() ) {
            return;
        }
        $post_id   = $post->ID;
        $post_type = $post->post_type;

        if ( BNFW_Notification::POST_TYPE != $post_type ) {
            $this->send_notification_async( 'trash-' . $post_type, $post_id );
        }
    }

    /**
     * Fires when a post is pending for review.
     *
     * @since 1.1
     * @param int $post_id Post ID
     * @param object $post Post object
     */
    public function on_post_pending( $post_id, $post ) {
        if ( $this->is_metabox_request() ) {
            return;
        }

        $post_type = $post->post_type;

        if ( BNFW_Notification::POST_TYPE != $post_type ) {
            $this->send_notification_async( 'pending-' . $post_type, $post_id );
        }
    }

    /**
     * On Media Published.
     *
     * @param int  $post_id Attachment post id.
     */
    public function new_publish_media_notification( $post_id ) {
        $post_type = get_post_type( $post_id );

        if ( BNFW_Notification::POST_TYPE != $post_type && $post_type == 'attachment' ) {
            $this->send_notification_async( 'new-media', $post_id );
        }
    }

    /**
     * On Media Attachment Data Update.
     *
     * @param int  $post_id Attachment post id.
     */
    public function media_attachment_data_update_notification( $post_id ) {
        $post_type = get_post_type( $post_id );
        if ( BNFW_Notification::POST_TYPE != $post_type && $post_type == 'attachment' ) {
            $this->send_notification_async( 'update-media', $post_id );
        }
    }

    /**
     * Fires when a post is scheduled.
     *
     * @since 1.1.5
     * @param int $post_id Post ID
     * @param object $post Post object
     */
    function on_post_scheduled( $post_id, $post ) {
        // Rest request also triggers the same hook. We can ignore it.
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return;
        }

        $post_type = $post->post_type;

        if ( BNFW_Notification::POST_TYPE != $post_type ) {
            $this->send_notification_async( 'future-' . $post_type, $post_id );
        }
    }

    /**
     * When the status of a comment is changed.
     *
     * @param string      $new_status New status.
     * @param string      $old_status Old status.
     * @param \WP_Comment $comment    Comment.
     */
    public function on_comment_status_change( $new_status, $old_status, $comment ) {
        if ( 'approved' !== $new_status ) {
            return;
        }

        $post = get_post( $comment->comment_post_ID );

        $notification_type = 'approve-' . $post->post_type . '-comment';

        $this->send_notification( $notification_type, $comment->comment_ID, false );

        // Send new comment notification after comment approve
        $notification_type = 'new-comment'; // old notification name

        if ( 'post' != $post->post_type ) {
            $notification_type = 'comment-' . $post->post_type;
        }

        $this->send_notification( $notification_type, $comment->comment_ID );

        // Send comment reply notification after comment approve.
        $this->commentsReply( $comment->comment_ID );
    }

    /**
     * Send notification for new comments
     *
     * @since 1.0
     * @param int $comment_id
     */
    public function comment_post( $comment_id ) {
        $the_comment = get_comment( $comment_id );
        $post        = get_post( $the_comment->comment_post_ID );

        if ( '1' !== $the_comment->comment_approved ) {
            if ( $this->can_send_comment_notification( $the_comment ) ) {
                $notification_type = 'moderate-' . $post->post_type . '-comment';
                $this->send_notification( $notification_type, $comment_id );
            }
        } else {
            $notification_type = 'new-comment'; // old notification name

            if ( 'post' != $post->post_type ) {
                $notification_type = 'comment-' . $post->post_type;
            }

            $this->send_notification( $notification_type, $comment_id );

            // comment reply notification.
            $this->commentsReply( $comment_id );
        }
    }

    /**
     * Send notification for comments reply
     *
     * @since 1.0
     * @param int $comment_id
     */
    public function commentsReply( $comment_id ) {
        $the_comment = get_comment( $comment_id );
        $post        = get_post( $the_comment->comment_post_ID );

        // comment reply notification.
        if ( $this->can_send_comment_notification( $the_comment ) ) {
            if ( $the_comment->comment_parent > 0 ) {
                $notification_type = 'reply-comment'; // old notification name
                if ( 'post' != $post->post_type ) {
                    $notification_type = 'commentreply-' . $post->post_type;
                }
                $notifications = $this->notifier->get_notifications( $notification_type );
                if ( count( $notifications ) > 0 ) {
                    $parent = get_comment( $the_comment->comment_parent );
                    if ( $parent->comment_author_email != $the_comment->comment_author_email ) {
                        foreach ( $notifications as $notification ) {
                            $this->engine->send_comment_reply_email( $this->notifier->read_settings( $notification->ID ), $the_comment, $parent );
                        }
                    }
                }
            }
        }
    }

    /**
     * Send notification for new trackback
     *
     * @since 1.0
     * @param unknown $comment_id
     */
    function trackback_post( $comment_id ) {
        $the_comment = get_comment( $comment_id );
        if ( $this->can_send_comment_notification( $the_comment ) ) {
            $this->send_notification( 'new-trackback', $comment_id );
        }
    }

    /**
     * Send notification for new pingbacks
     *
     * @since 1.0
     * @param unknown $comment_id
     */
    function pingback_post( $comment_id ) {
        $the_comment = get_comment( $comment_id );
        if ( $this->can_send_comment_notification( $the_comment ) ) {
            $this->send_notification( 'new-pingback', $comment_id );
        }
    }

    /**
     * Send notification for lost password.
     *
     * @since 1.0
     */
    function on_lost_password() {
        $user_login = sanitize_text_field( $_POST[ 'user_login' ] );
        $user       = get_user_by( 'login', $user_login ) ?: get_user_by( 'email', $user_login );
        if ( $user ) {
            $this->send_notification( 'admin-password', $user->ID );
        }
    }

    /**
     * Change the title of the password reset email that is sent to the user.
     *
     * @since 1.1
     *
     * @param string $title
     * @param string $user_login
     * @param string $user_data
     *
     * @return string
     */
    public function change_password_email_title( $title, $user_login = '',
                                                 $user_data = '' ) {
        $notifications = $this->notifier->get_notifications( 'user-password' );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting = $this->notifier->read_settings( end( $notifications )->ID );

            if ( '' === $user_data ) {
                return $this->engine->handle_shortcodes( $setting[ 'subject' ], 'user-password', $user_data->ID );
            } else {
                return $this->engine->handle_shortcodes( $setting[ 'subject' ], 'user-password', $user_data->ID );
            }
        }

        return $title;
    }

    /**
     * Change the message of the password reset email.
     *
     * @since 1.1
     *
     * @param string $message
     * @param string $key
     * @param string $user_login
     * @param string $user_data
     *
     * @return string
     */
    public function change_password_email_message( $message, $key,
                                                   $user_login = '',
                                                   $user_data = '' ) {
        $notifications = $this->notifier->get_notifications( 'user-password' );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting = $this->notifier->read_settings( end( $notifications )->ID );

            $message = $this->engine->handle_password_reset_shortcodes( $setting, $key, $user_login, $user_data );

            if ( 'html' == $setting[ 'email-formatting' ] ) {
                add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
                if ( 'true' !== $setting[ 'disable-autop' ] ) {
                    $message = wpautop( $message );
                }
            } else {
                add_filter( 'wp_mail_content_type', array( $this, 'set_text_content_type' ) );
                if ( 'text' == $setting[ 'email-formatting' ] ) {
                    $message = strip_tags( $message );
                }
            }
        } else {
            if ( $this->notifier->notification_exists( 'user-password', false ) ) {
                // disabled notification exists, so disable the email by returning empty string.
                return '';
            }
        }

        return $message;
    }

    /**
     * On Password reset.
     *
     * @param WP_User $user User who's password was changed.
     */
    public function on_password_reset( $user ) {
        $notifications = $this->notifier->get_notifications( 'password-changed' );
        foreach ( $notifications as $notification ) {
            $this->engine->send_password_changed_email( $this->notifier->read_settings( $notification->ID ), $user );
        }
    }

    /**
     * Should the password changed email be sent?
     *
     * @param $send
     * @param $user
     * @param $userdata
     *
     * @return bool
     */
    public function should_password_changed_email_be_sent( $send, $user,
                                                           $userdata ) {
        $bnfw = BNFW::factory();

        if ( ! $send ) {
            return $send;
        }

        return ! $bnfw->notifier->is_notification_disabled( 'password-changed' );
    }

    /**
     * On Password Changed.
     *
     * @since 1.6
     *
     * @param array $email_data Email Data.
     * @param array $user       User data.
     *
     * @return array Modified Email Data
     */
    public function on_password_changed( $email_data, $user ) {
        return $this->handle_filtered_data_notification( 'password-changed', $email_data, $user[ 'ID' ] );
    }

    /**
     * Should the email changed email be sent?
     *
     * @param $send
     * @param $user
     * @param $userdata
     *
     * @return bool
     */
    public function should_email_changed_email_be_sent( $send, $user_old_data,
                                                        $user_new_data ) {
        $bnfw = BNFW::factory();

        if ( $bnfw->notifier->notification_exists( 'admin-email-changed', false ) ) {
            $notifications = $bnfw->notifier->get_notifications( 'admin-email-changed' );

            if ( count( $notifications ) > 0 ) {
                // Ideally there should be only one notification for this type.
                // If there are multiple notification then we will read data about only the last one
                $setting               = $bnfw->notifier->read_settings( end( $notifications )->ID );
                $notification_disabled = apply_filters( 'bnfw_notification_disabled', ( 'true' === $setting[ 'disabled' ] ), $id, $setting );

                if ( ! $notification_disabled ) {

                    $setting[ 'message' ] = str_replace( '[user_old_email]', $user_old_data[ 'user_email' ], $setting[ 'message' ] );
                    $setting[ 'message' ] = str_replace( '[user_new_email]', $user_new_data[ 'user_email' ], $setting[ 'message' ] );
                    $bnfw->engine->send_notification( $setting, $user_old_data[ 'ID' ] );
                }
            }
        }

        if ( ! $send ) {
            return $send;
        }

        return ! $bnfw->notifier->is_notification_disabled( 'email-changed' );
    }

    /**
     * On Email Changed.
     *
     * @since 1.6
     *
     * @param array $email_data Email Data.
     * @param array $user       User data.
     *
     * @return array Modified Email Data
     */
    public function on_email_changed( $email_data, $user_old_data,
                                      $user_new_data ) {

        $email              = $this->handle_filtered_data_notification( 'email-changed', $email_data, $user_old_data[ 'ID' ] );
        $email[ 'message' ] = str_replace( '[user_old_email]', $user_old_data[ 'user_email' ], $email[ 'message' ] );
        $email[ 'message' ] = str_replace( '[user_new_email]', $user_new_data[ 'user_email' ], $email[ 'message' ] );
        return $email;
    }

    public function on_email_changing( $email_text, $new_user_details ) {
        $notification_name = 'email-changing';

        $notifications = $this->notifier->get_notifications( $notification_name );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting = $this->notifier->read_settings( end( $notifications )->ID );

            $email_text = $this->engine->handle_shortcodes( $setting[ 'message' ], $setting[ 'notification' ], $new_user_details[ 'newemail' ] );
            $email_text = $this->engine->handle_global_user_shortcodes( $email_text, $new_user_details[ 'newemail' ] );
            $email_text = str_replace( '[email_change_confirmation_link]', esc_url( admin_url( 'profile.php?newuseremail=' . $new_user_details[ 'hash' ] ) ), $email_text );
        }

        return $email_text;
    }

    /**
     * Send notification on core updated event.
     *
     * @since 1.6
     *
     * @param array  $email_data  Email Data.
     * @param string $type        The type of email being sent. Can be one of
     *                            'success', 'fail', 'manual', 'critical'.
     * @param object $core_update The update offer that was attempted.
     * @param mixed  $result      The result for the core update. Can be WP_Error.
     *
     * @return array Modified Email Data.
     */
    public function on_core_updated( $email_data, $type, $core_update, $result ) {
        $notifications = $this->notifier->get_notifications( 'core-updated' );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting = $this->notifier->read_settings( end( $notifications )->ID );

            $email_data = $this->engine->handle_core_updated_notification( $email_data, $setting, $type );
        }

        return $email_data;
    }

    /**
     * Process User update notifications.
     *
     * @since 1.6
     *
     * @param string     $notification_name Notification Name.
     * @param array      $email_data        Email Data.
     * @param string|int $extra_data        User Id.
     *
     * @return array Modified Email Data.
     */
    private function handle_filtered_data_notification( $notification_name,
                                                        $email_data, $extra_data ) {
        $notifications = $this->notifier->get_notifications( $notification_name );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting = $this->notifier->read_settings( end( $notifications )->ID );

            $email_data = $this->engine->handle_filtered_data_notification( $email_data, $setting, $extra_data );
        }

        return $email_data;
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
     * Send notification for new users.
     *
     * @since 1.0
     * @param int $user_id
     */
    public function user_register( $user_id ) {
        $this->send_notification( 'admin-user', $user_id );
    }

    /**
     * Send notification for user when user login.
     *
     * @since 1.0
     * @param string $user_name
     * @param object $user_data User object.
     */
    public function user_login( $user_name, $user_data ) {
        $user_id       = $user_data->ID;
        $notifications = $this->notifier->get_notifications( 'user-login' );

        foreach ( $notifications as $notification ) {
            $this->engine->send_user_login_email( $this->notifier->read_settings( $notification->ID ), get_userdata( $user_id ) );
        }

        $this->user_login_admin_notification( $user_id );
    }

    /**
     * Send notification for admin when user login.
     *
     * @since 1.0
     * @param int $user_id
     */
    public function user_login_admin_notification( $user_id ) {
        $notifications = $this->notifier->get_notifications( 'admin-user-login' );

        foreach ( $notifications as $notification ) {
            $this->engine->send_user_login_email_for_admin( $this->notifier->read_settings( $notification->ID ), get_userdata( $user_id ) );
        }
    }

    /**
     * Send notification about new users to site admin.
     *
     * @since 1.7.1
     *
     * @param array   $email_data Email details.
     * @param WP_User $user       User object.
     * @param string  $blogname   Blog name.
     *
     * @return array Modified email details.
     */
    public function handle_user_registered_admin_email( $email_data, $user,
                                                        $blogname ) {
        return $this->handle_filtered_data_notification( 'admin-user', $email_data, $user->ID );
    }

    /**
     * New User - Post-registration Email
     *
     * @since 1.1
     * @param int $user_id New user id
     */
    public function welcome_email( $user_id ) {
        $notifications = $this->notifier->get_notifications( 'welcome-email' );
        foreach ( $notifications as $notification ) {
            $this->engine->send_registration_email( $this->notifier->read_settings( $notification->ID ), get_userdata( $user_id ) );
        }
    }

    /**
     * Send notification when a user role changes.
     *
     * @since 1.3.9
     *
     * @param int    $user_id   User ID
     * @param string $new_role  New User role
     * @param array  $old_roles Old User role
     */
    public function user_role_changed( $user_id, $new_role, $old_roles ) {
        if ( ! empty( $old_roles ) ) {
            $notifications = $this->notifier->get_notifications( 'user-role' );
            foreach ( $notifications as $notification ) {

                /**
                 * Trigger User Role Changed - For User notification.
                 *
                 * @since 1.6.5
                 */
                if ( apply_filters( 'bnfw_trigger_user-role_notification', true, $notification, $new_role, $old_roles ) ) {
                    $this->engine->send_user_role_changed_email(
                    $this->notifier->read_settings( $notification->ID ),
                                                    $user_id,
                                                    $old_roles[ 0 ],
                                                    $new_role
                    );
                }
            }

            $notifications = $this->notifier->get_notifications( 'admin-role' );
            foreach ( $notifications as $notification ) {

                /**
                 * Trigger User Role Changed - For User notification.
                 *
                 * @since 1.6.5
                 */
                if ( apply_filters( 'bnfw_trigger_admin-role_notification', true, $notification, $new_role, $old_roles ) ) {
                    $setting            = $this->notifier->read_settings( $notification->ID );
                    $setting[ 'message' ] = $this->engine->handle_user_role_shortcodes( $setting[ 'message' ], $old_roles[ 0 ], $new_role );
                    $setting[ 'subject' ] = $this->engine->handle_user_role_shortcodes( $setting[ 'subject' ], $old_roles[ 0 ], $new_role );

                    $this->engine->send_notification( $setting, $user_id );
                }
            }
        }
    }

    /**
     * Send notification when a user role added through Members Plugin.
     *
     * @since 1.8.4
     *
     * @param int    $user_id   User ID
     * @param string $new_role  New User role
     *
     */
    public function user_role_added_from_member_plugin($user_id, $new_role){

        global $pagenow;

        if($pagenow != 'users.php')
            return;

        if(!$user_id)
            return;

        $notifications = $this->notifier->get_notifications( 'user-role' );

        foreach ( $notifications as $notification ) {

            if ( apply_filters( 'bnfw_trigger_user-role_notification', true, $notification, $new_role, null ) ) {
                $this->engine->send_user_role_changed_email(
                $this->notifier->read_settings( $notification->ID ),
                    $user_id,
                    null,
                    $new_role
                );
            }
        }

        $notifications_admin = $this->notifier->get_notifications( 'admin-role' );
        foreach ( $notifications_admin as $notification ) {
            if ( apply_filters( 'bnfw_trigger_admin-role_notification', true, $notification, $new_role, null ) ) {
                $setting            = $this->notifier->read_settings( $notification->ID );
                $setting[ 'message' ] = $this->engine->handle_user_role_shortcodes( $setting[ 'message' ], null, $new_role );
                $setting[ 'subject' ] = $this->engine->handle_user_role_shortcodes( $setting[ 'subject' ], null, $new_role );

                $this->engine->send_notification( $setting, $user_id );
            }
        }

    }

    /**
     * Send notification when a user role removed through Members Plugin.
     *
     * @since 1.8.4
     *
     * @param int    $user_id   User ID
     * @param string $old_role  New User role
     *
     */
    public function user_role_removed_from_member_plugin($user_id, $old_role){
        global $pagenow;

        if($pagenow != 'users.php')
            return;

        if(!$user_id)
            return;

        $notifications = $this->notifier->get_notifications( 'user-role' );

        foreach ( $notifications as $notification ) {
            if ( apply_filters( 'bnfw_trigger_user-role_notification', true, $notification, null, array($old_role) ) ) {
                $this->engine->send_user_role_changed_email(
                $this->notifier->read_settings( $notification->ID ),
                    $user_id,
                    $old_role,
                    null
                );
            }
        }

        $notifications_admin = $this->notifier->get_notifications( 'admin-role' );
        foreach ( $notifications_admin as $notification ) {
            if ( apply_filters( 'bnfw_trigger_admin-role_notification', true, $notification, null, array($old_role) ) ) {
                $setting            = $this->notifier->read_settings( $notification->ID );
                $setting[ 'message' ] = $this->engine->handle_user_role_shortcodes( $setting[ 'message' ], $old_role, null );
                $setting[ 'subject' ] = $this->engine->handle_user_role_shortcodes( $setting[ 'subject' ], $old_role, null );



                $this->engine->send_notification( $setting, $user_id );
            }
        }

    }

    /**
     * Send notification when a user role added support User Role Editor by Members Plugin.
     *
     * @since 1.3.9
     *
     * @param int    $user_id   User ID
     * @param string $new_role  New User role
     * @param array  $old_roles Old User role
     */
    public function user_role_added( $user_id, $old_user_data ) {

        if ( isset( $_POST[ 'members_user_roles' ] ) && ! empty( $_POST[ 'members_user_roles' ] ) ) {
            // Get the current user roles.
            $old_roles = (array) $old_user_data->roles;

            // Sanitize the posted roles.
            $new_roles = array_map( 'members_sanitize_role', $_POST[ 'members_user_roles' ] );

            sort( $old_roles );
            sort( $new_roles );
            $old_roles_str = implode( '', $old_roles );
            $new_roles_str = implode( '', $new_roles );
            if ( ! empty( $old_roles ) && $old_roles_str !== $new_roles_str ) {
                $notifications = $this->notifier->get_notifications( 'user-role' );
                foreach ( $notifications as $notification ) {

                    /**
                     * Trigger User Role Changed - For User notification.
                     *
                     * @since 1.6.5
                     */
                    if ( apply_filters( 'bnfw_trigger_user-role-added_notification', true, $notification, $new_roles, $old_roles ) ) {
                        $this->engine->send_user_role_added_email(
                        $this->notifier->read_settings( $notification->ID ),
                                                        $user_id,
                                                        $old_roles,
                                                        $new_roles
                        );
                    }
                }

                $notifications = $this->notifier->get_notifications( 'admin-role' );
                foreach ( $notifications as $notification ) {

                    /**
                     * Trigger User Role Changed - For User notification.
                     *
                     * @since 1.6.5
                     */
                    if ( apply_filters( 'bnfw_trigger_user-role-added_notification', true, $notification, $new_roles, $old_roles ) ) {
                        $setting            = $this->notifier->read_settings( $notification->ID );
                        $setting[ 'message' ] = $this->engine->handle_user_added_role_shortcodes( $setting[ 'message' ], $old_roles, $new_roles );
                        $setting[ 'subject' ] = $this->engine->handle_user_added_role_shortcodes( $setting[ 'subject' ], $old_roles, $new_roles );

                        $this->engine->send_notification( $setting, $user_id );
                    }
                }
            }
        }
    }

    /**
     * Sanitizes a role name.  This is a wrapper for the `sanitize_key()` WordPress function.  Only
     * alphanumeric characters and underscores are allowed.  Hyphens are also replaced with underscores.
     *
     * @since  1.0.0
     * @access public
     * @return int
     */
    function members_sanitize_role( $role ) {

        $_role = strtolower( $role );
        $_role = preg_replace( '/[^a-z0-9_\-\s]/', '', $_role );

        return apply_filters( 'members_sanitize_role', str_replace( ' ', '_', $_role ), $role );
    }

    /**
     * Send notification based on type and ref id
     *
     * @since 1.0
     * @param string $type Notification type.
     * @param mixed $ref_id Reference data.
     */
    public function send_notification( $type, $ref_id, $include_disabled = true ) {
        $notifications = $this->notifier->get_notifications( $type , $include_disabled);
        foreach ( $notifications as $notification ) {
            $this->engine->send_notification( $this->notifier->read_settings( $notification->ID ), $ref_id );
        }
    }

    /**
     * Send notification async based on type and ref id.
     *
     * @param  string  $type   Notification type.
     * @param mixed $ref_id Reference data.
     */
    public function send_notification_async( $type, $ref_id ) {
        $notifications = $this->notifier->get_notifications( $type, false );
        foreach ( $notifications as $notification ) {
            $transient = get_transient( 'bnfw-async-notifications' );
            if ( ! is_array( $transient ) ) {
                $transient = array();
            }

            $notification_data = array(
                'ref_id'            => $ref_id,
                'notification_id'   => $notification->ID,
                'notification_type' => $type,
            );

            if ( ! in_array( $notification_data, $transient ) ) {
                $transient[] = $notification_data;
                set_transient( 'bnfw-async-notifications', $transient, 600 );
            }
        }
    }

    /**
     * Can send comment notification or not
     *
     * @since 1.0
     * @param unknown $comment
     * @return unknown
     */
    private function can_send_comment_notification( $comment ) {
        // Returns false if the comment is marked as spam AND admin has enabled suppression of spam
        $suppress_spam = get_option( 'bnfw_suppress_spam' );
        if ( '1' === $suppress_spam && ( 0 === strcmp( $comment->comment_approved, 'spam' ) ) ) {
            return false;
        }
        return true;
    }

    /**
     * Handle user request email content.
     *
     * @param string $content Content.
     * @param array $email_data Email data.
     *
     * @return string Modified content.
     */
    public function handle_user_request_email_content( $content, $email_data ) {
        $field       = 'message';
        $new_content = '';

        switch ( $email_data[ 'description' ] ) {
            case 'Export Personal Data':
                $notification_name = 'ca-export-data';
                $new_content       = $this->handle_user_request_notification( $notification_name, $field, $email_data );
                break;
            case 'Erase Personal Data':
                $notification_name = 'ca-erase-data';
                $new_content       = $this->handle_user_request_notification( $notification_name, $field, $email_data );
                break;
        }

        if ( ! empty( $new_content ) ) {
            return $new_content;
        } else {
            return $content;
        }
    }

    /**
     * Handle user request email subject.
     *
     * @param string $subject    Subject
     * @param string $blogname   Blog name
     * @param array  $email_data Email data.
     *
     * @return string Modified subject.
     */
    public function handle_user_request_email_subject( $subject, $blogname,
                                                       $email_data ) {
        $field       = 'subject';
        $new_subject = '';

        switch ( $email_data[ 'description' ] ) {
            case 'Export Personal Data':
                $notification_name = 'ca-export-data';
                $new_subject       = $this->handle_user_request_notification( $notification_name, $field, $email_data );
                break;
            case 'Erase Personal Data':
                $notification_name = 'ca-erase-data';
                $new_subject       = $this->handle_user_request_notification( $notification_name, $field, $email_data );
                break;
        }
        if ( ! empty( $new_subject ) ) {
            return $new_subject;
        } else {
            return $subject;
        }
    }

    /**
     * Handle user confirmed action email content.
     *
     * @param string $content    Content.
     * @param array  $email_data Email data.
     *
     * @return string Modified content.
     */
    public function handle_user_confirmed_action_email_content( $content,
                                                                $email_data ) {
        $field       = 'message';
        $new_content = '';

        switch ( $email_data[ 'description' ] ) {
            case 'Export Personal Data':
                $notification_name = 'uc-export-data';
                $new_content       = $this->handle_user_confirmed_action_notification( $notification_name, $field, $email_data );
                break;
            case 'Erase Personal Data':
                $notification_name = 'uc-erase-data';
                $new_content       = $this->handle_user_confirmed_action_notification( $notification_name, $field, $email_data );
                break;
        }

        if ( ! empty( $new_content ) ) {
            return $new_content;
        } else {
            return $content;
        }
    }

    /**
     * Handle data exported email content.
     *
     * @param string $content Content.
     * @param int    $request_id
     *
     * @return string Modified content.
     */
    public function handle_data_export_email_content( $content, $request_id,$email_data ) {

        $field             = 'message';
        $notification_name = 'data-export';
        $new_content       = '';

        $notifications = $this->notifier->get_notifications( $notification_name );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting = $this->notifier->read_settings( end( $notifications )->ID );

            $new_content = $this->engine->handle_data_export_email_shortcodes( $setting[ $field ], $setting, $request_id );
            $new_content = $this->engine->handle_global_user_shortcodes( $new_content, $email_data['message_recipient'] );
        }

        if ( ! empty( $new_content ) ) {
            return $new_content;
        } else {
            return $content;
        }
    }

    public function handle_erasure_complete_email_subject( $subject, $sitename,
                                                           $email_data ) {
        return $this->handle_erasure_complete_email_notification( 'subject', $subject, $email_data );
    }

    public function handle_erasure_complete_email_content( $content, $email_data ) {
        if ( isset( $email_data[ 'privacy_policy_url' ] ) ) {
            return $this->handle_erasure_complete_email_notification( 'message', $content, $email_data );
        }

        return $content;
    }

    protected function handle_erasure_complete_email_notification( $field,
                                                                   $content,
                                                                   $email_data ) {
        $notification_name = 'data-erased';
        $new_content       = '';
        $notifications     = $this->notifier->get_notifications( $notification_name );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting     = $this->notifier->read_settings( end( $notifications )->ID );
            $new_content = $this->engine->handle_shortcodes( $setting[ $field ], $notification_name, $email_data );
        }
        if ( ! empty( $new_content ) ) {
            return $new_content;
        } else {
            return $content;
        }
    }

    /**
     * Send notification emails on shutdown.
     */
    public function on_shutdown() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        $transient = get_transient( 'bnfw-async-notifications' );
        if ( is_array( $transient ) ) {
            delete_transient( 'bnfw-async-notifications' );
            foreach ( $transient as $id_pairs ) {
                $this->engine->send_notification( $this->notifier->read_settings( $id_pairs[ 'notification_id' ] ), $id_pairs[ 'ref_id' ] );
            }
        }
    }

    /**
     * Handle user request notification.
     *
     * @param string $notification_name Notification name.
     * @param string $field             Field name.
     * @param array  $email_data        Email data.
     *
     * @return string Content.
     */
    protected function handle_user_request_notification( $notification_name,
                                                         $field, $email_data ) {
        $notifications = $this->notifier->get_notifications( $notification_name );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting = $this->notifier->read_settings( end( $notifications )->ID );

            return $this->engine->handle_user_request_email_shortcodes( $setting[ $field ], $setting, $email_data );
        }

        return '';
    }

    /**
     * Handle user confirmed action notification.
     *
     * @param string $notification_name Notification name.
     * @param string $field             Field name.
     * @param array  $email_data        Email data.
     *
     * @return string Content.
     */
    protected function handle_user_confirmed_action_notification( $notification_name,
                                                                  $field,
                                                                  $email_data ) {
        $notifications = $this->notifier->get_notifications( $notification_name );
        if ( count( $notifications ) > 0 ) {
            // Ideally there should be only one notification for this type.
            // If there are multiple notification then we will read data about only the last one
            $setting = $this->notifier->read_settings( end( $notifications )->ID );

            return $this->engine->handle_user_confirmed_action_email_shortcodes( $setting[ $field ], $setting, $email_data );
        }

        return '';
    }

    /**
     * Is this a metabox request?
     *
     * Block editor sends duplicate requests on post update.
     *
     * @return bool True if metabox request, False otherwise.
     */
    protected function is_metabox_request() {
        return ( isset( $_GET[ 'meta-box-loader' ] ) || isset( $_GET[ 'meta_box' ] ) );
    }


    /**
	 * Check if Gutenberg is active.
     *
	 *
	 * @return bool
	 * @since 1.3
	 */
	public function is_gutenberg_active() {
		$gutenberg    = false;
		$block_editor = false;

		if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
			// Gutenberg is installed and activated.
			$gutenberg = true;
		}

		if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
			// Block editor.
			$block_editor = true;
		}

		if ( ! $gutenberg && ! $block_editor ) {
			return false;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			return true;
		}

		$use_block_editor = ( get_option( 'classic-editor-replace' ) === 'no-replace' );

		return $use_block_editor;
	}

}

/* ------------------------------------------------------------------------ *
 * Fire up the plugin
 * ------------------------------------------------------------------------ */
BNFW::factory();
