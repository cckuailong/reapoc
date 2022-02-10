<?php

class WDIControllerGalleryBox {

  public function execute() {
    $ajax_task = WDILibrary::get('ajax_task', '', 'sanitize_text_field', 'POST');
    if (method_exists($this, $ajax_task)) {
      $this->$ajax_task();
    }
    else {
      $this->display();
    }
  }

  public function display() {
    require_once WDI_DIR . "/frontend/models/WDIModelGalleryBox.php";
    $model = new WDIModelGalleryBox();

    require_once WDI_DIR . "/frontend/views/WDIViewGalleryBox.php";
    $view = new WDIViewGalleryBox($model);
    $view->display();
  }
}