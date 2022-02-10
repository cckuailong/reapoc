<?php

namespace RebelCode\Wpra\Core\Database;

use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use RuntimeException;

/**
 * Interface for objects that represent a database table.
 *
 * @since 4.13
 */
interface TableInterface extends CollectionInterface
{
    /**
     * The filter for specifying the limit.
     *
     * @since 4.13
     */
    const FILTER_LIMIT = 'limit';

    /**
     * The filter for specifying the offset.
     *
     * @since 4.13
     */
    const FILTER_OFFSET = 'offset';

    /**
     * The filter for specifying the field to order by.
     *
     * @since 4.13
     */
    const FILTER_ORDER_BY = 'order_by';

    /**
     * The filter for specifying the order mode.
     *
     * @since 4.13
     */
    const FILTER_ORDER = 'order';

    /**
     * The filter for specifying arbitrary WHERE conditions.
     *
     * @since 4.13
     */
    const FILTER_WHERE = 'where';

    /**
     * Creates the table if it does not exist in the database.
     *
     * @since 4.13
     *
     * @throws RuntimeException If the table could not be created.
     */
    public function create();

    /**
     * Drops the table if it exists in the database.
     *
     * @since 4.13
     *
     * @throws RuntimeException If the table could not be dropped.
     */
    public function drop();
}
