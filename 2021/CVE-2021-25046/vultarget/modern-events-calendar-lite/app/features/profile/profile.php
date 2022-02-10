<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_profile $this */
/** @var array $atts */

$hide_canceleds = (isset($atts['hide-canceleds']) and $atts['hide-canceleds']) ? true : false;
$upcomings = (isset($atts['show-upcomings']) and $atts['show-upcomings']) ? true : false;

// Date & Time Format
$datetime_format = get_option('date_format').' '.get_option('time_format');

// MEC Render
$render = $this->getRender();

// Query
$q = array(
    'post_type'=>$this->PT,
    'author'=>get_current_user_id(),
    'posts_per_page'=>'-1',
    'post_status'=>array('pending', 'draft', 'future', 'publish'),
    'meta_query'=>array(),
    'date_query'=>array(),
    'orderby'=>'post_date',
    'order'=>'DESC',
);

// Hide Canceled Bookings
if($hide_canceleds)
{
    $q['meta_query'][] = array('key'=>'mec_verified', 'value'=>'-1', 'compare'=>'!=');
}

// Show Only Upcoming Bookings
if($upcomings)
{
    $q['date_query'] = array(
        'after' => current_time('Y-m-d H:i:s'),
    );
}

// The Query
$query = new WP_Query($q);

