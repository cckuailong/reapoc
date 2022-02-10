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

// Date Formats
$date_format1 = (isset($settings['single_date_format1']) and trim($settings['single_date_format1'])) ? $settings['single_date_format1'] : 'M d Y';
$time_format = get_option('time_format', 'H:i');

$occurrence = isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '';
$occurrence_end_date = trim($occurrence) ? $this->get_end_date_by_occurrence($event->data->ID, (isset($event->date['start']['date']) ? $event->date['start']['date'] : $occurrence)) : '';

$gmt_offset_seconds = $this->get_gmt_offset_seconds((trim($occurrence) ? $occurrence : $event->date['start']['date']), $event);

$gmt_start_time = strtotime((trim($occurrence) ? $occurrence : $event->date['start']['date']).' '.sprintf("%02d", $event->date['start']['hour']).':'.sprintf("%02d", $event->date['start']['minutes']).' '.$event->date['start']['ampm']) - $gmt_offset_seconds;
$gmt_end_time = strtotime((trim($occurrence_end_date) ? $occurrence_end_date : $event->date['end']['date']).' '.sprintf("%02d", ($event->date['end']['hour'] == '0') ? '12' : $event->date['end']['hour']).':'.sprintf("%02d", $event->date['end']['minutes']).' '.$event->date['end']['ampm']) - $gmt_offset_seconds;

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
<div class="mec-local-time-details mec-frontbox" id="mec_local_time_details">
    <i class="mec-sl-clock"></i><h3 class="mec-local-time mec-frontbox-title"><?php _e('Local Time', 'modern-events-calendar-lite'); ?></h3>
    <ul>
        <li><?php echo sprintf(__('Timezone: %s', 'modern-events-calendar-lite'), '<span>'.$timezone.'</span>'); ?></li>
        <li><?php echo sprintf(__('Date: %s', 'modern-events-calendar-lite'), $this->date_label(array('date'=>date('Y-m-d', $user_start_time)), array('date'=>date('Y-m-d', $user_end_time)), $date_format1)); ?></li>
        <?php if(!$hide_time and trim($time_format)): ?>
        <li><?php echo sprintf(__('Time: %s', 'modern-events-calendar-lite'), '<span>'.($allday ? $this->m('all_day', __('All Day' , 'modern-events-calendar-lite')) : ($hide_end_time ? date($time_format, $user_start_time) : date($time_format, $user_start_time).' - '.date($time_format, $user_end_time))).'</span>'); ?></li>
        <?php endif; ?>
    </ul>
</div>