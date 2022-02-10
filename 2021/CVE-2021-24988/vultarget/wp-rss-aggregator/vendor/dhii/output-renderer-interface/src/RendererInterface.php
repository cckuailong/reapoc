<?php

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Output\Exception\CouldNotRenderExceptionInterface;
use Dhii\Output\Exception\RendererExceptionInterface;

/**
 * Something that can produce output.
 *
 * @since 0.1
 */
interface RendererInterface
{
    /**
     * Produces output.
     *
     * @since 0.1
     *
     * @throws CouldNotRenderExceptionInterface If cannot render.
     * @throws RendererExceptionInterface       Any other problem related to the renderer.
     *
     * @return string|Stringable The output.
     */
    public function render();
}
