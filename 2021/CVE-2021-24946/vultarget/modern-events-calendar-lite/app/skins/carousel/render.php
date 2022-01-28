<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_carousel $this */

$styling = $this->main->get_styling();
$event_colorskin = (isset($styling['mec_colorskin']) or isset($styling['color'])) ? 'colorskin-custom' : '';
$settings = $this->main->get_settings();
$display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
$reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
?>
<div class="mec-wrap <?php echo $event_colorskin; ?>">
    <div class="mec-event-carousel-<?php echo $this->style; ?>">
        <?php 
            if($this->style == 'type4') $carousel_type = 'type4';
            elseif($this->style == 'type1') $carousel_type = 'type1';
            else $carousel_type = 'type2';
        ?>
        <div class='mec-owl-crousel-skin-<?php echo $carousel_type; ?> mec-owl-carousel mec-owl-theme'>
            <?php
                foreach($this->events as $date):
                foreach($date as $event):

                $location_id = $this->main->get_master_location_id($event);
                $location = ($location_id ? $this->main->get_location_data($location_id) : array());

                $organizer_id = $this->main->get_master_organizer_id($event);
                $organizer = ($organizer_id ? $this->main->get_organizer_data($organizer_id) : array());

                $event_color = isset($event->data->meta['mec_color']) ? '<span class="event-color" style="background: #'.$event->data->meta['mec_color'].'"></span>' : '';
                $event_date = (isset($event->date['start']) ? $event->date['start']['date'] : $event->data->meta['mec_start_date']);
                $event_start_date = !empty($event->date['start']['date']) ? $event->date['start']['date'] : '';
                $event_end_date = !empty($event->date['end']['date']) ? $event->date['end']['date'] : '';
                $start_time = (isset($event->data->time) ? $event->data->time['start'] : '');
                $end_time = (isset($event->data->time) ? $event->data->time['end'] : '');
                $multiple_date = ($event_start_date != $event_end_date) ? 'mec-multiple-event' : '';
            ?>
            <article class="<?php echo (isset($event->data->meta['event_past']) and trim($event->data->meta['event_past'])) ? 'mec-past-event ' : ''; ?>mec-event-article <?php echo $multiple_date; ?> mec-clear <?php echo $this->get_event_classes($event); ?>" itemscope>
            <?php do_action('mec_schema', $event); // MEC Schema ?>
            <?php if($this->style == 'type1'): ?>
                <div class="event-carousel-type1-head clearfix">
                    <div class="mec-event-date mec-color">
                        <div class="mec-event-image">
                            <?php
                                if($event->data->thumbnails['meccarouselthumb']) echo $this->display_link($event, $event->data->thumbnails['meccarouselthumb'], '');
                                else echo '<img src="'. $this->main->asset('img/no-image.png') .'" />';
                            ?>
                            <?php echo $this->get_label_captions($event); ?>
                        </div>
                        <div class="mec-event-date-carousel">
                            <?php echo $this->main->dateify($event, $this->date_format_type1_1); ?>
                            <div class="mec-event-date-info"><?php echo $this->main->dateify($event, $this->date_format_type1_2); ?></div>
                            <div class="mec-event-date-info-year"><?php echo $this->main->date_i18n($this->date_format_type1_3, strtotime($event->date['start']['date'])); ?></div>
                        </div>
                    </div>
                </div>
                <div class="mec-event-carousel-content">
                    <?php if($this->include_events_times) echo $this->main->display_time($start_time, $end_time); ?>
                    <h4 class="mec-event-carousel-title"><?php echo $this->display_link($event); ?><?php echo $this->display_custom_data($event); ?><?php echo $this->main->get_flags($event); ?></h4>
                    <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID); ?>
                    <p class="mec-carousel-event-location"><span><?php echo (isset($location['name']) ? $location['name'] : ''); echo (isset($location['address']) ? '</span><br>'.$location['address'] : ''); ?></p>
                    <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                    <?php echo $this->booking_button($event); ?>
                </div>
                <?php elseif($this->style == 'type2'): ?>
                <div class="event-carousel-type2-head clearfix">
                    <div class="mec-event-image">
                        <?php 
                            if($event->data->thumbnails['meccarouselthumb']) echo $this->display_link($event, $event->data->thumbnails['meccarouselthumb'], '');
                            else echo '<img src="'. $this->main->asset('img/no-image.png') .'" />';
                        ?>
                        <?php echo $this->get_label_captions($event); ?>
                    </div>
                    <div class="mec-event-carousel-content-type2">
                        <?php if(isset($settings['multiple_day_show_method']) && $settings['multiple_day_show_method'] == 'all_days') : ?>
                            <span class="mec-event-date-info"><?php echo $this->main->date_i18n($this->date_format_type2_1, strtotime($event->date['start']['date'])); ?></span>
                        <?php else: ?>
                            <span class="mec-event-date-info"><?php echo $this->main->dateify($event, $this->date_format_type2_1); ?></span>
                        <?php endif; ?>
                        <?php do_action('mec_carousel_type2_before_title', $event); ?>
                        <?php $soldout = $this->main->get_flags($event); ?>
                        <?php if($this->include_events_times) echo $this->main->display_time($start_time, $end_time); ?>
                        <h4 class="mec-event-carousel-title"><?php echo $this->display_link($event); ?><?php echo $this->display_custom_data($event); ?><?php echo $soldout; ?></h4>
                        <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID); ?>
                        <?php do_action('mec_carousel_type2_after_title', $event); ?>
                        <p class="mec-carousel-event-location"><span><?php echo (isset($location['name']) ? $location['name'] : ''); echo (isset($location['address']) ? '</span><br>'.$location['address'] : ''); ?></p>
                        <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                    </div>
                    <div class="mec-event-footer-carousel-type2">
                        <?php if($settings['social_network_status'] != '0') : ?>
                        <ul class="mec-event-sharing-wrap">
                            <li class="mec-event-share">
                                <a href="#" class="mec-event-share-icon">
                                    <i class="mec-sl-share mec-bg-color-hover mec-border-color-hover"></i>
                                </a>
                            </li>
                            <li>
                                <ul class="mec-event-sharing">
                                    <?php echo $this->main->module('links.list', array('event'=>$event)); ?>
                                </ul>
                            </li>
                        </ul>
                        <?php endif; ?>
                        <?php echo $this->display_link($event, ((is_array($event->data->tickets) and count($event->data->tickets) and !strpos($soldout, '%%soldout%%') and !$this->booking_button and !$this->main->is_expired($event)) ? $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')) : $this->main->m('view_detail', __('View Detail', 'modern-events-calendar-lite'))), 'mec-booking-button mec-bg-color-hover mec-border-color-hover'); ?>
                        <?php echo $this->booking_button($event); ?>
                    </div>
                </div>
                <?php elseif($this->style == 'type3'): ?>
                <div class="event-carousel-type3-head clearfix">
                    <div class="mec-event-image">
                        <?php 
                            // if($event->data->thumbnails['meccarouselthumb']) echo $event->data->thumbnails['meccarouselthumb'];
                            if($event->data->thumbnails['meccarouselthumb']) echo $this->display_link($event, $event->data->thumbnails['meccarouselthumb'], '');
                            else echo '<img src="'. $this->main->asset('img/no-image.png') .'" />';
                        ?>
                        <?php echo $this->get_label_captions($event); ?>
                    </div>
                    <div class="mec-event-footer-carousel-type3">
                        <?php if(isset($settings['multiple_day_show_method']) && $settings['multiple_day_show_method'] == 'all_days') : ?>
                            <div class="mec-event-date-info"><?php echo $this->main->date_i18n($this->date_format_type3_1, strtotime($event->date['start']['date'])); ?></div>
                        <?php else: ?>
                            <span class="mec-event-date-info"><?php echo $this->main->dateify($event, $this->date_format_type3_1); ?></span>
                        <?php endif; ?>
                        <?php $soldout = $this->main->get_flags($event); ?>
                        <?php if($this->include_events_times) echo $this->main->display_time($start_time, $end_time); ?>
                        <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                        <h4 class="mec-event-carousel-title"><?php echo $this->display_link($event); ?><?php echo $this->display_custom_data($event); ?><?php echo $soldout; ?></h4>
                        <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID); ?>
                        <p class="mec-carousel-event-location"><span><?php echo (isset($location['name']) ? $location['name'] : ''); echo (isset($location['address']) ? '</span><br>'.$location['address'] : ''); ?></p>
                        <?php if($settings['social_network_status'] != '0'): ?>
                            <ul class="mec-event-sharing-wrap">
                                <li class="mec-event-share">
                                    <a href="#" class="mec-event-share-icon">
                                        <i class="mec-sl-share mec-bg-color-hover mec-border-color-hover"></i>
                                    </a>
                                </li>
                                <li>
                                    <ul class="mec-event-sharing">
                                        <?php echo $this->main->module('links.list', array('event'=>$event)); ?>
                                    </ul>
                                </li>
                            </ul>
                        <?php endif; ?>
                        <?php echo $this->display_link($event, ((is_array($event->data->tickets) and count($event->data->tickets) and !strpos($soldout, '%%soldout%%') and !$this->booking_button and !$this->main->is_expired($event)) ? $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')) : $this->main->m('view_detail', __('View Detail', 'modern-events-calendar-lite'))), 'mec-booking-button mec-bg-color-hover mec-border-color-hover'); ?>
                        <?php echo $this->booking_button($event); ?>
                    </div>
                </div>
                <?php elseif($this->style == 'type4'): ?>
                <div class="event-carousel-type4-head clearfix">
                    <div class="mec-event-image">
                        <?php 
                            if($event->data->thumbnails['full']) echo $this->display_link($event, $event->data->thumbnails['full'], '');
                            else echo '<img src="'. $this->main->asset('img/no-image.png') .'" />';
                        ?>
                        <?php echo $this->get_label_captions($event); ?>
                    </div>
                    <div class="mec-event-overlay"></div>
                    <div class="mec-event-hover-carousel-type4">
                        <i class="mec-event-icon mec-bg-color mec-fa-calendar"></i>
                        <div class="mec-event-date">
                            <span class="mec-color"><?php echo $this->main->date_i18n('F d', strtotime($event_date)); ?></span> <?php echo $this->main->date_i18n('l', strtotime($event_date)); ?>
                        </div>
                        <?php if($this->include_events_times) echo $this->main->display_time($start_time, $end_time); ?>
                        <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                        <h4 class="mec-event-title"><?php echo $event->data->title.$this->main->get_flags($event).$event_color; ?><?php echo $this->display_custom_data($event); ?></h4>
                        <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID); ?>
                        <div class="mec-btn-wrapper">
                            <?php echo $this->display_link($event, ($this->main->m('event_detail', __('EVENT DETAIL', 'modern-events-calendar-lite'))), 'mec-event-button'); ?>
                            <?php echo $this->booking_button($event); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <?php if($this->style == 'type4'):  ?>
        <div class="row mec-carousel-type4-head">
            <div class="col-md-6 col-xs-6">
                <div class="mec-carousel-type4-head-link">
                    <?php if(!empty($this->archive_link)): ?><a class="mec-bg-color-hover" href="<?php echo esc_html($this->archive_link); ?>"><?php esc_html_e('View All Events' , 'modern-events-calendar-lite'); ?></a><?php endif; ?>
                </div>
            </div>
            <div class="col-md-6 col-xs-6">
                <div class="mec-carousel-type4-head-title">
                    <?php if(!empty($this->head_text)): ?><?php esc_html_e($this->head_text); ?><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
	</div>
</div>