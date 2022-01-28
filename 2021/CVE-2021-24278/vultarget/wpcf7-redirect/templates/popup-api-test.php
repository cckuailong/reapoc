<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="wpcfr-popup-wrap wpcfr-popup-hidden <?php echo $template; ?> middle-center">
	<div class="wpcfr-popup-wrap-inner">
		<span class="dashicons dashicons-no-alt wpcfr-close-popup top-right"></span>
		<div class="wpcfr-popup-wrap-content">
			<h3>
				<?php _e( 'Record', 'wpcf7-redirect' ); ?>
			</h3>
			<div class="wrapper">
				<textarea name="name" rows="8" cols="80">
					<?php
						ob_start();

						print_r( $this->record );

						echo esc_html( ob_get_clean() );
					?>
				</textarea>
			</div>
			<h3>
				<?php _e( 'Request', 'wpcf7-redirect' ); ?>
			</h3>
			<div class="wrapper">
				<textarea name="name" rows="8" cols="80">
					<?php
					ob_start();

					print_r( $this->request );

					echo esc_html( ob_get_clean() );
					?>
				</textarea>
			</div>
			<h3>
				<?php _e( 'Response', 'wpcf7-redirect' ); ?>
			</h3>
			<div class="wrapper">
				<?php if ( is_wp_error( $this->results ) ) : ?>
					<span class="err"><?php _e( 'Error!' ); ?></span>
					<textarea name="name" rows="8" cols="80">
						<?php
						ob_start();

						print_r( $this->results );

						echo esc_html( ob_get_clean() );
						?>
					</textarea>
				<?php else : ?>
					<div class="field-wrap">
						<div class="label">
							<label>
								<?php _e( 'Response code', 'wpcf7-redirect' ); ?>
							</label>
							<span><?php echo $this->results['response']['message']; ?>(<?php echo $this->results['response']['code']; ?>)</span>
						</div>
						<textarea name="name" rows="8" cols="80">
							<?php
							ob_start();

							print_r( $this->results );

							echo esc_html( ob_get_clean() );
							?>
						</textarea>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
