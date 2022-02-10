<?php

namespace WebpConverter\Loader;

use WebpConverter\Settings\Option\ExtraFeaturesOption;
use WebpConverter\Settings\Option\LoaderTypeOption;
use WebpConverter\Settings\Option\SupportedExtensionsOption;

/**
 * Supports method of loading images using rewrites from .htaccess file.
 */
class HtaccessLoader extends LoaderAbstract {

	const LOADER_TYPE = 'htaccess';

	/**
	 * {@inheritdoc}
	 */
	public function is_active_loader(): bool {
		$settings = $this->plugin_data->get_plugin_settings();
		return ( ( $settings[ LoaderTypeOption::OPTION_NAME ] ?? '' ) === self::LOADER_TYPE );
	}

	/**
	 * {@inheritdoc}
	 */
	public function activate_loader( bool $is_debug = false ) {
		if ( is_multisite() ) {
			return;
		}

		$settings = ( $is_debug ) ? $this->plugin_data->get_debug_settings() : $this->plugin_data->get_plugin_settings();

		$this->add_rewrite_rules_to_wp_content( true, $settings );
		$this->add_rewrite_rules_to_uploads( true, $settings );
		$this->add_rewrite_rules_to_uploads_webp( true, $settings );
	}

	/**
	 * {@inheritdoc}
	 */
	public function deactivate_loader() {
		if ( is_multisite() ) {
			return;
		}

		$settings = $this->plugin_data->get_plugin_settings();

		$this->add_rewrite_rules_to_wp_content( false, $settings );
		$this->add_rewrite_rules_to_uploads( false, $settings );
		$this->add_rewrite_rules_to_uploads_webp( false, $settings );
	}

	/**
	 * Saves rules to .htaccess file in /wp-content directory.
	 *
	 * @param bool    $is_active Is loader active?
	 * @param mixed[] $settings  Plugin settings.
	 *
	 * @return void
	 */
	private function add_rewrite_rules_to_wp_content( bool $is_active, array $settings ) {
		$path = dirname( apply_filters( 'webpc_dir_path', '', 'uploads' ) );
		if ( ! $is_active ) {
			$this->save_rewrites_in_htaccesss( $path );
			return;
		}

		$content = $this->add_comments_to_rules(
			[
				$this->get_mod_rewrite_rules( $settings ),
			]
		);

		$content = apply_filters( 'webpc_htaccess_rules', $content, $path . '/.htaccess' );
		$this->save_rewrites_in_htaccesss( $path, $content );
	}

	/**
	 * Saves rules to .htaccess file in /uploads directory.
	 *
	 * @param bool    $is_active Is loader active?
	 * @param mixed[] $settings  Plugin settings.
	 *
	 * @return void
	 */
	private function add_rewrite_rules_to_uploads( bool $is_active, array $settings ) {
		$path = apply_filters( 'webpc_dir_path', '', 'uploads' );
		if ( ! $is_active ) {
			$this->save_rewrites_in_htaccesss( $path );
			return;
		}

		$path_parts = explode( '/', apply_filters( 'webpc_dir_name', '', 'uploads' ) );
		$content    = $this->add_comments_to_rules(
			[
				$this->get_mod_rewrite_rules( $settings, end( $path_parts ) ),
				$this->get_mod_headers_rules( $settings ),
			]
		);

		$content = apply_filters( 'webpc_htaccess_rules', $content, $path . '/.htaccess' );
		$this->save_rewrites_in_htaccesss( $path, $content );
	}

	/**
	 * Saves rules to .htaccess file in /uploads-webpc directory.
	 *
	 * @param bool    $is_active Is loader active?
	 * @param mixed[] $settings  Plugin settings.
	 *
	 * @return void
	 */
	private function add_rewrite_rules_to_uploads_webp( bool $is_active, array $settings ) {
		$path = apply_filters( 'webpc_dir_path', '', 'webp' );
		if ( ! $is_active ) {
			$this->save_rewrites_in_htaccesss( $path );
			return;
		}

		$content = $this->add_comments_to_rules(
			[
				$this->get_mod_mime_rules( $settings ),
				$this->get_mod_expires_rules( $settings ),
				$this->get_mod_headers_rules( $settings ),
			]
		);

		$content = apply_filters( 'webpc_htaccess_rules', $content, $path . '/.htaccess' );
		$this->save_rewrites_in_htaccesss( $path, $content );
	}

