<?php if ( ! defined( 'ABSPATH' ) ) exit;
$this->custom_css();
?>
<div class="wrap fm_systemPropertyWrap">
<h3 class="fm_headingTitle"><?php _e('File Manager - System Properties','wp-file-manager'); ?>  <a href="https://filemanagerpro.io/product/file-manager" class="button button-primary fm_BuyProBtn" target="_blank" title="Click to Buy PRO"><?php  _e('Buy PRO', 'wp-file-manager'); ?></a></h3>

<table cellpadding="0" cellspacing="0" class="fm_systemPropertyTbl" style="background:#fff; padding:10px; border-left: 5px solid #09F;">
	
	<tr>
		<th><?php _e('PHP version','wp-file-manager'); ?></th>
		<td><code><?php echo phpversion(); ?></code></td>
	</tr>
	
	<tr>
		<th><?php _e('Maximum file upload size (upload_max_filesize)','wp-file-manager'); ?></th>
		<td><code><?php echo ini_get('upload_max_filesize'); ?></code></td>
	</tr>

	<tr>
		<th><?php _e('Post maximum file upload size (post_max_size)','wp-file-manager'); ?></th>
		<td><code><?php echo ini_get('post_max_size'); ?></code></td>
	</tr>
	
	<tr>
		<th><?php _e('Memory Limit (memory_limit)','wp-file-manager'); ?></th>
		<td><code><?php echo ini_get('memory_limit'); ?></code></td>
	</tr>
	
	<tr>
		<th><?php _e('Timeout (max_execution_time)','wp-file-manager'); ?></th>
		<td><code><?php echo ini_get('max_execution_time'); ?></code></td>
	</tr>
	
	<tr>
		<th><?php _e('Browser and OS (HTTP_USER_AGENT)','wp-file-manager'); ?></th>
		<td><code><?php echo $_SERVER['HTTP_USER_AGENT']; ?></code></td>
	</tr>
	
</table>
</div>