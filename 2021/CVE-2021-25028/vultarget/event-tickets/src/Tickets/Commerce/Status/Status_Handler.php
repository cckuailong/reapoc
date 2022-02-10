<?php

namespace TEC\Tickets\Commerce\Status;

use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Settings;

/**
 * Class Status_Handler
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Status
 */
class Status_Handler extends \tad_DI52_ServiceProvider {
	/**
	 * Statuses registered.
	 *
	 * @since 5.1.9
	 *
	 * @var Status_Interface[]
	 */
	protected $statuses = [];

	/**
	 * Which classes we will load for order statuses by default.
	 *
	 * @since 5.1.9
	 *
	 * @var string[]
	 */
	protected $default_statuses = [
		Created::class,
		Completed::class,
		Denied::class,
		Not_Completed::class,
		Pending::class,
		Refunded::class,
		Reversed::class,
		Undefined::class,
		Voided::class,
	];

	/**
	 * Which status every order will be created with.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $insert_status = Created::class;

	/**
	 * Sets up all the Status instances for the Classes registered in $default_statuses.
	 *
	 * @since 5.1.9
	 */
	public function register() {
		foreach ( $this->default_statuses as $status_class ) {
			// Spawn the new instance.
			$status = new $status_class;

			// Register as a singleton for internal ease of use.
			$this->container->singleton( $status_class, $status );

			// Collect this particular status instance in this class.
			$this->register_status( $status );
		}

		$this->container->singleton( static::class, $this );
	}

	/**
	 * Which status an order will be created with.
	 *
	 * @since 5.1.9
	 *
	 * @return Status_Interface
	 */
	public function get_insert_status() {
		return $this->container->make( $this->insert_status );
	}

	/**
	 * Gets the statuses registered.
	 *
	 * @since 5.1.9
	 *
	 * @return Status_Interface[]
	 */
	public function get_all() {
		return $this->statuses;
	}

	/**
	 * Fetches the first status registered with a given slug.
	 *
	 * @since 5.1.9
	 *
	 * @param string $slug
	 *
	 * @return Status_Interface|null
	 */
	public function get_by_slug( $slug ) {
		foreach ( $this->get_all() as $status ) {
			if ( $status->get_slug() === $slug ) {
				return $status;
			}
		}

		return null;
	}

	/**
	 * Fetches the first status registered with a given wp slug.
	 *
	 * @since 5.1.9
	 *
	 * @param string $slug
	 *
	 * @return Status_Interface
	 */
	public function get_by_wp_slug( $slug ) {
		foreach ( $this->get_all() as $status ) {
			if ( $status->get_wp_slug() === $slug ) {
				return $status;
			}
		}

		return null;
	}

	/**
	 * Fetches the status registered with a given class.
	 *
	 * @since 5.1.9
	 *
	 * @param string $class_name
	 *
	 * @return Status_Interface
	 */
	public function get_by_class( $class_name ) {
		foreach ( $this->get_all() as $status ) {
			$status_class = get_class( $status );

			if ( $status_class === $class_name ) {
				return $status;
			}
		}

		return null;
	}

	/**
	 * Using `wp_list_filter` fetches which Statuses match the flags and operator passed.
	 *
	 * @since 5.1.9
	 *
	 * @param string|array $flags
	 * @param string       $operator
	 *
	 * @return Status_Interface[]
	 */
	public function get_by_flags( $flags, $operator = 'AND' ) {
		$statuses = wp_list_filter( $this->get_all(), (array) $flags, $operator );

		return $statuses;
	}

	/**
	 * Register a given status into the Handler.
	 *
	 * @since 5.1.9
	 *
	 * @param Status_Interface $status Which status we are registering.
	 */
	public function register_status( Status_Interface $status ) {
		$this->statuses[] = $status;
	}

	/**
	 * Registers the post statuses with WordPress.
	 *
	 * @since 5.1.9
	 */
	public function register_order_statuses() {

		$statuses = $this->get_all();

		foreach ( $statuses as $status ) {
			register_post_status(
				$status->get_wp_slug(),
				$status->get_wp_arguments()
			);
		}
	}

