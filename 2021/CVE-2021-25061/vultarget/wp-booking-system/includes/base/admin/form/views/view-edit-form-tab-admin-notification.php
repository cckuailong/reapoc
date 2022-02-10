<!-- Form Changed Notice -->
<div class="wpbs-page-notice notice-info wpbs-form-changed-notice"> 
    <p><?php echo __( 'It appears you made changes to the form. Make sure you save the form before you make any changes on this page to ensure all email tags are up to date.', 'wp-booking-system' ); ?></p>
</div>

<!-- Enable Notification -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
	<label class="wpbs-settings-field-label" for="admin_notification_enable">
        <?php echo __( 'Enable Notification', 'wp-booking-system' ); ?>
        <?php echo wpbs_get_output_tooltip(__("Send an email to yourself (the website administrator) whenever a booking is made.", 'wp-booking-system'));?>
    </label>

	<div class="wpbs-settings-field-inner">
        <label for="admin_notification_enable" class="wpbs-checkbox-switch">
            <input name="admin_notification_enable" type="checkbox" id="admin_notification_enable"  class="regular-text wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo ( !empty($form_meta['admin_notification_enable'][0]) ) ? 'checked' : '';?> >
            <div class="wpbs-checkbox-slider"></div>
        </label>
	</div>
</div>

<div class="wpbs-user-notification-wrapper wpbs-settings-wrapper <?php echo ( !empty($form_meta['admin_notification_enable'][0]) ) ? 'wpbs-settings-wrapper-show' : '';?>">

    <!-- Email Tags -->
    <div class="card wpbs-email-tags-wrapper">
        <h2 class="title"><?php echo __( 'Email Tags', 'wp-booking-system' ); ?></h2>
        <p><?php echo __( 'You can use these dynamic tags in any of the fields. They will be replaced with the values submitted in the form.', 'wp-booking-system' ); ?></p>
        
        <?php wpbs_output_email_tags($form_data); ?>

    </div>

    <!-- Send To -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="admin_notification_send_to"><?php echo __( 'Send To', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="admin_notification_send_to" type="text" id="admin_notification_send_to" value="<?php echo ( !empty($form_meta['admin_notification_send_to'][0]) ) ? esc_attr($form_meta['admin_notification_send_to'][0]) : '';?>" class="regular-text" >
        </div>
    </div>

    <!-- From Name -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="admin_notification_from_name"><?php echo __( 'From Name', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="admin_notification_from_name" type="text" id="admin_notification_from_name" value="<?php echo ( !empty($form_meta['admin_notification_from_name'][0]) ) ? esc_attr($form_meta['admin_notification_from_name'][0]) : (isset($settings['default_from_name']) ? esc_attr($settings['default_from_name']) : '');?>" class="regular-text" >
        </div>
    </div>

    <!-- From Email -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="admin_notification_from_email"><?php echo __( 'From Email', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="admin_notification_from_email" type="text" id="admin_notification_from_email" value="<?php echo ( !empty($form_meta['admin_notification_from_email'][0]) ) ? esc_attr($form_meta['admin_notification_from_email'][0]) : (isset($settings['default_from_email']) ? esc_attr($settings['default_from_email']) : '');?>" class="regular-text" >
        </div>
    </div>

    <!-- Reply To -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="admin_notification_reply_to"><?php echo __( 'Reply To', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="admin_notification_reply_to" type="text" id="admin_notification_reply_to" value="<?php echo ( !empty($form_meta['admin_notification_reply_to'][0]) ) ? esc_attr($form_meta['admin_notification_reply_to'][0]) : (isset($settings['default_reply_to']) ? esc_attr($settings['default_reply_to']) : '');?>" class="regular-text" >
        </div>
    </div>

    <!-- Subject -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="admin_notification_subject"><?php echo __( 'Subject', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="admin_notification_subject" type="text" id="admin_notification_subject" value="<?php echo ( !empty($form_meta['admin_notification_subject'][0]) ) ? esc_attr($form_meta['admin_notification_subject'][0]) : '';?>" class="regular-text" >
        </div>
    </div>
    
    <!-- Message -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-xlarge">
        <label class="wpbs-settings-field-label" for="admin_notification_message"><?php echo __( 'Message', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <?php wp_editor(( !empty($form_meta['admin_notification_message'][0]) ) ? esc_textarea($form_meta['admin_notification_message'][0]) : '', 'admin_notification_message', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)) ?>
        </div>
    </div>   
   

</div>

