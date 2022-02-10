<?php

namespace RebelCode\Wpra\Core\Logger;

use RebelCode\Wpra\Core\Database\TableInterface;

/**
 * A logger specific to WP RSS Aggregator.
 *
 * This logger is an extended version of the {@link WpdbLogger}, that stores the feed ID extra property internally.
 * By implementing the {@link FeedLoggerInterface}, it is able to yield new instances that log messages spefific to
 * that feed source.
 *
 * @since 4.15.1
 *
 * @see   WpdbLogger
 * @see   FeedLoggerInterface
 */
class WpraLogger extends WpdbLogger implements FeedLoggerInterface
{
    /**
     * The key of the extra feed ID column in the logs table.
     *
     * @since 4.15.1
     */
    CONST LOG_FEED_ID = 'feed_id';

    /**
     * The ID of the feed for which messages are being logged.
     *
     * @since 4.15.1
     *
     * @var string
     */
    protected $feedId;

    /**
     * @inheritdoc
     *
     * @since 4.15.1
     */
    public function __construct(TableInterface $table, $columns = [], $extra = [])
    {
        parent::__construct($table, $columns, $extra);

        // Start without a feed ID
        $this->feedId = '';
    }

    /**
     * @inheritdoc
     *
     * @since 4.15.1
     */
    public function forFeedSource($feedId)
    {
        $instance = clone $this;
        $instance->setFeedId($feedId);

        return $instance;
    }

    /**
     * Sets the ID of the feed source for which messages will be logged by this instance.
     *
     * Also updates the callback for the extra feed ID table column.
     *
     * @since 4.15.1
     *
     * @param int $feedId The ID of the feed source.
     */
    protected function setFeedId($feedId)
    {
        $this->feedId = $feedId;

        // The feed ID extra prop will lazily evaluate to the feed ID in this instance
        $this->extra[static::LOG_FEED_ID] = function () {
            return $this->feedId;
        };
    }
}
