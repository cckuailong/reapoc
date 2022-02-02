<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<?php $this->refreshAddonPage(); $this->dismissAddonNoteOnPage(); ?>
<div class="wrap wpdiscuz_addons_page">
    <div style="float:left; width:50px; height:55px; margin:10px 10px 10px 0px;">
        <img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/wpdiscuz-7-logo.png")); ?>" style="height: 48px;"/>
    </div>
    <h1 style="padding-bottom:20px; padding-top:15px;"><?php esc_html_e("wpDiscuz Addons", "wpdiscuz"); ?></h1>
    <br style="clear:both" />
    <table width="100%" border="0" cellspacing="1" class="widefat">
        <tr>
            <td valign="top" style="padding:10px 10px 0px 10px;">
                <table width="100%" border="0" cellspacing="1">
                    <thead>
                        <tr>
                            <th style="font-size:16px; padding-bottom:15px;"><strong><?php esc_html_e("wpDiscuz Add-ons","wpdiscuz");?></strong></th>
                            <th style="font-size:16px; padding-bottom:15px; width:205px; text-align:center; border-bottom:1px solid #008EC2;"><a href="https://gvectors.com/forum/" style="color:#008EC2; overflow:hidden; outline:none;" target="_blank"><?php esc_html_e("Addons Support Forum","wpdiscuz");?></a></th>
                        </tr>
                    </thead>
                    <tr valign="top">
                        <td colspan="2" style="background:#FFF; text-align:left; font-size:16px; font-style: italic;">
                            <?php esc_html_e("All wpDiscuz addons are being developed by wpDiscuz developers at gVectors Team. These addons help us to keep top level development of the free wpDiscuz plugin. All essential and even dozens of extra-cool features are already available for free in the core wpDiscuz plugin. There are not any limits, any pro and paid versions of wpDiscuz. We have another dozens of awesome features in our to-do list which will be added for free in the future releases.","wpdiscuz");?>
                            <br />
                            <p style="font-size:10px; color:#B1B1B1; font-style:italic; text-align:right; line-height:12px; padding-top:6px; margin:0px;">
                               <?php _e(" Thank you!<br/> Sincerely yours,<br/> gVectors Team&nbsp;","wpdiscuz");?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br style="clear:both" />
    <div class="wpdiscuz-addons-wrapper">
        <?php
        foreach ($this->addons as $key => $addon) {
            $installed = class_exists($addon["class"]) ? true : false;
            if( $addon["class"] == "Bundle" ) continue;
            ?>
            <div class="wpdiscuz-addon-block">
                <div id="wpdiscuz-addon-<?php echo esc_attr($key); ?>" class="addon-thumb" style="background:url(<?php echo esc_url_raw($installed ? str_replace(".png", "-off.png", $addon["thumb"]) : $addon["thumb"]); ?>) top center no-repeat;">
                    &nbsp;
                </div>
                <div class="contenthover">
                    <div class="addon-isactive">
                        <?php if ($installed) { ?>
                            <div class="note-installed"><?php esc_html_e("Installed", "default"); ?></div>
                        <?php } else { ?>
                            <h3 style="font-weight:normal; font-size:22px; line-height: 25px; margin-bottom:2px; text-shadow: 0 0 2px #999;"><?php echo esc_html($addon["title"]); ?></h3>
                            <ul>
                                <!--<li style="line-height:16px;"><?php //esc_html_e('Version', 'default'); ?>: <?php //echo $addon['version']; ?></li>--> 
                                <li style="line-height:16px;">wpDiscuz: <?php esc_html_e("at least", "default"); ?> <?php echo esc_html($addon["requires"]); ?></li>
                            </ul>
                            <?php if( $addon["class"] == "Bundle" ): ?>
                                <a class="button button-primary addon-button" href="<?php echo esc_url_raw($addon["url"]); ?>" target="_blank" style="font-size:14px;"><?php echo esc_html__("More Info &raquo;", "wpdiscuz"); ?></a>
                            <?php else: ?>
                                <a class="button button-primary addon-button" href="<?php echo esc_url_raw($addon["url"]); ?>" target="_blank" style="font-size:14px;"><?php echo esc_html__("Live Preview | Buy", "wpdiscuz"); ?></a>
                            <?php endif; ?>

                        <?php } ?>
                    </div>
                </div>
                <div style="clear:both"></div>
                <div class="addon-info" style="<?php if ($installed) echo "background-color:#bbbbbb"; ?>">
                    <a href="<?php echo esc_url_raw($addon["url"]); ?>" target="_blank" title="<?php esc_attr_e("More information about", "default"); ?> <?php echo esc_attr($addon["title"]); ?> add-on &raquo;">
                        <p class="addon-title"><?php echo esc_html($addon["title"]); ?></p>
                    </a>
                    <div class="addon-desc"><?php echo esc_html($addon["desc"]); ?></div>
                </div>
            </div>
            <script language="javascript">jQuery(document).ready(function ($) { $('#wpdiscuz-addon-<?php echo $key ?>').contenthover({ overlay_width:230, overlay_height:<?php echo ($installed) ? "100" : "180"; ?>, effect:'slide', slide_direction:'right', overlay_x_position:'right', overlay_y_position:'center', overlay_background:'#e5e5e5', overlay_opacity:0.9}); });</script>
        <?php } ?>
    </div>
    <div style="clear:both;"></div>
    <div style="max-width: 83%; padding: 15px; background: #ffffff; margin-bottom: 15px; box-sizing: border-box;">
        <a href="https://gvectors.com/product/wpdiscuz-addons-bundle/" target="_blank" title="<?php esc_attr_e("View wpDiscuz Addons Bundle","wpdiscuz");?>"><img src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/addons/bundle/wpdiscuz-bundle.png")); ?>" alt="<?php esc_attr_e("wpDiscuz Addons Bundle", "default"); ?>" style="width: 100%;"/></a>
    </div>
    <h3>&nbsp;</h3>
    <hr />

</div>