<?php
/**
 * OAuth class file
 */
namespace MercadoPago;
use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\Attribute; 

/**
 * OAuth class
 * @RestMethod(resource="/oauth/token", method="create")
 */

class OAuth extends Entity
{
   /**
     * client_secret
     * @Attribute()
     * @var string
     */
    protected $client_secret;

    /**
     * grant_type
     * @Attribute()
     * @var string
     */
    protected $grant_type;

    /**
     * code
     * @Attribute()
     * @var string
     */
    protected $code;

    /**
     * redirect_uri
     * @Attribute()
     * @var string
     */
    protected $redirect_uri;

    /**
     * access_token
     * @Attribute()
     * @var string
     */
    protected $access_token;

    /**
     * public_key
     * @Attribute()
     * @var string
     */
    protected $public_key;

    /**
     * refresh_token
     * @Attribute()
     * @var string
     */
    protected $refresh_token;

    /**
     * live_mode
     * @Attribute()
     * @var boolean
     */
    protected $live_mode;

    /**
     * user_id
     * @Attribute()
     * @var int
     */
    protected $user_id;

    /**
     * token_type
     * @Attribute()
     * @var string
     */
    protected $token_type;

    /**
     * expires_in
     * @Attribute()
     * @var int
     */
    protected $expires_in;

    /**
     * scope
     * @Attribute()
     * @var string
     */
    protected $scope;


    /**
     * getAuthorizationURL
     * @param $app_id
     * @param $redirect_uri
     * @return string
     */
    public function getAuthorizationURL($app_id, $redirect_uri){
        $county_id = strtolower(SDK::getCountryId());
        return "https://auth.mercadopago.com.${county_id}/authorization?client_id=${app_id}&response_type=code&platform_id=mp&redirect_uri=${redirect_uri}";
    }


    /**
     * getOAuthCredentials
     * @param $authorization_code
     * @param $redirect_uri
     * @return bool|mixed
     * @throws \Exception
     */
    public function getOAuthCredentials($authorization_code, $redirect_uri){
      $this->client_secret = SDK::getAccessToken();
      $this->grant_type = 'authorization_code';
      $this->code = $authorization_code;
      $this->redirect_uri = $redirect_uri;

      return $this->save();
    }


    /**
     * refreshOAuthCredentials
     * @param $refresh_token
     * @return bool|mixed
     * @throws \Exception
     */
    public function refreshOAuthCredentials($refresh_token){
      $this->client_secret = SDK::getAccessToken();
      $this->grant_type = 'refresh_token';
      $this->refresh_token = $refresh_token;

      return $this->save();
    }
}
