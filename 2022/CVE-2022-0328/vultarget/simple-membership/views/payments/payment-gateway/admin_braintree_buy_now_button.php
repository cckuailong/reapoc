<?php
/* * ***************************************************************
 * Render the new Braintree payment button creation interface
 * ************************************************************** */

/*
  I've optimized render function in order to avoid code duplication.
  This function is responsible for rendering either Save or Edit button interface depending on the parameters.
  It's much easier to modify it as the changes (descriptions update etc) are reflected in both forms at once.
 */

function render_save_edit_braintree_button_interface($bt_opts, $is_edit_mode = false) {

    ?>

    <div class="swpm-orange-box">
        View the <a target="_blank" href="https://simple-membership-plugin.com/create-braintree-buy-now-button-for-membership-payment/">documentation</a>&nbsp;
        to learn how to create and use a Braintree Buy Now payment button.
    </div>

    <div class="postbox">
        <h3 class="hndle"><label for="title"><?php echo SwpmUtils::_('Braintree Buy Now Button Configuration'); ?></label></h3>
        <div class="inside">

            <form id="braintree_button_config_form" method="post">
                <input type="hidden" name="button_type" value="<?php echo $bt_opts['button_type']; ?>">
                <?php if (!$is_edit_mode) { ?>
                    <input type="hidden" name="swpm_button_type_selected" value="1">
                <?php } ?>

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
                    <?php if ($is_edit_mode) { ?>
                        <tr valign="top">
                            <th scope="row"><?php echo SwpmUtils::_('Button ID'); ?></th>
                            <td>
                                <input type="text" size="10" name="button_id" value="<?php echo $bt_opts['button_id']; ?>" readonly required />
                                <p class="description">This is the ID of this payment button. It is automatically generated for you and it cannot be changed.</p>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Button Title'); ?></th>
                        <td>
                            <input type="text" size="50" name="button_name" value="<?php echo ($is_edit_mode ? $bt_opts['button_name'] : ''); ?>" required />
                            <p class="description">Give this membership payment button a name. Example: Gold membership payment</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Membership Level'); ?></th>
                        <td>
                            <select id="membership_level_id" name="membership_level_id">
                                <?php echo ($is_edit_mode ? SwpmUtils::membership_level_dropdown($bt_opts['membership_level_id']) : SwpmUtils::membership_level_dropdown()); ?>
                            </select>
                            <p class="description">Select the membership level this payment button is for.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Payment Amount'); ?></th>
                        <td>
                            <input type="text" size="6" name="payment_amount" value="<?php echo ($is_edit_mode ? $bt_opts['payment_amount'] : ''); ?>" required />
                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th colspan="2"><div class="swpm-grey-box"><?php echo SwpmUtils::_('Braintree API key and account details. You can get this from your Braintree account.'); ?></div></th>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Merchant ID'); ?></th>
                        <td>
                            <input type="text" size="50" name="braintree_merchant_acc_id" value="<?php echo ($is_edit_mode ? $bt_opts['braintree_merchant_acc_id'] : ''); ?>" required/>
                            <p class="description">Enter you Braintree Merchant ID.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Public Key'); ?></th>
                        <td>
                            <input type="text" size="50" name="braintree_public_key" value="<?php echo ($is_edit_mode ? $bt_opts['braintree_public_key'] : ''); ?>" required />
                            <p class="description">Enter your Braintree public key.</p>
                        </td>
                    </tr>                    
                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Private Key'); ?></th>
                        <td>
                            <input type="text" size="50" name="braintree_private_key" value="<?php echo ($is_edit_mode ? $bt_opts['braintree_private_key'] : ''); ?>" required />
                            <p class="description">Enter your Braintree private key.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Merchant Account ID'); ?></th>
                        <td>
                            <input type="text" size="50" name="braintree_merchant_acc_name" value="<?php echo ($is_edit_mode ? $bt_opts['braintree_merchant_acc_name'] : ''); ?>" />
                            <p class="description">Enter your Braintree Merchant Account ID (This is different than the Merchant ID you specified above). Please note currency depends on the Merchant Account ID you specify. Leave empty to use the default one.
                                <?php
                                if ($is_edit_mode) {
                                    if (isset($bt_opts['currency_code']) && $bt_opts['currency_code'] != '') {
                                        ?>
                                        <br />The currency for this button is set to: <strong><?php echo $bt_opts['currency_code']; ?></strong>
                                        <?php
                                    }
                                }
                                ?>
                            </p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th colspan="2"><div class="swpm-grey-box"><?php echo SwpmUtils::_('The following details are optional.'); ?></div></th>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Return URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="return_url" value="<?php echo ($is_edit_mode ? $bt_opts['return_url'] : ''); ?>" />
                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>
                        </td>
                    </tr>

                </table>

                <p class="submit">
                    <?php wp_nonce_field('swpm_admin_add_edit_braintree_buy_now_btn','swpm_admin_add_edit_braintree_buy_now_btn') ?>                           
                    <input type="submit" name="swpm_braintree_buy_now_<?php echo ($is_edit_mode ? 'edit' : 'save'); ?>_submit" class="button-primary" value="<?php echo SwpmUtils::_('Save Payment Data'); ?>" >
                </p>

            </form>

        </div>
    </div>
    <?php
}

/* * ***************************************************************
 * Render save Braintree Buy now payment button interface
 * ************************************************************** */
add_action('swpm_create_new_button_for_braintree_buy_now', 'swpm_create_new_braintree_buy_now_button');

function swpm_create_new_braintree_buy_now_button() {

    //Test for PHP v5.4.0 or show error and don't show the remaining interface.
    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        //The server is using at least PHP version 5.4.0
        //Can use Braintree gateway library
    } else {
        //This server can't handle Braintree library
        echo '<div class="swpm-red-box">';
        echo '<p>The Braintree payment gateway library requires at least PHP 5.4.0. Your server is using a very old version of PHP that Braintree does not support.</p>';
        echo '<p>Request your hosting provider to upgrade your PHP to a more recent version then you will be able to use the Braintree gateway.<p>';
        echo '</div>';
        return;
    }

    $bt_opts = array(
        'button_type' => sanitize_text_field($_REQUEST['button_type']),
    );

    render_save_edit_braintree_button_interface($bt_opts);
}

