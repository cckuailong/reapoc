<?php

class DLM_LU_Content_Queue {

	const TABLE = 'legacy_upgrade_queue_content';

	/**
	 * check if queue has already been build once
	 *
	 * @return bool
	 */
	private function is_queue_already_build() {
		return ( 1 === absint( get_option( DLM_Constants::LU_OPTION_CONTENT_QUEUE_BUILD, 0 ) ) );
	}

	/**
	 * Get the queue table
	 *
	 * @return string
	 */
	private function get_queue_table() {
		global $wpdb;

		return $wpdb->prefix . self::TABLE;
	}

	/**
	 * Create database table if not exists
	 *
	 * @return bool
	 */
	private function create_table_if_not_exists() {
		global $wpdb;

		// create table
		$sql = "CREATE TABLE IF NOT EXISTS `" . $this->get_queue_table() . "` ( `content_id` INT NOT NULL , `processing` DATETIME NULL DEFAULT NULL , `done` DATETIME NULL DEFAULT NULL , PRIMARY KEY (`content_id`)) ;";
		$r   = $wpdb->query( $sql );

		return ( false === $r );
	}

	/**
	 * Build queue of downloads that need upgrading
	 *
	 * @return bool
	 */
	public function build_queue() {
		global $wpdb;

		// check if queue was already build because YOBO! (you only build once).
		if ( $this->is_queue_already_build() ) {
			return false;
		}

		// create database table if not exists
		$this->create_table_if_not_exists();

		// fetch content items that aren't in our queue
		$content_items = $wpdb->get_results( "SELECT P.`ID` FROM `{$wpdb->posts}` P LEFT JOIN `" . $this->get_queue_table() . "` Q ON P.ID=Q.content_id WHERE P.`post_type` IN ( 'post', 'page' ) AND Q.content_id IS NULL ;" );

		// loop and insert into queue
		if ( count( $content_items ) > 0 ) {
			foreach ( $content_items as $content_item ) {
				$wpdb->insert( $this->get_queue_table(), array( 'content_id' => $content_item->ID ) );
			}
		}

		// set queue build
		update_option( DLM_Constants::LU_OPTION_CONTENT_QUEUE_BUILD, 1 );

		return true;
	}

	/**
	 * Get queue of content items that need upgrading.
	 * This means we only return items that aren't currently upgrading or are already upgraded.
	 *
	 * @return array
	 */
	public function get_queue() {
		global $wpdb;

		return $wpdb->get_col( "SELECT `content_id` AS `id` from `" . $this->get_queue_table() . "` WHERE `processing` IS NULL AND `done` IS NULL " );
	}

	/**
	 * Mark content item as currently being upgraded
	 *
	 * @param int $content_id
	 *
	 * @return bool
	 */
	public function mark_upgrading( $content_id ) {
		global $wpdb;

		$res = $wpdb->query( $wpdb->prepare( "UPDATE `" . $this->get_queue_table() . "` SET `processing` = NOW() WHERE `content_id` = %d ;", $content_id ) );

		return ( false === $res );
	}

	/**
	 * Mark content item as successfully upgraded
	 *
	 * @param int $content_id
	 *
	 * @return bool
	 */
	public function mark_upgraded( $content_id ) {
		global $wpdb;

		$res = $wpdb->query( $wpdb->prepare( "UPDATE `" . $this->get_queue_table() . "` SET `done` = NOW() WHERE `content_id` = %d ;", $content_id ) );

		return ( false === $res );
	}

}