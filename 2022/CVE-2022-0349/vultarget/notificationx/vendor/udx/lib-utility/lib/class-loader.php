<?php
/**
 * PHP Loader
 *
 * @namespace UsabilityDynamics
 * @module Utility
 * @version 0.2.3
 * @author potanin@UD
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\Loader' ) ) {

    /**
     * Loader implements a PSR-0 class loader
     *
     *     $loader = new \UsabilityDynamics\Loader();
     *
     *     // register classes with namespaces
     *     $loader->add( 'Symfony', __DIR__ . '/framework' );
     *
     *     // register classes with namespaces by passing an array
     *     $loader->add( array(
     *        'UsabilityDynamics\\' => __DIR__ . '/usabilitydynamics'
     *        'JsonSchema\\' => __DIR__ . '/jsonschema/src'
     *     ));
     *
     *     // activate the autoloader
     *     $loader->register();
     *
     * This class is loosely based on the Symfony UniversalClassLoader.
     *
     * @class Loader
     * @author potanin@UD
     */
    class Loader {

      /**
       * Loader Class version.
       *
       * @property $version
       * @type {Object}
       */
      public static $version = '0.0.3';

      /**
       * Extra header parameters.
       *
       * @property $headers
       * @type {Object}
       */
      public static $headers = array(
        'theme' => array(
          'Name'        => 'Theme Name',
          'ThemeURI'    => 'Theme URI',
          'Description' => 'Description',
          'Author'      => 'Author',
          'AuthorURI'   => 'Author URI',
          'Version'     => 'Version',
          'Template'    => 'Template',
          'Status'      => 'Status',
          'Tags'        => 'Tags'
        )
      );

      /**
       * Configuration.
       *
       * @property $settings
       * @type {Object}
       */
      public $settings = array();

      /**
       * Array with fallback directories for auto-loading.
       *
       * @property $fallback_directories
       * @type {Object}
       */
      public $fallback_directories = array();

      /**
       * Array of stored namespace prefixes.
       *
       * @property $prefixes
       * @type {Object}
       */
      public $prefixes = array();

      /**
       * Array of stored class mappings.
       *
       * @property $class_map
       * @type {Object}
       */
      public $class_map = array();

      /**
       * Constructor for the Loader class.
       *
       * @method __construct
       * @for Loader
       * @constructor
       *
       * @param $settings array
       *
       * @return \UsabilityDynamics\Loader
       * @version 0.0.2
       * @since 0.0.2
       */
      function __construct( $settings = array() ) {

        // Save Loader Settings.
        $this->settings = json_decode( json_encode( $settings ) );

        // Load libraries that use namespaces.
        if( isset( $this->settings->controllers ) ) {
          $this->set_namespace( $this->settings->controllers );
        }

        // Loads libraries that do not use namespaces.
        if( isset( $this->settings->helpders ) ) {
          $this->add_class_map( $this->settings->helpers );
        }

        // Register Autoloader.
        $this->register( true );

        // Prepare Filters.
        add_filter( 'template_redirect', array( $this, 'template_redirect' ) );

        // Utility.
        add_filter( 'extra_theme_headers', array( $this, 'extra_theme_headers' ) );

        // @chainable
        return $this;

      }

      /**
       * Fronted Setup
       *
       * @return $this
       *
       * @method template_redirect
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      function template_redirect() {

        // Unrefister Autoloader.
        $this->unregister();

        // @chainable
        return $this;

      }

      /**
       * Add Color Scheme
       *
       * @method extra_theme_headers
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      function extra_theme_headers() {
        return (array) $this->headers;
      }

      /**
       * Add Class Map
       *
       * @param array $class_map Class to filename map
       *
       * @return $this
       *
       * @method add_class_map
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      public function add_class_map( array $class_map ) {

        if( $this->class_map ) {
          $this->class_map = array_merge( $this->class_map, $class_map );
        } else {
          $this->class_map = $class_map;
        }

        return $this;

      }

      /**
       * Registers a set of classes, merging with any others previously set.
       *
       * @param string       $prefix The classes prefix
       * @param array|string $paths   The location(s) of the classes
       * @param bool         $prepend Prepend the location(s)
       *
       * @return $this
       *
       * @method add_class
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      public function add_class( $prefix, $paths, $prepend = false ) {

        if( !$prefix ) {
          if( $prepend ) {
            $this->fallback_directories = array_merge( (array) $paths, (array) $this->fallback_directories );
          } else {
            $this->fallback_directories = array_merge( (array) $this->fallback_directories, (array) $paths );
          }

          return $this;
        }

        $first = $prefix[ 0 ];

        if( !isset( $this->prefixes[ $first ][ $prefix ] ) ) {
          $this->prefixes[ $first ][ $prefix ] = (array) $paths;

          return $this;
        }
        if( $prepend ) {
          $this->prefixes[ $first ][ $prefix ] = array_merge( (array) $paths, $this->prefixes[ $first ][ $prefix ] );
        } else {
          $this->prefixes[ $first ][ $prefix ] = array_merge( $this->prefixes[ $first ][ $prefix ], (array) $paths );
        }

        return $this;

      }

      /**
       * Registers a set of classes, replacing any others previously set.
       *
       * @param string|array $prefix The classes prefix or an object containing prefixes and strings.
       * @param array|string $paths  The location(s) of the classes
       *
       * @return $this
       *
       * @method set_namespace
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      public function set_namespace( $prefix, $paths = null ) {

        if( is_array( $prefix ) || is_object( $prefix ) ) {

          foreach( $prefix as $namespace => $path ) {
            $this->set_namespace( $namespace, $path );
          }

          return $this;

        }

        if( !$prefix ) {
          $this->fallback_directories = (array) $paths;
        } else {
          $this->prefixes[ substr( $prefix, 0, 1 ) ][ $prefix ] = (array) $paths;
        }

        return $this;

      }

      /**
       * Registers this instance as an autoloader.
       *
       * @param callback $autoload_function [optional]. The autoload function being registered.
       * @param bool     $throw This parameter specifies whether spl_autoload_register() should throw exceptions when the autoload_function cannot be registered.
       * @param bool     $prepend If true, spl_autoload_register() will prepend the autoloader on the autoload stack instead of appending it.
       *
       * @method register
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      public function register( $throw = false, $prepend = false ) {
        spl_autoload_register( array( $this, 'load_class' ), $throw, $prepend );
      }

      /**
       * Unregisters this instance as an autoloader.
       *
       * @method unregister
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      public function unregister() {
        spl_autoload_unregister( array( $this, 'load_class' ) );
      }

      /**
       * Loads the given class or interface.
       *
       * @param  string $class The name of the class
       *
       * @return bool|null True if loaded, null otherwise
       *
       * @method load_class
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      public function load_class( $class ) {

        if( $file = $this->find_file( $class ) ) {
          include $file;

          return true;
        }

      }

      /**
       * Finds the path to the file where the class is defined.
       *
       * - $namespace Raw namespace.
       * - $class_path Fully reoslved name.
       * - $class_name Just the class name.
       *
       * @param string $class The name of the class
       *
       * @return string|false The path if found, false otherwise
       *
       * @method find_file
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       */
      public function find_file( $class ) {

        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        if( '\\' == $class[ 0 ] ) {
          $class = substr( $class, 1 );
        }

        // $class = str_replace( 'Flawless\\', '\\', $class );

        if( isset( $this->class_map[ $class ] ) ) {
          return $this->class_map[ $class ];
        }

        if( false !== $pos = strrpos( $class, '\\' ) ) {
          $class_path = strtr( substr( $class, 0, $pos ), '\\', DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
          $class_name = substr( $class, $pos + 1 );
        } else {
          $class_path = null;
          $class_name = $class;
        }

        $namespace = $class_path;
        $class_path .= strtr( $class_name, '_', DIRECTORY_SEPARATOR ) . '.php';

        $first = $class[ 0 ];

        if( isset( $this->prefixes[ $first ] ) ) {

          //die( '<pre>' . print_r( $this->prefixes[ $first ], true ) . '</pre>' );

          foreach( $this->prefixes[ $first ] as $prefix => $dirs ) {
            if( 0 === strpos( $class, $prefix ) ) {
              foreach( $dirs as $dir ) {

                if( file_exists( $dir . DIRECTORY_SEPARATOR . $class_path ) ) {
                  return $dir . DIRECTORY_SEPARATOR . $class_path;
                }

                // If file not found, try with the namespace stripped
                if( file_exists( $dir . DIRECTORY_SEPARATOR . str_replace( $namespace, '', $class_path ) ) ) {
                  return $dir . DIRECTORY_SEPARATOR . str_replace( $namespace, '', $class_path );
                }

              }
            }
          }
        }

        foreach( $this->fallback_directories as $dir ) {
          if( file_exists( $dir . DIRECTORY_SEPARATOR . $class_path ) ) {
            return $dir . DIRECTORY_SEPARATOR . $class_path;
          }
        }

        return $this->class_map[ $class ] = false;

      }

      /**
       * Parse File Headers
       *
       * @example
       *
       *      Loader::get_file_data( 'style.css' );
       *      Loader::get_file_data( 'my-module/my-module.php', 'module' );
       *
       * @method get_file_data
       * @for Loader
       *
       * @author potanin@UD
       * @version 0.0.2
       * @since 0.0.2
       *
       * @param string $path Full path to the target file.
       * @param string $type Type of target file, defaults to theme, must be defined in Loader::$headers.
       *
       * @return array
       */
      public static function get_file_data( $path = '', $type = 'theme' ) {
        return array_filter( (array) get_file_data( $path, is_string( $type ) ? Loader::$headers[ $type ] : Loader::$headers[ 'theme' ], $type ) );
      }

    }

  }

}

