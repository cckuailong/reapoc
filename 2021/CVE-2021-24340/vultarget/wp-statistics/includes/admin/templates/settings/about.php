<table class="form-table">
    <tbody>
    <tr valign="top">
        <td scope="row" align="center">
            <a href="https://wp-statistics.com" target="_blank">
                <img src="<?php echo WP_STATISTICS_URL . 'assets/images/logo-250.png'; ?>">
            </a>
        </td>
    </tr>

    <tr valign="top">
        <td scope="row" align="center">
            <h2><?php echo sprintf(__('WP Statistics V%s', 'wp-statistics'), WP_STATISTICS_VERSION); ?></h2>
        </td>
    </tr>

    <tr valign="top">
        <td scope="row" align="center">
            <?php echo sprintf(
                __('This product includes GeoLite2 data created by %s.', 'wp-statistics'),
                '<a href="http://www.maxmind.com" target=_blank>MaxMind</a>'
            ); ?>
        </td>
    </tr>

    <tr valign="top">
        <td scope="row" align="center">
            <hr/>
        </td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><h2><?php _e('Donate', 'wp-statistics'); ?></h2></td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><?php echo sprintf(
                __(
                    'Feel like showing us how much you enjoy WP Statistics? Drop by our %sdonation%s page and show us some love!',
                    'wp-statistics'
                ),
                '<a href="https://wp-statistics.com/donate" target="_blank">',
                '</a>'
            ); ?></td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><h2><?php _e('Visit Us Online', 'wp-statistics'); ?></h2></td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><?php echo sprintf(
                __(
                    'Come visit our great new %swebsite%s and keep up to date on the latest news about WP Statistics.',
                    'wp-statistics'
                ),
                '<a href="https://wp-statistics.com" target="_blank">',
                '</a>'
            ); ?></td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><h2><?php _e('Rate and Review at WordPress.org', 'wp-statistics'); ?></h2></td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><?php printf(
                __(
                    'Thanks for installing WP Statistics, we encourage you to submit a %srating and review%s over at WordPress.org. Your feedback is greatly appreciated!',
                    'wp-statistics'
                ),
                '<a href="https://wordpress.org/support/plugin/wp-statistics/reviews/?rate=5#new-post" target="_blank">',
                '</a>'
            ); ?>
        </td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><h2><?php _e('Translations', 'wp-statistics'); ?></h2></td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><?php echo sprintf(
                __(
                    'WP Statistics supports internationalization. Please visit %sWP Statistics translations page%s to help translation.',
                    'wp-statistics'
                ),
                '<a href="https://wp-statistics.com/translations/" target="_blank">',
                '</a>'
            ); ?></td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2"><h2><?php _e('Support', 'wp-statistics'); ?></h2></td>
    </tr>

    <tr valign="top">
        <td scope="row" colspan="2">
            <p><?php _e(
                    "We're sorry you're having problem with WP Statistics and we're happy to help out. Here are a few things to do before contacting us:",
                    'wp-statistics'
                ); ?></p>

            <ul style="list-style-type: disc; list-style-position: inside; padding-left: 25px;">
                <li><?php echo sprintf(
                        __('Have you read the %sFAQs%s?', 'wp-statistics'),
                        '<a title="' .
                        __('FAQs', 'wp-statistics') .
                        '" href="https://wp-statistics.com/category/faq/" target="_blank">',
                        '</a>'
                    ); ?></li>
                <li><?php echo sprintf(
                        __('Have you read the %sdocumentation%s?', 'wp-statistics'),
                        '<a title="' .
                        __('Documentation', 'wp-statistics') .
                        '" href="https://wp-statistics.com/category/documentation/">',
                        '</a>'
                    ); ?></li>
                <li><?php echo sprintf(
                        __('Have you search the %ssupport forum%s for a similar issue?', 'wp-statistics'),
                        '<a href="http://wordpress.org/support/plugin/wp-statistics" target="_blank">',
                        '</a>'
                    ); ?></li>
                <li><?php _e(
                        'Have you search the Internet for any error messages you are receiving?',
                        'wp-statistics'
                    ); ?></li>
                <li><?php _e('Make sure you have access to your PHP error logs.', 'wp-statistics'); ?></li>
            </ul>

            <p><?php _e('And a few things to double-check:', 'wp-statistics'); ?></p>

            <ul style="list-style-type: disc; list-style-position: inside; padding-left: 25px;">
                <li><?php _e('How\'s your memory_limit in php.ini?', 'wp-statistics'); ?></li>
                <li><?php _e(
                        'Have you tried disabling any other plugins you may have installed?',
                        'wp-statistics'
                    ); ?></li>
                <li><?php _e('Have you tried using the default WordPress theme?', 'wp-statistics'); ?></li>
                <li><?php _e('Have you double checked the plugin settings?', 'wp-statistics'); ?></li>
                <li><?php _e('Do you have all the required PHP extensions installed?', 'wp-statistics'); ?></li>
                <li><?php echo __(
                            'Are you getting a blank or incomplete page displayed in your browser?',
                            'wp-statistics'
                        ) .
                        ' ' .
                        __(
                            'Did you view the source for the page and check for any fatal errors?',
                            'wp-statistics'
                        ); ?></li>
                <li><?php _e('Have you checked your PHP and web server error logs?', 'wp-statistics'); ?></li>
            </ul>

            <p><?php _e('Still not having any luck?', 'wp-statistics'); ?><?php echo sprintf(
                    __(
                        'Then please open a new thread on the %sWordPress.org support forum%s and we\'ll respond as soon as possible.',
                        'wp-statistics'
                    ),
                    '<a href="http://wordpress.org/support/plugin/wp-statistics" target="_blank">',
                    '</a>'
                ); ?></p>
        </td>
    </tr>

    </tbody>
</table>