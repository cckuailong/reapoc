<?php
/**
 * Class for handling embedded media in gallery
 *
 */
class WDWLibraryEmbed {

  public function __construct() {}

	public function get_provider($oembed, $url, $args = '') {
		$provider = false;
		if (!isset($args['discover'])) {
			$args['discover'] = true;
		}
		foreach ($oembed->providers as $matchmask => $data ) {
			list( $providerurl, $regex ) = $data;
			// Turn the asterisk-type provider URLs into regex
			if ( !$regex ) {
				$matchmask = '#' . str_replace( '___wildcard___', '(.+)', preg_quote( str_replace( '*', '___wildcard___', $matchmask ), '#' ) ) . '#i';
				$matchmask = preg_replace( '|^#http\\\://|', '#https?\://', $matchmask );
			}
			if ( preg_match( $matchmask, $url ) ) {
				$provider = str_replace( '{format}', 'json', $providerurl ); // JSON is easier to deal with than XML
				break;
			}
		}
		if ( !$provider && $args['discover'] ) {
			$provider = $oembed->discover($url);
		}
		return $provider;
	}

  /**
   * check host and get data for a given url
   * @return encode_json(associative array of data) on success
   * @return encode_json(array[false, "error message"]) on failure
   *
   * EMBED TYPES
   *
   *  EMBED_OEMBED_YOUTUBE_VIDEO
   *  EMBED_OEMBED_VIMEO_VIDEO
   *  EMBED_OEMBED_DAILYMOTION_VIDEO
   *  EMBED_OEMBED_INSTAGRAM_IMAGE
   *  EMBED_OEMBED_INSTAGRAM_VIDEO
   *  EMBED_OEMBED_INSTAGRAM_POST
   *  EMBED_OEMBED_FLICKR_IMAGE
   *
   *  EMBED_OEMBED_FACEBOOK_IMAGE
   *  EMBED_OEMBED_FACEBOOK_VIDEO
   *  EMBED_OEMBED_FACEBOOK_POST
   *
   *  RULES FOR NEW TYPES
   *
   *  1. begin type name with EMBED_
   *  2. if using WP native OEMBED class, add _OEMBED then
   *  3. add provider name
   *  4. add _VIDEO, _IMAGE FOR embedded media containing only video or image
   *  5. add _DIRECT_URL from static URL of image (not implemented yet)
   *
   */
  public static function add_embed( $url = '', $instagram_data = array() ) {
    $url = sanitize_text_field(urldecode($url));
    $embed_type = '';
    $host = '';
    /*returns this array*/
    $embedData = array(
      'name' => '',
      'description' => '',
      'filename' => '',
      'url' => '',
      'reliative_url' => '',
      'thumb_url' => '',
      'thumb' => '',
      'size' => '',
      'filetype' => '',
      'date_modified' => '',
      'resolution' => '',
      'redirect_url' => ''
    );

    $accepted_oembeds = array(
      'YOUTUBE' => '/youtube/',
      'VIMEO' => '/vimeo/',
      'FLICKR' => '/flickr/',
      'INSTAGRAM' => '/instagram/',
      'DAILYMOTION' => '/dailymotion/'
    );
    
    /*check if url is from facebook */
    //explodes URL based on slashes
    $first_token  = strtok($url, '/');
    $second_token = strtok('/');
    $third_token = strtok('/');	 
    //for video's url
    $fourth = strtok('/');
    //fifth is for post's fbid if url is post url
    $fifth = strtok('/');
    //sixth is for video's fbid if url is video url
    $sixth = strtok('/');

    if ( $second_token === 'www.facebook.com') {
      $json_data = array("error", "Incorect url.");
      if ( has_filter('init_facebook_add_embed_bwg') ) {
        $arg = array(
          'app_id' => BWG()->options->facebook_app_id,
          'app_secret' => BWG()->options->facebook_app_secret,
          'third_token' => $third_token,
          'fourth' => $fourth,
          'fifth' => $fifth,
          'sixth' => $sixth,
          'url' => $url
        );
        $json_data = array();
        $json_data = apply_filters('init_facebook_add_embed_bwg', array(), $arg);
      }
		  return json_encode($json_data);
    }
	
    /*check if we can embed this using wordpress class WP_oEmbed */
    if ( !function_exists( '_wp_oembed_get_object' ) )
      include( BWG()->abspath . WPINC . '/class-oembed.php' );
    // get an oembed object
    $oembed = _wp_oembed_get_object();
    if (method_exists($oembed, 'get_provider')) {
      // Since 4.0.0
      $provider = $oembed->get_provider($url);
    }
    else {
      $provider = self::get_provider($oembed, $url);
    }
    foreach ($accepted_oembeds as $oembed_provider => $regex) {
      if(preg_match($regex, $provider)==1){
        $host = $oembed_provider;
      }
    }
    /*
     * Wordpress oembed not recognize instagram post url,
     * so we check manually.
    */
    if ( !$host ) {
      $parse = parse_url($url);
      $host = ($parse['host'] == "www.instagram.com") ? 'INSTAGRAM' : FALSE;
    }

    /*return json_encode($host); for test*/
    /*handling oembed cases*/    
    if ( $host ) {
      if ( $host == 'INSTAGRAM' ) {
      $matches = array();
      $filename = '';
      $regex = "/^.*?instagram\.com\/p\/(.*?)[\/]?$/";
      if ( preg_match($regex, $url, $matches) ) {
        $filename = $matches[1];
        if ( strtolower(substr($filename, -5)) == '/post' ) {
          $filename = substr($filename, 0, -5);
        }
      }
      $description = !empty($instagram_data->caption) ? $instagram_data->caption : '';
      // Content
      if ( $host == 'INSTAGRAM' && strtolower(substr($url, -4)) != 'post' ) {
        if ( !empty($instagram_data) ) {
          $media_url = $instagram_data->media_url;
          if ( $instagram_data->media_type == 'VIDEO' ) {
            $embed_type = 'EMBED_OEMBED_INSTAGRAM_VIDEO';
            $thumb_url = $instagram_data->thumbnail_url;
          }
          else {
            if ( $instagram_data->media_type == 'IMAGE' ) {
              $embed_type = 'EMBED_OEMBED_INSTAGRAM_IMAGE';
              $thumb_url = $instagram_data->media_url;
            }
          }
          list($media_width, $media_height) = @getimagesize($thumb_url);
          $img_width = !empty($media_width) ? $media_width : '640';
          $img_height = !empty($media_height) ? $media_height : '640';
        }
        else {
          $result = self::instagram_oembed_connect($url);
          if ( !empty($result->error) ) {
            return json_encode($result->error);
          }
          $embed_type = 'EMBED_OEMBED_INSTAGRAM_POST';
          $media_url = base64_encode($result->html);
          $thumb_url = $result->thumbnail_url;
          $img_width = $result->thumbnail_width;
          $img_height = $result->thumbnail_height;
        }
      }
      // Whole post
      if ( $host == 'INSTAGRAM' && strtolower(substr($url, -4)) == 'post' ) {
        if ( !empty($instagram_data) ) {
          $result = self::instagram_oembed_connect($instagram_data->permalink);
          if ( !empty($result->error) ) {
            return json_encode($result->error);
          }
          $embed_type = 'EMBED_OEMBED_INSTAGRAM_POST';
          $media_url = base64_encode($result->html);
          $thumb_url = $result->thumbnail_url;
          $img_width = $result->thumbnail_width;
          $img_height = $result->thumbnail_height;
        }
      }
      $embedData = array(
        'name' => '',
        'description' => htmlspecialchars($description),
        'filename' => $filename,
        'url' => $media_url,
        'reliative_url' => $media_url,
        'thumb_url' => $thumb_url,
        'thumb' => $thumb_url,
        'size' => '',
        'filetype' => $embed_type,
        'resolution' => $img_width . " x " . $img_height . " px",
        'resolution_thumb' => $img_width . "x" . $img_height,
        'redirect_url' => '',
        'date_modified' => date("Y-m-d H:i:s"),
      );
      return json_encode($embedData);
    }
      $result = $oembed->fetch( $provider, $url);
      /*no data fetched for a known provider*/
      if ( !$result ) {
        return json_encode(array( "error", "please enter " . $host . " correct single media URL" ));
      }
      else { /*one of known oembed types*/
        $embed_type = 'EMBED_OEMBED_'.$host;
        switch ($embed_type) {
          case 'EMBED_OEMBED_YOUTUBE': {
            $youtube_regex = "#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#";
            $matches = array();
            preg_match($youtube_regex , $url , $matches);
            $filename = $matches[0];

            $embedData = array(
              'name' => '',
              'description' => htmlspecialchars($result->title),
              'filename' => $filename,
              'url' => $url,
              'reliative_url' => $url,
              'thumb_url' => $result->thumbnail_url,
              'thumb' => $result->thumbnail_url,
              'size' => '',
              'filetype' => $embed_type."_VIDEO",
              'date_modified' => date("Y-m-d H:i:s"),
              'resolution' => $result->width." x ".$result->height." px",
              'resolution_thumb' => $result->thumbnail_width . " x " . $result->thumbnail_height,
              'redirect_url' => '');
            return json_encode($embedData);
          }
          break;
          case 'EMBED_OEMBED_VIMEO': {
            $embedData = array(
              'name' => '',
              'description' => htmlspecialchars($result->title),
              'filename' => $result->video_id,
              'url' => $url,
              'reliative_url' => $url,
              'thumb_url' => $result->thumbnail_url,
              'thumb' => $result->thumbnail_url,
              'size' => '',
              'filetype' => $embed_type."_VIDEO",
              'date_modified' => date("Y-m-d H:i:s"),
              'resolution' => $result->thumbnail_width . " x " . $result->thumbnail_height,
              'resolution_thumb' => $result->thumbnail_width . " x " . $result->thumbnail_height,
              'redirect_url' => '');

            return json_encode($embedData);
		      }
          break;
          case 'EMBED_OEMBED_FLICKR': {
            $matches = preg_match('~^.+/(\d+)~',$url,$matches);
            $filename = $matches[1];
            /*if($result->type =='photo')
              $embed_type .= '_IMAGE';
            if($result->type =='video')
              $embed_type .= '_VIDEO';*/
              /*flickr video type not implemented yet*/
              $embed_type .= '_IMAGE';
                         
            $embedData = array(
              'name' => '',
              'description' => htmlspecialchars($result->title),
              'filename' =>substr($result->thumbnail_url, 0, -5)."b.jpg", 
              'url' => $url,
              'reliative_url' => $url,
              'thumb_url' => $result->thumbnail_url,
              'thumb' => $result->thumbnail_url,
              'size' => '',
              'filetype' => $embed_type,
              'date_modified' => date("Y-m-d H:i:s"),
              'resolution' => $result->width." x ".$result->height." px",
              'resolution_thumb' => $result->thumbnail_width . " x " . $result->thumbnail_height,
              'redirect_url' => '');
            return json_encode($embedData);
		      }
          break;
          case 'EMBED_OEMBED_DAILYMOTION': {
            $filename = strtok(basename($url), '_');
            $embedData = array(
              'name' => '',
              'description' => htmlspecialchars($result->title),
              'filename' => $filename,
              'url' => $url,
              'reliative_url' => $url,
              'thumb_url' => $result->thumbnail_url,
              'thumb' => $result->thumbnail_url,
              'size' => '',
              'filetype' => $embed_type."_VIDEO",
              'date_modified' => date("Y-m-d H:i:s"),
              'resolution' => $result->width." x ".$result->height." px",
              'resolution_thumb' => $result->thumbnail_width . " x " . $result->thumbnail_height,
              'redirect_url' => '');

            return json_encode($embedData);
          }
          break;
          case 'EMBED_OEMBED_GETTYIMAGES': {
			      /*not working yet*/
            $filename = strtok(basename($url), '_');
            
            $embedData = array(
              'name' => '',
              'description' => htmlspecialchars($result->title),
              'filename' => $filename,
              'url' => $url,
              'reliative_url' => $url,
              'thumb_url' => $result->thumbnail_url,
              'thumb' => $result->thumbnail_url,
              'size' => '',
              'filetype' => $embed_type,
              'date_modified' => date("Y-m-d H:i:s"),
              'resolution' => $result->width . " x " . $result->height . " px",
              'redirect_url' => '');

            return json_encode($embedData);
		      }
          default:
            return json_encode( array("error", __('The entered URL is incorrect. Please check the URL and try again.', BWG()->prefix) ) );
          break;
        }
      }
    }/*end of oembed cases*/
    else {
      /*check for direct image url*/
      /*check if something else*/
      /*not implemented yet*/
      return json_encode( array("error", __('The entered URL is incorrect. Please check the URL and try again.', BWG()->prefix) ) );
    }
    return json_encode( array("error", __('The entered URL is incorrect. Please check the URL and try again.', BWG()->prefix) ) );
  }