/* * ***************************************************************
 * Render edit Braintree Buy now payment button interface
 * ************************************************************** */
add_action('swpm_edit_payment_button_for_braintree_buy_now', 'swpm_edit_braintree_buy_now_button');

function swpm_edit_braintree_buy_now_button() {

    //Retrieve the payment button data and present it for editing.    

    $button_id = sanitize_text_field($_REQUEST['button_id']);
    $button_id = absint($button_id);

    $button = get_post($button_id); //Retrieve the CPT for this button
    //$button_image_url = get_post_meta($button_id, 'button_image_url', true);

    $bt_opts = array(
        'button_id' => $button_id,
        'button_type' => sanitize_text_field($_REQUEST['button_type']),
        'button_name' => $button->post_title,
        'membership_level_id' => get_post_meta($button_id, 'membership_level_id', true),
        'payment_amount' => get_post_meta($button_id, 'payment_amount', true),
        'braintree_merchant_acc_id' => get_post_meta($button_id, 'braintree_merchant_acc_id', true),
        'braintree_merchant_acc_name' => get_post_meta($button_id, 'braintree_merchant_acc_name', true),
        'braintree_public_key' => get_post_meta($button_id, 'braintree_public_key', true),
        'braintree_private_key' => get_post_meta($button_id, 'braintree_private_key', true),
        'return_url' => get_post_meta($button_id, 'return_url', true),
        'currency_code' => get_post_meta($button_id, 'currency_code', true),
    );

    render_save_edit_braintree_button_interface($bt_opts, true);
}

/*
 * Process submission and save the new or edit Braintree Buy now payment button data
 */

// This function tries to get Merchant Account currency
function swpm_get_braintree_default_currency($params) {
    $settings = SwpmSettings::get_instance();
    $sandbox_enabled = $settings->get_value('enable-sandbox-testing');

    if ($sandbox_enabled) {
        $braintree_env = "sandbox";
    } else {
        $braintree_env = "production";
    }

    require_once(SIMPLE_WP_MEMBERSHIP_PATH . 'lib/braintree/lib/autoload.php');
    try {
        Braintree_Configuration::environment($braintree_env);
        Braintree_Configuration::merchantId($params['merc_id']);
        Braintree_Configuration::publicKey($params['public_key']);
        Braintree_Configuration::privateKey($params['private_key']);
        $merchantAccount = Braintree_MerchantAccount::find($params['merc_name']);
    } catch (Exception $e) {
        // Something went wrong. We actually don't care what exactly happened, so we just return ''
        return '';
    }
    return $merchantAccount->currencyIsoCode;
}

add_action('swpm_create_new_button_process_submission', 'swpm_save_edit_braintree_buy_now_button_data');
add_action('swpm_edit_payment_button_process_submission', 'swpm_save_edit_braintree_buy_now_button_data');

//I've merged two (save and edit events) into one

