<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

add_filter('wcps_layout_elements', 'wcps_layout_elements_edd');
function wcps_layout_elements_edd($elements){

    $elements['edd_post_title'] = array('name' =>__('Download post title','woocommerce-products-slider'));

    $elements['edd_price'] = array('name' =>__('EDD price','woocommerce-products-slider'));
//    $elements['edd_variable_prices'] = array('name' =>__('EDD variable prices','woocommerce-products-slider'));
//    $elements['edd_sales_stats'] = array('name' =>__('EDD sales stats','woocommerce-products-slider'));
//    $elements['edd_earnings_stats'] = array('name' =>__('EDD earnings stats','woocommerce-products-slider'));
    $elements['edd_add_to_cart'] = array('name' =>__('EDD add to cart','woocommerce-products-slider'));
    $elements['edd_categories'] = array('name' =>__('EDD categories','woocommerce-products-slider'));
    $elements['edd_tags'] = array('name' =>__('EDD tags','woocommerce-products-slider'));


    return $elements;
}




add_action('wcps_layout_element_edd_post_title', 'wcps_layout_element_edd_post_title', 10);
function wcps_layout_element_edd_post_title($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $post_id = isset($args['post_id']) ? $args['post_id'] :  wcps_get_first_post('download');
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $link_to = isset($elementData['link_to']) ? $elementData['link_to'] : '';


    $post_data = get_post($post_id);
    $post_title = isset($post_data->post_title) ? $post_data->post_title : '';

    $element_class = !empty($element_index) ? 'element-post-title element-'.$element_index : 'element-post-title';

    if($link_to == 'post_link'):
        $post_title = '<a href="'.get_permalink($post_id).'">'.$post_title.'</a>';
    endif;

    ?>
    <div class="<?php echo $element_class; ?>"><?php echo $post_title; ?></div>
    <?php

}



add_action('wcps_layout_element_edd_add_to_cart', 'wcps_layout_element_edd_add_to_cart', 10);
function wcps_layout_element_edd_add_to_cart($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $post_id = isset($args['post_id']) ? $args['post_id'] :  wcps_get_first_post('download');
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';


    $element_class = !empty($element_index) ? 'element-post-title element-'.$element_index : 'element-post-title';

    $purchase_link = do_shortcode('[purchase_link id="'.$post_id.'" text="'.__('Add to Cart','woocommerce-products-slider').'" style="button"]'  );



    ?>
    <div class="<?php echo $element_class; ?>"><?php echo $purchase_link; ?></div>
    <?php

}




add_action('wcps_layout_element_edd_price', 'wcps_layout_element_edd_price', 10);
function wcps_layout_element_edd_price($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $post_id = isset($args['post_id']) ? $args['post_id'] :  wcps_get_first_post('download');
    $element_data = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-custom_text element-'.$element_index : 'element-custom_text';

    $variable_prices = edd_get_variable_prices( $post_id );
    $edd_prices_html = '';

    if( $variable_prices ) {
        $currency = edd_get_currency();



        $edd_prices_html.= '<ul>';
        foreach( $variable_prices as $price_id => $price ) {
            $edd_prices_html.= '<li>'.$price['name'].': '.$currency.' '.$price['amount'].'</li>';; //is the name of the price

        }
        $edd_prices_html.= '</ul>';

    }else{
        $edd_prices_html = edd_price($post_id,false);

    }

    ?>
    <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $edd_prices_html); ?></div>
    <?php

}



add_action('wcps_layout_element_edd_variable_prices', 'wcps_layout_element_edd_variable_prices', 10);
function wcps_layout_element_edd_variable_prices($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $post_id = isset($args['post_id']) ? $args['post_id'] :  wcps_get_first_post('download');
    $element_data = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-custom_text element-'.$element_index : 'element-custom_text';

    $currency = edd_get_currency();
    $prices = edd_get_variable_prices( $post_id );

    if( $prices ) {
        $edd_variable_prices_html = '';
        $edd_variable_prices_html.= '<ul>';
        foreach( $prices as $price_id => $price ) {
            $edd_variable_prices_html.= '<li>'.$price['name'].': '.$currency.' '.$price['amount'].'</li>';; //is the name of the price

        }
        $edd_variable_prices_html.= '</ul>';

    }

    ?>
    <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $edd_variable_prices_html); ?></div>
    <?php

}







