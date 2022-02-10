<?php

namespace RebelCode\Wpra\Core\Database;

use RebelCode\Wpra\Core\Data\Collections\NullCollection;

/**
 * A null implementation of a table.
 *
 * @since 4.13
 */
class NullTable extends NullCollection implements TableInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function create()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function drop()
    {
    }
}
