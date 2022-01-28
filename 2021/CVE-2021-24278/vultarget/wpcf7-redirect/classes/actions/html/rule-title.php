<?php
/**
 * rule-title.php file.
 *
 * @package cf7r
 */

 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }

 /**
 * Display the block title
 *
 * @version  1.0.0
 */
?>
<div class="field-wrap field-wrap-post-title">
    <label for="wpcf7-redirect-post-title">
        <strong><?php esc_html_e( 'Rule Title', 'wpcf7-redirect' );?></strong>
    </label>
    <input type="text" class="wpcf7-redirect-post-title-fields" placeholder="<?php esc_html_e( 'Rule title', 'wpcf7-redirect' );?>" name="wpcf7-redirect<?php echo $prefix;?>[post_title]" value="<?php echo $this->get_title();?>">
</div>
