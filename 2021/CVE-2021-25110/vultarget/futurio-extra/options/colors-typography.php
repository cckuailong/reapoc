<?php

if (!class_exists('Kirki')) {
    return;
}

Kirki::add_panel('colors', array(
    'priority' => 10,
    'title' => esc_attr__('Colors and Typography', 'futurio-extra'),
));

Kirki::add_section('main_colors_section', array(
    'title' => esc_attr__('Content', 'futurio-extra'),
    'panel' => 'colors',
    'priority' => 10,
));

/**
 * Colors
 */
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'main_typography_tab',
    'section' => 'main_colors_section',
    'transport' => 'postMessage',
    'default' => 'desktop',
    'choices' => array(
        'desktop' => '<i class="dashicons dashicons-desktop"></i>',
        'tablet' => '<i class="dashicons dashicons-tablet"></i>',
        'mobile' => '<i class="dashicons dashicons-smartphone"></i>',
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'typography',
    'settings' => 'main_typography',
    'label' => esc_attr__('Site font', 'futurio-extra'),
    'section' => 'main_colors_section',
    'transport' => 'auto',
    'choices' => futurio_extra_g_fonts(),
    'default' => array(
        'font-family' => '',
        'font-size' => '15px',
        'variant' => '400',
        'line-height' => '1.6',
        'letter-spacing' => '0px',
        futurio_extra_col() => '',
    ),
    'priority' => 10,
    'output' => array(
        array(
            'element' => 'body, nav.navigation.post-navigation a, .nav-subtitle',
        ),
        array(
            'choice' => 'color',
            'element' => '.comments-meta a',
        ),
    ),
    'active_callback' => array(
        array(
            'setting' => 'main_typography_tab',
            'operator' => '==',
            'value' => 'desktop',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'typography',
    'settings' => 'main_typography_titles',
    'label' => esc_attr__('Titles', 'futurio-extra'),
    'section' => 'main_colors_section',
    'choices' => futurio_extra_g_fonts(),
    'transport' => 'auto',
    'default' => array(
        'font-family' => '',
        futurio_extra_col() => '',
    ),
    'priority' => 10,
    'output' => array(
        array(
            'element' => '.news-item h2 a, .page-header, .page-header a, h1.single-title, h1, h2, h3, h4, h5, h6',
        ),
    ),
    'active_callback' => array(
        array(
            'setting' => 'main_typography_tab',
            'operator' => '==',
            'value' => 'desktop',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'typography',
    'settings' => 'main_typography_tablet',
    'label' => esc_attr__('Site font', 'futurio-extra'),
    'section' => 'main_colors_section',
    'transport' => 'auto',
    'default' => array(
        'font-size' => '',
        'line-height' => '',
        'letter-spacing' => '',
    ),
    'priority' => 10,
    'output' => array(
        array(
            'element' => 'body, nav.navigation.post-navigation a, .nav-subtitle',
            'media_query' => '@media (max-width: 991px)',
        ),
    ),
    'active_callback' => array(
        array(
            'setting' => 'main_typography_tab',
            'operator' => '==',
            'value' => 'tablet',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'typography',
    'settings' => 'main_typography_mobile',
    'label' => esc_attr__('Site font', 'futurio-extra'),
    'section' => 'main_colors_section',
    'transport' => 'auto',
    'default' => array(
        'font-size' => '',
        'line-height' => '',
        'letter-spacing' => '',
    ),
    'priority' => 10,
    'output' => array(
        array(
            'element' => 'body, nav.navigation.post-navigation a, .nav-subtitle',
            'media_query' => '@media (max-width: 767px)',
        ),
    ),
    'active_callback' => array(
        array(
            'setting' => 'main_typography_tab',
            'operator' => '==',
            'value' => 'mobile',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'custom',
    'settings' => 'main_typography_tab_end',
    'label' => '<hr/>',
    'section' => 'main_colors_section',
    'default' => '',
));
