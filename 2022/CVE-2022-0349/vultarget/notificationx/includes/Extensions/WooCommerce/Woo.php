<?php
/**
 * WooCommerce Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\WooCommerce;

use NotificationX\Core\Rules;
use NotificationX\Extensions\GlobalFields;

trait Woo {


    public function _init_fields(){
        add_filter('nx_conversion_product_list', [$this, 'products']);
        add_filter('nx_conversion_category_list', [$this, 'categories']);
    }


    public function categories($options){

        $product_categories = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ));

        $category_list = [];

        if( ! is_wp_error( $product_categories ) ) {
            foreach( $product_categories as $product ) {
                $category_list[ $product->slug ] = $product->name;
            }
        }

        $options = GlobalFields::get_instance()->normalize_fields($category_list, 'source', $this->id, $options);
        return $options;
    }

    public function products($options){
        $products = get_posts(array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'numberposts' => -1,
        ));
        $product_list = [];

        if( ! empty( $products ) ) {
            foreach( $products as $product ) {
                $product_list[ $product->ID ] = $product->post_title;
            }
        }
        wp_reset_postdata();

        $options = GlobalFields::get_instance()->normalize_fields($product_list, 'source', $this->id, $options);
        return $options;
    }

}