add_action('wcps_layout_element_edd_categories', 'wcps_layout_element_edd_categories', 10);
function wcps_layout_element_edd_categories($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $post_id = isset($args['post_id']) ? $args['post_id'] : wcps_get_first_post('download');
    $element_data = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-custom_text element-'.$element_index : 'element-custom_text';

    $term_list = wp_get_post_terms($post_id, 'download_category', array("fields" => "all"));
    $term_count = count($term_list);


    $edd_categories_html = '';
    $i = 1;
    if( !empty($term_list) ) {
        foreach( $term_list as $term ) {
            $edd_categories_html.= '<a href="#">'.$term->name.'</a>'; //is the name of the price
            if( $i < $term_count){
                $edd_categories_html .= ', ';
            }
            $i++;
        }
    }
    ?>
    <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $edd_categories_html); ?></div>
    <?php

}



add_action('wcps_layout_element_edd_tags', 'wcps_layout_element_edd_tags', 10);
function wcps_layout_element_edd_tags($args){

    //echo '<pre>'.var_export($args, true).'</pre>';
    $post_id = isset($args['post_id']) ? $args['post_id'] :  wcps_get_first_post('download');
    $element_data = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-custom_text element-'.$element_index : 'element-custom_text';

    $term_list = wp_get_post_terms($post_id, 'download_tag', array("fields" => "all"));
    $term_count = count($term_list);


    $edd_categories_html = '';
    $i = 1;
    if( !empty($term_list) ) {
        foreach( $term_list as $term ) {
            $edd_categories_html.= '<a href="#">'.$term->name.'</a>'; //is the name of the price
            if( $i < $term_count){
                $edd_categories_html .= ', ';
            }
            $i++;
        }
    }
    ?>
    <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $edd_categories_html); ?></div>
    <?php

}




add_action('wcps_layout_element_css_edd_post_title', 'wcps_layout_element_css_edd_post_title', 10);
function wcps_layout_element_css_edd_post_title($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($elementData['color']) ? $elementData['color'] : '';
    $font_size = isset($elementData['font_size']) ? $elementData['font_size'] : '';
    $font_family = isset($elementData['font_family']) ? $elementData['font_family'] : '';
    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';
    $text_align = isset($elementData['text_align']) ? $elementData['text_align'] : '';


    //echo '<pre>'.var_export($layout_id, true).'</pre>';

    ?>
    <style type="text/css">
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
        <?php if(!empty($margin)): ?>
            margin: <?php echo $margin; ?>;
        <?php endif; ?>
        <?php if(!empty($text_align)): ?>
            text-align: <?php echo $text_align; ?>;
        <?php endif; ?>
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
        <?php if(!empty($font_size)): ?>
            font-size: <?php echo $font_size; ?>;
        <?php endif; ?>
        <?php if(!empty($font_family)): ?>
            font-family: <?php echo $font_family; ?>;
        <?php endif; ?>
        }
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?> a{
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
        <?php if(!empty($font_size)): ?>
            font-size: <?php echo $font_size; ?>;
        <?php endif; ?>
        <?php if(!empty($font_family)): ?>
            font-family: <?php echo $font_family; ?>;
        <?php endif; ?>
        }


    </style>
    <?php
}



add_action('wcps_layout_element_css_edd_price', 'wcps_layout_element_css_edd_price', 10);
function wcps_layout_element_css_edd_price($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($elementData['color']) ? $elementData['color'] : '';
    $font_size = isset($elementData['font_size']) ? $elementData['font_size'] : '';
    $font_family = isset($elementData['font_family']) ? $elementData['font_family'] : '';
    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';
    $text_align = isset($elementData['text_align']) ? $elementData['text_align'] : '';

    ?>
    <style type="text/css">
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
        <?php if(!empty($font_size)): ?>
            font-size: <?php echo $font_size; ?>;
        <?php endif; ?>
        <?php if(!empty($font_family)): ?>
            font-family: <?php echo $font_family; ?>;
        <?php endif; ?>
        <?php if(!empty($margin)): ?>
            margin: <?php echo $margin; ?>;
        <?php endif; ?>
        <?php if(!empty($text_align)): ?>
            text-align: <?php echo $text_align; ?>;
        <?php endif; ?>
        }
    </style>
    <?php
}


