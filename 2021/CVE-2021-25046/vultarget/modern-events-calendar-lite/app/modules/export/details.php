<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_main $this */

// MEC Settings
$settings = $this->get_settings();

// Expired?
if($this->is_expired($event) and isset($settings['export_module_hide_expired']) and $settings['export_module_hide_expired']) return;

// Export module on single page is disabled
if(!isset($settings['export_module_status']) or (isset($settings['export_module_status']) and !$settings['export_module_status'])) return;

$title = isset($event->data->title) ? $event->data->title : '';
$location_id = $this->get_master_location_id($event);
$location_data = ($location_id ? $this->get_location_data($location_id) : array());
$location = (($location_id and $location_data) ? '&location='.urlencode($location_data['address']) : '');
$occurrence = isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '';
$occurrence_end_date = trim($occurrence) ? $this->get_end_date_by_occurrence($event->data->ID, (isset($event->date['start']['date']) ? $event->date['start']['date'] : $occurrence)) : '';

$content = (isset($event->data->post->post_content) and trim($event->data->post->post_content)) ? $event->data->post->post_content : $title;
$content = preg_replace('#<a[^>]*href="((?!/)[^"]+)">[^<]+</a>#', '$0 ( $1 )', $content);
$content = strip_shortcodes(strip_tags($content));
$content = apply_filters('mec_add_content_to_export_google_calendar_details', $content, $event->data->ID);

$start_date_temp = $start_hour_temp = '';
if(!empty($event->date))
{
    $start_date_temp = isset($event->date['start']['date']) ? $event->date['start']['date'] : NULL;
    $start_hour_temp = isset($event->date['start']['hour']) ? $event->date['start']['hour'] : NULL;
}

$start_minutes_temp = isset($event->date['start']['minutes']) ? $event->date['start']['minutes'] : NULL;
$start_ampm_temp = isset($event->date['start']['ampm']) ? $event->date['start']['ampm'] : NULL;

$end_date_temp = isset($event->date['end']['date']) ? $event->date['end']['date'] : NULL;
$end_hour_temp = isset($event->date['end']['hour']) ? $event->date['end']['hour'] : NULL;
$end_minutes_temp = isset($event->date['end']['minutes']) ? $event->date['end']['minutes'] : NULL;
$end_ampm_temp = isset($event->date['end']['ampm']) ? $event->date['end']['ampm'] : NULL;

if((is_null($start_date_temp) or is_null($start_hour_temp) or is_null($start_minutes_temp) or is_null($start_ampm_temp) or is_null($end_date_temp) or is_null($end_hour_temp) or is_null($end_minutes_temp) or is_null($end_ampm_temp)) and !trim($occurrence))
{
    return;
}

$allday = isset($event->data->meta['mec_allday']) ? $event->data->meta['mec_allday'] : 0;
if($allday)
{
    $start_hour_temp = 12;
    $start_minutes_temp = 0;
    $start_ampm_temp = 'AM';

    $end_hour_temp = 12;
    $end_minutes_temp = 0;
    $end_ampm_temp = 'AM';

    if(trim($occurrence_end_date)) $occurrence_end_date = date('Y-m-d', strtotime('+1 day', strtotime($occurrence_end_date)));
    $end_date_temp = date('Y-m-d', strtotime('+1 day', strtotime($end_date_temp)));
}

$start_time = strtotime((trim($occurrence) ? $occurrence : $start_date_temp).' '.sprintf("%02d", $start_hour_temp).':'.sprintf("%02d", $start_minutes_temp).' '.$start_ampm_temp);
$end_time = strtotime((trim($occurrence_end_date) ? $occurrence_end_date : $end_date_temp).' '.sprintf("%02d", $end_hour_temp).':'.sprintf("%02d", $end_minutes_temp).' '.$end_ampm_temp);
$gmt_offset_seconds = $this->get_gmt_offset_seconds($start_time, $event);

// Recurring Rules
$rrule = $this->get_ical_rrules($event->data, true);

$description = "$content";

ob_start();
do_action('mec_add_to_calander_event_description', $event);
$description .= html_entity_decode(ob_get_clean());
?>
<div class="mec-event-export-module mec-frontbox">
     <div class="mec-event-exporting">
        <div class="mec-export-details">
            <ul>
                <?php if($settings['sn']['googlecal']): ?><li><a class="mec-events-gcal mec-events-button mec-color mec-bg-color-hover mec-border-color" href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?php echo urlencode($title); ?>&dates=<?php echo gmdate('Ymd\\THi00\\Z', ($start_time - $gmt_offset_seconds)); ?>/<?php echo gmdate('Ymd\\THi00\\Z', ($end_time - $gmt_offset_seconds)); ?>&details=<?php echo urlencode($description); ?><?php echo $location; ?><?php echo (trim($rrule) ? '&recur='.urlencode($rrule) : ''); ?>" target="_blank"><?php echo __('+ Add to Google Calendar', 'modern-events-calendar-lite'); ?></a></li><?php endif; ?>
                <?php if($settings['sn']['ical']): ?><li><a class="mec-events-gcal mec-events-button mec-color mec-bg-color-hover mec-border-color" href="<?php echo $this->ical_URL($event->data->ID, $occurrence); ?>"><?php echo __('+ iCal / Outlook export', 'modern-events-calendar-lite'); ?></a></li><?php endif; ?>
            </ul>
        </div>
    </div>
</div>