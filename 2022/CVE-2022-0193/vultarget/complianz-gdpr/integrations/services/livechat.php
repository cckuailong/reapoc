<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_livechat_script' );
function cmplz_livechat_script( $tags ) {
	$tags[] = 'cdn.livechatinc.com/tracking.js';

	return $tags;
}


