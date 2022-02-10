<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_skin_masonry $this */

$styling = $this->main->get_styling();
$event_colorskin = (isset($styling['mec_colorskin']) || isset($styling['color'])) ? 'colorskin-custom' : '';
$settings = $this->main->get_settings();
$this->localtime = isset($this->skin_options['include_local_time']) ? $this->skin_options['include_local_time'] : false;
$display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
$reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
?>
<div class="mec-wrap <?php echo $event_colorskin; ?>">
    <div class="mec-event-masonry">
        <?php
        foreach($this->events as $date):
        foreach($date as $event):

            $location_id = $this->main->get_master_location_id($event);
            $location = ($location_id ? $this->main->get_location_data($location_id) : array());

            $organizer_id = $this->main->get_master_organizer_id($event);
            $organizer = ($organizer_id ? $this->main->get_organizer_data($organizer_id) : array());

            $event_color = isset($event->data->meta['mec_color']) ? '<span class="event-color" style="background: #'.$event->data->meta['mec_color'].'"></span>' : '';

            $start_time = (isset($event->data->time) ? $event->data->time['start'] : '');
            $end_time = (isset($event->data->time) ? $event->data->time['end'] : '');
            $event_start_date = !empty($event->date['start']['date']) ? $event->date['start']['date'] : '';

            // MEC Schema
            do_action('mec_schema', $event);

            $masonry_filter = '';
            if($this->filter_by == 'category')
            {
                if(isset($event->data->categories) && !empty($event->data->categories))
                {
                    $masonry_filter = "[";
                    foreach($event->data->categories as $key => $value) $masonry_filter .= '"' . $value['id'] . '",';

                    $masonry_filter .= "]";
                    $masonry_filter = str_replace(",]", "]", $masonry_filter);
                }
            }
            elseif($this->filter_by == 'label')
            {
                if(isset($event->data->labels) && !empty($event->data->labels))
                {
                    $masonry_filter = "[";
                    foreach($event->data->labels as $key => $value) $masonry_filter .= '"' . $value['id'] . '",';

                    $masonry_filter .= "]";
                    $masonry_filter = str_replace(",]", "]", $masonry_filter);
                }
            }
            elseif($this->filter_by == 'organizer')
            {
                if(isset($event->data->organizers) && !empty($event->data->organizers))
                {
                    $masonry_filter = "[";
                    foreach($event->data->organizers as $key => $value) $masonry_filter .= '"' . $value['id'] . '",';

                    $masonry_filter .= "]";
                    $masonry_filter = str_replace(",]", "]", $masonry_filter);
                }
            }
            elseif($this->filter_by == 'location')
            {
                if(isset($event->data->locations) && !empty($event->data->locations))
                {
                    $masonry_filter = "[";
                    foreach($event->data->locations as $key => $value) $masonry_filter .= '"' . $value['id'] . '",';

                    $masonry_filter .= "]";
                    $masonry_filter = str_replace(",]", "]", $masonry_filter);
                }
            }
            
            if(empty($masonry_filter)) $masonry_filter = "[\"\"]";
            ?>
            <div data-sort-masonry="<?php echo $event->date['start']['date']; ?>" class="<?php echo (isset($event->data->meta['event_past']) and trim($event->data->meta['event_past'])) ? 'mec-past-event ' : ''; ?>mec-masonry-item-wrap <?php echo $this->filter_by_classes($event->data->ID); ?>">
                <div class="mec-masonry">

                    <article class="mec-event-article mec-clear <?php echo $this->get_event_classes($event); ?>">
                        <?php if(isset($event->data->featured_image) and $this->masonry_like_grid): ?>
                            <div class="mec-masonry-img"><?php echo $this->display_link($event, get_the_post_thumbnail($event->data->ID , 'thumblist'), ''); ?></div>
                        <?php elseif(isset($event->data->featured_image) and isset($event->data->featured_image['full']) and trim($event->data->featured_image['full'])): ?>
                            <div class="mec-masonry-img"><?php echo $this->display_link($event, get_the_post_thumbnail($event->data->ID , 'full'), ''); ?></div>
                        <?php endif; ?>

                        <?php echo $this->get_label_captions($event); ?>

                        <div class="mec-masonry-content mec-event-grid-modern">
                            <div class="event-grid-modern-head clearfix">

                                <div class="mec-masonry-col<?php echo (isset($location['name']) and trim($location['name'])) ? '6' : '12'; ?>">
                                    <?php if(isset($settings['multiple_day_show_method']) and ($settings['multiple_day_show_method'] == 'all_days' or $settings['multiple_day_show_method'] == 'first_day_listgrid')): ?>
                                        <div class="mec-event-date mec-color"><?php echo $this->main->date_i18n($this->date_format_1, strtotime($event->date['start']['date'])); ?></div>
                                        <div class="mec-event-month"><?php echo $this->main->date_i18n($this->date_format_2, strtotime($event->date['start']['date'])); ?></div>
                                    <?php else: ?>
                                        <div class="mec-event-date mec-color"><?php echo $this->main->dateify($event, $this->date_format_1); ?></div>
                                        <div class="mec-event-month"><?php echo $this->main->dateify($event, $this->date_format_2); ?></div>
                                    <?php endif; ?>
                                    <div class="mec-event-detail"><?php echo $start_time.(trim($end_time) ? ' - '.$end_time : ''); ?></div>
                                    <?php if($this->localtime) echo $this->main->module('local-time.type2', array('event'=>$event)); ?>
                                </div>

                                <?php if(isset($location['name']) and trim($location['name'])): ?>
                                <div class="mec-masonry-col6">
                                    <div class="mec-event-location">
                                        <i class="mec-sl-location-pin mec-color"></i>
                                        <div class="mec-event-location-det">
                                            <h6 class="mec-location"><?php echo (isset($location['name']) ? $location['name'] : ''); ?></h6>
                                            <address class="mec-events-address"><span class="mec-address"><?php echo (isset($location['address']) ? $location['address'] : ''); ?></span></address>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                            </div>
                            <?php do_action('print_extra_fields_masonry', $event); ?>
                            <?php
                                $excerpt = trim($event->data->post->post_excerpt) ? $event->data->post->post_excerpt : '';

                                // Safe Excerpt for UTF-8 Strings
                                if(!trim($excerpt))
                                {
                                    $excerpt_count  = apply_filters('MEC_masonry_excerpt', '9');
                                    $ex = explode(' ', strip_tags(strip_shortcodes($event->data->post->post_content)));
                                    $words = array_slice($ex, 0, apply_filters('MEC_masonry_excerpt', '9'));

                                    $excerpt = implode(' ', $words);
                                }
                            ?>
                            <div class="mec-event-content">
                                <?php $soldout = $this->main->get_flags($event); ?>
                                <h4 class="mec-event-title"><?php echo $this->display_link($event); ?><?php echo $this->display_custom_data($event); ?><?php echo $soldout; ?> <?php do_action('mec_shortcode_virtual_badge', $event->data->ID ); ?> <?php echo $event_color; ?></h4>
                                <?php echo $this->main->get_normal_labels($event, $display_label).$this->main->display_cancellation_reason($event, $reason_for_cancellation); ?>
                                <?php echo $this->display_categories($event); ?>
                                <?php echo $this->display_organizers($event); ?>
                                <div class="mec-event-description mec-events-content">
                                    <p><?php echo $excerpt.(trim($excerpt) ? ' ...' : ''); ?></p>
                                </div>
                            </div>
                            <div class="mec-event-footer">
                                <?php echo $this->display_link($event, ((is_array($event->data->tickets) and count($event->data->tickets) and !strpos($soldout, '%%soldout%%') and !$this->booking_button and !$this->main->is_expired($event)) ? $this->main->m('register_button', __('REGISTER', 'modern-events-calendar-lite')) : $this->main->m('view_detail', __('View Detail', 'modern-events-calendar-lite'))), 'mec-booking-button'); ?>
                                <?php echo $this->booking_button($event); ?>
                                <?php do_action('mec_masonry_button', $event); ?>
                            </div>
                        </div>
                    </article>

                </div>
            </div>
        <?php endforeach; ?>
        <?php endforeach; ?>
	</div>
</div>