  /**
 * client side analogue is function spider_display_embed in bwg_embed.js
 *
 * @param embed_type: string , one of predefined accepted types
 * @param embed_id: string, id of media in corresponding host, or url if no unique id system is defined for host
 * @param attrs: associative array with html attributes and values format e.g. array('width'=>"100px", 'style'=>"display:inline;")
 * 
 */
  public static function display_embed($embed_type, $file_url, $embed_id = '', $attrs = array()) {
    $html_to_insert = '';
    $is_visible = true;
    if (isset($attrs['is_visible'])) {
      $is_visible = $attrs['is_visible'];
      $bwg = $attrs['bwg'];
      $image_key = $attrs['image_key'];
      /*  The attrs using in div as attribute  */
      unset($attrs['bwg'], $attrs['is_visible'], $attrs['image_key']);
      if (!$is_visible) {
        $attrs['class'] .= ' bwg_carousel_preload';
      }
    }
    switch ($embed_type) {
      case 'EMBED_OEMBED_YOUTUBE_VIDEO':
        {
          $oembed_youtube_html = '<iframe ';
          if ($embed_id != '') {
            $oembed_youtube_query_args = array();
            if (strpos($embed_id, "?t=") !== FALSE) {
              $seconds = 0;
              $start_info = substr($embed_id, (strpos($embed_id, "?t=") + 3), strlen($embed_id));
              $embed_id = substr($embed_id, 0, strpos($embed_id, "?t="));
              if (strpos($start_info, "h") !== FALSE) {
                $hours = substr($start_info, 0, strpos($start_info, "h"));
                $seconds += $hours * 3600;
              }
              if (strpos($start_info, "m") !== FALSE) {
                if (strpos($start_info, "h") !== FALSE) {
                  $minutes = substr($start_info, strpos($start_info, "h") + 1, -strpos($start_info, "m"));
                } else {
                  $minutes = substr($start_info, 0, strpos($start_info, "m"));
                }
                $seconds += $minutes * 60;
              }
              if (strpos($start_info, "s") !== FALSE) {
                if (strpos($start_info, "m") !== FALSE) {
                  $sec = substr($start_info, strpos($start_info, "m") + 1, -1);
                } else {
                  $sec = substr($start_info, 0, -1);
                }
                $seconds += $sec;
              }
              $oembed_youtube_query_args = array('start' => $seconds);
            }
            $oembed_youtube_query_args += array('enablejsapi' => 1, 'wmode' => 'transparent');
            if ($is_visible) {
              $oembed_youtube_html .= ' src="' . add_query_arg($oembed_youtube_query_args, '//www.youtube.com/embed/' . $embed_id) . '"';
            } else {
              $oembed_youtube_html .= 'id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '"  data-src="' . add_query_arg($oembed_youtube_query_args, '//www.youtube.com/embed/' . $embed_id) . '"';
            }
          }
          foreach ($attrs as $attr => $value) {
            if (preg_match('/src/i', $attr) === 0) {
              if ($attr != '' && $value != '') {
                $oembed_youtube_html .= ' ' . $attr . '="' . $value . '"';
              }
            }
          }
          $oembed_youtube_html .= " ></iframe>";
          $html_to_insert .= $oembed_youtube_html;
          break;
        }
      case 'EMBED_OEMBED_VIMEO_VIDEO':
        {
          $oembed_vimeo_html = '<iframe ';
          if ($embed_id != '') {
            if ($is_visible) {
              $oembed_vimeo_html .= ' src="' . '//player.vimeo.com/video/' . $embed_id . '?enablejsapi=1"';
            } else {
              $oembed_vimeo_html .= 'id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '" data-src="' . '//player.vimeo.com/video/' . $embed_id . '?enablejsapi=1"';
            }
          }
          foreach ($attrs as $attr => $value) {
            if (preg_match('/src/i', $attr) === 0) {
              if ($attr != '' && $value != '') {
                $oembed_vimeo_html .= ' ' . $attr . '="' . $value . '"';
              }
            }
          }
          $oembed_vimeo_html .= " ></iframe>";
          $html_to_insert .= $oembed_vimeo_html;
          break;
        }
      case 'EMBED_OEMBED_FLICKR_IMAGE':
        {
          $oembed_flickr_html = '<div ';
          foreach ($attrs as $attr => $value) {
            if (preg_match('/src/i', $attr) === 0) {
              if ($attr != '' && $value != '') {
                $oembed_flickr_html .= ' ' . $attr . '="' . $value . '"';
              }
            }
          }
          $oembed_flickr_html .= " >";
          if ($embed_id != '') {
            if ($is_visible) {
              $oembed_flickr_html .= '<img src="' . $embed_id . '"';
            } else {
              $oembed_flickr_html .= '<img id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '" data-src="' . $embed_id . '"';
            }
            $oembed_flickr_html .= ' style="' .
              'max-width:' . '100%' . " !important" .
              '; max-height:' . '100%' . " !important" .
              '; width:' . 'auto !important' .
              '; height:' . 'auto !important' .
              ';">';
          }
          $oembed_flickr_html .= "</div>";

          $html_to_insert .= $oembed_flickr_html;
          break;
        }
      case 'EMBED_OEMBED_FLICKR_VIDEO':
        {
          # code...not implemented yet
          break;
        }
      case 'EMBED_OEMBED_INSTAGRAM_POST':
        $oembed_instagram_html = '<div ';
        $id = '';
        foreach ($attrs as $attr => $value) {
          if (preg_match('/src/i', $attr) === 0) {
            if ($attr != '' && $value != '') {
              $oembed_instagram_html .= ' ' . $attr . '="' . $value . '"';
              if (strtolower($attr) == 'class') {
                $class = $value;
              }
            }
          }
        }
        $oembed_instagram_html .= ">";
        if ($file_url != '') {
          if ($is_visible) {
            $oembed_instagram_html .= '<div class="inner_instagram_iframe_' . $class . '"';
          } else {
            $oembed_instagram_html .= '<dev id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '" class="inner_instagram_iframe_' . $class . '"';
          }
          $oembed_instagram_html .= ' frameborder="0" scrolling="no" allowtransparency="false" allowfullscreen ' .
            'style="max-width: 100% !important; max-height: 100% !important; width: 100%; height: 100%; margin:0; vertical-align:middle;">' . base64_decode($file_url) . '</div>';
        }
        $oembed_instagram_html .= "</div>";
        $html_to_insert .= $oembed_instagram_html;
        break;
      case 'EMBED_OEMBED_INSTAGRAM_VIDEO':
        $oembed_instagram_html = '<div ';
        foreach ( $attrs as $attr => $value ) {
          if ( preg_match('/src/i', $attr) === 0 ) {
            if ( $attr != '' && $value != '' ) {
              $oembed_instagram_html .= ' ' . $attr . '="' . $value . '"';
            }
          }
        }
        $oembed_instagram_html .= " >";
        if ( $file_url != '' ) {
          $oembed_instagram_html .= '<video class="bwg_carousel_video" style="width:auto !important; height:auto !important; max-width:100% !important; max-height:100% !important; margin:0 !important;" controls>';
          if ( $is_visible ) {
            $oembed_instagram_html .= '<source src="' . $file_url;
          }
          else {
            $oembed_instagram_html .= '<source id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '" data-src="' . $file_url;
          }
          $oembed_instagram_html .= '" type="video/mp4"> Your browser does not support the video tag. </video>';
        }
        $oembed_instagram_html .= "</div>";
        $html_to_insert .= $oembed_instagram_html;
        break;
      case 'EMBED_OEMBED_INSTAGRAM_IMAGE':
        $oembed_instagram_html = '<div ';
        foreach ($attrs as $attr => $value) {
          if (preg_match('/src/i', $attr) === 0) {
            if ($attr != '' && $value != '') {
              $oembed_instagram_html .= ' ' . $attr . '="' . $value . '"';
            }
          }
        }
        $oembed_instagram_html .= ">";
        if ($embed_id != '') {
          if ($is_visible) {
            $oembed_instagram_html .= '<img src="' . $file_url . '"';
          } else {
            $oembed_instagram_html .= '<img  id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '" data-src=" '. $file_url .' "';
          }
          $oembed_instagram_html .= ' style="' .
            'max-width:' . '100%' . " !important" .
            '; max-height:' . '100%' . " !important" .
            '; width:' . 'auto !important' .
            '; height:' . 'auto !important' .
            ';">';
        }
        $oembed_instagram_html .= "</div>";
        $html_to_insert .= $oembed_instagram_html;
        break;
      case 'EMBED_OEMBED_FACEBOOK_IMAGE':
        $oembed_facebook_html = '<div ';
        foreach ($attrs as $attr => $value) {
          if (preg_match('/src/i', $attr) === 0) {
            if ($attr != '' && $value != '') {
              $oembed_facebook_html .= ' ' . $attr . '="' . $value . '"';
            }
          }
        }
        $oembed_facebook_html .= " >";
        if ($embed_id != '') {
          if ($is_visible) {
            $oembed_facebook_html .= '<img src="' . $file_url . '"';
          } else {
            $oembed_facebook_html .= '<img id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '" data-src="' . $file_url . '"';
          }
          $oembed_facebook_html .= ' style="' .
            'max-width:' . '100%' . " !important" .
            '; max-height:' . '100%' . " !important" .
            '; width:' . 'auto !important' .
            '; height:' . 'auto !important' .
            ';">';
        }
        $oembed_facebook_html .= "</div>";
        $html_to_insert .= $oembed_facebook_html;
        break;
      case 'EMBED_OEMBED_FACEBOOK_VIDEO':
        $oembed_facebook_html = '<iframe class="bwg_fb_video"';
        if ($embed_id != '') {
          if ($is_visible) {
            $oembed_facebook_html .= ' src="//www.facebook.com/video/embed?video_id=' . $file_url . '&enablejsapi=1&wmode=transparent"';
          } else {
            $oembed_facebook_html .= ' id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '" data-src="//www.facebook.com/video/embed?video_id=' . $file_url . '&enablejsapi=1&wmode=transparent"';
          }
        }
        foreach ($attrs as $attr => $value) {
          if (preg_match('/src/i', $attr) === 0) {
            if ($attr != '' && $value != '') {
              $oembed_facebook_html .= ' ' . $attr . '="' . $value . '"';
            }
          }
        }
        $oembed_facebook_html .= " ></iframe>";
        $html_to_insert .= $oembed_facebook_html;
        break;
      case 'EMBED_OEMBED_DAILYMOTION_VIDEO':
        $oembed_dailymotion_html = '<iframe ';
        if ($embed_id != '') {
          if ($is_visible) {
            $oembed_dailymotion_html .= ' src="' . '//www.dailymotion.com/embed/video/' . $embed_id . '?api=postMessage"';
          } else {
            $oembed_dailymotion_html .= ' id="bwg_carousel_preload_' . $bwg . '_' . $image_key . '" data-src="' . '//www.dailymotion.com/embed/video/' . $embed_id . '?api=postMessage"';
          }
        }
        foreach ($attrs as $attr => $value) {
          if (preg_match('/src/i', $attr) === 0) {
            if ($attr != '' && $value != '') {
              $oembed_dailymotion_html .= ' ' . $attr . '="' . $value . '"';
            }
          }
        }
        $oembed_dailymotion_html .= " ></iframe>";
        $html_to_insert .= $oembed_dailymotion_html;
        break;
      default:
        // Display embed media from add-ons.
        do_action('bwg_display_embed', $embed_type, $file_url, $embed_id, $attrs);
        break;
    }

    echo $html_to_insert;
  }

