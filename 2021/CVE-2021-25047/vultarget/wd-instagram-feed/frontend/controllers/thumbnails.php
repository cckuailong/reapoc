<?php

class WDI_Thumbnails_controller {

  public function execute( $feed_row, $wdi_feed_counter ) {
    //including model
    require_once(WDI_DIR . '/frontend/models/thumbnails.php');
    $model = new WDI_Thumbnails_model($feed_row, $wdi_feed_counter);
    //including view
    require_once(WDI_DIR . '/frontend/views/thumbnails.php');
    $view = new WDI_Thumbnails_view($model);
    $view->display();
  }
}