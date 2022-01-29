<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_formflow_timeline.php'); else {
$settings = new RM_Options;
?>
<link rel="stylesheet" type="text/css" href="<?php echo RM_BASE_URL . 'admin/css/'; ?>style_rm_formflow.css">
<?php if(defined('REGMAGIC_ADDON')) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo RM_ADDON_BASE_URL . 'admin/css/'; ?>style_rm_formflow.css">
<?php } ?>
<script type="text/javascript" src="<?php echo RM_BASE_URL . 'admin/js/'; ?>script_rm_formflow.js"></script>

    <div  class="rm-grid rm-dbfl">
        <div class="rm-grid-section dbfl rm_publish_section" id="rm_publish_timelinepopup">
            <div class="rm-directory-container dbfl">
                <div class="rm-publish-directory-col rm-pd-col-left rm-difl">
                    <?php echo sprintf(__("You can check individual user's login timeline by visiting the user's page inside User Manager. <a target='_blank' class='rm-more' href='%s'>More Info</a><br><br><a target='_blank' class='rm-more' href='%s'>Visit User Manager Now</a>", 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/wordpress-user-login-plugin-guide/#user-manager','?page=rm_user_manage'); ?>
                </div>
                <div class="rm-publish-directory-col rm-difl">  
                    <div class="rm-section-user-manager">
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . "images/user-manager.png"; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>