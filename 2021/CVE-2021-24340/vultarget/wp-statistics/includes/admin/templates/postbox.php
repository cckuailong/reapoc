<div id="postbox-container-1" class="postbox-container">
    <div class="meta-box-sortables">
        <div id="wps-plugins-support" class="postbox">
            <h2 class="hndle"><span><?php _e('Subscribe to Newsletter', 'wp-statistics'); ?></span></h2>

            <div class="inside">
                <form action="//veronalabs.us7.list-manage.com/subscribe/post?u=628dc1468c7c7db0ba42f0137&amp;id=ea320ea877" method="post" name="mc-embedded-subscribe-form" target="_blank" novalidate>
                    <p><?php _e('Subscribe to our mailing list for get any news of the WP-Statistics', 'wp-statistics'); ?></p>
                    <input name="EMAIL" type="email" class="ltr" value="<?php bloginfo('admin_email'); ?>">
                    <input type="hidden" name="b_628dc1468c7c7db0ba42f0137_ea320ea877" tabindex="-1" value="">
                    <input type="submit" value="<?php _e('Subscribe', 'wp-statistics'); ?>" name="subscribe" class="button">
                </form>
            </div>
        </div>
    </div>

    <?php
    // Check Disable PostBox
    if (apply_filters('wp_statistics_ads_setting_page_show', true) === false) {
        return;
    }

    $response      = wp_remote_get('https://wp-statistics.com/wp-json/plugin/postbox');
    $response_code = wp_remote_retrieve_response_code($response);

    if (!is_wp_error($response) and $response_code == '200') :
        $result = json_decode($response['body']);
        foreach ($result->items as $item) : ?>
            <div class="meta-box-sortables">
                <div id="wps-plugins-support" class="postbox">
                    <h2 class="hndle"><span><?php echo $item->title; ?></span></h2>
                    <div class="inside"><?php echo $item->content; ?></div>
                </div>
            </div>
        <?php
        endforeach;
    endif;
    ?>
</div>