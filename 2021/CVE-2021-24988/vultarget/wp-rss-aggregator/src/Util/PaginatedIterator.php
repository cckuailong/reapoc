<?php

namespace RebelCode\Wpra\Core\Util;

use Iterator;
use LimitIterator;

/**
 * A special iterator implementation that paginates another iterator by only iterating over a given page subset.
 *
 * @since 4.13
 */
class PaginatedIterator extends LimitIterator
{
    /**
     * The number of keys that have been yielded during an iteration.
     *
     * @since 4.13
     */
    protected $keyCount;

    /**
     * Whether or not to preserve keys.
     *
     * @since 4.13
     *
     * @var bool
     */
    protected $preserveKeys;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param Iterator $iterator     The inner iterator.
     * @param int      $page         The page number.
     * @param int      $num          The number of items per page.
     * @param bool     $preserveKeys Whether or not to preserve keys.
     */
    public function __construct(Iterator $iterator, $page, $num, $preserveKeys = false)
    {
        $num = max(1, $num);
        $page = max(1, $page);
        $offset = $num * ($page - 1);
        parent::__construct($iterator, $offset, $num);

        $this->preserveKeys = $preserveKeys;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function rewind()
    {
        parent::rewind();

        $this->keyCount = 0;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function key()
    {
        $key = ($this->preserveKeys)
            ? parent::key()
            : $this->keyCount;

        return $key;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function next()
    {
        parent::next();

        $this->keyCount++;
    }
}
