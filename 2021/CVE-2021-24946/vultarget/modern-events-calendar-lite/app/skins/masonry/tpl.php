<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_masonry $this */

// Get layout path
$render_path = $this->get_render_path();

ob_start();
include $render_path;
$items_html = ob_get_clean();

if(isset($this->atts['return_items']) and $this->atts['return_items'])
{
    echo json_encode(array('html'=>$items_html, 'end_date'=>$this->end_date, 'offset'=>($this->next_offset +1), 'count'=>$this->found, 'current_month_divider'=>$current_month_divider, 'has_more_event' => (int) $this->has_more_events));
    exit;
}

$sed_method = $this->sed_method;
if($sed_method == 'new') $sed_method = '0';

// Inclue Isotope Assets
$this->main->load_isotope_assets();

// Generating javascript code tpl
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#mec_skin_'.$this->id.'").mecMasonryView(
    {
        id: "'.$this->id.'",
        start_date: "'.$this->start_date.'",
        end_date: "'.$this->end_date.'",
		offset: "'.$this->next_offset.'",
        atts: "'.http_build_query(array('atts'=>$this->atts), '', '&').'",
        ajax_url: "'.admin_url('admin-ajax.php', NULL).'",
        sed_method: "'.$sed_method.'",
        image_popup: "'.$this->image_popup.'",
        masonry_like_grid: "'.$this->masonry_like_grid.'",
        fit_to_row: "'.$this->fit_to_row.'",
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
$event_colorskin = (isset($styling['mec_colorskin']) || isset($styling['color'])) ? ' colorskin-custom ' : '';

$dark_mode = (isset($styling['dark_mode']) ? $styling['dark_mode'] : '');
if($dark_mode == 1) $set_dark = 'mec-dark-mode';
else $set_dark = '';

do_action('mec_start_skin', $this->id);
do_action('mec_masonry_skin_head');
?>
<div class="mec-wrap mec-skin-masonry-container<?php echo $event_colorskin; ?><?php echo $this->html_class . ' ' . $set_dark; ?>" id="mec_skin_<?php echo $this->id; ?>" data-filterby="<?php echo trim($this->filter_by) ? trim($this->filter_by) : ''; ?>" data-sortascending="<?php echo (isset($this->show_only_expired_events) and $this->show_only_expired_events) ? false : true; ?>">
    <?php if(trim($this->filter_by)) echo $this->filter_by(); ?>

    <?php if($this->found): ?>
    <div class="mec-events-masonry-wrap" id="mec_skin_events_<?php echo $this->id; ?>">
        <?php echo $items_html; ?>
    </div>
    <div class="mec-skin-masonry-no-events-container mec-util-hidden" id="mec_skin_no_events_<?php echo $this->id; ?>">
        <?php _e('No event found!', 'modern-events-calendar-lite'); ?>
    </div>
    <?php else: ?>
    <div class="mec-skin-masonry-events-container" id="mec_skin_events_<?php echo $this->id; ?>">
        <?php _e('No event found!', 'modern-events-calendar-lite'); ?>
    </div>
    <?php endif; ?>
    
    <?php if($this->load_more_button and $this->found >= $this->limit): ?>
    <div class="mec-load-more-wrap"><div class="mec-load-more-button <?php echo ($this->has_more_events ? '' : 'mec-util-hidden'); ?>"><?php echo __('Load More', 'modern-events-calendar-lite'); ?></div></div>
    <?php endif; ?>
</div>
<?php do_action('mec_masonry_customization'); ?>