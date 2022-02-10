<?php
/**
 * Global Functions which can be accessible everywhere to enhance the functionality
 * 
 */

 if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Get WooCommerce Subscription Products
 * 
 * @since 1.0
 * @version 1.0
 */
if( !function_exists('mycred_get_subscription_products') ) {
    function mycred_get_subscription_products() {

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );

        $loop = get_posts( $args );

        $subs_prod = array();
        foreach ( $loop as $prod_id ) {
            $product_s = wc_get_product( $prod_id );
            
            if ($product_s->is_type('subscription')) {
                $subs_prod[ $product_s->get_id() ] = $product_s->get_title();
            }
        }

        return $subs_prod;
    }
}

/**
 * Get available subscription plans
 * 
 * @since 1.0
 * @version 1.0
 */
if( !function_exists('mycred_get_subscription_plans') ) {
    function mycred_get_subscription_plans() {

        $args = array(
            'post_type'      => 'mycred-subscription',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );

        $loop = get_posts( $args );

        $subs_prod = array();
        foreach ( $loop as $prod_id ) {
            $subs_prod[ $prod_id] = get_the_title( $prod_id );
        }

        return $subs_prod;
    }
}

/**
 * Get all myCRED Addons
 * 
 * @since 1.0
 * @version 1.0
 */
if( !function_exists('mycred_get_mycred_addons') ) {
    function mycred_get_mycred_addons() {

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );

        $loop = get_posts( $args );

        $subs_prod = array();
        foreach ( $loop as $prod_id ) {
            $product_s = wc_get_product( $prod_id );
            
            if ( !$product_s->is_type('subscription') ) {
                $subs_prod[ $product_s->get_id() ] = $product_s->get_title();
            }
        }

        return $subs_prod;
    }
}

/**
 * Get user membership key
 * 
 * @since 1.0
 * @version 1.0
 */
if( !function_exists('mycred_get_membership_key') ) {
    function mycred_get_membership_key() {

        $membership_key = wp_cache_get('mycred_membership_key');

        if( false === $membership_key ) {
            $membership_key = get_option( 'mycred_membership_key' );
            wp_cache_set( 'mycred_membership_key', $membership_key );
        }

        return $membership_key;
    }
}

/**
 * Get mycred USER ID (mycred.me)
 * 
 * @since 1.0
 * @version 1.0
 */
if( !function_exists('mycred_get_my_id') ) {
    function mycred_get_my_id() {

        if( !empty( mycred_get_membership_key() ) ) {
            $membership_key = mycred_get_membership_key();
            $membership_key = explode( '-', $membership_key );

            return $membership_key[0];
        }
    }
}

/**
 * Get user membership order ID
 * 
 * @since 1.0
 * @version 1.0
 */
if( !function_exists('mycred_get_subscription_order_id') ) {
    function mycred_get_subscription_order_id( $user_id = 0 ) {

        if( empty( $user_id ) ) $user_id = get_current_user_id();

        $customer_subscriptions = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $user_id, // Or $user_id
            'post_type'   => 'shop_subscription', // WC orders post type
            'post_status' => 'wc-active' // Only orders with status "completed"
        ) );

        // Iterating through each post subscription object
        foreach( $customer_subscriptions as $customer_subscription ){
            // The subscription ID
            $subscription_id = $customer_subscription->ID;
        }

        return $subscription_id;
    }
}

/**
 * Get Membership purchase date
 * 
 * @since 1.0
 * @version 1.0
 */
if( !function_exists('mycred_get_subscription_purchase_date') ) {
    function mycred_get_subscription_purchase_date( $user_id = 0 ) {

        if( empty( $user_id ) ) $user_id = get_current_user_id();

        $subscription_id = mycred_get_subscription_order_id( $user_id );
        $subscription = new WC_Subscription( $subscription_id );
        
        return $subscription->get_date('date_created');
    }
}

/**
 * Get membership end date
 * 
 * @since 1.0
 * @version 1.0
 */
if( !function_exists('mycred_get_subscription_end_date') ) {
    function mycred_get_subscription_end_date( $user_id = 0 ) {
            
        if( empty( $user_id ) ) $user_id = get_current_user_id();

        $subscription_id = mycred_get_subscription_order_id( $user_id );
        $subscription = new WC_Subscription( $subscription_id );
        
        return $subscription->get_date('next_payment');
    }
}

/**
 * Get membership end date
 * 
 * @since 1.0
 * @version 1.2
 */
