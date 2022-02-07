<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

add_filter('wcps_layout_elements', 'wcps_layout_elements_wishlist');

function wcps_layout_elements_wishlist($layout_elements){


    $layout_elements['wishlist'] = array('name' =>__('Wishlist','woocommerce-products-slider'));

    return $layout_elements;

}




add_action('wcps_layout_elements_option_wishlist','wcps_layout_elements_option_wishlist');
function wcps_layout_elements_option_wishlist($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';


    $label_text = isset($element_data['label_text']) ? $element_data['label_text'] : '';

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

            <span class="expand"><?php echo __('Wishlist','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[wishlist]',
                'title'		=> __('Font size','woocommerce-products-slider'),
                'details'	=> __('Choose font size.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '16px',

            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[wishlist]',
                'title'		=> __('Font family','woocommerce-products-slider'),
                'details'	=> __('Set font family.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_content_color',
                'parent' => $input_name.'[wishlist]',
                'title'		=> __('Color','woocommerce-products-slider'),
                'details'	=> __('Title text color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'background_color',
                'css_id'		=> $element_index.'_background_color',
                'parent' => $input_name.'[wishlist]',
                'title'		=> __('Background color','woocommerce-products-slider'),
                'details'	=> __('Choose background color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $background_color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[wishlist]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'padding',
                'css_id'		=> $element_index.'_padding',
                'parent' => $input_name.'[wishlist]',
                'title'		=> __('Padding','woocommerce-products-slider'),
                'details'	=> __('Set padding.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $padding,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            ?>

        </div>
    </div>
    <?php

}




add_action('wcps_layout_element_wishlist', 'wcps_layout_element_wishlist', 10);
function wcps_layout_element_wishlist($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $product_id = isset($args['product_id']) ? $args['product_id'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $element_class = !empty($element_index) ? 'element-wishlist element-'.$element_index : 'element-wishlist';

    ?>
    <div class="<?php echo $element_class; ?>"><?php echo do_shortcode('[wishlist_button id="'.$product_id.'" ]');; ?></div>
    <?php

}




add_action('wcps_layout_element_css_wishlist', 'wcps_layout_element_css_wishlist', 10);
function wcps_layout_element_css_wishlist($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($elementData['color']) ? $elementData['color'] : '';
    $background_color = isset($elementData['background_color']) ? $elementData['background_color'] : '';

    $font_size = isset($elementData['font_size']) ? $elementData['font_size'] : '';
    $font_family = isset($elementData['font_family']) ? $elementData['font_family'] : '';
    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';
    $text_align = isset($elementData['text_align']) ? $elementData['text_align'] : '';
    $padding = isset($elementData['padding']) ? $elementData['padding'] : '';


    //echo '<pre>'.var_export($layout_id, true).'</pre>';

    ?>
    <style type="text/css">

        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($margin)): ?>
            margin: <?php echo $margin; ?>;
        <?php endif; ?>

        }
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?> a{
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
            text-decoration: none;
        <?php if(!empty($font_size)): ?>
            font-size: <?php echo $font_size; ?>;
        <?php endif; ?>
        <?php if(!empty($font_family)): ?>
            font-family: <?php echo $font_family; ?>;
        <?php endif; ?>
        <?php if(!empty($padding)): ?>
            padding: <?php echo $padding; ?>;
        <?php endif; ?>
        <?php if(!empty($background_color)): ?>
            background-color: <?php echo $background_color; ?>;
        <?php endif; ?>
        <?php if(!empty($text_align)): ?>
            text-align: <?php echo $text_align; ?>;
        <?php endif; ?>
        }
    </style>
    <?php
}

