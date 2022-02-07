<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

add_action('wcps_slider_main', 'wcps_slider_main_ribbon', 10);
function wcps_slider_main_ribbon($args){
    $wcps_id = isset($args['wcps_id']) ? (int) $args['wcps_id'] : 0;
    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $slider_ribbon = isset($wcps_options['ribbon']) ? $wcps_options['ribbon'] : array();

    $ribbon_name = isset($slider_ribbon['ribbon_name']) ? $slider_ribbon['ribbon_name'] : '';
    $ribbon_custom = isset($slider_ribbon['ribbon_custom']) ? $slider_ribbon['ribbon_custom'] : '';
    $ribbon_position = isset($slider_ribbon['position']) ? $slider_ribbon['position'] : '';

    $ribbon_text = isset($slider_ribbon['text']) ? $slider_ribbon['text'] : '';
    $ribbon_background_img = isset($slider_ribbon['background_img']) ? $slider_ribbon['background_img'] : '';
    $ribbon_background_color = isset($slider_ribbon['background_color']) ? $slider_ribbon['background_color'] : '';
    $ribbon_text_color = isset($slider_ribbon['text_color']) ? $slider_ribbon['text_color'] : '';
    $ribbon_width = isset($slider_ribbon['width']) ? $slider_ribbon['width'] : '';
    $ribbon_height = isset($slider_ribbon['height']) ? $slider_ribbon['height'] : '';
    $ribbon_position = isset($slider_ribbon['position']) ? $slider_ribbon['position'] : '';



    if($ribbon_name == 'none'){
        $ribbon_url = '';
    }elseif($ribbon_name == 'custom'){
        $ribbon_url = $ribbon_custom;
    }else{
        $ribbon_url = wcps_plugin_url.'assets/front/images/ribbons/'.$ribbon_name.'.png';
    }



    $ribbon_url = apply_filters( 'wcps_ribbon_img', $ribbon_url );

    //var_dump($slider_ribbon);

    if(!empty($ribbon_url)):
        ?>
        <div class="wcps-ribbon <?php echo $ribbon_position; ?>" ><?php echo $ribbon_text; ?></div>

        <style type="text/css">
            .wcps-ribbon{
                background-color: <?php echo $ribbon_background_color; ?>;
                background-image: url("<?php echo $ribbon_background_img; ?>");
                color: <?php echo $ribbon_text_color; ?>;
                width: <?php echo $ribbon_width; ?>;
                height: <?php echo $ribbon_height; ?>;
                text-align: center;
                text-transform: uppercase;
                background-repeat: no-repeat;
                background-size: 100%;
            }
        </style>
    <?php
    endif;


}



add_action('wcps_slider_main', 'wcps_slider_main_items', 20);

