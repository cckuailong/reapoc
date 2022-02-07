<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


if ( is_plugin_active( 'yet-another-stars-rating/yet-another-stars-rating.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/yet-another-stars-rating/layout-elements.php');
}

if ( is_plugin_active( 'rating-widget/rating-widget.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/rating-widget/layout-elements.php');
}


if ( is_plugin_active( 'yith-woocommerce-wishlist/init.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/yith-woocommerce-wishlist/layout-elements.php');
}



if ( is_plugin_active( 'kk-star-ratings/index.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/kk-star-ratings/layout-elements.php');
}


if ( is_plugin_active( 'rate-my-post/rate-my-post.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/rate-my-post/layout-elements.php');
}


if ( is_plugin_active( 'wp-postratings/wp-postratings.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/wp-postratings/layout-elements.php');
}

if ( is_plugin_active( 'multi-rating/multi-rating.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/multi-rating/layout-elements.php');
}


if ( is_plugin_active( 'likebtn-like-button/likebtn_like_button.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/likebtn-like-button/layout-elements.php');
}

if ( is_plugin_active( 'wp-postviews/wp-postviews.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/wp-postviews/layout-elements.php');
}

if ( is_plugin_active( 'post-views-counter/post-views-counter.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/post-views-counter/layout-elements.php');
}

if ( is_plugin_active( 'page-views-count/page-views-count.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/page-views-count/layout-elements.php');
}

if ( is_plugin_active( 'page-visit-counter/page-visit-counter.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/page-visit-counter/layout-elements.php');
}



if ( is_plugin_active( 'wck-custom-fields-and-custom-post-types-creator/wck.php' ) ){

    require_once( post_grid_plugin_dir . 'includes/layout-elements/wck-custom-fields-and-custom-post-types-creator/layout-elements.php');
}



if ( is_plugin_active( 'wp-job-manager/wp-job-manager.php' ) ){

    require_once( post_grid_plugin_dir . 'includes/layout-elements/wp-job-manager/layout-elements.php');
}


if ( is_plugin_active( 'simple-job-board/simple-job-board.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/simple-job-board/layout-elements.php');
}

if ( is_plugin_active( 'site-reviews/site-reviews.php' ) ) {

    require_once( post_grid_plugin_dir . 'includes/layout-elements/site-reviews/layout-elements.php');
}