$id = 1;
?>
<div class="mec-profile">
    <?php if($query->have_posts()): ?>
    <table class="mec-profile-bookings">
        <tr>
            <td>
                <?php esc_html_e('#' , 'modern-events-calendar-lite'); ?>
            </td>
            <td>
                <?php esc_html_e('Event' , 'modern-events-calendar-lite'); ?>
            </td>
            <td>
                <?php esc_html_e('Date' , 'modern-events-calendar-lite'); ?>
            </td>
            <td>
                <?php esc_html_e('Status' , 'modern-events-calendar-lite'); ?>
            </td>
            <td>
                <?php esc_html_e('Attendees' , 'modern-events-calendar-lite'); ?>
            </td>
            <td>
                <?php esc_html_e('Invoice' , 'modern-events-calendar-lite'); ?>
            </td>
            <td>
                <?php esc_html_e('Map' , 'modern-events-calendar-lite'); ?>
            </td>
            <?php do_action( 'mec_profile_event_detail_header' ); ?>
            <td>
                <?php esc_html_e('Cancel' , 'modern-events-calendar-lite'); ?>
            </td>
        </tr>
        <?php while($query->have_posts()): $query->the_post();
            $ID = get_the_ID();
            $book_id = $ID;
            $transaction_id = $this->book->get_transaction_id_book_id($ID);
            $event_id = get_post_meta($ID, 'mec_event_id', true);
            $ticket_ids = get_post_meta($ID, 'mec_ticket_id', true);

            $confirmed = get_post_meta($ID, 'mec_confirmed', true);

            if($confirmed == '1') $status_class = 'mec-book-confirmed';
            elseif($confirmed == '-1') $status_class = 'mec-book-rejected';
            else $status_class = 'mec-book-pending';

            $transaction = $this->book->get_transaction($transaction_id);
            $timestamps = explode(':', get_post_meta($ID, 'mec_date', true));

            $start_time = $timestamps[0];
            $end_time = $timestamps[1];

            $booking_options = get_post_meta($event_id, 'mec_booking', true);
            $bookings_all_occurrences = isset($booking_options['bookings_all_occurrences']) ? $booking_options['bookings_all_occurrences'] : 0;

            if($bookings_all_occurrences)
            {
                $dates = $render->dates($event_id, NULL, 1, NULL);
                if(is_array($dates) and count($dates) and isset($dates[0]))
                {
                    $start_time = ((isset($dates[0]['start']) and isset($dates[0]['start']['timestamp'])) ? $dates[0]['start']['timestamp'] : 0);
                    $end_time = ((isset($dates[0]['end']) and isset($dates[0]['end']['timestamp'])) ? $dates[0]['end']['timestamp'] : 0);
                }
            }

            // Check If Event Exist
            $db = $this->getDB();
            $check_event_exist = $db->select("SELECT `ID` FROM `#__posts` WHERE `ID`={$event_id}", 'loadResult');

            $event = trim($check_event_exist) ? $render->data($event_id) : array();
        ?>
        <tr id="mec_profile_booking_<?php echo $ID; ?>">
            <td>
                <span class="mec-event-id"><?php echo $id; ?></span>
            </td>
            <td>
                <?php if(!isset($event->ID) or !isset($event->title)) : ?>
                <span class="mec-event-title"><?php _e('N/A', 'modern-events-calendar-lite'); ?></span>
                <?php else : ?>
                <a class="mec-event-title" href="<?php echo get_the_permalink($event->ID); ?>"><?php echo $event->title; ?></a>
                <?php do_action('mec_profile_event_title', $event, $transaction); ?>
                <?php endif; ?>
            </td>
            <td>
                 <span class="mec-event-date">
                    <div class="mec-tooltip">
                        <div class="box">
                            <?php echo trim(date($datetime_format, $start_time).' - '.date($datetime_format, $end_time), '- '); ?>
                        </div>
                        <i class="mec-sl-calendar"></i>
                    </div>
                </span>
            </td>
            <td>
                <span class="mec-event-status <?php echo $status_class;?>">
                    <div class="mec-tooltip">
                        <div class="box">
                            <?php echo $this->main->get_confirmation_label($confirmed); ?>
                        </div>
                        <i class="mec-sl-layers"></i>
                    </div>
                </span>
            </td>
            <td>
                <a class="mec-booking-number-of-attendees"  href="#mec_profile_booking_<?php echo $ID; ?>">
                    <?php echo sprintf(__('<i class="mec-sl-eye"></i> %s', 'modern-events-calendar-lite'), ((is_array($transaction) and isset($transaction['tickets'])) ? count($transaction['tickets']) : 0)); ?>
                </a>
            </td>
            <td>
                <span class="mec-profile-bookings-view-invoice"><a target="_blank" href="<?php echo $this->book->get_invoice_link($transaction_id); ?>"><i class="mec-sl-cloud-download"></i></a></span>
            </td>
            <td>
                <?php
                if(isset($event->ID))
                {
                    $location_id = $this->main->get_master_location_id($event);
                    $location_latitude = isset($event->locations[$location_id]['latitude']) ? $event->locations[$location_id]['latitude'] : NULL;
                    $location_longitude = isset($event->locations[$location_id]['longitude']) ? $event->locations[$location_id]['longitude'] : NULL;
                }
                ?>
                <span class="mec-profile-bookings-view-google-map">
                    <?php if((isset($location_latitude) and $location_latitude) and (isset($location_longitude) and $location_longitude)): ?>
                    <a target="_blank" href="<?php echo "https://www.google.com/maps?q={$location_latitude},{$location_longitude}"; ?>"><i class="mec-sl-map"></i></a>
                    <?php else: ?>
                    <i class="mec-sl-question mec-profile-no-location"></i>
                    <?php endif; ?>
                </span>
            </td>
            <?php do_action( 'mec_profile_event_detail', $event->ID, $book_id, $event ); ?>
            <td>
                <?php $mec_verified = get_post_meta($ID, 'mec_verified', true); ?>
                <span class="mec-profile-bookings-cancelation">
                    <?php if(intval($mec_verified) != -1): ?>
                    <a href="<?php echo trim(get_permalink($event_id), '/').'/cancel/'.get_post_meta($ID, 'mec_cancellation_key', true).'/'; ?>"><i class="mec-fa-calendar-times-o"></i></a>
                    <?php else: ?>
                    <i class="mec-sl-close mec-profile-cancel-booking"></i>
                    <?php endif; ?>
                </span>
            </td>
        </tr>
        <div id="mec_profile_booking_<?php echo $ID; ?>" class="mec-booking-attendees lity-hide">
            <div class="mec-booking-attendees-wrapper">
                <div class="mec-booking-attendees-head">
                    <span class="mec-booking-attendee-id">
                        <?php esc_html_e('#' , 'modern-events-calendar-lite'); ?>
                    </span>
                    <span class="mec-booking-attendee-name">
                        <?php esc_html_e('Name' , 'modern-events-calendar-lite'); ?>
                    </span>
                    <span class="mec-booking-attendee-email">
                        <?php esc_html_e('Email' , 'modern-events-calendar-lite'); ?>
                    </span>
                    <span class="mec-booking-attendee-ticket">
                        <?php esc_html_e('Ticket' , 'modern-events-calendar-lite'); ?>
                    </span>
                    <span class="mec-booking-attendee-ticket-variations">
                        <?php esc_html_e('Variations' , 'modern-events-calendar-lite'); ?>
                    </span>
                </div>
                <?php
                // Attendees
                if(isset($transaction['tickets']) and is_array($transaction['tickets']) and count($transaction['tickets']))
                {
                    $person_id = 1;
                    foreach($transaction['tickets'] as $attendee_i => $attendee)
                    {
                        if(!is_numeric($attendee_i)) continue;

                        echo '<div class="mec-booking-attendees-head-content">';
                        echo '<span class="mec-booking-attendee-id">'.$person_id.'</span>';
                        echo '<span class="mec-booking-attendee-name">'. ( isset($attendee['_name']) ? $attendee['_name'] : $attendee['name'] ).'</span>';
                        echo '<span class="mec-booking-attendee-email">'.$attendee['email'].'</span>';
                        echo '<span class="mec-booking-attendee-ticket">'.((isset($event->tickets[$attendee['id']]) ? $event->tickets[$attendee['id']]['name'] : '').' '.(isset($event->tickets[$attendee['id']]) ? $event->tickets[$attendee['id']]['price_label'] : '')).'</span>';

                        // Ticket Variations
                        echo '<span class="mec-booking-attendee-ticket-variations">';
                        if(isset($attendee['variations']) and is_array($attendee['variations']) and count($attendee['variations']))
                        {
                            $ticket_variations = $this->main->ticket_variations((trim($check_event_exist) ? $event_id : NULL), $attendee['id']);
                            foreach($attendee['variations'] as $variation_id=>$variation_count)
                            {
                                if(!$variation_count or ($variation_count and $variation_count < 0)) continue;

                                $variation_title = (isset($ticket_variations[$variation_id]) and isset($ticket_variations[$variation_id]['title'])) ? $ticket_variations[$variation_id]['title'] : '';
                                if(!trim($variation_title)) continue;

                                echo '<span class="mec-booking-attendee-ticket-variations-title">'.' + '.$variation_title.' ('.$variation_count.')'.'</span>';
                            }
                        }
                        else
                        {
                            echo '-';
                        }

                        echo '</span>';
                        $person_id++;
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
        <?php $id++; endwhile; wp_reset_postdata(); // Restore original Post Data ?>
    </table>
    <?php else: ?>
    <p><?php echo __('No bookings found!', 'modern-events-calendar-lite'); ?></p>
    <?php endif; ?>
</div>
<script>
jQuery( ".mec-booking-number-of-attendees" ).on('click',function(e)
{
    e.preventDefault();
    var attendee_id =  jQuery(this).attr('href');
    lity(attendee_id);
});
</script>