<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_bookingcalendar $this */

// Available Dates
$dates = isset($event->dates) ? $event->dates : array($event->date);

// Multiple Day Event
$multiple_date = (isset($dates) && $dates[0]['start']['date'] != $dates[0]['end']['date']) ? 'mec-multiple-event' : '';

$first_date = (isset($start) ? $start : (isset($dates[0]) ? $dates[0]['start']['date'] : NULL));
if(!$first_date) return;

// Settings
$settings = $this->main->get_settings();

// Is Booking Enabled for Ongoing Events
$booking_ongoing = (isset($settings['booking_ongoing']) and $settings['booking_ongoing']);

// Options
$event_color = isset($event->data->meta['mec_color']) ? '#'.$event->data->meta['mec_color'] : '';
$allday = isset($event->data->meta['mec_allday']) ? $event->data->meta['mec_allday'] : 0;
$date_format = (isset($settings['booking_date_format1']) and trim($settings['booking_date_format1'])) ? $settings['booking_date_format1'] : 'Y-m-d';
$date_format = trim(str_replace(['H', 'h', 'i', 's', 'A', 'a', 'G', 'g', 'B', 'u', 'v', ':'], '', $date_format), ': ');
$time_format = get_option('time_format');

// before/after Month
$_1month_before = strtotime('first day of -1 month', strtotime($first_date));
$_1month_after = strtotime('first day of +1 month', strtotime($first_date));
$current_month_time = strtotime($first_date);

$year = date('Y', strtotime($first_date));
$month = date('m', strtotime($first_date));
$active_day = date('d', strtotime($first_date));

// Start day of week
$week_start = $this->main->get_first_day_of_week();

// days and weeks vars
$running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
$days_in_previous_month = date('t', strtotime('-1 month', strtotime($active_day)));

$days_in_this_week = 1;
$day_counter = 0;

if($week_start == 0) $running_day = $running_day; // Sunday
elseif($week_start == 1) // Monday
{
    if($running_day != 0) $running_day = $running_day - 1;
    else $running_day = 6;
}
elseif($week_start == 6) // Saturday
{
    if($running_day != 6) $running_day = $running_day + 1;
    else $running_day = 0;
}
elseif($week_start == 5) // Friday
{
    if($running_day < 4) $running_day = $running_day + 2;
    elseif($running_day == 5) $running_day = 0;
    elseif($running_day == 6) $running_day = 1;
}

$navigator_html = '';

// Show previous navigation
if(strtotime(date('Y-m-t', $_1month_before)) >= time())
{
    $navigator_html .= '<div class="mec-previous-month mec-load-month mec-previous-month" data-mec-year="'.date('Y', $_1month_before).'" data-mec-month="'.date('m', $_1month_before).'"><a href="#" class="mec-load-month-link"><i class="mec-sl-angle-left"></i> '.$this->main->date_i18n('F', $_1month_before).'</a></div>';
}

$navigator_html .= '<div class="mec-calendar-header"><h2>'.$this->main->date_i18n('F Y', $current_month_time).'</h2></div>';

// Show next navigation
if(strtotime(date('Y-m-01', $_1month_after)) >= time())
{
    $navigator_html .= '<div class="mec-next-month mec-load-month mec-next-month" data-mec-year="'.date('Y', $_1month_after).'" data-mec-month="'.date('m', $_1month_after).'"><a href="#" class="mec-load-month-link">'.$this->main->date_i18n('F', $_1month_after).' <i class="mec-sl-angle-right"></i></a></div>';
}

// Generating javascript code tpl
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#mec_booking_calendar_'.$uniqueid.'").mecBookingCalendar(
    {
        active_month: {year: "'.date('Y', $current_month_time).'", month: "'.date('m', $current_month_time).'"},
        next_month: {year: "'.date('Y', $_1month_after).'", month: "'.date('m', $_1month_after).'"},
        ajax_url: "'.admin_url('admin-ajax.php', NULL).'",
        id: "'.$uniqueid.'",
        event_id: "'.$event->ID.'",
    });
});
</script>';

