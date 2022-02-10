<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_countdown $this */

$styling = $this->main->get_styling();
$event = $this->events[0];
$settings = $this->main->get_settings();
$this->localtime = isset($this->skin_options['include_local_time']) ? $this->skin_options['include_local_time'] : false;
$display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
$reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;

$dark_mode = (isset($styling['dark_mode']) ? $styling['dark_mode'] : '');
if($dark_mode == 1) $set_dark = 'mec-dark-mode';
else $set_dark = '';

// Event is not valid!
if(!isset($event->data)) return;

$event_colorskin = (isset($styling['mec_colorskin']) || isset($styling['color'])) ? 'colorskin-custom' : '';

$location_id = $this->main->get_master_location_id($event);
$event_location = ($location_id ? $this->main->get_location_data($location_id) : array());

$organizer_id = $this->main->get_master_organizer_id($event);
$event_organizer = ($organizer_id ? $this->main->get_organizer_data($organizer_id) : array());

$event_date = (isset($event->date['start']) ? $event->date['start']['date'] : $event->data->meta['mec_start_date']);
$event_title = $event->data->title;

$start_date = (isset($event->date['start']) and isset($event->date['start']['date'])) ? $event->date['start']['date'] : date('Y-m-d H:i:s');
$end_date = (isset($event->date['end']) and isset($event->date['end']['date'])) ? $event->date['end']['date'] : date('Y-m-d H:i:s');

$event_time = '';
if(isset($event->data->time['start_raw'])) $event_time = $event->data->time['start_raw'];
else
{
    $event_time .= sprintf("%02d", (isset($event->data->meta['mec_date']['start']['hour']) ? $event->data->meta['mec_date']['start']['hour'] : 8)).':';
    $event_time .= sprintf("%02d", (isset($event->data->meta['mec_date']['start']['minutes']) ? $event->data->meta['mec_date']['start']['minutes'] : 0));
    $event_time .= (isset($event->data->meta['mec_date']['start']['ampm']) ? $event->data->meta['mec_date']['start']['ampm'] : 'AM');
}

$event_etime = '';
if(isset($event->data->time['end_raw'])) $event_etime = $event->data->time['end_raw'];
else
{
    $event_etime .= sprintf("%02d", (isset($event->data->meta['mec_date']['end']['hour']) ? $event->data->meta['mec_date']['end']['hour'] : 6)).':';
    $event_etime .= sprintf("%02d", (isset($event->data->meta['mec_date']['end']['minutes']) ? $event->data->meta['mec_date']['end']['minutes'] : 0));
    $event_etime .= (isset($event->data->meta['mec_date']['end']['ampm']) ? $event->data->meta['mec_date']['end']['ampm'] : 'PM');
}

$start_time = date('D M j Y G:i:s', strtotime($start_date.' '.date('H:i:s', strtotime($event_time))));
$end_time = date('D M j Y G:i:s', strtotime($end_date.' '.date('H:i:s', strtotime($event_etime))));

$d1 = new DateTime($start_time);
$d2 = new DateTime(current_time("D M j Y G:i:s"));
$d3 = new DateTime($end_time);

$countdown_method = get_post_meta($event->ID, 'mec_countdown_method', true);
if(trim($countdown_method) == '') $countdown_method = 'global';

if($countdown_method == 'global') $ongoing = (isset($settings['hide_time_method']) and trim($settings['hide_time_method']) == 'end') ? true : false;
else $ongoing = ($countdown_method == 'end') ? true : false;

if($ongoing and $d3 < $d2) $ongoing = false;

// Skip if event is ongoing
if($d1 < $d2 and !$ongoing) return;

$gmt_offset = $this->main->get_gmt_offset($event, strtotime($start_date));
if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') === false) $gmt_offset = ' : '.$gmt_offset;
if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == true) $gmt_offset = substr(trim($gmt_offset), 0 , 3);
if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') == true) $gmt_offset = substr(trim($gmt_offset), 2 , 3);

// Generating javascript code of countdown module
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#mec_skin_countdown'.$this->id.'").mecCountDown(
    {
        date: "'.($ongoing ? $end_time : $start_time).$gmt_offset.'",
        format: "off"
    },
    function()
    {
    });
});
</script>';

// Include javascript code into the page
if($this->main->is_ajax() or $this->main->preview()) echo $javascript;
else $this->factory->params('footer', $javascript);

