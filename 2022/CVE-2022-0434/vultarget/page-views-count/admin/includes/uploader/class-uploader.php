<?php
/* "Copyright 2012 a3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\PageViewsCount\FrameWork {

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------
A3rev Plugin Uploader

TABLE OF CONTENTS

- var admin_uploader_url
- __construct()
- admin_uploader_url()
- uploader_js()
- uploader_style()
- uploader_init()
- get_silentpost()
- upload_input()
- change_button_text()
- modify_tabs()
- inside_popup()

-----------------------------------------------------------------------------------*/
class Uploader extends Admin_UI
{

	/**
	 * @var string
	 */
	private $admin_uploader_url;

	/*-----------------------------------------------------------------------------------*/
	/* Admin Uploader Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		if ( is_admin() ) {

			// include scripts to Admin UI Interface
			add_action( $this->plugin_name . '_init_scripts', array( $this, 'uploader_script' ) );

			// include styles to Admin UI Interface
			add_action( $this->plugin_name . '_init_styles', array( $this, 'uploader_style' ) );
		}

	}

	/*-----------------------------------------------------------------------------------*/
	/* admin_uploader_url */
	/*-----------------------------------------------------------------------------------*/
	public function admin_uploader_url() {
		if ( $this->admin_uploader_url ) return $this->admin_uploader_url;
		return $this->admin_uploader_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	public function uploader_script() {
		add_action( 'admin_enqueue_scripts', array( $this, 'uploader_js' ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Include Uploader Script */
	/*-----------------------------------------------------------------------------------*/
	public function uploader_js () {
		wp_enqueue_script( 'a3-uploader-script', $this->admin_uploader_url() . '/uploader-script.js', array( 'jquery', 'thickbox' ), $this->framework_version );
		if ( function_exists( 'wp_enqueue_media' ) ) {
		    wp_enqueue_media();
		} else {
		    wp_enqueue_script('media-upload');
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* Include Uploader Style */
	/*-----------------------------------------------------------------------------------*/
	public function uploader_style () {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'a3-uploader-style', $this->admin_uploader_url() . '/uploader.css', array(), $this->framework_version );
		if ( is_rtl() ) {
			wp_enqueue_style( 'a3-uploader-style-rtl', $this->admin_uploader_url() . '/uploader.rtl.css', array(), $this->framework_version );
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* Get Upload Input Field */
	/*-----------------------------------------------------------------------------------*/
	public function upload_input ( $name_attribute, $id_attribute = '', $value = '', $attachment_id = 0, $default_value = '', $field_name = '', $class = '', $css = '', $description = '', $strip_methods = true ) {
		$output = '';

		if ( trim( $value ) == '' ) $value = trim( $default_value );

		if ( strstr( $name_attribute, ']' ) ) {
			$attachment_id_name_attribute = substr_replace( $name_attribute, '_attachment_id', -1, 0 );
		} else {
			$attachment_id_name_attribute = $name_attribute.'_attachment_id';
		}

		if ( $strip_methods === false ) {
			$strip_methods = 0;
		} else {
			$strip_methods = 1;
		}

		$output .= '<input type="hidden" name="'.$attachment_id_name_attribute.'" id="'.$id_attribute.'_attachment_id" value="'.$attachment_id.'" class=" a3_upload_attachment_id" />';
		$output .= '<input data-strip-methods="'.$strip_methods.'" type="text" name="'.$name_attribute.'" id="'.$id_attribute.'" value="'.esc_attr( $value ).'" class="'.$id_attribute. ' ' .$class.' a3_upload" style="'.$css.'" rel="'.$field_name.'" /> ';
		$output .= '<input id="upload_'.$id_attribute.'" class="a3rev-ui-upload-button a3_upload_button button" type="button" value="'.__( 'Upload', 'page-views-count' ).'" /> '.$description;
		
		$output .= '<div style="clear:both;"></div><div class="a3_screenshot" id="'.$id_attribute.'_image" style="'.( ( $value == '' ) ? 'display:none;' : 'display:block;' ).'">';

		if ( $value != '' ) {
			$remove = '<a href="javascript:(void);" class="a3_uploader_remove a3-plugin-ui-delete-icon">&nbsp;</a>';

			$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );

			if ( $image ) {
				$output .= '<img class="a3_uploader_image" src="' . esc_url( $value ) . '" alt="" />'.$remove.'';
			} else {
				$parts = explode( "/", $value );

				for( $i = 0; $i < sizeof( $parts ); ++$i ) {
					$title = $parts[$i];
				}

				$output .= '';

				$title = __( 'View File', 'page-views-count' );

				$output .= '<div class="a3_no_image"><span class="a3_file_link"><a href="'.esc_url( $value ).'" target="_blank" rel="a3_external">'.$title.'</a></span>'.$remove.'</div>';

			}
		}

		$output .= '</div>';

		return $output;
	}
}

}
