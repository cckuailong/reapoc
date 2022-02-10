<?php
/**
 * Gutenberg class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.0.0
 */


namespace TUTOR;


if ( ! defined( 'ABSPATH' ) )
	exit;

class Gutenberg {

	public function __construct() {
		if ( ! function_exists('register_block_type')){
			return;
		}

		add_action( 'init', array($this, 'register_blocks') );
		add_filter('block_categories_all', array($this, 'registering_new_block_category'), 10, 2);
		add_action('wp_ajax_render_block_tutor', array($this, 'render_block_tutor'));
	}
	
	function register_blocks() {
		global $pagenow;
		if ( 'widgets.php' !== $pagenow ) {
			wp_register_script(
				'tutor-student-registration-block', tutor()->url . 'assets/js/gutenberg_blocks.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' )
			);
		}

		register_block_type( 'tutor-gutenberg/student-registration', array(
			'editor_script'     => 'tutor-student-registration-block',
			'render_callback'   => array($this, 'render_block_student_registration'),
		) );
		/*
		register_block_type( 'tutor-gutenberg/student-dashboard', array(
			'editor_script' => 'tutor-student-registration-block',
			'render_callback'   => array($this, 'render_block_tutor_dashboard'),
		) );*/
		register_block_type( 'tutor-gutenberg/instructor-registration', array(
			'editor_script' => 'tutor-student-registration-block',
			'render_callback'   => array($this, 'render_block_tutor_instructor_registration_form'),
		) );
	}

	public function registering_new_block_category($categories, $post ){
		return array_merge(
			array(
				array(
					'slug' => 'tutor',
					'title' => __( 'Tutor LMS', 'tutor' ),
				),
			),
			$categories
		);
	}

	public function render_block_student_registration($args){
		return do_shortcode("[tutor_student_registration_form]");
	}
	public function render_block_tutor_dashboard($args){
		return do_shortcode("[tutor_dashboard]");
	}
	public function render_block_tutor_instructor_registration_form($args){
		return do_shortcode("[tutor_instructor_registration_form]");
	}

	//For editor
	public function render_block_tutor(){
		tutils()->checking_nonce();

		$shortcode = sanitize_text_field($_POST['shortcode']);
		
		$allowed_shortcode = array(
			'tutor_instructor_registration_form',
			'tutor_student_registration_form'
		);
		
		if(!in_array($shortcode, $allowed_shortcode)) {
			wp_send_json_error( );
		}

		wp_send_json_success(do_shortcode("[{$shortcode}]"));
	}

}