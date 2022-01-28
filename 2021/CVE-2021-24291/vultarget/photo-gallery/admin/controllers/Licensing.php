<?php

/**
 * Class LicensingController_bwg
 */
class LicensingController_bwg {
  private $view;
  public function __construct() {
    require_once BWG()->plugin_dir . "/admin/views/Licensing.php";
    $this->view = new LicensingView_bwg();
  }

  public function execute() {
    $task = WDWLibrary::get('task');
    if (method_exists($this, $task)) {
      $this->$task();
    }
    else {
      $this->display();
    }
  }

  public function display() {
    $this->view->display();
  }
}
