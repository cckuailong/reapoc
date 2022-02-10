<?php

namespace TEC\Tickets\Commerce\Flag_Actions;

/**
 * Class Flag_Action_Handler
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Flag_Actions
 */
class Flag_Action_Handler extends \tad_DI52_ServiceProvider {
	/**
	 * Flag Actions registered.
	 *
	 * @since 5.1.9
	 *
	 * @var Flag_Action_Interface[]
	 */
	protected $flag_actions = [];

	/**
	 * Which classes we will load for order flag actions by default.
	 *
	 * @since 5.1.9
	 *
	 * @var string[]
	 */
	protected $default_flag_actions = [
		Generate_Attendees::class,
		Increase_Stock::class,
		Decrease_Stock::class,
		Archive_Attendees::class,
		Backfill_Purchaser::class,
		Send_Email::class,
		Increase_Sales::class,
		Decrease_Sales::class,
		End_Duplicated_Pending_Orders::class,
	];

	/**
	 * Gets the flag actions registered.
	 *
	 * @since 5.1.9
	 *
	 * @return Flag_Action_Interface[]
	 */
	public function get_all() {
		return $this->flag_actions;
	}

	/**
	 * Sets up all the Flag Action instances for the Classes registered in $default_flag_actions.
	 *
	 * @since 5.1.9
	 */
	public function register() {
		foreach ( $this->default_flag_actions as $flag_action_class ) {
			// Spawn the new instance.
			$flag_action = new $flag_action_class;

			// Register as a singleton for internal ease of use.
			$this->container->singleton( $flag_action_class, $flag_action );

			// Collect this particular status instance in this class.
			$this->register_flag_action( $flag_action );
		}

		$this->container->singleton( static::class, $this );
	}

	/**
	 * Register a given flag action into the Handler, and hook the handling to WP.
	 *
	 * @since 5.1.9
	 *
	 * @param Flag_Action_Interface $flag_action Which flag action we are registering.
	 */
	public function register_flag_action( Flag_Action_Interface $flag_action ) {
		$this->flag_actions[] = $flag_action;
		$flag_action->hook();
	}
}