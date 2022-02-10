<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_main $this */

// MEC Settings
$settings = $this->get_settings();

// The module is disabled
if(!isset($settings['next_event_module_status']) or (isset($settings['next_event_module_status']) and !$settings['next_event_module_status'])) return;

// Next Event Method
$method = (isset($settings['next_event_module_method']) ? $settings['next_event_module_method'] : 'occurrence');
$maximum = (isset($settings['next_event_module_multiple_count']) ? (int) $settings['next_event_module_multiple_count'] : 10);

// Not Multiple Occurrences
if($method != 'multiple')
{
    include MEC::import('app.modules.next-event.details', true, true);
    return;
}

// Date Format
$date_format1 = isset($settings['next_event_module_date_format1']) ? $settings['next_event_module_date_format1'] : 'M d Y';
$time_format = get_option('time_format');

$date = array();
if(!empty($event->date)) $date = $event->date;

$occurrence = (isset($date['start']) and isset($date['start']['date'])) ? $date['start']['date'] : date('Y-m-d');
if(isset($_GET['occurrence']) and trim($_GET['occurrence'])) $occurrence = sanitize_text_field($_GET['occurrence']);

$occurrence_time = isset($_GET['time']) ? sanitize_text_field($_GET['time']) : NULL;

// Event Dates
$dates = $this->get_event_next_occurrences($event, $occurrence, $maximum, $occurrence_time);

// Remove Current Occurrence
if(is_array($date) and isset($date['start']) and isset($date['start']['timestamp']) and is_array($dates) and isset($dates[0]) and isset($dates[0]['start']) and isset($dates[0]['start']['timestamp']) and $dates[0]['start']['timestamp'] == $date['start']['timestamp']) unset($dates[0]);

// Nothing Found!
if(!is_array($dates) or (is_array($dates) and !count($dates))) return false;

$time_comment = isset($event->data->meta['mec_comment']) ? $event->data->meta['mec_comment'] : '';
$allday = isset($event->data->meta['mec_allday']) ? $event->data->meta['mec_allday'] : 0;
$hide_time = isset($event->data->meta['mec_hide_time']) ? $event->data->meta['mec_hide_time'] : 0;
$hide_end_time = isset($event->data->meta['mec_hide_end_time']) ? $event->data->meta['mec_hide_end_time'] : 0;
?>
<div class="mec-next-event-details mec-frontbox" id="mec_next_event_details">
    <div class="mec-next-<?php echo $method; ?>">
        <h3 class="mec-frontbox-title"><?php echo esc_html__('Next Occurrences', 'modern-events-calendar-lite'); ?></h3>
        <ul>
            <?php foreach($dates as $date): ?>
            <li>
                <a href="<?php echo $this->get_event_date_permalink($event, $date['start']['date'], true, array('start_raw' => date($time_format, $date['start']['timestamp']))); ?>">
                    <span class="mec-date"><?php echo $this->date_label($date['start'], (isset($date['end']) ? $date['end'] : NULL), $date_format1); ?></span>

                    <?php if(!$hide_time): ?>
                    <span class="mec-time">
                        <dl>
                        <?php if($allday == '0' and isset($event->data->time) and trim($event->data->time['start'])): ?>
                        <dd><abbr class="mec-events-abbr"><?php echo date($time_format, $date['start']['timestamp']); ?> - <?php echo ($hide_end_time ? '' : ' '.date($time_format, $date['end']['timestamp'])); ?></abbr></dd>
                        <?php else: ?>
                        <dd><abbr class="mec-events-abbr"><?php echo $this->m('all_day', __('All Day' , 'modern-events-calendar-lite')); ?></abbr></dd>
                        <?php endif; ?>
                        </dl>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>