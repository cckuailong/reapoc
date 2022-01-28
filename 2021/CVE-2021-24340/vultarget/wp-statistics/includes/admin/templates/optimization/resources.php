<div class="wrap wps-wrap">
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('Resources', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('Memory usage in PHP', 'wp-statistics'); ?>:
            </th>
            <td>
                <strong><?php echo size_format(memory_get_usage(), 3); ?></strong>
                <p class="description"><?php _e('Memory usage in PHP', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('PHP Memory Limit', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php echo ini_get('memory_limit'); ?></strong>
                <p class="description"><?php _e('The memory limit a script is allowed to consume, set in php.ini.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <?php
        foreach ($result as $table_name => $number_row) {
            ?>
            <tr valign="top">
                <th scope="row">
                    <?php echo sprintf(__('Number of rows in the %s table', 'wp-statistics'), '<code>' . $table_name . '</code>'); ?>:
                </th>
                <td>
                    <strong><?php echo number_format_i18n($number_row); ?></strong> <?php echo _n('Row', 'Rows', number_format_i18n($number_row), 'wp-statistics'); ?>
                    <p class="description"><?php _e('Number of rows', 'wp-statistics'); ?></p>
                </td>
            </tr>
            <?php
        }
        ?>

        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('Version Info', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('WP Statistics Version', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php echo WP_STATISTICS_VERSION; ?></strong>
                <p class="description"><?php _e('The WP Statistics version you are running.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('PHP Version', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php echo phpversion(); ?></strong>
                <p class="description"><?php _e('The PHP version you are running.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('PHP Safe Mode', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php if (ini_get('safe_mode')) {
                        _e('Yes', 'wp-statistics');
                    } else {
                        _e('No', 'wp-statistics');
                    } ?></strong>

                <p class="description"><?php _e('Is PHP Safe Mode active. The GeoIP code is not supported in Safe Mode.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('PHP IPv6 Enabled', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php if (defined('AF_INET6')) {
                        _e('Yes', 'wp-statistics');
                    } else {
                        _e('No', 'wp-statistics');
                    } ?></strong>
                <p class="description"><?php _e('Is PHP compiled with IPv6 support. You may see warning messages in your PHP log if it is not and you receive HTTP headers with IPv6 addresses in them.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('jQuery Version', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong>
                    <script type="text/javascript">document.write(jQuery().jquery);</script>
                </strong>

                <p class="description"><?php _e('The jQuery version you are running.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('cURL Version', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php if (function_exists('curl_version')) {
                        $curl_ver = curl_version();
                        echo $curl_ver['version'];
                    } else {
                        _e('cURL not installed', 'wp-statistics');
                    } ?></strong>

                <p class="description"><?php _e(
                        'The PHP cURL Extension version you are running. cURL is required for the GeoIP code, if it is not installed GeoIP will be disabled.',
                        'wp-statistics'
                    ); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('Zlib gzopen()', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php if (function_exists('gzopen')) {
                        _e('Installed', 'wp-statistics');
                    } else {
                        _e('Not installed', 'wp-statistics');
                    } ?></strong>

                <p class="description"><?php _e('If the gzopen() function is installed. The gzopen() function is required for the GeoIP database to be downloaded successfully.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('GMP PHP extension', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php if (extension_loaded('gmp')) {
                        _e('Installed', 'wp-statistics');
                    } else {
                        _e('Not installed', 'wp-statistics');
                    } ?></strong>

                <p class="description"><?php _e('If the GMP Math PHP extension is loaded, either GMP or BCMath is required for the GeoIP database to be read successfully.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('BCMath PHP extension', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php if (extension_loaded('bcmath')) {
                        _e('Installed', 'wp-statistics');
                    } else {
                        _e('Not installed', 'wp-statistics');
                    } ?></strong>

                <p class="description"><?php _e('If the BCMath PHP extension is loaded, either GMP or BCMath is required for the GeoIP database to be read successfully.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('File Info', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('GeoIP Database', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php
                    $GeoIP_filename = \WP_STATISTICS\GeoIP::get_geo_ip_path('country');
                    $GeoIP_filedate = @filemtime($GeoIP_filename);

                    if ($GeoIP_filedate === false) {
                        _e('Database file does not exist.', 'wp-statistics');
                    } else {
                        echo size_format(@filesize($GeoIP_filename), 2) . __(', created on ', 'wp-statistics') . date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), $GeoIP_filedate);
                    } ?></strong>

                <p class="description"><?php _e('The file size and date of the GeoIP database.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('Client Info', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('Client IP', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php echo \WP_STATISTICS\IP::getIP(); ?></strong>
                <p class="description"><?php _e('The client IP address.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('User Agent', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php echo htmlentities(\WP_STATISTICS\UserAgent::getHttpUserAgent(), ENT_QUOTES); ?></strong>
                <p class="description"><?php _e('The client user agent string.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('Browser', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php $agent = \WP_STATISTICS\UserAgent::getUserAgent();
                    echo $agent['browser'];
                    ?></strong>

                <p class="description"><?php _e('The detected client browser.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('Version', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php echo $agent['version']; ?></strong>
                <p class="description"><?php _e('The detected client browser version.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e('Platform', 'wp-statistics'); ?>:
            </th>

            <td>
                <strong><?php echo $agent['platform']; ?></strong>
                <p class="description"><?php _e('The detected client platform.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('Server Info', 'wp-statistics'); ?></h3></th>
        </tr>

        <?php
        $list = array('SERVER_SOFTWARE', 'HTTP_HOST', 'REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_REAL_IP',);
        foreach ($list as $server) {
            if (isset($_SERVER[$server])) {
                echo '<tr valign="top">
                     <th scope="row">
                    ' . $server . '
                    </th>
                    <td>
                        <strong>' . $_SERVER[$server] . '</strong>
                    </td>
                </tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>