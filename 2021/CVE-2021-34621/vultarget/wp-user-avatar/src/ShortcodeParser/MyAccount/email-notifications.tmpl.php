<?php

use ProfilePress\Core\Classes\PROFILEPRESS_sql;
use ProfilePress\Core\ShortcodeParser\MyAccount\MyAccountTag;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$current_user = get_user_by('id', get_current_user_id());
?>
    <div class="profilepress-myaccount-notification">

        <h2><?= esc_html__('Email Notifications', 'wp-user-avatar') ?></h2>

        <?php if (isset($_GET['edit']) && $_GET['edit'] == 'true') : ?>
            <div class="profilepress-myaccount-alert pp-alert-success" role="alert">
                <?= apply_filters('ppmyac_email_notifications_success_message', esc_html__('Account was updated successfully.')); ?>
            </div>
        <?php endif;

        $contents = MyAccountTag::email_notification_endpoint_content();

        if ( ! empty($contents)) {

            foreach ($contents as $content) {
                ?>
                <div class="profilepress-myaccount-email-notifications-wrap">

                    <h3><?= $content['title']; ?></h3>

                    <div class="profilepress-myaccount-form-wrap">
                        <?= $content['content']; ?>
                    </div>

                </div>
                <?php
            }
        }
        ?>
    </div>
<?php

do_action('ppress_myaccount_notification');