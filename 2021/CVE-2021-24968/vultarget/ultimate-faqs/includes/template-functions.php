<?php

/**
 * Create a shortcode to display multiple FAQs
 * @since 2.0.0
 */
function ewd_ufaq_faqs_shortcode( $atts ) {
	global $ewd_ufaq_controller;

	// Define shortcode attributes
	$faq_atts = array(
		'search_string' 			=> '',
		'post__in' 					=> '',
		'post__in_string' 			=> '',
		'include_tag'				=> '',
		'include_category' 			=> '',
		'exclude_category' 			=> '',
		'include_category_ids' 		=> '',
		'exclude_category_ids' 		=> '',
		'include_category_children'	=> '',
		'no_comments' 				=> '',
		'orderby' 					=> '',
		'order' 					=> '',
		'display_all_answers' 		=> '',
		'faq_page' 					=> 1,
        'post_count' 				=> -1
	);

	if ( empty( $faq_atts['orderby'] ) ) { $faq_atts['orderby'] = $ewd_ufaq_controller->settings->get_setting( 'faq-order-by' ); }

	if ( empty( $faq_atts['order'] ) ) { $faq_atts['order'] = $ewd_ufaq_controller->settings->get_setting( 'faq-order' ); }

	// Create filter so addons can modify the accepted attributes
	$faq_atts = apply_filters( 'ewd_ufaq_faqs_shortcode_atts', $faq_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $faq_atts, $atts );

	$query = new ewdufaqQuery( $args );

	$query->parse_request_args();
	$query->prepare_args();

	// Render faqs
	ewd_ufaq_load_view_files();

	$faqs = new ewdufaqViewFAQs( $args );

	$faqs->set_faqs( $query->get_faqs() );
	
	$ewd_ufaq_controller->shortcode_printing = true;

	$output = $faqs->render();

	$ewd_ufaq_controller->shortcode_printing = false;

	return $output;
}
add_shortcode( 'ultimate-faqs', 'ewd_ufaq_faqs_shortcode' );

/**
 * Create a shortcode to display the FAQ search, plus any matching FAQs
 * @since 2.0.0
 */
function ewd_ufaq_search_faqs_shortcode( $atts ) {
	global $ewd_ufaq_controller;

	if ( ! $ewd_ufaq_controller->permissions->check_permission( 'search' ) ) { return; }

	// Define shortcode attributes
	$faq_atts = array(
		'include_category' 			=> '',
		'exclude_category' 			=> '',
		'show_on_load' 				=> '',
		'no_comments'				=> '',
		'orderby' 					=> '',
		'order' 					=> '',
		'display_all_answers'		=> '',
		'faq_page'					=> 1,
        'post_count' 				=> -1,
	);

	if ( empty( $faq_atts['orderby'] ) ) { $faq_atts['orderby'] = $ewd_ufaq_controller->settings->get_setting( 'faq-order-by' ); }

	if ( empty( $faq_atts['order'] ) ) { $faq_atts['order'] = $ewd_ufaq_controller->settings->get_setting( 'faq-order' ); }

	// Create filter so addons can modify the accepted attributes
	$faq_atts = apply_filters( 'ewd_ufaq_faqs_shortcode_atts', $faq_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $faq_atts, $atts );

	$query = new ewdufaqQuery( $args );

	$query->parse_request_args();
	$query->prepare_args();

	// Render faqs
	ewd_ufaq_load_view_files();

	$faqs = new ewdufaqViewFAQSearch( $args );

	$faqs->set_faqs( $query->get_faqs() );
	
	$ewd_ufaq_controller->shortcode_printing = true;

	$output = $faqs->render();

	$ewd_ufaq_controller->shortcode_printing = false;

	return $output;
}
add_shortcode( 'ultimate-faq-search', 'ewd_ufaq_search_faqs_shortcode' );

/**
 * Create a shortcode to display multiple FAQs
 * @since 2.0.0
 */
function ewd_ufaq_submit_faq_shortcode( $atts ) {
	global $ewd_ufaq_controller;

	if ( ! $ewd_ufaq_controller->permissions->check_permission( 'submit-faq' ) ) { return; }

	// Define shortcode attributes
	$faq_atts = array(
		'success_message' 			=> '',
		'submit_faq_form_title' 	=> '',
		'submit_faq_instructions' 	=> '',
		'submit_text'				=> '',
	);

	// Create filter so addons can modify the accepted attributes
	$faq_atts = apply_filters( 'ewd_ufaq_faqs_shortcode_atts', $faq_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $faq_atts, $atts );

	// Handle FAQ submission
	if ( isset( $_POST['submit_question'] ) ) {

		$args['faq_submitted'] = true;

		$faq = new ewdufaqFAQ();
		$success = $faq->insert_faq();

		if ( $success ) {

			$args['update_message'] = ! empty( $args['success_message'] ) ? $args['success_message'] : $ewd_ufaq_controller->settings->get_setting( 'label-thank-you-submit' );
		}
		else {

			$args['update_message'] = '';

			foreach ( $faq->validation_errors as $validation_error ) {

				$args['update_message'] .= '<br />' . $validation_error['message'];
			}
		}
	}

	// Render faq
	ewd_ufaq_load_view_files();

	$submit_faq = new ewdufaqViewSubmitFAQ( $args );

	$ewd_ufaq_controller->shortcode_printing = true;

	$output = $submit_faq->render();

	$ewd_ufaq_controller->shortcode_printing = false;

	return $output;
}
add_shortcode( 'submit-question', 'ewd_ufaq_submit_faq_shortcode' );

