<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Data\Collections\TwigTemplateCollection;
use RebelCode\Wpra\Core\Twig\Extensions\Date\TwigDateTranslator;
use RebelCode\Wpra\Core\Twig\Extensions\I18n\WpI18nExtension;
use RebelCode\Wpra\Core\Twig\Extensions\WpraExtension;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\CoreExtension;
use Twig\Extension\DebugExtension;
use Twig\Extensions\DateExtension;
use Twig\Extensions\TextExtension;
use Twig\Loader\FilesystemLoader;

/**
 * The Twig module for WP RSS Aggregator.
 *
 * @since 4.13
 */
class TwigModule implements ModuleInterface
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
             * The template loader for Twig.
             *
             * @since 4.13
             */
            'wpra/twig' => function (ContainerInterface $c) {
                return new TwigEnvironment(
                    $c->get('wpra/twig/loader'),
                    $c->get('wpra/twig/options')
                );
            },
            /*
             * The template loader for Twig.
             *
             * @since 4.13
             */
            'wpra/twig/loader' => function (ContainerInterface $c) {
                return new FilesystemLoader($c->get('wpra/twig/paths'));
            },
            /*
             * The template paths for Twig.
             *
             * @since 4.13
             */
            'wpra/twig/paths' => function (ContainerInterface $c) {
                return [
                    $c->get('wpra/core/templates_dir_path')
                ];
            },
            /*
             * The twig template collection.
             *
             * @since 4.13
             */
            'wpra/twig/collection' => function (ContainerInterface $c) {
                return new TwigTemplateCollection($c->get('wpra/twig'));
            },
            /*
             * The twig debug option.
             *
             * @since 4.13
             */
            'wpra/twig/debug' => function () {
                return false;
            },
            /*
             * The twig cache enabler option.
             *
             * @since 4.13
             */
            'wpra/twig/cache_enabled' => function (ContainerInterface $c) {
                return false;
            },
            /*
             * The path to the Twig cache.
             *
             * @since 4.13
             */
            'wpra/twig/cache_dir' => function (ContainerInterface $c) {
                return get_temp_dir() . 'wprss/twig-cache';
            },
            /*
             * The options for Twig.
             *
             * @since 4.13
             */
            'wpra/twig/options' => function (ContainerInterface $c) {
                return [
                    'debug' => $c->get('wpra/twig/debug'),
                    'cache' => $c->get('wpra/twig/cache_enabled'),
                ];
            },
            /*
             * The extensions to use for WPRA's Twig instance.
             *
             * @since 4.13
             */
            'wpra/twig/extensions' => function (ContainerInterface $c) {
                return [
                    $c->get('wpra/twig/extensions/i18n'),
                    $c->get('wpra/twig/extensions/date'),
                    $c->get('wpra/twig/extensions/text'),
                    $c->get('wpra/twig/extensions/debug'),
                    $c->get('wpra/twig/extensions/wpra'),
                ];
            },
            /*
             * The i18n extension for Twig.
             *
             * @since 4.13
             */
            'wpra/twig/extensions/i18n' => function (ContainerInterface $c) {
                return new WpI18nExtension();
            },
            /*
             * The date extension for Twig.
             *
             * @since 4.13
             */
            'wpra/twig/extensions/date' => function (ContainerInterface $c) {
                return new DateExtension(new TwigDateTranslator());
            },
            /*
             * The text extension for Twig.
             *
             * @since 4.13
             */
            'wpra/twig/extensions/text' => function (ContainerInterface $c) {
                return new TextExtension();
            },
            /*
             * The debug extension for Twig.
             *
             * @since 4.13
             */
            'wpra/twig/extensions/debug' => function (ContainerInterface $c) {
                return new DebugExtension();
            },
            /*
             * The custom WPRA extension for Twig.
             *
             * @since 4.13
             */
            'wpra/twig/extensions/wpra' => function (ContainerInterface $c) {
                return new WpraExtension();
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
        return [
            /*
             * Registers the Twig extensions for WPRA and sets the Twig timezone to match WordPress.
             *
             * @since 4.13
             */
            'wpra/twig' => function (ContainerInterface $c, TwigEnvironment $twig) {
                foreach ($c->get('wpra/twig/extensions') as $extension) {
                    $twig->addExtension($extension);
                }

                /* @var $twigCore CoreExtension */
                $twigCore = $twig->getExtension('\Twig\Extension\CoreExtension');;
                $twigCore->setTimezone($t = $c->get('wp/timezone'));

                return $twig;
            }
        ];
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
