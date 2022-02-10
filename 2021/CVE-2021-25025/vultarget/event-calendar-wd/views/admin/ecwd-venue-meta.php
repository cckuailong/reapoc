<?php
/**
 * Display for Event Custom Post Types
 */
global $post, $ecwd_options;
$post_id = $post->ID;
$meta = get_post_meta($post_id);

// Load up all post meta data

$ecwd_venue_meta_phone = get_post_meta($post->ID, 'ecwd_venue_meta_phone', true);
$ecwd_venue_meta_website = get_post_meta($post->ID, 'ecwd_venue_meta_website', true);
$ecwd_venue_show_map = get_post_meta($post->ID, 'ecwd_venue_show_map', true);


$ecwd_venue_location = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX . '_venue_location', true);
$ecwd_venue_lat_long = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX . '_venue_lat_long', true);
$ecwd_map_zoom = get_post_meta($post->ID, ECWD_PLUGIN_PREFIX . '_map_zoom', true);
if (!$ecwd_map_zoom) {
    $ecwd_map_zoom = 17;
}

?>


<table class="form-table ecwd-admin-fields">
    <tr>
        <td>
            <div class="ecwd-venue-meta-fields">
                <!-- start ecwd_event_location -->
                <div class="ecwd-meta-field">
                    <label for="ecwd_event_location"><?php _e('Address','event-calendar-wd')?>:</label>
                    <input type="text" name="ecwd_venue_location" id="ecwd_event_location"
                           value="<?php echo esc_attr($ecwd_venue_location); ?>" size="70"/>
                </div>
                <!-- end ecwd_event_location -->
                <!-- start ecwd_venue_meta_phone -->
                <div class="ecwd-meta-field">
                    <label for="ecwd_venue_meta_phone"><?php _e('Phone','event-calendar-wd')?>:</label>
                    <input type="text" name="ecwd_venue_meta_phone"
                           id="ecwd_venue_meta_phone" value="<?php echo esc_attr($ecwd_venue_meta_phone); ?>"/>
                </div>
                <!-- end ecwd_venue_meta_phone -->
                <!-- start ecwd_venue_meta_website -->
                <div class="ecwd-meta-field">
                    <label for="ecwd_venue_meta_website"><?php _e('Website','event-calendar-wd')?>:</label>
                    <input type="text" name="ecwd_venue_meta_website"
                           id="ecwd_venue_meta_website" value="<?php echo esc_attr($ecwd_venue_meta_website); ?>"/>
                </div>
                <!-- end ecwd_venue_meta_website -->
                <div class="ecwd-meta-field">
                    <label for="ecwd_venue_show_map"><?php _e('Show Google Maps','event-calendar-wd')?>:</label>
                    <input type='checkbox' id='ecwd_venue_show_map' name='ecwd_venue_show_map' value="1"
                      <?php checked($ecwd_venue_show_map, '1'); ?>/>
                </div>

            </div>

            <?php
            $description_class = ($ecwd_venue_show_map !== '1') ? 'ecwd_hidden' : '';
            $description_class .= ' description ecwd_venue_meta_decription';

            $gmap_key = (isset($ecwd_options['gmap_key'])) ? trim($ecwd_options['gmap_key']) : "";
            if (!empty($gmap_key)) {

                $class = ($ecwd_venue_show_map !== '1') ? "ecwd-hide-map" : "";
                ?>
                <div class="ecwd_google_map_wrapper">
                    <div class="ecwd_google_map <?php echo $class; ?>">
                        <?php
                        $ecwd_marker = 1;
                        if (!$ecwd_venue_lat_long) {
                            $ecwd_map_zoom = 9;
                            $ecwd_venue_lat_long = $lat . ',' . $long;
                            $ecwd_marker = 0;
                        } ?>
                        <input type="hidden" name="<?php echo ECWD_PLUGIN_PREFIX; ?>_venue_lat_long"
                               id="<?php echo ECWD_PLUGIN_PREFIX; ?>_lat_long"
                               value="<?php echo esc_attr($ecwd_venue_lat_long); ?>"/>
                        <input type="hidden" name="<?php echo ECWD_PLUGIN_PREFIX; ?>_marker"
                               id="<?php echo ECWD_PLUGIN_PREFIX; ?>_marker" value="<?php echo esc_attr($ecwd_marker); ?>"/>
                        <input type="hidden" name="<?php echo ECWD_PLUGIN_PREFIX; ?>_map_zoom"
                               id="<?php echo ECWD_PLUGIN_PREFIX; ?>_map_zoom"
                               value="<?php echo esc_attr($ecwd_map_zoom); ?>"/>

                        <div id="map-canvas" style="width: 100%; height: 300px; min-height: 300px;">

                        </div>
                        <?php
                        $latitude = $longitude = '';
                        if (!empty($ecwd_venue_lat_long)) {
                            $lat_long_data = explode(',', $ecwd_venue_lat_long);
                            if (is_array($lat_long_data) && count($lat_long_data) == 2) {
                                $latitude = $lat_long_data[0];
                                $longitude = $lat_long_data[1];
                            }
                        }
                        ?>
                        <label style="width:85px" for="<?php echo ECWD_PLUGIN_PREFIX; ?>_latitude">Latitude:</label>
                        <input type="text" id="<?php echo ECWD_PLUGIN_PREFIX; ?>_latitude" value="<?php echo esc_attr($latitude); ?>"/>
                        <br/>
                        <label style="width:85px" for="<?php echo ECWD_PLUGIN_PREFIX; ?>_longitude">Longitude:</label>
                        <input type="text" id="<?php echo ECWD_PLUGIN_PREFIX; ?>_longitude" value="<?php echo esc_attr($longitude); ?>"/>
                    </div>
                </div>
                <p class="<?php echo $description_class; ?>">
                    <?php _e('Fill in the address of the venue or click on the map to drag and drop the marker to a specific location', 'event-calendar-wd'); ?>
                </p>
            <?php } else { ?>
                <label></label>
                <span class="<?php echo $description_class; ?>">
          <?php _e('You need Google Maps API key to display maps.', 'event-calendar-wd'); ?>
                    <a href="edit.php?post_type=ecwd_event&page=ecwd_general_settings&tab=google_map"><?php _e('Get a key','event-calendar-wd')?></a>
        </span>
            <?php } ?>

        </td>
    </tr>
</table>
