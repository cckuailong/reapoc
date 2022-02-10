<?php
namespace Cloudinary {

    class Uploader {
        public static function build_upload_params(&$options)
        {
            $params = array("timestamp" => time(),
                "transformation" => \Cloudinary::generate_transformation_string($options),
                "public_id" => \Cloudinary::option_get($options, "public_id"),
                "callback" => \Cloudinary::option_get($options, "callback"),
                "format" => \Cloudinary::option_get($options, "format"),
                "backup" => \Cloudinary::option_get($options, "backup"),
                "faces" => \Cloudinary::option_get($options, "faces"),
                "image_metadata" => \Cloudinary::option_get($options, "image_metadata"),
                "exif" => \Cloudinary::option_get($options, "exif"),
                "colors" => \Cloudinary::option_get($options, "colors"),
                "type" => \Cloudinary::option_get($options, "type"),
                "eager" => Uploader::build_eager(\Cloudinary::option_get($options, "eager")),
                "headers" => Uploader::build_custom_headers(\Cloudinary::option_get($options, "headers")),
                "use_filename" => \Cloudinary::option_get($options, "use_filename"),
                "unique_filename" => \Cloudinary::option_get($options, "unique_filename"),
                "discard_original_filename" => \Cloudinary::option_get($options, "discard_original_filename"),
                "notification_url" => \Cloudinary::option_get($options, "notification_url"),
                "eager_notification_url" => \Cloudinary::option_get($options, "eager_notification_url"),
                "eager_async" => \Cloudinary::option_get($options, "eager_async"),
                "invalidate" => \Cloudinary::option_get($options, "invalidate"),
                "proxy" => \Cloudinary::option_get($options, "proxy"),
                "folder" => \Cloudinary::option_get($options, "folder"),
                "tags" => implode(",", \Cloudinary::build_array(\Cloudinary::option_get($options, "tags"))));
	    array_walk($params, function (&$value, $key){ $value = (is_bool($value) ? ($value ? "1" : "0") : $value);});
	    return array_filter($params,function($v){ return !is_null($v) && ($v !== "" );});
        }

        public static function upload($file, $options = array())
        {
            $params = Uploader::build_upload_params($options);
            return Uploader::call_api("upload", $params, $options, $file);
        }

        public static function destroy($public_id, $options = array())
        {
            $params = array(
                "timestamp" => time(),
                "type" => \Cloudinary::option_get($options, "type"),
                "invalidate" => \Cloudinary::option_get($options, "invalidate"),
                "public_id" => $public_id
            );
            return Uploader::call_api("destroy", $params, $options);
        }

        public static function rename($from_public_id, $to_public_id, $options = array())
        {
            $params = array(
                "timestamp" => time(),
                "type" => \Cloudinary::option_get($options, "type"),
                "from_public_id" => $from_public_id,
                "to_public_id" => $to_public_id,
                "overwrite" => \Cloudinary::option_get($options, "overwrite")
            );
            return Uploader::call_api("rename", $params, $options);
        }
        
        public static function explicit($public_id, $options = array())
        {
            $params = array(
                "timestamp" => time(),
                "public_id" => $public_id,
                "type" => \Cloudinary::option_get($options, "type"),
                "callback" => \Cloudinary::option_get($options, "callback"),
                "eager" => Uploader::build_eager(\Cloudinary::option_get($options, "eager")),
                "headers" => Uploader::build_custom_headers(\Cloudinary::option_get($options, "headers")),
                "tags" => implode(",", \Cloudinary::build_array(\Cloudinary::option_get($options, "tags")))
            );
            return Uploader::call_api("explicit", $params, $options);
        }

        public static function generate_sprite($tag, $options = array())
        {
            $transformation = \Cloudinary::generate_transformation_string(
              array_merge(array("fetch_format"=>\Cloudinary::option_get($options, "format")), $options));
            $params = array(
                "timestamp" => time(),
                "tag" => $tag,
                "async" => \Cloudinary::option_get($options, "async"),
                "notification_url" => \Cloudinary::option_get($options, "notification_url"),
                "transformation" => $transformation
            );
            return Uploader::call_api("sprite", $params, $options);
        }

