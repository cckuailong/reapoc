<?php

/**
 * Class EditimageModel_bwg
 */
class EditimageModel_bwg {
  public function get_image_data() {
    $id = (int) WDWLibrary::get('image_id', 0);
    global $wpdb;
    $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'bwg_image WHERE id="%d"', $id));
    return $row;
  }
}
