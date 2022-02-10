<?php

/**
 * Retrieves the system information.
 *
 * @since 4.17
 *
 * @return string
 */
function wpra_get_sys_info()
{
    ob_start();

    wprss_print_system_info();

    return ob_get_clean();
}

/**
 * Prints the system information
 *
 * @since 4.6.8
 */
function wprss_print_system_info() {
    global $wpdb;

    if ( ! class_exists( 'Browser' ) )
        require_once WPRSS_DIR . 'includes/libraries/browser.php';

    $browser = new Browser();

?>
### Begin System Info ###

## Please include this information when posting support requests ##

Multi-site:               <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

SITE_URL:                 <?php echo site_url() . "\n"; ?>
HOME_URL:                 <?php echo home_url() . "\n"; ?>

Plugin Version:           <?php echo WPRSS_VERSION . "\n"; ?>
WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>

<?php echo htmlspecialchars((string) $browser) ; ?>

PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            <?php $server_info = wprss_sysinfo_get_db_server();
                                if ( $server_info ) {
                                    if (isset($server_info['warning'])) {
                                        printf(
                                            '%s - %s',
                                            htmlspecialchars($server_info['extension']),
                                            htmlspecialchars($server_info['warning'])
                                        );
                                    } else {
                                        printf(
                                                '%1$s (%2$s)',
                                                htmlspecialchars($server_info['server_info']),
                                                htmlspecialchars($server_info['extension'])
                                            );
                                        }
                                } else {
                                    _e( 'Could not determine database driver version', 'wprss' );
                                }
                            ?>

Web Server Info:          <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE']) . "\n"; ?>

PHP Safe Mode:            <?php if (version_compare(PHP_VERSION, '5.4', '>=')) {
                                    echo "No\n";
                                } else {
                                    echo ini_get( 'safe_mode' ) ? "Yes" : "No\n";
                                } ?>
PHP Memory Limit:         <?php echo ini_get( 'memory_limit' ) . "\n"; ?>
PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>

WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

WP Table Prefix:          <?php
                                $wpdbPrefixLen = strlen($wpdb->prefix);
                                echo 'Length: ' . $wpdbPrefixLen;
                                echo ' | Status: ';
                                echo ($wpdbPrefixLen > 16)
                                    ? 'ERROR: Too Long'
                                    : 'Acceptable';
                                echo "\n";
                            ?>

Show On Front:            <?php echo get_option( 'show_on_front' ) . "\n" ?>
Page On Front:            <?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' #' . $id . "\n" ?>
Page For Posts:           <?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' #' . $id . "\n" ?>

Session:                  <?php echo isset( $_SESSION ) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
Session Name:             <?php echo esc_html( ini_get( 'session.name' ) ); ?><?php echo "\n"; ?>
Cookie Path:              <?php echo esc_html( ini_get( 'session.cookie_path' ) ); ?><?php echo "\n"; ?>
Save Path:                <?php echo esc_html( ini_get( 'session.save_path' ) ); ?><?php echo "\n"; ?>
Use Cookies:              <?php echo ini_get( 'session.use_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>
Use Only Cookies:         <?php echo ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>

UPLOAD_MAX_FILESIZE:      <?php if ( function_exists( 'phpversion' ) ) echo ( wprss_let_to_num( ini_get( 'upload_max_filesize' ) )/( 1024*1024 ) )."MB"; ?><?php echo "\n"; ?>
POST_MAX_SIZE:            <?php if ( function_exists( 'phpversion' ) ) echo ( wprss_let_to_num( ini_get( 'post_max_size' ) )/( 1024*1024 ) )."MB"; ?><?php echo "\n"; ?>
WordPress Memory Limit:   <?php echo ( wprss_let_to_num( WP_MEMORY_LIMIT )/( 1024*1024 ) )."MB"; ?><?php echo "\n"; ?>
DISPLAY ERRORS:           <?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
FSOCKOPEN:                <?php echo ( function_exists( 'fsockopen' ) ) ? __( 'Your server supports fsockopen.', 'wprss' ) : __( 'Your server does not support fsockopen.', 'wprss' ); ?><?php echo "\n"; ?>

PLUGIN MODULES:

<?php
foreach (wpra_modules() as $key => $module) {
    echo ' - ' . htmlspecialchars($key) . PHP_EOL;
}
?>

ACTIVE PLUGINS:

<?php
$plugins = get_plugins();
$active_plugins = get_option('active_plugins', []);
$inactive_plugins = [];
foreach ($plugins as $plugin_path => $plugin) {
    // If the plugin isn't active, don't show it.
    if (!in_array($plugin_path, $active_plugins)) {
        $inactive_plugins[] = $plugin;
        continue;
    }

    echo htmlspecialchars($plugin['Name']) . ': ' . htmlspecialchars($plugin['Version']) . "\n";
}

if (is_multisite()): ?>

NETWORK ACTIVE PLUGINS:

<?php
$plugins = wp_get_active_network_plugins();
$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

foreach ( $plugins as $plugin_path ) {
    $plugin_base = plugin_basename( $plugin_path );

    // If the plugin isn't active, don't show it.
    if ( !array_key_exists( $plugin_base, $active_plugins ) ) {
        continue;
    }

    $plugin = get_plugin_data( $plugin_path );

    echo htmlspecialchars($plugin['Name']) . ': ' . htmlspecialchars($plugin['Version']) . "\n";
}

endif;

if ( !is_multisite() ) : ?>

DEACTIVATED PLUGINS:

<?php
    foreach ( $inactive_plugins as $plugin ) {
        echo htmlspecialchars($plugin['Name']) . ': ' . htmlspecialchars($plugin['Version']) . "\n";
    }

endif;
?>

CURRENT THEME:

<?php
$theme_data = wp_get_theme();
echo htmlspecialchars($theme_data->Name) . ': ' . htmlspecialchars($theme_data->Version);
?>


SETTINGS:

<?php
$options_table = $wpdb->prefix . 'options';
$options_query = sprintf(
    'SELECT * FROM %s WHERE `option_name` LIKE "wprss%%" OR `option_name` LIKE "wpra%%"',
    $options_table
);
$options = $wpdb->get_results($options_query, OBJECT_K);

$options = apply_filters('wpra/debug/sysinfo/options', $options);

foreach ($options as $option) {
    $unserialized = maybe_unserialize($option->option_value);
    $value = apply_filters('wpra/debug/sysinfo/option_value', $unserialized, $option->option_name);

    if ($value === null) {
        continue;
    }

    if (!$value || is_scalar($value)) {
        printf(
            '%s %s',
            str_pad($option->option_name, 30),
            $option->option_value
        );
        echo PHP_EOL;
        continue;
    }

    printf('[%s]: ', $option->option_name);
    print_r($value);
}

?>

PHP EXTENSIONS:

<?php
$extensions = get_loaded_extensions();
sort($extensions);

foreach ($extensions as $extension) {
    echo '- ' . $extension . PHP_EOL;
}

?>

### End System Info ###
<?php
}


/**
 * Retrieves information about the DB server.
 *
 * Will use WordPress configuration by default;
 * Currently, the following members are present in the result:
 *  - 'extension': The extension that is used to connect. Possible values: 'mysqli', 'mysql'.
 *  - 'server_info': The version number of the database engine, i.e. '5.6.22'.
 *
 * @since 4.7.2
 * @param null|string $host The address of the database host, to which to connect.
 *	May contain the port number in standard URI format.
 *  Default: value of the DB_HOST constant, if defined, otherwise null.
 * @param null|string $username The username to be used for connecting to the database.
 *  Default: value of the DB_USER constant, if defined, otherwise null.
 * @param null|string $password The password to be used for connecting to the database.
 *	Default: value of the DB_PASSWORD constant, if defined, otherwise null.
 * @param null|int $port An integer, representing the port, at which to connect to the DB server.
 *	Default: auto-determined from host.
 * @return array|null An array, containing the following indexes, if successful: 'extension', 'server_info'.
 *	Otherwise, null.
 */
function wprss_sysinfo_get_db_server( $host = null, $username = null, $password = null, $port = null ) {
    $result = array();

    if ( is_null( $host ) && defined( 'DB_HOST') ) $host = DB_HOST;
    if ( is_null( $username ) && defined( 'DB_USER') ) $username = DB_USER;
    if ( is_null( $password ) && defined( 'DB_PASSWORD') ) $password = DB_PASSWORD;

    $server_address = explode( ':', $host, 2 );
    $host = $server_address[0];
    $port = is_null( $port )
        ? ( isset( $server_address[1] ) ? $server_address[1] : null )
        : $port;
    $port = $port ? intval( (string)$port ) : null;

    if ( function_exists( 'mysqli_get_server_info' ) ){
        $mysqli = new mysqli( $host, $username, $password, '', $port );
        $result['extension'] = 'mysqli';
        $result['server_info'] = $mysqli->server_info;
        return $result;
    }

    return null;
}
