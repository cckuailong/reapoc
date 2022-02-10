<?php

namespace MEC\Tickets;

use MEC;

class Tickets extends MEC\Singleton{

    /**
     * @param int $event_id
     * @return array
     */
    public function get_event_tickets($event_id){

        $tickets = (array)get_post_meta($event_id, 'mec_tickets', true);

        foreach($tickets as $k => $ticket){

            $tickets[$k] = new Ticket($ticket);
        }

        return $tickets;
    }

    /**
     * @param int $event_id
     * @param array $tickets //TODO: Ticket[]
     * @return void
     */
    public function update_event_tickets($event_id,$tickets){
        
        update_post_meta($event_id, 'mec_tickets', $tickets);
    }

    /**
     * @param int $event_id
     * @param int $ticket_id
     * @return array|null //TODO: Ticket
     */
    public function get_ticket($event_id,$ticket_id){

        if(!$event_id){

            return null;
        }

        $tickets = $this->get_event_tickets($event_id);

        return isset($tickets[$ticket_id]) ? $tickets[$ticket_id] : null;
    }
}