/**
 * Create a shortcode to display a single review
 * @since 2.0.0
 */
function ewd_ufaq_faq_shortcode( $atts ) {
	global $ewd_ufaq_controller;

	// Define shortcode attributes
	$faq_atts = array(
		'faq_id' 		=> 0,
		'faq_name'		=> '',
		'faq_slug'		=> '',
		'no_comments'	=> ''
	);

	// Create filter so addons can modify the accepted attributes
	$faq_atts = apply_filters( 'ewd_ufaq_review_shortcode', $faq_atts );

	$args = shortcode_atts( $faq_atts, $atts );

	$name_array = ! empty( $args['faq_name'] ) ? explode( ',', $args['faq_name'] ) : array();
	$slug_array = ! empty( $args['faq_slug'] ) ? explode( ',', $args['faq_slug'] ) : array();
	$id_array = ! empty( $args['faq_id'] ) ? explode( ',', $args['faq_id'] ) : array();

	$post_id_array = array();

	foreach ( $name_array as $post_name ) {

		$single_post = get_page_by_title( $post_name, 'OBJECT', EWD_UFAQ_FAQ_POST_TYPE );
		$post_id_array[] = $single_post->ID;
	}

	foreach ( $slug_array as $post_slug ) {

		$single_post = get_page_by_path( $post_slug, 'OBJECT', EWD_UFAQ_FAQ_POST_TYPE );
		$post_id_array[] = $single_post->ID;
	}

	foreach ( $id_array as $post_id ) {
		
		$post_id_array[] = (int) $post_id;
	}

	$faq_args = array(
		'post__in' 		=> $post_id_array,
		'no_comments'	=> $args['no_comments']
	);

	$output = ewd_ufaq_faqs_shortcode( $faq_args );

	return $output;
}
add_shortcode( 'select-faq', 'ewd_ufaq_faq_shortcode' );

/**
 * Create a shortcode to display a number of the most popular FAQs
 * @since 2.0.0
 */
function ewd_ufaq_popular_faqs_shortcode( $atts ) {

	$defaults = array(
		'no_comments'	=> '',
		'post_count'	=> 5
	);

	$shortcode_atts = shortcode_atts( $defaults, $atts ) ;

	$shortcode_atts['orderby'] = 'popular';
	$shortcode_atts['order'] = ! empty( $shortcode_atts['order'] ) ? $shortcode_atts['order'] : 'DESC';

	$output = ewd_ufaq_faqs_shortcode( $shortcode_atts );

	return $output;
}
add_shortcode( 'popular-faqs', 'ewd_ufaq_popular_faqs_shortcode' );

/**
 * Create a shortcode to display a number of the most recently published FAQs
 * @since 2.0.0
 */
function ewd_ufaq_recent_faqs_shortcode( $atts ) {

	$defaults = array(
		'no_comments'	=> '',
		'post_count'	=> 5
	);

	$shortcode_atts = shortcode_atts( $defaults, $atts );

	$shortcode_atts['orderby'] = 'date';

	$output = ewd_ufaq_faqs_shortcode( $shortcode_atts );

	return $output;
}
add_shortcode( 'recent-faqs', 'ewd_ufaq_recent_faqs_shortcode' );

/**
 * Create a shortcode to display a number of the top rated FAQs
 * @since 2.0.0
 */
function ewd_ufaq_top_rated_faqs_shortcode( $atts ) {

	$defaults = array(
		'no_comments'	=> '',
		'post_count'	=> 5
	);

	$shortcode_atts = shortcode_atts( $defaults, $atts );

	$shortcode_atts['orderby'] = 'rating';

	$output = ewd_ufaq_faqs_shortcode( $shortcode_atts );

	return $output;
}
add_shortcode( 'top-rated-faqs', 'ewd_ufaq_top_rated_faqs_shortcode' );

function ewd_ufaq_load_view_files() {

	$files = array(
		EWD_UFAQ_PLUGIN_DIR . '/views/Base.class.php' // This will load all default classes
	);

	$files = apply_filters( 'ewd_ufaq_load_view_files', $files );

	foreach( $files as $file ) {
		require_once( $file );
	}

}

if ( ! function_exists( 'ewd_ufaq_validate_captcha' ) ) {
function ewd_ufaq_validate_captcha() {

	$modifiedcode = intval( $_POST['ewd_ufaq_modified_captcha'] );
	$usercode = intval( $_POST['ewd_ufaq_captcha'] );

	$code = ewd_ufaq_decrypt_catpcha_code( $modifiedcode );

	$validate_captcha = $code == $usercode ? 'Yes' : 'No';

	return $validate_captcha;
}
}

if ( ! function_exists( 'ewd_ufaq_encrypt_captcha_code' ) ) {
function ewd_ufaq_encrypt_captcha_code( $code ) {
	
	$modifiedcode = ($code + 5) * 3;

	return $modifiedcode;
}
}

if ( ! function_exists( 'ewd_ufaq_encrypt_captcha_code' ) ) {
function ewd_ufaq_decrypt_catpcha_code( $modifiedcode ) {
	
	$code = ($modifiedcode / 3) - 5;

	return $code;
}
}

if ( ! function_exists( 'ewd_ufaq_decode_infinite_table_setting' ) ) {
function ewd_ufaq_decode_infinite_table_setting( $values ) {
	
	return is_array( json_decode( html_entity_decode( $values ) ) ) ? json_decode( html_entity_decode( $values ) ) : array();
}
}

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