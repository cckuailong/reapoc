<?php defined('ABSPATH') || die; ?>

<div class="glsr-metabox-field {{ class }}">
    <div class="glsr-label">{{ label }}</div>
    <div class="glsr-input wp-clearfix">
        {{ field }}
        <?php if (isset($field['review_object']) && 'avatar' === $field['path']) {
            echo $field['review_object']->avatar(64);
        } ?>
    </div>
</div>
