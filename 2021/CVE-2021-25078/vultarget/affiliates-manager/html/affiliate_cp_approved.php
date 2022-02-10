<div class="wrap">

	<h2><?php _e( 'Affiliate Control Panel', 'affiliates-manager' ) ?></h2>

	<h3><?php _e( 'Welcome!', 'affiliates-manager' ) ?></h3>
	<?php echo WPAM_MessageHelper::GetMessage('affiliate_application_approved')?>

	<br/><br/><br/>
	<a class="button-primary" href="<?php echo $this->viewData['confirmUrl']?>"><?php _e( 'Review Terms and Get Started!', 'affiliates-manager' ) ?></a>

</div>