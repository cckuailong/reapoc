<?php


namespace WPDM\Package;


class RestAPI
{

    function __construct()
    {
        add_action('rest_api_init', array($this, 'introduceEndpoints'));
    }

    function introduceEndpoints()
    {

        register_rest_route('wpdm', '/validate-captcha', array(
            'methods' => 'POST',
            'callback' => [new PackageLocks(), 'validateCaptcha'],
            'permission_callback' => '__return_true'
        ));

        register_rest_route('wpdm', '/validate-password', array(
            'methods' => 'POST',
            'callback' => [new PackageLocks(), 'validatePassword'],
            'permission_callback' => '__return_true'
        ));

        register_rest_route('wpdm', '/email-to-download', array(
            'methods' => 'POST',
            'callback' => [new PackageLocks(), 'handleEmailLock'],
            'permission_callback' => '__return_true'
        ));

        register_rest_route('wpdm', '/search', array(
            'methods' => 'GET',
            'callback' => [new PackageController(), 'search'],
            'permission_callback' => '__return_true'
        ));

    }
}
