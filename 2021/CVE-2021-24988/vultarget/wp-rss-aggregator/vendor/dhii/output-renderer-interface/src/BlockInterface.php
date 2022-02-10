<?php

namespace Dhii\Output;

use Dhii\Util\String\StringableInterface;

/**
 * Represents a unit of output.
 *
 * Blocks have access to all the data necessary
 * for rendering at the time of rendering.
 *
 * @since 0.1
 */
interface BlockInterface extends
        RendererInterface,
        StringableInterface
{
}
