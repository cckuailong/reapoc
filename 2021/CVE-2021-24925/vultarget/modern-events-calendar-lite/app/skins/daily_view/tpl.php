<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_daily_view $this */

// Get layout path
$render_path = $this->get_render_path();

// before/after Month
$_1month_before = strtotime('-1 Month', strtotime($this->start_date));
$_1month_after = strtotime('+1 Month', strtotime($this->start_date));

// Current month time
$current_month_time = strtotime($this->start_date);

$date_labels = $this->get_date_labels();

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
        $navigator_html .= '<div class="mec-previous-month mec-color mec-load-month" data-mec-year="'.date('Y', $_1month_before).'" data-mec-month="'.date('m', $_1month_before).'"><a href="#" class="mec-load-month-link"><i class="mec-sl-angle-left"></i></a></div>';
    }
    
    $navigator_html .= '<h4>'.$this->main->date_i18n('Y F', $current_month_time).'</h4>';
    
    // Show next month handler if needed
    if(!$this->show_only_expired_events or
       ($this->show_only_expired_events and strtotime(date('Y-m-01', $_1month_after)) <= time())
    )
    {
        $navigator_html .= '<div class="mec-color mec-next-month mec-load-month" data-mec-year="'.date('Y', $_1month_after).'" data-mec-month="'.date('m', $_1month_after).'"><a href="#" class="mec-load-month-link"><i class="mec-sl-angle-right"></i></a></div>';
    }
}

$month_html = '<div class="mec-today-container mec-calendar-d-top" id="mec_today_container'.$this->id.'_'.date('Ym', $current_month_time).'"></div>
<div class="mec-date-labels-container mec-calendar-d-table"><a href="#" class="mec-table-d-prev mec-color"><i class="mec-sl-angle-left"></i></a><a href="#" class="mec-table-d-next mec-color"><i class="mec-sl-angle-right"></i></a>'.$date_labels.'</div>
<div class="mec-date-labels-container mec-calendar-day-events mec-clear">'.$date_events.'</div>';

// Return the data if called by AJAX
if(isset($this->atts['return_items']) and $this->atts['return_items'])
{
    echo json_encode(array(
        'month'=>$month_html,
        'navigator'=>$navigator_html,
        'previous_month'=>array('label'=>$this->main->date_i18n('Y F', $_1month_before), 'id'=>date('Ym', $_1month_before), 'year'=>date('Y', $_1month_before), 'month'=>date('m', $_1month_before)),
        'current_month'=>array('label'=>$this->main->date_i18n('Y F', $current_month_time), 'id'=>date('Ym', $current_month_time), 'year'=>date('Y', $current_month_time), 'month'=>date('m', $current_month_time)),
        'next_month'=>array('label'=>$this->main->date_i18n('Y F', $_1month_after), 'id'=>date('Ym', $_1month_after), 'year'=>date('Y', $_1month_after), 'month'=>date('m', $_1month_after)),
    ));
    exit;
}

// Inclue OWL Assets
$this->main->load_owl_assets();

$sed_method = $this->sed_method;
if($sed_method == 'new') $sed_method = '0';

// Generating javascript code tpl
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#mec_daily_view_month_'.$this->id.'_'.date('Ym', $current_month_time).'").mecDailyView(
    {
        id: "'.$this->id.'",
        today: "'.date('Ymd', strtotime($this->active_day)).'",
        month_id: "'.date('Ym', $current_month_time).'",
        year: "'.date('Y', $current_month_time).'",
        month: "'.date('m', $current_month_time).'",
        day: "'.date('d', strtotime($this->active_day)).'",
        events_label: "'.esc_attr__('Events', 'modern-events-calendar-lite').'",
        event_label: "'.esc_attr__('Event', 'modern-events-calendar-lite').'",
        month_navigator: '.($this->next_previous_button ? 1 : 0).',
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
$event_colorskin = (isset($styling['mec_colorskin']) || isset($styling['color'])) ? 'colorskin-custom' : '';

$dark_mode = (isset($styling['dark_mode']) ? $styling['dark_mode'] : '');
if($dark_mode == 1) $set_dark = 'mec-dark-mode';
else $set_dark = '';

do_action('mec_start_skin', $this->id);
do_action('mec_daily_skin_head');
?>
<div id="mec_skin_<?php echo $this->id; ?>" class="mec-wrap <?php echo $event_colorskin . ' ' . $this->html_class . ' ' . $set_dark; ?>">
    
    <?php if($this->sf_status) echo $this->sf_search_form(); ?>
    
    <div class="mec-calendar mec-calendar-daily">
        <?php if($this->next_previous_button): ?>
        <div class="mec-skin-daily-view-month-navigator-container mec-calendar-a-month mec-clear">
            <div class="mec-month-navigator" id="mec_month_navigator<?php echo $this->id; ?>_<?php echo date('Ym', $current_month_time); ?>"><?php echo $navigator_html; ?></div>
        </div>
        <?php else: ?>
        <div class="mec-calendar-a-month mec-clear"><h4 class="mec-month-label"><?php echo $this->main->date_i18n('Y F', $current_month_time); ?></h4></div>
        <?php endif; ?>

        <div class="mec-skin-daily-view-events-container" id="mec_skin_events_<?php echo $this->id; ?>">
            <div class="mec-month-container mec-calendar-a-day mec-clear" id="mec_daily_view_month_<?php echo $this->id; ?>_<?php echo date('Ym', $current_month_time); ?>">
                <?php echo $month_html; ?>
            </div>
        </div>
    </div>
    
</div>