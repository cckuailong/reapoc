<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


add_filter('wcps_metabox_navs', 'wcps_metabox_navs_edd');
function wcps_metabox_navs_edd($tabs){

    global $post;
    $post_id = $post->ID;


    $wcps_options = get_post_meta($post_id,'wcps_options', true);
    $current_tab = isset($wcps_options['current_tab']) ? $wcps_options['current_tab'] : 'layouts';
    $slider_for = !empty($wcps_options['slider_for']) ? $wcps_options['slider_for'] : 'products';

    $tabs[] = array(
        'id' => 'query_edd_downloads',
        'title' => sprintf(__('%s Query edd downloads','woocommerce-products-slider'),'<i class="fas fa-qrcode"></i>'),
        'priority' => 3,
        'active' => ($current_tab == 'query_edd_downloads') ? true : false,
        'data_visible' => 'edd_downloads',
        'hidden' => ($slider_for == 'products')? true : false || ($slider_for == 'orders')? true : false || ($slider_for == 'categories')? true : false || ($slider_for == 'dokan_vendors')? true : false,
    );
    return $tabs;
}



add_filter('wcps_slider_for_args', 'wcps_slider_for_args_edd');
function wcps_slider_for_args_edd($args){

    $args['edd_downloads'] = __('EDD downloads', 'woocommerce-products-slider');

    return $args;
}



add_action('wcps_metabox_content_query_edd_downloads', 'wcps_metabox_content_query_edd_downloads');

