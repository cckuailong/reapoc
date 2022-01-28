<?php
/**
 * Class ThemesModel_bwg
 */
class ThemesModel_bwg {
  /**
   * Get rows data.
   *
   * @param  $params
   *
   * @return array $rows
   */
  public function get_rows_data( $params, $total = FALSE ) {
    global $wpdb;
    $order = $params['order'];
    $orderby = $params['orderby'];
    $page_per = $params['items_per_page'];
    $page_num = $params['page_num'];
    $search = $params['search'];

    $prepareArgs = array();
    if ( !$total ) {
      $query = 'SELECT *';
    }
    else {
      $query = 'SELECT COUNT(*)';
    }
    $query .= ' FROM `' . $wpdb->prefix . 'bwg_theme` AS `t`';

    if ( $search ) {
      $query .= ' WHERE `t`.`name` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }

    if ( !$total ) {
      $query .= ' ORDER BY `t`.`' . $orderby . '` ' . $order;
      $query .= ' LIMIT %d, %d';
      $prepareArgs[] = $page_num;
      $prepareArgs[] = $page_per;
    }
    if ( !$total ) {
      if ( !empty($prepareArgs) ) {
          $rows = $wpdb->get_results( $wpdb->prepare( $query, $prepareArgs ) );
      } else {
          $rows = $wpdb->get_results($query);
      }
    }
    else {
      if ( !empty($prepareArgs) ) {
          $rows = $wpdb->get_var($wpdb->prepare($query, $prepareArgs));
      } else {
          $rows = $wpdb->get_var($query);
      }
    }
    return $rows;
  }
  
  public function get_row_data($id, $reset = FALSE) {
    $row = new WD_BWG_Theme($id, $reset);
    return $row;
  }

  /**
   * Return total count of themes.
   *
   * @param $params
   *
   * @return array|null|object|string
   */
  public function total($params) {
    return $this->get_rows_data($params, TRUE);
  }

  /**
   * Delete row(s) from db.
   *
   * @param array $params
   * params = [selection, table, where, order_by, limit]
   *
   * @return false|int
   */
  public function delete_rows( $params ) {
    global $wpdb;
    $query = 'DELETE FROM ' . $wpdb->prefix . $params['table'];
    if ( isset($params['where']) ) {
      $where = $params['where'];
      $query .= ' WHERE ' . $where;
    }
    if ( isset($params['order_by']) ) {
      $query .= ' ' . $params['order_by'];
    }
    if ( isset($params['limit']) ) {
      $query .= ' ' . $params['limit'];
    }

    return $wpdb->query($query);
  }

  /**
   * Get row(s) from db.
   *
   * @param string $get_type
   * @param array  $params
   * params = [selection, table, where, order_by, limit]
   *
   * @return array
   */
  public function select_rows( $get_type, $params ) {
    global $wpdb;
    $query = "SELECT " . $params['selection'] . " FROM " . $wpdb->prefix . $params['table'];
    if ( isset($params['where']) ) {
      $query .= " WHERE " . $params['where'];
    }
    if ( isset($params['order_by']) ) {
      $query .= " " . $params['order_by'];
    }
    if ( isset($params['limit']) ) {
      $query .= " " . $params['limit'];
    }
    if ( $get_type == "get_col" ) {
      return $wpdb->get_col($query);
    }
    elseif ( $get_type == "get_var" ) {
      return $wpdb->get_var($query);
    }

    return $wpdb->get_row($query);
  }

  /**
   * Get request value.
   *
   * @param $table
   * @param $data
   *
   * @return false|int
   */
  public function insert_data_to_db( $table, $data ) {
    global $wpdb;
    $query = $wpdb->insert($wpdb->prefix . $table, $data);
    $wpdb->show_errors();

    return $query;
  }

  /**
   * Check if theme is default.
   *
   * @params int $id
   *
   * @return string
   */
  public function get_default( $id ) {
    global $wpdb;

    return $wpdb->get_var($wpdb->prepare('SELECT `default_theme` FROM `' . $wpdb->prefix . 'bwg_theme` WHERE id="%d"', $id));
  }

  /**
   * Update DB.
   *
   * @params array $params
   * @params array $where
   *
   * @return bool
   */
  public function update( $params, $where ) {
    global $wpdb;

    return $wpdb->update($wpdb->prefix . 'bwg_theme', $params, $where);
  }
}
