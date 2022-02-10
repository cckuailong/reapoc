<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class LibsNotInstalledNotice implements ErrorNotice {

	const ERROR_KEY = 'libs_not_installed';

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
			sprintf(
			/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
				__( 'On your server is not installed GD or Imagick library. Please read %1$sthe plugin FAQ%2$s, specifically question about requirements of plugin. This issue is plugin-independent. Please contact your server administrator in this case.', 'webp-converter-for-media' ),
				'<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
				'</a>'
			),
			__( 'You can also use "Remote server" option in "Conversion method" field in the plugin settings. This option allows you to convert your images using a remote server, so your server does not have to meet all technical requirements for libraries.', 'webp-converter-for-media' ),
		];
	}
}
