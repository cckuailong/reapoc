<h2 class="wps_title"><?php echo(isset($title) ? $title : (function_exists('get_admin_page_title') ? get_admin_page_title() : '')); ?></h2>
<?php do_action('wp_statistics_after_admin_page_title'); ?>
<div class="wp-clearfix"></div>