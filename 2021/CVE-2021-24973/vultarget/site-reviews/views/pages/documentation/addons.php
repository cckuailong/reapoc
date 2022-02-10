<?php defined('ABSPATH') || die; ?>

<?php foreach ($addons as $title => $section) : ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="">
            <span class="title"><?= $title; ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div class="inside">
        <?= $section; ?>
    </div>
</div>
<?php endforeach; ?>
