<?php

namespace Dhii\Output;

use Dhii\Factory\FactoryInterface;

/**
 * Something that can create blocks.
 *
 * @since 0.3
 */
interface BlockFactoryInterface extends FactoryInterface
{
    /**
     * The make config key that holds the content of the block.
     *
     * @since 0.3
     */
    const K_CONTENT = 'content';

    /**
     * {@inheritdoc}
     *
     * @since 0.3
     *
     * @return BlockInterface The new block
     */
    public function make($config = null);
}
