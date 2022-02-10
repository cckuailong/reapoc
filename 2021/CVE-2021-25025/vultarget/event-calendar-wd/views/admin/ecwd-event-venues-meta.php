<?php

global $post, $ecwd_options;
$post_id = $post->ID;
$single_event = $this->single_event_for_metas;

$ecwd_event_venue = ($single_event->venue !== null) ? $single_event->venue['post']->ID : "";
$ecwd_event_location = ($single_event->venue !== null) ? $single_event->venue['metas']['location'] : "";
$ecwd_event_show_map = ($single_event->venue !== null) ? $single_event->venue['metas']['show_map'] : "";
$ecwd_lat_long = ($single_event->venue !== null) ? $single_event->venue['metas']['lat_long'] : "";

$long = '';
$lat = '';
$ecwd_venue_meta_phone = "";
$ecwd_venue_meta_website = "";
$venue_meta_show_map = "";
$venue_title = "";
$venue_content = "";

$venue_meta_keys = array(
  'ecwd_venue_meta_phone',
  'ecwd_venue_meta_website',
  'ecwd_venue_show_map',
  'ecwd_venue_location',
  'ecwd_venue_lat_long',
  'ecwd_map_zoom'
);
$has_selected_venue = false;

$args = array(
  'post_type' => ECWD_PLUGIN_PREFIX . '_venue',
  'order' => "ASC",
  'orderby' => 'post_title',
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'ignore_sticky_posts' => 1
);

$venues_meta_data = array();
$venues = get_posts($args);
$selected_venue_metadata = null;

$ecwd_map_zoom = "";
if (!empty($venues)) {

  foreach ($venues as $venue) {
    $venues_meta_data[$venue->ID] = array();

    $venues_meta_data[$venue->ID]['post_title'] = esc_html($venue->post_title);

    foreach ($venue_meta_keys as $venue_meta_key) {
      $venues_meta_data[$venue->ID][$venue_meta_key] = get_post_meta($venue->ID, $venue_meta_key, true);
    }

    $venues_meta_data[$venue->ID]['edit_link'] = 'post.php?post=' . $venue->ID . '&action=edit';

    if ($venue->ID == $ecwd_event_venue) {
      $selected_venue_metadata = $venues_meta_data[$ecwd_event_venue];
      $has_selected_venue = true;
      $venue_meta_show_map = $venues_meta_data[$ecwd_event_venue]['ecwd_venue_show_map'];
      $ecwd_map_zoom = get_post_meta($venue->ID, 'ecwd_map_zoom', true);
    }

  }
}


wp_localize_script('ecwd-admin-scripts', "ecwd_venues", $venues_meta_data);


