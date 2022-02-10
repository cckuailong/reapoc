<?php
/**
 * Get all Paid Memberships Pro pages.
 *
 * @since 2.3
 * @return array
 */
function pmpro_get_pmpro_pages() {
	$pmpro_pages = array(
		'account' => intval( pmpro_getOption( 'account_page_id' ) ),
		'billing' => intval( pmpro_getOption( 'billing_page_id' ) ),
		'cancel' => intval( pmpro_getOption( 'cancel_page_id' ) ),
		'checkout' => intval( pmpro_getOption( 'checkout_page_id' ) ),
		'confirmation' => intval( pmpro_getOption( 'confirmation_page_id' ) ),
		'invoice' => intval( pmpro_getOption( 'invoice_page_id' ) ),
		'levels' => intval( pmpro_getOption( 'levels_page_id' ) ),
		'member_profile_edit' => intval( pmpro_getOption( 'member_profile_edit_page_id' ) ),
	);

	$pmpro_page_names = array();
	foreach ( $pmpro_pages as $pmpro_page_id => $pmpro_page ) {
		$pmpro_page_names[$pmpro_page_id] = get_the_title( $pmpro_page_id );
	}

	return apply_filters( 'pmpro_get_pmpro_pages', $pmpro_pages, $pmpro_page_names );
}

/**
 * Add Paid Memberships Pro nav menu meta box.
 *
 * @since 2.3
 */
function pmpro_nav_menu_meta_box() {
	add_meta_box( 'add-pmpro-pages', __( 'Paid Memberships Pro', 'paid-memberships-pro' ),'pmpro_pages_metabox_nav_links', 'nav-menus', 'side', 'low' );
}
add_action( 'admin_head-nav-menus.php', 'pmpro_nav_menu_meta_box' );

/**
 * Add links to Paid Memberships Pro nav menu meta box.
 *
 * @since 2.3
 */
function pmpro_pages_metabox_nav_links() {

	global $nav_menu_selected_id;

	// Get all the page settings.
	$pmpro_page_ids = pmpro_get_pmpro_pages();

	// Allow custom plugins to filter the page IDs.
	$pmpro_page_ids = apply_filters( 'pmpro_custom_nav_menu_items', $pmpro_page_ids );

	// Get the page data for these IDs.
	$pmpro_pages = get_pages( array( 'include' => $pmpro_page_ids ) );
	?>
	<div id="pmpro-page-items" class="posttypediv">
		<div class="tabs-panel tabs-panel-active">
			<ul class="categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $pmpro_pages ), 0, (object) array(
					'walker' => new Walker_Nav_Menu_Checklist(),
				) ); ?>

				<?php // Include the custom Log In and Log Out menu items. ?>
				<li>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> <?php _e( 'Log In', 'paid-memberships-pro'); ?>
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-type-name" name="menu-item[-1][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php _e( 'Log In', 'paid-memberships-pro'); ?>">
					<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="#">
					<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="menu-item-type-pmpro-login">
				</li>
				<li>
					<label class="menu-item-title">
						<input type="checkbox" class="menu-item-checkbox" name="menu-item[-2][menu-item-object-id]" value="-2"> <?php _e( 'Log Out', 'paid-memberships-pro'); ?>
					</label>
					<input type="hidden" class="menu-item-type" name="menu-item[-2][menu-item-type]" value="custom">
					<input type="hidden" class="menu-item-title" name="menu-item[-2][menu-item-title]" value="<?php _e( 'Log Out', 'paid-memberships-pro'); ?>">
					<input type="hidden" class="menu-item-url" name="menu-item[-2][menu-item-url]" value="#">
					<input type="hidden" class="menu-item-classes" name="menu-item[-2][menu-item-classes]" value="menu-item-type-pmpro-logout">
				</li>
			</ul>
		</div>
		<p class="button-controls wp-clearfix">
			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-pmpro-page-items" id="submit-pmpro-page-items" />
				<span class="spinner"></span>
			</span>
		</p>
	</div>
<?php
}

/**
 * Register Paid Memberships Pro nav menu item types in Customizer.
 *
 * @since  2.3
 * @param  array $item_types Menu item types.
 * @return array
 */
function pmpro_customize_nav_menu_available_item_types( $item_types ) {
	$item_types[] = array(
		'title'      => __( 'Paid Memberships Pro', 'paid-memberships-pro' ),
		'type_label' => __( 'Paid Memberships Pro Page', 'paid-memberships-pro' ),
		'type'       => 'pmpro_nav',
		'object'     => 'pmpro_pages',
	);
	return $item_types;
}
add_filter( 'customize_nav_menu_available_item_types', 'pmpro_customize_nav_menu_available_item_types' );

/**
 * Register Paid Memberships Pro pages to customize nav menu items.
 *
 * @since  2.3
 * @param  array   $items  List of nav menu items.
 * @param  string  $type   Nav menu type.
 * @param  string  $object Nav menu object.
 * @param  integer $page   Page number.
 * @return array
 */
