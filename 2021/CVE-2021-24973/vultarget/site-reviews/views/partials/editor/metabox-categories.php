<?php defined('ABSPATH') || die; ?>

<div id="taxonomy-<?= $tax_name; ?>" class="categorydiv">

    <ul id="<?= $tax_name; ?>-tabs" class="category-tabs">
        <li class="tabs"><a href="#<?= $tax_name; ?>-all"><?= $taxonomy->labels->all_items; ?></a></li>
        <li class="hide-if-no-js"><a href="#<?= $tax_name; ?>-pop"><?= _x('Most Used', 'admin-text', 'site-reviews'); ?></a></li>
    </ul>

    <div id="<?= $tax_name; ?>-pop" class="tabs-panel" style="display: none;">
        <ul id="<?= $tax_name; ?>checklist-pop" class="categorychecklist form-no-clear" >
            <?php $popular_ids = wp_popular_terms_checklist($tax_name); ?>
        </ul>
    </div>

    <div id="<?= $tax_name; ?>-all" class="tabs-panel">
        <input type="hidden" name="tax_input[<?= $tax_name; ?>][]" value='0' />
        <ul id="<?= $tax_name; ?>checklist" data-wp-lists="list:<?= $tax_name; ?>" class="categorychecklist form-no-clear">
            <?php wp_terms_checklist($post->ID, array('taxonomy' => $tax_name, 'popular_cats' => $popular_ids)); ?>
        </ul>
    </div>

    <?php if (current_user_can($taxonomy->cap->edit_terms)) : ?>
    <div id="<?= $tax_name; ?>-adder" class="wp-hidden-children">
        <a id="<?= $tax_name; ?>-add-toggle" href="#<?= $tax_name; ?>-add" class="hide-if-no-js taxonomy-add-new">
            <?= sprintf('+ %s', $taxonomy->labels->add_new_item); ?>
        </a>
        <div id="<?= $tax_name; ?>-add" class="category-add wp-hidden-child">
            <label class="screen-reader-text" for="new<?= $tax_name; ?>"><?= $taxonomy->labels->add_new_item; ?></label>
            <input type="text" name="new<?= $tax_name; ?>" id="new<?= $tax_name; ?>" class="form-required form-input-tip" value="<?= esc_attr($taxonomy->labels->new_item_name); ?>" aria-required="true"/>
            <input type="button" id="<?= $tax_name; ?>-add-submit" data-wp-lists="add:<?= $tax_name; ?>checklist:<?= $tax_name; ?>-add" class="button category-add-submit" value="<?= esc_attr($taxonomy->labels->add_new_item); ?>" />
            <?php wp_nonce_field('add-'.$tax_name, '_ajax_nonce-add-'.$tax_name, false); ?>
            <span id="<?= $tax_name; ?>-ajax-response"></span>
        </div>
    </div>
    <?php endif; ?>

</div>
