<div style="text-align: center;">
    <a href="https://wp-statistics.com" target="_blank"><img src="<?php echo WP_STATISTICS_URL . 'assets/images/logo-250.png'; ?>" alt="WP-Statistics" class="wps-about-logo"></a>
</div>

<div id="about-links" style="text-align: center;">
    <p>
        <a href="https://wp-statistics.com" target="_blank"><?php _e('Website', 'wp-statistics'); ?></a></p> | <p>
        <a href="https://wordpress.org/support/plugin/wp-statistics/reviews/?rate=5#new-post" target="_blank"><?php _e('Rate & Review', 'wp-statistics'); ?></a>
    </p>
    <?php
    if (\WP_STATISTICS\User::Access('manage')) {
        ?>
        | <p>
            <a href="<?php echo \WP_STATISTICS\Menus::admin_url('settings', array('tab' => 'about')); ?>"><?php _e('More Info', 'wp-statistics'); ?></a>
        </p>
        <?php
    }
    ?>
    <div class="wps-postbox-veronalabs">
        <a href="https://veronalabs.com" target="_blank" title="<?php _e('Power by VeronaLabs', 'wp-statistics'); ?>"><img src="<?php echo WP_STATISTICS_URL; ?>assets/images/veronalabs.svg" alt="VeronaLabs Co"/></a>
    </div>
</div>