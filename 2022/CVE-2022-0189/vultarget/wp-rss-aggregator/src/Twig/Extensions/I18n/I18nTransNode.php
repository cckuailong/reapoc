<?php

namespace RebelCode\Wpra\Core\Twig\Extensions\I18n;

use Twig_Extensions_Node_Trans;

/**
 * This extension overrides the translation function usage to use WordPress' {@link __()} and {@link _n()} functions.
 *
 * @since 4.13.2
 */
class I18nTransNode extends Twig_Extensions_Node_Trans
{
    /**
     * @inheritdoc
     *
     * @since 4.13.2
     */
    protected function getTransFunction($plural)
    {
        return ($plural) ? 'wprss_translate_n' : 'wprss_translate';
    }
}