$info_table_class = "ecwd_event_venue_info_content" . (($selected_venue_metadata == null) ? " ecwd_hidden" : "");
$form_table_class = "ecwd_event_venue_form_content" . (($selected_venue_metadata == null) ? "" : " ecwd_hidden");
$map_table_class = "ecwd_event_venue_map_content";
?>
<table class="form-table">
  <tr>
    <th scope="row"><?php _e('Event Venue', 'event-calendar-wd'); ?>:</th>
    <td>
      <select name="ecwd_event_venue" id="ecwd_event_venue">
        <option value="0"><?php _e('None','event-calendar-wd');?></option>
        <option value="new"><?php _e('Create New','event-calendar-wd');?></option>
        <optgroup label="Choose venue">
          <?php
          if (!empty($venues)) { ?>
            <?php foreach ($venues as $venue) { ?>
              <option value="<?php echo $venue->ID; ?>" <?php echo selected($venue->ID, $ecwd_event_venue); ?>>
                <?php echo esc_html($venue->post_title); ?>
              </option>
              <?php
            } ?>
            <?php
          }
          ?>
        </optgroup>
      </select>
      <p class="description"><?php _e('Select the venue of the event.', 'event-calendar-wd'); ?></p>
    </td>
  </tr>
  <tbody class="<?php echo $info_table_class; ?>">
  <tr class="ecwd_venue_info_field">
    <th><?php _e('Address:', 'event-calendar-wd'); ?></th>
    <td class="ecwd_venue_address_info">
      <?php echo (isset($selected_venue_metadata['ecwd_venue_location'])) ? esc_html($selected_venue_metadata['ecwd_venue_location']) : ""; ?>
    </td>
  </tr>
  <tr class="ecwd_venue_info_field">
    <th><?php _e('Phone:', 'event-calendar-wd'); ?></th>
    <td class="ecwd_venue_phone_info">
      <?php echo (isset($selected_venue_metadata['ecwd_venue_meta_phone'])) ? esc_html($selected_venue_metadata['ecwd_venue_meta_phone']) : ""; ?>
    </td>
  </tr>
  <tr class="ecwd_venue_info_field">
    <th><?php _e('Website:', 'event-calendar-wd'); ?></th>
    <td class="ecwd_venue_website_info">
      <?php echo (isset($selected_venue_metadata['ecwd_venue_meta_website'])) ? esc_html($selected_venue_metadata['ecwd_venue_meta_website']) : ""; ?>
    </td>
  </tr>
  </tbody>
  <tbody class="<?php echo $form_table_class; ?>">
  <tr class="ecwd_venue_form_field">
    <th>Title:<span style="color:#c60d0d"> *</span></th>
    <td class="ecwd_venue_phone_field">
      <input type="text" class="ecwd_event_venue_title_field"/>
    </td>
  </tr>
  <tr class="ecwd_venue_form_field">
    <th><?php _e('Description','event-calendar-wd');?>:</th>
    <td class="ecwd_venue_phone_field">
      <textarea class="ecwd_event_venue_content_field"></textarea>
    </td>
  </tr>
  <tr class="ecwd_venue_form_field">
    <th><?php _e('Phone','event-calendar-wd')?>:</th>
    <td class="ecwd_venue_phone_field">
      <input type="text" class="ecwd_event_venue_phone_field"/>
    </td>
  </tr>
  <tr class="ecwd_venue_form_field">
    <th><?php _e('Website','event-calendar-wd')?>:</th>
    <td class="ecwd_venue_phone_field">
      <input type="text" class="ecwd_event_venue_website_field"/>
    </td>
  </tr>
  </tbody>
  <tbody class="<?php echo $map_table_class; ?>">
  <?php

  $map_td_th_class = '';
  if ($selected_venue_metadata !== null) {
    if ($venue_meta_show_map !== '1') {
      $map_td_th_class = 'ecwd-hide-map-td';
    }
  }

  $location_field_class = ($selected_venue_metadata == null) ? '' : "ecwd_hidden";

  ?>
  <tr>
    <th class="<?php echo $map_td_th_class; ?>"><?php _e('Address','event-calendar-wd')?>:</th>
    <td class="<?php echo $map_td_th_class; ?>">
      <!-- start ecwd_event_location -->
      <div class="ecwd-meta-field">
        <input type="text" name="ecwd_event_location" id="ecwd_event_location"
               value="<?php echo $ecwd_event_location; ?>" class="<?php echo $location_field_class; ?>"
               size="40"/>
      </div>
      <!-- end ecwd_event_location -->
    </td>
  </tr>
  <tr>
    <th class="<?php echo $map_td_th_class; ?>"><?php _e('Show Google Map','event-calendar-wd')?>:</th>
    <td class="<?php echo $map_td_th_class; ?>">

      <?php
      $ecwd_marker = 1;
      if (!$ecwd_lat_long) {
        $ecwd_lat_long = ',';
        $ecwd_marker = 0;
      }

      $latitude = $longitude = '';
      if (!empty($ecwd_lat_long)) {
        $lat_long_data = explode(',', $ecwd_lat_long);
        if (is_array($lat_long_data) && count($lat_long_data) == 2) {
          $latitude = $lat_long_data[0];
          $longitude = $lat_long_data[1];
        }
      }

      $gmap_key = (isset($ecwd_options['gmap_key'])) ? trim($ecwd_options['gmap_key']) : "";

      $venue_description_class = 'description ecwd_venue_meta_decription';
      if ($selected_venue_metadata !== null) {
        $venue_description_class .= ' ecwd_hidden';
      }

      $show_map_container_class = 'ecwd-meta-field ecwd_venue_show_map_checkbox_container';
      if ($selected_venue_metadata !== null) {
        $show_map_container_class .= ' ecwd_hidden';
      }
      ?>
      <div class="<?php echo $show_map_container_class; ?>">
        <div class="checkbox-div">
          <input type='checkbox' class='ecwd_venue_show_map' id='ecwd_venue_show_map'
                 name='ecwd_venue_show_map' value="1" <?php checked($venue_meta_show_map, 1); ?>/>
          <label for="ecwd_venue_show_map"></label>
        </div>
      </div>
      <?php
      if (!empty($gmap_key)) {
        $venue_map_class = ($venue_meta_show_map !== '1') ? "ecwd-hide-map" : "";
        ?>
        <div class="ecwd_google_map_wrapper">
          <div class="ecwd_google_map <?php echo $venue_map_class; ?>">
            <?php

            if (!$ecwd_map_zoom) {
              $ecwd_map_zoom = 17;
            }

            $ecwd_marker = 1;
            if (!$ecwd_lat_long) {
              $ecwd_map_zoom = 9;
              $ecwd_lat_long = $lat . ',' . $long;
              $ecwd_marker = 0;
            }
            ?>
            <input type="hidden" name="<?php echo ECWD_PLUGIN_PREFIX; ?>_lat_long"
                   id="<?php echo ECWD_PLUGIN_PREFIX; ?>_lat_long"
                   value="<?php echo esc_attr($ecwd_lat_long); ?>"/>
            <input type="hidden" name="<?php echo ECWD_PLUGIN_PREFIX; ?>_marker"
                   id="<?php echo ECWD_PLUGIN_PREFIX; ?>_marker" value="<?php echo esc_attr($ecwd_marker); ?>"/>
            <input type="hidden" name="<?php echo ECWD_PLUGIN_PREFIX; ?>_map_zoom"
                   id="<?php echo ECWD_PLUGIN_PREFIX; ?>_map_zoom"
                   value="<?php echo esc_attr($ecwd_map_zoom); ?>"/>
            <div id="map-canvas" style="width: 100%; height: 300px; min-height: 300px;"></div>
            <?php
            $latitude = $longitude = '';
            if (!empty($ecwd_lat_long)) {
              $lat_long_data = explode(',', $ecwd_lat_long);
              if (is_array($lat_long_data) && count($lat_long_data) == 2) {
                $latitude = $lat_long_data[0];
                $longitude = $lat_long_data[1];
              }
            }

            $lat_long_container_class = 'ecwd_event_venue_lat_long';
            if ($selected_venue_metadata !== null) {
              $lat_long_container_class .= ' ecwd_hidden';
            }
            ?>
            <div class="<?php echo $lat_long_container_class; ?>">
              <label style="width:85px;display:inline-block;" for="ecwd_latitude">Latitude:</label>
              <input type="text" id="ecwd_latitude" value="<?php echo esc_attr($latitude); ?>"/>
              <br/>
              <label style="width:85px;display:inline-block;" for="ecwd_longitude">Longitude:</label>
              <input type="text" id="ecwd_longitude" value="<?php echo esc_attr($longitude); ?>"/>
            </div>
          </div>
        </div>
        <p class="<?php echo $venue_description_class; ?>">
          <?php _e('If venue is not specified you can fill in the address of the event location or click on the map to drag and drop the marker to the event location.', 'event-calendar-wd'); ?>
        </p>
      <?php } else { ?>
        <label></label>
        <span class="<?php echo $venue_description_class; ?>">
               <?php _e('You need Google Maps API key to display maps.', 'event-calendar-wd'); ?>
          <a href="edit.php?post_type=ecwd_event&page=ecwd_general_settings&tab=google_map"><?php _e('Get a key','event-calendar-wd')?></a>
            </span>
      <?php } ?>
    </td>
  </tr>
  </tbody>
  <?php

  $edit_link_class = "ecwd_event_venue_edit_link_container" . (($selected_venue_metadata == null) ? " ecwd_hidden" : "");
  $add_button_class = "ecwd_event_venue_add_button_container" . (($selected_venue_metadata == null) ? "" : " ecwd_hidden");

  ?>
  <tfoot class="<?php echo $edit_link_class; ?>">
  <tr>
    <th>
      <a class="button ecwd_edit_venue_link"
         href="<?php echo (isset($selected_venue_metadata['edit_link'])) ? $selected_venue_metadata['edit_link'] : '#' ?>"
         target="_blank"><?php _e('Edit Venue', 'event-calendar-wd'); ?></a>
    </th>
  </tr>
  </tfoot>
  <tfoot class="<?php echo $add_button_class; ?>">
  <th>
    <button class="button button-primary button-large ecwd-add-venue-save"><?php _e('Save Venue','event-calendar-wd')?></button>
    <span class="spinner"></span>
  </th>
  </tfoot>

</table>