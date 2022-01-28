<?php

/**
 * Class AddTagsModel_bwg
 */
class AddTagsModel_bwg {
  /**
   * Get rows data or total count.
   *
   * @param      $params
   * @param bool $total
   *
   * @return array|null|object|string
   */
  public function get_rows_data( $params, $total = FALSE ) {

    global $wpdb;
    $order = $params['order'];
    $orderby = $params['orderby'];
    $page_per = $params['items_per_page'];
    $page_num = $params['page_num'];
    $search = $params['search'];

    $prepareArgs = array("bwg_tag");

    if ( !$total ) {
      $query = 'SELECT table1.term_id as id, table1.name, table1.slug';
    }
    else {
      $query = 'SELECT COUNT(*)';
    }


    $query .= ' FROM `' . $wpdb->prefix . 'terms` AS table1';
    $query .= ' INNER JOIN `' . $wpdb->prefix . 'term_taxonomy` AS table2 ON table1.term_id = table2.term_id';
    $query .= ' WHERE table2.taxonomy=%s';

    if ( $search ) {
      $query .= ' AND `name` LIKE %s';
      $prepareArgs[] = "%" . $wpdb->esc_like($search) . "%";
    }
    if ( !$total ) {
      $query .= ' ORDER BY `' . $orderby . '` ' .$order;
      $query .= ' LIMIT %d, %d';
      $prepareArgs[] = $page_num;
      $prepareArgs[] = $page_per;
    }

    if ( !$total ) {
      $rows = $wpdb->get_results($wpdb->prepare($query, $prepareArgs));
    }
    else {
      $rows = $wpdb->get_var($wpdb->prepare($query, $prepareArgs));
    }

    return $rows;
  }

  /**
   * Return total count.
   *
   * @param $params
   *
   * @return array|null|object|string
   */
  public function total($params) {
    return $this->get_rows_data($params, TRUE);
  }
}
