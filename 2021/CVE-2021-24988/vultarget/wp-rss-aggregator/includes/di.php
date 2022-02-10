<?php
/*
 * The DI module
 */

use Aventura\Wprss\Core\CompositeContainer as WpraCompositeContainer;
use Aventura\Wprss\Core\Container;
use Aventura\Wprss\Core\ServiceProvider;
use Dhii\Di\CompositeContainer;
use Dhii\Di\CompositeContainerInterface;
use Dhii\Di\WritableCompositeContainerInterface;
use Dhii\Di\WritableContainerInterface;

define('WPRSS_SERVICE_ID_PREFIX', \WPRSS_PLUGIN_CODE . '.');


if (!function_exists('wprss_wp_container')) {

    /**
     * Gets the global WP container.
     *
     * This is intended to be used everywhere in WP, by all plugins.
     *
     * @since 4.11
     *
     * @staticvar CompositeContainer $container
     * @return CompositeContainerInterface The global composite container.
     */
    function wprss_wp_container()
    {
        static $container = null;

        if (is_null($container)) {
            $container = new CompositeContainer();

            /**
             * Exposes the global container at the moment of its initialization.
             *
             * This allows registration of child containers specific to plugins.
             *
             * @since 4.11
             *
             * @param WritableCompositeContainerInterface The global DI container.
             */
            do_action('wp_container_init', $container);
        }

        return $container;
    }
}

/**
 * Retrieves the container that has access to all services of all WPRA plugins.
 *
 * @since 4.11
 * @staticvar WpraCompositeContainer $container
 * @return CompositeContainerInterface The WPRA hub container.
 */
function wprss_hub_container()
{
    static $container = null;

    if (is_null($container)) {
        $container = new WpraCompositeContainer(wprss_wp_container());


        /**
         * Exposes the WPRA-wide container at the moment of its initialization.
         *
         * This allows registration of child containers specific to WPRA extensions.
         *
         * @since 4.11
         *
         * @param WritableCompositeContainerInterface The WPRA container DI container.
         */
        do_action('wprss_container_init', $container);
    }

    return $container;
}

/**
 * Retrieves the WPRA Core container instance.
 *
 * @since 4.11
 *
 * @staticvar Container $container
 * @return Container The container instance.
 */
function wprss_core_container()
{
    static $container = null;

    if (is_null($container)) {
        $serviceProvider = new ServiceProvider(array(
            'service_id_prefix'         => \WPRSS_SERVICE_ID_PREFIX,
            'event_prefix'              => \WPRSS_EVENT_PREFIX,
        ));
        $container = new Container($serviceProvider, wprss_hub_container());

        /**
         * Exposes the WPRA Core container at the moment of its initialization.
         *
         * @since 4.11
         *
         * @param WritableCompositeContainerInterface The container which has all WPRA Core services.
         */
        do_action('wprss_core_container_init', $container);
    }

    return $container;
}

// Making sure the global container is initialized - 1st tier
add_action('wprss_pre_init', function() {
    wprss_wp_container();
});

// Adding WPRA-wide container - 2nd tier
add_action('wp_container_init', function(WritableCompositeContainerInterface $parent) {
    $container = wprss_hub_container();

    $parent->add($container);
});

// Creating and attaching the WPRA Core container, and feeding service definitions to it
add_action('wprss_container_init', function(WritableCompositeContainerInterface $parent) {
    $container = wprss_core_container();

    $parent->add($container);
});

// Adds the bulk import service provider to the old Aventura container
add_filter('wprss_core_container_init', function (WritableContainerInterface $container) {
    $serviceProvider = new \Aventura\Wprss\Core\Model\BulkSourceImport\ServiceProvider(array(
        'notice_service_id_prefix' => \WPRSS_NOTICE_SERVICE_ID_PREFIX,
        'service_id_prefix' => \WPRSS_SERVICE_ID_PREFIX,
        'event_prefix' => \WPRSS_EVENT_PREFIX,
    ));
    $container->register($serviceProvider);
});
