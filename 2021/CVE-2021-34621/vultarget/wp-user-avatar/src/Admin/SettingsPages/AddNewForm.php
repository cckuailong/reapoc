<?php

namespace ProfilePress\Core\Admin\SettingsPages;

// Exit if accessed directly
use ProfilePress\Core\Classes\AjaxHandler;
use ProfilePress\Core\Classes\FormRepository;
use ProfilePress\Custom_Settings_Page_Api;

if ( ! defined('ABSPATH')) {
    exit;
}

class AddNewForm extends AbstractSettingsPage
{
    /**
     * Build the settings page structure. I.e tab, sidebar.
     */
    public function settings_admin_page()
    {
        add_action('wp_cspa_before_closing_header', [$this, 'back_to_overview']);
        add_action('wp_cspa_before_post_body_content', array($this, 'sub_header'), 10, 2);
        add_filter('wp_cspa_main_content_area', [$this, 'form_list']);

        $instance = Custom_Settings_Page_Api::instance();
        if ($_GET['page'] == PPRESS_MEMBER_DIRECTORIES_SLUG) {
            $instance->page_header(__('Add Member Directory', 'wp-user-avatar'));
        }
        $this->register_core_settings($instance, true);
        $instance->build(true);
    }

    /**
     */
    public function sub_header()
    {
        if ( ! empty($_GET['page']) && in_array($_GET['page'], [PPRESS_FORMS_SETTINGS_SLUG])) : ?>
            <div class="pp-add-new-form-wrapper">
                <div class="profile-press-design-gateway">
                    <div class="profile-press-design-gateway-inner">
                        <div class="pp-half clearfix">
                            <div class="pp-hald-first ppbd-active" data-builder-type="dragDropBuilder">
                                <div class="pp-half-meta-inner">
                                    <div class="pp-half-first-thumb responsive-image">
                                        <img src="<?= PPRESS_ASSETS_URL; ?>/images/admin/dragdrop-builder-icon.png">
                                    </div>
                                    <div class="pp-half-meta">
                                        <h2><?php _e('Drag & Drop Builder', 'wp-user-avatar') ?></h2>
                                        <p><?php _e('Create beautiful, responsive forms with easy to use drag & drop form builder.', 'wp-user-avatar'); ?></p>
                                    </div>
                                </div>
                                <button class="pp-builder-create-btn"><?php _e('Get Started', 'wp-user-avatar'); ?></button>
                            </div>

                            <?php if (class_exists('ProfilePress\Libsodium\Libsodium')) : ?>
                                <div class="pp-hald-first ppbd-active" data-builder-type="shortcodeBuilder">
                                    <div class="pp-half-meta-inner">
                                        <div class="pp-half-first-thumb responsive-image">
                                            <img src="<?= PPRESS_ASSETS_URL; ?>/images/admin/shortcode-builder-icon.png">
                                        </div>
                                        <div class="pp-half-meta">
                                            <h2><?php _e('Shortcode Builder', 'wp-user-avatar'); ?></h2>
                                            <p><?php _e('Code your own from scratch with complete control and flexibility using shortcodes.', 'wp-user-avatar'); ?></p>
                                        </div>
                                    </div>
                                    <button class="pp-builder-create-btn"><?php _e('Build Now', 'wp-user-avatar'); ?></button>
                                </div>
                            <?php endif; ?>

                            <?php if ( ! class_exists('ProfilePress\Libsodium\Libsodium')) : ?>
                                <div class="pp-hald-first">
                                    <a target="_blank" href='https://profilepress.net/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=shortcode_builder_upsell'>
                                        <div class="pp-half-meta-inner">
                                            <div class="pp-half-first-thumb responsive-image">
                                                <img src="<?= PPRESS_ASSETS_URL; ?>/images/admin/shortcode-builder-icon.png">
                                            </div>
                                            <div class="pp-half-meta">
                                                <h2><?php _e('Shortcode Builder', 'wp-user-avatar'); ?></h2>
                                                <p><?php _e('Code your own from scratch with complete control and flexibility using shortcodes.', 'wp-user-avatar'); ?></p>
                                            </div>
                                        </div>
                                        <button class="pp-builder-create-btn"><?php _e('Upgrade to Premium', 'wp-user-avatar'); ?></button>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="pp-main-ajax-body"></div>
            </div>
        <?php endif;


        if (in_array($_GET['page'], [PPRESS_MEMBER_DIRECTORIES_SLUG])) {

            AjaxHandler::get_instance()->get_forms_by_builder_type(
                FormRepository::MEMBERS_DIRECTORY_TYPE,
                'dragDropBuilder'
            );
        }
    }

    /**
     * Display list of optin
     */
    public function form_list()
    {
        return '<div class="pp-form-theme-listing-placeholder"></div>';
    }

    public function back_to_overview()
    {
        $url = PPRESS_FORMS_SETTINGS_PAGE;

        if (isset($_GET['page']) && $_GET['page'] == PPRESS_MEMBER_DIRECTORIES_SLUG) {
            $url = PPRESS_MEMBER_DIRECTORIES_SETTINGS_PAGE;
        }

        echo "<a class=\"add-new-h2\" href=\"$url\">" . esc_html__('Back to Overview', 'wp-user-avatar') . '</a>';
    }

    /**
     * @return AddNewForm
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}