<?php defined('ABSPATH') || die; ?>

<div class="nf-field-container label-above" data-field="{{ field_name }}">
    <div class="nf-field">
        <div class="field-wrap {{ class }}">
            <div class="nf-field-label">
                {{ label }}
            </div>
            <div class="nf-field-element">
                {{ field }}
            </div>
            {{ errors }}
        </div>
    </div>
</div>
