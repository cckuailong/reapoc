<?php

use ProfilePress\Core\Classes\EditUserProfile;
use ProfilePress\Core\Classes\ExtensionManager;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;
use ProfilePress\Core\Classes\UserAvatar;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$current_user_id = get_current_user_id();

$contact_infos = [];
$custom_fields = [];

if (ExtensionManager::is_enabled(ExtensionManager::CUSTOM_FIELDS)) {

    $contact_infos = PROFILEPRESS_sql::get_contact_info_fields();

    $custom_fields = PROFILEPRESS_sql::get_profile_custom_fields();
}

$success_message = EditUserProfile::get_success_message();

$custom_edit_profile = ppress_settings_by_key('myac_account_details_form', 'default', true);

$sub_menus = apply_filters('ppress_my_account_settings_sub_menus', ['general' => esc_html__('General', 'wp-user-avatar')]);
?>
    <div class="profilepress-myaccount-edit-profile">

        <h2><?= esc_html__('Account Settings', 'wp-user-avatar') ?></h2>

        <?php if (is_array($sub_menus) && count($sub_menus) > 1) : ?>
            <div class="profilepress-myaccount-submenus-wrap">

                <?php foreach ($sub_menus as $menu_id => $sub_menu) : ?>

                    <?php $is_active = ( ! isset($_GET['epview']) && $menu_id == 'general') || (isset($_GET['epview']) && $_GET['epview'] == $menu_id) ? ' ppsubmenu-active' : ''; ?>

                    <div class="profilepress-myaccount-submenu-wrap">
                        <a href="<?= esc_url(remove_query_arg('edit', add_query_arg('epview', $menu_id))) ?>" class="profilepress-myaccount-submenu-item<?= $is_active ?>">
                            <?= $sub_menu ?>
                        </a>
                    </div>

                <?php endforeach; ?>

            </div>
        <?php endif; ?>

        <?php if (isset($_GET['edit']) && $_GET['edit'] == 'true') : ?>
            <?= $success_message ?>
        <?php endif; ?>

        <?php if ( ! empty($this->edit_profile_form_error)) : ?>

            <?php if (strpos($this->edit_profile_form_error, 'profilepress-edit-profile-status') !== false) : ?>
                <?= $this->edit_profile_form_error ?>
            <?php else : ?>
                <div class="profilepress-edit-profile-status">
                    <?= $this->edit_profile_form_error ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <?php

        if ('default' !== $custom_edit_profile) {
            echo do_shortcode(sprintf('[profilepress-edit-profile id="%s"]', absint($custom_edit_profile)), true);
        } elseif ( ! empty($_GET['epview']) && $_GET['epview'] != 'general') {
            do_action('ppress_myaccount_edit_profile_submenu_content', sanitize_text_field($_GET['epview']));
        } else {

            $cover_image_url = ppress_get_cover_image_url();

            ob_start(); ?>
            [pp-edit-profile-form]

            <div class="profilepress-myaccount-form-wrap">

                <div class="profilepress-myaccount-form-field">
                    <div class="ppmyac-custom-file">
                        <?= do_shortcode('[edit-profile-cover-image id="pp-cover-image" class="ppmyac-custom-file-input"]', true) ?>
                        <label for="pp-cover-image" class="ppmyac-custom-file-label" data-browse="<?= esc_html__('Browse', 'wp-user-avatar'); ?>">
                            <?= esc_html__('Cover Image (min. width: 1000px)', 'wp-user-avatar') ?>
                        </label>
                    </div>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <div class="profilepress-myaccount-delete-cover-image-wrap">
                        <div class="profilepress-myaccount-cover-image">
                            <div class="profilepress-myaccount-has-cover-image" style="<?= ! $cover_image_url ? 'display:none' : '' ?>">
                                <?= do_shortcode('[pp-user-cover-image]', true); ?>
                            </div>
                            <?= do_shortcode(sprintf('[pp-remove-cover-image-button label="%s" class="ppmyac-remove-avatar"]', __('Remove', 'wp-user-avatar')), true); ?>
                            <div class="profilepress-myaccount-cover-image-empty" style="<?= $cover_image_url ? 'display:none' : '' ?>"></div>
                        </div>
                    </div>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <div class="ppmyac-custom-file">
                        <?= do_shortcode('[edit-profile-avatar id="pp-avatar" class="ppmyac-custom-file-input"]', true) ?>
                        <label for="pp-avatar" class="ppmyac-custom-file-label" data-browse="<?= esc_html__('Browse', 'wp-user-avatar'); ?>">
                            <?= esc_html__('Profile picture', 'wp-user-avatar') ?>
                        </label>
                    </div>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <div class="profilepress-myaccount-delete-avatar-wrap">
                        <div class="profilepress-myaccount-delete-avatar">
                            <?= UserAvatar::get_avatar_img($current_user_id); ?>
                            <?= do_shortcode(sprintf('[pp-remove-avatar-button label="%s" class="ppmyac-remove-avatar"]', __('Remove', 'wp-user-avatar')), true); ?>
                        </div>
                    </div>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <label for="edit-profile-email"><?= esc_html__('Email address', 'wp-user-avatar') ?></label>
                    <?= do_shortcode('[edit-profile-email id="edit-profile-email" class="profilepress-myaccount-form-control"]', true); ?>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <label for="edit-profile-first-name"><?= esc_html__('First name', 'wp-user-avatar') ?></label>
                    <?= do_shortcode('[edit-profile-first-name id="edit-profile-first-name" class="profilepress-myaccount-form-control"]', true); ?>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <label for="edit-profile-last-name"><?= esc_html__('Last name', 'wp-user-avatar') ?></label>
                    <?= do_shortcode('[edit-profile-last-name id="edit-profile-last-name" class="profilepress-myaccount-form-control"]', true); ?>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <label for="edit-profile-nickname"><?= esc_html__('Nickname', 'wp-user-avatar') ?></label>
                    <?= do_shortcode('[edit-profile-nickname id="edit-profile-nickname" class="profilepress-myaccount-form-control"]', true); ?>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <label for="eup_display_name"><?= esc_html__('Display name publicly as', 'wp-user-avatar') ?></label>
                    <?php $this->display_name_select_dropdown(); ?>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <label for="edit-profile-website"><?= esc_html__('Website', 'wp-user-avatar') ?></label>
                    <?= do_shortcode('[edit-profile-website id="edit-profile-website" class="profilepress-myaccount-form-control"]', true); ?>
                </div>

                <div class="profilepress-myaccount-form-field">
                    <label for="edit-profile-bio"><?= esc_html__('About yourself', 'wp-user-avatar') ?></label>
                    <?= do_shortcode('[edit-profile-bio id="edit-profile-bio" class="profilepress-myaccount-form-control"]', true); ?>
                </div>

                <?php if (is_array($contact_infos) && ! empty($contact_infos)) : ?>

                    <?php foreach ($contact_infos as $field_key => $label) : ?>
                        <div class="profilepress-myaccount-form-field">
                            <label for="<?= $field_key ?>"><?= $label ?></label>
                            <?= do_shortcode(sprintf('[edit-profile-cpf key="%1$s" id="%1$s" type="%2$s" class="profilepress-myaccount-form-control"]', $field_key, 'text'), true) ?>
                        </div>
                    <?php endforeach; ?>

                <?php endif; ?>

                <?php if (is_array($custom_fields) && ! empty($custom_fields)) : ?>

                    <?php foreach ($custom_fields as $custom_field) : ?>
                        <?php
                        $field_key = $custom_field['field_key'];
                        // skip woocommerce core billing / shipping fields added to wordpress profile admin page.
                        if (in_array($field_key, ppress_woocommerce_billing_shipping_fields())) continue;
                        ?>
                        <div class="profilepress-myaccount-form-field">
                            <?php if ($custom_field['type'] !== 'agreeable') : ?>
                                <label for="<?= $field_key ?>"><?= $custom_field['label_name'] ?></label>
                            <?php endif; ?>
                            <?= do_shortcode(sprintf('[edit-profile-cpf id="%1$s" key="%1$s" type="%2$s" class="profilepress-myaccount-form-control"]', $field_key, $custom_field['type'])) ?>
                        </div>
                    <?php endforeach; ?>

                <?php endif; ?>

                <div class="profilepress-myaccount-form-field">
                    <?= do_shortcode('[edit-profile-submit]', true); ?>
                </div>
            </div>

            <input type="hidden" name="ppmyac_form_action" value="updateProfile">

            [/pp-edit-profile-form]

            <?= do_shortcode(ob_get_clean(), true);
        }
        ?>
    </div>
<?php

do_action('ppress_myaccount_edit_profile');