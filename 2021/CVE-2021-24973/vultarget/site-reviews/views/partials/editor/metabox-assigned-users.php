<?php defined('ABSPATH') || die; ?>

<div class="glsr-search-box" id="glsr-search-users">
    <div class="glsr-search-box-wrap">
        <span class="glsr-spinner"><span class="spinner"></span></span>
        <input type="search" class="glsr-search-input" autocomplete="off" placeholder="<?= esc_attr_x('Search by ID or name...', 'admin-text', 'site-reviews'); ?>">
        <span class="glsr-search-results"></span>
    </div>
    <p><?= _x('Search for a user that you would like to assign this review to.', 'admin-text', 'site-reviews'); ?></p>
    <span class="glsr-selected-entries description"><?= $templates; ?></span>
</div>

<script type="text/html" id="tmpl-glsr-assigned-users">
<?php include glsr()->path('views/partials/editor/assigned-entry.php'); ?>
</script>
