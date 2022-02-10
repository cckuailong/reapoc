<?php 
	/**
		* Public Class
		*
		* @package     Wow_Plugin
		* @subpackage  Public
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	namespace wpcoder;
	
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	class Wow_Public_Class {
	
		private $arg;
		
		public function __construct( $arg ) {	
		
			$this->plugin_name      = $arg['plugin_name'];
			$this->plugin_menu      = $arg['plugin_menu'];
			$this->plugin_home_url  = $arg['plugin_home_url'];
			$this->plugin_version   = $arg['plugin_version'];
			$this->plugin_file      = $arg['plugin_file'];
			$this->plugin_slug      = $arg['plugin_slug'];
			$this->plugin_dir       = $arg['plugin_dir'];
			$this->plugin_url       = $arg['plugin_url'];
			$this->plugin_pref      = $arg['plugin_pref'];
			$this->author_url       = $arg['author_url'];
			$this->pro_url          = $arg['pro_url'];
			$this->shortcode        = $arg['shortcode'];
			
			$upload = wp_upload_dir();
			$this->basedir = $upload['basedir'] . '/' . $this->plugin_slug . '/';
			$this->baseurl = $upload['baseurl'] . '/' . $this->plugin_slug . '/';			
			
			add_shortcode( $this->shortcode, array( $this, 'shortcode' ) );		
			
		}		
		
		
		function shortcode( $atts ) {
			ob_start();
			require plugin_dir_path( __FILE__ ) . 'shortcode.php';				
			$shortcode = ob_get_contents();
			ob_end_clean();					
			return $shortcode;
		}		
		
	}		