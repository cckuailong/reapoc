<?php

$postedData = ppress_var(@$_POST['ppress_cc_data'], 'access_condition', []);

$who_can_access                      = ppressPOST_var('who_can_access', ppress_var($accessConditionData, 'who_can_access'), false, $postedData);
$access_roles                        = ppressPOST_var('access_roles', ppress_var($accessConditionData, 'access_roles', []), [], $postedData);
$noaccess_action                     = ppressPOST_var('noaccess_action', ppress_var($accessConditionData, 'noaccess_action'), false, $postedData);
$noaccess_action_message_type        = ppressPOST_var('noaccess_action_message_type', ppress_var($accessConditionData, 'noaccess_action_message_type'), false, $postedData);
$noaccess_action_message_custom      = ppressPOST_var('noaccess_action_message_custom', ppress_var($accessConditionData, 'noaccess_action_message_custom'), false, $postedData);
$noaccess_action_redirect_url        = ppressPOST_var('noaccess_action_redirect_url', ppress_var($accessConditionData, 'noaccess_action_redirect_url'), false, $postedData);
$noaccess_action_redirect_custom_url = ppressPOST_var('noaccess_action_redirect_custom_url', ppress_var($accessConditionData, 'noaccess_action_redirect_custom_url'), false, $postedData);
?>

