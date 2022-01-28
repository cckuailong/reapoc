<?php

namespace MailchimpAPI;

/**
 * Class MailchimpException
 * @package MailchimpAPI
 */
class MailchimpException extends \Exception
{
    /**
     * @var
     */
    public $message;
    /**
     * @var null
     */
    public $output;

    /**
     * MailchimpException constructor.
     * @param $message
     * @param null $output
     */
    public function __construct($message, $output = null)
    {
        $this->message = $message;
        $this->output = $output;
    }
}
