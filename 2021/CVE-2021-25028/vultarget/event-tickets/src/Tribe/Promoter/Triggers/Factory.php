<?php


namespace Tribe\Tickets\Promoter\Triggers;


use Tribe\Tickets\Promoter\Triggers\Builders\Attendee_Trigger;
use Tribe\Tickets\Promoter\Triggers\Contracts\Attendee_Model;
use Tribe\Tickets\Promoter\Triggers\Contracts\Triggered;
use Tribe\Tickets\Promoter\Triggers\Models\Attendee as AttendeeModel;
use Tribe__Tickets__Tickets;

class Factory {
	/**
	 * Create new triggers based on the different type of hooks.
	 *
	 * @since 4.12.3
	 */
	public function hook() {
		add_action( 'tribe_tickets_promoter_trigger_attendee', [ $this, 'build_attendee' ], 10, 3 );
	}

	/**
	 * When an action `tribe_tickets_promoter_trigger_attendee` is fired, react with an attendee trigger.
	 *
	 * @since 4.12.3
	 *
	 * @param string                  $type     The type of trigger message.
	 * @param Attendee_Model          $attendee The representation of the attendee.
	 * @param Tribe__Tickets__Tickets $ticket   The ticket provider instance.
	 */
	public function build_attendee( $type, Attendee_Model $attendee, Tribe__Tickets__Tickets $ticket ) {
		/**
		 * Create a new action to deliver a trigger action
		 *
		 * @since 4.12.3
		 *
		 * @param Triggered $trigger The type of trigger fired.
		 */
		do_action( 'tribe_tickets_promoter_trigger', new Attendee_Trigger( $type, $attendee, $ticket ) );
	}
}