  /**
   * @return json_encode(array("error","error message")) on failure
   * @return json_encode(array of data of instagram user recent posts) on success
   */
  public static function add_instagram_gallery( $access_token, $whole_post, $autogallery_image_number ) {
    @set_time_limit(0);
    $instagram_api_url = 'https://graph.instagram.com/v1.0/' . BWG()->options->instagram_user_id . '/media/?limit=100&fields=id,media_type,media_url,permalink,thumbnail_url,username,caption,timestamp&access_token=' . $access_token;
    $instagram_posts_response = wp_remote_get($instagram_api_url);
    if ( is_wp_error($instagram_posts_response) ) {
      return json_encode(array( "error", "cannot get Instagram user posts" ));
    }
    $posts_json = json_decode( wp_remote_retrieve_body( $instagram_posts_response ) );
    if ( !property_exists( $posts_json, 'data') ) {
      return json_encode(array( "error", "cannot get Instagram user posts data" ));
    }
    /*
    if instagram user has no posts
    */
    if ( empty( $posts_json->data) ) {
      return json_encode(array( "error", "Instagram user has no posts" ));
    }
    $posts_array = $posts_json->data;

    $instagram_album_data = array();
    $post_flag = '';
    if ( $whole_post == 1 ) {
      $post_flag = "post";
    }
    foreach ( $posts_array as $post_data ) {
      if( $post_data->media_type == 'CAROUSEL_ALBUM' ) {
        continue;
      }
      if ( count( $instagram_album_data ) < $autogallery_image_number ) {
        $url = $post_data->permalink . $post_flag;
        $post_to_embed = json_decode( self::add_embed( $url, $post_data ), TRUE );
        /* if add_embed function did not indexed array because of error */
        if ( !isset( $post_to_embed[0] ) ) {
          array_push( $instagram_album_data, $post_to_embed );
        }
      }
    }

    return json_encode($instagram_album_data);
  }

