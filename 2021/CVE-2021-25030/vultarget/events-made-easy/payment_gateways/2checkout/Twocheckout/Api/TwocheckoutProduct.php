<?php

class Twocheckout_Product extends Twocheckout
{

    public static function create($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/create_product';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function retrieve($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        if(array_key_exists("product_id",$params)) {
            $urlSuffix = 'products/detail_product';
        } else {
            $urlSuffix = 'products/list_products';
        }
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function update($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/update_product';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function delete($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/delete_product';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

}