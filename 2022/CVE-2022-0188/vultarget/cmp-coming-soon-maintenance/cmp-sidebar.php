<?php 
$slug = $this->cmp_selectedTheme();
$thumbnail = plugins_url('/img/thumbnails/'. $slug . '_thumbnail.jpg', __FILE__);
// if no thumbnail in CMP plugin folder, check directly in CMP theme folder
if ( !file_exists( CMP_PLUGIN_DIR . 'img/thumbnails/'. $slug . '_thumbnail.jpg' ) ) {
	$thumbnail = $this->cmp_themeURL( $slug ) . $slug . '/img/thumbnail.jpg';
} ?>
<div class="cmp-sidebar-wrapper">

	<div class="selected-theme widget">
		<h3 class="title"><?php _e('Selected CMP Theme', 'cmp-coming-soon-maintenance');?>: <?php echo ucwords( esc_html( str_replace( '_', ' ', $slug ) ) );?></h3>
		<img src="<?php echo esc_url( $thumbnail );?>" style="max-width:100%" alt="">
	
	</div>

	<div class="donate widget">

		<a href="https://niteothemes.com" target="_blank"><img src="<?php echo plugins_url('/img/niteothemes.svg', __FILE__);?>" alt="Niteo Logo" class="niteo-logo"></a>
		<p style="margin-top:0">
			<img src="<?php echo plugins_url('/img/alex.jpg', __FILE__);?>" alt="Alex, NiteoThemes">
			<img src="<?php echo plugins_url('/img/paul.jpg', __FILE__);?>" alt="Paul, NiteoThemes">
		</p>

		<p><?php echo sprintf(__('If you love our CMP plugin please donate few %s by clicking Donate button.', 'cmp-coming-soon-maintenance'), '<i class="fas fa-dollar-sign"></i><i class="fas fa-dollar-sign"></i><i class="fas fa-dollar-sign"></i>');?> <i class="far fa-smile-beam"></i></p>

		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<!-- <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"> -->
		</form>

		<p><?php echo sprintf(__('Follow us on %s.', 'cmp-coming-soon-maintenance'), '<a href="https://twitter.com/niteothemes" target="_blank">Twitter</a>');?></p>
	</div>

	<div class="clp-widget widget" style="padding: 0;">
		<h3 ><?php _e('Check out our other plugins!', 'cmp-coming-soon-maintenance');?></h3>
		<a href="https://wordpress.org/plugins/clp-custom-login-page/" target="_blank"><img src="<?php echo plugins_url('/img/clp-banner.png', __FILE__);?>" alt="CLP - Custom Login Page" style="max-width:100%;vertical-align:top"></a>
		<p style="padding: 0 2em"><a href="https://wordpress.org/plugins/clp-custom-login-page/" target="_blank" style="text-decoration:none">CLP - Custom Login Page</a> - <?php _e('Awesome plugin to customize WordPress Login Page!', 'cmp-coming-soon-maintenance');?></p>
	</div>

	<div class="cmp-rate-us widget">
		<h3 class="cmp-rate-us title"><?php _e('Thank you for rating us with five stars!', 'cmp-coming-soon-maintenance');?></h3>
		<p><?php echo sprintf(__('If you find our CMP plugin useful, please show us some love and give 5%s feedback by pressing button below.', 'cmp-coming-soon-maintenance'), '<i class="fas fa-star" aria-hidden="true"></i>');?></p>
		<a href="https://wordpress.org/support/plugin/cmp-coming-soon-maintenance/reviews/?rate=5#new-post" target="_blank" style="text-decoration:none;">

		<p class="button button-primary"><?php _e('Leave Feedback', 'cmp-coming-soon-maintenance');?></p>
		<i class="fas fa-star"></i>
		<i class="fas fa-star"></i>
		<i class="fas fa-star"></i>
		<i class="fas fa-star"></i>
		<i class="fas fa-star"></i>

		</a>
		<p><?php echo sprintf( __('We are always happy to help on %s in a case you run into some issues.', 'cmp-coming-soon-maintenance'), '<a href="http://wordpress.org/support/plugin/cmp-coming-soon-maintenance/" target="_blank" style="text-decoration:none;">WordPress Support forum</a>');?>
		</p>

	</div>

	<div class="request-feature widget">
		<h3 class="cmp-rate-us title"><?php _e('Request new features', 'cmp-coming-soon-maintenance');?></h3>
		<p><?php echo sprintf( __('Are you missing a cool feature or do you have idea how to improve CMP plugin? You can %s on official Wordpress Support Forum.', 'cmp-coming-soon-maintenance'), '<a href="http://wordpress.org/support/plugin/cmp-coming-soon-maintenance/" target="_blank" style="text-decoration:none;">request feature</a>' );?> <i class="far fa-smile-wink"></i></p>
		
	</div>

</div>