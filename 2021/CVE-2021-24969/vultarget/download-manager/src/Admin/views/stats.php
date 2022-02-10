<?php
$type = wpdm_query_var('type', array('validate' => 'alpha', 'default' => 'history'));
$base_page_uri = "edit.php?post_type=wpdmpro&page=wpdm-stats";
?>
<link rel="stylesheet" href="<?= WPDM_BASE_URL ?>/assets/css/settings-ui.css" />
<div class="wrap w3eden">

    <?php

    $actions = [
        ['link' => "edit.php?post_type=wpdmpro&page=wpdm-stats&task=export&__xnonce=".wp_create_nonce(NONCE_KEY), "class" => "primary", "name" => '<i class="sinc far fa-arrow-alt-circle-down"></i> ' . __("Export History", "download-manager")]
    ];

    $menus = [
            ['link' => "edit.php?post_type=wpdmpro&page=wpdm-stats&type=history", "name" => __("Download History", "download-manager"), "active" => ($type === 'history')],
    ];

    WPDM()->admin->pageHeader(esc_attr__( 'History and Stats', WPDM_TEXT_DOMAIN ), 'chart-pie color-purple', $menus, $actions);

    ?>



        <!--<div class="panel-heading">
            <a class="btn btn-primary btn-sm pull-right" href="<?/*= $base_page_uri; */?>&task=export&__xnonce=<?/*=wp_create_nonce(NONCE_KEY); */?>" style="font-weight: 400">
                <i class="sinc far fa-arrow-alt-circle-down"></i> <?php /*_e("Export History", 'download-manager'); */?>
            </a>
            <b><i class="fas fa-chart-line color-purple"></i> &nbsp; <?php /*echo __("Download Statistics", "download-manager"); */?></b>

        </div>-->


        <div class="wpdm-admin-page-content">
            <?php
            if(file_exists(wpdm_admin_tpl_path("stats/{$type}.php"))) include wpdm_admin_tpl_path("stats/{$type}.php");
            else if (isset($stat_types[$type])) call_user_func($stat_types[$type]['callback']);
            else \WPDM\__\Messages::error(__( 'Stats not found!', 'download-manager' ));
            ?>
        </div>


    <style>
        .notice{ display: none; }
    </style>