function wcps_slider_main_items($args){


    $wcps_id = isset($args['wcps_id']) ? (int) $args['wcps_id'] : 0;
    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $slider_for = isset($wcps_options['slider_for']) ? $wcps_options['slider_for'] : 'products';
    if($slider_for != 'products') return;

    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();

    if(empty($item_layout_id)){

        ?><i class="far fa-times-circle"></i> Please create a <a target="_blank" href="<?php echo admin_url(); ?>post-new.php?post_type=wcps_layout">layout</a> first. watch this video to learn <a href="https://www.youtube.com/watch?v=_HMHaSjjHdo&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=8&t=0s">customize layouts</a>
        <?php

        return;
    }

    $developer_options = isset($wcps_options['developer_options']) ? $wcps_options['developer_options'] : array();

    $query = isset($wcps_options['query']) ? $wcps_options['query'] : array();

    $posts_per_page = isset($query['posts_per_page']) ? $query['posts_per_page'] : 10;
    $query_order = isset($query['order']) ? $query['order'] : 'DESC';
    $query_orderby = isset($query['orderby']) ? $query['orderby'] : array();
    $query_ordberby_meta_key = isset($query['ordberby_meta_key']) ? $query['ordberby_meta_key'] : '';

    $hide_out_of_stock = isset($query['hide_out_of_stock']) ? $query['hide_out_of_stock'] : 'no_check';
    $product_featured = isset($query['product_featured']) ? $query['product_featured'] : 'no_check';
    $taxonomies = !empty($query['taxonomies']) ? $query['taxonomies'] : array();
    $taxonomy_relation = !empty($query['taxonomy_relation']) ? $query['taxonomy_relation'] : 'OR';


    $on_sale = isset($query['on_sale']) ? $query['on_sale'] : 'no';
    $product_ids = isset($query['product_ids']) ? $query['product_ids'] : '';
    $query_only = isset($query['query_only']) ? $query['query_only'] : 'no_check';

    //if(empty($post_id)) return;
    $query_args = array();

    $tax_query = array();
    $query_orderby_new = array();

    $query_args['post_type'] = 'product';

    //echo '<pre>'.var_export($query_orderby, true).'</pre>';

    if(!empty($query_orderby))
        foreach ($query_orderby as $elementIndex => $argData){
            $arg_order = isset($argData['arg_order']) ? $argData['arg_order'] :'';
            if(!empty($arg_order))
                $query_orderby_new[$elementIndex]  = $arg_order;
        }

    //echo '<pre>'.var_export($query_orderby, true).'</pre>';

    if(!empty($query_orderby_new))
    $query_args['orderby'] = $query_orderby_new;

    if(!empty($query_ordberby_meta_key))
    $query_args['meta_key']         = $query_ordberby_meta_key;

    $query_args['order']  			= $query_order;
    $query_args['posts_per_page'] 	= $posts_per_page;

    foreach($taxonomies as $taxonomy => $taxonomyData){

        $terms = !empty($taxonomyData['terms']) ? $taxonomyData['terms'] : array();
        $terms_relation = !empty($taxonomyData['terms_relation']) ? $taxonomyData['terms_relation'] : 'OR';

        if(!empty($terms)){
            $tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $terms,
                'operator'    => $terms_relation,
            );
        }
    }





    if($hide_out_of_stock == 'yes'){
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field' => 'name',
            'terms' => 'outofstock',
            'operator' => 'NOT IN',
        );
    }


    if($product_featured == 'no'){
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field' => 'name',
            'terms' => 'featured',
            'operator' => 'NOT IN',
        );
    }



    if($on_sale=='no'){
        $wc_get_product_ids_on_sale = wc_get_product_ids_on_sale();
        $query_args['post__not_in'] = $wc_get_product_ids_on_sale;
    }


    if(!empty($product_ids)){

        $product_ids = array_map('intval',explode(',', $product_ids));
        $query_args['post__in'] = $product_ids;
    }


    if($query_only == 'on_sale'){
        $wc_get_product_ids_on_sale = wc_get_product_ids_on_sale();
        $query_args['post__in'] = $wc_get_product_ids_on_sale;

    }elseif($query_only == 'featured'){

        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field' => 'name',
            'terms' => 'featured',
            'operator' => 'IN',
        );

    }elseif($query_only == 'in_stock'){

        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field' => 'name',
            'terms' => 'outofstock',
            'operator' => 'NOT IN',
        );

    }




    //echo '<pre>'.var_export($query_orderby, true).'</pre>';


    if(!empty($tax_query))
    $query_args['tax_query'] = array_merge(array( 'relation' => $taxonomy_relation ), $tax_query );

    $query_args = apply_filters('wcps_slider_query_args', $query_args, $args);

    if(in_array('query_args', $developer_options)){
        echo 'query_args: ############';
        echo '<pre>'.var_export($query_args, true).'</pre>';
    }



    //echo '<pre>'.var_export($query_args, true).'</pre>';
    $wcps_query = new WP_Query($query_args);

    if(in_array('found_posts', $developer_options)){

        echo 'found_posts: ############';
        echo '<pre>'.var_export(((int) $wcps_query->found_posts), true).'</pre>';
    }



    if ( $wcps_query->have_posts() ) :

        $wcps_items_class = apply_filters('wcps_items_wrapper_class', 'wcps-items owl-carousel owl-theme', $args);

        do_action('wcps_slider_before_items', $wcps_query, $args);

        ?>
        <div id="wcps-<?php echo $wcps_id; ?>" class="<?php echo $wcps_items_class; ?>">
            <?php

            $loop_count = 1;
            while ( $wcps_query->have_posts() ) : $wcps_query->the_post();

                $product_id = get_the_id();
                $args['product_id'] = $product_id;
                $args['loop_count'] = $loop_count;

                

                //echo '<pre>'.var_export($product_id, true).'</pre>';
                do_action('wcps_slider_item', $args);

                $loop_count++;
            endwhile;

            wp_reset_query();
            ?>
        </div>

        <?php


        do_action('wcps_slider_after_items', $wcps_query, $args);

        ?>

    <?php
    else:
        do_action('wcps_slider_no_item');
    endif;


}




add_action('wcps_slider_item', 'wcps_slider_item', 10);

function wcps_slider_item($args){

    $wcps_id = isset($args['wcps_id']) ? $args['wcps_id'] : '';

    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();
    $layout_elements_data = get_post_meta( $item_layout_id, 'layout_elements_data', true );

    $wcps_item_class = apply_filters('wcps_slider_item_class', 'item ', $args);


    //echo '<pre>'.var_export($item_layout_id, true).'</pre>';

    ?>
    <div class="<?php echo $wcps_item_class; ?>">
        <div class="elements-wrapper layout-<?php echo $item_layout_id; ?>">
            <?php
            if(!empty($layout_elements_data))
                foreach ($layout_elements_data as $elementGroupIndex => $elementGroupData){

                    if(!empty($elementGroupData))
                        foreach ($elementGroupData as $elementIndex => $elementData){

                            $args['elementData'] = $elementData;
                            $args['element_index'] = $elementGroupIndex;

                            //echo '<pre>'.var_export($elementIndex, true).'</pre>';


                            do_action('wcps_layout_element_'.$elementIndex, $args);
                        }
                }
            ?>
        </div>
    </div>
    <?php

}








