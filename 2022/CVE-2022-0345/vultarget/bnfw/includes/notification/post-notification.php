<?php
/**
 * Handle post notifications.
 *
 * @since 1.3.6
 */

/**
 * Define the list of post notifications.
 *
 * @since 1.3.6
 *
 * @param  array  $notifications List of post notifications
 * @param  string $post_type     Post type
 * @return array                 Filtered list of post notifications
 */
function bnfw_post_notifications( $notifications, $post_type ) {
	$notifications[] = 'new-' . $post_type;
	$notifications[] = 'update-' . $post_type;
	$notifications[] = 'pending-' . $post_type;
	$notifications[] = 'future-' . $post_type;
	$notifications[] = 'comment-' . $post_type;
    $notifications[] = 'moderate-' . $post_type . '-comment';
	$notifications[] = 'approve-' . $post_type . '-comment';
    if($post_type == 'post'){
	$notifications[] = 'new-comment';
	$notifications[] = 'new-trackback';
	$notifications[] = 'new-pingback';
	$notifications[] = 'approve-comment';
    }
	return $notifications;
}
add_filter( 'bnfw_post_notifications', 'bnfw_post_notifications', 10, 2 );
