<?php

/**
 * Class UninstallModel_bwg
 */
class UninstallModel_bwg {
  /**
   * Delete images folder.
   */
  public function delete_folder() {
    $delete_files = WDWLibrary::get('bwg_delete_files');
    if ( !empty($delete_files) ) {
      function delfiles($del_file) {
        if (is_dir($del_file)) {
          $del_folder = scandir($del_file);
          foreach ($del_folder as $file) {
            if ($file != '.' and $file != '..') {
              delfiles($del_file . '/' . $file);
            }
          }
          rmdir($del_file);
        }
        else {
          unlink($del_file);
        }
      }

      if (BWG()->upload_dir) {
        if (is_dir(BWG()->upload_dir)) {
          delfiles(BWG()->upload_dir);
        }
      }
    }
  }

  /**
   * Delete DB tables and other data.
   */
  public function delete_db_tables($params) {
    global $wpdb;
    // Delete terms.
    $terms = get_terms('bwg_tag', array( 'orderby' => 'count', 'hide_empty' => 0 ));
    foreach ( $terms as $term ) {
      wp_delete_term($term->term_id, 'bwg_tag');
    }
    // Delete custom pages for galleries.
    $posts = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'bwg_gallery' ));
    foreach ( $posts as $post ) {
      wp_delete_post($post->ID, TRUE);
    }
    // Delete custom pages for albums.
    $posts = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'bwg_album' ));
    foreach ( $posts as $post ) {
      wp_delete_post($post->ID, TRUE);
    }
    // Delete custom pages for tags.
    $posts = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'bwg_tag' ));
    foreach ( $posts as $post ) {
      wp_delete_post($post->ID, TRUE);
    }
    // Delete custom pages for share.
    $posts = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'bwg_share' ));
    foreach ( $posts as $post ) {
      wp_delete_post($post->ID, TRUE);
    }
    foreach ( $params['tables'] as $table ) {
      $wpdb->query("DROP TABLE IF EXISTS `" . $table . "`");
    }
    delete_option("wd_bwg_version");
    delete_option('wd_bwg_initial_version');
    delete_option("bwg_subscribe_done");
    delete_option("wd_bwg_options");
    delete_option('tenweb_notice_status');
    delete_user_meta(get_current_user_id(), 'bwg_photo_gallery');
    delete_option('tenweb_notice_status');
    delete_option('tenweb_notice_version');

    if ( isset($_COOKIE['bwg_image_asc_or_desc']) ) {
      $_COOKIE['bwg_image_asc_or_desc'] = '';
    }
    if ( isset($_COOKIE['bwg_image_order_by']) ) {
      $_COOKIE['bwg_image_order_by'] = '';
    }
    do_action( 'bwg_uninstall_after' );
  }
}
