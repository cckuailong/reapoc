<?php

/**
 *
 */
class IVE_Ibtana_CPT {

  private static $_instance;

  public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

  public function __construct() {

    add_filter( 'init', [ $this, 'init' ] );

    add_action( 'admin_head', function () {

      $role           = get_role( 'administrator' );

      $capabilities = $this->compile_post_type_capabilities( 'ibtana_template', 'ibtana_templates' );
      foreach ( $capabilities as $capability ) {
        $role->add_cap( $capability );
      }
    } );

    add_filter( 'wp_ajax_ive_ajax_save_template', [ $this, 'ive_ajax_save_template' ] );
    add_filter( 'wp_ajax_ive_get_saved_ibtana_templates_by_terms', [ $this, 'ive_get_saved_ibtana_templates_by_terms' ] );

    add_filter( 'wp_ajax_ive_get_saved_ibtana_templates_by_term_slug', [ $this, 'ive_get_saved_ibtana_templates_by_term_slug' ] );

    add_filter( 'wp_ajax_ive_import_saved_single_ibtana_template', [ $this, 'ive_import_saved_single_ibtana_template' ] );

    add_filter( 'wp_ajax_ive_export_saved_single_ibtana_template', [ $this, 'ive_export_saved_single_ibtana_template' ] );

    add_filter( 'wp_ajax_ive_delete_saved_single_ibtana_template', [ $this, 'ive_delete_saved_single_ibtana_template' ] );

    add_filter( 'wp_ajax_ive_delete_saved_all_ibtana_templates', [ $this, 'ive_delete_saved_all_ibtana_templates' ] );

    add_filter( 'wp_ajax_set_default_save_template_limit_info', [ $this, 'set_default_save_template_limit_info' ] );
  }

  /**
	 * Registers Ibtana template post type
	 */
	public function init() {

    // Compile capabiltites
    $capabilities = $this->compile_post_type_capabilities( 'ibtana_template', 'ibtana_templates' );

    // Create the custom post type
		register_post_type( 'ibtana_template', [
			'label'        => 'Ibtana templates',
			'labels' => array(
				'name'               => __( 'Ibtana templates', 'ibtana-visual-editor' ),
				'singular_name'      => __( 'Ibtana template', 'ibtana-visual-editor' ),
				'menu_name'          => __( 'Ibtana templates', 'ibtana-visual-editor' ),
				'name_admin_bar'     => __( 'Ibtana template', 'ibtana-visual-editor' ),
				'add_new'            => __( 'Add New', 'ibtana-visual-editor' ),
				'add_new_item'       => __( 'Add New Ibtana template', 'ibtana-visual-editor' ),
				'new_item'           => __( 'New Ibtana template', 'ibtana-visual-editor' ),
				'edit_item'          => __( 'Edit Ibtana template', 'ibtana-visual-editor' ),
				'view_item'          => __( 'View Ibtana template', 'ibtana-visual-editor' ),
				'all_items'          => __( 'Saved Templates', 'ibtana-visual-editor' ),
				'search_items'       => __( 'Search Ibtana templates', 'ibtana-visual-editor' ),
				'parent_item_colon'  => __( 'Parent Ibtana templates:', 'ibtana-visual-editor' ),
				'not_found'          => __( 'No ibtana templates found.', 'ibtana-visual-editor' ),
				'not_found_in_trash' => __( 'No ibtana templates found in Trash.', 'ibtana-visual-editor' ),
			),
      'public'                =>  true,
      'exclude_from_search'   =>  false,
			'show_ui'               =>  true,
			// 'show_in_menu'          =>  'edit.php?post_type=product',
      // 'show_in_menu'          =>  'ibtana-visual-editor',
      'show_in_menu'          =>  false,
      'show_in_admin_bar'     =>  false,
      'show_in_rest'          =>  true,
      // 'capability_type'   =>  'ibtana_template',
      'capabilities'      =>  $capabilities
		] );



    // create the custom taxonomy
    register_taxonomy( 'ibtana_template_type', array( 'ibtana_template' ), array(
      'labels' => array(
        'name'              =>  _x( 'Ibtana Template Type', 'taxonomy general name' ),
        'singular_name'     =>  _x( 'Ibtana Template Type', 'taxonomy singular name' ),
        'search_items'      =>  __( 'Search Ibtana Template Types' ),
        'all_items'         =>  __( 'All Ibtana Template Types' ),
        'parent_item'       =>  __( 'Parent Ibtana Template Type' ),
        'parent_item_colon' =>  __( 'Parent Ibtana Template Type:' ),
        'edit_item'         =>  __( 'Edit Ibtana Template Type' ),
        'update_item'       =>  __( 'Update Ibtana Template Type' ),
        'add_new_item'      =>  __( 'Add New Ibtana Template Type' ),
        'new_item_name'     =>  __( 'New Ibtana Template Type Name' ),
        'menu_name'         =>  __( 'Ibtana Template Types' ),
      ),
      'public'            =>  false,
      'hierarchical'      =>  true,
      'show_ui'           =>  true,
      // 'show_in_menu'      =>  true,
      'show_in_rest'      =>  true,
      'show_admin_column' =>  true,
      'query_var'         =>  true,
      'capabilities'      =>  array(
        'manage_terms'  =>  '',
        'edit_terms'    =>  '',
        'delete_terms'  =>  '',
        'assign_terms'  =>  ''
      ),
      'rewrite'           =>  array( 'slug' => 'ibtana_template_type' ),
    ) );
	}


