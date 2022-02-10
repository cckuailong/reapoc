<?php

class Twocheckout_Coupon extends Twocheckout
{

    public static function create($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/create_coupon';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function retrieve($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        if(array_key_exists("coupon_code",$params)) {
            $urlSuffix = 'products/detail_coupon';
        } else {
            $urlSuffix = 'products/list_coupons';
        }
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function update($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/update_coupon';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function delete($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'products/delete_coupon';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

}