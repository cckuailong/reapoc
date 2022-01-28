<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_main $this */

// MEC Settings
$settings = $this->get_settings();

// BuddyPress integration is disabled
if(!isset($settings['bp_status']) or (isset($settings['bp_status']) and !$settings['bp_status'])) return;
        
// Attendees Module is disabled
if(!isset($settings['bp_attendees_module']) or (isset($settings['bp_attendees_module']) and !$settings['bp_attendees_module'])) return;

// BuddyPress is not installed or activated
if(!function_exists('bp_activity_add')) return;

$date = $event->date;
$timestamp = (isset($date['start']) and isset($date['start']['timestamp'])) ? $date['start']['timestamp'] : current_time('timestamp');

$limit = isset($settings['bp_attendees_module_limit']) ? $settings['bp_attendees_module_limit'] : 30;
$bookings = $this->get_bookings($event->data->ID, $timestamp, $limit);

// Book Library
$book = $this->getBook();

// Start Date belongs to future but booking module cannot show so return without any output
if(!$this->can_show_booking_module($event) and $timestamp > time()) return;

$attendees = array();
foreach($bookings as $booking)
{
    if(!isset($attendees[$booking->post_author])) $attendees[$booking->post_author] = array();
    $attendees[$booking->post_author][] = $booking->ID;
}

// MEC User
$u = $this->getUser();
?>
<div class="mec-attendees-list-details mec-frontbox" id="mec_attendees_list_details">
    <h3 class="mec-attendees-list mec-frontbox-title"><?php _e('Event Attendees', 'modern-events-calendar-lite'); ?></h3>
    <?php if(!count($attendees)): ?>
    <p><?php _e('No attendee found! Be the first one to book!', 'modern-events-calendar-lite'); ?></p>
    <?php else: ?>
    <ul>
        <?php do_action('mec_attendeed_hook', $attendees); foreach($attendees as $attendee_id=>$attendee_bookings): ?>
        <li>
            <div class="mec-attendee-avatar">
                <a href="<?php echo bp_core_get_user_domain($attendee_id); ?>" title="<?php echo bp_core_get_user_displayname($attendee_id); ?>">
                    <?php echo bp_core_fetch_avatar(array('item_id'=>$attendee_id, 'type'=>'thumb')); ?>
                </a>
            </div>
            <?php
                $link = bp_core_get_userlink($attendee_id, false, true);
                $user = $u->get($attendee_id);

                $name = $user->display_name;
                if(!$name or is_email($name)) $name = trim($user->first_name.' '.$user->last_name);

                $total_attendees = 0;
                foreach($attendee_bookings as $booking_id) $total_attendees += $book->get_total_attendees($booking_id);
            ?>
            <div class="mec-attendee-profile-link">
                <?php echo '<a href="'.$link.'">'.$name.'</a>' . '<span class="mec-attendee-profile-ticket-number mec-bg-color">'. $total_attendees .'</span>' . '<span class="mec-color-hover"> ' . esc_html__( 'tickets' , 'modern-events-calendar-lite' ) . '<i class="mec-sl-arrow-down"></i></span>' ; ?>
            </div>

            <!-- MEC BuddyPress Integration Attendees Modules -->
            <div class="mec-attendees-toggle mec-util-hidden">
            <?php
                $un_attendees = array();
                foreach($attendee_bookings as $booking_id)
                {
                    $mec_attendees = get_post_meta($booking_id, 'mec_attendees', true);
                    foreach($mec_attendees as $mec_attendee_key => $mec_attendee)
                    {
                        if(!is_numeric($mec_attendee_key)) continue;

                        $email = isset($mec_attendee['email']) ? $mec_attendee['email'] : NULL;
                        if(!$email) continue;

                        if(!isset($un_attendees[$email])) $un_attendees[$email] = $mec_attendee;
                        else $un_attendees[$email]['count'] += $mec_attendee['count'];
                    }
                }

                // For Display Sorting Output.
                foreach($un_attendees as $mec_attendee)
                {
                    ?>
                    <div class="mec-attendees-item clearfix">
                        <?php
                            echo '<div class="mec-attendee-avatar-sec">'. get_avatar($mec_attendee['email'], '50') .'</div>';
                            echo '<div class="mec-attendee-profile-name-sec">'. (!is_email($mec_attendee['name']) ? $mec_attendee['name'] : 'N/A') .'</div>';
                            echo '<span class="mec-attendee-profile-ticket-sec">'. sprintf(_n('%s ticket', '%s tickets', $mec_attendee['count'], 'modern-events-calendar-lite'), $mec_attendee['count']) . '</span>';
                        ?>
                    </div>
                    <?php
                }
            ?>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>