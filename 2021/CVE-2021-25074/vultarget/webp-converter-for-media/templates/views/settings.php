<?php
/**
 * Main tab of plugin settings page.
 *
 * @var string[][] $errors_messages         .
 * @var string[]   $errors_codes            .
 * @var mixed[]    $options                 Options of plugin settings.
 * @var string     $submit_value            Value of submit button.
 * @var string     $submit_activate_token   .
 * @var string     $submit_deactivate_token .
 * @var bool       $token_valid_status      .
 * @var string     $settings_url            URL of plugin settings page (default view).
 * @var string     $settings_debug_url      URL of plugin settings page (debug view).
 * @var string     $api_calculate_url       URL of REST API endpoint.
 * @var string     $api_paths_url           URL of REST API endpoint.
 * @var string     $api_regenerate_url      URL of REST API endpoint.
 * @package WebP Converter for Media
 */

?>
<div class="wrap">
	<hr class="wp-header-end">
	<form method="post" action="<?php echo esc_url( $settings_url ); ?>" class="webpPage">
		<h1 class="webpPage__headline"><?php echo esc_html( __( 'WebP Converter for Media', 'webp-converter-for-media' ) ); ?></h1>
		<div class="webpPage__inner">
			<ul class="webpPage__columns">
				<li class="webpPage__column webpPage__column--large">
					<?php if ( isset( $_POST[ $submit_value ] ) ) : // phpcs:ignore ?>
						<div class="webpPage__alert">
							<?php echo esc_html( __( 'Changes were successfully saved!', 'webp-converter-for-media' ) ); ?>
							<?php echo esc_html( __( 'Please flush cache if you use caching plugin or caching via hosting.', 'webp-converter-for-media' ) ); ?>
						</div>
					<?php elseif ( isset( $_POST[ $submit_activate_token ] ) && $token_valid_status ) : // phpcs:ignore ?>
						<div class="webpPage__alert">
							<?php echo esc_html( __( 'The access token has been activated!', 'webp-converter-for-media' ) ); ?>
						</div>
					<?php endif; ?>
					<?php
					require_once dirname( __DIR__ ) . '/components/widgets/errors.php';
					require_once dirname( __DIR__ ) . '/components/widgets/options.php';
					require_once dirname( __DIR__ ) . '/components/widgets/regenerate.php';
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
	</form>
</div>