add_action('wcps_slider_main', 'wcps_slider_main_items_orders', 20);

function wcps_slider_main_items_orders($args){


    $wcps_id = isset($args['wcps_id']) ? (int) $args['wcps_id'] : 0;
    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $slider_for = isset($wcps_options['slider_for']) ? $wcps_options['slider_for'] : 'products';
    if($slider_for != 'orders') return;

    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();

    if(empty($item_layout_id)){

        ?><i class="far fa-times-circle"></i> Please create a <a target="_blank" href="<?php echo admin_url(); ?>post-new.php?post_type=wcps_layout">layout</a> first. watch this video to learn <a href="https://www.youtube.com/watch?v=_HMHaSjjHdo&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=8&t=0s">customize layouts</a>
        <?php

        return;
    }

    $developer_options = isset($wcps_options['developer_options']) ? $wcps_options['developer_options'] : array();

    $query_orders = isset($wcps_options['query_orders']) ? $wcps_options['query_orders'] : array();

    $posts_per_page = isset($query_orders['posts_per_page']) ? $query_orders['posts_per_page'] : 10;
    $query_order = isset($query_orders['order']) ? $query_orders['order'] : 'DESC';
    $query_orderby = isset($query_orders['orderby']) ? $query_orders['orderby'] : 'date';
    $product_ids = isset($query_orders['post_ids']) ? $query_orders['post_ids'] : '';

    //if(empty($post_id)) return;
    $query_args = array();

    $query_args['post_type'] = 'shop_order';
    $query_args['post_status'] = 'any';

    //echo '<pre>'.var_export($query_orderby, true).'</pre>';



    //echo '<pre>'.var_export($query_orderby, true).'</pre>';

    $query_args['orderby'] = $query_orderby;

    $query_args['order']  			= $query_order;
    $query_args['posts_per_page'] 	= $posts_per_page;




    if(!empty($product_ids)){

        $product_ids = array_map('intval',explode(',', $product_ids));
        $query_args['post__in'] = $product_ids;
    }


    $query_args = apply_filters('wcps_slider_query_args', $query_args, $args);

    if(in_array('query_args', $developer_options)){
        echo 'query_args: ############';
        echo '<pre>'.var_export($query_args, true).'</pre>';
    }



    //echo '<pre>'.var_export($query_args, true).'</pre>';
    $wcps_query = new WP_Query($query_args);

    if(in_array('found_posts', $developer_options)){

        echo 'found_posts: ############';
        echo '<pre>'.var_export(((int) $wcps_query->found_posts), true).'</pre>';
    }



    if ( $wcps_query->have_posts() ) :

        $wcps_items_class = apply_filters('wcps_items_wrapper_class', 'wcps-items owl-carousel owl-theme', $args);

        do_action('wcps_slider_before_items', $wcps_query, $args);

        ?>
        <div id="wcps-<?php echo $wcps_id; ?>" class="<?php echo $wcps_items_class; ?>">
            <?php

            $loop_count = 1;
            while ( $wcps_query->have_posts() ) : $wcps_query->the_post();

                $product_id = get_the_id();
                $args['post_id'] = $product_id;
                $args['loop_count'] = $loop_count;



                //echo '<pre>'.var_export($product_id, true).'</pre>';
                do_action('wcps_slider_item_order', $args);

                $loop_count++;
            endwhile;

            wp_reset_query();
            ?>
        </div>

        <?php


        do_action('wcps_slider_after_items', $wcps_query, $args);

        ?>

    <?php
    else:
        do_action('wcps_slider_no_item');
    endif;


}



add_action('wcps_slider_item_order', 'wcps_slider_item_order', 10);

