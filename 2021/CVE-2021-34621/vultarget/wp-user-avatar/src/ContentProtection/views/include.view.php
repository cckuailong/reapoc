<?php

use ProfilePress\Core\ContentProtection\ContentConditions;
use ProfilePress\Core\ContentProtection\SettingsPage;
use ProfilePress\Core\Classes\PROFILEPRESS_sql as PR;

$dbData = PR::get_meta_value(absint(ppress_var($_GET,'id')), SettingsPage::META_DATA_KEY);

$contentToRestrictData = ppress_var($dbData, 'content');

$accessConditionData   = ppress_var($dbData, 'access_condition');

add_action('add_meta_boxes', function () use ($contentToRestrictData) {
    add_meta_box(
        'ppress-content-protection-content',
        esc_html__('Content to Protect', 'wp-user-avatar'),
        function () use ($contentToRestrictData) {
            require dirname(__FILE__) . '/view.contentbox.php';
        },
        'ppcontentprotection'
    );
});

add_action('add_meta_boxes', function () use ($accessConditionData) {
    add_meta_box(
        'ppress-content-protection-access',
        esc_html__('Access Condition', 'wp-user-avatar'),
        function () use ($accessConditionData) {
            require dirname(__FILE__) . '/view.access-condition.php';
        },
        'ppcontentprotection'
    );
});

add_action('add_meta_boxes', function () {
    add_meta_box(
        'submitdiv',
        __('Publish', 'wp-user-avatar'),
        function () {
            require dirname(__FILE__) . '/include.view-sidebar.php';
        },
        'ppcontentprotection',
        'sidebar'
    );
});

do_action('add_meta_boxes', 'ppcontentprotection', '');
?>
<style type="text/css">
    .handle-actions {
        display: none
    }

    .pp-content-protection-access-box th {
        width: 33%;
    }
</style>
<script type="text/javascript">
    var ppress_cr_nonce = '<?= wp_create_nonce('ppress_cr_nonce'); ?>';
    var ppress_cr_conditions = <?php echo json_encode(ContentConditions::get_instance()->get_conditions()); ?>;
</script>
<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">
            <div id="titlediv">
                <div id="titlewrap">
                    <label class="screen-reader-text" id="title"><?= esc_html__('Add title', 'wp-user-avatar') ?></label>
                    <?php $postedData = ppress_var($_POST, 'ppress_cc_data', []); ?>
                    <input value="<?= ppressPOST_var('title', ppress_var($dbData, 'title'), false, $postedData) ?>" style="width:100%!important;max-width:100%!important;" type="text" name="ppress_cc_data[title]" id="title">
                </div>
            </div>
        </div>

        <div id="postbox-container-1" class="postbox-container">
            <?php do_meta_boxes('ppcontentprotection', 'sidebar', ''); ?>
        </div>
        <div id="postbox-container-2" class="postbox-container">
            <?php do_meta_boxes('ppcontentprotection', 'advanced', ''); ?>
        </div>
    </div>
    <br class="clear">
</div>

<script type="text/html" id="tmpl-ppress-cr-or-rule">
    <?php ContentConditions::get_instance()->rule_row('{{data.facetListId}}', '{{data.facetId}}'); ?>
</script>

<script type="text/html" id="tmpl-ppress-cr-and-rule">
    <?php ContentConditions::get_instance()->rules_group_row('{{data.facetListId}}', '{{data.facetId}}'); ?>
</script>

<script type="text/html" id="tmpl-ppress-cr-unlinked-and-rule-badge">
    <?php ContentConditions::get_instance()->unlinked_and_rule_badge(); ?>
</script>

<script type="text/html" id="tmpl-ppress-cr-linked-and-rule-badge">
    <?php ContentConditions::get_instance()->linked_and_rule_badge(); ?>
</script>