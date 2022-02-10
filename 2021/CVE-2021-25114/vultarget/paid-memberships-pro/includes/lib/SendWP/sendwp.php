<?php
/**
 * SendWP Integration
 * 
 * @since 2.4
 */
add_action( 'wp_ajax_paid_memberships_pro_sendwp_remote_install', 'wp_ajax_paid_memberships_pro_sendwp_remote_install_handler' );

/**
* Callback for admin-ajax request to install SendWP
* 
* @uses "wp_ajax_paid_memberships_pro_sendwp_remote_install" action
*/
function wp_ajax_paid_memberships_pro_sendwp_remote_install_handler () {

    // Verify nonce
    // Also check current user can install plugins
    $security = check_ajax_referer('pmpro_sendwp_install_nonce', 'sendwp_nonce', false);
    if ( ! $security ) {
        ob_end_clean();
        echo json_encode( array( 'error' => true, 'debug' => '!security') );
        exit;
    } else if( ! current_user_can('install_plugins') ) {
        ob_end_clean();
        echo json_encode( array( 'error' => true, 'debug' => '!user_capability') );
        exit;
    }

    $all_plugins = get_plugins();
    $is_sendwp_installed = false;
    foreach(get_plugins() as $path => $details ) {
        if(false === strpos($path, '/sendwp.php')) continue;
        $is_sendwp_installed = true;
        activate_plugin( $path );
        break;
    }

    //Display an error message if a connection is activated on the website
    if( $is_sendwp_installed && sendwp_client_connected() ){
        ob_end_clean();
        echo json_encode( array( 'error' => true, 'debug' => 'sendwp_connected') );
        exit;
    }

    if( ! $is_sendwp_installed ) {

        $plugin_slug = 'sendwp';

        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        
        /*
        * Use the WordPress Plugins API to get the plugin download link.
        */
        $api = plugins_api( 'plugin_information', array(
            'slug' => $plugin_slug,
        ) );
        if ( is_wp_error( $api ) ) {
            ob_end_clean();
            echo json_encode( array( 'error' => $api->get_error_message(), 'debug' => $api ) );
            exit;
        }
        
        /*
        * Use the AJAX Upgrader skin to quietly install the plugin.
        */
        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $install = $upgrader->install( $api->download_link );
        if ( is_wp_error( $install ) ) {
            ob_end_clean();
            echo json_encode( array( 'error' => $install->get_error_message(), 'debug' => $api ) );
            exit;
        }
        
        /*
        * Activate the plugin based on the results of the upgrader.
        * @NOTE Assume this works, if the download works - otherwise there is a false positive if the plugin is already installed.
        */
        $activated = activate_plugin( $upgrader->plugin_info() );

    }

    /*
     * Final check to see if SendWP is available.
     */
    if( ! function_exists('sendwp_get_server_url') ) {
        ob_end_clean();
        echo json_encode( array(
            'error' => __( 'Something went wrong. SendWP was not installed correctly.' ),
            'install' => $install,
            ) );
        exit;
    }
    
    echo json_encode( array(
        'partner_id' => 9343,
        'register_url' => esc_url( sendwp_get_server_url() . '_/signup' ),
        'client_name' => esc_attr( sendwp_get_client_name() ),
        'client_secret' => esc_attr( sendwp_get_client_secret() ),
        'client_redirect' => esc_url( sendwp_get_client_redirect() ),
        'client_url' => esc_url( sendwp_get_client_url() ),
    ) );
    exit;
}

/**
 * Disconnect sendWP
 */
function paid_memberships_pro_sendwp_disconnect() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Check nonce same for installation
    $security = check_ajax_referer('pmpro_sendwp_install_nonce', 'sendwp_nonce', false);

    if ( ! $security ) {
        return;
    }

    if ( function_exists( 'sendwp_disconnect_client' ) ) {
        sendwp_disconnect_client();
    }
    deactivate_plugins( 'sendwp/sendwp.php' );
    
    wp_send_json_success();
    exit;
}
add_action( 'wp_ajax_paid_memberships_pro_sendwp_disconnect', 'paid_memberships_pro_sendwp_disconnect' );

/**
 * Enqueue the SendWP JavaScript SDK.
 *
 * @uses admin_enqueue_scripts action
 */
function paid_memberships_pro_admin_enqueue_sendwp_installer() {

    //Register the JavaScript file
    wp_enqueue_script(
        'pmpro_sendwp_installer', 
        //@TODO Make sure this URL is correct
        plugins_url('installer.js', __FILE__)
    );
    // Print a nonce for the JavaScript to send back for verification
    // @todo verify text domain matches that of your plugin.
    wp_localize_script('pmpro_sendwp_installer', 'paid_memberships_pro_sendwp_vars', array(
        'nonce'  =>  wp_create_nonce( 'pmpro_sendwp_install_nonce' ),
        'security_failed_message'    =>  esc_html__( 'Security failed to check pmpro_sendwp_install_nonce', 'paid-memberships-pro'),
        'user_capability_message'    =>  esc_html__( 'Ask an administrator for install_plugins capability', 'paid-memberships-pro'),
        'sendwp_connected_message'    =>  esc_html__( 'SendWP is already connected.', 'paid-memberships-pro'),
        ) 
    );
}
add_action('admin_enqueue_scripts', 'paid_memberships_pro_admin_enqueue_sendwp_installer');