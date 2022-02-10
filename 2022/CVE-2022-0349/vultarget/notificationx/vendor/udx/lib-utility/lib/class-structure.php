<?php
/**
 * Inits Custom Post Types, Taxonimies, Meta
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Theme
 * @author peshkov@UD
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\Structure' ) ) {

    class Structure {
    
      /**
       *
       *
       */
      static private $args = array();
      
      /**
       *
       *
       */
      static private $structure = array();
      
      /**
       * Define Data Structure
       *
       * @param $args
       * @param $args.types       (array) Post type definitions
       * @param $args.meta        (array) Meta definitions.
       * @param $args.taxonomies  (array) Taxonomy fields.
       *
       * @return array|bool
       */
      static public function define( $args = array() ) {
      
        self::$args = wp_parse_args( $args, array(
          'types' => array(), // Custom post types
          'meta' => array(), // Meta fields
          'taxonomies' => array(), // Taxonomies
        ) );
        
        $structure = array();
        
        foreach( (array) self::$args[ 'types' ] as $object_type => $type ) {
        
          $object_type = sanitize_key( $object_type );
        
          self::$structure[ $object_type ] = array(
            'meta' => array(),
            'terms' => array(),
          );
          
          // STEP 1. Register post_type
          
          // Register Post Type
          $data = ( isset( $type[ 'data' ] ) && is_array( $type[ 'data' ] ) ) ? $type[ 'data' ] : array();

          if( !post_type_exists( $object_type ) ) {
            register_post_type( $object_type, self::_prepare_post_type( $object_type, $data ));
          }
          
          // STEP 2. Register taxonomy ( and Taxonomy's Post Type if theme supports 'extended-taxonomies' feature )
          
          // Define post type's taxonomies
          $taxonomies = ( isset( $type[ 'taxonomies' ] ) && is_array( $type[ 'taxonomies' ] ) ) ? $type[ 'taxonomies' ] : array(
            'post_tag',
            'category',
          );

          // Initialize taxonomies if they don't exist and assign them to the current post type
          foreach( (array) $taxonomies as $taxonomy ) {
            
            if( empty( $taxonomy ) || !is_string( $taxonomy ) ) {
              continue;
            }
            
            if( !taxonomy_exists( $taxonomy ) ) {
              $data = self::_prepare_taxonomy( $taxonomy );
              register_taxonomy( $taxonomy, null, $data );
            }
            
            register_taxonomy_for_object_type( $taxonomy, $object_type );
            
            //** Add custom post type for our taxonomy if theme supports extended-taxonomies */
            $taxonomy_post_type = '_tp_' . $taxonomy;
            if( current_theme_supports( 'extended-taxonomies' ) && !post_type_exists( $taxonomy_post_type ) ) {
              register_post_type( $taxonomy_post_type, array(
                'label' => $data[ 'label' ],
                'public' => false,
                'rewrite' => false,
                'labels' => array(
                  'name' => $data[ 'label' ],
                  'edit_item' => 'Edit Term: ' . $data[ 'label' ]
                ),
                'supports' => array( 'title', 'editor' ),
              ));
            }

            if( isset( $structure[ $object_type ] ) && isset( $structure[ $object_type ]['terms' ] ) && is_array( $structure[ $object_type ]['terms' ] ) ) {
              array_push( $structure[ $object_type ][ 'terms' ], $taxonomy );
            }

          }
          
          // STEP 3. Set meta fields and meta boxes
          
          // Stop here if Meta Box class doesn't exist
          if( !class_exists( '\RW_Meta_Box' ) ) {
            continue;
          }
          
          // Init \RW_Meta_Box defines if needed
          if ( !defined( 'RWMB_VER' ) ) {

            $reflector = new \ReflectionClass( '\RW_Meta_Box' );

            $file = dirname( dirname( $reflector->getFileName() ) ) . '/meta-box.php';
            if( !file_exists( $file ) ) {
              continue;
            }
            include_once( $file );
          }
          
          $metaboxes = ( isset( $type[ 'meta' ] ) && is_array( $type[ 'meta' ] ) ) ? $type[ 'meta' ] : array();

          foreach( $metaboxes as $key => $data ) {
            $data = self::_prepare_metabox( $key, $object_type, $data );

            if( $data ) {
              new \RW_Meta_Box( $data );
            }
          }
          
        }
        
        // STEP 4. reset static vars and return structure data.
        $structure = array(
          'post_types' => self::$structure,
          'schema' => self::$args,
        );
        
        self::$args = array();
        self::$structure = array();
        
        return $structure;
      }
      
      /**
       *
       *
       */
      static private function _prepare_metabox( $key, $object_type, $data ) {
        $label = \UsabilityDynamics\Utility::de_slug( $key );
        
        $data = wp_parse_args( $data, array(
          'id' => $key,
          'title' => $label,
          'pages' => array( $object_type ),
          'context'  => 'normal',
          'priority' => 'high',
          'autosave' => false,
          'fields' => array(),
        ) );

        // There is no sense to init empty metabox
        if( !is_array( $data[ 'fields' ] ) || empty( $data[ 'fields' ] ) ) {
          return false;
        }

        $fields = array();
        foreach( $data[ 'fields' ] as $field ) {
          array_push( self::$structure[ $object_type ][ 'meta' ], $field );
          $fields[] = self::_prepare_metafield( $field );
        }

        $data[ 'fields' ] = $fields;

        return $data;
      }
      
      /**
       *
       *
       */
      static private function _prepare_metafield( $key ) {
        $data = isset( self::$args[ 'meta' ][ $key ] ) ? (array) self::$args[ 'meta' ][ $key ] : array();
        $data = wp_parse_args( $data, array(
          'id' => $key,
          'name' => \UsabilityDynamics\Utility::de_slug( $key ),
          'type' => 'text',
        ) );
        return $data;
      }
      
      /**
       *
       *
       */
      static private function _prepare_taxonomy( $key ) {
        $data = isset( self::$args[ 'taxonomies' ][ $key ] ) && is_array( self::$args[ 'taxonomies' ][ $key ] ) ? self::$args[ 'taxonomies' ][ $key ] : array();
        $data = wp_parse_args( $data, array(
          'label' => \UsabilityDynamics\Utility::de_slug( $key ),
        ) );
        return $data;
      }
      
      /**
       *
       *
       */
      static private function _prepare_post_type( $key, $args = array() ) {
        $args = wp_parse_args( $args, array(
          'label' => \UsabilityDynamics\Utility::de_slug( $key ),
          'exclude_from_search' => false,
        ) );
        return $args;
      }

    }

  }

}



