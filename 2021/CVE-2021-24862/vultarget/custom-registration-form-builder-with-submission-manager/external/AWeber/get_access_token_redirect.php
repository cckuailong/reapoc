<?php
require_once('aweber_api.php');
// Replace with the keys of your application
// NEVER SHARE OR DISTRIBUTE YOUR APPLICATIONS'S KEYS!
//var_dump(!empty($_POST['consumer_key']) && !empty($_POST['consumer_key']));
if (!empty($_POST['consumer_key']) && !empty($_POST['consumer_key']))
{
    $consumerKey =$_POST['consumer_key'];
    $consumerSecret =$_POST['consumer_secret'];
}
else
{
    if(isset($_COOKIE['consumerKey']) && isset($_COOKIE['consumerSecret']))
    {
    $consumerKey =$_COOKIE['consumerKey'];
    $consumerSecret =$_COOKIE['consumerSecret'];
    }
   
}
try
{
    if(isset($consumerKey) && isset($consumerSecret))
    {
$aweber = new AWeberAPI($consumerKey, $consumerSecret);


    if (empty($_GET['oauth_token'])) {
        $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        list($requestToken, $requestTokenSecret) = $aweber->getRequestToken($callbackUrl);
        setcookie('consumerKey', $consumerKey);
        setcookie('consumerSecret', $consumerSecret);
        setcookie('requestTokenSecret', $requestTokenSecret);
        setcookie('callbackUrl', $callbackUrl);
        
        header("Location: {$aweber->getAuthorizeUrl()}");
        exit();
    }

    $aweber->user->tokenSecret = $_COOKIE['requestTokenSecret'];
    $aweber->user->requestToken = $_GET['oauth_token'];
    $aweber->user->verifier = $_GET['oauth_verifier'];
    list($accessToken, $accessTokenSecret) = $aweber->getAccessToken();
    echo $accessToken;
    echo "<br>";
    echo $accessTokenSecret;
    }
    else
    {
         echo "Oops! Consumer and secret key required..";
    }
}
catch(Exception $e)
{
      echo "Oops! Please provide correct consumer and secret key.";
}
    exit();






?>
