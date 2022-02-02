<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div class="wrap wpdiscuz_options_page">
    <div style="float:left; width:50px; height:55px; margin:10px 10px 10px 0px;">
        <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/wpdiscuz-7-logo.png")); ?>" style="height: 48px;"/>
    </div>
    <h1 style="padding-bottom:20px; padding-top:15px;"><?php esc_html_e("wpDiscuz Front-end Phrases", "wpdiscuz"); ?></h1>
    <br style="clear:both" />
    <?php settings_errors("wpdiscuz"); ?>
    <form action="<?php echo esc_url_raw(admin_url("admin.php?page=" . WpdiscuzCore::PAGE_PHRASES)); ?>" method="post" name="<?php echo esc_attr(WpdiscuzCore::PAGE_PHRASES); ?>" class="wc-phrases-settings-form wc-form">
        <?php
        wp_nonce_field("wc_phrases_form");
        ?>
        <div id="phrasesTab">
            <ul class="resp-tabs-list phrases_tab_id">
                <li><?php esc_html_e("General", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Form", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Comment", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Date/Time", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Email", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Notification", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Follow", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Social Login", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("User Settings", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Errors", "wpdiscuz"); ?></li>
                <li><?php esc_html_e("Media", "wpdiscuz"); ?></li>
            </ul>
            <div class="resp-tabs-container phrases_tab_id">
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-general.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-form.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-comment.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-datetime.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-email.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-notification.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-follow.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-social-login.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-user-settings.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-error.php"; ?>
                <?php include_once WPDISCUZ_DIR_PATH . "/options/phrases-layouts/phrases-media.php"; ?>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var width = 0;
                var phrasesTabsType = 'default';
                $('#phrasesTab ul.resp-tabs-list.phrases_tab_id li').each(function () {
                    width += $(this).outerWidth(true);
                });

                if (width > $('#phrasesTab').innerWidth()) {
                    phrasesTabsType = 'vertical';
                }
                $('#phrasesTab').wpdiscuzEasyResponsiveTabs({
                    type: phrasesTabsType, //Types: default, vertical, accordion
                    width: 'auto', //auto or any width like 600px
                    fit: true, // 100% fit in a container
                    tabidentify: 'phrases_tab_id' // The tab groups identifier
                });
                $(document).delegate('.phrases_tab_id .resp-tab-item', 'click', function () {
                    var activeTabIndex = $('.resp-tabs-list.phrases_tab_id li.resp-tab-active').index();
                    Cookies.set('phrasesActiveTabIndex', activeTabIndex, {expires: 30});
                });
                var savedIndex = Cookies.get('phrasesActiveTabIndex') >= 0 ? Cookies.get('phrasesActiveTabIndex') : 0;
                $('.resp-tabs-list.phrases_tab_id li').eq(savedIndex).click();
            });
        </script>
        <table class="form-table wc-form-table">
            <tbody>
                <tr valign="top">
                    <td colspan="4">
                        <p class="submit">
                            <?php $resetPhrasesUrl = admin_url("admin-post.php?action=resetPhrases"); ?>
                            <a id="wpdiscuz-reset-phrases" href="<?php echo esc_url_raw(wp_nonce_url($resetPhrasesUrl, "reset_phrases_nonce")); ?>" class="button button-secondary" style="margin-left: 5px;"><?php esc_html_e("Reset Phrases", "wpdiscuz"); ?></a>
                            <input type="submit" class="button button-primary" name="wc_submit_phrases" value="<?php esc_html_e("Save Changes", "wpdiscuz"); ?>" style="float: right;" />
                        </p>
                    </td>
                </tr>
            <input type="hidden" name="action" value="update" />
            </tbody>
        </table>
    </form>
</div>