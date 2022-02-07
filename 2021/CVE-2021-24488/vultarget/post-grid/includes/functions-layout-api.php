<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


add_shortcode('post_grid_ajax_fetch_block_hub_by_id', 'post_grid_ajax_fetch_block_hub_by_id');

/*
 * Ajax Function to fetch block from http://post_grid.com/ server
 *
 * */
function post_grid_ajax_fetch_block_hub_by_id(){



    check_ajax_referer( 'post_grid_ajax_nonce', 'post_grid_ajax_nonce' );

    if(!current_user_can('manage_options')) return;


    $responses = array();

    $post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : 51616;

    $post_grid_settings = get_option('post_grid_license');
    $license_key = isset($post_grid_settings['license_key']) ? $post_grid_settings['license_key'] : '';

    $api_params = array(
        'post_grid_remote_action' => 'layoutSearchByID',
        'post_id' => $post_id,
        'license_key' => $license_key,

    );




    // Send query to the license manager server
    $server_response = wp_remote_get(add_query_arg($api_params, post_grid_server_url), array('timeout' => 20, 'sslverify' => false));


    /*
     * Check is there any server error occurred
     *
     * */
    if (is_wp_error($server_response)){
        $responses['error'] = __('There is a server error', 'post-grid');
    }
    else{

        $response_data = json_decode(wp_remote_retrieve_body($server_response), true);

        //$response_data = json_decode($response_data);
        //error_log(serialize($response_data));





        $post_title = isset($response_data['post_title']) ? ($response_data['post_title']) : '';

        $layout_elements_data = isset($response_data['layout_elements_data']) ? ($response_data['layout_elements_data']) : array();
        $custom_scripts = isset($response_data['custom_scripts']) ? ($response_data['custom_scripts']) : array();
        $layout_options = isset($response_data['layout_options']) ? ($response_data['layout_options']) : array();
        $post_found = isset($response_data['post_found']) ? ($response_data['post_found']) : 'no';

        //echo '<pre>'.var_export($post_found, true).'</pre>';

        //error_log(serialize($post_title));


//        $post_found = isset($response_data->post_found) ? sanitize_text_field($response_data->post_found) : 'no';

        if($post_found == 'yes'){
            // Create post object
            $my_post = array(
                'post_title'    => $post_title,
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_type' => 'post_grid_layout',
            );
//
//        // Insert the post into the database
            $new_post_id = wp_insert_post( $my_post );
//
            update_post_meta($new_post_id, 'layout_options', $layout_options);
            update_post_meta($new_post_id, 'layout_elements_data', $layout_elements_data);
            update_post_meta($new_post_id, 'custom_scripts', $custom_scripts);


            $responses['is_saved'] = 'yes';
            //$responses['post_title'] = $post_title;
            $responses['post_id'] = $post_id;
            $responses['response_data'] = $response_data;
        }else{
            $responses['is_saved'] = 'no';
        }






    }


    echo json_encode( $responses );
    die();
}


add_action('wp_ajax_post_grid_ajax_fetch_block_hub_by_id', 'post_grid_ajax_fetch_block_hub_by_id');
//add_action('wp_ajax_nopriv_post_grid_ajax_fetch_block_hub_by_id', 'post_grid_ajax_fetch_block_hub_by_id');
