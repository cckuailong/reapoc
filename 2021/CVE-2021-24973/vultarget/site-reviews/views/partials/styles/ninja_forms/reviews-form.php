<?php defined('ABSPATH') || die; ?>

<div class="glsr-form-wrap nf-form-wrap ninja-forms-form-wrap">
    <div class="nf-form-layout">
        <form class="{{ class }}" id="{{ id }}" method="post" enctype="multipart/form-data">
            <div class="nf-form-content">
                {{ fields }}
                {{ response }}
                {{ submit_button }}
            </div>
        </form>
    </div>
</div>
