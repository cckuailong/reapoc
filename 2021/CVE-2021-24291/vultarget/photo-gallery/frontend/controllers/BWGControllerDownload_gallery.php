<?php

class BWGControllerDownload_gallery {
  public function execute() {
    $this->display();
  }

  public function display() {
    require_once BWG()->plugin_dir . "/frontend/views/BWGViewDownload_gallery.php";
    $view = new BWGViewDownload_gallery();

    $params = array();

    $params['bwg'] = WDWLibrary::get('bwg');
    $params['search'] = WDWLibrary::get('bwg_search_'.$params['bwg']);
    $params['gallery_id'] = WDWLibrary::get('gallery_id');
    $params['type'] = WDWLibrary::get('type');
    $params['tag_input_name'] = WDWLibrary::get('tag_input_name');
    $params['tag'] = WDWLibrary::get('tag');

    $view->display( $params );
  }
}