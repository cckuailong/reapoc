<?php defined('ABSPATH') || die; ?>

<div class="glsr-form-wrap wpforms-container wpforms-container-full">
    <form class="{{ class }}" method="post" enctype="multipart/form-data">
        <div class="wpforms-field-container">
            {{ fields }}
        </div>
        <div class="wpforms-submit-container">
            {{ submit_button }}
        </div>
        {{ response }}
    </form>
</div>
