<?php
/**
 * Define settings used throughout the plugin.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpIntegrations' ) ) :

	/**
	 * Class to handle configurable settings for Business Profile
	 *
	 * @since 0.0.1
	 */
	class bpfwpIntegrations {

		/**
		 * Initialize the class and register hooks.
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			add_filter( 'sanitize_option_bpfwp-settings', array( $this, 'check_for_articles_rich_snippets_change' ), 100 );

			add_filter( 'sanitize_option_bpfwp-settings', array( $this, 'check_for_wc_integration_change' ), 100 );
		}

		/**
		 * Check to see if WC integration was toggled on or off
		 *
		 * @since 2.0.0
		 */
		public function check_for_articles_rich_snippets_change( $val ) {
			global $bpfwp_controller;

			if ( ! is_object($bpfwp_controller) ) { return; }
	
			// WooCommerce integration has been turned off
			if ( empty( $val['article-rich-snippets'] ) and $bpfwp_controller->settings->get_setting( 'article-rich-snippets' ) ) {
				$article_post = get_page_by_path( 'business-profile-article-schema', OBJECT, $bpfwp_controller->cpts->schema_cpt_slug );

				if ($article_post) {
					$post_data = array(
						'ID' => $article_post->ID,
						'post_status' => 'disabled'
					);

					wp_update_post( $post_data );
				}
			}
			// WooCommerce integration has been turned on
			elseif ( ! empty( $val['article-rich-snippets'] ) and ! $bpfwp_controller->settings->get_setting( 'article-rich-snippets' ) ) {
				$article_post = get_page_by_path( 'business-profile-article-schema', OBJECT, $bpfwp_controller->cpts->schema_cpt_slug );

				// Post exists, enable it
				if ( $article_post ) {
					$post_data = array(
						'ID' => $article_post->ID,
						'post_status' => 'publish'
					);

					wp_update_post( $post_data );
				}
				// Post doesn't exist, create it
				else {
					$post_data = array(
						'post_title' => 'Business Profile Article Schema',
						'post_name' => 'business-profile-article-schema',
						'post_type' => $bpfwp_controller->cpts->schema_cpt_slug,
						'post_status' => 'publish',
					);

					$bpwc_post_id = wp_insert_post( $post_data );

					if ( $bpwc_post_id ) {
						$schema_meta_data = array(
							'schema_target_type' => 'post_type',
							'schema_target_value' => 'post',
							'schema_type' => 'Article',
							'default_display' => 'on',
							'field_defaults' => array(
								'_author_name' 						=> 'function display_name get_the_author_meta',
								'_datePublished'					=> 'function get_the_date',
								'_dateModified'						=> 'function get_the_modified_date',
								'_headline'							=> 'function get_the_title',
								'_image' 							=> 'function bpfwp_get_post_image_url',
								'_description'						=> 'function get_the_excerpt',
								'_publisher_name'					=> 'option blogname',
								'_publisher_logo_height'			=> 'function bpfwp_get_site_logo_height', 
								'_publisher_logo_width'				=> 'function bpfwp_get_site_logo_width',
								'_publisher_logo_url'				=> 'function bpfwp_get_site_logo_url'
							)
						);

						update_post_meta( $bpwc_post_id, 'bpfwp-schema-data', $schema_meta_data );
					}
				}
			}

			return $val;
		}

		/**
		 * Check to see if WC integration was toggled on or off
		 *
		 * @since 2.0.0
		 */
		public function check_for_wc_integration_change( $val ) {
			global $bpfwp_controller;

			if ( ! is_object($bpfwp_controller) ) { return; }
	
			// WooCommerce integration has been turned off
			if ( empty( $val['woocommerce-integration'] ) and $bpfwp_controller->settings->get_setting( 'woocommerce-integration' ) ) {
				$woocommerce_post = get_page_by_path( 'business-profile-woocommerce-schema', OBJECT, $bpfwp_controller->cpts->schema_cpt_slug );

				if ($woocommerce_post) {
					$post_data = array(
						'ID' => $woocommerce_post->ID,
						'post_status' => 'disabled'
					);

					wp_update_post( $post_data );
				}
			}
			// WooCommerce integration has been turned on
			elseif ( ! empty( $val['woocommerce-integration'] ) and ! $bpfwp_controller->settings->get_setting( 'woocommerce-integration' ) ) {
				$woocommerce_post = get_page_by_path( 'business-profile-woocommerce-schema', OBJECT, $bpfwp_controller->cpts->schema_cpt_slug );

				// Post exists, enable it
				if ( $woocommerce_post ) {
					$post_data = array(
						'ID' => $woocommerce_post->ID,
						'post_status' => 'publish'
					);

					wp_update_post( $post_data );
				}
				// Post doesn't exist, create it
				else {
					$post_data = array(
						'post_title' => 'Business Profile WooCommerce Schema',
						'post_name' => 'business-profile-woocommerce-schema',
						'post_type' => $bpfwp_controller->cpts->schema_cpt_slug,
						'post_status' => 'publish',
					);

					$bpwc_post_id = wp_insert_post( $post_data );

					if ( $bpwc_post_id ) {
						$schema_meta_data = array(
							'schema_target_type' => 'post_type',
							'schema_target_value' => 'product',
							'schema_type' => 'Product',
							'default_display' => 'on',
							'field_defaults' => array(
								'_image' 							=> 'function bpfwp_get_post_image_url',
								'_description'						=> 'function get_the_excerpt',
								'_sku'								=> 'meta _sku',
								'_review_reviewRating_ratingValue'	=> 'function bpfwp_wc_get_most_recent_review_rating',
								'_review_reviewBody'				=> 'function bpfwp_wc_get_most_recent_review_body',
								'_review_author_name'				=> 'function bpfwp_wc_get_most_recent_review_author',
								'_aggregateRating_ratingValue'		=> 'meta _wc_average_rating',
								'_aggregateRating_reviewCount'		=> 'meta _wc_review_count',
								'_offers_priceCurrency'				=> 'option woocommerce_currency',
								'_offers_price'						=> 'meta _price',
								'_offers_pricevalidUntil'			=> 'meta _sale_price_dates_to',
								'_offers_availability'				=> 'meta _stock_status'
							)
						);

						update_post_meta( $bpwc_post_id, 'bpfwp-schema-data', $schema_meta_data );
					}
				}
			}

			return $val;
		}
	}
endif;

?>