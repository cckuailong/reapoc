<?php
/**
 * Create a schema for a Recipe as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaRecipe' ) ) :

	/**
	 * Recipe schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaRecipe extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Recipe';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Recipe';


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
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_title', 'name', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'image', 
					'name' 				=> 'Image', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function bpfwp_get_post_image_url', 'image', $this->slug )
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
					'slug' 				=> 'description', 
					'name' 				=> 'Description', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_excerpt', 'description', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'datePublished', 
					'name' 				=> 'Date Published', 
					'input' 			=> 'text',
					'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_date', 'datePublished', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'video', 
					'name' 				=> 'Video', 
					'input' 			=> 'url',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'video', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'prepTime', 
					'name' 				=> 'Prep Time', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'prepTime', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'cookTime', 
					'name' 				=> 'Cook Time', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'cookTime', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'totalTime', 
					'name' 				=> 'Total Time', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'totalTime', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'keywords', 
					'name' 				=> 'Keywords', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'keywords', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'recipeYield', 
					'name' 				=> 'Recipe Yield', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'recipeYield', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'recipeCategory', 
					'name' 				=> 'Recipe Category', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'recipeCategory', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'recipeCuisine', 
					'name' 				=> 'Recipe Cuisine', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'recipeCuisine', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'nutrition', 
					'name' 				=> 'Nutrition', 
					'type'				=> 'NutritionInformation',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'calories', 
							'name' 				=> 'Calories', 
							'input' 			=> 'text',
							'recommended'		=> true,
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'calories', $this->slug, 'nutrition' )
						) ),
					)
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'recipeIngredient', 
					'name' 				=> 'Recipe Ingredients', 
					'input' 			=> 'textarea',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'recipeIngredient', $this->slug )
				) ),
				new bpfwpSchemaField( array( 
					'slug' 				=> 'recipeInstructions', 
					'name' 				=> 'Recipe Instructions', 
					'type'				=> 'ItemList',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'howToStep', 
							'name' 				=> 'How-To Step', 
							'type'				=> 'HowToStep',
							'repeatable'		=> true,
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'text', 
									'name' 				=> 'Text', 
									'input' 			=> 'text',
									'recommended'		=> true,
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'text', $this->slug, 'recipeInstructions', 'howToStep' )
								) ),
							)
						) ),
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
							'slug' 				=> 'ratingCount', 
							'name' 				=> 'Rating Count', 
							'input' 			=> 'text',
							'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'ratingCount', $this->slug, 'aggregateRating' )
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