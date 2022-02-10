<?php

namespace GeminiLabs\Sinergi\BrowserDetector;

class BrowserDetector implements DetectorInterface
{
    const FUNC_PREFIX = 'checkBrowser';

    protected static $userAgentString;

    /**
     * @var Browser
     */
    protected static $browser;

    protected static $browsersList = array(
        // well-known, well-used
        // Special Notes:
        // (1) Opera must be checked before FireFox due to the odd
        //     user agents used in some older versions of Opera
        // (2) WebTV is strapped onto Internet Explorer so we must
        //     check for WebTV before IE
        // (3) Because of Internet Explorer 11 using
        //     "Mozilla/5.0 ([...] Trident/7.0; rv:11.0) like Gecko"
        //     as user agent, tests for IE must be run before any
        //     tests checking for "Mozilla"
        // (4) (deprecated) Galeon is based on Firefox and needs to be
        //     tested before Firefox is tested
        // (5) OmniWeb is based on Safari so OmniWeb check must occur
        //     before Safari
        // (6) Netscape 9+ is based on Firefox so Netscape checks
        //     before FireFox are necessary
        // (7) Microsoft Edge must be checked before Chrome and Safari
        // (7) Vivaldi must be checked before Chrome
        'WebTv',
        'InternetExplorer',
        'Edge',
        'Opera',
        'Vivaldi',
        'Dragon',
        'Galeon',
        'NetscapeNavigator9Plus',
        'SeaMonkey',
        'Firefox',
        'Yandex',
        'Samsung',
        'Chrome',
        'OmniWeb',
        // common mobile
        'Android',
        'BlackBerry',
        'Nokia',
        'Gsa',
        // common bots
        'Robot',
        // wkhtmltopdf before Safari
        'Wkhtmltopdf',
        // WebKit base check (post mobile and others)
        'Safari',
        // everyone else
        'NetPositive',
        'Firebird',
        'Konqueror',
        'Icab',
        'Phoenix',
        'Amaya',
        'Lynx',
        'Shiretoko',
        'IceCat',
        'Iceweasel',
        'Mozilla', /* Mozilla is such an open standard that you must check it last */
    );