function swpm_save_edit_braintree_buy_now_button_data() {
    if (isset($_REQUEST['swpm_braintree_buy_now_save_submit'])) {
        //This is a Braintree buy now button save event.
        check_admin_referer( 'swpm_admin_add_edit_braintree_buy_now_btn', 'swpm_admin_add_edit_braintree_buy_now_btn' );

        $button_id = wp_insert_post(
                array(
                    'post_title' => sanitize_text_field($_REQUEST['button_name']),
                    'post_type' => 'swpm_payment_button',
                    'post_content' => '',
                    'post_status' => 'publish'
                )
        );

        $button_type = sanitize_text_field($_REQUEST['button_type']);
        add_post_meta($button_id, 'button_type', $button_type);
        add_post_meta($button_id, 'membership_level_id', sanitize_text_field($_REQUEST['membership_level_id']));
        add_post_meta($button_id, 'payment_amount', trim(sanitize_text_field($_REQUEST['payment_amount'])));
        add_post_meta($button_id, 'braintree_merchant_acc_name', trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_name'])));

        add_post_meta($button_id, 'braintree_merchant_acc_id', trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_id'])));
        add_post_meta($button_id, 'braintree_public_key', trim(sanitize_text_field($_REQUEST['braintree_public_key'])));
        add_post_meta($button_id, 'braintree_private_key', trim(sanitize_text_field($_REQUEST['braintree_private_key'])));

        add_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));
        //add_post_meta($button_id, 'button_image_url', trim(sanitize_text_field($_REQUEST['button_image_url'])));
        //Let's try to get currency code for current Merchant Account

        $params = array(
            'merc_name' => trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_name'])),
            'merc_id' => trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_id'])),
            'public_key' => trim(sanitize_text_field($_REQUEST['braintree_public_key'])),
            'private_key' => trim(sanitize_text_field($_REQUEST['braintree_private_key'])),
        );

        $currency_code = swpm_get_braintree_default_currency($params);

        add_post_meta($button_id, 'currency_code', $currency_code);

        //Redirect to the manage payment buttons interface
        $url = admin_url() . 'admin.php?page=simple_wp_membership_payments&tab=payment_buttons';
        SwpmMiscUtils::redirect_to_url($url);
    }

    if (isset($_REQUEST['swpm_braintree_buy_now_edit_submit'])) {
        //This is a Braintree buy now button edit event.
        check_admin_referer( 'swpm_admin_add_edit_braintree_buy_now_btn', 'swpm_admin_add_edit_braintree_buy_now_btn' );
        $button_id = sanitize_text_field($_REQUEST['button_id']);
        $button_id = absint($button_id);
        $button_type = sanitize_text_field($_REQUEST['button_type']);
        $button_name = sanitize_text_field($_REQUEST['button_name']);

        $button_post = array(
            'ID' => $button_id,
            'post_title' => $button_name,
            'post_type' => 'swpm_payment_button',
        );
        wp_update_post($button_post);

        $prev_merc_acc_name = get_post_meta($button_id, 'braintree_merchant_acc_name', true);
        $new_merc_acc_name = trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_name']));

        update_post_meta($button_id, 'button_type', $button_type);
        update_post_meta($button_id, 'membership_level_id', sanitize_text_field($_REQUEST['membership_level_id']));
        update_post_meta($button_id, 'payment_amount', trim(sanitize_text_field($_REQUEST['payment_amount'])));
        update_post_meta($button_id, 'braintree_merchant_acc_name', trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_name'])));

        update_post_meta($button_id, 'braintree_merchant_acc_id', trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_id'])));
        update_post_meta($button_id, 'braintree_public_key', trim(sanitize_text_field($_REQUEST['braintree_public_key'])));
        update_post_meta($button_id, 'braintree_private_key', trim(sanitize_text_field($_REQUEST['braintree_private_key'])));

        update_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));
        //update_post_meta($button_id, 'button_image_url', trim(sanitize_text_field($_REQUEST['button_image_url'])));

        if ($prev_merc_acc_name != $new_merc_acc_name) { //Looks like Merchant Account Name was modified, so let's try to request new currency code
            $params = array(
                'merc_name' => trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_name'])),
                'merc_id' => trim(sanitize_text_field($_REQUEST['braintree_merchant_acc_id'])),
                'public_key' => trim(sanitize_text_field($_REQUEST['braintree_public_key'])),
                'private_key' => trim(sanitize_text_field($_REQUEST['braintree_private_key'])),
            );

            $currency_code = swpm_get_braintree_default_currency($params);

            update_post_meta($button_id, 'currency_code', $currency_code);
        }

        echo '<div id="message" class="updated fade"><p>Payment button data successfully updated!</p></div>';
    }
}