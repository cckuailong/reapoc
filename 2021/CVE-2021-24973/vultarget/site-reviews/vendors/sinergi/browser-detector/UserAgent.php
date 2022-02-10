<?php

namespace GeminiLabs\Sinergi\BrowserDetector;

class UserAgent
{
    /**
     * @var string
     */
    private $userAgentString;

    /**
     * @param string $userAgentString
     */
    public function __construct($userAgentString = null)
    {
        if (null !== $userAgentString) {
            $this->setUserAgentString($userAgentString);
        }
    }

    /**
     * @param string $userAgentString
     *
     * @return $this
     */
    public function setUserAgentString($userAgentString)
    {
        $this->userAgentString = (string)$userAgentString;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgentString()
    {
        if (null === $this->userAgentString) {
            $this->createUserAgentString();
        }

        return $this->userAgentString;
    }

    /**
     * @return string
     */
    public function createUserAgentString()
    {
        $userAgentString = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $this->setUserAgentString($userAgentString);

        return $userAgentString;
    }
}
