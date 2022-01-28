<?php
/**
 * Class FilemanagerController
 */
class FilemanagerController {

  public $model;
  public $view;

  public $uploads_dir;
  public $uploads_url;
  public $page_per = 60;

  public function __construct() {
	require_once BWG()->plugin_dir . '/filemanager/model.php';
	$this->model = new FilemanagerModel($this);

	require_once BWG()->plugin_dir . '/filemanager/view.php';
	$this->view = new FilemanagerView($this, $this->model);

    $this->uploads_dir = BWG()->upload_dir;
    $this->uploads_url = BWG()->upload_url;
  }

  public function execute() {
    $task = isset($_REQUEST['task']) ? stripslashes(WDWLibrary::get('task','','sanitize_text_field','REQUEST')) : 'display';
    if (method_exists($this, $task)) {
      $this->$task();
    }
    else {
      $this->display();
    }
  }

  public function get_uploads_dir() {
    return $this->uploads_dir;
  }

  public function get_uploads_url() {
    return $this->uploads_url;
  }

  public function display() {
	$params = array();
	$dir = $this->model->get_from_session('dir', '');
    $search = $this->model->get_from_session('search', '');
    $page_num = $this->model->get_from_session('paged', 0);
    $extensions = $this->model->get_from_session('extensions', '*');
    $callback = $this->model->get_from_session('callback', '');
    $valid_types = explode( ',', strtolower($extensions) );

	// set session data.
	$session_data = array();
    $session_data['sort_by'] = $this->model->get_from_session('sort_by', 'date_modified');
    $session_data['sort_order'] = $this->model->get_from_session('sort_order', 'desc');
    $session_data['items_view'] = $this->model->get_from_session('items_view', 'thumbs');
    $session_data['clipboard_task'] = $this->model->get_from_session('clipboard_task', '');
    $session_data['clipboard_files'] = $this->model->get_from_session('clipboard_files', '');
    $session_data['clipboard_src'] = $this->model->get_from_session('clipboard_src', '');
    $session_data['clipboard_dest'] = $this->model->get_from_session('clipboard_dest', '');
	$params['session_data'] = $session_data;

	$params['dir'] = ($dir == '' || $dir == '/') ? '/' : $dir .'/';
	$params['path_components'] = $this->model->get_path_components( $dir );
	$params['search'] = $search;
	$params['page_num'] = $page_num;
	$params['valid_types'] = $valid_types;
	$params['orderby'] = $session_data['sort_by'];
	$params['order'] = $session_data['sort_order'];
	$params['page_per'] = $this->page_per;
	// get file lists.
	$items = $this->model->get_file_lists( $params );
	$params['items'] = $items;

	$pagination_args = array(
		'action' => 'addImages',
		'filemanager_msg' => '',
		'width' => '850',
		'height' => '550',
		'task' => 'pagination',
		'extensions' => '',
		'callback' => '',
		'dir' => $dir,
		'TB_iframe' => '1',
	);
	$ajax_pagination_url = wp_nonce_url( admin_url('admin-ajax.php'), 'addImages', 'bwg_nonce' );
	$ajax_pagination_url = add_query_arg($pagination_args, $ajax_pagination_url);
	$params['ajax_pagination_url'] = $ajax_pagination_url;

	$all_select_args = array(
		'action' => 'addImages',
		'task' => 'get_all_select',
	);
	$ajax_get_all_select_url = wp_nonce_url( admin_url('admin-ajax.php'), 'addImages', 'bwg_nonce' );
	$ajax_get_all_select_url = add_query_arg($all_select_args, $ajax_get_all_select_url);
	$params['ajax_get_all_select_url'] = $ajax_get_all_select_url;

    $this->view->display( $params );
  }

  function pagination() {
	$dir = $this->model->get_from_session('dir', '');
	$dir = ($dir == '') ? '/' : $dir .'/';
	$order   = $this->model->get_from_session('order', 'desc');
	$orderby = $this->model->get_from_session('orderby', 'date_modified');
	$search = $this->model->get_from_session('search', '');
	$paged = $this->model->get_from_session('paged', 0);
	$page_per = $this->page_per;
	$data = $this->model->get_file_lists(
		array(
		'dir' => $dir,
		'order' => $order,
		'orderby' => $orderby,
		'page_num' => $paged,
		'page_per' => $page_per,
		'search' => $search
		)
	);
	$html = '';
	$i = 0;
	if ( !empty($data['files']) ) {
		foreach($data['files'] as $file ) {
			++$i;
			$file['index'] = $paged * $this->page_per + $i;
			$html .= $this->view->print_file_thumb($file);
		}
	}
	$json = array('html' => $html);
	echo json_encode($json); exit;
  }

