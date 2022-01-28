<?php

namespace ProfilePress\Core\Classes;

class SendEmail
{
    protected $sender_name;

    protected $sender_email;

    protected $content_type;

    protected $to;

    protected $subject;

    protected $message;

    public function __construct($to, $subject, $message)
    {
        $this->to      = $to;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function email_content_type()
    {
        return ppress_get_setting('email_content_type', 'text/html', true);
    }

    public function email_sender_name()
    {
        return sanitize_text_field(
            ppress_get_setting('email_sender_name', ppress_site_title(), true)
        );
    }

    public function email_sender_email()
    {
        return sanitize_email(
            ppress_get_setting('email_sender_email', 'wordpress@' . ppress_site_url_without_scheme(), true)
        );
    }

    public function get_headers()
    {
        $headers = [];
        if ( ! defined('W3GUY_LOCAL')) {
            $headers[] = "From: {$this->email_sender_name()} <{$this->email_sender_email()}>";
            $headers[] = "Reply-To: {$this->email_sender_name()} <{$this->email_sender_email()}>";
        }
        $headers[] = sprintf("Content-type: %s", $this->email_content_type());
        $headers[] = 'charset=UTF-8';

        return apply_filters('ppress_email_headers', $headers);
    }

    public function before_send()
    {
        add_action('wp_mail_failed', [$this, 'log_wp_mail_failed']);

        add_filter('wp_mail_from', [$this, 'email_sender_email']);
        add_filter('wp_mail_from_name', [$this, 'email_sender_name']);
        add_filter('wp_mail_content_type', [$this, 'email_content_type']);
    }

    public function after_send()
    {
        remove_action('wp_mail_failed', [$this, 'log_wp_mail_failed']);

        remove_filter('wp_mail_from', [$this, 'email_sender_email']);
        remove_filter('wp_mail_from_name', [$this, 'email_sender_name']);
        remove_filter('wp_mail_content_type', [$this, 'email_content_type']);
    }

    public function templatified_email()
    {
        $message = $this->message;

        if ($this->email_content_type() == 'text/html') {
            $message        = htmlspecialchars_decode(stripslashes($message));
            $email_template = ppress_get_setting('email_template_type', 'default', true);

            if ($email_template == 'default') {
                ob_start();
                $email_subject = $this->subject;
                $email_content = $this->message;
                require dirname(__FILE__) . '/default-email-template.php';
                $message = ob_get_clean();
            }

            if ( ! is_customize_preview()) {
                /** @see https://github.com/MyIntervals/emogrifier/tree/v2.2.0 */
                $emogrifier = new \Pelago\Emogrifier();
                $emogrifier->setHtml($message);
                $message = $emogrifier->emogrify();
            }
        }

        return $message;
    }

    /**
     * @param \WP_Error $wp_error
     */
    public function log_wp_mail_failed($wp_error)
    {
        $mail_error_data = $wp_error->get_error_data();

        $context = [
            'to'      => $mail_error_data['to'],
            'subject' => $mail_error_data['subject']
        ];

        ppress_log_error($wp_error->get_error_message() . ' => ' . json_encode($context));
    }

    public function send()
    {
        if (empty($this->to) || empty($this->message) || empty($this->subject)) {
            return false;
        }

        $this->message = $this->templatified_email();

        $this->before_send();

        $result = wp_mail($this->to, $this->subject, $this->message, $this->get_headers());

        // if failed, try without the header.
        if ( ! $result) {
            $result = wp_mail($this->to, $this->subject, $this->message);
        }

        $this->after_send();

        if ( ! $result) {
            return false;
        }

        return $result;
    }
}