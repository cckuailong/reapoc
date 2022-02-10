<?php

if (!class_exists('Kirki')) {
    return;
}

Kirki::add_section('main_menu_colors_section', array(
    'title' => esc_attr__('Main Menu', 'futurio-extra'),
    'panel' => 'colors',
    'priority' => 10,
));

/**
 * Main Menu colors
 */
Kirki::add_field('futurio_extra', array(
    'type' => 'radio-buttonset',
    'settings' => 'typography_mainmenu_tab',
    'section' => 'main_menu_colors_section',
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
    'settings' => 'typography_mainmenu',
    'label' => esc_attr__('Menu Font', 'futurio-extra'),
    'section' => 'main_menu_colors_section',
    'choices' => futurio_extra_g_fonts(),
    'transport' => 'auto',
    'default' => array(
        'font-family' => '',
        'font-size' => '15px',
        'variant' => '400',
        'letter-spacing' => '0px',
        'text-transform' => 'uppercase',
        futurio_extra_col() => '',
    ),
    'priority' => 10,
    'output' => array(
        array(
            'element' => '#site-navigation, #site-navigation .navbar-nav > li > a, #site-navigation .dropdown-menu > li > a',
        ),
        array(
            'choice' => 'color',
            'element' => '.open-panel span',
            'property' => 'background-color',
        ),
        array(
            'choice' => 'color',
            'element' => '.open-panel span, .brand-absolute, .header-cart a.cart-contents, .header-login a, .top-search-icon i, .offcanvas-sidebar-toggle i, .site-header-cart, .site-header-cart a',
            'property' => 'color',
        ),
    ),
    'active_callback' => array(
        array(
            'setting' => 'typography_mainmenu_tab',
            'operator' => '==',
            'value' => 'desktop',
        ),
    ),
));

Kirki::add_field('futurio_extra', array(
    'type' => 'typography',
    'settings' => 'typography_mainmenu_tablet',
    'label' => esc_attr__('Menu Font', 'futurio-extra'),
    'section' => 'main_menu_colors_section',
    'transport' => 'auto',
    'default' => array(
        'font-size' => '',
        'letter-spacing' => '',
        'text-transform' => '',
    ),
    'priority' => 10,
    'output' => array(
        array(
            'element' => '#site-navigation, #site-navigation .navbar-nav > li > a, #site-navigation .dropdown-menu > li > a',
            'media_query' => '@media (max-width: 991px)',
        ),
    ),
    'active_callback' => array(
        array(
            'setting' => 'typography_mainmenu_tab',
            'operator' => '==',
            'value' => 'tablet',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'typography',
    'settings' => 'typography_mainmenu_mobile',
    'label' => esc_attr__('Menu Font', 'futurio-extra'),
    'section' => 'main_menu_colors_section',
    'transport' => 'auto',
    'default' => array(
        'font-size' => '',
        'letter-spacing' => '',
        'text-transform' => '',
    ),
    'priority' => 10,
    'output' => array(
        array(
            'element' => '#site-navigation, #site-navigation .navbar-nav > li > a, #site-navigation .dropdown-menu > li > a',
            'media_query' => '@media (max-width: 767px)',
        ),
    ),
    'active_callback' => array(
        array(
            'setting' => 'typography_mainmenu_tab',
            'operator' => '==',
            'value' => 'mobile',
        ),
    ),
));
Kirki::add_field('futurio_extra', array(
    'type' => 'custom',
    'settings' => 'main_menu_colors_section_end',
    'label' => '<hr/>',
    'section' => 'main_menu_colors_section',
    'default' => '',
));
