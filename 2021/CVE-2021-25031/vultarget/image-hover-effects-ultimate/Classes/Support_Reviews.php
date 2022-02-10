<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Classes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Support_Reviews
 *
 * @author $biplob018
 */
class Support_Reviews {

    /**
     * Revoke this function when the object is created.
     *
     */
    public function __construct() {
        add_action('admin_notices', array($this, 'first_install'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_notices', array($this, 'dismiss_button_scripts'));
    }

    /**
     * First Installation Track
     * @return void
     */
    public function first_install() {
        if (!current_user_can('manage_options')) {
            return;
        }
        $image = OXI_IMAGE_HOVER_URL . 'image/logo.png';
        echo _(' <div class="notice notice-info put-dismiss-noticenotice-has-thumbnail shortcode-addons-review-notice oxilab-image-hover-review-notice">
                    <div class="shortcode-addons-notice-thumbnail">
                        <img src="' . $image . '" alt=""></div>
                    <div class="shortcode-addons--notice-message">
                        <p>Hey, You’ve using <strong>Image Hover Effects Ultimate – Captions Hover with Visual Composer Extension</strong> more than 1 week – that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.!</p>
                        <ul class="shortcode-addons--notice-link">
                            <li>
                                <a href="https://wordpress.org/support/plugin/image-hover-effects-ultimate/reviews/" target="_blank">
                                    <span class="dashicons dashicons-external"></span>Ok, you deserve it!
                                </a>
                            </li>
                            <li>
                                <a class="oxi-image-support-reviews" sup-data="success" href="#">
                                    <span class="dashicons dashicons-smiley"></span>I already did
                                </a>
                            </li>
                            <li>
                                <a class="oxi-image-support-reviews" sup-data="maybe" href="#">
                                    <span class="dashicons dashicons-calendar-alt"></span>Maybe Later
                                </a>
                            </li>
                            <li>
                                <a href="https://wordpress.org/support/plugin/image-hover-effects-ultimate/">
                                    <span class="dashicons dashicons-sos"></span>I need help
                                </a>
                            </li>
                            <li>
                                <a class="oxi-image-support-reviews" sup-data="never"  href="#">
                                    <span class="dashicons dashicons-dismiss"></span>Never show again
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>');
    }

    /**
     * Admin Notice JS file loader
     * @return void
     */
    public function dismiss_button_scripts() {
        wp_enqueue_script('oxi-image-admin-notice', OXI_IMAGE_HOVER_URL . '/assets/backend/js/admin-notice.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_localize_script('oxi-image-admin-notice', 'ImageHoverUltimate', array(
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }

    /**
     * Admin Notice CSS file loader
     * @return void
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_script("jquery");
        wp_enqueue_style('oxi-image-admin-notice-css', OXI_IMAGE_HOVER_URL . '/assets/backend/css/notice.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->dismiss_button_scripts();
    }

}
