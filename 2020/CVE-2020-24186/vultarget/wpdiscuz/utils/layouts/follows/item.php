<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div class="wpd-item wpd-follow-item">
    <div class="wpd-item-left">
        <div class="wpd-item-link wpd-comment-meta">
            <i class="fas fa-user"></i> &nbsp; <span class="wpd-fl-name"><?php echo esc_html($fName); ?></span> &nbsp;&nbsp;
            <br><i class="fas fa-calendar-alt"></i> &nbsp; <span class="wpd-fl-date"><?php echo esc_html($postedDate); ?></span>
        </div>
    </div>
    <div class="wpd-item-right">
        <a href="#" class="wpd-delete-content wpd-not-clicked" data-wpd-content-id="<?php echo esc_attr($fId); ?>" data-wpd-delete-action="wpdCancelFollow" title="<?php esc_attr_e("Cancel this follow", "wpdiscuz"); ?>">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
    <div class="wpd-clear"></div>
</div>