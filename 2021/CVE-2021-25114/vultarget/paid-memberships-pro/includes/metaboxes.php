<?php
/**
 * Require Membership Meta Box
 */
function pmpro_page_meta() {
	global $post, $wpdb;
	$membership_levels = pmpro_getAllLevels( true, true );
	$membership_levels = pmpro_sort_levels_by_order( $membership_levels );
	$page_levels = $wpdb->get_col( "SELECT membership_id FROM {$wpdb->pmpro_memberships_pages} WHERE page_id = '" . intval( $post->ID ) . "'" );
?>
    <ul id="membershipschecklist" class="list:category categorychecklist form-no-clear">
    <input type="hidden" name="pmpro_noncename" id="pmpro_noncename" value="<?php echo esc_attr( wp_create_nonce( plugin_basename(__FILE__) ) )?>" />
	<?php
		$in_member_cat = false;
		foreach( $membership_levels as $level ) {
		?>
    	<li id="membership-level-<?php echo esc_attr( $level->id ); ?>">
        	<label class="selectit">
            	<input id="in-membership-level-<?php echo esc_attr( $level->id ); ?>" type="checkbox" <?php if(in_array($level->id, $page_levels)) { ?>checked="checked"<?php } ?> name="page_levels[]" value="<?php echo esc_attr( $level->id ) ;?>" />
				<?php
					echo esc_html( $level->name );
					//Check which categories are protected for this level
					$protectedcategories = $wpdb->get_col( "SELECT category_id FROM $wpdb->pmpro_memberships_categories WHERE membership_id = '" . intval( $level->id ) . "'");
					//See if this post is in any of the level's protected categories
					if( in_category( $protectedcategories, $post->id ) ) {
						$in_member_cat = true;
						echo ' *';
					}
				?>
            </label>
        </li>
    	<?php
		}
    ?>
    </ul>
	<?php
		if( 'post' == get_post_type( $post ) && $in_member_cat ) { ?>
		<p class="pmpro_meta_notice">* <?php _e("This post is already protected for this level because it is within a category that requires membership.", 'paid-memberships-pro' );?></p>
	<?php
		}

		do_action( 'pmpro_after_require_membership_metabox', $post );
	?>
<?php
}

/**
 * Saves meta options when a page is saved.
 */
function pmpro_page_save( $post_id ) {
	global $wpdb;

	if( empty( $post_id ) ) {
		return false;
	}

	// Post is saving somehow with our meta box not shown.
	if ( ! isset( $_POST['pmpro_noncename'] ) ) {
		return $post_id;
	}

	// Verify the nonce.
	if ( ! wp_verify_nonce( $_POST['pmpro_noncename'], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Don't try to update meta fields on AUTOSAVE.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Check permissions.
	if( ! empty( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	}

	// OK, we're authenticated. We need to find and save the data.
	if( ! empty( $_POST['page_levels'] ) ) {
		$mydata = $_POST['page_levels'];
	} else {
		$mydata = NULL;
	}

	// Remove all memberships for this page.
	$wpdb->query( "DELETE FROM {$wpdb->pmpro_memberships_pages} WHERE page_id = '" . intval( $post_id ) . "'" );

	// Add new memberships for this page.
	if( is_array( $mydata ) ) {
		foreach( $mydata as $level ) {
			$wpdb->query( "INSERT INTO {$wpdb->pmpro_memberships_pages} (membership_id, page_id) VALUES('" . intval( $level ) . "', '" . intval( $post_id ) . "')" );
		}
	}

	return $mydata;
}

/**
 * Wrapper to add meta boxes
 */
function pmpro_page_meta_wrapper() {
	add_meta_box( 'pmpro_page_meta', __( 'Require Membership', 'paid-memberships-pro' ), 'pmpro_page_meta', 'page', 'side', 'high' );
	add_meta_box( 'pmpro_page_meta', __( 'Require Membership', 'paid-memberships-pro' ), 'pmpro_page_meta', 'post', 'side', 'high' );
}
if ( is_admin() ) {
	add_action( 'admin_menu', 'pmpro_page_meta_wrapper' );
	add_action( 'save_post', 'pmpro_page_save' );
}

/**
 * Show membership level restrictions on category edit.
 */
function pmpro_taxonomy_meta( $term ) {
	global $membership_levels, $post, $wpdb;

	$protectedlevels = array();
	foreach( $membership_levels as $level ) {
		$protectedlevel = $wpdb->get_col( "SELECT category_id FROM $wpdb->pmpro_memberships_categories WHERE membership_id = '" . intval( $level->id ) . "' AND category_id = '" . intval( $term->term_id ) . "'" );
		if( ! empty( $protectedlevel ) ) {
			$protectedlevels[] .= '<a target="_blank" href="admin.php?page=pmpro-membershiplevels&edit=' . intval( $level->id ) . '">' . esc_html( $level->name ) . '</a>';
		}
	}
	
	if( ! empty( $protectedlevels ) ) {
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><?php _e( 'Membership Levels', 'paid-memberships-pro' ); ?></label></th>
		<td>
			<p><strong>
				<?php echo implode(', ',$protectedlevels); ?></strong></p>
			<p class="description"><?php _e( 'Only members of these levels will be able to view posts in this category.', 'paid-memberships-pro' ); ?></p>
		</td>
	</tr>
	<?php
	}
}
add_action( 'category_edit_form_fields', 'pmpro_taxonomy_meta', 10, 2 );
