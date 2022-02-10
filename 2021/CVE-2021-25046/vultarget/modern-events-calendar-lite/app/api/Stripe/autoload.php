<?php
namespace Stripe;

function mec_stripe_api_php_client_autoload($className)
{
    $classPath = explode('\\', $className);
    if($classPath[0] != 'Stripe')
    {
        return;
    }
    
    // Drop first Stripe
    $classPath = array_slice($classPath, 1);
    $filePath = dirname(__FILE__) . DS . implode(DS, $classPath) . '.php';
    
    if(file_exists($filePath))
    {
        require_once $filePath;
    }
}

spl_autoload_register(__NAMESPACE__.'\mec_stripe_api_php_client_autoload');