add_action('wcps_layout_element_css_edd_add_to_cart', 'wcps_layout_element_css_edd_add_to_cart', 10);
function wcps_layout_element_css_edd_add_to_cart($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($elementData['color']) ? $elementData['color'] : '';
    $font_size = isset($elementData['font_size']) ? $elementData['font_size'] : '';
    $font_family = isset($elementData['font_family']) ? $elementData['font_family'] : '';
    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';
    $text_align = isset($elementData['text_align']) ? $elementData['text_align'] : '';

    ?>
    <style type="text/css">
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
        <?php if(!empty($font_size)): ?>
            font-size: <?php echo $font_size; ?>;
        <?php endif; ?>
        <?php if(!empty($font_family)): ?>
            font-family: <?php echo $font_family; ?>;
        <?php endif; ?>
        <?php if(!empty($margin)): ?>
            margin: <?php echo $margin; ?>;
        <?php endif; ?>
        <?php if(!empty($text_align)): ?>
            text-align: <?php echo $text_align; ?>;
        <?php endif; ?>
        }
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?> a{
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
        <?php if(!empty($font_size)): ?>
            font-size: <?php echo $font_size; ?>;
        <?php endif; ?>
        <?php if(!empty($font_family)): ?>
            font-family: <?php echo $font_family; ?>;
        <?php endif; ?>
        }
    </style>
    <?php
}





add_action('wcps_layout_element_css_edd_categories', 'wcps_layout_element_css_edd_categories', 10);
function wcps_layout_element_css_edd_categories($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($elementData['color']) ? $elementData['color'] : '';
    $font_size = isset($elementData['font_size']) ? $elementData['font_size'] : '';
    $font_family = isset($elementData['font_family']) ? $elementData['font_family'] : '';
    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';
    $text_align = isset($elementData['text_align']) ? $elementData['text_align'] : '';

    ?>
    <style type="text/css">
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($margin)): ?>
            margin: <?php echo $margin; ?>;
        <?php endif; ?>
        <?php if(!empty($text_align)): ?>
            text-align: <?php echo $text_align; ?>;
        <?php endif; ?>
        }
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?> a{
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
        <?php if(!empty($font_size)): ?>
            font-size: <?php echo $font_size; ?>;
        <?php endif; ?>
        <?php if(!empty($font_family)): ?>
            font-family: <?php echo $font_family; ?>;
        <?php endif; ?>
        }
    </style>
    <?php
}




add_action('wcps_layout_element_css_edd_tags', 'wcps_layout_element_css_edd_tags', 10);
function wcps_layout_element_css_edd_tags($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $color = isset($elementData['color']) ? $elementData['color'] : '';
    $font_size = isset($elementData['font_size']) ? $elementData['font_size'] : '';
    $font_family = isset($elementData['font_family']) ? $elementData['font_family'] : '';
    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';
    $text_align = isset($elementData['text_align']) ? $elementData['text_align'] : '';

    ?>
    <style type="text/css">
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($margin)): ?>
            margin: <?php echo $margin; ?>;
        <?php endif; ?>
        <?php if(!empty($text_align)): ?>
            text-align: <?php echo $text_align; ?>;
        <?php endif; ?>
        }
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?> a{
        <?php if(!empty($color)): ?>
            color: <?php echo $color; ?>;
        <?php endif; ?>
        <?php if(!empty($font_size)): ?>
            font-size: <?php echo $font_size; ?>;
        <?php endif; ?>
        <?php if(!empty($font_family)): ?>
            font-family: <?php echo $font_family; ?>;
        <?php endif; ?>
        }
    </style>
    <?php
}











add_action('wcps_layout_elements_option_edd_post_title','wcps_layout_elements_option_edd_post_title');


