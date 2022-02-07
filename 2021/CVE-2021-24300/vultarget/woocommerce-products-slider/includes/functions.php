<?php
if ( ! defined('ABSPATH')) exit;  // if direct access



add_filter('the_content','wcps_preview_content');

function wcps_preview_content($content){

    if(is_singular('wcps')){

        $post_id = get_the_id();

        $content .= do_shortcode('[wcps id="'.$post_id.'"]');

    }

    return $content;

}

add_shortcode('wcps_update_title_wcps_layout', 'wcps_update_title_wcps_layout');
function wcps_update_title_wcps_layout(){

    $args = array(
        'post_type' => 'wcps_layout',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    $post_id = '';

    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : $wp_query->the_post();
            $post_id = get_the_id();

            $post_title = get_the_title();
            $post_title_arr = explode('Theme', $post_title);

            //var_dump($post_title_arr);

            $last_part = end($post_title_arr);

            wp_update_post(
                array(
                'ID'           => $post_id,
                'post_title'   => $last_part,
                )
            );


            echo '<br>';
        endwhile;
    else:

    endif;
}








function wcps_first_wcps_layout(){

    $args = array(
        'post_type' => 'wcps_layout',
        'post_status' => 'publish',
        'posts_per_page' => 1,
    );

    $post_id = '';

    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : $wp_query->the_post();

            $post_id = get_the_id();

            return $post_id;
        endwhile;
    else:

    endif;
}


function wcps_get_first_order_id(){

    $args = array(
        'post_type' => 'shop_order',
        'post_status' => 'publish',
        'posts_per_page' => 1,
    );

    $post_id ='';

    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : $wp_query->the_post();
            $product_id = get_the_id();
            return $product_id;
        endwhile;
    else:

    endif;
}

function wcps_get_first_product_id(){

    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 1,
    );

    $post_id ='';

    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : $wp_query->the_post();
            $product_id = get_the_id();
            return $product_id;
        endwhile;
    else:

    endif;
}


function wcps_get_first_post($post_type = 'product'){

    $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 1,
    );

    $post_id ='';

    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : $wp_query->the_post();
            $product_id = get_the_id();
            return $product_id;
        endwhile;
    else:

    endif;
}




function wcps_get_first_category_id($taxonomy = 'product_cat'){



    $terms = get_terms( array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ) );

    return $terms[1]->term_id;


}

function wcps_get_first_dokan_vendor_id(){

    $args = array(
        'role'         => 'shop_vendor',
        'orderby'      => 'registered',
        'order'        => 'DESC',
        'number'       => 1
    );

    $users = get_users( $args );
    $last_user_registered = isset($users[0]) ? $users[0] : '';

    $vendor_id = isset($last_user_registered->ID) ?$last_user_registered->ID : '';

    return $vendor_id;
}

function wcps_featured_product_ids($query_args){

    $query_args['tax_query'][] = array(
        'taxonomy' => 'product_visibility',
        'field' => 'name',
        'terms' => 'featured',
        'operator' => 'IN',
    );

    $query_args['post_type'] = 'product';
    $query_args['post_status'] = 'publish';
    $query_args['posts_per_page'] = -1;


    // var_dump($query_args);
    $wp_query = new WP_Query($query_args);

    $featured_post_ids = wp_list_pluck( $wp_query->posts, 'ID' );
    wp_reset_postdata();

    return $featured_post_ids;

}





function wcps_recently_viewed_products(){


    $viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
    $viewed_products = array_filter( array_map( 'absint', $viewed_products ) );


    return $viewed_products;
}



