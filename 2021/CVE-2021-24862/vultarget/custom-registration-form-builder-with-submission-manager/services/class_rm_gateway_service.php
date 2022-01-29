<?php


interface RM_Gateway_Service {
    function charge($data,$pricing_details);
    function refund();
    function cancel();
    function subscribe();
}
