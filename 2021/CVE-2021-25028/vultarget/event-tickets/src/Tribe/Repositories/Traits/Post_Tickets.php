<?php
/**
 * Post tickets trait that contains all of the ORM filters that can be used for any repository.
 *
 * @since   4.12.1
 *
 * @package Tribe\Tickets\Repositories\Traits
 */

namespace Tribe\Tickets\Repositories\Traits;

use Tribe__Repository;
use Tribe__Repository__Usage_Error;
use Tribe__Repository__Void_Query_Exception;
use Tribe__Utils__Array;

/**
 * Class Post_Tickets
 *
 * @since 4.12.1
 */
trait Post_Tickets {

	/**
	 * A re-implementation of the base `filter_by_cost` method to filter events by related
	 * ticket costs in place of their own cost meta.
	 *
	 * @since 4.12.1
	 *
	 * @param float|array $value       The cost to use for the comparison; in the case of `BETWEEN`, `NOT BETWEEN`,
	 *                                 `IN` and `NOT IN` operators this value should be an array.
	 * @param string      $operator    Teh comparison operator to use for the comparison, one of `<`, `<=`, `>`, `>=`,
	 *                                 `=`, `BETWEEN`, `NOT BETWEEN`, `IN`, `NOT IN`.
	 * @param string      $symbol      The desired currency symbol or symbols; this symbol can be a currency ISO code,
	 *                                 e.g. "USD" for U.S. dollars, or a currency symbol, e.g. "$".
	 *                                 In the latter case results will include any event with the matching currency
	 *                                 symbol, this might lead to ambiguous results.
	 *
	 * @throws Tribe__Repository__Usage_Error If the comparison operator is not supported of is using the `BETWEEN`,
	 *                                        `NOT BETWEEN` operators without passing a two element array `$value`.
	 */
	public function filter_by_cost( $value, $operator = '=', $symbol = null ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $repo ) ) {
			$repo = $this->decorated;
		}

		$operators = [
			'<',
			'<=',
			'>',
			'>=',
			'=',
			'!=',
			'BETWEEN',
			'NOT BETWEEN',
			'IN',
			'NOT IN',
		];
		if ( ! in_array( $operator, $operators, true ) ) {
			throw Tribe__Repository__Usage_Error::because_this_comparison_operator_is_not_supported( $operator, 'filter_by_cost' );
		}

		if (
			in_array( $operator, [ 'BETWEEN', 'NOT BETWEEN' ], true )
			&& ! (
				is_array( $value )
				&& 2 === count( $value )
			)
		) {
			throw Tribe__Repository__Usage_Error::because_this_comparison_operator_requires_an_value_of_type( $operator, 'filter_by_cost', 'array' );
		}

		if ( in_array( $operator, [ 'IN', 'NOT IN' ], true ) ) {
			$value = (array) $value;
		}

		$operator_name = Tribe__Utils__Array::get( Tribe__Repository::get_comparison_operators(), $operator, '' );
		$prefix        = str_replace( '-', '_', 'by_cost_' . $operator_name );

		global $wpdb;

		// Join to the meta that relates tickets to events.
		$repo->join_clause( "JOIN {$wpdb->postmeta} {$prefix}_ticket_event
			ON (
				{$prefix}_ticket_event.meta_value = {$wpdb->posts}.ID
				AND {$prefix}_ticket_event.meta_key REGEXP '^_tribe_.*_for_event$'
			)" );

		$price_regexp_frags = [
			// PayPal and WooCommerce tickets.
			'_price',
			// Easy Digital Downloads tickets.
			'edd_price',
		];
		$price_regexp       = '^(' . implode( '|', $price_regexp_frags ) . ')$';

		// Join to the ticket cost meta, allow for RSVP tickets too that have no price.
		$repo->join_clause( $wpdb->prepare( "LEFT JOIN {$wpdb->postmeta} {$prefix}_ticket_cost
			ON (
					{$prefix}_ticket_cost.post_id = {$prefix}_ticket_event.post_id
					AND (
						{$prefix}_ticket_cost.meta_key REGEXP %s
						OR {$prefix}_ticket_cost.meta_id IS NULL
					)
			)", $price_regexp ) );

		$prepared_value = is_array( $value ) ? $repo->prepare_interval( $value, '%d', $operator ) : $wpdb->prepare( '%d', $value );

		// Default the cost to `0` if not set to make RSVP tickets show as "free" tickets, with a cost of 0.
		$repo->where_clause( "IFNULL( {$prefix}_ticket_cost.meta_value, 0 ) {$operator} {$prepared_value}" );

		if ( null !== $symbol ) {
			$this->filter_by_cost_currency_symbol( $symbol );
		}
	}

	/**
	 * Filters events that have a ticket with a specific cost currency symbol.
	 *
	 * Events with a cost of `0` but a currency symbol set will be fetched when fetching
	 * by their symbols; RSVP tickets have no symbol and will never match any filtering
	 * by currency symbol.
	 * Filtering by currency symbol, when done in the context of Event Tickets, really means
	 * filtering events by tickets that come from providers with a specific currency ISO code.
	 * As an example filtering by "USD" when Tribe Commerce tickets use the "EUR" code and
	 * WooCommerce tickets use the "USD" code means "only fetch events that have WooCommerce
	 * tickets".
	 *
	 * @since 4.12.1
	 *
	 * @param string|array $symbol One or more currency symbols or currency ISO codes. E.g.
	 *                             "$" and "USD".
	 *
	 * @throws Tribe__Repository__Void_Query_Exception If no provider uses the specified currency symbol
	 *                                                 or ISO code.
	 */
	public function filter_by_cost_currency_symbol( $symbol ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $repo ) ) {
			$repo = $this->decorated;
		}

		/** @var \Tribe__Tickets__Commerce__Currency $currency */
		$currency      = tribe( 'tickets.commerce.currency' );
		$symbols       = (array) $symbol;
		$request_codes = [];

		/*
		 * Transform the request symbols into ISO codes; due to its ambiguous nature a
		 * symbol might match 0+ ISO codes.
		 */
		foreach ( $symbols as $request_symbol ) {
			$request_codes[] = (array) $currency->get_symbol_codes( $request_symbol );
		}
		$request_codes = array_unique( call_user_func_array( 'array_merge', $request_codes ) );

		if ( empty( $request_codes ) ) {
			$reason = 'The specified currency symbol or ISO code is not supported.';
			throw Tribe__Repository__Void_Query_Exception::because_the_query_would_yield_no_results( $reason );
		}

		// Compile a list of ticket providers that are active and use one of the requested ISO codes.
		$request_providers = [];

		if ( array_intersect( $request_codes, (array) $currency->get_currency_code() ) ) {
			$request_providers[] = 'tpp';
		}

		$providers = [
			'eddticket' => 'Tribe__Tickets_Plus__Commerce__EDD__Main',
			'wooticket' => 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main',
		];

		foreach ( $providers as $slug => $provider ) {
			if ( ! class_exists( $provider ) ) {
				continue;
			}

			$provider_symbol = $currency->get_provider_symbol( $provider );

			if ( ! is_string( $provider_symbol ) ) {
				continue;
			}

			$provider_codes = (array) $currency->get_symbol_codes( $provider_symbol );

			if ( array_intersect( $request_codes, $provider_codes ) ) {
				// This provider uses one of the request ISO codes, use it.
				$request_providers[] = $slug;
			}
		}

		if ( empty( $request_providers ) ) {
			$reason = 'No ticket provider uses the specified currency symbol or ISO code.';
			throw Tribe__Repository__Void_Query_Exception::because_the_query_would_yield_no_results( $reason );
		}

		$providers_regex = sprintf( '(%s)', implode( '|', $request_providers ) );

		global $wpdb;
		$prefix = 'by_cost_currency_symbol_';
		// Join to the meta that relates tickets to events but only for the providers that have the required symbols.
		$repo->join_clause( "JOIN {$wpdb->postmeta} {$prefix}_ticket_event
			ON (
				{$prefix}_ticket_event.meta_value = {$wpdb->posts}.ID
				AND {$prefix}_ticket_event.meta_key REGEXP '^_tribe_{$providers_regex}_for_event\$'
			)" );
	}

	/**
	 * Filters events to include only those that match the provided ticket state.
	 *
	 * This does NOT include RSVPs or events that have a cost assigned via the
	 * cost custom field.
	 *
	 * @since 4.12.1
	 *
	 * @param bool $has_tickets Indicates if the event should have ticket types attached to it or not.
	 */
	public function filter_by_has_tickets( $has_tickets = true ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $repo ) ) {
			$repo = $this->decorated;
		}

		global $wpdb;
		$prefix = 'has_tickets_';

		if ( (bool) $has_tickets ) {
			// Join to the meta that relates tickets to events but exclude RSVP tickets.
			$repo->join_clause( "JOIN {$wpdb->postmeta} {$prefix}_ticket_event ON (
					{$prefix}_ticket_event.meta_value = {$wpdb->posts}.ID
					AND {$prefix}_ticket_event.meta_key NOT REGEXP '^_tribe_rsvp_for_event$'
				)" );

			return;
		}

		// Join to the meta that relates tickets to events.
		$repo->join_clause( "LEFT JOIN {$wpdb->postmeta} {$prefix}_ticket_event
					ON {$prefix}_ticket_event.meta_value = {$wpdb->posts}.ID" );
		// Keep events that have no tickets assigned or are assigned RSVP tickets.
		$repo->where_clause( "{$prefix}_ticket_event.meta_id IS NULL
			OR {$prefix}_ticket_event.meta_key = '_tribe_rsvp_for_event'" );
	}

	/**
	 * Filters events to include only those that match the provided RSVP state.
	 *
	 * @since 4.12.1
	 *
	 * @param bool $has_rsvp Indicates if the event should have RSVP tickets attached to it or not.
	 */
	public function filter_by_has_rsvp( $has_rsvp = true ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $repo ) ) {
			$repo = $this->decorated;
		}

		global $wpdb;
		$prefix = 'has_rsvp_';

		if ( (bool) $has_rsvp ) {
			$repo->join_clause( "JOIN {$wpdb->postmeta} {$prefix}_ticket_event
			ON (
				{$prefix}_ticket_event.meta_value = {$wpdb->posts}.ID
				AND {$prefix}_ticket_event.meta_key = '_tribe_rsvp_for_event'
			)" );

			return;
		}

		// Join to the meta that relates tickets to events.
		$repo->join_clause( "LEFT JOIN {$wpdb->postmeta} {$prefix}_ticket_event
			ON (
				{$prefix}_ticket_event.meta_value = {$wpdb->posts}.ID
				AND {$prefix}_ticket_event.meta_key REGEXP '^_tribe_.*_for_event$'
			)" );
		// Keep any event without tickets or not related to an RSVP ticket.
		$repo->where_clause( "{$prefix}_ticket_event.meta_id IS NULL
			OR {$prefix}_ticket_event.meta_key != '_tribe_rsvp_for_event'" );
	}

	/**
	 * Filters events to include only those that match the provided RSVP or Ticket state.
	 *
	 * @since 5.2.0
	 *
	 * @param bool $has_rsvp_or_tickets Indicates if the event should have RSVP or tickets attached to it or not.
	 */
	public function filter_by_has_rsvp_or_tickets( $has_rsvp_or_tickets = true ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $repo ) ) {
			$repo = $this->decorated;
		}

		global $wpdb;
		$prefix = 'has_rsvp_or_tickets_';

		if ( (bool) $has_rsvp_or_tickets ) {
			// Join to the meta that relates tickets to events but exclude RSVP tickets.
			$repo->join_clause( "JOIN {$wpdb->postmeta} {$prefix}_ticket_event ON (
					{$prefix}_ticket_event.meta_value = {$wpdb->posts}.ID
					AND {$prefix}_ticket_event.meta_key REGEXP '^_tribe_.*_for_event$'
				)" );

			return;
		}

		// Join to the meta that relates tickets to events.
		$repo->join_clause( "LEFT JOIN {$wpdb->postmeta} {$prefix}_ticket_event
					ON {$prefix}_ticket_event.meta_value = {$wpdb->posts}.ID" );
		// Keep events that have no tickets assigned or are assigned RSVP tickets.
		$repo->where_clause( "{$prefix}_ticket_event.meta_id IS NULL" );
	}
}
