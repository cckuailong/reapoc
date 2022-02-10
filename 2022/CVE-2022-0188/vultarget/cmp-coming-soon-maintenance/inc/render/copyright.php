<?php 

$html = '';

$copyright = stripslashes( get_option( 'niteoCS_copyright', 'Made by <a href="https://niteothemes.com">NiteoThemes</a> with love.' ) ); 
$copyright = $this->cmp_wpml_translate_string( $copyright, 'Copyright' );

if ( $copyright ) { 
    
    $allowed_html = array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'target' => array(),
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array(),
    );

    $html = '<p class="copyright">' . wp_kses( $copyright, $allowed_html ) . '</p>';
    
}