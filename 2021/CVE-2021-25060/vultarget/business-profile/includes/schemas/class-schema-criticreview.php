<?php
/**
 * Create a schema for a Critic Review as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaCriticReview' ) ) :

	/**
	 * Critic Review schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaCriticReview extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'CriticReview';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Critic Review';


		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_fields() {
			require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-field.php';

			$fields = array(
				new bpfwpSchemaField( array( 
					'slug' 				=> 'author', 
					'name' 				=> 'Author', 
					'type'				=> 'Person',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_author', 'name', $this->slug, 'author' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'sameAs', 
							'name' 				=> 'Corresponding URL (sameAs)', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'sameAs', $this->slug, 'author' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'url', 
					'name' 				=> 'URL', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_permalink', 'url', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'description', 
					'name' 				=> 'Description', 
					'input' 			=> 'text',
					'recommended'		=> true,
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_excerpt', 'description', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'datePublished', 
					'name' 				=> 'Publish Date', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_date', 'datePublished', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'itemReviewed', 
					'name' 				=> 'Item Reviewed', 
					'type'				=> 'Thing',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'itemReviewed' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'sameAs', 
							'name' 				=> 'Corresponding URL (sameAs)', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'sameAs', $this->slug, 'itemReviewed' )
						) ),
						new bpfwpSchemaField( array(
							'slug' 				=> 'image', 
							'name' 				=> 'Image', 
							'input' 			=> 'url',
							'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function bpfwp_get_post_image_url', 'image', $this->slug, 'itemReviewed' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'priceRange', 
							'name' 				=> 'Price Range', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'priceRange', $this->slug, 'itemReviewed' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'address', 
							'name' 				=> 'Address', 
							'type'				=> 'PostalAddress',
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'addressLocality', 
									'name' 				=> 'Location (City, Province)', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'telephone', $this->slug, 'itemReviewed', 'address' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'postalCode', 
									'name' 				=> 'Postal/Zip Code', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'contactType', $this->slug, 'itemReviewed', 'address' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'streetAddress', 
									'name' 				=> 'Street Address', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'contactOption', $this->slug, 'itemReviewed', 'address' )
								) )
							)
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'telephone', 
							'name' 				=> 'Telephone', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'telephone', $this->slug, 'itemReviewed' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'aggregateRating', 
							'name' 				=> 'Aggregate Rating', 
							'type'				=> 'AggregateRating',
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'ratingValue', 
									'name' 				=> 'Rating', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingValue', $this->slug, 'itemReviewed', 'aggregateRating' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'ratingCount', 
									'name' 				=> 'Rating Count', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingCount', $this->slug, 'itemReviewed', 'aggregateRating' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'bestRating', 
									'name' 				=> 'Best Rating', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'bestRating', $this->slug, 'itemReviewed', 'aggregateRating' )
								) )
							)
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'reviewRating', 
					'name' 				=> 'Rating', 
					'type'				=> 'Rating',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'ratingValue', 
							'name' 				=> 'User Rating', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingValue', $this->slug, 'reviewRating' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'bestRating', 
							'name' 				=> 'Best Rating', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'bestRating', $this->slug, 'reviewRating' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'worstRating', 
							'name' 				=> 'Worst Rating', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'worstRating', $this->slug, 'reviewRating' )
						) )
					)
				) ),					
			);

			$this->fields = apply_filters( 'bpfwp_schema_fields', $fields, $this->slug );
		}


		/**
		 * Load the schema's child classes
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function initialize_children(  $depth ) {
			$depth--;

			$child_classes = array ();

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;