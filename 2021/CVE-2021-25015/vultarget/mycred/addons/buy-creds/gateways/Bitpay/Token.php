<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @package Bitpay
 */
class Token implements TokenInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $facade;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var array
     */
    protected $policies;

    /**
     * @var string
     */
    protected $pairingCode;

    /**
     * @var \DateTime
     */
    protected $pairingExpiration;

    /**
     */
    public function __construct()
    {
        $this->policies = array();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getToken();
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacade()
    {
        return $this->facade;
    }

    public function setFacade($facade)
    {
        $this->facade = $facade;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getPolicies()
    {
        return $this->policies;
    }

    public function setPolicies($policies)
    {
        $this->policies = $policies;

        return $this;
    }

    /**
     * @return string
     */
    public function getPairingCode()
    {
        return $this->pairingCode;
    }
    
    public function setPairingCode($pairingCode)
    {
        $this->pairingCode = $pairingCode;
        
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPairingExpiration()
    {
        return $this->pairingExpiration;
    }

    public function setPairingExpiration(\DateTime $pairingExpiration)
    {
        $this->pairingExpiration = $pairingExpiration;

        return $this;
    }
}
