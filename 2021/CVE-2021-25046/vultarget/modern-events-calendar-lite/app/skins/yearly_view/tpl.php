<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_yearly_view $this */

// Get layout path
$render_path = $this->get_render_path();

// before/after Month
$_1year_before = strtotime('first day of January '.(date('Y', strtotime($this->start_date))-1));
$_1year_after = strtotime('first day of January '.(date('Y', strtotime($this->start_date))+1));

// Current month time
$current_year_time = strtotime($this->start_date);

// Generate Month
ob_start();
include $render_path;
$year_html = ob_get_clean();

$navigator_html = '';

// Generate Month Navigator
if($this->next_previous_button)
{
    // Show previous year handler if showing past events allowed
    if(!isset($this->atts['show_past_events']) or 
       (isset($this->atts['show_past_events']) and $this->atts['show_past_events']) or
       (isset($this->atts['show_past_events']) and !$this->atts['show_past_events'] and date('Y', $_1year_before) >= current_time('Y'))
    )
    {
        $navigator_html .= '<div class="mec-previous-year mec-load-year mec-color-hover" data-mec-year="'.date('Y', $_1year_before).'"><a href="#" class="mec-load-month-link"><i class="mec-sl-angle-left"></i> '.$this->main->date_i18n('Y', $_1year_before).'</a></div>';
    }
    
    $navigator_html .= '<h2>'.$this->main->date_i18n('Y', $current_year_time).'</h2>';
    
    // Show next month handler if needed
    if(!$this->show_only_expired_events or
       ($this->show_only_expired_events and strtotime(date('Y-01-01', $_1year_after)) <= time())
    )
    {
        $navigator_html .= '<div class="mec-next-year mec-load-year mec-color-hover" data-mec-year="'.date('Y', $_1year_after).'"><a href="#" class="mec-load-month-link">'.$this->main->date_i18n('Y', $_1year_after).' <i class="mec-sl-angle-right"></i></a></div>';
    }
}

// Return the data if called by AJAX
if(isset($this->atts['return_items']) and $this->atts['return_items'])
{
    echo json_encode(array(
        'year'=>$year_html,
        'navigator'=>$navigator_html,
        'previous_year'=>array('label'=>$this->main->date_i18n('Y', $_1year_before), 'id'=>date('Y', $_1year_before), 'year'=>date('Y', $_1year_before), 'month'=>date('m', $_1year_before)),
        'current_year'=>array('label'=>$this->main->date_i18n('Y', $current_year_time), 'id'=>date('Y', $current_year_time), 'year'=>date('Y', $current_year_time), 'month'=>date('m', $current_year_time)),
        'next_year'=>array('label'=>$this->main->date_i18n('Y', $_1year_after), 'id'=>date('Y', $_1year_after), 'year'=>date('Y', $_1year_after), 'month'=>date('m', $_1year_after)),
    ));
    exit;
}

$sed_method = $this->sed_method;
if($sed_method == 'new') $sed_method = '0';

// Generating javascript code tpl
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#mec_yearly_view_year_'.$this->id.'_'.date('Y', $current_year_time).'").mecYearlyView(
    {
        id: "'.$this->id.'",
        today: "'.date('Ymd', strtotime($this->active_day)).'",
        year_id: "'.date('Y', $current_year_time).'",
        next_year: {year: "'.date('Y', $_1year_after).'"},
        events_label: "'.esc_attr__('Events', 'modern-events-calendar-lite').'",
        event_label: "'.esc_attr__('Event', 'modern-events-calendar-lite').'",
        year_navigator: '.($this->next_previous_button ? 1 : 0).',
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
do_action('mec_yearly_skin_head');
?>
<div id="mec_skin_<?php echo $this->id; ?>" class="mec-wrap <?php echo $this->html_class . ' ' . $set_dark; ?>">
    
    <?php if($this->sf_status) echo $this->sf_search_form(); ?>
    
    <div class="mec-wrap mec-yearly-view-wrap">

        <?php if($this->next_previous_button): ?>
        <div class="mec-yearly-title-sec">
            <div class="mec-year-navigator" id="mec_year_navigator_<?php echo $this->id; ?>_<?php echo date('Y', $current_year_time); ?>">
                <?php echo $navigator_html; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="mec-yearly-title-sec">
            <div class="mec-year-navigator">
                <h2><?php echo $this->main->date_i18n('Y', $current_year_time); ?></h2>
            </div>
        </div>
        <?php endif; ?>

        <div id="mec_skin_events_<?php echo $this->id; ?>">
            <div class="mec-year-container" id="mec_yearly_view_year_<?php echo $this->id; ?>_<?php echo date('Y', $current_year_time); ?>" data-year-id="<?php echo date('Y', $current_year_time); ?>">
                <?php echo $year_html; ?>
            </div>
        </div>
        <div class="clearfix"></div>

    </div>
    
</div>