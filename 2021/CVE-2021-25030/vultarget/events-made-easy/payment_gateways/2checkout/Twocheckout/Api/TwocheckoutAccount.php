<?php

class Twocheckout_Company extends Twocheckout
{

    public static function retrieve($format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'acct/detail_company_info';
        $result = $request->do_call($urlSuffix);
        return Twocheckout_Util::return_resp($result, $format);
    }
}

class Twocheckout_Contact extends Twocheckout
{

    public static function retrieve($format='json')
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = 'acct/detail_contact_info';
        $result = $request->do_call($urlSuffix);
        return Twocheckout_Util::return_resp($result, $format);
    }
}