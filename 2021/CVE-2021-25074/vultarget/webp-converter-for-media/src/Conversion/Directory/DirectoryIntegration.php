<?php

namespace WebpConverter\Conversion\Directory;

use WebpConverter\Conversion\OutputPath;
use WebpConverter\HookableInterface;

/**
 * Returns various types of paths for directories.
 */
class DirectoryIntegration implements HookableInterface {

	/**
	 * Objects of supported directories.
	 *
	 * @var DirectoryInterface[]
	 */
	private $directories = [];

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'webpc_dir_name', [ $this, 'get_dir_as_name' ], 0, 2 );
		add_filter( 'webpc_dir_path', [ $this, 'get_dir_as_path' ], 0, 2 );
		add_filter( 'webpc_dir_url', [ $this, 'get_dir_as_url' ], 0, 2 );
		add_filter( 'webpc_uploads_prefix', [ $this, 'get_prefix_path' ], 0 );
	}

	/**
	 * Adds support of directory, if available.
	 *
	 * @param DirectoryInterface $directory .
	 *
	 * @return self
	 */
	public function add_directory( DirectoryInterface $directory ): self {
		$this->directories[ $directory->get_type() ] = $directory;
		return $this;
	}

	/**
	 * Returns list of source directories.
	 *
	 * @return string[] Types of directories with labels.
	 */
	public function get_input_directories(): array {
		$values = [];
		foreach ( $this->directories as $directory ) {
			if ( ! $directory->is_output_directory() && $directory->is_available() ) {
				$values[ $directory->get_type() ] = $directory->get_label();
			}
		}
		return $values;
	}

	/**
	 * Returns list of output directories.
	 *
	 * @return string[] Types of directories with labels.
	 */
	public function get_output_directories(): array {
		$values = [];
		foreach ( $this->directories as $directory ) {
			if ( ! $directory->is_output_directory()
				&& ( $output_path = OutputPath::get_directory_path( $directory->get_server_path() ) )
				&& ( $output_path !== $directory->get_server_path() ) ) {
				$values[ $directory->get_type() ] = $output_path;
			}
		}
		return $values;
	}

	/**
	 * Returns server path of directory relative to WordPress root directory.
	 *
	 * @param mixed  $value          Default value.
	 * @param string $directory_type Type of directory.
	 *
	 * @return string Relative server path of directory.
	 * @internal
	 */
	public function get_dir_as_name( $value, string $directory_type ): string {
		if ( isset( $this->directories[ $directory_type ] ) ) {
			return $this->directories[ $directory_type ]->get_relative_path();
		}
		return $value;
	}

	/**
	 * Returns server path of directory.
	 *
	 * @param mixed  $value          Default value.
	 * @param string $directory_type Type of directory.
	 *
	 * @return string Server path of directory.
	 * @internal
	 */
	public function get_dir_as_path( $value, string $directory_type ): string {
		$directory_name = apply_filters( 'webpc_dir_name', '', $directory_type );
		if ( $directory_name === '' ) {
			if ( isset( $this->directories[ $directory_type ] ) ) {
				return $this->directories[ $directory_type ]->get_server_path();
			} else {
				return $value;
			}
		}

		$source_path = apply_filters( 'webpc_site_root', realpath( ABSPATH ) );
		return sprintf( '%1$s/%2$s', $source_path, $directory_name );
	}

	/**
	 * Returns URL of directory.
	 *
	 * @param mixed  $value          Default value.
	 * @param string $directory_type Type of directory.
	 *
	 * @return string URL of directory.
	 * @internal
	 */
	public function get_dir_as_url( $value, string $directory_type ): string {
		$directory_name = apply_filters( 'webpc_dir_name', '', $directory_type );
		if ( $directory_name === '' ) {
			if ( isset( $this->directories[ $directory_type ] ) ) {
				return $this->directories[ $directory_type ]->get_path_url();
			} else {
				return $value;
			}
		}

		$source_url = apply_filters( 'webpc_site_url', get_site_url() );
		return sprintf( '%1$s/%2$s', $source_url, $directory_name );
	}

	/**
	 * Returns prefix for wp-content directory for rules in .htaccess file.
	 *
	 * @return string Prefix for wp-content directory.
	 * @internal
	 */
	public function get_prefix_path(): string {
		$doc_dir   = realpath( $_SERVER['DOCUMENT_ROOT'] ) ?: ''; // phpcs:ignore
		$wp_rir    = apply_filters( 'webpc_site_root', realpath( ABSPATH ) );
		$diff_dir  = trim( str_replace( $doc_dir, '', $wp_rir ), '\/' );
		$diff_path = sprintf( '/%s/', $diff_dir );

		return str_replace( '//', '/', $diff_path );
	}
}
