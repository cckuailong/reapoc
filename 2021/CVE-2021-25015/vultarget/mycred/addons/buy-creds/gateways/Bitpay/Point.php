<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Object to represent a point on an elliptic curve
 *
 * @package Bitcore
 */
class Point implements PointInterface
{
    /**
     * MUST be a HEX value
     *
     * @var string
     */
    protected $x;

    /**
     * MUST be a HEX value
     *
     * @var string
     */
    protected $y;

    /**
     * @param string $x
     * @param string $y
     */
    public function __construct($x, $y)
    {
        $this->x = (string) $x;
        $this->y = (string) $y;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->isInfinity()) {
            return self::INFINITY;
        }

        return sprintf('(%s, %s)', $this->x, $this->y);
    }

    /**
     * @return string
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return string
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return boolean
     */
    public function isInfinity()
    {
        return (self::INFINITY == $this->x || self::INFINITY == $this->y);
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize(array($this->x, $this->y));
    }

    /**
     * @inheritdoc
     */
    public function unserialize($data)
    {
        list(
            $this->x,
            $this->y
        ) = unserialize($data);
    }
}
