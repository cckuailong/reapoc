<?php

class WPCF7_FormTag implements ArrayAccess {

	public $type;
	public $basetype;
	public $name = '';
	public $options = array();
	public $raw_values = array();
	public $values = array();
	public $pipes;
	public $labels = array();
	public $attr = '';
	public $content = '';

	public function __construct( $tag = array() ) {
		if ( is_array( $tag )
		or $tag instanceof self ) {
			foreach ( $tag as $key => $value ) {
				if ( property_exists( __CLASS__, $key ) ) {
					$this->{$key} = $value;
				}
			}
		}
	}

	public function is_required() {
		return ( '*' == substr( $this->type, -1 ) );
	}

	public function has_option( $opt ) {
		$pattern = sprintf( '/^%s(:.+)?$/i', preg_quote( $opt, '/' ) );
		return (bool) preg_grep( $pattern, $this->options );
	}

	public function get_option( $opt, $pattern = '', $single = false ) {
		$preset_patterns = array(
			'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'int' => '[0-9]+',
			'signed_int' => '-?[0-9]+',
			'class' => '[-0-9a-zA-Z_]+',
			'id' => '[-0-9a-zA-Z_]+',
		);

		if ( isset( $preset_patterns[$pattern] ) ) {
			$pattern = $preset_patterns[$pattern];
		}

		if ( '' == $pattern ) {
			$pattern = '.+';
		}

		$pattern = sprintf( '/^%s:%s$/i', preg_quote( $opt, '/' ), $pattern );

		if ( $single ) {
			$matches = $this->get_first_match_option( $pattern );

			if ( ! $matches ) {
				return false;
			}

			return substr( $matches[0], strlen( $opt ) + 1 );
		} else {
			$matches_a = $this->get_all_match_options( $pattern );

			if ( ! $matches_a ) {
				return false;
			}

			$results = array();

			foreach ( $matches_a as $matches ) {
				$results[] = substr( $matches[0], strlen( $opt ) + 1 );
			}

			return $results;
		}
	}

	public function get_id_option() {
		return $this->get_option( 'id', 'id', true );
	}

	public function get_class_option( $default = '' ) {
		if ( is_string( $default ) ) {
			$default = explode( ' ', $default );
		}

		$options = array_merge(
			(array) $default,
			(array) $this->get_option( 'class', 'class' ) );

		$options = array_filter( array_unique( $options ) );

		return implode( ' ', $options );
	}

	public function get_size_option( $default = '' ) {
		$option = $this->get_option( 'size', 'int', true );

		if ( $option ) {
			return $option;
		}

		$matches_a = $this->get_all_match_options( '%^([0-9]*)/[0-9]*$%' );

		foreach ( (array) $matches_a as $matches ) {
			if ( isset( $matches[1] )
			and '' !== $matches[1] ) {
				return $matches[1];
			}
		}

		return $default;
	}

	public function get_maxlength_option( $default = '' ) {
		$option = $this->get_option( 'maxlength', 'int', true );

		if ( $option ) {
			return $option;
		}

		$matches_a = $this->get_all_match_options(
			'%^(?:[0-9]*x?[0-9]*)?/([0-9]+)$%' );

		foreach ( (array) $matches_a as $matches ) {
			if ( isset( $matches[1] ) && '' !== $matches[1] ) {
				return $matches[1];
			}
		}

		return $default;
	}

	public function get_minlength_option( $default = '' ) {
		$option = $this->get_option( 'minlength', 'int', true );

		if ( $option ) {
			return $option;
		} else {
			return $default;
		}
	}

	public function get_cols_option( $default = '' ) {
		$option = $this->get_option( 'cols', 'int', true );

		if ( $option ) {
			return $option;
		}

		$matches_a = $this->get_all_match_options(
			'%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%' );

		foreach ( (array) $matches_a as $matches ) {
			if ( isset( $matches[1] ) && '' !== $matches[1] ) {
				return $matches[1];
			}
		}

		return $default;
	}

	public function get_rows_option( $default = '' ) {
		$option = $this->get_option( 'rows', 'int', true );

		if ( $option ) {
			return $option;
		}

		$matches_a = $this->get_all_match_options(
			'%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%' );

		foreach ( (array) $matches_a as $matches ) {
			if ( isset( $matches[2] )
			and '' !== $matches[2] ) {
				return $matches[2];
			}
		}

		return $default;
	}

	public function get_date_option( $opt ) {
		$option_value = $this->get_option( $opt, '', true );

		if ( empty( $option_value ) ) {
			return false;
		}

		$date = apply_filters( 'wpcf7_form_tag_date_option',
			null,
			array( $opt => $option_value )
		);

		if ( $date ) {
			$date_pattern = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/';

			if ( preg_match( $date_pattern, $date, $matches )
			and checkdate( $matches[2], $matches[3], $matches[1] ) ) {
				return $date;
			}
		} else {
			$datetime_obj = date_create_immutable(
				preg_replace( '/[_]+/', ' ', $option_value ),
				wp_timezone()
			);

			if ( $datetime_obj ) {
				return $datetime_obj->format( 'Y-m-d' );
			}
		}

		return false;
	}