if(!function_exists('wcps_metabox_content_query_edd_downloads')) {
    function wcps_metabox_content_query_edd_downloads($post_id){

        $settings_tabs_field = new settings_tabs_field();

        $wcps_options = get_post_meta( $post_id, 'wcps_options', true );
        $downloads_query = !empty($wcps_options['edd_downloads_query']) ? $wcps_options['edd_downloads_query'] : array();

        $posts_per_page = isset($downloads_query['posts_per_page']) ? $downloads_query['posts_per_page'] : 10;
        $query_order = isset($downloads_query['order']) ? $downloads_query['order'] : 'DESC';
        $query_orderby = isset($downloads_query['orderby']) ? $downloads_query['orderby'] : 'ID';

        $taxonomies = !empty($downloads_query['taxonomies']) ? $downloads_query['taxonomies'] : array();
        $taxonomy_relation = !empty($downloads_query['taxonomy_relation']) ? $downloads_query['taxonomy_relation'] : 'OR';

        $post_ids = isset($downloads_query['post_ids']) ? $downloads_query['post_ids'] : '';

        //var_dump($taxonomies);


        ?>
        <div class="section">
            <div class="section-title">Query edd downloads</div>
            <p class="description section-description">Setup edd downloads query settings.</p>


            <?php

            $args = array(
                'id'		=> 'posts_per_page',
                'parent'		=> 'wcps_options[edd_downloads_query]',
                'title'		=> __('Max number of download','woocommerce-products-slider'),
                'details'	=> __('Set custom number you want to display maximum number of download','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $posts_per_page,
                'default'		=> '10',
                'placeholder'		=> '10',
            );

            $settings_tabs_field->generate_field($args);



            $wcps_allowed_taxonomies = apply_filters('wcps_allowed_taxonomies', array('download_category', 'download_tag'));

            ob_start();


            $post_types =  array('download');
            $post_taxonomies =  array();

            $post_taxonomies = get_object_taxonomies( $post_types );

            if(!empty($post_taxonomies)){

                ?>

                <div class="expandable sortable">

                    <?php

                    foreach ($post_taxonomies as $taxonomy ) {

                        $terms = isset($taxonomies[$taxonomy]['terms']) ? $taxonomies[$taxonomy]['terms'] : array();
                        $terms_relation = isset($taxonomies[$taxonomy]['terms_relation']) ? $taxonomies[$taxonomy]['terms_relation'] : 'IN';

                        if(!in_array($taxonomy, $wcps_allowed_taxonomies)) continue;
                        //if($taxonomy != 'product_cat' && $taxonomy != 'product_tag') continue;

                        $the_taxonomy = get_taxonomy($taxonomy);
                        $args=array('orderby' => 'name', 'order' => 'ASC', 'taxonomy' => $taxonomy, 'hide_empty' => false);
                        $categories_all = get_categories($args);



                        ?>
                        <div class="item">
                            <div class="element-title header ">
                                <span class="expand"><i class="fas fa-expand"></i><i class="fas fa-compress"></i></span>
                                <?php
                                if(!empty($terms)):
                                    ?><i class="fas fa-check"></i><?php
                                else:
                                    ?><i class="fas fa-times"></i><?php
                                endif;?>
                                <span class="expand"><?php echo $the_taxonomy->labels->name; ?> - <?php echo $taxonomy; ?></span>

                            </div>
                            <div class="element-options options">

                                <?php
                                $term_list = array();
                                foreach($categories_all as $category_info){
                                    $term_list[$category_info->cat_ID] = $category_info->cat_name.'('.$category_info->count.')';
                                }




                                $args = array(
                                    'id'		=> 'terms',
                                    'parent' => 'wcps_options[edd_downloads_query][taxonomies]['.$taxonomy.']',
                                    'title'		=> __('Select terms','woocommerce-products-slider'),
                                    'details'	=> __('Choose some terms.','woocommerce-products-slider'),
                                    'type'		=> 'select',
                                    'multiple'		=> true,
                                    'value'		=> $terms,
                                    'args'		=> $term_list,
                                    'default'		=> array(),
                                );

                                $settings_tabs_field->generate_field($args);

                                $args = array(
                                    'id'		=> 'terms_relation',
                                    'css_id'		=> $taxonomy.'_terms_relation',
                                    'parent'		=> 'wcps_options[edd_downloads_query][taxonomies]['.$taxonomy.']',
                                    'title'		=> __('Terms relation','woocommerce-products-slider'),
                                    'details'	=> __('Choose term relation.','woocommerce-products-slider'),
                                    'type'		=> 'radio',
                                    'value'		=> $terms_relation,
                                    'default'		=> 'IN',
                                    'args'		=> array(
                                        'IN'=>__('IN','woocommerce-products-slider'),
                                        'NOT IN'=>__('NOT IN','woocommerce-products-slider'),
                                        'AND'=>__('AND','woocommerce-products-slider'),
                                        'EXISTS'=>__('EXISTS','woocommerce-products-slider'),
                                        'NOT EXISTS'=>__('NOT EXISTS','woocommerce-products-slider'),
                                    ),
                                );

                                $settings_tabs_field->generate_field($args, $post_id);


                                ?>

                            </div>
                        </div>
                        <?php






                    }

                    ?>
                </div>
                <?php

            }
            else{
                echo 'No categories found.';
            }



            $html = ob_get_clean();
            $args = array(
                'id' => 'wcps_categories',
                'title' => __('Download taxonomy  & terms', 'woocommerce-products-slider'),
                'details' => __('Choose download taxonomy & terms. click to expand and see there is categories or terms you can select.', 'woocommerce-products-slider'),
                'type' => 'custom_html',
                'html' => $html,
            );
            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'taxonomy_relation',
                'parent'		=> 'wcps_options[edd_downloads_query]',
                'title'		=> __('Taxonomy relation','woocommerce-products-slider'),
                'details'	=> __('Set taxonomy relation.','woocommerce-products-slider'),
                'type'		=> 'radio',
                'value'		=> $taxonomy_relation,
                'default'		=> 'OR',
                'args'		=> array(
                    'OR'=>__('OR','woocommerce-products-slider'),
                    'AND'=>__('AND','woocommerce-products-slider'),
                ),
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'order',
                'parent'		=> 'wcps_options[edd_downloads_query]',
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
                'parent'		=> 'wcps_options[edd_downloads_query]',
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
                'id'		=> 'post_ids',
                'parent'		=> 'wcps_options[edd_downloads_query]',
                'title'		=> __('Download ID\'s','woocommerce-products-slider'),
                'details'	=> __('You can display download by ids.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $post_ids,
                'default'		=> '',
                'placeholder'		=> '1,4,2',
            );

            $settings_tabs_field->generate_field($args);






            ?>

        </div>

        <?php






    }
}

