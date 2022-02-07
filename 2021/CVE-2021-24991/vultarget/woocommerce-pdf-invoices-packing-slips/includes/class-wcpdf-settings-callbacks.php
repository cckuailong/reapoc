<?php
namespace WPO\WC\PDF_Invoices;

use WPO\WC\PDF_Invoices\Documents\Sequential_Number_Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Settings_Callbacks' ) ) :

class Settings_Callbacks {
	/**
	 * Section null callback.
	 *
	 * @return void.
	 */
	public function section() {
	}
	
	/**
	 * Debug section callback.
	 *
	 * @return void.
	 */
	public function debug_section() {
		_e( '<b>Warning!</b> The settings below are meant for debugging/development only. Do not use them on a live website!' , 'woocommerce-pdf-invoices-packing-slips' );
	}
	
	/**
	 * Custom fields section callback.
	 *
	 * @return void.
	 */
	public function custom_fields_section() {
		_e( 'These are used for the (optional) footer columns in the <em>Modern (Premium)</em> template, but can also be used for other elements in your custom template' , 'woocommerce-pdf-invoices-packing-slips' );
	}

	/**
	 * Checkbox callback.
	 *
	 * args:
	 *   option_name - name of the main option
	 *   id          - key of the setting
	 *   value       - value if not 1 (optional)
	 *   default     - default setting (optional)
	 *   description - description (optional)
	 *
	 * @return void.
	 */
	public function checkbox( $args ) {
		extract( $this->normalize_settings_args( $args ) );

		// output checkbox	
		printf( '<input type="checkbox" id="%1$s" name="%2$s" value="%3$s" %4$s %5$s/>', $id, $setting_name, $value, checked( $value, $current, false ), !empty($disabled) ? 'disabled="disabled"' : '' );

		// print store empty input if true
		if( $store_unchecked ) {
			printf( '<input type="hidden" name="%s[wpo_wcpdf_setting_store_empty][]" value="%s"/>', $option_name, $id );
		}
	
		// output description.
		if ( isset( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}
	}

	/**
	 * Text input callback.
	 *
	 * args:
	 *   option_name - name of the main option
	 *   id          - key of the setting
	 *   size        - size of the text input (em)
	 *   default     - default setting (optional)
	 *   description - description (optional)
	 *   type        - type (optional)
	 *
	 * @return void.
	 */
	public function text_input( $args ) {
		extract( $this->normalize_settings_args( $args ) );

		if (empty($type)) {
			$type = 'text';
		}

		printf( '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" size="%5$s" placeholder="%6$s" %7$s/>', $type, $id, $setting_name, esc_attr( $current ), $size, $placeholder, !empty($disabled) ? 'disabled="disabled"' : '' );
	
		// output description.
		if ( isset( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}
	}

	/**
	 * Combined checkbox & text input callback.
	 *
	 * args:
	 *   option_name - name of the main option
	 *   id          - key of the setting
	 *   value       - value if not 1 (optional)
	 *   default     - default setting (optional)
	 *   description - description (optional)
	 *
	 * @return void.
	 */
	public function checkbox_text_input( $args ) {
		$args = $this->normalize_settings_args( $args );
		extract( $args );
		unset($args['description']); // already extracted, should only be used here
		
		// get checkbox	
		ob_start();
		$this->checkbox( $args );
		$checkbox = ob_get_clean();

		// get text input for insertion in wrapper
		$input_args = array(
			'id'			=> $args['text_input_id'],
			'default'		=> isset( $args['text_input_default'] ) ? (string) $args['text_input_default'] : NULL,
			'size'			=> isset( $args['text_input_size'] ) ? $args['text_input_size'] : NULL,
		)  + $args;
		unset($input_args['current']);

		ob_start();
		$this->text_input( $input_args );
		$text_input = ob_get_clean();

		if (!empty($text_input_wrap)) {
		 	printf( "{$checkbox} {$text_input_wrap}", $text_input);
		} else {
			echo "{$checkbox} {$text_input}";
		}
	
		// output description.
		if ( isset( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}
	}


	// Single text option (not part of any settings array)
	public function singular_text_element( $args ) {
		$option_name = $args['option_name'];
		$id = $args['id'];
		$size = isset( $args['size'] ) ? $args['size'] : '25';
		$class = isset( $args['translatable'] ) && $args['translatable'] === true ? 'translatable' : '';
	
		$option = get_option( $option_name );

		if ( isset( $option ) ) {
			$current = $option;
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}
	
		$html = sprintf( '<input type="text" id="%1$s" name="%2$s" value="%3$s" size="%4$s" class="%5$s"/>', $id, $option_name, $current, $size, $class );
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			$html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
	
		echo $html;
	}


	/**
	 * Textarea callback.
	 *
	 * args:
	 *   option_name - name of the main option
	 *   id          - key of the setting
	 *   width       - width of the text input (em)
	 *   height      - height of the text input (lines)
	 *   default     - default setting (optional)
	 *   description - description (optional)
	 *
	 * @return void.
	 */
	public function textarea( $args ) {
		extract( $this->normalize_settings_args( $args ) );
	
		printf( '<textarea id="%1$s" name="%2$s" cols="%4$s" rows="%5$s" placeholder="%6$s"/>%3$s</textarea>', $id, $setting_name, esc_textarea( $current ), $width, $height, $placeholder );
	
		// output description.
		if ( isset( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}
	}

	/**
	 * Select element callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Select field.
	 */
	public function select( $args ) {
		extract( $this->normalize_settings_args( $args ) );
	
		if ( isset( $enhanced_select ) ) {
			if ( isset( $multiple ) ) {
				$setting_name = "{$setting_name}[]";
				$multiple = 'multiple=multiple';
			} else {
				$multiple = '';
			}

			$placeholder = isset($placeholder) ? esc_attr( $placeholder ) : '';
			$title = isset($title) ? esc_attr( $title ) : '';
			$class = 'wc-enhanced-select wpo-wcpdf-enhanced-select';
			$css = 'width:400px';
			printf( '<select id="%1$s" name="%2$s" data-placeholder="%3$s" title="%4$s" class="%5$s" style="%6$s" %7$s>', $id, $setting_name, $placeholder, $title, $class, $css, $multiple );
		} else {
			printf( '<select id="%1$s" name="%2$s">', $id, $setting_name );
		}

		foreach ( $options as $key => $label ) {
			if ( isset( $multiple ) && is_array( $current ) ) {
				$selected = in_array($key, $current) ? ' selected="selected"' : '';
				printf( '<option value="%s"%s>%s</option>', $key, $selected, $label );
			} else {
				printf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
			}
		}

		echo '</select>';

		if (isset($custom)) {
			printf( '<div class="%1$s_custom custom">', $id );

			if (is_callable( array( $this, $custom['type'] ) ) ) {
				$this->{$custom['type']}( $custom['args'] );
			}
			echo '</div>';
			$custom_option = ! empty( $custom['custom_option'] ) ? $custom['custom_option'] : 'custom';
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				function check_<?php echo $id; ?>_custom() {
					var custom = $('#<?php echo $id; ?>').val();
					if (custom == '<?php echo $custom_option; ?>') {
						$( '.<?php echo $id; ?>_custom').show();
					} else {
						$( '.<?php echo $id; ?>_custom').hide();
					}
				}

				check_<?php echo $id; ?>_custom();

				$( '#<?php echo $id; ?>' ).on( 'change', function() {
					check_<?php echo $id; ?>_custom();
				});

			});
			</script>
			<?php
		}
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', $args['description'] );
		}

	}

	public function radio_button( $args ) {
		extract( $this->normalize_settings_args( $args ) );
	
		foreach ( $options as $key => $label ) {
			printf( '<input type="radio" class="radio" id="%1$s[%3$s]" name="%2$s" value="%3$s"%4$s />', $id, $setting_name, $key, checked( $current, $key, false ) );
			printf( '<label for="%1$s[%3$s]"> %4$s</label><br>', $id, $setting_name, $key, $label);
		}
		
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', $args['description'] );
		}

	}

