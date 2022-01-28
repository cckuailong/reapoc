<?php
/**
 * Class ThemesController_bwg
 */
class ThemesController_bwg {
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
    $this->model = new ThemesModel_bwg();
    $this->view = new ThemesView_bwg();

    $this->page = WDWLibrary::get('page');

    $this->actions = array(
      'duplicate' => array(
        'title' => __('Duplicate', BWG()->prefix),
        $this->bulk_action_name => __('duplicated', BWG()->prefix),
      ),
      'delete' => array(
        'title' => __('Delete', BWG()->prefix),
        $this->bulk_action_name => __('deleted', BWG()->prefix),
      ),
    );

    $user = get_current_user_id();
    $screen = get_current_screen();
    $option = $screen->get_option('per_page', 'option');
    $this->items_per_page = get_user_meta($user, $option, true);

    if ( empty ( $this->items_per_page) || $this->items_per_page < 1 ) {
      $this->items_per_page = $screen->get_option( 'per_page', 'default' );
    }
  }

  /**
   * Execute.
   */
  public function execute() {
    $task = WDWLibrary::get('task');
    $id = (int) WDWLibrary::get('current_id', 0);
    if ( $task != 'display' && method_exists($this, $task) ) {
      if ( $task != 'add' && $task != 'edit' ) {
        check_admin_referer(BWG()->nonce, BWG()->nonce);
      }
      $action = WDWLibrary::get('bulk_action', -1);
      if ( $action != -1 ) {
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
    $params['page_title'] = __('Themes', BWG()->prefix);
    $params['actions'] = $this->actions;
    $params['order'] = WDWLibrary::get('order', 'desc');
    $params['orderby'] = WDWLibrary::get('orderby', 'default_theme');
    // To prevent SQL injections.
    $params['order'] = ($params['order'] == 'desc') ? 'desc' : 'asc';
    if ( !in_array($params['orderby'], array( 'name', 'default_theme' )) ) {
      $params['orderby'] = 'default_theme';
    }
    $params['items_per_page'] = $this->items_per_page;
    $page = (int) WDWLibrary::get('paged', 1);
    $page_num = $page ? ($page - 1) * $params['items_per_page'] : 0;
    $params['page_num'] = $page_num;
    $params['search'] = WDWLibrary::get('s', '');;
    $params['total'] = $this->model->total($params);
    $params['rows_data'] = $this->model->get_rows_data($params);
    $this->view->display($params);
  }

  /**
   * Bulk actions.
   *
   * @param $task
   */
  public function bulk_action($task) {
    $message = 0;
    $successfully_updated = 0;

    $check = WDWLibrary::get('check', '');
    $all = WDWLibrary::get('check_all_items', '');
    $all = ($all == 'on' ? TRUE : FALSE);

    if ( $check ) {
      foreach ( $check as $form_id => $item ) {
        if ( method_exists($this, $task) ) {
          $message = $this->$task($form_id, TRUE, $all);
          if ( $message != 2 ) {
            // Increase successfully updated items count, if action doesn't failed.
            $successfully_updated++;
          }
        }
      }
      if ( $successfully_updated ) {
        $block_action = $this->bulk_action_name;
        $message = sprintf(_n('%s item successfully %s.', '%s items successfully %s.', $successfully_updated, BWG()->prefix), $successfully_updated, $this->actions[$task][$block_action]);
      }
    }
    WDWLibrary::redirect(add_query_arg(array(
                                         'page' => $this->page,
                                         'task' => 'display',
                                         ($message === 2 ? 'message' : 'msg') => $message,
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
    $isDefault = $this->model->get_default($id);
    if ( $isDefault ) {
      $message = 4;
    }
    else {
      global $wpdb;
      $where = ($all ? '' : ' WHERE id=%d');
      if( $where != '' ) {
          $delete = $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_theme`' . $where, $id));
      } else {
          $delete = $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'bwg_theme`' . $where);
      }
      if ( $delete ) {
        $message = 3;
      }
      else {
        $message = 2;
      }
    }
    if ( $bulk ) {
      return $message;
    }
    WDWLibrary::redirect( add_query_arg( array('page' => $this->page, 'task' => 'display', 'message' => $message), admin_url('admin.php') ) );
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
    $message = 2;
    $table = 'bwg_theme';
    $row = $this->model->select_rows("get_row", array(
      "selection" => "*",
      "table" => $table,
      "where" => "id=" . (int) $id,
    ));
    if ( $row ) {
      $row = (array) $row;
      unset($row['id']);
      $row['default_theme'] = 0;
      $inserted = $this->model->insert_data_to_db($table, (array) $row);
      if ( $inserted !== FALSE ) {
        $message = 11;
      }
    }
    if ( $bulk ) {
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
   * Add.
   *
   * @param int  $id
   */
	public function add( $id = 0 ) {
		$this->edit(0);
	}

  /**
   * Edit by id.
   *
   * @param int  $id
   * @param bool $bulk
   */
  public function edit( $id = 0, $bulk = FALSE ) {
    $reset = WDWLibrary::get('reset', FALSE);
    // Get Theme data.
    $row = $this->model->get_row_data($id, $reset);
		$current_type = WDWLibrary::get('current_type', 'Thumbnail');
		$form_action  = add_query_arg( array(
                                'page' => 'themes_' . BWG()->prefix,
								                'current_id' => $id,
                                BWG()->nonce => wp_create_nonce(BWG()->nonce),
							), admin_url('admin.php') );

		$tabs = array(
			'Thumbnail' => __('Thumbnail', BWG()->prefix),
			'Masonry' => __('Masonry', BWG()->prefix),
			'Mosaic' => __('Mosaic', BWG()->prefix),
			'Slideshow' => __('Slideshow', BWG()->prefix),
			'Image_browser' => __('Image browser', BWG()->prefix),
			'Compact_album' => __('Compact album', BWG()->prefix),
			'Masonry_album' => __('Masonry album', BWG()->prefix),
			'Extended_album' => __('Extended album', BWG()->prefix),
			'Blog_style' => __('Blog style', BWG()->prefix),
			'Lightbox' => __('Lightbox', BWG()->prefix),
			'Navigation' => __('Navigation', BWG()->prefix),
			'Carousel' => __('Carousel', BWG()->prefix),
		);

		$border_styles = array(
			'none' => __('None', BWG()->prefix),
			'solid' => __('Solid', BWG()->prefix),
			'dotted' => __('Dotted', BWG()->prefix),
			'dashed' => __('Dashed', BWG()->prefix),
			'double' => __('Double', BWG()->prefix),
			'groove' => __('Groove', BWG()->prefix),
			'ridge' => __('Ridge', BWG()->prefix),
			'inset' => __('Inset', BWG()->prefix),
			'outset' => __('Outset', BWG()->prefix),
		);

		$google_fonts = WDWLibrary::get_google_fonts();
		$font_families = array(
			'arial' => 'Arial',
			'lucida grande' => 'Lucida grande',
			'segoe ui' => 'Segoe ui',
			'tahoma' => 'Tahoma',
			'trebuchet ms' => 'Trebuchet ms',
			'verdana' => 'Verdana',
			'cursive' =>'Cursive',
			'fantasy' => 'Fantasy',
			'monospace' => 'Monospace',
			'serif' => 'Serif',
		);

		$aligns = array(
			'left' 	=> __('Left', BWG()->prefix),
			'center' 	=> __('Center', BWG()->prefix),
			'right' 	=> __('Right', BWG()->prefix),
		);

		$font_weights = array(
			'lighter' => __('Lighter', BWG()->prefix),
			'normal' => __('Normal', BWG()->prefix),
			'bold' => __('Bold', BWG()->prefix),
		);

		// ToDO: Remove after global update.
		$hover_effects = array(
			'none' => __('None', BWG()->prefix),
			'rotate' => __('Rotate', BWG()->prefix),
			'scale' => __('Scale', BWG()->prefix),
			'skew' => __('Skew', BWG()->prefix),
		);

		$thumbnail_hover_effects = array(
		  'none' => __('None', BWG()->prefix),
		  'rotate' => __('Rotate', BWG()->prefix),
		  'scale' => __('Scale', BWG()->prefix),
		  'zoom' => __('Zoom', BWG()->prefix),
		  'skew' => __('Skew', BWG()->prefix),
		);

		$button_styles = array(
			'bwg-icon-angle' => __('Angle', BWG()->prefix),
			'bwg-icon-chevron' => __('Chevron', BWG()->prefix),
			'bwg-icon-double' => __('Double', BWG()->prefix),
		);

		$rate_icons = array(
			'star' => __('Star', BWG()->prefix),
			'bell' => __('Bell', BWG()->prefix),
			'circle' => __('Circle', BWG()->prefix),
			'flag' => __('Flag', BWG()->prefix),
			'heart' => __('Heart', BWG()->prefix),
			'square' => __('Square', BWG()->prefix),
		);

		$active_tab = WDWLibrary::get('active_tab', 'Thumbnail');

		$params = array(
			'id' => $id,
			'row' => $row,
			'reset' => $reset,
			'form_action' => $form_action,
			'tabs' => $tabs,
			'current_type' => $current_type,
			'border_styles' => $border_styles,
			'google_fonts' => $google_fonts,
			'font_families' => $font_families,
			'aligns' => $aligns,
			'font_weights' => $font_weights,
			'hover_effects' => $hover_effects,
			'thumbnail_hover_effects' => $thumbnail_hover_effects,
			'button_styles' => $button_styles,
			'rate_icons' => $rate_icons,
      'active_tab' => $active_tab,
		);
		$this->view->edit( $params );
	}


  /**
   * Reset by id.
   *
   * @param int $id
   */
  public function reset( $id = 0 ) {
    WDWLibrary::redirect(add_query_arg(array(
                                         'page' => $this->page,
                                         'task' => 'edit',
                                         'current_id' => $id,
                                         'reset' => '1',
                                       ), admin_url('admin.php')));
  }

  /**
   * Save by id.
   *
   * @param int $id
   */
  public function save( $id = 0 ) {
    $data = $this->save_db($id);
    $active_tab = WDWLibrary::get('active_tab','Thumbnail');
    $page = WDWLibrary::get('page');
    $query_url = wp_nonce_url(admin_url('admin.php'), 'themes_bwg', 'bwg_nonce');
    $query_url = add_query_arg(array(
                                 'page' => $page,
                                 'task' => 'edit',
                                 'current_id' => $data['id'],
                                 'active_tab' => $active_tab,
                                 'message' => $data['msg'],
                               ), $query_url);
    WDWLibrary::spider_redirect($query_url);
  }
}
