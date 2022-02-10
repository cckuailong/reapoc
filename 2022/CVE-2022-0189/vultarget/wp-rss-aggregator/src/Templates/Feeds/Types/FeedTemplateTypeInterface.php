<?php

namespace RebelCode\Wpra\Core\Templates\Feeds\Types;

use Dhii\Output\TemplateInterface;
use RebelCode\Wpra\Core\Util\ParseArgsWithSchemaCapableTrait;

/**
 * Interface for objects that represent WP RSS Aggregator feed template types.
 *
 * @since 4.13
 */
interface FeedTemplateTypeInterface extends TemplateInterface
{
    /**
     * Retrieves the template type key.
     *
     * @since 4.13
     *
     * @return string
     */
    public function getKey();

    /**
     * Retrieves the template type name.
     *
     * @since 4.13
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieves the options available for this template type.
     *
     * @see ParseArgsWithSchemaCapableTrait
     *
     * @since 4.13
     *
     * @return array An array of option schemas, usable with {@link ParseArgsWithSchemaCapableTrait}.
     */
    public function getOptions();
}
