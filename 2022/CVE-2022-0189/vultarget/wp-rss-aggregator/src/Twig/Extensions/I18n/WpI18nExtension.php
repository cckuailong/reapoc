<?php

namespace RebelCode\Wpra\Core\Twig\Extensions\I18n;

use Twig\Extension\AbstractExtension;
use Twig_SimpleFilter;

/**
 * A Twig extension that adds internationalization to templates using WordPress' i18n function.
 *
 * @since 4.13.2
 */
class WpI18nExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13.2
     */
    public function getName()
    {
        return 'i18n';
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13.2
     */
    public function getTokenParsers()
    {
        return array(new I18nTransTokenParser());
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13.2
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('trans', '__'),
        );
    }
}
