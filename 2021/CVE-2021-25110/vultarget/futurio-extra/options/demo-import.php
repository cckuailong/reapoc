<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}

Kirki::add_section( 'one_click_import', array(
	'title'		 => esc_attr__( 'One Click Demo Import', 'futurio-extra' ),
	'priority'	 => 1,
) );

Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'one_click_import_demo',
	'label'       => esc_attr__( 'Futurio ready to import sites', 'futurio-extra' ),
  'description' => esc_attr__( 'Import your favorite site with one click and start your project in style!', 'futurio-extra' ),
	'section'     => 'one_click_import',
	'default'     => '<p><img src="' . esc_url( plugin_dir_url( dirname(__FILE__) ) ) . 'lib/admin/img/futurio-sites.png' .'"></p><a href="' . esc_url( admin_url( 'themes.php?page=futurio-panel-install-demos' ) ) . '" class="button action-btn view-site-library">' . esc_html__( 'See Library', 'futurio-extra' ) . '</a>',
	'priority'    => 10,
) );