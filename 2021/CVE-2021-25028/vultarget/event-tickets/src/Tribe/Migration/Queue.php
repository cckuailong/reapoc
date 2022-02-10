<?php
/**
 * Queue class for migration handling.
 *
 * @since 4.12.0
 */

namespace Tribe\Tickets\Migration;

/**
 * Class Queue
 *
 * @package Tribe\Tickets\Migration
 */
class Queue {

	/**
	 * Queue Identifier.
	 *
	 * @since 4.12.0
	 *
	 * @var string
	 */
	public $queue_id = '';

	/**
	 * Which action will be triggered as an single cron event.
	 *
	 * @since 4.12.0
	 *
	 * @var string
	 */
	protected $single_key;

	/**
	 * Which action will be triggered as an ongoing scheduled cron event.
	 *
	 * @since 4.12.0
	 *
	 * @var string
	 */
	protected $scheduled_key;

	/**
	 * Batch offset key used to track migration progress.
	 *
	 * @since 4.12.0
	 *
	 * @var string
	 */
	protected $batch_offset_key;

	/**
	 * Number of items to be processed in a single batch.
	 *
	 * @since 4.12.0
	 *
	 * @var int
	 */
	protected $batch_size = 200;

	/**
	 * Queue Hooks.
	 *
	 * @since 4.12.0
	 */
	public function hooks() {
		if ( null === $this->single_key ) {
			$this->single_key = 'tribe_tickets_migrate_single_' . $this->queue_id;
		}

		if ( null === $this->scheduled_key ) {
			$this->scheduled_key = 'tribe_tickets_migrate_' . $this->queue_id;
		}

		if ( null === $this->batch_offset_key ) {
			$this->batch_offset_key = 'tribe_tickets_migrate_offset_' . $this->batch_offset_key;
		}

		add_action( $this->single_key, [ $this, 'process_queue' ], 20, 0 );
		add_action( $this->scheduled_key, [ $this, 'process_queue' ], 20, 0 );

		// We can add custom ET+ deactivation handling based on TEC, but this isn't needed right now.
		add_action( 'tribe_events_blog_deactivate', [ $this, 'clear_scheduled_task' ] );
	}

	/**
	 * Register scheduled task to be used for processing batches on plugin activation.
	 *
	 * @since 4.12.0
	 */
	public function register_scheduled_task() {
		if ( wp_next_scheduled( $this->scheduled_key ) ) {
			return;
		}

		$interval = $this->get_cron_interval();

		if ( null === $interval ) {
			return;
		}

		wp_schedule_event( time(), $interval, $this->scheduled_key );
	}

	/**
	 * Clear the scheduled task on plugin deactivation.
	 *
	 * @since 4.12.0
	 */
	public function clear_scheduled_task() {
		wp_clear_scheduled_hook( $this->scheduled_key );
	}

	/**
	 * Processes the next waiting batch of orders to migrate, if there are any.
	 *
	 * @since 4.12.0
	 *
	 * @param null|int $batch_size Batch processing size override.
	 */
	public function process_queue( $batch_size = null ) {
		if ( null === $batch_size ) {
			$batch_size = $this->get_batch_size();
		}

		$current_offset = $this->get_current_offset();

		if ( 'complete' === $current_offset ) {
			$this->clear_scheduled_task();

			return;
		}

		$this->batch_size = (int) $batch_size;

		$processed = $this->process( $current_offset );

		// if no items are processed or not processed clear the task.
		if ( empty( $processed['processed'] ) && empty( $processed['not_processed'] ) ) {
			tribe_update_option( $this->batch_offset_key, 'complete' );

			$this->clear_scheduled_task();

			return;
		}

		$this->update_offset( $processed['not_processed'] );
	}

	/**
	 * Processes the next waiting batch of orders to migrate, if there are any.
	 *
	 * @since 4.12.0
	 *
	 * @param int $current_offset The current offset being used.
	 *
	 * @return array List of processed and not processed counts.
	 */
	public function process( $current_offset = 0 ) {
		$counts = [
			'processed'     => 0,
			'not_processed' => 0,
		];

		$posts = $this->get_posts( $current_offset );

		if ( empty( $posts ) ) {
			return $counts;
		}

		foreach ( $posts as $post ) {
			$processed = $this->process_post( $post );

			if ( $processed ) {
				$counts['processed'] ++;
			} else {
				$counts['not_processed'] ++;
			}
		}

		return $counts;
	}

	/**
	 * Process post in queue.
	 *
	 * @since 4.12.0
	 *
	 * @param WP_Post|int $post The post object or post ID, depending on what $this->get_query() is set to return.
	 *
	 * @return bool Whether the post was processed.
	 */
	public function process_post( $post ) {
		return false;
	}

	/**
	 * Get posts for migrating.
	 *
	 * @since 4.12.0
	 *
	 * @param int $current_offset The current offset being used.
	 *
	 * @return int[]|WP_Post[] List of post IDs or post objects.
	 */
	public function get_posts( $current_offset ) {
		return [];
	}

	/**
	 * Get the Current offset number.
	 *
	 * @since 4.12.0
	 *
	 * @return string|int Current offset number.
	 */
	public function get_current_offset() {
		$current_offset = tribe_get_option( $this->batch_offset_key );

		// Set up default current offset.
		if ( false === $current_offset || '' === $current_offset ) {
			$current_offset = 0;
		}

		return $current_offset;
	}

	/**
	 * Update the Offset Number with the Current Batch.
	 *
	 * @since 4.12.0
	 *
	 * @param int $not_processed the number of orders not processed.
	 */
	protected function update_offset( $not_processed ) {
		$current_offset = $this->get_current_offset();

		// Only set if numeric.
		if ( ! is_numeric( $current_offset ) ) {
			return;
		}

		$current_offset  = (int) $current_offset;
		$current_offset += $not_processed;

		tribe_update_option( $this->batch_offset_key, $current_offset );
	}

	/**
	 * Get the size to be used by each batch processed.
	 *
	 * @since 4.12.0
	 *
	 * @return int The batch size used by the migration.
	 */
	protected function get_batch_size() {
		/**
		 * Controls the size of each batch processed by default (ie, during cron updates of record inserts/updates).
		 *
		 * @since 4.12.0
		 *
		 * @param int $batch_size The batch size used by the migration.
		 */
		return (int) apply_filters( "tribe_tickets_migration_queue_batch_size_{$this->queue_id}", $this->batch_size );
	}

	/**
	 * Get the cron interval to be used by each batch processed.
	 *
	 * @since 4.12.0
	 *
	 * @return int The batch size used by the migration.
	 */
	protected function get_cron_interval() {
		/**
		 * Filter the interval at which to process the migration queue.
		 *
		 * By default the interval "hourly" is specified, however other intervals such as "daily"
		 * and "twicedaily" can normally be substituted.
		 *
		 * @since 4.12.0
		 *
		 * @param string|null $interval Cron interval or null to disable it.
		 *
		 * @see   'cron_schedules'
		 *
		 * @see   wp_schedule_event()
		 */
		return apply_filters( "tribe_tickets_migration_queue_interval_{$this->queue_id}", 'hourly' );
	}
}
