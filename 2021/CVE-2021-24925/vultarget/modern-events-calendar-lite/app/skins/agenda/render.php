<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_agenda $this */

$current_month_divider = $this->request->getVar('current_month_divider', 0);
$settings = $this->main->get_settings();
$this->localtime = isset($this->skin_options['include_local_time']) ? $this->skin_options['include_local_time'] : false;
$display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
$reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
?>
<?php foreach($this->events as $date=>$events): ?>

    <?php $month_id = date('Ym', strtotime($date)); if($this->month_divider and $month_id != $current_month_divider): $current_month_divider = $month_id; ?>
        <div class="mec-month-divider" data-toggle-divider="mec-toggle-<?php echo date('Ym', strtotime($date)); ?>-<?php echo $this->id; ?>"><span><?php echo $this->main->date_i18n('F Y', strtotime($date)); ?></span><i class="mec-sl-arrow-down"></i></div>
    <?php endif; ?>
    <div class="mec-events-agenda">

        <div class="mec-agenda-date-wrap">
            <i class="mec-sl-calendar"></i>
            <span class="mec-agenda-day"><?php echo $this->main->date_i18n($this->date_format_clean_1, strtotime($date)); ?></span>
            <span class="mec-agenda-date"><?php echo $this->main->date_i18n($this->date_format_clean_2, strtotime($date)); ?></span>
        </div>

        <div class="mec-agenda-events-wrap">
            <?php
                foreach($events as $event)
                {
                    $location_id = $this->main->get_master_location_id($event);
                    $location = ($location_id ? $this->main->get_location_data($location_id) : array());

                    $organizer_id = $this->main->get_master_organizer_id($event);
                    $organizer = ($organizer_id ? $this->main->get_organizer_data($organizer_id) : array());

                    $start_time = (isset($event->data->time) ? $event->data->time['start'] : '');
                    $end_time = (isset($event->data->time) ? $event->data->time['end'] : '');
                    $event_color = isset($event->data->meta['mec_color']) ? '<span class="event-color" style="background: #'.$event->data->meta['mec_color'].'"></span>' : '';
                    $event_start_date = !empty($event->date['start']['date']) ? $event->date['start']['date'] : '';

                    // MEC Schema
                    do_action('mec_schema', $event);

                    if($this->style == 'clean'): ?>
                    <div class="<?php echo (isset($event->data->meta['event_past']) and trim($event->data->meta['event_past'])) ? 'mec-past-event ' : ''; ?>mec-agenda-event <?php echo $this->get_event_classes($event); ?>">
                        <i class="mec-sl-clock "></i>
                        <span class="mec-agenda-time">
                            <?php
                                if(trim($start_time))
                                {
                                    echo '<span class="mec-start-time">'.$start_time.'</span>';
                                    if(trim($end_time)) echo ' - <span class="mec-end-time">'.$end_time.'</span>';
                                }
                            ?>
                        </span>
                        <span class="mec-agenda-event-title">
                            <?php echo $this->display_link($event); ?>
                            <?php echo $this->main->get_flags($event).$event_color; ?>
                            <?php echo $this->get_label_captions($event, 'mec-fc-style'); ?>
                            <?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
                            <?php echo $this->booking_button($event); ?>
                            <?php if($this->localtime) echo $this->main->module('local-time.type2', array('event'=>$event)); ?>
                        </span>
                        <?php echo $this->display_custom_data($event); ?>
                        <?php do_action('mec_agenda_skin_attribute', $organizer, $location); ?>
                    </div>
                <?php endif; ?>
            <?php } ?>
        </div>
    </div>
<?php endforeach;