function wcps_layout_data($layout){

    $layout_data = array();

    ob_start();
    ?>.__ID__ {vertical-align: top;}.__ID__ .layer-media{}.__ID__ .layer-content {padding: 5px 10px;}<?php

    $layout_data['flat']['css'] = ob_get_clean();
    $layout_data['flat']['preview_img'] = 'https://i.imgur.com/wLGFEu1.png';


    ob_start();
    ?>.__ID__ {overflow: hidden;position: relative;vertical-align: top;}.__ID__:hover .layer-media {-webkit-transform: scale(0);transform: scale(0);opacity: 0;-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";}.__ID__ .layer-media {-webkit-transition: all 1s ease 0s;transition: all 1s ease 0s;left: 0;top: 0;width: 100%;}.__ID__:hover .layer-content{opacity: 1;-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";}.__ID__ .layer-content {left: 0;opacity: 0;-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";position: absolute;top: 0;width: 100%;-webkit-transition: all 1s ease 0s;transition: all 1s ease 0s;}<?php

    $layout_data['zoomout']['css'] = ob_get_clean();
    $layout_data['zoomout']['preview_img'] = 'https://i.imgur.com/HmVriCY.gif';




    ob_start();
    ?>.__ID__{}.__ID__ .layer-media {background: rgb(255, 255, 255) none repeat scroll 0 0;border-radius: 50%;overflow: hidden;}.__ID__ .layer-media .thumb {height:240px;}.__ID__ .layer-content{}<?php

    $layout_data['thumbrounded']['css'] = ob_get_clean();
    $layout_data['thumbrounded']['preview_img'] = 'https://i.imgur.com/QlxfXdW.png';


    ob_start();
    ?>.__ID__{ overflow: hidden; position: relative; vertical-align: top; } .__ID__:hover .layer-media{ -webkit-transform: scale(1.5); transform: scale(1.5); opacity: 0; -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; } .__ID__ .layer-media{ -webkit-transition: all 1s ease 0s; transition: all 1s ease 0s; left: 0; top: 0; width: 100%; } .__ID__:hover .layer-content{ opacity: 1; -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)"; } .__ID__ .layer-content{ opacity: 0; -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; position: absolute; top: 0; width: 100%; -webkit-transition: all 1s ease 0s; transition: all 1s ease 0s; }<?php

    $layout_data['zoomin']['css'] = ob_get_clean();
    $layout_data['zoomin']['preview_img'] = 'https://i.imgur.com/v2wqjFi.gif';



    ob_start();
    ?>.__ID__{ overflow: hidden; position: relative; vertical-align: top; } .__ID__:hover .layer-media{ -webkit-transform: rotate(-30deg); transform: rotate(-30deg); opacity: 0; -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; } .__ID__ .layer-media{ -webkit-transition: all 1s ease 0s; transition: all 1s ease 0s; left: 0; top: 0; width: 100%; } .__ID__:hover .layer-content{ opacity: 1; -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)"; } .__ID__ .layer-content{ left: 0; opacity: 0; -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; position: absolute; top: 0; width: 100%; -webkit-transition: all 1s ease 0s; transition: all 1s ease 0s; }<?php

    $layout_data['spinleft']['css'] = ob_get_clean();
    $layout_data['spinleft']['preview_img'] = 'https://i.imgur.com/tBzV6di.gif';

    ob_start();
    ?>.__ID__{ overflow: hidden; position: relative; vertical-align: top; } .__ID__:hover .layer-media{ z-index:9; } .__ID__ .layer-media{ -webkit-transition: all 0.4s ease 0s; transition: all 0.4s ease 0s; left: 0; top: 0; width: 100%; } .__ID__:hover .layer-content{ } .__ID__ .layer-content{ background: rgba(33, 33, 33, 0.63) none repeat scroll 0 0; bottom: 0; color: rgb(255, 255, 255); left: 0; position: absolute; -webkit-transition: all 1s ease 0s; transition: all 1s ease 0s; width: 100%; padding: 10px 0; }<?php

    $layout_data['contentbottom']['css'] = ob_get_clean();
    $layout_data['contentbottom']['preview_img'] = 'https://i.imgur.com/sUfqp9d.png';



    $layout_data = apply_filters('wcps_layout_data', $layout_data);


    return isset($layout_data[$layout]) ? $layout_data[$layout] : array();

}



add_action( 'template_redirect', 'wcps_track_product_view', 20 );


function wcps_track_product_view() {
	
    $wcps_settings = get_option('wcps_settings');
    $track_product_view = isset($wcps_settings['track_product_view']) ? $wcps_settings['track_product_view'] : 'no';

    if($track_product_view=='yes' && is_singular('product')){
        global $post;

        if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) )
            $viewed_products = array();
        else
            $viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );

        if ( ! in_array( $post->ID, $viewed_products ) ) {
            $viewed_products[] = $post->ID;
        }

        if ( sizeof( $viewed_products ) > 15 ) {
            array_shift( $viewed_products );
        }

        // Store for session only
        wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
    }


}




function wcps_add_shortcode_column( $columns ) {
    return array_merge( $columns, 
        array( 'shortcode' => __( 'Shortcode',  'woocommerce-products-slider' ) ) );
}
add_filter( 'manage_wcps_posts_columns' , 'wcps_add_shortcode_column' );


function wcps_posts_shortcode_display( $column, $post_id ) {
    if ($column == 'shortcode'){
		?>
        <input style="background:#bfefff" type="text" onClick="this.select();" value="[wcps <?php echo 'id=&quot;'.$post_id.'&quot;';?>]" /><br />
      <textarea cols="50" rows="1" style="background:#bfefff" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[wcps id='; echo "'".$post_id."']"; echo '"); ?>'; ?></textarea>
        <?php		
		
    }
}
add_action( 'manage_wcps_posts_custom_column' , 'wcps_posts_shortcode_display', 10, 2 );