function wcps_layout_elements_option_edd_post_title($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';


    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $link_to = isset($element_data['link_to']) ? $element_data['link_to'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $custom_css = isset($element_data['custom_css']) ? $element_data['custom_css'] : '';
    $custom_css_hover = isset($element_data['custom_css_hover']) ? $element_data['custom_css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('Download title','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[edd_post_title]',
                'title'		=> __('Color','woocommerce-products-slider'),
                'details'	=> __('Title text color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[edd_post_title]',
                'title'		=> __('Font size','woocommerce-products-slider'),
                'details'	=> __('Set font size.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[edd_post_title]',
                'title'		=> __('Font family','woocommerce-products-slider'),
                'details'	=> __('Set font family.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[edd_post_title]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[edd_post_title]',
                'title'		=> __('Text align','woocommerce-products-slider'),
                'details'	=> __('Choose text align.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'woocommerce-products-slider'),'right'=> __('Right', 'woocommerce-products-slider'),'center'=> __('Center', 'woocommerce-products-slider') ),
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'link_to',
                'css_id'		=> $element_index.'_link_to',
                'parent' => $input_name.'[edd_post_title]',
                'title'		=> __('Link to','woocommerce-products-slider'),
                'details'	=> __('Choose option to link product.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $link_to,
                'default'		=> 'product_link',
                'args'		=> array(
                    'none'=> __('None', 'woocommerce-products-slider'),
                    'post_link'=> __('Download link', 'woocommerce-products-slider'),
                ),
            );

            $settings_tabs_field->generate_field($args);




            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element-<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','woocommerce-products-slider'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','woocommerce-products-slider'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}





add_action('wcps_layout_elements_option_edd_price','wcps_layout_elements_option_edd_price');


function wcps_layout_elements_option_edd_price($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '%s';

    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $custom_css = isset($element_data['custom_css']) ? $element_data['custom_css'] : '';
    $custom_css_hover = isset($element_data['custom_css_hover']) ? $element_data['custom_css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('EDD price','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[edd_price]',
                'title'		=> __('Wrapper html','woocommerce-products-slider'),
                'details'	=> __('Set font wrapper html. ex: Price %s','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_edd_price',
                'parent' => $input_name.'[edd_price]',
                'title'		=> __('Color','woocommerce-products-slider'),
                'details'	=> __('Title text color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[edd_price]',
                'title'		=> __('Font size','woocommerce-products-slider'),
                'details'	=> __('Set font size.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[edd_price]',
                'title'		=> __('Font family','woocommerce-products-slider'),
                'details'	=> __('Set font family.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[edd_price]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[edd_price]',
                'title'		=> __('Text align','woocommerce-products-slider'),
                'details'	=> __('Choose text align.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'woocommerce-products-slider'),'right'=> __('Right', 'woocommerce-products-slider'),'center'=> __('Center', 'woocommerce-products-slider') ),
            );

            $settings_tabs_field->generate_field($args);




            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element-<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','woocommerce-products-slider'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','woocommerce-products-slider'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('wcps_layout_elements_option_edd_variable_prices','wcps_layout_elements_option_edd_variable_prices');


function wcps_layout_elements_option_edd_variable_prices($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';


    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $custom_css = isset($element_data['custom_css']) ? $element_data['custom_css'] : '';
    $custom_css_hover = isset($element_data['custom_css_hover']) ? $element_data['custom_css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('EDD variable prices','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php


            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[edd_variable_prices]',
                'title'		=> __('Wrapper html','woocommerce-products-slider'),
                'details'	=> __('Set font wrapper html.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_edd_variable_prices',
                'parent' => $input_name.'[edd_variable_prices]',
                'title'		=> __('Color','woocommerce-products-slider'),
                'details'	=> __('Title text color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[edd_variable_prices]',
                'title'		=> __('Font size','woocommerce-products-slider'),
                'details'	=> __('Set font size.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[edd_variable_prices]',
                'title'		=> __('Font family','woocommerce-products-slider'),
                'details'	=> __('Set font family.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[edd_variable_prices]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[edd_variable_prices]',
                'title'		=> __('Text align','woocommerce-products-slider'),
                'details'	=> __('Choose text align.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'woocommerce-products-slider'),'right'=> __('Right', 'woocommerce-products-slider'),'center'=> __('Center', 'woocommerce-products-slider') ),
            );

            $settings_tabs_field->generate_field($args);




            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element-<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','woocommerce-products-slider'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','woocommerce-products-slider'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('wcps_layout_elements_option_edd_sales_stats','wcps_layout_elements_option_edd_sales_stats');


function wcps_layout_elements_option_edd_sales_stats($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';


    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $custom_css = isset($element_data['custom_css']) ? $element_data['custom_css'] : '';
    $custom_css_hover = isset($element_data['custom_css_hover']) ? $element_data['custom_css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('EDD sales stats','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[edd_sales_stats]',
                'title'		=> __('Wrapper html','woocommerce-products-slider'),
                'details'	=> __('Set font wrapper html.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_edd_variable_prices',
                'parent' => $input_name.'[edd_sales_stats]',
                'title'		=> __('Color','woocommerce-products-slider'),
                'details'	=> __('Title text color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[edd_sales_stats]',
                'title'		=> __('Font size','woocommerce-products-slider'),
                'details'	=> __('Set font size.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[edd_sales_stats]',
                'title'		=> __('Font family','woocommerce-products-slider'),
                'details'	=> __('Set font family.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[edd_sales_stats]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[edd_sales_stats]',
                'title'		=> __('Text align','woocommerce-products-slider'),
                'details'	=> __('Choose text align.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'woocommerce-products-slider'),'right'=> __('Right', 'woocommerce-products-slider'),'center'=> __('Center', 'woocommerce-products-slider') ),
            );

            $settings_tabs_field->generate_field($args);




            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element-<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','woocommerce-products-slider'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','woocommerce-products-slider'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('wcps_layout_elements_option_edd_add_to_cart','wcps_layout_elements_option_edd_add_to_cart');


function wcps_layout_elements_option_edd_add_to_cart($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';


    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $custom_css = isset($element_data['custom_css']) ? $element_data['custom_css'] : '';
    $custom_css_hover = isset($element_data['custom_css_hover']) ? $element_data['custom_css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('EDD add to cart','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_edd_add_to_cart',
                'parent' => $input_name.'[edd_add_to_cart]',
                'title'		=> __('Color','woocommerce-products-slider'),
                'details'	=> __('Title text color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[edd_add_to_cart]',
                'title'		=> __('Font size','woocommerce-products-slider'),
                'details'	=> __('Set font size.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[edd_add_to_cart]',
                'title'		=> __('Font family','woocommerce-products-slider'),
                'details'	=> __('Set font family.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[edd_add_to_cart]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[edd_add_to_cart]',
                'title'		=> __('Text align','woocommerce-products-slider'),
                'details'	=> __('Choose text align.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'woocommerce-products-slider'),'right'=> __('Right', 'woocommerce-products-slider'),'center'=> __('Center', 'woocommerce-products-slider') ),
            );

            $settings_tabs_field->generate_field($args);




            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element-<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','woocommerce-products-slider'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','woocommerce-products-slider'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}



add_action('wcps_layout_elements_option_edd_categories','wcps_layout_elements_option_edd_categories');


function wcps_layout_elements_option_edd_categories($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';


    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '%s';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '';

    $custom_css = isset($element_data['custom_css']) ? $element_data['custom_css'] : '';
    $custom_css_hover = isset($element_data['custom_css_hover']) ? $element_data['custom_css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('EDD categories','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php

            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[edd_categories]',
                'title'		=> __('Wrapper html','woocommerce-products-slider'),
                'details'	=> __('Set font wrapper html.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[edd_categories]',
                'title'		=> __('Color','woocommerce-products-slider'),
                'details'	=> __('Title text color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[edd_categories]',
                'title'		=> __('Font size','woocommerce-products-slider'),
                'details'	=> __('Set font size.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[edd_categories]',
                'title'		=> __('Font family','woocommerce-products-slider'),
                'details'	=> __('Set font family.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[edd_categories]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[edd_categories]',
                'title'		=> __('Text align','woocommerce-products-slider'),
                'details'	=> __('Choose text align.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'woocommerce-products-slider'),'right'=> __('Right', 'woocommerce-products-slider'),'center'=> __('Center', 'woocommerce-products-slider') ),
            );

            $settings_tabs_field->generate_field($args);




            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element-<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','woocommerce-products-slider'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','woocommerce-products-slider'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}


add_action('wcps_layout_elements_option_edd_tags','wcps_layout_elements_option_edd_tags');


function wcps_layout_elements_option_edd_tags($parameters){

    $settings_tabs_field = new settings_tabs_field();

    $input_name = isset($parameters['input_name']) ? $parameters['input_name'] : '{input_name}';
    $element_data = isset($parameters['element_data']) ? $parameters['element_data'] : array();
    $element_index = isset($parameters['index']) ? $parameters['index'] : '';


    $color = isset($element_data['color']) ? $element_data['color'] : '';
    $font_size = isset($element_data['font_size']) ? $element_data['font_size'] : '';
    $font_family = isset($element_data['font_family']) ? $element_data['font_family'] : '';
    $margin = isset($element_data['margin']) ? $element_data['margin'] : '';
    $text_align = isset($element_data['text_align']) ? $element_data['text_align'] : '';

    $wrapper_html = isset($element_data['wrapper_html']) ? $element_data['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $custom_css = isset($element_data['custom_css']) ? $element_data['custom_css'] : '';
    $custom_css_hover = isset($element_data['custom_css_hover']) ? $element_data['custom_css_hover'] : '';



    ?>
    <div class="item">
        <div class="element-title header ">
            <span class="remove" onclick="jQuery(this).parent().parent().remove()"><i class="fas fa-times"></i></span>
            <span class="sort"><i class="fas fa-sort"></i></span>

            <span class="expand"><?php echo __('EDD tags','woocommerce-products-slider'); ?></span>
        </div>
        <div class="element-options options">

            <?php


            $args = array(
                'id'		=> 'wrapper_html',
                'css_id'		=> $element_index.'_wrapper_html',
                'parent' => $input_name.'[edd_tags]',
                'title'		=> __('Wrapper html','woocommerce-products-slider'),
                'details'	=> __('Set font wrapper html.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $wrapper_html,
                'default'		=> '',
                'placeholder'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'color',
                'css_id'		=> $element_index.'_color',
                'parent' => $input_name.'[edd_tags]',
                'title'		=> __('Color','woocommerce-products-slider'),
                'details'	=> __('Title text color.','woocommerce-products-slider'),
                'type'		=> 'colorpicker',
                'value'		=> $color,
                'default'		=> '',
            );

            $settings_tabs_field->generate_field($args);

            $args = array(
                'id'		=> 'font_size',
                'css_id'		=> $element_index.'_font_size',
                'parent' => $input_name.'[edd_tags]',
                'title'		=> __('Font size','woocommerce-products-slider'),
                'details'	=> __('Set font size.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_size,
                'default'		=> '',
                'placeholder'		=> '14px',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'font_family',
                'css_id'		=> $element_index.'_font_family',
                'parent' => $input_name.'[edd_tags]',
                'title'		=> __('Font family','woocommerce-products-slider'),
                'details'	=> __('Set font family.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $font_family,
                'default'		=> '',
                'placeholder'		=> 'Open Sans',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'margin',
                'css_id'		=> $element_index.'_margin',
                'parent' => $input_name.'[edd_tags]',
                'title'		=> __('Margin','woocommerce-products-slider'),
                'details'	=> __('Set margin.','woocommerce-products-slider'),
                'type'		=> 'text',
                'value'		=> $margin,
                'default'		=> '',
                'placeholder'		=> '5px 0',
            );

            $settings_tabs_field->generate_field($args);


            $args = array(
                'id'		=> 'text_align',
                'css_id'		=> $element_index.'_text_align',
                'parent' => $input_name.'[edd_tags]',
                'title'		=> __('Text align','woocommerce-products-slider'),
                'details'	=> __('Choose text align.','woocommerce-products-slider'),
                'type'		=> 'select',
                'value'		=> $text_align,
                'default'		=> 'left',
                'args'		=> array('left'=> __('Left', 'woocommerce-products-slider'),'right'=> __('Right', 'woocommerce-products-slider'),'center'=> __('Center', 'woocommerce-products-slider') ),
            );

            $settings_tabs_field->generate_field($args);




            ob_start();
            ?>
            <textarea readonly type="text"  onclick="this.select();">.element-<?php echo $element_index?>{}</textarea>
            <?php

            $html = ob_get_clean();

            $args = array(
                'id'		=> 'use_css',
                'title'		=> __('Use of CSS','woocommerce-products-slider'),
                'details'	=> __('Use following class selector to add custom CSS for this element.','woocommerce-products-slider'),
                'type'		=> 'custom_html',
                'html'		=> $html,

            );

            $settings_tabs_field->generate_field($args);

            ?>

        </div>
    </div>
    <?php

}












