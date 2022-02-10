<?php SimpleWpMembership::enqueue_validation_scripts(); ?>
<div class="wrap" id="swpm-level-page">
<form action="" method="post" name="swpm-edit-level" id="swpm-edit-level" class="validate swpm-validate-form"<?php do_action('level_edit_form_tag');?>>
<input name="action" type="hidden" value="editlevel" />
<?php wp_nonce_field( 'edit_swpmlevel_admin_end', '_wpnonce_edit_swpmlevel_admin_end' ) ?>
<h2><?php echo  SwpmUtils::_('Edit membership level'); ?></h2>
<p>
    <?php 
    echo SwpmUtils::_('You can edit details of a selected membership level from this interface. ');
    echo SwpmUtils::_('You are currently editing: '). stripslashes($alias);
    ?>
</p>
<table class="form-table">
    <tbody>
	<tr>
		<th scope="row"><label for="alias"><?php echo  SwpmUtils::_('Membership Level Name'); ?> <span class="description"><?php echo  SwpmUtils::_('(required)'); ?></span></label></th>
		<td><input class="regular-text validate[required]" name="alias" type="text" id="alias" value="<?php echo stripslashes($alias);?>" aria-required="true" /></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="role"><?php echo  SwpmUtils::_('Default WordPress Role'); ?> <span class="description"><?php echo  SwpmUtils::_('(required)'); ?></span></label></th>
		<td><select  class="regular-text" name="role"><?php wp_dropdown_roles( $role ); ?></select></td>
	</tr>
    <tr>
        <th scope="row"><label for="subscription_period"><?php echo  SwpmUtils::_('Access Duration'); ?> <span class="description"><?php echo  SwpmUtils::_('(required)'); ?></span></label>
        </th>
        <td>
                <p><input type="radio" <?php echo  checked(SwpmMembershipLevel::NO_EXPIRY,$subscription_duration_type,false)?> value="<?php echo  SwpmMembershipLevel::NO_EXPIRY?>" name="subscription_duration_type" /> <?php echo  SwpmUtils::_('No Expiry (Access for this level will not expire until cancelled)')?></p>                                
                <p><input type="radio" <?php echo  checked(SwpmMembershipLevel::DAYS,$subscription_duration_type,false)?> value="<?php echo  SwpmMembershipLevel::DAYS ?>" name="subscription_duration_type" /> <?php echo  SwpmUtils::_('Expire After')?> 
                    <input type="text" value="<?php echo  checked(SwpmMembershipLevel::DAYS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  SwpmMembershipLevel::DAYS ?>"> <?php echo  SwpmUtils::_('Days (Access expires after given number of days)')?></p>
                
                <p><input type="radio" <?php echo  checked(SwpmMembershipLevel::WEEKS,$subscription_duration_type,false)?> value="<?php echo  SwpmMembershipLevel::WEEKS?>" name="subscription_duration_type" /> <?php echo  SwpmUtils::_('Expire After')?> 
                    <input type="text" value="<?php echo  checked(SwpmMembershipLevel::WEEKS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  SwpmMembershipLevel::WEEKS ?>"> <?php echo  SwpmUtils::_('Weeks (Access expires after given number of weeks)')?></p>
                
                <p><input type="radio" <?php echo  checked(SwpmMembershipLevel::MONTHS,$subscription_duration_type,false)?> value="<?php echo  SwpmMembershipLevel::MONTHS?>" name="subscription_duration_type" /> <?php echo  SwpmUtils::_('Expire After')?> 
                    <input type="text" value="<?php echo  checked(SwpmMembershipLevel::MONTHS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  SwpmMembershipLevel::MONTHS?>"> <?php echo  SwpmUtils::_('Months (Access expires after given number of months)')?></p>
                
                <p><input type="radio" <?php echo  checked(SwpmMembershipLevel::YEARS,$subscription_duration_type,false)?> value="<?php echo  SwpmMembershipLevel::YEARS?>" name="subscription_duration_type" /> <?php echo  SwpmUtils::_('Expire After')?> 
                    <input type="text" value="<?php echo  checked(SwpmMembershipLevel::YEARS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  SwpmMembershipLevel::YEARS?>"> <?php echo  SwpmUtils::_('Years (Access expires after given number of years)')?></p>                
                
                <p><input type="radio" <?php echo  checked(SwpmMembershipLevel::FIXED_DATE,$subscription_duration_type,false)?> value="<?php echo  SwpmMembershipLevel::FIXED_DATE?>" name="subscription_duration_type" /> <?php echo  SwpmUtils::_('Fixed Date Expiry')?> 
                    <input type="text" class="swpm-date-picker" value="<?php echo  checked(SwpmMembershipLevel::FIXED_DATE,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  SwpmMembershipLevel::FIXED_DATE?>" id="subscription_period_<?php echo  SwpmMembershipLevel::FIXED_DATE?>"> <?php echo  SwpmUtils::_('(Access expires on a fixed date)')?></p>                                
        </td>        
    </tr>
            <tr>
            <th scope="row">
                <label for="email_activation"><?php echo  SwpmUtils::_('Email Activation'); ?></label>
            </th>
            <td>
                <input name="email_activation" type="checkbox" value="1" <?php checked($email_activation);?>>
                <p class="description">
                    <?php echo  SwpmUtils::_('Enable new user activation via email. When enabled, members will need to click on an activation link that is sent to their email address to activate the account. Useful for free membership.');?>
                    <?php echo '<a href="https://simple-membership-plugin.com/email-activation-for-members/" target="_blank">' . SwpmUtils::_('View Documentation') . '.</a>'; ?>
                    <?php echo '<br><strong>'.SwpmUtils::_('Note:').'</strong> '.SwpmUtils::_('If enabled, decryptable member password is temporarily stored in the database until the account is activated.'); ?>
                </p>
            </td>
	</tr>
    <?php echo  apply_filters('swpm_admin_edit_membership_level_ui', '', $id);?>
</tbody>
</table>
<?php submit_button(SwpmUtils::_('Save Membership Level '), 'primary', 'editswpmlevel', true, array( 'id' => 'editswpmlevelsub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $('.swpm-date-picker').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: "-100:+100"});
});
</script>
