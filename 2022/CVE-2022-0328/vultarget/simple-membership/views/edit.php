<?php
$auth = SwpmAuth::get_instance();
$user_data = (array) $auth->userData;
$user_data['membership_level_alias'] = $auth->get('alias');
extract($user_data, EXTR_SKIP);
$settings=SwpmSettings::get_instance();
$force_strong_pass=$settings->get_value('force-strong-passwords');
if (!empty($force_strong_pass)) {
    $pass_class="validate[custom[strongPass],minSize[8]]";
} else {
    $pass_class="";
}
SimpleWpMembership::enqueue_validation_scripts();
//The admin ajax causes an issue with the JS validation if done on form submission. The edit profile doesn't need JS validation on email. There is PHP validation which will catch any email error.
//SimpleWpMembership::enqueue_validation_scripts(array('ajaxEmailCall' => array('extraData'=>'&action=swpm_validate_email&member_id='.SwpmAuth::get_instance()->get('member_id'))));
?>
<div class="swpm-edit-profile-form">
    <form id="swpm-editprofile-form" name="swpm-editprofile-form" method="post" action="" class="swpm-validate-form">
        <?php wp_nonce_field('swpm_profile_edit_nonce_action', 'swpm_profile_edit_nonce_val') ?>
        <table>
            <?php apply_filters('swpm_edit_profile_form_before_username', ''); ?>
            <tr class="swpm-profile-username-row" <?php apply_filters('swpm_edit_profile_form_username_tr_attributes', ''); ?>>
                <td><label for="user_name"><?php echo SwpmUtils::_('Username'); ?></label></td>
                <td><?php echo $user_name ?></td>
            </tr>
            <tr class="swpm-profile-email-row">
                <td><label for="email"><?php echo SwpmUtils::_('Email'); ?></label></td>
                <td><input type="text" id="email" name="email" size="50" autocomplete="off" class="" value="<?php echo $email; ?>" /></td>
            </tr>
            <tr class="swpm-profile-password-row">
                <td><label for="password"><?php echo SwpmUtils::_('Password'); ?></label></td>
                <td><input type="password" id="password" value="" size="50" name="password" class="<?php echo $pass_class;?>" autocomplete="off" placeholder="<?php echo SwpmUtils::_('Leave empty to keep the current password'); ?>" /></td>
            </tr>
            <tr class="swpm-profile-password-retype-row">
                <td><label for="password_re"><?php echo SwpmUtils::_('Repeat Password'); ?></label></td>
                <td><input type="password" id="password_re" value="" size="50" name="password_re" autocomplete="off" placeholder="<?php echo SwpmUtils::_('Leave empty to keep the current password'); ?>" /></td>
            </tr>
            <tr class="swpm-profile-firstname-row" <?php apply_filters('swpm_edit_profile_form_firstname_tr_attributes', ''); ?>>
                <td><label for="first_name"><?php echo SwpmUtils::_('First Name'); ?></label></td>
                <td><input type="text" id="first_name" value="<?php echo $first_name; ?>" size="50" name="first_name" /></td>
            </tr>
            <tr class="swpm-profile-lastname-row" <?php apply_filters('swpm_edit_profile_form_lastname_tr_attributes', ''); ?>>
                <td><label for="last_name"><?php echo SwpmUtils::_('Last Name'); ?></label></td>
                <td><input type="text" id="last_name" value="<?php echo $last_name; ?>" size="50" name="last_name" /></td>
            </tr>
            <tr class="swpm-profile-phone-row" <?php apply_filters('swpm_edit_profile_form_phone_tr_attributes', ''); ?>>
                <td><label for="phone"><?php echo SwpmUtils::_('Phone'); ?></label></td>
                <td><input type="text" id="phone" value="<?php echo $phone; ?>" size="50" name="phone" /></td>
            </tr>
            <tr class="swpm-profile-street-row" <?php apply_filters('swpm_edit_profile_form_street_tr_attributes', ''); ?>>
                <td><label for="address_street"><?php echo SwpmUtils::_('Street'); ?></label></td>
                <td><input type="text" id="address_street" value="<?php echo $address_street; ?>" size="50" name="address_street" /></td>
            </tr>
            <tr class="swpm-profile-city-row" <?php apply_filters('swpm_edit_profile_form_city_tr_attributes', ''); ?>>
                <td><label for="address_city"><?php echo SwpmUtils::_('City'); ?></label></td>
                <td><input type="text" id="address_city" value="<?php echo $address_city; ?>" size="50" name="address_city" /></td>
            </tr>
            <tr class="swpm-profile-state-row" <?php apply_filters('swpm_edit_profile_form_state_tr_attributes', ''); ?>>
                <td><label for="address_state"><?php echo SwpmUtils::_('State'); ?></label></td>
                <td><input type="text" id="address_state" value="<?php echo $address_state; ?>" size="50" name="address_state" /></td>
            </tr>
            <tr class="swpm-profile-zipcode-row" <?php apply_filters('swpm_edit_profile_form_zipcode_tr_attributes', ''); ?>>
                <td><label for="address_zipcode"><?php echo SwpmUtils::_('Zipcode'); ?></label></td>
                <td><input type="text" id="address_zipcode" value="<?php echo $address_zipcode; ?>" size="50" name="address_zipcode" /></td>
            </tr>
            <tr class="swpm-profile-country-row" <?php apply_filters('swpm_edit_profile_form_country_tr_attributes', ''); ?>>
                <td><label for="country"><?php echo SwpmUtils::_('Country'); ?></label></td>
                <td><select id="country" name="country"><?php echo SwpmMiscUtils::get_countries_dropdown($country) ?></select></td>
            </tr>
            <tr class="swpm-profile-company-row" <?php apply_filters('swpm_edit_profile_form_company_tr_attributes', ''); ?>>
                <td><label for="company_name"><?php echo SwpmUtils::_('Company Name'); ?></label></td>
                <td><input type="text" id="company_name" value="<?php echo $company_name; ?>" size="50" name="company_name" /></td>
            </tr>            
            <tr class="swpm-profile-membership-level-row" <?php apply_filters('swpm_edit_profile_form_membership_level_tr_attributes', ''); ?>>
                <td><label for="membership_level"><?php echo SwpmUtils::_('Membership Level'); ?></label></td>
                <td>
                    <?php echo $membership_level_alias; ?>
                </td>
            </tr>
            <?php apply_filters('swpm_edit_profile_form_after_membership_level', ''); ?>
        </table>
        <?php apply_filters('swpm_edit_profile_form_before_submit', ''); ?>
        <p class="swpm-edit-profile-submit-section">
            <input type="submit" value="<?php echo SwpmUtils::_('Update') ?>" class="swpm-edit-profile-submit" name="swpm_editprofile_submit" />
        </p>
        <?php echo SwpmUtils::delete_account_button(); ?>

        <input type="hidden" name="action" value="custom_posts" />

    </form>
</div>