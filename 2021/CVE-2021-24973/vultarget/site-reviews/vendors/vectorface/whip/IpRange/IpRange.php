<?php

namespace GeminiLabs\Vectorface\Whip\IpRange;

/**
 * An interface for IP ranges.
 * @copyright Vectorface, Inc 2015
 * @author Daniel Bruce <dbruce1126@gmail.com>
 */
interface IpRange
{
    /**
     * Returns whether or not a given IP address falls within this range.
     * @param string $ipAddress The given IP address.
     * @return boolean Returns true if the IP address falls within the range
     *         and false otherwise.
     */
    public function containsIp($ipAddress);
}
