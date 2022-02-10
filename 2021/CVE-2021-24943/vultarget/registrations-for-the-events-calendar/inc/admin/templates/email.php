<?php
settings_errors(); ?>
<h1><?php _e( 'Email Settings', 'registrations-for-the-events-calendar' ); ?></h1>
<div class="rtec-individual-available-notice">
    <p><strong><span class="rtec-individual-available">&#42;</span><?php _e( 'Can also be set for each event separately on the Events->Edit page', 'registrations-for-the-events-calendar' ); ?></strong></p>
</div>
<?php
$new_status = get_transient( 'rtec_new_messages' );
if ( $new_status === 'yes' ) : ?>
    <div class="rtec-notice">
        <p><?php _e( 'For best results with email delivery, check out the related <a href="https://roundupwp.com/faq/my-confirmationnotification-emails-are-missing/" target="_blank">article</a> on our website', 'registrations-for-the-events-calendar' ); ?></p>
    </div>
<?php endif; ?>
<hr>
<form method="post" action="options.php">
    <?php settings_fields( 'rtec_options' ); ?>
    <?php do_settings_sections( 'rtec_email_all' ); ?>
    <input class="button-primary" type="submit" name="save" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
    <hr>
    <?php do_settings_sections( 'rtec_email_notification' ); ?>
    <input class="button-primary" type="submit" name="save" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
    <hr>
    <?php do_settings_sections( 'rtec_email_confirmation' ); ?>
    <input class="button-primary" type="submit" name="save" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
</form>