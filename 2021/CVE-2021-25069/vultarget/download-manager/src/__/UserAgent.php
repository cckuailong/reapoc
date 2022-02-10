<?php


namespace WPDM\__;


class UserAgent
{
    public $HTTP_USER_AGENT;
    public $browserName = 'Unknown';
    public $browserVersion = 'Unknown';
    public $OS = 'Unknown';

    function __construct()
    {
        $this->HTTP_USER_AGENT = isset($_SERVER, $_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        //$this->parse($this->HTTP_USER_AGENT);
    }

    function set($HTTP_USER_AGENT){
        $this->HTTP_USER_AGENT = $HTTP_USER_AGENT;
        return $this;
    }

    function parse()
    {
        $this->getOS();
        $this->getBrowser();
        return $this;
    }

    function getOS()
    {
        if (preg_match('/linux/i', $this->HTTP_USER_AGENT)) {
            $this->OS = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $this->HTTP_USER_AGENT)) {
            $this->OS = 'Mac OS X';
        } elseif (preg_match('/windows|win32/i', $this->HTTP_USER_AGENT)) {
            $this->OS = 'Windows';
        } else
            $this->OS = 'Unknown';
        return $this->OS;
    }

    function getBrowser()
    {
        $ub = "";
        if (preg_match('/MSIE/i', $this->HTTP_USER_AGENT) && !preg_match('/Opera/i', $this->HTTP_USER_AGENT)) {
            $this->browserName = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $this->HTTP_USER_AGENT)) {
            $this->browserName = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/OPR/i', $this->HTTP_USER_AGENT)) {
            $this->browserName = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Chrome/i', $this->HTTP_USER_AGENT) && !preg_match('/Edge/i', $this->HTTP_USER_AGENT)) {
            $this->browserName = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $this->HTTP_USER_AGENT) && !preg_match('/Edge/i', $this->HTTP_USER_AGENT)) {
            $this->browserName = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Netscape/i', $this->HTTP_USER_AGENT)) {
            $this->browserName = 'Netscape';
            $ub = "Netscape";
        } elseif (preg_match('/Edge/i', $this->HTTP_USER_AGENT)) {
            $this->browserName = 'Edge';
            $ub = "Edge";
        } elseif (preg_match('/Trident/i', $this->HTTP_USER_AGENT)) {
            $this->browserName = 'Internet Explorer';
            $ub = "MSIE";
        } else
            $this->browserName = 'Unknown';

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if(preg_match_all($pattern, $this->HTTP_USER_AGENT, $matches)) {
            $i = count($matches['browser']);
            if ($i != 1) {
                if (strripos($this->HTTP_USER_AGENT, "Version") < strripos($this->HTTP_USER_AGENT, $ub)) {
                    $this->browserVersion = $matches['version'][0];
                } else {
                    $this->browserVersion = $matches['version'][1];
                }
            } else {
                $this->browserVersion = $matches['version'][0];
            }
        } else {
            $this->browserVersion = 'Unknown';
        }

        if ($this->browserVersion == null || $this->browserVersion == "") {
            $this->browserVersion = "Unknown";
        }
        return json_decode( json_encode( ['name' => $this->browserName, 'version' => $this->browserVersion ] ), false );
    }

}
