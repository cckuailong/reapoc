<?php
/**
 * Template: Cancel
 *
 * See documentation for how to override the PMPro templates.
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 2.0
 *
 * @author Paid Memberships Pro
 */
global $pmpro_msg, $pmpro_msgt, $pmpro_confirm, $current_user, $wpdb;

if(isset($_REQUEST['levelstocancel']) && $_REQUEST['levelstocancel'] !== 'all') {
	//convert spaces back to +
	$_REQUEST['levelstocancel'] = str_replace(array(' ', '%20'), '+', $_REQUEST['levelstocancel']);

	//get the ids
	$old_level_ids = array_map('intval', explode("+", preg_replace("/[^0-9al\+]/", "", $_REQUEST['levelstocancel'])));

} elseif(isset($_REQUEST['levelstocancel']) && $_REQUEST['levelstocancel'] == 'all') {
	$old_level_ids = 'all';
} else {
	$old_level_ids = false;
}
?>
<div id="pmpro_cancel" class="<?php echo pmpro_get_element_class( 'pmpro_cancel_wrap', 'pmpro_cancel' ); ?>">
	<?php
		if($pmpro_msg)
		{
			?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo $pmpro_msg?></div>
			<?php
		}
	?>
	<?php
		if(!$pmpro_confirm)
		{
			if($old_level_ids)
			{
				if(!is_array($old_level_ids) && $old_level_ids == "all")
				{
					?>
					<p><?php _e('Are you sure you want to cancel your membership?', 'paid-memberships-pro' ); ?></p>
					<?php
				}
				else
				{
					$level_names = $wpdb->get_col("SELECT name FROM $wpdb->pmpro_membership_levels WHERE id IN('" . implode("','", $old_level_ids) . "')");
					?>
					<p><?php printf(_n('Are you sure you want to cancel your %s membership?', 'Are you sure you want to cancel your %s memberships?', count($level_names), 'paid-memberships-pro'), pmpro_implodeToEnglish($level_names)); ?></p>
					<?php
				}
			?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_actionlinks' ); ?>">
				<a class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit pmpro_yeslink yeslink', 'pmpro_btn-submit' ); ?>" href="<?php echo pmpro_url("cancel", "?levelstocancel=" . esc_attr($_REQUEST['levelstocancel']) . "&confirm=true")?>"><?php _e('Yes, cancel this membership', 'paid-memberships-pro' );?></a>
				<a class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-cancel pmpro_nolink nolink', 'pmpro_btn-cancel' ); ?>" href="<?php echo pmpro_url("account")?>"><?php _e('No, keep this membership', 'paid-memberships-pro' );?></a>
			</div>
			<?php
			}
			else
			{
				if($current_user->membership_level->ID)
				{
					?>
					<h2><?php _e("My Memberships", 'paid-memberships-pro' );?></h2>
					<table class="<?php echo pmpro_get_element_class( 'pmpro_table' ); ?>" width="100%" cellpadding="0" cellspacing="0" border="0">
						<thead>
							<tr>
								<th><?php _e("Level", 'paid-memberships-pro' );?></th>
								<th><?php _e("Expiration", 'paid-memberships-pro' ); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$current_user->membership_levels = pmpro_getMembershipLevelsForUser($current_user->ID);
								foreach($current_user->membership_levels as $level) {
								?>
								<tr>
									<td class="<?php echo pmpro_get_element_class( 'pmpro_cancel-membership-levelname' ); ?>">
										<?php echo $level->name?>
									</td>
									<td class="<?php echo pmpro_get_element_class( 'pmpro_cancel-membership-expiration' ); ?>">
									<?php
										if($level->enddate) {
											$expiration_text = date_i18n( get_option( 'date_format' ), $level->enddate );
   										} else {
   											$expiration_text = "---";
										}
       									 
										echo apply_filters( 'pmpro_account_membership_expiration_text', $expiration_text, $level );
									?>
									</td>
									<td class="<?php echo pmpro_get_element_class( 'pmpro_cancel-membership-cancel' ); ?>">
										<a href="<?php echo pmpro_url("cancel", "?levelstocancel=" . $level->id)?>"><?php _e("Cancel", 'paid-memberships-pro' );?></a>
									</td>
								</tr>
								<?php
								}
							?>
						</tbody>
					</table>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?>">
						<a href="<?php echo pmpro_url("cancel", "?levelstocancel=all"); ?>"><?php _e("Cancel All Memberships", 'paid-memberships-pro' );?></a>
					</div>
					<?php
				}
			}
		}
		else
		{
			?>
			<p class="<?php echo pmpro_get_element_class( 'pmpro_cancel_return_home' ); ?>"><a href="<?php echo get_home_url()?>"><?php _e('Click here to go to the home page.', 'paid-memberships-pro' );?></a></p>
			<?php
		}
	?>
</div> <!-- end pmpro_cancel, pmpro_cancel_wrap -->
