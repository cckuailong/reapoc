<?php

namespace MailchimpAPI\Settings;

use MailchimpAPI\MailchimpException;

/**
 * Class MailchimpSettings
 * @package MailchimpAPI\Settings
 */
class MailchimpSettings
{
    /**
     * @var bool
     */
    private $debug = false;
    /**
     * @var null
     */
    private $log_file = null;
    /**
     * @var bool
     */
    private $verify_ssl = true;
    /**
     * @var array
     */
    private $custom_curl_settings = [];


    /*************************************
     * GETTERS
     *************************************/

    /**
     * @return bool
     */
    public function shouldDebug()
    {
        return $this->debug;
    }

    /**
     * @return null
     */
    public function getLogFile()
    {
        return $this->log_file;
    }

    /**
     * @return bool
     */
    public function shouldVerifySsl()
    {
        return $this->verify_ssl;
    }

    /**
     * @return bool
     */
    public function shouldDebugAndLog()
    {
        return ($this->shouldDebug() && $this->getLogFile());
    }


    /**
     * @return array
     */
    public function getCustomCurlSettings()
    {
        return $this->custom_curl_settings;
    }

    /*************************************
     * SETTERS
     *************************************/

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool)$debug;
    }

    /**
     * Set the log file from an absolute path to a writable file
     * log file must already exist to be writable
     *
     * @param null $log_file
     *
     * @throws MailchimpException
     */
    public function setLogFile($log_file)
    {
        if (is_writable($log_file)) {
            $this->log_file = $log_file;
        } else {
            throw new MailchimpException("Cannot write to provided log file");
        }
    }

    /**
     * @param bool $verify_ssl
     */
    public function setVerifySsl($verify_ssl)
    {
        $this->verify_ssl = (bool)$verify_ssl;
    }

    /**
     * Set custom curl options by providing a map of
     * option => value
     *
     * @param array $options
     */
    public function setCustomCurlSettings(array $options)
    {
        foreach ($options as $option => $value) {
            $this->custom_curl_settings[$option] = $value;
        }
    }
}
