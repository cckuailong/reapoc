<?php

/**
 * Class to handle everything related to the walk-through that runs on plugin activation
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

class ewdufaqInstallationWalkthrough {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_install_screen' ) );
		add_action( 'admin_head', array( $this, 'hide_install_screen_menu_item' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 9999 );

		add_action( 'admin_head', array( $this, 'admin_enqueue' ) );

		add_action( 'wp_ajax_ewd_ufaq_welcome_add_category', array( $this, 'create_category' ) );
		add_action( 'wp_ajax_ewd_ufaq_welcome_add_faq', array( $this, 'create_faq' ) );
		add_action( 'wp_ajax_ewd_ufaq_welcome_add_faq_page', array( $this, 'add_faqs_page' ) );
		add_action( 'wp_ajax_ewd_ufaq_welcome_set_options', array( $this, 'set_options' ) );
	}

	/**
	 * On activation, redirect the user if they haven't used the plugin before
	 * @since 2.0.0
	 */
	public function redirect() {
		if ( ! get_transient( 'ewd-ufaq-getting-started' ) ) 
			return;

		delete_transient( 'ewd-ufaq-getting-started' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		if ( ! empty( get_posts( array( 'post_type' => EWD_UFAQ_FAQ_POST_TYPE ) ) ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'index.php?page=ewd-ufaq-getting-started' ) );
		exit;
	}

	/**
	 * Create the installation admin page
	 * @since 2.0.0
	 */
	public function register_install_screen() {

		add_dashboard_page(
			esc_html__( 'Ultimate FAQs - Welcome!', 'ultimate-faqs' ),
			esc_html__( 'Ultimate FAQs - Welcome!', 'ultimate-faqs' ),
			'manage_options',
			'ewd-ufaq-getting-started',
			array($this, 'display_install_screen')
		);
	}

	/**
	 * Hide the installation admin page from the WordPress sidebar menu
	 * @since 2.0.0
	 */
	public function hide_install_screen_menu_item() {

		remove_submenu_page( 'index.php', 'ewd-ufaq-getting-started' );
	}

	/**
	 * Create an FAQ category
	 * @since 2.0.0
	 */
	public function create_category() {

		$category_name = isset( $_POST['category_name'] ) ? sanitize_text_field( $_POST['category_name'] ) : '';
    	$category_description = isset( $_POST['category_description'] ) ? sanitize_textarea_field( $_POST['category_description'] ) : '';

    	$category_term_id = wp_insert_term( $category_name, EWD_UFAQ_FAQ_CATEGORY_TAXONOMY, array('description' => $category_description) );

    	echo json_encode ( array( 'category_name' => $category_name, 'category_id' => $category_term_id['term_id'] ) );

    	exit();
	}

	public function create_faq() {

		$args = array(
	        'post_title' => isset( $_POST['faq_question'] ) ? sanitize_text_field( $_POST['faq_question'] ) : '',
	        'post_content' => isset( $_POST['faq_answer'] ) ? sanitize_text_field( $_POST['faq_answer'] ) : '',
	        'post_status' => 'publish',
	        'post_type' => EWD_UFAQ_FAQ_POST_TYPE
	    );

	    $faq_post_id = wp_insert_post( $args );	
	
	    if ( isset( $_POST['faq_category'] ) and $_POST['faq_category'] ) {

	        wp_set_post_terms( $faq_post_id, sanitize_text_field( $_POST['faq_category'] ), EWD_UFAQ_FAQ_CATEGORY_TAXONOMY );
	    }
	
	    exit();
	}

	/**
	 * Add in a page with the ultimate-faqs shortcode
	 * @since 2.0.0
	 */
	public function add_faqs_page() {

		$args = array(
    	    'post_title' => isset($_POST['faq_page_title'] ) ? sanitize_text_field( $_POST['faq_page_title'] ) : '',
        	'post_content' => '<!-- wp:paragraph --><p> [ultimate-faqs] </p><!-- /wp:paragraph -->',
    	    'post_status' => 'publish',
    	    'post_type' => 'page'
    	);

    	wp_insert_post( $args );
	
	    exit();
	}

	/**
	 * Set a number of key options selected during the walk-through process
	 * @since 2.0.0
	 */
	public function set_options() {

		$ewd_ufaq_options = get_option( 'ewd-ufaq-settings' );

		if ( isset( $_POST['faq_accordion'] ) ) { $ewd_ufaq_options['faq-accordion'] = intval( $_POST['faq_accordion'] ); }
		if ( isset( $_POST['disable_faq_toggle'] ) ) { $ewd_ufaq_options['disable-faq-toggle'] = intval( $_POST['disable_faq_toggle'] ); }
		if ( isset( $_POST['group_by_category'] ) ) { $ewd_ufaq_options['group-by-category'] = intval( $_POST['group_by_category'] ); }
		if ( isset( $_POST['order_by_setting'] ) ) { $ewd_ufaq_options['faq-order-by'] = sanitize_text_field( $_POST['order_by_setting'] ); }

		update_option( 'ewd-ufaq-settings', $ewd_ufaq_options );
	
	    exit();
	}

	/**
	 * Enqueue the admin assets necessary to run the walk-through and display it nicely
	 * @since 2.0.0
	 */
	public function admin_enqueue() {

		if ( ! isset( $_GET['page'] ) or $_GET['page'] != 'ewd-ufaq-getting-started' ) { return; }

		wp_enqueue_style( 'ewd-ufaq-admin-css', EWD_UFAQ_PLUGIN_URL . '/assets/css/admin.css', array(), EWD_UFAQ_VERSION );
		wp_enqueue_style( 'ewd-ufaq-sap-admin-css', EWD_UFAQ_PLUGIN_URL . '/lib/simple-admin-pages/css/admin.css', array(), EWD_UFAQ_VERSION );
		wp_enqueue_style( 'ewd-ufaq-welcome-screen', EWD_UFAQ_PLUGIN_URL . '/assets/css/ewd-ufaq-welcome-screen.css', array(), EWD_UFAQ_VERSION );
		wp_enqueue_style( 'ewd-ufaq-admin-settings-css', EWD_UFAQ_PLUGIN_URL . '/lib/simple-admin-pages/css/admin-settings.css', array(), EWD_UFAQ_VERSION );
		
		wp_enqueue_script( 'ewd-ufaq-getting-started', EWD_UFAQ_PLUGIN_URL . '/assets/js/ewd-ufaq-welcome-screen.js', array( 'jquery' ), EWD_UFAQ_VERSION );
		wp_enqueue_script( 'ewd-ufaq-admin-settings-js', EWD_UFAQ_PLUGIN_URL . '/lib/simple-admin-pages/js/admin-settings.js', array( 'jquery' ), EWD_UFAQ_VERSION );
		wp_enqueue_script( 'ewd-ufaq-admin-spectrum-js', EWD_UFAQ_PLUGIN_URL . '/lib/simple-admin-pages/js/spectrum.js', array( 'jquery' ), EWD_UFAQ_VERSION );
	}

	/**
	 * Output the HTML of the walk-through screen
	 * @since 2.0.0
	 */
	public function display_install_screen() { 
		global $ewd_ufaq_controller;

		$faq_accordion = $ewd_ufaq_controller->settings->get_setting( 'faq-accordion' );
		$disable_faq_toggle = $ewd_ufaq_controller->settings->get_setting( 'disable-faq-toggle' );
		$group_by_category = $ewd_ufaq_controller->settings->get_setting( 'group-by-category' );
		$faq_order_by = $ewd_ufaq_controller->settings->get_setting( 'faq-order-by' );

		?>

		<div class='ewd-ufaq-welcome-screen'>
			
			<div class='ewd-ufaq-welcome-screen-header'>
				<h1><?php _e('Welcome to Ultimate FAQs', 'ultimate-faqs'); ?></h1>
				<p><?php _e('Thanks for choosing Ultimate FAQs! The following will help you get started with the setup by creating your first FAQ and category, as well as adding your FAQs to a page and configuring a few key options.', 'ultimate-faqs'); ?></p>
			</div>

			<div class='ewd-ufaq-welcome-screen-box ewd-ufaq-welcome-screen-categories ewd-ufaq-welcome-screen-open' data-screen='categories'>
				<h2><?php _e('1. Create Categories', 'ultimate-faqs'); ?></h2>
				<div class='ewd-ufaq-welcome-screen-box-content'>
					<p><?php _e('Categories let you organize your FAQs in a way that\'s easy for you - and your customers - to find.', 'ultimate-faqs'); ?></p>
					<table class='form-table ewd-ufaq-welcome-screen-created-categories'>
						<tr class='ewd-ufaq-welcome-screen-add-category-name ewd-ufaq-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Category Name', 'ultimate-faqs' ); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<input type='text'>
							</td>
						</tr>
						<tr class='ewd-ufaq-welcome-screen-add-category-description ewd-ufaq-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Category Description', 'ultimate-faqs' ); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<textarea></textarea>
							</td>
						</tr>
						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-ufaq-welcome-screen-add-category-button'><?php _e('Add Category', 'ultimate-faqs'); ?></div>
							</td>
						</tr>
						<tr></tr>
						<tr>
							<td colspan="2">
								<h3><?php _e('Created Categories', 'ultimate-faqs'); ?></h3>
								<table class='ewd-ufaq-welcome-screen-show-created-categories'>
									<tr>
										<th class='ewd-ufaq-welcome-screen-show-created-categories-name'><?php _e('Name', 'ultimate-faqs'); ?></th>
										<th class='ewd-ufaq-welcome-screen-show-created-categories-description'><?php _e('Description', 'ultimate-faqs'); ?></th>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					
					<div class='ewd-ufaq-welcome-screen-next-button' data-nextaction='questions'><?php _e('Next', 'ultimate-faqs'); ?></div>
					<div class='clear'></div>
				</div>
			</div>

			<div class='ewd-ufaq-welcome-screen-box ewd-ufaq-welcome-screen-questions' data-screen='questions'>
				<h2><?php _e('2. Questions & Answers', 'ultimate-faqs'); ?></h2>
				<div class='ewd-ufaq-welcome-screen-box-content'>
					<p><?php _e('Create your first set of questions and answers. Don\'t worry, you can always add more later.', 'ultimate-faqs'); ?></p>
					<table class='form-table ewd-ufaq-welcome-screen-created-categories'>
						<tr class='ewd-ufaq-welcome-screen-add-faq-question ewd-ufaq-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Questions', 'ultimate-faqs' ); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<input type='text'>
							</td>
						</tr>
						<tr class='ewd-ufaq-welcome-screen-add-faq-answer ewd-ufaq-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Answer', 'ultimate-faqs' ); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<textarea></textarea>
							</td>
						</tr>
						<tr class='ewd-ufaq-welcome-screen-add-faq-category ewd-ufaq-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'FAQ Category', 'ultimate-faqs' ); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<select>
									<option></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-ufaq-welcome-screen-add-faq-button'><?php _e('Add FAQ', 'ultimate-faqs'); ?></div>
							</td>
						</tr>
						<tr></tr>
						<tr>
							<td colspan="2">
								<h3><?php _e('Created FAQs', 'ultimate-faqs'); ?></h3>
								<table class='ewd-ufaq-welcome-screen-show-created-faqs'>
									<tr>
										<th class='ewd-ufaq-welcome-screen-show-created-faq-question'><?php _e('Question', 'ultimate-faqs'); ?></th>
										<th class='ewd-ufaq-welcome-screen-show-created-faq-answer'><?php _e('Answer', 'ultimate-faqs'); ?></th>
										<th class='ewd-ufaq-welcome-screen-show-created-faq-category'><?php _e('Category', 'ultimate-faqs'); ?></th>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					<div class='ewd-ufaq-welcome-clear'></div>
					<div class='ewd-ufaq-welcome-screen-next-button' data-nextaction='faq-page'><?php _e('Next', 'ultimate-faqs'); ?></div>
					<div class='ewd-ufaq-welcome-screen-previous-button' data-previousaction='questions'><?php _e('Previous', 'ultimate-faqs'); ?></div>
					<div class='ewd-ufaq-clear'></div>
				</div>
			</div>

			<div class='ewd-ufaq-welcome-screen-box ewd-ufaq-welcome-screen-faq-page' data-screen='faq-page'>
				<h2><?php _e('3. Add an FAQ Page', 'ultimate-faqs'); ?></h2>
				<div class='ewd-ufaq-welcome-screen-box-content'>
					<p><?php _e('You can create a dedicated FAQ page below, or skip this step and add your FAQs to a page you\'ve already created manually.', 'ultimate-faqs'); ?></p>
					<table class='form-table ewd-ufaq-welcome-screen-faq-page'>
						<tr class='ewd-ufaq-welcome-screen-add-faq-page-name ewd-ufaq-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Page Title', 'ultimate-faqs' ); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<input type='text' value='FAQs'>
							</td>
						</tr>
						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-ufaq-welcome-screen-add-faq-page-button' data-nextaction='options'><?php _e( 'Create Page', 'ultimate-faqs' ); ?></div>
							</td>
						</tr>
					</table>

					<div class='ewd-ufaq-welcome-clear'></div>
					<div class='ewd-ufaq-welcome-screen-next-button' data-nextaction='options'><?php _e('Next', 'ultimate-faqs'); ?></div>
					<div class='ewd-ufaq-welcome-screen-previous-button' data-previousaction='questions'><?php _e('Previous', 'ultimate-faqs'); ?></div>
					<div class='ewd-ufaq-clear'></div>
				</div>
			</div>

			<div class='ewd-ufaq-welcome-screen-box ewd-ufaq-welcome-screen-options' data-screen='options'>
				<h2><?php _e('4. Set Key Options', 'ultimate-faqs'); ?></h2>
				<div class='ewd-ufaq-welcome-screen-box-content'>
					<p><?php _e('Options can always be changed later, but here are a few tha a lot of users want to set for themselves.', 'ultimate-faqs'); ?></p>
					<table class='form-table'>
						<tr>
							<th scope='row'><?php _e('FAQ Accordion', 'ultimate-faqs'); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<fieldset>
									<div class='sap-admin-hide-radios'>
										<input type='checkbox' name='faq_accordion' value='1'>
									</div>
									<label class='sap-admin-switch'>
										<input type='checkbox' class='sap-admin-option-toggle' data-inputname='faq_accordion' <?php if ( $faq_accordion == '1' ) { echo 'checked'; } ?>>
										<span class='sap-admin-switch-slider round'></span>
									</label>		
									<p class='description'><?php _e('Should the FAQs accordion? (Only one FAQ is open at a time, requires FAQ Toggle)', 'ultimate-faqs'); ?></p>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope='row'><?php _e('FAQ Toggle', 'ultimate-faqs'); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<fieldset>
									<div class='sap-admin-hide-radios'>
										<input type='checkbox' name='disable_faq_toggle' value='1'>
									</div>
									<label class='sap-admin-switch'>
										<input type='checkbox' class='sap-admin-option-toggle' data-inputname='disable_faq_toggle' <?php if ( $disable_faq_toggle == '1' ) { echo 'checked'; } ?>>
										<span class='sap-admin-switch-slider round'></span>
									</label>		
									<p class='description'><?php _e('Should the FAQs open on a separate page when clicked, instead of opening and closing?', 'ultimate-faqs'); ?></p>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope='row'><?php _e('Group FAQs by Category', 'ultimate-faqs'); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<fieldset>
									<div class='sap-admin-hide-radios'>
										<input type='checkbox' name='group_by_category' value='1'>
									</div>
									<label class='sap-admin-switch'>
										<input type='checkbox' class='sap-admin-option-toggle' data-inputname='group_by_category' <?php if ( $group_by_category == '1' ) { echo 'checked'; } ?>>
										<span class='sap-admin-switch-slider round'></span>
									</label>		
									<p class='description'><?php _e('Should FAQs be grouped by category, or should all categories be mixed together?', 'ultimate-faqs'); ?></p>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope='row'><?php _e('FAQ Ordering', 'ultimate-faqs'); ?></th>
							<td class='ewd-ufaq-welcome-screen-option'>
								<fieldset>
									<select name='order_by_setting'>
										<option value='date' <?php if ( $faq_order_by == 'date' ) { echo 'selected';} ?> ><?php _e( 'Created Date', 'ultimate-faqs' ); ?></option>
										<option value='title' <?php if ( $faq_order_by == 'title' ) { echo 'selected';} ?> ><?php _e( 'Title', 'ultimate-faqs' ); ?></option>
					  					<option value='modified' <?php if ( $faq_order_by == 'modified' ) { echo 'selected';} ?> ><?php _e( 'Modified Date', 'ultimate-faqs' ); ?></option>
									</select>
									<p class='description'><?php _e('How should individual FAQs be ordered?', 'ultimate-faqs'); ?></p>
								</fieldset>
							</td>
						</tr>
					</table>
		
					<div class='ewd-ufaq-welcome-screen-save-options-button'><?php _e('Save Options', 'ultimate-faqs'); ?></div>
					<div class='ewd-ufaq-welcome-clear'></div>
					<div class='ewd-ufaq-welcome-screen-previous-button' data-previousaction='faq-page'><?php _e('Previous', 'ultimate-faqs'); ?></div>
					<div class='ewd-ufaq-welcome-screen-finish-button'><a href='admin.php?page=ewd-ufaq-settings'><?php _e('Finish', 'ultimate-faqs'); ?></a></div>
					
					<div class='ewd-ufaq-clear'></div>
				</div>
			</div>

			<div class='ewd-ufaq-welcome-screen-skip-container'>
				<a href='admin.php?page=ewd-ufaq-settings'><div class='ewd-ufaq-welcome-screen-skip-button'><?php _e('Skip Setup', 'ultimate-faqs'); ?></div></a>
			</div>
		</div>

	<?php }
}


?>