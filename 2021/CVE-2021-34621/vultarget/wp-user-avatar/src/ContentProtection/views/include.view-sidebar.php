<?php

use ProfilePress\Core\ContentProtection\WPListTable;

?>
<div class="submitbox" id="submitpost">

    <div id="major-publishing-actions">
        <div id="delete-action">
            <?php if (isset($_GET['action']) && 'edit' == $_GET['action']) : ?>
                <a class="submitdelete deletion pp-confirm-delete" href="<?= WPListTable::delete_rule_url(absint($_GET['id'])); ?>">
                    <?= esc_html__('Delete', 'wp-user-avatar') ?>
                </a>
            <?php endif; ?>
        </div>

        <div id="publishing-action">
            <input type="submit" name="ppress_save_rule" class="button button-primary button-large" value="<?= esc_html__('Save Rule', 'wp-user-avatar') ?>">
        </div>
        <div class="clear"></div>
    </div>

</div>