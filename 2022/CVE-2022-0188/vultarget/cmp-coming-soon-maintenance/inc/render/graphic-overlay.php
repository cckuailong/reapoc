<?php
$overlay = get_option('niteoCS_overlay', 'solid-color');
$opacity = get_option('niteoCS_overlay[opacity]', '0.4');
$color = get_option('niteoCS_overlay[color]', '#0a0a0a');   
$html = '';

switch ( $overlay ) {
    case 'solid-color':     
        $html = '<div class="background-overlay solid-color" style="background-color:'.esc_attr( $color ).';opacity:'.esc_attr( $opacity ).'"></div>';
        break;

    case 'gradient':
        $overlay_gradient  = get_option('niteoCS_overlay[gradient]', '#d53369:#cbad6d');

        if ( $overlay_gradient == 'custom' ) {
            $gradient_one = get_option('niteoCS_overlay[gradient_one]', '#e5e5e5');
            $gradient_two = get_option('niteoCS_overlay[gradient_two]', '#e5e5e5');
            
        } else {
            $gradient = explode(":", $overlay_gradient);
            $gradient_one = $gradient[0];
            $gradient_two = $gradient[1]; 
        }

        
        $html = '<div class="background-overlay gradient" style="background:-moz-linear-gradient(-45deg, '.esc_attr( $gradient_one ).' 0%, '.esc_attr( $gradient_two ).' 100%);background:-webkit-linear-gradient(-45deg, '.esc_attr( $gradient_one ).' 0%, '.esc_attr( $gradient_two ).' 100%);background:linear-gradient(135deg,'.esc_attr( $gradient_one ).' 0%, '.esc_attr( $gradient_two ).' 100%);opacity:'.esc_attr( $opacity ).'"></div>';
        break;
    
    default:
        break;
}