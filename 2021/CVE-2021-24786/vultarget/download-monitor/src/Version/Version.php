<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Download_Version class.
 */
class DLM_Download_Version {

	/** @var int */
	private $id;

	/** @var int */
	private $author;

	/** @var int */
	private $download_id;

	/** @var int */
	private $menu_order;

	/** @var bool If this version is latest version of Download */
	private $latest = false;

	/** @var DateTime */
	private $date;

	/** @var string */
	private $version;

	/** @var int */
	private $download_count = null;

	/** @var int */
	private $filesize;

	/** @var string */
	private $md5;

	/** @var string */
	private $sha1;

	/** @var string */
	private $sha256;

	/** @var string */
	private $crc32b;

	/** @var array */
	private $mirrors = array();

	/** @var string */
	private $url;

	/** @var string */
	private $filename;

	/** @var string */
	private $filetype;

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function get_author() {
		return $this->author;
	}

	/**
	 * @param int $author
	 */
	public function set_author( $author ) {
		$this->author = $author;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return 'Download #' . $this->get_download_id() . ' File Version';
	}

	/**
	 * @return int
	 */
	public function get_download_id() {
		return $this->download_id;
	}

	/**
	 * @param int $download_id
	 */
	public function set_download_id( $download_id ) {
		$this->download_id = $download_id;
	}

	/**
	 * @return int
	 */
	public function get_menu_order() {
		return $this->menu_order;
	}

	/**
	 * @param int $menu_order
	 */
	public function set_menu_order( $menu_order ) {
		$this->menu_order = $menu_order;
	}

	/**
	 * @param bool $latest
	 */
	public function set_latest($latest) {
		$this->latest = $latest;
	}

	/**
	 * @return bool
	 */
	public function is_latest() {
		return $this->latest;
	}

	/**
	 * @return DateTime
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * @param DateTime $date
	 */
	public function set_date( $date ) {
		$this->date = $date;
	}

	/**
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @param string $version
	 */
	public function set_version( $version ) {
		$this->version = $version;
	}

	/**
	 * Helper method that returns version.
	 * If version is empty it'll return 1 as a default version number.
	 *
	 * @return string
	 */
	public function get_version_number() {
		$version = $this->get_version();
		if ( empty( $version ) ) {
			$version = 1;
		}

		return $version;
	}

	/**
	 * Helper method that returns if this version has a version label set.
	 *
	 * @return bool
	 */
	public function has_version_number() {
		$version = $this->get_version();
		return ! empty( $version );
	}

	/**
	 * @return int
	 */
	public function get_download_count() {
		return $this->download_count;
	}

	/**
	 * @param int $download_count
	 */
	public function set_download_count( $download_count ) {
		$this->download_count = $download_count;
	}

	/**
	 * @return string
	 */
	public function get_filename() {
		return $this->filename;
	}

	/**
	 * @param string $filename
	 */
	public function set_filename( $filename ) {
		$this->filename = $filename;
	}

	/**
	 * @return int
	 */
	public function get_filesize() {
		return $this->filesize;
	}

	/**
	 * @param int $filesize
	 */
	public function set_filesize( $filesize ) {
		$this->filesize = $filesize;
	}

	/**
	 * Get a formatted filesize
	 */
	public function get_filesize_formatted() {
		return size_format( $this->get_filesize() );
	}

	/**
	 * @return string
	 */
	public function get_md5() {
		return $this->md5;
	}

	/**
	 * @param string $md5
	 */
	public function set_md5( $md5 ) {
		$this->md5 = $md5;
	}

	/**
	 * @return string
	 */
	public function get_sha1() {
		return $this->sha1;
	}

	/**
	 * @param string $sha1
	 */
	public function set_sha1( $sha1 ) {
		$this->sha1 = $sha1;
	}

	/**
	 * @return string
	 */
	public function get_sha256() {
		return $this->sha256;
	}

	/**
	 * @param string $sha256
	 */
	public function set_sha256( $sha256 ) {
		$this->sha256 = $sha256;
	}

	/**
	 * @return string
	 */
	public function get_crc32b() {
		return $this->crc32b;
	}

	/**
	 * @param string $crc32b
	 */
	public function set_crc32b( $crc32b ) {
		$this->crc32b = $crc32b;
	}

	/**
	 * @return array
	 */
	public function get_mirrors() {
		return $this->mirrors;
	}

	/**
	 * @param array $mirrors
	 */
	public function set_mirrors( $mirrors ) {
		$this->mirrors = $mirrors;
	}

	/**
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function set_url( $url ) {
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function get_filetype() {
		return $this->filetype;
	}

	/**
	 * @param string $filetype
	 */
	public function set_filetype( $filetype ) {
		$this->filetype = $filetype;
	}

	/**
	 * Get the version slug
	 *
	 * @return string
	 */
	public function get_version_slug() {
		return sanitize_title_with_dashes( $this->version );
	}

	/**
	 * Increase the version and total download count
	 *
	 * @access public
	 * @return void
	 */
	public function increase_download_count() {
		// File download_count
		$this->download_count = absint( get_post_meta( $this->id, '_download_count', true ) ) + 1;
		update_post_meta( $this->id, '_download_count', $this->download_count );

		// Parent download download_count
		$parent_download_count = absint( get_post_meta( $this->download_id, '_download_count', true ) ) + 1;
		update_post_meta( $this->download_id, '_download_count', $parent_download_count );
	}

	/**
	 *
	 * Deprecated methods below.
	 *
	 */

	/**
	 * Deprecated, use $file_manager->get_file_hashes() if you want to generate hashes
	 *
	 * @deprecated 4.0
	 *
	 * @access public
	 *
	 * @param string $file_path
	 *
	 * @return array
	 */
	public function get_file_hashes( $file_path ) {

		DLM_Debug_Logger::deprecated( 'DLM_Download_Version::get_file_hashes()' );

		// Get the hashes
		return download_monitor()->service( 'hasher' )->get_file_hashes( $file_path );
	}

	/**
	 * @deprecated 4.0
	 *
	 * @return string
	 */
	public function get_crc32() {
		return $this->get_crc32b();
	}
}