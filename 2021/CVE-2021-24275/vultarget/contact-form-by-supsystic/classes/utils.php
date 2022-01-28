<?php
class utilsCfs {
    static public function jsonEncode($arr) {
        return (is_array($arr) || is_object($arr)) ? json_encode_utf_normal($arr) : json_encode_utf_normal(array());
    }
    static public function jsonDecode($str) {
        if(is_array($str))
            return $str;
        if(is_object($str))
            return (array)$str;
        return empty($str) ? array() : json_decode($str, true);
    }
    static public function unserialize($data) {
        return unserialize($data);
    }
    static public function serialize($data) {
        return serialize($data);
    }
    static public function createDir($path, $params = array('chmod' => NULL, 'httpProtect' => false)) {
        if(@mkdir($path)) {
            if(!is_null($params['chmod'])) {
                @chmod($path, $params['chmod']);
            }
            if(!empty($params['httpProtect'])) {
                self::httpProtectDir($path);
            }
            return true;
        }
        return false;
    }
    static public function httpProtectDir($path) {
        $content = 'DENY FROM ALL';
        if(strrpos($path, DS) != strlen($path))
            $path .= DS;
        if(file_put_contents($path. '.htaccess', $content)) {
            return true;
        }
        return false;
    }
    /**
     * Copy all files from one directory ($source) to another ($destination)
     * @param string $source path to source directory
     * @params string $destination path to destination directory
     */
    static public function copyDirectories($source, $destination) {
        if(is_dir($source)) {
            @mkdir($destination);
            $directory = dir($source);
            while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
                if ( $readdirectory == '.' || $readdirectory == '..' ) {
                    continue;
                }
                $PathDir = $source . '/' . $readdirectory; 
                if (is_dir($PathDir)) {
                    utilsCfs::copyDirectories( $PathDir, $destination . '/' . $readdirectory );
                    continue;
                }
                copy( $PathDir, $destination . '/' . $readdirectory );
            }
            $directory->close();
        } else {
            copy( $source, $destination );
        }
    }
    static public function getIP() {
		$res = '';
		if(!isset($_SERVER['HTTP_CLIENT_IP']) || empty($_SERVER['HTTP_CLIENT_IP'])) {
			if(!isset($_SERVER['HTTP_X_REAL_IP']) || empty($_SERVER['HTTP_X_REAL_IP'])) {
				if(!isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']) || empty($_SERVER['HTTP_X_SUCURI_CLIENTIP'])) {
					if(!isset($_SERVER['HTTP_X_FORWARDED_FOR']) || empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
						$res = $_SERVER['REMOTE_ADDR'];
					} else
						$res = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else
					$res = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
			} else
				$res = $_SERVER['HTTP_X_REAL_IP'];
		} else
			$res = $_SERVER['HTTP_CLIENT_IP'];
		
		return $res;
        //return (empty($_SERVER['HTTP_CLIENT_IP']) ? (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']) : $_SERVER['HTTP_CLIENT_IP']);
    }
    
    /**
     * Parse xml file into simpleXML object
     * @param string $path path to xml file
     * @return mixed object SimpleXMLElement if success, else - false
     */
    static public function getXml($path) {
        if(is_file($path)) {
            return simplexml_load_file($path);
        }
        return false;
    }
    /**
     * Check if the element exists in array
     * @param array $param 
     */
    static public function xmlAttrToStr($param, $element) {
        if (isset($param[$element])) {
            // convert object element to string
            return (string)$param[$element];
        } else {
            return '';
        }
    }
    static public function xmlNodeAttrsToArr($node) {
        $arr = array();
        foreach($node->attributes() as $a => $b) {
            $arr[$a] = utilsCfs::xmlAttrToStr($node, $a);
        }
        return $arr;
    }
    static public function deleteFile($str) {
        return @unlink($str);
    }
    static public function deleteDir($str){
        if(is_file($str)){
            return self::deleteFile($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                utilsCfs::deleteDir($path);
            }
            return @rmdir($str);
        }
    }
    /**
     * Retrives list of directories ()
     */
    static public function getDirList($path) {
        $res = array();
        if(is_dir($path)){
            $files = scandir($path);
            foreach($files as $f) {
                if($f == '.' || $f == '..' || $f == '.svn') continue;
                if(!is_dir($path. $f))                      continue;
                $res[$f] = array('path' => $path. $f. DS);
            }
        }
        return $res;
    }
    /**
     * Retrives list of files
     */
    static public function getFilesList($path) {
        $files = array();
        if(is_dir($path)){
            $dirHandle = opendir($path);
            while(($file = readdir($dirHandle)) !== false) {
                if($file != '.' && $file != '..' && $f != '.svn' && is_file($path. DS. $file)) {
                    $files[] = $file;
                }
            }
        }
        return $files;
    }
    /**
     * Check if $var is object or something another in future
     */
    static public function is($var, $what = '') {
        if (!is_object($var)) {
            return false;
        }
        if(get_class($var) == $what) {
            return true;
        }
        return false;
    }
    /**
     * Get array with all monthes of year, uses in paypal pro and sagepay payment modules for now, than - who knows)
     * @return array monthes
     */
    static public function getMonthesArray() {
        static $monthsArray = array();
        //Some cache
        if(!empty($monthsArray))
            return $monthsArray;
        for ($i=1; $i<13; $i++) {
            $monthsArray[sprintf('%02d', $i)] = strftime('%B', mktime(0,0,0,$i,1,2000));
        }
        return $monthsArray;
    }
    /**
     * Get an array with years range from current year
     * @param int $from - how many years from today ago
     * @param int $to - how many years in future
     * @param $formatKey - format for keys in array, @see strftime
     * @param $formatVal - format for values in array, @see strftime
     * @return array - years 
     */
    static public function getYearsArray($from, $to, $formatKey = '%Y', $formatVal = '%Y') {
        $today = getdate();
        $yearsArray = array();
        for ($i=$today['year']-$from; $i <= $today['year']+$to; $i++) {
            $yearsArray[strftime($formatKey,mktime(0,0,0,1,1,$i))] = strftime($formatVal,mktime(0,0,0,1,1,$i));
        }
        return $yearsArray;
    }
    /**
     * Make replacement in $text, where it will be find all keys with prefix ":" and replace it with corresponding value
     * @see email_templatesModel::renderContent()
     * @see checkoutView::getSuccessPage()
     */
    static public function makeVariablesReplacement($text, $variables) {
        if(!empty($text) && !empty($variables) && is_array($variables)) {
            foreach($variables as $k => $v) {
                $text = str_replace(':'. $k, $v, $text);
            }
            return $text;
        }
        return false;
    }
    /**
     * Retrive full directory of plugin
     * @param string $name - plugin name
     * @return string full path in file system to plugin directory
     */
    static public function getPluginDir($name = '') {
        return WP_PLUGIN_DIR. DS. $name. DS;
    }
    static public function getPluginPath($name = '') {
        return WP_PLUGIN_URL. '/'. $name. '/';
    }
    static public function getExtModDir($plugName) {
		return self::getPluginDir($plugName);
    }
    static public function getExtModPath($plugName) {
		return self::getPluginPath($plugName);
    }
    static public function getCurrentWPThemePath() {
        return get_template_directory_uri();
    }
    static public function isThisCommercialEdition() {
        /*$commercialModules = array('rating');
        foreach($commercialModules as $m) {
            if(!frameCfs::_()->getModule($m)) 
                return false;
            if(!is_dir(frameCfs::_()->getModule($m)->getModDir())) 
                return false;
        }
        return true;*/
        foreach(frameCfs::_()->getModules() as $m) {
            if(is_object($m) && $m->isExternal()) // Should be at least one external module
                return true;
        }
        return false;
    }
    static public function checkNum($val, $default = 0) {
        if(!empty($val) && is_numeric($val))
            return $val;
        return $default;
    }
    static public function checkString($val, $default = '') {
        if(!empty($val) && is_string($val))
            return $val;
        return $default;
    }
    /**
     * Retrives extension of file
     * @param string $path - path to a file
     * @return string - file extension
     */
    static public function getFileExt($path) {
        return strtolower( pathinfo($path, PATHINFO_EXTENSION) );
    }
    static public function getRandStr($length = 10, $allowedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890', $params = array()) {
        $result = '';
        $allowedCharsLen = strlen($allowedChars);
		if(isset($params['only_lowercase']) && $params['only_lowercase']) {
			$allowedChars = strtolower($allowedChars);
		}
        while(strlen($result) < $length) {
          $result .= substr($allowedChars, rand(0, $allowedCharsLen), 1);
        }

        return $result;
    }
    /**
     * Get current host location
     * @return string host string
     */
    static public function getHost() {
        return $_SERVER['HTTP_HOST'];
    }
    /**
     * Check if device is mobile
     * @return bool true if user are watching this site from mobile device
     */
    static public function isMobile() {
        return mobileDetect::_()->isMobile();
    }
    /**
     * Check if device is tablet
     * @return bool true if user are watching this site from tablet device
     */
    static public function isTablet() {
        return mobileDetect::_()->isTablet();
    }
    static public function getUploadsDir() {
        $uploadDir = wp_upload_dir();
        return $uploadDir['basedir'];
    }
    static public function getUploadsPath() {
        $uploadDir = wp_upload_dir();
        return $uploadDir['baseurl'];
    }
    static public function arrToCss($data) {
        $res = '';
        if(!empty($data)) {
            foreach($data as $k => $v) {
                $res .= $k. ':'. $v. ';';
            }
        }
        return $res;
    }
    /**
     * Activate all CSP Plugins
     * 
     * @return NULL Check if it's site or multisite and activate.
     */
    static public function activatePlugin() {
        global $wpdb;
		if(CFS_TEST_MODE) {
			add_action('activated_plugin', array(frameCfs::_(), 'savePluginActivationErrors'));
		}
        if (function_exists('is_multisite') && is_multisite()) {
			// $orig_id = $wpdb->blogid;
            $blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_id as $id) {
                if (switch_to_blog($id)) {
                    installerCfs::init();
					restore_current_blog();
                } 
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerCfs::init();
        }
    }

    /**
     * Delete All CSP Plugins
     * 
     * @return NULL Check if it's site or multisite and decativate it.
     */
    static public function deletePlugin() {
        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
			// $orig_id = $wpdb->blogid;
            $blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_id as $id) {
                if (switch_to_blog($id)) {
                    installerCfs::delete();
					restore_current_blog();
                } 
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerCfs::delete();
        }
    }
	static public function deactivatePlugin() {
		global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
			// $orig_id = $wpdb->blogid;
            $blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_id as $id) {
                if (switch_to_blog($id)) {
                    installerCfs::deactivate();
					restore_current_blog();
                } 
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerCfs::deactivate();
        }
	}
	static public function isWritable($filename) {
		return is_writable($filename);
	}
	
	static public function isReadable($filename) {
		return is_readable($filename);
	}
	
	static public function fileExists($filename) {
		return file_exists($filename);
	}
	static public function isPluginsPage() {
		return (basename(reqCfs::getVar('SCRIPT_NAME', 'server')) === 'plugins.php');
	}
	static public function isSessionStarted() {
		if(version_compare(PHP_VERSION, '5.4.0') >= 0 && function_exists('session_status')) {
			return !(session_status() == PHP_SESSION_NONE);
		} else {
			return !(session_id() == '');
		}
	}
	static public function generateBgStyle($data) {
		$stageBgStyles = array();
		$stageBgStyle = '';
		switch($data['type']) {
			case 'color':
				$stageBgStyles[] = 'background-color: '. $data['color'];
				$stageBgStyles[] = 'opacity: '. $data['opacity'];
				break;
			case 'img':
				$stageBgStyles[] = 'background-image: url('. $data['img']. ')';
				switch($data['img_pos']) {
					case 'center':
						$stageBgStyles[] = 'background-repeat: no-repeat';
						$stageBgStyles[] = 'background-position: center center';
						break;
					case 'tile':
						$stageBgStyles[] = 'background-repeat: repeat';
						break;
					case 'stretch':
						$stageBgStyles[] = 'background-repeat: no-repeat';
						$stageBgStyles[] = '-moz-background-size: 100% 100%';
						$stageBgStyles[] = '-webkit-background-size: 100% 100%';
						$stageBgStyles[] = '-o-background-size: 100% 100%';
						$stageBgStyles[] = 'background-size: 100% 100%';
						break;
				}
				break;
		}
		if(!empty($stageBgStyles)) {
			$stageBgStyle = implode(';', $stageBgStyles);
		}
		return $stageBgStyle;
	}
	/**
	 * Parse worcfsess post/page/custom post type content for images and return it's IDs if there are images
	 * @param string $content Post/page/custom post type content
	 * @return array List of images IDs from content
	 */
	static public function parseImgIds($content) {
		$res = array();
		preg_match_all('/wp-image-(?<ID>\d+)/', $content, $matches);
		if($matches && isset($matches['ID']) && !empty($matches['ID'])) {
			$res = $matches['ID'];
		}
		return $res;
	}
	/**
	 * Retrive file path in file system from provided URL, it should be in wp-content/uploads
	 * @param string $url File url path, should be in wp-content/uploads
	 * @return string Path in file system to file
	 */
	static public function getUploadFilePathFromUrl($url) {
		$uploadsPath = self::getUploadsPath();
		$uploadsDir = self::getUploadsDir();
		return str_replace($uploadsPath, $uploadsDir, $url);
	}
	/**
	 * Retrive file URL from provided file system path, it should be in wp-content/uploads
	 * @param string $path File path, should be in wp-content/uploads
	 * @return string URL to file
	 */
	static public function getUploadUrlFromFilePath($path) {
		$uploadsPath = self::getUploadsPath();
		$uploadsDir = self::getUploadsDir();
		return str_replace($uploadsDir, $uploadsPath, $path);
	}
	static public function getUserBrowserString() {
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : false;
	}
	static public function getBrowser() {
		$u_agent = self::getUserBrowserString();
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version = '';
		$pattern = '';
		
		if($u_agent) {
			//First get the platform?
			if (preg_match('/linux/i', $u_agent)) {
				$platform = 'linux';
			}
			elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
				$platform = 'mac';
			}
			elseif (preg_match('/windows|win32/i', $u_agent)) {
				$platform = 'windows';
			}
			// Next get the name of the useragent yes seperately and for good reason
			if((preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) || (strpos($u_agent, 'Trident/7.0; rv:11.0') !== false))
			{
				$bname = 'Internet Explorer';
				$ub = "MSIE";
			}
			elseif(preg_match('/Firefox/i',$u_agent))
			{
				$bname = 'Mozilla Firefox';
				$ub = "Firefox";
			}
			elseif(preg_match('/Chrome/i',$u_agent))
			{
				$bname = 'Google Chrome';
				$ub = "Chrome";
			}
			elseif(preg_match('/Safari/i',$u_agent))
			{
				$bname = 'Apple Safari';
				$ub = "Safari";
			}
			elseif(preg_match('/Opera/i',$u_agent))
			{
				$bname = 'Opera';
				$ub = "Opera";
			}
			elseif(preg_match('/Netscape/i',$u_agent))
			{
				$bname = 'Netscape';
				$ub = "Netscape";
			}

			// finally get the correct version number
			$known = array('Version', $ub, 'other');
			$pattern = '#(?<browser>' . join('|', $known) .
			')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			if (!preg_match_all($pattern, $u_agent, $matches)) {
				// we have no matching number just continue
			}

			// see how many we have
			$i = count($matches['browser']);
			if ($i != 1) {
				//we will have two since we are not using 'other' argument yet
				//see if version is before or after the name
				if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
					$version= $matches['version'][0];
				}
				else {
					$version= $matches['version'][1];
				}
			}
			else {
				$version= $matches['version'][0];
			}
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
		);
	}
	static public function getBrowsersList() {
		return array(
			'Unknown', 'Internet Explorer', 'Mozilla Firefox', 'Google Chrome', 'Apple Safari', 
			'Opera', 'Netscape',
		);
	}
	static public function getLangCode2Letter() {
		$langCode = self::getLangCode();
		return strlen($langCode) > 2 ? substr($langCode, 0, 2) : $langCode;
	}
	static public function getLangCode() {
		return get_locale();
	}
	static public function getBrowserLangCode() {
		return isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])
			? strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2))
			: self::getLangCode2Letter();
	}
	static public function getTimeRange() {
		$time = array();
		$hours = range(1, 11);
		array_unshift($hours, 12);
		$k = 0;
		$count = count($hours);
		for($i = 0; $i < 4 * $count; $i++) {
			$newItem = $hours[ $k ];
			$newItem .= ':'. (($i % 2) ? '30' : '00');
			$newItem .= ($i < $count * 2) ? 'am' : 'pm';
			if($i % 2)
				$k++;
			if($i == $count * 2 - 1)
				$k = 0;
			$time[] = $newItem;
		}
		return array_combine($time, $time);
	}
	static public function getSearchEnginesList() {
		return array(
			'google.com' => array('label' => 'Google'),
			'yahoo.com' => array('label' => 'Yahoo!'),
			'youdao.com' => array('label' => 'Youdao'),
			'yandex' => array('label' => 'Yandex'),
			'sogou.com' => array('label' => 'Sogou'),
			'qwant.com' => array('label' => 'Qwant'),
			'bing.com' => array('label' => 'Bing'),
			'munax.com' => array('label' => 'Munax'),
		);
	}
	static public function getSocialList() {
		return array(
			'facebook.com' => array('label' => 'Facebook'),
			'pinterest.com' => array('label' => 'Pinterest'),
			'instagram.com' => array('label' => 'Instagram'),
			'yelp.com' => array('label' => 'Yelp'),
			'vk.com' => array('label' => 'VKontakte'),
			'myspace.com' => array('label' => 'Myspace'),
			'linkedin.com' => array('label' => 'LinkedIn'),
			'plus.google.com' => array('label' => 'Google+'),
			'google.com' => array('label' => 'Google'),
		);
	}
	static public function getReferalUrl() {
		// Simple for now
		return reqCfs::getVar('HTTP_REFERER', 'server');
	}
	static public function getReferalHost() {
		$refUrl = self::getReferalUrl();
		if(!empty($refUrl)) {
			$refer = parse_url( $refUrl );
			if($refer && isset($refer['host']) && !empty($refer['host'])) {
				return $refer['host'];
			}
		}
		return false;
	}
	static public function rgbToArray($rgb) {
		$rgb = array_map('trim', 
				explode(',', 
					trim(str_replace(array('rgb', 'a', '(', ')'), '', $rgb))));
		return $rgb;
	}
	static public function rgbToHex($rgb) {
		if(is_string($rgb)) {
			$rgb = self::rgbToArray($rgb);
		}
		$hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

		return $hex;
	}
	static public function hexToRgb($hex) {
		if(strpos($hex, 'rgb') !== false) {	// Maybe it's already in rgb format - just return it as array
			return self::rgbToArray($hex);
		}
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1). substr($hex,0,1));
			$g = hexdec(substr($hex,1,1). substr($hex,1,1));
			$b = hexdec(substr($hex,2,1). substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		//return implode(",", $rgb); // returns the rgb values separated by commas
		return $rgb; // returns an array with the rgb values
	}
	static public function replaceVariables($str, $variables) {
		foreach($variables as $k => $v) {
			$str = str_replace('['. $k. ']', $v, $str);
		}
		return $str;
	}
	static public function encodeArrayTxt( $arr, $forceOrd = false ) {
		$arr = self::serialize($arr);
		if(function_exists('base64_encode') && !$forceOrd) {
			return base64_encode( $arr );
		}
		$len = strlen( $arr );
		$res = array();
		for($i = 0; $i < $len; $i++) {
			$res[] = ord( $arr[ $i ] );
		}
		return implode('|', $res). ':ORD_ENC';
	}
	static public function decodeArrayTxt( $str ) {
		$resStr = '';
		if(strpos($str, ':ORD_ENC')) {
			$str = explode('|', str_replace(':ORD_ENC', '', $str));
			foreach($str as $ord) {
				$resStr .= chr( $ord );
			}
		} elseif(function_exists('base64_decode')) {
			$resStr = base64_decode( $str );
		}
		return self::unserialize($resStr);
	}
	static public function getLanguages() {
		$allLanguages = array();
		if(!function_exists('wp_get_available_translations') && file_exists(ABSPATH . 'wp-admin/includes/translation-install.php')) {
			require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
		}
		if(function_exists('wp_get_available_translations')) {	// As it was included only from version 4.0.0
			$allLanguages = wp_get_available_translations();
		}
		return $allLanguages;
	}
	static public function getLanguagesForSelect() {
		$languagesForSelect = array();
		$allLanguages = self::getLanguages();
		if($allLanguages) {
			foreach($allLanguages as $l) {
				if(!isset($l['iso']) || !isset($l['iso'][1])) {
					$isoCode = $l['language'];
				} else {
					$isoCode = $l['iso'][1];
				}
				if(isset($languagesForSelect[ $isoCode ])) {
					$isoCode = isset($l['iso'][2]) ? $l['iso'][2] : ( isset($l['iso'][3]) ? $l['iso'][3] : $l['language'] );
				}
				if(isset( $languagesForSelect[ $isoCode ]) ) {
					$isoCode = $l['language'];
				}
				$languagesForSelect[ $isoCode ] = $l['native_name'];
			}
		}
		return $languagesForSelect;
	}
	static public function hexToRgbaStr($hex, $alpha = 1) {
		$rgbArr = self::hexToRgb($hex);
		return "rgba(" . implode(',', $rgbArr) . ',' . $alpha . ")";
	}
	static public function getFaIconsList() {
		return array('fa-adjust','fa-adn','fa-align-center','fa-align-justify','fa-align-left','fa-align-right','fa-ambulance','fa-anchor','fa-android','fa-angellist','fa-angle-double-down','fa-angle-double-left','fa-angle-double-right','fa-angle-double-up','fa-angle-down','fa-angle-left','fa-angle-right','fa-angle-up','fa-apple','fa-archive','fa-area-chart','fa-arrow-circle-down','fa-arrow-circle-left','fa-arrow-circle-o-down','fa-arrow-circle-o-left','fa-arrow-circle-o-right','fa-arrow-circle-o-up','fa-arrow-circle-right','fa-arrow-circle-up','fa-arrow-down','fa-arrow-left','fa-arrow-right','fa-arrow-up','fa-arrows','fa-arrows-alt','fa-arrows-h','fa-arrows-v','fa-asterisk','fa-at','fa-automobile(alias)','fa-backward','fa-ban','fa-bank(alias)','fa-bar-chart','fa-bar-chart-o(alias)','fa-barcode','fa-bars','fa-bed','fa-beer','fa-behance','fa-behance-square','fa-bell','fa-bell-o','fa-bell-slash','fa-bell-slash-o','fa-bicycle','fa-binoculars','fa-birthday-cake','fa-bitbucket','fa-bitbucket-square','fa-bitcoin(alias)','fa-bold','fa-bolt','fa-bomb','fa-book','fa-bookmark','fa-bookmark-o','fa-briefcase','fa-btc','fa-bug','fa-building','fa-building-o','fa-bullhorn','fa-bullseye','fa-bus','fa-buysellads','fa-cab(alias)','fa-calculator','fa-calendar','fa-calendar-o','fa-camera','fa-camera-retro','fa-car','fa-caret-down','fa-caret-left','fa-caret-right','fa-caret-square-o-down','fa-caret-square-o-left','fa-caret-square-o-right','fa-caret-square-o-up','fa-caret-up','fa-cart-arrow-down','fa-cart-plus','fa-cc','fa-cc-amex','fa-cc-discover','fa-cc-mastercard','fa-cc-paypal','fa-cc-stripe','fa-cc-visa','fa-certificate','fa-chain(alias)','fa-chain-broken','fa-check','fa-check-circle','fa-check-circle-o','fa-check-square','fa-check-square-o','fa-chevron-circle-down','fa-chevron-circle-left','fa-chevron-circle-right','fa-chevron-circle-up','fa-chevron-down','fa-chevron-left','fa-chevron-right','fa-chevron-up','fa-child','fa-circle','fa-circle-o','fa-circle-o-notch','fa-circle-thin','fa-clipboard','fa-clock-o','fa-close(alias)','fa-cloud','fa-cloud-download','fa-cloud-upload','fa-cny(alias)','fa-code','fa-code-fork','fa-codepen','fa-coffee','fa-cog','fa-cogs','fa-columns','fa-comment','fa-comment-o','fa-comments','fa-comments-o','fa-compass','fa-compress','fa-connectdevelop','fa-copy(alias)','fa-copyright','fa-credit-card','fa-crop','fa-crosshairs','fa-css3','fa-cube','fa-cubes','fa-cut(alias)','fa-cutlery','fa-dashboard(alias)','fa-dashcube','fa-database','fa-dedent(alias)','fa-delicious','fa-desktop','fa-deviantart','fa-diamond','fa-digg','fa-dollar(alias)','fa-dot-circle-o','fa-download','fa-dribbble','fa-dropbox','fa-drupal','fa-edit(alias)','fa-eject','fa-ellipsis-h','fa-ellipsis-v','fa-empire','fa-envelope','fa-envelope-o','fa-envelope-square','fa-eraser','fa-eur','fa-euro(alias)','fa-exchange','fa-exclamation','fa-exclamation-circle','fa-exclamation-triangle','fa-expand','fa-external-link','fa-external-link-square','fa-eye','fa-eye-slash','fa-eyedropper','fa-facebook','fa-facebook-f(alias)','fa-facebook-official','fa-facebook-square','fa-fast-backward','fa-fast-forward','fa-fax','fa-female','fa-fighter-jet','fa-file','fa-file-archive-o','fa-file-audio-o','fa-file-code-o','fa-file-excel-o','fa-file-image-o','fa-file-movie-o(alias)','fa-file-o','fa-file-pdf-o','fa-file-photo-o(alias)','fa-file-picture-o(alias)','fa-file-powerpoint-o','fa-file-sound-o(alias)','fa-file-text','fa-file-text-o','fa-file-video-o','fa-file-word-o','fa-file-zip-o(alias)','fa-files-o','fa-film','fa-filter','fa-fire','fa-fire-extinguisher','fa-flag','fa-flag-checkered','fa-flag-o','fa-flash(alias)','fa-flask','fa-flickr','fa-floppy-o','fa-folder','fa-folder-o','fa-folder-open','fa-folder-open-o','fa-font','fa-forumbee','fa-forward','fa-foursquare','fa-frown-o','fa-futbol-o','fa-gamepad','fa-gavel','fa-gbp','fa-ge(alias)','fa-gear(alias)','fa-gears(alias)','fa-genderless(alias)','fa-gift','fa-git','fa-git-square','fa-github','fa-github-alt','fa-github-square','fa-gittip(alias)','fa-glass','fa-globe','fa-google','fa-google-plus','fa-google-plus-square','fa-google-wallet','fa-graduation-cap','fa-gratipay','fa-group(alias)','fa-h-square','fa-hacker-news','fa-hand-o-down','fa-hand-o-left','fa-hand-o-right','fa-hand-o-up','fa-hdd-o','fa-header','fa-headphones','fa-heart','fa-heart-o','fa-heartbeat','fa-history','fa-home','fa-hospital-o','fa-hotel(alias)','fa-html5','fa-ils','fa-image(alias)','fa-inbox','fa-indent','fa-info','fa-info-circle','fa-inr','fa-instagram','fa-institution(alias)','fa-ioxhost','fa-italic','fa-joomla','fa-jpy','fa-jsfiddle','fa-key','fa-keyboard-o','fa-krw','fa-language','fa-laptop','fa-lastfm','fa-lastfm-square','fa-leaf','fa-leanpub','fa-legal(alias)','fa-lemon-o','fa-level-down','fa-level-up','fa-life-bouy(alias)','fa-life-buoy(alias)','fa-life-ring','fa-life-saver(alias)','fa-lightbulb-o','fa-line-chart','fa-link','fa-linkedin','fa-linkedin-square','fa-linux','fa-list','fa-list-alt','fa-list-ol','fa-list-ul','fa-location-arrow','fa-lock','fa-long-arrow-down','fa-long-arrow-left','fa-long-arrow-right','fa-long-arrow-up','fa-magic','fa-magnet','fa-mail-forward(alias)','fa-mail-reply(alias)','fa-mail-reply-all(alias)','fa-male','fa-map-marker','fa-mars','fa-mars-double','fa-mars-stroke','fa-mars-stroke-h','fa-mars-stroke-v','fa-maxcdn','fa-meanpath','fa-medium','fa-medkit','fa-meh-o','fa-mercury','fa-microphone','fa-microphone-slash','fa-minus','fa-minus-circle','fa-minus-square','fa-minus-square-o','fa-mobile','fa-mobile-phone(alias)','fa-money','fa-moon-o','fa-mortar-board(alias)','fa-motorcycle','fa-music','fa-navicon(alias)','fa-neuter','fa-newspaper-o','fa-openid','fa-outdent','fa-pagelines','fa-paint-brush','fa-paper-plane','fa-paper-plane-o','fa-paperclip','fa-paragraph','fa-paste(alias)','fa-pause','fa-paw','fa-paypal','fa-pencil','fa-pencil-square','fa-pencil-square-o','fa-phone','fa-phone-square','fa-photo(alias)','fa-picture-o','fa-pie-chart','fa-pied-piper','fa-pied-piper-alt','fa-pinterest','fa-pinterest-p','fa-pinterest-square','fa-plane','fa-play','fa-play-circle','fa-play-circle-o','fa-plug','fa-plus','fa-plus-circle','fa-plus-square','fa-plus-square-o','fa-power-off','fa-print','fa-puzzle-piece','fa-qq','fa-qrcode','fa-question','fa-question-circle','fa-quote-left','fa-quote-right','fa-ra(alias)','fa-random','fa-rebel','fa-recycle','fa-reddit','fa-reddit-square','fa-refresh','fa-remove(alias)','fa-renren','fa-reorder(alias)','fa-repeat','fa-reply','fa-reply-all','fa-retweet','fa-rmb(alias)','fa-road','fa-rocket','fa-rotate-left(alias)','fa-rotate-right(alias)','fa-rouble(alias)','fa-rss','fa-rss-square','fa-rub','fa-ruble(alias)','fa-rupee(alias)','fa-save(alias)','fa-scissors','fa-search','fa-search-minus','fa-search-plus','fa-sellsy','fa-send(alias)','fa-send-o(alias)','fa-server','fa-share','fa-share-alt','fa-share-alt-square','fa-share-square','fa-share-square-o','fa-shekel(alias)','fa-sheqel(alias)','fa-shield','fa-ship','fa-shirtsinbulk','fa-shopping-cart','fa-sign-in','fa-sign-out','fa-signal','fa-simplybuilt','fa-sitemap','fa-skyatlas','fa-skype','fa-slack','fa-sliders','fa-slideshare','fa-smile-o','fa-soccer-ball-o(alias)','fa-sort','fa-sort-alpha-asc','fa-sort-alpha-desc','fa-sort-amount-asc','fa-sort-amount-desc','fa-sort-asc','fa-sort-desc','fa-sort-down(alias)','fa-sort-numeric-asc','fa-sort-numeric-desc','fa-sort-up(alias)','fa-soundcloud','fa-space-shuttle','fa-spinner','fa-spoon','fa-spotify','fa-square','fa-square-o','fa-stack-exchange','fa-stack-overflow','fa-star','fa-star-half','fa-star-half-empty(alias)','fa-star-half-full(alias)','fa-star-half-o','fa-star-o','fa-steam','fa-steam-square','fa-step-backward','fa-step-forward','fa-stethoscope','fa-stop','fa-street-view','fa-strikethrough','fa-stumbleupon','fa-stumbleupon-circle','fa-subscript','fa-subway','fa-suitcase','fa-sun-o','fa-superscript','fa-support(alias)','fa-table','fa-tablet','fa-tachometer','fa-tag','fa-tags','fa-tasks','fa-taxi','fa-tencent-weibo','fa-terminal','fa-text-height','fa-text-width','fa-th','fa-th-large','fa-th-list','fa-thumb-tack','fa-thumbs-down','fa-thumbs-o-down','fa-thumbs-o-up','fa-thumbs-up','fa-ticket','fa-times','fa-times-circle','fa-times-circle-o','fa-tint','fa-toggle-down(alias)','fa-toggle-left(alias)','fa-toggle-off','fa-toggle-on','fa-toggle-right(alias)','fa-toggle-up(alias)','fa-train','fa-transgender','fa-transgender-alt','fa-trash','fa-trash-o','fa-tree','fa-trello','fa-trophy','fa-truck','fa-try','fa-tty','fa-tumblr','fa-tumblr-square','fa-turkish-lira(alias)','fa-twitch','fa-twitter','fa-twitter-square','fa-umbrella','fa-underline','fa-undo','fa-university','fa-unlink(alias)','fa-unlock','fa-unlock-alt','fa-unsorted(alias)','fa-upload','fa-usd','fa-user','fa-user-md','fa-user-plus','fa-user-secret','fa-user-times','fa-users','fa-venus','fa-venus-double','fa-venus-mars','fa-viacoin','fa-video-camera','fa-vimeo-square','fa-vine','fa-vk','fa-volume-down','fa-volume-off','fa-volume-up','fa-warning(alias)','fa-wechat(alias)','fa-weibo','fa-weixin','fa-whatsapp','fa-wheelchair','fa-wifi','fa-windows','fa-won(alias)','fa-wordpress','fa-wrench','fa-xing','fa-xing-square','fa-yahoo','fa-yelp','fa-yen(alias)','fa-youtube','fa-youtube-play','fa-youtube-square');
	}
	static public function toAdminEmail($email) {
		if($email == 'admin@mail.com') {	// This was email from our test server
			static $adminEmail;
			if(empty($adminEmail)) {
				$adminEmail = get_bloginfo('admin_email');
			}
			$email = $adminEmail;
		}
		return $email;
	}
}
