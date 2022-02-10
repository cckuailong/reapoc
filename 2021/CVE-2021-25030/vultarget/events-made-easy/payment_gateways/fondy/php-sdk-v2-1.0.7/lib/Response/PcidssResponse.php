<?php

namespace Cloudipsp\Response;

use Cloudipsp\Pcidss;

class PcidssResponse extends Response
{
    /**
     * @return bool
     */
    public function is3ds()
    {
        if (array_key_exists('acs_url', $this->response['response'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $response_url
     * @return mixed
     * @throws \Cloudipsp\Exception\ApiException
     */
    public function get3dsFormContent($response_url = '')
    {
        trigger_error('Deprecated: this function is deprecated use get3dsForm instead!', E_NOTICE);
        $data = $this->getData();
        return Pcidss::get3dsFrom($data, $response_url);
    }
}
