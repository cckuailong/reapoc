<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}

Kirki::add_field( 'futurio_extra', array(
  'type'     => 'select',
  'settings' => 'custom_header',
  'label'    => esc_attr__( 'Elementor custom header', 'futurio-extra' ),
  'section'  => 'title_tagline',
  'default'  => '',
  'placeholder' => esc_attr__( 'Select an option', 'futurio-extra' ),
  'description' => esc_attr__( 'Note: This will override all options defined below and disable the header and main menu.', 'futurio-extra' ),
  'priority' => 5,
  'choices'  => Kirki_Helper::get_posts(
    array(
    	'posts_per_page' => -1,
    	'post_type'      => 'elementor_library'
    )
  ),
  'active_callback' => 'futurio_extra_check_for_elementor',
) );

Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'radio-buttonset',
	'settings'	 => 'title_heading',
	'label'		 => __( 'Heading', 'futurio-extra' ),
	'section'	 => 'title_tagline',
	'default'	 => 'boxed',
	'priority'	 => 5,
	'choices'	 => array(
		'full'			 => __( 'Separate center', 'futurio-extra' ),
		'boxed'			 => __( 'Inside menu', 'futurio-extra' ),
	),
) );
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'section' => 'title_tagline',
  'settings' => 'title_margin_tab',
  'priority' => 5,
  'transport' => 'postMessage',
  'default' => 'desktop',
  'choices' => array(
      'desktop' => '<i class="dashicons dashicons-desktop"></i>',
      'tablet' => '<i class="dashicons dashicons-tablet"></i>',
      'mobile' => '<i class="dashicons dashicons-smartphone"></i>',
  ),
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'full',
		),
	),
));
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'title_margin',
	'label'		 => esc_html__( 'Header spacing', 'futurio-extra' ),
	'section'	 => 'title_tagline',
	'transport'		 => 'auto',
  'priority'	 => 5,
	'default'     => 15,
  'choices'     => array(
		'min'  => '0',
		'max'  => '600',
		'step' => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.heading-row .site-heading',
			'property'	 => 'padding-bottom',
			'units'		 => 'px',
		),
    array(
			'element'	 => '.heading-row .site-heading',
			'property'	 => 'padding-top',
			'units'		 => 'px',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'full',
		),
    array(
			'setting'	 => 'title_margin_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'title_margin_tablet',
	'label'		 => esc_html__( 'Header spacing', 'futurio-extra' ),
	'section'	 => 'title_tagline',
	'transport'		 => 'auto',
  'priority'	 => 5,
	'default'     => 15,
  'choices'     => array(
		'min'  => '0',
		'max'  => '600',
		'step' => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.heading-row .site-heading',
			'property'	 => 'padding-bottom',
			'units'		 => 'px',
      'media_query'	 => '@media (max-width: 992px)',
		),
    array(
			'element'	 => '.heading-row .site-heading',
			'property'	 => 'padding-top',
			'units'		 => 'px',
      'media_query'	 => '@media (max-width: 992px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'full',
		),
    array(
			'setting'	 => 'title_margin_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
	'settings'	 => 'title_margin_mobile',
	'label'		 => esc_html__( 'Header spacing', 'futurio-extra' ),
	'section'	 => 'title_tagline',
	'transport'		 => 'auto',
  'priority'	 => 5,
	'default'     => 15,
  'choices'     => array(
		'min'  => '0',
		'max'  => '600',
		'step' => '1',
	),
	'output'	 => array(
		array(
			'element'	 => '.heading-row .site-heading',
			'property'	 => 'padding-bottom',
			'units'		 => 'px',
      'media_query'	 => '@media (max-width: 768px)',
		),
    array(
			'element'	 => '.heading-row .site-heading',
			'property'	 => 'padding-top',
			'units'		 => 'px',
      'media_query'	 => '@media (max-width: 768px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'full',
		),
    array(
			'setting'	 => 'title_margin_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'title_margin_tab_end',
	'label'       => '<hr/>',
	'section'     => 'title_tagline',
	'default'     => '',
	'priority'    => 5,
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'full',
		),
	),
) );
/**
 * Add tabs for static and floating header
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'section' => 'title_tagline',
  'settings' => 'title_logo_height_tab',
  'priority' => 6,
  'transport' => 'postMessage',
  'default' => 'static',
  'choices' => array(
      'static' => esc_html__('Static header', 'futurio-extra'),
      'floating' => esc_html__('Floating header', 'futurio-extra'),
  ),
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'boxed',
		),
	),
));
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
  'label' => esc_html__('Title and logo height', 'futurio-extra'),
	'settings'	 => 'title_logo_height_static',
	'section'	 => 'title_tagline',
  'priority' => 6,
	'transport'		 => 'auto',
	'default'     => '80',
  'choices'     => array(
		'min'  => '1',
		'max'  => '200',
		'step' => '1',
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_logo_height_tab',
			'operator'	 => '==',
			'value'		 => 'static',
		),
    array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'boxed',
		),
	),
  'output'	 => array(
		array(
			'element'	 => '.site-heading.navbar-brand',
			'property'	 => 'height',
			'units'		 => 'px',
		),
    array(
			'element'	 => '.site-branding-logo img',
			'property'	 => 'max-height',
			'units'		 => 'px',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'dimensions',
	'settings'    => 'f_logo_spacing',
	'label'       => esc_attr__( 'Logo spacing', 'futurio-extra' ),
	'section'     => 'title_tagline',
  'priority'	 => 6,
	'default'     => array(
		'top'    => '0px',
		'right'  => '0px',
		'bottom' => '0px',
		'left'   => '0px',
	),
	'transport'   => 'auto',
	'output'      => array(
		array(
			'property' => 'padding',
			'element'  => '.heading-menu .site-branding-logo img',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'custom_logo',
			'operator'	 => '!=',
			'value'		 => '',
		),
    array(
			'setting'	 => 'title_logo_height_tab',
			'operator'	 => '==',
			'value'		 => 'static',
		),
    array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'boxed',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'dimensions',
	'settings'    => 'f_title_spacing',
	'label'       => esc_attr__( 'Site Title and Tagline spacing', 'futurio-extra' ),
	'section'     => 'title_tagline',
  'priority'	 => 6,
	'default'     => array(
		'top'    => '0px',
		'right'  => '0px',
		'bottom' => '0px',
		'left'   => '0px',
	),
	'transport'   => 'auto',
	'output'      => array(
		array(
			'property' => 'padding',
			'element'  => '.heading-menu .site-branding-text',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'display_header_text',
			'operator'	 => '==',
			'value'		 => false,
		),
    array(
			'setting'	 => 'title_logo_height_tab',
			'operator'	 => '==',
			'value'		 => 'static',
		),
    array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'boxed',
		),
	), 
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'slider',
  'label' => esc_html__('Title and logo height', 'futurio-extra'),
	'settings'	 => 'title_logo_height_floating',
	'section'	 => 'title_tagline',
  'priority' => 6,
	'transport'		 => 'auto',
	'default'     => '50',
  'choices'     => array(
		'min'  => '1',
		'max'  => '200',
		'step' => '1',
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_logo_height_tab',
			'operator'	 => '==',
			'value'		 => 'floating',
		),
    array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'boxed',
		),
	),
  'output'	 => array(
		array(
			'element'	 => '.shrink .site-heading.navbar-brand',
			'property'	 => 'height',
			'units'		 => 'px',
		),
    array(
			'element'	 => '.shrink .site-branding-logo img',
			'property'	 => 'max-height',
			'units'		 => 'px',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'dimensions',
	'settings'    => 'f_logo_spacing_floating',
	'label'       => esc_attr__( 'Logo spacing', 'futurio-extra' ),
	'section'     => 'title_tagline',
  'priority'	 => 6,
	'default'     => array(
		'top'    => '0px',
		'right'  => '0px',
		'bottom' => '0px',
		'left'   => '0px',
	),
	'transport'   => 'auto',
	'output'      => array(
		array(
			'property' => 'padding',
			'element'  => '.shrink .heading-menu .site-branding-logo img',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'custom_logo',
			'operator'	 => '!=',
			'value'		 => '',
		),
    array(
			'setting'	 => 'title_logo_height_tab',
			'operator'	 => '==',
			'value'		 => 'floating',
		),
    array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'boxed',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'dimensions',
	'settings'    => 'f_title_spacing_floating',
	'label'       => esc_attr__( 'Site Title and Tagline spacing', 'futurio-extra' ),
	'section'     => 'title_tagline',
  'priority'	 => 6,
	'default'     => array(
		'top'    => '0px',
		'right'  => '0px',
		'bottom' => '0px',
		'left'   => '0px',
	),
	'transport'   => 'auto',
	'output'      => array(
		array(
			'property' => 'padding',
			'element'  => '.shrink .heading-menu .site-branding-text',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'display_header_text',
			'operator'	 => '==',
			'value'		 => false,
		),
    array(
			'setting'	 => 'title_logo_height_tab',
			'operator'	 => '==',
			'value'		 => 'floating',
		),
    array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'boxed',
		),
	), 
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'title_logo_height_tab_end',
	'label'       => '<hr/>',
	'section'     => 'title_tagline',
	'default'     => '',
	'priority'    => 6,
  'active_callback'	 => array(
		array(
			'setting'	 => 'title_heading',
			'operator'	 => '==',
			'value'		 => 'boxed',
		),
	),
) );