	public function get_default_option( $default = '', $args = '' ) {
		$args = wp_parse_args( $args, array(
			'multiple' => false,
			'shifted' => false,
		) );

		$options = (array) $this->get_option( 'default' );
		$values = array();

		if ( empty( $options ) ) {
			return $args['multiple'] ? $values : $default;
		}

		foreach ( $options as $opt ) {
			$opt = sanitize_key( $opt );

			if ( 'user_' == substr( $opt, 0, 5 )
			and is_user_logged_in() ) {
				$primary_props = array( 'user_login', 'user_email', 'user_url' );
				$opt = in_array( $opt, $primary_props ) ? $opt : substr( $opt, 5 );

				$user = wp_get_current_user();
				$user_prop = $user->get( $opt );

				if ( ! empty( $user_prop ) ) {
					if ( $args['multiple'] ) {
						$values[] = $user_prop;
					} else {
						return $user_prop;
					}
				}

			} elseif ( 'post_meta' == $opt and in_the_loop() ) {
				if ( $args['multiple'] ) {
					$values = array_merge( $values,
						get_post_meta( get_the_ID(), $this->name ) );
				} else {
					$val = (string) get_post_meta( get_the_ID(), $this->name, true );

					if ( strlen( $val ) ) {
						return $val;
					}
				}

			} elseif ( 'get' == $opt and isset( $_GET[$this->name] ) ) {
				$vals = (array) $_GET[$this->name];
				$vals = array_map( 'wpcf7_sanitize_query_var', $vals );

				if ( $args['multiple'] ) {
					$values = array_merge( $values, $vals );
				} else {
					$val = isset( $vals[0] ) ? (string) $vals[0] : '';

					if ( strlen( $val ) ) {
						return $val;
					}
				}

			} elseif ( 'post' == $opt and isset( $_POST[$this->name] ) ) {
				$vals = (array) $_POST[$this->name];
				$vals = array_map( 'wpcf7_sanitize_query_var', $vals );

				if ( $args['multiple'] ) {
					$values = array_merge( $values, $vals );
				} else {
					$val = isset( $vals[0] ) ? (string) $vals[0] : '';

					if ( strlen( $val ) ) {
						return $val;
					}
				}

			} elseif ( 'shortcode_attr' == $opt ) {
				if ( $contact_form = WPCF7_ContactForm::get_current() ) {
					$val = $contact_form->shortcode_attr( $this->name );

					if ( strlen( $val ) ) {
						if ( $args['multiple'] ) {
							$values[] = $val;
						} else {
							return $val;
						}
					}
				}

			} elseif ( preg_match( '/^[0-9_]+$/', $opt ) ) {
				$nums = explode( '_', $opt );

				foreach ( $nums as $num ) {
					$num = absint( $num );
					$num = $args['shifted'] ? $num : $num - 1;

					if ( isset( $this->values[$num] ) ) {
						if ( $args['multiple'] ) {
							$values[] = $this->values[$num];
						} else {
							return $this->values[$num];
						}
					}
				}
			}
		}

		if ( $args['multiple'] ) {
			$values = array_unique( $values );
			return $values;
		} else {
			return $default;
		}
	}

	public function get_data_option( $args = '' ) {
		$options = (array) $this->get_option( 'data' );

		return apply_filters( 'wpcf7_form_tag_data_option', null, $options, $args );
	}

	public function get_limit_option( $default = MB_IN_BYTES ) {
		$pattern = '/^limit:([1-9][0-9]*)([kKmM]?[bB])?$/';

		$matches = $this->get_first_match_option( $pattern );

		if ( $matches ) {
			$size = (int) $matches[1];

			if ( ! empty( $matches[2] ) ) {
				$kbmb = strtolower( $matches[2] );

				if ( 'kb' == $kbmb ) {
					$size *= KB_IN_BYTES;
				} elseif ( 'mb' == $kbmb ) {
					$size *= MB_IN_BYTES;
				}
			}

			return $size;
		}

		return (int) $default;
	}

	public function get_first_match_option( $pattern ) {
		foreach( (array) $this->options as $option ) {
			if ( preg_match( $pattern, $option, $matches ) ) {
				return $matches;
			}
		}

		return false;
	}

	public function get_all_match_options( $pattern ) {
		$result = array();

		foreach( (array) $this->options as $option ) {
			if ( preg_match( $pattern, $option, $matches ) ) {
				$result[] = $matches;
			}
		}

		return $result;
	}

	public function offsetSet( $offset, $value ) {
		if ( property_exists( __CLASS__, $offset ) ) {
			$this->{$offset} = $value;
		}
	}

	public function offsetGet( $offset ) {
		if ( property_exists( __CLASS__, $offset ) ) {
			return $this->{$offset};
		}

		return null;
	}

	public function offsetExists( $offset ) {
		return property_exists( __CLASS__, $offset );
	}

	public function offsetUnset( $offset ) {
	}
}
