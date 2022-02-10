<?php

namespace Instamojo;

class Instamojo {
    const version = '1.1';

    protected $curl;
    protected $endpoint = 'https://www.instamojo.com/api/1.1/';
    protected $api_key = null;
    protected $auth_token = null;

    /**
    * @param string $api_key
    * @param string $auth_token is available on the d
    * @param string $endpoint can be set if you are working on an alternative server.
    * @return array AuthToken object.
    */
    public function __construct($api_key, $auth_token=null, $endpoint=null) 
    {
        $this->api_key = (string) $api_key;
        $this->auth_token = (string) $auth_token;
        if(!is_null($endpoint)){
            $this->endpoint = (string) $endpoint;   
        }
    }

    public function __destruct() 
    {
        if(!is_null($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
    * @return array headers with Authentication tokens added 
    */
    private function build_curl_headers() 
    {
        $headers = array("X-Api-key: $this->api_key");
        if($this->auth_token) {
            $headers[] = "X-Auth-Token: $this->auth_token";
        }
        return $headers;        
    }

    /**
    * @param string $path
    * @return string adds the path to endpoint with.
    */
    private function build_api_call_url($path)
    {
        if (strpos($path, '/?') === false and strpos($path, '?') === false) {
            return $this->endpoint . $path . '/';
        }
        return $this->endpoint . $path;

    }

    /**
    * @param string $method ('GET', 'POST', 'DELETE', 'PATCH')
    * @param string $path whichever API path you want to target.
    * @param array $data contains the POST data to be sent to the API.
    * @return array decoded json returned by API.
    */
    private function api_call($method, $path, array $data=null) 
    {
        $path = (string) $path;
        $method = (string) $method;
        $data = (array) $data;
        $headers = $this->build_curl_headers();
        $request_url = $this-> build_api_call_url($path);

        $options = array();
        $options[CURLOPT_HTTPHEADER] = $headers;
        $options[CURLOPT_RETURNTRANSFER] = true;
        
        if($method == 'POST') {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        } else if($method == 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        } else if($method == 'PATCH') {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);         
            $options[CURLOPT_CUSTOMREQUEST] = 'PATCH';
        } else if ($method == 'GET' or $method == 'HEAD') {
            if (!empty($data)) {
                /* Update URL to container Query String of Paramaters */
                $request_url .= '?' . http_build_query($data);
            }
        }
        // $options[CURLOPT_VERBOSE] = true;
        $options[CURLOPT_URL] = $request_url;
        $options[CURLOPT_SSL_VERIFYPEER] = true;
        $options[CURLOPT_CAINFO] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cacert.pem';

        $this->curl = curl_init();
        $setopt = curl_setopt_array($this->curl, $options);
        $response = curl_exec($this->curl);
        $headers = curl_getinfo($this->curl);

        $error_number = curl_errno($this->curl);
        $error_message = curl_error($this->curl);
        $response_obj = json_decode($response, true);

        if($error_number != 0){
            if($error_number == 60){
                throw new \Exception("Something went wrong. cURL raised an error with number: $error_number and message: $error_message. " .
                                    "Please check http://stackoverflow.com/a/21114601/846892 for a fix." . PHP_EOL);
            }
            else{
                throw new \Exception("Something went wrong. cURL raised an error with number: $error_number and message: $error_message." . PHP_EOL);
            }
        }

        if($response_obj['success'] == false) {
            $message = json_encode($response_obj['message']);
            throw new \Exception($message . PHP_EOL);
        }
        return $response_obj;
    }

    /**
    * @return string URL to upload file or cover image asynchronously
    */
    public function getUploadUrl()
    {
        $result = $this->api_call('GET', 'links/get_file_upload_url', array());
        return $result['upload_url'];
    }

    /**
    * @param string $file_path
    * @return string JSON returned when the file upload is complete.
    */
    public function uploadFile($file_path)
    {
        $upload_url = $this->getUploadUrl();
        $file_path = realpath($file_path);
        $file_name = basename($file_path);
        $ch = curl_init();
        $data = array('fileUpload' => $this->getCurlValue($file_path, $file_name));
        curl_setopt($ch, CURLOPT_URL, $upload_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }

    public function getCurlValue($file_path, $file_name, $content_type='')
    {
        // http://stackoverflow.com/a/21048702/846892
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        // See: https://wiki.php.net/rfc/curl-file-upload
        if (function_exists('curl_file_create')) {
            return curl_file_create($file_path, $content_type, $file_name);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$file_path};filename=$file_name";
        if ($content_type) {
            $value .= ';type=' . $content_type;
        }

        return $value;
    }

    /**
    * Uploads any file or cover image mentioned in $link and 
    * updates it with the json required by the API.
    * @param array $link
    * @return array $link updated with uploaded file information if applicable.
    */
    public function uploadMagic(array $link)
    {
        if(!empty($link['file_upload'])) {
            $file_upload_json = $this->uploadFile($link['file_upload']);
            $link['file_upload_json'] = $file_upload_json;
            unset($link['file_upload']);
        }
        if(!empty($link['cover_image'])) {
            $cover_image_json = $this->uploadFile($link['cover_image']);
            $link['cover_image_json'] = $cover_image_json;
            unset($link['cover_image']);
        }
        return $link;        
    }

    /**
    * Authenticate using username and password of a user.
    * Automatically updates the auth_token value.
    * @param array $args contains username=>USERNAME and password=PASSWORD 
    * @return array AuthToken object.
    */
    public function auth(array $args)
    {
        $response = $this->api_call('POST', 'auth', $args);
        $this->auth_token = $response['auth_token']['auth_token']; 
        return $this->auth_token; 
    }

    /**
    * @return array list of Link objects.
    */
    public function linksList() 
    {
        $response = $this->api_call('GET', 'links', array());   
        return $response['links'];
    }

    /**
    * @return array single Link object.
    */  
    public function linkDetail($slug) 
    {
        $response = $this->api_call('GET', 'links/' . $slug, array()); 
        return $response['link'];
    }

    /**
    * @return array single Link object.
    */  
    public function linkCreate(array $link) 
    {   
        if(empty($link['currency'])){
            $link['currency'] = 'INR';
        }
        $link = $this->uploadMagic($link);
        $response = $this->api_call('POST', 'links', $link);
        return $response['link'];
    }

    /**
    * @return array single Link object.
    */  
    public function linkEdit($slug, array $link) 
    {
        $link = $this->uploadMagic($link);
        $response = $this->api_call('PATCH', 'links/' . $slug, $link);
        return $response['link'];
    }

    /**
    * @return array single Link object.
    */  
    public function linkDelete($slug) 
    {
        $response = $this->api_call('DELETE', 'links/' . $slug, array());
        return $response;
    }

    /**
    * @return array list of Payment objects.
    */  
    public function paymentsList($limit = null, $page = null) 
    {
        $params = array();
        if (!is_null($limit)) {
            $params['limit'] = $limit;
        }

        if (!is_null($page)) {
            $params['page'] = $page;
        }

        $response = $this->api_call('GET', 'payments', $params);
        return $response['payments'];
    }

    /**
    * @param string payment_id as provided by paymentsList() or Instamojo's webhook or redirect functions.
    * @return array single Payment object.
    */  
    public function paymentDetail($payment_id) 
    {
        $response = $this->api_call('GET', 'payments/' . $payment_id, array()); 
        return $response['payment'];
    }


    /////   Request a Payment  /////

    /**
    * @param array single PaymentRequest object.
    * @return array single PaymentRequest object.
    */
    public function paymentRequestCreate(array $payment_request) 
    {
        $response = $this->api_call('POST', 'payment-requests', $payment_request); 
        return $response['payment_request'];
    }

    /**
    * @param string id as provided by paymentRequestCreate, paymentRequestsList, webhook or redirect.
    * @return array single PaymentRequest object.
    */
    public function paymentRequestStatus($id) 
    {
        $response = $this->api_call('GET', 'payment-requests/' . $id, array()); 
        return $response['payment_request'];
    }

    /**
    * @param string id as provided by paymentRequestCreate, paymentRequestsList, webhook or redirect.
    * @param string payment_id as received with the redirection URL or webhook.
    * @return array single PaymentRequest object.
    */
    public function paymentRequestPaymentStatus($id, $payment_id) 
    {
        $response = $this->api_call('GET', 'payment-requests/' . $id . '/' . $payment_id, array()); 
        return $response['payment_request'];
    }


    /**
    * @param array datetime_limits containing datetime data with keys 'max_created_at', 'min_created_at',
    * 'min_modified_at' and 'max_modified_at' in ISO 8601 format(optional).
    * @return array containing list of PaymentRequest objects.
    * For more information on the allowed date formats check the
    * docs: https://www.instamojo.com/developers/request-a-payment-api/#toc-filtering-payment-requests
    */
    public function paymentRequestsList($datetime_limits=null) 
    {
        $endpoint = 'payment-requests';

        if(!empty($datetime_limits)){
            $query_string = http_build_query($datetime_limits);

            if(!empty($query_string)){
                $endpoint .= '/?' . $query_string;
            }
        }
        $response = $this->api_call('GET', $endpoint, array()); 
        return $response['payment_requests'];
    }


    /////   Refunds  /////

    /**
    * @param array single Refund object.
    * @return array single Refund object.
    */
    public function refundCreate(array $refund) 
    {
        $response = $this->api_call('POST', 'refunds', $refund); 
        return $response['refund'];
    }

    /**
    * @param string id as provided by refundCreate or refundsList.
    * @return array single Refund object.
    */
    public function refundDetail($id) 
    {
        $response = $this->api_call('GET', 'refunds/' . $id, array()); 
        return $response['refund'];
    }

    /**
    * @return array containing list of Refund objects.
    */
    public function refundsList() 
    {
        $response = $this->api_call('GET', 'refunds', array()); 
        return $response['refunds'];
    }

}
?>