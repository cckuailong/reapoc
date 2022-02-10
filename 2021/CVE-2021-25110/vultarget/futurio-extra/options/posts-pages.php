<?php

if (!class_exists('Kirki')) {
    return;
}

Kirki::add_panel('posts_pages_panel', array(
    'priority' => 10,
    'title' => esc_attr__('Posts and pages', 'futurio-extra'),
));

Kirki::add_section('blog_posts', array(
    'title' => esc_attr__('Blog posts archive', 'futurio-extra'),
    'panel' => 'posts_pages_panel',
    'priority' => 10,
));

Kirki::add_section('posts_pages', array(
    'title' => esc_attr__('Single post and page', 'futurio-extra'),
    'panel' => 'posts_pages_panel',
    'priority' => 10,
));


/**
 * Single post and page
 */
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'single_featured_image',
    'label' => __('Featured image', 'futurio-extra'),
    'section' => 'posts_pages',
    'default' => 'full',
    'priority' => 10,
    'choices' => array(
        'inside' => esc_attr__('Inside post', 'futurio-extra'),
        'full' => esc_attr__('Full Width', 'futurio-extra'),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'blog_single_image',
    'label' => esc_attr__('Image dimensions', 'futurio-extra'),
    'section' => 'posts_pages',
    'default' => 'default',
    'priority' => 10,
    'choices' => array(
        'default' => esc_attr__('Default', 'futurio-extra'),
        'custom' => esc_attr__('Custom', 'futurio-extra'),
    ),
    'active_callback' => array(
        array(
            'setting' => 'single_featured_image',
            'operator' => '==',
            'value' => 'inside',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'dimensions',
    'settings' => 'blog_single_image_set',
    'description' => sprintf(__('If you set new dimensions, you need to run %s plugin. It will regenerate all of your thumbnails to the new image sizes.', 'futurio-extra'), '<a href="' . esc_url('https://wordpress.org/plugins/regenerate-thumbnails/') . '" target="_blank"><strong>Regenerate Thumbnails</strong></a>'),
    'section' => 'posts_pages',
    'default' => array(
        'width' => '1140px',
        'height' => '641px',
    ),
    'priority' => 10,
    'transport' => 'auto',
    'active_callback' => array(
        array(
            'setting' => 'blog_single_image',
            'operator' => '==',
            'value' => 'custom',
        ),
        array(
            'setting' => 'single_featured_image',
            'operator' => '==',
            'value' => 'inside',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'single_title_position',
    'label' => esc_attr__('Title', 'futurio-extra'),
    'section' => 'posts_pages',
    'default' => 'full',
    'priority' => 10,
    'choices' => array(
        'inside' => esc_attr__('Inside post', 'futurio-extra'),
        'full' => esc_attr__('Full Width', 'futurio-extra'),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'slider',
    'settings' => 'content_spacing',
    'label' => esc_html__('Content spacing', 'futurio-extra'),
    'section' => 'posts_pages',
    'transport' => 'auto',
    'priority' => 20,
    'default' => '0',
    'choices' => array(
        'min' => '0',
        'max' => '50',
        'step' => '1',
    ),
    'output' => array(
        array(
            'element' => '.futurio-content',
            'property' => 'padding-left',
            'units' => '%',
        ),
        array(
            'element' => '.futurio-content',
            'property' => 'padding-right',
            'units' => '%',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'slider',
    'settings' => 'post_image_spacing',
    'label' => esc_attr__('Image area height', 'futurio-extra'),
    'section' => 'posts_pages',
    'default' => '60',
    'transport' => 'auto',
    'priority' => 10,
    'choices' => array(
        'min' => '0',
        'max' => '900',
        'step' => '1',
    ),
    'output' => array(
        array(
            'element' => '.full-head-img',
            'property' => 'padding-bottom',
            'units' => 'px',
        ),
        array(
            'element' => '.full-head-img',
            'property' => 'padding-top',
            'units' => 'px',
        ),
    ),
    'active_callback' => array(
        array(
            array(
                'setting' => 'single_featured_image',
                'operator' => '==',
                'value' => 'full',
            ),
            array(
                'setting' => 'single_title_position',
                'operator' => '==',
                'value' => 'full',
            ),
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'color',
    'settings' => 'featured_post_img_overlay',
    'label' => esc_attr__('Image overlay', 'futurio-extra'),
    'section' => 'posts_pages',
    'default' => 'rgba(0,0,0,0.3)',
    'choices' => array(
        'alpha' => true,
    ),
    'transport' => 'auto',
    'priority' => 10,
    'output' => array(
        array(
            'element' => '.full-head-img:after ',
            'property' => 'background-color',
        ),
    ),
    'active_callback' => array(
        array(
            array(
                'setting' => 'single_featured_image',
                'operator' => '==',
                'value' => 'full',
            ),
            array(
                'setting' => 'single_title_position',
                'operator' => '==',
                'value' => 'full',
            ),
        ),
    ),
));

/**
 * Blog posts archive
 */
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'blog_archive_date',
    'label' => esc_attr__('Date', 'futurio-extra'),
    'section' => 'blog_posts',
    'default' => 'on',
    'priority' => 10,
    'transport' => 'auto',
    'choices' => array(
        'on' => esc_attr__('Visible', 'futurio-extra'),
        'off' => esc_attr__('Hidden', 'futurio-extra'),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'blog_archive_comments',
    'label' => esc_attr__('Comments', 'futurio-extra'),
    'section' => 'blog_posts',
    'default' => 'on',
    'priority' => 10,
    'choices' => array(
        'on' => esc_attr__('Visible', 'futurio-extra'),
        'off' => esc_attr__('Hidden Off', 'futurio-extra'),
        'all' => esc_attr__('Hidden all', 'futurio-extra'),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'blog_archive_author',
    'label' => esc_attr__('Author', 'futurio-extra'),
    'section' => 'blog_posts',
    'default' => 'on',
    'priority' => 10,
    'transport' => 'auto',
    'choices' => array(
        'on' => esc_attr__('Visible', 'futurio-extra'),
        'off' => esc_attr__('Hidden', 'futurio-extra'),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'slider',
    'settings' => 'blog_archive_excerpt',
    'label' => esc_attr__('Excerpt', 'futurio-extra'),
    'section' => 'blog_posts',
    'default' => 35,
    'priority' => 10,
    'transport' => 'auto',
    'choices' => array(
        'min' => '0',
        'max' => '100',
        'step' => '1',
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'blog_archive_image',
    'label' => esc_attr__('Image dimensions', 'futurio-extra'),
    'section' => 'blog_posts',
    'default' => 'default',
    'priority' => 10,
    'choices' => array(
        'default' => esc_attr__('Default', 'futurio-extra'),
        'custom' => esc_attr__('Custom', 'futurio-extra'),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'dimensions',
    'settings' => 'blog_archive_image_set',
    'description' => sprintf(__('If you set new dimensions, you need to run %s plugin. It will regenerate all of your thumbnails to the new image sizes.', 'futurio-extra'), '<a href="' . esc_url('https://wordpress.org/plugins/regenerate-thumbnails/') . '" target="_blank"><strong>Regenerate Thumbnails</strong></a>'),
    'section' => 'blog_posts',
    'default' => array(
        'width' => '720px',
        'height' => '405px',
    ),
    'priority' => 10,
    'transport' => 'auto',
    'active_callback' => array(
        array(
            'setting' => 'blog_archive_image',
            'operator' => '==',
            'value' => 'custom',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'slider',
    'settings' => 'blog_images_radius',
    'label' => esc_attr__('Image border radius', 'futurio-extra'),
    'section' => 'blog_posts',
    'default' => 0,
    'priority' => 10,
    'transport' => 'auto',
    'choices' => array(
        'min' => '0',
        'max' => '250',
        'step' => '1',
    ),
    'output' => array(
        array(
            'element' => '.news-thumb.col-md-6 img',
            'property' => 'border-radius',
            'units' => 'px',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'slider',
    'settings' => 'blog_images_shadow',
    'label' => esc_attr__('Image shadow', 'futurio-extra'),
    'section' => 'blog_posts',
    'default' => 0,
    'transport' => 'auto',
    'priority' => 10,
    'choices' => array(
        'min' => '0',
        'max' => '40',
        'step' => '1',
    ),
    'output' => array(
        array(
            'element' => '.news-thumb.col-md-6 img',
            'property' => 'box-shadow',
            'value_pattern' => '0px 0px $px 0px rgba(0,0,0,0.35)'
        ),
    ),
));
