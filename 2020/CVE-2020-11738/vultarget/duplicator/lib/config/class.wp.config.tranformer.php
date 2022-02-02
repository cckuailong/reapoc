<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!class_exists('WPConfigTransformer')):
/**
 * Transforms a wp-config.php file.
 */
class WPConfigTransformer {

    const REPLACE_TEMP_STIRNG = '_1_2_RePlAcE_3_4_TeMp_5_6_StRiNg_7_8_';

	/**
	 * Path to the wp-config.php file.
	 *
	 * @var string
	 */
	protected $wp_config_path;

	/**
	 * Original source of the wp-config.php file.
	 *
	 * @var string
	 */
	protected $wp_config_src;

	/**
	 * Array of parsed configs.
	 *
	 * @var array
	 */
	protected $wp_configs = array();

	/**
	 * Instantiates the class with a valid wp-config.php.
	 *
	 * @throws Exception If the wp-config.php file is missing.
	 * @throws Exception If the wp-config.php file is not writable.
	 *
	 * @param string $wp_config_path Path to a wp-config.php file.
	 */
	public function __construct( $wp_config_path ) {
		if ( ! file_exists( $wp_config_path ) ) {
			throw new Exception( 'wp-config.php file does not exist.' );
		}
		// Duplicator Extra
		/*
		if ( ! is_writable( $wp_config_path ) ) {
			throw new Exception( 'wp-config.php file is not writable.' );
		}
		*/

		$this->wp_config_path = $wp_config_path;
	}

	/**
	 * Checks if a config exists in the wp-config.php file.
	 *
	 * @throws Exception If the wp-config.php file is empty.
	 * @throws Exception If the requested config type is invalid.
	 *
	 * @param string $type Config type (constant or variable).
	 * @param string $name Config name.
	 *
	 * @return bool
	 */
	public function exists( $type, $name ) {
		$wp_config_src = file_get_contents( $this->wp_config_path );

		if ( ! trim( $wp_config_src ) ) {
			throw new Exception( 'wp-config.php file is empty.' );
		}

		// SnapCreek custom change
		// Normalize the newline to prevent an issue coming from OSX
		$wp_config_src = str_replace(array("\n\r", "\r"), array("\n", "\n"), $wp_config_src);

		$this->wp_config_src = $wp_config_src;
		$this->wp_configs    = $this->parse_wp_config( $this->wp_config_src );

		if ( ! isset( $this->wp_configs[ $type ] ) ) {
			throw new Exception( "Config type '{$type}' does not exist." );
		}

		return isset( $this->wp_configs[ $type ][ $name ] );
	}

	/**
	 * Get the value of a config in the wp-config.php file.
	 *
	 * @throws Exception If the wp-config.php file is empty.
	 * @throws Exception If the requested config type is invalid.
	 *
	 * @param string $type Config type (constant or variable).
	 * @param string $name Config name.
	 *
	 * @return array
	 */
	public function get_value( $type, $name, $get_real_value = true) {
		$wp_config_src = file_get_contents( $this->wp_config_path );
		if ( ! trim( $wp_config_src ) ) {
			throw new Exception( 'wp-config.php file is empty.' );
		}

		// SnapCreek custom change
		// Normalize the newline to prevent an issue coming from OSX
		$wp_config_src = str_replace(array("\n\r", "\r"), array("\n", "\n"), $wp_config_src);


		$this->wp_config_src = $wp_config_src;
		$this->wp_configs    = $this->parse_wp_config( $this->wp_config_src );

		if ( ! isset( $this->wp_configs[ $type ] ) ) {
			throw new Exception( "Config type '{$type}' does not exist." );
		}

		// Duplicator Extra
		$val = $this->wp_configs[ $type ][ $name ]['value'];
        if ($get_real_value) {
            return self::getRealValFromVal($val);
        } else {
            return $val;
        }

		return $val;
	}