  public static function check_instagram_galleries(){
    global $wpdb;
    $instagram_galleries = $wpdb->get_results( "SELECT id, gallery_type, gallery_source, update_flag, autogallery_image_number  FROM " . $wpdb->prefix . "bwg_gallery WHERE gallery_type='instagram' OR gallery_type='instagram_post'", OBJECT );
       
    $galleries_to_update = array();
    if($instagram_galleries){
      foreach ($instagram_galleries as $gallery) {
        if($gallery->update_flag == 'add' || $gallery->update_flag == 'replace'){
          array_push($galleries_to_update, $gallery);
        }
      }
      if(!empty($galleries_to_update)){
        return $galleries_to_update;
      }
      else{
        return array(false, "No instagram gallery has to be updated");
      }
    }
    else{
      return array(false,"There is no instagram gallery");
    }
  }
  
  public static function refresh_social_gallery($args) {
    global $wpdb;
    $id = $args->id;
    $type = $args->gallery_type;
    $update_flag = $args->update_flag;
    $autogallery_image_number = $args->autogallery_image_number;

	  $is_instagram = false;
    if ( $type == 'instagram' ) {
      $is_instagram = TRUE;
      $whole_post = 0;
    }
    elseif ( $type == 'instagram_post' ) {
      $is_instagram = TRUE;
      $whole_post = 1;
    }

    if ( !$id || !$type ) {
      return array( FALSE, "Gallery id, type or source are empty" );
    }

	  $images_new = array();

    if ($is_instagram) {
      $instagram_access_token = BWG()->options->instagram_access_token;
      if ( !$instagram_access_token ) {
        return array(false, "Cannot get access token from the database");
      }
      $data = self::add_instagram_gallery($instagram_access_token, $whole_post, $autogallery_image_number);
      $images_new = json_decode($data);
    }
    elseif( !empty($args->images_list) ) {
		  $images_new = $args->images_list;
    }

    if ( empty($images_new) ) {
      return array(false, "Cannot get social data");
    }
    $images = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "bwg_image WHERE gallery_id = %d", $id), OBJECT);
    $images_count = sizeof($images);
    
    $images_update = array(); /*ids and orders of images existing in both arrays*/
    $images_insert = array(); /*data of new images*/
    $images_dated = array(); /*ids and orders of images not existing in the array of new images*/
    $new_order = 0; /*how many images should be added*/
    if($images_count!=0){
      $author = $images[0]->author; /* author is the same for the images in the gallery */
    }
    else{
      $author = 1; 
    }
    /*loops to compare new and existing images*/
    foreach ($images_new as $image_new) {
      $to_add = true;
      if($images_count != 0){
        foreach($images as $image){
          if($image_new->filename == $image->filename){
            /*if that image exist, do not update*/
            $to_add = false;
          }
        }
      }
      if ( $to_add ) {
        /*if image does not exist, insert*/
        $new_order++;
        $new_image_data = array(
          'gallery_id' => $id,
          'slug' => sanitize_title($image_new->name),
          'filename' => $image_new->filename,
          'image_url' => $image_new->url,
          'thumb_url' => $image_new->thumb_url,
          'description' => self::spider_replace4byte($image_new->description),
          'alt' => self::spider_replace4byte($image_new->name),
          'date' => $image_new->date_modified,
          'size' => $image_new->size,
          'filetype' => $image_new->filetype,
          'resolution' => $image_new->resolution,
          'resolution_thumb' => $image_new->resolution_thumb,
          'author' => $author,
          'order' => $new_order,
          'published' => 1,
          'comment_count' => 0,
          'avg_rating' => 0,
          'rate_count' => 0,
          'hit_count' => 0,
          'redirect_url' => $image_new->redirect_url,
        );
        array_push($images_insert, $new_image_data);
      }
    }

    if($images_count != 0) {
      foreach ($images as $image) {
        $is_dated = true;
        foreach($images_new as $image_new){
          if($image_new->filename == $image->filename){
            /* if that image exist, do not update */
            /* shift order by a number of new images */
            $image_update = array(
              'id' => $image->id ,
              'order'=> intval($image->order) + $new_order,
              "slug" => sanitize_title($image_new->name),
              "description" => $image_new->description,
              "alt" => $image_new->name,
              "date" => $image_new->date_modified);
            array_push($images_update, $image_update);
            $is_dated = false;
          }
        }
        if($is_dated){
        	$image_dated = array(
            'id' => $image->id ,
            'order'=> intval($image->order) + $new_order,
            );
          array_push($images_dated, $image_dated);
        }
      }
    }
    /*endof comparing loops*/
    
    $to_unpublish = true;
    if($update_flag == 'add'){
      $to_unpublish = false;
    }
    if($update_flag == 'replace'){
      $to_unpublish = true;
    }

    /*update old images*/
    if($images_count != 0){
		if($to_unpublish){
    		foreach ($images_dated as $image) {
    			$q = 'UPDATE ' .  $wpdb->prefix . 'bwg_image SET published=0, `order` =%s WHERE `id`=%d';
				  $wpdb->query( $wpdb->prepare($q, array($image['order'], $image['id'])) );
    		}
    	}
    	else {
    		foreach ($images_dated as $image) {
				$q = 'UPDATE ' .  $wpdb->prefix . 'bwg_image SET `order` =%s WHERE `id`=%d';
          $wpdb->query( $wpdb->prepare($q, array($image['order'], $image['id'])) );
    		}		
    	}

		foreach ($images_update as $image) {
			$save = $wpdb->update($wpdb->prefix . 'bwg_image',
        array(
			  'order' => $image['order'],
			  'slug' => self::spider_replace4byte($image['slug']),
			  'description' => self::spider_replace4byte($image['description']),
			  'alt' => self::spider_replace4byte($image['alt']),
			  'date' => $image['date']
			  ),
        array('id' => $image['id']),
        array('%s','%s','%s','%s','%s'),
        array('%d')
      );
		}
    }
		/*add new images*/
    foreach ( $images_insert as $image ) {
      $wpdb->insert($wpdb->prefix . 'bwg_image', array(
        'gallery_id' => $image['gallery_id'],
        'slug' => self::spider_replace4byte($image['slug']),
        'filename' => $image['filename'],
        'image_url' => $image['image_url'],
        'thumb_url' => $image['thumb_url'],
        'description' => self::spider_replace4byte($image['description']),
        'alt' => self::spider_replace4byte($image['alt']),
        'date' => $image['date'],
        'size' => $image['size'],
        'filetype' => $image['filetype'],
        'resolution' => $image['resolution'],
        'resolution_thumb' => $image['resolution_thumb'],
        'author' => $image['author'],
        'order' => $image['order'],
        'published' => $image['published'],
        'comment_count' => $image['comment_count'],
        'avg_rating' => $image['avg_rating'],
        'rate_count' => $image['rate_count'],
      ),
      array(
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%d',
        '%f',
        '%d',
        '%d',
        '%s',
        '%d',
        '%d',
      )
      );
    }

		$time = date('d F Y, H:i');
		/*return time of last update*/
		return array(true, $time);
	}

    /**
     * Spider replace 4 byte.
     *
     * @param $string
     * @return mixed
     */
	public static function spider_replace4byte($string) {
		return preg_replace('%(?:
			  \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
			| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
			| \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
		)%xs', '', $string);    
	}

  /**
   * Instagram oembed connect.
   *
   * @param string $url
   *
   * @return false|mixed|string
   */
  public static function instagram_oembed_connect( $url = '' ) {
    // oEmbed API 2020 connect.
    $data = new stdClass();
    $instagram_oembed_url = 'https://graph.facebook.com/v9.0/instagram_oembed/?url=' . $url . '&omitscript=true&access_token=356432828483035|0e211da32da5f501d25541fa10f4d6c0';
    $get_embed_data = wp_remote_get($instagram_oembed_url);
    if ( is_wp_error($get_embed_data) ) {
      $data->error = array( 'error', 'Instagram API connect failed.' );
      return $data;
    }
    $data = json_decode(wp_remote_retrieve_body($get_embed_data));
    if ( empty($data) ) {
      $data->error = array( 'error', wp_remote_retrieve_body($get_embed_data) );
      return $data;
    }

    return $data;
  }
}