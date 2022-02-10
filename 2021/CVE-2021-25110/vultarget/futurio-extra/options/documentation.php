<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}

Kirki::add_section( 'futurio_docs', array(
	'title'		 => esc_attr__( 'Documentation', 'futurio-extra' ),
	'priority'	 => 2,
) );

Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'docs_links',
	'label'       => esc_attr__( 'Knowledge Base', 'futurio-extra' ),
  'description' => esc_attr__( 'Not sure how something works? Take a peek at the knowledge base and learn.', 'futurio-extra' ),
	'section'     => 'futurio_docs',
	'default'     => '<a href="' . esc_url( 'https://futuriowp.com/docs/futurio/' ) . '" class="button action-btn view-site-library" target="_blank">' . esc_html__( 'Visit Knowledge Base', 'futurio-extra' ) . '</a>',
	'priority'    => 10,
) );