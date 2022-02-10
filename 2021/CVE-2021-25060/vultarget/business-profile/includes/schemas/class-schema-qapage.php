<?php
/**
 * Create a schema for a QA Page as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaQAPage' ) ) :

	/**
	 * QA Page schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaQAPage extends bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'QAPage';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'QA Page';


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
					'slug' 				=> 'QAPage', 
					'name' 				=> 'QA Page', 
					'type'				=> 'QAPage',
					'input'				=> 'SchemaField',
					'children' 			=> array (
						new bpfwpSchemaField( array( 
							'slug' 				=> 'question', 
							'name' 				=> 'Question', 
							'type'				=> 'Question',
							'repeatable'		=> true,
							'input'				=> 'SchemaField',
							'children' 			=> array (
								new bpfwpSchemaField( array( 
									'slug' 				=> 'name', 
									'name' 				=> 'Name', 
									'recommended'		=> true,
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_title', 'name', $this->slug, 'QAPage', 'question' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'text', 
									'name' 				=> 'Text', 
									'recommended'		=> true,
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'text', $this->slug, 'QAPage', 'question' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'answerCount', 
									'name' 				=> 'Answer Count', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'answerCount', $this->slug, 'QAPage', 'question' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'upvoteCount', 
									'name' 				=> 'Upvote Count', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'upvoteCount', $this->slug, 'QAPage', 'question' )
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'dateCreated', 
									'name' 				=> 'Date Created', 
									'input' 			=> 'text',
									'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_date', 'dateCreated', $this->slug, 'QAPage', 'question' )
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
											'callback'			=> apply_filters( 'bpfwp_schema_field_callback', 'function get_the_author', 'name', $this->slug, 'QAPage', 'question', 'author' )
										) ),
									)
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'acceptedAnswer', 
									'name' 				=> 'Answer', 
									'type'				=> 'Answer',
									'input'				=> 'SchemaField',
									'children' 			=> array (
										new bpfwpSchemaField( array( 
											'slug' 				=> 'text', 
											'name' 				=> 'Text', 
											'input' 			=> 'text',
											'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'QAPage', 'question', 'acceptedAnswer' )
										) ),
										new bpfwpSchemaField( array( 
											'slug' 				=> 'upvoteCount', 
											'name' 				=> 'Upvote Count', 
											'input' 			=> 'text',
											'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'upvoteCount', $this->slug, 'QAPage', 'question', 'acceptedAnswer' )
										) ),
										new bpfwpSchemaField( array( 
											'slug' 				=> 'dateCreated', 
											'name' 				=> 'Date Created', 
											'input' 			=> 'text',
											'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'dateCreated', $this->slug, 'QAPage', 'question', 'acceptedAnswer' )
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
													'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'QAPage', 'question', 'acceptedAnswer', 'author' )
												) ),
											)
										) ),
									)
								) ),
								new bpfwpSchemaField( array( 
									'slug' 				=> 'suggestedAnswer', 
									'name' 				=> 'Suggested Answer', 
									'type'				=> 'Answer',
									'input'				=> 'SchemaField',
									'children' 			=> array (
										new bpfwpSchemaField( array( 
											'slug' 				=> 'text', 
											'name' 				=> 'Text', 
											'input' 			=> 'text',
											'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'QAPage', 'question', 'suggestedAnswer' )
										) ),
										new bpfwpSchemaField( array( 
											'slug' 				=> 'upvoteCount', 
											'name' 				=> 'Upvote Count', 
											'input' 			=> 'text',
											'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'upvoteCount', $this->slug, 'QAPage', 'question', 'suggestedAnswer' )
										) ),
										new bpfwpSchemaField( array( 
											'slug' 				=> 'dateCreated', 
											'name' 				=> 'Date Created', 
											'input' 			=> 'text',
											'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'dateCreated', $this->slug, 'QAPage', 'question', 'suggestedAnswer' )
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
													'callback'			=> apply_filters( 'bpfwp_schema_field_callback', null, 'name', $this->slug, 'QAPage', 'question', 'suggestedAnswer', 'author' )
												) ),
											)
										) ),
									)
								) ),
							)
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