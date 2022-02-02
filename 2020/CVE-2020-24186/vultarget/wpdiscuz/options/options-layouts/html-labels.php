<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<!-- Option start -->
<div class="wpd-opt-row">
    <div class="wpd-opt-intro">
        <img class="wpd-opt-img" src="<?php echo esc_url_raw(plugins_url(WPDISCUZ_DIR_NAME . "/assets/img/dashboard/" . $setting["icon"])); ?>" style="height: 56px; padding-top: 5px;"/>
        <?php echo sprintf(__("User Labels are small rectangles with specific background colors indicating comment author role, such as Guest, Member, Author, Admin, etc... These labels can be enabled and disabled for certain user role. Also you can change label colors using according options below.<br> Besides labels you can enable User Reputation, Badges and Ranks based on user activity, using %s addon. This addon integrates wpDiscuz comment system with an adaptive user points management plugin %s.", "wpdiscuz"), "<a href='https://gvectors.com/product/wpdiscuz-mycred/'  target='_blank' style='color:#07B290;'>wpDiscuz myCRED Integration</a>", "<a href='https://wordpress.org/plugins/mycred/' target='_blank'>myCRED</a>"); ?>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 10px;">
        <a href="https://wpdiscuz.com/docs/wpdiscuz-7/plugin-settings/user-labels-and-badges/" title="<?php esc_attr_e("Read the documentation", "wpdiscuz") ?>" target="_blank"><i class="far fa-question-circle"></i></a>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="blogRoleLabels" style="border-bottom: none;">
    <div class="wpd-opt-input" style="width: calc(100% - 40px);">
        <h2 style="margin-bottom: 0px;font-size: 15px; color: #555;"><?php echo esc_html($setting["options"]["blogRoleLabels"]["label"]) ?></h2>
        <p class="wpd-desc"><?php echo $setting["options"]["blogRoleLabels"]["description"] ?></p>
        <hr />
        <div class="wpd-multi-check" style="padding-top: 10px; padding-left: 10px;">
            <?php
            foreach ($this->labels["blogRoleLabels"] as $role => $value) {
                ?>
                <div class="wpd-mublock-inline" style="width: 200px; min-width: 33%;">
                    <input type="checkbox" <?php checked((int) $value); ?> value="1" name="<?php echo esc_attr(WpdiscuzCore::TAB_LABELS); ?>[blogRoleLabels][<?php echo esc_attr($role); ?>]" id="blogRoleLabels<?php echo esc_attr($role); ?>" style="margin:0px; vertical-align: middle;" />
                    <label for="blogRoleLabels<?php echo esc_attr($role); ?>"><?php echo esc_html(ucfirst(str_replace("_", " ", $role))); ?></label>
                </div>
                <?php
            }
            ?>
            <div class="wpd-clear"></div>
        </div>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 36px;">
        <?php $this->printDocLink($setting["options"]["blogRoleLabels"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->

<!-- Option start -->
<div class="wpd-opt-row" data-wpd-opt="commenterLabelColors">
    <div class="wpd-opt-input" style="width: calc(100% - 40px);">
        <h2 style="margin-bottom: 0px;font-size: 15px; color: #555;"><?php echo esc_html($setting["options"]["commenterLabelColors"]["label"]) ?></h2>
        <p class="wpd-desc"><?php echo $setting["options"]["commenterLabelColors"]["description"] ?></p>
        <hr />
        <?php
        foreach ($this->labels["blogRoles"] as $roleName => $color) {
            ?>
            <div class="wpd-color-wrap" style="width: 200px; min-width: 33%;">
                <input type="text" title="<?php esc_attr(esc_attr_e("label color", "wpdiscuz")); ?>" class="wpdiscuz-color-picker regular-text" value="<?php echo esc_attr($color); ?>" id="blogRoles_<?php echo esc_attr($roleName); ?>" name="<?php echo esc_attr(WpdiscuzCore::TAB_LABELS); ?>[blogRoles][<?php echo esc_attr($roleName); ?>]" placeholder="<?php esc_attr_e("Example: #00FF00", "wpdiscuz"); ?>"/>
                <label><span style='color:<?php echo esc_attr($color); ?>'><?php echo esc_html(ucfirst(str_replace("_", " ", $roleName))); ?></span></label>
            </div>
            <?php
        }
        ?>
        <div class="wpd-clear"></div>
    </div>
    <div class="wpd-opt-doc" style="padding-top: 36px;">
        <?php $this->printDocLink($setting["options"]["commenterLabelColors"]["docurl"]) ?>
    </div>
</div>
<!-- Option end -->