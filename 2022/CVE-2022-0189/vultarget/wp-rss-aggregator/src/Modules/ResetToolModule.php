<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Templates\NullTemplate;

/**
 * The module that adds the "Reset" tool to WP RSS Aggregator.
 *
 * @since 4.17
 */
class ResetToolModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getFactories()
    {
        return [
            /*
             * Information about the "Reset" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools/reset/info' => function (ContainerInterface $c) {
                return [
                    'name' => __('Reset', 'wprss'),
                    'template' => $c->has('wpra/twig/collection')
                        ? $c->get('wpra/twig/collection')['admin/tools/reset.twig']
                        : new NullTemplate(),
                ];
            },
            /*
             * The handler that listens to items delete requests and deletes the imported items.
             *
             * @since 4.17
             */
            'wpra/admin/tools/reset/items_handler' => function (ContainerInterface $c) {
                return function () use ($c) {
                    $reset = filter_input(INPUT_POST, 'wpra_delete_all_items', FILTER_DEFAULT);
                    if (empty($reset)) {
                        return;
                    }

                    check_admin_referer('wpra_delete_all_items', 'wpra_delete_all_items_nonce');

                    wprss_feed_reset();

                    add_action('admin_notices', function () use ($c) {
                        echo $c->get('wpra/admin/tools/reset/items_notice');
                    });
                };
            },
            /*
             * The handler that listens to settings reset requests and resets the settings.
             *
             * @since 4.17
             */
            'wpra/admin/tools/reset/settings_handler' => function (ContainerInterface $c) {
                return function () use ($c) {
                    $reset = filter_input(INPUT_POST, 'wpra_reset_settings', FILTER_DEFAULT);
                    if (empty($reset)) {
                        return;
                    }

                    check_admin_referer('wpra_reset_settings', 'wpra_reset_settings_nonce');
                    do_action( 'wprss_before_restore_settings' );

                    foreach ($c->get('wpra/admin/tools/reset/settings_to_reset') as $setting ) {
                        delete_option($setting);
                    }

                    do_action( 'wprss_after_restore_settings' );
                    add_action('admin_notices', function () use ($c) {
                        echo $c->get('wpra/admin/tools/reset/settings_notice');
                    });
                };
            },
            /*
             * The notice to show when the items are being deleted.
             *
             * @since 4.17
             */
            'wpra/admin/tools/reset/items_notice' => function () {
                return sprintf(
                    '<div class="updated"><p>%s</p></div>',
                    __('The items are being deleted in the background.', 'wprss')
                );
            },
            /*
             * The notice to show when the settings have been reset.
             *
             * @since 4.17
             */
            'wpra/admin/tools/reset/settings_notice' => function () {
                return sprintf(
                    '<div class="updated"><p>%s</p></div>',
                    __('The plugin settings have been reset to default.', 'wprss')
                );
            },
            'wpra/admin/tools/reset/settings_to_reset' => function () {
                return apply_filters(
                    'wprss_settings_to_restore',
                    [
                        'wprss_settings_general',
                        'wprss_settings_notices',
                        'wprss_addon_notices',
                        'wprss_pwsv',
                        'wprss_db_version',
                        WPRSS_INTRO_DID_INTRO_OPTION,
                        WPRSS_UPDATE_PAGE_PREV_VERSION_OPTION,
                    ]
                );
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getExtensions()
    {
        return [
            /*
             * Registers the "Reset" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools' => function (ContainerInterface $c, $tools) {
                return $tools + ['reset' => $c->get('wpra/admin/tools/reset/info')];
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function run(ContainerInterface $c)
    {
        add_action('admin_init', $c->get('wpra/admin/tools/reset/settings_handler'));
        add_action('admin_init', $c->get('wpra/admin/tools/reset/items_handler'));
    }
}
