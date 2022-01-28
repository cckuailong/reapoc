<?php
// Get the historical number of visitors to the site
$historical_visitors = WP_STATISTICS\Historical::get('visitors');

// Get the historical number of visits to the site
$historical_visits = WP_STATISTICS\Historical::get('visits');

?>
<div class="wrap wps-wrap">
    <form id="wps_historical_form" method="post">
        <?php wp_nonce_field('historical_form', 'wp-statistics-nonce'); ?>
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row" colspan="2"><h3><?php _e('Historical Values', 'wp-statistics'); ?></h3></th>
            </tr>

            <tr valign="top" id="wps_historical_purge" style="display: none">
                <th scope="row" colspan=2>
                    <?php _e('Note: As you have just purged the database you must reload this page for these numbers to be correct.', 'wp-statistics'); ?>
                </th>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <?php _e('Visitors', 'wp-statistics'); ?>:
                </th>
                <td>
                    <input type="text" size="10" value="<?php echo $historical_visitors; ?>" id="wps_historical_visitors" name="wps_historical_visitors">
                    <p class="description"><?php echo sprintf(__('Number of historical number of visitors to the site (current value is %s).', 'wp-statistics'), number_format_i18n($historical_visitors)); ?></p>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <?php _e('Visits', 'wp-statistics'); ?>:
                </th>
                <td>
                    <input type="text" size="10" value="<?php echo $historical_visits; ?>" id="wps_historical_visits" name="wps_historical_visits">
                    <p class="description"><?php echo sprintf(__('Number of historical number of visits to the site (current value is %s).', 'wp-statistics'), number_format_i18n($historical_visits)); ?></p>
                </td>
            </tr>

            <tr valign="top">
                <td colspan=2>
                    <input id="historical-submit" class="button button-primary" type="submit" value="<?php _e('Update Now!', 'wp-statistics'); ?>" name="historical-submit"/>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
