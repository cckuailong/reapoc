<?php
$html = '';
$form_type = get_option('niteoCS_contact_form_type', 'cf7');

if ( $form_type == 'disabled' ) {
    return false;
}

$form_id = get_option('niteoCS_contact_form_id', '');

switch ( $form_type ) {
    case 'cf7':
        $replace  = array('<p>', '</p>' );
        $html =  str_replace( $replace, '', do_shortcode('[contact-form-7 id='.$form_id.']') ) ; 
        break;
    
    default:
        $html = '';
        break;
}