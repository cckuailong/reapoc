<?php
/**
 * Create a schema for Subscription and Paywalled Content as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaSubscriptionAndPaywalledContent' ) ) :

	/**
	 * Subscription and Paywalled Content schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaSubscriptionAndPaywalledContent extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'SubscriptionAndPaywalledContent';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Subscription and Paywalled Content';


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
					'slug' 				=> 'headline', 
					'name' 				=> 'Headline', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_title', 'headline', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'image', 
					'name' 				=> 'Image', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function bpfwp_get_post_image_url', 'image', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'datePublished', 
					'name' 				=> 'Date Published', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'datePublished', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'dateModified', 
					'name' 				=> 'Date Modified', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'dateModified', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'description', 
					'name' 				=> 'Description', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_excerpt', 'description', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'isAccessibleForFree', 
					'name' 				=> 'Accessible for Free?', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'isAccessibleForFree', $this->slug )
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
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_author', 'name', $this->slug, 'author' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'publisher', 
					'name' 				=> 'Publisher', 
					'type'				=> 'Organization',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'name', 
							'name' 				=> 'Name', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'publisher' )
						) ),
						new bpfwpSchemaField( array( 
							'slug' 				=> 'logo', 
							'name' 				=> 'Logo', 
							'input' 			=> 'url',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'logo', $this->slug, 'publisher' )
						) ),
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