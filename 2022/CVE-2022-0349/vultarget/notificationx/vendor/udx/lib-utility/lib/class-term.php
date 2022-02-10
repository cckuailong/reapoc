<?php
/**
 * Wordpress Terms ( taxonomies ) meta manager.
 * Moved from theme Flawless.
 *
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Theme
 * @author potanin@UD
 * @author peshkov@UD
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\Term' ) ) {

    class Term {
    
      /**
       * Add meta data field to a term.
       *
       */
      static public function add_meta( $term_id, $meta_key, $meta_value, $unique = false ) {

        if( current_theme_supports( 'extended-taxonomies' ) ) {
          return add_post_meta( self::get_post_for_extended_term( $term_id )->ID, $meta_key, $meta_value, $unique );
        }

        return add_metadata( 'taxonomy', $term_id, $meta_key, $meta_value, $unique );
      }

      /**
       * Remove metadata matching criteria from a term.
       *
       *
       */
      static public function delete_meta( $term_id, $meta_key, $meta_value = '' ) {

        if( current_theme_supports( 'extended-taxonomies' ) ) {
          return delete_post_meta( self::get_post_for_extended_term( $term_id )->ID, $meta_key, $meta_value );
        }

        return delete_metadata( 'taxonomy', $term_id, $meta_key, $meta_value );
      }

      /**
       * Retrieve term meta field for a term.
       *
       */
      static public function get_meta( $term_id, $key, $single = false ) {

        if( current_theme_supports( 'extended-taxonomies' ) ) {
          return get_post_meta( self::get_post_for_extended_term( $term_id )->ID, $key, $single );
        }

        return get_metadata( 'taxonomy', $term_id, $key, $single );
      }

      /**
       * Update term meta field based on term ID.
       *
       */
      static public function update_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {

        if( current_theme_supports( 'extended-taxonomies' ) ) {
          return update_post_meta( self::get_post_for_extended_term( $term_id )->ID, $meta_key, $meta_value, $prev_value );
        }

        return update_metadata( 'taxonomy', $term_id, $meta_key, $meta_value, $prev_value );
      }
      
      /**
       * {}
       *
       * @author potanin@UD
       */
      static public function get_post_for_extended_term( $term_id = false, $taxonomy = false ) {
        global $wpdb;

        if( !$term_id ) {
          return false;
        }

        if( is_object( $term_id ) ) {
          $term_id = $term_id->term_id;
          $taxonomy = $taxonomy ? $taxonomy : $term_id->taxonomy;
        }

        //** Try to get taxonomy -if this term only has one relationship, it's a good guess */
        if( !$taxonomy ) {
          $taxonomy = $wpdb->get_col( "SELECT taxonomy FROM {$wpdb->term_taxonomy} WHERE term_id = {$term_id}" );

          if( count( $taxonomy ) > 1 ) {
            return false;
          } else {
            $taxonomy = $taxonomy[0];
          }
        }

        if( !is_numeric( $term_id ) || empty( $taxonomy ) ) {
          return false;
        }

        $post_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE meta_key = 'extended_term_id' AND meta_value = '{$term_id}' AND post_type = '_tp_{$taxonomy}' " );

        if( !$post_id ) {
          return false;
        }

        if( $post_id ) {
          $post = get_post( $post_id );
        }

        if( !$post ) {
          return false;
        }

        return $post;

      }

    }

  }

}



