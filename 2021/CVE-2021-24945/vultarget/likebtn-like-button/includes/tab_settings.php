<?php

// Settings tab
function likebtn_admin_settings() {

    //global $likebtn_plans;
    global $likebtn_sync_intervals;

    wp_enqueue_script('likebtn-jquery-ui', _likebtn_get_public_url().'js/jquery/jquery-ui/jquery-ui.js', array('jquery'), LIKEBTN_VERSION);
    wp_enqueue_style('likebtn-jquery-ui-css', _likebtn_get_public_url().'css/jquery/jquery-ui/jquery-ui.css', false, LIKEBTN_VERSION, 'all');
    wp_enqueue_script('likebtn-durationpicker', _likebtn_get_public_url().'js/jquery/jquery.ui.durationPicker.js', array('jquery', 'likebtn-jquery-ui'), LIKEBTN_VERSION);
    wp_enqueue_style('likebtn-durationpicker', _likebtn_get_public_url().'css/jquery/jquery.ui.durationPicker.css', array(), LIKEBTN_VERSION, 'all');

    // reset sync interval
    if (!get_option('likebtn_account_email') || !get_option('likebtn_account_api_key') || !get_option('likebtn_site_id')) {
        update_option('likebtn_sync_inerval', '');
    }

    // If account data has changed, refresh the plan
    $account_data_hash = md5(get_option('likebtn_account_email').get_option('likebtn_account_api_key').get_option('likebtn_site_id'));

    if (!get_option('likebtn_account_data_hash') || $account_data_hash != get_option('likebtn_account_data_hash')) {
        update_option('likebtn_account_data_hash', $account_data_hash);
        // run plan sunchronization
        require_once(dirname(__FILE__) . '/../likebtn_like_button.class.php');
        $likebtn = new LikeBtnLikeButton();
        $likebtn->syncPlan();
        // run synchronization
        $likebtn->syncVotes();
    }

    // Save IP vote interval if changed
    $ipvi = get_option('likebtn_ipvi');
    if ($ipvi === '' || $ipvi === NULL) {
        // Not initicalized yet
        $ipvi = LIKEBTN_IP_VOTE_INTERVAL;
    }
    if (_likebtn_is_stat_enabled() && $ipvi != get_option('likebtn_ipvi_hash')) {
        require_once(dirname(__FILE__) . '/../likebtn_like_button.class.php');
        $likebtn = new LikeBtnLikeButton();
        $ipvi_result = $likebtn->setIpvi($ipvi);

        if ($ipvi_result['result'] == 'success') {
            update_option('likebtn_ipvi_hash', $ipvi);
        } else {
            _likebtn_add_notice(array(
                'msg' => __('Error occured saving IP vote interval: '.$ipvi_result['message'], 'likebtn-like-button'),
                'class' => 'error'
            ));
        }
    }

    // Save initial likes if changed
    $init_l_from = (int)get_option('likebtn_init_l_from');
    $init_l_to = (int)get_option('likebtn_init_l_to');

    if (_likebtn_is_stat_enabled()) {
        if ($init_l_from != (int)get_option('likebtn_init_l_from_prev') ||
            $init_l_to != (int)get_option('likebtn_init_l_to_prev'))
        {
            require_once(dirname(__FILE__) . '/../likebtn_like_button.class.php');
            $likebtn = new LikeBtnLikeButton();
            $init_l_result = $likebtn->setInitL($init_l_from, $init_l_to);

            if ($init_l_result['result'] == 'success') {
                if (isset($init_l_result['response']['init_l_from'])) {
                    $init_l_from = (int)$init_l_result['response']['init_l_from'];
                }
                if (isset($init_l_result['response']['init_l_to'])) {
                    $init_l_to = (int)$init_l_result['response']['init_l_to'];
                }
                update_option('likebtn_init_l_from', $init_l_from);
                update_option('likebtn_init_l_to', $init_l_to);

                update_option('likebtn_init_l_from_prev', $init_l_from);
                update_option('likebtn_init_l_to_prev', $init_l_to);
            } else {
                update_option('likebtn_init_l_from', get_option('likebtn_init_l_from_prev'));
                update_option('likebtn_init_l_to', get_option('likebtn_init_l_to_prev'));

                _likebtn_add_notice(array(
                    'msg' => __('Error occured saving initial number of likes:'.' '.$init_l_result['message'], 'likebtn-like-button'),
                    'class' => 'error'
                ));
            }
        }
    }

    $export_config = likebtn_get_config();

    likebtn_admin_header();
    ?>
    <script type="text/javascript">
        var likebtn_msg_error = '<?php _e("Error occured:", 'likebtn-like-button'); ?><br/>● <?php _e('Make sure that <a href="https://wordpress.org/support/topic/howto-disable-php-errors-using-htaccess-on-a-shared-hosting" target="_blank">displaying errors is disabled</a>', 'likebtn-like-button'); ?><br/>● <?php _e("Disable WP HTTP Compression plugin if you have it enabled", 'likebtn-like-button'); ?>';
        jQuery(document).ready(function() {
            scriptSettings();
        });
    </script>
    <div class="likebtn_subpage">
        <form method="post" action="options.php" novalidate="novalidate" autocomplete="off">
            <?php settings_fields('likebtn_settings'); ?>

            <div class="postbox likebtn_postbox">
                <h3><?php _e('Account Details', 'likebtn-like-button'); ?></h3>
                <div class="inside">
                    <p>
                        <?php /*_e('To get your account data:', 'likebtn-like-button'); */ ?>
                        <ol>
                            <li>
                                <?php echo strtr(
                                    __('Register on <a href="%url_register%">LikeBtn.com</a>', 'likebtn-like-button'), 
                                    array('%url_register%'=>"javascript:likebtnPopup('".__('http://likebtn.com/en/customer.php/register/', 'likebtn-like-button')."');void(0)")); 
                                ?>
                            </li>
                            <li>
                                <?php if (!is_multisite()): ?>
                                    <?php echo strtr(
                                        __('Add your website to your account on <a href="%url_websites%">Websites</a> page.', 'likebtn-like-button'), 
                                        array('%url_websites%'=>"javascript:likebtnPopup('".__('http://likebtn.com/en/customer.php/websites', 'likebtn-like-button')."');void(0)")); 
                                    ?>
                                <?php else: ?>
                                    <?php echo strtr(
                                        __('Add each website of the network as a separate website to your account on <a href="%url_websites%">Websites</a> page. If using path-based (sub-directories) multisite network please make sure to specify the "Subdirectory" when adding each website. Also see <a href="%bulk_discount%">bulk discount pricing</a>.', 'likebtn-like-button'), 
                                        array('%url_websites%'=>"javascript:likebtnPopup('".__('http://likebtn.com/en/customer.php/websites', 'likebtn-like-button')."');void(0)", '%bulk_discount%'=>"javascript:likebtnPopup('".__('https://likebtn.com/en/', 'likebtn-like-button')."pricing#bulk_discount_pricing');void(0)")); 
                                    ?>
                                    
                                <?php endif ?>
                            </li>
                            <li>
                                <?php echo __('Check and save data.', 'likebtn-like-button'); ?>
                            </li>
                        </ol>
                    </p>
                    <input class="button-primary likebtn_button_green" type="button" value="<?php _e('Get Account Data', 'likebtn-like-button'); ?>" onclick="likebtnGetAccountData('<?php _e('http://likebtn.com/en/customer.php/register/', 'likebtn-like-button') ?>')" />
                    <?php /* For add_domain */ ?>
                    <?php if (get_option('likebtn_acc_data_correct') != '1'): ?>
                        <div style="display:none">
                            <?php echo _likebtn_get_markup(LIKEBTN_ENTITY_POST, 'demo', array(), '', true, true, true) ?>
                        </div>
                    <?php endif ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('E-mail', 'likebtn-like-button'); ?></label></th>
                            <td>
                                <input type="text" name="likebtn_account_email" value="<?php echo htmlspecialchars(get_option('likebtn_account_email')) ?>" onkeyup="accountChange(this)" class="likebtn_account likebtn_input" id="likebtn_account_email_input"/><br/>
                                <p class="description"><?php _e('Your LikeBtn.com account email. Can be found on <a href="http://likebtn.com/en/customer.php/profile/edit" target="_blank">Profile</a> page', 'likebtn-like-button') ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('API key', 'likebtn-like-button'); ?></label></th>
                            <td>
                                <input type="text" name="likebtn_account_api_key" value="<?php echo htmlspecialchars(get_option('likebtn_account_api_key')) ?>" onkeyup="accountChange(this)" class="likebtn_account likebtn_input" id="likebtn_account_api_key_input" maxlength="32" /><br/>
                                <p class="description"><?php _e('Your website API key on LikeBtn.com. Can be obtained on <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites</a> page', 'likebtn-like-button') ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Site ID', 'likebtn-like-button'); ?></label></th>
                            <td>
                                <input type="text" name="likebtn_site_id" value="<?php echo htmlspecialchars(get_option('likebtn_site_id')) ?>" class="likebtn_input" id="likebtn_site_id_input" maxlength="24" /><br/>
                                <p class="description">
                                    <?php _e('Your Site ID on LikeBtn.com. Can be obtained on <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites</a> page.', 'likebtn-like-button') ?> <?php _e('If your website has multiple addresses or you are developing a website on a local server and planning to move it to a live domain, you can add domains to the website <a href="http://likebtn.com/en/customer.php/websites" target="_blank">here</a>.', 'likebtn-like-button') ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="middle">
                            <th scope="row">&nbsp;</th>
                            <td class="likebtn_mid_row">
                                <input class="button-primary likebtn_s_btn" type="button" value="<?php _e('Check Account Data', 'likebtn-like-button'); ?>" onclick="checkAccount('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" /> &nbsp;<strong class="likebtn_check_account_container"></strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', 'likebtn-like-button'); ?>" />
            </p>
            <br/>

            <?php if (get_option('likebtn_plan') < LIKEBTN_PLAN_PRO): ?>
                <strong class="likebtn_error">
                    <?php echo strtr(
                        __('Website tariff plan does not allow to synchronize likes into your database – <a href="%url_upgrade%">upgrade</a> your website to PRO or higher plan.', 'likebtn-like-button'), 
                        array('%url_upgrade%'=>"javascript:likebtnPopup('".__('https://likebtn.com/en/pricing', 'likebtn-like-button')."');void(0)")); 
                    ?>
                </strong>
                <br/><br/>
            <?php endif ?>

            <div class="postbox likebtn_postbox" id="synchronization">
                <h3><?php _e('Synchronization', 'likebtn-like-button'); ?> <i class="premium_feature" title="PRO / VIP / ULTRA"></i></h3>
                <div class="inside">
                    <p class="description">
                        <?php _e('Enable synchronization of likes from LikeBtn.com into your database to:', 'likebtn-like-button'); ?><br/>
                        &nbsp;&nbsp;● <?php _e('View statistics on Statistics tab.', 'likebtn-like-button'); ?><br/>
                        &nbsp;&nbsp;● <?php _e('Sort content by likes.', 'likebtn-like-button'); ?><br/>
                        &nbsp;&nbsp;● <?php _e('Use most liked content widget and shortcode.', 'likebtn-like-button'); ?><br/>
                    </p>
                    <table class="form-table">
                        <tr valign="middle">
                            <th scope="row"><label><?php _e('Synchronization status', 'likebtn-like-button'); ?></label>

                            </th>
                            <td class="likebtn_sync_cntr <?php if (_likebtn_is_stat_enabled()): ?>likebtn_sync_ena_flag<?php else: ?>likebtn_sync_dis_flag<?php endif ?> likebtn_mid_row">
                                <input type="hidden" name="likebtn_sync_inerval" value="<?php echo get_option('likebtn_sync_inerval'); ?>" />
                                <p>
                                    <strong class="likebtn_success likebtn_sync_ena"><?php _e('Enabled', 'likebtn-like-button'); ?></strong>
                                    <strong class="likebtn_error likebtn_sync_dis"><?php _e('Disabled', 'likebtn-like-button'); ?></strong> 
                                </p>
                                <?php if (!_likebtn_is_stat_enabled()): ?>
                                    <p class="description likebtn_sync_dis"><?php _e('Please ugrade at least to PRO, enter, check and save account data above', 'likebtn-like-button'); ?></p>
                                <?php endif ?>
                                <div <?php if (!get_option('likebtn_account_email') || !get_option('likebtn_account_api_key') || !get_option('likebtn_site_id')): ?>style="display:none"<?php endif ?>>
                                    <br/>
                                    <input class="button-primary likebtn_button_green likebtn_sync_dis" type="button" value="<?php _e('Enable Sync', 'likebtn-like-button'); ?>" onclick="testSync('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" />

                                    <input class="button-primary likebtn_sync_ena likebtn_s_btn" type="button" value="<?php _e('Test Sync', 'likebtn-like-button'); ?>" onclick="testSync('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" /> 
                                    &nbsp;<strong class="likebtn_test_sync_container"></strong>
                                    
                                    <div class="liketbtn_mansync_wr" style="display:none">
                                        <br/><br/>
                                        <input class="button-secondary likebtn_ttip" type="button" value="<?php _e('Run Full Sync Manually', 'likebtn-like-button'); ?>" onclick="manualSync('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" title="<?php _e("ATTENTION: Use this feature carefully since full synchronization may affect your website performance. If you don't experience any problems with likes synchronization better to avoid using this feature.", 'likebtn-like-button') ?>" /> &nbsp;<strong class="likebtn_manual_sync_container"><img src="<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif" class="hidden"/></strong>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Diagnostics', 'likebtn-like-button'); ?></label>
                                <i class="likebtn_help" title="​<?php _e('Check if your server configuration satisfies the Like button plugin requirements.', 'likebtn-like-button'); ?>">&nbsp;</i>
                            </th>
                            <td class="likebtn_mid_row">
                                <input class="button-secondary likebtn_ttip" type="button" value="<?php _e('Check the System', 'likebtn-like-button'); ?>" onclick="systemCheck('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" /> &nbsp;<strong class="likebtn_sc_container"></strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', 'likebtn-like-button'); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox" id="ip_vote_interval">
                <h3><?php _e('IP Address Vote Interval', 'likebtn-like-button'); ?> <i class="premium_feature" title="ULTRA"></i></h3>
                <div class="inside">
                    <p class="description">
                        <?php _e('If you\'ve set up voters identification by Username for a particular post type on Buttons tab, this IP address vote interval option is ignored for that post type.' , 'likebtn-like-button'); ?><br/>
                    </p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Interval', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <?php
                                    $ipvi_custom = false;
                                    if ((int)$ipvi != 0 && (int)$ipvi != LIKEBTN_MAX_IP_VOTE_INTERVAL) {
                                        $ipvi_custom = true;   
                                    }
                                ?>
                                <select class="disabled" onchange="ipviSelect(this)" onclick="ipviSelect(this)" disabled="disabled" id="ipvi_select">
                                    <option value="<?php echo LIKEBTN_MAX_IP_VOTE_INTERVAL; ?>" <?php selected(LIKEBTN_MAX_IP_VOTE_INTERVAL, $ipvi); ?> ><?php _e('Allow ONE vote per IP address', 'likebtn-like-button') ?></option>
                                    <option value="0" <?php selected(0, (int)$ipvi); ?> ><?php _e('Allow UNLIMITED votes from the same IP address', 'likebtn-like-button') ?></option>
                                    <option value="-1" <?php if ($ipvi_custom): ?>selected="selected"<?php endif ?> ><?php _e('Custom IP address vote interval', 'likebtn-like-button') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top" class="<?php if (!$ipvi_custom): ?>hidden<?php endif ?> ipvi_custom">
                            <th scope="row">&nbsp;</label>
                            </th>
                            <td>
                                <div id="ipvi_duration"></div>
                            </td>
                        </tr>
                        <tr valign="top" class="<?php if (!$ipvi_custom): ?>hidden<?php endif ?> ipvi_custom">
                            <th scope="row">&nbsp;</label>
                            </th>
                            <td>
                                <input type="number" min="1" max="31557599" value="<?php echo (int)$ipvi ?>" onkeyup="ipviChanged(this)" class="disabled" id="ipvi_secs" readonly="readonly" /> <strong><?php _e('total seconds', 'likebtn-like-button'); ?></strong>
                                <input type="hidden" name="likebtn_ipvi" id="ipvi_secs_hidden" value="<?php echo (int)$ipvi ?>" />
                            </td>
                        </tr>
                        <tr valign="top" id="likebtn_ipvi_change">
                            <th scope="row"></th>
                            <td class="likebtn_mid_row">
                                <input class="button-secondary" type="button" value="<?php _e('Change', 'likebtn-like-button'); ?>" onclick="ipviChange('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" /> &nbsp;<strong class="likebtn_ipvi_change_container"></strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" id="gdpr" type="submit" name="Save" value="<?php _e('Save All Changes', 'likebtn-like-button'); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox" id="initial_likes">
                <h3><?php _e('Initial number of likes on buttons', 'likebtn-like-button'); ?> <i class="premium_feature" title="ULTRA"></i></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Randomize likes', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <?php _e('From', 'likebtn-like-button'); ?>:
                                <input type="number" value="<?php echo (int)get_option('likebtn_init_l_from'); ?>" name="likebtn_init_l_from" min="0" max="9999999" <?php if (!likebtn_check_plan(LIKEBTN_PLAN_ULTRA) || !_likebtn_is_stat_enabled()): ?>disabled="disabled"<?php endif ?>/>
                                &nbsp;
                                <?php _e('To', 'likebtn-like-button'); ?>:
                                <input type="number" value="<?php echo (int)get_option('likebtn_init_l_to'); ?>" name="likebtn_init_l_to" min="0" max="9999999" <?php if (!likebtn_check_plan(LIKEBTN_PLAN_ULTRA) || !_likebtn_is_stat_enabled()): ?>disabled="disabled"<?php endif ?>/>
                            </td>
                        </tr>
                     
                        <tr>
                            <td colspan="2">
                                <p class="notice update-nag">
                                    <?php _e('It is not recommended to use this feature if you need to sort posts by likes, as sorting will not work properly.', 'likebtn-like-button') ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', 'likebtn-like-button'); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox">
                <h3><?php _e('GDPR Compliance', 'likebtn-like-button'); ?></h3>
                <div class="inside">
                    <p class="description">
                        <?php _e("To be GDPR compliant plugin stores IP addresses in a hashed anonymized form. If you don't need to be GDPR compliant and want to see actual IP addresses, just uncheck this checkbox.", 'likebtn-like-button'); ?>
                    </p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Be GDPR compliant', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <p class="description">
                                    <input type="checkbox" name="likebtn_gdpr" value="1" <?php checked('1', get_option('likebtn_gdpr')); ?> />
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', 'likebtn-like-button'); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox">
                <h3><?php _e('Notify Admin by Email on New Votes', 'likebtn-like-button'); ?> <i class="premium_feature" title="PRO"></i></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Enabled', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <p class="description">
                                    <input type="checkbox" name="likebtn_notify_enabled" value="1" <?php checked('1', get_option('likebtn_notify_enabled')); ?> <?php if (!likebtn_check_plan(LIKEBTN_PLAN_PRO) || !_likebtn_is_stat_enabled()): ?>class="disabled"<?php endif ?> <?php if (likebtn_check_plan(LIKEBTN_PLAN_PRO) && _likebtn_is_stat_enabled()): ?>onclick="jQuery('#notify_container').toggleClass('hidden')"<?php else: ?>onclick="return false;"<?php endif ?>/> 
                                    <?php _e('Send email notification every time there is a new vote', 'likebtn-like-button'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>

                    <?php if (!_likebtn_is_stat_enabled()): ?>
                        <p class="likebtn_error"><?php _e('Configure Synchronization in order to use this feature', 'likebtn-like-button'); ?></p>
                    <?php endif ?>

                    <table class="form-table <?php if (!likebtn_check_plan(LIKEBTN_PLAN_PRO) || get_option('likebtn_notify_enabled') != '1' || !_likebtn_is_stat_enabled()): ?>hidden<?php endif ?>" id="notify_container">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Send email to', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <input name="likebtn_notify_to" class="likebtn_input" value="<?php echo htmlspecialchars(get_option('likebtn_notify_to')); ?>" />
                                <p class="description">
                                    <?php _e('Comma separated emails to send notifications to.', 'likebtn-like-button'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Email from', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <input name="likebtn_notify_from" class="likebtn_input" value="<?php echo htmlspecialchars(get_option('likebtn_notify_from')); ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Email subject', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <input name="likebtn_notify_subject" class="likebtn_input" value="<?php echo htmlspecialchars(get_option('likebtn_notify_subject')); ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Notification text', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <textarea name="likebtn_notify_text" class="likebtn_input" rows="5"><?php echo htmlspecialchars(get_option('likebtn_notify_text')); ?></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">&nbsp;</th>
                            <td class="likebtn_mid_row">
                                <input class="button-secondary" type="button" value="<?php _e('Send Test Notification', 'likebtn-like-button'); ?>" onclick="sendTestVoteNotification('<?php echo _likebtn_get_public_url() ?>img/ajax_loader.gif')" />&nbsp; <strong class="likebtn_vn_message"></strong>
                                    <div class="likebtn_vn_container margin-top"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', 'likebtn-like-button'); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox">
                <h3><?php _e('Sorting', 'likebtn-like-button'); ?></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('bbPress replies sorting', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <select name="likebtn_bbp_replies_sort">
                                    <option value="" <?php selected('', get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Default', 'likebtn-like-button') ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_LIKES; ?>" <?php selected(LIKEBTN_META_KEY_LIKES, get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Likes', 'likebtn-like-button') ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_DISLIKES; ?>" <?php selected(LIKEBTN_META_KEY_DISLIKES, get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Dislikes', 'likebtn-like-button') ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES; ?>" <?php selected(LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Likes minus dislikes', 'likebtn-like-button') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('BuddyPress activities sorting', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <select name="likebtn_bp_act_sort">
                                    <option value="" <?php selected('', get_option('likebtn_bbp_replies_sort')); ?> ><?php _e('Default', 'likebtn-like-button') ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_LIKES; ?>" <?php selected(LIKEBTN_META_KEY_LIKES, get_option('likebtn_bp_act_sort')); ?> ><?php _e('Likes', 'likebtn-like-button') ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_DISLIKES; ?>" <?php selected(LIKEBTN_META_KEY_DISLIKES, get_option('likebtn_bp_act_sort')); ?> ><?php _e('Dislikes', 'likebtn-like-button') ?></option>
                                    <option value="<?php echo LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES; ?>" <?php selected(LIKEBTN_META_KEY_LIKES_MINUS_DISLIKES, get_option('likebtn_bp_act_sort')); ?> ><?php _e('Likes minus dislikes', 'likebtn-like-button') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><a href="<?php _e('http://likebtn.com/en/', 'likebtn-like-button') ?>wordpress-like-button-plugin#sort_posts_by_likes" target="_blank"><?php _e('How to sort other types of content?', 'likebtn-like-button') ?></a></td>
                        </tr>
                    </table>
                </div>
            </div>

            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', 'likebtn-like-button'); ?>" />
            </p>
            <br/>

            <div class="postbox likebtn_postbox">
                <h3><?php _e('Miscellaneous', 'likebtn-like-button'); ?></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Custom CSS', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <textarea name="likebtn_css" class="likebtn_input" rows="4"><?php echo htmlspecialchars(get_option('likebtn_css')); ?></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Custom JavaScript', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                <textarea name="likebtn_js" class="likebtn_input" rows="4"><?php echo htmlspecialchars(get_option('likebtn_js')); ?></textarea>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php _e('Show info notices', 'likebtn-like-button'); ?></label>
                            </th>
                            <td>
                                 <input type="checkbox" name="likebtn_info_message" value="1" <?php checked('1', get_option('likebtn_info_message')); ?> />

                                 <small class="description"><?php _e("Show notice instead of the button when it is restricted by tariff plan", 'likebtn-like-button'); ?></small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <p>
                <input class="button-primary" type="submit" name="Save" value="<?php _e('Save All Changes', 'likebtn-like-button'); ?>" />
            </p>

        </form>
        <br/>

        <div class="postbox likebtn_postbox">
            <h3><?php _e('Configuration Import & Export', 'likebtn-like-button'); ?></h3>
            <div class="inside">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label><?php _e('Export configuration', 'likebtn-like-button'); ?></label>
                        </th>
                        <td>
                            <p class="description">
                                <?php _e('This box contains all the Like Button configuration encoded as a string so you can easily copy it.'); ?>
                            </p>
                            <textarea class="likebtn_input" rows="4"><?php echo htmlspecialchars($export_config); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label><?php _e('Import configuration', 'likebtn-like-button'); ?></label>
                        </th>
                        <td>
                            <form method="post" action="" onsubmit="return importSubmit('<?php _e('Current settings and buttons configuration will be overwritten. Please type IMPORT in order to import configuration:', 'likebtn-like-button'); ?>')">
                                <p class="description">
                                    <?php _e('Paste your Like Button configuration here and the plugin will load it into the database.'); ?>
                                </p>
                                <textarea class="likebtn_input" rows="4" name="likebtn_import_config"></textarea>
                                <p class="notice update-nag"> 
                                    <?php _e('On importing current settings and buttons configuration will be overwritten. Votes and statistics will be preserved.', 'likebtn-like-button'); ?>
                                </p>
                                <input class="button-primary" type="submit" value="<?php _e('Import', 'likebtn-like-button'); ?>"/>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <br/>
        <div class="postbox likebtn_postbox">
            <h3><?php _e('Resetting', 'likebtn-like-button'); ?></h3>
            <div class="inside">
                <table class="form-table">
                    <tr valign="top">
                        <td>
                            <form method="post" action="" id="likebtn_fr_form">
                                <input type="hidden" name="likebtn_full_reset" value="" />
                                <input type="button" class="button-secondary likebtn_ttip" onclick="likebtnFullReset('<?php _e("Votes and stats will be removed permanently and can not be restored. If you want to continue please enter RESET:", 'likebtn-like-button') ?>')" value="<?php _e('Reset Votes & Stats', 'likebtn-like-button'); ?>" title="<?php _e('Remove ALL votes and stats', 'likebtn-like-button'); ?>">
                            </form>
                            <?php if (is_multisite()): ?>
                                <p class="notice update-nag"> 
                                    <?php _e('ATTENTION: Resetting will reset votes on all the websites of the multisite network.', 'likebtn-like-button'); ?>
                                </p>
                            <?php endif ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if (!get_option('likebtn_account_email') || !get_option('likebtn_account_api_key')): ?>
            <br/>
            <?php echo _likebtn_get_markup('post', 'demo', array(), '', false, false); ?>
        <?php endif ?>

    </div>
    <?php

    _likebtn_admin_footer();
}
