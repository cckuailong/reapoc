<?php
/**
 * class CFF_Theme_CSS
 */

namespace CustomFacebookFeed\Builder;


class CFF_Theme_CSS {

	const WRAP_SELECTOR = '.cff-preview-ctn';

	/**
	 * @var string
	 */
	var $file;

	/**
	 * @var string
	 */
	var $css;

	/**
	 * @var array
	 */
	var $parsed;

	/**
	 * @var array
	 */
	var $styles;

	public function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Whether or not a cache exists for this stylesheet. Updates daily or when the theme's stylesheet changes
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function is_cached() {
		$stored_styles = get_option( 'cff_theme_styles', array( 'file' => '', 'last_checked' => 0, 'styles' => array() ) );

		if ( empty( $stored_styles['file'] )
		     || $stored_styles['file'] !== $this->file ) {
			return false;
		}

		if ( empty( $stored_styles['last_checked'] )
		     || $stored_styles['last_checked'] < (time() - DAY_IN_SECONDS) ) {
			return false;
		}

		$this->styles = $stored_styles['styles'];

		return true;
	}

	/**
	 * Stores the styles in a wp_option
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function cache() {
		$stored_styles = get_option( 'cff_theme_styles', array( 'file' => '', 'last_checked' => 0, 'styles' => array() ) );

		$stored_styles['file'] = $this->file;
		$stored_styles['styles'] = $this->styles;
		$stored_styles['last_checked'] = time();

		return update_option( 'cff_theme_styles', $stored_styles, false );
	}

	/**
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_styles() {
		return $this->styles;
	}

	/**
	 * Makes an HTTP request to get the contents of the stylesheet
	 *
	 * @since 4.0
	 */
	public function load_css() {
		$url = $this->file;
		$args = array(
			'timeout' => 60,
			'sslverify' => false
		);
		$response = wp_remote_get( esc_url_raw( $url ), $args );

		if ( ! is_wp_error( $response ) ) {
			// certain ways of representing the html for double quotes causes errors so replaced here.
			$this->css = $response['body'];
		} else {
			$this->css = false;
		}
	}

	/**
	 * Uses a regex to detect selectors and styles and coverts them to key => value pairs
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function parse() {
		if ( empty( $this->css ) ) {
			return false;
		}
		$css = $this->css;
		preg_match_all( '/(?ims)([a-z0-9\s\.\:#_\-@,]+)\{([^\}]*)\}/', $css, $arr);
		$result = array();
		foreach ( $arr[0] as $i => $x ){
			$selector = trim( $arr[1][ $i ] );
			$rules = explode( ';', trim( $arr[2][ $i ] ) );
			$rules_arr = array();
			foreach ( $rules as $strRule ) {
				if ( !empty( $strRule ) ) {
					$rule = explode( ":", $strRule );
					$rule_0 = isset( $rule[0] ) ? $rule[0] : 'null';
					$rule_1 = isset( $rule[1] ) ? $rule[1] : '';
					$rules_arr[ trim($rule_0) ] = trim( $rule_1 );
				}
			}

			$selectors = explode(',', trim( $selector ) );
			foreach ( $selectors as $strSel ) {
				if ( ! isset( $result[ $strSel ] ) ) {
					$result[ $strSel ] = $rules_arr;
				} else {
					$result[ $strSel . '_2' ] = $rules_arr;
				}
			}
		}

		$this->parsed = $result;
	}

	/**
	 * Looks for styles based on specified selectors that are used
	 * in generating the style HTML
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function find_styles() {
		if ( empty( $this->css ) ) {
			return array();
		}
		foreach ( $this->parsed as $selector => $property_array ) {
			foreach ( $property_array as $property => $style ) {
				$this->process( $selector, $property, $style );
			}
		}
	}

	/**
	 * Loop through all selectors and see if they can be used in our generated
	 * style HTML
	 *
	 * @param string $selector
	 * @param string $property
	 * @param string $style
	 *
	 * @since 4.0
	 */
	public function process( $selector, $property, $style ) {
		$selector = trim( $selector );
		if ( $selector === 'body' ) {
			if ( in_array( $property, array( 'color', 'background-color', 'background', 'font-size' ), true ) ) {
				if ( ! isset( $this->styles[ $selector ]['properties'][ $property ]['style'] ) ) {
					$this->styles[ $selector ]['properties'][ $property ]['style'] = $style;
				}
			}
		} elseif ( $selector === 'a' ) {

			if ( in_array( $property, array( 'color', 'font-weight', 'text-decoration' ), true ) ) {
				if ( ! isset( $this->styles[ $selector ]['properties'][ $property ]['style'] ) ) {
					$this->styles[ $selector ]['properties'][ $property ]['style'] = $style;
				}
			}
		} elseif ( $selector === 'a:hover' ) {
			if ( in_array( $property, array( 'color', 'font-weight', 'text-decoration' ), true ) ) {
				if ( ! isset( $this->styles[ $selector ]['properties'][ $property ]['style'] ) ) {
					$this->styles[ $selector ]['properties'][ $property ]['style'] = $style;
				}
			}
		} elseif ( $selector === 'p' ) {
			if ( in_array( $property, array( 'color', 'font-weight', 'font-size' ), true ) ) {
				if ( ! isset( $this->styles[ $selector ]['properties'][ $property ]['style'] ) ) {
					$this->styles[ $selector ]['properties'][ $property ]['style'] = $style;
				}
			}
		} elseif ( $selector === 'h3' ) {
			if ( in_array( $property, array( 'color', 'font-weight', 'font-size' ), true ) ) {
				if ( ! isset( $this->styles[ $selector ]['properties'][ $property ]['style'] ) ) {
					$this->styles[ $selector ]['properties'][ $property ]['style'] = $style;
				}
			}
		} elseif ( $selector === '.entry-content' ) {
			if ( in_array( $property, array( 'color', 'font-size' ), true ) ) {
				if ( ! isset( $this->styles[ $selector ]['properties'][ $property ]['style'] ) ) {
					$this->styles[ $selector ]['properties'][ $property ]['style'] = $style;
				}
			}
		} elseif ( $selector === '.entry-content a' ) {
			if ( in_array( $property, array( 'color', 'font-weight', 'text-decoration' ), true ) ) {
				if ( ! isset( $this->styles[ $selector ]['properties'][ $property ]['style'] ) ) {
					$this->styles[ $selector ]['properties'][ $property ]['style'] = $style;
				}
			}
		}
	}

	/**
	 * Creates the actual style HTML as a string
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function generate_style_html() {
		if ( empty( $this->styles ) ) {
			return '';
		}

		$wrap_selector = self::WRAP_SELECTOR;
		$html = '<style id="sb-customizer-preview-styles">' . "\n";
		foreach ( $this->styles as $selector => $data ) {
			$selector = $selector === 'body' ? $wrap_selector : $wrap_selector . ' ' . $selector;
			$html .= $selector . '{' . "\n";
			foreach ( $data as $property_data => $values ) {
				foreach ( $values as $property_key => $property_values ) {
					$html .= '  ' . $property_key . ': ' . $property_values['style'] . ';' . "\n";
				}
			}
			$html .= '}' . "\n";
		}
		$html .= '</style>';

		return $html;
	}
}