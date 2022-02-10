<?php defined('ABSPATH') || die; ?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?= esc_html(get_admin_page_title()); ?>
        <a href="<?= admin_url('index.php?page='.glsr()->id.'-welcome'); ?>" class="page-title-action"><?= _x('About', 'admin-text', 'site-reviews'); ?></a>
    </h1>
    <?= $notices; ?>
    <p><?= _x('Click an active tab to expand/collapse all sections.', 'admin-text', 'site-reviews'); ?></p>
    <nav class="glsr-nav-tab-wrapper nav-tab-wrapper">
        <?php foreach ($tabs as $id => $title) : ?>
        <a class="glsr-nav-tab nav-tab" data-id="<?= $id; ?>" href="<?= glsr_admin_url('documentation', $id); ?>" tabindex="0"><?= $title; ?></a>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($tabs as $id => $title) : ?>
    <div class="glsr-nav-view ui-tabs-hide" id="<?= $id; ?>">
        <?php glsr()->render('pages/documentation/'.$id, $data); ?>
    </div>
    <?php endforeach; ?>
    <input type="hidden" name="_active_tab">
    <input type="hidden" name="_wp_http_referer" value="<?= $http_referer; ?>">
</div>
