<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_timetable $this */

// Get layout path
$render_path = $this->get_render_path();

// before/after Month
$_1month_before = strtotime('-1 Month', strtotime($this->start_date));
$_1month_after = strtotime('+1 Month', strtotime($this->start_date));

// Current month time
$current_month_time = strtotime($this->start_date);

// Weeks
$weeks = '';
foreach($this->weeks as $week_number=>$week)
{
    $first_week_day = $week[0];
    
    $i = 1;
    while(strtotime($first_week_day) < $current_month_time)
    {
        if($i > 7)
        {
            $first_week_day = $this->weeks[$week_number+1][0];
        }
        else
        {
            $first_week_day = (isset($week[$i]) ? $week[$i] : NULL);
            $i++;
        }
    }
    
    $weeks .= '<dl class="mec-weekly-view-week '.((isset($this->week_of_days[$this->active_date]) and $week_number == $this->week_of_days[$this->active_date]) ? 'mec-weekly-view-week-active' : '').'" id="mec_weekly_view_week_'.$this->id.'_'.date('Ym', strtotime($first_week_day)).$week_number.'" data-week-id="'.date('Ym', strtotime($first_week_day)).$week_number.'" data-week-number="' . $week_number . '" data-max-weeks="'.count($this->weeks).'">';
    foreach($week as $day)
    {
        $time = strtotime($day);
        $count = isset($this->events[$day]) ? count($this->events[$day]) : 0;
        $weeks .= '<dt data-date-id="'.date('Ymd', $time).'" data-events-count="'.$count.'" class="'.(!$count ? 'mec-timetable-has-no-event ' : '').(($day == $this->active_date) ? 'mec-timetable-day-active' : '').'">'
                .'<span>'.$this->main->date_i18n('D', $time).'</span> '
                .$this->main->date_i18n('j', $time)
                .'</dt>';
    }
    
    $weeks .= '</dl>';
}

// Generate Events
ob_start();
include $render_path;
$date_events = ob_get_clean();

$navigator_html = '';

// Generate Month Navigator
if($this->next_previous_button)
{
    // Show previous month handler if showing past events allowed
    if(!isset($this->atts['show_past_events']) or 
       (isset($this->atts['show_past_events']) and $this->atts['show_past_events']) or
       (isset($this->atts['show_past_events']) and !$this->atts['show_past_events'] and strtotime(date('Y-m-t', $_1month_before)) >= time())
    )
    {
        $navigator_html .= '<div class="mec-previous-month mec-load-month mec-color" data-mec-year="'.date('Y', $_1month_before).'" data-mec-month="'.date('m', $_1month_before).'"><a href="#" class="mec-load-month-link"><i class="mec-sl-angle-left"></i></a></div>';
    }
    
    $navigator_html .= '<h4 class="mec-month-label">'.$this->main->date_i18n('Y F', $current_month_time).'</h4>';
    
    // Show next month handler if needed
    if(!$this->show_only_expired_events or
       ($this->show_only_expired_events and strtotime(date('Y-m-01', $_1month_after)) <= time())
    )
    {
        $navigator_html .= '<div class="mec-next-month mec-load-month mec-color" data-mec-year="'.date('Y', $_1month_after).'" data-mec-month="'.date('m', $_1month_after).'"><a href="#" class="mec-load-month-link"><i class="mec-sl-angle-right"></i></a></div>';
    }
}

$week_html = '<div class="mec-calendar-d-top"><div class="mec-previous-month mec-load-week mec-color" href="#"><i class="mec-sl-angle-left"></i></div><div class="mec-next-month mec-load-week mec-color" href="#"><i class="mec-sl-angle-right"></i></div><h3 class="mec-current-week">'.sprintf(__('Week %s', 'modern-events-calendar-lite'), '<span>'.(isset($this->week_of_days[$this->today]) ? $this->week_of_days[$this->today] : 1).'</span>').'</h3></div>';

$month_html = '<div class="mec-weeks-container mec-calendar-d-table">'.$weeks.'</div>
<div class="mec-week-events-container">'.$date_events.'</div>';

