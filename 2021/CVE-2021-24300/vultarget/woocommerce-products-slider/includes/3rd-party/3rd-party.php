<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );



if ( is_plugin_active( 'dokan-lite/dokan.php' ) ) {

    require_once( wcps_plugin_dir . 'includes/3rd-party/dokan-lite/class-metabox-wcps-hook.php');
    require_once( wcps_plugin_dir . 'includes/3rd-party/dokan-lite/functions-layout-element.php');
}

if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {

    require_once( wcps_plugin_dir . 'includes/3rd-party/easy-digital-downloads/class-metabox-wcps-hook.php');
    require_once( wcps_plugin_dir . 'includes/3rd-party/easy-digital-downloads/functions-layout-element.php');
    require_once( wcps_plugin_dir . 'includes/3rd-party/easy-digital-downloads/wcps-slider-hook.php');

}



if ( is_plugin_active( 'wishlist/wishlist.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/wishlist.php');
}


if ( is_plugin_active( 'wish-list-for-woocommerce/wish-list-for-woocommerce.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/wish-list-for-woocommerce.php');
}


//WPClever.net
if ( is_plugin_active( 'woo-smart-wishlist/index.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/woo-smart-wishlist.php');
}

if ( is_plugin_active( 'woo-smart-quick-view/index.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/woo-smart-quick-view.php');
}

if ( is_plugin_active( 'woo-smart-compare/index.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/woo-smart-compare.php');
}

if ( is_plugin_active( 'wpc-countdown-timer/wpc-countdown-timer.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/wpc-countdown-timer.php');
}


if ( is_plugin_active( 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/ti-woocommerce-wishlist.php');
}

//if ( is_plugin_active( 'advanced-product-labels-for-woocommerce/woocommerce-advanced-products-labels.php' ) ) {
//    require_once( wcps_plugin_dir . 'includes/3rd-party/advanced-product-labels-for-woocommerce.php');
//}


//if ( is_plugin_active( 'perfect-woocommerce-brands/perfect-woocommerce-brands.php' ) ) {
//    require_once( wcps_plugin_dir . 'includes/3rd-party/perfect-woocommerce-brands.php');
//}


//yith

if ( is_plugin_active( 'yith-woocommerce-quick-view/init.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/yith-woocommerce-quick-view.php');
}


if ( is_plugin_active( 'yith-woocommerce-wishlist/init.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/yith-woocommerce-wishlist.php');
}

if ( is_plugin_active( 'yith-woocommerce-brands-add-on/init.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/yith-woocommerce-brands-add-on.php');
}

if ( is_plugin_active( 'yith-woocommerce-compare/init.php' ) ) {
    require_once( wcps_plugin_dir . 'includes/3rd-party/yith-woocommerce-compare.php');
}

