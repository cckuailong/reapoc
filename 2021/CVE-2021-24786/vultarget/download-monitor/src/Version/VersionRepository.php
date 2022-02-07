<?php

interface DLM_Version_Repository {

	/**
	 * Retrieve versions
	 *
	 * @param array $filters
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array<DLM_Download_Version>
	 */
	public function retrieve( $filters=array(), $limit=0, $offset=0 );

	/**
	 * Retrieve single version
	 *
	 * @param int $id
	 *
	 * @return DLM_Download_Version
	 */
	public function retrieve_single( $id );

	/**
	 * Returns number of rows for given filters
	 *
	 * @param array $filters
	 *
	 * @return int
	 */
	public function num_rows( $filters=array() );

	/**
	 * @param DLM_Download_Version $version
	 *
	 * @return bool
	 */
	public function persist( $version );
}