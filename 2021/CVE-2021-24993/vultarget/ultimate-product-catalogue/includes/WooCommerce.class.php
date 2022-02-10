<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdupcpWooCommerce' ) ) {
/**
 * Class to handle interactions with the WooCommerce platform
 *
 * @since 5.0.0
 */
class ewdupcpWooCommerce {

	// Record the terms that have been edited, to avoid infinite loops
	public $edited_terms = array();
	
	// Record the products that have been edited, to avoid infinite loops
	public $edited_posts = array();

	public function __construct() {
		
		add_action( 'init', array( $this, 'add_hooks' ) );

		if ( ! empty( $_POST['ewd-upcp-settings']['woocommerce-sync'] ) ) { 
			
			add_action( 'init', array( $this, 'run_sync' ) );
		}
	}

	/**
	 * Adds in the necessary hooks to handle WooCommerce integration
	 * @since 5.0.0
	 */
	public function add_hooks() {
		global $ewd_upcp_controller;

		if ( empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-sync' ) ) ) { return; }

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			add_action( 'admin_notices', array( $this, 'display_woocommerce_inactive_notice' ) );
		}

		add_action( 'woocommerce_cart_emptied',	array( $this, 'clear_product_catalog_cart' ) );

		add_action( 'woocommerce_before_single_product', array( $this, 'maybe_add_back_to_catalog_link' ) );

		add_action( 'edited_product_cat', 				array( $this, 'import_category_into_upcp' ) );
		add_action( 'edited_product_tag', 				array( $this, 'import_tag_into_upcp' ) );

		add_action( 'woocommerce_attribute_added', 		array( $this, 'import_attributes_into_upcp' ) );
		add_action( 'woocommerce_attribute_updated', 	array( $this, 'import_attributes_into_upcp' ) );

		add_action( 'woocommerce_update_product', 		array( $this, 'import_product_into_upcp' ) );

		add_action( 'edited_' . EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY, array( $this, 'import_category_from_upcp' ) );
		add_action( 'edited_' . EWD_UPCP_PRODUCT_TAG_TAXONOMY, 		array( $this, 'import_tag_from_upcp' ) );

		add_action( 'ewd_upcp_custom_fields_updated', 				array( $this, 'import_custom_fields_from_upcp' ) );

		add_action( 'ewd_upcp_product_saved', 						array( $this, 'import_product_from_upcp' ) );
	}

	/**
	 * Syncs WooCommerce products, categories, etc. when the WooCommerce Integration
	 * setting is saved and toggled on.
	 * @since 5.0.0
	 */
	public function run_sync() {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) { return; }

		$this->import_categories_into_upcp();
		$this->import_categories_from_upcp();

		$this->import_tags_into_upcp();
		$this->import_tags_from_upcp();

		$this->import_attributes_into_upcp();
		$this->import_custom_fields_from_upcp();

