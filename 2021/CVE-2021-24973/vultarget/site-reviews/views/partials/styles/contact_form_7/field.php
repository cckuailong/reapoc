<?php defined('ABSPATH') || die; ?>

<p class="{{ class }}" data-field="{{ field_name }}">
    <label for="{{ for }}">{{ label_text }}<br>
        <span class="wpcf7-form-control-wrap">
            {{ field }}
            {{ errors }}
        </span>
    </label>
</p>
