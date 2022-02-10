<?php

class Twocheckout_Payment extends Twocheckout
{

    public static function retrieve($format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'acct/list_payments';
        $result = $request->do_call($urlSuffix);
        $response = Twocheckout_Util::return_resp($result, $format);
        return $response;
    }

        public static function pending($format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'acct/detail_pending_payment';
        $result = $request->do_call($urlSuffix);
        $response = Twocheckout_Util::return_resp($result, $format);
        return $response;
    }

}