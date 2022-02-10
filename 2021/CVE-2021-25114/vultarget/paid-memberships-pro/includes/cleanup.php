<?php
/*
	Clean things up when deletes happen, etc. (This stuff needs a better home.)
*/
//deleting a user? remove their account info.
function pmpro_delete_user($user_id = NULL)
{
	global $wpdb;

	//changing their membership level to 0 will cancel any subscription and remove their membership level entry
	//we don't remove the orders because it would affect reporting
	if(pmpro_changeMembershipLevel(0, $user_id))
	{
		//okay
	}
	else
	{
		//okay, guessing they didn't have a level
	}
}
add_action('delete_user', 'pmpro_delete_user');
add_action('wpmu_delete_user', 'pmpro_delete_user');

/**
 * Show a notice on the Delete User form so admin knows that membership and subscriptions will be cancelled.
 *
 * @param WP_User $current_user WP_User object for the current user.
 * @param int[]   $userids      Array of IDs for users being deleted.
 */
function pmpro_delete_user_form_notice( $current_user, $userids ) {
	// Check if any users for deletion have an an active membership level.
	foreach ( $userids as $user_id ) {
		$userids_have_levels = pmpro_hasMembershipLevel( null, $user_id );
		if ( ! empty( $userids_have_levels ) ) {
			break;
		}
	}

	// Show a notice if users for deletion have an an active membership level.
	if ( ! empty( $userids_have_levels ) ) { ?>
		<div class="notice notice-error inline">
			<?php if ( count( $userids ) > 1 ) {
				_e( '<p><strong>Warning:</strong> One or more users for deletion have an active membership level. Deleting a user will also cancel their membership and recurring subscription.</p>', 'paid-memberships-pro' );
			} else {
				_e( '<p><strong>Warning:</strong> This user has an active membership level. Deleting a user will also cancel their membership and recurring subscription.</p>', 'paid-memberships-pro' );
			}
		?>
		</div>
	<?php
	}
}
add_action( 'delete_user_form', 'pmpro_delete_user_form_notice', 10, 2 );

//deleting a category? remove any level associations
function pmpro_delete_category($cat_id = NULL)
{
	global $wpdb;
	$sqlQuery = "DELETE FROM $wpdb->pmpro_memberships_categories WHERE category_id = '" . $cat_id . "'";
	$wpdb->query($sqlQuery);
}
add_action('delete_category', 'pmpro_delete_category');

//deleting a post? remove any level associations
function pmpro_delete_post($post_id = NULL)
{
	global $wpdb;
	$sqlQuery = "DELETE FROM $wpdb->pmpro_memberships_pages WHERE page_id = '" . $post_id . "'";
	$wpdb->query($sqlQuery);
}
add_action('delete_post', 'pmpro_delete_post');