    public static function getRealValFromVal($val)
    {
        if ($val[0] === '\'') {
            // string with '
            $result = substr($val, 1, strlen($val) - 2);
            return str_replace(array('\\\'', '\\\\'), array('\'', '\\'), $result);
        } else if ($val[0] === '"') {
            // string with "
            return json_decode(str_replace('\\$', '$', $val));
        } else if (strcasecmp($val, 'true') === 0) {
            return true;
        } else if (strcasecmp($val, 'false') === 0) {
            return false;
        } else if (strcasecmp($val, 'null') === 0) {
            return null;
        } else if (preg_match('/^[-+]?[0-9]+$/', $val)) {
            return (int) $val;
        } else if (preg_match('/^[-+]?[0-9]+\.[0-9]+$/', $val)) {
            return (float) $val;
        } else {
            return $val;
        }
    }

        /**
	 * Adds a config to the wp-config.php file.
	 *
	 * @throws Exception If the config value provided is not a string.
	 * @throws Exception If the config placement anchor could not be located.
	 *
	 * @param string $type    Config type (constant or variable).
	 * @param string $name    Config name.
	 * @param string $value   Config value.
	 * @param array  $options (optional) Array of special behavior options.
	 *
	 * @return bool
	 */
	public function add( $type, $name, $value, array $options = array() ) {
		if ( ! is_string( $value ) ) {
			throw new Exception( 'Config value must be a string.' );
		}

		if ( $this->exists( $type, $name ) ) {
			return false;
		}

		$defaults = array(
			'raw'       => false, // Display value in raw format without quotes.
			'anchor'    => "/* That's all, stop editing!", // Config placement anchor string.
			'separator' => PHP_EOL, // Separator between config definition and anchor string.
			'placement' => 'before', // Config placement direction (insert before or after).
		);

		list( $raw, $anchor, $separator, $placement ) = array_values( array_merge( $defaults, $options ) );

		$raw       = (bool) $raw;
		$anchor    = (string) $anchor;
		$separator = (string) $separator;
		$placement = (string) $placement;

		// Custom code by the SnapCreek Team
		if ( false === strpos( $this->wp_config_src, $anchor ) ) {
			$other_anchor_points = array(
				'/** Absolute path to the WordPress directory',
				// ABSPATH defined check with single quote
				"if ( !defined('ABSPATH') )",
				"if ( ! defined( 'ABSPATH' ) )",
				"if (!defined('ABSPATH') )",
				"if(!defined('ABSPATH') )",
				"if(!defined('ABSPATH'))",
				"if ( ! defined( 'ABSPATH' ))",
				"if ( ! defined( 'ABSPATH') )",
				"if ( ! defined('ABSPATH' ) )",
				"if (! defined( 'ABSPATH' ))",
				"if (! defined( 'ABSPATH') )",
				"if (! defined('ABSPATH' ) )",
				"if ( !defined( 'ABSPATH' ))",
				"if ( !defined( 'ABSPATH') )",
				"if ( !defined('ABSPATH' ) )",
				"if( !defined( 'ABSPATH' ))",
				"if( !defined( 'ABSPATH') )",
				"if( !defined('ABSPATH' ) )",
				// ABSPATH defined check with double quote
				'if ( !defined("ABSPATH") )',
				'if ( ! defined( "ABSPATH" ) )',
				'if (!defined("ABSPATH") )',
				'if(!defined("ABSPATH") )',
				'if(!defined("ABSPATH"))',
				'if ( ! defined( "ABSPATH" ))',
				'if ( ! defined( "ABSPATH") )',
				'if ( ! defined("ABSPATH" ) )',
				'if (! defined( "ABSPATH" ))',
				'if (! defined( "ABSPATH") )',
				'if (! defined("ABSPATH" ) )',
				'if ( !defined( "ABSPATH" ))',
				'if ( !defined( "ABSPATH") )',
				'if ( !defined("ABSPATH" ) )',
				'if( !defined( "ABSPATH" ))',
				'if( !defined( "ABSPATH") )',
				'if( !defined("ABSPATH" ) )',

				'/** Sets up WordPress vars and included files',
				'require_once(ABSPATH',
				'require_once ABSPATH',
				'require_once( ABSPATH',
				'require_once',
				"define( 'DB_NAME'",
				'define( "DB_NAME"',
				"define('DB_NAME'",
				'define("DB_NAME"',
				'require',
				'include_once',
			);
			foreach ($other_anchor_points as $anchor_point) {
				$anchor_point    = (string) $anchor_point;
				if ( false !== strpos( $this->wp_config_src, $anchor_point ) ) {
					$anchor = $anchor_point;
					break;
				}
			}
		}

		if ( false === strpos( $this->wp_config_src, $anchor ) ) {
			throw new Exception( 'Unable to locate placement anchor.' );
		}

		$new_src  = $this->normalize( $type, $name, $this->format_value( $value, $raw ) );
		$new_src  = ( 'after' === $placement ) ? $anchor . $separator . $new_src : $new_src . $separator . $anchor;
		$contents = str_replace( $anchor, $new_src, $this->wp_config_src );

		return $this->save( $contents );
	}

