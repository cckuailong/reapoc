<?php
/**
 * Widget displayed server configuration on plugin settings page.
 *
 * @var string $settings_url          URL of plugin settings page.
 * @var string $size_png_path         Size of file.
 * @var string $size_png2_path        Size of file.
 * @var string $size_png_url          Size of file.
 * @var string $size_png2_url         Size of file.
 * @var string $size_png_as_webp_url  Size of file.
 * @var string $size_png2_as_webp_url Size of file.
 * @package WebP Converter for Media
 */

?>
<div class="webpPage__widget">
	<h3 class="webpPage__widgetTitle webpPage__widgetTitle--second">
		<?php echo esc_html( __( 'Your server configuration', 'webp-converter-for-media' ) ); ?>
	</h3>
	<div class="webpContent">
		<div class="webpPage__widgetRow">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
					/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
						__( 'Please compare your configuration with the configuration that is given in the technical requirements in %1$sthe plugin FAQ%2$s. If your server does not meet the technical requirements, please contact your server Administrator.', 'webp-converter-for-media' ),
						'<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
						'</a>'
					)
				);
				?>
			</p>
			<a href="<?php echo esc_url( $settings_url ); ?>"
				class="webpLoader__button webpButton webpButton--blue"
			>
				<?php echo esc_html( __( 'Back to settings', 'webp-converter-for-media' ) ); ?>
			</a>
		</div>
		<div class="webpPage__widgetRow">
			<div class="webpServerInfo">
				<?php
				require_once dirname( __DIR__ ) . '/server/filters.php';
				require_once dirname( __DIR__ ) . '/server/wordpress.php';
				require_once dirname( __DIR__ ) . '/server/debug.php';
				require_once dirname( __DIR__ ) . '/server/gd.php';
				require_once dirname( __DIR__ ) . '/server/imagick.php';
				?>
			</div>
		</div>
		<div class="webpPage__widgetRow">
			<a href="<?php echo esc_url( $settings_url ); ?>"
				class="webpLoader__button webpButton webpButton--blue"
			>
				<?php echo esc_html( __( 'Back to settings', 'webp-converter-for-media' ) ); ?>
			</a>
		</div>
	</div>
</div>
