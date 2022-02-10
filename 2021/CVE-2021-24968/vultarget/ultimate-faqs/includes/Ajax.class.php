<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewdufaqAJAX' ) ) {
	/**
	 * Class to handle AJAX interactions for Ultimate FAQs
	 *
	 * @since 2.0.0
	 */
	class ewdufaqAJAX {

		public function __construct() { 

			add_action( 'wp_ajax_ewd_ufaq_search', array( $this, 'return_search_results' ) );
			add_action( 'wp_ajax_nopriv_ewd_ufaq_search', array( $this, 'return_search_results' ) );

			add_action( 'wp_ajax_ewd_ufaq_record_view', array( $this, 'record_view' ) );
			add_action( 'wp_ajax_nopriv_ewd_ufaq_record_view', array( $this, 'record_view' ) );

			add_action( 'wp_ajax_ewd_ufaq_update_rating', array( $this, 'update_rating' ) );
			add_action( 'wp_ajax_nopriv_ewd_ufaq_update_rating', array( $this, 'update_rating' ) );

			add_action( 'wp_ajax_ewd_ufaq_update_order', array( $this, 'update_order' ) );
		}

		/**
		 * Get the results of the FAQ search
		 * @since 2.0.0
		 */
		public function return_search_results() {
			global $ewd_ufaq_controller;
			
			$faq_atts = array(
				'is_search'					=> 1,
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

			$query = new ewdufaqQuery( $faq_atts );

			$query->parse_request_args();
			$query->prepare_args();

			$faqs = new ewdufaqViewFAQs( $faq_atts );

			$faqs->set_request_parameters();

			$faqs->set_faqs( $query->get_faqs() );

			$faqs->set_faqs_options();

			$faqs->create_faq_data();

			ob_start();

			$ewd_ufaq_controller->shortcode_printing = true;

			$faqs->print_faqs();

			$ewd_ufaq_controller->shortcode_printing = false;

			$output = ob_get_clean();

			$output = $output ? $output : sprintf( $ewd_ufaq_controller->settings->get_setting( 'label-no-results-found' ), $query->args['search_string'] );

			wp_send_json_success(
				array(
					'output' 		=> $output,
					'faq_count' 	=> $faqs->faq_count,
					'max_page'		=> $faqs->max_page,
					'request_count'	=> intval( $_POST['request_count'] )
				)
			);

		    die();
		}

		/**
		 * Records the number of time an FAQ post is opened
		 * @since 2.0.0
		 */
		public function record_view() {
			global $wpdb;

    		$post_id = intval( $_POST['post_id'] );

    		$meta_id = $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id=%d AND meta_key='ufaq_view_count'", $post_id ) );
    
    		if ( $meta_id != '' and $meta_id != 0 ) { $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_value=meta_value+1 WHERE post_id=%d AND meta_key='ufaq_view_count'", $post_id ) ); }
    		else { $wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value) VALUES (%d,'ufaq_view_count','1')", $post_id ) ); }

    		die();
		}

		/**
		 * Update the up or down rating for an FAQ
		 * @since 2.0.0
		 */
		public function update_rating() {

			$faq_id = is_numeric( $_POST['faq_id'] ) ? intval( $_POST['faq_id'] ) : 0;
    		$vote_type = sanitize_text_field( $_POST['vote_type'] );

    		if ( $vote_type == 'up' ) { 

    		    $up_votes = get_post_meta( $faq_id, 'FAQ_Up_Votes', true );
    		    update_post_meta( $faq_id, 'FAQ_Up_Votes', $up_votes + 1 );

    		    $total_score = get_post_meta( $faq_id, 'FAQ_Total_Score', true );
    		    update_post_meta( $faq_id, 'FAQ_Total_Score', $total_score + 1 );
    		}
    		if ( $vote_type == 'down' ) {

    		    $down_votes = get_post_meta( $faq_id, 'FAQ_Down_Votes', true );
    		    update_post_meta( $faq_id, 'FAQ_Down_Votes', $down_votes + 1 );

    		    $total_score = get_post_meta( $faq_id, 'FAQ_Total_Score', true );
    		    update_post_meta( $faq_id, 'FAQ_Total_Score', $total_score - 1 );
    		}

		    die();
		}

		/**
		 * Updates the order of the FAQ, based on the ordering table
		 * @since 2.0.0
		 */
		public function update_order() {
			global $ewd_ufaq_controller;

			if ( ! is_array( $_POST['ewd-ufaq-item'] ) or ! $ewd_ufaq_controller->permissions->check_permission( 'ordering' ) ) { return; }

    		foreach ( $_POST['ewd-ufaq-item'] as $key => $id ) {

    		    update_post_meta( intval( $id ), 'ufaq_order', intval( $key ) );
    		}

   			die();
		}
	}
}