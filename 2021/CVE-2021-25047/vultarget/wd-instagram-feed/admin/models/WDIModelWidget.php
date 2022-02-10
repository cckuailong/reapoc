<?php

class WDIModelWidget {
  
  public function __construct() {
  }
  
  public function get_feeds() {
    global $wpdb;
    $query = "SELECT id,feed_name,feed_type FROM " . $wpdb->prefix . WDI_FEED_TABLE." WHERE published=1";
    $rows = $wpdb->get_results($query);
    return $rows;
  }
}