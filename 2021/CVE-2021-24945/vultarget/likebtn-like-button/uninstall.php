<?php
/**
 * Like Button Rating Uninstall
 *
 * Uninstalling deletes options.
 *
 * @author 		LikeBtn
 * @category 	Core
 */
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

require_once('likebtn_like_button.php');

likebtn_uninstall();