<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class BypassingApacheNotice implements ErrorNotice {

	const ERROR_KEY = 'bypassing_apache';

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
			__( 'Requests to images are processed by your server bypassing Apache. When loading images, rules from the .htaccess file are not executed. Change the server settings to handle the rules in the .htaccess file when loading static files.', 'webp-converter-for-media' ),
			__( 'Find options similar to "Smart static files processing" or "Serve static files directly by Nginx" in your server settings for Apache and Nginx configuration. These types of options should be turned off for the rules in the .htaccess file to function properly. If you have "Nginx caching" or similar setting active, disable it or remove the following extensions from the list of saved to the cache: .jpg, .jpeg, .png and .gif.', 'webp-converter-for-media' ),
			__( 'In this case, please contact your server administrator.', 'webp-converter-for-media' ),
		];
	}
}
