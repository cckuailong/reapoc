<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_Main $this */

// PRO Version is required
if(!$this->getPRO()) return;

// MEC Settings
$settings = $this->get_settings();

// Google Maps on single page is disabled
if(!isset($settings['google_maps_status']) or (isset($settings['google_maps_status']) and !$settings['google_maps_status'])) return;

$event = $event[0];
$uniqueid = (isset($uniqueid) ? $uniqueid : $event->data->ID);

// Map is disabled for this event
$dont_show_map = ((isset($event->data->meta['mec_dont_show_map']) and is_numeric($event->data->meta['mec_dont_show_map'])) ? $event->data->meta['mec_dont_show_map'] : 0);
if(isset($event->date) and isset($event->date['start']) and isset($event->date['start']['timestamp'])) $dont_show_map = MEC_feature_occurrences::param($event->ID, $event->date['start']['timestamp'], 'dont_show_map', $dont_show_map);

if($dont_show_map) return;

// Event ID
$event_id = $event->ID;

$location_id = $this->get_master_location_id($event);
$location = ($location_id ? $this->get_location_data($location_id) : array());

// Event location geo point
$latitude = isset($location['latitude']) ? $location['latitude'] : '';
$longitude = isset($location['longitude']) ? $location['longitude'] : '';
$address = isset($location['address']) ? $location['address'] : '';

// Try to get the latitude and longitude on the fly
if(!trim($latitude) or !trim($longitude))
{
    $geo_point = $this->get_lat_lng($address);

    $latitude = $geo_point[0];
    $longitude = $geo_point[1];

    if($location_id)
    {
        update_term_meta($location_id, 'latitude', $latitude);
        update_term_meta($location_id, 'longitude', $longitude);
    }
}

// Still Latitude and Longitude are wrong!
if(!trim($latitude) or !trim($longitude)) return;

// Include Map Assets such as JS and CSS libraries
if(!$this->is_ajax()) $this->load_map_assets();

// Get Direction Status
$get_direction = (isset($settings['google_maps_get_direction_status']) and in_array($settings['google_maps_get_direction_status'], array(0,1,2))) ? $settings['google_maps_get_direction_status'] : 0;

$additional_location_ids = get_post_meta($event_id, 'mec_additional_location_ids', true);
$event_locations = array_keys((array)$event->data->locations);

$map_data = new stdClass;
$map_data->id = $uniqueid;
$map_data->atts = array(
    'location_map_zoom' => (isset($settings['google_maps_zoomlevel']) ? $settings['google_maps_zoomlevel'] : 14),
    'location_center_lat' => null,
    'location_center_long' => null,
    'use_orig_map' => true
);

$map_data->events = array($event_id => $event);
$map_data->render = $render;
$map_data->geolocation = '0';
$map_data->sf_status = null;

$current_event = (isset($map_data->events[$event_id]) ? array($map_data->events[$event_id]) : array());
$map_data->events = apply_filters('mec_location_load_additional', $current_event, $additional_location_ids, $event_locations);

// Initialize MEC Google Maps jQuery plugin
$javascript = '<script type="text/javascript">
var p'.$uniqueid.';
jQuery(document).ready(function()
{
    p'.$uniqueid.' = jQuery("#mec_map_canvas'.$uniqueid.'").mecGoogleMaps(
    {
        latitude: "'.$latitude.'",
        longitude: "'.$longitude.'",
        autoinit: '.((!isset($auto_init) or (isset($auto_init) and $auto_init)) ? 'true' : 'false').',
        zoom: '.(isset($settings['google_maps_zoomlevel']) ? $settings['google_maps_zoomlevel'] : 14).',
        icon: "'.apply_filters('mec_marker_icon', $this->asset('img/m-04.png')).'",
        styles: '.((isset($settings['google_maps_style']) and trim($settings['google_maps_style']) != '') ? $this->get_googlemap_style($settings['google_maps_style']) : "''").',
        fullscreen_button: '.((isset($settings['google_maps_fullscreen_button']) and trim($settings['google_maps_fullscreen_button'])) ? 'true' : 'false').',
        markers: '.json_encode($render->markers($map_data->events)).',
        clustering_images: "'.$this->asset('img/cluster1/m').'",
        getDirection: '.$get_direction.',
        directionOptions:
        {
            form: "#mec_get_direction_form'.$uniqueid.'",
            reset: "#mec_map_get_direction_reset'.$uniqueid.'",
            addr: "#mec_get_direction_addr'.$uniqueid.'",
            destination:
            {
                latitude: "'.$latitude.'",
                longitude: "'.$longitude.'",
            },
            startMarker: "'.apply_filters('mec_start_marker_icon', $this->asset('img/m-03.png')).'",
            endMarker: "'.apply_filters('mec_end_marker_icon', $this->asset('img/m-04.png')).'"
        }
    });
});

function mec_init_gmap'.$uniqueid.'()
{
    p'.$uniqueid.'.init();
}
</script>';
$javascript = apply_filters('mec_map_load_script', $javascript, $map_data, $settings);

if(!function_exists('is_plugin_active')) include_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Include javascript code into the footer
if($this->is_ajax()) echo $javascript;
elseif (is_plugin_active( 'mec-single-builder/mec-single-builder.php')) echo $javascript;
else $factory->params('footer', $javascript);
?>

<div class="mec-googlemap-details" id="mec_map_canvas<?php echo $uniqueid; ?>" style="height: 500px;">
    <?php if (is_plugin_active( 'divi-single-builder/divi-single-builder.php')) : ?>
         <img src="<?php echo plugin_dir_url(__FILE__ ); ?>../../../assets/img/map.jpg" />
    <?php else : ?>
        <?php do_action('mec_map_inner_element_tools', $settings); ?>
    <?php endif; ?>
</div>
<?php do_action('mec_map_before_direction'); ?>
<?php if($get_direction): ?>
<div class="mec-get-direction">
    <form method="post" action="#" id="mec_get_direction_form<?php echo $uniqueid; ?>" class="clearfix">
        <div class="mec-map-get-direction-address-cnt">
            <input class="mec-map-get-direction-address" type="text" placeholder="<?php esc_attr_e('Address from ...', 'modern-events-calendar-lite') ?>" id="mec_get_direction_addr<?php echo $uniqueid; ?>" />
            <span class="mec-map-get-direction-reset mec-util-hidden" id="mec_map_get_direction_reset<?php echo $uniqueid; ?>">X</span>
        </div>
        <div class="mec-map-get-direction-btn-cnt btn btn-primary">
            <input type="submit" value="<?php _e('Get Directions', 'modern-events-calendar-lite'); ?>" />
        </div>
    </form>
</div>
<?php endif;