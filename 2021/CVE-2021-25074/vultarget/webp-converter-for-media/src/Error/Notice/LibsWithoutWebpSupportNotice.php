<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class LibsWithoutWebpSupportNotice implements ErrorNotice {

	const ERROR_KEY = 'libs_without_webp_support';

	/**
	 * {@inheritdoc}
	 */
	public function get_key(): string {
		return self::ERROR_KEY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_message(): array {
		return [
			__( 'The selected option of "Conversion method" does not support WebP format. GD or Imagick library is installed on your server, but it does not support the WebP format. This means that you cannot convert images to WebP format on your server, because it does not meet the technical requirements of the plugin. This issue is plugin-independent.', 'webp-converter-for-media' ),
			__( 'You can also use "Remote server" option in "Conversion method" field in the plugin settings. This option allows you to convert your images using a remote server, so your server does not have to meet all technical requirements for libraries.', 'webp-converter-for-media' ),
			__( 'This will allow you to convert your images to WebP without any problems and thus speed up your website.', 'webp-converter-for-media' ),
		];
	}
}
