<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 5/14/18
 * Time: 4:26 PM
 */

class ecwd_events_query {

  private $query_args = array();

  public function __construct(){
    $this->set_default_query_args();
  }

  /**
   * @param $events_ids array
   * @return array
   * */
  public function get_metas($events_ids){
    global $wpdb;

    $ids = implode("','", $events_ids);
    $ids = "'" . $ids . "'";


    $table = $wpdb->postmeta;
    $query = "SELECT * FROM " . $table . " where post_id IN (" . $ids . ") AND meta_key LIKE 'ecwd_%';";

    return $wpdb->get_results($query, OBJECT);
  }

  /**
   * @return array
   * */
  public function get_posts(){

    $wp_query = new WP_Query();
    $events = $wp_query->query($this->query_args);

    $this->reset_query();
    return $events;
  }


  /**
   * Search on post title and description
   * @param $search string
   **/
  public function search($search){
    $this->set_query_arg('s', $search);
  }

  public function get_all_events(){
    return $this->query();
  }

  /**
   * @param $from string date format Y/m/d
   * @param $to string date format Y/m/d
   * */
  public function filter_by_date($from = "", $to = ""){

    $date_query = array();

    $from_query = array();
    if(!empty($from)) {

      $from_query = array(
        'key' => 'ecwd_event_date_from',
        'value' => $from,
        'compare' => '>=',
        'type' => 'DATE'
      );

    }

    $to_query = array();
    if(!empty($to)) {

      $to_query = array(
        'key' => 'ecwd_event_date_to',
        'value' => $to,
        'compare' => '<=',
        'type' => 'DATE'
      );

    }

    if(!empty($to_query) && !empty($from_query)) {

      $date_query = array(
        'relation' => 'AND',
        $from_query,
        $to_query,
      );

    } else if(!empty($to_query)) {
      $date_query = $to_query;
    } else if(!empty($from_query)) {
      $date_query = $from_query;
    }

    $this->add_query_arg('meta_query', $date_query);
  }


  /**
   * @param $from string date format Y/m/d
   * @param $to string date format Y/m/d
   * */
  public function filter_recurring($from, $to){

    $date_query = array(
      'relation' => 'AND',
      array(
        'key' => 'ecwd_event_date_from',
        'value' => $to,
        'compare' => '<=',
        'type' => 'DATE'
      ),
      array(
        'relation' => 'OR',
        array(
          'relation' => 'AND',
          array(
            'key' => 'ecwd_event_repeat_event',
            'value' => "no_repeat",
            'compare' => '=',
          ),
          array(
            'key' => 'ecwd_event_date_to',
            'value' => $from,
            'compare' => '>=',
            'type' => 'DATE'
          )
        ),
        array(
          'relation' => 'AND',
          array(
            'key' => 'ecwd_event_repeat_event',
            'value' => "no_repeat",
            'compare' => '!=',
          ),
          array(
            'key' => 'ecwd_event_repeat_repeat_until',
            'value' => $from,
            'compare' => '>=',
            'type' => 'DATE'
          )
        )
      )

    );

    $this->add_query_arg('meta_query', $date_query);
  }

  public function filter_past_events(){
    $date_query = array(
      array(
        'relation' => 'AND',
        array(
          'key' => 'ecwd_event_repeat_event',
          'value' => "no_repeat",
          'compare' => '=',
        ),
        array(
          'key' => 'ecwd_event_date_to',
          'value' => ECWD::ecwd_date('Y/m/d', time()),
          'compare' => '<',
          'type' => 'DATE'
        )
      ),
    );

    $this->add_query_arg('meta_query', $date_query);
  }


  public function filter_excluded_events($calendar_id){

    $exclude_query = array(
      'relation' => 'OR',
      array(
        'key' => ECWD_PLUGIN_PREFIX . '_event_calendars',
        'value' => serialize(strval($calendar_id)),
        'compare' => 'NOT LIKE'
      ),
      array(
        'key' => ECWD_PLUGIN_PREFIX . '_event_calendars',
        'compare' => 'NOT EXISTS'
      ),
      array(
        'key' => ECWD_PLUGIN_PREFIX . '_event_calendars',
        'value' => '',
      ),
    );

    $this->add_query_arg('meta_query', $exclude_query);
  }

  /**
   * @param $calendar_id string|array
   * @param $exclude boolean exclude authors no
   * */
  public function filter_by_events_id($events_id=array(), $exclude = false){

    if($exclude === false){
      $this->set_query_arg('post__in', $events_id);
    }else{
      $this->set_query_arg('post__not_in', $events_id);
    }

  }

