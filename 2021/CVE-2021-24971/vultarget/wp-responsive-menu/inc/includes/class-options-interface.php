<?php
/**
 * @package   Wpr_Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Wpr_Options_Framework_Interface {

	/**
	 * Generates the tabs that are used in the options menu
	 */
	static function wpr_optionsframework_tabs() {
		$counter = 0;
		$options = & Wpr_Options_Framework::_wpr_optionsframework_options();
		$menu = '';

		foreach ( $options as $value ) {
			// Heading for Navigation
			if ( $value['type'] == "heading" ) {
				$counter++;
				$class = '';
				$class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
				$class = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower($class) ) . '-tab';
				$menu .= '<a id="options-group-'.  $counter . '-tab" class="nav-tab ' . $class .'" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#options-group-'.  $counter ) . '">' . $value['name'] . '</a>';
			}
		}

		return $menu;
	}

	/**
	 * Generates the options fields that are used in the form.
	 */
	static function wpr_optionsframework_fields() {

		global $allowedtags;
		$wpr_optionsframework_settings = get_option( 'wpr_optionsframework' );

		// Gets the unique option id
		if ( isset( $wpr_optionsframework_settings['id'] ) ) {
			$option_name = $wpr_optionsframework_settings['id'];
		}
		else {
			$option_name = 'wpr_optionsframework';
		};

		$settings = get_option($option_name);
		$options = & Wpr_Options_Framework::_wpr_optionsframework_options();

		$counter = 0;
		$menu = '';

		foreach ( $options as $value ) {

			$val = '';
			$select_value = '';
			$output = '';

			// Wrap all options
			if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) ) {

				// Keep all ids lowercase with no spaces
				$value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['id']) );

				$id = 'section-' . $value['id'];

				$class = 'section';
				if ( isset( $value['type'] ) ) {
					$class .= ' section-' . $value['type'];
				}
				if ( isset( $value['class'] ) ) {
					$class .= ' ' . $value['class'];
				}

				// If there is a description save it for labels
				$explain_value = '';
				if ( isset( $value['desc'] ) ) {
					$explain_value = $value['desc'];
				}

				$output .= '<div id="' . esc_attr( $id ) .'" class="row ' . esc_attr( $class ) . '">'."\n";
				if ( isset( $value['name'] ) ) {
					$output .= '<div class="col-md-4 heading"><label class="explain" for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['name'] ) . '</label>';
					if( $explain_value != '' )
						$output .= '<span class="dashicons dashicons-editor-help pull-right" data-toggle="tooltip" data-placement="top" title="'. wp_kses( $explain_value, $allowedtags) .'"></span>';
					$output .= '</div>' . "\n";
				}
				if ( $value['type'] != 'editor' ) {
					$output .= '<div class="col-md-8 option">' . "\n" . '<div class="controls">' . "\n";
				}
				else {
					$output .= '<div class="option">' . "\n" . '<div>' . "\n";
				}
			}

			// Set default value to $val
			if ( isset( $value['std'] ) ) {
				$val = $value['std'];
			}

			// If the option is already saved, override $val
			if ( ( $value['type'] != 'heading' ) && ( $value['type'] != 'info') ) {
				if ( isset( $settings[($value['id'])]) ) {
					$val = $settings[($value['id'])];
					// Striping slashes of non-array options
					if ( !is_array($val) ) {
						$val = stripslashes( $val );
					}
				}
			}

			// Set the placeholder if one exists
			$placeholder = '';
			if ( isset( $value['placeholder'] ) ) {
				$placeholder = ' placeholder="' . esc_attr( $value['placeholder'] ) . '"';
			}

			if ( has_filter( 'wpr_optionsframework_' . $value['type'] ) ) {
				$output .= apply_filters( 'wpr_optionsframework_' . $value['type'], $option_name, $value, $val );
			}


			switch ( $value['type'] ) {

			// Basic text input
			case 'text':
				$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="form-control" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '"' . $placeholder . ' />';
				break;

			// Code
			case 'code':
				$output .= '<div id="' . esc_attr( $value['id'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '"></div>';
				$output .= '<textarea name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '_textarea" style="display: none">'.esc_attr( $val ).'</textarea>';
				break;

			// Password input
			case 'password':
				$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="password" value="' . esc_attr( $val ) . '" />';
				break;

			// Textarea
			case 'textarea':
				$rows = '8';

				if ( isset( $value['settings']['rows'] ) ) {
					$custom_rows = $value['settings']['rows'];
					if ( is_numeric( $custom_rows ) ) {
						$rows = $custom_rows;
					}
				}

				$val = stripslashes( $val );
				$output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '"' . $placeholder . '>' . esc_textarea( $val ) . '</textarea>';
				break;

			// Select Box
			case 'select':
				$output .= '<select class="form-control" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

				foreach ($value['options'] as $key => $option ) {
					$output .= '<option'. selected( $val, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
				}
				$output .= '</select>';
				break;


			// Radio Box
			case "radio":
				$name = $option_name .'['. $value['id'] .']';
				$output .= '<div class="btn-group" data-toggle="buttons">';
				foreach ($value['options'] as $key => $option) {
					$id = $option_name . '-' . $value['id'] .'-'. $key;
					$active = '';
					if( $val == $key ) $active = 'active';
					$output .= '<label class="btn btn-default '. $active .'" for="' . esc_attr( $id ) . '"><input class="of-input of-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' autocomplete="off" />' . esc_html( $option ) . '</label>';
				}
				$output .= '</div>';
				break;

			// Image Selectors
			case "images":
				$name = $option_name .'['. $value['id'] .']';
				foreach ( $value['options'] as $key => $option ) {
					$selected = '';
					if ( $val != '' && ($val == $key) ) {
						$selected = ' of-radio-img-selected';
					}
					$output .= '<input type="radio" id="' . esc_attr( $value['id'] .'_'. $key) . '" class="of-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" '. checked( $val, $key, false ) .' />';
					$output .= '<div class="of-radio-img-label">' . esc_html( $key ) . '</div>';
					$output .= '<img src="' . esc_url( $option ) . '" alt="' . $option .'" class="of-radio-img-img' . $selected .'" onclick="document.getElementById(\''. esc_attr($value['id'] .'_'. $key) .'\').checked=true;" />';
				}
				break;

			case "hidemenupages":
        $cats = $val;

			  $output .= '<select class="wprmenu-hide-menu-pages of-input" multiple="multiple" name="' . esc_attr( $option_name . '[' . $value['id'] . '][]' ) . '" id="' . esc_attr( $value['id'] ) . '">';
			  foreach ($value['options'] as $key => $option ) {
			    if( is_array( $cats) && !empty( $cats ) && isset($cats[$key] ) && $cats[$key] === $option ) {
			      $output .= '<option value="' . esc_attr( $key ) . '" selected="selected">' . esc_html( $option ) . '</option>';
			    }
			    else {
            $output .= '<option  value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
			    }
				}
				$output .= '</select>';
			  break;

			// Checkbox
			case "checkbox":
				$output .= '<label class="toggle"><input id="' . esc_attr( $value['id'] ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" '. checked( $val, 1, false) .' /><span class="handle"></span></label>';
				// $output .= '<span class="dashicons dashicons-editor-help" data-toggle="tooltip" data-placement="top" title="'. wp_kses( $explain_value, $allowedtags) .'"></span>';
				break;

			// Multicheck
			case "multicheck":
				foreach ($value['options'] as $key => $option) {
					$checked = '';
					$label = $option;
					$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));

					$id = $option_name . '-' . $value['id'] . '-'. $option;
					$name = $option_name . '[' . $value['id'] . '][' . $option .']';

					if ( isset($val[$option]) ) {
						$checked = checked($val[$option], 1, false);
					}

					$output .= '<input id="' . esc_attr( $id ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $name ) . '" ' . $checked . ' /><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
				}
				break;

			// Color picker
			case "color":
				$default_color = '';
				if ( isset($value['std']) ) {
					if ( $val !=  $value['std'] )
						$default_color = ' data-default-color="' .$value['std'] . '" ';
				}
				$output .= '<input name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '" class="of-color"  type="text" value="' . esc_attr( $val ) . '"' . $default_color .' />';

				break;

			// Uploader
			case "upload":
				$output .= Wpr_Options_Framework_Media_Uploader::wpr_optionsframework_uploader( $value['id'], $val, null );

				break;
			//Number field
			case "number":
				$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="number" value="' . esc_attr( $val ) . '"' . $placeholder . ' />';
				break;

			//Icon field
			case "icon":
				$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input wpr-icon-picker" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '"' . $placeholder . ' />';
				break;

			//Sort Field
				case "menusort":
				$order = empty($val) ? 'Menu,Search,Social' : $val;
				$output .= '<input type="hidden" id="' . esc_attr( $value['id'] ) . '" class="of-input" name="'.esc_attr( $option_name . '[order_menu_items]' ).'"  value="'.$val.'"><ul id="wpr-sortable" class="list-group">';
				foreach( explode(',', $order) as $item ) {
					if( $item == 'Social' ) {
						$class = 'facebook';
						$disabled = 'disabled';
					}
					else {
						$class = $item;
						$disabled = '';
					}
					$output .= '<li class=" '.$disabled.' list-group-item list-group-item-primary " id="'.$item.'"><i class="wpr-icon-'.strtolower($class).'"></i>'.$item.'</li>';
				}
				$output .= '</ul>';
				break;

			//Repeater field for social icons
			case "social" :
				$socials = json_decode( $val );
				$output .='<div class="wpr-new-field"><input type="button" class="wpr-add-new btn btn-success" value="Add New"></div><div class="wpr-social-fields">';
				
				if( is_array ( $socials ) && count( $socials ) > 0 ) {
					foreach( $socials as $social ) {
						
						$output .= '<div class="wpr-new-fields"><input type="text" name="' . esc_attr( $option_name . '[' . $value['id'] . '][icon][]' ) . '" class="wpr-icon-picker"  value="'.$social->icon.'">';
						
						$output .= '<input type="text" name="' . esc_attr( $option_name . '[' . $value['id'] . '][link][]' ) . '" class="social_link form-control" value="'.$social->link.'"><input type="button" class="button" value="Remove" /></div>';

					}
				}
				else {
						$output .= '<div class="wpr-new-fields"><input type="text" name="' . esc_attr( $option_name . '[' . $value['id'] . '][icon][]' ) . '" class="wpr-icon-picker"  value="">';
						
						$output .= '<input type="text" placeholder="Enter your url here" name="' . esc_attr( $option_name . '[' . $value['id'] . '][link][]' ) . '" class="' . esc_attr( $value['id'] . '_link form-control' ) . '"  value=""><input type="button" class="btn btn-danger" value="Remove" /></div>';
				}
				$output .= '</div>';
			break;

			// Showcase and demo import
			case "showcase":
				$output .= '<div class="wprmenu-showcase-wrapper">';
				$output .= Wpr_Options_Framework_Interface::wprmenu_get_demodata();
				$output .= '</div>';
			break;

			// Background
			case 'background':

				$background = $val;

				// Background Color
				$default_color = '';
				if ( isset( $value['std']['color'] ) ) {
					if ( $val !=  $value['std']['color'] )
						$default_color = ' data-default-color="' .$value['std']['color'] . '" ';
				}
				$output .= '<input name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" class="of-color of-background-color"  type="text" value="' . esc_attr( $background['color'] ) . '"' . $default_color .' />';

				// Background Image
				if ( !isset($background['image']) ) {
					$background['image'] = '';
				}

				$output .= Wpr_Options_Framework_Media_Uploader::wpr_optionsframework_uploader( $value['id'], $background['image'], null, esc_attr( $option_name . '[' . $value['id'] . '][image]' ) );

				$class = 'of-background-properties';
				if ( '' == $background['image'] ) {
					$class .= ' hide';
				}
				$output .= '<div class="' . esc_attr( $class ) . '">';

				// Background Repeat
				$output .= '<select class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $value['id'] . '][repeat]'  ) . '" id="' . esc_attr( $value['id'] . '_repeat' ) . '">';
				$repeats = wpr_of_recognized_background_repeat();

				foreach ($repeats as $key => $repeat) {
					$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>'. esc_html( $repeat ) . '</option>';
				}
				$output .= '</select>';

				// Background Position
				$output .= '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position]' ) . '" id="' . esc_attr( $value['id'] . '_position' ) . '">';
				$positions = wpr_of_recognized_background_position();

				foreach ($positions as $key=>$position) {
					$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position'], $key, false ) . '>'. esc_html( $position ) . '</option>';
				}
				$output .= '</select>';

				// Background Attachment
				$output .= '<select class="of-background of-background-attachment" name="' . esc_attr( $option_name . '[' . $value['id'] . '][attachment]' ) . '" id="' . esc_attr( $value['id'] . '_attachment' ) . '">';
				$attachments = wpr_of_recognized_background_attachment();

				foreach ($attachments as $key => $attachment) {
					$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['attachment'], $key, false ) . '>' . esc_html( $attachment ) . '</option>';
				}
				$output .= '</select>';
				$output .= '</div>';

				break;

			// Editor
			case 'editor':
				// $output .= '<span class="dashicons dashicons-editor-help" data-toggle="tooltip" data-placement="top" title="'. wp_kses( $explain_value, $allowedtags) .'"></span>'."\n";
				echo $output;
				$textarea_name = esc_attr( $option_name . '[' . $value['id'] . ']' );
				$default_editor_settings = array(
					'textarea_name' => $textarea_name,
					'media_buttons' => false,
					'tinymce' => array( 'plugins' => 'wordpress' )
				);
				$editor_settings = array();
				if ( isset( $value['settings'] ) ) {
					$editor_settings = $value['settings'];
				}
				$editor_settings = array_merge( $default_editor_settings, $editor_settings );
				wp_editor( $val, $value['id'], $editor_settings );
				$output = '';
				break;

			// Info
			case "info":
				$id = '';
				$class = 'section';
				if ( isset( $value['id'] ) ) {
					$id = 'id="' . esc_attr( $value['id'] ) . '" ';
				}
				if ( isset( $value['type'] ) ) {
					$class .= ' section-' . $value['type'];
				}
				if ( isset( $value['class'] ) ) {
					$class .= ' ' . $value['class'];
				}

				$output .= '<div ' . $id . 'class="' . esc_attr( $class ) . '">' . "\n";
				if ( isset($value['name']) ) {
					$output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
				}
				if ( isset( $value['desc'] ) ) {
					$output .= apply_filters('wpr_of_sanitize_info', $value['desc'] ) . "\n";
				}
				$output .= '</div>' . "\n";
				break;

			// Heading for Navigation
			case "heading":
				$counter++;
				if ( $counter >= 2 ) {
					$output .= '</div>'."\n";
				}
				$class = '';
				$class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
				$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
				$output .= '<div id="options-group-' . $counter . '" class="group ' . $class . '">';
				$output .= '<h3>' . esc_html( $value['name'] ) . '</h3>' . "\n";
				break;
			}

			if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) ) {
				$output .= '</div>';
				if ( ( $value['type'] != "checkbox" ) && ( $value['type'] != "editor" ) ) {
					// $output .= '<span class="dashicons dashicons-editor-help" data-toggle="tooltip" data-placement="top" title="'. wp_kses( $explain_value, $allowedtags) .'"></span>'."\n";
				}
				$output .= '</div></div>'."\n";
			}

			echo $output;
		}

		// Outputs closing div if there tabs
		if ( Wpr_Options_Framework_Interface::wpr_optionsframework_tabs() != '' ) {
			echo '</div>';
		}
	}


	//Get demo data
	static function wprmenu_get_demodata() {
		
		$items = get_transient('wprm_api_demo_items_list');
		
		if( ! $items )
			$items = Wpr_Options_Framework_Interface::wprm_fetch_demo_items();


		$data = '';
		$class = '';
		$data .= '<h6 class="wprmenu-demo">'.__( 'Easy Setup With Our Predefined Demo', 'wprmenu' ).'</h6>';
		
		if( is_object($items) ) {
			
			if( isset($items->Free) && !empty($items->Free) ) {
				$data .= '<h6 class="wpr-demo-type">'.__('Free Demo', 'wprmenu').'</h6>';
				$data .= '<ul class="wprmenu-demo-list">';
				foreach( $items->Free as $key => $val ) {
					$image_path = $val->image_path;
					$data .= '<li class="wprmenu-data-list">';
					$data .= '<div class="wprmenu-content image-overlay" >';
					$data .= '<div data-demo-type="Free" data-demo-id="'.$val->demo_id.'"  data-settings="'.$val->settings.'" class="wprmenu-content-image" style="background-image: url('.$image_path.'); "></div>';
					$data .= '<span class="view-demo"><a target="_blank" href="'.$val->demo_url.'">'.__('View Demo', 'wprmenu').'</a></span>';
					$data .= '<span class="wprmenu-data import-demo">'.__('Import Demo', 'wprmenu').'</span>';
					$data .= '</div>';
					$data .= '</li>';
				}
				$data .= '</ul>';
			}

			if( isset($items->Pro) && !empty($items->Pro) ) {
				$data .= '<h6 class="wpr-demo-type">'.__('Pro Demo', 'wprmenu').'</h6>';
				$data .= '<ul class="wprmenu-demo-list">';
				foreach( $items->Pro as $key => $val ) {
					$image_path = $val->image_path;
					$data .= '<li class="wprmenu-data-list">';
					$data .= '<div class="wprmenu-content image-overlay">';
					$data .= '<div data-demo-type="Pro"  class="wprmenu-content-image free-version" style="background-image: url('.$image_path.'); "></div>';
					$data .= '<span class="view-demo"><a target="_blank" href="'.$val->demo_url.'">'.__('View Demo', 'wprmenu').'</a></span>';
					$data .= '<span class="wprmenu-data import-demo">'.__('Import Requires Pro Version', 'wprmenu').'</span>';
					$data .= '</div>';
					$data .= '</li>';
				}
				$data .= '</ul>';
			}
		}
		
		return $data;
	}

	static function wprm_fetch_demo_items() {
		$site_name = MG_WPRM_DEMO_SITE_URL;
		$remoteLink = $site_name.'/wp-json/wprmenu-server/v1';

		$data = wp_remote_get($remoteLink);
		$items = array();

		if( is_array($data) && isset($data['body']) ) {
			$items = $data['body'];
			$items = json_decode($items);
			set_transient('wprm_api_demo_items_list', $items, 60 * 60 * 24);	
		}
		return $items;
	}

}