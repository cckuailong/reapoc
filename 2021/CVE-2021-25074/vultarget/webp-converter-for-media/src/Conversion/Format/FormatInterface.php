<?php

namespace WebpConverter\Conversion\Format;

/**
 * Interface for class that supports output format for images.
 */
interface FormatInterface {

	/**
	 * Returns extension of output format.
	 *
	 * @return string Format extension
	 */
	public function get_extension(): string;

	/**
	 * Returns mime type of output format.
	 *
	 * @return string Format mime type
	 */
	public function get_mime_type(): string;

	/**
	 * Returns label of output format.
	 *
	 * @return string Format label.
	 */
	public function get_label(): string;

	/**
	 * Returns status is output format available?
	 *
	 * @param string $conversion_method Type of conversion method.
	 *
	 * @return bool Is format available?
	 */
	public function is_available( string $conversion_method ): bool;
}