  /**
   * @param $calendar_id string|array
   * @param $exclude boolean exclude authors no
   * @param $relation string OR|AND
   * */
  public function filter_by_calendars($calendar_id, $exclude = false, $relation = 'OR'){

    $compare = ($exclude === false) ? "LIKE" : "NOT LIKE";
    if(!is_array($calendar_id)) {
      $calendar_query = array(
        'key' => 'ecwd_event_calendars',
        'value' => serialize(strval($calendar_id)),
        'compare' => $compare
      );
      $this->add_query_arg('meta_query', $calendar_query);
      return;
    }


    $calendar_query = array();
    foreach($calendar_id as $cal_id) {
      $calendar_query[] = array(
        'key' => 'ecwd_event_calendars',
        'value' => serialize(strval($cal_id)),
        'compare' => $compare
      );
    }

    $calendar_query['relation'] = $relation;

    $this->add_query_arg('meta_query', $calendar_query);
  }

  /**
   * @param $venue_id string|array
   * @param $relation string OR|AND
   * */
  public function filter_by_venues($venue_id){

    if(!is_array($venue_id)) {
      $calendar_query = array(
        'key' => 'ecwd_event_venue',
        'value' => $venue_id,
        'compare' => "="
      );
      $this->add_query_arg('meta_query', $calendar_query);
      return;
    }


    $calendar_query = array();
    foreach($venue_id as $v_id) {
      $calendar_query[] = array(
        'key' => 'ecwd_event_venue',
        'value' => $v_id,
        'compare' => "="
      );
    }

    $calendar_query['relation'] = 'OR';

    $this->add_query_arg('meta_query', $calendar_query);
  }

  /**
   * @param $organizer_id string|array
   * @param $exclude boolean exclude authors no
   * @param $relation string OR|AND
   * */
  public function filter_by_organizers($organizer_id, $exclude = false, $relation = 'OR'){

    $compare = ($exclude === false) ? "LIKE" : "NOT LIKE";
    if(!is_array($organizer_id)) {
      $calendar_query = array(
        'key' => 'ecwd_event_organizers',
        'value' => serialize(strval($organizer_id)),
        'compare' => $compare
      );
      $this->add_query_arg('meta_query', $calendar_query);
      return;
    }


    $calendar_query = array();
    foreach($organizer_id as $cal_id) {
      $calendar_query[] = array(
        'key' => 'ecwd_event_organizers',
        'value' => serialize(strval($cal_id)),
        'compare' => $compare
      );
    }

    $calendar_query['relation'] = $relation;

    $this->add_query_arg('meta_query', $calendar_query);
  }

  /**
   * @param $from string date format Y/m/d
   * @param $to string date format Y/m/d
   * @param $relation string AND|OR
   * */
  public function filter_by_taxonomies($categories = array(), $tags = array(), $relation = 'OR'){

    $tax_query = array();

    if(!empty($categories)) {

      $tax_query[] = array(
        'taxonomy' => 'ecwd_event_category',
        'terms' => $categories
      );

    }

    if(!empty($tags)) {

      $tax_query[] = array(
        'taxonomy' => 'ecwd_event_tag',
        'terms' => $tags
      );

    }

    $tax_query['relation'] = $relation;
    $this->set_query_arg('tax_query', $tax_query);
  }


  /**
   * @param $value string|int|array author id or ids
   * @param $exclude boolean exclude authors no
   * */
  public function filter_by_author($author_id, $exclude = false){

    if(!is_array($author_id)) {
      $author_id = array($author_id);
    }

    if($exclude === false) {
      $this->set_query_arg('author__in', $author_id);
    } else {
      $this->set_query_arg('author__not_in', $author_id);
    }

  }

  /**
   * @param $status string|array ['publish','pending','draft','private','any',...]
   * */
  public function filter_by_post_status($status){
    if(!is_array($status)) {
      $status = array($status);
    }
    $this->set_query_arg('post_status', $status);
  }

  /**
   * @param $relation string OR|AND
   * */
  public function meta_query_relation($relation){
    if(!isset($this->query_args['meta_query'])) {
      $this->query_args['meta_query'] = array();
    }
    $this->query_args['meta_query']['relation'] = $relation;
  }

  public function set_query_arg($key, $value){
    $this->query_args[$key] = $value;
  }

  public function add_query_arg($key, $value){

    if(!isset($this->query_args[$key])) {
      $this->query_args[$key] = array();
    }

    $this->query_args[$key][] = $value;
  }

  public function get_query_args(){
    return $this->query_args;
  }

  public function order($key, $value){

  }

  public function reset_query(){
    $this->set_default_query_args();
  }

  private function set_default_query_args(){

    $this->query_args = array(
      'posts_per_page' => -1,
      'post_type' => 'ecwd_event',
      'post_status' => array('publish'),
      'meta_key' => 'ecwd_event_date_from',
      'orderby' => 'meta_value',
      'order' => 'ASC',
      'suppress_filters' => false
    );

  }

  private function sanitize_text($str){
    if(!is_array($str)) {
      return sanitize_text_field($str);
    } else {
      foreach($str as $key => $value) {
        $str[$key] = $this->sanitize_text($value);
      }
      return $str;
    }

  }


}