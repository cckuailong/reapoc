<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\RestApi\Auth\AuthUserIsAdmin;
use RebelCode\Wpra\Core\RestApi\EndPointManager;

/**
 * The REST API module for WP RSS Aggregator.
 *
 * @since 4.13
 */
class RestApiModule implements ModuleInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getFactories()
    {
        return [
            /*
             * The WP RSS Aggregator REST API namespace.
             *
             * @since 4.13
             */
            'wpra/rest_api/v1/namespace' => function () {
                return 'wpra/v1';
            },
            /*
             * The REST API endpoint manager.
             *
             * @since 4.13
             */
            'wpra/rest_api/v1/endpoint_manager' => function (ContainerInterface $c) {
                return new EndPointManager(
                    $c->get('wpra/rest_api/v1/namespace'),
                    $c->get('wpra/rest_api/v1/endpoints')
                );
            },
            /*
             * The REST API endpoints.
             *
             * @since 4.13
             */
            'wpra/rest_api/v1/endpoints' => function (ContainerInterface $c) {
                return [];
            },
            /*
             * The authorization callback function to checking if the request user is a logged-in admin.
             *
             * @since 4.13
             */
            'wpra/rest_api/v1/auth/user_is_admin' => function (ContainerInterface $c) {
                return new AuthUserIsAdmin();
            },
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getExtensions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function run(ContainerInterface $c)
    {
        // Register routes with WordPress
        add_action('rest_api_init', function () use ($c) {
            /* @var $manager EndPointManager */
            $manager = $c->get('wpra/rest_api/v1/endpoint_manager');
            $manager->register();
        });
    }
}
