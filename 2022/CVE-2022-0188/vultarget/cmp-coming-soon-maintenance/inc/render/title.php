<?php 

global $allowedposttags;

$title = stripslashes( get_option('niteoCS_body_title', 'SOMETHING IS HAPPENING!') ); 

$title = $this->cmp_wpml_translate_string( $title, 'Title' );

// wrap text between stars in title in span for future formatting
$title_array = explode('*', $title);

if ( count( $title_array ) == 3 ) {
    $title = $title_array[0] !== '' ? '<span class="cmp-title light">' . $title_array[0] . '</span>' : '';
    $title .= $title_array[1] !== '' ? '<span class="cmp-title bold">' . $title_array[1] . '</span>' : '';
    $title .= $title_array[2] !== '' ? '<span class="cmp-title light">' . $title_array[2] . '</span>' : '';
}

$html = ( $title == '' ) ? '' : '<h2 class="cmp-title animated '. $class .'">' . wp_kses( $title, $allowedposttags ) . '</h2>';

?>
