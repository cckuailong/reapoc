<?php

namespace ProfilePress\Core\Admin\SettingsPages;

use ProfilePress\Core\Classes\ExtensionManager;
use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Custom_Settings_Page_Api;

class ExtensionsSettingsPage extends AbstractSettingsPage
{
    public function __construct()
    {
        add_filter('ppress_admin_hooks', function () {
            add_action('admin_menu', array($this, 'register_settings_page'));
        });

        if ( ! ExtensionManager::is_premium()) {
            add_filter('install_plugins_tabs', [$this, 'add_extension_tab']);
            add_action('install_plugins_ppress_extensions', [$this, 'extension_view']);
        }
    }

    public function add_extension_tab($tabs)
    {
        $tabs['ppress_extensions'] = 'ProfilePress ' . __('Addons', 'wp-user-avatar');

        return $tabs;
    }

    public function extension_view($tabs)
    {
        $this->admin_page_callback();
    }

    public function admin_page_title()
    {
        return esc_html__('Addons', 'wp-user-avatar');
    }

    public function register_settings_page()
    {
        add_submenu_page(
            PPRESS_SETTINGS_SLUG,
            $this->admin_page_title() . ' - ProfilePress',
            '<span style="color:#f18500">' . $this->admin_page_title() . '</span>',
            'manage_options',
            PPRESS_EXTENSIONS_SETTINGS_SLUG,
            [$this, 'settings_page_function']);
    }

    public function settings_page_function()
    {
        add_action('wp_cspa_main_content_area', array($this, 'admin_page_callback'), 10, 2);
        add_action('wp_cspa_form_tag', function () {
            echo 'id="ppress-extension-manager-form"';
        });

        $instance = Custom_Settings_Page_Api::instance();
        $instance->option_name(EM::DB_OPTION_NAME);
        $instance->add_view_classes('ppress-extensions');
        $instance->page_header($this->admin_page_title());
        $this->register_core_settings($instance, true);
        $instance->build(true);
    }

    public function admin_page_callback()
    {
        ?>
        <div class="ppress-extensions-items-wrap">
            <?php if (EM::is_premium()) : ?>
                <div class="ppress-extensions-header">
                    <div class="ppress-extensions-header-buttons">
                        <div class="button-content">
                            <button type="button" class="button-primary ppress-extensions-button" onclick="jQuery('.ppress-extension-manager-checkbox').prop('checked', true);jQuery('#ppress-extension-manager-form').submit();">
                                <?= esc_html__('Activate All', 'wp-user-avatar') ?>
                            </button>
                            <button type="button" class="button-secondary ppress-extensions-button" onclick="jQuery('.ppress-extension-manager-checkbox').prop('checked', false);jQuery('#ppress-extension-manager-form').submit();">
                                <?= esc_html__('Deactivate All', 'wp-user-avatar') ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="ppress-extensions-upsell-wrap">
                    <div class="notice-content">
                            <span>
                                <?= sprintf(
                                    esc_html__('Upgrade to Premium to unlock extensions and other great features. As a valued ProfilePress Lite user, you will %1$sreceive 20%3$s off%2$s your purchase, automatically applied at checkout!', 'wp-user-avatar'),
                                    '<span class="ppress-extensions-upsell-highlight">', '</span>', '%'
                                ) ?>
                            </span>
                        <div class="ppress-extensions-upsell-button">
                            <a target="_blank" href="https://profilepress.net/pricing/?discount=20PPOFF&utm_source=liteplugin&utm_medium=extension-page&utm_campaign=notice&utm_content=upsell" class="button-primary">
                                <?= esc_html__('Upgrade Now', 'wp-user-avatar') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="ppress-extensions-items-row">

                <?php foreach (EM::available_extensions() as $id => $extension) :
                    $name = sprintf('%s[%s]', EM::DB_OPTION_NAME, $id);
                    $extension_class = ppress_var(EM::class_map(), $id);
                    $upgrade_url = "https://profilepress.net/pricing/?utm_source=WordPress&utm_campaign=liteplugin&utm_medium=extension-upgrade&utm_content=$id";
                    if ( ! EM::is_premium()) {
                        $upgrade_url = add_query_arg('discount', '20PPOFF', $upgrade_url);
                    }

                    $upgrade_label = ! EM::is_premium() ? esc_html__('Upgrade to Premium', 'wp-user-avatar') : esc_html__('Upgrade Plan', 'wp-user-avatar');

                    ?>

                    <?php $dependency_available = isset($extension['is_available']) && true !== ($callable_result = call_user_func($extension['is_available'])) ? $callable_result : true; ?>
                    <div class="ppress-extension-item-wrap">
                        <div class="ppress-extension-item-card">
                            <div class="ppress-extension-card-body<?= EM::is_premium() && $extension_class::$instance_flag && (true !== $dependency_available) ? ' ppress-unavailable' : '' ?>">
                                <div class="ppress-extension-card-header">
                                    <?= ppress_var($extension, 'icon', '') ?>
                                    <?= $extension['title'] ?>
                                </div>
                                <div class="ppress-extension-card-description">
                                    <div><?= $extension['description'] ?></div>
                                    <div class="ppress-extension-card-learn-more">
                                        <a href="<?= $extension['url'] ?>" target="_blank"><?= esc_html__('Learn More', 'wp-user-avatar') ?></a>
                                        <a href="<?= $extension['url'] ?>" target="_blank" class="no-underline"> →</a>
                                    </div>
                                </div>
                            </div>
                            <div class="ppress-extension-card-footer">

                                <?php if (EM::is_premium() && $extension_class::$instance_flag) : ?>

                                    <?php if (true !== $dependency_available) : ?>
                                        <span class="ppress-extension-status">
                                            <?= sprintf(esc_html__('Unavailable: %s', 'wp-user-avatar'), "<span>$callable_result</span>") ?>
                                        </span>
                                    <?php else : ?>
                                        <div class="ppress-extension-card-install-activate">
                                    <span class="ppress-extension-card-status">
                                        <?= EM::is_enabled($id) ? esc_html__('Activated', 'wp-user-avatar') : esc_html__('Deactivated', 'wp-user-avatar') ?>
                                    </span>
                                            <label for="ppress-<?= $id ?>">
                                                <input type="hidden" name="<?= $name ?>" value="false">
                                                <input class="ppress-extension-manager-checkbox" type="checkbox" name="<?= $name ?>" id="ppress-<?= $id ?>" value="true" onchange="jQuery('#ppress-extension-manager-form').submit();" <?php checked(EM::is_enabled($id)) ?>>
                                                <span class="ppress-extension-use-switch"></span>
                                            </label>
                                        </div>
                                    <?php endif; ?>

                                <?php else : ?>
                                    <div class="ppress-extensions-upgrade-cta">
                                        <a class="button-primary ppress-extensions-button" href="<?= $upgrade_url ?>" target="_blank">
                                            <?= $upgrade_label ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="save_ppress_extension_manager" value="true">
        </div>
        <?php
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}