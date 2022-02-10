<?php

class ecwd_single_event {

  public $id;// null|int
  public $title;
  public $content;
  private $featured_image_url;
  private $permalink;
  private $start_date;
  private $end_date;
  private $all_day = false;//boolean
  public $calendars = array();//array calendar ids
  public $organizers = array();//array organizers list (id, title, description, metas)
  public $venue = null;//null|array('post'=> WP_Post, 'metas' => array)
  public $tags = array();//tags list
  public $categories = array();//categories list
  public $post;//wp_post
  public $repeat = array(
    'ecwd_event_repeat_event' => "no_repeat",//no_repeat,daily,weekly,monthly,yearly
    'ecwd_event_day' => array(),//[monday,tuesday,wednesday,thursday,friday,saturday,sunday]
    'ecwd_event_repeat_how' => "",//number|""
    'ecwd_event_repeat_month_on_days' => "1",//1|2
    'ecwd_event_repeat_year_on_days' => "1",//1|2
    'ecwd_event_repeat_on_the_m' => null,
    'ecwd_event_repeat_on_the_y' => null,
    'ecwd_monthly_list_monthly' => null,
    'ecwd_monthly_week_monthly' => null,
    'ecwd_monthly_list_yearly' => null,
    'ecwd_monthly_week_yearly' => null,
    'ecwd_event_repeat_repeat_until' => "",
    'ecwd_event_year_month' => null,
  ); //array
  public $event_url = "";
  public $video_url = "";
  public $other_metas = array();//array meta_key:meta_value
  private $is_default_dates;

  public function __construct($id = null, $title = "", $content = ""){
    $this->id = $id;
    $this->title = $title;
    $this->content = $content;
    $this->set_default_dates();
  }

  public function set_start_date($start_date){
    $this->start_date = $start_date;
    $this->is_default_dates = false;
  }

  public function set_end_date($end_date){
    $this->end_date = $end_date;
    $this->is_default_dates = false;
  }

  public function set_all_day($all_day){
    if(is_string($all_day)) {
      $this->all_day = ($all_day === '1');
    } else {
      $this->all_day = ($all_day === true);
    }
  }

  public function set_venue($venue_id){

    $venue_id = intval($venue_id);
    if($venue_id === 0) {
      return;
    }

    $venue = get_post($venue_id);
    if($venue === null) {
      return;
    }

    $this->venue = array(
      'post' => $venue,
      'metas' => array(
        'location' => get_post_meta($venue_id, 'ecwd_venue_location', true),
        'show_map' => get_post_meta($venue_id, 'ecwd_venue_show_map', true),
        'lat_long' => get_post_meta($venue_id, 'ecwd_venue_lat_long', true),
        'map_zoom' => get_post_meta($venue_id, 'ecwd_map_zoom', true),
        'phone' => get_post_meta($venue_id, 'ecwd_venue_meta_phone', true),
        'website' => get_post_meta($venue_id, 'ecwd_venue_meta_website', true),
      )
    );

  }

  public function set_repeat(){

    foreach($this->repeat as $meta_key => $default_value) {
      if(isset($_POST[$meta_key])) {
        $this->repeat[$meta_key] = $_POST[$meta_key];
      }
    }

  }

  public function set_permalink(){
    if($this->post !== null) {
      $this->permalink = get_the_permalink($this->post);
    } else {
      $this->permalink = get_the_permalink($this->id);
    }
  }

  public function set_featured_image_url(){
    if($this->post !== null) {
      $this->featured_image_url = get_the_post_thumbnail_url($this->post);
    } else {
      $this->featured_image_url = get_the_post_thumbnail_url($this->id);
    }
  }

  public function set_categories(){
    if($this->post !== null) {
      $this->categories = get_the_terms($this->post, 'ecwd_event_category');
    } else {
      $this->categories = get_the_terms($this->id, 'ecwd_event_category');
    }
  }

  public function set_tags(){
    if($this->post !== null) {
      $this->tags = get_the_terms($this->post, 'ecwd_event_tag');
    } else {
      $this->tags = get_the_terms($this->id, 'ecwd_event_tag');
    }
  }

