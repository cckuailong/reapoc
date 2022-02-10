<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_tile $this */

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

if(isset($this->atts['return_only_items']) and $this->atts['return_only_items'])
{
    echo json_encode(array('html'=>$month_html, 'end_date'=>$this->end_date, 'offset'=>$this->next_offset, 'count'=>$this->found, 'has_more_event' => (int) $this->has_more_events));
    exit;
}

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
    jQuery("#mec_tile_month_'.$this->id.'_'.date('Ym', $current_month_time).'").mecTileView(
    {
        id: "'.$this->id.'",
        start_date: "'.$this->start_date.'",
        end_date: "'.$this->end_date.'",
		offset: "'.$this->next_offset.'",
		limit: "'.$this->limit.'",
		load_method: "'.$this->load_method.'",
        today: "'.date('Ymd', strtotime($this->active_day)).'",
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
        }
    });
});
</script>';

// Include javascript code into the page
if($this->main->is_ajax() or $this->main->preview()) echo $javascript;
else $this->factory->params('footer', $javascript);

$styling = $this->main->get_styling();

$event_colorskin = (isset($styling['mec_colorskin'] ) || isset($styling['color'])) ? 'colorskin-custom' : '';
$dark_mode = (isset($styling['dark_mode'])) ? $styling['dark_mode'] : '';

if($dark_mode == 1) $set_dark = 'mec-dark-mode';
else $set_dark ='';

do_action('mec_start_skin', $this->id);
do_action('mec_tile_head');
?>
<div id="mec_skin_<?php echo $this->id; ?>" class="mec-wrap <?php echo $event_colorskin . ' ' . $this->html_class . ' ' . $set_dark; ?>">
    
    <?php if($this->sf_status) echo $this->sf_search_form(); ?>
    
    <div class="mec-tile">
        <div class="mec-calendar-topsec">
            <div class="mec-clear">
                <?php if($this->next_previous_button): ?>
                <div class="mec-skin-tile-month-navigator-container">
                    <div class="mec-month-navigator" id="mec_month_navigator_<?php echo $this->id; ?>_<?php echo date('Ym', $current_month_time); ?>"><?php echo $navigator_html; ?></div>
                </div>
                <?php endif; ?>

                <div class="mec-calendar-table" id="mec_skin_events_<?php echo $this->id; ?>">
                    <?php if($this->load_method === 'list'): ?>
                        <?php echo $month_html; ?>
                    <?php else: ?>
                        <div class="mec-month-container mec-month-container-selected" id="mec_tile_month_<?php echo $this->id; ?>_<?php echo date('Ym', $current_month_time); ?>" data-month-id="<?php echo date('Ym', $current_month_time); ?>"><?php echo $month_html; ?></div>
                    <?php endif; ?>
                </div>

                <?php if($this->load_method === 'list' and $this->load_more_button and $this->found >= $this->limit): ?>
                <div class="mec-load-more-wrap"><div class="mec-load-more-button <?php echo ($this->has_more_events ? '' : 'mec-util-hidden'); ?>"><?php echo __('Load More', 'modern-events-calendar-lite'); ?></div></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
</div>