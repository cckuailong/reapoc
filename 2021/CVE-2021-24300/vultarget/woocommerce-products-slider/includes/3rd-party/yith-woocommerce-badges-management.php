<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

add_filter('wcps_layout_elements', 'wcps_layout_elements_yith_badges');

function wcps_layout_elements_yith_badges($layout_elements){


    $layout_elements['yith_badges'] = array('name' =>__('YITH badges','woocommerce-products-slider'));

    return $layout_elements;

}




add_action('wcps_layout_elements_option_yith_badges','wcps_layout_elements_option_yith_badges');
function wcps_layout_elements_option_yith_badges($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $background_color = isset($element_data['background_color']) ? $element_data['background_color'] : '';
    $padding = isset($element_data['padding']) ? $element_data['padding'] : '';

    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('YITH badges','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[yith_badges]',
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




add_action('wcps_layout_element_yith_badges', 'wcps_layout_element_yith_badges', 10);
function wcps_layout_element_yith_badges($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $product_id = isset($args['product_id']) ? $args['product_id'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $element_class = !empty($element_index) ? 'element-yith_badges element-'.$element_index : 'element-yith_badges';

    wp_enqueue_script( 'yith_wccl_frontend' );
    wp_enqueue_style( 'yith_wccl_frontend' );



}

remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
add_action( 'woocommerce_variable_add_to_cart', array( $this, 'variable_add_to_cart' ), 30 );


add_action('wcps_layout_element_css_yith_badges', 'wcps_layout_element_css_yith_badges', 10);
function wcps_layout_element_css_yith_badges($args){


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

