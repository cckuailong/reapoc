<?php
importClassUms('mobileDetectUms');

class utilsUms {
	static public $isMobile = null;

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
        $data = preg_replace_callback ( '!s:(\d+):"(.*?)";!',
            function($match) {
                return ($match[1] == strlen($match[2])) ? $match[0] : 's:'. strlen($match[2]). ':"'. $match[2]. '";';
            },
            $data );
				if (is_array($data)) {
					return $data;
				}
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
                    utilsUms::copyDirectories( $PathDir, $destination . '/' . $readdirectory );
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
            $arr[$a] = utilsUms::xmlAttrToStr($node, $a);
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
                utilsUms::deleteDir($path);
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
    static public function getCurrentWPThemeDir() {
        static $themePath;
        if(empty($themePath)) {
            $themePath = get_theme_root(). DS. utilsUms::getCurrentWPThemeCode(). DS;
        }
        return $themePath;
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
        if(strpos($plugName, 'wp-content/themes') !== false) {  //for modules in theme dir
            return ABSPATH. $plugName. DS;
        } else
            return self::getPluginDir($plugName);
    }
    static public function getExtModPath($plugName) {
        if(strpos($plugName, 'wp-content/themes') !== false) {  //for modules in theme dir
            return UMS_SITE_URL. str_replace(DS, '/', $plugName). '/';
        } else
            return self::getPluginPath($plugName);
    }
    static public function getCurrentWPThemePath() {
        return get_template_directory_uri();
    }
    static public function getCurrentWPThemeCode() {
        static $activeThemeName;
        if(empty($activeThemeName)) {
					$activeThemeName = wp_get_theme()->get('Name');
        }
        return $activeThemeName;
    }
    static public function isThisCommercialEdition() {
        /*$commercialModules = array('rating');
        foreach($commercialModules as $m) {
            if(!frameUms::_()->getModule($m))
                return false;
            if(!is_dir(frameUms::_()->getModule($m)->getModDir()))
                return false;
        }
        return true;*/
        foreach(frameUms::_()->getModules() as $m) {
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
		if(self::$isMobile === null) {
			$mobileDetect = new mobileDetectUms();
			self::$isMobile = $mobileDetect->isMobile();
		}
		return self::$isMobile;
	}
	/**
	 * Check if device is tablet
	 * @return bool true if user are watching this site from tablet device
	 */
	static public function isTablet() {
		$mobileDetect = new mobileDetectUms();
		return $mobileDetect->isTablet();
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
		if(UMS_TEST_MODE) {
			add_action('activated_plugin', array(frameUms::_(), 'savePluginActivationErrors'));
		}
        if (function_exists('is_multisite') && is_multisite()) {
			// $orig_id = $wpdb->blogid;
            $blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_id as $id) {
                if (switch_to_blog($id)) {
                    installerUms::init();
					restore_current_blog();
                }
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerUms::init();
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
                    installerUms::delete();
					restore_current_blog();
                }
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerUms::delete();
        }
    }
	static public function deactivatePlugin() {
		global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
			// $orig_id = $wpdb->blogid;
            $blog_id = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_id as $id) {
                if (switch_to_blog($id)) {
                    installerUms::deactivate();
					restore_current_blog();
                }
            }
			// restore_current_blog();
			// switch_to_blog($orig_id);
            return;
        } else {
            installerUms::deactivate();
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
		return (basename(reqUms::getVar('SCRIPT_NAME', 'server')) === 'plugins.php');
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
	 * Parse worumsess post/page/custom post type content for images and return it's IDs if there are images
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
		$lang = get_locale();
		return strlen($lang) > 2 ? substr($lang, 0, 2) : $lang;
	}
	static public function umsExtractImgTags($str) {
		preg_match_all('/<img[^>]+>/i', $str, $result);
		if(!empty($result) && !empty($result[0])) {
			return $result[0];
		}
		return false;
	}
	static public function classExists($name) {
		return !empty($name) && class_exists($name) ? true : false;
	}
}
