<?php
/*
 * Plugin Name:	Dilaz Metabox
 * Plugin URI:	http://webdilaz.com/plugins/dilaz-metabox/
 * Description:	Create custom metaboxes for WordPress themes and plugins.
 * Author:		WebDilaz Team
 * Version:		2.5
 * Author URI:	http://webdilaz.com/
 * License:		GPL-2.0+
 * License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
||
|| --------------------------------------------------------------------------------------------
|| Metabox
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Metabox
|| @version		2.5
|| @since		Dilaz Metabox 2.0
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @License		GPL-2.0+
|| @License URI	http://www.gnu.org/licenses/gpl-2.0.txt
|| 
*/

defined('ABSPATH') || exit;

/**
 * DilazMetabox functions
 */
require_once plugin_dir_path(__FILE__) .'inc/functions.php';


/**
 * DilazMetabox main class
 */
if (!class_exists('DilazMetabox')) {
	final class DilazMetabox {
		
		/**
		 * Metabox parameters
		 *
		 * @var array
		 * @since 2.0
		 */
		private $_params = array();
		
		
		/**
		 * Metabox prefix
		 *
		 * @var string
		 * @since 2.0
		 */
		protected $_prefix;
		
		
		/**
		 * The single instance of the class
		 *
		 * @var string
		 * @since 2.0
		 */
		protected static $_instance = null;
		
		
		/**
		 * Main DilazMetabox instance
		 *
		 * Make sure only only one instance can be loaded
		 *
		 * @since 2.0
		 * @static
		 * @see DilazMetabox()
		 * @return DilazMetabox object - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
		
		/**
		 * Cloning is forbidden
		 *
		 * @since 2.0
		 * @return void 
		 */
		public function __clone() {
			_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'dilaz-metabox'), '2.0');
		}
		
		
		/**
		 * Unserializing instances of this class is forbidden
		 *
		 * @since 2.0
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'dilaz-metabox'), '2.0');
		}
		
		
		/**
		 * Contructor method
		 *
		 * @since 1.0
		 * @param array	$prefix metabox prefix
		 * 
		 */
		function __construct($metabox_args) {
			
			do_action('dilaz_metabox_before_load');
			
			$this->args      = $metabox_args;
			$this->_params   = $this->args[0];
			$this->metaboxes = $this->args[1];
			$this->_prefix   = DilazMetaboxFunction::preparePrefix($this->_params['prefix']);
			
			# Hooks
			add_action('init', array(&$this, 'init'));
			add_action('init', array(&$this, 'metaboxClass'));
			
			do_action('dilaz_metabox_after_load');
			
		}
		
		
		/**
		 * Initialize the metabox class
		 *
		 * @since	1.0
		 * @return	void
		 */
		public function metaboxClass() {
			if (!class_exists('Dilaz_Meta_Box'))
				require_once DILAZ_MB_DIR .'inc/metabox-class.php';
			
			$prefix           = $this->_prefix;
			$parameters       = $this->_params;
			$dilaz_meta_boxes = array();
			$dilaz_meta_boxes = $this->metaboxes;
			$dilaz_meta_boxes = apply_filters('dilaz_meta_box_filter', $dilaz_meta_boxes, $prefix, $parameters);
			
			new Dilaz_Meta_Box($prefix, $dilaz_meta_boxes, $parameters);
		}
		
		
		/**
		 * Initialize Admin Panel
		 *
		 * @since 1.0
		 * @return void
		 */
		public function init() {

			do_action('dilaz_metabox_before_init');
			
			# Load constants
			$this->constants();
			
			# Load parameters
			$this->parameters();
			
			# include required files 
			$this->includes();
			
			do_action('dilaz_metabox_after_init');
		}
		
		
		/**
		 * Add metabox parameters
		 *
		 * @since	1.0
		 * @return	array
		 */
		public function parameters() {
			return $this->_params;
		}
		
		
		/**
		 * Constants
		 *
		 * @since 1.0
		 * @return void
		 */
		public function constants() {
			@define('DILAZ_MB_URL', plugin_dir_url(__FILE__));
			@define('DILAZ_MB_DIR', plugin_dir_path(__FILE__));
			@define('DILAZ_MB_IMAGES', DILAZ_MB_URL .'assets/images/');
		}
		
		
		/**
		 * Includes
		 *
		 * @since 1.0
		 * @return void
		 */
		public function includes() {
			
			do_action('dilaz_metabox_after_includes');
			
			do_action('dilaz_metabox_before_includes');
		}
		
	}
}