  public function set_metas(){
    $metas = get_post_meta($this->id);

    foreach($metas as $key => $meta) {

      switch($key) {
        case "ecwd_event_date_from":
          $this->set_start_date($meta[0]);
          break;
        case "ecwd_event_date_to":
          $this->set_end_date($meta[0]);
          break;
        case "ecwd_all_day_event":
          $this->all_day = ($meta[0] == '1');
          break;
        case "ecwd_event_calendars":
          $this->calendars = maybe_unserialize($meta[0]);
          break;
        case "ecwd_event_organizers":
          $this->organizers = maybe_unserialize($meta[0]);
          break;
        case "ecwd_event_venue":
          if(!empty($meta[0])) {
            $this->set_venue($meta[0]);
          }
          break;
        case "ecwd_event_url":
          $this->event_url = $meta[0];
          break;
        case "ecwd_event_video":
          $this->video_url = $meta[0];
          break;
        case 'ecwd_event_repeat_event':
        case 'ecwd_event_repeat_how':
        case 'ecwd_event_repeat_on_the_m':
        case 'ecwd_event_repeat_on_the_y':
        case 'ecwd_monthly_list_monthly':
        case 'ecwd_monthly_week_monthly':
        case 'ecwd_monthly_list_yearly':
        case 'ecwd_monthly_week_yearly':
        case 'ecwd_event_repeat_repeat_until':
        case 'ecwd_event_year_month':
          $this->repeat[$key] = maybe_unserialize($meta[0]);
          break;
        case 'ecwd_event_day':
          $val = maybe_unserialize($meta[0]);
          if(!is_array($val)) {
            $val = array();
          }
          $this->repeat[$key] = $val;
          break;
        case 'ecwd_event_repeat_month_on_days':
        case 'ecwd_event_repeat_year_on_days':
          $val = maybe_unserialize($meta[0]);

          if(!($val === '1' || $val === '2')) {
            $val = '1';
          }
          $this->repeat[$key] = $val;
          break;
        default:
          $this->other_metas[$key] = maybe_unserialize($meta[0]);
      }

    }

  }

  public function get_location_info(){

    $location_info = array(
      'location' => '',
      'show_map' => '',
      'lat_long' => '',
      'zoom' => '',
      'complete_data' => false
    );

    if($this->venue === null || $this->venue['post'] === null) {
      return $location_info;
    }

    $location_info = array();
    $complete_data = true;

    if(isset($this->venue['metas']['location'])) {
      $location_info['location'] = $this->venue['metas']['location'];
    } else {
      $complete_data = false;
    }

    if(isset($this->venue['metas']['show_map'])) {
      $location_info['show_map'] = $this->venue['metas']['show_map'];
    } else {
      $complete_data = false;
    }

    if(isset($this->venue['metas']['lat_long'])) {
      $location_info['lat_long'] = $this->venue['metas']['lat_long'];
    } else {
      $complete_data = false;
    }

    if(isset($this->venue['metas']['map_zoom'])) {
      $location_info['zoom'] = $this->venue['metas']['map_zoom'];
    } else {
      $complete_data = false;
    }

    $location_info['complete_data'] = $complete_data;
    return $location_info;
  }

  public function get_start_date(){
    if($this->start_date === null) {
      $this->set_default_dates();
    }
    return $this->start_date;
  }

  public function get_end_date(){
    if($this->end_date === null) {
      $this->set_default_dates();
    }
    return $this->end_date;
  }

  public function set_default_dates(){
    $today = ECWD::ecwd_date('Y-m-d H:i', time());

    $this->start_date = ECWD::ecwd_date('Y/m/d H:i', strtotime($today . "+1 days"));
    $this->end_date = ECWD::ecwd_date('Y/m/d H:i', strtotime($this->start_date . "+1 hour"));
    $this->is_default_dates = true;
  }

  public function get_all_day(){
    return $this->all_day;
  }

  public function get_permalink(){
    if($this->permalink === null) {
      $this->set_permalink();
    }
    return $this->permalink;
  }

  public function get_featured_image_url(){
    if($this->featured_image_url === null) {
      $this->set_featured_image_url();
    }
    return $this->featured_image_url;
  }

  public function get_is_default_dates(){
    return $this->is_default_dates;
  }

  public function __clone(){

  }

}
