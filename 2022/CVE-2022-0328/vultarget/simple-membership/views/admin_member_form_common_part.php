<tr class="swpm-admin-edit-membership-level">
    <th scope="row"><label for="membership_level"><?php echo SwpmUtils::_('Membership Level'); ?></label></th>
    <td><select class="regular-text" name="membership_level" id="membership_level">
            <?php foreach ($levels as $level): ?>
                <option <?php echo ($level['id'] == $membership_level) ? "selected='selected'" : ""; ?> value="<?php echo $level['id']; ?>"> <?php echo $level['alias'] ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr class="swpm-admin-edit-access-starts">
    <th scope="row"><label for="subscription_starts"><?php echo SwpmUtils::_('Access Starts') ?> </label></th>
    <td><input class="regular-text" name="subscription_starts" type="text" id="subscription_starts" value="<?php echo esc_attr($subscription_starts); ?>" /></td>
</tr>  
<tr class="swpm-admin-edit-first-name">
    <th scope="row"><label for="first_name"><?php echo SwpmUtils::_('First Name') ?> </label></th>
    <td><input class="regular-text" name="first_name" type="text" id="first_name" value="<?php echo esc_attr($first_name); ?>" /></td>
</tr>
<tr class="swpm-admin-edit-last-name">
    <th scope="row"><label for="last_name"><?php echo SwpmUtils::_('Last Name') ?> </label></th>
    <td><input class="regular-text" name="last_name" type="text" id="last_name" value="<?php echo esc_attr($last_name); ?>" /></td>
</tr>
<tr class="swpm-admin-edit-gender">
    <th scope="row"><label for="gender"><?php echo SwpmUtils::_('Gender'); ?></label></th>
    <td><select class="regular-text" name="gender" id="gender">
            <?php echo SwpmUtils::gender_dropdown($gender) ?>
        </select>
    </td>
</tr>
<tr class="swpm-admin-edit-phone">
    <th scope="row"><label for="phone"><?php echo SwpmUtils::_('Phone') ?> </label></th>
    <td><input class="regular-text" name="phone" type="text" id="phone" value="<?php echo esc_attr($phone); ?>" /></td>
</tr>
<tr class="swpm-admin-edit-address-street">
    <th scope="row"><label for="address_street"><?php echo SwpmUtils::_('Street') ?> </label></th>
    <td><input class="regular-text" name="address_street" type="text" id="address_street" value="<?php echo esc_attr($address_street); ?>" /></td>
</tr>
<tr class="swpm-admin-edit-address-city">
    <th scope="row"><label for="address_city"><?php echo SwpmUtils::_('City') ?> </label></th>
    <td><input class="regular-text" name="address_city" type="text" id="address_city" value="<?php echo esc_attr($address_city); ?>" /></td>
</tr>
<tr class="swpm-admin-edit-address-state">
    <th scope="row"><label for="address_state"><?php echo SwpmUtils::_('State') ?> </label></th>
    <td><input class="regular-text" name="address_state" type="text" id="address_state" value="<?php echo esc_attr($address_state); ?>" /></td>
</tr>
<tr class="swpm-admin-edit-address-zipcode">
    <th scope="row"><label for="address_zipcode"><?php echo SwpmUtils::_('Zipcode') ?> </label></th>
    <td><input class="regular-text" name="address_zipcode" type="text" id="address_zipcode" value="<?php echo esc_attr($address_zipcode); ?>" /></td>
</tr>
<tr class="swpm-admin-edit-address-country">
    <th scope="row"><label for="country"><?php echo SwpmUtils::_('Country') ?> </label></th>
    <td><select class="regular-text" id="country" name="country"><?php echo SwpmMiscUtils::get_countries_dropdown($country) ?></select></td>
</tr>
<tr class="swpm-admin-edit-company">
    <th scope="row"><label for="company_name"><?php echo SwpmUtils::_('Company') ?></label></th>
    <td><input name="company_name" type="text" id="company_name" class="regular-text" value="<?php echo esc_attr($company_name); ?>" /></td>
</tr>      
<tr class="swpm-admin-edit-member-since">
    <th scope="row"><label for="member_since"><?php echo SwpmUtils::_('Member Since') ?> </label></th>
    <td><input class="regular-text" name="member_since" type="text" id="member_since" value="<?php echo esc_attr($member_since); ?>" /></td>
</tr>
