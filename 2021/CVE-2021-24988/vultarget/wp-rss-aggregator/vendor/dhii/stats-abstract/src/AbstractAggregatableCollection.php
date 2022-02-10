<?php

namespace Dhii\Stats;

use Dhii\Collection;

/**
 * Common functionality for collections that can be aggregated.
 *
 * Aggregation is the process of producing totals from the elements of a collection.
 *
 * @since 0.1.0
 */
abstract class AbstractAggregatableCollection extends Collection\AbstractIterableCollection
{
    protected $stats;
    protected $statAggregator;

    /**
     * Retrieve the instance of the stat aggregator which will aggregate stats for this instance.
     *
     * @since 0.1.0
     *
     * @return AggregatorInterface
     */
    protected function _getStatAggregator()
    {
        return $this->statAggregator;
    }

    /**
     * Sets the stats aggregator to be used by this instance for calculating totals.
     *
     * @since 0.1.0
     *
     * @param AggregatorInterface $aggregator The stats aggregator for this instance to use.
     *
     * @return AbstractResultSet This instance.
     */
    protected function _setStatAggregator(AggregatorInterface $aggregator)
    {
        $this->statAggregator = $aggregator;

        return $this;
    }

    /**
     * Low-level retrieval of stat values.
     *
     * Operates on raw values, no cache is generated.
     *
     * @since 0.1.0
     *
     * @param string|null $code The code of the stat, the value for which to retrieve.
     *                          If null, retrieve all stats.
     *
     * @return mixed[]|mixed|null If a code is supplied, retrieves the stat value for that code, or null if no stat with that code.
     *                            Otherwise, a map of all stat codes to their values, or null if no stats have been aggregated.
     */
    protected function _getStatsValue($code = null)
    {
        if (is_null($code)) {
            return $this->stats;
        }

        return isset($this->stats[$code])
                ? $this->stats[$code]
                : null;
    }

    /**
     * Low-level assignment of stat values.
     *
     * Assigns all values in one go, or one at a time.
     *
     * @since 0.1.0
     *
     * @param mixed[]|string $stats An map of stat names to stat values, or a stat code.
     * @param mixed          $value If a stat code is specified, its value will be set to this.
     *
     * @return AbstractAggregatableCollection This instance.
     */
    protected function _setStatsValue($stats, $value = null)
    {
        if (is_array($stats)) {
            $this->stats = $stats;

            return $this;
        }

        if (!is_array($this->stats)) {
            $this->stats = array();
        }

        $this->stats[$stats] = $value;

        return $this;
    }

    /**
     * Clears stat cache.
     *
     * After this, retrieval of a stat value will trigger generation of cache.
     *
     * @since 0.1.0
     *
     * @return AbstractAggregatableCollection This instance.
     */
    protected function _resetStats()
    {
        $this->stats = null;

        return $this;
    }

    /**
     * Retrieve codes for stats that should be aggregated from this collection's items.
     *
     * @since 0.1.0
     *
     * @return string[] An array of stat codes.
     */
    protected function _getAggregatableStatCodes()
    {
        return array();
    }

    /**
     * Get one or all stat values.
     *
     * If no cached values available, triggers their generation.
     *
     * @since 0.1.0
     *
     * @param string|null $code The code, for which to retrieve the stat value.
     *                          If null, all stats will be retrieved.
     *
     * @return mixed[]|mixed A stat value, or a map of stat codes to values.
     */
    protected function _getStats($code = null)
    {
        if (is_null($this->stats)) {
            $this->stats = $this->_getStatAggregator()->aggregate(
                $this->_getAggregatableStatCodes(),
                $this->_getCachedItems());
        }

        if (is_null($code)) {
            return $this->stats;
        }

        return isset($this->stats[$code])
                ? $this->stats[$code]
                : null;
    }
}
