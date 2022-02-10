<?php
/**
 * Inline Style generator
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

$isize   = ! empty( $param['iconSize'] ) ? $param['iconSize'] : 24;
$lsize   = ! empty( $param['labelSize'] ) ? $param['labelSize'] : 15;
$iwidth  = $isize * 2;
$iwidth1 = $iwidth - 1;
$iwidth2 = $iwidth + 2;
$iwidth3 = $iwidth + 8;
$misize   = ! empty( $param['mobiliconSize'] ) ? $param['mobiliconSize'] : 24;
$mlsize   = ! empty( $param['mobillabelSize'] ) ? $param['mobillabelSize'] : 15;
$miwidth  = $misize * 2;
$miwidth1 = $miwidth - 1;
$miwidth2 = $miwidth + 2;
$miwidth3 = $miwidth + 8;
$mlsize   = $misize - 9;

$idd    = $id;
$zindex = ! empty( $param['zindex'] ) ? $param['zindex'] : 0;

$css = '.float-menu-' . $id . ' {
z-index: ' . $zindex . ';
}';

$count_i = ( ! empty( $param['menu_1']['item_type'] ) ) ? count( $param['menu_1']['item_type'] ) : '0';
if ( $count_i > 0 ) {
	for ( $i = 0; $i < $count_i; $i ++ ) {
		$css .= '.fm-item-' . $idd . '-' . $i . ' .fm-icon, .fm-item-' . $idd . '-' . $i
		        . ' a:hover .fm-icon, .fm-item-' . $idd . '-' . $i . ' .fm-label{';
		$css .= 'color:' . $param['menu_1']['color'][ $i ] . ';';
		$css .= 'background-color:' . $param['menu_1']['bcolor'][ $i ] . ';';
		$css .= '}';
	}
}
$css           .= '
	.fm-bar.fm-right li,
	.fm-right .fm-mask,
	.fm-hit,
	.fm-icon {
		height: ' . $iwidth . 'px;
	}
	.fm-bar a,
	.fm-icon,
	.fm-round .fm-hit,
	.fm-sub > ul
	{
		width: ' . $iwidth . 'px;
	}
	.fm-icon,
	.fm-label {
		line-height:' . $iwidth . 'px;
	}
	.fm-icon {
		font-size: ' . $isize . 'px;
	}
	.fm-label {
		font-size: ' . $lsize . 'px;
	}
	.fm-icon .fa {
		line-height: ' . $iwidth . 'px !important; 
	}
	
	.fm-label,
	.fm-label-space .fm-hit,
	.fm-sub.fm-side > ul
	{
		left: ' . $iwidth . 'px;
	}
	.fm-right .fm-label,
	.fm-right.fm-label-space .fm-hit,
	.fm-right .fm-sub.fm-side > ul
	{
		right: ' . $iwidth . 'px;
	}
	.fm-round.fm-label-space .fm-hit
	{
		width: ' . $iwidth2 . 'px;
	}
	.fm-sub > ul { 	
		top: ' . $iwidth . 'px;
	}
	.fm-round li,
	.fm-round .fm-mask,
	.fm-round .fm-icon,
	.fm-round a,
	.fm-round .fm-label {
		border-radius: ' . $isize . 'px;
	}
	.fm-connected .fm-label {
		padding: 0 11px 0 ' . $iwidth3 . 'px;
	}
	.fm-right.fm-connected .fm-label {
		padding: 0 ' . $iwidth3 . 'px 0 11px;
	}
	.fm-connected.fm-round .fm-label {
		padding: 0 12px 0 ' . $iwidth1 . 'px;
	}
	.fm-right.fm-connected.fm-round .fm-label {
		padding: 0 ' . $iwidth1 . 'px 0 12px;
	}	
	';
$mobilieScreen = ! empty( $param['mobilieScreen'] ) ? $param['mobilieScreen'] : 480;
$css           .= '
	@media only screen and (max-width: ' . $mobilieScreen . 'px){
		.fm-bar.fm-right li,
		.fm-right .fm-mask,
		.fm-hit,
		.fm-icon {
			height: ' . $miwidth . 'px;
		}
		.fm-bar a,
		.fm-icon,
		.fm-round .fm-hit,
		.fm-sub > ul
		{
			width: ' . $miwidth . 'px;
		}
		.fm-icon,
		.fm-label {
			line-height:' . $miwidth . 'px;
		}
		.fm-icon {
			font-size: ' . $misize . 'px;
		}
		.fm-label {
			font-size: ' . $mlsize . 'px;
		}
		.fm-icon .fa {
			line-height: ' . $miwidth . 'px !important; 
		}
		
		.fm-label,
		.fm-label-space .fm-hit,
		.fm-sub.fm-side > ul
		{
			left: ' . $miwidth . 'px;
		}
		.fm-right .fm-label,
		.fm-right.fm-label-space .fm-hit,
		.fm-right .fm-sub.fm-side > ul
		{
			right: ' . $miwidth . 'px;
		}
		.fm-round.fm-label-space .fm-hit
		{
			width: ' . $miwidth2 . 'px;
		}
		.fm-sub > ul { 	
			top: ' . $miwidth . 'px;
		}
		.fm-round li,
		.fm-round .fm-mask,
		.fm-round .fm-icon,
		.fm-round a,
		.fm-round .fm-label {
			border-radius: ' . $misize . 'px;
		}
		.fm-connected .fm-label {
			padding: 0 11px 0 ' . $miwidth3 . 'px;
		}
		.fm-right.fm-connected .fm-label {
			padding: 0 ' . $miwidth3 . 'px 0 11px;
		}
		.fm-connected.fm-round .fm-label {
			padding: 0 12px 0 ' . $miwidth1 . 'px;
		}
		.fm-right.fm-connected.fm-round .fm-label {
			padding: 0 ' . $miwidth1 . 'px 0 12px;
		}
	}';
if ( ! empty( $param['include_mobile'] ) ) {
	$screen = ! empty( $param['screen'] ) ? $param['screen'] : 480;
	$css    .= '
		@media only screen and (max-width: ' . $screen . 'px){
			.float-menu-' . $idd . ' {
				display:none;
			}
		}';
}
if ( ! empty( $param['include_more_screen'] ) ) {
	$screen_more = ! empty( $param['screen_more'] ) ? $param['screen_more'] : 1200;
	$css         .= '
		@media only screen and (min-width: ' . $screen_more . 'px){
			.float-menu-' . $idd . ' {
				display:none;
			}
		}';
}

$css = trim( preg_replace( '~\s+~s', ' ', $css ) );