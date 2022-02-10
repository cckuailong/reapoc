<?php

/**
 * Create a shortcode to display a product catalog
 * @since 5.0.0
 */
function ewd_upcp_catalog_shortcode( $atts ) {
	global $ewd_upcp_controller;

	// Define shortcode attributes
	$catalog_atts = array(
		'id'				=> 1,
		'excluded_layouts'	=> 'none',
		'starting_layout'	=> 'thumbnail',
		'products_per_page'	=> '',
		'current_page'		=> 1,
		'sidebar'			=> '',
		'overview_mode'		=> '',
		'omit_fields'		=> '',
		'ajax_url'			=> '',
		'category'			=> '',
		'subcategory'		=> '',
		'tags'				=> '',
		'custom_fields'		=> '',
		'prod_name'			=> '',
		'max_price'			=> '',
		'min_price'			=> '',
		'orderby'			=> '',
		'order'				=> '',
	);

	// Create filter so addons can modify the accepted attributes
	$catalog_atts = apply_filters( 'ewd_upcp_catalog_shortcode_atts', $catalog_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $catalog_atts, $atts );

	// Load the view files
	ewd_upcp_load_view_files();

	$catalog = new ewdupcpViewCatalog( $args );

	$catalog->set_request_parameters();

	$output = $catalog->render();

	return $output;
}
add_shortcode( 'product-catalog', 'ewd_upcp_catalog_shortcode' );
add_shortcode( 'product-catalogue', 'ewd_upcp_catalog_shortcode' ); // for backwards compatibility

/**
 * Create a shortcode to display one or more minimal product thumbnails
 * @since 5.0.0
 */
function ewd_upcp_minimal_products_shortcode( $atts ) {
	global $ewd_upcp_controller;

	// Define shortcode attributes
	$catalog_atts = array(
		'catalogue_url'			=> '',
		'product_ids'			=> '',
		'catalogue_id'			=> '',
		'category_id'			=> '',
		'subcategory_id'		=> '',
		'product_count'			=> 3,
		'products_wide'			=> 3,
	);

	// Create filter so addons can modify the accepted attributes
	$catalog_atts = apply_filters( 'ewd_upcp_minimal_products_shortcode_atts', $catalog_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $catalog_atts, $atts );

	// Load the view files
	ewd_upcp_load_view_files();

	if ( ( ! empty( get_query_var( 'single_product' ) ) or ! empty( $_GET['SingleProduct'] ) ) and $catalogue_url == '' ) {

		return ewd_upcp_catalog_shortcode( array() );
	}

	$products = new ewdupcpViewMinimalProducts( $args );

	$output = $products->render();

	return $output;
}
add_shortcode( 'insert-products', 'ewd_upcp_minimal_products_shortcode' );

function ewd_upcp_load_view_files() {

	$files = array(
		EWD_UPCP_PLUGIN_DIR . '/views/Base.class.php' // This will load all default classes
	);

	$files = apply_filters( 'ewd_upcp_load_view_files', $files );

	foreach( $files as $file ) {
		require_once( $file );
	}

}

if ( ! function_exists( 'ewd_upcp_decode_infinite_table_setting' ) ) {
function ewd_upcp_decode_infinite_table_setting( $values ) {
	
	return is_array( json_decode( html_entity_decode( $values ) ) ) ? json_decode( html_entity_decode( $values ) ) : array();
}
}

// add an output buffer layer for the plugin
add_action(	'init', 'ewd_upcp_add_ob_start' );
add_action(	'shutdown', 'ewd_upcp_flush_ob_end' );

// If there's an IPN request, add our setup function to potentially handle it
if ( isset($_POST['ipn_track_id']) ) { add_action( 'init', 'ewd_upcp_setup_paypal_ipn', 11 ); }

/**
 * Sets up the PayPal IPN process
 * @since 5.0.0
 */
if ( !function_exists( 'ewd_upcp_setup_paypal_ipn' ) ) {
function ewd_upcp_setup_paypal_ipn() {
	global $ewd_upcp_controller;

	if ( empty( $ewd_upcp_controller->settings->get_setting( 'allow-order-payments' ) ) ) { return; }
	
	ewd_upcp_handle_paypal_ipn();
}
} // endif;

/**
 * Opens a buffer when handling PayPal IPN requests
 * @since 5.0.0
 */
if ( !function_exists( 'ewd_upcp_add_ob_start' ) ) {
function ewd_upcp_add_ob_start() { 
    ob_start();
}
} // endif;

/**
 * Closes a buffer when handling PayPal IPN requests
 * @since 5.0.0
 */
if ( !function_exists( 'ewd_upcp_flush_ob_end' ) ) {
function ewd_upcp_flush_ob_end() {
    if ( ob_get_length() ) { ob_end_clean(); }
}
} // endif;

if ( ! function_exists( 'ewd_hex_to_rgb' ) ) {
function ewd_hex_to_rgb( $hex ) {

	$hex = str_replace("#", "", $hex);

	// return if the string isn't a color code
	if ( strlen( $hex ) !== 3 and strlen( $hex ) !== 6 ) { return '0,0,0'; }

	if(strlen($hex) == 3) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}

	$rgb = $r . ", " . $g . ", " . $b;
  
	return $rgb;
}
}

if ( ! function_exists( 'ewd_format_classes' ) ) {
function ewd_format_classes( $classes ) {

	if ( count( $classes ) ) {
		return ' class="' . join( ' ', $classes ) . '"';
	}
}
}

if ( ! function_exists( 'ewd_add_frontend_ajax_url' ) ) {
function ewd_add_frontend_ajax_url() { ?>
    
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
<?php }
}

if ( ! function_exists( 'ewd_random_string' ) ) {
function ewd_random_string( $length = 10 ) {

	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';

    for ( $i = 0; $i < $length; $i++ ) {

        $randstring .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
    }

    return $randstring;
}
}