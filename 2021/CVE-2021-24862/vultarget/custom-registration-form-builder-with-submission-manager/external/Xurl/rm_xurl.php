<?php

class RM_Xurl
{

    public $h_curl;
    public $last_result;
    public $default_url;
    public $last_error;

    public function __construct($url, $headers_in_output = false)
    {
        $this->h_curl = curl_init();
        $this->set_opt(CURLOPT_RETURNTRANSFER, true);
        $this->set_opt(CURLOPT_FOLLOWLOCATION, true);
        $this->header($headers_in_output);
        $this->default_url = $url;
        $this->set_url($this->default_url);
    }

    public function set_opt($opt_name, $value)
    {
        curl_setopt($this->h_curl, $opt_name, $value);
    }

    public function set_req_type($req_type_name)
    {
        if (strtolower($req_type_name) == 'post')
            $this->set_opt(CURLOPT_POST, true);
        elseif (strtolower($req_type_name) == 'get')
            $this->set_opt(CURLOPT_HTTPGET, true);
    }

    public function exec()
    {
        $this->last_result = curl_exec($this->h_curl);
        if($this->last_result === false)
            $this->last_error = curl_error ($this->h_curl);
        
        return $this->last_result;
    }

    public function get_last_result()
    {
        return $this->last_result;
    }

    public function get_last_error()
    {
        return $this->last_error;
    }

    public function end()
    {
        curl_close($this->h_curl);
    }

    public function set_url($url)
    {
        $this->set_opt(CURLOPT_URL, $url);
    }

    //Reset back to default URL
    public function reset_url()
    {
        $this->set_opt(CURLOPT_URL, $this->get_default_url());
    }

    public function get_default_url()
    {
        return $this->default_url;
    }

    public function header($true_or_false)
    {
        $this->set_opt(CURLOPT_HEADER, $true_or_false);
    }

    public function post($fields)
    {
        $req_str = "";

        foreach ($fields as $field => $value)
        {
            $req_str .= urlencode($field) . "=" . urlencode($value) . "&";
        }
        rtrim($req_str, '&');

        $this->set_req_type('POST');
        $this->set_opt(CURLOPT_POSTFIELDS, $req_str);
        return $this->exec();
    }

    public function get($fields)
    {
        $req_str = "?";

        foreach ($fields as $field => $value)
        {
            $req_str .= urlencode($field) . "=" . urlencode($value) . "&";
        }
        rtrim($req_str, '&');

        $url_for_get = $this->get_default_url() . $req_str;

        $this->set_req_type('GET');
        $this->set_url($url_for_get);
        $res = $this->exec();

        //Reset back to original url
        $this->reset_url();

        return $res;
    }

}
