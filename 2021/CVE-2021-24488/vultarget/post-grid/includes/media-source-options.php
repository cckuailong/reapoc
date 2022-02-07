<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

add_action('media_source_options_featured_image', 'media_source_options_featured_image');

function media_source_options_featured_image($media_source){
    $settings_tabs_field = new settings_tabs_field();


    $index = isset($media_source['index']) ? $media_source['index'] : '';
    $input_name = isset($media_source['input_name']) ? $media_source['input_name'] : '';
    $source_data = isset($media_source['source_data']) ? $media_source['source_data'] : '';



    $margin = isset($source_data['margin']) ? $source_data['margin'] : '';
    $enable = isset($source_data['enable']) ? $source_data['enable'] : '';
    $image_size = isset($source_data['image_size']) ? $source_data['image_size'] : '';
    $link_to = isset($source_data['link_to']) ? $source_data['link_to'] : '';
    $link_target = isset($source_data['link_target']) ? $source_data['link_target'] : '';


    $args = array(
        'id'		=> 'enable',
        'parent' => $input_name.'[media][media_source][featured_image]',
        'title'		=> __('Enable','post-grid'),
        'details'	=> __('Enable or disable this media source.','post-grid'),
        'type'		=> 'radio',
        'value'		=> $enable,
        'default'		=> 'no',
        'args'		=> array(
            'no'=>__('No','post-grid'),
            'yes'=>__('Yes','post-grid'),
        ),
    );

    $settings_tabs_field->generate_field($args);


    $args = array(
        'id'		=> 'image_size',
        'parent' => $input_name.'[media][media_source][featured_image]',
        'title'		=> __('Image size','post-grid'),
        'details'	=> __('Select media image size','post-grid'),
        'type'		=> 'select',
        'value'		=> $image_size,
        'default'		=> 'large',
        'args'		=> post_grid_image_sizes(),
    );

    $settings_tabs_field->generate_field($args);

    $args = array(
        'id'		=> 'link_to',
        'css_id'		=> $index.'_link_to',
        'parent' => $input_name.'[media][media_source][featured_image]',
        'title'		=> __('Link to','post-grid'),
        'details'	=> __('Choose link to featured image.','post-grid'),
        'type'		=> 'select',
        'value'		=> $link_to,
        'default'		=> 'post_link',
        'args'		=> array(
            'post_link'=> __('Post link', 'post-grid'),
            'none'=> __('None', 'post-grid'),
        ),
    );

    $settings_tabs_field->generate_field($args);


    $args = array(
        'id'		=> 'link_target',
        'css_id'		=> $index.'_link_target',
        'parent' => $input_name.'[media][media_source][featured_image]',
        'title'		=> __('Link target','post-grid'),
        'details'	=> __('Choose link target.','post-grid'),
        'type'		=> 'select',
        'value'		=> $link_target,
        'default'		=> '_self',
        'args'		=> array(
            '_blank'=> __('_blank', 'post-grid'),
            '_parent'=> __('_parent', 'post-grid'),
            '_self'=> __('_self', 'post-grid'),
            '_top'=> __('_top', 'post-grid'),

        ),
    );

    $settings_tabs_field->generate_field($args);


}

add_action('media_source_options_first_image', 'media_source_options_first_image');

function media_source_options_first_image($media_source){
    $settings_tabs_field = new settings_tabs_field();


    $index = isset($media_source['index']) ? $media_source['index'] : '';
    $input_name = isset($media_source['input_name']) ? $media_source['input_name'] : '';
    $source_data = isset($media_source['source_data']) ? $media_source['source_data'] : '';

    $enable = isset($source_data['enable']) ? $source_data['enable'] : '';
    $link_to = isset($source_data['link_to']) ? $source_data['link_to'] : '';
    $link_target = isset($source_data['link_target']) ? $source_data['link_target'] : '';



    $args = array(
        'id'		=> 'enable',
        'parent' => $input_name.'[media][media_source][first_image]',
        'title'		=> __('Enable','post-grid'),
        'details'	=> __('Enable or disable this media source.','post-grid'),
        'type'		=> 'radio',
        'value'		=> $enable,
        'default'		=> 'no',
        'args'		=> array(
            'no'=>__('No','post-grid'),
            'yes'=>__('Yes','post-grid'),
        ),
    );

    $settings_tabs_field->generate_field($args);

    $args = array(
        'id'		=> 'link_to',
        'css_id'		=> $index.'_link_to',
        'parent' => $input_name.'[media][media_source][first_image]',
        'title'		=> __('Link to','post-grid'),
        'details'	=> __('Choose link to featured image.','post-grid'),
        'type'		=> 'select',
        'value'		=> $link_to,
        'default'		=> 'post_link',
        'args'		=> array(
            'post_link'=> __('Post link', 'post-grid'),
            'none'=> __('None', 'post-grid'),
        ),
    );

    $settings_tabs_field->generate_field($args);


    $args = array(
        'id'		=> 'link_target',
        'css_id'		=> $index.'_link_target',
        'parent' => $input_name.'[media][media_source][first_image]',
        'title'		=> __('Link target','post-grid'),
        'details'	=> __('Choose link target.','post-grid'),
        'type'		=> 'select',
        'value'		=> $link_target,
        'default'		=> '_self',
        'args'		=> array(
            '_blank'=> __('_blank', 'post-grid'),
            '_parent'=> __('_parent', 'post-grid'),
            '_self'=> __('_self', 'post-grid'),
            '_top'=> __('_top', 'post-grid'),

        ),
    );

    $settings_tabs_field->generate_field($args);
}



