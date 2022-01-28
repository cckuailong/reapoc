<?php

class BWGControllerShare {

  public function __construct() {
  }

  public function execute() {
    $this->display();
  }

  public function display() {
    require_once BWG()->plugin_dir . "/frontend/models/BWGModelShare.php";
    $model = new BWGModelShare();

    require_once BWG()->plugin_dir . "/frontend/views/BWGViewShare.php";
    $view = new BWGViewShare($model);

    $view->display();
  }
}