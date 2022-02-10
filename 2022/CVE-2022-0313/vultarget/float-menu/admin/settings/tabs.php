<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Tabs menu for Settings
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

$tab_elements = array(
	'main'  => esc_attr__( 'Main', $this->plugin['text'] ),
	'menu'  => esc_attr__( 'Menu', $this->plugin['text'] ),
	'popup' => esc_attr__( 'Popup', $this->plugin['text'] ),
);

$tab_li      = '';
$tab_content = '';
$i           = '1';
foreach ( $tab_elements as $key => $val ) {
//	$active      = ( $i == 1 ) ? 'is-active' : '';
//	$tab_li      .= '<li class="' . esc_attr( $active ) . ' is-marginless" data-tab="' . absint( $i ) . '"><a>' . esc_attr( $val ) . '</a></li>';
//	$tab_content .= '<div class="' . esc_attr( $active ) . ' tab-content" data-content="' . absint( $i ) . '">';
//	ob_start();
//	include( $key . '.php' );
//	$tab_content .= ob_get_contents();
//	ob_end_clean();
//	$tab_content .= '</div>';
//	$i ++;
}
?>

<div class="tabs is-centered" id="tab">
    <ul>
        <li class="is-active" data-tab="1"><a><?php esc_html_e( 'Settings', $this->plugin['text'] );?></a></li>
        <li data-tab="2"><a><?php esc_html_e( 'Menu', $this->plugin['text'] );?></a></li>
        <li data-tab="3"><a><?php esc_html_e( 'Popup', $this->plugin['text'] );?></a></li>
    </ul>
</div>
<div id="tab-content" class="inside">
    <div class="is-active tab-content" data-content="1">
        <?php include_once ('main.php');?>
    </div>
</div>