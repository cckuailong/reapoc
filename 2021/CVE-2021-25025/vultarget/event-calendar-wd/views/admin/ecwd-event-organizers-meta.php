<?php
/**
 * Display for Event Custom Post Types
 */
global $post;

$single_event = $this->single_event_for_metas;

$post_id = $post->ID;
$type = ECWD_PLUGIN_PREFIX . '_organizer';
$args = array(
  'post_type' => $type,
  'order' => "ASC",
  'orderby' => 'post_title',
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'ignore_sticky_posts' => 1
);
$organizer_posts = get_posts($args);
$event_organizers = $single_event->organizers;
if (!$event_organizers || $event_organizers == '' || !is_array($event_organizers)) {
  $event_organizers = array();
}

?>
<div id="<?php echo ECWD_PLUGIN_PREFIX; ?>-display-options-wrap" class="ecwd-admin-fields">
  <div class="ecwd-add_organizer-container">
    <a class="ecwd-add-organizer" href="#"><?php _e('Add organizer', 'event-calendar-wd'); ?></a>
    <div class="ecwd-add-organizer-form" style="display: none">
      <div class="ecwd-organizer-meta-fields">

      <!-- start ecwd-add-organizer-title -->
      <div class="ecwd-meta-field">
        <label for="ecwd-add-organizer-title"><?php _e('Title','event-calendar-wd')?>:<span style="color:#c60d0d"> *</span></label>
        <input type="text" name="ecwd-add-organizer-title" id="ecwd-add-organizer-title"/>
      </div>
      <!-- end ecwd-add-organizer-title -->

      <!-- start ecwd-add-organizer-content -->
      <div class="ecwd-meta-field">
        <label for="ecwd-add-organizer-content" style="vertical-align: top;"><?php _e('Description','event-calendar-wd')?>:</label>
        <textarea name="ecwd-add-organizer-content" id="ecwd-add-organizer-content"></textarea>
      </div>
      <!-- end ecwd-add-organizer-content -->

      <!-- start ecwd_organizer_meta_phone -->
      <div class="ecwd-meta-field">
        <label for="ecwd_organizer_meta_phone"><?php _e('Phone','event-calendar-wd')?>:</label>
        <input type="text" name="ecwd_organizer_meta_phone" id="ecwd_organizer_meta_phone"/>
      </div>
      <!-- end ecwd_event_location -->
      <!-- start ecwd_venue_meta_phone -->
      <div class="ecwd-meta-field">
        <label for="ecwd_organizer_meta_website"><?php _e('Website','event-calendar-wd')?>:</label>
        <input type="text" name="ecwd_organizer_meta_website" id="ecwd_organizer_meta_website"/>
      </div>
        <div style="display: inline-block;">
        <button class="button button-primary button-large ecwd-add-organizer-save">
          <?php _e('Save Organizer', 'event-calendar-wd'); ?>
        </button>
        <span class="spinner"></span>
      </div>
      <div class="ecwd-organizer-template" style="display: none">
        <p>
          <label for="ecwd_event_organizers_{organizer_id}" id="ecwd_event_calendar_label_{organizer_id}">
            <input type="checkbox" name="ecwd_event_organizers[]" id="ecwd_event_organizers_{organizer_id}"
                   value="{organizer_id}" checked="checked"/>{organizer_title}</label>
        </p>
      </div>
    </div>
  </div>

  <div class="ecwd-meta-control ecwd-organizers-list">
    <?php
    if (count($organizer_posts) > 0) {
      foreach ($organizer_posts as $organizer_post) { ?>
        <p>
          <label for="<?php echo ECWD_PLUGIN_PREFIX; ?>_event_organizers_<?php echo $organizer_post->ID; ?>"
                 id="<?php echo ECWD_PLUGIN_PREFIX; ?>_event_calendar_label_<?php echo $organizer_post->ID ?>">
            <input type="checkbox" name="<?php echo ECWD_PLUGIN_PREFIX; ?>_event_organizers[]"
                   id="<?php echo ECWD_PLUGIN_PREFIX; ?>_event_organizers_<?php echo $organizer_post->ID; ?>"
                   value="<?php echo $organizer_post->ID; ?>" <?php if (in_array($organizer_post->ID, $event_organizers)) {
              echo 'checked="checked"';
            }
            ?> />
            <?php echo esc_html($organizer_post->post_title); ?>
          </label>
        </p>

      <?php }

    } else {

    }
    ?>

  </div>
</div>

