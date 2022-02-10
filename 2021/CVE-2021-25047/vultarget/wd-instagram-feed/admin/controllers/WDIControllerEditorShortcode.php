<?php

class WDIControllerEditorShortcode {

  public function execute() {
    $this->display();
  }

  public function display() {
    require_once WDI_DIR . "/admin/models/WDIModelEditorShortcode.php";
    $model = new WDIModelEditorShortcode();

    require_once WDI_DIR . "/admin/views/WDIViewEditorShortcode.php";
    $view = new WDIViewEditorShortcode($model);
    $view->display();
  }
}