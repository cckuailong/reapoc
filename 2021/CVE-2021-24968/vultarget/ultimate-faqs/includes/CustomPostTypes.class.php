<?php
/**
 * Class to handle all custom post type definitions for Ultimate FAQs
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewdufaqCustomPostTypes' ) ) {
class ewdufaqCustomPostTypes {

	public function __construct() {

		// Call when plugin is initialized on every page load
		add_action( 'admin_init', 		array( $this, 'create_nonce' ) );
		add_action( 'init', 			array( $this, 'load_cpts' ) );

		// Handle metaboxes
		add_action( 'add_meta_boxes', 		array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', 			array( $this, 'save_meta' ) );
		add_action( 'post_edit_form_tag', 	array( $this, 'add_multipart_form_encoding' ) );

		// Add columns and filters to the admin list of FAQs
		add_filter( 'manage_ufaq_posts_columns', 			array( $this, 'register_faq_table_columns' ) );
		add_action( 'manage_ufaq_posts_custom_column', 		array( $this, 'display_faq_columns_content' ), 10, 2 );
		add_filter( 'manage_edit-ufaq_sortable_columns', 	array( $this, 'register_post_column_sortables' ) );
		add_filter( 'request', 								array( $this, 'orderby_custom_columns' ) );
		add_filter( 'parse_query', 							array( $this, 'convert_to_taxonomy_term' ) );
		add_filter( 'restrict_manage_posts', 				array( $this, 'add_categories_dropdown' ) );
		add_filter( 'posts_clauses',						array( $this, 'sort_by_category' ), 10, 2 );

		// Add the option to bulk reset views from FAQs
		add_filter( 'bulk_actions-edit-ufaq', 			array( $this, 'add_reset_view_count_bulk_action' ) );
		add_filter( 'handle_bulk_actions-edit-ufaq', 	array( $this, 'handle_reset_view_count_bulk_action' ), 10, 3 );
	}

	/**
	 * Initialize custom post types
	 * @since 2.0.0
	 */
	public function load_cpts() {
		global $ewd_ufaq_controller;

		// Define the faq custom post type
		$args = array(
			'labels' => array(
				'name' 					=> __( 'FAQs',           			'ultimate-faqs' ),
				'singular_name' 		=> __( 'FAQ',                   	'ultimate-faqs' ),
				'menu_name'         	=> __( 'FAQs',          			'ultimate-faqs' ),
				'name_admin_bar'    	=> __( 'FAQs',                  	'ultimate-faqs' ),
				'add_new'           	=> __( 'Add New',                 	'ultimate-faqs' ),
				'add_new_item' 			=> __( 'Add New FAQ',           	'ultimate-faqs' ),
				'edit_item'         	=> __( 'Edit FAQ',               	'ultimate-faqs' ),
				'new_item'          	=> __( 'New FAQ',                	'ultimate-faqs' ),
				'view_item'         	=> __( 'View FAQ',               	'ultimate-faqs' ),
				'search_items'      	=> __( 'Search FAQs',           	'ultimate-faqs' ),
				'not_found'         	=> __( 'No FAQs found',          	'ultimate-faqs' ),
				'not_found_in_trash'	=> __( 'No FAQs found in trash', 	'ultimate-faqs' ),
				'all_items'         	=> __( 'All FAQs',              	'ultimate-faqs' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_icon' => 'dashicons-format-chat',
			'rewrite' => array( 
				'slug' => $ewd_ufaq_controller->settings->get_setting( 'slug-base' ) 
			),
			'supports' => array(
				'title', 
				'editor', 
				'author',
				'excerpt',
				'comments'
			),
			'show_in_rest' => true,
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_ufaq_faqs_args', $args );

		// Add an action so addons can hook in before the post type is registered
		do_action( 'ewd_ufaq_faqs_pre_register' );

		// Register the post type
		register_post_type( EWD_UFAQ_FAQ_POST_TYPE, $args );

		// Add an action so addons can hook in after the post type is registered
		do_action( 'ewd_ufaq_faqs_post_register' );

		// Define the review category taxonomy
		$args = array(
			'labels' => array(
				'name' 				=> __( 'FAQ Categories',			'ultimate-faqs' ),
				'singular_name' 	=> __( 'FAQ Category',				'ultimate-faqs' ),
				'search_items' 		=> __( 'Search FAQ Categories', 	'ultimate-faqs' ),
				'all_items' 		=> __( 'All FAQ Categories', 		'ultimate-faqs' ),
				'parent_item' 		=> __( 'Parent FAQ Category', 		'ultimate-faqs' ),
				'parent_item_colon' => __( 'Parent FAQ Category:', 		'ultimate-faqs' ),
				'edit_item' 		=> __( 'Edit FAQ Category', 		'ultimate-faqs' ),
				'update_item' 		=> __( 'Update FAQ Category', 		'ultimate-faqs' ),
				'add_new_item' 		=> __( 'Add New FAQ Category', 		'ultimate-faqs' ),
				'new_item_name' 	=> __( 'New FAQ Category Name', 	'ultimate-faqs' ),
				'menu_name' 		=> __( 'FAQ Categories', 			'ultimate-faqs' ),
            ),
			'public' 		=> true,
			'query_var'		=> true,
            'hierarchical' 	=> true,
            'show_in_rest' 	=> true,
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_ufaq_category_args', $args );

		register_taxonomy( EWD_UFAQ_FAQ_CATEGORY_TAXONOMY, EWD_UFAQ_FAQ_POST_TYPE, $args );

		// Define the review category taxonomy
		$args = array(
			'labels' => array(
				'name' 				=> __( 'FAQ Tags',				'ultimate-faqs' ),
				'singular_name' 	=> __( 'FAQ Tag',				'ultimate-faqs' ),
				'search_items' 		=> __( 'Search FAQ Tags', 		'ultimate-faqs' ),
				'all_items' 		=> __( 'All FAQ Tags', 			'ultimate-faqs' ),
				'parent_item' 		=> __( 'Parent FAQ Tag', 		'ultimate-faqs' ),
				'parent_item_colon' => __( 'Parent FAQ Tag:', 		'ultimate-faqs' ),
				'edit_item' 		=> __( 'Edit FAQ Tag', 			'ultimate-faqs' ),
				'update_item' 		=> __( 'Update FAQ Tag', 		'ultimate-faqs' ),
				'add_new_item' 		=> __( 'Add New FAQ Tag', 		'ultimate-faqs' ),
				'new_item_name' 	=> __( 'New FAQ Tag Name', 		'ultimate-faqs' ),
				'menu_name' 		=> __( 'FAQ Tags', 				'ultimate-faqs' ),
            ),
			'public' 		=> true,
            'hierarchical' 	=> false,
            'show_in_rest' 	=> true,
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_ufaq_tag_args', $args );

		register_taxonomy( EWD_UFAQ_FAQ_TAG_TAXONOMY, EWD_UFAQ_FAQ_POST_TYPE, $args );
	}

	/**
	 * Generate a nonce for secure saving of metadata
	 * @since 2.0.0
	 */
	public function create_nonce() {

		$this->nonce = wp_create_nonce( basename( __FILE__ ) );
	}

	/**
	 * Add in new columns for the ufaq type
	 * @since 2.0.0
	 */
	public function add_meta_boxes() {

		$meta_boxes = array(

			// Add in the FAQ meta information
			'review_meta' => array (
				'id'		=>	'ufaq',
				'title'		=> esc_html__( 'FAQ Details', 'ultimate-faqs' ),
				'callback'	=> array( $this, 'show_faq_meta' ),
				'post_type'	=> EWD_UFAQ_FAQ_POST_TYPE,
				'context'	=> 'normal',
				'priority'	=> 'high'
			),

			// Add in a link to the documentation for the plugin
			'us_meta_need_help' => array (
				'id'		=>	'ewd_ufaq_meta_need_help',
				'title'		=> esc_html__( 'Need Help?', 'ultimate-faqs' ),
				'callback'	=> array( $this, 'show_need_help_meta' ),
				'post_type'	=> EWD_UFAQ_FAQ_POST_TYPE,
				'context'	=> 'side',
				'priority'	=> 'high'
			),
		);

		// Create filter so addons can modify the metaboxes
		$meta_boxes = apply_filters( 'ewd_ufaq_meta_boxes', $meta_boxes );

		// Create the metaboxes
		foreach ( $meta_boxes as $meta_box ) {
			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				$meta_box['callback'],
				$meta_box['post_type'],
				$meta_box['context'],
				$meta_box['priority']
			);
		}
	}

	/**
	 * Add in a link to the plugin documentation
	 * @since 2.0.0
	 */
	public function show_faq_meta( $post ) { 
		global $ewd_ufaq_controller;

		$faq_author = get_post_meta( $post->ID, 'EWD_UFAQ_Post_Author', true );

		$user = wp_get_current_user();
		$faq_author = $faq_author ? $faq_author : $user->display_name;


		$up_votes = get_post_meta( $post->ID, 'FAQ_Up_Votes', true );
		$down_votes = get_post_meta( $post->ID, 'FAQ_Down_Votes', true );

		?>
	
		<input type="hidden" name="ewd_ufaq_nonce" value="<?php echo $this->nonce; ?>">

		<div class='ewd-ufaq-meta-field'>
			<label for='Post_Author'>
				<?php _e( 'Author Display Name:', 'ultimate-faqs' ); ?>
			</label>
			<input type='text' id='ewd-ufaq-post-author' name='faq_author' value='<?php echo esc_attr( $faq_author ); ?>' size='25' />
		</div>

		<?php if ( $ewd_ufaq_controller->settings->get_setting( 'faq-ratings' ) ) { ?>

			<div class='ewd-ufaq-meta-field'>
				<label class='ewd-ufaq-meta-label' for='review_karma'>
					<?php _e( 'Up Votes:', 'ultimate-faqs' ); ?>
				</label>
				<input type='text' id='ewd-ufaq-review-karma' name='up_votes' value='<?php echo esc_attr( $up_votes ); ?>' size='25' />
				<label class='ewd-ufaq-meta-label' for='review_karma'>
					<?php _e( 'Down Votes:', 'ultimate-faqs' ); ?>
				</label>
				<input type='text' id='ewd-ufaq-review-karma' name='down_votes' value='<?php echo esc_attr( $down_votes ); ?>' size='25' />
			</div>

		<?php } ?>

		<?php $custom_fields = ewd_ufaq_decode_infinite_table_setting( $ewd_ufaq_controller->settings->get_setting( 'faq-fields' ) ); ?>

		<?php foreach ( $custom_fields as $faq_field ) { ?>

			<?php $field_value = get_post_meta( $post->ID, "Custom_Field_" . $faq_field->id, true ); ?>

			<div class='ewd-ufaq-meta-field'>
				<label class='ewd-ufaq-score-label' for='<?php echo esc_attr( $faq_field->name ); ?>'>
					<?php echo esc_html( $faq_field->name ) ?>
				</label>

					<?php $options = explode( ',', $faq_field->options ); ?>

					<?php if ( $faq_field->type == 'textarea' ) { ?>
	
							<textarea id='ewd-ufaq-<?php echo esc_attr( $faq_field->name ); ?>' name='EWD_UFAQ_Custom_Field_<?php echo esc_attr( $faq_field->id ); ?>'>
								<?php echo esc_html( $field_value ); ?>
							</textarea>
	
					<?php } elseif ( $faq_field->type == 'dropdown' ) { ?>
						<?php if ( ! empty( $options ) ) { ?>
	
							<select name='EWD_UFAQ_Custom_Field_<?php echo esc_attr( $faq_field->id ); ?>'>
								<?php foreach ( $options as $option ) { ?>
	
									<option value='<?php echo esc_attr( $option ); ?>' <?php echo ( $option == $field_value ? 'selected' : '' ); ?> >
										<?php echo esc_html( $option ); ?>
									</option>
								<?php } ?>
							</select>
	
						<?php } ?>
					<?php } elseif ( $faq_field->type == 'checkbox' ) { ?>
						<?php $field_value = is_array( $field_value ) ? $field_value : array(); ?>
						<?php if ( ! empty( $options ) ) { ?>
	
							<div class='ewd-ufaq-fields-page-radio-checkbox-container'>
								<?php foreach ( $options as $option ) { ?>
	
									<div class='ewd-ufaq-fields-page-radio-checkbox-each'>
										<input type='checkbox' name='EWD_UFAQ_Custom_Field_<?php echo esc_attr( $faq_field->id ); ?>[]' value='<?php echo esc_attr( $option ); ?>' <?php echo ( in_array( $option, $field_value ) ? 'checked' : '' ); ?> />
										<?php echo esc_html( $option ); ?>
									</div>
								<?php } ?>
							</div>
	
						<?php } ?>
					<?php } elseif ( $faq_field->type == 'radio' ) { ?>
						<?php if ( ! empty( $options ) ) { ?>
	
							<div class='ewd-ufaq-fields-page-radio-checkbox-container'>
								<?php foreach ( $options as $option ) { ?>
	
									<div class='ewd-ufaq-fields-page-radio-checkbox-each'>
										<input type='radio' name='EWD_UFAQ_Custom_Field_<?php echo esc_attr( $faq_field->id ); ?>' value='<?php echo esc_attr( $option ); ?>' <?php echo ( $option == $field_value ? 'checked' : '' ); ?> />
										<?php echo esc_html( $option ); ?>
									</div>
								<?php } ?>
							</div>
	
						<?php } ?>
					<?php } elseif ( $faq_field->type == 'date' ) { ?>
	
						<input type='date' class='ewd-ufaq-jquery-datepicker' id='ewd-ufaq-<?php echo esc_attr( $faq_field->name ); ?>' name='EWD_UFAQ_Custom_Field_<?php echo esc_attr( $faq_field->id ); ?>' value='<?php echo esc_attr( $field_value ); ?>' />
	
					<?php } elseif ( $faq_field->type == 'DateTime' ) { ?>
	
						<input type='datetime-local' id='ewd-ufaq-<?php echo esc_attr( $faq_field->name ); ?>' name='EWD_UFAQ_Custom_Field_<?php echo esc_attr( $faq_field->id ); ?>' value='<?php echo esc_attr( $field_value ); ?>' />
	
					<?php } else { ?>
	
						<input type='text' id='ewd-ufaq-<?php echo esc_attr( $faq_field->name ); ?>' name='EWD_UFAQ_Custom_Field_<?php echo esc_attr( $faq_field->id ); ?>' value='<?php echo esc_attr( $field_value ); ?>' size='25' />
	
					<?php } ?>

				</div>

			<?php 
			}
		} 

	/**
	 * Add in a link to the plugin documentation
	 * @since 2.0.0
	 */
	public function show_need_help_meta() { ?>
    
    	<div class='ewd-ufaq-need-help-box'>
    		<div class='ewd-ufaq-need-help-text'>Visit our Support Center for documentation and tutorials</div>
    	    <a class='ewd-ufaq-need-help-button' href='https://www.etoilewebdesign.com/support-center/?Plugin=UFAQ' target='_blank'>GET SUPPORT</a>
    	</div>

	<?php }

	/**
	 * Save the metabox data for each review
	 * @since 2.0.0
	 */
	public function save_meta( $post_id ) {
		global $ewd_ufaq_controller;

		// Verify nonce
		if ( ! isset( $_POST['ewd_ufaq_nonce'] ) || ! wp_verify_nonce( $_POST['ewd_ufaq_nonce'], basename( __FILE__ ) ) ) {

			return $post_id;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

			return $post_id;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( ! get_post_meta( $post_id, 'ufaq_order', true ) ) { update_post_meta( $post_id, 'ufaq_order', 9999 ); }

		if ( isset( $_POST['faq_author'] ) ) 	{ update_post_meta( $post_id, 'EWD_UFAQ_Post_Author', sanitize_text_field( $_POST['faq_author'] ) ); }
		if ( isset( $_POST['up_votes'] ) ) 		{ update_post_meta( $post_id, 'FAQ_Up_Votes', sanitize_text_field( $_POST['up_votes'] ) ); }
		if ( isset( $_POST['down_votes'] ) ) 	{ update_post_meta( $post_id, 'FAQ_Down_Votes', sanitize_text_field( $_POST['down_votes'] ) ); }

		$custom_fields = ewd_ufaq_decode_infinite_table_setting( $ewd_ufaq_controller->settings->get_setting( 'faq-fields' ) );

		foreach ( $custom_fields as $faq_field ) { 
			
			$input_name = 'EWD_UFAQ_Custom_Field_' . $faq_field->id;

			if ( $faq_field->type == 'file' ) {

				$uploaded_file = wp_handle_upload( $_FILES[ $input_name ], array( 'test_form' => false ) );
				$field_value = $uploaded_file['url'];
			}
			elseif ( $faq_field->type == 'checkbox' ) {

				$field_value = ( isset( $_POST[ $input_name ] ) and is_array( $_POST[ $input_name ] ) ) ? array_map( 'sanitize_text_field', $_POST[ $input_name ] ) : array();
				update_post_meta( $post_id, 'Custom_Field_' . $faq_field->id, $field_value );
			}
			else {
				
				$field_value = sanitize_text_field( $_POST[ $input_name ] );
				update_post_meta( $post_id, 'Custom_Field_' . $faq_field->id, $field_value );
			}
		}
	}

	/**
	 * Add in multi-part encoding to handle file uploading in custom fields
	 * @since 2.0.0
	 */
	public function add_multipart_form_encoding() {
		global $post;

    	if ( ! $post ) { return; }

    	if ( get_post_type( $post->ID ) != EWD_UFAQ_FAQ_POST_TYPE ) { return; }

    	echo ' enctype="multipart/form-data"';
	}

	/**
	 * Add in new columns for the ufaq type
	 * @since 2.0.0
	 */
	public function register_faq_table_columns( $defaults ) {
		global $ewd_ufaq_controller;
		
		$defaults['ewd_ufaq_views'] = __( '# of Views', 'ultimate-faqs' );
		$defaults['ewd_ufaq_categories'] = __( 'Categories', 'ultimate-faqs' );
		$defaults['ewd_ufaq_id'] = __( 'Post ID', 'ultimate-faqs' );

		return $defaults;
	}


	/**
	 * Set the content for the custom columns
	 * @since 2.0.0
	 */
	public function display_faq_columns_content ( $column_name, $post_id ) {
		
		if ( $column_name == 'ewd_ufaq_views' ) {

			echo ( get_post_meta( $post_id, 'ufaq_view_count', true ) ? get_post_meta( $post_id, 'ufaq_view_count', true ) : 0 );
		}

		if ( $column_name == 'ewd_ufaq_categories' ) {

			echo get_the_term_list($post_id, EWD_UFAQ_FAQ_CATEGORY_TAXONOMY, '', ', ', '');
		}

		if ( $column_name == 'ewd_ufaq_id' ) {

			echo $post_id;
		}
	}

	/**
	 * Register the sortable columns
	 * @since 2.0.0
	 */
	public function register_post_column_sortables( $column ) {
		global $ewd_ufaq_controller;
	    
	    $column['ewd_ufaq_views'] = 'ewd_ufaq_views';
    	$column['ewd_ufaq_categories'] = 'ewd_ufaq_product';

   		return $column;
	}

	/**
	 * Adjust the wp_query if the orderby clause is one of the custom ones
	 * @since 2.0.0
	 */
	public function orderby_custom_columns( $vars ) {
		global $wpdb;

		if ( ! isset( $vars['orderby'] ) ) { return $vars; }

		if ( $vars['orderby'] == 'ewd_ufaq_views' ) {
			
			$vars = array_merge( 
				$vars, 
				array(
        	    	'meta_key' => 'ufaq_view_count',
        	    	'orderby' => 'meta_value_num'
        	    ) 
        	);
		}

		return $vars;
	}

	/**
	 * Converts the UFAQ category to a taxonomy term
	 * @since 2.0.0
	 */
	public function convert_to_taxonomy_term( $query ) {
		global $typenow;
    	global $pagenow;

    	$taxonomy = EWD_UFAQ_FAQ_CATEGORY_TAXONOMY;

    	$q_vars = &$query->query_vars;

    	if ( empty( $typenow ) or $typenow != EWD_UFAQ_FAQ_POST_TYPE ) { return $query; }

    	if ( ! isset( $q_vars[$taxonomy] ) or ! is_numeric( $q_vars[$taxonomy] )or $q_vars[$taxonomy] == 0 ) { return $query; }
    	    
    	$term = get_term_by( 'id', $q_vars[$taxonomy], $taxonomy );
        $q_vars[$taxonomy] = $term->slug;
	}

	/**
	 * Add a select box for the FAQ's category for UFAQ posts
	 * @since 2.0.0
	 */
	public function add_categories_dropdown() {
		global $typenow;
	    global $wp_query;

	    if ( $typenow != EWD_UFAQ_FAQ_POST_TYPE ) { return; }

	    $faq_taxonomy = get_taxonomy( EWD_UFAQ_FAQ_CATEGORY_TAXONOMY );

	    $args = array(
	        'show_option_all' =>  __("Show All {$faq_taxonomy->label}"),
	        'taxonomy'        =>  EWD_UFAQ_FAQ_CATEGORY_TAXONOMY,
	        'name'            =>  EWD_UFAQ_FAQ_CATEGORY_TAXONOMY,
	        'orderby'         =>  'name',
	        'selected'        =>  isset( $wp_query->query['term'] ) ? $wp_query->query['term'] : '',
	        'hierarchical'    =>  true,
	        'depth'           =>  3,
	        'show_count'      =>  true, // Show # listings in parens
	        'hide_empty'      =>  true,
	    );

	    wp_dropdown_categories( $args );
	}

	/**
	 * Sort the FAQs by the category they've been assigned to
	 * @since 2.0.0
	 */
	public function sort_by_category( $clauses, $wp_query ) {
		global $wpdb;
		
		if ( ! isset( $wp_query->query['orderby'] ) or $wp_query->query['orderby'] != EWD_UFAQ_FAQ_CATEGORY_TAXONOMY ) { return $clauses; }
		
		$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;
		
		$clauses['where'] .= "AND (taxonomy = 'ufaq-category' OR taxonomy IS NULL)";
		$clauses['groupby'] = "object_id";
		$clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC)";

		if ( strtoupper( $wp_query->get( 'order' ) ) == 'ASC' ) {
		    
		    $clauses['orderby'] .= 'ASC';
		} 
		else { 

		    $clauses['orderby'] .= 'DESC';
		}

		return $clauses;
	}

	/**
	 * Adds in a bulk action to reset the FAQ view count
	 * @since 2.0.0
	 */
	public function add_reset_view_count_bulk_action( $actions ) {

		$actions['reset_view_count'] = __( 'Reset View Count', 'ultimate-faqs' );

		return $actions;
	}

	/**
	 * Handles the bulk action to reset the FAQ view count
	 * @since 2.0.0
	 */
	public function handle_reset_view_count_bulk_action( $redirect_to, $doaction, $post_ids ) {

		if ( $doaction != 'reset_view_count' ) { return $redirect_to; }

		foreach ( $post_ids as $post_id ) {
			
			update_post_meta( $post_id, 'ufaq_view_count', 0 );
		}

		return $redirect_to;
	}
}
} // endif;
