<?php
/**
 * Modal with poll displayed in list of plugins.
 *
 * @var string[] $errors         List of errors detected by plugin.
 * @var mixed[]  $reasons        Reasons for plugin deactivation.
 * @var mixed[]  $settings       Plugin settings.
 * @var string   $api_url        URL of API for feedback request.
 * @var string   $plugin_version .
 * @package WebP Converter for Media
 */

?>
<div class="webpModal" hidden>
	<div class="webpModal__outer">
		<form action="<?php echo esc_url( $api_url ); ?>" method="POST" class="webpModal__form">
			<h2 class="webpModal__headline">
				<?php echo esc_html( __( 'We are sorry that you are leaving our plugin WebP Converter for Media', 'webp-converter-for-media' ) ); ?>
			</h2>
			<div class="webpModal__desc">
				<?php echo esc_html( __( 'Can you please take a moment to tell us why you are deactivating this plugin (your answer is completely anonymous)?', 'webp-converter-for-media' ) ); ?>
			</div>
			<div class="webpModal__options">
				<?php foreach ( $reasons as $index => $reason ) : ?>
					<div class="webpField">
						<input type="radio"
							name="webpc_reason"
							value="<?php echo esc_attr( $reason['key'] ); ?>"
							id="webpc-option-<?php echo esc_attr( $index ); ?>"
							class="webpField__input webpField__input--radio"
							data-placeholder="<?php echo esc_attr( $reason['placeholder'] ); ?>"
						>
						<label for="webpc-option-<?php echo esc_attr( $index ); ?>"></label>
						<span class="webpField__label"><?php echo esc_html( $reason['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
			<textarea class="webpModal__textarea" name="webpc_comment" rows="2"></textarea>
			<ul class="webpModal__buttons">
				<li class="webpModal__button">
					<button type="submit" class="webpModal__buttonInner webpButton webpButton--green">
						<?php echo esc_html( __( 'Submit and Deactivate', 'webp-converter-for-media' ) ); ?>
					</button>
				</li>
				<li class="webpModal__button">
					<button type="button" class="webpModal__buttonInner webpButton webpButton--blue">
						<?php echo esc_html( __( 'Skip and Deactivate', 'webp-converter-for-media' ) ); ?>
					</button>
				</li>
			</ul>
			<input type="hidden" name="webpc_error_codes"
				value="<?php echo esc_attr( implode( ',', $errors ) ); ?>"
			>
			<input type="hidden" name="webpc_plugin_settings"
				value='<?php echo json_encode( $settings ); ?>'
			>
			<input type="hidden" name="webpc_plugin_version"
				value="<?php echo esc_attr( $plugin_version ); ?>"
			>
		</form>
	</div>
</div>