        public static function multi($tag, $options = array())
        {
            $transformation = \Cloudinary::generate_transformation_string($options);
            $params = array(
                "timestamp" => time(),
                "tag" => $tag,
                "format" => \Cloudinary::option_get($options, "format"),
                "async" => \Cloudinary::option_get($options, "async"),
                "notification_url" => \Cloudinary::option_get($options, "notification_url"),
                "transformation" => $transformation
            );
            return Uploader::call_api("multi", $params, $options);
        }

        public static function explode($public_id, $options = array())
        {
            $transformation = \Cloudinary::generate_transformation_string($options);
            $params = array(
                "timestamp" => time(),
                "public_id" => $public_id,
                "format" => \Cloudinary::option_get($options, "format"),
                "type" => \Cloudinary::option_get($options, "type"),
                "notification_url" => \Cloudinary::option_get($options, "notification_url"),
                "transformation" => $transformation
            );
            return Uploader::call_api("explode", $params, $options);
        }

        // options may include 'exclusive' (boolean) which causes clearing this tag from all other resources
        public static function add_tag($tag, $public_ids = array(), $options = array())
        {
            $exclusive = \Cloudinary::option_get($options, "exclusive");
            $command = $exclusive ? "set_exclusive" : "add";
            return Uploader::call_tags_api($tag, $command, $public_ids, $options);
        }

        public static function remove_tag($tag, $public_ids = array(), $options = array())
        {
            return Uploader::call_tags_api($tag, "remove", $public_ids, $options);
        }

        public static function replace_tag($tag, $public_ids = array(), $options = array())
        {
            return Uploader::call_tags_api($tag, "replace", $public_ids, $options);
        }

        public static function call_tags_api($tag, $command, $public_ids = array(), &$options = array())
        {
            $params = array(
                "timestamp" => time(),
                "tag" => $tag,
                "public_ids" => \Cloudinary::build_array($public_ids),
                "type" => \Cloudinary::option_get($options, "type"),
                "command" => $command
            );
            return Uploader::call_api("tags", $params, $options);
        }

        private static $TEXT_PARAMS = array("public_id", "font_family", "font_size", "font_color", "text_align", "font_weight", "font_style", "background", "opacity", "text_decoration");

        public static function text($text, $options = array())
        {
            $params = array("timestamp" => time(), "text" => $text);
            foreach (Uploader::$TEXT_PARAMS as $key) {
                $params[$key] = \Cloudinary::option_get($options, $key);
            }
            return Uploader::call_api("text", $params, $options);
        }

        public static function call_api($action, $params, $options = array(), $file = NULL)
        {
            $return_error = \Cloudinary::option_get($options, "return_error");
            $params = \Cloudinary::sign_request($params, $options);

            $api_url = \Cloudinary::cloudinary_api_url($action, $options);

            # Serialize params
            $api_url .= "?" . preg_replace("/%5B\d+%5D/", "%5B%5D", http_build_query($params)); 

            $ch = curl_init($api_url);

            $post_params = array();
            if ($file) {
                if (file_exists($file) && function_exists("curl_file_create")) {
                    $post_params['file'] = curl_file_create($file);
                } else if (!preg_match('/^@|^https?:|^s3:|^data:[^;]*;base64,([a-zA-Z0-9\/+\n=]+)$/', $file)) {
                    $post_params["file"] = "@" . $file;
                } else {
                    $post_params["file"] = $file;
                }
            }

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
            curl_setopt($ch, CURLOPT_CAINFO,realpath(dirname(__FILE__))."/cacert.pem");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $curl_error = NULL;
            if(curl_errno($ch))
            {
                $curl_error = curl_error($ch);
            }

            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response_data = $response;

            curl_close($ch);
            if ($curl_error != NULL) {
                throw new \Exception("Error in sending request to server - " . $curl_error);
            }
            if ($code != 200 && $code != 400 && $code != 500 && $code != 401 && $code != 404) {
                throw new \Exception("Server returned unexpected status code - " . $code . " - " . $response_data);
            }
            $result = json_decode($response_data, TRUE);
            if ($result == NULL) {
                throw new \Exception("Error parsing server response (" . $code . ") - " . $response_data);
            }
            if (isset($result["error"])) {
                if ($return_error) {
                    $result["error"]["http_code"] = $code;
                } else {
                    throw new \Exception($result["error"]["message"]);
                }
            }
            return $result;
        }
        protected static function build_eager($transformations) {
            $eager = array();
            foreach (\Cloudinary::build_array($transformations) as $trans) {
                $transformation = $trans;
                $format = \Cloudinary::option_consume($tranformation, "format");
                $single_eager = implode("/", array_filter(array(\Cloudinary::generate_transformation_string($transformation), $format)));
                array_push($eager, $single_eager);
            }
            return implode("|", $eager);
        }