	/**
	 * Generates rules for rewriting source images to output images.
	 *
	 * @param mixed[]     $settings    Plugin settings.
	 * @param string|null $output_path Location of .htaccess file.
	 *
	 * @return string Rules for .htaccess file.
	 */
	private function get_mod_rewrite_rules( array $settings, $output_path = null ): string {
		$content = '';
		if ( ! $settings[ SupportedExtensionsOption::OPTION_NAME ] ) {
			return $content;
		}

		$prefix_path = apply_filters( 'webpc_uploads_prefix', '/' );
		$prefix_rule = apply_filters( 'webpc_htaccess_prefix_rule', $prefix_path );
		$path        = apply_filters( 'webpc_dir_name', '', 'webp' );
		if ( $output_path !== null ) {
			$path .= '/' . $output_path;
		}

		foreach ( $this->get_mime_types() as $format => $mime_type ) {
			$content .= '<IfModule mod_rewrite.c>' . PHP_EOL;
			$content .= '  RewriteEngine On' . PHP_EOL;
			foreach ( $settings[ SupportedExtensionsOption::OPTION_NAME ] as $ext ) {
				$content .= "  RewriteCond %{HTTP_ACCEPT} ${mime_type}" . PHP_EOL;
				$content .= "  RewriteCond %{DOCUMENT_ROOT}${prefix_path}${path}/$1.${ext}.${format} -f" . PHP_EOL;
				if ( ! in_array( ExtraFeaturesOption::OPTION_VALUE_REFERER_DISABLED, $settings[ ExtraFeaturesOption::OPTION_NAME ] ) ) {
					$content .= "  RewriteCond %{HTTP_HOST}@@%{HTTP_REFERER} ^([^@]*)@@https?://\\1/.*" . PHP_EOL;
				}
				$content .= "  RewriteRule (.+)\.${ext}$ ${prefix_rule}${path}/$1.${ext}.${format} [NC,T=${mime_type},L]" . PHP_EOL;
			}
			$content .= '</IfModule>' . PHP_EOL;
		}

		return apply_filters( 'webpc_htaccess_mod_rewrite', trim( $content ), $path );
	}

	/**
	 * Generates rules for mod_headers.
	 *
	 * @param mixed[] $settings Plugin settings.
	 *
	 * @return string Rules for .htaccess file.
	 */
	private function get_mod_headers_rules( array $settings ): string {
		$content = '';

		$content .= '<IfModule mod_headers.c>' . PHP_EOL;
		$content .= '  Header always set Cache-Control "private"' . PHP_EOL;
		$content .= '  Header append Vary "Accept"' . PHP_EOL;
		$content .= '</IfModule>';

		return apply_filters( 'webpc_htaccess_mod_headers', $content );
	}

	/**
	 * Generates rules for mod_expires.
	 *
	 * @param mixed[] $settings Plugin settings.
	 *
	 * @return string Rules for .htaccess file.
	 */
	private function get_mod_expires_rules( array $settings ): string {
		$content = '';
		if ( ! in_array( ExtraFeaturesOption::OPTION_VALUE_MOD_EXPIRES, $settings[ ExtraFeaturesOption::OPTION_NAME ] ) ) {
			return $content;
		}

		$content .= '<IfModule mod_expires.c>' . PHP_EOL;
		$content .= '  ExpiresActive On' . PHP_EOL;
		foreach ( $this->get_mime_types() as $format => $mime_type ) {
			$content .= "  ExpiresByType ${mime_type} \"access plus 1 year\"" . PHP_EOL;
		}
		$content .= '</IfModule>';

		return apply_filters( 'webpc_htaccess_mod_expires', $content );
	}

	/**
	 * Generates rules that add support for output formats.
	 *
	 * @param mixed[] $settings Plugin settings.
	 *
	 * @return string Rules for .htaccess file.
	 */
	private function get_mod_mime_rules( array $settings ): string {
		$content = '';
		if ( ! $settings[ SupportedExtensionsOption::OPTION_NAME ] ) {
			return $content;
		}

		$content .= '<IfModule mod_mime.c>' . PHP_EOL;
		foreach ( $this->get_mime_types() as $format => $mime_type ) {
			$content .= "  AddType ${mime_type} .${format}" . PHP_EOL;
		}
		$content .= '</IfModule>';

		return apply_filters( 'webpc_htaccess_mod_mime', $content );
	}

	/**
	 * Adds comments before and after rules for .htaccess file.
	 *
	 * @param string[] $rules Rules for .htaccess file.
	 *
	 * @return string Rules for .htaccess file.
	 */
	private function add_comments_to_rules( array $rules ): string {
		if ( ! $rules ) {
			return '';
		}

		$rows   = [];
		$rows[] = '';
		$rows[] = '# BEGIN WebP Converter';
		$rows[] = '# ! --- DO NOT EDIT PREVIOUS LINE --- !';
		$rows   = array_merge( $rows, array_filter( $rules ) );
		$rows[] = '# ! --- DO NOT EDIT NEXT LINE --- !';
		$rows[] = '# END WebP Converter';
		$rows[] = '';

		return implode( PHP_EOL, $rows );
	}

	/**
	 * Saves rules to .htaccess file in selected location.
	 *
	 * @param string $path_dir Location of .htaccess file.
	 * @param string $rules    Rules for .htaccess file.
	 *
	 * @return void
	 */
	private function save_rewrites_in_htaccesss( string $path_dir, string $rules = '' ) {
		$path_file = $path_dir . '/.htaccess';

		$code = ( is_readable( $path_file ) ) ? file_get_contents( $path_file ) ?: '' : '';
		$code = preg_replace( '/((:?[\r\n|\r|\n]?)# BEGIN WebP Converter(.*?)# END WebP Converter(:?(:?[\r\n|\r|\n]+)?))/s', '', $code );
		if ( $rules && $code ) {
			$code = PHP_EOL . $code;
		}
		$code = $rules . $code;

		if ( is_writable( $path_dir ) ) {
			file_put_contents( $path_file, $code );
		}
	}
}
