<?php
/**
 * CMP Activation botice template
 */
?>

<style>
.cmp-feedback.updated {border-left-color: #18a0d2;position: relative;min-height: 90px;}
.cmp-notice-icon {float: left;margin-right: 1em;margin-top: 1em;}
.cmp-leave-feedback {text-align: right;position: absolute;right: 1em;bottom: 1em;}
@media screen and (max-width: 1366px) { .cmp-leave-feedback {position: relative;bottom: initial;margin: 1em 0;} }
</style>

<div class="cmp-feedback updated activation">
    <div class="cmp-notice-icon">
        <img src="<?php echo plugins_url('../img/cmp.png', __FILE__);?>" alt="CMP Logo" class="cmp-logo">
    </div>

    <h3><?php _e('Thank you for installing CMP - Coming soon & Maintenance Plugin!', 'cmp-coming-soon-maintenance');?></h3>
    <span><?php printf( esc_html__( 'You can customize the Coming Soon Page in %1$s or activate the Coming Soon Mode by Top bar CMP icon right away!', 'cmp-coming-soon-maintenance' ), '<a href="'. admin_url() . 'admin.php?page=cmp-settings">CMP Settings</a>'); ?></span>
    <div class="cmp-leave-feedback">
        <div><a href="#dismiss" class="cmp-activation-dismiss"><?php echo esc_html__( 'Dismiss', 'cmp-coming-soon-maintenance' ); ?></a></div>
    </div>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {
		$(document).on( 'click', '.cmp-activation-dismiss', function( event ) {
			event.preventDefault();
			$.post( ajaxurl, {
				action: 'cmp_ajax_dismiss_activation_notice',
				nonce: '<?php echo wp_create_nonce( 'cmp-coming-soon-maintenance-nonce' ); ?>'
			});
			$('.cmp-feedback.activation').remove();
		});
	});
</script>