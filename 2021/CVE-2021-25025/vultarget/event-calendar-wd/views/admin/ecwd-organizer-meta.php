<?php
/**
 * Display for Event Custom Post Types
 */

global $post, $ecwd_options;
$post_id = $post->ID;

$ecwd_organizer_meta_phone = get_post_meta($post->ID, 'ecwd_organizer_meta_phone', true);
$ecwd_organizer_meta_website = get_post_meta($post->ID, 'ecwd_organizer_meta_website', true);


?>


<table class="form-table ecwd-admin-fields">
  <tr>
    <td>
      <div class="ecwd-organizer-meta-fields">
        <!-- start ecwd_organizer_meta_phone -->
        <div class="ecwd-meta-field">
          <label for="ecwd_organizer_meta_phone"><?php _e('Phone','event-calendar-wd')?>:</label>
          <input type="text" name="ecwd_organizer_meta_phone" id="ecwd_organizer_meta_phone"
                 value="<?php echo esc_attr($ecwd_organizer_meta_phone); ?>"/>
        </div>
        <!-- end ecwd_event_location -->
        <!-- start ecwd_venue_meta_phone -->
        <div class="ecwd-meta-field">
          <label for="ecwd_organizer_meta_website"><?php _e('Website','event-calendar-wd')?>:</label>
          <input type="text" name="ecwd_organizer_meta_website"
                 id="ecwd_organizer_meta_website" value="<?php echo esc_attr($ecwd_organizer_meta_website); ?>"/>
        </div>
      </div>
    </td>
  </tr>
</table>
