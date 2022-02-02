<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<li class='wpd-list-item' data-action="wpdGetActivityPage">
    <i class='fas fa-comments'></i>
    <span><?php echo esc_html($this->options->phrases["wc_user_settings_activity"]); ?></span>
    <input class='wpd-rel' type='hidden' value='wpd-content-item-1'/>
</li>