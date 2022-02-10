<?php defined('ABSPATH') || die; ?>

<p class="about-description">Please take some time to read this upgrade guide.</p>
<div class="is-fullwidth">
    <div class="glsr-flex-row">
        <div class="glsr-column">
            <?php include trailingslashit(__DIR__).'upgrade/v500.php'; ?>
            <?php include trailingslashit(__DIR__).'upgrade/v400.php'; ?>
        </div>
    </div>
</div>
