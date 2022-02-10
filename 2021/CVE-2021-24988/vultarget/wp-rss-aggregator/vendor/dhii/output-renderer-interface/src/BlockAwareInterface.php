<?php

namespace Dhii\Output;

/**
 * Something that can have a block retrieved from it.
 *
 * @since 0.1
 */
interface BlockAwareInterface
{
    /**
     * Retrieves the block associated with this instance.
     *
     * @since 0.1
     *
     * @return BlockInterface
     */
    public function getBlock();
}
