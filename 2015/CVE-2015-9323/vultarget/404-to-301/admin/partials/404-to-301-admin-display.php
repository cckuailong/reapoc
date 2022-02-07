<?php
if ( ! defined( 'WPINC' ) ) {
	die('Nice try dude. But I am sorry');
}
/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the dashboard pages of the plugin.
 *
 * @link       http://iscode.co/product/404-to-301/
 * @since      2.0.0
 *
 * @package    I4T3
 * @subpackage I4T3/admin/partials
 */
?>

<!-- This should be primarily consist of HTML with a little bit of PHP in it. -->
	<?php $active_tab = ( !isset( $_GET['tab'] ) ) ? 'general' : $_GET['tab'];

	$general_class = '';
	$credits_class = '';
	${$active_tab.'_class'} = 'nav-tab-active';
	?>

	<div class="wrap">
	    <h2>404 to 301 | Settings</h2><br>
	    <?php if( isset($_GET['settings-updated']) ) { ?>
			<div class="updated">
				<p><strong>404 to 301 settings updated successfully</strong></p>
			</div>
		<?php } ?>
	    <h2 class="nav-tab-wrapper">
		    <a href="?page=i4t3-settings&tab=general" class="nav-tab <?php echo $general_class; ?>">General</a>
		    <a href="?page=i4t3-settings&tab=credits" class="nav-tab <?php echo $credits_class ?>">Help & Info</a>
	    </h2>
	</div>

	<?php

	switch ( $active_tab ) {

		case 'general':
			// Get list of active pages
			$args = array(
				'post_type' => 'page',
				'post_status' => 'publish'
			);
			$pages = get_pages( $args );
			include_once('404-to-301-admin-general-tab.php');
			break;
		case 'credits':
			$current_user = wp_get_current_user();
			include_once('404-to-301-admin-credits-tab.php');
			break;
		default:
	    	include_once('404-to-301-admin-general-tab.php');
	}
