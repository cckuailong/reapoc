<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#hash-ips-submit").click(function () {
            var agree = confirm('<?php _e('This will replace all IP addresses in the database with hash values and cannot be undo, are you sure?', 'wp-statistics'); ?>');

            if (agree)
                location.href = document.URL + '&tab=updates&hash-ips=1';

        });
    });
</script>
<div class="wrap wps-wrap">
    <table class="form-table">
        <tbody>
        <?php if (\WP_STATISTICS\GeoIP::active()) { ?>
            <tr valign="top">
                <th scope="row" colspan="2"><h3><?php _e('GeoIP Options', 'wp-statistics'); ?></h3></th>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="populate-submit"><?php _e('Countries:', 'wp-statistics'); ?></label>
                </th>

                <td>
                    <input id="populate-submit" class="button button-primary" type="button" value="<?php _e('Update Now!', 'wp-statistics'); ?>" name="populate-submit" onclick="location.href=document.URL+'&tab=updates&populate=1'">
                    <p class="description"><?php _e('Updates any unknown location data in the database, this may take a while', 'wp-statistics'); ?></p>
                </td>
            </tr>
        <?php } ?>

        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('IP Addresses', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="populate-submit"><?php _e('Hash IP Addresses:', 'wp-statistics'); ?></label>
            </th>

            <td>
                <input id="hash-ips-submit" class="button button-primary" type="button" value="<?php _e('Update Now!', 'wp-statistics'); ?>" name="hash-ips-submit">
                <p class="description"><?php _e('Replace IP addresses in the database with hash values, you will not be able to recover the IP addresses in the future to populate location information afterwards and this may take a while', 'wp-statistics'); ?></p>
            </td>
        </tr>

        </tbody>
    </table>
</div>