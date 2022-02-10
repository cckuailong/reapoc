<?php

namespace Dhii\Output;

/**
 * Something that can have a renderer retrieved from it.
 *
 * @since 0.1
 */
interface RendererAwareInterface
{
    /**
     * Retrieves the renderer that is associated with this instance.
     *
     * @since 0.1
     *
     * @return RendererInterface The renderer.
     */
    public function getRenderer();
}
