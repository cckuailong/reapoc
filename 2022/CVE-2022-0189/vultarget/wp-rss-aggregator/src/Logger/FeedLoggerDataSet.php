<?php

namespace RebelCode\Wpra\Core\Logger;

use ArrayIterator;
use Psr\Log\LoggerInterface;
use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\Util\IteratorDelegateTrait;

/**
 * A data set that contains, and creates on-demand, the logger instances for WP RSS Aggregator feed sources.
 *
 * @since 4.13
 */
class FeedLoggerDataSet implements DataSetInterface
{
    /* @since 4.13 */
    use IteratorDelegateTrait;

    /**
     * The logger instances.
     *
     * @since 4.13
     *
     * @var LoggerInterface[]
     */
    protected $instances;

    /**
     * A callable that should accept a feed source ID and return a logger instance.
     *
     * @since 4.13
     *
     * @var callable
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param callable $factory A callable that should accept a feed source ID and return a logger instance.
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
        $this->instances = [];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetGet($feedId)
    {
        if (!$this->offsetExists($feedId)) {
            $this->instances[$feedId] = call_user_func_array($this->factory, [$feedId]);
        }

        return $this->instances[$feedId];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetExists($feedId)
    {
        return isset($this->instances[$feedId]);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetSet($feedId, $instance)
    {
        $this->instances[$feedId] = $instance;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function offsetUnset($feedId)
    {
        unset($this->instances[$feedId]);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function getIterator()
    {
        return new ArrayIterator($this->instances);
    }
}
