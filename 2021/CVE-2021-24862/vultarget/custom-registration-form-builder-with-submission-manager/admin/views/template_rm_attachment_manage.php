<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_attachment_manage.php'); else {
//s echo "<pre>", var_dump($data);
global $rm_env_requirements;
?>

<?php if (!($rm_env_requirements & RM_REQ_EXT_ZIP)){ ?>
 <div class="shortcode_notification"><p class="rm-notice-para"><?php echo RM_UI_Strings::get('RM_ERROR_EXTENSION_ZIP');?></p></div>
 <?php } ?>

<div class="rmagic">
    <div class="operationsbar">
        <!-- <div class="icons">
            <img alt="" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/supporticon.png'; ?>">>
        </div> -->
        <div class="rmtitle"><?php echo RM_UI_Strings::get('TITLE_ATTACHMENT_PAGE'); ?></div>
        <div class="nav">
                <ul>  
                    <li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
                </ul>
    </div>
    </div>
    
        <!-- Plugin gold and silver edition banner-->
        <?php 
    $rm_promo_banner_title = __('View and Download form attachments at a single place by upgrading', 'custom-registration-form-builder-with-submission-manager');
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>

    </div>

<?php } ?>