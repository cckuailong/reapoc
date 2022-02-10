<?php defined('ABSPATH') || die; ?>

<div class="locked-indicator">
    <span class="locked-indicator-icon" aria-hidden="true"></span>
    <span class="screen-reader-text">
        <?= sprintf(_x('&#8220;%s&#8221; is locked', 'admin-text', 'site-reviews'), _draft_or_post_title()); ?>
    </span>
</div>
