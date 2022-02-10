<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_monthly_view $this */

// table headings
$headings = $this->main->get_weekday_abbr_labels();
echo '<dl class="mec-calendar-table-head"><dt class="mec-calendar-day-head">'.implode('</dt><dt class="mec-calendar-day-head">', $headings).'</dt></dl>';

// Start day of week
$week_start = $this->main->get_first_day_of_week();

$display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
$reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;

// Single Event Display Method
$target_set = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : false;
$target_url = ($target_set == 'new') ? 'target="_blank"' : '';

// days and weeks vars
$running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
$days_in_previous_month = date('t', strtotime('-1 month', strtotime($this->active_day)));

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
            $selected_day = (str_replace('-', '', $this->active_day) == $day_id) ? ' mec-selected-day' : '';
            $selected_day_date = (str_replace('-', '', $this->active_day) == $day_id) ? ' mec-bg-color' : '';

            // Print events
            if(isset($events[$today]) and count($events[$today]))
            {
                echo '<dt class="mec-calendar-day'.$selected_day.'" data-mec-cell="'.$day_id.'" data-day="'.$list_day.'" data-month="'.date('Ym', $time).'"><div class="mec-calendar-novel-selected-day'.$selected_day_date.'">'.$list_day.'</div>';
                foreach($events[$today] as $event)
                {
                    $event_color = isset($event->data->meta['mec_color'])? '#'.$event->data->meta['mec_color']:'';
                    $start_date = (isset($event->date['start']['date']) ? str_replace ( '-', '', $event->date['start']['date'] ) : '');
                    $end_date = (isset($event->date['end']['date']) ? str_replace ( '-', '', $event->date['end']['date'] ) : '');

                    // MEC Schema
                    do_action('mec_schema', $event);

                    echo '<a class="'.((isset($event->data->meta['event_past']) and trim($event->data->meta['event_past'])) ? 'mec-past-event ' : '').'event-single-link-novel" data-event-id="'.$event->data->ID.'" href="'.$this->main->get_event_date_permalink($event, $event->date['start']['date']).'" '.$target_url.'>';

                    echo '<div style="background:'.$event_color.'" class="mec-single-event-novel mec-event-article '.$this->get_event_classes($event).'">';
                    echo '<h4 class="mec-event-title">'.$event->data->title.'</h4>'.$this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation);
                    echo $this->get_label_captions($event);

                    do_action('mec_shortcode_virtual_badge', $event->data->ID);

                    echo $this->display_cost($event);
                    echo $this->display_custom_data($event);

                    echo '</div>';
                    echo '</a>';
                }

                echo '</dt>';
            }
            else
            {
                echo '<dt class="mec-calendar-day'.$selected_day.'" data-mec-cell="'.$day_id.'" data-day="'.$list_day.'" data-month="'.date('Ym', $time).'">'.$list_day.'</dt>';
                echo '</dt>';
            }

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