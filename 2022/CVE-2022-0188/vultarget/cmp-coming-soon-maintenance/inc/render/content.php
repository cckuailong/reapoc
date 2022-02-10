<?php

$lang = $this->cmp_get_current_lang() ? '_'.$this->cmp_get_current_lang() : '';
$content = stripslashes( get_option('niteoCS_body'.$lang, '') );
$wpautop = get_option('niteoCS_wpautop', '1');


$html = do_shortcode( $content );

$html = $wpautop == "1" ? wpautop( $html ) : $html;

if ( isset($GLOBALS['wp_embed']) ) {
    $html = $GLOBALS['wp_embed']->autoembed( $html );
}
