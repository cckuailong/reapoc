<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Mailer
{
    /**
     * The recepient of the email
     *
     * @access protected
     * @var    string
     *
     */
    protected $send_to;

    /**
     * The From Name header
     *
     * @access protected
     * @var    string
     *
     */
    protected $from_name;

    /**
     * The From Email header
     *
     * @access protected
     * @var    string
     *
     */
    protected $from_email;

    /**
     * The Reply To header
     *
     * @access protected
     * @var    string
     *
     */
    protected $reply_to;

    /**
     * The email subject
     *
     * @access protected
     * @var    string
     *
     */
    protected $subject;

    /**
     * The email message
     *
     * @access protected
     * @var    string
     *
     */
    protected $message;

    /**
     * Sent the email
     * 
     */
    public function send()
    {
        // If send_to is empty, exit
        if (empty($this->send_to)) {
            return false;
        }

        // Email Headers
        $headers = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'To: ' . $this->send_to;

        if (!empty($this->from_email)) {
            $headers[] = 'From: ' . $this->from_name . ' <' . $this->from_email . '>';
        }

        if (!empty($this->reply_to)) {
            $headers[] = 'Reply-To: ' . $this->reply_to;
        }

        wp_mail($this->send_to, $this->subject, $this->message, $headers);

    }

}
