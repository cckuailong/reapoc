<?php
class BWGModelWidgetFrontEnd {
  public function get_tags_data($count = 0) {
	global $wpdb;
	$count = abs(intval($count));
  $limit = '';
  $format = '';
	if($count) {
	  $limit = ' LIMIT %';
	  $format = $count;
  }
    $rows = $wpdb->get_results($wpdb->prepare('SELECT
									`image`.`thumb_url` AS `thumb_url`,
									`image`.`id` AS `image_id`,
									`tags`.`name`,
									`tags`.`slug`,
									`tags`.`term_id`,
									`image`.`filetype`
								FROM ' . $wpdb->prefix . 'terms AS tags
								INNER JOIN ' . $wpdb->prefix . 'term_taxonomy AS taxonomy ON taxonomy.term_id=tags.term_id
								INNER JOIN
								(SELECT `image`.`thumb_url`, `tag`.`tag_id`, `image`.`id`, `image`.`filetype` FROM ' . $wpdb->prefix . 'bwg_image AS image
								INNER JOIN ' . $wpdb->prefix . 'bwg_image_tag AS tag ON image.id=tag.image_id ORDER BY RAND()) AS image ON image.tag_id=tags.term_id WHERE taxonomy.taxonomy="bwg_tag" GROUP BY tags.term_id' . $limit, $format));
    foreach ( $rows as $row ) {
      $row->permalink = WDWLibrary::get_custom_post_permalink(array( 'slug' => $row->slug, 'post_type' => 'tag' ));
    }

    return $rows;
  }
}
