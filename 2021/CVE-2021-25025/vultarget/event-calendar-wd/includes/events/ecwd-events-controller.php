<?php
include_once 'ecwd-single-event.php';

class ecwd_events_controller {

  private $week_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');

  public function __construct(){
  }


  public function create_event(){
    //insert post
  }

  public function update_post(){
    //title, contet, etc
  }

  public function get_past_events(){
    include_once 'ecwd-events-query.php';
    $ecwd_query = new ecwd_events_query();
    $ecwd_query->filter_past_events();
    $ecwd_query->filter_by_post_status(array('publish','private'));
    $ecwd_query->meta_query_relation('AND');
    $events = $ecwd_query->get_posts();
    $single_events_list = $this->convert_wp_post_to_ecwd_event($events);
    return $single_events_list;
  }

  public function get_excluded_events($calendar_id){
    include_once 'ecwd-events-query.php';
    $ecwd_query = new ecwd_events_query();
    $ecwd_query->filter_excluded_events($calendar_id);
    $ecwd_query->filter_by_post_status(array('publish','private'));
    $ecwd_query->meta_query_relation('AND');
    $events = $ecwd_query->get_posts();
    $single_events_list = $this->convert_wp_post_to_ecwd_event($events);
    return $single_events_list;

  }

  public function get_events(){
    include_once 'ecwd-events-query.php';
    $ecwd_query = new ecwd_events_query();


    //$ecwd_query->filter_by_taxonomies(array(25, 26), array(23));
    //    $ecwd_query->filter_by_calendars(['143', '166']);
    //    $ecwd_query->filter_by_venues(['51','145']);
    //    $ecwd_query->filter_by_organizers(['153','142']);
    //    $ecwd_query->filter_by_post_status(['publish','private']);
    //    $ecwd_query->search('');
    $ecwd_query->filter_by_date('2018-07-01', '2018-07-31');
    //$ecwd_query->meta_query_relation('AND');


    $events = $ecwd_query->get_posts();
    $single_events_list = $this->convert_wp_post_to_ecwd_event($events);


    echo '<pre>';
    var_dump($single_events_list);
    die;

  }


  public function get_recurring_events($calendar_id, $start_date, $end_date){

    $recurring_events = $this->get_recurring_events_from_cache($calendar_id, $start_date, $end_date);
    if($recurring_events !== null) {
      echo "from cache-" . $calendar_id;
      echo "<br/>";

      if(!empty($recurring_events)){
        foreach($recurring_events as $e){
          foreach($e as $t){

            echo $t->title.' - '.
              $t->get_start_date() .' - '.
              $t->get_end_date();
            echo "<br/>";
          }
        }
      }

      return $recurring_events;
    }

    include_once 'ecwd-events-query.php';
    $ecwd_query = new ecwd_events_query();
    $ecwd_query->filter_recurring($start_date, $end_date);
    $ecwd_query->filter_by_calendars($calendar_id);
    $ecwd_query->meta_query_relation('AND');

    $events = $ecwd_query->get_posts();
    $ecwd_events_list = array();
    if(!empty($events)) {
      $ecwd_events_list = $this->convert_wp_post_to_ecwd_event($events);
      $ecwd_events_list = $this->calculate_recurring_events($ecwd_events_list, $start_date, $end_date);
    }

    $this->cache_recurring_events($calendar_id, $ecwd_events_list, $start_date, $end_date);
    echo "no cache-" . $calendar_id;
    echo "<br/>";


    if(!empty($ecwd_events_list)){
      foreach($ecwd_events_list as $e){
        foreach($e as $t){

          echo $t->title.' - '.
            $t->get_start_date() .' - '.
            $t->get_end_date();
          echo "<br/>";
        }
      }
    }

    return $ecwd_events_list;
  }


  public function delete_events($events_id){
    foreach($events_id as $id) {
      wp_delete_post($id, true);
    }
  }

  /**
   * @param $event array of ecwd_single_event
   * @return array of ecwd_single_event
   * */
  private function calculate_recurring_events($events, $start_date, $end_date){

    $events_list = array();
    foreach($events as $event) {

      switch($event->repeat['ecwd_event_repeat_event']) {

        case "daily":
          $events_list[$event->id] = $this->daily_recurring($event, $start_date, $end_date);
          break;
        case "weekly":
          $events_list[$event->id] = $this->weekly_recurring($event, $start_date, $end_date);
          break;
        case "monthly":
          $events_list[$event->id] = $this->monthly_recurring($event, $start_date, $end_date);
          break;
        case "yearly":
          $events_list[$event->id] = $this->yearly_recurring($event, $start_date, $end_date);
          break;
        default:
          $events_list[$event->id] = $this->no_repeat($event, $start_date, $end_date);
      }

    }

    return $events_list;
  }

