<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_map $this */

// MEC Settings
$settings = $this->main->get_settings();
$settings['view_mode'] = isset($this->atts['location_view_mode'])?$this->atts['location_view_mode']:'normal';
$settings['map'] = isset($settings['default_maps_view'])?$settings['default_maps_view']:'google';

// Return the data if called by AJAX
if(isset($this->atts['return_items']) and $this->atts['return_items'])
{
    echo json_encode(array('markers'=>$this->render->markers($this->events)));
    exit;
}

$events_data = $this->render->markers($this->events);
if(count($this->events))
{
    // Include Map Assets such as JS and CSS libraries
    $this->main->load_map_assets($settings);

    $javascript = '<script type="text/javascript">
    jQuery(document).ready(function()
    {
        jQuery("#mec_map_canvas'.$this->id.'").mecGoogleMaps(
        {
            id: "'.$this->id.'",
            atts: "'.http_build_query(array('atts'=>$this->atts), '', '&').'",
            zoom: '.(isset($settings['google_maps_zoomlevel']) ? $settings['google_maps_zoomlevel'] : 14).',
            icon: "'.apply_filters('mec_marker_icon', $this->main->asset('img/m-04.png')).'",
            styles: '.((isset($settings['google_maps_style']) and trim($settings['google_maps_style']) != '') ? $this->main->get_googlemap_style($settings['google_maps_style']) : "''").',
            fullscreen_button: '.((isset($settings['google_maps_fullscreen_button']) and trim($settings['google_maps_fullscreen_button'])) ? 'true' : 'false').',
            markers: '.json_encode($events_data).',
            geolocation: '.$this->geolocation.',
            geolocation_focus: '.$this->geolocation_focus.',
            clustering_images: "'.$this->main->asset('img/cluster1/m').'",
            getDirection: 0,
            ajax_url: "'.admin_url('admin-ajax.php', NULL).'",
            sf:
            {
                container: "'.($this->sf_status ? '#mec_search_form_'.$this->id : '').'",
                reset: '.($this->sf_reset_button ? 1 : 0).',
                refine: '.($this->sf_refine ? 1 : 0).',
            },
        });
    });
    </script>';

    $javascript = apply_filters('mec_map_load_script', $javascript, $this, $settings);

    // Include javascript code into the page
    if($this->main->is_ajax() or $this->main->preview()) echo $javascript;
    else $this->factory->params('footer', $javascript);
}

do_action('mec_start_skin', $this->id);
do_action('mec_map_skin_head');
?>
<?php if($settings['view_mode'] == 'normal') : ?>
<div class="mec-wrap mec-skin-map-container <?php echo $this->html_class; ?>" id="mec_skin_<?php echo $this->id; ?>">

    <?php if($this->sf_status) echo $this->sf_search_form(); ?>
    <?php do_action('mec_map_skin_before_form', $settings); ?>

    <?php if(count($this->events)): ?>
    <div class="mec-googlemap-skin" id="mec_map_canvas<?php echo $this->id; ?>" style="height: 500px;">
        <?php do_action('mec_map_inner_element_tools', $settings); ?>
    </div>
    <?php else: ?>
    <p class="mec-error"><?php _e('No events found!', 'modern-events-calendar-lite'); ?></p>
    <?php endif; ?>

</div>
<?php else: ?>
<div class="mec-wrap">
    <div class="row">
        <div class="col-sm-12">
            <div class="mec-skin-map-container <?php echo $this->html_class; ?>" id="mec_skin_<?php echo $this->id; ?>">
                <?php if($this->sf_status) echo $this->sf_search_form(); ?>
            </div>
        </div>
    </div>
    <div class="row mec-map-events-wrap">
        <div class="col-sm-7">
            <?php if(count($this->events)): ?>
                <div class="mec-googlemap-skin" id="mec_map_canvas<?php echo $this->id; ?>" style="height: 600px;">
                    <?php do_action('mec_map_inner_element_tools', $settings); ?>
                </div>
            <?php else: ?>
                <p class="mec-error"><?php _e('No events found!', 'modern-events-calendar-lite'); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-sm-5" id="mec-map-skin-side-<?php echo $this->id; ?>"></div>
    </div>
</div>
<?php endif; ?>