	/**
	 * Multiple text element callback.
	 * @param  array $args Field arguments.
	 * @return string	   Text input field.
	 */
	public function multiple_text_input( $args ) {
		extract( $this->normalize_settings_args( $args ) );

		if (!empty($header)) {
			echo "<p><strong>{$header}</strong>:</p>";
		}

		printf('<p class="%s multiple-text-input">', $id);
		foreach ($fields as $name => $field) {
			$size = $field['size'];
			$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';

			if (isset($field['label_width'])) {
				$style = sprintf( 'style="display:inline-block; width:%1$s;"', $field['label_width'] );
			} else {
				$style = '';
			}

			$description = isset( $field['description'] ) ? '<span style="font-style:italic;">'.$field['description'].'</span>' : '';

			// output field label
			if (isset($field['label'])) {
				printf( '<label for="%1$s_%2$s" %3$s>%4$s:</label>', $id, $name, $style, $field['label'] );
			}

			// output field
			$field_current = isset($current[$name]) ? $current[$name] : '';
			$type = isset( $field['type'] ) ? $field['type'] : 'text';
			printf( '<input type="%1$s" id="%2$s_%4$s" name="%3$s[%4$s]" value="%5$s" size="%6$s" placeholder="%7$s"/> %8$s<br/>', $type, $id, $setting_name, $name, esc_attr( $field_current ), $size, $placeholder, $description );
		}
		echo "</p>";
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', $args['description'] );
		}
	}

	/**
	 * Multiple text element callback.
	 * @param  array $args Field arguments.
	 * @return string	   Text input field.
	 */
	public function multiple_checkboxes( $args ) {
		extract( $this->normalize_settings_args( $args ) );

		foreach ($fields as $name => $label) {
			// $label = $field['label'];

			// output checkbox	
			$field_current = isset($current[$name]) ? $current[$name] : '';
			printf( '<input type="checkbox" id="%1$s_%3$s" name="%2$s[%3$s]" value="%4$s"%5$s />', $id, $setting_name, $name, $value, checked( $value, $field_current, false ) );

			// output field label
			printf( '<label for="%1$s_%2$s">%3$s</label><br>', $id, $name, $label );

		}
	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', $args['description'] );
		}
	}

	/**
	 * Media upload callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Media upload button & preview.
	 */
	public function media_upload( $args ) {
		extract( $this->normalize_settings_args( $args ) );

		if( !empty($current) && $attachment = wp_get_attachment_image_src( $current, 'full', false ) ) {
			$general_settings = get_option('wpo_wcpdf_settings_general');
			$attachment_src = $attachment[0];
			$attachment_width = $attachment[1];
			$attachment_height = $attachment[2];
			// check if we have the height saved on settings
			$header_logo_height = !empty($general_settings['header_logo_height']) ? $general_settings['header_logo_height'] : '3cm';
			if ( stripos( $header_logo_height, 'mm' ) != false ) {
				$in_height = floatval($header_logo_height)/25.4;
			} elseif ( stripos( $header_logo_height, 'cm' ) != false ) {
				$in_height = floatval($header_logo_height)/2.54;
			} elseif ( stripos( $header_logo_height, 'in' ) != false ) {
				$in_height = floatval($header_logo_height);
			} else {
				// don't display resolution
			}

			printf('<img src="%1$s" style="display:block" id="img-%4$s"/>', $attachment_src, $attachment_width, $attachment_height, $id );
			if ( !empty($attachment_height) && !empty($in_height) ) {
				$attachment_resolution = round(absint($attachment_height)/$in_height);
				printf(
					'<div class="attachment-resolution"><p class="description">%s: %sdpi</p></div>',
					__( 'Image resolution', 'woocommerce-pdf-invoices-packing-slips' ),
					$attachment_resolution
				);

				// warn the user if the image is unnecessarily large
				if ($attachment_resolution > 600 ) {
					printf(
						'<div class="attachment-resolution-warning notice notice-warning inline"><p>%s</p></div>',
						esc_html__( 'The image resolution exceeds the recommended maximum of 600dpi. This will unnecessarily increase the size of your PDF files and could negatively affect performance.', 'woocommerce-pdf-invoices-packing-slips' )
					); 
				}
			}

			printf('<span class="button wpo_remove_image_button" data-input_id="%1$s">%2$s</span> ', $id, $remove_button_text );
		}

		printf( '<input id="%1$s" name="%2$s" type="hidden" value="%3$s" data-settings_callback_args="%4$s" data-ajax_nonce="%5$s"/>', $id, $setting_name, $current, esc_attr( json_encode( $args ) ), wp_create_nonce( "wpo_wcpdf_get_media_upload_setting_html" ) );
		
		printf( '<span class="button wpo_upload_image_button %4$s" data-uploader_title="%1$s" data-uploader_button_text="%2$s" data-remove_button_text="%3$s" data-input_id="%4$s">%2$s</span>', $uploader_title, $uploader_button_text, $remove_button_text, $id );
	
		// Displays option description.
		if ( isset( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}
	}

	/**
	 * Next document number edit callback.
	 *
	 * @param  array $args Field arguments.
	 */
	public function next_number_edit( $args ) {
		extract( $args );
		$number_store_method = WPO_WCPDF()->settings->get_sequential_number_store_method();
		$number_store = new Sequential_Number_Store( $store, $number_store_method );
		$next_number = $number_store->get_next();
		$nonce = wp_create_nonce( "wpo_wcpdf_next_{$store}" );
		printf( '<input id="next_%1$s" class="next-number-input" type="text" size="%2$s" value="%3$s" disabled="disabled" data-store="%1$s" data-nonce="%4$s"/> <span class="edit-next-number dashicons dashicons-edit"></span><span class="save-next-number button secondary" style="display:none;">%5$s</span>', $store, $size, $next_number, $nonce, __( 'Save', 'woocommerce-pdf-invoices-packing-slips' ) );
		// Displays option description.
		if ( isset( $description ) ) {
			printf( '<p class="description">%s</p>', $description );
		}
	}	

	/**
	 * Wrapper function to create tabs for settings in different languages
	 * @param  [type] $args     [description]
	 * @param  [type] $callback [description]
	 * @return [type]           [description]
	 */
	public function i18n_wrap ( $args ) {
		extract( $this->normalize_settings_args( $args ) );

		if ( $languages = $this->get_languages() ) {
			printf( '<div id="%s-%s-translations" class="translations">', $option_name, $id)
			?>
				<ul>
					<?php foreach ( $languages as $lang_code => $language_name ) {
						$translation_id = "{$option_name}_{$id}_{$lang_code}";
						printf('<li><a href="#%s">%s</a></li>', $translation_id, $language_name );
					}
					?>
				</ul>
				<?php foreach ( $languages as $lang_code => $language_name ) {
					$translation_id = "{$option_name}_{$id}_{$lang_code}";
					printf( '<div id="%s">', $translation_id );
					$args['lang'] = $lang_code;
					// don't use internationalized placeholders since they're not translated,
					// to avoid confusion (user thinking they're all the same)
					if ( $callback == 'multiple_text_input' ) {
						foreach ($fields as $key => $field_args) {
							if (!empty($field_args['placeholder']) && isset($field_args['i18n_placeholder'])) {
								$args['fields'][$key]['placeholder'] = '';
							}
						}
					} else {
						if (!empty($args['placeholder']) && isset($args['i18n_placeholder'])) {
							$args['placeholder'] = '';
						}
					}
					// specific description for internationalized fields (to compensate for missing placeholder)
					if (!empty($args['i18n_description'])) {
						$args['description'] = $args['i18n_description'];
					}
					if ( is_array( $callback ) ) {
						call_user_func( $callback, $args );
					} else {
						call_user_func( array( $this, $callback ), $args );
					}
					echo '</div>';
				}
				?>
			
			</div>
			<?php
		} else {
			$args['lang'] = 'default';
			if ( is_array( $callback ) ) {
				call_user_func( $callback, $args );
			} else {
				call_user_func( array( $this, $callback ), $args );
			}
		}
	}

	public function get_languages () {
		$multilingual = function_exists('icl_get_languages');
		// $multilingual = true; // for development

		if ($multilingual) {
			// use this instead of function call for development outside of WPML
			// $icl_get_languages = 'a:3:{s:2:"en";a:8:{s:2:"id";s:1:"1";s:6:"active";s:1:"1";s:11:"native_name";s:7:"English";s:7:"missing";s:1:"0";s:15:"translated_name";s:7:"English";s:13:"language_code";s:2:"en";s:16:"country_flag_url";s:43:"http://yourdomain/wpmlpath/res/flags/en.png";s:3:"url";s:23:"http://yourdomain/about";}s:2:"fr";a:8:{s:2:"id";s:1:"4";s:6:"active";s:1:"0";s:11:"native_name";s:9:"Français";s:7:"missing";s:1:"0";s:15:"translated_name";s:6:"French";s:13:"language_code";s:2:"fr";s:16:"country_flag_url";s:43:"http://yourdomain/wpmlpath/res/flags/fr.png";s:3:"url";s:29:"http://yourdomain/fr/a-propos";}s:2:"it";a:8:{s:2:"id";s:2:"27";s:6:"active";s:1:"0";s:11:"native_name";s:8:"Italiano";s:7:"missing";s:1:"0";s:15:"translated_name";s:7:"Italian";s:13:"language_code";s:2:"it";s:16:"country_flag_url";s:43:"http://yourdomain/wpmlpath/res/flags/it.png";s:3:"url";s:26:"http://yourdomain/it/circa";}}';
			// $icl_get_languages = unserialize($icl_get_languages);
			
			$icl_get_languages = icl_get_languages('skip_missing=0');
			$languages = array();
			foreach ($icl_get_languages as $lang => $data) {
				$languages[$data['language_code']] = $data['native_name'];
			}
		} else {
			return false;
		}

		return $languages;
	}

	public function normalize_settings_args ( $args ) {
		$args['value'] = isset( $args['value'] ) ? $args['value'] : 1;

		$args['placeholder'] = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
		$args['store_unchecked'] = isset( $args['store_unchecked'] ) && $args['store_unchecked'] ? true : false;

		// get main settings array
		$option = get_option( $args['option_name'] );
	
		$args['setting_name'] = "{$args['option_name']}[{$args['id']}]";

		if ( !isset($args['lang']) && !empty($args['translatable']) ) {
			$args['lang'] = 'default';
		}

		if ( ! array_key_exists( 'current', $args ) ) {
			if (isset($args['lang'])) {
				// i18n settings name
				$args['setting_name'] = "{$args['setting_name']}[{$args['lang']}]";
				// copy current option value if set
				
				if ( $args['lang'] == 'default' && !empty($option[$args['id']]) && !isset( $option[$args['id']]['default'] ) ) {
					// we're switching back from WPML to normal
					// try english first
					if ( isset( $option[$args['id']]['en'] ) ) {
						$args['current'] = $option[$args['id']]['en'];
					} elseif ( is_array( $option[$args['id']] ) ) {
						// fallback to the first language if english not found
						$first = array_shift($option[$args['id']]);
						if (!empty($first)) {
							$args['current'] = $first;
						}
					} elseif ( is_string( $option[$args['id']] ) ) {
						$args['current'] = $option[$args['id']];
					} else {
						// nothing, really?
						$args['current'] = '';
					}
				} else {
					if ( isset( $option[$args['id']][$args['lang']] ) ) {
						$args['current'] = $option[$args['id']][$args['lang']];
					} elseif (isset( $option[$args['id']]['default'] )) {
						$args['current'] = $option[$args['id']]['default'];
					}
				}
			} else {
				// copy current option value if set
				if ( isset( $option[$args['id']] ) ) {
					$args['current'] = $option[$args['id']];
				}
			}
		}

		// falback to default or empty if no value in option
		if ( !isset($args['current']) ) {
			$args['current'] = isset( $args['default'] ) ? $args['default'] : '';
		} elseif ( empty($args['current']) && isset($args['default_if_empty']) && $args['default_if_empty'] == true ) { // force fallback if empty 'current' and 'default_if_empty' equals to true
			$args['current'] = isset( $args['default'] ) ? $args['default'] : '';
		}

		return $args;
	}

	/**
	 * Validate options.
	 *
	 * @param  array $input options to valid.
	 *
	 * @return array		validated options.
	 */
	public function validate( $input ) {
		// echo '<pre>';var_dump($input);die('</pre>');
		// Create our array for storing the validated options.
		$output = array();

		if (empty($input) || !is_array($input)) {
			return $input;
		}

		if (!empty($input['wpo_wcpdf_setting_store_empty'])) { //perhaps we should use a more unique/specific name for this
			foreach ($input['wpo_wcpdf_setting_store_empty'] as $key) {
				if (empty($input[$key])) {
					$output[$key] = 0;
				}
			}
			unset($input['wpo_wcpdf_setting_store_empty']);
		}
	
		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {
	
			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[$key] ) ) {
				if ( is_array( $input[$key] ) ) {
					foreach ( $input[$key] as $sub_key => $sub_value ) {
						$output[$key][$sub_key] = $input[$key][$sub_key];
					}
				} else {
					$output[$key] = $input[$key];
				}
			}
		}
	
		// Return the array processing any additional functions filtered by this action.
		return apply_filters( 'wpo_wcpdf_validate_input', $output, $input );
	}
}


endif; // class_exists

return new Settings_Callbacks();