  /**
   * @param $event ecwd_single_event
   * @return array of ecwd_single_event
   * */
  private function daily_recurring($event, $start_date, $end_date){
    global $ecwd_options;

    $until = $event->repeat['ecwd_event_repeat_repeat_until'];
    $how = intval($event->repeat['ecwd_event_repeat_how']);
    $from = $event->get_start_date();
    $to = $event->get_end_date();


    if(strtotime($until) > strtotime($end_date)) {
      $until = $end_date;
    }

    $event_days = $this->get_date_diff($from, $until);
    $event_days_long = $this->get_date_diff($from, $to);
    if($event_days < 0) {
      return array();
    }

    $from_date = $from;
    $events_date = array();
    for($i = 0; $i <= $event_days; $i++) {
      $date = strtotime(ECWD::ecwd_date("Y-m-d", strtotime($from_date)) . " +" . $i . " day");

      $date = ECWD::ecwd_date("Y-n-j", $date);

      if(strtotime($until) >= strtotime(ECWD::ecwd_date('Y-m-d', strtotime($date)))) {

        $from_date = strtotime((ECWD::ecwd_date("Y-m-d", (strtotime($from_date))) . " +" . (($how - 1)) . " days"));
        $from_date = ECWD::ecwd_date('Y-m-d', $from_date);
        $from = $date;
        $to = ECWD::ecwd_date('Y-m-d', strtotime($from . ' + ' . $event_days_long . ' days'));
        if(strtotime($date) <= strtotime($end_date) && strtotime($date) >= strtotime($start_date) && in_array(strtolower(ECWD::ecwd_date('l', strtotime($date))), $this->week_days)) {
          $events_date[] = array('start' => $from, 'end' => $to);
        }
      }
    }

    return $this->clone_event($event, $events_date);
  }

  /**
   * @param $event ecwd_single_event
   * @return array of ecwd_single_event
   * */
  private function weekly_recurring($event, $start_date, $end_date){
    global $ecwd_options;

    $until = $event->repeat['ecwd_event_repeat_repeat_until'];
    $how = intval($event->repeat['ecwd_event_repeat_how']);
    $from = $event->get_start_date();
    $to = $event->get_end_date();

    if(!empty($event->repeat['ecwd_event_day'])) {
      $days = $event->repeat['ecwd_event_day'];
    } else {
      $days = array(strtolower(ECWD::ecwd_date('l', strtotime($from))));
    }

    if(strtotime($until) > strtotime($end_date)) {
      $until = $end_date;
    }

    $week_start = (isset($ecwd_options['week_starts'])) ? $ecwd_options['week_starts'] : "0";

    if(count($days)) {
      if($week_start == '0') {

        if(in_array('saturday', $days)) {
          $event_week_last_day = 'saturday';
        } else {
          $event_week_last_day = $days[count($days) - 1];
        }
      } else {
        $event_week_last_day = $days[count($days) - 1];
      }

    }

    $event_days = $this->get_date_diff($from, $until);
    $event_days_long_def = $this->get_date_diff($from, $to);

    if($event_days < 0) {
      return array();
    }

    $from_date = $from;

    $events_date = array();
    for($i = 0; $i <= $event_days; $i++) {
      $event_date = strtotime(ECWD::ecwd_date("Y-m-d", strtotime($from_date)) . " +" . $i . " day");
      $week_day = strtolower(ECWD::ecwd_date('l', $event_date));
      $event_date = ECWD::ecwd_date("Y-n-j", $event_date);

      if(isset($event_week_last_day) && is_array($days) && in_array($week_day, $days)) {

        if($how > 1 && $week_day == $event_week_last_day) {
          $from_date = strtotime((ECWD::ecwd_date("Y-m-d", (strtotime($from_date))) . " +" . (($how * 7) - 7) . " days"));
          $from_date = ECWD::ecwd_date('Y-m-d', $from_date);
        }

        $from = $event_date;
        $event_days_long = $event_days_long_def;
        if(strtotime($until) >= strtotime(ECWD::ecwd_date('Y-m-d', strtotime($event_date)))) {
          $to = ECWD::ecwd_date('Y-m-d', strtotime($event_date . ' + ' . $event_days_long . ' days'));
          if((strtotime($event_date) <= strtotime($end_date) && strtotime($event_date) >= strtotime($start_date) && in_array(strtolower(ECWD::ecwd_date('l', strtotime($event_date))), $this->week_days))) {
            $events_date[] = array('start' => $from, 'end' => $to);
          }
        }
      }
    }

    return $this->clone_event($event, $events_date);
  }

