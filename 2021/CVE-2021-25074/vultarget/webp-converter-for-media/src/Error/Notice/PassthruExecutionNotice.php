<?php

namespace WebpConverter\Error\Notice;

use WebpConverter\Loader\PassthruLoader;

/**
 * {@inheritdoc}
 */
class PassthruExecutionNotice implements ErrorNotice {

	const ERROR_KEY = 'passthru_execution';

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
		$passthru_url = PassthruLoader::get_loader_url();
		return [
			sprintf(
			/* translators: %s: anchor tag */
				__( 'Execution of the PHP file from path "%s" is blocked on your server, or access to this file is blocked. Add an exception and enable this file to be executed via HTTP request. To do this, check the security plugin settings (if you are using) or the security settings of your server.', 'webp-converter-for-media' ),
				'<a href="' . $passthru_url . '" target="_blank">' . $passthru_url . '</a>',
				'<br><br>'
			),
			__( 'In this case, please contact your server administrator.', 'webp-converter-for-media' ),
		];
	}
}
