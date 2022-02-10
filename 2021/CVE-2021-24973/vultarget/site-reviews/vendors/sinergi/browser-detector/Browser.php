<?php
/**
 * @package sinergi/browser-detector v6.1.2
 */
namespace GeminiLabs\Sinergi\BrowserDetector;

/**
 * Browser Detection.
 */
class Browser
{
    const UNKNOWN = 'unknown';
    const VIVALDI = 'Vivaldi';
    const OPERA = 'Opera';
    const OPERA_MINI = 'Opera Mini';
    const WEBTV = 'WebTV';
    const IE = 'Internet Explorer';
    const POCKET_IE = 'Pocket Internet Explorer';
    const KONQUEROR = 'Konqueror';
    const ICAB = 'iCab';
    const OMNIWEB = 'OmniWeb';
    const FIREBIRD = 'Firebird';
    const FIREFOX = 'Firefox';
    const SEAMONKEY = 'SeaMonkey';
    const ICEWEASEL = 'Iceweasel';
    const SHIRETOKO = 'Shiretoko';
    const MOZILLA = 'Mozilla';
    const AMAYA = 'Amaya';
    const LYNX = 'Lynx';
    const WKHTMLTOPDF = 'wkhtmltopdf';
    const SAFARI = 'Safari';
    const SAMSUNG_BROWSER = 'SamsungBrowser';
    const CHROME = 'Chrome';
    const NAVIGATOR = 'Navigator';
    const GOOGLEBOT = 'GoogleBot';
    const SLURP = 'Yahoo! Slurp';
    const W3CVALIDATOR = 'W3C Validator';
    const BLACKBERRY = 'BlackBerry';
    const ICECAT = 'IceCat';
    const NOKIA_S60 = 'Nokia S60 OSS Browser';
    const NOKIA = 'Nokia Browser';
    const MSN = 'MSN Browser';
    const MSNBOT = 'MSN Bot';
    const NETSCAPE_NAVIGATOR = 'Netscape Navigator';
    const GALEON = 'Galeon';
    const NETPOSITIVE = 'NetPositive';
    const PHOENIX = 'Phoenix';
    const GSA = 'Google Search Appliance';
    const YANDEX = 'Yandex';
    const EDGE = 'Edge';
    const DRAGON = 'Dragon';

    const VERSION_UNKNOWN = 'unknown';

    /**
     * @var UserAgent
     */
    private $userAgent;

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $version;

    /**
     * @var bool
     */
    private $isRobot = false;

    /**
     * @var bool
     */
    private $isChromeFrame = false;

    /**
     * @var bool
     */
    private $isFacebookWebView = false;

    /**
     * @var bool
     */
    private $isCompatibilityMode = false;

    /**
     * @param null|string|UserAgent $userAgent
     *
     * @throws \Sinergi\BrowserDetector\InvalidArgumentException
     */
    public function __construct($userAgent = null)
    {
        if ($userAgent instanceof UserAgent) {
            $this->setUserAgent($userAgent);
        } elseif (null === $userAgent || is_string($userAgent)) {
            $this->setUserAgent(new UserAgent($userAgent));
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * Set the name of the OS.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    /**
     * Return the name of the Browser.
     *
     * @return string
     */
    public function getName()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return $this->name;
    }

    /**
     * Check to see if the specific browser is valid.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isBrowser($name)
    {
        return (0 == strcasecmp($this->getName(), trim($name)));
    }

    /**
     * Set the version of the browser.
     *
     * @param string $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = (string)$version;

        return $this;
    }

    /**
     * The version of the browser.
     *
     * @return string
     */
    public function getVersion()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return (string) $this->version;
    }

    /**
     * Set the Browser to be a robot.
     *
     * @param bool $isRobot
     *
     * @return $this
     */
    public function setIsRobot($isRobot)
    {
        $this->isRobot = (bool)$isRobot;

        return $this;
    }

    /**
     * Is the browser from a robot (ex Slurp,GoogleBot)?
     *
     * @return bool
     */
    public function getIsRobot()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return $this->isRobot;
    }

    /**
     * @return bool
     */
    public function isRobot()
    {
        return $this->getIsRobot();
    }

    /**
     * @param bool $isChromeFrame
     *
     * @return $this
     */
    public function setIsChromeFrame($isChromeFrame)
    {
        $this->isChromeFrame = (bool)$isChromeFrame;

        return $this;
    }

    /**
     * Used to determine if the browser is actually "chromeframe".
     *
     * @return bool
     */
    public function getIsChromeFrame()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return $this->isChromeFrame;
    }

    /**
     * @return bool
     */
    public function isChromeFrame()
    {
        return $this->getIsChromeFrame();
    }

    /**
     * @param bool $isFacebookWebView
     *
     * @return $this
     */
    public function setIsFacebookWebView($isFacebookWebView)
    {
        $this->isFacebookWebView = (bool) $isFacebookWebView;

        return $this;
    }

    /**
     * Used to determine if the browser is actually "facebook".
     *
     * @return bool
     */
    public function getIsFacebookWebView()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return $this->isFacebookWebView;
    }

    /**
     * @return bool
     */
    public function isFacebookWebView()
    {
        return $this->getIsFacebookWebView();
    }

    /**
     * @param UserAgent $userAgent
     *
     * @return $this
     */
    public function setUserAgent(UserAgent $userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return UserAgent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param bool
     *
     * @return $this
     */
    public function setIsCompatibilityMode($isCompatibilityMode)
    {
        $this->isCompatibilityMode = $isCompatibilityMode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCompatibilityMode()
    {
        return $this->isCompatibilityMode;
    }

    /**
     * Render pages outside of IE's compatibility mode.
     */
    public function endCompatibilityMode()
    {
        header('X-UA-Compatible: IE=edge');
    }
}
