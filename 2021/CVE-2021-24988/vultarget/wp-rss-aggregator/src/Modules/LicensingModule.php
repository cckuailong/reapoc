<?php

namespace RebelCode\Wpra\Core\Modules;

use Aventura\Wprss\Core\Licensing\Manager;
use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Licensing\Addon;

/**
 * The licensing module for WP RSS Aggregator.
 *
 * @since 4.14
 */
class LicensingModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function run(ContainerInterface $c)
    {
        /* @var $manager Manager */
        $manager = $c->get('wpra/licensing/manager');
        /* @var $addons Addon[] */
        $addons = $c->get('wpra/licensing/addons');

        // Gets the addons from the service and registers them using the legacy filter
        add_filter('wprss_register_addon', function ($argAddons) use ($addons) {
            foreach ($addons as $addon) {
                $argAddons[$addon->key] = $addon->name;
            }

            return $argAddons;
        });

        // Trigger after the licensing system has loaded and the admin-side has initialized
        // Register each addon's updater instance to listen for potential plugin updates
        add_action('wprss_init_licensing', function() use ($manager, $addons) {
            add_action('admin_init', function () use ($manager, $addons) {
                // Stop if the manager is not recent (because another addon auto-loaded a different version)
                if (!method_exists($manager, 'initUpdaterInstance')) {
                    return;
                }

                foreach ($addons as $addon) {
                    $manager->initUpdaterInstance(
                        $addon->key,
                        $addon->name,
                        $addon->version,
                        $addon->filePath,
                        $addon->storeUrl
                    );
                }
            });
        });
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function getFactories()
    {
        return [
            /*
             * The licensing manager instance - uses the instance from the old system.
             *
             * @since 4.14
             */
            'wpra/licensing/manager' => function () {
                return wprss_licensing_get_manager();
            },
            /*
             * Mirror for 'wpra/addons' service in the addons module.
             *
             * @since 4.14
             */
            'wpra/licensing/addons' => function (ContainerInterface $c) {
                if ($c->has('wpra/addons')) {
                    return $c->get('wpra/addons');
                }

                return [];
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function getExtensions()
    {
        return [];
    }
}