        protected static function build_custom_headers($headers) {
            if ($headers == NULL) {
                return NULL;
            } elseif (is_string($headers)) {
                return $headers;
            } elseif ($headers == array_values($headers)) {
                return implode("\n", $headers);
            } else {
                $join_pair = function($key, $value) { return $key . ": " . $value; };
                return implode("\n", array_map($join_pair, array_keys($headers), array_values($headers)));
            }
        }  
    }

	class PreloadedFile {
		public static $PRELOADED_CLOUDINARY_PATH = "/^([^\/]+)\/([^\/]+)\/v(\d+)\/([^#]+)#([^\/]+)$/";
		
  		public $filename, $version, $public_id, $signature, $resource_type, $type;
		
	    public function __construct($file_info) {
	    	if (preg_match(\Cloudinary\PreloadedFile::$PRELOADED_CLOUDINARY_PATH, $file_info, $matches)) {
	    		$this->resource_type = $matches[1]; 
	    		$this->type = $matches[2];
	    		$this->version = $matches[3];
	    		$this->filename = $matches[4];
	    		$this->signature = $matches[5];
				
				$public_id_and_format = $this->split_format($this->filename);
				$this->public_id = $public_id_and_format[0];
				$this->format = $public_id_and_format[1]; 

			} else {
				throw new \InvalidArgumentException("Invalid preloaded file info");	
			}			
	    }
		
		public function is_valid() {
		    $public_id = $this->resource_type == "raw" ? $this->filename : $this->public_id;
		    $expected_signature = \Cloudinary::api_sign_request(array("public_id" => $public_id, "version" => $this->version), \Cloudinary::config_get("api_secret")); 
		    return $this->signature == $expected_signature;			
		}

    protected function split_format($identifier) {
			$last_dot = strrpos($identifier, ".");
			
			if ($last_dot === false) {
				return array($identifier, NULL);
			} 
			$public_id = substr($identifier, 0, $last_dot);	
			$format = substr($identifier, $last_dot+1);
			return array($public_id, $format);    
		}

		public function identifier() {
			return "v" . $this->version . "/" . $this->filename;
		}
		
		  
	    public function __toString() {
			return $this->resource_type . "/" . $this->type . "/v" . $this->version . "/" . $this->filename . "#" . $this->signature;
		}
		
	}
}

namespace {
    function cl_upload_url($options = array()) 
    {
        if (!isset($options["resource_type"])) $options["resource_type"] = "auto";
        return Cloudinary::cloudinary_api_url("upload", $options);      
    }

    function cl_upload_tag_params($options = array()) 
    {
        $params = Cloudinary\Uploader::build_upload_params($options);
        $params = Cloudinary::sign_request($params, $options);
        return json_encode($params);
    }
    
    function cl_image_upload_tag($field, $options = array())
    {
        $html_options = Cloudinary::option_get($options, "html", array());

        $classes = array("cloudinary-fileupload");
        if (isset($html_options["class"])) {
            array_unshift($classes, Cloudinary::option_consume($html_options, "class"));
        }
        $tag_options = array_merge($html_options, array("type" => "file", "name" => "file",
            "data-url" => cl_upload_url($options),
            "data-form-data" => cl_upload_tag_params($options),
            "data-cloudinary-field" => $field,
            "class" => implode(" ", $classes),
        ));
        return '<input ' . Cloudinary::html_attrs($tag_options) . '/>';
    }

    function cl_form_tag($callback_url, $options = array())
    {
        $form_options = Cloudinary::option_get($options, "form", array());

        $options["callback_url"] = $callback_url;

        $params = Cloudinary\Uploader::build_upload_params($options);
        $params = Cloudinary::sign_request($params, $options);

        $api_url = Cloudinary::cloudinary_api_url("upload", $options);

        $form = "<form enctype='multipart/form-data' action='" . $api_url . "' method='POST' " . Cloudinary::html_attrs($form_options) . ">\n";
        foreach ($params as $key => $value) {
            $form .= "<input " . Cloudinary::html_attrs(array("name" => $key, "value" => $value, "type" => "hidden")) . "/>\n";
        }
        $form .= "</form>\n";

        return $form;
    }
}
?>
