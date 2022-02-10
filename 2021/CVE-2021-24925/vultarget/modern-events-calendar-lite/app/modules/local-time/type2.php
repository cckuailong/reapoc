<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_main $this */

// MEC Settings
$settings = $this->get_settings();

// The module is disabled
if(!isset($settings['local_time_module_status']) or (isset($settings['local_time_module_status']) and !$settings['local_time_module_status'])) return;

// Get the visitor Timezone
$timezone = $this->get_timezone_by_ip();

// Timezone is not detected!
if(!$timezone) return;

$start_time = isset($event->data->time['start_raw']) ? $event->data->time['start_raw'] : '';
$end_time = isset($event->data->time['end_raw']) ? $event->data->time['end_raw'] : '';

// Date Formats
$date_format1 = (isset($settings['single_date_format1']) and trim($settings['single_date_format1'])) ? $settings['single_date_format1'] : 'M d Y';
$time_format = get_option('time_format', 'H:i');

$gmt_offset_seconds = $this->get_gmt_offset_seconds($event->date['start']['date'], $event);

/**
 * TODO: Convert to class
 */
$event_id = $event->ID;

global $MEC_Events_dates, $MEC_Events_dates_localtime, $MEC_Shortcode_id;
if(!isset($MEC_Events_dates_localtime[$MEC_Shortcode_id]) || empty($MEC_Events_dates_localtime[$MEC_Shortcode_id]))
{
    $MEC_Events_dates_localtime[$MEC_Shortcode_id] = $MEC_Events_dates;
}

$dates = array();
if(is_array($MEC_Events_dates_localtime[$MEC_Shortcode_id][$event_id]))
{
    $k = $this->array_key_first($MEC_Events_dates_localtime[$MEC_Shortcode_id][$event_id]);
    if(isset($MEC_Events_dates_localtime[$MEC_Shortcode_id][$event_id][$k]))
    {
        $dates = (isset($MEC_Events_dates_localtime[$MEC_Shortcode_id][$event_id][$k]) ? $MEC_Events_dates_localtime[$MEC_Shortcode_id][$event_id][$k] : NULL);
        $start_time = isset($dates['start']['time']) ? $dates['start']['time'] : $start_time;
        $end_time = isset($dates['end']['time']) ? $dates['end']['time'] : $end_time;
        unset($MEC_Events_dates_localtime[$MEC_Shortcode_id][$event_id][$k]);
    }
}

$start_date = (isset($dates['start']['date']) ? $dates['start']['date'] : $event->date['start']['date']);
$end_date = (isset($dates['end']['date']) ? $dates['end']['date'] : $event->date['end']['date']);

$gmt_start_time = strtotime($start_date.' '.$start_time) - $gmt_offset_seconds;
$gmt_end_time = strtotime($end_date.' '.$end_time) - $gmt_offset_seconds;

$user_timezone = new DateTimeZone($timezone);
$gmt_timezone = new DateTimeZone('GMT');
$gmt_datetime = new DateTime(date('Y-m-d H:i:s', $gmt_start_time), $gmt_timezone);
$offset = $user_timezone->getOffset($gmt_datetime);

$user_start_time = $gmt_start_time + $offset;
$user_end_time = $gmt_end_time + $offset;

$allday = isset($event->data->meta['mec_allday']) ? $event->data->meta['mec_allday'] : 0;
$hide_time = isset($event->data->meta['mec_hide_time']) ? $event->data->meta['mec_hide_time'] : 0;
$hide_end_time = isset($event->data->meta['mec_hide_end_time']) ? $event->data->meta['mec_hide_end_time'] : 0;
?>
<div class="mec-localtime-details" id="mec_localtime_details">
    <div class="mec-localtime-wrap">
        <i class="mec-sl-clock"></i>
        <span class="mec-localtitle"><?php _e('Local Time:', 'modern-events-calendar-lite'); ?></span>
        <div class="mec-localdate"><?php echo sprintf(__('%s |', 'modern-events-calendar-lite'), $this->date_label(array('date'=>date('Y-m-d', $user_start_time)), array('date'=>date('Y-m-d', $user_end_time)), $date_format1)); ?></div>
        <?php if(!$hide_time and trim($time_format)): ?>
        <div class="mec-localtime"><?php echo sprintf(__('%s', 'modern-events-calendar-lite'), '<span>'.($allday ? $this->m('all_day', __('All Day' , 'modern-events-calendar-lite')) : ($hide_end_time ? date($time_format, $user_start_time) : date($time_format, $user_start_time).' - '.date($time_format, $user_end_time))).'</span>'); ?></div>
        <?php endif; ?>
    </div>
</div>