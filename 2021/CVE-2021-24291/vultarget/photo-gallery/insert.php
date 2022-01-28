<?php

/**
 * Class BWGInsert
 */
class BWGInsert {
  public static function tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $bwg_shortcode = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_shortcode` (
    `id` bigint(20) NOT NULL,
    `tagtext` mediumtext NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_shortcode);

    $bwg_gallery = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_gallery` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description` mediumtext NOT NULL,
    `page_link` mediumtext NOT NULL,
    `preview_image` mediumtext NOT NULL,
    `random_preview_image` mediumtext NOT NULL,
    `order` bigint(20) NOT NULL,
    `author` bigint(20) NOT NULL,
    `published` tinyint(1) NOT NULL,
    `gallery_type` varchar(32) NOT NULL,
    `gallery_source` varchar(256) NOT NULL,
    `autogallery_image_number` int(4) NOT NULL,
    `update_flag` varchar(32) NOT NULL,
	`modified_date` int(10) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_gallery);

    $bwg_album = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_album` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description` mediumtext NOT NULL,
    `preview_image` mediumtext NOT NULL,
    `random_preview_image` mediumtext NOT NULL,
    `order` bigint(20) NOT NULL,
    `author` bigint(20) NOT NULL,
    `published` tinyint(1) NOT NULL,
	`modified_date` int(10) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_album);

    $bwg_album_gallery = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_album_gallery` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `album_id` bigint(20) NOT NULL,
    `is_album` tinyint(1) NOT NULL,
    `alb_gal_id` bigint(20) NOT NULL,
    `order` bigint(20) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_album_gallery);

    $bwg_image = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_image` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `gallery_id` bigint(20) NOT NULL,
    `slug` longtext NOT NULL,
    `filename` varchar(255) NOT NULL,
    `image_url` mediumtext NOT NULL,
    `thumb_url` mediumtext NOT NULL,
    `description` mediumtext NOT NULL,
    `alt` mediumtext NOT NULL,
    `date` varchar(128) NOT NULL,
    `size` varchar(128) NOT NULL,
    `filetype` varchar(128) NOT NULL,
    `resolution` varchar(128) NOT NULL,
    `resolution_thumb` varchar(128) NOT NULL,
    `author` bigint(20) NOT NULL,
    `order` bigint(20) NOT NULL,
    `published` tinyint(1) NOT NULL,
    `comment_count` bigint(20) NOT NULL,
    `avg_rating` float(20) NOT NULL,
    `rate_count` bigint(20) NOT NULL,
    `hit_count` bigint(20) NOT NULL,
    `redirect_url` varchar(255) NOT NULL,
    `pricelist_id` bigint(20) NOT NULL,
	`modified_date` int(10) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_image);

    $bwg_image_tag = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_image_tag` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `tag_id` bigint(20) NOT NULL,
    `image_id` bigint(20) NOT NULL,
    `gallery_id` bigint(20) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_image_tag);

    $bwg_theme = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_theme` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `options` longtext NOT NULL,
    `default_theme` tinyint(1) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_theme);

    $bwg_image_comment = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_image_comment` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `image_id` bigint(20) NOT NULL,
    `name` varchar(255) NOT NULL,
    `date` varchar(64) NOT NULL,
    `comment` mediumtext NOT NULL,
    `url` mediumtext NOT NULL,
    `mail` mediumtext NOT NULL,
    `published` tinyint(1) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_image_comment);

    $bwg_image_rate = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_image_rate` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `image_id` bigint(20) NOT NULL,
    `rate` float(16) NOT NULL,
    `ip` varchar(64) NOT NULL,
    `date` varchar(64) NOT NULL,
    PRIMARY KEY (`id`)
  ) " . $charset_collate . ";";
    $wpdb->query($bwg_image_rate);

    $exists_default = $wpdb->get_var('SELECT count(id) FROM ' . $wpdb->prefix . 'bwg_theme');

    if (!$exists_default) {
      $theme1 = new WD_BWG_Theme(0, false, 'Light');
      $wpdb->insert($wpdb->prefix . 'bwg_theme', array(
        'id' => 1,
        'name' => __('Light', BWG()->prefix),
        'options' => json_encode($theme1),
        'default_theme' => 1
      ));
      $theme2 = new WD_BWG_Theme(0, false, 'Dark');
      $wpdb->insert($wpdb->prefix . 'bwg_theme', array(
        'id' => 2,
        'name' => __('Dark', BWG()->prefix),
        'options' => json_encode($theme2),
        'default_theme' => 0
      ));
    }

	$file_paths = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "bwg_file_paths` (
		`id` bigint(20) NOT NULL AUTO_INCREMENT,
		`is_dir` tinyint(1) DEFAULT 0,
		`path` mediumtext,
		`type` varchar(5),
		`name` varchar(250),
		`filename` varchar(250),
		`alt` varchar(250),
		`thumb` varchar(250),
		`size` varchar(10),
		`resolution` varchar(15),
		`resolution_thumb` varchar(15),
		`credit` varchar(250),
		`aperture` int(10),
		`camera` varchar(250),
		`caption` varchar(250),
		`iso` int(10),
		`orientation` int(10),
		`copyright` varchar(250),
		`tags` mediumtext,
		`date_modified` datetime,
		`author` bigint(20) DEFAULT 1,
		PRIMARY KEY (`id`)
	) " . $charset_collate . ";";
	$wpdb->query($file_paths);
  }
}