	function get_all_select() {
		$dir = $this->model->get_from_session('dir', '');
		$search = $this->model->get_from_session('search', '');
		$order = $this->model->get_from_session('order', 'desc');
		$orderby = $this->model->get_from_session('orderby', 'date_modified');
		$data = array();
		$data = $this->model->get_all_files( array('dir' => $dir, 'search' => $search, 'orderby' => $orderby, 'order' => $order) );
		$json = array('data' => $data);
		echo json_encode($json); exit;
	}

  /**
   * esc dir.
   * @param $dir
   *
   * @return mixed
   */
	private function esc_dir($dir) {
		$dir = str_replace('../', '', $dir);

		return $dir;
	}

  public function make_dir() {

    global $wpdb;
    $input_dir = (isset($_REQUEST['dir']) ? str_replace('\\', '', WDWLibrary::get('dir','','sanitize_text_field','REQUEST')) : '');
    $input_dir = htmlspecialchars_decode($input_dir, ENT_COMPAT | ENT_QUOTES);
    $input_dir = $this->esc_dir($input_dir);

    $cur_dir_path = $input_dir == '' ? $this->uploads_dir : $this->uploads_dir . '/' . $input_dir;

    $new_dir_path_name = isset($_REQUEST['new_dir_name']) ? stripslashes(WDWLibrary::get('new_dir_name','','sanitize_text_field','REQUEST')) : '';

    // Do not sanitize folder name, if it contents mime types in name
    $mime_types = wp_get_mime_types();
    $filetype = wp_check_filetype( 'test.' . $new_dir_path_name, $mime_types );
    if ( $filetype['ext'] !== $new_dir_path_name && '.' . $filetype['ext'] !== $new_dir_path_name ) {
      $new_dir_path_name = sanitize_file_name($new_dir_path_name);
    }

    $new_dir_path = $cur_dir_path . '/' . $new_dir_path_name;
    $new_dir_path = htmlspecialchars_decode($new_dir_path, ENT_COMPAT | ENT_QUOTES);
    $new_dir_path = $this->esc_dir($new_dir_path);

    if (file_exists($new_dir_path) == true) {
      $msg = __("Directory already exists.", BWG()->prefix);
    }
    else {
      $msg = '';
      $path = $input_dir . '/';
      $data = array(
		'is_dir' => 1,
		'path' => $path,
		'name' => $new_dir_path_name,
		'alt' => str_replace("_", " ", $new_dir_path_name),
		'filename' => str_replace("_", " ", $new_dir_path_name),
		'thumb' => '/filemanager/images/dir.png',
		'date_modified' => date("Y-m-d H:i:s"),
		'author' => get_current_user_id(),
      );
      $format = array(
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d'
      );
      $wpdb->insert($wpdb->prefix . 'bwg_file_paths', $data, $format);
      mkdir($new_dir_path);
    }
    $args = array(
      'action' => 'addImages',
      'filemanager_msg' => $msg,
      'bwg_width' => '850',
      'bwg_height' => '550',
      'task' => 'display',
      'extensions' => WDWLibrary::get('extensions'),
      'callback' => WDWLibrary::get('callback'),
      'dir' => $input_dir,
      'TB_iframe' => '1',
    );
    $query_url = wp_nonce_url( admin_url('admin-ajax.php'), 'addImages', 'bwg_nonce' );
    $query_url  = add_query_arg($args, $query_url);
    header('Location: ' . $query_url);
    exit;
  }

