<?php
/**
 * Parameters for Main page setting (Add New)
 *
 * @package     Wow_Plugin
 * @subpackage  Add/Settings/Main
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// create array of the icons
include_once( 'icons.php' );
$icons_new = array();
foreach ( $icons as $key => $value ) {
	$icons_new[ $value ] = $value;
}


$content = array(
	'name' => 'param[content]',
	'id'   => 'content',
	'type' => 'editor',
	'val'  => isset( $param['content'] ) ? $param['content'] : '',
);

$tax_args   = array(
	'public'   => true,
	'_builtin' => false,
);
$output     = 'names';
$operator   = 'and';
$taxonomies = get_taxonomies( $tax_args, $output, $operator );

$show_option = array(
	'all'        => esc_attr__( 'All posts and pages', $this->plugin['text'] ),
	'onlypost'   => esc_attr__( 'All posts', $this->plugin['text'] ),
	'onlypage'   => esc_attr__( 'All pages', $this->plugin['text'] ),
	'posts'      => esc_attr__( 'Posts with certain IDs', $this->plugin['text'] ),
	'pages'      => esc_attr__( 'Pages with certain IDs', $this->plugin['text'] ),
	'postsincat' => esc_attr__( 'Posts in Categorys with IDs', $this->plugin['text'] ),
	'expost'     => esc_attr__( 'All posts. except...', $this->plugin['text'] ),
	'expage'     => esc_attr__( 'All pages, except...', $this->plugin['text'] ),
	'shortecode' => esc_attr__( 'Where shortcode is inserted', $this->plugin['text'] ),
);
if ( $taxonomies ) {
	$show_option['taxonomy'] = esc_attr__( 'Taxonomy', $this->plugin['text'] );
}

$show = array(
	'id'     => 'show',
	'name'   => 'param[show]',
	'type'   => 'select',
	'val'    => isset( $param['show'] ) ? $param['show'] : 'shortecode',
	'option' => $show_option,
	'func'   => 'showchange(this);',
	'sep'    => '<p/>',
);

$show_help = array(
	'text' => esc_attr__( 'Choose a condition to target to specific content.', $this->plugin['text'] ),
);

// Taxonomy
$taxonomy_option = array();
if ( $taxonomies ) {
	foreach ( $taxonomies as $taxonomy ) {
		$taxonomy_option[ $taxonomy ] = $taxonomy;
	}
}

$taxonomy = array(
	'id'     => 'taxonomy',
	'name'   => 'param[taxonomy]',
	'type'   => 'select',
	'val'    => isset( $param['taxonomy'] ) ? $param['taxonomy'] : '',
	'option' => $taxonomy_option,
	'sep'    => '<p/>',
);

// Content ID'sa
$id_post = array(
	'id'     => 'id_post',
	'name'   => 'param[id_post]',
	'type'   => 'text',
	'val'    => isset( $param['id_post'] ) ? $param['id_post'] : '',
	'option' => array(
		'placeholder' => esc_attr__( 'Enter IDs, separated by comma.', $this->plugin['text'] ),
	),
	'sep'    => '<p/>',
);