  /**
   * @param $event ecwd_single_event
   * @return array of ecwd_single_event
   * */
  private function monthly_recurring($event, $start_date, $end_date){
    global $ecwd_options;

    $until = $event->repeat['ecwd_event_repeat_repeat_until'];
    $how = intval($event->repeat['ecwd_event_repeat_how']);
    $from = $event->get_start_date();
    $to = $event->get_end_date();

    if(strtotime($until) > strtotime($end_date)) {
      $until = $end_date;
    }

    $event_days = $this->get_date_diff($from, $until);
    $event_days_long = $this->get_date_diff($from, $to);
    $event_from = $from;
    $from_date = $from;
    $repeat_days = $event->repeat['ecwd_event_repeat_month_on_days'];

    $repeat_when = ($event->repeat['ecwd_monthly_list_monthly'] !== null) ? $event->repeat['ecwd_monthly_list_monthly'] : false;
    $repeat_day = ($event->repeat['ecwd_monthly_week_monthly'] !== null) ? $event->repeat['ecwd_monthly_week_monthly'] : false;

    $min_date = strtotime($event_from);
    $max_date = strtotime("+1 MONTH", strtotime($until));

    $events_date = array();
    if($max_date >= $min_date) {
      $i = 0;
      $min_date = strtotime(ECWD::ecwd_date("Y-m-1", strtotime($event_from)));
      while(($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
        $date = strtotime(ECWD::ecwd_date("Y-m-d", strtotime($event_from)) . " +" . $i * $how . " MONTH");
        if($i > 0) {
          if($repeat_days == 2 && $repeat_day && $repeat_when && ECWD::ecwd_date('Y-m', strtotime($event_from)) !== ECWD::ecwd_date('Y-m', $date)) {
            $date = strtotime(ECWD::ecwd_date("Y-m-1", strtotime($event_from)) . " +" . $i * $how . " MONTH");
            $month_year = ECWD::ecwd_date("F Y", $date);

            $repeat_date = ECWD::ecwd_date('Y-m-d', strtotime($repeat_when . ' ' . ucfirst($repeat_day) . ' of ' . $month_year));
            if($repeat_date == '1970-01-01' || $repeat_date == '1969-12-31') {
              $repeat_date = ECWD::ecwd_date('Y-m-d', strtotime($month_year . ' ' . $repeat_when . ' ' . $repeat_day));
            }

            //echo $repeat_date1.'-------'.$repeat_date.'<br />';
            $date = strtotime($repeat_date);
          }
        }
        $date = ECWD::ecwd_date("Y-n-j", $date);
        $i++;
        if(strtotime($until) >= strtotime(ECWD::ecwd_date('Y-m-d', strtotime($date)))) {
          $min_date = $from_date = strtotime((ECWD::ecwd_date("Y-m-1", (strtotime($date))) . " +" . (($how)) . " month"));
          $event_days -= 30;
          $from_date = strtotime((ECWD::ecwd_date("Y-m-d", $from_date) . " - 1  day"));

          $from_date = ECWD::ecwd_date('Y-m-d', $from_date);
          $from = $date;
          $to = strtotime((ECWD::ecwd_date("Y-m-d", (strtotime($from))) . " +" . ($event_days_long) . " days"));
          $to = ECWD::ecwd_date('Y-m-d', $to);

          if(strtotime($date) <= strtotime($end_date) && strtotime($date) >= strtotime($start_date) && in_array(strtolower(ECWD::ecwd_date('l', strtotime($date))), $this->week_days)) {
            $events_date[] = array('start' => $from, 'end' => $to);
          }
        }
      }
    }

    return $this->clone_event($event, $events_date);
  }

  /**
   * @param $event ecwd_single_event
   * @return array of ecwd_single_event
   * */
  private function yearly_recurring($event, $start_date, $end_date){
    global $ecwd_options;

    $until = $event->repeat['ecwd_event_repeat_repeat_until'];
    $how = intval($event->repeat['ecwd_event_repeat_how']);
    $from = $event->get_start_date();
    $to = $event->get_end_date();

    if(strtotime($until) > strtotime($end_date)) {
      $until = $end_date;
    }

    $event_days_long = $this->get_date_diff($from, $to);
    $event_from = $from;
    $from_date = $from;
    $repeat_days = $event->repeat['ecwd_event_repeat_year_on_days'];
    $repeat_when = ($event->repeat['ecwd_monthly_list_yearly'] !== null) ? $event->repeat['ecwd_monthly_list_yearly'] : false;
    $repeat_day = ($event->repeat['ecwd_monthly_week_yearly'] !== null) ? $event->repeat['ecwd_monthly_week_yearly'] : false;

    if($event->repeat['ecwd_event_year_month'] !== null && $repeat_days == 2) {
      $month = $event->repeat['ecwd_event_year_month'];
      $month_name = ECWD::ecwd_date('F', strtotime('2015-' . $month . '-1'));
    } else {
      $month_name = ECWD::ecwd_date('F', strtotime($from_date));
    }

    $min_date = strtotime($event_from);
    $max_date = strtotime($until);
    $i = 0;
    $events_date = array();
    while($min_date <= $max_date) {
      $min_date = strtotime("+1 YEAR", $min_date);
      $date = strtotime(ECWD::ecwd_date("Y-m-d", strtotime($event_from)) . " +" . $i * $how . " YEAR");
      if($i > 0) {
        if($repeat_days == 1) {
          $month_year = $month_name . ' ' . ECWD::ecwd_date("d Y", $date);
          $repeat_date = strtotime(ECWD::ecwd_date('Y-m-d', strtotime($month_year)));
          $date = $repeat_date;
        }
        if($repeat_days == 2 && $repeat_day && $repeat_when) {
          $month_year = $month_name . ' ' . ECWD::ecwd_date("Y", $date);
          //echo $repeat_when.' '.ucfirst( $repeat_day ).' of '.$month_year.'<br />';
          $repeat_date = ECWD::ecwd_date('Y-m-d', strtotime($repeat_when . ' ' . ucfirst($repeat_day) . ' of ' . $month_year));
          if($repeat_date == '1970-01-01' || $repeat_date == '1969-12-31') {
            $repeat_date = ECWD::ecwd_date('Y-m-d', strtotime($month_year . ' ' . $repeat_when . ' ' . $repeat_day));
          }
          //don't know why, but "last sunday,last monday... returns last s,m of previous month"
          if($repeat_when == 'last') {
            $repeat_date = ECWD::ecwd_date('Y-m-d', strtotime($repeat_when . ' ' . ucfirst($repeat_day) . ' of ' . $month_year, strtotime("+1 MONTH", $repeat_date)));
          }
          $date = strtotime($repeat_date);
        }
      }

      $date = ECWD::ecwd_date("Y-n-j", $date);
      $i++;

      if(strtotime($until) >= strtotime(ECWD::ecwd_date('Y-m-d', strtotime($end_date))) && strtotime($date) <= strtotime($until)) {
        $from_date = strtotime((ECWD::ecwd_date("Y-m-d", (strtotime($from_date))) . " +" . (($how)) . " year"));
        $from_date = strtotime((ECWD::ecwd_date("Y-m-d", $from_date) . " - 1  day"));
        $from_date = ECWD::ecwd_date('Y-m-d', $from_date);
        $from = $date;
        $to = strtotime((ECWD::ecwd_date("Y-m-d", (strtotime($from))) . " +" . ($event_days_long) . " days"));
        $to = ECWD::ecwd_date('Y-m-d', $to);
        if((strtotime($date) <= strtotime($end_date) && strtotime($date) >= strtotime($start_date) && in_array(strtolower(ECWD::ecwd_date('l', strtotime($date))), $this->week_days))) {
          $events_date[] = array('start' => $from, 'end' => $to);
        }
      }
    }

    return $this->clone_event($event, $events_date);
  }

  /**
   * @param $event ecwd_single_event
   * @return array of ecwd_single_event
   * */
  private function no_repeat($event, $start_date, $end_date){
    global $ecwd_options;

    $from = $event->get_start_date();
    $to = $event->get_end_date();

    $date = strtotime(ECWD::ecwd_date("Y-m-d", strtotime($from)));
    $date = ECWD::ecwd_date("Y-n-j", $date);

    if(isset($ecwd_options["long_events"]) && $ecwd_options["long_events"] == '1') {
      $m1 = ECWD::ecwd_date('m', strtotime($to));
      $m2 = ECWD::ecwd_date('m', strtotime($end_date));
      if(strtotime($to) <= strtotime($end_date) || (strtotime($to) >= strtotime($end_date) && $m1 == $m2)) {
        if(strtotime($from) < strtotime($start_date)) {
          $date = $start_date;
        }
      }
    }

    $events_date = array();
    if(strtotime($date) <= strtotime($end_date) && strtotime($date) >= strtotime($start_date) && in_array(strtolower(ECWD::ecwd_date('l', strtotime($date))), $this->week_days)) {
      $events_date[] = array('start' => $from, 'end' => $to);
    }

    return $this->clone_event($event, $events_date);
  }

  /**
   * @param $calendar_id string
   * @param $events array of ecwd_single_event
   * */
  private function cache_recurring_events($calendar_id, $ecwd_events, $start_date, $end_date){

    if(empty($ecwd_events)) {
      return;
    }

    $events_id = array();
    foreach($ecwd_events as $event_id => $events) {

      foreach($events as $event) {
        $events_id[$event_id][] = array(
          'start' => $event->get_start_date(),
          'end' => $event->get_end_date(),
        );
      }

    }

    $transient_name = 'ecwd_recurring_events_' . $calendar_id . '-' . $start_date . '-' . $end_date;
    set_site_transient($transient_name, $events_id, 2 * 24 * 60 * 60);//2 day

    $transients = get_post_meta($calendar_id, 'ecwd_recurring_events_transient', true);
    if(!is_array($transients)) {
      $transients = array();
    }
    $transients[] = $transient_name;
    update_post_meta($calendar_id, 'ecwd_recurring_events_transient', $transients);
  }


  private function get_recurring_events_from_cache($calendar_id, $start_date, $end_date){
    $transient_name = 'ecwd_recurring_events_' . $calendar_id . '-' . $start_date . '-' . $end_date;
    $recurring_events = get_site_transient($transient_name);

    if($recurring_events === false) {
      return null;
    }

    $ids = array();
    foreach($recurring_events as $event_id => $events_date) {
      $ids[] = $event_id;
    }

    if(empty($ids)) {
      return array();
    }

    include_once 'ecwd-events-query.php';
    $ecwd_query = new ecwd_events_query();
    $ecwd_query->filter_by_events_id($ids);

    $events = $ecwd_query->get_posts();
    if(empty($events) || count($events) !== count($ids)) {
      return null;//clear cache
    }

    $events = $this->convert_wp_post_to_ecwd_event($events);

    $events_list = array();
    foreach($events as $event) {
      $events_list[$event->id] = $this->clone_event($event, $recurring_events[$event->id], false);
    }

    return $events_list;
  }

  /**
   * @param $event ecwd_single_event
   * @param $events_date array
   * @return array of ecwd_single_event
   **/
  private function clone_event($event, $events_date, $add_time = true){

    $events_list = array();
    if(empty($events_date)) {
      return $events_list;
    }

    $start_time = ECWD::ecwd_date('H:i', strtotime($event->get_start_date()));
    $end_time = ECWD::ecwd_date('H:i', strtotime($event->get_end_date()));

    foreach($events_date as $i=>$ev_dates) {
      $new_event = clone $event;

      if($add_time === true) {

        $ev_dates['start'] = ECWD::ecwd_date('Y-m-d', strtotime($ev_dates['start']));
        $ev_dates['end'] = ECWD::ecwd_date('Y-m-d', strtotime($ev_dates['end']));

        $new_event->set_start_date($ev_dates['start'] . ' ' . $start_time);
        $new_event->set_end_date($ev_dates['end'] . ' ' . $end_time);
      } else {
        $new_event->set_start_date($ev_dates['start']);
        $new_event->set_end_date($ev_dates['end']);
      }
      $events_list[] = $new_event;
    }

    return $events_list;
  }

  public function get_date_diff($begin_date, $end_date){
    if($end_date == '') {
      return 0;
    }

    $from_date = ECWD::ecwd_date('Y-n-j', strtotime($begin_date));
    $to_date = ECWD::ecwd_date('Y-n-j', strtotime($end_date));
    $date_parts1 = explode('-', $from_date);
    $date_parts2 = explode('-', $to_date);

    $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
    $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);

    return $end_date - $start_date;
  }