  public function parsing_items() {
		$dir = $this->model->get_from_session('dir', '');
		$dir = ($dir == '' || $dir == '/') ? '/' : $dir .'/';
		$input_dir = (isset($_REQUEST['dir']) ? str_replace('\\', '', WDWLibrary::get('dir','','sanitize_text_field','REQUEST')) : '');
		$valid_types = explode(',', isset($_REQUEST['extensions']) ? strtolower(WDWLibrary::get('extensions','','sanitize_text_field','REQUEST')) : '*');
		$parsing = $this->model->files_parsing_db(array(
			'refresh' => true,
			'dir' => BWG()->upload_dir . $dir,
			'path' => $dir,
			'valid_types' => $valid_types,
		));
		$_REQUEST['file_names'] = '';
		$args = array(
			'action' => 'addImages',
			'filemanager_msg' => '',
			'width' => '850',
			'height' => '550',
			'task' => 'display',
			'extensions' => WDWLibrary::get('extensions'),
			'callback' => WDWLibrary::get('callback'),
			'dir' => $input_dir,
			'TB_iframe' => '1',
		);
		$query_url = wp_nonce_url( admin_url('admin-ajax.php'), 'addImages', 'bwg_nonce' );
		$query_url = add_query_arg($args, $query_url);
		header('Location: ' . $query_url);
		exit;
	}

  public function rename_item() {
	global $wpdb;
    $input_dir = (isset($_REQUEST['dir']) ? str_replace('\\', '', WDWLibrary::get('dir','','sanitize_text_field','REQUEST')) : '');
    $input_dir = htmlspecialchars_decode($input_dir, ENT_COMPAT | ENT_QUOTES);
    $input_dir = $this->esc_dir($input_dir);

    $cur_dir_path = $input_dir == '' ? $this->uploads_dir : $this->uploads_dir . '/' . $input_dir;

    $file_names = explode('**#**', (isset($_REQUEST['file_names']) ? stripslashes(WDWLibrary::get('file_names','','sanitize_text_field','REQUEST')) : ''));

    $file_name = $file_names[0];
    $file_name = htmlspecialchars_decode($file_name, ENT_COMPAT | ENT_QUOTES);
    $file_name = str_replace('../', '', $file_name);

    $file_new_name = (isset($_REQUEST['file_new_name']) ? stripslashes(WDWLibrary::get('file_new_name','','sanitize_text_field','REQUEST')) : '');
    $file_new_name = htmlspecialchars_decode($file_new_name, ENT_COMPAT | ENT_QUOTES);
    $file_new_name = $this->esc_dir($file_new_name);

    $file_path = $cur_dir_path . '/' . $file_name;
    $thumb_file_path = $cur_dir_path . '/thumb/' . $file_name;
    $original_file_path = $cur_dir_path . '/.original/' . $file_name;
    $msg = '';

    if (file_exists($file_path) == false) {
      $msg = __("File doesn't exist.", BWG()->prefix);
    }
    elseif (is_dir($file_path) == true) {
      if (rename($file_path, $cur_dir_path . '/' . sanitize_file_name($file_new_name)) == false) {
        $msg = __("Can't rename the file.", BWG()->prefix);
      }
      else {
        $args = array(
          $input_dir,
          $file_name,
          $input_dir,
          $file_name,
          $input_dir,
          $file_name,
          $input_dir,
          $file_name,
          $input_dir,
          $file_name,
          $input_dir,
          $file_name
        );
        $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'bwg_image SET
          image_url = INSERT(image_url, LOCATE("%s/%s", image_url), CHAR_LENGTH("%s/%s"), "%s/%s"),
          thumb_url = INSERT(thumb_url, LOCATE("%s/%s", thumb_url), CHAR_LENGTH("%s/%s"), "%s/%s")', $args));
        $args = array(
          $input_dir,
          $file_name,
          $input_dir,
          $file_name,
          $input_dir,
          $file_name
        );
          $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'bwg_gallery SET
          preview_image = INSERT(preview_image, LOCATE("%s/%s", preview_image), CHAR_LENGTH("%s/%s"), "%s/%s")', $args));

		// Update all paths.
		$path_where = (empty($input_dir) ? '/' : $input_dir .'/');
		$paths = $this->getRecursivePathLists( $path_where, $file_name);
		$wpdb->update($wpdb->prefix . 'bwg_file_paths',
			array(
				'name' => $file_new_name,
				'filename' => $file_new_name,
				'alt' => $file_new_name
			),
			array('path' => $path_where, 'name' => $file_name),
      array('%s','%s','%s'),
      array('%s','%s')
		);
		if ( !empty($paths) ) {
			foreach( $paths as $val) {
				$wpdb->update($wpdb->prefix . 'bwg_file_paths',
					array('path' => str_replace($file_name, $file_new_name, $val)),
					array('path' =>  $val),
          array('%s'),
          array('%s')
        );
			}
		}
	  }
    }
    elseif ((strrpos($file_name, '.') !== false)) {
      $file_extension = substr($file_name, strrpos($file_name, '.') + 1);
      if (rename($file_path, $cur_dir_path . '/' . $file_new_name . '.' . $file_extension) == false) {
        $msg = __("Can't rename the file.", BWG()->prefix);
      }
      else {
        $wpdb->update($wpdb->prefix . 'bwg_image', array(
          'filename' => $file_new_name,
          'image_url' => $input_dir . '/' . $file_new_name . '.' . $file_extension,
          'thumb_url' => $input_dir . '/thumb/' . $file_new_name . '.' . $file_extension,
          ),
          array('thumb_url' =>  $input_dir . '/thumb/' . $file_name),
          array('%s','%s','%s'),
          array('%s')
        );
        $wpdb->update($wpdb->prefix . 'bwg_gallery',
                      array('preview_image' => $input_dir . '/thumb/' . $file_new_name . '.' . $file_extension),
                      array('preview_image' =>  $input_dir . '/thumb/' . $file_name),
                      array('%s'),
                      array('%s')

        );

		$path = $input_dir .'/';
		$wpdb->update($wpdb->prefix . 'bwg_file_paths',
			array(
				'name'	 	=> $file_new_name . '.' . $file_extension,
				'filename' 	=> $file_new_name,
				'thumb' 	=> 'thumb/'. $file_new_name . '.' . $file_extension,
				'alt' 		=> $file_new_name,
				'date_modified' => date('Y-m-d H:i:s')
			),
			array('path' => $path, 'name' => $file_name),
      array('%s','%s','%s','%s','%s'),
      array('%s','%s')

    );

        rename($thumb_file_path, $cur_dir_path . '/thumb/' . $file_new_name . '.' . $file_extension);
        rename($original_file_path, $cur_dir_path . '/.original/' . $file_new_name . '.' . $file_extension);
      }
    }
    else {
      $msg = __("Can't rename the file.", BWG()->prefix);
	}
    $_REQUEST['file_names'] = '';
    $args = array(
      'action' => 'addImages',
      'filemanager_msg' => $msg,
      'bwg_width' => '850',
      'bwg_height' => '550',
      'task' => 'display',
      'extensions' => WDWLibrary::get('extensions'),
      'callback' => WDWLibrary::get('callback'),
      'dir' => $input_dir,
      'TB_iframe' => '1',
    );
    $query_url = wp_nonce_url( admin_url('admin-ajax.php'), 'addImages', 'bwg_nonce' );
    $query_url = add_query_arg($args, $query_url);
    header('Location: ' . $query_url);
    exit;
  }

