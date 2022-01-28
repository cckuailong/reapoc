<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_yearly_view $this */

// table headings
$headings = $this->main->get_weekday_abbr_labels();

// Start day of week
$week_start = $this->main->get_first_day_of_week();

// Get date suffix 
$settings = $this->main->get_settings();

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

$rows = 1;
?>
<div class="mec-calendar mec-yearly-calendar">

    <div class="mec-calendar-table-title">
        <?php echo $this->main->date_i18n('F', strtotime($this->year.'-'.$month.'-01')); ?>
    </div>
    <div class="mec-calendar-table">
        <?php echo '<div class="mec-calendar-table-head"><dl><dt>'.implode('</dt><dt>', $headings).'</dt></dl></div>'; ?>

        <div class="mec-calendar-table-body">
            <dl>
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

                    // Print events
                    if(isset($events[$today]) and count($events[$today]))
                    {
                        echo '<dt class="mec-has-event"><a href="#mec_yearly_view'.$this->id.'_'.date('Ymd', $time).'" class="mec-has-event-a">'.$list_day.'</a></dt>';
                    }
                    else
                    {
                        echo '<dt>'.$list_day.'</dt>';
                    }

                    if($running_day == 6)
                    {
                        echo '</dl>';

                        if((($day_counter+1) != $days_in_month) or (($day_counter+1) == $days_in_month and $days_in_this_week == 7))
                        {
                            echo '<dl>';
                            $rows++;
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

                if($rows == 5)
                {
                    echo '</dl><dl>';
                    for($j = 0; $j <= 6; $j++)
                    {
                        echo '<dt class="mec-table-nullday">'.($x+$j).'</dt>';
                    }
                }
                ?>
            </dl>
        </div>
    </div>
</div>