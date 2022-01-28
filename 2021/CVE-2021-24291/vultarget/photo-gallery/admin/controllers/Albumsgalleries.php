<?php

/**
* Class AlbumsgalleriesController_bwg
*/
class AlbumsgalleriesController_bwg {
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
		$this->model = new AlbumsgalleriesModel_bwg();
		$this->view = new AlbumsgalleriesView_bwg();
	}

	/**
	* Execute.
	*/
	public function execute() {
		$task = WDWLibrary::get('task');
		if ( method_exists($this, $task) ) {
			if ( $task != 'edit' && $task != 'display' ) {
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
    $params = array();
    $params['page_title'] = __('Galleries / Gallery groups', BWG()->prefix);
    $params['page_url'] = $this->page;
    $params['album_id'] = WDWLibrary::get('album_id', 0, 'intval');
    $params['order'] = WDWLibrary::get('order', 'asc');
    $params['orderby'] = WDWLibrary::get('orderby', 'is_album');
    // To prevent SQL injections.
    $params['order'] = ($params['order'] == 'desc') ? 'desc' : 'asc';
    if ( !in_array($params['orderby'], array( 'name', 'slug' )) ) {
      $params['orderby'] = 'is_album';
    }
    $params['items_per_page'] = $this->items_per_page;
    $page = WDWLibrary::get('paged', 1, 'intval');
    $page_num = $page ? ($page - 1) * $params['items_per_page'] : 0;
    $params['page_num'] = $page_num;
    $params['search'] = WDWLibrary::get('s');

    $params['rows'] = $this->model->get_rows_data($params);
    $params['total'] = $this->model->total($params);

    $this->view->display($params);
  }
}