if( !function_exists('mycred_is_membership_active') ) {
    function mycred_is_membership_active() {

        $membership_status = wp_cache_get('mycred_membership_status');

        if( 'yes' == get_transient( 'mycred_is_membership_active' ) && !isset($_GET['mycred-refresh-license'])) {
            // if transient is set return its value, unless user clicks on refresh license

            return true;
            
        }

        if( false === $membership_status ) {

            $user_license_key = mycred_get_membership_key();

            $mycred_version = str_pad( (int) str_replace( '.', '', myCRED_VERSION ), 3, '0' );
            
            $url = rtrim( get_bloginfo( 'url' ), '/' );
            if( $mycred_version >= 188 && !empty( $user_license_key ) &&
                mycred_get_membership_details(true)['plan'][0]['key'] == $user_license_key &&
                in_array( $url, mycred_get_membership_details(true)['sites'][0] )
            ) {
                $membership_status = true;
                
                set_transient( 'mycred_is_membership_active', 'yes' , DAY_IN_SECONDS*7 );
                // setting transient so membership request is not sent to mycred server for next 2 days
            } else {

                set_transient( 'mycred_is_membership_active', 'no' , DAY_IN_SECONDS*7 );
            }
            wp_cache_set( 'mycred_membership_status', $membership_status );
        }

        return $membership_status;
    }
}

/**
 * Get membership details
 * 
 * @since 1.0
 * @version 1.1
 */
if( !function_exists('mycred_get_membership_details') ) {
    function mycred_get_membership_details($force = false) {

        $membership_details = array();
        if (true === $force) {
            $membership_details = mycred_send_req_for_membership_details();
        } else {

            $saved_membership_details = get_option('mycred_membership_details');

            if (empty($saved_membership_details)) {
                $membership_details = mycred_send_req_for_membership_details();
            } else {
                $membership_details = $saved_membership_details;
            }

        }

        return $membership_details;

    }
}

/**
 * Send Request for membership details
 * 
 * @since 1.0
 * @version 1.1
 */
if( !function_exists('mycred_send_req_for_membership_details') ) {
    function mycred_send_req_for_membership_details() {

        $membership_details = wp_cache_get('mycred_membership_details');

        if( false === $membership_details ) {

            $url = 'https://mycred.me/wp-json/membership/v1/member/'.mycred_get_my_id().'?time='.time();
            $data = wp_remote_get( $url );

            if( is_array( $data ) && ! is_wp_error( $data ) && ! empty( $data['response']['code'] ) && $data['response']['code'] == 200 ) {

                $membership_details = json_decode( $data['body'], true );

                $membership_details_to_save = array();
                if(isset($membership_details['addons']) && !empty($membership_details['addons'])) {

                    foreach($membership_details['addons'] as $key => $value) {
                        $membership_details_to_save['addons'][$key]['name'] = $value['name'];
                        $membership_details_to_save['addons'][$key]['slug'] = $value['slug'];
                        $membership_details_to_save['addons'][$key]['folder'] = $value['folder'];
                    }
                    
                }

                if(isset($membership_details['order']) && !empty($membership_details['order'])) {

                    foreach($membership_details['order'] as $key => $value) {
                        $membership_details_to_save['order'][$key]['expire'] = $value['expire'];
                    }
                    
                }
                update_option( 'mycred_membership_details', $membership_details_to_save );

            } else {

                $membership_details = array (
                    "addons" => array(),
                    "sites" => array(),
                    "plan" => array(
                        array (
                            "ID" => "",
                            "title" => "",
                            "key" => "",
                        )
                    ),
                    "order" => array (
                        array ( 
                            "order_id" => NULL,
                            "purchase" => 0,
                            "expire" => 0,
                        )
                    )
                );
            
            }

            wp_cache_set( 'mycred_membership_details', $membership_details );
        }

        return $membership_details;

    }
}


/**
 * Add check for License link to all mycred addons
 * 
 * @since 1.0
 * @version 1.1
 */

if( !function_exists('mycred_refresh_license') ) {
    function mycred_refresh_license($plugin_meta, $slug, $file, $plugin_data  ) {

        $plugin_meta[] = '<a href="'.admin_url( 'plugins.php?mycred-refresh-license='.$slug).'">'.__('Refresh License', 'mycred').'</a>';
        
        return $plugin_meta;

    }

    add_filter( 'mycred_plugin_info', 'mycred_refresh_license', 80, 4 );
} 

if( !function_exists('mycred_send_refresh_license_req') ) {
    function mycred_send_refresh_license_req() {

        if (isset($_GET['mycred-refresh-license'])) {
            mycred_get_membership_details(true);
            
        }

    }

    add_filter( 'pre_current_active_plugins', 'mycred_send_refresh_license_req');
} 