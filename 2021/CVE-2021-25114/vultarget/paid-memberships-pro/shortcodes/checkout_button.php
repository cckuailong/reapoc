<?php
/*
	Shortcode to show a link/button linking to the checkout page for a specific level
*/
function pmpro_checkout_button_shortcode($atts, $content=null, $code="")
{
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_checkout_button level="3"]

	extract(shortcode_atts(array(
		'level' => NULL,
		'text' => NULL,
		'class' => NULL
	), $atts));
	
	ob_start(); ?>
 	<span class="<?php pmpro_get_element_class( 'span_pmpro_checkout_button' ); ?>">
 		<?php echo pmpro_getCheckoutButton($level, $text, $class); ?>
 	</span>
 	<?php
 	return ob_get_clean();
}
add_shortcode("pmpro_button", "pmpro_checkout_button_shortcode");
add_shortcode("pmpro_checkout_button", "pmpro_checkout_button_shortcode");