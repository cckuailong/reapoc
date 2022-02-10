<?php

class Twocheckout_Sale extends Twocheckout
{

    public static function retrieve($params=array(), $format='json')
    {
        $request = new Twocheckout_Api_Requester();
        if(array_key_exists("sale_id",$params) || array_key_exists("invoice_id",$params)) {
            $urlSuffix = 'sales/detail_sale';
        } else {
            $urlSuffix = 'sales/list_sales';
        }
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function refund($params=array(), $format='json') {
        $request = new Twocheckout_Api_Requester();
        if(array_key_exists("lineitem_id",$params)) {
            $urlSuffix ='sales/refund_lineitem';
            $result = $request->do_call($urlSuffix, $params);
        } elseif(array_key_exists("invoice_id",$params) || array_key_exists("sale_id",$params)) {
            $urlSuffix ='sales/refund_invoice';
            $result = $request->do_call($urlSuffix, $params);
        } else {
            $result = Twocheckout_Message::message('Error', 'You must pass a sale_id, invoice_id or lineitem_id to use this method.');
        }
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function stop($params=array(), $format='json') {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix ='sales/stop_lineitem_recurring';
        if(array_key_exists("lineitem_id",$params)) {
            $result = $request->do_call($urlSuffix, $params);
        } elseif(array_key_exists("sale_id",$params)) {
            $result = Twocheckout_Sale::retrieve($params, 'array');
            $lineitemData = Twocheckout_Util::get_recurring_lineitems($result);
            if (isset($lineitemData[0])) {
                $i = 0;
                $stoppedLineitems = array();
                foreach( $lineitemData as $value )
                {
                    $params = array('lineitem_id' => $value);
                    $result = $request->do_call($urlSuffix, $params);
                    $result = json_decode($result, true);
                    if ($result['response_code'] == "OK") {
                        $stoppedLineitems[$i] = $value;
                    }
                    $i++;
                }
                $result = Twocheckout_Message::message('OK', $stoppedLineitems);
            } else {
                throw new Twocheckout_Error("No recurring lineitems to stop.");
            }
        } else {
            throw new Twocheckout_Error('You must pass a sale_id or lineitem_id to use this method.');
        }
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function active($params=array(), $format='json') {
        if(array_key_exists("sale_id",$params)) {
            $result = Twocheckout_Sale::retrieve($params);
            $array = Twocheckout_Util::return_resp($result, 'array');
            $lineitemData = Twocheckout_Util::get_recurring_lineitems($array);
            if (isset($lineitemData[0])) {
                $result = Twocheckout_Message::message('OK', $lineitemData);
                if ($format == 'array') {
                    return Twocheckout_Util::return_resp($result, $format);
                } else {
                    return Twocheckout_Util::return_resp($result, 'force_json');
                }
            } else {
                throw new Twocheckout_Error("No active recurring lineitems.");
            }
        } else {
            throw new Twocheckout_Error("You must pass a sale_id to use this method.");
        }
    }

    public static function comment($params=array(), $format='json') {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix ='sales/create_comment';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function ship($params=array(), $format='json') {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix ='sales/mark_shipped';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

    public static function reauth($params=array(), $format='json') {
        $request = new Twocheckout_Api_Requester();
        $urlSuffix ='sales/reauth';
        $result = $request->do_call($urlSuffix, $params);
        return Twocheckout_Util::return_resp($result, $format);
    }

}