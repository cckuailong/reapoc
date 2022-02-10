<?php
/**
 * Create a schema for a Product as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaProduct' ) ) :

	/**
	 * Product schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaProduct extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Product';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Product';


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
					'slug' 				=> 'name', 
					'name' 				=> 'Name', 
					'input' 			=> 'text',
					'recommended'		=> true,
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_title', 'name', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'image', 
					'name' 				=> 'Image', 
					'input' 			=> 'text',
					'recommended'		=> true,
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'image', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'description', 
					'name' 				=> 'Description', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_excerpt', 'description', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'brand', 
					'name' 				=> 'Brand', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'brand', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'sku', 
					'name' 				=> 'SKU', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'sku', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'review', 
					'name' 				=> 'Review', 
					'type'				=> 'Review',
					'input'				=> 'SchemaField',
					'children' 			=> array (
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
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingValue', $this->slug, 'review', 'reviewRating' )
								) )
								
							)
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'reviewBody', 
							'name' 				=> 'Review Body', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'reviewBody', $this->slug, 'review' )
						) ),
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
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'review', 'author' )
								) )
							)
						) )
					)
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
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingValue', $this->slug, 'aggregateRating' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'reviewCount', 
							'name' 				=> 'Review Count', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'reviewCount', $this->slug, 'aggregateRating' )
						) )
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'offers', 
					'name' 				=> 'Offer', 
					'type'				=> 'Offer',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'url', 
							'name' 				=> 'URL', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_permalink', 'url', $this->slug, 'offers' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'priceCurrency', 
							'name' 				=> 'Currency', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'priceCurrency', $this->slug, 'offers' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'price', 
							'name' 				=> 'Price', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'priceCurrency', $this->slug, 'offers' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'availability', 
							'name' 				=> 'Availability', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'https://schema.org/InStock', 'priceCurrency', $this->slug, 'offers' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'seller', 
							'name' 				=> 'Seller', 
							'type'				=> 'Organization',
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'name', 
									'name' 				=> 'Name', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'offers', 'seller' )
								) )
							)
						) )
					)
				) )
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