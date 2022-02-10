<?php
if (! defined ( 'ABSPATH' ))
	exit (); // Exit if accessed directly

/* ----------------------------------------------------------------------------------- */
	/* Define the URL and DIR path */
	/* ----------------------------------------------------------------------------------- */

define ( 'thumbs_rating_url', plugins_url () . "/" . dirname ( plugin_basename ( __FILE__ ) ) );
define ( 'thumbs_rating_path', WP_PLUGIN_DIR . "/" . dirname ( plugin_basename ( __FILE__ ) ) );

/* ----------------------------------------------------------------------------------- */
/* Init */
/* Localization */
/* ----------------------------------------------------------------------------------- */

if (! function_exists ( 'thumbs_rating_init' )) :
	function thumbs_rating_init() {
		load_plugin_textdomain ( 'thumbs-rating', false, basename ( dirname ( __FILE__ ) ) . '/languages' );
	}
	add_action ( 'plugins_loaded', 'thumbs_rating_init' );


endif;

/* ----------------------------------------------------------------------------------- */
/* Encue the Scripts for the Ajax call */
/* ----------------------------------------------------------------------------------- */

if (! function_exists ( 'thumbs_rating_scripts' )) :
	function thumbs_rating_scripts() {
		wp_enqueue_script ( 'thumbs_rating_scripts', thumbs_rating_url . '/js/general.js', array (
				'jquery'
		), '4.0.1' );
		wp_localize_script ( 'thumbs_rating_scripts', 'thumbs_rating_ajax', array (
				'ajax_url' => admin_url ( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce ( 'thumbs-rating-nonce' )
		) );
	}
	add_action ( 'wp_enqueue_scripts', 'thumbs_rating_scripts' );


endif;

/* ----------------------------------------------------------------------------------- */
/* Encue the Styles for the Thumbs up/down */
/* ----------------------------------------------------------------------------------- */

if (! function_exists ( 'thumbs_rating_styles' )) :
	function thumbs_rating_styles() {
		wp_register_style ( "thumbs_rating_styles", thumbs_rating_url . '/css/style.css', "", "1.0.0" );
		wp_enqueue_style ( 'thumbs_rating_styles' );
	}
	add_action ( 'wp_enqueue_scripts', 'thumbs_rating_styles' );


endif;

/* ----------------------------------------------------------------------------------- */
/* Add the thumbs up/down links to the content */
/* ----------------------------------------------------------------------------------- */

if (! function_exists ( 'thumbs_rating_getlink' )) :
	function thumbs_rating_getlink($post_ID = '', $type_of_vote = '', $show_div_wrap = true, $likelabel = 'Like' ) {
		// Sanatize params
		$post_ID = intval ( sanitize_text_field ( $post_ID ) );
		$type_of_vote = intval ( sanitize_text_field ( $type_of_vote ) );

		$thumbs_rating_link = "";

		if ($post_ID == '')
			$post_ID = get_the_ID ();

		$thumbs_rating_up_count = get_post_meta ( $post_ID, '_thumbs_rating_up', true ) != '' ? get_post_meta ( $post_ID, '_thumbs_rating_up', true ) : '0';
		$thumbs_rating_down_count = get_post_meta ( $post_ID, '_thumbs_rating_down', true ) != '' ? get_post_meta ( $post_ID, '_thumbs_rating_down', true ) : '0';

		$voted  = isset( $type_of_vote ) && $type_of_vote == 2 ? false : true;

		$link_button = '<span class="likebtn-wrapper lb-loaded lb-style-white lb-popup-position-top lb-popup-style-light lb-unlike-not-allowed">';
		$link_button .= '<span class="likebtn-button lb-like thumbs-rating-up">';
		$link_button .= '<span onclick="thumbs_rating_vote(this, ' . $post_ID . ', 1, \'' . $likelabel . '\');" class="lb-a">';
		$link_button .= '<span class="likebtn-icon lb-like-icon">&nbsp;</span>';
		$link_button .= '<span class="likebtn-label lb-like-label">' . $likelabel . '</span>';
		$link_button .= '</span>';
		$link_button .= '<span class="lb-count" data-count="'. $thumbs_rating_up_count . '" style="display: inline-block;">'. $thumbs_rating_up_count . '</span>';
		$link_button .= '</span>';

		//<!-- <span class="likebtn-button lb-dislike thumbs-rating-down">
		//						<span onclick="thumbs_rating_vote(this, ' . $post_ID . ', 2);" class="lb-a">
		//							<span class="likebtn-icon lb-dislike-icon">&nbsp;</span>
		//						</span>
		//						<span class="lb-count" data-count="'. $thumbs_rating_down_count . '" style="display: inline-block;">'. $thumbs_rating_down_count . '</span>
		//					</span> -->
		//				</span>';

		if ( $show_div_wrap ) {
			$thumbs_rating_link = '<div class="thumbs-rating-container" id="thumbs-rating-' . $post_ID . '" data-content-id="' . $post_ID . '">';
		}

		$thumbs_rating_link .= $link_button;

		if ( $show_div_wrap ) {
			$thumbs_rating_link .= '</div>';
		}

		return $thumbs_rating_link;
	}


endif;

/* ----------------------------------------------------------------------------------- */
/* Handle the Ajax request to vote up or down */
/* ----------------------------------------------------------------------------------- */

if (! function_exists ( 'thumbs_rating_add_vote_callback' )) :
	function thumbs_rating_add_vote_callback() {

		// Check the nonce - security
		check_ajax_referer ( 'thumbs-rating-nonce', 'nonce' );

		global $wpdb;

		// Get the POST values

		$post_ID = intval ( $_POST ['postid'] );
		$type_of_vote = intval ( $_POST ['type'] );
		$selection = '';
		$likelabel = $_POST['likelabel'];

		if ( isset( $_POST['selection'] ) ) {
			$selection = intval ( $_POST ['selection'] );
		}

		$alternate = '';
		if ( isset( $_POST ['alternate'] ) ) {
			$alternate = intval ( $_POST ['alternate'] );
		}

		$serveraddress = $_SERVER['REMOTE_ADDR'];
		$post_voter_ips = get_option ( 'link_library_voter_ips' );
		$current_ip_votes = array();
		if ( empty( $post_voter_ips ) ) {
			$post_voter_ips = array();
		}

		if ( isset( $post_voter_ips[$serveraddress] ) ) {
			$current_ip_votes = $post_voter_ips[$serveraddress];
		}

		$user_id = get_current_user_id();
		$post_voter_users = get_option ( 'link_library_voter_users' );
		$current_user_votes = array();
		if ( 0 != $user_id ) {
			if ( empty( $post_voter_users ) ) {
				$post_voter_users = array();
			}

			if ( isset( $post_voter_users[$user_id] ) ) {
				$current_user_votes = $post_voter_users[$user_id];
			}
		}

		// Check the type and retrieve the meta values

		if ($type_of_vote == 1) {

			if ( !in_array( $post_ID, $current_ip_votes ) && $selection == 1 ) {
				$current_ip_votes[] = $post_ID;

				if ( 0 != $user_id ) {
					$current_user_votes[] = $post_ID;
				}
			} elseif ( in_array( $post_ID, $current_ip_votes ) && $selection == 0) {
				$key = array_search( $post_ID, $current_ip_votes );
				if ( $key !== false ) {
					unset( $current_ip_votes[$key] );
				}

				if ( 0 != $user_id ) {
					$user_key = array_search( $post_ID, $current_user_votes );
					if ( $user_key !== false ) {
						unset( $current_user_votes[$user_key] );
					}
				}
			}

			if ( empty( $current_ip_votes ) ) {
				unset( $post_voter_ips[$serveraddress] );
			} else {
				$post_voter_ips[$serveraddress] = $current_ip_votes;
			}

			if ( 0 != $user_id ) {
				if ( empty( $current_user_votes ) ) {
					unset( $post_voter_users[$user_id] );
				} else {
					$post_voter_users[$user_id] = $current_user_votes;
				}
			}

			update_option ( 'link_library_voter_ips', $post_voter_ips );
			update_option ( 'link_library_voter_users', $post_voter_users );

			$other_meta_name = '_thumbs_rating_down';
			$meta_name = "_thumbs_rating_up";
		} elseif ($type_of_vote == 2) {
			$other_meta_name = "_thumbs_rating_up";
			$meta_name = "_thumbs_rating_down";
		}

		if ($selection == 0){
			$thumbs_rating_count = get_post_meta ( $post_ID, $meta_name, true ) != '' ? get_post_meta ( $post_ID, $meta_name, true ) : '0';
			$thumbs_rating_count = $thumbs_rating_count - 1;

		} elseif ($selection == 1) {
			$thumbs_rating_count = get_post_meta ( $post_ID, $meta_name, true ) != '' ? get_post_meta ( $post_ID, $meta_name, true ) : '0';
			$thumbs_rating_count = $thumbs_rating_count + 1;

			if ($alternate == 1){
				$thumbs_alternate_rating_count = get_post_meta ( $post_ID, $other_meta_name, true ) != '' ? get_post_meta ( $post_ID, $other_meta_name, true ) : '0';
				$thumbs_alternate_rating_count = $thumbs_alternate_rating_count - 1;
				update_post_meta ( $post_ID, $other_meta_name, $thumbs_alternate_rating_count );
			}
		}

		update_post_meta ( $post_ID, $meta_name, $thumbs_rating_count );

		// Retrieve the meta value from the DB
		$results = thumbs_rating_getlink ( $post_ID, $type_of_vote, false, $likelabel );

		die ( $results ) ;
	}

	add_action ( 'wp_ajax_thumbs_rating_add_vote', 'thumbs_rating_add_vote_callback' );
	add_action ( 'wp_ajax_nopriv_thumbs_rating_add_vote', 'thumbs_rating_add_vote_callback' );


endif;

/* ----------------------------------------------------------------------------------- */
/* Print our JavaScript function in the footer. We want to check if the user has already voted on the page load */
/* ----------------------------------------------------------------------------------- */

if (! function_exists ( 'thumbs_rating_check' )) :
	function thumbs_rating_check() {

		$serveraddress = $_SERVER['REMOTE_ADDR'];
		$post_voter_ips = get_option ( 'link_library_voter_ips' );
		$current_ip_votes = array();

		if ( isset( $post_voter_ips[$serveraddress] ) ) {
			$current_ip_votes = $post_voter_ips[$serveraddress];
		}

		$user_id = get_current_user_id();
		$current_user_votes = array();
		if ( 0 != $user_id ) {
			$post_voter_users = get_option ( 'link_library_voter_users' );
			if ( !empty( $post_voter_users ) && isset( $post_voter_users[$user_id] ) ) {
				$current_user_votes = $post_voter_users[$user_id];
			}
		}
		$json_array_users = json_encode( $current_user_votes );

		$output = "<script>\n";
		$output .= "\tjQuery(document).ready(function() {\n";
		$output .= "\t\tvar current_ip_votes = [";

		$number_of_votes_ip = count( $current_ip_votes );
		$current_vote_ip = 1;
		foreach( $current_ip_votes as $current_ip_vote ) {
			$output .= $current_ip_vote;
			if ( $current_vote_ip != $number_of_votes_ip ) {
				$output .= ',';
				$current_vote_ip++;
			}
		}

		$output .= "];\n";
		$output .= "\t\tvar current_user_votes = [";

		$number_of_votes_user = count( $current_user_votes );
		$current_vote_user = 1;
		foreach( $current_user_votes as $current_user_vote ) {
			$output .= $current_user_vote;
			if ( $current_vote_user != $number_of_votes_user ) {
				$output .= ',';
				$current_vote_user++;
			}
		}

		$output .= "];\n";

		$output .= "\t\tupdateCount();\n";

		$output .= "\t\tfunction updateCount () {\n";
		$output .= "\t\t\tjQuery( '.thumbs-rating-container' ).each( function() {\n";
		$output .= "\t\t\t\tvar icon_container = jQuery( this );\n";
		$output .= "\t\t\t\tvar content_id  = icon_container.data('content-id');\n";
		$output .= "\t\t\t\tvar vote_count  = icon_container.find('.lb-count').attr('data-count');\n";
		$output .= "\t\t\t\tif ( ( current_ip_votes.includes( content_id ) || current_user_votes.includes( content_id ) ) && vote_count > 0 ) {\n";
		$output .= "\t\t\t\t\ticon_container.find('.likebtn-button.lb-like').addClass('thumbs-rating-voted lb-voted');\n";
		$output .= "\t\t\t\t}\n";
		$output .= "\t\t\t});\n";
		$output .= "\t\t};\n";
		$output .= "});\n";
		$output .= "</script>\n";

		return $output;
	}

endif;
