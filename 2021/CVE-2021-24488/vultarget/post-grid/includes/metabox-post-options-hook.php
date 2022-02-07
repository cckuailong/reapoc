<?php

/*
* @Author 		PickPlugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access


add_action('post_grid_post_options_content_options', 'post_grid_post_options_content_options',10, 2);

function post_grid_post_options_content_options($tab, $post_id){

    $settings_tabs_field = new settings_tabs_field();

    $class_post_grid_functions = new class_post_grid_functions();
    $post_grid_skins = $class_post_grid_functions->skins();

    $skin_list = array();

    foreach($post_grid_skins as $skin_key=>$skin_data){

        $skin_list[$skin_key] = $skin_data['name'];
    }


    $post_grid_post_settings = get_post_meta($post_id, 'post_grid_post_settings', true);

    $post_skin = !empty($post_grid_post_settings['post_skin']) ? $post_grid_post_settings['post_skin'] : 'flat';
    $custom_thumb_source = !empty($post_grid_post_settings['custom_thumb_source']) ? $post_grid_post_settings['custom_thumb_source'] : '';
    $thumb_custom_url = !empty($post_grid_post_settings['thumb_custom_url']) ? $post_grid_post_settings['thumb_custom_url'] : '';



    ?>
    <div class="section">
        <div class="section-title">Options</div>
        <p class="description section-description">Change post option here.</p>


        <?php

        $args = array(
            'id'		=> 'custom_thumb_source',
            'parent'		=> 'post_grid_post_settings',
            'title'		=> __('Custom thumbnail image source','post-grid'),
            'details'	=> __('You can use custom thumbnail image source.','post-grid'),
            'type'		=> 'media_url',
            'value'		=> $custom_thumb_source,
            'default'		=> '',
        );

        $settings_tabs_field->generate_field($args, $post_id);

        $args = array(
            'id'		=> 'thumb_custom_url',
            'parent'		=> 'post_grid_post_settings',
            'title'		=> __('Custom link to this post','post-grid'),
            'details'	=> __('You can use custom link to this post.','post-grid'),
            'type'		=> 'text',
            'value'		=> $thumb_custom_url,
            'default'		=> '',
        );

        $settings_tabs_field->generate_field($args, $post_id);








        ?>
    </div>
    <?php
}



