<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC update table class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_updateTable extends MEC_base
{
    public $main;
    public $settings;
    public $book;
    public $factory;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Main
        $this->main = $this->getMain();

        // MEC Settings
        $this->settings = $this->main->get_settings();

        // Import MEC Book
        $this->book = $this->getBook();

        // Import MEC Factory
        $this->factory = $this->getFactory();
    }

    /**
     * Initialize update table
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        if(!is_admin()) return;

        // Run the Update Function
        $this->factory->action('wp_loaded', array($this, 'calculate'));
    }

    public function calculate()
    {
        $postType = $this->main->get_book_post_type();
        $bookings = get_posts([
            'post_type' => $postType,
            'numberposts' => -1,
            'post_status' => 'publish',
        ]);

        if($bookings)
        {
            foreach($bookings as $booking)
            {
                if(metadata_exists('post', $booking->ID, 'mec_attendees_price')) continue;

                $eventID = $booking->mec_event_id;
                $transactionID = get_post_meta($booking->ID, 'mec_transaction_id', true);
                $transaction = get_option($transactionID, []);
                $tickets = $transaction ? $transaction['tickets'] : [];
                $discount = $transaction && isset($transaction['discount']) ? $transaction['discount'] : 0;
                $eventTickets = get_post_meta($eventID, 'mec_tickets', true);

                $attendeesMeta = get_post_meta($booking->ID, 'mec_attendees', true) ? get_post_meta($booking->ID, 'mec_attendees', true) : (get_post_meta($booking->ID, 'mec_attendee', true) ? get_post_meta($booking->ID, 'mec_attendee', true) : []);
                $mecAttendeesPrice = [];

                if($attendeesMeta)
                {
                    $attendeesCount = 0;
                    foreach($attendeesMeta as $attendeeMetaKey => $attendeeMeta)
                    {
                        if(!isset($attendeeMeta['id'])) continue;

                        $tickets[$attendeeMetaKey]['price'] = isset($eventTickets[$attendeeMeta['id']]['price']) ? (float)$eventTickets[$attendeeMeta['id']]['price'] : 0;
                        if(isset($attendeeMeta['variations']) and is_array($attendeeMeta['variations']) and count($attendeeMeta['variations']))
                        {
                            $ticketVariations = $this->main->ticket_variations($eventID, $attendeeMeta['id']);
                            if(isset($this->settings['ticket_variations_status']) and $this->settings['ticket_variations_status'] and count($ticketVariations))
                            {
                                foreach($ticketVariations as $ticketVariationId => $ticketVariation)
                                {
                                    if(!is_numeric($ticketVariationId) or !isset($ticketVariation['title']) or (isset($ticketVariation['title']) and !trim($ticketVariation['title']))) continue;

                                    if($attendeeMeta['variations'][$ticketVariationId])
                                    {
                                        $tickets[$attendeeMetaKey]['price'] += $attendeeMeta['variations'][$ticketVariationId]*$ticketVariation['price'];
                                    }
                                }
                            }
                        }
                    }

                    $attendeeFeesAmount = $this->getFeesAmount($eventID, $tickets)/count($attendeesMeta);
                    foreach($attendeesMeta as $attendeeMetaKey => $attendeeMeta)
                    {
                        $tickets[$attendeeMetaKey]['price'] += (float)$attendeeFeesAmount;
                        if($tickets[$attendeeMetaKey]['price'] && $tickets[$attendeeMetaKey]['price'] !== 0)
                        {
                            ++$attendeesCount;
                        }
                    }

                    $attendeeDiscount = $attendeesCount ? $discount/$attendeesCount : 0;
                    foreach($attendeesMeta as $attendeeMetaKey => $attendeeMeta)
                    {
                        $mecAttendeesPrice[$attendeeMetaKey] = $tickets[$attendeeMetaKey]['price'] - $attendeeDiscount;
                    }
                }

                update_post_meta($booking->ID, 'mec_attendees_price', $mecAttendeesPrice);
            }
        }
    }

    /**
     * Get Fees Price
     *
     * @since   1.0.0
     */
    public function getFeesAmount($eventID = null, $tickets = [])
    {
        $total = 0;
        $totalFeesAmount = 0;
        $totalTicketsCount = 0;

        foreach($tickets as $ticketID => $ticket)
        {
            $total += (float)$ticket['price'];
            ++$totalTicketsCount;
        }

        if(isset($this->settings['taxes_fees_status']) and $this->settings['taxes_fees_status'])
        {
            $fees = $this->book->get_fees($eventID);
            foreach($fees as $key => $fee)
            {
                if(!is_numeric($key)) continue;

                $feeAmount = 0;
                if($fee['type'] == 'percent') $feeAmount = (($total)*$fee['amount'])/100;
                elseif($fee['type'] == 'amount') $feeAmount = ($totalTicketsCount*$fee['amount']);
                elseif($fee['type'] == 'amount_per_booking') $feeAmount = $fee['amount'];

                $totalFeesAmount += $feeAmount;
            }
        }

        return $totalFeesAmount;
    }
}