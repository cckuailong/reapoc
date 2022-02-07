<?php
if ( ! defined('ABSPATH')) exit;  // if direct access





add_filter('wcps_slider_for_args', 'wcps_slider_for_args_dokan');
function wcps_slider_for_args_dokan($args){

    $args['dokan_vendors'] = __('Dokan vendors', 'woocommerce-products-slider');

    return $args;
}

add_filter('wcps_metabox_navs', 'wcps_metabox_navs_dokan');
function wcps_metabox_navs_dokan($tabs){

    global $post;
    $post_id = $post->ID;


    $wcps_options = get_post_meta($post_id,'wcps_options', true);
    $current_tab = isset($wcps_options['current_tab']) ? $wcps_options['current_tab'] : 'layouts';
    $slider_for = !empty($wcps_options['slider_for']) ? $wcps_options['slider_for'] : 'products';

    $tabs[] = array(
        'id' => 'query_dokan_vendors',
        'title' => sprintf(__('%s Query dokan vendors','woocommerce-products-slider'),'<i class="fas fa-qrcode"></i>'),
        'priority' => 3,
        'active' => ($current_tab == 'query_dokan_vendors') ? true : false,
        'data_visible' => 'dokan_vendors',
        'hidden' => ($slider_for == 'products')? true : false || ($slider_for == 'orders')? true : false || ($slider_for == 'categories')? true : false || ($slider_for == 'edd_downloads')? true : false,
    );
    return $tabs;
}


add_action('wcps_metabox_content_query_dokan_vendors', 'wcps_metabox_content_query_dokan_vendors');

if(!function_exists('wcps_metabox_content_query_dokan_vendors')) {
    function wcps_metabox_content_query_dokan_vendors($post_id){

        $settings_tabs_field = new settings_tabs_field();

        $wcps_options = get_post_meta( $post_id, 'wcps_options', true );
        $vendors_query = !empty($wcps_options['dokan_vendors_query']) ? $wcps_options['dokan_vendors_query'] : array();

        $posts_per_page = isset($vendors_query['posts_per_page']) ? $vendors_query['posts_per_page'] : 10;
        $query_order = isset($vendors_query['order']) ? $vendors_query['order'] : 'DESC';
        $query_orderby = isset($vendors_query['orderby']) ? $vendors_query['orderby'] : 'ID';

        $vendors_ids = isset($vendors_query['vendors_ids']) ? $vendors_query['vendors_ids'] : '';

        ?>
        <div class="section">
            <div class="section-title">Query Vendors</div>
            <p class="description section-description">Setup vendors query settings.</p>


            <?php

            $args = array(
                'id'		=> 'posts_per_page',
                'parent'		=> 'wcps_options[dokan_vendors_query]',
                'title'		=> __('Max number of vendors','woocommerce-products-slider'),
                'details'	=> __('Set custom number you want to display maximum number of vendors','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $posts_per_page,
                'default'		=> '10',
                'placeholder'		=> '10',
            );

            $settings_tabs_field->generate_field($args);



            $args = array(
                'id'		=> 'order',
                'parent'		=> 'wcps_options[dokan_vendors_query]',
                'title'		=> __('Query order','woocommerce-products-slider'),
                'details'	=> __('Set query order.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $query_order,
                'default'		=> 'DESC',
                'args'		=> array(
                    'DESC'=>__('Descending','woocommerce-products-slider'),
                    'ASC'=>__('Ascending','woocommerce-products-slider'),
                ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'orderby',
                'parent'		=> 'wcps_options[dokan_vendors_query]',
                'title'		=> __('Query orderby','woocommerce-products-slider'),
                'details'	=> __('Set query orderby.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $query_orderby,
                'default'		=> 'DESC',
                'args'		=> array(
                    'ID'=>__('ID','woocommerce-products-slider'),
                    'display_name'=>__('display name','woocommerce-products-slider'),
                    'user_login'=>__('user login','woocommerce-products-slider'),
                    'user_nicename'=>__('user nicename','woocommerce-products-slider'),


                ),
            );

            $settings_tabs_field->generate_field($args);








            $args = array(
                'id'		=> 'vendors_ids',
                'parent'		=> 'wcps_options[dokan_vendors_query]',
                'title'		=> __('vendor ID\'s','woocommerce-products-slider'),
                'details'	=> __('You can display vendors by ids.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $vendors_ids,
                'default'		=> '',
                'placeholder'		=> '1,4,2',
            );

            $settings_tabs_field->generate_field($args);










            ?>

        </div>

        <?php






    }
}

