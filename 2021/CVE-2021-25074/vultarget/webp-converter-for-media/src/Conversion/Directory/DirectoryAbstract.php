<?php

namespace WebpConverter\Conversion\Directory;

/**
 * Abstract class for class that supports data about directory.
 */
abstract class DirectoryAbstract implements DirectoryInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_label(): string {
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool {
		return ( file_exists( $this->get_server_path() ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_output_directory(): bool {
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_server_path(): string {
		$source_path    = apply_filters( 'webpc_site_root', realpath( ABSPATH ) );
		$directory_name = apply_filters( 'webpc_dir_name', $this->get_relative_path(), $this->get_type() );
		return sprintf( '%1$s/%2$s', $source_path, $directory_name );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_path_url(): string {
		$source_url     = apply_filters( 'webpc_site_url', get_site_url() );
		$directory_name = apply_filters( 'webpc_dir_name', $this->get_relative_path(), $this->get_type() );
		return sprintf( '%1$s/%2$s', $source_url, $directory_name );
	}
}
