<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<li class='wpd-list-item' data-action="wpdGetSubscriptionsPage">
    <i class='fas fa-bell'></i>
    <span><?php echo esc_html($this->options->phrases["wc_user_settings_subscriptions"]); ?></span>
    <input class='wpd-rel' type='hidden' value='wpd-content-item-2'/>
</li>