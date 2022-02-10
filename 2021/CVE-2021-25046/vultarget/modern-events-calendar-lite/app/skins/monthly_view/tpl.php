<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_monthly_view $this */

// Get layout path
$render_path = $this->get_render_path();

// before/after Month
$_1month_before = strtotime('first day of -1 month', strtotime($this->start_date));
$_1month_after = strtotime('first day of +1 month', strtotime($this->start_date));

// Current month time
$current_month_time = strtotime($this->start_date);

// Generate Month
ob_start();
include $render_path;
$month_html = ob_get_clean();

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
        $navigator_html .= '<div class="mec-previous-month mec-load-month mec-previous-month" data-mec-year="'.date('Y', $_1month_before).'" data-mec-month="'.date('m', $_1month_before).'"><a href="#" class="mec-load-month-link"><i class="mec-sl-angle-left"></i> '.$this->main->date_i18n('F', $_1month_before).'</a></div>';
    }
    
    $navigator_html .= '<div class="mec-calendar-header"><h2>'.$this->main->date_i18n('F Y', $current_month_time).'</h2></div>';
    
    // Show next month handler if needed
    if(!$this->show_only_expired_events or
       ($this->show_only_expired_events and strtotime(date('Y-m-01', $_1month_after)) <= time())
    )
    {
        $navigator_html .= '<div class="mec-next-month mec-load-month mec-next-month" data-mec-year="'.date('Y', $_1month_after).'" data-mec-month="'.date('m', $_1month_after).'"><a href="#" class="mec-load-month-link">'.$this->main->date_i18n('F', $_1month_after).' <i class="mec-sl-angle-right"></i></a></div>';
    }
}

// Return the data if called by AJAX
if(isset($this->atts['return_items']) and $this->atts['return_items'])
{
    echo json_encode(array(
        'month'=>$month_html,
        'events_side'=>(in_array($this->style, array('clean', 'modern')) ? $this->events_str : ''),
        'navigator'=>$navigator_html,
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
    jQuery("#mec_monthly_view_month_'.$this->id.'_'.date('Ym', $current_month_time).'").mecMonthlyView(
    {
        id: "'.$this->id.'",
        today: "'.date('Ymd', strtotime($this->active_day)).'",
        display_all: "'.($this->display_all ? 1 : 0).'",
        month_id: "'.date('Ym', $current_month_time).'",
        active_month: {year: "'.date('Y', strtotime($this->start_date)).'", month: "'.date('m', strtotime($this->start_date)).'"},
        next_month: {year: "'.date('Y', $_1month_after).'", month: "'.date('m', $_1month_after).'"},
        events_label: "'.esc_attr__('Events', 'modern-events-calendar-lite').'",
        event_label: "'.esc_attr__('Event', 'modern-events-calendar-lite').'",
        month_navigator: '.($this->next_previous_button ? 1 : 0).',
        atts: "'.http_build_query(array('atts'=>$this->atts), '', '&').'",
        style: "'.(isset($this->skin_options['style']) ? $this->skin_options['style'] : NULL).'",
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

$dark_mode = (isset($styling['dark_mode']) ? $styling['dark_mode'] : '');
if($dark_mode == 1) $set_dark = 'mec-dark-mode';
else $set_dark = '';

if($this->style == 'classic' || $this->style == 'novel' || $this->style == 'simple' )
{
    $cal_style        = 'mec-box-calendar mec-event-calendar-classic mec-event-container-'.$this->style;
    $div_start_topsec = $div_end_topsec = $events_side = $div_footer = '';
}
else
{
    $cal_style        = $this->style == 'modern' ? 'mec-box-calendar' : '';
    $div_start_topsec = '<div class="mec-calendar-topsec">';
    $div_end_topsec   = '</div>';
    $events_side      = '<div class="mec-calendar-events-side mec-clear"><div class="mec-month-side" id="mec_month_side_'.$this->id.'_'.date('Ym', $current_month_time).'">'.$this->events_str.'</div></div>';
    $div_footer       = '<div class="mec-event-footer"></div>';
}

do_action('mec_start_skin', $this->id);
do_action('mec_monthly_skin_head');
?>
<div id="mec_skin_<?php echo $this->id; ?>" class="mec-wrap <?php echo $event_colorskin . ' ' . $this->html_class . ' ' . $set_dark; ?>">
    
    <?php if($this->sf_status) echo $this->sf_search_form(); ?>
    
    <div class="mec-calendar <?php echo $cal_style; ?>">
        <?php echo $div_start_topsec; ?>
        <div class="mec-calendar-side mec-clear">
            <?php if($this->next_previous_button): ?>
            <div class="mec-skin-monthly-view-month-navigator-container">
                <div class="mec-month-navigator" id="mec_month_navigator_<?php echo $this->id; ?>_<?php echo date('Ym', $current_month_time); ?>"><?php echo $navigator_html; ?></div>
            </div>
            <?php else: ?>
            <div class="mec-calendar-header"><h2><?php echo $this->main->date_i18n('Y F', $current_month_time); ?></h2></div>
            <?php endif; ?>

            <div class="mec-calendar-table" id="mec_skin_events_<?php echo $this->id; ?>">
                <div class="mec-month-container mec-month-container-selected" id="mec_monthly_view_month_<?php echo $this->id; ?>_<?php echo date('Ym', $current_month_time); ?>" data-month-id="<?php echo date('Ym', $current_month_time); ?>"><?php echo $month_html; ?></div>
            </div>
        </div>
        <?php echo $events_side . $div_end_topsec . $div_footer; ?>
    </div>
    
</div>