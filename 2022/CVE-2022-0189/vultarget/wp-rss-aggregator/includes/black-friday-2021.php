<?php

add_action('admin_notices', function () {
    if (!wprss_is_wprss_page() || count(wprss_get_addons()) > 0) {
        return;
    }

    $year = (int) date('Y');
    $month = (int) date('n');
    $day = (int) date('j');

    if ($year !== 2021 || $month !== 11 || $day < 22 || $day > 29) {
        return;
    }

    printf(
        '<div class="notice notice-info">
            <p>
                %s
                <a href="https://www.wprssaggregator.com/pricing/" target="_blank"><b>%s</b></a>
                %s
            </p>
        </div>',
        __('Black Friday/Cyber Monday:', 'wprss'),
        __('Get 30% off WP RSS Aggregator plans today!', 'wprss'),
        __('Offer ends on the 29th of November.', 'wprss')
    );
});
