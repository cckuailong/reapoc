<?php
namespace MercadoPago;

use Exception;

/**
 * Config Class Doc Comment
 *
 * @package MercadoPago
 */
class Config
    extends Config\AbstractConfig
{
    /**
     * Available parsers
     * @var array
     */
    private $_supportedFileParsers = [
        'MercadoPago\\Config\\Json',
        'MercadoPago\\Config\\Yaml',
    ];

    private $_restclient = null;

    /**
     * Default values
     * @return array
     */
    protected function getDefaults()
    {
        return [
            'base_url'      => 'https://api.mercadopago.com',
            'CLIENT_ID'     => '',
            'CLIENT_SECRET' => '',
            'USER_ID'       => '',
            'APP_ID'        => '',
            'ACCESS_TOKEN'  => '',
            'REFRESH_TOKEN' => '',
            'sandbox_mode'  => true,
        ];
    }

    /**
     * @param null $path
     *
     * Static load method
     * @return static
     */
    public static function load($path = null)
    {
        return new static($path);
    }

    /**
     * Config constructor.
     *
     * @param null $path
     * @param null $restClient
     */
    public function __construct($path = null, $restClient = null)
    {
        $this->data = [];
        $this->_restclient = $restClient;
        if (is_file($path)) {
            $info = pathinfo($path);
            $parts = explode('.', $info['basename']);
            $extension = array_pop($parts);
            $parser = $this->_getParser($extension);

            foreach ((array)$parser->parse($path) as $key => $value) {
                $this->set($key, $value);
            }
        }

        parent::__construct($this->data);
    }

    /**
     * @param $extension
     *
     * Get Parser depending on extension
     * @return null
     * @throws Exception
     */
    private function _getParser($extension)
    {
        $parser = null;
        foreach ($this->_supportedFileParsers as $fileParser) {
            $tempParser = new $fileParser;
            if (in_array($extension, $tempParser->getSupportedExtensions($extension))) {
                $parser = $tempParser;
                continue;
            }
        }
        if ($parser === null) {
            throw new Exception('Unsupported configuration format');
        }

        return $parser;
    }

    /**
     * Set config value
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        parent::set($key, $value);

        if ($key == "ACCESS_TOKEN") { 
            $user = $this->getUserId($value);
            parent::set('USER_ID', $user['id']);
            parent::set('COUNTRY_ID', $user['country_id']);
        }
        
        if (parent::get('CLIENT_ID') != "" && parent::get('CLIENT_SECRET') != "" && empty(parent::get('ACCESS_TOKEN'))) {
            
            $response = $this->getToken();

            if (isset($response['access_token'])) {
                parent::set('ACCESS_TOKEN', $response['access_token']);
            

                $user = $this->getUserId($response['access_token']);

                if (isset($user['id'])) {
                    parent::set('USER_ID', $user['id']);
                }

            }
            
        }
    }


    /** 
     * @return mixed
     */
    public function getUserId()
    {
        if (!$this->_restclient) {
            $this->_restclient = new RestClient();
            $this->_restclient->setHttpParam('address', $this->get('base_url'));
        }
        $response = $this->_restclient->get("/users/me");

        return $response["body"];
    }

    /**
     * Obtain token with key and secret
     * @return mixed
     */
    public function getToken()
    {
        if (!$this->_restclient) {
            $this->_restclient = new RestClient();
        }
        $data = ['grant_type'    => 'client_credentials',
            'client_id'     => $this->get('CLIENT_ID'),
            'client_secret' => $this->get('CLIENT_SECRET')];
        $this->_restclient->setHttpParam('address', $this->get('base_url'));
        $response = $this->_restclient->post("/oauth/token", ['json_data' => json_encode($data)]);
        return $response['body'];
    }

    /**
     * Refresh token
     * @return mixed
     * //TODO check valid response with production credentials
     */
    public function refreshToken()
    {
        if (!$this->_restclient) {
            $this->_restclient = new RestClient();
        }
        $data = ['grant_type'    => 'refresh_token',
                'refresh_token'     => $this->get('REFRESH_TOKEN'),
                'client_secret' => $this->get('ACCESS_TOKEN')];
        $this->_restclient->setHttpParam('address', $this->get('base_url'));
        $response = $this->_restclient->post("/oauth/token", ['json_data' => json_encode($data)]);
        if (isset($response['access_token']) && isset($response['refresh_token']) && isset($response['client_id']) && isset($response['client_secret'])) {
            parent::set('ACCESS_TOKEN', $response['access_token']);
            parent::set('REFRESH_TOKEN', $response['refresh_token']);
        }
        return $response['body'];
    }

    public function getData(){
        return $this->data;
    }
}