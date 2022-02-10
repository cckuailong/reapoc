<?php defined('ABSPATH') || die; ?>

<div id="misc-pub-pinned" class="misc-pub-section misc-pub-pinned">
    <label for="pinned-status"><?= _x('Pinned', 'admin-text', 'site-reviews'); ?>:</label>
    <span id="pinned-status-text" class="pinned-status-text"><?= $pinned ? $context['yes'] : $context['no']; ?></span>
    <a href="#pinned-status" class="edit-pinned-status hide-if-no-js">
        <span aria-hidden="true"><?= _x('Edit', 'admin-text', 'site-reviews'); ?></span>
        <span class="screen-reader-text"><?= _x('Edit pinned status', 'admin-text', 'site-reviews'); ?></span>
    </a>
    <div id="pinned-status-select" class="pinned-status-select hide-if-js">
        <input type="hidden" name="<?= glsr()->id; ?>[is_pinned]" id="hidden-pinned-status" value="<?= intval($pinned); ?>">
        <select id="pinned-status">
            <option value="1"<?php selected($pinned, false); ?>><?= _x('Pin', 'admin-text', 'site-reviews'); ?></option>
            <option value="0"<?php selected($pinned, true); ?>><?= _x('Unpin', 'admin-text', 'site-reviews'); ?></option>
        </select>
        <a href="#pinned-status" class="save-pinned-status hide-if-no-js button" data-no="{{ no }}" data-yes="{{ yes }}"><?= _x('OK', 'admin-text', 'site-reviews'); ?></a>
        <a href="#pinned-status" class="cancel-pinned-status hide-if-no-js button-cancel"><?= _x('Cancel', 'admin-text', 'site-reviews'); ?></a>
    </div>
</div>
