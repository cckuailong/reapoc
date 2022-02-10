<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC schema class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_schema extends MEC_base
{
    public $factory;
    public $main;
    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();

        // Import MEC Main
        $this->main = $this->getMain();
        
        // MEC Settings
        $this->settings = $this->main->get_settings();
    }
    
    /**
     * Initialize locations feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Schema Meta Box
        $this->factory->action('mec_metabox_details', array($this, 'meta_box_schema'), 60);
        if(!isset($this->settings['fes_section_schema']) or (isset($this->settings['fes_section_schema']) and $this->settings['fes_section_schema'])) $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_schema'), 60);

        // Save Schema Data
        $this->factory->action('save_post', array($this, 'save_event'));

        // Print Schema
        $this->factory->action('mec_schema', array($this, 'schema'), 10);
        $this->factory->filter('mec_schema_text', array($this, 'schema_text'), 10, 2);
    }
    
    /**
     * Show location meta box
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_schema($post)
    {
        $event_status = get_post_meta($post->ID, 'mec_event_status', true);
        if(!trim($event_status)) $event_status = 'EventScheduled';

        $moved_online_link = get_post_meta($post->ID, 'mec_moved_online_link', true);
        $cancelled_reason = get_post_meta($post->ID, 'mec_cancelled_reason', true);
        $display_cancellation_reason_in_single_page = get_post_meta($post->ID, 'mec_display_cancellation_reason_in_single_page', true);
        ?>
        <div class="mec-meta-box-fields mec-event-tab-content" id="mec-schema">
            <h4><?php echo __('SEO Schema', 'modern-events-calendar-lite'); ?></h4>
            <p><?php _e("Following statuses are for informing search engines (Google, bing, etc) about your events so they can manage your events better. Therefore you can use these statuses to be more Search Engine Friendly.", 'modern-events-calendar-lite'); ?></p>

			<div class="mec-form-row">
                <label>
                    <input class="mec-schema-event-status" type="radio" name="mec[event_status]" value="EventScheduled" <?php echo ($event_status == 'EventScheduled' ? 'checked' : ''); ?>>
                    <?php _e('Scheduled', 'modern-events-calendar-lite'); ?>
                </label>
                <p class="description"><?php _e('For active events!', 'modern-events-calendar-lite'); ?></p>
			</div>
            <div class="mec-form-row">
                <label>
                    <input class="mec-schema-event-status" type="radio" name="mec[event_status]" value="EventPostponed" <?php echo ($event_status == 'EventPostponed' ? 'checked' : ''); ?>>
                    <?php _e('Postponed', 'modern-events-calendar-lite'); ?>
                </label>
                <p class="description"><?php _e('If you postponed an event then you can use this status!', 'modern-events-calendar-lite'); ?></p>
            </div>
            <div class="mec-form-row">
                <label>
                    <input class="mec-schema-event-status" type="radio" name="mec[event_status]" value="EventCancelled" <?php echo ($event_status == 'EventCancelled' ? 'checked' : ''); ?>>
                    <?php _e('Cancelled', 'modern-events-calendar-lite'); ?>
                </label>
                <p class="description"><?php _e('If you cancelled an event then you should select this status!', 'modern-events-calendar-lite'); ?></p>
            </div>
            <div id="mec_cancelled_reason_wrapper" class="event-status-schema" <?php echo ($event_status == 'EventCancelled' ? '' : 'style="display: none;"'); ?>>
                <div class="mec-form-row">
                    <label class="mec-col-2" for="mec_cancelled_reason"><?php _e('Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-9" type="text" id="mec_cancelled_reason" name="mec[cancelled_reason]" value="<?php echo $cancelled_reason; ?>" placeholder="<?php esc_html_e('Please write your reasons here', 'modern-events-calendar-lite'); ?>">
                </div>
                <div>
                    <p class="description"><?php _e('This will be displayed in Single Event and Shortcode/Calendar Pages', 'modern-events-calendar-lite'); ?></p>
                </div>
                <div class="mec-form-row">
                    <input
                        <?php
                        if (isset($display_cancellation_reason_in_single_page) and $display_cancellation_reason_in_single_page == true) {
                            echo 'checked="checked"';
                        }
                        ?>
                            type="checkbox" name="mec[display_cancellation_reason_in_single_page]" id="mec_display_cancellation_reason_in_single_page" value="1"/><label
                            for="mec_display_cancellation_reason_in_single_page"><?php _e('Display in single event page', 'modern-events-calendar-lite'); ?></label>
                </div>
            </div>
            <div class="mec-form-row">
                <label>
                    <input class="mec-schema-event-status" type="radio" name="mec[event_status]" value="EventMovedOnline" <?php echo ($event_status == 'EventMovedOnline' ? 'checked' : ''); ?>>
                    <?php _e('Moved Online', 'modern-events-calendar-lite'); ?>
                </label>
                <p class="description"><?php _e('For the events that moved online!', 'modern-events-calendar-lite'); ?></p>
            </div>
            <div id="mec_moved_online_link_wrapper" class="event-status-schema" <?php echo ($event_status == 'EventMovedOnline' ? '' : 'style="display: none;"'); ?>>
                <div class="mec-form-row">
                    <label class="mec-col-2" for="mec_moved_online_link"><?php _e('Online Link', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-9" type="url" id="mec_moved_online_link" name="mec[moved_online_link]" value="<?php echo $moved_online_link; ?>" placeholder="https://online-platform.com/event-id">
                </div>
                <div>
                    <p class="description"><?php _e('Link to join online event. If you leave it empty event link will be used.', 'modern-events-calendar-lite'); ?></p>
                </div>
            </div>
        </div>
        <script>
        jQuery(document).ready(function()
        {
            jQuery('input.mec-schema-event-status').on('change', function()
            {
                var value = jQuery(this).val();
                if(value === 'EventMovedOnline')
                {
                    jQuery('#mec_moved_online_link_wrapper').show();
                    jQuery('#mec_cancelled_reason_wrapper').hide();
                }
                else if(value === 'EventCancelled')
                {
                    jQuery('#mec_moved_online_link_wrapper').hide();
                    jQuery('#mec_cancelled_reason_wrapper').show();
                }
                else
                {
                    jQuery('#mec_moved_online_link_wrapper').hide();
                    jQuery('#mec_cancelled_reason_wrapper').hide();
                } 
            });
        });
        </script>
    <?php
    }
    
    /**
     * Save event schema data
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return boolean
     */
    public function save_event($post_id)
    {
        // Check if our nonce is set.
        if(!isset($_POST['mec_event_nonce'])) return false;

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['mec_event_nonce']), 'mec_event_data')) return false;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if(defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) return false;

        // Get Modern Events Calendar Data
        $_mec = isset($_POST['mec']) ? $_POST['mec'] : array();

        $event_status = isset($_mec['event_status']) ? sanitize_text_field($_mec['event_status']) : 'EventScheduled';
        if(!in_array($event_status, array('EventScheduled', 'EventPostponed', 'EventCancelled', 'EventMovedOnline'))) $event_status = 'EventScheduled';

        update_post_meta($post_id, 'mec_event_status', $event_status);

        $moved_online_link = (isset($_mec['moved_online_link']) and filter_var($_mec['moved_online_link'], FILTER_VALIDATE_URL)) ? esc_url($_mec['moved_online_link']) : '';
        update_post_meta($post_id, 'mec_moved_online_link', $moved_online_link);

        $cancelled_reason = (isset($_mec['cancelled_reason']) and !empty($_mec['cancelled_reason'])) ? esc_html($_mec['cancelled_reason']) : '';
        update_post_meta($post_id, 'mec_cancelled_reason', $cancelled_reason);

        $display_cancellation_reason_in_single_page = (isset($_mec['display_cancellation_reason_in_single_page']) and !empty($_mec['display_cancellation_reason_in_single_page'])) ? true : false;
        update_post_meta($post_id, 'mec_display_cancellation_reason_in_single_page', $display_cancellation_reason_in_single_page);

        return true;
    }

    public function schema($event)
    {
        $status = isset($this->settings['schema']) ? $this->settings['schema'] : 0;
        if(!$status) return;

        $speakers = array();
        if(isset($event->data->speakers) and is_array($event->data->speakers) and count($event->data->speakers))
        {
            foreach($event->data->speakers as $key => $value)
            {
                $speakers[] = array(
                    "@type" 	=> "Person",
                    "name"		=> $value['name'],
                    "image"		=> $value['thumbnail'],
                    "sameAs"	=> $value['facebook'],
                );
            }
        }

        $start_timestamp = (isset($event->data->time['start_timestamp']) ? $event->data->time['start_timestamp'] : (isset($event->date['start']['timestamp']) ? $event->date['start']['timestamp'] : strtotime($event->date['start']['date'])));

        // All Params
        $params = MEC_feature_occurrences::param($event->ID, $start_timestamp, '*');

        $event_status = (isset($event->data->meta['mec_event_status']) and trim($event->data->meta['mec_event_status'])) ? $event->data->meta['mec_event_status'] : 'EventScheduled';
        $event_status = (isset($params['event_status']) and trim($params['event_status']) != '') ? $params['event_status'] : $event_status;

        if(!in_array($event_status, array('EventScheduled', 'EventPostponed', 'EventCancelled', 'EventMovedOnline'))) $event_status = 'EventScheduled';

        $cost = isset($event->data->meta['mec_cost']) ? preg_replace("/[^0-9.]/", '', $event->data->meta['mec_cost']) : '';
        $cost = (isset($params['cost']) and trim($params['cost']) != '') ? preg_replace("/[^0-9.]/", '', $params['cost']) : $cost;

        $location_id = $this->main->get_master_location_id($event);
        $location = ($location_id ? $this->main->get_location_data($location_id) : array());

        $event_link = $this->main->get_event_date_permalink($event, $event->date['start']['date']);
        $soldout = $this->main->is_soldout($event, $event->date);

        $organizer_id = $this->main->get_master_organizer_id($event);
        $organizer = ($organizer_id ? $this->main->get_organizer_data($organizer_id) : array());

        $moved_online_link = (isset($event->data->meta['mec_moved_online_link']) and trim($event->data->meta['mec_moved_online_link'])) ? $event->data->meta['mec_moved_online_link'] : '';
        $moved_online_link = (isset($params['moved_online_link']) and trim($params['moved_online_link']) != '') ? $params['moved_online_link'] : $moved_online_link;
        ?>
        <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Event",
            "eventStatus": "https://schema.org/<?php echo $event_status; ?>",
            "startDate": "<?php echo $event->date['start']['date']; ?>",
            "endDate": "<?php echo $event->date['end']['date']; ?>",
            "eventAttendanceMode": "<?php echo ($event_status === 'EventMovedOnline' ? "https://schema.org/OnlineEventAttendanceMode" : "https://schema.org/OfflineEventAttendanceMode"); ?>",
            "location":
            {
                "@type": "<?php echo (($event_status === 'EventMovedOnline') ? 'VirtualLocation' : 'Place'); ?>",
                <?php if($event_status === 'EventMovedOnline'): ?>
                "url": "<?php echo (trim($moved_online_link) ? esc_url($moved_online_link) : esc_url($event_link)); ?>"
                <?php else: ?>
                "name": "<?php echo (isset($location['name']) ? $location['name'] : ''); ?>",
                "image": "<?php echo (isset($location['thumbnail']) ? esc_url($location['thumbnail'] ) : ''); ?>",
                "address": "<?php echo (isset($location['address']) ? $location['address'] : ''); ?>"
                <?php endif; ?>
            },
            "organizer":
            {
                "@type": "Person",
                "name": "<?php echo (isset($organizer['name']) ? $organizer['name'] : ''); ?>",
                "url": "<?php echo (isset($organizer['url']) ? esc_url($organizer['url']) : ''); ?>"
            },
            "offers":
            {
                "url": "<?php echo $event->data->permalink; ?>",
                "price": "<?php echo $cost ?>",
                "priceCurrency": "<?php echo isset($this->settings['currency']) ? $this->settings['currency'] : ''; ?>",
                "availability": "<?php echo ($soldout ? "https://schema.org/SoldOut" : "https://schema.org/InStock"); ?>",
                "validFrom": "<?php echo date('Y-m-d\TH:i', strtotime($event->date['start']['date'])); ?>"
            },
            "performer": <?php echo (count($speakers) ? json_encode($speakers) : '""'); ?>,
            "description": "<?php echo esc_html(preg_replace('/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '<div class="figure">$1</div>', preg_replace('/\s/u', ' ', $event->data->post->post_content))); ?>",
            "image": "<?php echo !empty($event->data->featured_image['full']) ? esc_html($event->data->featured_image['full']) : '' ; ?>",
            "name": "<?php esc_html_e($event->data->title); ?>",
            "url": "<?php echo $event_link; ?>"
        }
        </script>
        <?php
    }

    public function schema_text($text, $event)
    {
        ob_start();
        do_action('mec_schema', $event);
        return ob_get_clean();
    }
}