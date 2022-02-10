<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Tergeting settings
 *
 * @package     Wow_Plugin
 * @subpackage  Settings
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


//region Devices
$screen_more = array(
	'label'    => esc_attr__( 'Don\'t show on screens more', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[screen_more]',
		'id'    => 'screen_more',
		'value' => isset( $param['screen_more'] ) ? $param['screen_more'] : '1024',
		'min'   => '0',
		'step'  => '1',
	],
	'checkbox' => [
		'name'  => 'param[include_more_screen]',
		'class' => 'checkLabel',
		'id'    => 'include_more_screen',
		'value' => isset( $param['include_more_screen'] ) ? $param['include_more_screen'] : 0,
	],
	'addon'    => [
		'unit' => 'px',
	],
	'tooltip'     => esc_attr__( 'Specify the window breakpoint when the popup will be shown', 'modal-window' ),
);

$mobil_show = ! empty( $param['mobil_show'] ) ? $param['mobil_show'] : 0;

$screen = array(
	'label'    => esc_attr__( 'Don\'t show on screens less', 'modal-window' ),
	'attr'     => [
		'name'  => 'param[screen]',
		'id'    => 'screen',
		'value' => isset( $param['screen'] ) ? $param['screen'] : '480',
		'min'   => '0',
		'step'  => '0.01',
	],
	'checkbox' => [
		'name'  => 'param[include_mobile]',
		'class' => 'checkLabel',
		'id'    => 'include_mobile',
		'value' => isset( $param['include_mobile'] ) ? $param['include_mobile'] : $mobil_show,
	],
	'addon'    => [
		'unit' => 'px',
	],
	'tooltip'     => esc_attr__( 'Specify the window breakpoint ( min width)', 'modal-window' ),
);
//endregion

//region Display
$tax_args   = array(
	'public'   => true,
	'_builtin' => false,
);
$output     = 'names';
$operator   = 'and';
$taxonomies = get_taxonomies( $tax_args, $output, $operator );

$show_option = array(
	'all'        => esc_attr__( 'All posts and pages', 'modal-window' ),
	'shortecode' => esc_attr__( 'Where shortcode is inserted', 'modal-window' ),
);
if ( $taxonomies ) {
	$show_option['taxonomy'] = esc_attr__( 'Taxonomy', 'modal-window' );
}

$show = array(
	'label'   => esc_attr__( 'Display on', 'modal-window' ),
	'attr'    => [
		'name'  => 'param[show]',
		'id'    => 'show',
		'value' => isset( $param['show'] ) ? $param['show'] : 'all',
	],
	'options' => $show_option,
	'tooltip'    => esc_attr__( 'Choose a condition to target to specific content.', 'modal-window' ),
	'icon'    => '',
	'func'    => 'showChange()',
);
//endregion