	/**
	 * Updates an existing config in the wp-config.php file.
	 *
	 * @throws Exception If the config value provided is not a string.
	 *
	 * @param string $type    Config type (constant or variable).
	 * @param string $name    Config name.
	 * @param string $value   Config value.
	 * @param array  $options (optional) Array of special behavior options.
	 *
	 * @return bool
	 */
	public function update( $type, $name, $value, array $options = array() ) {
		if ( ! is_string( $value ) ) {
			throw new Exception( 'Config value must be a string.' );
		}

		$defaults = array(
			'add'       => true, // Add the config if missing.
			'raw'       => false, // Display value in raw format without quotes.
			'normalize' => false, // Normalize config output using WP Coding Standards.
		);

		list( $add, $raw, $normalize ) = array_values( array_merge( $defaults, $options ) );

		$add       = (bool) $add;
		$raw       = (bool) $raw;
		$normalize = (bool) $normalize;

		if ( ! $this->exists( $type, $name ) ) {
			return ( $add ) ? $this->add( $type, $name, $value, $options ) : false;
		}

		$old_src   = $this->wp_configs[ $type ][ $name ]['src'];
		$old_value = $this->wp_configs[ $type ][ $name ]['value'];
		$new_value = $this->format_value( $value, $raw );

		if ( $normalize ) {
			$new_src = $this->normalize( $type, $name, $new_value );
		} else {
			$new_parts    = $this->wp_configs[ $type ][ $name ]['parts'];
			$new_parts[1] = str_replace( $old_value, $new_value, $new_parts[1] ); // Only edit the value part.
			$new_src      = implode( '', $new_parts );
		}

        $contents = preg_replace(
			sprintf( '/(?<=^|;|<\?php\s|<\?\s)(\s*?)%s/m', preg_quote( trim( $old_src ), '/' ) ),
			'$1' . self::REPLACE_TEMP_STIRNG ,
			$this->wp_config_src
		);
        $contents = str_replace(self::REPLACE_TEMP_STIRNG, trim($new_src), $contents);
		return $this->save( $contents );
	}

	/**
	 * Removes a config from the wp-config.php file.
	 *
	 * @param string $type Config type (constant or variable).
	 * @param string $name Config name.
	 *
	 * @return bool
	 */
	public function remove( $type, $name ) {
		if ( ! $this->exists( $type, $name ) ) {
			return false;
		}

		$pattern  = sprintf( '/(?<=^|;|<\?php\s|<\?\s)%s\s*(\S|$)/m', preg_quote( $this->wp_configs[ $type ][ $name ]['src'], '/' ) );
		$contents = preg_replace( $pattern, '$1', $this->wp_config_src );

		return $this->save( $contents );
	}

	/**
	 * Applies formatting to a config value.
	 *
	 * @throws Exception When a raw value is requested for an empty string.
	 *
	 * @param string $value Config value.
	 * @param bool   $raw   Display value in raw format without quotes.
	 *
	 * @return mixed
	 */
	protected function format_value( $value, $raw ) {
		if ( $raw && '' === trim( $value ) ) {
			throw new Exception( 'Raw value for empty string not supported.' );
		}

		return ( $raw ) ? $value : var_export( $value, true );
	}

