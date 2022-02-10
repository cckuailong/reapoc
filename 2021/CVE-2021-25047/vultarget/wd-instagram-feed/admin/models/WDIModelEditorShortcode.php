<?php

class WDIModelEditorShortcode {

  public function __construct() {
  }

  public function get_row_data() {
    global $wpdb;
    $row = $wpdb->get_results("SELECT id, feed_name, feed_thumb FROM " . $wpdb->prefix . WDI_FEED_TABLE." WHERE published=1 ORDER BY `feed_name` ASC");
    return $row;
  }

  public function get_first_feed_id(){
    global $wpdb;
    $min_id = $wpdb->get_var('SELECT MIN(id) FROM ' . $wpdb->prefix . WDI_FEED_TABLE.' WHERE published=1');
    return $min_id;
  }
}