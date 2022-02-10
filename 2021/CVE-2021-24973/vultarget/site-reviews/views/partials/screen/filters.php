<?php defined('ABSPATH') || die; ?>

<fieldset class="metabox-prefs">
    <legend><?= _x('Filters', 'admin-text', 'site-reviews'); ?></legend>
    <?php foreach ($filters as $name => $filter) : ?>
        <label>
            <input class="enable-filter-tog" name="<?= $setting; ?>[]" type="checkbox" value="<?= $name; ?>" <?php checked(in_array($name, $enabled), true); ?> />
            <?= $filter; ?>
        </label>
    <?php endforeach; ?>
    <div style="margin-top:8px;">
        <?= _x('Enabling a filter will only display the dropdown if there are options to filter by, otherwise it will remain hidden.', 'admin-text', 'site-reviews'); ?>
    </div>
</fieldset>
