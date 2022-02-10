<!-- Submit Button -->
<div class="wpbs-settings-field-translation-wrapper">
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="submit_button_label"><?php echo __( 'Submit Button Label', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="submit_button_label" type="text" id="submit_button_label" value="<?php echo ( !empty($form_meta['submit_button_label'][0]) ) ? esc_attr($form_meta['submit_button_label'][0]) : '';?>" class="regular-text" >
            <?php if(wpbs_translations_active()): ?><a href="#" class="wpbs-settings-field-show-translations"><?php echo __( 'Translations', 'wp-booking-system' ); ?> <i class="wpbs-icon-down-arrow"></i></a><?php endif; ?>

        </div>
    </div>
    <?php if(wpbs_translations_active()): ?>
    <div class="wpbs-settings-field-translations">
        <?php foreach($active_languages as $language): ?>
            <!-- Submit Button -->
            <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">

                <label class="wpbs-settings-field-label" for="submit_button_label_translation_<?php echo $language;?>"><img src="<?php echo WPBS_PLUGIN_DIR_URL ;?>/assets/img/flags/<?php echo $language;?>.png" /> <?php echo $languages[$language];?></label>

                <div class="wpbs-settings-field-inner">
                    <input name="submit_button_label_translation_<?php echo $language;?>" type="text" id="submit_button_label_translation_<?php echo $language;?>" value="<?php echo ( !empty($form_meta['submit_button_label_translation_' . $language][0]) ) ? esc_attr($form_meta['submit_button_label_translation_' . $language][0]) : '';?>" class="regular-text" >
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif ?>
</div>

