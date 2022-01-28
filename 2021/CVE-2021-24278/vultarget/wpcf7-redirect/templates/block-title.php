<?php
/**
 * Displays a conditional block title
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="block-title <?php echo $active_tab_title; ?>" data-rel="<?php echo $group_block_key; ?>">
	<?php $active_tab_title = ''; ?>
	<span class="dashicons dashicons-edit"></span>
	<span class="dashicons dashicons-yes show-on-edit" data-rel="<?php echo $group_block_key; ?>"></span>
	<span class="dashicons dashicons-no show-on-edit" data-rel="<?php echo $group_block_key; ?>"></span>
	<span class="dashicons dashicons-minus show-on-edit remove-block"></span>
	<input type="text" name="wpcf7-redirect<?php echo $prefix; ?>[blocks][<?php echo $group_block_key; ?>][block_title]" value="<?php echo $group_block['block_title']; ?>" data-original="<?php echo $group_block['block_title']; ?>" readonly="readonly">
</div>
