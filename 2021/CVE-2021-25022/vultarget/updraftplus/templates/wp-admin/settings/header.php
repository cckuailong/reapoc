<?php if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed'); ?>

<div class="wrap" id="updraft-wrap">

	<h1><?php echo $updraftplus->plugin_title; ?></h1>
	<div class="updraftplus-top-menu">
		<a href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/");?>" target="_blank">UpdraftPlus.Com</a> | 
		<?php
			if (!defined('UPDRAFTPLUS_NOADS_B')) {
				?>
				<a href="<?php echo $updraftplus->get_url('premium');?>" target="_blank"><?php _e("Premium", 'updraftplus'); ?></a> |
			<?php
			}
		?>
		<a href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/news/");?>" target="_blank"><?php _e('News', 'updraftplus');?></a>  | 
		<a href="https://twitter.com/updraftplus" target="_blank"><?php _e('Twitter', 'updraftplus');?></a> | 
		<a href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/support/");?>" target="_blank"><?php _e("Support", 'updraftplus');?></a> | 
		<?php
			if (!is_file(UPDRAFTPLUS_DIR.'/udaddons/updraftplus-addons.php')) {
			?>
				<a href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/newsletter-signup");?>" target="_blank"><?php _e("Newsletter sign-up", 'updraftplus'); ?></a> |
			<?php
			}
	?>
		<a href="https://david.dw-perspective.org.uk" target="_blank"><?php _e("Lead developer's homepage", 'updraftplus');?></a> | <a aria-label="F, A, Q" href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/support/frequently-asked-questions/");?>" target="_blank"><?php _e('FAQs', 'updraftplus'); ?></a> | <a aria-label="more plug-ins" href="https://www.simbahosting.co.uk/s3/shop/" target="_blank"><?php _e('More plugins', 'updraftplus');?></a> - <span tabindex="0"><?php _e('Version', 'updraftplus');?>: <?php echo $updraftplus->version; ?></span>
	</div>