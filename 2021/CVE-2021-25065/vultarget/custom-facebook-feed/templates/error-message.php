<?php
/**
 * Custom Facebook Feed : Error Message Template
 * Display different error message
 *
 * @version 2.19 Custom Facebook Feed by Smash Balloon
 *
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Shortcode_Display;
//Check to see whether this feed will have a PPCA error come Sep 4. If so, display a warning notice.

$cff_ppca_check_error = CFF_Shortcode_Display::get_error_check( $page_id, $user_id, $access_token );


//If there's no data then show a pretty error message
if( empty($FBdata->data) || isset($FBdata->cached_error) || $cff_ppca_check_error ) :
	//Check whether it's an error in the backup cache
	if( isset($FBdata->cached_error) ) $FBdata->error = $FBdata->cached_error;
	//Show custom message for the PPCA error
	if( isset($FBdata->error->message) && strpos($FBdata->error->message, 'Page Public Content Access') !== false ) {
		$FBdata->error->message = esc_html__('(#10) To use "Page Public Content Access", your use of this endpoint must be reviewed and approved by Facebook.' , 'custom-facebook-feed');
		$FBdata->error->type = $FBdata->error->code = $FBdata->error->error_subcode = NULL;
	}

	$cap 			= CFF_Shortcode_Display::get_error_message_cap();
	$cff_ppca_error = CFF_Shortcode_Display::get_error_check_ppca( $FBdata )

?>
<div class="cff-error-msg">
	<div>
		<i class="fa fa-lock" aria-hidden="true" style="margin-right: 5px;"></i><b><?php echo esc_html__('This message is only visible to admins.', 'custom-facebook-feed'); ?></b><br/>
		<?php
			if ( !$cff_ppca_check_error ) echo esc_html__('Problem displaying Facebook posts.', 'custom-facebook-feed');
			if ( isset($FBdata->cached_error) ) echo esc_html__(' Backup cache in use.', 'custom-facebook-feed');
		?>
		<?php if( $cff_ppca_check_error || $cff_ppca_error ): ?>
			</div>
			<?php if( $cff_ppca_error ): ?>
				<b>PPCA Error:</b> <?php echo esc_html__('Due to Facebook API changes it is no longer possible to display a feed from a Facebook Page you are not an admin of. The Facebook feed below is not using a valid Access Token for this Facebook page and so has stopped updating.', 'custom-facebook-feed'); ?>
			<?php else: ?>
				<a class="cff_notice_dismiss" href="<?php echo esc_url( add_query_arg( 'cff_ppca_check_notice_dismiss', '0' )  ); ?>"><span class="fa fa-times-circle" aria-hidden="true"></span></a>
				<b class="cff-warning-notice">PPCA Error:</b> <?php echo esc_html__('Due to Facebook API changes on September 4, 2020, it will no longer be possible to display a feed from a Facebook Page you are not an admin of. The Facebook feed below is not using a valid Access Token for this Facebook page and so will stop updating after this date.', 'custom-facebook-feed'); ?>
			<?php endif; ?>
			<?php if(  current_user_can( $cap )  ): ?>
				<br /><b style="margin-top: 5px; display: inline-block;"><?php echo esc_html__('Action Required.', 'custom-facebook-feed'); ?>:</b> <?php echo esc_html__('Please', 'custom-facebook-feed'); ?> <a href="https://smashballoon.com/facebook-ppca-error-notice/" target="_blank"><?php echo esc_html__('see here', 'custom-facebook-feed'); ?></a> <?php echo esc_html__('for information on how to fix this.', 'custom-facebook-feed'); ?>
			<?php endif; ?>

		<?php else: ?>
			<br/><a href="javascript:void(0);" id="cff-show-error" onclick="cffShowError()"><?php echo esc_html__('Click to show error', 'custom-facebook-feed'); ?></a>
			<script type="text/javascript">function cffShowError() { document.getElementById("cff-error-reason").style.display = "block"; document.getElementById("cff-show-error").style.display = "none"; }</script>
			</div>
			<div id="cff-error-reason">
				<?php if( isset($FBdata->error->message) ): ?>
					<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo $FBdata->error->message; ?>
				<?php endif; ?>
				<?php if( isset($FBdata->error->type) ): ?>
					<b><?php echo esc_html__('Type', 'custom-facebook-feed'); ?>:</b> <?php echo $FBdata->error->type; ?>
				<?php endif; ?>
				<?php if( isset($FBdata->error->error_subcode) ): ?>
					<b><?php echo esc_html__('Subcode', 'custom-facebook-feed'); ?>:</b> <?php echo $FBdata->error->error_subcode; ?>
				<?php endif; ?>
				<?php if( isset($FBdata->error_msg) ): ?>
					<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo $FBdata->error_msg; ?>
				<?php endif; ?>
				<?php if( isset($FBdata->error_code) ): ?>
					<?php echo esc_html__('Code', 'custom-facebook-feed'); ?>: <?php echo $FBdata->error_code; ?>
				<?php endif; ?>
				<?php if( $FBdata == null ): ?>
					<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo esc_html__('Server configuration issue', 'custom-facebook-feed'); ?>
				<?php endif; ?>
				<?php if( empty($FBdata->error) && empty($FBdata->error_msg) && $FBdata !== null ): ?>
					<b><?php echo esc_html__('Error', 'custom-facebook-feed'); ?>:</b> <?php echo esc_html__('No posts available for this Facebook ID', 'custom-facebook-feed'); ?>
				<?php endif; ?>
				<?php if( current_user_can($cap) ): ?>
					<br /><b><?php echo esc_html__('Solution', 'custom-facebook-feed'); ?>:</b> <a href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank"><?php echo esc_html__('See here', 'custom-facebook-feed'); ?></a> <?php echo esc_html__('for how to solve this error', 'custom-facebook-feed'); ?>
				<?php endif; ?>

			</div>
		<?php endif; ?>
		<?php if( current_user_can($cap) ): ?>
			<style>#cff .cff-error-msg{ display: block !important; }</style>
		<?php endif; ?>

</div>
<?php endif; ?>