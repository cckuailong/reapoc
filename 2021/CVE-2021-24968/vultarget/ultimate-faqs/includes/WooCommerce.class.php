<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewdufaqWooCommerce' ) ) {
	/**
	 * Class to handle WooCommerce integration for Ultimate FAQs
	 *
	 * @since 2.0.0
	 */
	class ewdufaqWooCommerce {

		public function __construct() {

			add_filter( 'woocommerce_product_tabs', array( $this, 'add_woocommerce_tab' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_admin_product_page_faq_tab' ), 10, 1 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_admin_product_page_faqs' ) );

			add_action( 'wp_ajax_ewd_ufaq_add_wc_faqs', array( $this, 'add_wc_faqs' ) );
			add_action( 'wp_ajax_ewd_ufaq_delete_wc_faqs', array( $this, 'delete_wc_faqs' ) );
			add_action( 'wp_ajax_ewd_ufaq_wc_faq_category', array( $this, 'wc_faq_category' ) );
		}

		/**
		 * Adds an FAQ tab to WooCommerce
		 * @since 2.0.0
		 */
		public function add_woocommerce_tab( $tabs ) {
			global $product;
			global $ewd_ufaq_controller;

			if ( ! $ewd_ufaq_controller->settings->get_setting( 'woocommerce-faqs' ) ) { return $tabs; }

			$product_post = ( $ewd_ufaq_controller->settings->get_setting( 'woocommerce-use-product' ) and is_object( $product ) ) ? get_post( $product->get_id() ) : get_post( get_the_id() );
		
			$ufaq_product_category = get_term_by( 'name', $product_post->post_title, EWD_UFAQ_FAQ_CATEGORY_TAXONOMY );
		
			$wc_cats = get_the_terms( $product_post, 'product_cat' );

			$ufaq_wccat_category = false;

			if ( $wc_cats ) {

				foreach ( $wc_cats as $wc_cat ) {

					if ( get_term_by( 'name', $wc_cat->name, EWD_UFAQ_FAQ_CATEGORY_TAXONOMY ) ) { $ufaq_wccat_category = true; }
				}
			}
		
			$current_faqs = is_array( get_post_meta( $product_post->ID, 'EWD_UFAQ_WC_Selected_FAQs', true ) ) ? get_post_meta( $product_post->ID, 'EWD_UFAQ_WC_Selected_FAQs', true ) : array();
		
			if ( ( $ufaq_product_category or $ufaq_wccat_category or ! empty( $current_faqs ) ) ) {

				$tabs['faq_tab'] = array(
					'title' 	=> $ewd_ufaq_controller->settings->get_setting( 'label-woocommerce-tab' ),
					'priority' 	=> 50,
					'callback' 	=> array( $this, 'faq_tab_content' )
				);
		
				return $tabs;
			}
		}

		public function admin_enqueue( $hook ) {
			global $post;
			global $ewd_ufaq_controller;

			if ( ! $ewd_ufaq_controller->settings->get_setting( 'woocommerce-faqs' ) ) { return; }

			if ( $hook != 'edit.php' and $hook != 'post-new.php' and $hook != 'post.php' ) { return; }

    		if ( ! isset( $post->post_type ) or $post->post_type != 'product' ) { return; }

    		wp_enqueue_style( 'ewd-ufaq-wc-admin-css', EWD_UFAQ_PLUGIN_URL . '/assets/css/ewd-ufaq-wc-admin.css', array(), EWD_UFAQ_VERSION );

    		wp_enqueue_script( 'ewd-ufaq-wc-admin-js', EWD_UFAQ_PLUGIN_URL . '/assets/js/ewd-ufaq-wc-admin.js', array( 'jquery' ), EWD_UFAQ_VERSION );
		}

		public function faq_tab_content() {
			global $product;
			global $ewd_ufaq_controller;
		
			$product_post = ( $ewd_ufaq_controller->settings->get_setting( 'woocommerce-use-product' ) and is_object( $product ) ) ? get_post( $product->get_id() ) : get_post( get_the_id() );
			
			$ufaq_product_category = get_term_by( 'name', $product_post->post_title, EWD_UFAQ_FAQ_CATEGORY_TAXONOMY );
		
			echo '<h2>' . esc_html( $ewd_ufaq_controller->settings->get_setting( 'label-woocommerce-tab' ) ) . '</h2>';
		
			$current_faqs = is_array( get_post_meta( $product_post->ID, 'EWD_UFAQ_WC_Selected_FAQs', true ) ) ? get_post_meta( $product_post->ID, 'EWD_UFAQ_WC_Selected_FAQs', true ) : array();
		
			if ( ! empty( $current_faqs ) ) {

				$faq_list = implode( ',', $current_faqs );
				echo do_shortcode( "[ultimate-faqs post__in_string='". $faq_list . "']" );
			}
			else {

				$wc_cats = get_the_terms( $product_post, 'product_cat' );
				$ufaq_wc_category_list = '';

				if ( $wc_cats ) {

					foreach ( $wc_cats as $wc_cat ) {

						$ufaq_wc_category = get_term_by( 'name', $wc_cat->name, EWD_UFAQ_FAQ_CATEGORY_TAXONOMY );
						if ( $ufaq_wc_category ) { $ufaq_wc_category_list .= ',' . $ufaq_wc_category->slug; }
					}
				}
				echo do_shortcode( "[ultimate-faqs include_category='". $ufaq_product_category->slug . $ufaq_wc_category_list . "']" );
			}
		}

		public function add_admin_product_page_faq_tab( $tabs ) {
			global $ewd_ufaq_controller;

			if ( ! $ewd_ufaq_controller->settings->get_setting( 'woocommerce-faqs' ) ) { return $tabs; }
			
			$args = array(
				'label' 	=> __('FAQs', 'ultimate-faqs'),
				'target' 	=> 'ewd_ufaq_faqs',
				'class' 	=> array()
			);
		
			$tabs['faqs'] = $args;
		
			return $tabs;
		}

		public function add_admin_product_page_faqs() {
			global $thepostid;
			global $ewd_ufaq_controller;

			if ( ! $ewd_ufaq_controller->settings->get_setting( 'woocommerce-faqs' ) ) { return $tabs; }
		
			$current_faqs = (array) get_post_meta( $thepostid, 'EWD_UFAQ_WC_Selected_FAQs', true );
		
			$all_faqs = get_posts( array( 'numberposts' => -1, 'post_type' => EWD_UFAQ_FAQ_POST_TYPE ) );
			$categories = get_terms( array( 'taxonomy' => EWD_UFAQ_FAQ_CATEGORY_TAXONOMY ) );

			?>
		
			<div id='ewd_ufaq_faqs' class='panel woocommerce_options_panel'>
		
				<div class='ewd-ufaq-explanation'>
					<?php _e( 'You can use the form below to select which FAQs to display for this product, or leave it blank to use the default category naming system.', 'ultimate-faqs' ); ?>
				</div>
		
				<div id='ewd-ufaq-add-delete-faq-form-container'>

					<div id='ewd-ufaq-add-faq-form-div'>
						
						<form id='ewd-ufaq-add-faq-form'>

							<select class='ewd-ufaq-category-filter' name='ewd-ufaq-category-filter'>
								<option value=''><?php _e('All Categories', 'ultimate-faqs' ); ?></option>
								<?php foreach ( $categories as $category ) { ?> <option value='<?php echo $category->term_id; ?>'><?php echo esc_html( $category->name ); ?></option><?php } ?>
							</select>

							<table class='form-table ewd-ufaq-faq-add-table'>

								<tr>
									<th><?php _e( 'Add?', 'ultimate-faqs' ); ?></th>
									<th><?php _e( 'FAQ', 'ultimate-faqs' ); ?></th>
								</tr>

								<?php foreach ( $all_faqs as $faq ) { ?>

									<tr class='ewd-ufaq-faq-row' data-faqid='<?php echo $faq->ID; ?>'>
										<td><input type='checkbox' class='ewd-ufaq-add-faq' name='Add_FAQs[]' value='<?php echo $faq->ID; ?>'/></td>
										<td><?php echo esc_html( $faq->post_title ); ?></td>
									</tr>
								<?php } ?>

							</table>
						</form>

						<button class='ewd-ufaq-add-faq-button'><?php _e( 'Add FAQs', 'ultimate-faqs' ); ?></button>
					</div>
		
					<div id='ewd-ufaq-delete-faq-form-div'>
						
						<form id='ewd-ufaq-delete-faq-form'>
							
							<input type='hidden' id='ewd-ufaq-post-id' value='<?php echo esc_attr( $thepostid ); ?>' />
							
							<table class='form-table ewd-ufaq-delete-table'>
								
								<tr>
									<th><?php _e( 'Delete?', 'ultimate-faqs' ); ?></th>
									<th><?php _e( 'FAQ', 'ultimate-faqs' ); ?></th>
								</tr>

								<?php foreach ( $current_faqs as $faq_id ) { ?>

									<?php $faq = get_post( $faq_id ); ?>
									<tr class='ewd-ufaq-faq-row ewd-ufaq-delete-faq-row' data-faqid='<?php echo $faq_id; ?>'>
										<td><input type='checkbox' class='ewd-ufaq-delete-faq' name='Delete_FAQs[]' value='<?php echo $faq_id; ?>'/></td>
										<td><?php echo esc_html( $faq->post_title ); ?></td>
									</tr>
								<?php } ?>

							</table>
						</form>
						
						<button class='ewd-ufaq-delete-faq-button'><?php _e( 'Delete FAQs', 'ultimate-faqs' ); ?></button>
					</div>
				</div>
			</div>

		<?php
		
		}

		public function add_wc_faqs() {

			$post_id = intval( $_POST['Post_ID'] );
		
		    $current_faqs = (array) get_post_meta( $post_id, 'EWD_UFAQ_WC_Selected_FAQs', true );
		
		    $faqs = json_decode( stripslashes_deep( $_POST['FAQs'] ) );
		    if ( ! is_array( $faqs ) ) { $faqs = array(); }
		
		    $added_faqs = array();
		    foreach ( $faqs as $faq ) {

		        if ( in_array( $faq, $current_faqs ) ) { continue; }

		        $current_faqs[] = $faq;
		
		        $faq_post = get_post( $faq );
		        $added_faqs[] = array( 'ID' => $faq, 'Name' => $faq_post->post_title);
		    }
		
		    update_post_meta( $post_id, 'EWD_UFAQ_WC_Selected_FAQs', $current_faqs );
		
		    echo json_encode( $added_faqs );
		
		    die();
		}

		public function delete_wc_faqs() {

			$post_id = intval( $_POST['Post_ID'] );
		
		    $current_faqs = (array) get_post_meta( $post_id, 'EWD_UFAQ_WC_Selected_FAQs', true );

		    $faqs = json_decode( stripslashes_deep( $_POST['FAQs'] ) );
		    if ( ! is_array( $faqs ) ) { $faqs = array(); }

		    $remaining_faqs = array_diff( $current_faqs, $faqs );

		    update_post_meta( $post_id, 'EWD_UFAQ_WC_Selected_FAQs', $remaining_faqs );

		    die();
		}

		public function wc_faq_category() {

			$cat_id = intval( $_POST['Cat_ID'] );
    
		    $args = array(
		    	'numberposts' 	=> -1, 
		    	'post_type' 	=> EWD_UFAQ_FAQ_POST_TYPE,
		    );

		    if ( $cat_id != '' ) {

		        $args['tax_query'] = array(
		        	array(
		            	'taxonomy' => EWD_UFAQ_FAQ_CATEGORY_TAXONOMY,
		            	'terms' => $cat_id
		            )
		        );
		    }

		    $all_faqs = get_posts( $args );

		    ob_start();

		    ?>
		
		    <table class='form-table ewd-ufaq-faq-add-table'>
		    	<tr>
		    		<th><?php _e( 'Add?', 'ultimate-faqs' ); ?></th>
		    		<th><?php _e( 'FAQ', 'ultimate-faqs' ); ?></th>
		    	</tr>
		    	<?php foreach ( $all_faqs as $faq ) { ?>

		        	<tr class='ewd-ufaq-faq-row' data-faqid='<?php echo $faq->ID; ?>'>
		        		<td><input type='checkbox' class='ewd-ufaq-add-faq' name='Add_FAQs[]' value='<?php echo $faq->ID; ?>'/></td>
		        		<td><?php echo esc_html( $faq->post_title ); ?></td>
		        	</tr>
		   		<?php } ?>
		    </table>

		    <?php

		    $output = ob_get_clean();
		    
		    echo $output;
		
		    die();
		}
	}
}