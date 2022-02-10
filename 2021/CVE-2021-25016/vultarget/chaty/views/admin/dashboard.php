<?php
$chaty_widgets = array();
$widget = "";
$date_status = false;
$is_deleted = get_option("cht_is_default_deleted");
if($is_deleted === false) {
    $cht_widget_title = get_option("cht_widget_title");
    $cht_widget_title = empty($cht_widget_title)?"Widget-1":$cht_widget_title;
    $status = get_option("cht_active");
    $date = get_option("cht_created_on");
    $date_status = ($date === false || empty($date))?0:1;
    $widget = array(
        'title' => $cht_widget_title,
        'index' => 0,
        'nonce' => wp_create_nonce("chaty_remove__0"),
        'status' => $status,
        'created_on' => $date
    );
    $chaty_widgets[] = $widget;
}
//echo "<pre>"; print_r($chaty_widgets); die;
?>
<div class="wrap">
    <h2></h2>
    <div class="container" dir="ltr">
        <header class="header">
            <img src="<?php echo esc_url(CHT_PLUGIN_URL.'admin/assets/images/logo.svg'); ?>" alt="Chaty" class="logo">
		    <?php settings_errors(); ?>
            <div class="ml-auto">
                <a class="btn-white" href="<?php echo esc_url(admin_url("admin.php?page=chaty-upgrade")) ?>">
				    <?php esc_attr_e('Create New Widget', CHT_OPT); ?>
                </a>
                <a target="_blank" class="btn-red" href="<?php echo esc_url($this->getUpgradeMenuItemUrl()); ?>">
				    <?php esc_attr_e('Upgrade Now', CHT_OPT); ?>
                    <svg width="17" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.4674 7.42523L11.8646 0.128021C11.7548 0.128021 11.6449 0 11.4252 0C11.3154 0 11.0956 0 10.9858 0.128021L9.44777 1.92032C9.22806 2.17636 9.22806 2.56042 9.33791 2.81647L11.7548 6.017H0.549289C0.219716 6.017 0 6.27304 0 6.6571V9.21753C0 9.60159 0.219716 9.85763 0.549289 9.85763H11.8646L9.44777 13.0582C9.22806 13.3142 9.22806 13.6983 9.44777 13.9543L11.0956 15.6186C11.2055 15.7466 11.3154 15.7466 11.4252 15.7466C11.5351 15.7466 11.7548 15.6186 11.8646 15.4906L17.4674 8.19336C17.5772 8.06534 17.5772 7.68127 17.4674 7.42523Z" transform="translate(0.701416 18.3653) rotate(-90)" fill="white"/>
                    </svg>
                </a>
            </div>
        </header>
        <div class="chaty-table">
            <div class="chaty-table-header">
                <span>Dashboard</span>
                <div class="pull-right">
                    <a class="cht-add-new-widget" href="<?php echo esc_url(admin_url("admin.php?page=chaty-upgrade")) ?>">
                        <?php esc_attr_e('Create New Widget', CHT_OPT); ?>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="fix-width"><?php esc_html_e("Status", CHT_OPT); ?></th>
                        <th><?php esc_html_e("Widget name", CHT_OPT); ?></th>
                        <?php if($date_status) { ?>
                            <th><?php esc_html_e("Created On", CHT_OPT); ?></th>
                        <?php } ?>
                        <th class="fix-width"><?php esc_html_e("Actions", CHT_OPT); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($chaty_widgets as $widget) { ?>
                        <tr id="widget_<?php echo esc_attr($widget['index']) ?>" data-widget="<?php echo esc_attr($widget['index']) ?>" data-nonce="<?php echo esc_attr($widget['nonce']) ?>">
                            <td>
                                <label class="chaty-switch" for="trigger_on_time<?php echo esc_attr($widget['index']) ?>">
                                    <input type="checkbox" class="change-chaty-status" name="chaty_trigger_on_time" id="trigger_on_time<?php echo esc_attr($widget['index']) ?>" value="yes" <?php checked($widget['status'], 1) ?>>
                                    <div class="chaty-slider round"></div>
                                </label>
                            </td>
                            <td class="widget-title"><?php echo esc_attr($widget['title']) ?></td>
                            <?php if($date_status) { ?>
                                <?php if(!empty($widget['created_on'])) {?>
                                    <td><?php echo esc_attr(date("F j, Y"), strtotime($widget['created_on'])) ?></td>
                                <?php } else { ?>
                                    <td>&nbsp;</td>
                                <?php } ?>
                            <?php } ?>
                            <td class="chaty-actions">
                                <a href="<?php echo esc_url(admin_url("admin.php?page=chaty-app&widget=".esc_attr($widget['index']))) ?>"><span class="cht-tooltip" data-title="Edit"><span class="dashicons dashicons-edit-large"></span></span></a>
                                <a class="clone-widget" href="javascript:;"><span class="cht-tooltip" data-title="Duplicate"><span class="dashicons dashicons-admin-page"></span></span></a>
                                <a class="remove-widget" href="javascript:;"><span class="cht-tooltip" data-title="Delete"><span class="dashicons dashicons-trash"></span></span></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="chaty-popup" id="clone-widget">
    <div class="chaty-popup-outer"></div>
    <div class="chaty-popup-inner popup-pos-bottom">
        <div class="chaty-popup-content">
            <div class="chaty-popup-close">
                <a href="javascript:void(0)" class="close-delete-pop close-chaty-popup-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M15.6 15.5c-.53.53-1.38.53-1.91 0L8.05 9.87 2.31 15.6c-.53.53-1.38.53-1.91 0s-.53-1.38 0-1.9l5.65-5.64L.4 2.4C-.13 1.87-.13 1.02.4.49s1.38-.53 1.91 0l5.64 5.63L13.69.39c.53-.53 1.38-.53 1.91 0s.53 1.38 0 1.91L9.94 7.94l5.66 5.65c.52.53.52 1.38 0 1.91z"></path></svg>
                </a>
            </div>
            <form class="" action="<?php echo admin_url("admin.php?page=chaty-widget-settings") ?>" method="get">
                <div class="a-card a-card--normal">
                    <div class="chaty-popup-header">
                        Duplicate Widget?
                    </div>
                    <div class="chaty-popup-body">
                        Please select a name for your new duplicate widget
                        <div class="chaty-popup-input">
                            <input type="text" name="widget_title" id="widget_title">
                            <input type="hidden" name="copy-from" id="widget_clone_id">
                            <input type="hidden" name="page" value="chaty-widget-settings">
                        </div>
                    </div>
                    <input type="hidden" id="delete_widget_id" value="">
                    <div class="chaty-popup-footer">
                        <button type="submit" class="btn btn-primary">Create Widget</button>
                        <button type="button" class="btn btn-default close-chaty-popup-btn">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="chaty-popup" id="delete-widget">
    <div class="chaty-popup-outer"></div>
    <div class="chaty-popup-inner popup-pos-bottom">
        <div class="chaty-popup-content">
            <div class="chaty-popup-close">
                <a href="javascript:void(0)" class="close-delete-pop close-chaty-popup-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M15.6 15.5c-.53.53-1.38.53-1.91 0L8.05 9.87 2.31 15.6c-.53.53-1.38.53-1.91 0s-.53-1.38 0-1.9l5.65-5.64L.4 2.4C-.13 1.87-.13 1.02.4.49s1.38-.53 1.91 0l5.64 5.63L13.69.39c.53-.53 1.38-.53 1.91 0s.53 1.38 0 1.91L9.94 7.94l5.66 5.65c.52.53.52 1.38 0 1.91z"></path></svg>
                </a>
            </div>
            <div class="a-card a-card--normal">
                <div class="chaty-popup-header">
                    Delete Widget?
                </div>
                <div class="chaty-popup-body">
                    Are you sure you want to delete this widget?
                </div>
                <input type="hidden" id="delete_widget_id" value="">
                <div class="chaty-popup-footer">
                    <button type="button" class="btn btn-primary" id="delete-widget-btn" onclick="javascript:removeWidgetItem();">Delete Widget</button>
                    <button type="button" class="btn btn-default close-chaty-popup-btn">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var dataWidget = -1;
jQuery(document).ready(function () {

    jQuery(document).on("click", ".clone-widget", function(){
        window.location = "<?php echo esc_url(admin_url("admin.php?page=chaty-upgrade")); ?>";
    });

    jQuery(document).on("click", ".change-chaty-status", function(e){
        dataWidget = jQuery(this).closest("tr").data("widget");
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'change_chaty_widget_status',
                widget_nonce: jQuery("#widget_"+dataWidget).data("nonce"),
                widget_index: "_"+jQuery("#widget_"+dataWidget).data("widget")
            },
            beforeSend: function (xhr) {

            },
            success: function (res) {

            },
            error: function (xhr, status, error) {

            }
        });
    });

    jQuery(document).on("click", ".remove-widget", function(){
        dataWidget = jQuery(this).closest("tr").data("widget");
        jQuery("#delete-widget").show();
    });
});

function removeWidgetItem() {
    if(dataWidget == -1) {
        return;
    }
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'remove_chaty_widget',
            widget_nonce: jQuery("#widget_"+dataWidget).data("nonce"),
            widget_index: "_"+jQuery("#widget_"+dataWidget).data("widget")
        },
        beforeSend: function (xhr) {

        },
        success: function (res) {
            window.location = res;
        },
        error: function (xhr, status, error) {

        }
    });
}
</script>