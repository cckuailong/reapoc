<?php
/**
 * User: shahnuralam
 * Date: 5/7/17
 * Time: 2:19 AM
 */
global $wpdb;
if (!defined('ABSPATH')) die('!');
?>
<div class="w3eden">
    <div class="list-group">
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo get_option('wpdm_fb_likes', 0); ?></span>
            <?php _e( "Facebook Likes" , "download-manager" ); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo get_option('wpdm_tweets', 0); ?></span>
            <?php _e( "Tweets" , "download-manager" ); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo get_option('wpdm_gplus1s', 0); ?></span>
            <?php _e( "Google +1" , "download-manager" ); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo get_option('wpdm_linkedins', 0); ?></span>
            <?php _e( "LinkedIn Shares" , "download-manager" ); ?>
        </div>

    </div>
</div>