  public function remove_items() {
    global $wpdb;
    $input_dir = (isset($_REQUEST['dir']) ? str_replace('\\', '', (WDWLibrary::get('dir','','sanitize_text_field','REQUEST'))) : '');
    $input_dir = htmlspecialchars_decode($input_dir, ENT_COMPAT | ENT_QUOTES);
    $input_dir = $this->esc_dir($input_dir);

    $cur_dir_path = $input_dir == '' ? $this->uploads_dir : $this->uploads_dir . '/' . $input_dir;

    $file_names = explode('**#**', (isset($_REQUEST['file_names']) ? stripslashes(WDWLibrary::get('file_names','','sanitize_text_field','REQUEST')) : ''));
    $path = $input_dir .'/';
    $msg = '';
	  $file_path_tbl = $wpdb->prefix . 'bwg_file_paths';
    foreach ($file_names as $file_name) {
      $file_name = htmlspecialchars_decode($file_name, ENT_COMPAT | ENT_QUOTES);
      $file_name = str_replace('../', '', $file_name);
      $file_path = $cur_dir_path . '/' . $file_name;
      $thumb_file_path = $cur_dir_path . '/thumb/' . $file_name;
      $original_file_path = $cur_dir_path . '/.original/' . $file_name;
      if (file_exists($file_path) == false) {
        $msg = __("Some of the files couldn't be removed.", BWG()->prefix);
      }
      else {
        if ( is_dir($file_path) == true ) {
			$paths = $this->getRecursivePathLists($path, $file_name);
			if ( !empty($paths) ) {
				$wpdb->delete( $file_path_tbl, array('path' => $path, 'name' => $file_name), array('%s','%s'));
				foreach( $paths as $val ) {
					$wpdb->delete( $file_path_tbl, array('path' => $val), array('%s') );
				}
			}
        }
        else {
          $wpdb->delete( $file_path_tbl, array('path' => $path, 'name' => $file_name), array('%s','%s') );
        }
        $this->remove_file_dir($file_path, $input_dir, $file_name);
        if (file_exists($thumb_file_path)) {
          $this->remove_file_dir($thumb_file_path);
        }
        if (file_exists($original_file_path)) {
          $this->remove_file_dir($original_file_path);
        }
      }
    }
    $_REQUEST['file_names'] = '';
    $args = array(
      'action' => 'addImages',
      'filemanager_msg' => $msg,
      'bwg_width' => '850',
      'bwg_height' => '550',
      'task' => 'show_file_manager',
      'extensions' => WDWLibrary::get('extensions'),
      'callback' => WDWLibrary::get('callback'),
      'dir' => $input_dir,
      'TB_iframe' => '1',
    );
    $query_url = wp_nonce_url( admin_url('admin-ajax.php'), 'addImages', 'bwg_nonce' );
    $query_url = add_query_arg($args, $query_url);
    header('Location: ' . $query_url);
    exit;
  }

