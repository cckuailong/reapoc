<?php

namespace Dhii\Output;

/**
 * Something that can have a template retrieved from it.
 *
 * @since 0.2
 */
interface TemplateAwareInterface
{
    /**
     * Retrieves the template associated with this instance.
     *
     * @since 0.2
     *
     * @return TemplateInterface|null The template, if any.
     */
    public function getTemplate();
}
