<?php
if ( ! defined('ABSPATH')) exit;  // if direct access




add_action('wcps_slider_main', 'wcps_slider_main_items_edd_downloads', 20);

function wcps_slider_main_items_edd_downloads($args){


    $wcps_id = isset($args['wcps_id']) ? (int) $args['wcps_id'] : 0;
    $wcps_options = get_post_meta( $wcps_id, 'wcps_options', true );
    $slider_for = isset($wcps_options['slider_for']) ? $wcps_options['slider_for'] : 'products';
    if($slider_for != 'edd_downloads') return;

    $item_layout_id = isset($wcps_options['item_layout_id']) ? $wcps_options['item_layout_id'] : wcps_first_wcps_layout();

    if(empty($item_layout_id)){

        ?><i class="far fa-times-circle"></i> Please create a <a target="_blank" href="<?php echo admin_url(); ?>post-new.php?post_type=wcps_layout">layout</a> first. watch this video to learn <a href="https://www.youtube.com/watch?v=_HMHaSjjHdo&list=PL0QP7T2SN94bgierw1J8Qn3sf4mZo7F9f&index=8&t=0s">customize layouts</a>
        <?php

        return;
    }

    $developer_options = isset($wcps_options['developer_options']) ? $wcps_options['developer_options'] : array();

    $edd_downloads_query = isset($wcps_options['edd_downloads_query']) ? $wcps_options['edd_downloads_query'] : array();

    $posts_per_page = isset($edd_downloads_query['posts_per_page']) ? $edd_downloads_query['posts_per_page'] : 10;
    $query_order = isset($edd_downloads_query['order']) ? $edd_downloads_query['order'] : 'DESC';
    $query_orderby = isset($edd_downloads_query['orderby']) ? $edd_downloads_query['orderby'] : 'date';
    $product_ids = isset($edd_downloads_query['post_ids']) ? $edd_downloads_query['post_ids'] : '';

    //if(empty($post_id)) return;
    $query_args = array();

    $query_args['post_type'] = 'download';
    $query_args['post_status'] = 'publish';

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
                do_action('wcps_slider_item_edd_download', $args);

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



add_action('wcps_slider_item_edd_download', 'wcps_slider_item_edd_download', 10);

function wcps_slider_item_edd_download($args){


    $wcps_id = isset($args['wcps_id']) ? $args['wcps_id'] : '';
    $post_id = isset($args['post_id']) ? $args['post_id'] : '';

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







