<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 *
 * @package Bitpay
 */
class Application implements ApplicationInterface
{
    /**
     * @var array
     */
    protected $users;

    /**
     * @var array
     */
    protected $orgs;

    /**
     */
    public function __construct()
    {
        $this->users = array();
        $this->orgs  = array();
    }

    /**
     * @inheritdoc
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @inheritdoc
     */
    public function getOrgs()
    {
        return $this->orgs;
    }

    /**
     * Add user to stack
     *
     * @param UserInterface $user
     *
     * @return ApplicationInterface
     */
    public function addUser(UserInterface $user)
    {
        if (!empty($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * Add org to stack
     *
     * @param OrgInterface $org
     *
     * @return ApplicationInterface
     */
    public function addOrg(OrgInterface $org)
    {
        if (!empty($org)) {
            $this->orgs[] = $org;
        }

        return $this;
    }
}
