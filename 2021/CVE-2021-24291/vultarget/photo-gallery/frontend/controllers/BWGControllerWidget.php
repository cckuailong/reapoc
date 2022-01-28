<?php
class BWGControllerWidgetFrontEnd {

  public function __construct() {
  }

  public function execute($params = array(), $from_shortcode = "tags") {
    if ($from_shortcode == 'tags') {
      $this->view_tags($params);
    }
  }

  public function view_tags($params = array()) {
    require_once BWG()->plugin_dir . "/frontend/models/BWGModelWidget.php";
    $model = new BWGModelWidgetFrontEnd();

    require_once BWG()->plugin_dir . "/frontend/views/BWGViewWidget.php";
    $view = new BWGViewWidgetFrontEnd($model);
    $view->view_tags($params);
  }
}