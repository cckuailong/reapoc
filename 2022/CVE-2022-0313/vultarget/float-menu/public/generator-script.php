<?php
/**
 * Inline Script generator
 *
 * @package     Wow_Plugin
 * @author      Dmytro Lobov <d@dayes.dev>
 * @copyright   2019 Wow-Company
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$mobile_screen            = ! empty( $param['mobile_screen'] ) ? $param['mobile_screen'] : 768;
$mobile_rules             = ! empty( $param['mobile_rules'] ) ? 'true' : 'false';

$js .= 'jQuery(document).ready(function() {';
$js .= '
	jQuery(".float-menu-' . $id . '").floatingMenu({
		position: ["' . $param['menu'] . '", "center"],
		offset: [0, 0],
		shape: "square",
		sideSpace: ' . $param['sideSpace'] .',
		buttonSpace: ' . $param['buttonSpace'] . ',
		labelSpace: ' . $param['labelSpace'] . ',
		labelConnected: ' . $param['labelConnected'] . ',
		labelEffect: "fade",
		labelAnim: [' . $param['labelSpeed'] . ', "easeOutQuad"],
		color: "default",
		overColor: "default",
		labelsOn: ' . $param['labelsOn'] . ',
		mobileEnable: ' . $mobile_rules . ',
		mobileScreen: ' . $mobile_screen . ',
	});
';
$js .= '});';

$js = trim( preg_replace( '~\s+~s', ' ', $js ) );