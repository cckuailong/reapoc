<?php	


/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access


?>





<div class="wrap">

	<div id="icon-tools" class="icon32"><br></div><?php echo "<h2>".sprintf(__('%s - Data Update', 'post-grid'), post_grid_plugin_name)."</h2>";?>


    <div class=" post-grid-settings">


        <?php

        $default_query_args['post_type'] = array('post_grid');
        $default_query_args['post_status'] = array('any');

        $post_grid_wp_query = new WP_Query($default_query_args);

        if ( $post_grid_wp_query->have_posts() ) :
            ?>
            <ul>
            <?php
            while ( $post_grid_wp_query->have_posts() ) : $post_grid_wp_query->the_post();

                $post_id = get_the_id();
                $post_grid_meta_options = get_post_meta( get_the_ID(), 'post_grid_meta_options', true );
                $categories = $post_grid_meta_options['categories'];
                $meta_query = $post_grid_meta_options['meta_query'];


                $tax_arr = array();

                if(!empty($categories))
                foreach ($categories as $categoriesData){
                    $categoriesArr = explode(',', $categoriesData);

                    $tax_name = isset($categoriesArr[0]) ? $categoriesArr[0] : '';
                    $term_id = isset($categoriesArr[1]) ? $categoriesArr[1] : '';

                    if(!empty($term_id)){
                        $tax_arr[$tax_name]['checked'] = $tax_name;
                        $tax_arr[$tax_name]['terms_relation'] = 'IN';
                        $tax_arr[$tax_name]['terms'][] = $term_id;
                    }



                }



                $post_grid_meta_options['taxonomies'] = $tax_arr;

                if(!empty($meta_query))
                foreach ($meta_query as $index => $meta_queryData){

                    $meta_args[$index] = array_merge(array('arg_type'=>'single'), $meta_queryData);




                }


                $post_grid_meta_options['meta_query'] = $meta_args;

                update_post_meta($post_id, 'post_grid_meta_options', $post_grid_meta_options);

                ?>
                <li><?php echo get_the_title(); ?></li>
            <?php

                //echo  $post_id;
            endwhile;
            ?>
           </ul>
        <?php
        endif;


        $post_grid_info = get_option('post_grid_info');

        $post_grid_info['data_update_status'] =  'done';
        update_option('post_grid_info',$post_grid_info);


        ?>
		

        
    </div>









</div>
