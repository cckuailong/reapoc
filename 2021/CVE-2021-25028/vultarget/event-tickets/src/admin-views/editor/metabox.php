<?php
/**
 * @var WP_Post                      $post
 * @var bool                         $show_global_stock
 * @var Tribe__Tickets__Global_Stock $global_stock
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$post_id = get_the_ID();

/** @var Tribe__Tickets__Admin__Views $admin_views */
$admin_views = tribe( 'tickets.admin.views' );
?>

<div class="tribe-tickets-editor-blocker">
	<span class="spinner"></span>
</div>

<div id="event_tickets" class="eventtable" aria-live="polite">
	<?php wp_nonce_field( 'tribe-tickets-meta-box', 'tribe-tickets-post-settings' ); ?>

	<?php $admin_views->template( [ 'editor', 'panel', 'list' ], get_defined_vars() ); ?>

	<?php $admin_views->template( [ 'editor', 'panel', 'ticket' ], get_defined_vars() ); ?>

	<?php $admin_views->template( [ 'editor', 'panel', 'settings' ], get_defined_vars() ); ?>

</div>
