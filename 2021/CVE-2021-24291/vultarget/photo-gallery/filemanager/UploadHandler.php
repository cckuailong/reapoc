<?php
/*
 * jQuery File Upload Plugin PHP Class 6.4.1
 * 
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
if ( function_exists('current_user_can') ) {

  if ( !current_user_can(BWG()->options->permissions) ) {
    die('Access Denied');
  }
}
else {
  die('Access Denied');
}
require_once(BWG()->plugin_dir . '/filemanager/controller.php');
$controller = new FilemanagerController();
$upload_handler = new bwg_UploadHandler(array(
                                          'upload_dir' => $controller->uploads_dir . (isset($_GET['dir']) ? str_replace('\\', '', (WDWLibrary::get('dir','','sanitize_text_field','GET'))) : '/'),
                                          'upload_url' => $controller->uploads_url,
                                          'accept_file_types' => '/\.(gif|jpe?g|png|svg|aac|m4a|f4a|oga|ogg|mp3|zip)$/i',
                                        ));

class bwg_UploadHandler {
  protected $options;
  // PHP File Upload error message codes:
  // http://php.net/manual/en/features.file-upload.errors.php
  protected $error_messages = array(
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk',
    8 => 'A PHP extension stopped the file upload',
    'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
    'max_file_size' => 'File is too big',
    'min_file_size' => 'File is too small',
    'accept_file_types' => 'Filetype not allowed',
    'max_number_of_files' => 'Maximum number of files exceeded',
    'max_width' => 'Image exceeds maximum width',
    'min_width' => 'Image requires a minimum width',
    'max_height' => 'Image exceeds maximum height',
    'min_height' => 'Image requires a minimum height',
  );

  function __construct( $options = NULL, $initialize = TRUE, $error_messages = NULL ) {

    $this->options = array(
      'media_library_folder' => 'imported_from_media_libray' . '/',
      'script_url' => $this->get_full_url() . '/',
      'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']) . '/files/',
      'upload_url' => $this->get_full_url() . '/files/',
      'user_dirs' => FALSE,
      'mkdir_mode' => 0755,
      'param_name' => 'files',
      // Set the following option to 'POST', if your server does not support
      // DELETE requests. This is a parameter sent to the client:
      'delete_type' => 'DELETE',
      'access_control_allow_origin' => '*',
      'access_control_allow_credentials' => FALSE,
      'access_control_allow_methods' => array(
        'OPTIONS',
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
      ),
      'access_control_allow_headers' => array(
        'Content-Type',
        'Content-Range',
        'Content-Disposition',
      ),
      // Enable to provide file downloads via GET requests to the PHP script:
      'download_via_php' => FALSE,
      // Defines which files can be displayed inline when downloaded:
      'inline_file_types' => '/\.(gif|jpe?g|png|svg)$/i',
      // Defines which files (based on their names) are accepted for upload:
      'accept_file_types' => '/.+$/i',
      // The php.ini settings upload_max_filesize and post_max_size
      // take precedence over the following max_file_size setting:
      'max_file_size' => NULL,
      'min_file_size' => 1,
      // The maximum number of files for the upload directory:
      'max_number_of_files' => NULL,
      // Image resolution restrictions:
      'max_width' => (isset($_POST['upload_img_width']) ? WDWLibrary::get('upload_img_width','','intval','POST') : BWG()->options->upload_img_width),
      'max_height' => (isset($_POST['upload_img_height']) ? WDWLibrary::get('upload_img_height','','intval','POST') : BWG()->options->upload_img_height),
      'min_width' => 1,
      'min_height' => 1,
      // Set the following option to false to enable resumable uploads:
      'discard_aborted_uploads' => TRUE,
      // Set to true to rotate images based on EXIF meta data, if available:
      'orient_image' => TRUE,
    );
    if ( !$this->options['max_width'] || !$this->options['max_height'] ) {
      $this->options['max_width'] = NULL;
      $this->options['max_height'] = NULL;
    }
    $this->options += array(
      'image_versions' => array(
        '.original' => array(
          'max_width' => NULL,
          'max_height' => NULL,
          'jpeg_quality' => BWG()->options->jpeg_quality,
        ),
        '' => array(
          'max_width' => $this->options['max_width'],
          'max_height' => $this->options['max_height'],
          'jpeg_quality' => BWG()->options->jpeg_quality,
        ),
        'thumb' => array(
          'max_width' => ((isset($_POST['upload_thumb_width']) && WDWLibrary::get('upload_thumb_width',0,'intval','POST')) ? WDWLibrary::get('upload_thumb_width',0,'intval','POST') : BWG()->options->upload_thumb_width),
          'max_height' => ((isset($_POST['upload_thumb_height']) && WDWLibrary::get('upload_thumb_height',0,'intval','POST')) ? WDWLibrary::get('upload_thumb_height',0,'intval','POST') : BWG()->options->upload_thumb_height),
          'jpeg_quality' => BWG()->options->jpeg_quality,
        ),
      ),
    );
    $this->options['max_width'] = NULL;
    $this->options['max_height'] = NULL;
    if ( $options ) {
      $this->options = array_merge($this->options, $options);
    }

    if ( $error_messages ) {
      $this->error_messages = array_merge($this->error_messages, $error_messages);
    }
    if ( $initialize ) {
      $this->initialize();
    }
  }

  protected function initialize() {
    switch ( $_SERVER['REQUEST_METHOD'] ) {
      case 'OPTIONS':
      case 'HEAD':
        $this->head();
        break;
      case 'GET':
        $this->get();
        break;
      case 'PATCH':
      case 'PUT':
      case 'POST':
        $this->post();
        break;
      case 'DELETE':
        $this->delete();
        break;
      default:
        $this->header('HTTP/1.1 405 Method Not Allowed');
    }
  }

  protected function get_full_url() {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    return ($https ? 'https://' : 'http://') . (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . ($https && $_SERVER['SERVER_PORT'] === 443 || $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) . substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
  }

  protected function get_user_id() {
    WDWLibrary::bwg_session_start();

    return session_id();
  }

  protected function get_user_path() {
    if ( $this->options['user_dirs'] ) {
      return $this->get_user_id() . '/';
    }

    return '';
  }

  protected function get_upload_path( $file_name = NULL, $version = NULL ) {
    $file_name = $file_name ? $file_name : '';
    $version_path = empty($version) ? '' : $version . '/';
    $media_library_folder = (isset($_REQUEST['import']) && WDWLibrary::get('import',0,'intval','REQUEST') == 1) ? $this->options['media_library_folder'] : '';

    return $this->options['upload_dir'] . $media_library_folder . $this->get_user_path() . $version_path . $file_name;
  }

  protected function get_query_separator( $url ) {
    return strpos($url, '?') === FALSE ? '?' : '&';
  }

  protected function get_download_url( $file, $version = NULL ) {
    if ( $this->options['download_via_php'] ) {
      $url = $this->options['script_url'] . $this->get_query_separator($this->options['script_url']) . 'file=' . rawurlencode($file->name);
      if ( $version ) {
        $url .= '&version=' . rawurlencode($version);
      }

      return $url . '&download=1';
    }
    $version_path = empty($version) ? '' : rawurlencode($version) . '/';
    $file_path = !empty($file->path) ? $file->path : '';
    $url = $this->options['upload_url'] . $this->get_user_path() . $file_path . $version_path . rawurlencode($file->name);

    return $url;
  }

  protected function set_file_delete_properties( $file ) {
    $file->delete_url = $this->options['script_url'] . $this->get_query_separator($this->options['script_url']) . 'file=' . rawurlencode($file->name);
    $file->delete_type = $this->options['delete_type'];
    if ( $file->delete_type !== 'DELETE' ) {
      $file->delete_url .= '&_method=DELETE';
    }
    if ( $this->options['access_control_allow_credentials'] ) {
      $file->delete_with_credentials = TRUE;
    }
  }

  // Fix for overflowing signed 32 bit integers,
  // works for sizes up to 2^32-1 bytes (4 GiB - 1):
  protected function fix_integer_overflow( $size ) {
    if ( $size < 0 ) {
      $size += 2.0 * (PHP_INT_MAX + 1);
    }

    return $size;
  }

  protected function get_file_size( $file_path, $clear_stat_cache = FALSE ) {
    /*if ($clear_stat_cache) {
      clearstatcache(true, $file_path);
    }*/
    return $this->fix_integer_overflow(filesize($file_path));
  }

  protected function is_valid_file_object( $file_name ) {
    $file_path = $this->get_upload_path($file_name);
    if ( is_file($file_path) && $file_name[0] !== '.' ) {
      return TRUE;
    }

    return FALSE;
  }

  protected function get_file_object( $file_name ) {
    if ( $this->is_valid_file_object($file_name) ) {
      $file = new stdClass();
      $file->name = $file_name;
      $file->size = $this->get_file_size($this->get_upload_path($file_name));
      $file->url = $this->get_download_url($file);
      foreach ( $this->options['image_versions'] as $version => $options ) {
        if ( !empty($version) ) {
          if ( is_file($this->get_upload_path($file_name, $version)) ) {
            $file->{$version . '_url'} = $this->get_download_url($file, $version);
          }
        }
      }
      $this->set_file_delete_properties($file);

      return $file;
    }

    return NULL;
  }

  protected function get_file_objects( $iteration_method = 'get_file_object' ) {
    $upload_dir = $this->get_upload_path();
    if ( !is_dir($upload_dir) ) {
      return array();
    }

    return array_values(array_filter(array_map(array( $this, $iteration_method ), scandir($upload_dir))));
  }

  protected function count_file_objects() {
    return count($this->get_file_objects('is_valid_file_object'));
  }

  protected function create_scaled_image( $file_name, $version, $options ) {
    $file_path = $this->get_upload_path($file_name);
    if ( !empty($version) && ($version != 'main') ) {
      $version_dir = $this->get_upload_path(NULL, $version);
      if ( !is_dir($version_dir) ) {
        mkdir($version_dir, $this->options['mkdir_mode'], TRUE);
      }
      $new_file_path = $version_dir . '/' . $file_name;
    }
    else {
      $new_file_path = $file_path;
    }

    $success = WDWLibrary::resize_image($file_path, $new_file_path, $options['max_width'], $options['max_height']);

    return $success;
  }

  protected function get_error_message( $error ) {
    return array_key_exists($error, $this->error_messages) ? $this->error_messages[$error] : $error;
  }

  function get_config_bytes( $val ) {
    $val = trim($val);
    $int_val = intval($val);
    $last = strtolower($val[strlen($val) - 1]);
    switch ( $last ) {
      case 'g':
        $int_val *= 1024;
      case 'm':
        $int_val *= 1024;
      case 'k':
        $int_val *= 1024;
    }

    return $this->fix_integer_overflow($int_val);
  }

  protected function validate( $uploaded_file, $file, $error, $index ) {
    if ( $error ) {
      $file->error = $this->get_error_message($error);

      return FALSE;
    }
    $content_length = $this->fix_integer_overflow(intval($_SERVER['CONTENT_LENGTH']));
    $post_max_size = $this->get_config_bytes(ini_get('post_max_size'));
    if ( $post_max_size && ($content_length > $post_max_size) ) {
      $file->error = $this->get_error_message('post_max_size');

      return FALSE;
    }
    if ( !preg_match($this->options['accept_file_types'], $file->name) ) {
      $file->error = $this->get_error_message('accept_file_types');

      return FALSE;
    }
    if ( $uploaded_file && is_uploaded_file($uploaded_file) ) {
      $file_size = $this->get_file_size($uploaded_file);
    }
    else {
      $file_size = $content_length;
    }
    if ( $this->options['max_file_size'] && ($file_size > $this->options['max_file_size'] || $file->size > $this->options['max_file_size']) ) {
      $file->error = $this->get_error_message('max_file_size');

      return FALSE;
    }
    if ( $this->options['min_file_size'] && $file_size < $this->options['min_file_size'] ) {
      $file->error = $this->get_error_message('min_file_size');

      return FALSE;
    }
    if ( is_int($this->options['max_number_of_files']) && ($this->count_file_objects() >= $this->options['max_number_of_files']) ) {
      $file->error = $this->get_error_message('max_number_of_files');

      return FALSE;
    }
    list($img_width, $img_height) = @getimagesize(htmlspecialchars_decode($uploaded_file, ENT_COMPAT | ENT_QUOTES));
    if ( is_int($img_width) ) {
      // if ($this->options['max_width'] && $img_width > $this->options['max_width']) {
      // $file->error = $this->get_error_message('max_width');
      // return false;
      // }
      // if ($this->options['max_height'] && $img_height > $this->options['max_height']) {
      // $file->error = $this->get_error_message('max_height');
      // return false;
      // }
      if ( $this->options['min_width'] && $img_width < $this->options['min_width'] ) {
        $file->error = $this->get_error_message('min_width');

        return FALSE;
      }
      if ( $this->options['min_height'] && $img_height < $this->options['min_height'] ) {
        $file->error = $this->get_error_message('min_height');

        return FALSE;
      }
    }

    return TRUE;
  }

  protected function upcount_name_callback( $matches ) {
    $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
    $ext = isset($matches[2]) ? $matches[2] : '';

    return '_(' . $index . ')' . $ext;
  }

  protected function upcount_name( $name ) {
    return preg_replace_callback('/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/', array(
      $this,
      'upcount_name_callback',
    ), $name, 1);
  }

  protected function get_unique_filename( $name, $type, $index, $content_range ) {
    while ( is_dir($this->get_upload_path($name)) ) {
      $name = $this->upcount_name($name);
    }
    // Keep an existing filename if this is part of a chunked upload:
    $uploaded_bytes = $this->fix_integer_overflow(intval(isset($content_range[1]) ? $content_range[1] : 0));
    while ( is_file($this->get_upload_path($name)) ) {
      if ( $uploaded_bytes === $this->get_file_size($this->get_upload_path($name)) ) {
        break;
      }
      $name = $this->upcount_name($name);
    }

    return $name;
  }

  protected function trim_file_name( $name, $type, $index, $content_range ) {
    // Remove path information and dots around the filename, to prevent uploading
    // into different directories or replacing hidden system files.
    // Also remove control characters and spaces (\x00..\x20) around the filename:
    $name = trim(stripslashes($name), ".\x00..\x20");
    $name = str_replace(array( " ", '%', '&' ), array( "_", '', '' ), $name);
    $tempname = explode(".", $name);
    if ( $tempname[0] == '' ) {
      $tempname[0] = 'unnamed-file';
      $name = $tempname[0] . "." . $tempname[1];
    }
    // Use a timestamp for empty filenames:
    if ( !$name ) {
      $name = str_replace('.', '-', microtime(TRUE));
    }
    // Add missing file extension for known image types:
    if ( strpos($name, '.') === FALSE && preg_match('/^image\/(gif|jpe?g|png|svg)/', $type, $matches) ) {
      $name .= '.' . $matches[1];
    }

    return $name;
  }

  protected function get_file_name( $name, $type, $index, $content_range ) {
    return $this->get_unique_filename($this->trim_file_name($name, $type, $index, $content_range), $type, $index, $content_range);
  }

  protected function handle_form_data( $file, $index ) {
    // Handle form data, e.g. $_REQUEST['description'][$index]
  }

  protected function orient_image( $file ) {
    if ( !function_exists('exif_read_data') ) {
      return FALSE;
    }
    $path = isset($file->path) ? $file->path : "/";
    $file_path = BWG()->upload_dir . $path . $file->name;
    $exif = @exif_read_data($file_path);
    if ( $exif === FALSE ) {
      return;
    }
    if ( $exif['ExposureProgram'] == 7 ) {
      /* 7 for portrait. */
      if ( $exif['Orientation'] == 6 ) {
        return;
      }
      $rotate = 270;
    }
    else {
      /* 8 for landscape. */
      if ( $exif['Orientation'] == 1 ) {
        return;
      }
      $rotate = 0;
    }
    @ini_set('memory_limit', '-1');
    if ( strpos($file->type, 'jp') !== FALSE ) {
      $source = imagecreatefromjpeg($file_path);
      $image = @imagerotate($source, $rotate, 0);
      imagejpeg($source, $file_path);
      imagedestroy($source);
      imagedestroy($image);
    }
    elseif ( strpos($file->type, 'png') !== FALSE ) {
      $source = imagecreatefrompng($file_path);
      imagealphablending($source, FALSE);
      imagesavealpha($source, TRUE);
      $image = imagerotate($source, $rotate, imageColorAllocateAlpha($source, 0, 0, 0, 127));
      imagealphablending($image, FALSE);
      imagesavealpha($image, TRUE);
      imagepng($image, $file_path, BWG()->options->png_quality);
      imagedestroy($source);
      imagedestroy($image);
    }
    elseif ( strpos($file->type, 'gif') !== FALSE ) {
      $source = imagecreatefromgif($file_path);
      imagealphablending($source, FALSE);
      imagesavealpha($source, TRUE);
      $image = imagerotate($source, $rotate, imageColorAllocateAlpha($source, 0, 0, 0, 127));
      imagealphablending($image, FALSE);
      imagesavealpha($image, TRUE);
      imagegif($image, $file_path, BWG()->options->png_quality);
      imagedestroy($source);
      imagedestroy($image);
    }
    @ini_restore('memory_limit');
  }

  protected function handle_image_file( $file_path, $file ) {
    $failed_versions = array();

      foreach ( $this->options['image_versions'] as $version => $options ) {
        if ( $this->create_scaled_image($file->name, $version, $options) ) {
          if ( $version === '' && $this->options['orient_image'] ) {
            // Rotate only base size image (not thumb and original).
            $this->orient_image($file);
          }
          if ( !empty($version) ) {
            $file->{$version . '_url'} = $this->get_download_url($file, $version);
          }
          else {
            $file->size = $this->get_file_size($file_path, TRUE);
          }
        }
        else {
          $failed_versions[] = $version;
        }
      }


    switch ( count($failed_versions) ) {
      case 0:
        break;
      case 1:
        $file->error = 'Failed to create scaled version: ' . $failed_versions[0];
        break;
      default:
        $file->error = 'Failed to create scaled versions: ' . implode(', ', $failed_versions);
    }

    if ( !$file->error ) {
	 global $wpdb;
	 $file->filename = str_replace("_", " ", substr($file->name, 0, strrpos($file->name, '.')));
	 $file_ex = explode('.', $file->name);
	 $file->type = strtolower(end($file_ex));
	 $file->thumb = $file->name;
	 $file->size = (int) ($file->size / 1024) . ' KB';
	 // ini_set('allow_url_fopen',1);
	 $image_info = @getimagesize(htmlspecialchars_decode($file->url, ENT_COMPAT | ENT_QUOTES));
	 if ( $file->type == 'svg' ) {
	   $size = $this->get_svg_size($file->dir . $file->name);
	   if ( !empty($size) ) {
		$file->resolution = $size['width'] . " x " . $size['height'] . " px ";
	   }
	   else {
		$file->resolution = "";
	   }
	 }
	 else {
	   $file->resolution = $image_info[0] . ' x ' . $image_info[1] . ' px';
	 }
	 if ( BWG()->options->read_metadata ) {
	   $meta = WDWLibrary::read_image_metadata($file->dir . '/.original/' . $file->name);
	   $file->alt = ($meta['title']) ? $meta['title'] : str_replace("_", " ", $file->filename);
	   $file->credit = isset($meta['credit']) ? $meta['credit'] : '';
	   $file->aperture = isset($meta['aperture']) ? $meta['aperture'] : '';
	   $file->camera = isset($meta['camera']) ? $meta['camera'] : '';
	   $file->caption = isset($meta['caption']) ? $meta['caption'] : '';
	   $file->iso = isset($meta['iso']) ? $meta['iso'] : '';
	   $file->orientation = isset($meta['orientation']) ? $meta['orientation'] : '';
	   $file->copyright = isset($meta['copyright']) ? $meta['copyright'] : '';
	   $file->tags = isset($meta['tags']) ? $meta['tags'] : '';
	 }
      $wpdb->insert($wpdb->prefix . 'bwg_file_paths', $this->set_file_info($file));
    }
  }

  protected function handle_zip_file( $file_path, $file ) {
    $zip = new ZipArchive;
    $res = $zip->open($file_path);
    if ( $res === TRUE ) {
      $allow_extract = TRUE;
      for ( $i = 0; $i < $zip->numFiles; $i++ ) {
        $OnlyFileName = $zip->getNameIndex($i);
        $FullFileName = $zip->statIndex($i);
        if ( !($FullFileName['name'][strlen($FullFileName['name']) - 1] == "/") ) {
          if ( !preg_match('#\.(gif|jpe?g|png|svg|bmp|mp4|flv|webm|ogg|mp3|wav|pdf|ini|txt)$#i', $OnlyFileName) ) {
            $allow_extract = FALSE;
          }
        }
      }
      if ( $allow_extract ) {
        $target_dir = substr($file_path, 0, strlen($file_path) - 4);
        if ( !is_dir($target_dir) ) {
          mkdir($target_dir, 0755);
        }
        $zip->extractTo($target_dir);
      }
      else {
        $file->error = 'Zip file should contain only image files.';
      }
      $zip->close();
      if ( $allow_extract ) {
        global $wpdb;
        $folder = new stdClass();
        $folder_name = pathinfo($file->name, PATHINFO_FILENAME);
        $folder->path = '/';
        $folder->name = $folder_name;
        $folder->filename = $folder_name;
        $folder->alt = $folder_name;
        $wpdb->insert($wpdb->prefix . 'bwg_file_paths', $this->set_folder_info($folder));
        $this->handle_directory($target_dir);
      }
    }
    unlink($file_path);

    return $file->error;
  }

  protected function handle_directory( $target_dir ) {
    $extracted_files = scandir($target_dir);
    if ( $extracted_files ) {
      $temp_upload_dir = $this->options['upload_dir'];
      $this->options['upload_dir'] = $target_dir . '/';
      foreach ( $extracted_files as $ex_file ) {
        if ( $ex_file != '.' && $ex_file != '..' ) {
          $ex_file = $target_dir . '/' . $ex_file;
          rename($ex_file, str_replace(array( " ", "%" ), array( "_", "" ), $ex_file));
          $ex_file = str_replace(array( " ", "%" ), array( "_", "" ), $ex_file);
          if ( is_file($ex_file) ) {
            $type = filetype($ex_file);
            $name = basename($ex_file);
            $extension = explode(".", $name);
            $extension = end($extension);
            $name = str_replace('.' . $extension, strtolower('.' . $extension), $name);
            $index = NULL;
            $content_range = NULL;
            $size = $this->get_file_size($ex_file);
            $file = new stdClass();
            $file->dir = $this->get_upload_path();
            $file->name = $name;
            $file->path = "/" . trailingslashit(pathinfo($target_dir, PATHINFO_FILENAME));
            $file->size = $this->fix_integer_overflow(intval($size));
            $file->type = $type;
            $file->url = $this->get_download_url($file);
            list($img_width, $img_height) = @getimagesize(htmlspecialchars_decode($ex_file, ENT_COMPAT | ENT_QUOTES));
            if ( $this->options['max_width'] && $this->options['max_height'] ) {
              // Zip Upload.
              $this->create_scaled_image($file->name, 'main', $this->options);
            }
            if ( is_int($img_width) ) {
              $file->error = FALSE;
              $this->handle_image_file($ex_file, $file);
            }
          }
          elseif ( is_dir($ex_file) ) {
            $this->handle_directory($ex_file);
          }
        }
      }
      $this->options['upload_dir'] = $temp_upload_dir;
    }
  }

  protected function handle_file_import( $uploaded_file, $name ) {
    $parent_dir = wp_upload_dir();
    $basedir = $parent_dir['basedir'];
    $file_type_array = explode('.', $name);
    $type = strtolower(end($file_type_array));
    $file = new stdClass();
    if ( WDWLibrary::allowed_upload_types($type) ) {
      $file->dir = $this->get_upload_path();
      $file->error = FALSE;
      $file->name = $name;
      $file->type = $type;
      $this->handle_form_data($file, 0);
      $upload_dir = $this->get_upload_path();
      if ( !is_dir($upload_dir) ) {
        mkdir($upload_dir, $this->options['mkdir_mode'], TRUE);
      }
      $file_path = $this->get_upload_path($file->name);

      if ( ! file_exists( $file_path ) ) {
        copy($basedir . '/' . $uploaded_file, $file_path);

        if ( $this->options['max_width'] && $this->options['max_height']  && $type != 'svg' ) {
          // Media library Upload.
          $this->create_scaled_image($file->name, 'main', $this->options);
        } else {
          $thumb_path = $this->get_upload_path($file->name, 'thumb');
          copy($basedir . '/' . $uploaded_file, $thumb_path);
        }

        list($img_width) = @getimagesize(htmlspecialchars_decode($file_path, ENT_COMPAT | ENT_QUOTES));
        if ( is_int($img_width) ) {
          $this->handle_image_file($file_path, $file);
        }
        $this->set_file_delete_properties($file);
        $file->dublicate = false;
      }
      else {
        $file->dublicate = true;
      }

      // Additional information.
      $file->path = '/' . $this->options['media_library_folder'];
      $file->filetype = $type;
      $file->filename = str_replace('.' . $file->filetype, '', $file->name);
      $file->alt = $file->filename;
      $file->reliative_url = $this->options['upload_url'] . '/' . $this->options['media_library_folder'] . $file->name;
      $file->url = '/' . $this->options['media_library_folder'] . $file->name;
      $file->thumb = $this->options['upload_url'] . '/' . $this->options['media_library_folder'] . 'thumb/' . $file->name;
      $file->thumb_url = '/' . $this->options['media_library_folder'] . 'thumb/' . $file->name;

      $file_size_kb = (int) (filesize($file_path) / 1024);
      $file->size = $file_size_kb . ' KB';
      $file->date_modified = date('Y-m-d H:i:s', filemtime($file_path));
      $image_info = @getimagesize(htmlspecialchars_decode($file_path, ENT_COMPAT | ENT_QUOTES));

      if ( $type == 'svg' ) {
        $size = $this->get_svg_size($file->dir.$file->name);
          if( !empty($size) ) {
            $file->resolution = $size['width'] . " x " . $size['height'] . " px ";
            $file->resolution_thumb = $size['width'] . "x" . $size['height'];
          } else {
            $file->resolution = "";
            $file->resolution_thumb = "";
          }
      } else {
          $file->resolution = $image_info[0] . ' x ' . $image_info[1] . ' px';
          $file->resolution_thumb = WDWLibrary::get_thumb_size($file->thumb_url);
      }
      if ( BWG()->options->read_metadata ) {
        $meta = WDWLibrary::read_image_metadata($upload_dir . '.original/' . $file->name);
        $file->credit = isset($meta['credit']) ? $meta['credit'] : "";
        $file->aperture = isset($meta['aperture']) ? $meta['aperture'] : "";
        $file->camera = isset($meta['camera']) ? $meta['camera'] : "";
        $file->caption = isset($meta['caption']) ? $meta['caption'] : "";
        $file->iso = isset($meta['iso']) ? $meta['iso'] : "";
        $file->orientation = isset($meta['orientation']) ? $meta['orientation'] : "";
        $file->copyright = isset($meta['copyright']) ? $meta['copyright'] : "";
        $file->alt = $meta['title'] ? $meta['title'] : $file->filename;
        $file->tags = isset($meta['tags']) ? $meta['tags'] : "";
      }
    }
    else {
      $file->error = TRUE;
    }

    return $file;
  }

  protected function handle_file_upload( $uploaded_file, $name, $size, $type, $error, $index = NULL, $content_range = NULL, $path = '' ) {
    $file = new stdClass();
    $file->dir = $this->get_upload_path();
    $file->path = $path;
    $file->name = $this->get_file_name($name, $type, $index, $content_range);
    $file->size = $this->fix_integer_overflow(intval($size));
    $file->type = $type;
    if ( $this->validate($uploaded_file, $file, $error, $index) ) {
      $this->handle_form_data($file, $index);
      $upload_dir = $this->get_upload_path();
      if ( !is_dir($upload_dir) ) {
        mkdir($upload_dir, $this->options['mkdir_mode'], TRUE);
      }
      $file_path = $this->get_upload_path($file->name);
      $append_file = $content_range && is_file($file_path) && $file->size = $this->get_file_size($file_path);
      if ( $uploaded_file && is_uploaded_file($uploaded_file) ) {
        // multipart/formdata uploads (POST method uploads)
        if ( $append_file ) {
          file_put_contents($file_path, fopen($uploaded_file, 'r'), FILE_APPEND);
        }
        else {
          move_uploaded_file($uploaded_file, $file_path);
        }
      }
      else {
        // Non-multipart uploads (PUT method support)
        file_put_contents($file_path, fopen('php://input', 'r'), $append_file ? FILE_APPEND : 0);
      }

      $file_size = $this->get_file_size($file_path, $append_file);
      if ( $file_size === $file->size ) {
        if ( $this->options['max_width'] && $this->options['max_height'] ) {
          // Upload.
          $this->create_scaled_image($file->name, 'main', $this->options);
        }
        $file->url = $this->get_download_url($file);
        list($img_width, $img_height) = @getimagesize(htmlspecialchars_decode($file_path, ENT_COMPAT | ENT_QUOTES));
        if ( is_int($img_width) || $type == "image/svg+xml" ) {
          $file->error = FALSE;
          $this->handle_image_file($file_path, $file);
        }
        else {
          $file->error = $this->handle_zip_file($file_path, $file);
        }
      }
      else {
        $file->size = $file_size;
        if ( !$content_range && $this->options['discard_aborted_uploads'] ) {
          unlink($file_path);
          $file->error = 'abort';
        }
      }
      $this->set_file_delete_properties($file);
    }

    return $file;
  }

  public function get_svg_size( $image ) {

    $xml = simplexml_load_file($image);
    $attr = $xml->attributes();

    $size = array(
      'width' => json_decode($attr->width),
      'height'=> json_decode($attr->height),
    );

    return $size;
  }

  protected function readfile( $file_path ) {
    return readfile($file_path);
  }

  protected function body( $str ) {
    echo $str;
  }

  protected function header( $str ) {
    header($str);
  }

  protected function generate_response( $content, $print_response = TRUE ) {
    if ( $print_response ) {
      $json = json_encode($content);
      $redirect = isset($_REQUEST['redirect']) ? WDWLibrary::get('redirect','','sanitize_text_field','REQUEST') : NULL;
      if ( $redirect ) {
        $this->header('Location: ' . sprintf($redirect, rawurlencode($json)));

        return;
      }
      $this->head();
      if ( isset($_SERVER['HTTP_CONTENT_RANGE']) ) {
        $files = isset($content[$this->options['param_name']]) ? $content[$this->options['param_name']] : NULL;
        if ( $files && is_array($files) && is_object($files[0]) && $files[0]->size ) {
          $this->header('Range: 0-' . ($this->fix_integer_overflow(intval($files[0]->size)) - 1));
        }
      }
      $this->body($json);
    }

    return $content;
  }

  protected function get_version_param() {
    return isset($_GET['version']) ? basename(stripslashes(WDWLibrary::get('version','','sanitize_text_field','GET'))) : NULL;
  }

  protected function get_file_name_param() {
    return isset($_GET['file']) ? basename(stripslashes(WDWLibrary::get('file','','sanitize_text_field','GET'))) : NULL;
  }

  protected function get_file_type( $file_path ) {
    switch ( strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) ) {
      case 'jpeg':
      case 'jpg':
        return 'image/jpeg';
      case 'png':
        return 'image/png';
      case 'gif':
        return 'image/gif';
      case 'svg':
        return 'image/svg';
      default:
        return '';
    }
  }

  protected function download() {
    if ( !$this->options['download_via_php'] ) {
      $this->header('HTTP/1.1 403 Forbidden');

      return;
    }
    $file_name = $this->get_file_name_param();
    if ( $this->is_valid_file_object($file_name) ) {
      $file_path = $this->get_upload_path($file_name, $this->get_version_param());
      if ( is_file($file_path) ) {
        if ( !preg_match($this->options['inline_file_types'], $file_name) ) {
          $this->header('Content-Description: File Transfer');
          $this->header('Content-Type: application/octet-stream');
          $this->header('Content-Disposition: attachment; filename="' . $file_name . '"');
          $this->header('Content-Transfer-Encoding: binary');
        }
        else {
          // Prevent Internet Explorer from MIME-sniffing the content-type:
          $this->header('X-Content-Type-Options: nosniff');
          $this->header('Content-Type: ' . $this->get_file_type($file_path));
          $this->header('Content-Disposition: inline; filename="' . $file_name . '"');
        }
        $this->header('Content-Length: ' . $this->get_file_size($file_path));
        $this->header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($file_path)));
        $this->readfile($file_path);
      }
    }
  }

  protected function send_content_type_header() {
    $this->header('Vary: Accept');
    if ( isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== FALSE) ) {
      $this->header('Content-type: application/json');
    }
    else {
      $this->header('Content-type: text/plain');
    }
  }

  protected function send_access_control_headers() {
    $this->header('Access-Control-Allow-Origin: ' . $this->options['access_control_allow_origin']);
    $this->header('Access-Control-Allow-Credentials: ' . ($this->options['access_control_allow_credentials'] ? 'true' : 'false'));
    $this->header('Access-Control-Allow-Methods: ' . implode(', ', $this->options['access_control_allow_methods']));
    $this->header('Access-Control-Allow-Headers: ' . implode(', ', $this->options['access_control_allow_headers']));
  }

  public function head() {
    $this->header('Pragma: no-cache');
    $this->header('Cache-Control: no-store, no-cache, must-revalidate');
    $this->header('Content-Disposition: inline; filename="files.json"');
    // Prevent Internet Explorer from MIME-sniffing the content-type:
    $this->header('X-Content-Type-Options: nosniff');
    if ( $this->options['access_control_allow_origin'] ) {
      $this->send_access_control_headers();
    }
    $this->send_content_type_header();
  }

  public function get( $print_response = TRUE ) {
    if ( isset($_GET['import']) && WDWLibrary::get('import',0,'intval','GET') == 1 ) {
      $file_names = json_decode(isset($_REQUEST['file_namesML']) ? stripslashes(WDWLibrary::get('file_namesML','','sanitize_text_field','REQUEST')) : '');
      $files = array();
      foreach ( $file_names as $index => $value ) {
        $file_name_array = explode('/', $value);
        $files[] = $this->handle_file_import($value, end($file_name_array));
      }
      echo json_encode($files);

      return;
    }
    if ( $print_response && isset($_GET['download']) ) {
      return $this->download();
    }
    $file_name = $this->get_file_name_param();
    if ( $file_name ) {
      $response = array(
        substr($this->options['param_name'], 0, -1) => $this->get_file_object($file_name),
      );
    }
    else {
      $response = array(
        $this->options['param_name'] => $this->get_file_objects(),
      );
    }

    return $this->generate_response($response, $print_response);
  }

  public function post( $print_response = TRUE ) {
    global $wpdb;
    $path = isset($_REQUEST['dir']) ? str_replace('\\', '', (WDWLibrary::get('dir','','sanitize_text_field','REQUEST'))) . '/' : '/';
    if ( isset($_REQUEST['import']) && WDWLibrary::get('import',0,'intval','REQUEST') == 1 ) {
      $files = array();
      $file_names = json_decode(isset($_REQUEST['file_namesML']) ? stripslashes(WDWLibrary::get('file_namesML','','sanitize_text_field','REQUEST')) : array());
      if ( !empty($file_names) ) {
        // Create IMPORTED_FROM_MEDIA_LIBRAY folder.
        if ( !is_dir($this->get_upload_path()) ) {
          $folder = new stdClass();
          $folder_name = trim($this->options['media_library_folder'], '/');
          $folder->path = '/';
          $folder->name = $folder_name;
          $folder->filename = $folder_name;
          $folder->alt = $folder_name;
          $wpdb->insert($wpdb->prefix . 'bwg_file_paths', $this->set_folder_info($folder));
        }
        // Adding images on IMPORTED_FROM_MEDIA_LIBRAY folder.
        foreach ( $file_names as $index => $value ) {
          $file_name_array = explode('/', $value);
          $file_info = $this->handle_file_import($value, end($file_name_array));
          $files[] = $file_info;
          if ( empty($file_info->error) && !$file_info->dublicate) {
            $wpdb->insert($wpdb->prefix . 'bwg_file_paths', $this->set_file_info($file_info));
          }
        }
        echo json_encode($files);
      }

      return;
    }
    if ( isset($_REQUEST['_method']) && WDWLibrary::get('_method','','sanitize_text_field','REQUEST') === 'DELETE' ) {
      return $this->delete($print_response);
    }
    $upload = isset($_FILES[$this->options['param_name']]) ? $_FILES[$this->options['param_name']] : NULL;
    $files = array();
    // Parse the Content-Disposition header, if available:
    $file_name = isset($_SERVER['HTTP_CONTENT_DISPOSITION']) ? rawurldecode(preg_replace('/(^[^"]+")|("$)/', '', $_SERVER['HTTP_CONTENT_DISPOSITION'])) : NULL;
    // Parse the Content-Range header, which has the following form:
    // Content-Range: bytes 0-524287/2000000
    $content_range = isset($_SERVER['HTTP_CONTENT_RANGE']) ? preg_split('/[^0-9]+/', $_SERVER['HTTP_CONTENT_RANGE']) : NULL;
    $size = $content_range ? $content_range[3] : NULL;
    if ( $upload && is_array($upload['tmp_name']) ) {
      // param_name is an array identifier like "files[]",
      // $_FILES is a multi-dimensional array:
      foreach ( $upload['tmp_name'] as $index => $value ) {
        $filename = $file_name ? $file_name : $upload['name'][$index];
        $extension = explode(".", $filename);
        $extension = end($extension);
        $filename = str_replace('.' . $extension, strtolower('.' . $extension), $filename);
        $files[] = $this->handle_file_upload($upload['tmp_name'][$index], $filename, $size ? $size : $upload['size'][$index], $upload['type'][$index], $upload['error'][$index], $index, $content_range, $path);
      }
    }
    else {
      $filename = $file_name ? $file_name : (isset($upload['name']) ? $upload['name'] : NULL);
      $extension = explode(".", $filename);
      $extension = end($extension);
      $filename = str_replace('.' . $extension, strtolower('.' . $extension), $filename);
      // param_name is a single object identifier like "file",
      // $_FILES is a one-dimensional array:
      $files[] = $this->handle_file_upload(isset($upload['tmp_name']) ? $upload['tmp_name'] : NULL, $filename, $size ? $size : (isset($upload['size']) ? $upload['size'] : $_SERVER['CONTENT_LENGTH']), isset($upload['type']) ? $upload['type'] : $_SERVER['CONTENT_TYPE'], isset($upload['error']) ? $upload['error'] : NULL, NULL, $content_range, $path);
    }
    return $this->generate_response(array( $this->options['param_name'] => $files ), $print_response);
  }

  public function delete( $print_response = TRUE ) {
    $file_name = $this->get_file_name_param();
    $file_path = $this->get_upload_path($file_name);
    $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
    if ( $success ) {
      foreach ( $this->options['image_versions'] as $version => $options ) {
        if ( !empty($version) ) {
          $file = $this->get_upload_path($file_name, $version);
          if ( is_file($file) ) {
            unlink($file);
          }
        }
      }
    }

    return $this->generate_response(array( 'success' => $success ), $print_response);
  }

  /**
   * Set folder info
   *
   * @param $info
   *
   * @return mixed
   */
  private function set_folder_info( $info ) {
    $data['is_dir'] = 1;
    $data['path'] = $info->path;
    $data['name'] = $info->name;
    $data['filename'] = $info->name;
    $data['thumb'] = '/filemanager/images/dir.png';
    $data['alt'] = $info->alt;
    $data['date_modified'] = date("Y-m-d H:i:s");
    $data['author'] = get_current_user_id();

    return $data;
  }

  /**
   * Set file info.
   *
   * @param $info
   *
   * @return mixed
   */
  private function set_file_info( $info ) {
    $iconv_mime_decode_function_exist = FALSE;
    if ( function_exists('iconv_mime_decode') ) {
      $iconv_mime_decode_function_exist = TRUE;
    }

    $data = array();
    $data['is_dir'] = 0;
    $data['date_modified'] = date('Y-m-d H:i:s');
    $data['path'] = isset($info->path) ? $info->path : '';
    $data['type'] = isset($info->type) ? $info->type : '';
    $data['name'] = isset($info->name) ? $info->name : '';
    $data['filename'] = isset($info->filename) ? $info->filename : '';
    $data['alt'] = isset($info->alt) ? $info->alt : '';
    $data['thumb'] = isset($info->name) ? 'thumb/' . $info->name : '';
    $data['size'] = isset($info->size) ? $info->size : '';
    $data['author'] = get_current_user_id();

    if ( $data['type'] == 'svg') {
        $size = $this->get_svg_size($info->dir.$data['name']);
        if( !empty($size['width']) && !empty($size['height']) ) {
            $data['resolution'] = $size['width'] . " x " . $size['height'] . " px ";
            $data['resolution_thumb'] = $size['width'] . "x" . $size['height'];
        } else {
            $data['resolution'] = WDWLibrary::get('upload_img_width') . " x " . WDWLibrary::get('upload_img_height') . " px ";
            $data['resolution_thumb'] = WDWLibrary::get('upload_thumb_width') . "x" . WDWLibrary::get('upload_thumb_height');
        }
    } else {
        $data['resolution'] = isset($info->resolution) ? $info->resolution : '';
        $resolution_thumb = WDWLibrary::get_thumb_size("/" . $data['thumb']);
        if ( $resolution_thumb == '' && !empty($data['resolution']) ) {
          $temp = explode(" ", $data['resolution']);
          $resolution_thumb = $temp[0] . "x" . $temp[2];
        }
        $data['resolution_thumb'] = $resolution_thumb;

        $data['credit'] = isset($info->credit) ? $this->mime_decode($info->credit, $iconv_mime_decode_function_exist) : '';
        $data['aperture'] = isset($info->aperture) ? $this->mime_decode($info->aperture, $iconv_mime_decode_function_exist) : '';
        $data['camera'] = isset($info->camera) ? $this->mime_decode($info->camera, $iconv_mime_decode_function_exist) : '';
        $data['caption'] = isset($info->caption) ? $this->mime_decode($info->caption, $iconv_mime_decode_function_exist) : '';
        $data['iso'] = isset($info->iso) ? $this->mime_decode($info->iso, $iconv_mime_decode_function_exist) : '';
        $data['orientation'] = isset($info->orientation) ? $info->orientation : '';
        $data['copyright'] = isset($info->copyright) ? $this->mime_decode($info->copyright, $iconv_mime_decode_function_exist) : '';
        $data['tags'] = isset($info->tags) ? $this->mime_decode($info->tags, $iconv_mime_decode_function_exist) : '';

    }

    return $data;
  }

  private function mime_decode($value, $function_exist) {

    if ( $value && $function_exist ) {
	 $value = iconv_mime_decode($value, 2, 'UTF-8');
    }
    return $value;

  }
}

die();