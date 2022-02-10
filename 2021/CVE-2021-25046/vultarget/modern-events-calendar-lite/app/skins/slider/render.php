<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_slider $this */

$styling = $this->main->get_styling();
$event_colorskin = (isset($styling['mec_colorskin']) or isset($styling['color'])) ? 'colorskin-custom' : '';
$settings = $this->main->get_settings();
$this->localtime = isset($this->skin_options['include_local_time']) ? $this->skin_options['include_local_time'] : false;
$display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
$reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
?>
<div class="mec-wrap <?php echo $event_colorskin; ?>">
    <div class="mec-slider-<?php echo $this->style; ?>-wrap" >
        <div class='mec-slider-<?php echo $this->style; ?> mec-owl-carousel mec-owl-theme'>
            <?php
                foreach($this->events as $date):
                foreach($date as $event):
                
                // Featured Image
                $src = $event->data->featured_image['large'];

                $location_id = $this->main->get_master_location_id($event);
                $location = ($location_id ? $this->main->get_location_data($location_id) : array());

                $event_color = isset($event->data->meta['mec_color']) ? '<span class="event-color" style="background: #'.$event->data->meta['mec_color'].'"></span>' : '';

                $start_time = (isset($event->data->time) ? $event->data->time['start'] : '');
                $end_time = (isset($event->data->time) ? $event->data->time['end'] : '');
                $event_start_date = !empty($event->date['start']['date']) ? $event->date['start']['date'] : '';
                
                $excerpt = trim($event->data->post->post_excerpt) ? $event->data->post->post_excerpt : '';

                // Safe Excerpt for UTF-8 Strings
                if(!trim($excerpt))
                {
                    $ex = explode(' ', strip_tags(strip_shortcodes($event->data->post->post_content)));
                    $words = array_slice($ex, 0, 25);

                    $excerpt = implode(' ', $words);
                }
            ?>
            <article class="<?php echo (isset($event->data->meta['event_past']) and trim($event->data->meta['event_past'])) ? 'mec-past-event ' : ''; ?>mec-event-article mec-clear <?php echo $this->get_event_classes($event); ?>">
                <?php echo $this->get_label_captions($event); ?>
                <?php do_action('mec_schema', $event); // MEC Schema ?>
                <?php if($this->style == 't1'): ?>
                    <div class="mec-slider-t1-img" style="background: url(<?php echo $src; ?>);"></div>
                    <div class="mec-slider-t1-content mec-event-grid-modern">

                        <div class="event-grid-modern-head clearfix">
                            <div class="mec-event-date mec-color"><?php echo $this->main->date_i18n($this->date_format_type1_1, strtotime($event->date['start']['date'])); ?></div>
                            <div class="mec-event-month"><?php echo $this->main->date_i18n($this->date_format_type1_2, strtotime($event->date['start']['date'])); ?></div>
                            <div class="mec-event-detail"><div class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : ''); ?></div></div>
                            <div class="mec-event-day"><?php echo $this->main->date_i18n($this->date_format_type1_3, strtotime($event->date['start']['date'])); ?></div>
                        </div>
                        <div class="mec-event-content">
                            <?php $soldout = $this->main->get_flags($event); ?>
                            <h4 class="mec-event-title"><?php echo $this->display_link($event); ?><?php echo $soldout.$event_color.$this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?></h4>
                            <div class="mec-event-detail"><div class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : '') . (isset($location['address']) ? ' | '.$location['address'] : ''); ?></div></div>
                            <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                            <?php echo $this->display_custom_data($event); ?>
                        </div>
                        <div class="mec-event-footer">
                            <?php echo $this->display_link($event, ((is_array($event->data->tickets) and count($event->data->tickets) and !strpos($soldout, '%%soldout%%')) ? $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')) : $this->main->m('view_detail', __('View Detail', 'modern-events-calendar-lite'))), 'mec-booking-button'); ?>
                        </div>
                    </div>
                <?php elseif($this->style == 't2'): ?>
                    <div class="mec-slider-t2-img" style="background: url(<?php echo $src; ?> );"></div>
                    <div class="mec-slider-t2-content mec-event-grid-modern mec-bg-color">

                        <div class="event-grid-modern-head clearfix">
                            <div class="mec-event-date mec-color"><?php echo $this->main->date_i18n($this->date_format_type2_1, strtotime($event->date['start']['date'])); ?></div>
                            <div class="mec-event-month"><?php echo $this->main->date_i18n($this->date_format_type2_2, strtotime($event->date['start']['date'])); ?></div>
                            <div class="mec-event-detail"><div class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : ''); ?></div></div>
                            <div class="mec-event-day"><?php echo $this->main->date_i18n($this->date_format_type2_3, strtotime($event->date['start']['date'])); ?></div>
                        </div>
                        <div class="mec-event-content">
                            <?php $soldout = $this->main->get_flags($event); ?>
                            <h4 class="mec-event-title"><?php echo $this->display_link($event); ?><?php echo $soldout.$event_color; ?></h4>
                            <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
                            <div class="mec-event-detail">
                                <span class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : '') . (isset($location['address']) ? ' | '.$location['address'] : ''); ?></span>
                                <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                            </div>
                            <?php echo $this->display_custom_data($event); ?>
                        </div>
                        <div class="mec-event-footer">
                            <?php echo $this->display_link($event, ((is_array($event->data->tickets) and count($event->data->tickets) and !strpos($soldout, '%%soldout%%')) ? $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')) : $this->main->m('view_detail', __('View Detail', 'modern-events-calendar-lite'))), 'mec-booking-button'); ?>
                        </div>
                    </div>
                <?php elseif($this->style == 't3'): ?>
                    <div class="mec-slider-t3-img" style="background: url(<?php echo $src; ?> );"></div>
                    <div class="mec-slider-t3-content mec-event-grid-modern">
                        <div class="event-grid-modern-head clearfix">
                            <div class="mec-event-date mec-color"><?php echo $this->main->date_i18n($this->date_format_type3_1, strtotime($event->date['start']['date'])); ?></div>
                            <div class="mec-event-month"><?php echo $this->main->date_i18n($this->date_format_type3_2, strtotime($event->date['start']['date'])); ?></div>
                            <div class="mec-event-detail"><div class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : ''); ?></div></div>
                            <div class="mec-event-day"><?php echo $this->main->date_i18n($this->date_format_type3_3, strtotime($event->date['start']['date'])); ?></div>
                        </div>
                        <div class="mec-event-content">
                            <?php $soldout = $this->main->get_flags($event); ?>
                            <h4 class="mec-event-title"><?php echo $this->display_link($event); ?><?php echo $soldout.$event_color; ?></h4>
                            <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
                            <div class="mec-event-detail">
                                <span class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : '') . (isset($location['address']) ? ' | '.$location['address'] : ''); ?></span>
                                <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                            </div>
                            <?php echo $this->display_custom_data($event); ?>
                        </div>
                        <div class="mec-slider-t3-footer">
                            <?php echo $this->display_link($event, ((is_array($event->data->tickets) and count($event->data->tickets) and !strpos($soldout, '%%soldout%%')) ? $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')) : $this->main->m('view_detail', __('View Detail', 'modern-events-calendar-lite'))), 'mec-booking-button'); ?>
                        </div>
                    </div>
                <?php elseif($this->style == 't4'): ?>
                    <div class="mec-slider-t4-img" style="background: url(<?php echo $src; ?> );"></div>
                    <div class="mec-slider-t4-content mec-event-grid-modern">

                        <div class="event-grid-modern-head clearfix">
                            <div class="mec-event-date mec-color"><?php echo $this->main->date_i18n($this->date_format_type4_1, strtotime($event->date['start']['date'])); ?></div>
                            <div class="mec-event-month"><?php echo $this->main->date_i18n($this->date_format_type4_2, strtotime($event->date['start']['date'])); ?></div>
                            <div class="mec-event-detail"><div class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : ''); ?></div></div>
                            <div class="mec-event-day"><?php echo $this->main->date_i18n($this->date_format_type4_3, strtotime($event->date['start']['date'])); ?></div>
                        </div>
                        <div class="mec-event-content">
                            <?php $soldout = $this->main->get_flags($event); ?>
                            <h4 class="mec-event-title"><?php echo $this->display_link($event); ?><?php echo $soldout.$event_color; ?></h4>
                            <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation);?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
                            <div class="mec-event-detail">
                                <span class="mec-event-loc-place"><?php echo (isset($location['name']) ? $location['name'] : '') . (isset($location['address']) ? ' | '.$location['address'] : ''); ?></span>
                                <?php if($this->localtime) echo $this->main->module('local-time.type3', array('event'=>$event)); ?>
                            </div>
                        </div>
                        <?php echo $this->display_custom_data($event); ?>
                        <div class="mec-slider-t4-footer">
                            <?php echo $this->display_link($event, ((is_array($event->data->tickets) and count($event->data->tickets) and !strpos($soldout, '%%soldout%%')) ? $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')) : $this->main->m('view_detail', __('View Detail', 'modern-events-calendar-lite'))), 'mec-booking-button'); ?>
                        </div>
                    </div>
                <?php elseif($this->style == 't5'): ?>
                    <div class="mec-slider-t5-img" style="background: url(<?php echo $src; ?> );"></div>
                    <div class="mec-slider-t5-content mec-event-grid-modern">
                        <div class="event-grid-modern-head clearfix">
                            <div class="mec-slider-t5-col6">
                                <div class="mec-event-date mec-color"><?php echo $this->main->date_i18n($this->date_format_type5_1, strtotime($event->date['start']['date'])); ?></div>
                                <div class="mec-event-month"><?php echo $this->main->date_i18n($this->date_format_type5_2, strtotime($event->date['start']['date'])); ?></div>
                                <div class="mec-event-detail">
                                    <?php echo $start_time.(trim($end_time) ? ' - '.$end_time : ''); ?>
                                    <?php if($this->localtime) echo $this->main->module('local-time.type2', array('event'=>$event)); ?>
                                </div>
                            </div>
                            <div class="mec-slider-t5-col6">
                                <div class="mec-event-location">
                                    <i class="mec-sl-location-pin mec-color"></i>
                                    <div class="mec-event-location-det">
                                        <h6 class="mec-location"><?php echo (isset($location['name']) ? $location['name'] : ''); ?></h6>
                                        <address class="mec-events-address"><span class="mec-address"><?php echo (isset($location['address']) ? $location['address'] : ''); ?></span></address>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mec-event-content">
                            <?php $soldout = $this->main->get_flags($event); ?>
                            <h4 class="mec-event-title"><?php echo $this->display_link($event); ?><?php echo $soldout.$event_color; ?></h4>
                            <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?><?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?>
                            <div class="mec-event-description mec-events-content">
                                <p><?php echo $excerpt.(trim($excerpt) ? ' ...' : ''); ?></p>
                            </div>
                            <?php echo $this->display_custom_data($event); ?>
                        </div>
                        <div class="mec-event-footer">
                            <?php echo $this->display_link($event, ((is_array($event->data->tickets) and count($event->data->tickets) and !strpos($soldout, '%%soldout%%')) ? $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')) : $this->main->m('view_detail', __('View Detail', 'modern-events-calendar-lite'))), 'mec-booking-button'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
	</div>
</div>