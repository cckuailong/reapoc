<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class QUBELY_Template {

	/**
	 * Qubely Template.
	 * @since 1.1.0
	 */
	public function __construct() {
		//Include Qubely default template without wrapper
		add_filter( 'template_include', array($this, 'template_include') );

		//Add Qubely supported Post type in page template
        $post_types = get_post_types();
		if( !empty($post_types) ){
			foreach ($post_types as $post_type){
				add_filter( "theme_{$post_type}_templates", array( $this, 'add_qubely_template' ) );
			}
		}
	}

	/**
	 * @param $template
	 * @since 1.1.0
	 */
	public function template_include($template){
		if ( is_singular() ) {
			$page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
			if ( $page_template === 'qubely_full_width' ) {
				$template = QUBELY_DIR_PATH . 'core/template-full-width.php';
            }
            if ( $page_template === 'qubely_canvas' ) {
				$template = QUBELY_DIR_PATH . 'core/template-canvas.php';
			}
		}
		return $template;
	}

	/**
	 * @param $post_templates
	 * @since 1.1.0
	 */
	public function add_qubely_template($post_templates){
		$post_templates['qubely_full_width'] = __('Qubely Full Width', 'qubely');
		$post_templates['qubely_canvas'] = __('Qubely Canvas', 'qubely');
		return $post_templates;
	}
}
new QUBELY_Template();