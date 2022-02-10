<?php defined('ABSPATH') || die; ?>

<h2 class="title"><?= _x('Translation Settings', 'admin-text', 'site-reviews'); ?></h2>

<p><?= sprintf(_x('Here you can customise any text of the plugin, including the review form labels and placeholders. However, if you have a multilingual website you should use the %s plugin instead and select "Custom" when it asks you to choose a location for the new translation file.', 'admin-text', 'site-reviews'), '<a href="https://wordpress.org/plugins/loco-translate/">Loco Translate</a>'); ?></p>

<div class="glsr-strings-form">
    <div class="glsr-search-box" id="glsr-search-translations">
        <span class="screen-reader-text"><?= _x('Search for translatable text', 'admin-text', 'site-reviews'); ?></span>
        <div class="glsr-search-box-wrap">
            <span class="glsr-spinner"><span class="spinner"></span></span>
            <input type="search" class="glsr-search-input" autocomplete="off" placeholder="<?= _x('Search here for text to translate...', 'admin-text', 'site-reviews'); ?>">
            <?php wp_nonce_field('search-translations', '_search_nonce', false); ?>
            <div class="glsr-search-results" data-prefix="{{ database_key }}"></div>
        </div>
    </div>
    <table class="glsr-strings-table wp-list-table widefat striped {{ class }}">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-primary"><?= _x('Original Text', 'admin-text', 'site-reviews'); ?></th>
                <th scope="col" class="manage-column"><?= _x('Custom Translation', 'admin-text', 'site-reviews'); ?></th>
            </tr>
        </thead>
        <tbody>{{ translations }}</tbody>
    </table>
    <input type="hidden" name="{{ database_key }}[settings][strings][]">
</div>

<script type="text/html" id="tmpl-glsr-string-plural">
<?php include glsr()->path('views/partials/translations/plural.php'); ?>
</script>
<script type="text/html" id="tmpl-glsr-string-single">
<?php include glsr()->path('views/partials/translations/single.php'); ?>
</script>
