<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

/**
 *
 * Render the output of the link-library-search shortcode
 *
 * @param $libraryoptions   Selected library settings array
 *
 * @return                  List of categories output for browser
 */

function RenderLinkLibrarySearchForm( $libraryoptions ) {

	$libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
	extract( $libraryoptions );

	$output = '<form method="get" id="llsearch"';

	if ( !empty( $searchresultsaddress ) ) {
		$output .= ' action="' . $searchresultsaddress . '"';
	}

	$output .= ">\n";
	$output .= "<div>\n";
	$output .= "<input type='text' onfocus=\"this.value=''\" value='";

	if ( $searchtextinsearchbox && isset( $_GET['searchll'] ) && !empty( $_GET['searchll'] ) ) {
		$output .= $_GET['searchll'];
	} else {
		$output .= $searchfieldtext;
	}

	$output .= "' name='searchll' id='searchll' />";

	if ( isset( $_GET['page_id'] ) && !empty( $_GET['page_id'] ) ) {
		$output .= '<input type="hidden" name="page_id" value="' . $_GET['page_id'] . '" />';
	} elseif ( isset( $_GET['p'] ) && !empty( $_GET['p'] ) ) {
		$output .= '<input type="hidden" name="p" value="' . $_GET['p'] . '" />';
	}

	if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
		$output .= '<input type="hidden" name="link_price" value="' . $_GET['link_price'] . '" />';
	}

	if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
		$output .= '<input type="hidden" name="link_tags" value="' . $_GET['link_tags'] . '" />';
	}

	$output .= "<input type='submit' id='searchbutton' value='" . $searchlabel . "' />";

	if ( $showsearchreset ) {
		$output .= "<input type='submit' id='resetbutton' value='" . __( 'Reset search', 'link-library' ) . "' />";
	}

	$output .= "</div>\n";
	$output .= "</form>\n\n";

	$resetaddress = get_permalink();
	if ( !empty( $searchresultsaddress ) ) {
		$resetaddress = $searchresultsaddress;
	}

	$output .= "<script type='text/javascript'>\n";
	$output .= "jQuery(document).ready(function () {\n";
	$output .= "\tjQuery('#searchbutton').click(function () {\n";
	$output .= "\t\tif (jQuery('#searchll').val() == '" . $searchfieldtext . "') {\n";
	$output .= "\t\t\treturn false;\n";
	$output .= "\t\t}\n";
	$output .= "\t\telse {\n";
	$output .= "\t\t\tjQuery('#llsearch').submit();\n";
	$output .= "\t\t}\n";
	$output .= "\t});\n";
	$output .= "\tjQuery('#resetbutton').click(function () {\n";
	$output .= "\t\twindow.location.href = '" . $resetaddress . "';\n";
	$output .= "\t\treturn false;\n";
	$output .= "\t});\n";
	$output .= "});\n";
	$output .= "</script>";

	return $output;
}
