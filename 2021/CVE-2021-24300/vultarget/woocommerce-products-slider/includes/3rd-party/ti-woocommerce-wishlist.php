<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

add_filter('wcps_layout_elements', 'wcps_layout_elements_ti_wishlist');

function wcps_layout_elements_ti_wishlist($layout_elements){


    $layout_elements['ti_wishlist'] = array('name' =>__('TI Wishlist','woocommerce-products-slider'));

    return $layout_elements;

}




add_action('wcps_layout_elements_option_ti_wishlist','wcps_layout_elements_option_ti_wishlist');
function wcps_layout_elements_option_ti_wishlist($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';



    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('TI Wishlist','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[ti_wishlist]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            ?>

        </div>
    </div>
    <?php

}




add_action('wcps_layout_element_ti_wishlist', 'wcps_layout_element_ti_wishlist', 10);
function wcps_layout_element_ti_wishlist($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $product_id = isset($args['product_id']) ? $args['product_id'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $element_class = !empty($element_index) ? 'element-ti_wishlist element-'.$element_index : 'element-ti_wishlist';



        ?>
        <div class="<?php echo $element_class; ?>"><?php echo do_shortcode('[ti_wishlists_addtowishlist id="'.$product_id.'"]'); ?></div>
        <?php



}




add_action('wcps_layout_element_css_ti_wishlist', 'wcps_layout_element_css_ti_wishlist', 10);
function wcps_layout_element_css_ti_wishlist($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';

    //echo '<pre>'.var_export($layout_id, true).'</pre>';

    ?>
    <style type="text/css">

        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($margin)): ?>
            margin: <?php echo $margin; ?>;
        <?php endif; ?>

        }

    </style>
    <?php
}