function pmpro_customize_nav_menu_available_items( $items, $type, $object, $page ) {
	// Only add items to our new item type ('pmpro_pages' object).
	if ( $object !== 'pmpro_pages' ) {
		return $items;
	}

	// Don't allow pagination since all items are loaded at once.
	if ( 0 < $page ) {
		return $items;
	}

	// Get all the page settings.
	$pmpro_page_ids = pmpro_get_pmpro_pages();

	// Allow custom plugins to filter the page IDs.
	$pmpro_page_ids = apply_filters( 'pmpro_custom_nav_menu_items', $pmpro_page_ids );

	// Get the page data for these IDs.
	$pmpro_pages = get_pages( array( 'include' => $pmpro_page_ids ) );

	// Include conditional log in / log out menu item.
	//$pmpro_pages['login-out'] = __( 'Log in/Log Out Conditional', 'paid-memberships-pro' );

	foreach ( $pmpro_pages as $pmpro_page ) {
		$items[] = array(
			'id'         => 'post-' . $pmpro_page->ID,
			'title'      => html_entity_decode( $pmpro_page->post_title, ENT_QUOTES, get_bloginfo( 'charset' ) ),
			'type_label' => get_post_type_object( $pmpro_page->post_type )->labels->singular_name,
			'object'     => $pmpro_page->post_type,
			'object_id'  => intval( $pmpro_page->ID ),
			'url'        => get_permalink( intval( $pmpro_page->ID ) ),
		);
	}

	// Include the custom Log In and Log Out menu items.
	$items[] = array(
		'id'         => 'pmpro-login',
		'title'      => __( 'Log In', 'paid-memberships-pro'),
		'type'       => 'pmpro-login',
		'type_label' => __( 'Page', 'paid-memberships-pro'),
		'object'     => 'page',
		'url'        => '#',
	);

	$items[] = array(
		'id'         => 'pmpro-logout',
		'title'      => __( 'Log Out', 'paid-memberships-pro'),
		'type'       => 'pmpro-logout',
		'type_label' => __( 'Page', 'paid-memberships-pro'),
		'object'     => 'page',
		'url'        => '#',
	);

	return $items;
}
add_filter( 'customize_nav_menu_available_items', 'pmpro_customize_nav_menu_available_items', 10, 4 );

/**
 * Filter nav menus with our custom Log In or Log Out links.
 * Remove the appropriate link based on logged in status.
 *
 * @since 2.3
 */
function pmpro_swap_log_in_log_out_menu_link( $sorted_menu_items, $args ) {

	foreach ( $sorted_menu_items as $key => $item ) {

		// Hide or Show the Log In link and filter the URL.
		if ( in_array( 'menu-item-type-pmpro-login', $item->classes ) ) {
			if ( is_user_logged_in() ) {
				unset( $sorted_menu_items[$key] );
			} else {
				$sorted_menu_items[$key]->url = pmpro_login_url();
				//$remove_key = array_search( 'menu-item-pmpro-login', $item->classes );
				$remove_key2 = array_search( 'menu-item-object-', $item->classes );
				//unset($sorted_menu_items[$key]->classes[$remove_key]);
				unset($sorted_menu_items[$key]->classes[$remove_key2]);
			}
		}

		// Hide or Show the Log Our link and filter the URL.
		if ( in_array( 'menu-item-type-pmpro-logout', $item->classes ) ) {
			if ( ! is_user_logged_in() ) {
				unset( $sorted_menu_items[$key] );
			} else {
				$sorted_menu_items[$key]->url = wp_logout_url();
				//$remove_key = array_search( 'menu-item-pmpro-logout', $item->classes );
				$remove_key2 = array_search( 'menu-item-object-', $item->classes );
				//unset($sorted_menu_items[$key]->classes[$remove_key]);
				unset($sorted_menu_items[$key]->classes[$remove_key2]);
			}
		}

	}

	return $sorted_menu_items;
}
add_filter( 'wp_nav_menu_objects', 'pmpro_swap_log_in_log_out_menu_link', 10, 2 );

/**
 * Custom menu functions for Paid Memberships Pro
 *
 * @since 2.3
 */
function pmpro_register_menus() {
	// Register PMPro menu areas.
	register_nav_menus(
		array(
			'pmpro-login-widget' => __( 'Log In Widget - PMPro', 'paid-memberships-pro' ),
		)
	);
}
add_action( 'after_setup_theme', 'pmpro_register_menus' );

/**
 * Hide the WordPress Toolbar from Subscribers.
 *
 * @since 2.3
 */
function pmpro_hide_toolbar() {
	global $current_user;
	$hide_toolbar = pmpro_getOption( 'hide_toolbar' );
	if ( ! empty( $hide_toolbar ) && is_user_logged_in() && in_array( 'subscriber', (array) $current_user->roles ) ) {
		$hide = true;
	} else {
		$hide = false;
	}	
	$hide = apply_filters( 'pmpro_hide_toolbar', $hide );
	if ( $hide ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}
}
add_action( 'init', 'pmpro_hide_toolbar', 9 );