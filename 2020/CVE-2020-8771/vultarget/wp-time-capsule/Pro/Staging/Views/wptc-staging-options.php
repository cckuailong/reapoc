<?php
class Staging_Options_Wptc{

  public function __construct() {
    $this->init();
  }

  public function init(){
    $this->get_status();
  }

  private function get_status(){
    $this->print_title();
  }

  private function print_title(){
    echo "<h2 id='staging_area_wptc'>Staging Area</h2> <div id='staging_current_progress' style='display:none'>Checking status...</div>";
  }
}

new Staging_Options_Wptc();