<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 22/5/20 08:18
 */
if(!defined("ABSPATH")) die();


$type = wpdm_query_var('_type', 'txt', 'link');
$menus = [
    ['link' => "edit.php?post_type=wpdmpro&page=templates&_type=link", "name" => __("Link Templates", "download-manager"), "active" => ($type === 'link')],
    ['link' => "edit.php?post_type=wpdmpro&page=templates&_type=page", "name" => __("Page Templates", "download-manager"), "active" => ($type === 'page')],
    ['link' => "edit.php?post_type=wpdmpro&page=templates&_type=email", "name" => __("Email Templates", "download-manager"), "active" => ($type === 'email')],
];

WPDM()->admin->pageHeader(esc_attr__('Templates', WPDM_TEXT_DOMAIN), 'magic color-purple', $menus);


?>

        <div class="wpdm-admin-page-content">

