<?php

namespace WP_STATISTICS\MetaBox;

class quickstats
{
    /**
     * Get Quick States Meta Box Data
     *
     * @param array $args
     * @return array
     */
    public static function get($args = array())
    {
        return summary::getSummaryHits(array('user-online', 'visitors', 'visits', 'hit-chart'));
    }

}