	/**
	 * Fires when a post is transitioned from one status to another so that we can make another hook that is namespaced.
	 *
	 * @since 5.1.9
	 *
	 * @param string   $new_status New post status.
	 * @param string   $old_status Old post status.
	 * @param \WP_Post $post       Post object.
	 */
	public function transition_order_post_status_hooks( $new_status, $old_status, $post ) {
		if ( Order::POSTTYPE !== $post->post_type ) {
			return;
		}

		$new_status = $this->get_by_wp_slug( $new_status );
		$old_status = $this->get_by_wp_slug( $old_status );

		if ( ! isset( $new_status ) ) {
			return;
		}

		/**
		 * Fires when a post is transitioned from one status to another.
		 *
		 * @since 5.1.9
		 *
		 * @param Status_Interface      $new_status New post status.
		 * @param Status_Interface|null $old_status Old post status.
		 * @param \WP_Post              $post       Post object.
		 */
		do_action( 'tec_tickets_commerce_order_status_transition', $new_status, $old_status, $post );

		if ( $old_status ) {
			/**
			 * Fires when a post is transitioned from one status to another.
			 *
			 * The dynamic portions of the hook name, `$new_status` and `$old_status`,
			 * refer to the old and new post statuses, respectively.
			 *
			 * @since 5.1.9
			 *
			 * @param Status_Interface      $new_status New post status.
			 * @param Status_Interface|null $old_status Old post status.
			 * @param \WP_Post              $post       Post object.
			 */
			do_action( "tec_tickets_commerce_order_status_{$old_status->get_slug()}_to_{$new_status->get_slug()}", $new_status, $old_status, $post );
		}

		/**
		 * Fires when a post is transitioned from one status to another.
		 *
		 * The dynamic portions of the hook name, `$new_status`, refer to the new post status.
		 *
		 * @since 5.1.9
		 *
		 * @param Status_Interface      $new_status New post status.
		 * @param Status_Interface|null $old_status Old post status.
		 * @param \WP_Post              $post       Post object.
		 */
		do_action( "tec_tickets_commerce_order_status_{$new_status->get_slug()}", $new_status, $old_status, $post );

		$this->trigger_status_hooks_by_flags( $new_status, $old_status, $post );
	}

	/**
	 * When a given order is transitioned from a status to another we will pull all it's flags and trigger a couple of
	 * extra hooks so that all the required actions can be triggered, examples:
	 * - Generating Attendees
	 * - Re-stocking ticket/event
	 * - Throwing a warning
	 * - Handling Email communication
	 *
	 * @since 5.1.9
	 *
	 * @param Status_Interface      $new_status New post status.
	 * @param Status_Interface|null $old_status Old post status.
	 * @param \WP_Post              $post       Post object.
	 */
	public function trigger_status_hooks_by_flags( Status_Interface $new_status, $old_status, $post ) {
		$flags = $new_status->get_flags();

		foreach ( $flags as $flag ) {
			/**
			 * Fires when a post is transitioned from one status to another and contains a given flag.
			 *
			 * The dynamic portions of the hook name, `$flag`, refer to a given flag that this new status contains.
			 *
			 * @since 5.1.9
			 *
			 * @param Status_Interface      $new_status New post status.
			 * @param Status_Interface|null $old_status Old post status.
			 * @param \WP_Post              $post       Post object.
			 */
			do_action( "tec_tickets_commerce_order_status_flag_{$flag}", $new_status, $old_status, $post );

			/**
			 * Fires when a post is transitioned from one status to another and contains a given flag.
			 *
			 * The dynamic portions of the hook name, `$new_status` and `$flag`, refer to the new post status and a
			 * given flag that this new status contains.
			 *
			 * @since 5.1.9
			 *
			 * @param Status_Interface      $new_status New post status.
			 * @param Status_Interface|null $old_status Old post status.
			 * @param \WP_Post              $post       Post object.
			 */
			do_action( "tec_tickets_commerce_order_status_{$new_status->get_slug()}_flag_{$flag}", $new_status, $old_status, $post );
		}
	}

	/**
	 * Gets the status in which we decrease inventory and add an attendee.
	 *
	 * @since 5.1.10
	 *
	 * @return Status_Abstract
	 */
	public function get_inventory_decrease_status() {
		$status = $this->get_by_slug( tribe_get_option( Settings::$option_stock_handling, Pending::SLUG ) );

		if ( ! $status instanceof Status_Abstract ) {
			$status = tribe( Pending::class );
		}

		return $status;
	}

	/**
	 * Whether an order status will mark a transaction as completed one way or another.
	 *
	 * A transaction might be completed because it successfully completed, because it
	 * was refunded or denied.
	 *
	 * @since  5.1.9
	 *
	 * @param string $payment_status
	 *
	 * @return bool
	 */
	public function is_complete_transaction_status( $payment_status ) {
		$statuses = $this->get_by_flags( [ 'count_completed', 'count_refunded' ], 'OR' );
		$statuses = array_map( static function ( $status ) {
			return $status->get_slug();
		}, $statuses );

		return in_array( $payment_status, $statuses, true );

	}

	/**
	 * Whether an order status will mark a transaction as generating revenue or not.
	 *
	 * @since 5.1.9
	 *
	 * @param string $payment_status
	 *
	 * @return bool
	 */
	public function is_revenue_generating_status( $payment_status ) {
		$statuses = $this->get_by_flags( 'count_completed' );
		$statuses = array_map( static function ( $status ) {
			return $status->get_slug();
		}, $statuses );

		return in_array( $payment_status, $statuses, true );
	}
}