<?php defined('ABSPATH') || die; ?>

<div class="glsr-welcome wrap about-wrap about-wrap-content">
    <h1>Welcome to <?= glsr()->name; ?></h1>
    <div class="glsr-about-text about-text">Site Reviews is a free WordPress review plugin with advanced features that makes it easy to manage reviews on your website. Follow the instructions below to get started!</div>
    <div class="badge">Version <?= glsr()->version; ?></div>
    <p class="about-buttons">
        <a class="components-button is-secondary" href="<?= glsr_admin_url('documentation'); ?>">Documentation</a>
        <a class="components-button is-secondary" href="https://wordpress.org/support/plugin/site-reviews/">Support</a>
        <a class="components-button is-secondary dashicon dashicons-facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A//geminilabs.io/site-reviews">Share</a>
        <a class="components-button is-secondary dashicon dashicons-twitter" target="_blank" href="https://twitter.com/intent/tweet?text=Site Reviews is a fantastic WordPress review plugin with advanced features that makes it easy to manage reviews on your website.&url=https://geminilabs.io/site-reviews&hashtags=WordPress,reviewplugins,">Tweet</a>
    </p>
    <nav class="glsr-nav-tab-wrapper nav-tab-wrapper">
        <?php foreach ($tabs as $id => $title) : ?>
        <a class="glsr-nav-tab nav-tab" data-id="<?= $id; ?>" href="<?= admin_url('index.php?page=site-reviews-welcome&tab='.$id); ?>" tabindex="0"><?= $title; ?></a>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($tabs as $id => $title) : ?>
    <div class="glsr-nav-view ui-tabs-hide" id="<?= $id; ?>">
        <?php $template->render('pages/welcome/'.$id, $data); ?>
    </div>
    <?php endforeach; ?>
    <input type="hidden" name="_active_tab">
    <input type="hidden" name="_wp_http_referer" value="<?= $http_referer; ?>">
</div>
