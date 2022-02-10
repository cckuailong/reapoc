<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Handlers\LoadTextDomainHandler;

/**
 * The WP RSS Aggregator internationalization module.
 *
 * @since 4.13
 */
class I18nModule implements ModuleInterface
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
             * The text domain for WP RSS Aggregator.
             *
             * @since 4.13
             */
            'wpra/i18n/domain' => function () {
                return 'wprss';
            },
            /*
             * The name of the languages' parent directory.
             *
             * @since 4.17.3
             */
            'wpra/i18n/languages/parent_dir' => function (ContainerInterface $c) {
                return $c->has('wpra/core/plugin_dir_name')
                    ? $c->get('wpra/core/plugin_dir_name')
                    : '';
            },
            /*
             * The path to the languages directory.
             *
             * @since 4.13
             */
            'wpra/i18n/languages_rel_dir' => function (ContainerInterface $c) {
                return $c->get('wpra/i18n/languages/parent_dir') . '/languages';
            },
            /*
             * The handler that loads the plugin's text domain.
             *
             * @since 4.13
             */
            'wpra/i18n/load_text_domain_handler' => function (ContainerInterface $c) {
                return new LoadTextDomainHandler(
                    $c->get('wpra/i18n/domain'),
                    $c->get('wpra/i18n/languages_rel_dir')
                );
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
        call_user_func($c->get('wpra/i18n/load_text_domain_handler'));
    }
}
