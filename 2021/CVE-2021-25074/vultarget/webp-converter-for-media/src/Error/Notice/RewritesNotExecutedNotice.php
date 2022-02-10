<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class RewritesNotExecutedNotice implements ErrorNotice {

	const ERROR_KEY = 'rewrites_not_executed';

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
			/* translators: %1$s: directory tag, %2$s: invalid config value, %3$s: correct config value */
				__( 'Your server does not supports using .htaccess files from custom locations. This is usually related to the virtual host settings in the Apache configuration. In the .conf file appropriate for your VirtualHost, in the %1$s section, replace the value of %2$s with the value of %3$s.', 'webp-converter-for-media' ),
				sprintf( '<strong>%s</strong>', '&lt;Directory&gt;...&lt;/Directory&gt;' ),
				sprintf( '<strong>%s</strong>', 'AllowOverride None' ),
				sprintf( '<strong>%s</strong>', 'AllowOverride All' )
			),
			sprintf(
			/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
				__( 'If you are using a Nginx server, check the configuration for Nginx in %1$sthe plugin FAQ%2$s. Proper configuration of virtual hosts for Nginx is required for the plugin to function properly. Manual configuration is also necessary if you are using the WordPress Multisite Network.', 'webp-converter-for-media' ),
				'<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
				'</a>'
			),
			__( 'In this case, please contact your server administrator.', 'webp-converter-for-media' ),
		];
	}
}