  public function paste_items() {
	global $wpdb;
	$input_dir = (isset($_REQUEST['dir']) ? str_replace('\\', '', (WDWLibrary::get('dir','','sanitize_text_field','REQUEST'))) : '');
	$input_dir = htmlspecialchars_decode($input_dir, ENT_COMPAT | ENT_QUOTES);
	$input_dir = $this->esc_dir($input_dir);


	$msg = '';
	$flag = TRUE;

	$file_names = explode('**#**', (isset($_REQUEST['clipboard_files']) ? stripslashes(WDWLibrary::get('clipboard_files','','sanitize_text_field','REQUEST')) : ''));
	$src_dir = (isset($_REQUEST['clipboard_src']) ? stripslashes(WDWLibrary::get('clipboard_src','','sanitize_text_field','REQUEST')) : '');
	$relative_source_dir = $src_dir;
	$src_dir = $src_dir == '' ? $this->uploads_dir : $this->uploads_dir . '/' . $src_dir;
	$src_dir = htmlspecialchars_decode($src_dir, ENT_COMPAT | ENT_QUOTES);
	$src_dir = $this->esc_dir($src_dir);

	$dest_dir = (isset($_REQUEST['clipboard_dest']) ? stripslashes(WDWLibrary::get('clipboard_dest','','sanitize_text_field','REQUEST')) : '');
	$dest_dir = $dest_dir == '' ? $this->uploads_dir : $this->uploads_dir . '/' . $dest_dir;
	$dest_dir = htmlspecialchars_decode($dest_dir, ENT_COMPAT | ENT_QUOTES);
	$dest_dir = $this->esc_dir($dest_dir);

	$path_old = (isset($_REQUEST['clipboard_src']) ? stripslashes(WDWLibrary::get('clipboard_src','','sanitize_text_field','REQUEST')) .'/' : '/');
	$path_new = (isset($_REQUEST['clipboard_dest']) ? stripslashes(WDWLibrary::get('clipboard_dest','','sanitize_text_field','REQUEST')) .'/' : '/');
	$file_path_tbl = $wpdb->prefix . 'bwg_file_paths';

	switch ((isset($_REQUEST['clipboard_task']) ? stripslashes(WDWLibrary::get('clipboard_task','','sanitize_text_field','REQUEST')) : '')) {
		case 'copy': {
			foreach ($file_names as $file_name) {
				$file = $wpdb->get_row( $wpdb->prepare('SELECT * FROM `' . $file_path_tbl . '` WHERE `path` ="%s" AND `name`="%s"', array($path_old, $file_name)), 'ARRAY_A' );
				unset($file['id']);
				$file_name = htmlspecialchars_decode($file_name, ENT_COMPAT | ENT_QUOTES);
				$file_name = str_replace('../', '', $file_name);
				$src = $src_dir . '/' . $file_name;
				if (file_exists($src) == false) {
					$msg = "Failed to copy some of the files.";
					$msg = $file_name;
					continue;
				}
				$dest = $dest_dir . '/' . $file_name;
				if ( !is_dir($src_dir . '/' . $file_name) ) {
					if ( !is_dir($dest_dir . '/thumb') ) {
						mkdir($dest_dir . '/thumb', 0755);
					}
					$thumb_src = $src_dir . '/thumb/' . $file_name;
					$thumb_dest = $dest_dir . '/thumb/' . $file_name;
					if (!is_dir($dest_dir . '/.original')) {
						mkdir($dest_dir . '/.original', 0755);
					}
					$original_src = $src_dir . '/.original/' . $file_name;
					$original_dest = $dest_dir . '/.original/' . $file_name;
				}

				$i = 0;
				$new_file_name = '';
				$new_file_title = '';
				if ( file_exists($dest) == true ) {
					$path_parts = pathinfo($dest);
					$extension = !empty( $path_parts['extension'] ) ? '.' . $path_parts['extension'] : '';
					while ( file_exists($path_parts['dirname'] . '/' . $path_parts['filename'] . '(' . ++$i . ')' . $extension )) {}
					$dest = $path_parts['dirname'] . '/' . $path_parts['filename'] . '(' . $i . ')' . $extension;
					$new_file_name = $path_parts['filename'] . '(' . $i . ')' . $extension;
					$new_file_title = $path_parts['filename'] . '(' . $i . ')';
					if ( !is_dir($src_dir . '/' . $file_name) ) {
						$thumb_dest = $path_parts['dirname'] . '/thumb/' . $new_file_name;
						$original_dest = $path_parts['dirname'] . '/.original/' . $new_file_name;
					}
				}
				if ( !$this->copy_file_dir($src, $dest) ) {
					$msg = __("Failed to copy some of the files.", BWG()->prefix);
				}
				if ( !is_dir($src_dir . '/' . $file_name) ) {
					$_file_name = !empty($new_file_name) ? $new_file_name : $file_name;
					$_file_title = !empty($new_file_title) ? $new_file_title : preg_replace("/\.[^.]+$/", "", $file_name);
					$file['path'] = $path_new;
					$file['name'] = $_file_name;
					$file['thumb'] = $_file_name;
					$file['filename'] = $_file_title;
					$file['alt'] = $_file_title;
					$wpdb->insert( $file_path_tbl, $file );
					$this->copy_file_dir($thumb_src, $thumb_dest);
					$this->copy_file_dir($original_src, $original_dest);
				}
				else {
					$path_where = '/'. $file_name .'/';
					$path_file = (isset($_REQUEST['clipboard_dest']) ? stripslashes(WDWLibrary::get('clipboard_dest','','sanitize_text_field','REQUEST')) .'/' : '');

					$file['path'] = $path_file;
					$file['name'] = !empty($new_file_title) ? $new_file_title : $file['name'];
					$file['filename'] = !empty($new_file_title) ? $new_file_title : $file['filename'];
					$file['alt'] = !empty($new_file_title) ? $new_file_title : $file['alt'];
					$wpdb->insert( $file_path_tbl, $file );

					$files = $wpdb->get_results( $wpdb->prepare('SELECT * FROM `' . $file_path_tbl . '` WHERE `path` ="%s"',array($path_where)), 'ARRAY_A' );
					foreach( $files as $file ) {
						unset($file['id']);
						$file['path'] = $path_file . (!empty($new_file_title) ? $new_file_title .'/' : $file_name .'/');
						$wpdb->insert( $file_path_tbl, $file );
					}
				}
			}
		} break;
		case 'cut': {
        if ( $src_dir != $dest_dir ) {
			foreach ( $file_names as $file_name ) {
				$file_name = htmlspecialchars_decode($file_name, ENT_COMPAT | ENT_QUOTES);
				$file_name = str_replace('../', '', $file_name);
				$src = $src_dir . '/' . $file_name;
				$dest = $dest_dir . '/' . $file_name;

				if ( (file_exists($src) == FALSE) || (file_exists($dest) == TRUE) ) {
					$flag = FALSE;
				} else {
					$flag = rename($src, $dest);
				}
				if ( !$flag ) {
					$msg = __("Failed to move some of the files.", BWG()->prefix);
				}
				else {
					if ( is_dir($dest_dir . '/' . $file_name) ) {
					  $temp_dir = str_replace($this->uploads_dir . '/', '', $src);
					  $temp_inputdir = str_replace(str_replace($input_dir, '', $dest_dir), '', $dest);
            $prepareArgs = array(
              $temp_dir,
              $temp_dir,
              $temp_inputdir,
              $temp_dir,
              $temp_dir,
              $temp_inputdir
            );
						$wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'bwg_image SET
						image_url = INSERT(image_url, LOCATE("%s", image_url), CHAR_LENGTH("%s"), "%s"),
						thumb_url = INSERT(thumb_url, LOCATE("%s", thumb_url), CHAR_LENGTH("%s"), "%s")', $prepareArgs));

            $prepareArgs = array(
              $temp_dir,
              $temp_dir,
              $temp_inputdir
            );
						$wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'bwg_gallery SET preview_image =
						INSERT(preview_image, LOCATE("%s", preview_image), CHAR_LENGTH("%s"), "%s")', $prepareArgs));

						$paths = $this->getRecursivePathLists($path_old, $file_name);
						$wpdb->update(
						  $file_path_tbl,
              array('path' => $path_new),
              array('path' => $path_old, 'name' => $file_name),
              array('%s'),
              array('%s','%s')
            );
						$path_where = $path_old . $file_name .'/';
						foreach ( $paths as $val ) {
							$path_update = $path_new . str_replace($path_old, '', $val);
							$wpdb->update(
							  $file_path_tbl,
                array('path' => $path_update),
                array('path' => $val),
                array('%s'),
                array('%s')
              );
						}
					}
					else {
						$thumb_src = $src_dir . '/thumb/' . $file_name;
						$thumb_dest = $dest_dir . '/thumb/' . $file_name;
						if ( !is_dir($dest_dir . '/thumb') ) {
							mkdir($dest_dir . '/thumb', 0755);
						}
						$original_src = $src_dir . '/.original/' . $file_name;
						$original_dest = $dest_dir . '/.original/' . $file_name;
						if ( !is_dir($dest_dir . '/.original') ) {
							mkdir($dest_dir . '/.original', 0755);
						}
						rename($thumb_src, $thumb_dest);
						rename($original_src, $original_dest);

						$wpdb->update($wpdb->prefix . 'bwg_image',
							array(
								'filename' => $file_name,
								'image_url' => str_replace(str_replace($input_dir, '', $dest_dir), '', $dest),
								'thumb_url' => $input_dir . '/thumb/' . $file_name,
							),
							array('thumb_url' => $relative_source_dir . '/thumb/' . $file_name),
              array('%s','%s','%s'),
              array('%s')
            );
						$wpdb->update($wpdb->prefix . 'bwg_gallery',
							array('preview_image' => $input_dir . '/thumb/' . $file_name),
							array('preview_image' => $relative_source_dir . '/thumb/' . $file_name),
              array('%s'),
              array('%s')
            );

						$wpdb->update(
						  $file_path_tbl,
              array('path' => $path_new),
              array('path' => $path_old, 'name' => $file_name) ,
              array('%s'),
              array('%s','%s')
            );
						}
					}
				}
			}
		} break;
    }

	$args = array(
      'action' => 'addImages',
      'filemanager_msg' => $msg,
      'bwg_width' => '850',
      'bwg_height' => '550',
      'task' => 'show_file_manager',
      'extensions' => WDWLibrary::get('extensions','','sanitize_text_field','REQUEST'),
      'callback' => WDWLibrary::get('callback','','sanitize_text_field','REQUEST'),
      'dir' => $input_dir,
      'TB_iframe' => '1',
    );

    $query_url = wp_nonce_url( admin_url('admin-ajax.php'), 'addImages', 'bwg_nonce' );
    $query_url = add_query_arg($args, $query_url);
    header('Location: ' . $query_url);
    exit;
  }