		$this->import_products_into_upcp();
		$this->import_products_from_upcp();
	}

	/**
	 * If sync enabled, clear UPCP cart when WooCommerce cart is emptied
	 * @since 5.0.0
	 */
	public function clear_product_catalog_cart() {
		global $ewd_upcp_controller;

		$ewd_upcp_controller->ajax->clear_cart();
	}

	/**
	 * Display a notice if WooCommerce sync is enabled, but WooCommerce isn't activated
	 * @since 5.0.0
	 */
	public function display_woocommerce_inactive_notice() { ?>
		
		<div class="notice notice-warning is-dismissible">

    	    <p>
    	    	<?php _e( 'Ultimate Product Catalog WooCommerce sync is enabled, but WooCommerce is not activated. Please activate WooCommerce and then re-save the Product Catalog\'s WooCommerce options tab for the syncing feature to work correctly.', 'ultimate-product-catalogue' ); ?>
    	    </p>

    	</div>

		<?php
	}

	/**
	 * Adds a 'Back to Catalog' link and hides the default WooCommerce breadcrumbs, if enabled
	 * @since 5.0.0
	 */
	public function maybe_add_back_to_catalog_link() {
		global $ewd_upcp_controller;

		if ( empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-back-link' ) ) or empty( $ewd_upcp_controller->settings->get_setting( 'woocommerce-sync' ) ) ) { return; }

		if ( empty( $_GET['ewd_upcp_catalog_url'] ) ) { return; }

		?>

		<style>
			.woocommerce-breadcrumb {display:none;}
		</style>

		<a class='ewd-upcp-catalogue-link' href='<?php echo esc_attr( esc_url_raw( $_GET['ewd_upcp_catalog_url'] ) ); ?>'>
			<?php echo esc_html( $ewd_upcp_controller->settings->get_setting( 'label-back-to-catalog' ) ); ?>
		</a>

		<?php
	}

	/**
	 * Import WooCommerce categories into UPCP
	 * @since 5.0.0
	 */
	public function import_categories_into_upcp() {

		$args = array(
			'hide_empty' => false
		);

		$categories = get_terms( 'product_cat', $args );

    	if ( ! $categories ) { return; }
    	    
    	foreach ( $categories as $category ) {

    	    $this->import_category_into_upcp( $category->term_id );
    	}
	}

	/**
	 * Import categories from UPCP into WooCommerce
	 * @since 5.0.0
	 */
	public function import_categories_from_upcp() {

		$args = array(
			'hide_empty' => false
		);

		$categories = get_terms( EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY, $args );

    	if ( ! $categories ) { return; }
    	    
    	foreach ( $categories as $category ) {

    	    $this->import_category_from_upcp( $category->term_id );
    	}
	}

	/**
	 * Import WooCommerce tags into UPCP
	 * @since 5.0.0
	 */
	public function import_tags_into_upcp() {

		$args = array(
			'hide_empty' => false
		);

		$tags = get_terms( 'product_tag', $args );

    	if ( ! $tags ) { return; }
    	    
    	foreach ( $tags as $tag ) {

    	    $this->import_tag_into_upcp( $tag->term_id );
    	}
	}

	/**
	 * Import tags from UPCP into WooCommerce
	 * @since 5.0.0
	 */
	public function import_tags_from_upcp() {

		$args = array(
			'hide_empty' => false
		);

		$tags = get_terms( EWD_UPCP_PRODUCT_TAG_TAXONOMY, $args );

    	if ( ! $tags ) { return; }
    	    
    	foreach ( $tags as $tag ) {

    	    $this->import_tag_from_upcp( $tag->term_id );
    	}
	}

	/**
	 * Import WooCommerce attributes into UPCP (as custom fields)
	 * @since 5.0.0
	 */
	public function import_attributes_into_upcp() {
		global $wpdb;
		global $ewd_upcp_controller;

		$custom_fields = $ewd_upcp_controller->settings->get_custom_fields();

		$wc_attribute_table_name = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
    	$wc_attributes = $wpdb->get_results("SELECT * FROM $wc_attribute_table_name");
   	
    	foreach ( $wc_attributes as $wc_attribute ) {

    		$term_ids = $wpdb->get_results( $wpdb->prepare( "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy=%s", 'pa_' . $wc_attribute->attribute_name ) );

    		$attribute_terms = array();
    		$term_id_for_value = array();
    		$replace_values = array();

    		foreach ( $term_ids as $term_id ) {

				$attribute_terms[] = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM $wpdb->terms WHERE term_id=%d", $term_id->term_id ) );
			}

			$exists = false;
			$max_id = 0;

			foreach ( $custom_fields as $key => $custom_field ) {

				$max_id = max( $custom_field->id, $max_id );

				if ( $custom_field->woocommerce_id != $wc_attribute->attribute_id ) { continue; }

				$exists = true;

				$custom_fields[ $key ]->name = $wc_attribute->attribute_label;
				$custom_fields[ $key ]->slug = $wc_attribute->attribute_name;
				$custom_fields[ $key ]->type = $wc_attribute->attribute_type;
				$custom_fields[ $key ]->options = implode( ',', $attribute_terms );
			}

			if ( ! $exists ) {

				$custom_fields[] = (object) array(
					'id'					=> $max_id + 1,
					'name' 					=> $wc_attribute->attribute_label,
					'slug' 					=> $wc_attribute->attribute_name,
					'type' 					=> $wc_attribute->attribute_type,
					'options' 				=> implode( ',', $attribute_terms ),
					'displays'				=> array(),
					'filter_control_type'	=> 'checkbox',
					'searchable'			=> false,
					'tabbed_display'		=> false,
					'comparison_display'	=> false,
					'woocommerce_id'		=> $wc_attribute->attribute_id
				);
			}
    	}

    	$ewd_upcp_controller->settings->update_custom_fields( $custom_fields );
	}

	/**
	 * Import custom fields from UPCP into WooCommerce (as attributes)
	 * @since 5.0.0
	 */
	public function import_custom_fields_from_upcp() {
		global $wpdb;
		global $ewd_upcp_controller;

		$custom_fields = $ewd_upcp_controller->settings->get_custom_fields();

		$wc_attribute_table_name = $wpdb->prefix . 'woocommerce_attribute_taxonomies';

    	if ( ! $custom_fields ) { return; }
    	
    	foreach ( $custom_fields as $key => $custom_field ) {

    	    $attribute = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wc_attribute_table_name WHERE attribute_id=%d", $custom_field->woocommerce_id ) );

    	    if ( empty( $attribute ) ) {

    	    	$wpdb->query( $wpdb->prepare( "INSERT INTO $wc_attribute_table_name (attribute_label, attribute_name, attribute_type, attribute_orderby, attribute_public) VALUES ( %s, %s, %s, 'menu_order', 1 )", $custom_field->name, $custom_field->slug, $custom_field->type ) );
    	    	
    	    	$custom_fields[ $key ]->woocommerce_id = $wpdb->insert_id;

    	    	// Load the attribute so that we can insert term values below 
    	    	$attribute = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wc_attribute_table_name WHERE attribute_id=%d", $custom_fields[ $key ]->woocommerce_id ) );
    	    }
    	    else {

    	    	$wpdb->query( $wpdb->prepare( "UPDATE $wc_attribute_table_name SET attribute_label=%s, attribute_name=%s, attribute_type=%s WHERE attribute_id=%d", $custom_field->name, $custom_field->slug, $custom_field->type, $attribute->attribute_id ) );
    	    }

    	    if ( empty( $custom_field->options ) ) { continue; }

    	    $attribute_values = explode( ',', $custom_field->options );

    	    $term_ids = array();

    	    foreach ( $attribute_values as $attribute_value ) {

    	    	$term_id = $wpdb->get_var( 
    	    		$wpdb->prepare(
    	    			"SELECT terms.term_id 
    	    				FROM $wpdb->terms terms 
    	    				INNER JOIN $wpdb->term_taxonomy taxonomy ON terms.term_id = taxonomy.term_id 
    	    				WHERE terms.name=%s AND taxonomy.taxonomy=%s",
    	    			$attribute_value,
    	    			'pa_' . $attribute->attribute_name
    	    		)
    	    	);

    	    	if ( ! empty( $term_id ) ) { 

    	    		$term_ids[] = $term_id;

    	    		continue; 
    	    	}

    	    	$wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->terms (name, slug) VALUES (%s, %s)", $attribute_value, sanitize_title_with_dashes( $attribute_value ) ) );
    			
    			if ( $wpdb->insert_id ) { 

    				$term_ids[] = $wpdb->insert_id;

    				$wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->term_taxonomy (term_id, taxonomy) VALUES (%d, %s)", $wpdb->insert_id, 'pa_' . $attribute->attribute_name ) );
    			}
    	    }

    	    if ( empty( $term_ids ) ) { continue; }

    	    $wpdb->query( 
    	    	$wpdb->prepare( 
    	    		"DELETE terms.*  FROM $wpdb->terms terms 
    	    		INNER JOIN $wpdb->term_taxonomy taxonomy ON terms.term_id = taxonomy.term_id
    	    		WHERE terms.term_id NOT IN ( '" . implode( "', '", $term_ids ) . "' ) AND taxonomy.taxonomy=%s",
    	    		'pa_' . $attribute->attribute_name
    	    	) 
    	    );
    	}

    	delete_transient( 'wc_attribute_taxonomies' );
    	
    	$ewd_upcp_controller->settings->update_custom_fields( $custom_fields );
	}

	/**
	 * Import WooCommerce products into UPCP
	 * @since 5.0.0
	 */
	public function import_products_into_upcp() {

		$args = array(
			'posts_per_page' 	=> -1,
			'post_type'			=> 'product',
		);

		$wc_products = get_posts( $args );

    	if ( ! $wc_products ) { return; }
    	    
    	foreach ( $wc_products as $wc_product ) {

    	    $this->import_product_into_upcp( $wc_product->ID );
    	}
	}

	/**
	 * Import WooCommerce products from UPCP
	 * @since 5.0.0
	 */
	public function import_products_from_upcp() {

		$args = array(
			'posts_per_page' 	=> -1,
			'post_type'			=> EWD_UPCP_PRODUCT_POST_TYPE,
		);

		$upcp_products = get_posts( $args );

    	if ( ! $upcp_products ) { return; }
    	    
    	foreach ( $upcp_products as $upcp_product ) {

    	    $this->import_product_from_upcp( $upcp_product->ID );
    	}
	}

	/**
	 * Adds a WooCommerce category to UPCP if it does not exist, edit otherwise
	 * @since 5.0.0
	 */
	public function import_category_into_upcp( $term_id ) {

		if ( $this->check_term_loop( $term_id ) ) { return; }

		$wc_category = get_term( $term_id, 'product_cat' );

		$args = array(
			'hide_empty'	=> false,
			'meta_query'	=> array(
				array(
					'key'		=> 'woocommerce_id',
					'value'		=> $term_id,
					'compare'	=> 'LIKE'
				)
			),
			'taxonomy'		=> EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY
		);

		$upcp_categories = get_terms( $args );

		if ( empty( $upcp_categories ) ) {

			$args = array(
				'description'	=> $wc_category->description,
				'parent'		=> $this->get_upcp_category_parent_from_wc_category( $wc_category )
			);

			$upcp_category = wp_insert_term( $wc_category->name, EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY, $args );

			if ( ! is_wp_error( $upcp_category ) ) {

				update_term_meta( $upcp_category['term_id'], 'image', get_term_meta( $wc_category->term_id, 'thumbnail_id', true ) );
				update_term_meta( $upcp_category['term_id'], 'woocommerce_id', $wc_category->term_id );
			}
		}
		else {

			$upcp_category = reset( $upcp_categories );

			$args = array(
				'name'			=> $wc_category->name,
				'description'	=> $wc_category->description,
				'parent'		=> $this->get_upcp_category_parent_from_wc_category( $wc_category )
			);

			wp_update_term( $upcp_category->term_id, EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY, $args );

			update_term_meta( $upcp_category->term_id, 'image', get_term_meta( $wc_category->term_id, 'thumbnail_id', true ) );
		}
	}

	/**
	 * Adds a UPCP category to WooCommerce if it does not exist, edit otherwise
	 * @since 5.0.0
	 */
	public function import_category_from_upcp( $term_id ) {

		if ( $this->check_term_loop( $term_id ) ) { return; }

		$upcp_category = get_term( $term_id, EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY );

		$wc_category = get_term( get_term_meta( $upcp_category->term_id, 'woocommerce_id', true ), 'product_cat' );

		if ( empty( $wc_category ) or is_wp_error( $wc_category ) ) {

			$args = array(
				'description'	=> $upcp_category->description,
				'parent'		=> $this->get_wc_category_parent_from_upcp_category( $upcp_category )
			);

			$wc_category = wp_insert_term( $upcp_category->name, 'product_cat', $args );

			if ( ! is_wp_error( $wc_category ) ) {

				update_term_meta( $upcp_category->term_id, 'woocommerce_id', $wc_category['term_id'] );
			}
		}
		else {

			$args = array(
				'name'			=> $upcp_category->name,
				'description'	=> $upcp_category->description,
				'parent'		=> $this->get_wc_category_parent_from_upcp_category( $upcp_category )
			);

			wp_update_term( $wc_category->term_id, 'product_cat', $args );
		}
	}

	/**
	 * Adds a WooCommerce tag to UPCP if it does not exist, edit otherwise
	 * @since 5.0.0
	 */
	public function import_tag_into_upcp( $term_id ) {

		if ( $this->check_term_loop( $term_id ) ) { return; }

		$wc_tag = get_term( $term_id, 'product_tag' );

		$args = array(
			'hide_empty'	=> false,
			'meta_query'	=> array(
				array(
					'key'		=> 'woocommerce_id',
					'value'		=> $term_id,
					'compare'	=> 'LIKE'
				)
			),
			'taxonomy'		=> EWD_UPCP_PRODUCT_TAG_TAXONOMY
		);

		$upcp_tags = get_terms( $args );

		if ( empty( $upcp_tags ) ) {

			$args = array(
				'description'	=> $wc_tag->description,
			);

			$upcp_tag = wp_insert_term( $wc_tag->name, EWD_UPCP_PRODUCT_TAG_TAXONOMY, $args );

			if ( ! is_wp_error( $upcp_tag ) ) {

				update_term_meta( $upcp_tag['term_id'], 'woocommerce_id', $wc_tag->term_id );
			}
		}
		else {

			$upcp_tag = reset( $upcp_tags );

			$args = array(
				'name'			=> $wc_tag->name,
				'description'	=> $wc_tag->description,
			);

			wp_update_term( $upcp_tag->term_id, EWD_UPCP_PRODUCT_TAG_TAXONOMY, $args );
		}
	}

	/**
	 * Adds a UPCP tag to WooCommerce if it does not exist, edit otherwise
	 * @since 5.0.0
	 */
	public function import_tag_from_upcp( $term_id ) {

		if ( $this->check_term_loop( $term_id ) ) { return; }

		$upcp_tag = get_term( $term_id, EWD_UPCP_PRODUCT_TAG_TAXONOMY );

		$wc_tag = get_term( get_term_meta( $upcp_tag->term_id, 'woocommerce_id', true ), 'product_tag' );

		if ( empty( $wc_tag ) or is_wp_error( $wc_tag ) ) {

			$args = array(
				'description'	=> $upcp_tag->description
			);

			$wc_tag = wp_insert_term( $upcp_tag->name, 'product_tag', $args );

			if ( ! is_wp_error( $wc_tag ) ) {

				update_term_meta( $upcp_tag->term_id, 'woocommerce_id', $wc_tag['term_id'] );
			}
		}
		else {

			$args = array(
				'name'			=> $upcp_tag->name,
				'description'	=> $upcp_tag->description
			);

			wp_update_term( $wc_tag->term_id, 'product_tag', $args );
		}
	}

	/**
	 * Creates a UPCP product based on a WooCommerce product if it does not exist,
	 * updates the product otherwise
	 * @since 5.0.0
	 */
	public function import_product_into_upcp( $wc_post_id ) {
		global $wpdb;
		global $ewd_upcp_controller;

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

			return $post_id;
		}

		if ( get_post_status( $wc_post_id ) == 'trash' ) { return; }
		
		$wc_product = get_post( $wc_post_id );

		$args = array(
			'post_type'		=> EWD_UPCP_PRODUCT_POST_TYPE,
			'meta_query' 	=> array(
		    	array(
		    		'key' 		=> 'woocommerce_id',
		    		'value' 	=> $wc_product->ID,
		    	)
			)
		);

		$query = new WP_Query( $args );

		$upcp_post_id = empty( $query->posts ) ? false : reset( $query->posts )->ID;

		$args = array(
			'post_content'	=> $wc_product->post_content,
			'post_title'	=> $wc_product->post_title,
			'post_type'		=> EWD_UPCP_PRODUCT_POST_TYPE,
			'post_name'		=> $wc_product->post_name,
			'post_status'	=> 'publish',
			'meta_input'	=> array(
				'price'					=> get_post_meta( $wc_product->ID, '_regular_price', true ),
				'sale_price'			=> get_post_meta( $wc_product->ID, '_sale_price', true ),
				'sale_mode'				=> get_post_meta( $wc_product->ID, '_sale_price', true ) == get_post_meta( $wc_product->ID, '_price', true ) ? true : false,
				'views'					=> 0,
				'display'				=> $wc_product->post_status == 'publish' ? true : false,
				'related_products'		=> array(),
				'order'					=> 9999,
				'woocommerce_id'		=> $wc_product->ID,
				'_yoast_wpseo_metadesc'	=> get_post_meta( $wc_product->ID, '_yoast_wpseo_metadesc', true ),
				'_yoast_wpseo_title'	=> get_post_meta( $wc_product->ID, '_yoast_wpseo_title', true ),
			)
		);

		if ( ! empty( $upcp_post_id ) ) { $args['ID'] = $upcp_post_id; }

		$post_id = wp_insert_post( $args );

		if ( ! $post_id ) { return; }

		set_post_thumbnail( $post_id, get_post_thumbnail_id( $wc_product->ID ) );

		// Categories
		wp_set_post_terms( $post_id, null, EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY );

		$wc_categories = wp_get_post_terms( $wc_product->ID, 'product_cat' );

		foreach( $wc_categories as $wc_category ) {

			$args = array(
				'hide_empty'	=> false,
				'meta_query'	=> array(
					array(
						'key'		=> 'woocommerce_id',
						'value'		=> $wc_category->term_id,
						'compare'	=> 'LIKE'
					)
				),
				'taxonomy'		=> EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY
			);

			$upcp_categories = get_terms( $args );

			if ( ! empty( $upcp_categories ) ) { wp_set_post_terms( $post_id, intval( $upcp_categories[0]->term_id ), EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY, true ); }
		}

		// Tags
		wp_set_post_terms( $post_id, null, EWD_UPCP_PRODUCT_TAG_TAXONOMY );

		$wc_tags = wp_get_post_terms( $wc_product->ID, 'product_tag' );

		foreach( $wc_tags as $wc_tag ) {

			$args = array(
				'hide_empty'	=> false,
				'meta_query'	=> array(
					array(
						'key'		=> 'woocommerce_id',
						'value'		=> $wc_tag->term_id,
						'compare'	=> 'LIKE'
					)
				),
				'taxonomy'		=> EWD_UPCP_PRODUCT_TAG_TAXONOMY
			);

			$upcp_tags = get_terms( $args );

			if ( ! empty( $upcp_tags) ) { wp_set_post_terms( $post_id, intval( $upcp_tags[0]->term_id ), EWD_UPCP_PRODUCT_TAG_TAXONOMY, true ); }
		}

		// Images
		$wc_image_ids = explode( ',', get_post_meta( $wc_product->ID, '_product_image_gallery', true ) );

		$images = (array) get_post_meta( $post_id, 'product_images', true );

		foreach ( $wc_image_ids as $wc_image_id ) {

			$wc_image_url = wp_get_attachment_url( $wc_image_id );

			foreach ( $images as $image ) {

				if ( ! is_object( $image ) ) { continue; }

				if ( $image->url == $wc_image_url ) { continue 2; }
			}

			$images[] = (object) array(
				'url'			=> $wc_image_url,
				'description'	=> get_the_title( $wc_image_id ),
			);
		}

		update_post_meta( $post_id, 'product_images', $images );

		// Custom Fields
		$custom_fields = $ewd_upcp_controller->settings->get_custom_fields(); 

		$wc_attribute_table_name = $wpdb->prefix . 'woocommerce_attribute_taxonomies';

		$attributes = $wpdb->get_results( "SELECT * FROM $wc_attribute_table_name" );

		foreach ( $attributes as $attribute ) {

			$terms = wp_get_object_terms( $wc_product->ID, 'pa_' . $attribute->attribute_name );
			
			if ( ! is_array( $terms ) ) { continue; }

			$term_string = '';

			foreach ( $terms as $term ) {

				$term_string .= $term->name . ',';
			}
			
			foreach ( $custom_fields as $custom_field ) {

				if ( $custom_field->woocommerce_id != $attribute->attribute_id ) { continue; }

				update_post_meta( $post_id, 'custom_field_' . $custom_field->id, trim( $term_string, ',' ) );
			}
		}
	}

	/**
	 * Creates a WooCommerce product based on a UPCP product if it does not exist,
	 * updates the product otherwise
	 * @since 5.0.0
	 */
	public function import_product_from_upcp( $post_id ) {
		global $wpdb;
		global $ewd_upcp_controller;

		if ( get_post_status( $post_id ) == 'trash' ) { return; }
		
		$upcp_product = new ewdupcpProduct();

		$upcp_product->load_post( $post_id );

		$args = array(
			'post_type'		=> 'product',
			'post_name'		=> $upcp_product->slug,
			'post_title'	=> $upcp_product->name,
			'post_content'	=> $upcp_product->description,
		);

		$args['post_status'] = empty( $upcp_product->display ) ? 'draft' : 'publish';

		if ( ! empty( $upcp_product->woocommerce_id ) and get_post( $upcp_product->woocommerce_id ) ) { $args['ID'] = $upcp_product->woocommerce_id; }

		$wc_post_id = wp_insert_post( $args );

		if ( ! $wc_post_id ) { return false; }

		update_post_meta( $wc_post_id, '_regular_price', preg_replace( "/[^0-9.]/", '', $upcp_product->regular_price ) );
		update_post_meta( $wc_post_id, '_sale_price', preg_replace( "/[^0-9.]/", '', $upcp_product->sale_price ) );
		update_post_meta( $wc_post_id, '_price', preg_replace( "/[^0-9.]/", '', $upcp_product->current_price ) );
		update_post_meta( $wc_post_id, '_visibility', 'visible' );
		update_post_meta( $wc_post_id, '_stock_status', 'instock' );
		update_post_meta( $wc_post_id, '_downloadable', 'no' );
		update_post_meta( $wc_post_id, '_virtual', 'no' );

		$thumbnail_id = get_post_thumbnail_id( $upcp_product->ID );

		if ( $thumbnail_id ) {

			set_post_thumbnail( $wc_post_id, $thumbnail_id );
		}

		if ( empty( $upcp_product->woocommerce_id ) ) { update_post_meta( $upcp_product->id, 'woocommerce_id', $wc_post_id ); }
		
		// Categories
		wp_set_post_terms( $wc_post_id, null, 'product_cat' );

		foreach ( $upcp_product->categories as $upcp_category ) { 

			wp_set_post_terms( $wc_post_id, intval( get_term_meta( $upcp_category->term_id, 'woocommerce_id', true ) ), 'product_cat', true );
		}

		foreach ( $upcp_product->subcategories as $upcp_subcategory ) { 

			wp_set_post_terms( $wc_post_id, intval( get_term_meta( $upcp_subcategory->term_id, 'woocommerce_id', true ) ), 'product_cat', true );
		}

		// Tags
		wp_set_post_terms( $wc_post_id, null, 'product_tag' );

		foreach ( $upcp_product->tags as $upcp_tag ) { 

			wp_set_post_terms( $wc_post_id, intval( get_term_meta( $upcp_tag->term_id, 'woocommerce_id', true ) ), 'product_tag', true );
		}

		// Additional Images
		$images = $upcp_product->get_all_images();

		$wc_image_post_ids = array();

		foreach ( $images as $image ) {

			$thumbnail_id = attachment_url_to_postid( $image->url );

			if ( $thumbnail_id ) { $wc_image_post_ids[] = $thumbnail_id; }
		}

		update_post_meta( $wc_post_id, '_product_image_gallery', implode( ',', $wc_image_post_ids ) );

		// Custom Fields
		$custom_fields = $ewd_upcp_controller->settings->get_custom_fields();

		foreach ( $custom_fields as $custom_field ) {

			wp_set_object_terms( $wc_post_id, null, 'pa_' . $custom_field->slug );

			$field_value = get_post_meta( $upcp_product->id, 'custom_field_' . $custom_field->id, true );

			if ( empty( $field_value ) ) { continue; }

			$values = explode( ',', $field_value );

			foreach ( $values as $value ) {

				$wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->terms (name, slug) VALUES (%s, %s)", $value, sanitize_title_with_dashes( $value ) ) );

				$args = array(
					'taxonomy'		=> 'pa_' . $custom_field->slug,
					'hide_empty'	=> false,
					'name'			=> $field_value
				);

				$terms = get_terms( $args );

				if ( empty( $terms ) or is_wp_error( $terms ) ) { continue; }

				wp_set_object_terms( $wc_post_id, $terms[0]->term_id, 'pa_' . $custom_field->slug, true );

				$current_attributes = is_array( get_post_meta( $wc_post_id, '_product_attributes', true ) ) ? get_post_meta( $wc_post_id, '_product_attributes', true ) : array();

				$current_attributes[ 'pa_' . $custom_field->slug ] = array(
					'name' => 'pa_' . $custom_field->slug,
					'value' => $value,
					'is_visible' => '1',
					'is_variation' => '0',
					'is_taxonomy' => '1'
				);

				update_post_meta( $wc_post_id, '_product_attributes', $current_attributes );
			}
		}
	}

	/**
	 * Returns the parent UPCP term_id for a given WooCommerce category
	 * @since 5.0.0
	 */
	public function get_upcp_category_parent_from_wc_category( $wc_category ) {

		if ( empty( $wc_category->parent ) ) { return 0; }

		$args = array(
			'hide_empty'	=> false,
			'meta_query'	=> array(
				array(
					'key'		=> 'woocommerce_id',
					'value'		=> $wc_category->parent,
					'compare'	=> 'LIKE'
				)
			),
			'taxonomy'		=> EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY
		);

		$upcp_categories = get_terms( $args );

		if ( empty( $upcp_categories ) ) { return 0; }

		return $upcp_categories[0]->term_id;
	}

	/**
	 * Returns the parent UPCP term_id for a given WooCommerce category
	 * @since 5.0.0
	 */
	public function get_wc_category_parent_from_upcp_category( $upcp_category ) {

		if ( empty( $upcp_category->parent ) ) { return 0; }

		return get_term_meta( $upcp_category->parent, 'woocommerce_id', true );
	}

	/**
	 * Checks the term currently being edited. Returns true if it has been edited already.
	 * @since 5.0.0
	 */
	public function check_term_loop( $term_id ) {

		if ( in_array( $term_id, $this->edited_terms ) ) {

			return true;
		}

		$this->edited_terms[] = $term_id;

		return false;
	}
}

}