// Include javascript code into the page
echo $javascript;
?>
<div class="mec-booking-calendar mec-wrap" id="mec_booking_calendar_<?php echo $uniqueid; ?>">
    <div class="mec-booking-calendar-month-navigation"><?php echo $navigator_html; ?></div>
    <div class="mec-calendar mec-box-calendar mec-event-calendar-classic mec-event-container-novel <?php echo $multiple_date; ?>">
        <?php
            // Table Headings
            $headings = $this->main->get_weekday_abbr_labels();
            echo '<dl class="mec-calendar-table-head"><dt class="mec-calendar-day-head">'.implode('</dt><dt class="mec-calendar-day-head">', $headings).'</dt></dl>';
        ?>
        <dl class="mec-calendar-row">
            <?php
                // print "blank" days until the first of the current week
                for($x = 0; $x < $running_day; $x++)
                {
                    echo '<dt class="mec-table-nullday">'.($days_in_previous_month - ($running_day-1-$x)).'</dt>';
                    $days_in_this_week++;
                }

                // keep going with days ....
                for($list_day = 1; $list_day <= $days_in_month; $list_day++)
                {
                    $time = strtotime($year.'-'.$month.'-'.$list_day);
                    $today = date('Y-m-d', $time);
                    $day_id = date('Ymd', $time);

                    $render = '';
                    $first_day  = '';
                    $middle_day = '';
                    $last_day = '';
                    $repeat = 0;

                    foreach($dates as $date)
                    {
                        if(!isset($date['fake']) and strtotime($date['start']['date']) <= $time and $time <= strtotime($date['end']['date']) and ($booking_ongoing or (isset($date['start']['timestamp']) and $date['start']['timestamp'] >= current_time('timestamp', 0))))
                        {
                            $repeat++;
                            $date_timestamp = $this->book->timestamp($date['start'], $date['end']);
                            $start_datetime = $date['start']['date'].' '.sprintf("%02d", $date['start']['hour']).':'.sprintf("%02d", $date['start']['minutes']).' '.$date['start']['ampm'];

                            $render .='<div class="mec-booking-calendar-date '.($this->main->is_soldout($event->ID, $start_datetime) ?'mec-booking-calendar-date-soldout' : '').' " data-timestamp="'.$this->book->timestamp($date['start'], $date['end']).'">' .(($date['start']['date'] !== $date['end']['date']) ? '<div class="mec-booking-calendar-date-hover">'.strip_tags($this->main->date_label($date['start'], $date['end'], $date_format, ' - ', false, (isset($date['allday']) ? $date['allday'] : 0), $event)).'</div><div class="mec-booking-calendar-time-hover">' : ($allday != 0 ? esc_html__('All Day' , 'modern-events-calendar-lite') : '')).strip_tags($this->main->date_label($date['start'], $date['end'], $time_format, ' - ', false, (isset($date['allday']) ? $date['allday'] : 0))).(($date['start']['date'] !== $date['end']['date']) ?'</div>' : '') .'</div>';
                            $first_day = strtotime($date['start']['date']) == $time ? ' first-day' : null;
                            $middle_day = (strtotime($date['end']['date']) != $time && strtotime($date['start']['date']) != $time) ? ' middle-day' : null;
                            $last_day = strtotime($date['end']['date']) == $time ? ' last-day' : null;
                        }
                    }

                    $repeat_class = $repeat > 1 ? ' mec-has-time-repeat' : '';
                    $date_for_wrap = $repeat == 1 ? 'data-timestamp="'.$date_timestamp.'"' : '';
                    $custom_class1 = $repeat == 1 ? ' mec-has-one-repeat-in-day' : '';
                    $custom_class2 = $repeat >= 1 ? ' mec-has-event-for-booking' : '';

                    echo '<dt class="mec-calendar-day'.$repeat_class.$custom_class1.$custom_class2.$first_day.$last_day.$middle_day.'" '.$date_for_wrap.'><div class="mec-calendar-novel-selected-day"><span>'.$list_day.'</span></div>';
                    echo '<div class="mec-booking-tooltip">'.$render.'</div>';
                    echo '</dt>';

                    if($running_day == 6)
                    {
                        echo '</dl>';

                        if((($day_counter+1) != $days_in_month) or (($day_counter+1) == $days_in_month and $days_in_this_week == 7))
                        {
                            echo '<dl class="mec-calendar-row">';
                        }

                        $running_day = -1;
                        $days_in_this_week = 0;
                    }

                    $days_in_this_week++; $running_day++; $day_counter++;
                }

                // finish the rest of the days in the week
                if($days_in_this_week < 8)
                {
                    for($x = 1; $x <= (8 - $days_in_this_week); $x++)
                    {
                        echo '<dt class="mec-table-nullday">'.$x.'</dt>';
                    }
                }
            ?>
        </dl>
    </div>
</div>
<div class="mec-choosen-time-message disable"><?php echo __('Chosen Time:', 'modern-events-calendar-lite'); ?> <span class="mec-choosen-time"></span></div>