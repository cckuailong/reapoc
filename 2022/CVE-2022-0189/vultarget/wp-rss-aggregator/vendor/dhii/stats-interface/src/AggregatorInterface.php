<?php

namespace Dhii\Stats;

/**
 * Something that can act as a stats aggregator.
 *
 * A stats aggregator is something that calculates totals based on individual values.
 *
 * @since 0.1.0
 */
interface AggregatorInterface
{
    /**
     * Aggregates stats from a set of items.
     *
     * @since 0.1.0
     *
     * @param mixed[]              $totals Existing totals.
     * @param mixed[]|\Traversable $items  A list of items to aggregate the totals from.
     *
     * @return mixed[] The new totals.
     */
    public function aggregate($totals, $items);
}
