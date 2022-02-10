<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class ConstantContact_Popb_sync {



function getAccessTokenMultiWays($accessToken, $refreshToken, $oauthToken){


	$clientId = 'e4811788-6713-4ac8-81a2-0cde444c0987';
	$clientSecret = 'Zfful29ve-2Zn3UiyFdg1w';
	$redirectURI = urlencode( "https://pluginops.com" );

	if ($accessToken == false || $accessToken == '') {
		if ($refreshToken != '' && $refreshToken != false) {
			$accessToken =  $this->getAccessViaRefreshToken( $refreshToken, $clientId, $clientSecret);
		}else{

			if ($oauthToken == '') {
				return 'Invalid OAuth';
			}
			$accessToken = $this->getAccessToken($redirectURI, $clientId, $clientSecret, $oauthToken);
			$accessToken = json_decode($accessToken);

			if ( $accessToken->error ) {
				update_option( 'popb_constant_contact_access_token', false, null );
				update_option( 'popb_constant_contact_refresh_token', false, null );
				return $accessToken->error;
			}else{
				$token = $accessToken->access_token;
				update_option( 'popb_constant_contact_access_token', $token, null );
				$refreshToken = $accessToken->refresh_token;
				update_option( 'popb_constant_contact_refresh_token', $refreshToken, null );

				$accessToken = $token;
			}
			
		}

	}

	return $accessToken;

}



function getConstantContactLists($oauthToken,$accessToken,$refreshToken){


	$fetchedAccessToken = $this->getAccessTokenMultiWays($accessToken, $refreshToken, $oauthToken);

	$url = 'https://api.cc.email/v3/contact_lists?include_count=false&grant_type=authorization_code&scope=contact_data';
	$header[] = "Authorization: Bearer $fetchedAccessToken";
	$header[] = 'Content-Type: application/json';
	$header[] = 'cache-control: no-cache';
	
	$body = '';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	if(curl_error($ch)) echo 'error:' . curl_error($ch);

	$response = json_decode($response);

	if ($response->error) {
		return $response->error;
	}

	return $response;

}


function getConstantContactCustomFields($refreshToken){


	$clientId = 'e4811788-6713-4ac8-81a2-0cde444c0987';
	$clientSecret = 'Zfful29ve-2Zn3UiyFdg1w';
	$redirectURI = urlencode( "https://pluginops.com" );

	$fetchedAccessToken =  $this->getAccessViaRefreshToken( $refreshToken, $clientId, $clientSecret);

	$url = 'https://api.cc.email/v3/contact_custom_fields';
	$header[] = "Authorization: Bearer $fetchedAccessToken";
	$header[] = 'Content-Type: application/json';
	
	$body = '';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	if(curl_error($ch)) echo 'error:' . curl_error($ch);

	$response = json_decode($response);

	if ($response->error) {
		return $response->error;
	}

	return $response;

}



/*
	* This function can be used to exchange an authorization code for an access token.
	* Make this call by passing in the code present when the account owner is redirected back to you.
	* The response will contain an 'access_token' and 'refresh_token'
*/

/*
 * This function can be used to exchange an authorization code for an access token.
 * Make this call by passing in the code present when the account owner is redirected back to you.
 * The response will contain an 'access_token' and 'refresh_token'
 */

/***
 * @param $redirectURI - URL Encoded Redirect URI
 * @param $clientId - API Key
 * @param $clientSecret - API Secret
 * @param $code - Authorization Code
 * @return string - JSON String of results
 */
function getAccessToken($redirectURI, $clientId, $clientSecret, $code) {
    // Use cURL to get access token and refresh token
    $ch = curl_init();

    // Define base URL
    $base = 'https://idfed.constantcontact.com/as/token.oauth2';

    // Create full request URL
    $url = $base . '?code=' . $code . '&redirect_uri=' . $redirectURI . '&grant_type=authorization_code&scope=contact_data';
    curl_setopt($ch, CURLOPT_URL, $url);

    // Set authorization header
    // Make string of "API_KEY:SECRET"
    $auth = $clientId . ':' . $clientSecret;
    // Base64 encode it
    $credentials = base64_encode($auth);
    // Create and set the Authorization header to use the encoded credentials
    $authorization = 'Authorization: Basic ' . $credentials;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));

    // Set method and to expect response
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Make the call
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}


/*
 * This function can be used to exchange a refresh token for a new access token and refresh token.
 * Make this call by passing in the refresh token returned with the access token.
 * The response will contain a new 'access_token' and 'refresh_token'
 */

/***
 * @param $refreshToken - The refresh token provided with the previous access token
 * @param $clientId - API Key
 * @param $clientSecret - API Secret
 * @return string - JSON String of results
 */
function getAccessViaRefreshToken($refreshToken, $clientId, $clientSecret) {
    // Use cURL to get a new access token and refresh token
    $ch = curl_init();

    // Define base URL
    $base = 'https://idfed.constantcontact.com/as/token.oauth2';

    // Create full request URL
    $url = $base . '?refresh_token=' . $refreshToken . '&grant_type=refresh_token';
    curl_setopt($ch, CURLOPT_URL, $url);

    // Set authorization header
    // Make string of "API_KEY:SECRET"
    $auth = $clientId . ':' . $clientSecret;
    // Base64 encode it
    $credentials = base64_encode($auth);
    // Create and set the Authorization header to use the encoded credentials
    $authorization = 'Authorization: Basic ' . $credentials;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));

    // Set method and to expect response
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Make the call
    $result = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($result);

    if ($result->access_token) {
    	$token = $result->access_token;
		update_option( 'popb_constant_contact_access_token', $token, null );
		$refreshToken = $result->refresh_token;
		update_option( 'popb_constant_contact_refresh_token', $refreshToken, null );
    }

    if ($result->error) {
    	$token = $result->error;
    }
    
    return $token;
}




function constactContactSyncFormBuilderData($body, $refreshToken ){

	$clientId = 'e4811788-6713-4ac8-81a2-0cde444c0987';
	$clientSecret = 'Zfful29ve-2Zn3UiyFdg1w';
	$redirectURI = urlencode( "https://pluginops.com" );
	
	$accessToken =  $this->getAccessViaRefreshToken( $refreshToken, $clientId, $clientSecret);

	$url = 'https://api.cc.email/v3/contacts';
	$header[] = "Authorization: Bearer $accessToken";
	$header[] = 'Content-Type: application/json';
	$header[] = 'cache-control: no-cache';
					
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	if(curl_error($ch)) echo 'error:' . curl_error($ch);
	
	return $response;

}


}

?>