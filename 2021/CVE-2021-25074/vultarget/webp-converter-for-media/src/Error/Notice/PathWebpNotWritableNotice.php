<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class PathWebpNotWritableNotice implements ErrorNotice {

	const ERROR_KEY = 'path_webp_not_writable';

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
			/* translators: %1$s: filter name, %2$s: server path, %3$s: open anchor tag, %4$s: close anchor tag */
				__( 'The path for saving converted WebP files does not exist and cannot be created (function is_writable() returns false). Use filter %1$s to set the correct path. The current using path is: %2$s. Please read %3$sthe plugin FAQ%4$s to learn more.', 'webp-converter-for-media' ),
				'<strong>webpc_dir_path</strong>',
				'<strong>' . apply_filters( 'webpc_dir_path', '', 'webp' ) . '</strong>',
				'<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
				'</a>'
			),
		];
	}
}
