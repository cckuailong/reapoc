<?php
//This file is used to edit member's profile from the admin dashboard of the plugin.

//The admin ajax causes an issue with the JS validation if done on form submission. The edit profile doesn't need JS validation on email. There is PHP validation which will catch any email error.
//SimpleWpMembership::enqueue_validation_scripts(array('ajaxEmailCall' => array('extraData'=>'&action=swpm_validate_email&member_id='.$member_id)));
?>
<div class="wrap" id="swpm-profile-page" type="edit">
    <form action="" method="post" name="swpm-edit-user" id="swpm-edit-user" enctype="multipart/form-data" class="validate swpm-validate-form"<?php do_action('user_new_form_tag');?>>
    <input name="action" type="hidden" value="edituser" />
    <?php wp_nonce_field( 'edit_swpmuser_admin_end', '_wpnonce_edit_swpmuser_admin_end' ) ?>
    <h3><?php echo  SwpmUtils::_('Edit Member') ?></h3>
    <p>
        <?php echo  SwpmUtils::_('Edit existing member details.'); ?>
        <?php echo  SwpmUtils::_(' You are currently editing member with member ID: '); ?>
        <?php echo esc_attr($member_id); ?>
    </p>
    <table class="form-table">
        <tr class="form-field form-required swpm-admin-edit-username">
            <th scope="row"><label for="user_name"><?php echo  SwpmUtils::_('Username'); ?> <span class="description"><?php echo  SwpmUtils::_('(required)'); ?></span></label></th>
            <td>
                <?php
                if (empty($user_name)) {
                    //This is a record with incomplete registration. The member need to complete the registration by clicking on the unique link sent to them
                    ?>
                    <div class="swpm-yellow-box" style="max-width:450px;">
                        <p>This user account registration is not complete yet. The member needs to click on the unique registration completion link (sent to his email) and complete the registration by choosing a username and password.</p>
                        <br />
                        <p>You can go to the <a href="admin.php?page=simple_wp_membership_settings&tab=4" target="_blank">Tools Interface</a> and generate another unique "Registration Completion" link then send the link to the user. Alternatively, you can use that link yourself and complete the registration on behalf of the user.</p>
                        <br />
                        <p>If you suspect that this user has lost interest in becoming a member then you can delete this member record.</p>
                    </div>
                    <?php
                } else {
                    echo esc_attr($user_name);
                }
                ?>
            </td>
        </tr>
        <tr class="form-required swpm-admin-edit-email">
            <th scope="row"><label for="email"><?php echo  SwpmUtils::_('E-mail'); ?> <span class="description"><?php echo  SwpmUtils::_('(required)'); ?></span></label></th>
            <td><input name="email" autocomplete="off" class="regular-text validate[required,custom[email],ajax[ajaxEmailCall]]" type="text" id="email" value="<?php echo esc_attr($email); ?>" /></td>
        </tr>
        <tr class="swpm-admin-edit-password">
            <th scope="row"><label for="password"><?php echo  SwpmUtils::_('Password'); ?> <span class="description"><?php _e('(twice, leave empty to retain old password)', 'simple-membership'); ?></span></label></th>
            <td><input class="regular-text"  name="password" type="password" id="pass1" autocomplete="off" /><br />
            <input class="regular-text" name="password_re" type="password" id="pass2" autocomplete="off" />
            <br />
            <div id="pass-strength-result"><?php echo SwpmUtils::_('Strength indicator'); ?></div>
            <p class="description indicator-hint"><?php echo SwpmUtils::_('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
            </td>
	</tr>
	<tr class="swpm-admin-edit-account-state">
            <th scope="row"><label for="account_state"><?php echo  SwpmUtils::_('Account Status'); ?></label></th>
            <td>
                <select class="regular-text" name="account_state" id="account_state">
                        <?php echo  SwpmUtils::account_state_dropdown($account_state);?>
                </select>
                <p class="description">
                    <?php echo SwpmUtils::_("This is the member's account status. If you want to manually activate an expired member's account then read"); ?>
                    <a href="https://simple-membership-plugin.com/manually-activating-expired-members-account/" target="_blank"><?php echo SwpmUtils::_("this documentation"); ?></a>
                    <?php echo SwpmUtils::_(" to learn how to do it."); ?>
                </p>
            </td>
	</tr>
	<tr class="swpm-admin-edit-notify-user">
            <th scope="row"><label for="account_state_change"><?php echo  SwpmUtils::_('Notify User'); ?></label></th>
            <td><input type="checkbox" id="account_status_change" name="account_status_change" />
                <p class="description indicator-hint">
                    <?php echo SwpmUtils::_("You can use this option to send a quick notification email to this member (the email will be sent when you hit the save button below)."); ?>
                </p>
            </td>
	</tr>
    <?php include('admin_member_form_common_part.php');?>
        <tr class="swpm-admin-edit-subscriber-id">
		<th scope="row"><label for="subscr_id"><?php echo  SwpmUtils::_('Subscriber ID/Reference') ?> </label></th>
		<td><input class="regular-text" name="subscr_id" type="text" id="subscr_id" value="<?php echo esc_attr($subscr_id); ?>" /></td>
	</tr>
        <tr class="swpm-admin-edit-expiry-date">
		<th scope="row"><label for="member_expiry_date"><?php echo SwpmUtils::_('Expiry Date') ?> </label></th>
		<td>
                    <?php
                    $member_current_expiry_date = SwpmMemberUtils::get_formatted_expiry_date_by_user_id($member_id);
                    echo esc_attr($member_current_expiry_date);
                    ?>
                    <p class="description indicator-hint">
                        <?php echo SwpmUtils::_('This is calculated based on the current membership level assigned to this member and the expiry condition that you have set for that membership level.') ?>
                    </p>
                </td>
	</tr>
        <tr class="swpm-admin-edit-last-accessed">
		<th scope="row"><label for="last_accessed"><?php echo SwpmUtils::_('Last Accessed Date') ?> </label></th>
		<td>
                    <?php echo esc_attr($last_accessed); ?>
                    <p class="description indicator-hint"><?php echo SwpmUtils::_('This value gets updated when this member logs into your site.') ?></p>
                </td>
	</tr>
        <tr class="swpm-admin-edit-last-accessed-ip">
		<th scope="row"><label for="last_accessed_from_ip"><?php echo  SwpmUtils::_('Last Accessed From IP') ?> </label></th>
		<td>
                    <?php echo esc_attr($last_accessed_from_ip); ?>
                    <p class="description indicator-hint"><?php echo SwpmUtils::_('This value gets updated when this member logs into your site.') ?></p>
                </td>
	</tr>

    </table>

    <?php include('admin_member_form_common_js.php'); ?>
    <?php echo apply_filters('swpm_admin_custom_fields', '',$membership_level); ?>
    <?php submit_button( SwpmUtils::_('Save Data'), 'primary', 'editswpmuser', true, array( 'id' => 'createswpmusersub' ) ); ?>
    <?php
    $delete_swpmuser_nonce = wp_create_nonce( 'delete_swpmuser_admin_end' );
    $member_delete_url = "?page=simple_wp_membership&member_action=delete&member_id=".$member_id."&delete_swpmuser_nonce=".$delete_swpmuser_nonce;
    echo '<div class="swpm-admin-delete-user-profile-link">';
    echo '<a style="color:red;font-weight:bold;" href="'.$member_delete_url.'" onclick="return confirm(\'Are you sure you want to delete this user profile?\')">'.SwpmUtils::_('Delete User Profile').'</a>';
    echo '</div>';
    ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $('#account_status_change').change(function(){
        var target = $(this).closest('tr');
        var $body = '<textarea rows="5" cols="60" id="notificationmailbody" name="notificationmailbody">' + SwpmSettings.statusChangeEmailBody + '</textarea>';
        var $head = '<input type="text" size="60" id="notificationmailhead" name="notificationmailhead" value="' + SwpmSettings.statusChangeEmailHead + '" />';
        var content = '<tr><th scope="row">Mail Subject</th><td>' + $head + '</td></tr>';
        content += '<tr><th scope="row">Mail Body</th><td>' + $body + '</td></tr>';
        if (this.checked) {
            target.after(content);
        }
        else {
            if (target.next('tr').find('#notificationmailhead').length > 0) {
                target.next('tr').remove();
                target.next('tr').remove();
            }
        }
    });
});
</script>
