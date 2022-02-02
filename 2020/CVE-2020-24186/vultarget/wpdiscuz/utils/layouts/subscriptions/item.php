<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div class="wpd-item">
    <div class="wpd-item-left">
        <div class="wpd-item-link wpd-comment-meta">
            <i class="fas fa-user"></i> <?php echo esc_html($author); ?> &nbsp; 
            <i class="fas fa-calendar-alt"></i> <?php echo esc_html($postedDate); ?>
        </div>
        <div class="wpd-item-link wpd-comment-item-link">
            <a class="wpd-comment-link" href="<?php echo $link; ?>" target="_blank" title="<?php echo esc_attr($content); ?>">
                <?php echo $content; ?>
            </a>
        </div>
        <div class="wpd-item-link wpd-post-item-link">
            <i class="far fa-bell"></i> 
            <?php echo esc_html($sTypeInfo); ?>
        </div>
    </div>
    <div class="wpd-item-right">
        <a href="#" class="wpd-delete-content wpd-not-clicked" data-wpd-content-id="<?php echo esc_attr($sId); ?>" data-wpd-delete-action="wpdCancelSubscription" title="<?php esc_attr_e("Cancel this subscription", "wpdiscuz"); ?>">
            <i class="far fa-bell-slash"></i>
        </a>
    </div>
    <div class="wpd-clear"></div>
</div>