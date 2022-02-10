<?php

namespace RebelCode\Wpra\Core\Modules;

use DateTimeZone;
use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Wp\WpRolesProxy;

/**
 * A module that provides various WordPress components as services.
 *
 * @since 4.13
 */
class WpModule implements ModuleInterface
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
             * The WordPress global wpdb instance.
             *
             * @since 4.13
             */
            'wp/db' => function () {
                global $wpdb;

                return $wpdb;
            },
            /*
             * The WordPress user roles manager instance.
             *
             * @since 4.13
             */
            'wp/roles' => function () {
                return new WpRolesProxy();
            },
            /*
             * The WordPress timezone as a DateTimeZone object.
             *
             * @since 4.13.1
             */
            'wp/timezone' => function (ContainerInterface $c) {
                return new DateTimeZone($c->get('wp/timezone_name'));
            },
            /*
             * The WordPress timezone name.
             *
             * @since 4.13.1
             */
            'wp/timezone_name' => function () {
                $timezone_string = get_option('timezone_string');

                if (!empty($timezone_string)) {
                    return $timezone_string;
                }

                $offset = get_option('gmt_offset');
                $hours = (int) $offset;
                $minutes = ($offset - floor($offset)) * 60;
                $offset = sprintf('%+03d:%02d', $hours, $minutes);

                return $offset;
            }
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
    }
}
