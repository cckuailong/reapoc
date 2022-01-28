<?php
// Get IP Method
$ip_method = \WP_STATISTICS\IP::getIPMethod();

// Add TickBox
add_thickbox();
?>
    <!-- Show Help $_SERVER -->
    <style>
        #TB_window {
            direction: ltr;
        }
    </style>
    <div id="list-of-php-server" style="display:none;">
        <table style="direction: ltr;">
            <tr>
                <td width="330" style="border-bottom: 1px solid #ccc;padding-top:10px;padding-bottom:10px;">
                    <b><?php _e('$_SERVER', 'wp-statistics'); ?></b></td>
                <td style="border-bottom: 1px solid #ccc;padding-top:10px;padding-bottom:10px;"><b><?php _e('Value', 'wp-statistics'); ?></b></td>
            </tr>
            <?php
            foreach ($_SERVER as $key => $value) {
                // Check Value is Array
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                ?>
                <tr>
                    <td width="330" style="padding-top:10px;padding-bottom:10px;">
                        <b><?php echo $key; ?></b></td>
                    <td style="padding-top:10px;padding-bottom:10px;"> <?php echo ($value == "" ? "-" : substr(str_replace(array("\n", "\r"), '', trim($value)), 0, 200)) . (strlen($value) > 200 ? '..' : ''); ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <th scope="row" colspan="2" style="padding-bottom: 10px; font-weight: normal;line-height: 25px;">
                <?php _e('Your real IP detected by ipify.org service:', 'wp-statistics'); ?>
            </th>
        </tr>

        <tr valign="top">
            <th scope="row" colspan="2">
                <code id="user_real_ip" style="padding: 15px;font-size: 30px;font-weight: 200; letter-spacing: 2px;font-family: 'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,'Helvetica Neue',sans-serif;">
                    <script type="application/javascript">
                        jQuery(document).ready(function () {
                            jQuery.ajax({
                                url: "https://api.ipify.org?format=json",
                                dataType: 'json',
                                error: function (jqXHR) {
                                    if (jqXHR.status == 0) {
                                        jQuery("code#user_real_ip").html("<?php _e('Please check your internet connection and try again.', 'wp-statistics'); ?>");
                                    }
                                },
                                success: function (json) {
                                    jQuery("code#user_real_ip").html(json['ip']);
                                }
                            });
                        });
                    </script>
                </code></th>
        </tr>

        <tr>
            <td colspan="3">
                <p><?php _e('The items below return the IP address that is different on each server. Is the best way that you choose.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <?php
        foreach (\WP_STATISTICS\IP::$ip_methods_server as $method) {
            ?>
            <tr valign="top">
                <th scope="row" colspan="2" style="padding-top: 8px;padding-bottom: 8px;">
                    <table>
                        <tr>
                            <td style="width: 10px; padding: 0px;">
                                <input type="radio" name="ip_method" style="vertical-align: -3px;" value="<?php echo $method; ?>"<?php if ($ip_method == $method) {
                                    echo " checked=\"checked\"";
                                } ?>>
                            </td>
                            <td style="width: 250px;"> <?php printf(__('Use %1$s', 'wp-statistics'), $method); ?></td>
                            <td><code><?php
                                    if (isset($_SERVER[$method]) and !empty($_SERVER[$method])) {
                                        echo $_SERVER[$method];
                                    } else {
                                        _e('No available data.', 'wp-statistics');
                                    } ?>
                                </code>
                                <?php
                                if (isset($_SERVER[$method]) and !empty($_SERVER[$method]) and \WP_STATISTICS\IP::check_sanitize_ip($_SERVER[$method]) === false) {
                                    echo ' &nbsp;&nbsp;<a href="https://wp-statistics.com/sanitize-user-ip/" style="color: #d04f4f;" target="_blank" title="' . __('Your value required to sanitize user IP', 'wp-statistics') . '"><span class="dashicons dashicons-warning"></span></a>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </th>
            </tr>
            <?php
        }
        ?>

        <!-- Custom Header -->
        <tr valign="top">
            <th scope="row" colspan="2" style="padding-top: 0px;padding-bottom: 0px;">
                <table>
                    <tr>
                        <td style="width: 10px; padding: 0px;">
                            <input type="radio" name="ip_method" style="vertical-align: -3px;" value="CUSTOM_HEADER" <?php if (!in_array($ip_method, \WP_STATISTICS\IP::$ip_methods_server)) {
                                echo " checked=\"checked\"";
                            } ?>>
                        </td>
                        <td style="width: 250px;"> <?php echo __('Use Custom Header', 'wp-statistics'); ?></td>
                        <td style="padding-left: 0px;">
                            <input type="text" name="user_custom_header_ip_method" autocomplete="off" style="padding: 5px; width: 250px;height: 35px;" value="<?php if (!in_array($ip_method, \WP_STATISTICS\IP::$ip_methods_server)) {
                                echo $ip_method;
                            } ?>">

                            <p class="description">
                                <?php if (!in_array($ip_method, \WP_STATISTICS\IP::$ip_methods_server)) {
                                    echo '<code>';
                                    if (isset($_SERVER[$ip_method]) and !empty($_SERVER[$ip_method])) {
                                        echo $_SERVER[$ip_method];
                                    } else {
                                        _e('No available data.', 'wp-statistics');
                                    }
                                }
                                echo '</code>';
                                if (!in_array($ip_method, \WP_STATISTICS\IP::$ip_methods_server) and isset($_SERVER[$ip_method]) and !empty($_SERVER[$ip_method]) and \WP_STATISTICS\IP::check_sanitize_ip($_SERVER[$ip_method]) === false) {
                                    echo ' &nbsp;&nbsp;<a href="https://wp-statistics.com/sanitize-user-ip/" style="color: #d04f4f;" target="_blank" title="' . __('Your value required to sanitize user IP', 'wp-statistics') . '"><span class="dashicons dashicons-warning"></span></a>';
                                }
                                ?></p>
                            <p class="description"><?php _e('Fill this field if your server uses the custom key in <code>$_SERVER</code> for getting IP.', 'wp-statistics'); ?></p>
                            <p class="description"><?php _e('e.g. <code>HTTP_CF_CONNECTING_IP</code> in CloudFlare.', 'wp-statistics'); ?></p>
                            <p class="description">
                                <a href="#TB_inline?&width=850&height=600&inlineId=list-of-php-server" class="thickbox"><?php _e('Show all <code>$_SERVER</code> in your server.', 'wp-statistics'); ?></a>
                            </p>
                        </td>
                    </tr>
                </table>
            </th>
        </tr>

        </tbody>
    </table>

<?php submit_button(__('Update', 'wp-statistics'), 'primary', 'submit');