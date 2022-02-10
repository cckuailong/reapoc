<?php
/**
 * Create a schema field to be used for schema classes.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaField' ) ) :

	/**
	 * Schema field for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaField {

		/**
		 * The type used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $type = '';

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = '';

		/**
		 * The display name for this field
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = '';

		/**
		 * The input type for this field
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $input = '';

		/**
		 * Whether this field is should be recommended to be filled in (bolded)
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    boolean
		 */
		public $recommended = false;

		/**
		 * Whether there can be multiple instances of this field
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    boolean
		 */
		public $repeatable = false;

		/**
		 * What default value, if any, should exist for this field. 
		 * Can include 'function', 'option' or 'meta' to return, respectively,
		 * the value of a function, get_option or appropriate get_meta call
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $callback = '';
		
		/**
		 * Child fields for this class
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    array
		 */
		public $children = array();

		/**
		 * Initialize the class and recursively initialize child classes.
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function __construct( $args ) {

			$this->set_properties( $args );
		}

		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_properties( $args ) {
			
			if ( isset($args['type']) ) { $this->type = $args['type']; }
			if ( isset($args['slug']) ) { $this->slug = $args['slug']; }
			if ( isset($args['name']) ) { $this->name = $args['name']; }
			if ( isset($args['recommended']) ) { $this->recommended = $args['recommended']; }
			if ( isset($args['repeatable']) ) { $this->repeatable = $args['repeatable']; }
			if ( isset($args['input']) ) { $this->input = $args['input']; }
			if ( isset($args['callback']) ) { $this->callback = $args['callback']; }
			if ( isset($args['children']) ) { $this->children = $args['children']; }
		}


		/**
		 * Get the default value for this field based on the object this field is called for
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  int $object_id ID of the object we're fetching a default value for.
		 * @param  string $object_type description of the type of object (post, taxnomy, etc.)
		 * @return mixed $value 
		 */
		public function get_default_value( $object_id, $object_type = 'post' ) {
			
			if ( ! isset($this->callback) ) { return; }

			if ( strpos($this->callback, ' ') === false ) { return $this->callback; }

			$operation = substr($this->callback, 0, strpos($this->callback, ' '));
			$command = substr($this->callback, strpos($this->callback, ' ') + 1);

			if ( $operation == 'function' ) {

				$args = array();
				while ( strpos($command, ' ') !== false ) { 
					$args[] = substr($command, 0, strpos($command, ' '));
					$command = substr($command, strpos($command, ' ') + 1); 
				} 

				if ( function_exists($command) ) { $value = $command( ...$args ); }
				else { $value = false; }
			}

			elseif ( $operation == 'option' ) {
				$value = get_option($command);
			} 

			elseif ( $operation == 'meta' ) {
				if ( $object_type == 'post' ) { $value = get_post_meta( $object_id, $command, true ); }
				if ( $object_type == 'taxonomy' ) { $value = get_term_meta( $object_id, $command, true ); }
			}

			// Not a valid operation, so return the entire string as the value
			else { 
				$value = $operation . ' ' . $command;
			}

			return $value;
		}
	}
endif;
