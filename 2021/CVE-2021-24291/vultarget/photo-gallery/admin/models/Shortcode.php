<?php

class ShortcodeModel_bwg {

  public function get_shortcode_data() {
    global $wpdb;
    $shortcode = $wpdb->get_results("SELECT id, tagtext FROM " . $wpdb->prefix . "bwg_shortcode ORDER BY `id` ASC");

    return $shortcode;
  }

  public function get_shortcode_max_id() {
    global $wpdb;
    $max_id = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "bwg_shortcode");
    return $max_id;
  }

  public function get_gallery_rows_data() {
    global $wpdb;
    $query = "SELECT * FROM " . $wpdb->prefix . "bwg_gallery WHERE published=1 ORDER BY name";
    $rows = $wpdb->get_results($query);
    return $rows;
  }

  public function get_album_rows_data() {
    global $wpdb;
    $query = "SELECT * FROM " . $wpdb->prefix . "bwg_album WHERE published=1 ORDER BY name";
    $rows = $wpdb->get_results($query);
    return $rows;
  }

  public function get_theme_rows_data() {
    global $wpdb;
    $query = "SELECT * FROM " . $wpdb->prefix . "bwg_theme ORDER BY `default_theme` DESC, `name`";
    $rows = $wpdb->get_results($query);
    return $rows;
  }

  public function get_tag_rows_data() {
    global $wpdb;
    $query ="SELECT * FROM ".$wpdb->prefix."terms as A LEFT JOIN ".$wpdb->prefix ."term_taxonomy as B ON A.term_id = B.term_id WHERE B.taxonomy='bwg_tag'";
    $rows = $wpdb->get_results($query);
    return $rows;
  }
}