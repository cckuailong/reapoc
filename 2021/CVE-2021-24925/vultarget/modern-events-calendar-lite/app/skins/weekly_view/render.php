<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_weekly_view $this */

$has_events = array();
$settings = $this->main->get_settings();
$this->localtime = isset($this->skin_options['include_local_time']) ? $this->skin_options['include_local_time'] : false;
$display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
$reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
?>
<ul class="mec-weekly-view-dates-events">
    <?php foreach($this->events as $date=>$events): $week = $this->week_of_days[$date]; ?>
    <?php
        if(!isset($has_events[$week]))
        {
            foreach($this->weeks[$week] as $weekday) if(isset($this->events[$weekday]) and count($this->events[$weekday])) $has_events[$week] = true;
        }
    ?>
    <?php if(count($events)): ?>
    <li class="mec-weekly-view-date-events mec-util-hidden mec-calendar-day-events mec-clear mec-weekly-view-week-<?php echo $this->id; ?>-<?php echo $this->year.$this->month.$week; ?>" id="mec_weekly_view_date_events<?php echo $this->id; ?>_<?php echo date('Ymd', strtotime($date)); ?>" data-week-number="<?php echo $week; ?>">
        <?php foreach($events as $event): ?>
            <?php
                $location_id = $this->main->get_master_location_id($event);
                $location = ($location_id ? $this->main->get_location_data($location_id) : array());

                $start_time = (isset($event->data->time) ? $event->data->time['start'] : '');
                $end_time = (isset($event->data->time) ? $event->data->time['end'] : '');
                $event_color = isset($event->data->meta['mec_color']) ? '<span class="event-color" style="background: #'.$event->data->meta['mec_color'].'"></span>' : '';
                $event_start_date = !empty($event->date['start']['date']) ? $event->date['start']['date'] : '';

                // MEC Schema
                do_action('mec_schema', $event);
            ?>
            <?php do_action('mec_weekly_view_content', $event); ?>
            <article class="<?php echo (isset($event->data->meta['event_past']) and trim($event->data->meta['event_past'])) ? 'mec-past-event ' : ''; ?>mec-event-article <?php echo $this->get_event_classes($event); ?>">
                <div class="mec-event-list-weekly-date mec-color"><span class="mec-date-day"><?php echo $this->main->date_i18n('d', strtotime($event->date['start']['date'])); ?></span><?php echo $this->main->date_i18n('F', strtotime($event->date['start']['date'])); ?></div>
                <div class="mec-event-image"><?php echo $event->data->thumbnails['thumbnail']; ?></div>
                <?php echo $this->get_label_captions($event); ?>

                <?php if($this->display_detailed_time and $this->main->is_multipleday_occurrence($event)): ?>
                <div class="mec-event-detailed-time mec-event-time mec-color"><i class="mec-sl-clock-o"></i> <?php echo $this->display_detailed_time($event); ?></div>
                <?php elseif(trim($start_time)): ?>
                <div class="mec-event-time mec-color"><i class="mec-sl-clock-o"></i> <?php echo $start_time.(trim($end_time) ? ' - '.$end_time : ''); ?></div>
                <?php endif; ?>

                <h4 class="mec-event-title"><?php echo $this->display_link($event); ?><?php echo $this->display_custom_data($event); ?><?php echo $this->main->get_flags($event).$event_color.$this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?></h4>
                <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                <div class="mec-event-detail"><div class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : ''); ?></div></div>
                <?php echo $this->display_categories($event); ?>
                <?php echo $this->display_organizers($event); ?>
                <?php echo $this->display_cost($event); ?>
                <?php echo $this->booking_button($event); ?>
            </article>
        <?php endforeach; ?>
    </li>
    <?php elseif(!isset($has_events[$week])): $has_events[$week] = 'printed'; ?>
    <li class="mec-weekly-view-date-events mec-util-hidden mec-calendar-day-events mec-clear mec-weekly-view-week-<?php echo $this->id; ?>-<?php echo date('Ym', strtotime($date)).$week; ?>" id="mec_weekly_view_date_events<?php echo $this->id; ?>_<?php echo date('Ymd', strtotime($date)); ?>" data-week-number="<?php echo $week; ?>">
        <article class="mec-event-article"><h4 class="mec-event-title"><?php _e('No Events', 'modern-events-calendar-lite'); ?></h4><div class="mec-event-detail"></div></article>
    </li>
    <?php endif; ?>
    <?php endforeach; ?>
</ul>
<div class="mec-event-footer"></div>