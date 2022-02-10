<?php

class Twocheckout_Return extends Twocheckout
{

    public static function check($params=array(), $secretWord, $format='json')
    {
        $hashSecretWord = $secretWord;
        $hashSid = $params['sid'];
        $hashTotal = $params['total'];
        $hashOrder = $params['order_number'];
        $StringToHash = strtoupper(md5($hashSecretWord . $hashSid . $hashOrder . $hashTotal));
        if ($StringToHash != $params['key']) {
            $result = Twocheckout_Message::message('Fail', 'Hash Mismatch');
        } else {
            $result = Twocheckout_Message::message('Success', 'Hash Matched');
        }
        return Twocheckout_Util::return_resp($result, $format);
    }

}