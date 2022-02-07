<?php
if ( ! defined('ABSPATH')) exit;  // if direct access







add_filter('wcps_layout_elements', 'wcps_layout_elements_dokan');
function wcps_layout_elements_dokan($layout_elements){

    $layout_elements['dokan_store_name'] = array('name' =>__('Dokan store name','woocommerce-products-slider'));
    $layout_elements['dokan_store_address'] = array('name' =>__('Dokan store address','woocommerce-products-slider'));
    $layout_elements['dokan_store_city'] = array('name' =>__('Dokan store city','woocommerce-products-slider'));
    $layout_elements['dokan_store_country'] = array('name' =>__('Dokan store country','woocommerce-products-slider'));
    $layout_elements['dokan_store_phone'] = array('name' =>__('Dokan store phone','woocommerce-products-slider'));
    $layout_elements['dokan_banner'] = array('name' =>__('Dokan banner','woocommerce-products-slider'));
    $layout_elements['dokan_avatar'] = array('name' =>__('Dokan avatar','woocommerce-products-slider'));

    return $layout_elements;
}


add_action('wcps_layout_element_dokan_store_name', 'wcps_layout_element_dokan_store_name', 10);
function wcps_layout_element_dokan_store_name($args){

    $user_id = isset($args['user_id']) ? $args['user_id'] : (int) wcps_get_first_dokan_vendor_id();

    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($elementData['wrapper_html']) ? $elementData['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-term_title element-'.$element_index : 'element-term_title';

    $dokan_profile_settings = get_user_meta($user_id, 'dokan_profile_settings', true );

    $store_name = isset($dokan_profile_settings['store_name']) ? $dokan_profile_settings['store_name'] : '';

    if(!empty($store_name)):
        ?>
        <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $store_name); ?></div>
        <?php
    endif;


}


add_action('wcps_layout_element_dokan_store_address', 'wcps_layout_element_dokan_store_address', 10);
function wcps_layout_element_dokan_store_address($args){

    $user_id = isset($args['user_id']) ? $args['user_id'] : (int) wcps_get_first_dokan_vendor_id();

    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($elementData['wrapper_html']) ? $elementData['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-term_title element-'.$element_index : 'element-term_title';

    $dokan_profile_settings = get_user_meta($user_id, 'dokan_profile_settings', true );

    $address_street_1 = isset($dokan_profile_settings['address']['street_1']) ? $dokan_profile_settings['address']['street_1'] : '';

    if(!empty($address_street_1)):
        ?>
        <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $address_street_1); ?></div>
        <?php
    endif;


}



add_action('wcps_layout_element_dokan_store_city', 'wcps_layout_element_dokan_store_city', 10);
function wcps_layout_element_dokan_store_city($args){

    $user_id = isset($args['user_id']) ? $args['user_id'] : (int) wcps_get_first_dokan_vendor_id();

    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($elementData['wrapper_html']) ? $elementData['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-term_title element-'.$element_index : 'element-term_title';

    $dokan_profile_settings = get_user_meta($user_id, 'dokan_profile_settings', true );

    $address_city = isset($dokan_profile_settings['address']['city']) ? $dokan_profile_settings['address']['city'] : '';

    if(!empty($address_city)):
        ?>
        <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $address_city); ?></div>
        <?php
    endif;


}



add_action('wcps_layout_element_dokan_store_country', 'wcps_layout_element_dokan_store_country', 10);
function wcps_layout_element_dokan_store_country($args){

    $user_id = isset($args['user_id']) ? $args['user_id'] : (int) wcps_get_first_dokan_vendor_id();

    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($elementData['wrapper_html']) ? $elementData['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-term_title element-'.$element_index : 'element-term_title';

    $dokan_profile_settings = get_user_meta($user_id, 'dokan_profile_settings', true );

    $address_country = isset($dokan_profile_settings['address']['country']) ? $dokan_profile_settings['address']['country'] : '';

    if(!empty($address_country)):
        ?>
        <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $address_country); ?></div>
        <?php
    endif;


}



