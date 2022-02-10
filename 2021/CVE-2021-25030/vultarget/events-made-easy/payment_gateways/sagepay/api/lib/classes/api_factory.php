<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * Factory to obtain an instance of any of the available APIs.
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayApiFactory
{
    /**
     * Create instance of required integration type
     *
     * @param string           $type    Integration type
     * @param SagepaySettings  $config  Sagepay config instance
     *
     * @return SagepayAbstractApi
     */
    static public function create($type, SagepaySettings $config)
    {
        $integration = strtolower($type);
        $integrationApi = 'Sagepay' . ucfirst($integration) . 'Api';

        if (class_exists($integrationApi))
        {
            return new $integrationApi($config);
        }
        else
        {
            return NULL;
        }
    }

}
