<?php

class DLM_LU_Download_Queue {

	const TABLE = 'legacy_upgrade_queue_downloads';

	/**
	 * check if queue has already been build once
	 *
	 * @return bool
	 */
	private function is_queue_already_build() {
		return ( 1 === absint( get_option( DLM_Constants::LU_OPTION_DOWNLOAD_QUEUE_BUILD, 0 ) ) );
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
		$sql = "CREATE TABLE IF NOT EXISTS `" . $this->get_queue_table() . "` ( `legacy_id` INT NOT NULL , `new_id` INT NULL DEFAULT NULL , `processing` DATETIME NULL DEFAULT NULL , `done` DATETIME NULL DEFAULT NULL , PRIMARY KEY (`legacy_id`)) ;";
		$r   = $wpdb->query( $sql );

		return ( false === $r );
	}

	/**
	 * Get new ID of legacy ID
	 *
	 * @param $legacy_id
	 *
	 * @return int
	 */
	public function get_new_id( $legacy_id ) {
		global $wpdb;

		$legacy_id = absint( $legacy_id );

		return absint( $wpdb->get_var( $wpdb->prepare( "SELECT `new_id` FROM `" . $this->get_queue_table() . "` WHERE `legacy_id` = %d ", $legacy_id ) ) );
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

		// legacy tables we're fetching from
		$upgrader      = new DLM_LU_Download_Upgrader();
		$legacy_tables = $upgrader->get_legacy_tables();

		// fetch legacy downloads that aren't in our queue
		$legacy_downloads = $wpdb->get_results( "SELECT F.`ID` FROM `{$legacy_tables['files']}` F LEFT JOIN `" . $this->get_queue_table() . "` Q ON F.ID=Q.legacy_id WHERE Q.legacy_id IS NULL ;" );

		// loop and insert into queue
		if ( count( $legacy_downloads ) > 0 ) {
			foreach ( $legacy_downloads as $legacy_download ) {
				$wpdb->insert( $this->get_queue_table(), array( 'legacy_id' => $legacy_download->ID ) );
			}
		}

		// set queue build
		update_option( DLM_Constants::LU_OPTION_DOWNLOAD_QUEUE_BUILD, 1 );

		return true;
	}

	/**
	 * Get queue of downloads that need upgrading.
	 * This means we only return items that aren't currently upgrading or are already upgraded.
	 *
	 * @return array
	 */
	public function get_queue() {
		global $wpdb;

		return $wpdb->get_col( "SELECT `legacy_id` AS `id` from `" . $this->get_queue_table() . "` WHERE `new_id` IS NULL AND `processing` IS NULL AND `done` IS NULL " );
	}

	/**
	 * Mark download as currently being upgraded
	 *
	 * @param int $legacy_id
	 *
	 * @return bool
	 */
	public function mark_download_upgrading( $legacy_id ) {
		global $wpdb;

		$res = $wpdb->query( $wpdb->prepare( "UPDATE `" . $this->get_queue_table() . "` SET `processing` = NOW() WHERE `legacy_id` = %d ;", $legacy_id ) );

		return ( false === $res );
	}

	/**
	 * Mark download as successfully upgraded
	 *
	 * @param int $legacy_id
	 * @param int $new_id
	 *
	 * @return bool
	 */
	public function mark_download_upgraded( $legacy_id, $new_id ) {
		global $wpdb;

		$res = $wpdb->query( $wpdb->prepare( "UPDATE `" . $this->get_queue_table() . "` SET `done` = NOW(), `new_id` = %d WHERE `legacy_id` = %d ;", $new_id, $legacy_id ) );

		return ( false === $res );
	}

}