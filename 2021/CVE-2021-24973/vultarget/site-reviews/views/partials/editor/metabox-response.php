<?php defined('ABSPATH') || die; ?>

<label class="screen-reader-text" for="response"><?= _x('Respond Publicly', 'admin-text', 'site-reviews'); ?></label>
<textarea class="glsr-response" name="response" id="response" rows="1" cols="40"><?= $response; ?></textarea>
<p><?= _x('If you need to publicly respond to this review, enter your response here.', 'admin-text', 'site-reviews'); ?></p>
