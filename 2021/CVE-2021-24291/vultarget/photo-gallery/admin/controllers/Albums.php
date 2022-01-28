<?php

/**
 * Class AlbumsController_bwg
 */
class AlbumsController_bwg {
  /**
   * @var $model
   */
  private $model;
  /**
   * @var $view
   */
  private $view;
  /**
   * @var string $page
   */
  private $page;
  /**
   * @var string $bulk_action_name
   */
  private $bulk_action_name;
  /**
   * @var int $items_per_page
   */
  private $items_per_page = 20;
  /**
   * @var array $actions
   */
  private $actions = array();

  public function __construct() {
    $this->model = new AlbumsModel_bwg();
    $this->view = new AlbumsView_bwg();
    $this->page = WDWLibrary::get('page');
    $this->actions = array(
      'publish' => array(
        'title' => __('Publish', BWG()->prefix),
        $this->bulk_action_name => __('published', BWG()->prefix),
      ),
      'unpublish' => array(
        'title' => __('Unpublish', BWG()->prefix),
        $this->bulk_action_name => __('unpublished', BWG()->prefix),
      ),
      'duplicate' => array(
        'title' => __('Duplicate', BWG()->prefix),
        $this->bulk_action_name => __('duplicate', BWG()->prefix),
      ),
      'delete' => array(
        'title' => __('Delete', BWG()->prefix),
        $this->bulk_action_name => __('deleted', BWG()->prefix),
      ),
    );
    $user = get_current_user_id();
    $screen = get_current_screen();
	if ( !empty($screen) ) {
		$option = $screen->get_option('per_page', 'option');
		$this->items_per_page = get_user_meta($user, $option, TRUE);
		if (empty ($this->items_per_page) || $this->items_per_page < 1) {
		  $this->items_per_page = $screen->get_option('per_page', 'default');
		}
    }
  }

  /**
   * Execute.
   */
  public function execute() {
    $task = WDWLibrary::get('task');
    $id = WDWLibrary::get('current_id', 0, 'intval');
    if ($task != 'display' && method_exists($this, $task)) {
      if ($task != 'edit') {
        check_admin_referer(BWG()->nonce, BWG()->nonce);
      }
      $action = WDWLibrary::get('bulk_action');
      if ($action != '') {
        $this->bulk_action($action);
      }
      else {
        $this->$task($id);
      }
    }
    else {
      $this->display();
    }
  }

  /**
   * Display.
   */
  public function display() {
    // Set params for view.
    $params = array();
    $params['page'] = $this->page;
    $params['page_title'] = __('Gallery Groups', BWG()->prefix);
    $params['actions'] = $this->actions;
    $params['order'] = WDWLibrary::get('order', 'asc');
    $params['orderby'] = WDWLibrary::get('orderby', 'name');
    // To prevent SQL injections.
    $params['order'] = ($params['order'] == 'desc') ? 'desc' : 'asc';
    if (!in_array($params['orderby'], array('name', 'author'))) {
      $params['orderby'] = 'id';
    }
    $params['items_per_page'] = $this->items_per_page;
    $page = WDWLibrary::get('paged', 1, 'intval');
    if ( $page < 0 ) {
      $page = 1;
    }
    $page_num = $page ? ($page - 1) * $params['items_per_page'] : 0;
    $params['paged'] = $page;
    $params['page_num'] = $page_num;
    $params['search'] = WDWLibrary::get('s');

    $params['total'] = $this->model->total($params);
    $params['rows'] = $this->model->get_rows_data($params);

    $url_arg = array();
    $page_url = add_query_arg(array(
      'page' => $this->page,
      BWG()->nonce => wp_create_nonce(BWG()->nonce),
    ), admin_url('admin.php'));

    $page_url = add_query_arg($url_arg, $page_url);
    $params['page_url'] = $page_url;

    $this->view->display($params);
  }

  /**
   * Bulk actions.
   *
   * @param $task
   */
  public function bulk_action($task = '') {
    $message = 0;
    $successfully_updated = 0;
    $url_arg = array('page' => $this->page, 'task' => 'display');

    $check = WDWLibrary::get('check');
    $all = WDWLibrary::get('check_all_items');
    $all = ($all == 'on' ? TRUE : FALSE);

    if (method_exists($this, $task)) {
      if ($all) {
        $message = $this->$task(0, TRUE, TRUE);
        $url_arg['message'] = $message;
      }
      else {
        if ($check) {
          foreach ($check as $form_id => $item) {
            $message = $this->$task($form_id, TRUE);
            if ($message != 2) {
              // Increase successfully updated items count, if action doesn't failed.
              $successfully_updated++;
            }
          }
        }
        if ($successfully_updated) {
          $bulk_action = $this->bulk_action_name;
          $message = sprintf(_n('%s item successfully %s.', '%s items successfully %s.', $successfully_updated, BWG()->prefix), $successfully_updated, $this->actions[$task][$bulk_action]);
        }
        $key = ($message === 2 ? 'message' : 'msg');
        $url_arg[$key] = $message;
      }
    }

    WDWLibrary::redirect(add_query_arg($url_arg, admin_url('admin.php')));
  }