do_action('mec_start_skin', $this->id);
do_action('mec_countdown_skin_head');
?>
<style>
.mec-event-countdown-style3 .mec-event-date, .mec-wrap .mec-event-countdown-style2, .mec-wrap .mec-event-countdown-style1, .mec-event-countdown-style1 .mec-event-countdown-part3 .mec-event-button {background: <?php echo $this->bg_color; ?> ;}
.mec-wrap .mec-event-countdown-style1 .mec-event-countdown-part2:after { border-color: transparent transparent transparent<?php echo $this->bg_color; ?>;}
.mec-event-countdown-style3 .mec-event-date:after {border-color: transparent transparent <?php echo $this->bg_color; ?> transparent;}
</style>
<div class="mec-wrap <?php echo $this->html_class . ' ' . $set_dark; ?>" id="mec_skin_<?php echo $this->id; ?>">
<?php
    // MEC Schema
    do_action('mec_schema', $event);

    if($this->style == 'style1'): ?>
    <article class="mec-event-countdown-style1 col-md-12 <?php echo $this->get_event_classes($event); ?>">
        <div class="mec-event-countdown-part1 col-md-4">
            <div class="mec-event-upcoming"><?php echo sprintf(__('%s Upcoming Event', 'modern-events-calendar-lite'), '<span>'.__('Next', 'modern-events-calendar-lite').'</span>'); ?></div>
            <h4 class="mec-event-title"><?php echo $event_title.$this->main->get_flags($event); ?> <?php echo $this->get_label_captions($event ,'mec-fc-style'); ?></h4>
            <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
        </div>
        <div class="mec-event-countdown-part2 col-md-5">
            <div class="mec-event-date-place">
                <div class="mec-event-date"><?php echo $this->main->date_i18n($this->date_format_style11, strtotime($event_date)); ?></div>
                <div class="mec-event-place"><?php echo (isset($event_location['name']) ? ' - '.$event_location['name'] : ''); ?></div>
            </div>
            <div class="mec-event-countdown" id="mec_skin_countdown<?php echo $this->id; ?>">
                <ul class="clockdiv" id="countdown">
                    <div class="days-w block-w">
                        <li>
                            <span class="mec-days">00</span>
                            <p class="mec-timeRefDays label-w"><?php _e('days', 'modern-events-calendar-lite'); ?></p>
                        </li>
                    </div>
                    <div class="hours-w block-w">
                        <li>
                            <span class="mec-hours">00</span>
                            <p class="mec-timeRefHours label-w"><?php _e('hours', 'modern-events-calendar-lite'); ?></p>
                        </li>
                    </div>  
                    <div class="minutes-w block-w">
                        <li>
                            <span class="mec-minutes">00</span>
                            <p class="mec-timeRefMinutes label-w"><?php _e('minutes', 'modern-events-calendar-lite'); ?></p>
                        </li>
                    </div>
                    <div class="seconds-w block-w">
                        <li>
                            <span class="mec-seconds">00</span>
                            <p class="mec-timeRefSeconds label-w"><?php _e('seconds', 'modern-events-calendar-lite'); ?></p>
                        </li>
                    </div>
                </ul>
            </div>
            <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
        </div>
        <div class="mec-event-countdown-part3 col-md-3">
            <?php echo $this->display_link($event, $this->main->m('event_detail', __('EVENT DETAIL', 'modern-events-calendar-lite')), 'mec-event-button'); ?>
        </div>
    </article>
    <?php elseif($this->style == 'style2'): ?>
    <article class="mec-event-countdown-style2 <?php echo $this->get_event_classes($event); ?>">
        <div class="mec-event-countdown-part1 col-md-4">
            <div class="mec-event-upcoming"><?php echo sprintf(__('%s Upcoming Event', 'modern-events-calendar-lite'), '<span>'.__('Next', 'modern-events-calendar-lite').'</span>'); ?></div>
            <h4 class="mec-event-title"><?php echo $event_title.$this->main->get_flags($event); ?> <?php echo $this->get_label_captions($event,'mec-fc-style'); ?></h4>
            <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
        </div>
        <div class="mec-event-countdown-part2 col-md-5">
            <div class="mec-event-date-place">
                <div class="mec-event-date"><?php echo $this->main->date_i18n($this->date_format_style21, strtotime($event_date)); ?></div>
                <div class="mec-event-place"><?php echo (isset($event_location['name']) ? ' - '.$event_location['name'] : ''); ?></div>
            </div>
            <div class="mec-event-countdown" id="mec_skin_countdown<?php echo $this->id; ?>">
                <ul class="clockdiv" id="countdown">
                    <div class="days-w block-w">
                        <li>
                            <span class="mec-days">00</span>
                            <p class="mec-timeRefDays label-w"><?php _e('days', 'modern-events-calendar-lite'); ?></p>
                        </li>
                    </div>
                    <div class="hours-w block-w">    
                        <li>
                            <span class="mec-hours">00</span>
                            <p class="mec-timeRefHours label-w"><?php _e('hours', 'modern-events-calendar-lite'); ?></p>
                        </li>
                    </div>  
                    <div class="minutes-w block-w">
                        <li>
                            <span class="mec-minutes">00</span>
                            <p class="mec-timeRefMinutes label-w"><?php _e('minutes', 'modern-events-calendar-lite'); ?></p>
                        </li>
                    </div>
                    <div class="seconds-w block-w">
                        <li>
                            <span class="mec-seconds">00</span>
                            <p class="mec-timeRefSeconds label-w"><?php _e('seconds', 'modern-events-calendar-lite'); ?></p>
                        </li>
                    </div>
                </ul>
            </div>
            <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
        </div>
        <div class="mec-event-countdown-part3 col-md-3">
            <?php echo $this->display_link($event, $this->main->m('event_detail', __('EVENT DETAIL', 'modern-events-calendar-lite')), 'mec-event-button'); ?>
        </div>
    </article>
    <?php elseif($this->style == 'style3'): ?>
    <article class="mec-event-countdown-style3 <?php echo $this->get_event_classes($event); ?>">
        <div class="mec-event-countdown-part1">
            <div class="mec-event-countdown-part-title">
                <div class="mec-event-upcoming"><?php echo sprintf(__('%s Upcoming Event', 'modern-events-calendar-lite'), '<span>'.__('Next', 'modern-events-calendar-lite').'</span>'); ?></div>
            </div>
            <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
            <div class="mec-event-countdown-part-details">
                <div class="mec-event-date">
                    <span class="mec-date1"><?php echo $this->main->date_i18n($this->date_format_style31, strtotime($event_date)); ?></span>
                    <span class="mec-date2"><?php echo $this->main->date_i18n($this->date_format_style32, strtotime($event_date)); ?></span>
                    <span class="mec-date3"><?php echo $this->main->date_i18n($this->date_format_style33, strtotime($event_date)); ?></span>
                </div>
                <div class="mec-event-title-link">
                    <h4 class="mec-event-title"><?php echo $event_title.$this->main->get_flags($event); ?><?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php echo $this->get_label_captions($event,'mec-fc-style'); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?></h4>
                    <?php echo $this->display_link($event, $this->main->m('event_detail', __('EVENT DETAIL', 'modern-events-calendar-lite')), 'mec-event-link'); ?>
                </div>
                <div class="mec-event-countdown" id="mec_skin_countdown<?php echo $this->id; ?>">
                    <ul class="clockdiv" id="countdown">
                        <div class="days-w block-w">
                            <li>
                                <span class="mec-days">00</span>
                                <p class="mec-timeRefDays label-w"><?php _e('days', 'modern-events-calendar-lite'); ?></p>
                            </li>
                        </div>
                        <div class="hours-w block-w">    
                            <li>
                                <span class="mec-hours">00</span>
                                <p class="mec-timeRefHours label-w"><?php _e('hours', 'modern-events-calendar-lite'); ?></p>
                            </li>
                        </div>  
                        <div class="minutes-w block-w">
                            <li>
                                <span class="mec-minutes">00</span>
                                <p class="mec-timeRefMinutes label-w"><?php _e('minutes', 'modern-events-calendar-lite'); ?></p>
                            </li>
                        </div>
                        <div class="seconds-w block-w">
                            <li>
                                <span class="mec-seconds">00</span>
                                <p class="mec-timeRefSeconds label-w"><?php _e('seconds', 'modern-events-calendar-lite'); ?></p>
                            </li>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mec-event-countdown-part2">
            <div class="mec-event-image">
                <?php echo $this->display_link($event, $event->data->thumbnails['meccarouselthumb'], ''); ?>
            </div>
        </div>
    </article>
    <?php endif; ?>
</div>