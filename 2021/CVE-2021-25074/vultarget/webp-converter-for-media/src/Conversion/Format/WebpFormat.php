<?php

namespace WebpConverter\Conversion\Format;

/**
 * Supports WebP as output format for images.
 */
class WebpFormat extends FormatAbstract {

	const FORMAT_EXTENSION = 'webp';

	/**
	 * {@inheritdoc}
	 */
	public function get_extension(): string {
		return self::FORMAT_EXTENSION;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_mime_type(): string {
		return 'image/webp';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label(): string {
		return 'WebP';
	}
}
