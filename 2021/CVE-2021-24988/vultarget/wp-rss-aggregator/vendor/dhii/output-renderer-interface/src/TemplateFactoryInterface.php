<?php

namespace Dhii\Output;

use Dhii\Factory\FactoryInterface;

/**
 * Something that can create templates.
 *
 * @since 0.3
 */
interface TemplateFactoryInterface extends FactoryInterface
{
    /**
     * The make config key that holds the template, which the new instance will represent.
     *
     * @since 0.3
     */
    const K_TEMPLATE = 'template';

    /**
     * {@inheritdoc}
     *
     * @since 0.3
     *
     * @return TemplateInterface The new template.
     */
    public function make($config = null);
}
