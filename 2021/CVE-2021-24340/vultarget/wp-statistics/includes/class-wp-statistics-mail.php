<?php

class WP_Statistics_Mail
{
    private $to = array();
    private $cc = array();
    private $bcc = array();
    private $headers = array();
    private $attachments = array();
    private $sendAsHTML = true;
    private $subject = '';
    private $from = '';
    private $headerTemplate = false;
    private $headerVariables = array();
    private $template = false;
    private $variables = array();
    private $afterTemplate = false;
    private $footerVariables = array();
    private $body;

    /**
     * Init WordPress Mail
     *
     * @return WP_Statistics_Mail
     */
    public static function init()
    {
        return new self;
    }

    /**
     * Set recipients
     *
     * @param array|String $to
     * @return Object $this
     */
    public function setTo($to)
    {
        if (is_array($to)) {
            $this->to = $to;
        } else {
            $this->to = explode(',', $to);
        }
        return $this;
    }

    /**
     * Get recipients
     *
     * @return array $to
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set Cc recipients
     *
     * @param String|array $cc
     * @return Object $this
     */
    public function setCc($cc)
    {
        if (is_array($cc)) {
            $this->cc = $cc;
        } else {
            $this->cc = array($cc);
        }
        return $this;
    }

    /**
     * Get Cc recipients
     *
     * @return array $cc
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set Email Bcc recipients
     *
     * @param String|array $bcc
     * @return Object $this
     */
    public function setBcc($bcc)
    {
        if (is_array($bcc)) {
            $this->bcc = $bcc;
        } else {
            $this->bcc = array($bcc);
        }
        return $this;
    }

    /**
     * Set email Bcc recipients
     *
     * @return array $bcc
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * Set email Subject
     *
     * @param string $subject
     * @return Object $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Return email subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set From header
     *
     * @param String
     * @return Object $this
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Set the email's headers
     *
     * @param String|array $headers [description]
     * @return Object $this
     */
    public function setHeaders($headers)
    {
        if (is_array($headers)) {
            $this->headers = $headers;
        } else {
            $this->headers = array($headers);
        }
        return $this;
    }

    /**
     * Return headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns email content type
     * @return String
     */
    public function HTMLFilter()
    {
        return 'text/html';
    }

    /**
     * Set email content type
     *
     * @param Bool $html
     * @return Object $this
     */
    public function sendAsHTML($html)
    {
        $this->sendAsHTML = $html;
        return $this;
    }

    /**
     * Attach a file or array of files.
     * File-paths must be absolute.
     *
     * @param String|array $path
     * @return Object $this
     * @throws Exception
     */
    public function setAttach($path)
    {
        if (is_array($path)) {
            $this->attachments = array();
            foreach ($path as $path_) {
                if (!file_exists($path_)) {
                    throw new Exception("Attachment not found at $path");
                } else {
                    $this->attachments[] = $path_;
                }
            }
        } else {
            if (!file_exists($path)) {
                throw new Exception("Attachment not found at $path");
            }
            $this->attachments = array($path);
        }
        return $this;
    }

    /**
     * Set the before-template file
     *
     * @param String $template Path to HTML template
     * @param array $variables
     * @return Object $this
     * @throws Exception
     */
    public function templateHeader($template, $variables = null)
    {
        if (!file_exists($template)) {
            throw new Exception('Template file not found');
        }
        if (is_array($variables)) {
            $this->headerVariables = $variables;
        }
        $this->headerTemplate = $template;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Set the template file
     *
     * @param String $template Path to HTML template
     * @param array $variables
     * @return Object $this
     * @throws Exception
     */
    public function setTemplate($template, $variables = null)
    {
        if ($template and !file_exists($template)) {
            throw new Exception('File not found');
        }

        if (is_array($variables)) {
            $this->variables = $variables;
        }

        $this->template = $template;
        return $this;
    }

    /**
     * Set the after-template file
     *
     * @param String $template Path to HTML template
     * @param array $variables
     * @return Object $this
     * @throws Exception
     */
    public function setTemplateFooter($template, $variables = null)
    {
        if (!file_exists($template)) {
            throw new Exception('Template file not found');
        }
        if (is_array($variables)) {
            $this->footerVariables = $variables;
        }
        $this->afterTemplate = $template;
        return $this;
    }

    /**
     * Renders the template
     *
     * @return String
     * @throws Exception
     */
    public function render()
    {
        return $this->renderPart('before') .
            $this->renderPart('main') .
            $this->renderPart('after');
    }

    /**
     * Render a specific part of the email
     *
     * @param String $part before, after, main
     * @return String
     * @throws Exception
     * @author Anthony Budd
     */
    public function renderPart($part = 'main')
    {
        switch ($part) {
            case 'before':
                $templateFile = $this->headerTemplate;
                $variables    = $this->headerVariables;
                break;
            case 'after':
                $templateFile = $this->afterTemplate;
                $variables    = $this->footerVariables;
                break;
            case 'main':
            default:
                $templateFile = $this->template;
                $variables    = $this->variables;
                break;
        }

        if ($templateFile === false) {
            return '';
        }

        $extension = strtolower(pathinfo($templateFile, PATHINFO_EXTENSION));
        if ($extension === 'php') {

            ob_start();
            ob_clean();
            foreach ($variables as $key => $value) {
                $$key = $value;
            }

            include $templateFile;
            $html = ob_get_clean();
            return $html;

        } elseif ($extension === 'html') {

            $template = file_get_contents($templateFile);
            if (!is_array($variables) || empty($variables)) {
                return $template;
            }
            return $this->parseAsMustache($template, $variables);

        } else {
            throw new Exception("Unknown extension {$extension} in path '{$templateFile}'");
        }
    }

    public function buildSubject()
    {
        return $this->parseAsMustache(
            $this->subject,
            array_merge($this->headerVariables, $this->variables, $this->footerVariables));
    }

    public function parseAsMustache($string, $variables = array())
    {
        preg_match_all('/\{\{\s*.+?\s*\}\}/', $string, $matches);
        foreach ($matches[0] as $match) {
            $var = str_replace('{', '', str_replace('}', '', preg_replace('/\s+/', '', $match)));
            if (isset($variables[$var]) && !is_array($variables[$var])) {
                $string = str_replace($match, $variables[$var], $string);
            }
        }
        return $string;
    }

    /**
     * Builds Email Headers
     *
     * @return String email headers
     */
    public function buildHeaders()
    {
        $headers = '';
        $headers .= implode("\r\n", $this->headers) . "\r\n";
        foreach ($this->bcc as $bcc) {
            $headers .= sprintf("Bcc: %s \r\n", $bcc);
        }
        foreach ($this->cc as $cc) {
            $headers .= sprintf("Cc: %s \r\n", $cc);
        }
        if (!empty($this->from)) {
            $headers .= sprintf("From: %s \r\n", $this->from);
        }
        return $headers;
    }

    /**
     * Sends a rendered email using
     * WordPress's wp_mail() function
     *
     * @return Bool
     * @throws Exception
     */
    public function send()
    {
        if (count($this->to) === 0) {
            throw new Exception('You must set at least 1 recipient');
        }

        /**
         * Modify the body the the template exists.
         */
        if ($this->template) {
            $this->body = $this->render();
        }

        if ($this->sendAsHTML) {
            add_filter('wp_mail_content_type', array($this, 'HTMLFilter'));
        }

        return wp_mail($this->to, $this->buildSubject(), $this->body, $this->buildHeaders(), $this->attachments);
    }

}