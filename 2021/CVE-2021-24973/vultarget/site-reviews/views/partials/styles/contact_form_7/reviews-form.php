<?php defined('ABSPATH') || die; ?>

<div class="wpcf7">
    <div class="glsr-form-wrap">
        <form class="{{ class }}" id="{{ id }}" method="post" enctype="multipart/form-data">
            {{ fields }}
            {{ submit_button }}
            {{ response }}
        </form>
    </div>
</div>