// Return the data if called by AJAX
if(isset($this->atts['return_items']) and $this->atts['return_items'])
{
    echo json_encode(array(
        'month'=>$week_html.$month_html,
        'navigator'=>$navigator_html,
        'week_id'=>date('Ym', $current_month_time).$this->week_of_days[$this->today],
        'active_day'=>date('Ymd', strtotime($this->active_date)),
        'previous_month'=>array('label'=>$this->main->date_i18n('Y F', $_1month_before), 'id'=>date('Ym', $_1month_before), 'year'=>date('Y', $_1month_before), 'month'=>date('m', $_1month_before)),
        'current_month'=>array('label'=>$this->main->date_i18n('Y F', $current_month_time), 'id'=>date('Ym', $current_month_time), 'year'=>date('Y', $current_month_time), 'month'=>date('m', $current_month_time)),
        'next_month'=>array('label'=>$this->main->date_i18n('Y F', $_1month_after), 'id'=>date('Ym', $_1month_after), 'year'=>date('Y', $_1month_after), 'month'=>date('m', $_1month_after)),
    ));
    exit;
}

$sed_method = $this->sed_method;
if($sed_method == 'new') $sed_method = '0';

// Generating javascript code tpl
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#mec_timetable_month_'.$this->id.'_'.date('Ym', $current_month_time).'").mecTimeTable(
    {
        id: "'.$this->id.'",
        today: "'.date('Ymd', strtotime($this->start_date)).'",
        week: "'.$this->week_of_days[$this->active_date].'",
        month_id: "'.date('Ym', $current_month_time).'",
        active_day: "'.date('Ymd', strtotime($this->active_date)).'",
        month_navigator: '.($this->next_previous_button ? 1 : 0).',
        changeWeekElement: "#mec_skin_'.$this->id.' .mec-load-week",
        atts: "'.http_build_query(array('atts'=>$this->atts), '', '&').'",
        ajax_url: "'.admin_url('admin-ajax.php', NULL).'",
        sed_method: "'.$sed_method.'",
        image_popup: "'.$this->image_popup.'",
        sf:
        {
            container: "'.($this->sf_status ? '#mec_search_form_'.$this->id : '').'",
            reset: '.($this->sf_reset_button ? 1 : 0).',
            refine: '.($this->sf_refine ? 1 : 0).',
        },
    });
});
</script>';

// Include javascript code into the page
if($this->main->is_ajax() or $this->main->preview()) echo $javascript;
else $this->factory->params('footer', $javascript);

$styling = $this->main->get_styling();
$event_colorskin = (isset($styling['mec_colorskin'] ) || isset($styling['color'])) ? 'colorskin-custom' : '';
?>
<div id="mec_skin_<?php echo $this->id; ?>" class="mec-timetable-wrap mec-wrap <?php echo $event_colorskin . ' ' . $this->html_class; ?>">

    <?php if($this->sf_status) echo $this->sf_search_form(); ?>
    
    <div class="mec-calendar mec-calendar-daily mec-calendar-weekly mec-calendar-timetable">
        <?php if($this->next_previous_button): ?>
        <div class="mec-skin-weekly-view-month-navigator-container mec-calendar-a-month mec-clear">
            <div class="mec-month-navigator" id="mec_month_navigator<?php echo $this->id; ?>_<?php echo date('Ym', $current_month_time); ?>"><?php echo $navigator_html; ?></div>
        </div>
        <?php else: ?>
        <div class="mec-calendar-a-month mec-clear">
            <h4 class="mec-month-label"><?php echo $this->main->date_i18n('Y F', $current_month_time); ?></h4>
        </div>
        <?php endif; ?>

        <div class="mec-skin-weekly-view-events-container" id="mec_skin_events_<?php echo $this->id; ?>">
            <div class="mec-month-container" id="mec_timetable_month_<?php echo $this->id; ?>_<?php echo date('Ym', $current_month_time); ?>">
                <?php echo $week_html . $month_html; ?>
            </div>
        </div>
    </div>
    
</div>