	/**
	 * Normalizes the source output for a name/value pair.
	 *
	 * @throws Exception If the requested config type does not support normalization.
	 *
	 * @param string $type  Config type (constant or variable).
	 * @param string $name  Config name.
	 * @param mixed  $value Config value.
	 *
	 * @return string
	 */
	protected function normalize( $type, $name, $value ) {
		if ( 'constant' === $type ) {
			$placeholder = "define( '%s', %s );";
		} elseif ( 'variable' === $type ) {
			$placeholder = '$%s = %s;';
		} else {
			throw new Exception( "Unable to normalize config type '{$type}'." );
		}

		return sprintf( $placeholder, $name, $value );
	}

	/**
	 * Parses the source of a wp-config.php file.
	 *
	 * @param string $src Config file source.
	 *
	 * @return array
	 */
	protected function parse_wp_config( $src ) {
		$configs             = array();
		$configs['constant'] = array();
		$configs['variable'] = array();

		// Strip comments.
		foreach ( token_get_all( $src ) as $token ) {
			if ( in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) ) {
				$src = str_replace( $token[1], '', $src );
			}
		}

		preg_match_all( '/(?<=^|;|<\?php\s|<\?\s)(\h*define\s*\(\s*[\'"](\w*?)[\'"]\s*)(,\s*(\'\'|""|\'.*?[^\\\\]\'|".*?[^\\\\]"|.*?)\s*)((?:,\s*(?:true|false)\s*)?\)\s*;)/ims', $src, $constants );
        preg_match_all( '/(?<=^|;|<\?php\s|<\?\s)(\h*\$(\w+)\s*=)(\s*(\'\'|""|\'.*?[^\\\\]\'|".*?[^\\\\]"|.*?)\s*;)/ims', $src, $variables );


		if ( ! empty( $constants[0] ) && ! empty( $constants[1] ) && ! empty( $constants[2] ) && ! empty( $constants[3] ) && ! empty( $constants[4] ) && ! empty( $constants[5] ) ) {
			foreach ( $constants[2] as $index => $name ) {
				$configs['constant'][ $name ] = array(
					'src'   => $constants[0][ $index ],
					'value' => $constants[4][ $index ],
					'parts' => array(
						$constants[1][ $index ],
						$constants[3][ $index ],
						$constants[5][ $index ],
					),
				);
			}
		}

		if ( ! empty( $variables[0] ) && ! empty( $variables[1] ) && ! empty( $variables[2] ) && ! empty( $variables[3] ) && ! empty( $variables[4] ) ) {
			// Remove duplicate(s), last definition wins.
			$variables[2] = array_reverse( array_unique( array_reverse( $variables[2], true ) ), true );
			foreach ( $variables[2] as $index => $name ) {
				$configs['variable'][ $name ] = array(
					'src'   => $variables[0][ $index ],
					'value' => $variables[4][ $index ],
					'parts' => array(
						$variables[1][ $index ],
						$variables[3][ $index ],
					),
				);
			}
		}

		return $configs;
	}

	/**
	 * Saves new contents to the wp-config.php file.
	 *
	 * @throws Exception If the config file content provided is empty.
	 * @throws Exception If there is a failure when saving the wp-config.php file.
	 *
	 * @param string $contents New config contents.
	 *
	 * @return bool
	 */
	protected function save( $contents ) {
		if ( ! trim( $contents ) ) {
			throw new Exception( 'Cannot save the wp-config.php file with empty contents.' );
		}

		if ( $contents === $this->wp_config_src ) {
			return false;
		}

		$result = file_put_contents( $this->wp_config_path, $contents, LOCK_EX );

		if ( false === $result ) {
			throw new Exception( 'Failed to update the wp-config.php file.' );
		}

		return true;
	}

}

endif;
