<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var $this MEC_skin_available_spot **/

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
$event_thumb_url = $event->data->featured_image['large'];
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

$event_start_date = !empty($event->date['start']['date']) ? $event->date['start']['date'] : '';

$start_time = date('D M j Y G:i:s', strtotime($start_date.' '.date('H:i:s', strtotime($event_time))));
$end_time = date('D M j Y G:i:s', strtotime($end_date.' '.date('H:i:s', strtotime($event_etime))));

$d1 = new DateTime($start_time);
$d2 = new DateTime(current_time("D M j Y G:i:s"));
$d3 = new DateTime($end_time);

$ongoing = (isset($settings['hide_time_method']) and trim($settings['hide_time_method']) == 'end') ? true : false;

// Skip if event is expired
if($ongoing) if($d3 < $d2) $ongoing = false;
if($d1 < $d2 and !$ongoing) return;

$gmt_offset = $this->main->get_gmt_offset($event, strtotime($start_date));
if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') === false) $gmt_offset = ' : '.$gmt_offset;
if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == true) $gmt_offset = '';

// Generating javascript code of countdown module
$javascript = '<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery("#mec_skin_available_spot'.$this->id.'").mecCountDown(
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

$occurrence_time = isset($event->date['start']['timestamp']) ? $event->date['start']['timestamp'] : strtotime($event->date['start']['date']);

$book = $this->getBook();
$availability = $book->get_tickets_availability($event->data->ID, $occurrence_time);
$event_color = isset($event->data->meta['mec_color']) ? '<span class="event-color" style="background: #'.$event->data->meta['mec_color'].'"></span>' : '';

$spots = 0;
$total_spots = -1;
foreach($availability as $ticket_id=>$count)
{
    if(!is_numeric($ticket_id))
    {
        $total_spots = $count;
        continue;
    }

    if($count != '-1') $spots += $count;
    else
    {
        $spots = -1;
        break;
    }
}

if($total_spots >= 0) $spots = min($spots, $total_spots);

do_action('mec_start_skin', $this->id);
do_action('mec_available_spot_skin_head');
?>
<div class="mec-wrap <?php echo $event_colorskin; ?> <?php echo $this->html_class . ' ' . $set_dark; ?>" id="mec_skin_<?php echo $this->id; ?>">
    <div class="mec-av-spot-wrap">
        <?php
            // MEC Schema
            do_action('mec_schema', $event);
        ?>
        <div class="mec-av-spot">
            <article class="<?php echo (isset($event->data->meta['event_past']) and trim($event->data->meta['event_past'])) ? 'mec-past-event ' : ''; ?>mec-event-article mec-clear <?php echo $this->get_event_classes($event); ?>">

                <?php if($event_thumb_url): ?>
                <div class="mec-av-spot-img" style="background: url('<?php echo $event_thumb_url; ?>');"></div>
                <?php endif; ?>

                <?php echo $this->get_label_captions($event); ?>

                <div class="mec-av-spot-head clearfix">
                    <div class="mec-av-spot-col6">
                        <div class="mec-av-spot-box"><?php _e('Available Spot(s):', 'modern-events-calendar-lite'); ?> <span class="mec-av-spot-count mec-color"><?php echo ($spots != '-1' ? $spots : __('Unlimited', 'modern-events-calendar-lite')); ?></span></div>
                    </div>
                    <div class="mec-av-spot-col6">
                        <div class="mec-event-countdown" id="mec_skin_available_spot<?php echo $this->id; ?>">
                            <ul class="clockdiv" id="countdown">
                                <li class="days-w block-w">
                                    <span class="mec-days">00</span>
                                    <p class="mec-timeRefDays label-w"><?php _e('days', 'modern-events-calendar-lite'); ?></p>
                                </li>
                                <li class="hours-w block-w">
                                    <span class="mec-hours">00</span>
                                    <p class="mec-timeRefHours label-w"><?php _e('hours', 'modern-events-calendar-lite'); ?></p>
                                </li>
                                <li class="minutes-w block-w">
                                    <span class="mec-minutes">00</span>
                                    <p class="mec-timeRefMinutes label-w"><?php _e('minutes', 'modern-events-calendar-lite'); ?></p>
                                </li>
                                <li class="seconds-w block-w">
                                    <span class="mec-seconds">00</span>
                                    <p class="mec-timeRefSeconds label-w"><?php _e('seconds', 'modern-events-calendar-lite'); ?></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mec-av-spot-content mec-event-grid-modern">

                    <div class="event-grid-modern-head clearfix">
                        <div class="mec-av-spot-col6">
                            <div class="mec-event-date mec-color"><?php echo $this->main->date_i18n($this->date_format1, strtotime($event_date)); ?></div>
                            <div class="mec-event-month"><?php echo $this->main->date_i18n($this->date_format2, strtotime($event_date)); ?></div>
                            <div class="mec-event-detail"><?php echo (isset($event->data->time) and isset($event->data->time['start'])) ? $event->data->time['start'] : ''; ?><?php echo (isset($event->data->time) and isset($event->data->time['end']) and trim($event->data->time['end'])) ? ' - '.$event->data->time['end'] : ''; ?></div>
                            <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                        </div>
                        <div class="mec-av-spot-col6">
                            <?php if(isset($event_location['name'])): ?>
                            <div class="mec-event-location">
                                <i class="mec-sl-location-pin mec-color"></i>
                                <div class="mec-event-location-det">
                                    <h6 class="mec-location"><?php echo $event_location['name']; ?></h6>
                                    <?php if(isset($event_location['address']) and trim($event_location['address'])): ?><address class="mec-events-address"><span class="mec-address"><?php echo $event_location['address']; ?></span></address><?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mec-event-content">
                        <h4 class="mec-event-title"><?php echo $this->display_link($event); ?><?php echo $this->main->get_flags($event).$event_color; ?></h4>
                        <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation);?>
                        <?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
                        <?php
                            $excerpt = trim($event->data->post->post_excerpt) ? $event->data->post->post_excerpt : '';

                            // Safe Excerpt for UTF-8 Strings
                            if(!trim($excerpt))
                            {
                                $ex = explode(' ', strip_tags(strip_shortcodes($event->data->post->post_content)));
                                $words = array_slice($ex, 0, 30);

                                $excerpt = implode(' ', $words);
                            }
                        ?>
                        <div class="mec-event-description mec-events-content">
                            <p><?php echo $excerpt.(trim($excerpt) ? ' ...' : ''); ?></p>
                        </div>
                    </div>
                    <div class="mec-event-footer">
                        <?php echo $this->display_link($event, $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')), 'mec-booking-button'); ?>
                    </div>
                </div>
            </article>
        </div>

    </div>
</div>