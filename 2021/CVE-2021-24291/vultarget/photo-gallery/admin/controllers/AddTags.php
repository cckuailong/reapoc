<?php

/**
 * Class AddTagsController_bwg
 */
class AddTagsController_bwg {
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
   * @var int $items_per_page
   */
  private $items_per_page = 20;

  public function __construct() {
    $this->model = new AddTagsModel_bwg();
    $this->view = new AddTagsView_bwg();
    $this->page = WDWLibrary::get('page');
  }

  /**
   * Execute.
   */
  public function execute() {
    $task = WDWLibrary::get('task');
    if ( method_exists($this, $task) ) {
      if ( $task != 'display' ) {
        check_admin_referer(BWG()->nonce, BWG()->nonce);
      }
      $this->$task();
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
    $params['page_title'] = __('Tags', BWG()->prefix);
    $params['order'] = WDWLibrary::get('order', 'asc');
    $params['orderby'] = WDWLibrary::get('orderby', 'name');
    // To prevent SQL injections.
    $params['order'] = ($params['order'] == 'desc') ? 'desc' : 'asc';
    if ( !in_array($params['orderby'], array( 'name', 'slug' )) ) {
      $params['orderby'] = 'id';
    }
    $params['items_per_page'] = $this->items_per_page;
    $page = (int) WDWLibrary::get('paged', 1);
    $page_num = $page ? ($page - 1) * $params['items_per_page'] : 0;
    $params['page_num'] = $page_num;
    $params['search'] = WDWLibrary::get('s', '');

    $params['total'] = $this->model->total($params);
    $params['rows'] = $this->model->get_rows_data($params);

    $this->view->display($params);
  }
}
