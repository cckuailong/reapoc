<?php

$default_strings = wpbs_form_default_strings();
$strings = array(
    'validation_errors' => array(
        'label' => __('Validation Errors', 'wp-booking-system')
    ),
    'required_field' => array(
        'label' => __('Required Field', 'wp-booking-system')
    ),
    'invalid_email' => array(
        'label' => __('Invalid Email', 'wp-booking-system'),
    ),
    'select_date' => array(
        'label' => __('No Date Selected', 'wp-booking-system'),
    )
);

foreach ($strings as $key => $string): ?>
<!-- Required Field -->
<div class="wpbs-settings-field-translation-wrapper">
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="form_strings_<?php echo $key;?>">
            <?php echo $string['label'] ?>
            <?php if(isset($string['tooltip'])): ?>
                <?php echo wpbs_get_output_tooltip($string['tooltip']);?>
            <?php endif ?>
        </label>
        <div class="wpbs-settings-field-inner">
            <input name="form_strings_<?php echo $key;?>" type="text" id="form_strings_<?php echo $key;?>" value="<?php echo (!empty($form_meta['form_strings_' . $key][0])) ? esc_attr($form_meta['form_strings_' . $key][0]) : $default_strings[$key]; ?>" class="regular-text" >
            <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-settings-field-show-translations"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-down-arrow"></i></a><?php endif?>
        </div>
    </div>
    <?php if (wpbs_translations_active()): ?>
    <!-- Required Field Translations -->
    <div class="wpbs-settings-field-translations">
        <?php foreach ($active_languages as $language): ?>
            <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
                <label class="wpbs-settings-field-label" for="form_strings_<?php echo $key;?>_translation_<?php echo $language; ?>"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                <div class="wpbs-settings-field-inner">
                    <input name="form_strings_<?php echo $key;?>_translation_<?php echo $language; ?>" type="text" id="form_strings_<?php echo $key;?>_translation_<?php echo $language; ?>" value="<?php echo (!empty($form_meta['form_strings_'.$key.'_translation_' . $language][0])) ? esc_attr($form_meta['form_strings_'.$key.'_translation_' . $language][0]) : ''; ?>" class="regular-text" >
                </div>
            </div>
        <?php endforeach;?>
    </div>
    <?php endif?>
</div>
<?php endforeach;?>