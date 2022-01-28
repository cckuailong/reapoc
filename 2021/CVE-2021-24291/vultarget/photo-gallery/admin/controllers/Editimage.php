<?php

/**
 * Class EditimageController_bwg
 */
class EditimageController_bwg {
  /**
   * @var $model
   */
  private $model;
  /**
   * @var $view
   */
  private $view;

  public function __construct() {
    $this->model = new EditimageModel_bwg();
    $this->view = new EditimageView_bwg();
  }

  public function execute() {
    $task = WDWLibrary::get('task');
    if ( $task != 'display' && method_exists($this, $task) ) {
        $this->$task();
    }
    else {
      $this->display();
    }
  }

  public function display() {
    $image_data = array();
    $type = WDWLibrary::get('type', 'display');
    if ( $type == 'crop' || $type == 'rotate' ) {
      $image_data = $this->model->get_image_data();
    }
    $this->view->$type($image_data);
  }
}
