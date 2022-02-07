<?php

interface DLM_Download_Repository {

	/**
	 * Retrieve items
	 *
	 * @param array $filters
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array<DLM_Download>
	 */
	public function retrieve( $filters=array(), $limit=0, $offset=0 );

	/**
	 * Retrieve single item
	 *
	 * @param int $id
	 *
	 * @return DLM_Download
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
	 * @param DLM_Download $download
	 *
	 * @return bool
	 */
	public function persist( $download );
}