  /**
   * Add/Edit.
   *
   * @param int $id
   */
  public function edit( $id = 0 ) {
    $row = $this->model->get_row_data($id);
    if ($id && empty($row->slug)) {
      WDWLibrary::redirect(add_query_arg(array(
        'page' => $this->page,
        'task' => 'display',
      ), admin_url('admin.php')));
    }
    // Set params for view.
    $params = array();
    $params['id'] = $id;
    $params['row'] = $row;
    $params['form_action'] = add_query_arg(array(
      'page' => $this->page,
      'current_id' => $id,
      BWG()->nonce => wp_create_nonce($this->page),
    ), admin_url('admin.php'));
    $params['add_albums_galleries_action'] = add_query_arg(array(
      'action' => 'albumsgalleries_' . BWG()->prefix,
      'album_id' => $id,
      'width' => '785',
      'height' => '550',
      BWG()->nonce => wp_create_nonce('albumsgalleries_' . BWG()->prefix),
      'TB_iframe' => '1'
    ), admin_url('admin-ajax.php'));

    $params['add_preview_image_action'] = add_query_arg(array(
      'action' => 'addImages',
      'bwg_width' => '800',
      'bwg_height' => '550',
      'extensions' => 'jpg,jpeg,png,gif,svg',
      'callback' => 'bwg_add_preview_image',
      BWG()->nonce => wp_create_nonce('addImages'),
      'TB_iframe' => '1',
    ), admin_url('admin-ajax.php'));
    $params['preview_action'] = WDWLibrary::get_custom_post_permalink(array(
      'slug' => $row->slug,
      'post_type' => 'album',
    ));
    $params['shortcode_id'] = WDWLibrary::get_shortcode_id(array('slug' => $row->slug, 'post_type' => 'album'));
    $params['albums_galleries'] = $this->model->get_albums_galleries_data($id);

    $this->view->edit($params);
  }

  /**
   * Save.
   *
   * @param int $id
   */
  public function save( $id ) {
    $save = $this->model->save($id);
    WDWLibrary::redirect(add_query_arg(array(
      'page' => $this->page,
      'task' => 'edit',
      'current_id' => $save['current_id'],
      'message' => $save['message_id'],
    ), admin_url('admin.php')));
  }

  /**
   * Delete form by id.
   *
   * @param      $id
   * @param bool $bulk
   * @param bool $all
   *
   * @return int
   */
  public function delete( $id, $bulk = FALSE, $all = FALSE ) {
    $message = $this->model->delete($id, $all);
    if ($bulk) {
      return $message;
    }
    WDWLibrary::redirect(add_query_arg(array(
      'page' => $this->page,
      'task' => 'display',
      'message' => $message,
    ), admin_url('admin.php')));
  }

  /**
   * Publish.
   *
   * @param      $id
   * @param bool $bulk
   * @param bool $all
   *
   * @return int
   */
  public function publish( $id, $bulk = FALSE, $all = FALSE ) {
    global $wpdb;
    $where = ($all ? '' : ' WHERE id=%d');
    if ( $where != '' ) {
      $updated = $wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_album` SET published=1' . $where, $id));
    } else {
      $updated = $wpdb->query('UPDATE `' . $wpdb->prefix . 'bwg_album` SET published=1' . $where);
    }
    $message = 2;
    if ($updated) {
      $message = 9;
    }
    if ($bulk) {
      return $message;
    }
    else {
      WDWLibrary::redirect(add_query_arg(array(
        'page' => $this->page,
        'task' => 'display',
        'message' => $message,
      ), admin_url('admin.php')));
    }
  }

  /**
   * Unpublish.
   *
   * @param      $id
   * @param bool $bulk
   * @param bool $all
   *
   * @return int
   */
  public function unpublish( $id, $bulk = FALSE, $all = FALSE ) {
    global $wpdb;
    $where = ($all ? '' : ' WHERE id=%d');
    if ( $where != '' ) {
      $updated = $wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_album` SET published=0' . $where, $id));
    } else {
      $updated = $wpdb->query('UPDATE `' . $wpdb->prefix . 'bwg_album` SET published=0' . $where);

    }
    $message = 2;
    if ($updated) {
      $message = 10;
    }
    if ($bulk) {
      return $message;
    }
    else {
      WDWLibrary::redirect(add_query_arg(array(
        'page' => $this->page,
        'task' => 'display',
        'message' => $message,
      ), admin_url('admin.php')));
    }
  }

  /**
   * Duplicate by id.
   *
   * @param      $id
   * @param bool $bulk
   * @param bool $all
   *
   * @return int
   */
  public function duplicate( $id, $bulk = FALSE, $all = FALSE ) {
    $message_id = $this->model->duplicate($id, $all);
    if ($bulk) {
      return $message_id;
    }
    WDWLibrary::redirect(add_query_arg(array(
      'page' => $this->page,
      'task' => 'display',
      'message' => $message_id,
    ), admin_url('admin.php')));
  }
}