<div class="pp-content-protection-access-box">
    <table class="form-table">
        <tbody>
        <tr id="pp-cc-accessible-row">
            <th>
                <label for="pp-cc-accessible"><?= esc_html__('Who can access the content?', 'wp-user-avatar') ?></label>
            </th>
            <td>
                <select id="pp-cc-accessible" name="ppress_cc_data[access_condition][who_can_access]">
                    <option value="everyone" <?php selected($who_can_access, 'everyone'); ?>><?= esc_html__('Everyone', 'wp-user-avatar') ?></option>
                    <option value="login" <?php selected($who_can_access, 'login'); ?>><?= esc_html__('Logged in users', 'wp-user-avatar') ?></option>
                    <option value="logout" <?php selected($who_can_access, 'logout'); ?>><?= esc_html__('Logged out users', 'wp-user-avatar') ?></option>
                </select>
            </td>
        </tr>
        <tr id="pp-cc-access-role-row">
            <th>
                <label for="pp-cc-access-role"><?= esc_html__('Select roles that can access content', 'wp-user-avatar') ?></label>
            </th>
            <td>
                <span>
                    <?php foreach (ppress_wp_roles_key_value(false) as $key => $value) : ?>
                        <label style="display:block">
                            <input type="checkbox" name="ppress_cc_data[access_condition][access_roles][]" value="<?= $key ?>" <?= in_array($key, $access_roles) ? 'checked=checked' : ''; ?>>
                            <span><?= $value ?></span>
                        </label>
                    <?php endforeach; ?>
            </td>
        </tr>
        <tr id="pp-cc-access-noaccess-action-row">
            <th>
                <label for="pp-cc-access-noaccess-action">
                    <?= esc_html__('What happens when users without access tries to view content?', 'wp-user-avatar') ?>
                </label>
            </th>
            <td>
                <select id="pp-cc-access-noaccess-action" name="ppress_cc_data[access_condition][noaccess_action]">
                    <option value="message" <?php selected($noaccess_action, 'message') ?>><?= esc_html__('Show access restricted message', 'wp-user-avatar') ?></option>
                    <option value="redirect" <?php selected($noaccess_action, 'redirect') ?>><?= esc_html__('Redirect user', 'wp-user-avatar') ?></option>
                </select>
            </td>
        </tr>
        <tr id="pp-cc-access-noaccess-action-message-row">
            <th>
                <label for="pp-cc-access-noaccess-action-message">
                    <?= esc_html__('Message to show to unauthorized users', 'wp-user-avatar') ?>
                </label>
            </th>
            <td>
                <select id="pp-cc-access-noaccess-action-message" name="ppress_cc_data[access_condition][noaccess_action_message_type]">
                    <option value="global" <?php selected($noaccess_action_message_type, 'global') ?>><?= esc_html__('Global Restrict Access Message', 'wp-user-avatar') ?></option>
                    <option value="custom" <?php selected($noaccess_action_message_type, 'custom') ?>><?= esc_html__('Custom message', 'wp-user-avatar') ?></option>
                    <option value="post_excerpt" <?php selected($noaccess_action_message_type, 'post_excerpt') ?>><?= esc_html__('Post Excerpt', 'wp-user-avatar') ?></option>
                    <option value="post_excerpt_global" <?php selected($noaccess_action_message_type, 'post_excerpt_global') ?>><?= esc_html__('Post Excerpt + Global Restrict Access Message', 'wp-user-avatar') ?></option>
                    <option value="post_excerpt_custom" <?php selected($noaccess_action_message_type, 'post_excerpt_custom') ?>><?= esc_html__('Post Excerpt + Custom Message', 'wp-user-avatar') ?></option>
                </select>
                <p class="description">
                    <?= sprintf(
                        esc_html__('Note that Global Restrict Access Message can be %scustomized here%s.'),
                        '<a href="' . PPRESS_SETTINGS_SETTING_PAGE . '#access_settings?global_restricted_access_message_row" target="_blank">', '</a>'
                    ); ?>
                </p>
            </td>
        </tr>
        <tr id="pp-cc-access-noaccess-action-message-custom-row">
            <th>
                <label for="pp-cc-access-noaccess-action-message-custom">
                    <?= esc_html__('Custom Restricted Message', 'wp-user-avatar') ?>
                </label>
            </th>
            <td>
                <?php
                remove_all_actions('media_buttons');
                remove_all_filters('media_buttons_context');
                remove_all_filters('mce_buttons', 10);
                remove_all_filters('mce_external_plugins', 10);
                remove_all_actions('after_wp_tiny_mce');
                wp_editor($noaccess_action_message_custom, 'pp-cc-access-noaccess-action-message-custom', [
                    'textarea_name' => 'ppress_cc_data[access_condition][noaccess_action_message_custom]',
                    'textarea_rows' => 20,
                    'wpautop'       => false,
                    'media_buttons' => false,
                ]);
                ?>
            </td>
        </tr>
        <tr id="pp-cc-access-noaccess-action-redirect-row">
            <th>
                <label for="pp-cc-access-noaccess-action-redirect">
                    <?= esc_html__('Where should users be redirected to?', 'wp-user-avatar') ?>
                </label>
            </th>
            <td>
                <select id="pp-cc-access-noaccess-action-redirect" name="ppress_cc_data[access_condition][noaccess_action_redirect_url]">
                    <option value="login_page" <?php selected($noaccess_action_redirect_url, 'login_page') ?>><?= esc_html__('Login page', 'wp-user-avatar') ?></option>
                    <option value="custom_url" <?php selected($noaccess_action_redirect_url, 'custom_url') ?>><?= esc_html__('Custom URL', 'wp-user-avatar') ?></option>
                </select>
            </td>
        </tr>
        <tr id="pp-cc-access-noaccess-action-redirect-custom-url-row">
            <th>
                <label for="pp-cc-access-noaccess-action-redirect-custom-url">
                    <?= esc_html__('Redirect URL', 'wp-user-avatar') ?>
                </label>
            </th>
            <td>
                <input type="text" id="pp-cc-access-noaccess-action-redirect-custom-url" name="ppress_cc_data[access_condition][noaccess_action_redirect_custom_url]" value="<?= $noaccess_action_redirect_custom_url ?>">
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    (function ($) {
        $(function () {
            $('#pp-cc-accessible').on('change', function () {

                var val = this.value;

                switch (val) {
                    case 'everyone':
                        $('#pp-cc-access-role-row').hide();
                        $('#pp-cc-access-noaccess-action-row').hide();
                        $('#pp-cc-access-noaccess-action-message-row').hide();
                        $('#pp-cc-access-noaccess-action-message-custom-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').hide();
                        break;
                    case 'login':
                        $('#pp-cc-access-noaccess-action-message-custom-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').hide();
                        $('#pp-cc-access-noaccess-action-message-row').hide();
                        // all show code must be after hide()
                        $('#pp-cc-access-role-row').show().find('select').change();
                        $('#pp-cc-access-noaccess-action-row').show().find('select').change();
                        break;
                    case 'logout':
                        $('#pp-cc-access-role-row').hide();
                        $('#pp-cc-access-noaccess-action-message-custom-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-row').hide();
                        // all show code must be after hide()
                        $('#pp-cc-access-noaccess-action-row').show().find('select').change();
                        break;
                }
            });

            $('#pp-cc-access-noaccess-action').on('change', function () {

                var val = this.value;

                switch (val) {
                    case 'message':
                        $('#pp-cc-access-noaccess-action-message-custom-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').hide();
                        // all show code must be after hide()
                        $('#pp-cc-access-noaccess-action-message-row').show().find('select').change();
                        break;
                    case 'redirect':
                        $('#pp-cc-access-noaccess-action-message-row').hide();
                        $('#pp-cc-access-noaccess-action-message-custom-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').hide();
                        // all show code must be after hide()
                        $('#pp-cc-access-noaccess-action-redirect-row').show().find('select').change();
                        break;
                }
            });

            $('#pp-cc-access-noaccess-action-message').on('change', function () {

                var val = this.value;

                switch (val) {
                    case 'global':
                    case 'post_excerpt_global':
                        $('#pp-cc-access-noaccess-action-message-custom-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').hide();
                        break;
                    case 'custom':
                    case 'post_excerpt_custom':
                        $('#pp-cc-access-noaccess-action-redirect-row').hide();
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').hide();
                        $('#pp-cc-access-noaccess-action-message-custom-row').show().find('select').change();
                        break;
                }
            });

            $('#pp-cc-access-noaccess-action-redirect').on('change', function () {

                var val = this.value;

                switch (val) {
                    case 'login_page':
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').hide();
                        break;
                    case 'custom_url':
                        $('#pp-cc-access-noaccess-action-redirect-custom-url-row').show().find('select').change();
                        break;
                }
            });

            $('#pp-cc-accessible').change();
        });
    })(jQuery)
</script>