<?php
/**
 * Debug tab of plugin settings page.
 *
 * @var string $settings_url          URL of plugin settings page (default view).
 * @var string $size_png_path         Size of file.
 * @var string $size_png2_path        Size of file.
 * @var string $size_png_url          Size of file.
 * @var string $size_png2_url         Size of file.
 * @var string $size_png_as_webp_url  Size of file.
 * @var string $size_png2_as_webp_url Size of file.
 * @package WebP Converter for Media
 */

?>
<div class="wrap">
	<hr class="wp-header-end">
	<div class="webpPage">
		<h1 class="webpPage__headline"><?php echo esc_html( __( 'WebP Converter for Media', 'webp-converter-for-media' ) ); ?></h1>
		<div class="webpPage__inner">
			<ul class="webpPage__columns">
				<li class="webpPage__column webpPage__column--large">
					<?php
					require_once dirname( __DIR__ ) . '/components/widgets/server.php';
					?>
				</li>
				<li class="webpPage__column webpPage__column--small">
					<?php
					require_once dirname( __DIR__ ) . '/components/widgets/about.php';
					require_once dirname( __DIR__ ) . '/components/widgets/support.php';
					require_once dirname( __DIR__ ) . '/components/widgets/donate.php';
					?>
				</li>
			</ul>
		</div>
	</div>
</div>
