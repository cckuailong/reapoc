<?php
/**
 * Settings only for subscribers and contributors.
 *
 *
 */

class WP_User_Avatar_Subscriber
{
    public function __construct()
    {
        add_action('user_edit_form_tag', array($this, 'wpua_add_edit_form_multipart_encoding'));

        add_action('admin_init', function () {
            global $blog_id, $wpdb;
            $wp_user_roles = $wpdb->get_blog_prefix($blog_id) . 'user_roles';
            $user_roles    = get_option($wp_user_roles);

            if (isset($user_roles['subscriber']['capabilities']['edit_posts'])) {
                unset($user_roles['subscriber']['capabilities']['edit_posts']);
                update_option($wp_user_roles, $user_roles);
            }
        });
    }

    /**
     * Allow multipart data in form
     */
    public function wpua_add_edit_form_multipart_encoding()
    {
        echo ' enctype="multipart/form-data"';
    }
}

/**
 * Initialize
 */
function wpua_subscriber_init()
{
    global $wpua_subscriber;
    $wpua_subscriber = new WP_User_Avatar_Subscriber();
}

add_action('init', 'wpua_subscriber_init');
