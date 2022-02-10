<?php
class Wpr_Options_Framework_Media_Uploader {

	/**
	 * Initialize the media uploader class
	 *
	 * @since 1.7.0
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'wpr_optionsframework_media_scripts' ) );
	}

	/**
	 * Media Uploader Using the WordPress Media Library.
	 *
	 * Parameters:
	 *
	 * string $_id - A token to identify this field (the name).
	 * string $_value - The value of the field, if present.
	 * string $_desc - An optional description of the field.
	 *
	 */

	static function wpr_optionsframework_uploader( $_id, $_value, $_desc = '', $_name = '' ) {

		$wpr_optionsframework_settings = get_option( 'wpr_optionsframework' );

		// Gets the unique option id
		$option_name = $wpr_optionsframework_settings['id'];

		$output = '';
		$id = '';
		$class = '';
		$int = '';
		$value = '';
		$name = '';

		$id = strip_tags( strtolower( $_id ) );

		// If a value is passed and we don't have a stored value, use the value that's passed through.
		if ( $_value != '' && $value == '' ) {
			$value = $_value;
		}

		if ($_name != '') {
			$name = $_name;
		}
		else {
			$name = $option_name.'['.$id.']';
		}

		if ( $value ) {
			$class = ' has-file';
		}
		$output .= '<input id="' . $id . '" class="upload form-control pull-left' . $class . '" type="text" name="'.$name.'" value="' . $value . '" placeholder="' . __('No file chosen', 'textdomain') .'" />' . "\n";
		if ( function_exists( 'wp_enqueue_media' ) ) {
			if ( ( $value == '' ) ) {
				$output .= '<input id="upload-' . $id . '" class="upload-button btn btn-success pull-left" type="button" value="' . __( 'Upload', 'textdomain' ) . '" />' . "\n";
			} else {
				$output .= '<input id="remove-' . $id . '" class="remove-file btn btn-success pull-left" type="button" value="' . __( 'Remove', 'textdomain' ) . '" />' . "\n";
			}
		} else {
			$output .= '<p><i>' . __( 'Upgrade your version of WordPress for full media support.', 'textdomain' ) . '</i></p>';
		}

		if ( $_desc != '' ) {
			$output .= '<span class="of-metabox-desc">' . $_desc . '</span>' . "\n";
		}

		$output .= '<div class="screenshot" id="' . $id . '-image">' . "\n";

		if ( $value != '' ) {
			$remove = '<a class="remove-image">Remove</a>';
			$image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
			if ( $image ) {
				$output .= '<img src="' . $value . '" alt="" />' . $remove;
			} else {
				$parts = explode( "/", $value );
				for( $i = 0; $i < sizeof( $parts ); ++$i ) {
					$title = $parts[$i];
				}

				// No output preview if it's not an image.
				$output .= '';

				// Standard generic output if it's not an image.
				$title = __( 'View File', 'textdomain' );
				$output .= '<div class="no-image"><span class="file_link"><a href="' . $value . '" target="_blank" rel="external">'.$title.'</a></span></div>';
			}
		}
		$output .= '</div>' . "\n";
		return $output;
	}

	/**
	 * Enqueue scripts for file uploader
	 */
	function wpr_optionsframework_media_scripts( $hook ) {

		$menu = Wpr_Options_Framework_Admin::menu_settings();

        if ( substr( $hook, -strlen( $menu['menu_slug'] ) ) !== $menu['menu_slug'] )
	        return;

		if ( function_exists( 'wp_enqueue_media' ) )
			wp_enqueue_media();

		wp_register_script( 'of-media-uploader', WPR_OPTIONS_FRAMEWORK_DIRECTORY .'js/media-uploader.js', array( 'jquery' ), Wpr_Options_Framework::VERSION );
		wp_enqueue_script( 'of-media-uploader' );
		wp_localize_script( 'of-media-uploader', 'wpr_optionsframework_l10n', array(
			'upload' => __( 'Upload', 'textdomain' ),
			'remove' => __( 'Remove', 'textdomain' )
		) );
	}
}