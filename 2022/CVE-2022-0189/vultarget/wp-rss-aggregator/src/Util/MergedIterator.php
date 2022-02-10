<?php

namespace RebelCode\Wpra\Core\Util;

use AppendIterator;
use stdClass;
use Traversable;

/**
 * An implementation of an iterator that iterates over several iterators in sequence, without yielding duplicate keys.
 *
 * Once a key and its value have been yielded, no further values for the same keys are yielded. In other words, the
 * precedence for iterators is "first come, first serve".
 *
 * @since 4.13
 */
class MergedIterator extends AppendIterator
{
    /**
     * Temporary list of keys yielded during iteration, used to avoid yielded duplicates.
     *
     * @since 4.13
     *
     * @var array
     */
    protected $keys;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param array|stdClass|Traversable $iterators The iterators.
     */
    public function __construct($iterators = [])
    {
        parent::__construct();

        foreach ($iterators as $iterator) {
            $iterator->rewind();
            $this->append($iterator);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function rewind()
    {
        parent::rewind();

        $this->keys = [];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function next()
    {
        do {
            parent::next();
            $nextKey = $this->key();
        } while ($this->valid() && isset($this->keys[$nextKey]));

        $this->keys[$nextKey] = 1;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function current()
    {
        $this->keys[$this->key()] = 1;

        return parent::current();
    }
}
