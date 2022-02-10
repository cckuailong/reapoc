<div class="notice notice-info is-dismissible glsr-notice" data-dismiss="welcome">
    <p><?= $text; ?></p>
    <p>
        <a class="components-button is-secondary is-small" href="<?= admin_url('index.php?page='.glsr()->id.'-welcome&tab=whatsnew'); ?>">✨&nbsp;<?= _x('See What\'s New', 'admin-text', 'site-reviews'); ?></a>
        &nbsp;
        <a href="<?= admin_url('index.php?page='.glsr()->id.'-welcome&tab=upgrade-guide'); ?>"><?= _x('Read the upgrade guide', 'admin-text', 'site-reviews'); ?> →</a>
    </p>
</div>