  public function import_items() {
    $args = array(
      'action' => 'bwg_UploadHandler',
      'importer_thumb_width' => WDWLibrary::get('importer_thumb_width','','intval','REQUEST'),
      'importer_thumb_height' => WDWLibrary::get('importer_thumb_height','','intval','REQUEST'),
      'callback' => WDWLibrary::get('callback','','sanitize_text_field','REQUEST'),
      'file_namesML' => WDWLibrary::get('file_namesML','','sanitize_text_field','REQUEST'),
      'importer_img_width' => WDWLibrary::get('importer_img_width','','intval','REQUEST'),
      'importer_img_height' => WDWLibrary::get('importer_img_height','','intval','REQUEST'),
      'import' => 'true',
      'redir' => WDWLibrary::get('dir','','sanitize_text_field','REQUEST'),
      'dir' => WDWLibrary::get('dir','','sanitize_text_field','REQUEST') . '/',
    );

    $query_url = wp_nonce_url( admin_url('admin-ajax.php'), 'bwg_UploadHandler', 'bwg_nonce' );
    $query_url = add_query_arg($args, $query_url);
    header('Location: ' . $query_url);
    exit;
  }

  private function remove_file_dir($del_file_dir, $input_dir = FALSE, $file_name = FALSE) {
    $del_file_dir = $this->esc_dir($del_file_dir);
    if (is_dir($del_file_dir) == true) {
      $files_to_remove = scandir($del_file_dir);
      foreach ($files_to_remove as $file) {
        if ($file != '.' and $file != '..') {
          $this->remove_file_dir($del_file_dir . '/' . $file, $input_dir . '/' . $file_name, $file);
        }
      }
      rmdir($del_file_dir);
    }
    else {
      unlink($del_file_dir);
      if ( $input_dir !== FALSE && $file_name !== FALSE ) {
        global $wpdb;
        $deleted_image_dir = $input_dir . '/thumb/' . $file_name;
        // delete image by preview_image.
        $wpdb->delete($wpdb->prefix . 'bwg_image', array( 'thumb_url' => $deleted_image_dir ), array('%s'));
        // Get gallery by preview_image or random_preview_image.
        $galleries = $wpdb->get_results($wpdb->prepare('SELECT `id` FROM `' . $wpdb->prefix . 'bwg_gallery` WHERE `preview_image` = "%s" OR `random_preview_image` = "%s"', array($deleted_image_dir,$deleted_image_dir)));
        // Update random preview image on bwg_gallery.
        if ( !empty($galleries) ) {
          $gallerIds = array();
          foreach ( $galleries as $item ) {
            $gallerIds[$item->id] = $item->id;
          }
          // Get thumb images by gallery id.
          $thumbIds = array();
          $implodeGalIds = implode(',', $gallerIds);
          $thumbs = $wpdb->get_results($wpdb->prepare('SELECT `gallery_id`, `thumb_url` FROM `' . $wpdb->prefix . 'bwg_image` WHERE `gallery_id` IN (%s)', array($implodeGalIds)));
          if ( !empty($thumbs) ) {
            foreach ( $thumbs as $item ) {
              $thumbIds[$item->gallery_id][] = $item->thumb_url;
            }
          }
          foreach ( $gallerIds as $gid ) {
            $random_preview_image = '';
            if ( !empty($thumbIds[$gid]) ) {
              $rand_keys = array_rand($thumbIds[$gid], 1);
              $random_preview_image = $thumbIds[$gid][$rand_keys];
              if ( !preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $random_preview_image) ) {
                $random_preview_image = wp_normalize_path($thumbIds[$gid][$rand_keys]);
              }
            }
            $wpdb->update($wpdb->prefix . 'bwg_gallery', array(
              'preview_image' => '',
              'random_preview_image' => $random_preview_image,
            ),
            array( 'id' => $gid ),
            array('%s','%s'),
            array('%d')
            );
          }
        }
      }
    }
  }

  private function copy_file_dir($src, $dest) {
    $src = $this->esc_dir($src);
    $dest = $this->esc_dir($dest);

    if (is_dir($src) == true) {
      $dir = opendir($src);
      @mkdir($dest);
      while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
          if (is_dir($src . '/' . $file)) {
            $this->copy_file_dir($src . '/' . $file, $dest . '/' . $file);
          }
          else {
            copy($src . '/' . $file, $dest . '/' . $file);
          }
        }
      }
      closedir($dir);
      return true;
    }
    else {
      return copy($src, $dest);
    }
  }

  /**
   * Get recursive path lists.
   *
   * @param string $path
   * @param string $name
   * @param int    $level
   *
   * @return array
   */
	private function getRecursivePathLists( $path = '/', $name = '', $level = 0 ) {
		global $wpdb;
    $prepareArgs = array($path);
		static $parents = array();
		$where = '';
		if( $level == 0 ) {
      $where = ' AND `name`="%s"';
      $prepareArgs[] = $name;
    }

		$items = $wpdb->get_results( $wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'bwg_file_paths` WHERE `is_dir` = 1 AND `path` ="%s"' . $where, $prepareArgs) );
    if ( !empty($items) ) {
      foreach ( $items as $item ) {
        $path = $item->path . $item->name . '/';
        $children = $this->getRecursivePathLists($path, $item->name, $level + 1);
        $parents[] = $path;
      }
    }

		return $parents;
	}
}
