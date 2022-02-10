<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_main $this */

// MEC Settings
$settings = $this->get_settings();

// The module is disabled
if(!isset($settings['next_event_module_status']) or (isset($settings['next_event_module_status']) and !$settings['next_event_module_status'])) return;

// Next Event Method
$method = isset($settings['next_event_module_method']) ? $settings['next_event_module_method'] : 'occurrence';

// Multiple Occurrences
if($method == 'multiple')
{
    include MEC::import('app.modules.next-event.multiple', true, true);
    return;
}

// Date Format
$date_format1 = isset($settings['next_event_module_date_format1']) ? $settings['next_event_module_date_format1'] : 'M d Y';

$date = array();
if(!empty($event->date)) $date = $event->date;

$start_date = (isset($date['start']) and isset($date['start']['date'])) ? $date['start']['date'] : date('Y-m-d');
if(isset($_GET['occurrence']) and trim($_GET['occurrence'])) $start_date = sanitize_text_field($_GET['occurrence']);

$next_date = array();
$next_time = array();

// Show next occurrence from other events
if($method == 'event')
{
    $start_hour = (isset($date['start']) and isset($date['start']['hour'])) ? $date['start']['hour'] : 8;
    $start_minutes = (isset($date['start']) and isset($date['start']['minutes'])) ? $date['start']['minutes'] : 0;
    $start_ampm = (isset($date['start']) and isset($date['start']['ampm'])) ? $date['start']['ampm'] : 'AM';

    $next = $this->get_next_event(array
    (
        'show_past_events'=>0,
        'sk-options'=>array
        (
            'list'=>array
            (
                'start_date_type'=>'date',
                'start_date'=>$start_date,
                'limit'=>1,
            )
        ),
        'seconds_date'=>$start_date,
        'seconds'=>$this->time_to_seconds($this->to_24hours($start_hour, $start_ampm), $start_minutes),
        'exclude'=>($method == 'event' ? array($event->ID) : NULL),
        'include'=>NULL,
    ));

    // Nothing Found!
    if(!isset($next->data)) return false;

    $next_date = $next->date;
    $next_time = $next->data->time;
}
else
{
    // Nothing Found!
    if(!isset($event->dates) or (isset($event->dates) and !is_array($event->dates)) or (isset($event->dates) and is_array($event->dates) and !count($event->dates))) return false;

    $custom_days = false;
    if(isset($event->data->meta['mec_repeat_type']) and $event->data->meta['mec_repeat_type'] === 'custom_days') $custom_days = true;

    if(isset($date['start']['hour']) and isset($date['start']['minutes']) and isset($date['start']['ampm']))
    {
        $s_hour = $date['start']['hour'];
        if(strtoupper($date['start']['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

        $start_date .= ' '.sprintf("%02d", $s_hour).':'.sprintf("%02d", $date['start']['minutes']).' '.$date['start']['ampm'];
    }

    $next = $event;

    // Occurrences
    $found = false;

    foreach($event->dates as $occ)
    {
        $start_datetime = $occ['start']['date'];
        if($custom_days)
        {
            $s_hour = $occ['start']['hour'];
            if(strtoupper($occ['start']['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

            $start_time = sprintf("%02d", $s_hour).':'.sprintf("%02d", $occ['start']['minutes']).' '.$occ['start']['ampm'];
            $start_datetime .= ' '.$start_time;
        }

        if(strtotime($start_datetime) >= strtotime($start_date))
        {
            $found = true;
            $next_date = $occ;
            $next_time = $next->data->time;

            if($custom_days)
            {
                $end_datetime = $occ['end']['date'];
                $e_hour = $occ['end']['hour'];
                if(strtoupper($occ['end']['ampm']) == 'AM' and $e_hour == '0') $e_hour = 12;

                $end_time = sprintf("%02d", $e_hour).':'.sprintf("%02d", $occ['end']['minutes']).' '.$occ['end']['ampm'];
                $end_datetime .= ' '.$end_time;

                $next_time = array(
                    'start' => $this->get_time(strtotime($start_datetime)),
                    'end' => $this->get_time(strtotime($end_datetime)),
                    'start_raw' => $start_time,
                    'end_raw' => $end_time,
                );
            }

            break;
        }
    }

    // Nothing Found!
    if(!$found) return false;
}

$time_comment = isset($next->data->meta['mec_comment']) ? $next->data->meta['mec_comment'] : '';
$allday = isset($next->data->meta['mec_allday']) ? $next->data->meta['mec_allday'] : 0;

$midnight_event = $this->is_midnight_event($next);
if($midnight_event) $next_date['end']['date'] = date('Y-m-d', strtotime('-1 Day', strtotime($next_date['end']['date'])));
?>
<div class="mec-next-event-details mec-frontbox" id="mec_next_event_details">
    <div class="mec-next-<?php echo $method; ?>">
        <h3 class="mec-frontbox-title"><?php echo ($method == 'occurrence' ? esc_html__('Next Occurrence', 'modern-events-calendar-lite') : esc_html__('Next Event', 'modern-events-calendar-lite')); ?></h3>
        <ul>
            <li>
                <a href="<?php echo $this->get_event_date_permalink($next, $next_date['start']['date'], true, $next_time); ?>"><?php echo ($method == 'occurrence' ? __('Go to occurrence page', 'modern-events-calendar-lite') : $next->data->title); ?></a>
            </li>
            <li>
                <i class="mec-sl-calendar"></i>
                <h6><?php _e('Date', 'modern-events-calendar-lite'); ?></h6>
                <dl><dd><abbr class="mec-events-abbr"><?php echo $this->date_label($next_date['start'], (isset($next_date['end']) ? $next_date['end'] : NULL), $date_format1); ?></abbr></dd></dl>
            </li>
            <?php if(isset($next->data->time) and trim($next->data->time['start'])): ?>
            <li>
                <i class="mec-sl-clock"></i>
                <h6><?php _e('Time', 'modern-events-calendar-lite'); ?></h6>
                <i class="mec-time-comment"><?php echo (isset($time_comment) ? $time_comment : ''); ?></i>
                <dl>
                <?php if($allday == '0' and isset($next->data->time) and trim($next->data->time['start'])): ?>
                <dd><abbr class="mec-events-abbr"><?php echo $next_time['start']; ?><?php echo (trim($next_time['end']) ? ' - '.$next_time['end'] : ''); ?></abbr></dd>
                <?php else: ?>
                <dd><abbr class="mec-events-abbr"><?php echo $this->m('all_day', __('All Day' , 'modern-events-calendar-lite')); ?></abbr></dd>
                <?php endif; ?>
                </dl>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>