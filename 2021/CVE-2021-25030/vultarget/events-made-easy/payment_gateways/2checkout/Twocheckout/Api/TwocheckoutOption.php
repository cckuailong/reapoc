<?php

class Twocheckout_Option extends Twocheckout
{

    public static function create($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/create_option';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function retrieve($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        if(array_key_exists("option_id",$params)) {
            $urlSuffix = 'products/detail_option';
        } else {
            $urlSuffix = 'products/list_options';
        }
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function update($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/update_option';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function delete($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/delete_option';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

}