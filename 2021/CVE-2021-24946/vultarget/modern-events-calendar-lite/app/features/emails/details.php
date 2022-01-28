<?php
/** no direct access **/
defined('MECEXEC') or die();

$time = get_post_meta($post->ID, 'mec_time', true);
if($time == '') $time = 1;

$type = get_post_meta($post->ID, 'mec_type', true);
if($type == '') $type = 'day';

$afterbefore = get_post_meta($post->ID, 'mec_afterbefore', true);
if($afterbefore == '') $afterbefore = 'before';

$all = get_post_meta($post->ID, 'mec_all', true);
if($all == '') $all = 1;

$events = get_post_meta($post->ID, 'mec_events', true);
if(!is_array($events)) $events = array();

// Upcoming Events
$upcoming_event_ids = $this->main->get_upcoming_event_ids();
?>
<div class="mec-form-row mec-email-metabox mec-details">
    <h3><?php esc_html_e('Time', 'modern-events-calendar-lite'); ?></h3>
    <div>
        <span><?php esc_html_e('Send it', 'modern-events-calendar-lite'); ?></span>
        <input type="number" min="1" name="mec[time]" placeholder="2" value="<?php echo esc_attr($time); ?>" title="<?php esc_attr_e('Time', 'modern-events-calendar-lite'); ?>" required>
        <select name="mec[type]" title="<?php esc_attr_e('Time Type', 'modern-events-calendar-lite'); ?>">
            <option value="day" <?php echo ($type === 'day' ? 'selected="selected"' : ''); ?>><?php esc_html_e('Day(s)', 'modern-events-calendar-lite'); ?></option>
            <option value="hour" <?php echo ($type === 'hour' ? 'selected="selected"' : ''); ?>><?php esc_html_e('Hour(s)', 'modern-events-calendar-lite'); ?></option>
            <option value="minute" <?php echo ($type === 'minute' ? 'selected="selected"' : ''); ?>><?php esc_html_e('Minute(s)', 'modern-events-calendar-lite'); ?></option>
        </select>
        <select name="mec[afterbefore]" title="<?php esc_attr_e('After / Before', 'modern-events-calendar-lite'); ?>">
            <option value="before" <?php echo ($afterbefore === 'before' ? 'selected="selected"' : ''); ?>><?php esc_html_e('Before', 'modern-events-calendar-lite'); ?></option>
            <option value="after" <?php echo ($afterbefore === 'after' ? 'selected="selected"' : ''); ?>><?php esc_html_e('After', 'modern-events-calendar-lite'); ?></option>
        </select>
        <span><?php esc_html_e('event occurrence.', 'modern-events-calendar-lite'); ?></span>
    </div>

    <h3><?php esc_html_e('Events', 'modern-events-calendar-lite'); ?></h3>
    <div>
        <input type="hidden" name="mec[all]" value="0">
        <label>
            <input type="checkbox" name="mec[all]" <?php echo ($all ? 'checked' : ''); ?> value="1" onchange="jQuery('#mec_events_container').toggleClass('w-hidden');"> <?php esc_html_e('All Events', 'modern-events-calendar-lite'); ?>
        </label>
        <div id="mec_events_container" class="<?php echo ($all ? 'w-hidden' : ''); ?>">
            <ul>
                <?php foreach($upcoming_event_ids as $upcoming_event_id): $event = get_post($upcoming_event_id); ?>
                <li>
                    <label>
                        <input type="checkbox" name="mec[events][]" value="<?php echo $event->ID; ?>" <?php echo (in_array($event->ID, $events) ? 'checked' : ''); ?>> <?php esc_html_e($event->post_title); ?>
                    </label>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <h3><?php esc_html_e('Placeholders', 'modern-events-calendar-lite'); ?></h3>
    <?php MEC_feature_notifications::display_placeholders(); ?>

    <?php
        // Add a nonce field so we can check for it later.
        wp_nonce_field('mec_email_data', 'mec_email_nonce');
    ?>
</div>