<?php

class Twocheckout_Company extends Twocheckout
{

    public static function retrieve()
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = '/api/acct/detail_company_info';
        $result = $request->doCall($urlSuffix);
        return Twocheckout_Util::returnResponse($result);
    }
}

class Twocheckout_Contact extends Twocheckout
{

    public static function retrieve()
    {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix = '/api/acct/detail_contact_info';
        $result = $request->doCall($urlSuffix);
        return Twocheckout_Util::returnResponse($result);
    }
}
