<?php

namespace WebpConverter\Conversion\Format;

/**
 * Adds support for all output formats and returns information about them.
 */
class FormatFactory {

	/**
	 * Objects of supported output formats.
	 *
	 * @var FormatInterface[]
	 */
	private $formats = [];

	public function __construct() {
		$this->set_integration( new AvifFormat() );
		$this->set_integration( new WebpFormat() );
	}

	/**
	 * Sets integration for format.
	 *
	 * @param FormatInterface $format .
	 *
	 * @return void
	 */
	private function set_integration( FormatInterface $format ) {
		$this->formats[] = $format;
	}

	/**
	 * Returns list of output formats.
	 *
	 * @return string[] Extensions of output formats with labels.
	 */
	public function get_formats(): array {
		$values = [];
		foreach ( $this->formats as $format ) {
			$values[ $format->get_extension() ] = $format->get_label();
		}
		return $values;
	}

	/**
	 * Returns list of available output formats.
	 *
	 * @param string|null $conversion_method Name of conversion method.
	 *
	 * @return string[] Extensions of output formats with labels.
	 */
	public function get_available_formats( string $conversion_method = null ): array {
		$values = [];
		if ( $conversion_method === null ) {
			return $values;
		}

		foreach ( $this->formats as $format ) {
			if ( ! $format->is_available( $conversion_method ) ) {
				continue;
			}
			$values[ $format->get_extension() ] = $format->get_label();
		}
		return $values;
	}

	/**
	 * Returns extensions of output formats.
	 *
	 * @return string[] Extensions of output formats.
	 */
	public function get_format_extensions(): array {
		$values = [];
		foreach ( $this->formats as $format ) {
			$values[] = $format->get_extension();
		}
		return $values;
	}

	/**
	 * Returns mime types of output formats.
	 *
	 * @param string[] $output_formats Extensions of output formats.
	 *
	 * @return string[] Mime types of output formats.
	 */
	public function get_mime_types( array $output_formats ): array {
		$values = [];
		foreach ( $this->formats as $format ) {
			if ( ! in_array( $format->get_extension(), $output_formats ) ) {
				continue;
			}
			$values[ $format->get_extension() ] = $format->get_mime_type();
		}
		return $values;
	}
}
