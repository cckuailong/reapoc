<?php
/**
 * Display for Event Custom Post Types
 */
global $post;
$single_event = $this->single_event_for_metas;

$post_id = $post->ID;
$type = ECWD_PLUGIN_PREFIX.'_calendar';
$args = array(
    'post_type' => $type,
    'order' => "ASC",
    'orderby' => 'post_title',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'ignore_sticky_posts' => 1
);
$calendar_posts = get_posts($args);

if(current_user_can('read_private_posts')) {
  $private_args = $args;
  $private_args['post_status'] = array('private');
  $private_calendar_posts = get_posts($private_args);
  if(!empty($private_calendar_posts)) {
    foreach($private_calendar_posts as $private_calendar_post) {
      $calendar_posts[] = $private_calendar_post;
    }
  }
}

$event_calendars = $event_calendars = $single_event->calendars;
if(!$event_calendars){
    $event_calendars = array();
}
if(isset($_GET['cal_id']) && $_GET['cal_id']){
    $event_calendars[] = sanitize_text_field($_GET['cal_id']);
}

global $pagenow;
if ($pagenow == "post-new.php" && empty($event_calendars)) {
    $ecwd_default_calendar = get_option('ecwd_default_calendar');
    if ($ecwd_default_calendar !== false && $ecwd_default_calendar !== null) {
        $event_calendars[] = $ecwd_default_calendar;
    }
}

?>
<div id="ecwd-display-options-wrap">
    <div class="ecwd-meta-control">
        <?php foreach ($calendar_posts as $calendar_post) { ?>
            <p>
                <label for = "ecwd_event_calendar_<?php echo $calendar_post->ID; ?>" id = "ecwd_event_calendar_label_<?php echo $calendar_post->ID ?>">
                    <input type = "checkbox" name = "ecwd_event_calendars[]" id = "ecwd_event_calendar_<?php echo $calendar_post->ID; ?>" value = "<?php echo $calendar_post->ID; ?>" <?php if(in_array($calendar_post->ID, $event_calendars)){echo 'checked="checked"';}
                    ?> />
                    <?php echo esc_html($calendar_post->post_title); ?>
                </label>
            </p>

        <?php }
        ?>

    </div>
</div>

