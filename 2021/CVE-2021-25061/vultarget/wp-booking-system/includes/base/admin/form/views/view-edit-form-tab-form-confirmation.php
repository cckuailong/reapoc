<!-- Confirmation Type -->
<?php $confirmation_type = ( !empty($form_meta['form_confirmation_type'][0]) ) ? esc_attr($form_meta['form_confirmation_type'][0]) : ''; ?>
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="form_confirmation_type"><?php echo __( 'Confirmation Type', 'wp-booking-system' ); ?></label>

    <div class="wpbs-settings-field-inner">
        <select name="form_confirmation_type" id="form_confirmation_type">
            <option <?php echo ($confirmation_type == 'message') ? 'selected' : '';?> value="message"><?php echo __( 'Show a Message', 'wp-booking-system' ); ?></option>
            <option <?php echo ($confirmation_type == 'redirect') ? 'selected' : '';?> value="redirect"><?php echo __( 'Redirect to a Page', 'wp-booking-system' ); ?></option>
        </select>
    </div>
</div>

<!-- Message -->
<div class="wpbs-settings-field-translation-wrapper">
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-xlarge <?php echo (empty($confirmation_type) || $confirmation_type == 'message') ? 'wpbs-confirmation-type-show' : '';?> wpbs-confirmation-type wpbs-confirmation-type-message">
        <label class="wpbs-settings-field-label" for="form_confirmation_message"><?php echo __( 'Message', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <?php wp_editor(( !empty($form_meta['form_confirmation_message'][0]) ) ? esc_textarea($form_meta['form_confirmation_message'][0]) : '', 'form_confirmation_message', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)) ?>
            <?php if(wpbs_translations_active()): ?><a href="#" class="wpbs-settings-field-show-translations"><?php echo __( 'Translations', 'wp-booking-system' ); ?> <i class="wpbs-icon-down-arrow"></i></a><?php endif ?>
        </div>
    </div>
    <?php if(wpbs_translations_active()): ?>
    <div class="wpbs-settings-field-translations">
        <?php foreach($active_languages as $language): ?>
            <!-- Submit Button -->
            <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-xlarge">

                <label class="wpbs-settings-field-label" for="form_confirmation_message_translation_<?php echo $language;?>"><img src="<?php echo WPBS_PLUGIN_DIR_URL ;?>/assets/img/flags/<?php echo $language;?>.png" /> <?php echo $languages[$language];?></label>

                <div class="wpbs-settings-field-inner">
                    <div><?php wp_editor(( !empty($form_meta['form_confirmation_message_translation_' . $language][0]) ) ? esc_textarea($form_meta['form_confirmation_message_translation_' . $language][0]) : '', 'form_confirmation_message_translation_' . $language, array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)) ?></div>
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif ?>
</div>

<!-- Redirect URL -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large <?php echo ($confirmation_type == 'redirect') ? 'wpbs-confirmation-type-show' : '';?> wpbs-confirmation-type wpbs-confirmation-type-redirect">
    <label class="wpbs-settings-field-label" for="form_confirmation_redirect_url"><?php echo __( 'Redirect URL', 'wp-booking-system' ); ?></label>

    <div class="wpbs-settings-field-inner">
        <input name="form_confirmation_redirect_url" type="text" id="form_confirmation_redirect_url" value="<?php echo ( !empty($form_meta['form_confirmation_redirect_url'][0]) ) ? esc_attr($form_meta['form_confirmation_redirect_url'][0]) : '';?>" class="regular-text" >
    </div>
</div>