  /**
   * @param $events array of WP_Post
   * @return array of ecwd_single_event
   * */
  private function convert_wp_post_to_ecwd_event($events){
    $single_events_list = array();
    foreach($events as $event) {

      $single_event = new ecwd_single_event($event->ID, $event->post_title, $event->post_content);
      $single_event->post = $event;
      $single_event->set_permalink();
      $single_event->set_featured_image_url();
      $single_event->set_categories();
      $single_event->set_tags();
      $single_event->set_metas();

      $single_events_list[$event->ID] = $single_event;
    }
    return $single_events_list;
  }

  /**
   * @param $ev ecwd_single_event
   * */
  public function update_meta_values($ev){

    $old_ev = $this->convert_wp_post_to_ecwd_event(array(get_post($ev->id)));
    $old_ev = $old_ev[$ev->id];

    update_post_meta($ev->id, 'ecwd_event_date_from', $this->sanitize_text($ev->get_start_date()));
    update_post_meta($ev->id, 'ecwd_event_date_to', $this->sanitize_text($ev->get_end_date()));
    if($ev->get_all_day() === true) {
      update_post_meta($ev->id, 'ecwd_all_day_event', '1');
    } else {
      delete_post_meta($ev->id, 'ecwd_all_day_event');
    }
    update_post_meta($ev->id, 'ecwd_event_calendars', $this->sanitize_text($ev->calendars));
    //venue and venue data
    $location_info = $ev->get_location_info();
    if($location_info['complete_data'] === false) {

      delete_post_meta($ev->id, 'ecwd_event_venue');
      delete_post_meta($ev->id, 'ecwd_event_location');
      delete_post_meta($ev->id, 'ecwd_lat_long');
      delete_post_meta($ev->id, 'ecwd_event_show_map');
      delete_post_meta($ev->id, 'ecwd_map_zoom');

    } else {

      update_post_meta($ev->id, 'ecwd_event_venue', $this->sanitize_text($ev->venue['post']->ID));
      update_post_meta($ev->id, 'ecwd_event_location', $this->sanitize_text($location_info['location']));
      update_post_meta($ev->id, 'ecwd_lat_long', $this->sanitize_text($location_info['lat_long']));
      update_post_meta($ev->id, 'ecwd_event_show_map', $this->sanitize_text($location_info['show_map']));
      update_post_meta($ev->id, 'ecwd_map_zoom', $this->sanitize_text($location_info['zoom']));

    }

    update_post_meta($ev->id, 'ecwd_event_organizers', $this->sanitize_text($ev->organizers));
    update_post_meta($ev->id, 'ecwd_event_url', $this->sanitize_text($ev->event_url));
    update_post_meta($ev->id, 'ecwd_event_video', $this->sanitize_text($ev->video_url));
    foreach($ev->repeat as $meta_key => $value) {

      if($value !== null) {
        update_post_meta($ev->id, $meta_key, $this->sanitize_text($value));
      } else {
        delete_post_meta($ev->id, $meta_key);
      }

    }

    $events_metas_data = array(
      $ev->get_start_date(),
      $ev->get_end_date(),
      json_encode($ev->calendars),
      json_encode($ev->repeat)
    );

    $old_events_metas_data = array(
      $old_ev->get_start_date(),
      $old_ev->get_end_date(),
      json_encode($old_ev->calendars),
      json_encode($old_ev->repeat)
    );

    if(md5(implode(":" , $events_metas_data)) !== md5(implode(":", $old_events_metas_data))) {
      $calendars_id = array_unique(array_merge($ev->calendars, $old_ev->calendars));
      self::clear_recurring_events_cache($calendars_id);
    }
  }

  public function sanitize_text($str){
    if(!is_array($str)) {
      return sanitize_text_field($str);
    } else {
      foreach($str as $key => $value) {
        $str[$key] = $this->sanitize_text($value);
      }
      return $str;
    }

  }

  /**
   * @param $calendars_id array of calendars id
   * */
  public static function clear_recurring_events_cache($calendars_id){
    foreach($calendars_id as $cal_id) {
      $transients = get_post_meta($cal_id, 'ecwd_recurring_events_transient', true);
      if(is_array($transients) && !empty($transients)) {
        foreach($transients as $transient) {
          delete_site_transient($transient);
        }
      }
      delete_post_meta($cal_id, 'ecwd_recurring_events_transient');
    }
  }

}