add_action('wcps_layout_element_dokan_store_phone', 'wcps_layout_element_dokan_store_phone', 10);
function wcps_layout_element_dokan_store_phone($args){

    $user_id = isset($args['user_id']) ? $args['user_id'] : (int) wcps_get_first_dokan_vendor_id();

    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($elementData['wrapper_html']) ? $elementData['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-term_title element-'.$element_index : 'element-term_title';

    $dokan_profile_settings = get_user_meta($user_id, 'dokan_profile_settings', true );

    $phone = isset($dokan_profile_settings['phone']) ? $dokan_profile_settings['phone'] : '';

    if(!empty($phone)):
        ?>
        <div class="<?php echo $element_class; ?>"><?php echo sprintf($wrapper_html, $phone); ?></div>
        <?php
    endif;


}

add_action('wcps_layout_element_dokan_banner', 'wcps_layout_element_dokan_banner', 10);
function wcps_layout_element_dokan_banner($args){

    $user_id = isset($args['user_id']) ? $args['user_id'] : (int) wcps_get_first_dokan_vendor_id();

    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($elementData['wrapper_html']) ? $elementData['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-term_title element-'.$element_index : 'element-term_title';

    $dokan_profile_settings = get_user_meta($user_id, 'dokan_profile_settings', true );
    $banner_id = !empty($dokan_profile_settings['gravatar']) ? $dokan_profile_settings['banner'] : '';


    $store_url = dokan_get_store_url($user_id);

    $banner_urls = wp_get_attachment_image_src($banner_id, 'full');
    $banner_image_url = isset($banner_urls[0]) ? $banner_urls[0] : '';

    ?>
    <div class="<?php echo $element_class; ?>"><a href="<?php echo $store_url; ?>"><img src="<?php echo $banner_image_url; ?>"></a> </div>
    <?php

}


add_action('wcps_layout_element_dokan_avatar', 'wcps_layout_element_dokan_avatar', 10);
function wcps_layout_element_dokan_avatar($args){

    $user_id = isset($args['user_id']) ? $args['user_id'] : (int) wcps_get_first_dokan_vendor_id();

    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $element_index = isset($args['element_index']) ? $args['element_index'] : '';

    $wrapper_html = isset($elementData['wrapper_html']) ? $elementData['wrapper_html'] : '';
    $wrapper_html = !empty($wrapper_html) ? $wrapper_html : '%s';

    $element_class = !empty($element_index) ? 'element-term_title element-'.$element_index : 'element-term_title';

    $dokan_profile_settings = get_user_meta($user_id, 'dokan_profile_settings', true );
    $store_url = dokan_get_store_url($user_id);

    $gravatar_id = !empty($dokan_profile_settings['gravatar']) ? $dokan_profile_settings['gravatar'] : '';
    $banner_urls = wp_get_attachment_image_src($gravatar_id, 'full');
    $banner_image_url = isset($banner_urls[0]) ? $banner_urls[0] : '';

    ?>
    <div class="<?php echo $element_class; ?>"><a href="<?php echo $store_url; ?>"><img src="<?php echo $banner_image_url; ?>"></a> </div>
    <?php

}






add_action('wcps_layout_element_css_dokan_store_name', 'wcps_layout_element_css_dokan_store_name', 10);
function wcps_layout_element_css_dokan_store_name($args){


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

add_action('wcps_layout_element_css_dokan_store_address', 'wcps_layout_element_css_dokan_store_address', 10);
function wcps_layout_element_css_dokan_store_address($args){


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



add_action('wcps_layout_element_css_dokan_store_city', 'wcps_layout_element_css_dokan_store_city', 10);
function wcps_layout_element_css_dokan_store_city($args){


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




add_action('wcps_layout_element_css_dokan_store_country', 'wcps_layout_element_css_dokan_store_country', 10);
function wcps_layout_element_css_dokan_store_country($args){


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


add_action('wcps_layout_element_css_dokan_store_phone', 'wcps_layout_element_css_dokan_store_phone', 10);
function wcps_layout_element_css_dokan_store_phone($args){


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


add_action('wcps_layout_element_css_dokan_banner', 'wcps_layout_element_css_dokan_banner', 10);
function wcps_layout_element_css_dokan_banner($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';
    $text_align = isset($elementData['text_align']) ? $elementData['text_align'] : '';
    $width = isset($elementData['width']) ? $elementData['width'] : '';
    $height = isset($elementData['height']) ? $elementData['height'] : '';

    ?>
    <style type="text/css">
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($width)): ?>
            width: <?php echo $width; ?>;
        <?php endif; ?>
        <?php if(!empty($height)): ?>
            height: <?php echo $height; ?>;
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

add_action('wcps_layout_element_css_dokan_avatar', 'wcps_layout_element_css_dokan_avatar', 10);
function wcps_layout_element_css_dokan_avatar($args){


    $element_index = isset($args['element_index']) ? $args['element_index'] : '';
    $elementData = isset($args['elementData']) ? $args['elementData'] : array();
    $layout_id = isset($args['layout_id']) ? $args['layout_id'] : '';

    $margin = isset($elementData['margin']) ? $elementData['margin'] : '';
    $text_align = isset($elementData['text_align']) ? $elementData['text_align'] : '';
    $width = isset($elementData['width']) ? $elementData['width'] : '';

    ?>
    <style type="text/css">
        .layout-<?php echo $layout_id; ?> .element-<?php echo $element_index; ?>{
        <?php if(!empty($width)): ?>
            width: <?php echo $width; ?>;
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