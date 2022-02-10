<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_full_calendar $this */

// Inclue OWL Assets
$this->main->load_owl_assets();

$sed_method = $this->sed_method;
if($sed_method == 'new') $sed_method = '0';

// Generating javascript code tpl
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#mec_skin_'.$this->id.'").mecFullCalendar(
    {
        id: "'.$this->id.'",
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
        skin: "'.$this->default_view.'",
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

if($this->sf_display_label) $label_all_set = 'mec-there-labels';
else $label_all_set = '';

do_action('mec_start_skin', $this->id);
do_action('mec_full_skin_head');
?>
<div id="mec_skin_<?php echo $this->id; ?>" class="mec-wrap <?php echo $event_colorskin . ' '. $label_all_set . ' ' . $set_dark; ?> mec-full-calendar-wrap">

    <div class="mec-search-form mec-totalcal-box">
        <?php if($this->sf_status): ?>
            <?php
                $sf_month_filter = (isset($this->sf_options['month_filter']) ? $this->sf_options['month_filter'] : array());
                $sf_category = (isset($this->sf_options['category']) ? $this->sf_options['category'] : array());
                $sf_location = (isset($this->sf_options['location']) ? $this->sf_options['location'] : array());
                $sf_organizer = (isset($this->sf_options['organizer']) ? $this->sf_options['organizer'] : array());
                $sf_speaker = (isset($this->sf_options['speaker']) ? $this->sf_options['speaker'] : array());
                $sf_tag = (isset($this->sf_options['tag']) ? $this->sf_options['tag'] : array());
                $sf_label = (isset($this->sf_options['label']) ? $this->sf_options['label'] : array());
                $sf_text_search = (isset($this->sf_options['text_search']) ? $this->sf_options['text_search'] : array());
                $sf_address_search = (isset($this->sf_options['address_search']) ? $this->sf_options['address_search'] : array());
                $sf_event_cost = (isset($this->sf_options['event_cost']) ? $this->sf_options['event_cost'] : array());
                $sf_local_time = (isset($this->sf_options['time_filter']) ? $this->sf_options['time_filter'] : array());

                $sf_month_filter_status = (isset($sf_month_filter['type']) and trim($sf_month_filter['type'])) ? true : false;
                $sf_category_status = (isset($sf_category['type']) and trim($sf_category['type'])) ? true : false;
                $sf_location_status = (isset($sf_location['type']) and trim($sf_location['type'])) ? true : false;
                $sf_organizer_status = (isset($sf_organizer['type']) and trim($sf_organizer['type'])) ? true : false;
                $sf_speaker_status = (isset($sf_speaker['type']) and trim($sf_speaker['type'])) ? true : false;
                $sf_tag_status = (isset($sf_tag['type']) and trim($sf_tag['type'])) ? true : false;
                $sf_label_status = (isset($sf_label['type']) and trim($sf_label['type'])) ? true : false;
                $sf_text_search_status = (isset($sf_text_search['type']) and trim($sf_text_search['type'])) ? true : false;
                $sf_address_search_status = (isset($sf_address_search['type']) and trim($sf_address_search['type'])) ? true : false;
                $sf_event_cost_status = (isset($sf_event_cost['type']) and trim($sf_event_cost['type'])) ? true : false;
                $sf_local_time_status = (isset($sf_local_time['type']) and trim($sf_local_time['type'])) ? true : false;

                // Status of Speakers Feature
                $speakers_status = (!isset($this->settings['speakers_status']) or (isset($this->settings['speakers_status']) and !$this->settings['speakers_status'])) ? false : true;
                $sf_columns = 12;
            ?>
        <?php
            if((!empty($sf_category) && $sf_category["type"] == 'dropdown') || (!empty($sf_location) && $sf_location["type"] == 'dropdown') || (!empty($sf_organizer) && $sf_organizer["type"] == 'dropdown') || (!empty($sf_speaker) && $sf_speaker["type"] == 'dropdown') || (!empty($sf_tag) && $sf_tag["type"] == 'dropdown') || (!empty($sf_label) && $sf_label["type"] == 'dropdown')) $wrapper_class = 'class="mec-dropdown-wrap"';
            else $wrapper_class = '';
        ?>
        <div id="mec_search_form_<?php echo $this->id; ?>" <?php echo $wrapper_class; ?>>
            <?php if($sf_category_status): ?>
                <?php echo $this->sf_search_field('category', $sf_category , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($sf_location_status): ?>
                <?php echo $this->sf_search_field('location', $sf_location , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($sf_organizer_status): ?>
                <?php echo $this->sf_search_field('organizer', $sf_organizer , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($sf_speaker_status and $speakers_status): ?>
                <?php echo $this->sf_search_field('speaker', $sf_speaker , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($sf_tag_status): ?>
                <?php echo $this->sf_search_field('tag', $sf_tag , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($sf_label_status): ?>
                <?php echo $this->sf_search_field('label', $sf_label , $this->sf_display_label); ?>
            <?php endif; ?>
        </div>
        <div id="mec_search_form_<?php echo $this->id; ?>">
            <?php if($sf_address_search_status): ?>
                <?php echo $this->sf_search_field('address_search', $sf_address_search , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($sf_event_cost_status): $sf_columns -= 3; ?>
                <?php echo $this->sf_search_field('event_cost', $sf_event_cost , $this->sf_display_label); ?>
            <?php endif; ?>
        </div>
        <div id="mec_search_form_<?php echo $this->id; ?>" class="mec-full-calendar-search-ends">
            <?php if($sf_text_search_status): ?>
                <?php echo $this->sf_search_field('text_search', $sf_text_search , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($sf_month_filter_status): $sf_columns -= 3; ?>
                <?php echo $this->sf_search_field('month_filter', $sf_month_filter , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($sf_local_time_status): $sf_columns -= 3; ?>
                <?php echo $this->sf_search_field('time_filter', $sf_local_time , $this->sf_display_label); ?>
            <?php endif; ?>
            <?php if($this->sf_reset_button): ?>
            <div class="mec-search-reset-button"><button class="button mec-button" id="mec_search_form_<?php echo $this->id; ?>_reset" type="button"><?php echo esc_html__('Reset', 'modern-events-calendar-lite'); ?></button></div>
            <?php endif; ?>
            <div class="col-md-12 mec-tab-loader">
                <div class="mec-totalcal-view">
                    <?php if($this->yearly): ?><span class="mec-totalcal-yearlyview<?php if($this->default_view == 'yearly') echo ' mec-totalcalview-selected'; ?>" data-skin="yearly"><?php _e('Yearly', 'modern-events-calendar-lite'); ?></span><?php endif; ?>
                    <?php if($this->monthly): ?><span class="mec-totalcal-monthlyview<?php if($this->default_view == 'monthly') echo ' mec-totalcalview-selected'; ?>" data-skin="monthly"><?php _e('Monthly', 'modern-events-calendar-lite'); ?></span><?php endif; ?>
                    <?php if($this->weekly): ?><span class="mec-totalcal-weeklyview<?php if($this->default_view == 'weekly') echo ' mec-totalcalview-selected'; ?>" data-skin="weekly"><?php _e('Weekly', 'modern-events-calendar-lite'); ?></span><?php endif; ?>
                    <?php if($this->daily): ?><span class="mec-totalcal-dailyview<?php if($this->default_view == 'daily') echo ' mec-totalcalview-selected'; ?>" data-skin="daily"><?php _e('Daily', 'modern-events-calendar-lite'); ?></span><?php endif; ?>
                    <?php if($this->list): ?><span class="mec-totalcal-listview<?php if($this->default_view == 'list') echo ' mec-totalcalview-selected'; ?>" data-skin="list"><?php _e('List', 'modern-events-calendar-lite'); ?></span><?php endif; ?>
                    <?php if($this->grid): ?><span class="mec-totalcal-gridview<?php if($this->default_view == 'grid') echo ' mec-totalcalview-selected'; ?>" data-skin="grid"><?php _e('Grid', 'modern-events-calendar-lite'); ?></span><?php endif; ?>
                    <?php if($this->tile): ?><span class="mec-totalcal-tileview<?php if($this->default_view == 'tile') echo ' mec-totalcalview-selected'; ?>" data-skin="tile"><?php _e('Tile', 'modern-events-calendar-lite'); ?></span><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; // this is for if($this->sf_status): ?>
    </div>

    <div id="mec_full_calendar_container_<?php echo $this->id; ?>" class="mec-full-calendar-skin-container">
        <?php echo $this->load_skin($this->default_view); ?>
    </div>

</div>