add_action('media_source_options_siteorigin_first_image', 'media_source_options_siteorigin_first_image');

function media_source_options_siteorigin_first_image($media_source){
    $settings_tabs_field = new settings_tabs_field();


    $index = isset($media_source['index']) ? $media_source['index'] : '';
    $input_name = isset($media_source['input_name']) ? $media_source['input_name'] : '';
    $source_data = isset($media_source['source_data']) ? $media_source['source_data'] : '';

    $enable = isset($source_data['enable']) ? $source_data['enable'] : '';
    $link_to = isset($source_data['link_to']) ? $source_data['link_to'] : '';
    $link_target = isset($source_data['link_target']) ? $source_data['link_target'] : '';



    $args = array(
        'id'		=> 'enable',
        'parent' => $input_name.'[media][media_source][siteorigin_first_image]',
        'title'		=> __('Enable','post-grid'),
        'details'	=> __('Enable or disable this media source.','post-grid'),
        'type'		=> 'radio',
        'value'		=> $enable,
        'default'		=> 'no',
        'args'		=> array(
            'no'=>__('No','post-grid'),
            'yes'=>__('Yes','post-grid'),
        ),
    );

    $settings_tabs_field->generate_field($args);

    $args = array(
        'id'		=> 'link_to',
        'css_id'		=> $index.'_link_to',
        'parent' => $input_name.'[media][media_source][siteorigin_first_image]',
        'title'		=> __('Link to','post-grid'),
        'details'	=> __('Choose link to featured image.','post-grid'),
        'type'		=> 'select',
        'value'		=> $link_to,
        'default'		=> 'post_link',
        'args'		=> array(
            'post_link'=> __('Post link', 'post-grid'),
            'none'=> __('None', 'post-grid'),
        ),
    );

    $settings_tabs_field->generate_field($args);

    $args = array(
        'id'		=> 'link_target',
        'css_id'		=> $index.'_link_target',
        'parent' => $input_name.'[media][media_source][siteorigin_first_image]',
        'title'		=> __('Link target','post-grid'),
        'details'	=> __('Choose link target.','post-grid'),
        'type'		=> 'select',
        'value'		=> $link_target,
        'default'		=> '_self',
        'args'		=> array(
            '_blank'=> __('_blank', 'post-grid'),
            '_parent'=> __('_parent', 'post-grid'),
            '_self'=> __('_self', 'post-grid'),
            '_top'=> __('_top', 'post-grid'),

        ),
    );

    $settings_tabs_field->generate_field($args);

}









add_action('media_source_options_empty_thumb', 'media_source_options_empty_thumb');

function media_source_options_empty_thumb($media_source){
    $settings_tabs_field = new settings_tabs_field();


    $index = isset($media_source['index']) ? $media_source['index'] : '';
    $input_name = isset($media_source['input_name']) ? $media_source['input_name'] : '';
    $source_data = isset($media_source['source_data']) ? $media_source['source_data'] : '';

    $enable = isset($source_data['enable']) ? $source_data['enable'] : '';
    $default_thumb_src = isset($source_data['default_thumb_src']) ? $source_data['default_thumb_src'] : '';
    $link_to = isset($source_data['link_to']) ? $source_data['link_to'] : '';
    $link_target = isset($source_data['link_target']) ? $source_data['link_target'] : '';




    $args = array(
        'id'		=> 'enable',
        'parent' => $input_name.'[media][media_source][empty_thumb]',
        'title'		=> __('Enable','post-grid'),
        'details'	=> __('Enable or disable this media source.','post-grid'),
        'type'		=> 'radio',
        'value'		=> $enable,
        'default'		=> 'no',
        'args'		=> array(
            'no'=>__('No','post-grid'),
            'yes'=>__('Yes','post-grid'),
        ),
    );

    $settings_tabs_field->generate_field($args);


    $args = array(
        'id'		=> 'link_to',
        'css_id'		=> $index.'_link_to',
        'parent' => $input_name.'[media][media_source][empty_thumb]',
        'title'		=> __('Link to','post-grid'),
        'details'	=> __('Choose link to featured image.','post-grid'),
        'type'		=> 'select',
        'value'		=> $link_to,
        'default'		=> 'post_link',
        'args'		=> array(
            'post_link'=> __('Post link', 'post-grid'),
            'none'=> __('None', 'post-grid'),
        ),
    );

    $settings_tabs_field->generate_field($args);

    $args = array(
        'id'		=> 'link_target',
        'css_id'		=> $index.'_link_target',
        'parent' => $input_name.'[media][media_source][empty_thumb]',
        'title'		=> __('Link target','post-grid'),
        'details'	=> __('Choose link target.','post-grid'),
        'type'		=> 'select',
        'value'		=> $link_target,
        'default'		=> '_self',
        'args'		=> array(
            '_blank'=> __('_blank', 'post-grid'),
            '_parent'=> __('_parent', 'post-grid'),
            '_self'=> __('_self', 'post-grid'),
            '_top'=> __('_top', 'post-grid'),

        ),
    );

    $settings_tabs_field->generate_field($args);

    $args = array(
        'id'		=> 'default_thumb_src',
        'parent' => $input_name.'[media][media_source][empty_thumb]',
        'title'		=> __('Default thumbnail','post-grid'),
        'details'	=> __('Choose default thumbnail.','post-grid'),
        'type'		=> 'media_url',
        'value'		=> $default_thumb_src,
        'default'		=> '',
    );

    $settings_tabs_field->generate_field($args);

}