  public function compile_post_type_capabilities( $singular = 'ibtana_template', $plural = 'ibtana_templates' ) {
    return [
      'edit_post'               =>  "edit_$singular",
      'read_post'               =>  "read_$singular",
      // 'delete_post'             =>  "delete_$singular",
      'edit_posts'              =>  "edit_$plural",
      'edit_others_posts'       =>  "edit_others_$plural",
      'publish_posts'           =>  "publish_$plural",
      'read_private_posts'      =>  "read_private_$plural",
      'read'                    =>  "read",
      // 'delete_posts'            =>  "delete_$plural",
      // 'delete_private_posts'    =>  "delete_private_$plural",
      // 'delete_published_posts'  =>  "delete_published_$plural",
      // 'delete_others_posts'     =>  "delete_others_$plural",
      'edit_private_posts'      =>  "edit_private_$plural",
      'edit_published_posts'    =>  "edit_published_$plural",
      // 'create_posts'            =>  "edit_$plural",
    ];
  }


  public function ive_import_saved_single_ibtana_template() {

    // Check for nonce security
    if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
      exit;
    }

  	$single_ive_builder_template = get_post( sanitize_text_field( $_POST['post_id'] ) );
  	if ( !$single_ive_builder_template ) {
  		wp_send_json( [
  			 'status' =>	false,
  			 'msg'		=>	__( 'Template Not Found!', 'ibtana-visual-editor' )
  			]
  		);
  		exit;
  	}
  	$post_content = $single_ive_builder_template->post_content;
  	wp_update_post( wp_slash( array(
      'ID' 						=> sanitize_text_field( $_POST['page_id'] ),
      'post_content'	=> $post_content
  	) ) );
  	wp_send_json( [ 'status' => true ] );
  }

  public function ive_get_saved_ibtana_templates_by_term_slug() {

    // Check for nonce security
    if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
      exit;
    }

    $template_posts = get_posts( [
      'numberposts'	=>	-1,
      'post_type'		=>	'ibtana_template',
      'tax_query'		=>	array(
        array(
          'taxonomy'          =>  'ibtana_template_type',
          'field'             =>  'slug',
          'terms'             =>  sanitize_text_field( $_POST['term_slug'] ),
          'include_children'  =>  false
        )
      )
    ] );

    wp_send_json( [ 'ibtana_templates_response' => $template_posts ] );
  }

  public function ive_get_saved_ibtana_templates_by_terms() {

    // Check for nonce security
    if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
      exit;
    }

    $custom_terms = get_terms( array(
        'taxonomy'    => 'ibtana_template_type',
        'hide_empty'  => false,
    ) );

    $posts_by_terms   = [];
    $new_custom_terms = [];

    foreach ( $custom_terms as $key => $custom_term ) {

      $template_posts = [];

      if ( $custom_term->count ) {
        array_push( $new_custom_terms, $custom_term );
      } else {
        continue;
      }

      if ( empty( $posts_by_terms ) ) {
        $template_posts = get_posts( [
          'numberposts'	=>	-1,
          'post_type'		=>	'ibtana_template',
          'tax_query'		=>	array(
            array(
              'taxonomy'          =>  'ibtana_template_type',
              'field'             =>  'term_id',
              'terms'             =>  $custom_term->term_id,
              'include_children'  =>  false
            )
          )
        ] );

        foreach ( $template_posts as $template_post ) {
          array_push( $posts_by_terms, $template_post );
        }
      }
    }

    $template_limit_info = self::get_template_limit_info();

  	wp_send_json( [ 'ibtana_templates_response' => array(
      'ibtana_terms'                        =>  $new_custom_terms,
      'ibtana_posts'                        =>  $posts_by_terms,
      'save_templates_limit'                =>  $template_limit_info['save_templates_limit'],
      'saved_templates'                     =>  $template_limit_info['saved_templates'],
      'is_add_on_providing_template_limit'  =>  $template_limit_info['is_add_on_providing_template_limit']
    ) ] );
  }


  public function set_default_save_template_limit_info() {

    // Check for nonce security
    if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
      exit;
    }

    $ive_default_settings = array(
      'save_template_limit' => isset( $_POST['save_template_limit'] ) ? sanitize_text_field( $_POST['save_template_limit'] ) : 2
    );
    update_option( 'ive_default_settings', $ive_default_settings );
    wp_send_json(
      [
        'status'  =>  true
      ]
    );
  }


  public static function get_template_limit_info() {
    $save_template_limit = 2;
    $ive_default_settings = get_option( 'ive_default_settings' );

    if ( $ive_default_settings ) {
      if ( isset( $ive_default_settings['save_template_limit'] ) && ( $ive_default_settings['save_template_limit'] != '' ) ) {
        $save_template_limit = $ive_default_settings['save_template_limit'];
      }
    }

    $is_add_on_providing_template_limit = false;
    $ive_add_on_license_info = apply_filters( 'ive_add_on_license_info', [] );
    foreach ( $ive_add_on_license_info as $ive_add_on_license_info ) {
      if ( isset( $ive_add_on_license_info['save_templates_limit'] ) && $ive_add_on_license_info['save_templates_limit'] ) {
        $save_template_limit += $ive_add_on_license_info['save_templates_limit'];
        $is_add_on_providing_template_limit = true;
      }
    }

    return [
      'save_templates_limit'                =>  $save_template_limit,
      'saved_templates'                     =>  wp_count_posts( 'ibtana_template' )->publish,
      'is_add_on_providing_template_limit'  =>  $is_add_on_providing_template_limit
    ];
  }

  public function ive_ajax_save_template() {

    // Check for nonce security
  	if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
  		exit;
  	}

    $template_posts = wp_count_posts( 'ibtana_template' );

    $template_limit_info = self::get_template_limit_info();

    if ( $template_posts->publish < $template_limit_info['save_templates_limit'] ) {

      $_POST_post_type = sanitize_text_field( $_POST['post_type'] );

      $term_name = 'Ibtana ' . ucfirst( $_POST_post_type ) . ' Template';

      if ( !term_exists( $term_name, 'ibtana_template_type' ) ) {
        $term_slug = 'ibtana-' . $_POST_post_type . '-' . 'template';
        wp_insert_term(
          $term_name,
          'ibtana_template_type',
          array(
            'slug'  => $term_slug,
          )
        );
      }

      $ter_id = term_exists( $term_name, 'ibtana_template_type' );
      $term_ids = array();
      array_push( $term_ids, $ter_id['term_id'] );

    	$title   = sanitize_text_field( $_POST['title'] );

    	$post_id = wp_insert_post(
        [
          'post_title'    =>  $title,
          'post_content'  =>  wp_filter_post_kses( $_POST['tpl'] ),
          'post_type'     =>  'ibtana_template',
          'post_status'   =>  'publish',
        ]
      );

    	wp_set_post_terms( $post_id, $term_ids, 'ibtana_template_type' );

      $template_limit_info = self::get_template_limit_info();
      wp_send_json(
        [
          'status'                              =>  true,
          'msg'                                 =>	__( "Successfully saved template '" . $title . "'.", "ibtana-visual-editor" ),
          'save_templates_limit'                =>  $template_limit_info['save_templates_limit'],
          'saved_templates'                     =>  $template_limit_info['saved_templates'],
          'is_add_on_providing_template_limit'  =>  $template_limit_info['is_add_on_providing_template_limit']
        ]
      );
      exit;

    } else {
      wp_send_json(
        [
          'status'  =>  false,
          'msg'     =>  __(
            "Can't Save More Than " . $template_limit_info['save_templates_limit'] . " Templates.",
            "ibtana-visual-editor"
          )
        ]
      );
      exit;
    }

  }


  public function ive_export_saved_single_ibtana_template() {

    // Check for nonce security
    if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
      exit;
    }

    $single_ive_builder_template = get_post( sanitize_text_field( $_POST['post_id'] ) );
    if ( !$single_ive_builder_template ) {
      wp_send_json( [
         'status' =>	false,
         'msg'		=>	__( 'Template Not Found!', 'ibtana-visual-editor' )
        ]
      );
      exit;
    }

    wp_send_json( [
       'status'       =>	true,
       'msg'		      =>	__( 'Template Found!', 'ibtana-visual-editor' ),
       'post_content' =>  $single_ive_builder_template->post_content
      ]
    );
  }


  public function ive_delete_saved_all_ibtana_templates() {
    // Check for nonce security
    if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
      exit;
    }
    $_POST_post_ids = IVE_Loader::ive_sanitize_array( $_POST['post_ids'] );
    foreach ( $_POST_post_ids as $post_id ) {
      wp_delete_post( $post_id, true );
    }
    wp_send_json_success();
  }


  public function ive_delete_saved_single_ibtana_template() {

    // Check for nonce security
    if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
      exit;
    }


    $post_id = sanitize_text_field( $_POST['post_id'] );

    $single_ive_builder_template = get_post( $post_id );
    if ( !$single_ive_builder_template ) {
      wp_send_json( [
         'status' =>	false,
         'msg'		=>	__( 'Template Not Found!', 'ibtana-visual-editor' )
        ]
      );
      exit;
    }

    $is_deleted = wp_delete_post( $post_id, true );

    if ( $is_deleted ) {
      $template_limit_info = self::get_template_limit_info();
      wp_send_json(
        [
          'status'                             =>  true,
          'msg'		                             =>  __( 'Successfully Deleted!', 'ibtana-visual-editor' ),
          'save_templates_limit'               =>  $template_limit_info['save_templates_limit'],
          'saved_templates'                    =>  $template_limit_info['saved_templates'],
          'is_add_on_providing_template_limit' =>  $template_limit_info['is_add_on_providing_template_limit']
        ]
      );
      exit;
    }


  }

}

IVE_Ibtana_CPT::instance();
