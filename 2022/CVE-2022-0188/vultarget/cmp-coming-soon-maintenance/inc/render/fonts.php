<?php 
$custom = '';
$google_fonts = '';
$custom_font = '';
$heading = null;
$content = null;
// get google fonts html
if ( $heading_font['variant'] !== 'Not Applicable' || $content_font['variant'] !== 'Not Applicable' ) {
    
    // get fonts subset
    $google_fonts = $this->cmp_get_google_fonts();
    $heading_break = FALSE;
    $content_break = FALSE;

    foreach ( $google_fonts as $font => $val ) {

        if ( $val['text'] == $heading_font['family'] ) {
            $heading_subsets =  isset($val['subset']) ? $val['subset'] : array();
            $heading_break = TRUE;
        }

        if ( $val['text'] == $content_font['family'] ) {
            $content_subsets =  $val['subset'];
            $content_break = TRUE;
        }

        if ( $heading_break === TRUE && $content_break === TRUE ) {
            break;
        }
    }

    if ( $heading_subsets === null || $content_subsets === null ) {
        $subset = ($heading_subsets === null) ? $content_subsets : $heading_subsets;
        $subset = ($content_subsets === null) ? $heading_subsets : $content_subsets;
        
    } else {
        $subset = array_unique( array_merge( $heading_subsets, $content_subsets ) );
    }

    

    if ( $heading_font['variant'] !== 'Not Applicable' ) {
        $heading = esc_attr( str_replace(' ', '+', $heading_font['family']) ) .':'. esc_attr(str_replace('italic', 'i', $heading_font['variant'] ));
    }

    if ( $content_font['variant'] !== 'Not Applicable' ) {
        $content = esc_attr( str_replace(' ', '+', $content_font['family']) ) .':400,700,'. esc_attr(str_replace('italic', '', $content_font['variant'] ));
    }

    $separator = ( $heading !== null && $content !== null ) ? '%7C' : '';


    $google_fonts = '<link href="https://fonts.googleapis.com/css?family='. $heading . $separator . $content .'&amp;subset=' . implode(',', $subset) . '" rel="stylesheet">';
    
}

// get custom font html
if ( $heading_font['variant'] === 'Not Applicable' || $content_font['variant'] === 'Not Applicable' ) {

    $custom_fonts = json_decode(get_option('niteoCS_custom_fonts'), true);
    $custom_font = '';

    foreach ( $custom_fonts as $custom ) {

        if ( $custom['id'] === $heading_font['family'] ) {
            if ( is_array($custom['ids']) ) {
                $custom_font .= $this->cmp_get_font_src( $heading_font['family'], $custom['ids'] );
            }
        }
        
        if ( $custom['id'] === $content_font['family'] ) {
                if ( is_array($custom['ids']) ) {
                $custom_font .= $this->cmp_get_font_src( $content_font['family'], $custom['ids'] );
            }
        }
    }
}