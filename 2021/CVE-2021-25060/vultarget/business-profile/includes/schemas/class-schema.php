<?php
/**
 * Create a schema as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchema' ) ) :

	/**
	 * Base class for creating schemas for Business Profile
	 *
	 * @since 2.0.0
	 */
	abstract class bpfwpSchema {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = '';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = '';

		/**
		 * The @type property for this schema, if different from slug
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $type = '';

		/**
		 * Fields that can be filled in for this class
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    array
		 */
		public $fields = array();
		
		/**
		 * Children of this class
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
		public function __construct( $args = array( 'depth' => 10 ) ) {

			$this->set_fields();
			if ( $args['depth'] > 0 ) { $this->initialize_children( $args['depth'] ); }
		}

		/**
		 * Load the schema's default fields, must be set in the child class
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		abstract public function set_fields();


		/**
		 * Load the schema's child classes, must be set in the child class
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		abstract public function initialize_children( $depth );

	}
endif;
