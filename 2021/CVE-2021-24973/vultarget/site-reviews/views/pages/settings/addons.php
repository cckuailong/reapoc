<?php defined('ABSPATH') || die; ?>

<?php if (count($settings) > 1) : ?>
    <ul class="glsr-subsubsub subsubsub">
    <?php foreach ($settings as $key => $rows) : ?>
        <li><a href="<?= glsr_admin_url('settings', 'addons', $key); ?>" tabindex="0"><?= ucfirst($key); ?></a><span>|</span></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php foreach ($settings as $key => $rows) : ?>
    <div class="glsr-nav-view-section" id="<?= $key; ?>">
        <?php glsr()->action('addon/settings/'.$key, $rows); ?>
    </div>
<?php endforeach; ?>
