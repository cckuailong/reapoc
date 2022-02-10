<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


/* Check if Futurio theme is activated */


add_action('admin_notices', 'futurio_extra_admin_notices', 0);

function futurio_extra_requirements() {
    $futurio_extra_errors = array();
    $theme = wp_get_theme();

    if (( 'Futurio' != $theme->name ) && ( 'Futurio' != $theme->parent_theme )) {
        $futurio_extra_errors[] = sprintf(__('You need to have <a href="%s" target="_blank">Futurio</a> theme in order to use Futurio Extra plugin.', 'futurio-extra'), esc_url(admin_url('theme-install.php?theme=futurio')));
    }
    return $futurio_extra_errors;
}

function futurio_extra_admin_notices() {

    $futurio_extra_errors = futurio_extra_requirements();

    if (empty($futurio_extra_errors))
        return;

    echo '<div class="notice error futurio-credits-notice is-dismissible">';
    echo '<p>' . join($futurio_extra_errors) . '</p>';
    echo '</div>';
}

/**
 * @review_dismiss()
 * @review_pending()
 * @futurio_review_notice_message()
 * Make all the above functions working.
 */
function futurio_review_notice() {

    futurio_review_dismiss();
    futurio_review_pending();

    $activation_time = get_site_option('futurio_active_time');
    $review_dismissal = get_site_option('futurio_review_dismiss');
    $maybe_later = get_site_option('futurio_maybe_later');

    if ('yes' == $review_dismissal) {
        return;
    }

    if (!$activation_time) {
        add_site_option('futurio_active_time', time());
    }

    $daysinseconds = 604800; // 7 Days in seconds.
    if ('yes' == $maybe_later) {
        $daysinseconds = 1209600; // 14 Days in seconds.
    }

    if (time() - $activation_time > $daysinseconds) {
        add_action('admin_notices', 'futurio_review_notice_message');
    }
}

add_action('admin_init', 'futurio_review_notice');

/**
 * For the notice preview.
 */
function futurio_review_notice_message() {
    $scheme = (parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)) ? '&' : '?';
    $url = $_SERVER['REQUEST_URI'] . $scheme . 'futurio_review_dismiss=yes';
    $dismiss_url = wp_nonce_url($url, 'futurio-review-nonce');

    $_later_link = $_SERVER['REQUEST_URI'] . $scheme . 'futurio_review_later=yes';
    $later_url = wp_nonce_url($_later_link, 'futurio-review-nonce');
    ?>

    <div class="futurio-review-notice">
        <div class="futurio-review-thumbnail">
            <img src="<?php echo esc_url(get_template_directory_uri()) . '/img/futurio-logo.png'; ?>" alt="">
        </div>
        <div class="futurio-review-text">
            <h3><?php esc_html_e('Leave A Review?', 'futurio-extra') ?></h3>
            <p><?php esc_html_e('We hope you\'ve enjoyed using Futurio theme! Would you consider leaving us a review on WordPress.org?', 'futurio-extra') ?></p>
            <ul class="futurio-review-ul">
                <li>
                    <a href="https://wordpress.org/support/theme/futurio/reviews/?rate=5#new-post" target="_blank">
                        <span class="dashicons dashicons-external"></span>
                        <?php esc_html_e('Sure! I\'d love to!', 'futurio-extra') ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $dismiss_url ?>">
                        <span class="dashicons dashicons-smiley"></span>
                        <?php esc_html_e('I\'ve already left a review', 'futurio-extra') ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $later_url ?>">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <?php esc_html_e('Maybe Later', 'futurio-extra') ?>
                    </a>
                </li>
                <li>
                    <a href="https://futuriowp.com/support/" target="_blank">
                        <span class="dashicons dashicons-sos"></span>
                        <?php esc_html_e('I need help!', 'futurio-extra') ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $dismiss_url ?>">
                        <span class="dashicons dashicons-dismiss"></span>
                        <?php esc_html_e('Never show again', 'futurio-extra') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <?php
}

/**
 * For Dismiss! 
 */
function futurio_review_dismiss() {

    if (!is_admin() ||
            !current_user_can('manage_options') ||
            !isset($_GET['_wpnonce']) ||
            !wp_verify_nonce(sanitize_key(wp_unslash($_GET['_wpnonce'])), 'futurio-review-nonce') ||
            !isset($_GET['futurio_review_dismiss'])) {

        return;
    }

    add_site_option('futurio_review_dismiss', 'yes');
}

/**
 * For Maybe Later Update.
 */
function futurio_review_pending() {

    if (!is_admin() ||
            !current_user_can('manage_options') ||
            !isset($_GET['_wpnonce']) ||
            !wp_verify_nonce(sanitize_key(wp_unslash($_GET['_wpnonce'])), 'futurio-review-nonce') ||
            !isset($_GET['futurio_review_later'])) {

        return;
    }
    // Reset Time to current time.
    update_site_option('futurio_active_time', time());
    update_site_option('futurio_maybe_later', 'yes');
}

function futurio_pro_notice() {

    futurio_pro_dismiss();

    $activation_time = get_site_option('futurio_active_time');

    if (!$activation_time) {
        add_site_option('futurio_active_time', time());
    }

    $daysinseconds = 432000; // 5 Days in seconds (432000).

    if (time() - $activation_time > $daysinseconds) {
        if (!defined('FUTURIO_PRO_CURRENT_VERSION')) {
            add_action('admin_notices', 'futurio_pro_notice_message');
        }
    }
}

add_action('admin_init', 'futurio_pro_notice');

/**
 * For PRO notice 
 */
function futurio_pro_notice_message() {
    $scheme = (parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)) ? '&' : '?';
    $url = $_SERVER['REQUEST_URI'] . $scheme . 'futurio_pro_dismiss=yes';
    $dismiss_url = wp_nonce_url($url, 'futurio-pro-nonce');
    ?>

    <div class="futurio-review-notice">
        <div class="futurio-review-thumbnail">
            <img src="<?php echo esc_url(FUTURIO_EXTRA_PLUGIN_URL) . 'img/futurio-pro-logo.png'; ?>" alt="">
        </div>
        <div class="futurio-review-text">
            <h3><?php esc_html_e('Go PRO for More Features', 'futurio-extra') ?></h3>
            <p><?php _e('Get the  <a href="https://futuriowp.com/futurio-pro/" target="_blank">Pro version</a> for more stunning elements, demos and customization options. Now with 25% discount for lifetime plan.', 'futurio-extra') ?></p>
            <ul class="futurio-review-ul">
                <li class="show-mor-message">
                    <a href="https://futuriowp.com/futurio-pro/" target="_blank">
                        <span class="dashicons dashicons-external"></span>
                        <?php esc_html_e('Show me more', 'futurio-extra') ?>
                    </a>
                </li>
                <li class="hide-message">
                    <a href="<?php echo $dismiss_url ?>">
                        <span class="dashicons dashicons-smiley"></span>
                        <?php esc_html_e('Hide this message', 'futurio-extra') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <?php
}

/**
 * For PRO Dismiss! 
 */
function futurio_pro_dismiss() {

    if (!is_admin() ||
            !current_user_can('manage_options') ||
            !isset($_GET['_wpnonce']) ||
            !wp_verify_nonce(sanitize_key(wp_unslash($_GET['_wpnonce'])), 'futurio-pro-nonce') ||
            !isset($_GET['futurio_pro_dismiss'])) {

        return;
    }
    $daysinseconds = 1209600; // 14 Days in seconds.
    $newtime = time() + $daysinseconds;
    update_site_option('futurio_active_time', $newtime);
}
