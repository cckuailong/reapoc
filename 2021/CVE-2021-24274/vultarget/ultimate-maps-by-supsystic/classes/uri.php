<?php
class uriUms {
	/**
	 * Tell link form method to replace symbols for special html caracters only for ONE output
	 */
	static private $_oneHtmlEnc = false;
    static public function fileToPageParam($file) {
        $file = str_replace(DS, '/', $file);
        return substr($file, strpos($file, UMS_PLUG_NAME));
    }
    static public function _($params) {
        global $wp_rewrite;
        $link = '';
		if(is_string($params) && (strpos($params, 'http') === 0
			|| strpos($params, UMS_PLUG_NAME) !== false)	// If relative links in WP is used (by other plugin for example)
		) {
			if(self::isHttps())
				$params = self::makeHttps($params);
			return $params;
		} elseif(is_array($params) && isset($params['page_id'])) {
            if(is_null($wp_rewrite)) {
                $wp_rewrite = new WP_Rewrite();
            }
            $link = get_page_link($params['page_id']);
            unset($params['page_id']);
        } elseif(is_array($params) && isset($params['baseUrl'])) {
            $link = $params['baseUrl'];
            unset($params['baseUrl']);
        } else {
            $link = UMS_URL;
        }
        if(!empty($params)) {
            $query = is_array($params) ? http_build_query($params, '', '&') : $params;
            $link .= (strpos($link, '?') === false ? '?' : '&'). $query;
        }
		if(self::$_oneHtmlEnc) {
			$link = str_replace('&', '&amp;', $link);
			self::$_oneHtmlEnc = false;
		}
        return $link;
    }
    static public function _e($params) {
        echo self::_($params);
    }
    static public function page($id) {
        return get_page_link($id);
    }
    static public function mod($name, $action = '', $data = NULL) {
        $params = array('mod' => $name);
        if($action)
            $params['action'] = $action;
		$params['pl'] = UMS_CODE;
        if($data) {
            if(is_array($data)) {
                $params = array_merge($params, $data);
				if(isset($data['reqType']) && $data['reqType'] == 'ajax') {
					$params['baseUrl'] = admin_url('admin-ajax.php');
				}
            } elseif(is_string($data)) {
                $params = http_build_query($params);
                $params .= '&'. $data;
            }
        }
        return self::_($params);
    }
    /**
     * Get current path
     * @return string current link
     */
    static public function getCurrent() {
        if (!empty($_SERVER['HTTPS'])) {
            return 'https://'. $_SERVER['HTTP_HOST']. $_SERVER['SCRIPT_NAME'];
        } else {
            return 'http://'. $_SERVER['HTTP_HOST']. $_SERVER['SCRIPT_NAME'];
        }
    }
	static public function getFullUrl() {
		$url = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$url .= $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
		return $url;
	}
	/**
	 * Replace symbols to special html caracters in one output
	 */
	static public function oneHtmlEnc() {
		self::$_oneHtmlEnc = true;
	}
	static public function makeHttps($link) {
		if(strpos($link, 'https:') === false) {
			$link = str_replace('http:', 'https:', $link);
		}
		return $link;
	}
	static public function isHttps() {
		return is_ssl();
		//return (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on');
	}
}
