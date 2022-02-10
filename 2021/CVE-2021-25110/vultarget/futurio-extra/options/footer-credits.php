<?php
if ( !class_exists( 'Kirki' ) ) {
	return;
}

Kirki::add_section( 'code_section', array(
	'title'		 => esc_attr__( 'Copyright', 'futurio-extra' ),
	'priority'	 => 10,
) );

Kirki::add_field( 'futurio_extra', array(
	'type'			 => 'editor',
	'settings'		 => 'footer-credits',
	'label'			 => __( 'Footer credits', 'futurio-extra' ),
	'description'	 => __( 'HTML is allowed.<br/> Use <code>%current_year%</code> to update year automatically.<br/> Use <code>%copy%</code> to include copyright symbol.', 'futurio-extra' ),
	'section'		 => 'code_section',
	'transport'		 => 'postMessage',
	'js_vars'		 => array(
		array(
			'element'	 => '.footer-credits-text',
			'function'	 => 'html',
		),
	),
	'default'		 => '',
	'priority'		 => 10,
  'active_callback' => 'futurio_extra_footer_check',
) );

Kirki::add_field( 'futurio_extra', array(
  'type'     => 'select',
  'settings' => 'custom_footer',
  'label'    => esc_attr__( 'Elementor custom footer', 'futurio-extra' ),
  'description' => esc_attr__( 'Note: This will override the footer credits option defined above.', 'futurio-extra' ),
  'section'  => 'code_section',
  'default'  => '',
  'placeholder' => esc_attr__( 'Select an option', 'futurio-extra' ),
  'priority' => 10,
  'choices'  => Kirki_Helper::get_posts(
    array(
    	'posts_per_page' => -1,
    	'post_type'      => 'elementor_library'
    )
  ),
  'active_callback' => 'futurio_extra_check_for_elementor',
) );

function futurio_extra_footer_check() {
  
  if ( futurio_extra_check_for_elementor() && get_theme_mod( 'custom_footer', '' ) != '' ) return false;

  return true;

}