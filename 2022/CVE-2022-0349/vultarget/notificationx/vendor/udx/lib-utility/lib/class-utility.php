<?php
/**
 * Utility Classs
 *
 * @copyright Copyright (c) 2010 - 2013, Usability Dynamics, Inc.
 *
 * @author team@UD
 * @namespace UsabilityDynamics
 * @module Utility
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\Utility' ) ) {

    /**
     * Utility Library.
     *
     * @submodule Utility
     * @version 0.2.2
     * @class Utility
     */
    class Utility {

      /**
       * Class version.
       *
       * @static
       * @property $version
       * @type string
       */
      public static $version = '0.4.0';

      /**
       * Textdomain String
       *
       * @public
       * @property text_domain
       * @var string
       */
      public static $text_domain = 'lib-utility';

      /**
       * Constructor for initializing class, in static mode as well as dynamic.
       *
       * @todo Should make the transdomain configuraiton.
       *
       * @since 0.1.1
       * @author potanin@UD
       */
      public function __construct() {}

      /**
       *
       * call_user_func( 'UsabilityDynamics\Utility::optimizeDatabase', 'flush-duplicate-meta' );
       *
       * UsabilityDynamics\Utility::optimizeDatabase( 'flush-duplicate-meta' );
       *
       * @param $action
       */
      static public function optimizeDatabase( $action ) {
        global $wpdb;
        //die('optimizeDatabase');

        switch( $action ) {

          case 'flush-duplicate-meta':

            $allposts = get_posts(array(
              "numberposts" => 10,
              "offset" => 10,
              "post_type" => 'any',
              "post_status" => 'any'
            ));

            foreach ( $allposts as $_post ) {

              $postmeta = get_post_meta($postinfo->ID, $key);

              die( '<pre>' . print_r( $postmeta, true ) . '</pre>');

            }

            $keys = array('address', 'address2', 'city', 'state', 'zip'); //Add post meta keys here

            foreach ( $keys as $key ) {

              foreach( $allposts as $postinfo) {

                // Fetch array of custom field values


                //print_r($postinfo);

                if (!empty($postmeta) ) {

                  // Delete the custom field for this post (all occurrences)
                  delete_post_meta($postinfo->ID, $key);

                  // Insert one and only one custom field
                  update_post_meta($postinfo->ID, $key, $postmeta[0]);

                }
              }

            }


            break;

          default:

          break;

        }

        //$wpdb->query( "DELETE a,b,c FROM {$wpdb->posts} a LEFT JOIN wp_term_relationships b ON (a.ID = b.object_id) LEFT JOIN {$wpdb->postmeta} c ON (a.ID = c.post_id) WHERE a.post_type = 'revision'" );
        //$wpdb->query( "DELETE pm FROM wp_postmeta pm LEFT JOIN wp_posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL;" );
        //$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE ('_site_transient_%');" );
        //$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE ('_transient_%');" );


      }

      /**
       * Looks for a json or a php file in specified directory, if the file is not found traverse up and look for it again until its found or until a document root is reached
       *
       *
       * @todo Honor the "nocase" option setting.
       * @todo Perhaps wrap simplexml_load_file into  - potanin@UD
       *
       * @since 0.3.2
       * @method findUp
       * @param string $name      - Name of file to find.
       * @param string $cwd       - Directory to start seeking from. Defaults to __DIR__
       * @param string $required  - If the file is required - if not found, will trigger an error.
       * @author tosheen@UD
       * @return array|bool|mixed|\SimpleXMLElement
       */
      static public function findUp( $name = false, $cwd = false, $required = false ) {

        // No name provided, bail.
        if( !$name ) {
          return false;
        }

        // If first argument appears to be an object/array, treat it as a configuration object.
        if( is_array( $name ) || is_object( $name ) ) {

          // Apply default settings to passed argument.
          $_settings = self::defaults( $name, array(
            "cwd" => $cwd ? $cwd : null,
            "required" => $required ? $required : false,
            "nocase" => true
          ));

          $name     = $_settings->name;
          $cwd      = $_settings->cwd;
          $required = $_settings->required;
          $nocase   = $_settings->nocase;

        }

        // No CWD set, backtrace to determine caller.
        $cwd = $cwd ? $cwd : dirname( self::backtrace_caller()->file );

        // Determine if file is JSON
        $fileData      = explode( '.', $name );
        $fileExtension = $fileData[ count( $fileData ) - 1 ];

        // Determine traverse path
        $_path = ( !empty( $cwd ) ? $cwd : $_SERVER[ 'DOCUMENT_ROOT' ] );

        $file = $_path . DIRECTORY_SEPARATOR . $name;

        // Trigger Error and bail on failures.
        if( !is_dir( $_path ) ) {

          // Trigger error if required, otherwise fail silently.
          if( $required ) {
            trigger_error( __( 'Required file not found.', self::$text_domain ), E_USER_ERROR );
          }

          return false;

        }

        if( file_exists( $file ) ) {

          // Fetch and parse JSON.
          if( $fileExtension === 'json' ) {
            return json_decode( file_get_contents( $file ) );
          }

          // Fetch and parse XML.
          if( $fileExtension === 'xml' ) {
            return simplexml_load_file( $file );
          }

          // Include all others.
          return include_once( $file );

        }

        if( $_path != $_SERVER[ 'DOCUMENT_ROOT' ] ) {
          $lastDirSeparator   = strrpos( $_path, DIRECTORY_SEPARATOR, -1 );
          $_path              = substr( $_path, 0, $lastDirSeparator );
          return self::findUp( $name, $_path );
        }

        // Trigger error if file was required.
        if( $required ) {
          trigger_error( __( 'Required file not found.', self::$text_domain ), E_USER_ERROR );
        }

        // Couldn't find anything
        return false;

      }

      /**
       * Get Caller Object
       *
       * @method backtrace_caller
       * @since 0.3.2
       * @author potanin@UD
       * @param int $depth
       * @return object
       */
      static public function backtrace_caller( $depth = 1 ) {

        // Always add one level to backtrace.
        $_backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, ( $depth ? $depth : 1 ) + 1 );

        // Return first hit as object, or a scaffolded object.
        return $_backtrace[1] ? (object) $_backtrace[1] : (object) array( 'file' => null );

      }

      /**
       * Set Dot-Notated Array Value
       *
       * @param array $arr
       * @param       $path
       * @param       $val
       *
       * @return mixed
       */
      static public function _set_val( array &$arr, $path, $val ) {
        $loc = & $arr;

        foreach( explode( '.', $path ) as $step ) {
          $loc = & $loc[ $step ];
        }

        return $loc = $val;

      }

      /**
       * Convert dot-notated array key->value items to nested object
       *
       * @param $items
       * @return array
       */
      static public function unwrap( $items ) {

        $_result = array();

        foreach( (array) $items as $key => $value ) {
          self::_set_val( $_result, $key, $value );
        }

        return (array) $_result;

      }

      /**
       * Wrapper for wp_parse_args.
       *
       * @author potanin@UD
       * @since 0.3.0
       * @param $args
       * @param $defaults
       *
       * @return object
       */
      static public function parse_args( $args, $defaults ) {

        return (object) wp_parse_args( $args, $defaults );

      }

      /**
       * Parses Query.
       * HACK. The current logic solves the issue of max_input_vars in the case if query is huge.
       *
       * @see parse_str() Default PHP function
       * @version 1.1
       * @author peshkov@UD
       * @param $request
       * @return array|mixed
       */
      static public function parse_str( $request ) {
        $data = array();
        $tokens = explode( "&", $request );
        foreach ( $tokens as $token ) {
          $token = str_replace( '%2B', md5( '%2B' ), $token );
          $arr = array();
          parse_str( $token, $arr );
          array_walk_recursive( $arr, function( &$value,$key ) {
            $value = str_replace( md5( "%2B" ), "+", $value );
          });
          $data = self::extend( $data, $arr );
        }
        return $data;
      }

      /**
       * Deep Conversion
       *
       * @updated 2.0
       * @since 0.1
       */
      static public function array_to_object( $array = array() ) {
        return json_decode( json_encode( $array ) );
      }

      /**
       * Recursively converts object to array
       *
       * @since 2.0
       * @author peshkov@UD
       */
      static public function object_to_array( $data ) {

        if ( is_object( $data ) ) {
          $data = get_object_vars( $data );
        }

        if ( is_array( $data ) ) {
          foreach ( $data as $k => $v ) {
            if ( is_object( $v ) ) {
              $data[ $k ] = self::object_to_array( $v );
            }
          }
        }

        return $data;

      }

      /**
       * Parse standard WordPress readme file
       *
       * @source Readme Parser ( http://www.tomsdimension.de/wp-plugins/readme-parser )
       * @author potanin@UD
       */
      static public function parse_readme( $readme_file = false ) {

        if( !$readme_file ) {
          $readme_file = untrailingslashit( TEMPLATEPATH ) . '/readme.txt';
        }

        $file = @file_get_contents( $readme_file );

        if( !$file ) {
          return false;
        }

        $file = preg_replace( "/(\n\r|\r\n|\r|\n)/", "\n", $file );

        // headlines
        $s = array( '===', '==', '=' );
        $r = array( 'h2', 'h3', 'h4' );
        for( $x = 0; $x < sizeof( $s ); $x++ ) {
          $file = preg_replace( '/(.*?)' . $s[ $x ] . '(?!\")(.*?)' . $s[ $x ] . '(.*?)/', '$1<' . $r[ $x ] . '>$2</' . $r[ $x ] . '>$3', $file );
        }

        // inline
        $s = array( '\*\*', '\'' );
        $r = array( 'b', 'code' );
        for( $x = 0; $x < sizeof( $s ); $x++ ) {
          $file = preg_replace( '/(.*?)' . $s[ $x ] . '(?!\s)(.*?)(?!\s )' . $s[ $x ] . '(.*?)/', '$1<' . $r[ $x ] . '>$2</' . $r[ $x ] . '>$3', $file );
        }

        // ' _italic_ '
        $file = preg_replace( '/(\s)_(\S.*?\S)_(\s|$)/', '<em>$2</em> ', $file );

        // ul lists
        $s = array( '\*', '\+', '\-' );
        for( $x = 0; $x < sizeof( $s ); $x++ ) {
          $file = preg_replace( '/^[ ' . $s[ $x ] . ' ](\s)(.*?)(\n|$)/m', '<li>$2</li>', $file );
        }

        $file = preg_replace( '/\n<li>(.*?)/', '<ul><li>$1', $file );
        $file = preg_replace( '/(<\/li>)(?!<li>)/', '$1</ul>', $file );

        // ol lists
        $file = preg_replace( '/(\d{1,2}\. )\s(.*?)(\n|$)/', '<li>$2</li>', $file );
        $file = preg_replace( '/\n<li>(.*?)/', '<ol><li>$1', $file );
        $file = preg_replace( '/(<\/li>)(?!(\<li\>|\<\/ul\> ))/', '$1</ol>', $file );

        // ol screenshots style
        $file = preg_replace( '/(?=Screenshots)(.*?)<ol>/', '$1<ol class="readme-parser-screenshots">', $file );

        // line breaks
        $file = preg_replace( '/(.*?)(\n)/', "$1<br/>\n", $file );
        $file = preg_replace( '/(1|2|3|4)(><br\/>)/', '$1>', $file );
        $file = str_replace( '</ul><br/>', '</ul>', $file );
        $file = str_replace( '<br/><br/>', '<br/>', $file );

        // urls
        $file = str_replace( 'http://www.', 'www.', $file );
        $file = str_replace( 'www.', 'http://www.', $file );
        $file = preg_replace( '#(^|[^\"=]{1})(http://|ftp://|mailto:|https://)([^\s<>]+)([\s\n<>]|$)#', '$1<a href="$2$3">$3</a>$4', $file );

        // divs
        $file = preg_replace( '/(<h3> Description <\/h3>)/', "$1\n<div class=\"readme-description readme-div\">\n", $file );
        $file = preg_replace( '/(<h3> Installation <\/h3>)/', "</div>\n$1\n<div id=\"readme-installation\" class=\"readme-div\">\n", $file );
        $file = preg_replace( '/(<h3> Frequently Asked Questions <\/h3>)/', "</div>\n$1\n<div id=\"readme-faq\" class=\"readme-div\">\n", $file );
        $file = preg_replace( '/(<h3> Screenshots <\/h3>)/', "</div>\n$1\n<div id=\"readme-screenshots\" class=\"readme-div\">\n", $file );
        $file = preg_replace( '/(<h3> Arbitrary section <\/h3>)/', "</div>\n$1\n<div id=\"readme-arbitrary\" class=\"readme-div\">\n", $file );
        $file = preg_replace( '/(<h3> Changelog <\/h3>)/', "</div>\n$1\n<div id=\"readme-changelog\" class=\"readme-changelog readme-div\">\n", $file );
        $file = $file . '</div>';

        return $file;

      }

      /**
       * Detects Variable Type.
       *
       * Distinguishes between object and array based on associative status.
       *
       * @source http://php.net/manual/en/function.gettype.php
       * @since 1.0.4
       */
      static public function get_type( $var ) {

        if( is_object( $var ) ) return get_class( $var );
        if( is_null( $var ) ) return 'null';
        if( is_string( $var ) ) return 'string';

        if( is_array( $var ) ) {

          if( self::is_associative( $var ) ) {
            return 'object';
          }

          return 'array';

        }

        if( is_int( $var ) ) return 'integer';
        if( is_bool( $var ) ) return 'boolean';
        if( is_float( $var ) ) return 'float';
        if( is_resource( $var ) ) return 'resource';

      }

      /**
       * Test if Array is Associative
       *
       * @param $arr
       * @return bool
       */
      static public function is_associative( $arr ) {

        if( !$arr ) {
          return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1) ? true : false;

      }

      /**
       * Port of Lo-dash defaults function.
       *
       * Basically switches the order of arguments used by Utility::extend();
       *
       * @method defaults
       *
       * @param array $data Data to be applied against defaults. $data Data to be applied against defaults.
       * @param array $defaults Default array|object. $defaults Default array|object.
       *
       * @return object Extended data wtih defaults.@since 0.2.5
       */
      static public function defaults( $data = array(), $defaults = array() ) {

        // Extend and return data object/arary with defaults.
        return (object) self::extend( (array) $defaults, (array) $data );

      }

      /**
       * Fix Serialized (Broken) Array Strings
       *
       * @example
       *
       *    foreach( $wpdb->get_results( "SELECT meta_id, meta_value from {$wpdb->postmeta} WHERE meta_key = '_cfct_build_data'" ) as $row ) {
       *        $fixed = \UsabilityDynamics\Utility::repair_serialized_object( $row->meta_value );
       *        $wpdb->query("UPDATE {$wpdb->postmeta} SET meta_value = {$fixed} WHERE meta_key = {$row->meta_key}");
       *    }
       *
       * @param $input
       *
       * @return mixed
       */
      static public function repair_serialized_object( $input ) {
        return preg_replace( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $input );
      }

      /**
       * Rename uploaded files as the hash of their original.
       *
       * @public
       * @since 0.2.4
       * @method hashify_file_name
       * @for UsabilityDynamics\Utility
       *
       * @param string       $filename Original filename to hashify..
       * @param array|object $args Configuration arguments.
       *
       * @return string
       * @author sopp@ID
       */
      public static function hashify_file_name( $filename = null, $args = array() ) {

        if( !$filename ) {
          return '';
        }

        $args = wp_parse_args( $args, array(
          'limit' => 99
        ) );

        $info = pathinfo( $filename );
        $ext  = empty( $info[ 'extension' ] ) ? '' : '.' . $info[ 'extension' ];

        return md5( basename( $filename, $ext ) ) . rand( 0, $args[ 'limit' ] ) . $ext;
      }

      /**
       * Tests if remote image can be loaded.
       *
       * Returns URL to image if valid.
       * Return false if image is invalid or could not be reached.
       *
       * @example
       *
       *      // Try to load image.
       *      if( $url = Utility::can_get_image( $theme_settings->logo_url ) ) {
       *        echo "Image Found: $url.";
       *      }
       *
       *
       * @method can_get_image
       * @for Utility
       *
       * @param bool $url Valid URL to an image.
       *
       * @since 0.2.2
       * @return bool|int|string
       */
      static public function can_get_image( $url = false ) {

        if( !is_string( $url ) ) {
          return false;
        }

        if( empty( $url ) ) {
          return false;
        }

        //** Test if post_id */
        if( is_numeric( $url ) && $image_attributes = wp_get_attachment_image_src( $url, 'full' ) ) {
          $url = $image_attributes[ 0 ];
        }

        $result = wp_remote_get( $url, array( 'timeout' => 10 ) );

        if( is_wp_error( $result ) ) {
          return false;
        }

        //** Image content types should always begin with 'image' ( I hope ) */
        if( strpos( $result[ 'headers' ][ 'content-type' ], 'image' ) !== 0 ) {
          return false;
        }

        return $url;

      }

      /**
       * Return array of active plugins for current instance
       *
       * Improvement over wp_get_active_and_valid_plugins() which doesn't return any plugins when in MS
       *
       * @method get_active_plugins
       * @for Utility
       *
       * @since 0.2.0
       */
      static public function get_active_plugins() {
        $mu_plugins      = (array) wp_get_mu_plugins();
        $regular_plugins = (array) wp_get_active_and_valid_plugins();

        if( is_multisite() ) {
          $network_plugins = (array) wp_get_active_network_plugins();
        } else {
          $network_plugins = array();
        }

        return array_merge( $regular_plugins, $mu_plugins, $network_plugins );

      }

      /**
       * Validate URL
       *
       * @for Utility
       * @since 0.1.1
       *
       * @param string $url
       *
       * @param string $url
       *
       * @return bool
       */
      static public function is_url( $url = '' ) {
        return esc_url( $url );
      }

      /**
       * Strip out protected keys from an associative array.
       *
       * Example below will remove all keys from array that being with $$:
       *
       * <code>
       * strip_protected_keys( $my_array, array( 'prefix' => '$$' ) );
       * </code>
       *
       * @since 2.0
       * @author potanin@UD
       */
      static public function strip_protected_keys( $array, $args = '' ) {

        $args = wp_parse_args( $args, array( 'prefix' => '_' ) );

        foreach( (array) $array as $key => $value ) {

          if( strpos( $key, $args[ 'prefix' ] ) === 0 ) {
            unset( $array[ $key ] );
            continue;
          }

          if( is_array( $value ) ) {
            $array[ $key ] = self::strip_protected_keys( $value, $args );
          }

        }

        $array = array_filter( $array );

        return $array;

      }

      /**
       * Recursively remove empty values from array.
       *
       * @method array_filter_deep
       * @for Utility
       *
       * @version 1.0.1
       * @since 1.0.3
       * @author potanin@UD
       */
      static public function array_filter_deep( $haystack = array() ) {

        foreach( (array) $haystack as $key => $value ) {

          if( is_object( $value ) || is_array( $value ) ) {

            if( is_object( $haystack ) ) {
              $haystack->{$key} = self::array_filter_deep( (array) $value );
            } else if( is_array( $haystack ) ) {
              $haystack[ $key ] = self::array_filter_deep( (array) $value );
            }

          }

        }

        return array_filter( (array) $haystack );

      }

      /**
       * Determines if a passed timestamp is newer than a requirement.
       *
       * Usage: UD_API::is_fresher_than( $timestamp, '5 minutes' );
       *
       * @since 1.0.3
       */
      static public function fresher_than( $time, $ago = '1 week' ) {
        return ( strtotime( "-" . $ago ) < $time ) ? true : false;
      }

      /**
       * Starts a timer for the passed string.
       *
       * @since 1.0.0.2
       * @author potanin@UD
       */
      static public function timer_start( $function = 'global' ) {
        global $ud_api;

        return $ud_api[ 'timers' ][ $function ][ 'start' ] = microtime( true );
      }

      /**
       * Stop a timer.
       *
       * @since 1.0.0.2
       * @author potanin@UD
       */
      static public function timer_stop( $function = 'global', $precision = 2 ) {
        global $ud_api;

        return $ud_api[ 'timers' ][ $function ][ 'start' ] ? round( microtime( true ) - $ud_api[ 'timers' ][ $function ][ 'start' ], $precision ) : false;
      }

      /**
       * Start Profiling, can also double as timer.
       *
       * Profiling will only start if another profiling process is not already running.
       * XHProf is required, other profilers may be added later.
       *
       * @updated 1.0.4
       * @since 1.0.0.2
       * @author potanin@UD
       */
      static public function profiler_start( $method = false, $args = false ) {
        global $ud_api;

        if( $ud_api[ 'profiling_now' ] && ( $ud_api[ 'profiling_now' ] != $method ) ) {
          return;
        }

        define( 'UD_API_Profiling', true );

        if( extension_loaded( 'xhprof' ) && function_exists( 'xhprof_enable' ) ) {
          xhprof_enable( XHPROF_FLAGS_CPU | XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_MEMORY, $args );
        }

        return self::timer_start( $ud_api[ 'profiling_now' ] = $method );

      }

      /**
       * Stop Profiling.
       *
       * @since 1.0.0.2
       * @author potanin@UD
       */
      static public function profiler_stop( $method = false, $args = false ) {
        global $ud_api;

        if( $ud_api[ 'profiling_now' ] && ( $ud_api[ 'profiling_now' ] != $method ) ) {
          return;
        }

        if( extension_loaded( 'xhprof' ) && class_exists( 'XHProfRuns_Default' ) ) {
          $xhprof_data = xhprof_disable();
          $xhprof_runs = new XHProfRuns_Default();
          $xhprof_runs->save_run( $xhprof_data, $method );
        }

        unset( $ud_api[ 'profiling_now' ] );

        return self::timer_stop( $method );

      }

      /**
       * Attempt to download a remote files attachments
       *
       * @param bool  $images
       * @param array $args
       *
       * @return bool|object
       */
      static public function image_fetch( $images = false, $args = array() ) {

        $images = array_filter( (array) $images );

        //** Image URLs may be passed as string or array, or none at all */
        if( count( $images ) < 1 ) {
          return false;
        }

        self::timer_start( __METHOD__ );

        $args = wp_parse_args( $args, array(
          'upload_dir' => false,
          'timeout'    => 30,
        ) );

        /**
         * Regular Image Download.
         */
        foreach( (array) $images as $count => $url ) {

          $url = esc_url_raw( $url );

          $_image = array(
            'source_url' => $url,
            'error'      => false
          );

          //** Set correct filename ( some URLs can have not valid file extensions ) */
          $filename = sanitize_file_name( basename( $url ) );
          $ext      = false;
          $filetype = wp_check_filetype( $filename );
          if( !$filetype[ 'ext' ] ) {
            $file_headers = get_headers( $url, 1 );
            if( strpos( $file_headers[ 0 ], '200 OK' ) ) {
              if( isset( $file_headers[ 'Content-Type' ] ) ) {
                $file_mime = sanitize_mime_type( $file_headers[ 'Content-Type' ] );
                switch( $file_mime ) {
                  case "image/gif":
                    $ext = 'gif';
                    break;
                  case "image/jpeg":
                    $ext = 'jpg';
                    break;
                  case "image/png":
                    $ext = 'png';
                    break;
                  case "image/bmp":
                    $ext = 'bmp';
                    break;
                }
                if( $ext ) {
                  $filename .= '.' . $ext;
                }
              }
            }
          } else {
            $ext = $filetype[ 'ext' ];
          }

          $_wp_error_data = array(
            'url'       => $url,
            'filename'  => $filename,
            'file_type' => $ext,
          );

          //** We MUST NOT allow to upload not-image files */
          if( !$ext || !in_array( $ext, array( 'gif', 'jpg', 'png', 'bmp', 'jpeg' ) ) ) {
            $_image[ 'error' ] = new WP_Error( __METHOD__, __( 'Invalid file type.', self::$text_domain ), $_wp_error_data );
          }

          //** Set file path */
          if( !empty( $args[ 'upload_dir' ] ) ) {

            if( wp_mkdir_p( $args[ 'upload_dir' ] ) ) {
              $_image[ 'file' ] = trailingslashit( $args[ 'upload_dir' ] ) . wp_unique_filename( $args[ 'upload_dir' ], $filename );
            } else {
              $_image[ 'error' ] = new WP_Error( __METHOD__, __( 'Could not create mentioned directory.', self::$text_domain ) );
            }

          } else {

            $wp_upload_bits = wp_upload_bits( $filename, null, '' );
            if( $wp_upload_bits[ 'error' ] ) {
              $_image[ 'error' ] = new WP_Error( __METHOD__, $wp_upload_bits[ 'error' ], $wp_upload_bits );
            }
            $_image = self::extend( $_image, $wp_upload_bits );

          }

          if( !is_wp_error( $_image[ 'error' ] ) ) {

            $wp_remote_request = wp_remote_request( $url, array_filter( array(
              'method'   => 'GET',
              'timeout'  => $args[ 'timeout' ],
              'stream'   => true,
              'filename' => $_image[ 'file' ]
            ) ) );

            if( is_wp_error( $wp_remote_request ) ) {
              $wp_remote_request->add_data( $_wp_error_data );
              $_image[ 'error' ] = $wp_remote_request;
            } else {

              $_image[ 'file' ]     = $wp_remote_request[ 'filename' ];
              $_image[ 'filesize' ] = filesize( $_image[ 'file' ] );

              /* Disabled. Was failing multiple images
              if( isset( $wp_remote_request[ 'headers' ][ 'content-length'] ) && $_image[ 'filesize' ] != $wp_remote_request[ 'headers' ][ 'content-length'] ) {
                $_image[ 'error' ] =  new WP_Error( 'image_fetch', __( 'Remote file has incorrect size', self::$text_domain ), array(
                  'headers' => $wp_remote_request[ 'headers' ],
                  'image' => $_image
                ));
              }*/

              if( 0 == $_image[ 'filesize' ] ) {
                $_image[ 'error' ] = new WP_Error( __METHOD__, __( 'Zero size file downloaded', self::$text_domain ) );
              }

              $_image = self::extend( $_image, wp_check_filetype( $_image[ 'file' ] ) );

              //require_once( ABSPATH . 'wp-admin/includes/image.php' );
              //wp_update_attachment_metadata( $row->attachment_id, wp_generate_attachment_metadata( $row->attachment_id, $upload[ 'file' ] ) );
            }

          }

          if( is_wp_error( $_image[ 'error' ] ) ) {
            @unlink( $_image[ 'file' ] );
          }

          $return[ $count ] = (object) array_filter( $_image );

        } //** End foreach */

        return (object) array(
          'images' => $return,
          'timer'  => self::timer_stop( __METHOD__ )
        );

      }

      /**
       * Checks if images exist and returns images dimensions
       *
       * @param mixed $images Image url
       * @param mixed $args
       *
       * @return array
       * @author peshkov@UD
       */
      static public function image_dimensions( $images = false, $args = array() ) {

        $result = array();
        $images = array_filter( (array) $images );

        //** Image URLs may be passed as string or array, or none at all */
        if( count( $images ) < 1 ) {
          return $result;
        }

        self::timer_start( __METHOD__ );

        //** Params below are used only by RIM ( getMultiImageTypeAndSize ) **/
        $args = wp_parse_args( $args, array(
          'max_num_of_threads'   => 10,
          'time_limit'           => 30,
          'curl_connect_timeout' => 2,
          'curl_timeout'         => 3,
        ) );

        //** If PHP 5.3.0, and rim class found, we use it. In other case we use default function getimagesize() */
        if( version_compare( PHP_VERSION, '5.3.0' ) >= 0 && method_exists( 'rim', 'getMultiImageTypeAndSize' ) ) {
          $rim      = new rim();
          $response = $rim->getMultiImageTypeAndSize( $images, $args );
          if( is_array( $response ) ) {
            foreach( $response as $r ) {
              $result[ ] = array(
                'width'  => isset( $r[ 'image_data' ][ 'width' ] ) ? $r[ 'image_data' ][ 'width' ] : 0,
                'height' => isset( $r[ 'image_data' ][ 'height' ] ) ? $r[ 'image_data' ][ 'height' ] : 0,
                'url'    => isset( $r[ 'url' ] ) ? $r[ 'url' ] : false,
                'error'  => !empty( $r[ 'error' ] ) ? new WP_Error( 'image_fetch', __( 'Could not get image dimensions (headers)', 'wpp' ), $r[ 'error' ] ) : false
              );
            }
          }
        } else {
          $result = array();
          foreach( $images as $image ) {
            $r         = @getimagesize( $image );
            $result[ ] = array(
              'width'  => isset( $r[ 0 ] ) ? $r[ 0 ] : 0,
              'height' => isset( $r[ 1 ] ) ? $r[ 1 ] : 0,
              'url'    => $image,
              'error'  => empty( $r ) ? new WP_Error( 'image_fetch', __( 'Could not get image dimensions (headers)', 'wpp' ) ) : false
            );
          }

        }

        return $result;
      }

      /**
       * Converts slashes for Windows paths.
       *
       * @since 1.0.0.0
       * @source Flawless
       * @author potanin@UD
       */
      static public function fix_path( $path ) {
        return str_replace( '\\', '/', $path );
      }

      /**
       * Applies trim() function to all values in an array
       *
       * @source WP-Property
       * @since 0.6.0
       */
      static public function trim_array( $array = array() ) {

        foreach( (array) $array as $key => $value ) {

          if( is_object( $value ) ) {
            continue;
          }

          $array[ $key ] = is_array( $value ) ? self::trim_array( $value ) : trim( $value );
        }

        return $array;

      }

      /**
       * Returns all available image sizes
       *
       * @method all_image_sizes
       * @for Utility
       *
       * @param $size {String}
       *
       * @returns array keys: 'width' and 'height'
       */
      static public function all_image_sizes( $size = '' ) {
        global $_wp_additional_image_sizes;

        $image_sizes = (array) $_wp_additional_image_sizes;

        $image_sizes[ 'thumbnail' ] = array(
          'width'  => intval( get_option( 'thumbnail_size_w' ) ),
          'height' => intval( get_option( 'thumbnail_size_h' ) )
        );

        $image_sizes[ 'medium' ] = array(
          'width'  => intval( get_option( 'medium_size_w' ) ),
          'height' => intval( get_option( 'medium_size_h' ) )
        );

        $image_sizes[ 'large' ] = array(
          'width'  => intval( get_option( 'large_size_w' ) ),
          'height' => intval( get_option( 'large_size_h' ) )
        );

        foreach( (array) $image_sizes as $_size => $data ) {
          $image_sizes[ $_size ]            = array_filter( (array) $data );
          $image_sizes[ $_size ][ 'label' ] = self::de_slug( $_size );
          $image_sizes[ $_size ][ 'crop' ]  = isset( $image_sizes[ $_size ][ 'crop' ] ) && $image_sizes[ $_size ][ 'crop' ] ? $image_sizes[ $_size ][ 'crop' ] : false;
        }

        if( $size ) {
          return array_filter( (array) $image_sizes[ $size ] );
        }

        return array_filter( (array) $image_sizes );

      }

      /**
       * Retrieves the attachment ID from the file URL ( guid )
       *
       * @global object $wpdb
       *
       * @param string  $guid
       *
       * @return string
       * @author peshkovUD
       */
      static public function get_image_id_by_guid( $guid ) {
        global $wpdb;
        $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid='%s';", $guid ) );

        return !empty( $attachment[ 0 ] ) ? $attachment[ 0 ] : false;
      }

      /**
       * Returns Image link (url)
       *
       * If image with the current size doesn't exist, we try to generate it.
       * If image cannot be resized, the URL to the main image (original) is returned.
       *
       * @todo Add something to check if requested image size is bigger than the original, in which case cannot be "resized"
       * @todo Add a check to see if the specified image dimensions have changed. Right now only checks if slug exists, not the actualy size.
       *
       * @param bool  $attachment_id
       * @param bool  $size
       * @param array $args
       *
       * @return array|bool|mixed
       */
      static public function get_image_link( $attachment_id = false, $size = false, $args = array() ) {
        global $wp_properties;

        if( !$size || !$attachment_id ) {
          return false;
        }

        $image_sizes = self::all_image_sizes( $size );

        $args = wp_parse_args( $args, array(
          'cache_id'    => sanitize_title( $attachment_id . $size ),
          'return'      => 'string',
          'default'     => '',
          'cache_group' => 'ud_api'
        ) );

        //** Added 'return' arg to avoid cache problems odokienko@UD */
        $args[ 'cache_id' ] .= $args[ 'return' ];

        if( $return = wp_cache_get( $args[ 'cache_id' ], $args[ 'cache_group' ] ) ) {
          return $return;
        }

        $attachment_image_src = ( array ) wp_get_attachment_image_src( $attachment_id, $size );

        //** If wp_get_attachment_image_src() returned the information we need, we return it */
        if( empty( $image_sizes ) || ( is_array( $attachment_image_src ) && $attachment_image_src[ 1 ] == $image_sizes[ $size ][ 'width' ] ) ) {

          $return = $args[ 'return' ] == 'string' ? $attachment_image_src[ 0 ] : array(
            'url'    => $attachment_image_src[ 0 ],
            'link'   => $attachment_image_src[ 0 ],
            'width'  => $attachment_image_src[ 1 ],
            'height' => $attachment_image_src[ 2 ],
            'crop'   => $attachment_image_src[ 3 ]
          );

          wp_cache_set( $args[ 'cache_id' ], $return, $args[ 'cache_group' ] );

          return $return;
        }

        //** If we are this far, that means that the returned image, if any, was not the right size, so we regenreate */
        $image_resize = image_resize( get_attached_file( $attachment_id, true ), $image_sizes[ $size ][ 'width' ], $image_sizes[ $size ][ 'height' ], $image_sizes[ $size ][ 'crop' ] );

        if( is_wp_error( $image_resize ) || !file_exists( $image_resize ) ) {

          if( $attachment_image_src[ 0 ] ) {
            $return = $args[ 'default' ] ? $args[ 'default' ] : $attachment_image_src[ 0 ];
          } else {
            $return = $args[ 'default' ];
          }

        }

        //** If image was resized, we update metadata, cache our result, and return */
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        if( function_exists( 'wp_update_attachment_metadata' ) ) {
          wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, get_attached_file( $attachment_id, true ) ) );
        }

        $attachment_image_src = (array) wp_get_attachment_image_src( $attachment_id, $size );

        $return = $args[ 'return' ] == 'string' ? $attachment_image_src[ 0 ] : array(
          'url'    => $attachment_image_src[ 0 ],
          'link'   => $attachment_image_src[ 0 ],
          'width'  => $attachment_image_src[ 1 ],
          'height' => $attachment_image_src[ 2 ],
          'crop'   => $attachment_image_src[ 3 ]
        );

        wp_cache_set( $args[ 'cache_id' ], $return, $args[ 'cache_group' ] );

        return $return;

      }

      /**
       * Returns Image link (url) with custom size.
       * Almost the same as get_image_link, but the current function can generate images with custom sizes.
       * It generates image with custom size only once.
       *
       * @global     $wpdb
       *
       * @param type $atts
       *
       * @return string
       * @author peshkov@UD
       * @since 0.2.5
       */
      static function get_image_link_with_custom_size( $attachment_id, $width, $height, $crop = false ) {
        global $wpdb;

        // Sanitize
        $height       = absint( $height );
        $width        = absint( $width );
        $needs_resize = true;

        // Look through the attachment meta data for an image that fits our size.
        $meta       = wp_get_attachment_metadata( $attachment_id );
        $upload_dir = wp_upload_dir();
        $base_url   = strtolower( $upload_dir[ 'baseurl' ] );
        $src        = trailingslashit( $base_url ) . $meta[ 'file' ];
        foreach( $meta[ 'sizes' ] as $key => $size ) {
          if( ( $size[ 'width' ] == $width && $size[ 'height' ] == $height ) || $key == sprintf( 'resized-%dx%d', $width, $height ) ) {
            if( !empty( $size[ 'file' ] ) ) {
              $src = str_replace( basename( $src ), $size[ 'file' ], $src );
            }
            $needs_resize = false;
            break;
          }
        }

        // If an image of such size was not found, we can create one.
        if( $needs_resize ) {
          $attached_file = get_attached_file( $attachment_id );
          $resized       = image_make_intermediate_size( $attached_file, $width, $height, $crop );
          if( is_wp_error( $resized ) ) {
            return $resized;
          }

          // Let metadata know about our new size.
          $key = sprintf( 'resized-%dx%d', $width, $height );
          $meta[ 'sizes' ][ $key ] = $resized;
          if( !empty( $resized[ 'file' ] ) ) {
            $src = str_replace( basename( $src ), $resized[ 'file' ], $src );
          }
          wp_update_attachment_metadata( $attachment_id, $meta );

          // Record in backup sizes so everything's cleaned up when attachment is deleted.
          $backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
          if( !is_array( $backup_sizes ) ) $backup_sizes = array();
          $backup_sizes[ $key ] = $resized;
          update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $backup_sizes );

        }

        return array(
          'url'    => esc_url( $src ),
          'width'  => absint( $width ),
          'height' => absint( $height ),
        );
      }

      /**
       * Insert array into an associative array before a specific key
       *
       * @source http://stackoverflow.com/questions/6501845/php-need-help-inserting-arrays-into-associative-arrays-at-given-keys
       * @author potanin@UD
       */
      static public function array_insert_before( $array, $key, $new ) {
        $array = (array) $array;
        $keys  = array_keys( $array );
        $pos   = (int) array_search( $key, $keys );

        return array_merge(
          array_slice( $array, 0, $pos ),
          $new,
          array_slice( $array, $pos )
        );
      }

      /**
       * Insert array into an associative array after a specific key
       *
       * @source http://stackoverflow.com/questions/6501845/php-need-help-inserting-arrays-into-associative-arrays-at-given-keys
       * @author potanin@UD
       */
      static public function array_insert_after( $array, $key, $new ) {
        $array = (array) $array;
        $keys  = array_keys( $array );
        $pos   = (int) array_search( $key, $keys ) + 1;

        return array_merge(
          array_slice( $array, 0, $pos ),
          $new,
          array_slice( $array, $pos )
        );
      }

      /**
       * Attemp to convert a plural US word into a singular.
       *
       * @todo API Service Candidate since we ideally need a dictionary reference.
       * @author potanin@UD
       */
      static public function depluralize( $word ) {
        $rules = array( 'ss' => false, 'os' => 'o', 'ies' => 'y', 'xes' => 'x', 'oes' => 'o', 'ies' => 'y', 'ves' => 'f', 's' => '' );

        foreach( array_keys( $rules ) as $key ) {

          if( substr( $word, ( strlen( $key ) * -1 ) ) != $key )
            continue;

          if( $key === false )
            return $word;

          return substr( $word, 0, strlen( $word ) - strlen( $key ) ) . $rules[ $key ];

        }

        return $word;

      }

      /**
       * Convert bytes into the logical unit of measure based on size.
       *
       * @source Flawless
       * @since 1.0.0.0
       * @author potanin@UD
       */
      static public function format_bytes( $bytes, $precision = 2 ) {
        _deprecated_function( __FUNCTION__, '2.3.0', 'size_format()' );

        return size_format( $bytes, $precision );
      }

      /**
       * Used to enable/disable/print SQL log
       *
       * Usage:
       * self::sql_log( 'enable' );
       * self::sql_log( 'disable' );
       * $queries= self::sql_log( 'print_log' );
       *
       * @since 0.1.0
       */
      static public function sql_log( $action = 'attach_filter' ) {
        global $wpdb;

        if( !in_array( $action, array( 'enable', 'disable', 'print_log' ) ) ) {
          $wpdb->ud_queries[ ] = array( $action, $wpdb->timer_stop(), $wpdb->get_caller() );

          return $action;
        }

        if( $action == 'enable' ) {
          add_filter( 'query', array( __CLASS__, 'sql_log' ), 75 );
        }

        if( $action == 'disable' ) {
          remove_filter( 'query', array( __CLASS__, 'sql_log' ), 75 );
        }

        if( $action == 'print_log' ) {
          $result = array();
          foreach( (array) $wpdb->ud_queries as $query ) {
            $result[ ] = $query[ 0 ] ? $query[ 0 ] . ' (' . $query[ 1 ] . ')' : $query[ 2 ];
          }

          return $result;
        }

      }

      /**
       * Return data for UD Log
       *
       * @updated 1.04
       * @sincde 1.03
       * @note This is a proof of concept, in future it should be able to support AJAX calls so can be displayed via Dynamic Filter.
       * @author potanin@UD
       */
      static public function log( $message = '', $args = array() ) {
        global $wpdb;

        //** Prevents MySQL Gone Away. @todo Should check if connection exists before automatically connecting. */
        //$wpdb->db_connect();

        //** Create Log if it does not exist */
        if( !$wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}ud_log';" ) ) {
          require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
          dbDelta( "CREATE TABLE {$wpdb->prefix}ud_log (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      post_id mediumint(9) DEFAULT NULL COMMENT 'ID of related post.',
      product VARCHAR(100) DEFAULT '' NOT NULL COMMENT 'Slug of related product.',
      feature VARCHAR(100) DEFAULT '' NOT NULL COMMENT 'Slug of specific feature, if applicable.',
      message text NOT NULL COMMENT 'Long description of log entry.',
      type VARCHAR(100) DEFAULT '' NOT NULL COMMENT 'Type of variable stored in message. May be concatentaetd with other data for additional information.',
      action VARCHAR(128) DEFAULT '' NOT NULL COMMENT 'If applicable, a slug for a specific action that triggered the entry.',
      method VARCHAR(128) DEFAULT '' NOT NULL COMMENT 'If applicable, PHP method that triggered log entry.',
      time int(11) NOT NULL,
      UNIQUE KEY id (id),
      KEY post_id (post_id),
      KEY type (type)
    );" );
        }

        $args = array_filter( (array) shortcode_atts( array(
          'post_id' => null,
          'type'    => gettype( $message ),
          'message' => maybe_serialize( $message ),
          'product' => null,
          'feature' => null,
          'action'  => null,
          'method'  => null,
          'time'    => time()
        ), $args ) );

        //** Only the keys below may be updated via $args */
        $wpdb->insert( $wpdb->prefix . 'ud_log', $args );

        return $wpdb->insert_id ? $message : false;
      }

      /**
       * Return data for UD Log
       *
       * @note This is a proof of concept, in future it should be able to support AJAX calls so can be displayed via Dynamic Filter.
       * @author potanin@UD
       */
      static public function get_log( $args = false ) {
        global $wpdb;

        $args = wp_parse_args( $args, array(
          'offset'    => 0,
          'limit'     => 100,
          'last_id'   => false,
          'sort_type' => 'ASC',
          'direction' => 'greater',
          'product'   => '',
          'post_id'   => false,
        ) );

        $where = array();
        if( $args[ 'last_id' ] && $args[ 'last_id' ] > 1 ) {
          $direction = '';
          switch( $args[ 'direction' ] ) {
            case 'greater':
              $direction = '>';
              break;
            case 'less':
              $direction = '<';
              break;
          }
          if( !empty( $direction ) ) {
            $where[ ] = " l.id {$direction} {$args['last_id']} ";
          }
        }

        foreach( $args as $k => $v ) {
          if( in_array( $k, array( 'product', 'post_id' ) ) && !empty( $v ) ) {
            if( is_array( $v ) ) {
              $where[ ] = " l.{$k} IN ( '" . implode( "','", $v ) . "' ) ";
            } else {
              $where[ ] = " l.{$k} = '{$v}' ";
            }
          }
        }

        if( !empty( $where ) ) {
          $where = " WHERE " . implode( " AND ", $where ) . " ";
        } else {
          $where = '';
        }

        $response = $wpdb->get_results( "
    SELECT l.*, p.post_title
    FROM {$wpdb->prefix}ud_log l
    LEFT JOIN {$wpdb->posts} p ON l.post_id = p.ID
    {$where}
    ORDER BY l.id {$args['sort_type']}
    LIMIT {$args['offset']}, {$args['limit']};
  " );

        //die( '<pre>' . print_r( $wpdb->last_query ,true) . '</pre>' );

        return $response;

      }

      /**
       * Removes data from Logs table
       *
       * @param mixed $args
       *
       * @author peshkov@UD
       */
      static public function clear_log( $args = array() ) {
        global $wpdb;

        $args = array_filter( self::prepare_to_sql( wp_parse_args( $args, array(
          'id'         => false,
          'product'    => false,
          'feature'    => false,
          'product_id' => false,
          'type'       => false,
          'action'     => false,
        ) ) ) );

        $where = "";
        foreach( $args as $k => $v ) {
          $where .= empty( $where ) ? " WHERE " : " AND ";
          $where .= " {$k} = '{$v}' ";
        }

        return $wpdb->query( "DELETE FROM {$wpdb->prefix}ud_log {$where}" );
      }

      /**
       * Add an entry to the plugin-specifig log.
       *
       * Creates log if one does not exist.
       *
       * <code>
       * UD\Utility::log( "Settings updated." );
       * </code>
       *
       * @depreciated peshkov@UD
       */
      static public function _log( $message = false, $args = array() ) {

        $args = wp_parse_args( $args, array(
          'type'   => 'default',
          'object' => false,
          'prefix' => 'ud',
        ) );

        extract( $args );

        $log = "{$prefix}_log";

        if( !did_action( 'init' ) ) {
          _doing_it_wrong( __FUNCTION__, sprintf( __( 'You cannot call UD\Utility::log() before the %1$s hook, since the current user is not yet known.' ), 'init' ), '3.4' );
        }

        $current_user = wp_get_current_user();

        $this_log = get_option( $log );

        if( empty( $this_log ) ) {

          $this_log = array();

          $entry = array(
            'time'    => time(),
            'message' => __( 'Log Started.', self::$text_domain ),
            'user'    => $current_user->ID,
            'type'    => $type
          );

        }

        if( $message ) {

          $entry = array(
            'time'    => time(),
            'message' => $message,
            'user'    => $type == 'system' ? 'system' : $current_user->ID,
            'type'    => $type,
            'object'  => $object
          );

        }

        if( !is_array( $entry ) ) {
          return false;
        }

        array_push( $this_log, $entry );

        $this_log = array_filter( $this_log );

        update_option( $log, $this_log );

        return true;

      }

      /**
       * Helpder function for figuring out if another specific function is a predecesor of current function.
       *
       * @since 1.0.0.0
       * @author potanin@UD
       */
      static public function _backtrace_function( $function = false ) {

        foreach( debug_backtrace() as $step ) {
          if( $function && $step[ 'function' ] == $function ) {
            return true;
          }
        }

      }

      /**
       * Helpder function for figuring out if a specific file is a predecesor of current file.
       *
       * @since 1.0.0.0
       * @author potanin@UD
       */
      static public function _backtrace_file( $file = false ) {

        foreach( debug_backtrace() as $step ) {
          if( $file && basename( $step[ 'file' ] ) == $file ) {
            return true;
          }
        }

      }

      /**
       * Fixed serialized arrays which sometimes get messed up in WordPress
       *
       * @source http://shauninman.com/archive/2008/01/08/recovering_truncated_php_serialized_arrays
       */
      static public function repair_serialized_array( $serialized ) {
        $tmp = preg_replace( '/^a:\d+:\{/', '', $serialized );

        return self::repair_serialized_array_callback( $tmp ); // operates on and whittles down the actual argument
      }

      /**
       * The recursive function that does all of the heavy lifing. Do not call directly.


       */
      static public function repair_serialized_array_callback( &$broken ) {

        $data  = array();
        $index = null;
        $len   = strlen( $broken );
        $i     = 0;

        while( strlen( $broken ) ) {
          $i++;
          if( $i > $len ) {
            break;
          }

          if( substr( $broken, 0, 1 ) == '}' ) // end of array
          {
            $broken = substr( $broken, 1 );

            return $data;
          } else {
            $bite = substr( $broken, 0, 2 );
            switch( $bite ) {
              case 's:': // key or value
                $re = '/^s:\d+:"([^\"]*)";/';
                if( preg_match( $re, $broken, $m ) ) {
                  if( $index === null ) {
                    $index = $m[ 1 ];
                  } else {
                    $data[ $index ] = $m[ 1 ];
                    $index          = null;
                  }
                  $broken = preg_replace( $re, '', $broken );
                }
                break;

              case 'i:': // key or value
                $re = '/^i:(\d+);/';
                if( preg_match( $re, $broken, $m ) ) {
                  if( $index === null ) {
                    $index = (int) $m[ 1 ];
                  } else {
                    $data[ $index ] = (int) $m[ 1 ];
                    $index          = null;
                  }
                  $broken = preg_replace( $re, '', $broken );
                }
                break;

              case 'b:': // value only
                $re = '/^b:[01];/';
                if( preg_match( $re, $broken, $m ) ) {
                  $data[ $index ] = (bool) $m[ 1 ];
                  $index          = null;
                  $broken         = preg_replace( $re, '', $broken );
                }
                break;

              case 'a:': // value only
                $re = '/^a:\d+:\{/';
                if( preg_match( $re, $broken, $m ) ) {
                  $broken         = preg_replace( '/^a:\d+:\{/', '', $broken );
                  $data[ $index ] = self::repair_serialized_array_callback( $broken );
                  $index          = null;
                }
                break;

              case 'N;': // value only
                $broken         = substr( $broken, 2 );
                $data[ $index ] = null;
                $index          = null;
                break;
            }
          }
        }

        return $data;
      }

      /**
       * Determine if an item is in array and return checked
       *
       * @since 0.5.0
       */
      static public function checked_in_array( $item, $array ) {

        if( is_array( $array ) && in_array( $item, $array ) ) {
          echo ' checked="checked" ';
        }

      }

      /**
       * Check if the current WP version is older then given parameter $version.
       *
       * @param string $version
       *
       * @return bool
       * @since 1.0.0.0
       * @author peshkov@UD
       */
      static public function is_older_wp_version( $version = '' ) {
        if( empty( $version ) || (float) $version == 0 ) return false;
        $current_version = get_bloginfo( 'version' );
        /** Clear version numbers */
        $current_version = preg_replace( "/^([0-9\.]+)-(.)+$/", "$1", $current_version );
        $version         = preg_replace( "/^([0-9\.]+)-(.)+$/", "$1", $version );

        return ( (float) $current_version < (float) $version ) ? true : false;
      }

      /**
       * Determine if any requested template exists and return path to it.
       *
       * == Usage ==
       * The function will search through: STYLESHEETPATH, TEMPLATEPATH, and any custom paths you pass as second argument.
       *
       * $best_template = UD\Utility::get_template_part( array(
       *   'template-ideal-match',
       *   'template-default',
       * ), array( PATH_TO_MY_TEMPLATES );
       *
       * Note: load_template() extracts $wp_query->query_vars into the loaded template, so to add any global variables to the template, add them to
       * $wp_query->query_vars prior to calling this function.
       *
       * @param mixed $name List of requested templates. Will be return the first found
       * @param array $path [optional]. Method tries to find template in theme, but also it can be found in given list of pathes.
       * @param array $opts [optional]. Set of additional params: 
       *   - string $instance. Template can depend on instance. For example: facebook, PDF, etc. Uses filter: ud::template_part::{instance}
       *   - boolean $load. if true, rendered HTML will be returned, in other case, only found template's path.
       * @load boolean [optional]. If true and a template is found, the template will be loaded via load_template() and returned as a string
       * @author peshkov@UD
       * @version 1.1
       */
      static public function get_template_part( $name, $path = array(), $opts = array() ) {
        $name = (array)$name;
        $template = "";

        /**
         * Set default instance.
         * Template can depend on instance. For example: facebook, PDF, etc.
         */
        $instance = apply_filters( "ud::current_instance", "default" );

        $opts = wp_parse_args( $opts, array(
          'instance' => $instance,
          'load' => false,
        ) );
        
        //** Allows to add/change templates storage directory. */
        $path = apply_filters( "ud::template_part::path", $path, $name, $opts );

        foreach ( $name as $n ) {
          $n = "{$n}.php";
          $template = locate_template( $n, false );
          if ( empty( $template ) && !empty( $path ) ) {
            foreach ( (array)$path as $p ) {
              if ( file_exists( $p . "/" . $n ) ) {
                $template = $p . "/" . $n;
                break( 2 );
              }
            }
          }
          if ( !empty( $template ) ) break;
        }

        $template = apply_filters( "ud::template_part::{$opts['instance']}", $template, array( 'name' => $name, 'path' => $path, 'opts' => $opts ) );
        
        //** If match and load was requested, get template and return */
        if( !empty( $template ) && $opts[ 'load' ] == true ) {
          ob_start();
          load_template( $template, false );
          return ob_get_clean();
        }

        return !empty( $template ) ? $template : false;

      }

      /**
       * The goal of function is going through specific filters and return (or print) classes.
       * This function should not be called directly.
       * Every ud plugin/theme should have own short function ( wrapper ) for calling it. E.g., see: wpp_css().
       * So, use it in template as: <div id="my_element" class="<?php wpp_css("{name_of_template}::my_element"); ?>"> </div>
       *
       * Arguments:
       *  - instance [string] - UD plugin|theme's slug. E.g.: wpp, denali, wpi, etc
       *  - element [string] - specific element in template which will use the current classes.
       *    Element should be called as {template}::{specific_name_of_element}. Where {template} is name of template,
       *    where current classes will be used. This standart is optional. You can set any element's name if you want.
       *  - classes [array] - set of classes which will be used for element.
       *  - return [boolean] - If false, the function prints all classes like 'class1 class2 class3'
       *
       * @param array $args
       *
       * @return string
       * @author peshkov@UD
       * @version 0.1
       */
      static public function get_css_classes( $args = array() ) {

        //** Set arguments */
        $args = wp_parse_args( (array) $args, array(
          'classes'  => array(),
          'instance' => '',
          'element'  => '',
          'return'   => false,
        ) );

        extract( $args );

        //** Cast (set correct types) to avoid issues */
        if( !is_array( $classes ) ) {
          $classes = trim( $classes );
          $classes = str_replace( ',', ' ', $classes );
          $classes = explode( ' ', $classes );
        }

        foreach( $classes as &$c ) {
          $c = trim( $c );
        }

        $instance = (string) $instance;
        $element  = (string) $element;

        //** Now go through the filters */
        $classes = apply_filters( "$instance::css::$element", $classes, $args );

        if( !$return ) {
          echo implode( " ", (array) $classes );
        }

        return implode( " ", (array) $classes );

      }

      /**
       * Return simple array of column tables in a table
       *
       * @version 0.6
       */
      static public function get_column_names( $table ) {

        global $wpdb;

        $table_info = $wpdb->get_results( "SHOW COLUMNS FROM $table" );

        if( empty( $table_info ) ) {
          return array();
        }

        foreach( (array) $table_info as $row ) {
          $columns[ ] = $row->Field;
        }

        return $columns;

      }

      /**
       * Port of jQuery.extend() function.
       *
       * @since 1.0.3
       */
      static public function extend() {

        $arrays = func_get_args();
        $base   = array_shift( $arrays );
        if( !is_array( $base ) ) $base = empty( $base ) ? array() : array( $base );
        foreach( (array) $arrays as $append ) {
          if( !is_array( $append ) ) $append = array( $append );
          foreach( (array) $append as $key => $value ) {
            if( !array_key_exists( $key, $base ) and !is_numeric( $key ) ) {
              $base[ $key ] = $append[ $key ];
              continue;
            }
            if( ( isset( $value ) && @is_array( $value ) ) || ( isset( $base[ $key ] ) && @is_array( $base[ $key ] ) ) ) {
              
              // extend if exists, otherwise create.
              if( isset( $base[ $key ] ) ) {
                $base[ $key ] = self::extend( $base[ $key ], $append[ $key ] );
              } else {
                $base[ $key ] = $append[ $key ];
              }            
              
            } else if( is_numeric( $key ) ) {
              if( !in_array( $value, $base ) ) $base[ ] = $value;
            } else {
              $base[ $key ] = $value;
            }
          }
        }

        return $base;
      }

      /**
       * Returns a URL to a post object based on passed variable.
       *
       * If its a number, then assumes its the id, If it resembles a slug, then get the first slug match.
       *
       * @since 1.0.0
       *
       * @param bool|string $title A page title, although ID integer can be passed as well
       *
       * @return string The page's URL if found, otherwise the general blog URL
       */
      static public function post_link( $title = false ) {

        global $wpdb;

        if( !$title ) return get_bloginfo( 'url' );

        if( is_numeric( $title ) ) return get_permalink( $title );

        if( $id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s  AND post_status='publish'", $title ) ) ) return get_permalink( $id );

        if( $id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE LOWER(post_title) = %s AND post_status='publish'", strtolower( $title ) ) ) ) return get_permalink( $id );

      }

      /**
       * Used to get the current plugin's log created via UD class
       *
       * If no log exists, it creates one, and then returns it in chronological order.
       *
       * Example to view log:
       * <code>
       * print_r( self::get_log() );
       * </code>
       *
       * $param string Event description
       *
       * @depreciated peshkov@UD
       * @uses get_option()
       * @uses update_option()
       * @return array Using the get_option function returns the contents of the log.
       *
       * @param bool $args
       *
       * @return array Using the get_option function returns the contents of the log.
       */
      static public function _get_log( $args = false ) {

        $args = wp_parse_args( $args, array(
          'limit'  => 20,
          'prefix' => 'ud'
        ) );

        extract( $args );

        $this_log = get_option( $prefix . '_log' );

        if( empty( $this_log ) ) {
          $this_log = self::log( false, array( 'prefix' => $prefix ) );
        }

        $entries = (array) get_option( $prefix . '_log' );

        $entries = array_reverse( $entries );

        $entries = array_slice( $entries, 0, $args[ 'args' ] ? $args[ 'args' ] : $args[ 'limit' ] );

        return $entries;

      }

      /**
       * Delete UD log for this plugin.
       *
       * @uses update_option()
       */
      static public function delete_log( $args = array() ) {

        $args = wp_parse_args( $args, array(
          'prefix' => 'ud'
        ) );

        extract( $args );

        $log = "{$prefix}_log";

        delete_option( $log );
      }

      /**
       * Creates Admin Menu page for UD Log
       *
       * @todo Need to make sure this will work if multiple plugins utilize the UD classes
       * @see function show_log_page
       * @since 1.0.0
       * @uses add_action() Calls 'admin_menu' hook with an anonymous ( lambda-style ) function which uses add_menu_page to create a UI Log page
       */
      static public function add_log_page() {
        if( did_action( 'admin_menu' ) ) {
          _doing_it_wrong( __FUNCTION__, sprintf( __( 'You cannot call UD\Utility::add_log_page() after the %1$s hook.' ), 'init' ), '3.4' );

          return false;
        }
        add_action( 'admin_menu', function() {
          add_menu_page( __( 'Log', UD_API_Transdomain ), __( 'Log', UD_API_Transdomain ), current_user_can( 'manage_options' ), 'ud_log', array( 'UD_API', 'show_log_page' ) );
        });
      }

      /**
       * Displays the UD UI log page.
       *
       * @todo Add button or link to delete log
       * @todo Add nonce to clear_log functions
       * @todo Should be refactored to implement adding LOG tabs for different instances (wpp, wpi, wp-crm). peshkov@UD
       *
       * @since 1.0.0.0
       */
      static public function show_log_page() {

        if( $_REQUEST[ 'ud_action' ] == 'clear_log' ) {
          self::delete_log();
        }

        $output = array();

        $output[ ] = '<style type="text/css">.ud_event_row b { background:none repeat scroll 0 0 #F6F7DC; padding:2px 6px;}</style>';

        $output[ ] = '<div class="wrap">';
        $output[ ] = '<h2>' . __( 'Log Page for', self::$text_domain ) . ' ud_log ';
        $output[ ] = '<a href="' . admin_url( "admin.php?page=ud_log&ud_action=clear_log" ) . '" class="button">' . __( 'Clear Log', self::$text_domain ) . '</a></h2>';

        $output[ ] = '<table class="widefat"><thead><tr>';
        $output[ ] = '<th style="width: 150px">' . __( 'Timestamp', self::$text_domain ) . '</th>';
        $output[ ] = '<th>' . __( 'Type', self::$text_domain ) . '</th>';
        $output[ ] = '<th>' . __( 'Event', self::$text_domain ) . '</th>';
        $output[ ] = '<th>' . __( 'User', self::$text_domain ) . '</th>';
        $output[ ] = '<th>' . __( 'Related Object', self::$text_domain ) . '</th>';
        $output[ ] = '</tr></thead>';

        $output[ ] = '<tbody>';

        foreach( (array) self::_get_log() as $event ) {
          $output[ ] = '<tr class="ud_event_row">';
          $output[ ] = '<td>' . self::nice_time( $event[ 'time' ] ) . '</td>';
          $output[ ] = '<td>' . $event[ 'type' ] . '</td>';
          $output[ ] = '<td>' . $event[ 'message' ] . '</td>';
          $output[ ] = '<td>' . ( is_numeric( $event[ 'user' ] ) ? get_userdata( $event[ 'user' ] )->display_name : __( 'None' ) ) . '</td>';
          $output[ ] = '<td>' . $event[ 'object' ] . '</td>';
          $output[ ] = '</tr>';
        }

        $output[ ] = '</tbody></table>';

        $output[ ] = '</div>';

        echo implode( '', (array) $output );

      }

      /**
       * Replace in $str all entries of keys of the given $values
       * where each key will be rounded by $brackets['left'] and $brackets['right']
       * with the relevant values of the $values
       *
       * @param string|array $str
       * @param array        $values
       * @param array        $brackets
       *
       * @return string|array
       * @author odokienko@UD
       */
      static public function replace_data( $str = '', $values = array(), $brackets = array( 'left' => '[', 'right' => ']' ) ) {
        $values       = (array) $values;
        $replacements = array_keys( $values );
        array_walk( $replacements, function(&$val) use ($brackets){
          $val = $brackets[ 'left' ] . $val . $brackets[ 'right' ];
        } );

        return str_replace( $replacements, array_values( $values ), $str );
      }

      /**
       * Wrapper function to send notification with WP-CRM or without one
       *
       * @param array|mixed $args ['message']          using in email notification
       *
       * @uses self::replace_data()
       * @uses wp_crm_send_notification()
       * @return boolean false if notification was not sent successfully
       * @autor odokienko@UD
       */
      static public function send_notification( $args = array() ) {

        $args = wp_parse_args( $args, array(
          'ignore_wp_crm'   => false,
          'user'            => false,
          'trigger_action'  => false,
          'data'            => array(),
          'message'         => '',
          'subject'         => '',
          'crm_log_message' => ''
        ) );

        if( is_numeric( $args[ 'user' ] ) ) {
          $args[ 'user' ] = get_user_by( 'id', $args[ 'user' ] );
        } elseif( filter_var( $args[ 'user' ], FILTER_VALIDATE_EMAIL ) ) {
          $args[ 'user' ] = get_user_by( 'email', $args[ 'user' ] );
        } elseif( is_string( $args[ 'user' ] ) ) {
          $args[ 'user' ] = get_user_by( 'login', $args[ 'user' ] );
        }

        if( !is_object( $args[ 'user' ] ) || empty( $args[ 'user' ]->data->user_email ) ) {
          return false;
        }

        if( function_exists( 'wp_crm_send_notification' ) &&
          empty( $args[ 'ignore_wp_crm' ] )
        ) {

          if( !empty( $args[ 'crm_log_message' ] ) ) {
            wp_crm_add_to_user_log( $args[ 'user' ]->ID, self::replace_data( $args[ 'crm_log_message' ], $args[ 'data' ] ) );
          }

          if ( !empty( $args[ 'trigger_action' ] ) && is_callable( 'WP_CRM_N', 'get_trigger_action_notification' ) ) {
            $notifications = \WP_CRM_N::get_trigger_action_notification( $args[ 'trigger_action' ] );
            if ( !empty( $notifications ) ) {
              return wp_crm_send_notification( $args[ 'trigger_action' ], $args[ 'data' ] );
            }
          }

        }

        if( empty( $args[ 'message' ] ) ) {
          return false;
        }

        return wp_mail( $args[ 'user' ]->data->user_email, self::replace_data( $args[ 'subject' ], $args[ 'data' ] ), self::replace_data( $args[ 'message' ], $args[ 'data' ] ) );

      }

      /**
       * Turns a passed string into a URL slug
       *
       * @param bool|string $content
       * @param bool|string $args Optional list of arguments to overwrite the defaults.
       *
       * @author potanin@UD
       * @version 1.1.0
       * @since 1.0.0
       * @return string
       */
      static public function create_slug( $content = null, $args = false ) {

        if( !$content ) {
          return null;
        }

        $args = wp_parse_args( $args, array(
          'separator' => '-'
        ) );

        $content = preg_replace( '~[^\\pL0-9_]+~u', $args[ 'separator' ], $content ); // substitutes anything but letters, numbers and '_' with separator
        $content = trim( $content, $args[ 'separator' ] );
        $content = iconv( "utf-8", "us-ascii//TRANSLIT", $content ); // TRANSLIT does the whole job
        $content = strtolower( $content );

        // Removed the following logic because it was removing custom separators such as ":". - potanin@UD | October 17, 2013
        // return preg_replace( '~[^-a-z0-9_]+~', '', $content ); // keep only letters, numbers, '_' and separator

        return $content;

      }

      /**
       * Convert a slug to a more readable string
       *
       * @since 1.3
       *
       * @param $string
       *
       * @return string
       */
      static public function de_slug( $string ) {
        return ucwords( str_replace( "_", " ", $string ) );
      }

      /**
       * Returns current url
       *
       * @param mixed $args GET args which should be added to url
       * @param mixed $except_args GET args which will be removed from URL if they exist
       *
       * @return string
       * @author peshkov@UD
       */
      static public function current_url( $args = array(), $except_args = array() ) {
        $url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];

        $args        = wp_parse_args( $args );
        $except_args = wp_parse_args( $except_args );

        if( !empty( $args ) ) {
          foreach( (array) $args as $k => $v ) {
            if( is_string( $v ) ) $url = add_query_arg( $k, $v, $url );
          }
        }

        if( !empty( $except_args ) ) {
          foreach( (array) $except_args as $arg ) {
            if( is_string( $arg ) ) $url = remove_query_arg( $arg, $url );
          }
        }

        return $url;
      }

      /**
       * Prepares data for SQL query.
       *
       * i.e. It should be used when $pwdb->prepare cannot be used.
       * For example:
       * we have situation when SQL query could not be prepared by default $wpdb->prepare:
       * $titles = array( "John's appartment", " '; DELETE FROM $wpdb->posts;# " );
       * $wpdb->query( "SELECT ID FROM $wpdb->posts WHERE post_title IN ( '" . implode ("','", UD_Utility::prepare_to_sql( $titles ) ) . "' ) " );
       *
       * @global type $wpdb
       *
       * @param mixed $args data which should be prepared for SQL query
       *
       * @return mixed prepared data
       * @author peshkov@UD
       */
      static public function prepare_to_sql( $args ) {
        global $wpdb;

        $prepared = $args;
        if( is_array( $prepared ) ) {
          foreach( $prepared as $k => $v ) {
            if( is_string( $v ) ) {
              $prepared[ $k ] = $wpdb->_real_escape( $v );
            } else if( is_array( $v ) ) {
              $prepared[ $k ] = self::prepare_to_sql( $v );
            }
          }
        } else if( is_string( $prepared ) ) {
          $prepared = $wpdb->_real_escape( $prepared );
        }

        return $prepared;
      }

      /**
       * Returns date and/or time using the WordPress date or time format, as configured.
       *
       * @param bool|string $time Date or time to use for calculation.
       * @param bool|string $args List of arguments to overwrite the defaults.
       *
       * @uses wp_parse_args()
       * @uses get_option()
       * @return string|bool Returns formatted date or time, or false if no time passed.
       * @updated 3.0
       */
      static public function nice_time( $time = false, $args = false ) {

        $args = wp_parse_args( $args, array(
          'format' => 'date_and_time'
        ) );

        if( !$time ) {
          return false;
        }

        if( $args[ 'format' ] == 'date' ) {
          return date( get_option( 'date_format' ), $time );
        }

        if( $args[ 'format' ] == 'time' ) {
          return date( get_option( 'time_format' ), $time );
        }

        if( $args[ 'format' ] == 'date_and_time' ) {
          return date( get_option( 'date_format' ), $time ) . ' ' . date( get_option( 'time_format' ), $time );
        }

        return false;

      }

      /**
       * This function is for the encryption of data
       *
       * @source http://stackoverflow.com/questions/1289061/best-way-to-use-php-to-encrypt-and-decrypt
       * @source http://php.net/manual/en/function.base64-encode.php
       * @author williams@ud
       *
       * @param mixed       $pt Object or plain text string
       * @param bool|string $salt The salt to use
       *
       * @return mixed
       */
      static public function encrypt( $pt, $salt = false ) {

        if( !$salt ) $salt = AUTH_SALT;
        $encrypted = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $salt ), $pt, MCRYPT_MODE_CBC, md5( md5( $salt ) ) ) );
        $encrypted = str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), $encrypted );

        return $encrypted;

      }

      /**
       * This function decrypts data
       *
       * @source http://stackoverflow.com/questions/1289061/best-way-to-use-php-to-encrypt-and-decrypt
       * @source http://php.net/manual/en/function.base64-encode.php
       * @author williams@ud
       *
       * @param mixed       $ct Ciphertext
       * @param bool|string $salt The salt to use
       *
       * @return string
       */
      static public function decrypt( $ct, $salt = false ) {

        if( !$salt ) $salt = AUTH_SALT;
        $data = str_replace( array( '-', '_' ), array( '+', '/' ), $ct );
        $mod4 = strlen( $data ) % 4;
        if( $mod4 ) {
          $data .= substr( '====', $mod4 );
        }
        $decrypted = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $salt ), base64_decode( $data ), MCRYPT_MODE_CBC, md5( md5( $salt ) ) ), "\0" );

        return ( $decrypted );

      }

      /**
       * Returns array of full pathes of files or directories which we try to find.
       *
       * @param mixed   $needle Directory(ies) or file(s) which we want to find
       * @param string  $path The path where we try to find it
       * @param boolean $_is_dir We're finding dir or file. Default is file.
       *
       * @return array
       * @author peshkov@UD
       */
      static public function find_file_in_system( $needle, $path, $_is_dir = false ) {
        $return = array();
        $needle = (array) $needle;
        $dir    = @opendir( $path );

        if( $dir ) {
          while( ( $file = readdir( $dir ) ) !== false ) {
            if( $file[ 0 ] == '.' ) {
              continue;
            }
            $fullpath = trailingslashit( $path ) . $file;
            if( is_dir( $fullpath ) ) {
              if( $_is_dir && in_array( $file, $needle ) ) {
                $return[ ] = $fullpath;
              }
              $return = array_merge( $return, self::find_file_in_system( $needle, $fullpath, $_is_dir ) );
            } else {
              if( !$_is_dir && in_array( $file, $needle ) ) {
                $return[ ] = $fullpath;
              }
            }
          }
        }

        return $return;
      }

      /**
       * Gets complicated html entity e.g. Table and ou|ol
       * and removes whitespace characters include new line.
       * we should to do this before use nl2br
       *
       * @author odokienko@UD
       */
      static public function cleanup_extra_whitespace( $content ) {
        $content = preg_replace_callback( '~<(?:table|ul|ol )[^>]*>.*?<\/( ?:table|ul|ol )>~ims', function($matches){
          return preg_replace('~>[\s]+<((?:t[rdh]|li|\/tr|/table|/ul ))~ims', '><$1', $matches[0]);
        }, $content );

        return $content;
      }

      /**
       * Parses passed directory for widget files
       * includes and registers widget if they exist
       *
       * @param string  $path
       * @param boolean $cache
       *
       * @return null
       * @author peshkov@UD
       */
      static public function maybe_load_widgets( $path, $cache = true ) {
        if ( !is_dir( $path ) ) {
          return null;
        }

        $_widgets = wp_cache_get( 'widgets', 'usabilitydynamics' );
        if( !is_array( $_widgets ) ) {
          $_widgets = array();
        }

        if( $cache && !empty( $_widgets[ $path ] ) && is_array( $_widgets[ $path ] ) ) {
          foreach( $_widgets[ $path ] as $_widget ) {
            include_once( $path . "/" . $_widget[ 'file' ] );
            register_widget( $_widget[ 'headers' ][ 'class' ] );
          }
          return null;
        }

        $_widgets[ $path ] = array();

        if ( $dir = @opendir( $path ) ) {
          $headers = array(
            'name' => 'Name',
            'id' => 'ID',
            'type' => 'Type',
            'group' => 'Group',
            'class' => 'Class',
            'version' => 'Version',
            'description' => 'Description',
          );
          while ( false !== ( $file = readdir( $dir ) ) ) {
            $data = @get_file_data( $path . "/" . $file, $headers, 'widget' );
            if( $data[ 'type' ] == 'widget' && !empty( $data[ 'class' ] ) ) {
              include_once( $path . "/" . $file );
              if( class_exists( $data[ 'class' ] ) ) {
                //var_dump( $data[ 'class' ] );
                array_push( $_widgets[ $path ], array(
                  'file' => $file,
                  'headers' => $data,
                ) );
                register_widget( $data[ 'class' ] );
              }
            }
          }
        }

        wp_cache_set( 'widgets', $_widgets, 'usabilitydynamics' );

      }

      /**
       * Includes all PHP files from specific folder
       *
       * @param string $dir Directory's path
       * @author peshkov@UD
       * @version 0.2
       */
      static public function load_files ( $dir = '' ) {
        if ( !empty( $dir ) && is_dir( $dir ) ) {
          if ( $dh = opendir( $dir ) ) {
            while (($file = readdir($dh)) !== false) {
              $path = trailingslashit( $dir ) . $file;
              if( !in_array( $file, array( '.', '..' ) ) && is_file( $path ) && 'php' == pathinfo( $path, PATHINFO_EXTENSION ) ) {
                include_once( $path );
              }
            }
            closedir( $dh );
          }
        }
      }

      /**
       * Localization Functionality.
       *
       * Replaces array's l10n data.
       * Helpful for localization of data which is stored in JSON files ( see /schemas )
       *
       * Usage:
       *
       * add_filter( 'ud::schema::localization', function($locals){
       *    return array_merge( array( 'value_for_translating' => __( 'Blah Blah' ) ), $locals );
       * });
       *
       * $result = self::l10n_localize (array(
       *  'key' => 'l10n.value_for_translating'
       * ) );
       *
       *
       * @param array $data
       * @param array $l10n translated values
       * @return array
       * @author peshkov@UD
       */
      static public function l10n_localize( $data, $l10n = array() ) {

        if ( !is_array( $data ) && !is_object( $data ) ) {
          return $data;
        }

        //** The Localization's list. */
        $l10n = apply_filters( 'ud::schema::localization', $l10n );

        //** Replace l10n entries */
        foreach( $data as $k => $v ) {
          if ( is_array( $v ) ) {
            $data[ $k ] = self::l10n_localize( $v, $l10n );
          } elseif ( is_string( $v ) ) {
            if ( strpos( $v, 'l10n' ) !== false ) {
              preg_match_all( '/l10n\.([^\s]*)/', $v, $matches );
              if ( !empty( $matches[ 1 ] ) ) {
                foreach ( $matches[ 1 ] as $i => $m ) {
                  if ( array_key_exists( $m, $l10n ) ) {
                    $data[ $k ] = str_replace( $matches[ 0 ][ $i ], $l10n[ $m ], $data[ $k ] );
                  }
                }
              }
            }
          }
        }

        return $data;
      }
      
      /**
       * Wrapper for json_encode function.
       * Emulates JSON_UNESCAPED_UNICODE.
       *
       * @param type $arr
       * @return JSON
       * @author peshkov@UD
       */
      static public function json_encode( $arr ) {
        // convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
        array_walk_recursive( $arr,  function(&$item, $key){
          if (is_string($item)) 
            $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), "UTF-8");
        });
        return mb_decode_numericentity( json_encode( $arr ), array( 0x80, 0xffff, 0, 0xffff ), 'UTF-8' );
      }
      
      /**
       * Merges any number of arrays / parameters recursively,
       *
       * Replacing entries with string keys with values from latter arrays.
       * If the entry or the next value to be assigned is an array, then it
       * automagically treats both arguments as an array.
       * Numeric entries are appended, not replaced, but only if they are
       * unique
       *
       * @source http://us3.php.net/array_merge_recursive
       * @version 0.4
       */
      static public function array_merge_recursive_distinct() {
        $arrays = func_get_args();
        $base = array_shift( $arrays );
        if ( !is_array( $base ) ) $base = empty( $base ) ? array() : array( $base );
        foreach ( (array)$arrays as $append ) {
          if ( !is_array( $append ) ) $append = empty( $append ) ? array() : array( $append );
          foreach ( (array)$append as $key => $value ) {
            if ( !array_key_exists( $key, $base ) and !is_numeric( $key ) ) {
              $base[ $key ] = $append[ $key ];
              continue;
            }
            if ( @is_array( $value ) && isset( $base[ $key ] ) && isset( $append[ $key ] ) && is_array( $base[ $key ] ) && is_array( $append[ $key ] ) ) {
              $base[ $key ] = self::array_merge_recursive_distinct( $base[ $key ], $append[ $key ] );
            } else if ( is_numeric( $key ) ) {
              if ( !in_array( $value, $base ) ) $base[ ] = $value;
            } else {
              $base[ $key ] = $value;
            }
          }
        }
        return $base;
      }

    }

  }

}
