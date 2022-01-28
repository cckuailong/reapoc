<?php
class reqUms {
    static protected $_requestData;
    static protected $_requestMethod;

    static public function init() {
		// Empty for now
    }
	static public function startSession() {
		if(!utilsUms::isSessionStarted()) {
			session_start();
		}
	}
/**
 * @param string $name key in variables array
 * @param string $from from where get result = "all", "input", "get"
 * @param mixed $default default value - will be returned if $name wasn't found
 * @return mixed value of a variable, if didn't found - $default (NULL by default)
 */

 static public function sanitize_array( &$array, $parentKey = '' ) {
    $allowed = '<div><span><pre><p><br><hr><hgroup><h1><h2><h3><h4><h5><h6>
      <ul><ol><li><dl><dt><dd><strong><em><b><i><u>
      <img><a><abbr><address><blockquote><area><audio><video>
      <form><fieldset><label><input><textarea>
      <caption><table><tbody><td><tfoot><th><thead><tr>
      <iframe><select><option>';
    $keys = array('description');
    foreach ($array as $key => &$value) {
      if (in_array($key, $keys)) {
        if (!is_array($value)) {
          $value = strip_tags($value, $allowed);
        }
      } else {
        if( !is_array($value) )	{
          $value = sanitize_text_field($value);
        } else {
          self::sanitize_array($value);
        }
      }
    }
    return $array;
  }

    static public function getVar($name, $from = 'all', $default = NULL) {
        $from = strtolower($from);
        if($from == 'all') {
            if(isset($_GET[$name])) {
                $from = 'get';
            } elseif(isset($_POST[$name])) {
                $from = 'post';
            }
        }

        switch($from) {
        case 'get':
              if(isset($_GET[$name])) {
                if (is_array($_GET[$name])) {
                  return self::sanitize_array($_GET[$name]);
                } else {
                  return sanitize_text_field($_GET[$name]);
                }
              }
        break;
        case 'post':
              if(isset($_POST[$name])) {
                if (is_array($_POST[$name])) {
                  return self::sanitize_array($_POST[$name]);
                } else {
                  return sanitize_text_field($_POST[$name]);
                }
              }
        break;
        case 'session':
            if(isset($_SESSION[$name])) {
              if (is_array($_SESSION[$name])) {
                return self::sanitize_array($_SESSION[$name]);
              } else {
                return sanitize_text_field($_SESSION[$name]);
              }
            }
        break;
        case 'file':
              if (!empty($_FILES['csv_import_file_maps']) || !empty($_FILES['csv_import_file_markers'])) {
                $files = !empty($_FILES['csv_import_file_maps']) ? $_FILES['csv_import_file_maps'] : $_FILES['csv_import_file_markers'];
                $fileInfo = wp_check_filetype(basename($files['name']));
                if (!empty($fileInfo['ext'])) {
                   return $files;
                }
              }
        break;
        case 'server':
            if(isset($_SERVER[$name])) {
              if (is_array($_SERVER[$name])) {
                return self::sanitize_array($_SERVER[$name]);
              } else {
                return sanitize_text_field($_SERVER[$name]);
              }
            }
        break;
			  case 'cookie':
				if(isset($_COOKIE[$name])) {
					$value = sanitize_text_field($_COOKIE[$name]);
					if(strpos($value, '_JSON:') === 0) {
						$value = explode('_JSON:', $value);
						$value = utilsUms::jsonDecode(array_pop($value));
					}
          return sanitize_text_field($value);
				}
				break;
        }
        return $default;
    }
	static public function isEmpty($name, $from = 'all') {
		$val = self::getVar($name, $from);
		return empty($val);
	}
    static public function setVar($name, $val, $in = 'input') {
        $in = strtolower($in);
        if (is_array($val)) {
          $val = $this->sanitize_array($val);
        } else {
          $val = sanitize_text_field($val);
        }
        switch($in) {
            case 'get':
                $_GET[$name] = $val;
            break;
            case 'post':
                $_POST[$name] = $val;
            break;
            case 'session':
                $_SESSION[$name] = $val;
            break;
        }
    }
    static public function clearVar($name, $in = 'input') {
        $in = strtolower($in);
        switch($in) {
            case 'get':
                if(isset($_GET[$name]))
                    unset($_GET[$name]);
            break;
            case 'post':
                if(isset($_POST[$name]))
                    unset($_POST[$name]);
            break;
            case 'session':
                if(isset($_SESSION[$name]))
                    unset($_SESSION[$name]);
            break;
        }
    }
    static public function get($what) {
        $what = strtolower($what);
        switch($what) {
            case 'get':
                if (is_array($_GET)) {
                  return self::sanitize_array($_GET);
                } else {
                  return sanitize_text_field($_GET);
                }
                break;
            case 'post':
                if (is_array($_POST)) {
                  return self::sanitize_array($_POST);
                } else {
                  return sanitize_text_field($_POST);
                }
                break;
            case 'session':
                if (is_array($_SESSION)) {
                  return self::sanitize_array($_SESSION);
                } else {
                  return sanitize_text_field($_SESSION);
                }
                break;
            case 'files':
                if (!empty($_FILES['kml_file']) && !empty($_FILES['kml_file']['name'])) {
                  $fileInfo = wp_check_filetype(basename($_FILES['kml_file']['name']));
                  if (!empty($fileInfo['ext']) && !empty($files['kml_file'])) {
                     $files = $_FILES['kml_file'];
                  }
                  return $files;
                }
                break;
        }
        return NULL;
    }
    static public function getMethod() {
        if(!self::$_requestMethod) {
            self::$_requestMethod = strtoupper( self::getVar('method', 'all', $_SERVER['REQUEST_METHOD']) );
        }
        return self::$_requestMethod;
    }
    static public function getAdminPage() {
        $pagePath = self::getVar('page');
        if(!empty($pagePath) && strpos($pagePath, '/') !== false) {
            $pagePath = explode('/', $pagePath);
            return str_replace('.php', '', $pagePath[count($pagePath) - 1]);
        }
        return false;
    }
    static public function getRequestUri() {
        return $_SERVER['REQUEST_URI'];
    }
    static public function getMode() {
        $mod = '';
        if(!($mod = self::getVar('mod')))  //Frontend usage
            $mod = self::getVar('page');     //Admin usage
        return $mod;
    }
}
