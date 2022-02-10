<?php

namespace WebpConverter\Conversion\Format;

use WebpConverter\Conversion\Method\MethodFactory;

/**
 * Abstract class for class that supports output format for images.
 */
abstract class FormatAbstract implements FormatInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_label(): string {
		return sprintf( '.%s', $this->get_extension() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available( string $conversion_method ): bool {
		return ( new MethodFactory() )
			->is_method_available( $conversion_method, $this->get_extension() );
	}
}
