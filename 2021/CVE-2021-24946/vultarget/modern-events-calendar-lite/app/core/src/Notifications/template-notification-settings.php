<?php

if(!defined('ABSPATH')){
    die || exit;
}

$class = $atts['class'];
$group_id = $atts['group_id'];
$base_field_name = $atts['base_field_name'];
$section_title = $atts['section_title'];
$enable_options_text = $atts['enable_options_text'];
$enable_options_description = $atts['enable_options_description'];
$placeholders = $atts['placeholders'];
$options = $atts['options'];

?>

<div id="<?php echo $group_id ?>_notification_section" class="mec-options-fields <?php echo $class ?>">

    <h4 class="mec-form-subtitle"><?php echo $section_title; ?></h4>
    <div class="mec-form-row">
    <div class="mec-col-12">
        <label>
            <input type="hidden" name="<?php echo $base_field_name; ?>[<?php echo $group_id; ?>_notification][status]" value="0" />
            <input onchange="jQuery('#mec_notification_<?php echo $group_id; ?>_notification_container_toggle').toggle();" value="1" type="checkbox" name="<?php echo $base_field_name; ?>[<?php echo $group_id; ?>_notification][status]" <?php if(!isset($options['status']) or (isset($options['status']) and $options['status'])) echo 'checked="checked"'; ?> /> <?php $enable_options_text; ?>
        </label>
    </div>
    <p class="mec-col-12 description"><?php echo $enable_options_description ?></p>
    </div>
    <div id="mec_notification_<?php echo $group_id;  ?>_notification_container_toggle" class="<?php if(isset($options) and isset($options['status']) and !$options['status']) echo 'mec-util-hidden'; ?>">

        <?php do_action( 'mec_notifications_settings_start', $group_id, $options ); ?>

        <div class="mec-form-row">
            <div class="mec-col-3">
                <label for="mec_notifications_<?php echo $group_id;  ?>_notification_subject"><?php _e('Email Subject', 'modern-events-calendar-lite'); ?></label>
            </div>
            <div class="mec-col-9">
                <input type="text" name="<?php echo $base_field_name; ?>[<?php echo $group_id; ?>_notification][subject]" id="mec_notifications_<?php echo $group_id;  ?>_notification_subject" value="<?php echo (isset($options['subject']) ? stripslashes($options['subject']) : ''); ?>" />
            </div>
        </div>

        <!-- Start Receiver Users -->
        <div class="mec-form-row">
            <div class="mec-col-3">
                <label for="mec_notifications_<?php echo $group_id;  ?>_notification_receiver_users"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></label>
            </div>
            <div class="mec-col-9">
                <?php
                    $users = isset($options['receiver_users']) ? $options['receiver_users'] : array();
                    echo \MEC\Base::get_main()->get_users_dropdown($users, $group_id . '_notification');
                ?>
                <span class="mec-tooltip">
                    <div class="box left">
                        <h5 class="title"><?php _e('Receiver Users', 'modern-events-calendar-lite'); ?></h5>
                        <div class="content"><p><?php esc_attr_e('Select users to send a copy of email to them!', 'modern-events-calendar-lite'); ?></p></div>
                    </div>
                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                </span>
            </div>
        </div>
        <!-- End Receiver Users -->

        <!-- Start Receiver Roles -->
        <div class="mec-form-row">
            <div class="mec-col-3">
                <label for="mec_notifications_<?php echo $group_id;  ?>_notification_receiver_roles"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></label>
            </div>
            <div class="mec-col-9">
                <?php
                    $roles = isset($options['receiver_roles']) ? $options['receiver_roles'] : array();
                    echo \MEC\Base::get_main()->get_roles_dropdown($roles, $group_id . '_notification');
                ?>
                <span class="mec-tooltip">
                    <div class="box left">
                        <h5 class="title"><?php _e('Receiver Roles', 'modern-events-calendar-lite'); ?></h5>
                        <div class="content"><p><?php esc_attr_e('Select users a specific role.', 'modern-events-calendar-lite'); ?></p></div>
                    </div>
                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                </span>
            </div>
        </div>
        <!-- End Receiver Roles -->

        <div class="mec-form-row">
            <div class="mec-col-3">
                <label for="mec_notifications_<?php echo $group_id;  ?>_notification_recipients"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></label>
            </div>
            <div class="mec-col-9">
                <input type="text" name="<?php echo $base_field_name; ?>[<?php echo $group_id; ?>_notification][recipients]" id="mec_notifications_<?php echo $group_id;  ?>_notification_recipients" value="<?php echo (isset($options['recipients']) ? $options['recipients'] : ''); ?>" />
                <span class="mec-tooltip">
                    <div class="box left">
                        <h5 class="title"><?php _e('Custom Recipients', 'modern-events-calendar-lite'); ?></h5>
                        <div class="content"><p><?php esc_attr_e('Insert comma separated emails for multiple recipients.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/notifications/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                    </div>
                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                </span>
            </div>
        </div>

        <?php do_action( 'mec_notifications_settings_before_editor', $group_id, $options ); ?>

        <div class="mec-form-row">
            <label for="mec_notifications_<?php echo $group_id;  ?>_notification_content"><?php _e('Email Content', 'modern-events-calendar-lite'); ?></label>
            <?php
            $content = isset($options['content']) ? stripslashes($options['content']) : '';
            wp_editor(
                $content,
                'mec_notifications_' . $group_id . '_notification_content',
                array(
                    'textarea_name'=> $base_field_name.'['. $group_id.'_notification][content]'
                )
            );
            ?>
        </div>

        <?php do_action( 'mec_notifications_settings_after_editor', $group_id, $options ); ?>

        <div class="mec-form-row">
            <div class="mec-col-12">
                <p class="description"><?php _e('You can use the following placeholders', 'modern-events-calendar-lite'); ?></p>
                <ul>
                    <?php foreach($placeholders as $p_key => $p_text): ?>
                        <li><span><?php echo $p_key ?></span>: <?php echo $p_text; ?></li>
                    <?php endforeach; ?>
                    <?php do_action( 'mec_extra_field_notifications', $group_id ); ?>
                </ul>
            </div>
        </div>

        <?php do_action( 'mec_notifications_settings_end', $group_id, $options ); ?>

    </div>
</div>