    /**
     * Routine to determine the browser type.
     *
     * @param Browser $browser
     * @param UserAgent $userAgent
     *
     * @return bool
     */
    public static function detect(Browser $browser, UserAgent $userAgent = null)
    {
        self::$browser = $browser;
        if (is_null($userAgent)) {
            $userAgent = self::$browser->getUserAgent();
        }
        self::$userAgentString = $userAgent->getUserAgentString();

        self::$browser->setName(Browser::UNKNOWN);
        self::$browser->setVersion(Browser::VERSION_UNKNOWN);

        self::checkChromeFrame();
        self::checkFacebookWebView();

        foreach (self::$browsersList as $browserName) {
            $funcName = self::FUNC_PREFIX . $browserName;

            if (self::$funcName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user is using Chrome Frame.
     *
     * @return bool
     */
    public static function checkChromeFrame()
    {
        if (strpos(self::$userAgentString, 'chromeframe') !== false) {
            self::$browser->setIsChromeFrame(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user is using Facebook.
     *
     * @return bool
     */
    public static function checkFacebookWebView()
    {
        if (strpos(self::$userAgentString, 'FBAV') !== false) {
            self::$browser->setIsFacebookWebView(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user is using a BlackBerry.
     *
     * @return bool
     */
    public static function checkBrowserBlackBerry()
    {
        if (stripos(self::$userAgentString, 'blackberry') !== false) {
            if (stripos(self::$userAgentString, 'Version/') !== false) {
                $aresult = explode('Version/', self::$userAgentString);
                if (isset($aresult[1])) {
                    $aversion = explode(' ', $aresult[1]);
                    self::$browser->setVersion($aversion[0]);
                }
            } else {
                $aresult = explode('/', stristr(self::$userAgentString, 'BlackBerry'));
                if (isset($aresult[1])) {
                    $aversion = explode(' ', $aresult[1]);
                    self::$browser->setVersion($aversion[0]);
                }
            }
            self::$browser->setName(Browser::BLACKBERRY);

            return true;
        } elseif (stripos(self::$userAgentString, 'BB10') !== false) {
            $aresult = explode('Version/10.', self::$userAgentString);
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion('10.' . $aversion[0]);
            }
            self::$browser->setName(Browser::BLACKBERRY);
            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is a robot.
     *
     * @return bool
     */
    public static function checkBrowserRobot()
    {
        if (stripos(self::$userAgentString, 'bot') !== false ||
            stripos(self::$userAgentString, 'spider') !== false ||
            stripos(self::$userAgentString, 'crawler') !== false
        ) {
            self::$browser->setIsRobot(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Internet Explorer.
     *
     * @return bool
     */
    public static function checkBrowserInternetExplorer()
    {
        // Test for v1 - v1.5 IE
        if (stripos(self::$userAgentString, 'microsoft internet explorer') !== false) {
            self::$browser->setName(Browser::IE);
            self::$browser->setVersion('1.0');
            $aresult = stristr(self::$userAgentString, '/');
            if (preg_match('/308|425|426|474|0b1/i', $aresult)) {
                self::$browser->setVersion('1.5');
            }

            return true;
        } // Test for versions > 1.5 and < 11 and some cases of 11
        else {
            if (stripos(self::$userAgentString, 'msie') !== false && stripos(self::$userAgentString, 'opera') === false
            ) {
                // See if the browser is the odd MSN Explorer
                if (stripos(self::$userAgentString, 'msnb') !== false) {
                    $aresult = explode(' ', stristr(str_replace(';', '; ', self::$userAgentString), 'MSN'));
                    self::$browser->setName(Browser::MSN);
                    if (isset($aresult[1])) {
                        self::$browser->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));
                    }

                    return true;
                }
                $aresult = explode(' ', stristr(str_replace(';', '; ', self::$userAgentString), 'msie'));
                self::$browser->setName(Browser::IE);
                if (isset($aresult[1])) {
                    self::$browser->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));
                }
                // See https://msdn.microsoft.com/en-us/library/ie/hh869301%28v=vs.85%29.aspx
                // Might be 11, anyway !
                if (stripos(self::$userAgentString, 'trident') !== false) {
                    preg_match('/rv:(\d+\.\d+)/', self::$userAgentString, $matches);
                    if (isset($matches[1])) {
                        self::$browser->setVersion($matches[1]);
                    }

                    // At this poing in the method, we know the MSIE and Trident
                    // strings are present in the $userAgentString. If we're in
                    // compatibility mode, we need to determine the true version.
                    // If the MSIE version is 7.0, we can look at the Trident
                    // version to *approximate* the true IE version. If we don't
                    // find a matching pair, ( e.g. MSIE 7.0 && Trident/7.0 )
                    // we're *not* in compatibility mode and the browser really
                    // is version 7.0.
                    if (stripos(self::$userAgentString, 'MSIE 7.0;')) {
                        if (stripos(self::$userAgentString, 'Trident/7.0;')) {
                            // IE11 in compatibility mode
                            self::$browser->setVersion('11.0');
                            self::$browser->setIsCompatibilityMode(true);
                        } elseif (stripos(self::$userAgentString, 'Trident/6.0;')) {
                            // IE10 in compatibility mode
                            self::$browser->setVersion('10.0');
                            self::$browser->setIsCompatibilityMode(true);
                        } elseif (stripos(self::$userAgentString, 'Trident/5.0;')) {
                            // IE9 in compatibility mode
                            self::$browser->setVersion('9.0');
                            self::$browser->setIsCompatibilityMode(true);
                        } elseif (stripos(self::$userAgentString, 'Trident/4.0;')) {
                            // IE8 in compatibility mode
                            self::$browser->setVersion('8.0');
                            self::$browser->setIsCompatibilityMode(true);
                        }
                    }
                }

                return true;
            } // Test for versions >= 11
            else {
                if (stripos(self::$userAgentString, 'trident') !== false) {
                    self::$browser->setName(Browser::IE);

                    preg_match('/rv:(\d+\.\d+)/', self::$userAgentString, $matches);
                    if (isset($matches[1])) {
                        self::$browser->setVersion($matches[1]);

                        return true;
                    } else {
                        return false;
                    }
                } // Test for Pocket IE
                else {
                    if (stripos(self::$userAgentString, 'mspie') !== false ||
                        stripos(
                            self::$userAgentString,
                            'pocket'
                        ) !== false
                    ) {
                        $aresult = explode(' ', stristr(self::$userAgentString, 'mspie'));
                        self::$browser->setName(Browser::POCKET_IE);

                        if (stripos(self::$userAgentString, 'mspie') !== false) {
                            if (isset($aresult[1])) {
                                self::$browser->setVersion($aresult[1]);
                            }
                        } else {
                            $aversion = explode('/', self::$userAgentString);
                            if (isset($aversion[1])) {
                                self::$browser->setVersion($aversion[1]);
                            }
                        }

                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Determine if the browser is Opera.
     *
     * @return bool
     */
    public static function checkBrowserOpera()
    {
        if (stripos(self::$userAgentString, 'opera mini') !== false) {
            $resultant = stristr(self::$userAgentString, 'opera mini');
            if (preg_match('/\//', $resultant)) {
                $aresult = explode('/', $resultant);
                if (isset($aresult[1])) {
                    $aversion = explode(' ', $aresult[1]);
                    self::$browser->setVersion($aversion[0]);
                }
            } else {
                $aversion = explode(' ', stristr($resultant, 'opera mini'));
                if (isset($aversion[1])) {
                    self::$browser->setVersion($aversion[1]);
                }
            }
            self::$browser->setName(Browser::OPERA_MINI);

            return true;
        } elseif (stripos(self::$userAgentString, 'OPiOS') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'OPiOS'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::OPERA_MINI);

            return true;
        } elseif (stripos(self::$userAgentString, 'opera') !== false) {
            $resultant = stristr(self::$userAgentString, 'opera');
            if (preg_match('/Version\/(1[0-2].*)$/', $resultant, $matches)) {
                if (isset($matches[1])) {
                    self::$browser->setVersion($matches[1]);
                }
            } elseif (preg_match('/\//', $resultant)) {
                $aresult = explode('/', str_replace('(', ' ', $resultant));
                if (isset($aresult[1])) {
                    $aversion = explode(' ', $aresult[1]);
                    self::$browser->setVersion($aversion[0]);
                }
            } else {
                $aversion = explode(' ', stristr($resultant, 'opera'));
                self::$browser->setVersion(isset($aversion[1]) ? $aversion[1] : '');
            }
            self::$browser->setName(Browser::OPERA);

            return true;
        } elseif (stripos(self::$userAgentString, ' OPR/') !== false) {
            self::$browser->setName(Browser::OPERA);
            if (preg_match('/OPR\/([\d\.]*)/', self::$userAgentString, $matches)) {
                if (isset($matches[1])) {
                    self::$browser->setVersion($matches[1]);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Samsung.
     *
     * @return bool
     */
    public static function checkBrowserSamsung()
    {
        if (stripos(self::$userAgentString, 'SamsungBrowser') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'SamsungBrowser'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::SAMSUNG_BROWSER);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Chrome.
     *
     * @return bool
     */
    public static function checkBrowserChrome()
    {
        if (stripos(self::$userAgentString, 'Chrome') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'Chrome'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::CHROME);

            return true;
        } elseif (stripos(self::$userAgentString, 'CriOS') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'CriOS'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::CHROME);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Vivaldi.
     *
     * @return bool
     */
    public static function checkBrowserVivaldi()
    {
        if (stripos(self::$userAgentString, 'Vivaldi') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'Vivaldi'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::VIVALDI);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Microsoft Edge.
     *
     * @return bool
     */
    public static function checkBrowserEdge()
    {
        if (stripos(self::$userAgentString, 'Edge') !== false) {
            $version = explode('Edge/', self::$userAgentString);
            if (isset($version[1])) {
                self::$browser->setVersion((float)$version[1]);
            }
            self::$browser->setName(Browser::EDGE);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Google Search Appliance.
     *
     * @return bool
     */
    public static function checkBrowserGsa()
    {
        if (stripos(self::$userAgentString, 'GSA') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'GSA'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::GSA);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is WebTv.
     *
     * @return bool
     */
    public static function checkBrowserWebTv()
    {
        if (stripos(self::$userAgentString, 'webtv') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'webtv'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::WEBTV);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is NetPositive.
     *
     * @return bool
     */
    public static function checkBrowserNetPositive()
    {
        if (stripos(self::$userAgentString, 'NetPositive') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'NetPositive'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion(str_replace(array('(', ')', ';'), '', $aversion[0]));
            }
            self::$browser->setName(Browser::NETPOSITIVE);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Galeon.
     *
     * @return bool
     */
    public static function checkBrowserGaleon()
    {
        if (stripos(self::$userAgentString, 'galeon') !== false) {
            $aresult = explode(' ', stristr(self::$userAgentString, 'galeon'));
            $aversion = explode('/', $aresult[0]);
            if (isset($aversion[1])) {
                self::$browser->setVersion($aversion[1]);
            }
            self::$browser->setName(Browser::GALEON);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Konqueror.
     *
     * @return bool
     */
    public static function checkBrowserKonqueror()
    {
        if (stripos(self::$userAgentString, 'Konqueror') !== false) {
            $aresult = explode(' ', stristr(self::$userAgentString, 'Konqueror'));
            $aversion = explode('/', $aresult[0]);
            if (isset($aversion[1])) {
                self::$browser->setVersion($aversion[1]);
            }
            self::$browser->setName(Browser::KONQUEROR);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is iCab.
     *
     * @return bool
     */
    public static function checkBrowserIcab()
    {
        if (stripos(self::$userAgentString, 'icab') !== false) {
            $aversion = explode(' ', stristr(str_replace('/', ' ', self::$userAgentString), 'icab'));
            if (isset($aversion[1])) {
                self::$browser->setVersion($aversion[1]);
            }
            self::$browser->setName(Browser::ICAB);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is OmniWeb.
     *
     * @return bool
     */
    public static function checkBrowserOmniWeb()
    {
        if (stripos(self::$userAgentString, 'omniweb') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'omniweb'));
            $aversion = explode(' ', isset($aresult[1]) ? $aresult[1] : '');
            self::$browser->setVersion($aversion[0]);
            self::$browser->setName(Browser::OMNIWEB);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Phoenix.
     *
     * @return bool
     */
    public static function checkBrowserPhoenix()
    {
        if (stripos(self::$userAgentString, 'Phoenix') !== false) {
            $aversion = explode('/', stristr(self::$userAgentString, 'Phoenix'));
            if (isset($aversion[1])) {
                self::$browser->setVersion($aversion[1]);
            }
            self::$browser->setName(Browser::PHOENIX);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Firebird.
     *
     * @return bool
     */
    public static function checkBrowserFirebird()
    {
        if (stripos(self::$userAgentString, 'Firebird') !== false) {
            $aversion = explode('/', stristr(self::$userAgentString, 'Firebird'));
            if (isset($aversion[1])) {
                self::$browser->setVersion($aversion[1]);
            }
            self::$browser->setName(Browser::FIREBIRD);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Netscape Navigator 9+.
     *
     * @return bool
     */
    public static function checkBrowserNetscapeNavigator9Plus()
    {
        if (stripos(self::$userAgentString, 'Firefox') !== false &&
            preg_match('/Navigator\/([^ ]*)/i', self::$userAgentString, $matches)
        ) {
            if (isset($matches[1])) {
                self::$browser->setVersion($matches[1]);
            }
            self::$browser->setName(Browser::NETSCAPE_NAVIGATOR);

            return true;
        } elseif (stripos(self::$userAgentString, 'Firefox') === false &&
            preg_match('/Netscape6?\/([^ ]*)/i', self::$userAgentString, $matches)
        ) {
            if (isset($matches[1])) {
                self::$browser->setVersion($matches[1]);
            }
            self::$browser->setName(Browser::NETSCAPE_NAVIGATOR);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Shiretoko.
     *
     * @return bool
     */
    public static function checkBrowserShiretoko()
    {
        if (stripos(self::$userAgentString, 'Mozilla') !== false &&
            preg_match('/Shiretoko\/([^ ]*)/i', self::$userAgentString, $matches)
        ) {
            if (isset($matches[1])) {
                self::$browser->setVersion($matches[1]);
            }
            self::$browser->setName(Browser::SHIRETOKO);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Ice Cat.
     *
     * @return bool
     */
    public static function checkBrowserIceCat()
    {
        if (stripos(self::$userAgentString, 'Mozilla') !== false &&
            preg_match('/IceCat\/([^ ]*)/i', self::$userAgentString, $matches)
        ) {
            if (isset($matches[1])) {
                self::$browser->setVersion($matches[1]);
            }
            self::$browser->setName(Browser::ICECAT);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Nokia.
     *
     * @return bool
     */
    public static function checkBrowserNokia()
    {
        if (preg_match("/Nokia([^\/]+)\/([^ SP]+)/i", self::$userAgentString, $matches)) {
            self::$browser->setVersion($matches[2]);
            if (stripos(self::$userAgentString, 'Series60') !== false ||
                strpos(self::$userAgentString, 'S60') !== false
            ) {
                self::$browser->setName(Browser::NOKIA_S60);
            } else {
                self::$browser->setName(Browser::NOKIA);
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Firefox.
     *
     * @return bool
     */
    public static function checkBrowserFirefox()
    {
        if (stripos(self::$userAgentString, 'safari') === false) {
            if (preg_match("/Firefox[\/ \(]([^ ;\)]+)/i", self::$userAgentString, $matches)) {
                if (isset($matches[1])) {
                    self::$browser->setVersion($matches[1]);
                }
                self::$browser->setName(Browser::FIREFOX);

                return true;
            } elseif (preg_match('/Firefox$/i', self::$userAgentString, $matches)) {
                self::$browser->setVersion('');
                self::$browser->setName(Browser::FIREFOX);

                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the browser is SeaMonkey.
     *
     * @return bool
     */
    public static function checkBrowserSeaMonkey()
    {
        if (stripos(self::$userAgentString, 'safari') === false) {
            if (preg_match("/SeaMonkey[\/ \(]([^ ;\)]+)/i", self::$userAgentString, $matches)) {
                if (isset($matches[1])) {
                    self::$browser->setVersion($matches[1]);
                }
                self::$browser->setName(Browser::SEAMONKEY);

                return true;
            } elseif (preg_match('/SeaMonkey$/i', self::$userAgentString, $matches)) {
                self::$browser->setVersion('');
                self::$browser->setName(Browser::SEAMONKEY);

                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the browser is Iceweasel.
     *
     * @return bool
     */
    public static function checkBrowserIceweasel()
    {
        if (stripos(self::$userAgentString, 'Iceweasel') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'Iceweasel'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::ICEWEASEL);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Mozilla.
     *
     * @return bool
     */
    public static function checkBrowserMozilla()
    {
        if (stripos(self::$userAgentString, 'mozilla') !== false &&
            preg_match('/rv:[0-9].[0-9][a-b]?/i', self::$userAgentString) &&
            stripos(self::$userAgentString, 'netscape') === false
        ) {
            $aversion = explode(' ', stristr(self::$userAgentString, 'rv:'));
            preg_match('/rv:[0-9].[0-9][a-b]?/i', self::$userAgentString, $aversion);
            self::$browser->setVersion(str_replace('rv:', '', $aversion[0]));
            self::$browser->setName(Browser::MOZILLA);

            return true;
        } elseif (stripos(self::$userAgentString, 'mozilla') !== false &&
            preg_match('/rv:[0-9]\.[0-9]/i', self::$userAgentString) &&
            stripos(self::$userAgentString, 'netscape') === false
        ) {
            $aversion = explode('', stristr(self::$userAgentString, 'rv:'));
            self::$browser->setVersion(str_replace('rv:', '', $aversion[0]));
            self::$browser->setName(Browser::MOZILLA);

            return true;
        } elseif (stripos(self::$userAgentString, 'mozilla') !== false &&
            preg_match('/mozilla\/([^ ]*)/i', self::$userAgentString, $matches) &&
            stripos(self::$userAgentString, 'netscape') === false
        ) {
            if (isset($matches[1])) {
                self::$browser->setVersion($matches[1]);
            }
            self::$browser->setName(Browser::MOZILLA);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Lynx.
     *
     * @return bool
     */
    public static function checkBrowserLynx()
    {
        if (stripos(self::$userAgentString, 'lynx') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'Lynx'));
            $aversion = explode(' ', (isset($aresult[1]) ? $aresult[1] : ''));
            self::$browser->setVersion($aversion[0]);
            self::$browser->setName(Browser::LYNX);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Amaya.
     *
     * @return bool
     */
    public static function checkBrowserAmaya()
    {
        if (stripos(self::$userAgentString, 'amaya') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'Amaya'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::AMAYA);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Safari.
     *
     * @return bool
     */
    public static function checkBrowserWkhtmltopdf()
    {
        if (stripos(self::$userAgentString, 'wkhtmltopdf') !== false) {
            self::$browser->setName(Browser::WKHTMLTOPDF);
            return true;
        }

        return false;
    }
    /**
     * Determine if the browser is Safari.
     *
     * @return bool
     */
    public static function checkBrowserSafari()
    {
        if (stripos(self::$userAgentString, 'Safari') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'Version'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            } else {
                self::$browser->setVersion(Browser::VERSION_UNKNOWN);
            }
            self::$browser->setName(Browser::SAFARI);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Yandex.
     *
     * @return bool
     */
    public static function checkBrowserYandex()
    {
        if (stripos(self::$userAgentString, 'YaBrowser') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'YaBrowser'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::YANDEX);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Comodo Dragon / Ice Dragon / Chromodo.
     *
     * @return bool
     */
    public static function checkBrowserDragon()
    {
        if (stripos(self::$userAgentString, 'Dragon') !== false) {
            $aresult = explode('/', stristr(self::$userAgentString, 'Dragon'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                self::$browser->setVersion($aversion[0]);
            }
            self::$browser->setName(Browser::DRAGON);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Android.
     *
     * @return bool
     */
    public static function checkBrowserAndroid()
    {
        // Navigator
        if (stripos(self::$userAgentString, 'Android') !== false) {
            if (preg_match('/Version\/([\d\.]*)/i', self::$userAgentString, $matches)) {
                if (isset($matches[1])) {
                    self::$browser->setVersion($matches[1]);
                }
            } else {
                self::$browser->setVersion(Browser::VERSION_UNKNOWN);
            }
            self::$browser->setName(Browser::NAVIGATOR);

            return true;
        }

        return false;
    }
}
