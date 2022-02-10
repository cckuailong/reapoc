<?php

class WDI_ImageBrowser_controller {

  public function execute( $feed_row, $wdi_feed_counter ) {
    //including model
    require_once(WDI_DIR . '/frontend/models/imagebrowser.php');
    $model = new WDI_ImageBrowser_model($feed_row, $wdi_feed_counter);
    //including view
    require_once(WDI_DIR . '/frontend/views/imagebrowser.php');
    $view = new WDI_ImageBrowser_view($model);
    $view->display();
  }
}