function wcps_slider_item_order($args){

    $first_term_id = (int) wcps_get_first_category_id();

    $wcps_id = isset($args['wcps_id']) ? $args['wcps_id'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : $first_term_id;

    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();
    $layout_elements_data = get_post_meta( $item_layout_id, 'layout_elements_data', true );

    $wcps_item_class = apply_filters('wcps_slider_item_class', 'item ', $args);

    ?>
    <div class="<?php echo $wcps_item_class; ?>">
        <div class="elements-wrapper layout-<?php echo $item_layout_id; ?>">
            <?php
            if(!empty($layout_elements_data))
                foreach ($layout_elements_data as $elementGroupIndex => $elementGroupData){

                    if(!empty($elementGroupData))
                        foreach ($elementGroupData as $elementIndex => $elementData){

                            $args['elementData'] = $elementData;
                            $args['element_index'] = $elementGroupIndex;

                            //echo '<pre>'.var_export($elementIndex, true).'</pre>';

                            do_action('wcps_layout_element_'.$elementIndex, $args);
                        }
                }
            ?>
        </div>
    </div>
    <?php

}






add_action('wcps_slider_main', 'wcps_slider_main_items_dokan_vendors', 20);

function wcps_slider_main_items_dokan_vendors($args){


    $wcps_id = isset($args['wcps_id']) ? (int) $args['wcps_id'] : 0;
    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $slider_for = isset($wcps_options['slider_for']) ? $wcps_options['slider_for'] : 'products';
    if($slider_for != 'dokan_vendors') return;

    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();

    if(empty($item_layout_id)){

        ?><i class="far fa-times-circle"></i> Please create a <a target="_blank" href="<?php echo admin_url(); ?>post-new.php?post_type=wcps_layout">layout</a> first. watch this video to learn <a href="https://www.youtube.com/watch?v=_HMHaSjjHdo&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=8&t=0s">customize layouts</a>
        <?php

        return;
    }

    $developer_options = isset($wcps_options['developer_options']) ? $wcps_options['developer_options'] : array();

    $dokan_vendors_query = isset($wcps_options['dokan_vendors_query']) ? $wcps_options['dokan_vendors_query'] : array();

    $posts_per_page = isset($dokan_vendors_query['posts_per_page']) ? $dokan_vendors_query['posts_per_page'] : 10;
    $query_order = isset($dokan_vendors_query['order']) ? $dokan_vendors_query['order'] : 'DESC';
    $query_orderby = isset($dokan_vendors_query['orderby']) ? $dokan_vendors_query['orderby'] : 'date';
    $product_ids = isset($dokan_vendors_query['post_ids']) ? $dokan_vendors_query['post_ids'] : '';

    //if(empty($post_id)) return;
    $query_args = array();

    //$query_args['role__in'] 	= array('shop_vendor');
    $query_args['orderby'] = $query_orderby;
    $query_args['order']  			= $query_order;
    $query_args['number'] 	= $posts_per_page;








    $query_args = apply_filters('wcps_slider_query_dokan_vendors_args', $query_args, $args);





    $authors = get_users($query_args);
    //$authors = $wp_user_query->get_results();

    //echo '<pre>'.var_export($authors, true).'</pre>';

    if ( !empty($authors) ) :

        $wcps_items_class = apply_filters('wcps_items_wrapper_class', 'wcps-items owl-carousel owl-theme', $args);


        ?>
        <div id="wcps-<?php echo $wcps_id; ?>" class="<?php echo $wcps_items_class; ?>">
            <?php

            $loop_count = 1;
            foreach ($authors as $author){

               $args['user_id'] = $author->ID;
               $args['loop_count'] = $loop_count;

               //echo '<pre>'.var_export($product_id, true).'</pre>';
               do_action('wcps_slider_item_dokan_vendor', $args);

               $loop_count++;
            }

           ?>
        </div>

    <?php
    else:
        do_action('wcps_slider_no_item');
    endif;


}



add_action('wcps_slider_item_dokan_vendor', 'wcps_slider_item_dokan_vendors', 10);

function wcps_slider_item_dokan_vendors($args){

    $first_user_id = (int) wcps_get_first_dokan_vendor_id();

    $wcps_id = isset($args['wcps_id']) ? $args['wcps_id'] : $first_user_id;
    $user_id = isset($args['user_id']) ? $args['user_id'] : '';

    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();
    $layout_elements_data = get_post_meta( $item_layout_id, 'layout_elements_data', true );

    $wcps_item_class = apply_filters('wcps_slider_item_class', 'item ', $args);

    //echo '<pre>'.var_export($user_id, true).'</pre>';

    ?>
    <div class="<?php echo $wcps_item_class; ?>">
        <div class="elements-wrapper layout-<?php echo $item_layout_id; ?>">
            <?php
            if(!empty($layout_elements_data))
                foreach ($layout_elements_data as $elementGroupIndex => $elementGroupData){

                    if(!empty($elementGroupData))
                        foreach ($elementGroupData as $elementIndex => $elementData){

                            $args['elementData'] = $elementData;
                            $args['element_index'] = $elementGroupIndex;

                            do_action('wcps_layout_element_'.$elementIndex, $args);
                        }
                }
            ?>
        </div>
    </div>
    <?php

}












add_action('wcps_slider_main', 'wcps_slider_main_items_categories', 20);

function wcps_slider_main_items_categories($args){


    $wcps_id = isset($args['wcps_id']) ? (int) $args['wcps_id'] : 0;
    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $slider_for = isset($wcps_options['slider_for']) ? $wcps_options['slider_for'] : 'products';
    if($slider_for != 'categories') return;

    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();

    $query = !empty($wcps_options['query_categories']) ? $wcps_options['query_categories'] : array();
    $taxonomies = !empty($query['taxonomies']) ? $query['taxonomies'] : array();

    if(empty($item_layout_id)){

        ?><i class="far fa-times-circle"></i> Please create a <a target="_blank" href="<?php echo admin_url(); ?>post-new.php?post_type=wcps_layout">layout</a> first. watch this video to learn <a href="https://www.youtube.com/watch?v=_HMHaSjjHdo&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=8&t=0s">customize layouts</a>
        <?php

        return;
    }



    $terms_list = array();
    $loop_count = 0;

    if(!empty($taxonomies) && is_array($taxonomies)):

            $wcps_items_class = apply_filters('wcps_items_wrapper_class', 'wcps-items owl-carousel owl-theme', $args);

            ?>
            <div id="wcps-<?php echo $wcps_id; ?>" class="<?php echo $wcps_items_class; ?>">
            <?php

            foreach ($taxonomies as $taxonomy){
                $terms = isset($taxonomy['terms']) ? $taxonomy['terms'] : array();
                foreach ( $terms as $terms_id){
                    //$terms_list[] =  $terms_id;

                    $args['term_id'] = $terms_id;
                    $args['loop_count'] = $loop_count;

                    do_action('wcps_slider_item_term', $args);

                    $loop_count++;
                }
            }

            ?>
        </div>
        <?php

    else:
        do_action('wcps_slider_no_item');
    endif;


    //echo '<pre>'.var_export($terms_list, true).'</pre>';


}



add_action('wcps_slider_item_term', 'wcps_slider_item_term', 10);

function wcps_slider_item_term($args){

    $first_term_id = (int) wcps_get_first_category_id();

    $wcps_id = isset($args['wcps_id']) ? $args['wcps_id'] : '';
    $term_id = isset($args['term_id']) ? $args['term_id'] : $first_term_id;

    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();
    $layout_elements_data = get_post_meta( $item_layout_id, 'layout_elements_data', true );

    $wcps_item_class = apply_filters('wcps_slider_item_class', 'item ', $args);

    ?>
    <div class="<?php echo $wcps_item_class; ?>">
        <div class="elements-wrapper layout-<?php echo $item_layout_id; ?>">
            <?php
            if(!empty($layout_elements_data))
                foreach ($layout_elements_data as $elementGroupIndex => $elementGroupData){

                    if(!empty($elementGroupData))
                        foreach ($elementGroupData as $elementIndex => $elementData){

                            $args['elementData'] = $elementData;
                            $args['element_index'] = $elementGroupIndex;

                            //echo '<pre>'.var_export($elementIndex, true).'</pre>';

                            do_action('wcps_layout_element_'.$elementIndex, $args);
                        }
                }
            ?>
        </div>
    </div>
    <?php

}















add_filter('wcps_slider_main', 'wcps_slider_main_scripts', 90);

function wcps_slider_main_scripts( $args){

    $wcps_id = isset($args['wcps_id']) ? $args['wcps_id'] : '';

    $wcps_options = get_post_meta($wcps_id, 'wcps_options', true);

    $container = isset($wcps_options['container']) ? $wcps_options['container'] : array();
    $container_background_img_url = isset($container['background_img_url']) ? $container['background_img_url'] : '';
    $container_background_color = isset($container['background_color']) ? $container['background_color'] : '';
    $container_padding = isset($container['padding']) ? $container['padding'] : '';
    $container_margin = isset($container['margin']) ? $container['margin'] : '';

    $item_style = isset($wcps_options['item_style']) ? $wcps_options['item_style'] : array();
    $item_height = isset($wcps_options['item_height']) ? $wcps_options['item_height'] : array();

    $item_height_large = isset($item_height['large']) ? $item_height['large'] : '';
    $item_height_medium = isset($item_height['medium']) ? $item_height['medium'] : '';
    $item_height_small = isset($item_height['small']) ? $item_height['small'] : '';



    $item_padding = isset($item_style['padding']) ? $item_style['padding'] : '';
    $item_margin = isset($item_style['margin']) ? $item_style['margin'] : '10px';
    $item_background_color = isset($item_style['background_color']) ? $item_style['background_color'] : '';
    $item_text_align = isset($item_style['text_align']) ? $item_style['text_align'] : '';

    $slider_option = isset($wcps_options['slider']) ? $wcps_options['slider'] : array();

    $slider_column_large = isset($slider_option['column_large']) ? $slider_option['column_large'] : 3;
    $slider_column_medium = isset($slider_option['column_medium']) ? $slider_option['column_medium'] : 2;
    $slider_column_small = isset($slider_option['column_small']) ? $slider_option['column_small'] : 1;

    $slider_slideby_large = isset($slider_option['slideby_large']) ? $slider_option['slideby_large'] : 3;
    $slider_slideby_medium = isset($slider_option['slideby_medium']) ? $slider_option['slideby_medium'] : 2;
    $slider_slideby_small = isset($slider_option['slideby_small']) ? $slider_option['slideby_small'] : 1;



    $slider_slide_speed = isset($slider_option['slide_speed']) ? $slider_option['slide_speed'] : 1000;
    $slider_pagination_speed = isset($slider_option['pagination_speed']) ? $slider_option['pagination_speed'] : 1200;

    $slider_auto_play = isset($slider_option['auto_play']) ? $slider_option['auto_play'] : 'true';
    $auto_play_speed = !empty($slider_option['auto_play_speed']) ? $slider_option['auto_play_speed'] : 1000;
    $auto_play_timeout = !empty($slider_option['auto_play_timeout']) ? $slider_option['auto_play_timeout'] : 1200;

    $auto_play_timeout = ($auto_play_speed >= $auto_play_timeout) ? $auto_play_speed + 1000 : $auto_play_timeout;

    $slider_rewind = !empty($slider_option['rewind']) ? $slider_option['rewind'] : 'true';
    $slider_loop = !empty($slider_option['loop']) ? $slider_option['loop'] : 'true';
    $slider_center = !empty($slider_option['center']) ? $slider_option['center'] : 'true';
    $slider_stop_on_hover = isset($slider_option['stop_on_hover']) ? $slider_option['stop_on_hover'] : 'true';
    $slider_navigation = isset($slider_option['navigation']) ? $slider_option['navigation'] : 'true';
    $navigation_position = isset($slider_option['navigation_position']) ? $slider_option['navigation_position'] : '';
    $navigation_background_color = isset($slider_option['navigation_background_color']) ? $slider_option['navigation_background_color'] : '';
    $navigation_color = isset($slider_option['navigation_color']) ? $slider_option['navigation_color'] : '';
    $navigation_style = isset($slider_option['navigation_style']) ? $slider_option['navigation_style'] : 'flat';

    $dots_background_color = isset($slider_option['dots_background_color']) ? $slider_option['dots_background_color'] : '';
    $dots_active_background_color = isset($slider_option['dots_active_background_color']) ? $slider_option['dots_active_background_color'] : '';

    $slider_pagination = isset($slider_option['pagination']) ? $slider_option['pagination'] : 'true';
    $slider_pagination_count = isset($slider_option['pagination_count']) ? $slider_option['pagination_count'] : 'false';
    $slider_rtl = !empty($slider_option['rtl']) ? $slider_option['rtl'] : 'false';
    $slider_lazy_load = isset($slider_option['lazy_load']) ? $slider_option['lazy_load'] : 'true';
    $slider_mouse_drag = isset($slider_option['mouse_drag']) ? $slider_option['mouse_drag'] : 'true';
    $slider_touch_drag = isset($slider_option['touch_drag']) ? $slider_option['touch_drag'] : 'true';

    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : '';
    $layout_elements_data = get_post_meta( $item_layout_id, 'layout_elements_data', true );
    $args['layout_id'] = $item_layout_id;


    $wcps_settings = get_option( 'wcps_settings' );
    $font_aw_version = isset($wcps_settings['font_aw_version']) ? $wcps_settings['font_aw_version'] : 'none';

    if($font_aw_version == 'v_5'){
        $navigation_text_prev = '<i class="fas fa-chevron-left"></i>';
        $navigation_text_next = '<i class="fas fa-chevron-right"></i>';
    }elseif ($font_aw_version == 'v_4'){
        $navigation_text_prev = '<i class="fa fa-chevron-left"></i>';
        $navigation_text_next = '<i class="fa fa-chevron-right"></i>';
    }else{
        $navigation_text_prev = '<i class="fas fa-chevron-left"></i>';
        $navigation_text_next = '<i class="fas fa-chevron-right"></i>';
    }


    $navigation_text_prev = !empty($slider_option['navigation_text']['prev']) ? $slider_option['navigation_text']['prev'] : $navigation_text_prev;
    $navigation_text_next = !empty($slider_option['navigation_text']['next']) ? $slider_option['navigation_text']['next'] : $navigation_text_next;

    //var_dump($slider_navigation);

    ?>
        <script>
            jQuery(document).ready(function($){
                $("#wcps-<?php echo $wcps_id; ?>").owlCarousel({
                    items : <?php echo $slider_column_large; ?>, //10 items above 1000px browser width
                    autoHeight:false,
                    responsiveClass:true,
                    responsive:{
                        0:{
                            items:<?php echo $slider_column_small; ?>,
                            slideBy:<?php echo $slider_slideby_small; ?>,
                            nav:<?php echo $slider_navigation; ?>,

                        },
                        600:{
                            items:<?php echo $slider_column_medium; ?>,
                            slideBy:<?php echo $slider_slideby_medium; ?>,
                            nav:<?php echo $slider_navigation; ?>,
                        },
                        900:{
                            items:<?php echo $slider_column_medium; ?>,
                            slideBy:<?php echo $slider_slideby_medium; ?>,
                            nav:<?php echo $slider_navigation; ?>,
                        },
                        1000:{
                            items:<?php echo $slider_column_large; ?>,
                            slideBy:<?php echo $slider_slideby_large; ?>,
                            nav:<?php echo $slider_navigation; ?>,
                        }
                    },
                    autoplay:<?php echo $slider_auto_play; ?>,
                    autoplaySpeed:<?php echo $auto_play_speed; ?>,
                    autoplayTimeout:<?php echo $auto_play_timeout; ?>,
                    autoplayHoverPause:<?php echo $slider_stop_on_hover; ?>,
                    loop:<?php echo $slider_loop; ?>,
                    rewind:<?php echo $slider_rewind; ?>,
                    center:<?php echo $slider_center; ?>,
                    rtl:<?php echo $slider_rtl; ?>,
                    navContainerClass: 'owl-nav <?php echo $navigation_position; ?> <?php echo $navigation_style; ?>',
                    nav:<?php echo $slider_navigation; ?>,
                    navText : ['<?php echo $navigation_text_prev; ?>','<?php echo $navigation_text_next; ?>'],
                    navSpeed:<?php echo $slider_slide_speed; ?>,
                    slideBy:<?php echo $slider_slideby_large; ?>,
                    dots:<?php echo $slider_pagination; ?>,
                    dotsSpeed:<?php echo $slider_pagination_speed; ?>,
                    mouseDrag:<?php echo $slider_mouse_drag; ?>,
                    touchDrag:<?php echo $slider_touch_drag; ?>,
                    lazyLoad:<?php echo $slider_lazy_load; ?>,
                });
                $(document).on('change', '#wcps-<?php echo $wcps_id; ?> .wcps-items-cart .quantity', function(){
                    quantity = $(this).val();
                    console.log(quantity);
                    $(this).next().attr('data-quantity', quantity);
                })
            });
        </script>

        <style type="text/css">
            .wcps-container-<?php echo $wcps_id; ?>{
            <?php if(!empty($container_padding)): ?>
                padding: <?php echo $container_padding; ?>;
            <?php endif; ?>
            <?php if(!empty($container_margin)): ?>
                margin: <?php echo $container_margin; ?>;
            <?php endif; ?>
            <?php if(!empty($container_background_color)): ?>
                background-color: <?php echo $container_background_color; ?>;
            <?php endif; ?>
            <?php if(!empty($container_background_img_url)): ?>
                background-image: url(<?php echo $container_background_img_url; ?>) repeat scroll 0 0;
            <?php endif; ?>

                position: relative;
                overflow: hidden;
            }
            /*ribbon position*/
            .wcps-container-<?php echo $wcps_id; ?> .wcps-ribbon.topright{
                position: absolute;
                right: -25px;
                top: 15px;
                box-shadow: 0 2px 4px -1px rgb(51, 51, 51);
                transform: rotate(45deg);
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .wcps-ribbon.topleft{
                position: absolute;
                left: -25px;
                top: 15px;
                box-shadow: 0 2px 4px -1px rgb(51, 51, 51);
                transform: rotate(-45deg);
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .wcps-ribbon.bottomleft{
                position: absolute;
                left: -25px;
                bottom: 10px;
                box-shadow: 0 2px 4px -1px rgb(51, 51, 51);
                transform: rotate(45deg);
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .wcps-ribbon.bottomright{
                position: absolute;
                right: -24px;
                bottom: 10px;
                box-shadow: 0 2px 4px -1px rgb(51, 51, 51);
                transform: rotate(-45deg);
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .wcps-ribbon.none{
                display: none;
            }
            .wcps-container-<?php echo $wcps_id; ?> .item {
            <?php if(!empty($item_padding)): ?>
                padding: <?php echo $item_padding; ?>;
            <?php endif; ?>
            <?php if(!empty($item_margin)): ?>
                margin: <?php echo $item_margin; ?>;
            <?php endif; ?>
            <?php if(!empty($item_background_color)): ?>
                background: <?php echo $item_background_color; ?>;
            <?php endif; ?>
            <?php if(!empty($item_text_align)): ?>
                text-align: <?php echo $item_text_align; ?>;
            <?php endif; ?>

            }
            @media only screen and ( min-width: 0px ) and ( max-width: 767px ) {
                .wcps-container-<?php echo $wcps_id; ?> .item {
                <?php if(!empty($item_height_small)): ?>
                    height: <?php echo $item_height_small; ?>;
                <?php endif; ?>
                }
            }
            @media only screen and ( min-width: 768px ) and ( max-width: 1023px ) {
                .wcps-container-<?php echo $wcps_id; ?> .item {
                <?php if(!empty($item_height_medium)): ?>
                    height: <?php echo $item_height_medium; ?>;
                <?php endif; ?>

                }
            }
            @media only screen and (min-width: 1024px ){
                .wcps-container-<?php echo $wcps_id; ?> .item {
                <?php if(!empty($item_height_large)): ?>
                    height: <?php echo $item_height_large; ?>;
                <?php endif; ?>

                }
            }



            #wcps-<?php echo $wcps_id; ?> .wcps-items{
                padding-top:45px;
            }
            .wcps-container-<?php echo $wcps_id; ?> .on-sale{}
            .wcps-container-<?php echo $wcps_id; ?> .on-sale img{
                width: 30px;
                height: auto;
                box-shadow: none;
                display: inline-block;
                vertical-align: middle;
            }
            .wcps-container-<?php echo $wcps_id; ?> .on-sale.topright{
                position: absolute;
                right: 20px;
                top: 15px;
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .on-sale.topleft{
                position: absolute;
                left: 20px;
                top: 15px;
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .on-sale.bottomleft{
                position: absolute;
                left: 20px;
                bottom: 10px;
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .on-sale.bottomright{
                position: absolute;
                right: 20px;
                bottom: 10px;
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .featured-mark img{
                width: 30px;
                height: auto;
                box-shadow: none;
                display: inline-block;
                vertical-align: middle;
            }
            .wcps-container-<?php echo $wcps_id; ?> .featured-mark.topright{
                position: absolute;
                right: 20px;
                top: 15px;
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .featured-mark.topleft{
                position: absolute;
                left: 20px;
                top: 15px;
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .featured-mark.bottomleft{
                position: absolute;
                left: 20px;
                bottom: 10px;
                z-index: 10;
            }
            .wcps-container-<?php echo $wcps_id; ?> .featured-mark.bottomright{
                position: absolute;
                right: 20px;
                bottom: 10px;
                z-index: 10;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-dots{
                text-align: center;
                width: 100%;
                margin: 30px 0 0;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-dots .owl-dot{
            <?php if(!empty($dots_background_color)): ?>
                background: <?php echo $dots_background_color; ?>;
            <?php endif; ?>
                border-radius: 20px;
                display: inline-block;
                height: 15px;
                margin: 5px 7px;
                width: 15px;
                outline: none;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-dots .owl-dot.active, #wcps-<?php echo $wcps_id; ?> .owl-dots .owl-dot:hover{
            <?php if(!empty($dots_active_background_color)): ?>
                background: <?php echo $dots_active_background_color; ?>;
            <?php endif; ?>
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav button{
            <?php if(!empty($navigation_background_color)): ?>
                background: <?php echo $navigation_background_color; ?>;
            <?php endif; ?>
            <?php if(!empty($navigation_color)): ?>
                color: <?php echo $navigation_color; ?>;
            <?php endif; ?>
                margin: 0 5px;
                outline: none;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.topright{
                position: absolute;
                right: 15px;
                top: 15px;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.topleft{
                position: absolute;
                left: 15px;
                top: 15px;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.bottomleft{
                position: absolute;
                left: 15px;
                bottom: 15px;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.bottomright{
                position: absolute;
                right: 15px;
                bottom: 15px;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.middle-fixed{
                position: absolute;
                top: 50%;
                transform: translate(0, -50%);
                width: 100%;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.middle-fixed  .owl-next{
                float: right;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.middle-fixed .owl-prev{
                float: left;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.middle{
                position: absolute;
                top: 50%;
                transform: translate(0, -50%);
                width: 100%;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.middle  .owl-next{
                float: right;
                right: -20%;
                position: absolute;
                transition: all ease 1s 0s;
            }
            #wcps-<?php echo $wcps_id; ?>:hover .owl-nav.middle  .owl-next{
                right: 0;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.middle  .owl-prev{
                left: -20%;
                position: absolute;
                transition: all ease 1s 0s;
            }
            #wcps-<?php echo $wcps_id; ?>:hover .owl-nav.middle  .owl-prev{
                left: 0;
                position: absolute;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.flat button{
                padding: 5px 20px;
                border-radius: 0;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.border button{
                padding: 5px 20px;
                border: 2px solid #777;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.semi-round button{
                padding: 5px 20px;
                border-radius: 8px;
            }
            #wcps-<?php echo $wcps_id; ?> .owl-nav.round button{
                border-radius: 50px;
                width: 50px;
                height: 50px;
            }
            #wcps-<?php echo $wcps_id; ?> .quantity{
                width: 45px;
            }
            <?php

            $custom_css = isset($wcps_options['custom_css']) ? $wcps_options['custom_css'] : '';
            echo str_replace('__ID__', $wcps_id, $custom_css);

            $custom_scripts = get_post_meta($item_layout_id,'custom_scripts', true);
            $layout_custom_css = isset($custom_scripts['custom_css']) ? $custom_scripts['custom_css'] : '';

            echo str_replace('__ID__', 'layout-'.$item_layout_id, $layout_custom_css);

            ?>
        </style>
    <?php
    if(!empty($layout_elements_data))
        foreach ($layout_elements_data as $elementGroupIndex => $elementGroupData){

            if(!empty($elementGroupData))
                foreach ($elementGroupData as $elementIndex => $elementData){
                    $args['elementData'] = $elementData;
                    $args['element_index'] = $elementGroupIndex;
                    do_action('wcps_layout_element_css_'.$elementIndex, $args);
                }
        }
}




add_filter('wcps_slider_main', 'wcps_slider_main_enqueue_scripts', 99);

function wcps_slider_main_enqueue_scripts( $args){

    $wcps_settings = get_option( 'wcps_settings' );

    $font_aw_version = !empty($wcps_settings['font_aw_version']) ? $wcps_settings['font_aw_version'] : 'v_5';



    wp_enqueue_script('owl.carousel');
    wp_enqueue_style('owl.carousel');

    //wp_enqueue_style('owl.carousel');
    //wp_enqueue_script('owl.carousel');

    if($font_aw_version == 'v_5'){
        wp_enqueue_style('font-awesome-5');
    }elseif ($font_aw_version == 'v_4'){
        wp_enqueue_style('font-awesome-4');
    }
}





