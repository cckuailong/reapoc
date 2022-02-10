<?php
/** no direct access **/
defined('MECEXEC') or die();

use Elementor\Plugin;
if(!did_action('elementor/loaded')) return;

$styling = $this->main->get_styling();
$event_colorskin = (isset($styling['mec_colorskin'] ) || isset($styling['color'])) ? 'colorskin-custom' : '';
$settings = $this->main->get_settings();
$current_month_divider = $this->request->getVar('current_month_divider', 0);

global $MEC_Shortcode_id;
$MEC_Shortcode_id = !empty($MEC_Shortcode_id) ? $MEC_Shortcode_id : $this->atts['id'];

// colorful
$colorful_flag = $colorful_class = '';
if($this->style == 'colorful')
{
	$colorful_flag = true;
	$this->style = 'modern';
	$colorful_class = ' mec-event-custom-colorful';
}
?>
<div class="mec-wrap <?php echo $event_colorskin.$colorful_class; ?>">
    <div class="mec-event-custom-<?php echo $this->style; ?>">
        <?php
            $count = $this->count;

            if($count == 0 or $count == 5) $col = 4;
            else $col = 12 / $count;
            global $post;
            global $mec_old_post;
            $mec_old_post = $post;

            $rcount = 1;
            if(!empty($this->events))
            {
                foreach($this->events as $date => $events)
                {
                    $month_id = date('Ym', strtotime($date));
                    foreach($events as $event)
                    {
                        global $post;
                        $post = $event->data->post;

                        if($this->count == '1' and $this->month_divider and $month_id != $current_month_divider): $current_month_divider = $month_id; ?>
                            <div class="mec-month-divider" data-toggle-divider="mec-toggle-<?php echo date('Ym', strtotime($date)); ?>-<?php echo $this->id; ?>"><span><?php echo $this->main->date_i18n('F Y', strtotime($date)); ?></span><i class="mec-sl-arrow-down"></i></div>
                        <?php endif;

                        echo ($rcount == 1) ? '<div class="row">' : '';
                        echo '<div class="col-md-'.$col.' col-sm-'.$col.'">';
                        echo '<article class="mec-event-article mec-sd-event-article'. get_the_ID().' mec-clear" itemscope>';
                        echo Plugin::instance()->frontend->get_builder_content_for_display($this->style, true);
                        echo '</article></div>';

                        if($rcount == $count)
                        {
                            echo '</div>';
                            $rcount = 0;
                        }

                        $rcount++;
                    }
                }
            }

            global $post;
            global $mec_old_post;
            $post = $mec_old_post;
        ?>
	</div>
</div>
<?php
$map_eventss = array();
if(isset($map_events) && !empty($map_events))
{
    foreach($map_events as $key => $value)
    {
        foreach($value as $keyy => $valuee) $map_eventss[] = $valuee;
    }
}

if(isset($this->map_on_top) and $this->map_on_top):
if(isset($map_eventss) and !empty($map_eventss))
{
    // Include Map Assets such as JS and CSS libraries
    $this->main->load_map_assets();

    $map_javascript = '<script type="text/javascript">
    jQuery(document).ready(function()
    {
        var jsonPush = gmapSkin('.json_encode($this->render->markers($map_eventss)).');
        jQuery("#mec_googlemap_canvas'.$this->id.'").mecGoogleMaps(
        {
            id: "'.$this->id.'",
            atts: "'.http_build_query(array('atts'=>$this->atts), '', '&').'",
            zoom: '.(isset($settings['google_maps_zoomlevel']) ? $settings['google_maps_zoomlevel'] : 14).',
            icon: "'.apply_filters('mec_marker_icon', $this->main->asset('img/m-04.png')).'",
            styles: '.((isset($settings['google_maps_style']) and trim($settings['google_maps_style']) != '') ? $this->main->get_googlemap_style($settings['google_maps_style']) : "''").',
            markers: jsonPush,
            clustering_images: "'.$this->main->asset('img/cluster1/m').'",
            getDirection: 0,
            ajax_url: "'.admin_url('admin-ajax.php', NULL).'",
            geolocation: "'.$this->geolocation.'",
        });
    });
    </script>';

    $map_data = new stdClass;
    $map_data->id = $this->id;
    $map_data->atts = $this->atts;
    $map_data->events =  $map_eventss;
    $map_data->render = $this->render;
    $map_data->geolocation = $this->geolocation;
    $map_data->sf_status = null;
    $map_data->main = $this->main;

    $map_javascript = apply_filters('mec_map_load_script', $map_javascript, $map_data, $settings);

    // Include javascript code into the page
    if($this->main->is_ajax()) echo $map_javascript;
    else $this->factory->params('footer', $map_javascript);
}
endif;