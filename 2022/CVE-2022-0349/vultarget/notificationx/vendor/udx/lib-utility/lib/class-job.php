<?php
/**
 * Job Instances.
 *
 * This is a mixture of traditional UD Utility "log" methods with job handling.
 * Jobs may utilize RaaS support.
 *
 * - Jobs and Batches both use same post type (_ud_job). However, batches will have a parent while jobs will not.
 * - Jobs have "job-ready" post_status once ready for processing.
 * - Batches have "job-batch-ready" post_status once ready for processing.
 *
 * @author team@UD
 * @version 0.2.4
 * @namespace UsabilityDynamics
 * @module Utility
 * @author potanin@UD
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\Job' ) ) {

    /**
     * Job Class
     *
     * Extends UsabilityDynamics\Utility to inherit version, text_domain, etc.
     *
     * @class Job
     * @author potanin@UD
     */
    class Job extends Utility {

      /**
       * Job Instance Defaults.
       *
       * self::$defaults
       *
       * @property $defaults
       * @public
       * @type {Array}
       */
      public static $defaults = array(
        'type' => '_default',
        'post_title' => null,
        'post_password' => null,
        'post_status' => 'job-ready',
        'post_type' => '_ud_job'
      );

      /**
       * Meta Keys.
       *
       * self::$_meta
       *
       * @property $_meta
       * @private
       * @type {Array}
       */
      public static $_meta = array(
        'type' => array( 'is_single' => true ),
        'status' => array( 'is_single' => true ),
        'callback::worker' => array( 'is_single' => true ),
        'callback::complete' => array( 'is_single' => true )
      );

      /**
       * Job Instance ID.
       *
       * @property $id
       * @private
       * @type {Integer}
       */
      public $id = null;

      /**
       * Job Status.
       *
       * @property $status
       * @private
       * @type {Integer}
       */
      private $status = null;

      /**
       * Job Type.
       *
       * @property $type
       * @private
       * @type {Integer}
       */
      private $type = null;

      /**
       * Job Batches.
       *
       * @property $batches
       * @private
       * @type {Array}
       */
      private $batches = array();

      /**
       * Job Instance Settings.
       *
       * @property $_settings
       * @private
       * @type {Object}
       */
      private $_settings = stdClass;

      /**
       * Constructor for the Job class.
       *
       *
       * @todo Finish job loading.
       *
       * @method __construct
       * @for Job
       * @constructor
       * @param array|\UsabilityDynamics\object $settings array
       * @return \UsabilityDynamics\Job
       * @version 0.0.1
       * @since 0.0.1
       */
      public function __construct( $settings = array() ) {

        // Register Job Post Type, if needed.
        $this->_register_post_type();

        // Save Settings to Instance, applying defaults, returns deeply-converted object.
        $this->_settings = self::defaults( $settings, self::$defaults );

        // Load job if ID is set.
        if( $this->_settings->id ) {
          return Job::query( array( "id" => $this->_settings->id ) );
        }

        // Generate Title.
        $this->_settings->post_title    = sprintf( __( 'Job %s', self::$text_domain ), $this->_settings->type );

        // Generate public job hash.
        $this->_settings->post_password = uniqid( $this->_settings->type . '-' );

        // Encode payload.
        $this->_settings->post_content = json_encode( (array) $this->_settings->post_content );

        // Insert Job, get job ID.
        $this->id = wp_insert_post( $this->_settings );

        // Handle creation error.
        if( $this->id instanceof WP_Error ) {
          return $this->id;
        }

        // Commit Meta Key.
        foreach( (array) self::$_meta as $_key => $_options ) {

          $_value = $this->_settings->{$_key};

          if( $_value ) {
            update_post_meta( $this->id, 'job::' . $_key, $_value );
          }

        }


        // Worker.
        return $this;

      }

      /**
       * Load Job.
       *
       * Parses post object and adds declared meta.
       *
       * @param null $job
       *
       * @internal param null $id
       *
       * @return object
       */
      public function load_meta( $job = null ) {

        if( !$job ) {
          $_id = $this->id;
        }

        if( is_object( $job ) && $job->id ) {
          $_id = $job->id;
        }

        if( is_integer( $job ) ) {
          $_id = $job;
        }

        if( !$_id ) {
          return new WP_Error( __( 'Could not load job with unknown ID.', self::$text_domain ) );
        }

        $_meta = array();

        foreach( (array) self::$_meta as $_key => $_options ) {
          $_value = get_post_meta( $_id, 'job::' . $_key, $_options[ 'is_single' ] ? true : false );

          if( $_value ) {
            $_meta[ $_key ] = $_value;
          }

        }

        return $_meta;

      }

      /**
       * Delete Job Instance.
       *
       */
      public function delete() {
        global $wpdb;

        // Kill Babies.
        foreach( (array) $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent=%d;", $this->id ) ) as $_child ) {
          $_post = wp_delete_post( $_child, true );
        }

        // Kill Parent.
        $_post = wp_delete_post( $this->id, true );

        // Return object.
        return $_post;

      }

      /**
       * Push Batch Cargo.
       *
       * @param array $data
       * @param array $args
       *
       * @return int|void|\WP_Error
       */
      public function push( $data = array(), $args = array() ) {

        // Convert to JSON String.
        $_data = json_encode( (array) $data );

        // Generate public job hash.
        $_batchid = wp_insert_post( self::defaults( $args, array(
          'post_parent' => $this->id,
          'post_status' => 'job-ready',
          'post_title' => sprintf( __( 'Job Batch %s', self::$text_domain ), $this->_settings->type ),
          'post_password' => uniqid( $this->_settings->type . '-' ),
          'post_type' => $this->_settings->post_type,
          'post_content' => $_data
        )) );

        // Add to batch list if not an error.
        if( !is_wp_error( $_batchid ) ) {
          $this->batches[ $_batchid ] = json_decode( $_data );
        }

        return $_batchid;

      }

      /**
       * Process Mutiple Jobs.
       *
       *
       * @param string $type Job type.
       * @param array  $args
       *
       * @return null
       */
      public static function process_jobs( $type = null, $args = array() ) {

        $_results = array();

        foreach( self::query( $type ) as $_count => $_job ) {

          $_batches = self::query( $_job->type, array(
            "post_parent" => $_job->ID,
            "post_status" => 'job-ready',

          ));

          //die( '<pre>' . print_r( $_batches, true ) . '</pre>' );
          // Get Batches

          // $_results[ $_count ] = apply_filters( 'job::' . $_job->type, $_job, $_results[ $_count ] = $_results[ $_count ] || array(), $_count );

        }

        return $_results;

      }

      /**
       * Query Job Instance(s)
       *
       * @todo Should use instances of Job instead of WP_Post.
       *
       * @param null  $type
       * @param array $args
       *
       * @return array
       */
      public static function query( $type = null, $args = array() ) {

        // Build get_posts query.
        $args = self::defaults( $args, array(
          'posts_per_page'  => 100,
          'offset'          => 0,
          'post_parent'     => 0,
          'orderby'         => 'post_date',
          'order'           => 'DESC',
          'post_type'       => '_ud_job',
          'post_status'     => 'job-ready'
        ));

        // Query by ID.
        if( is_numeric( $type ) ) {
          $args->ID = $type;
        }

        // Query by type.
        if( is_string( $type ) ) {
          $args->meta_key   = 'job::type';
          $args->meta_value = $type;
        }

        // Get all top-level jobs.
        $_jobs = get_posts( (array) $args );

        // Extend Job objects with meta.
        foreach( (array) $_jobs as $_count => $_job ) {

          // Remove unused keys.
          unset( $_jobs[ $_count ]->ping_status );
          unset( $_jobs[ $_count ]->to_ping );
          unset( $_jobs[ $_count ]->pinged );
          unset( $_jobs[ $_count ]->pinged );
          unset( $_jobs[ $_count ]->guid );
          unset( $_jobs[ $_count ]->menu_order );
          unset( $_jobs[ $_count ]->post_mime_type );
          unset( $_jobs[ $_count ]->filter );

          // Decode cargo.
          $_jobs[ $_count ]->post_content = json_decode( $_jobs[ $_count ]->post_content ? $_jobs[ $_count ]->post_content : array() );
          $_jobs[ $_count ]->post_excerpt = json_decode( $_jobs[ $_count ]->post_excerpt ? $_jobs[ $_count ]->post_excerpt : array() );

          // Load Meta into Object.
          foreach( self::load_meta( $_job->ID ) as $_key => $_value ) {
            $_jobs[ $_count ]->{$_key} = $_value;
          }

        }

        // Return formatted jobs result.
        return (object) $_jobs;

      }

        /**
       * Register Job Post Type
       *
       * @private
       * @uses get_post_type_object
       * @uses register_post_type
       */
      private function _register_post_type() {

        // Return if already registered.
        if( get_post_type_object( $this->_settings->post_type ) ) {
          return get_post_type_object( $this->_settings->post_type );
        }

        // Register;
        register_post_type( $this->_settings->post_type, array(
          'labels' => array(
            'name'               => __( 'Jobs', self::$text_domain ),
            'singular_name'      => __( 'Jobs', self::$text_domain ),
            'new_item'           => __( 'New Job', self::$text_domain ),
            'view_item'          => __( 'View Job', self::$text_domain ),
            'all_items'          => __( 'Jobs', self::$text_domain )
          ),
          'description'          => __( 'UsabilityDynamics Jobs.', self::$text_domain ),
          'public'               => false,
          'hierarchical'         => true,
          'exclude_from_search'  => true,
          'publicly_queryable'   => false,
          'show_ui'              => false,
          'show_in_menu'         => false,
          'show_in_nav_menus'    => false,
          'show_in_admin_bar'    => false,
          'map_meta_cap'         => false,
          'supports'             => array( 'raas' ),
          'has_archive'          => false,
          'rewrite'              => false,
          'query_var'            => false,
          'can_export'           => true,
          'delete_with_user'     => false,
          '_edit_link'           => 'veneer/?job=%d',
        ));

        // Return post type object.
        return get_post_type_object( $this->_settings->post_type );

      }

    }

  }

}

