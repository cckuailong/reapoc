<?php defined( 'ABSPATH' ) || exit; ?>

<?php $user = wp_get_current_user(); ?>

<section class="home-hero hero-standard hero-standard-full">
	<div class="wrapper flex <?php echo ! $this->is_scan() && ! $this->is_active() ? 'reg-form' : ''; ?> ">
		<?php if ( ! $this->is_registration_form() ) : ?>
		<div class="logo-row flex top-logo">
				<div class="accessibe-logo">
					<img class="hero-cover-image" data-lazyload-ignore="true" src="<?php echo WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/accessibe-full-logo.svg'; ?>" alt="Blind person hero image">
				</div>
				<?php if ( $this->is_scan() ) : ?>
					<form  action="<?php echo $this->get_settings_url(); ?>" method="post" class="accesibe-settings-form">
						<button type="submit" class="site-button js-active site-button-animate site-button-right-icon site-button-full" data-element="signup-button">
							<span class="align-middle">
								<?php _e( 'Create a Free Account!', 'wpcf7-redirect' ); ?> <span class="dashicons dashicons-arrow-right-alt2"></span>
							</span>
						</button>
						<input type="hidden" name="start-free-trial" value="<?php echo qs_get_plugin_display_name(); ?>" />
					</form>
				<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php
		if ( $this->is_registration_form() ) :
			?>
			<div class="main-form-wrap">
				<div class="home-hero-content hero-left">
					<div class="hero-left-inner">
						<h3 class="home-hero-title">The #1 <em>Fully</em> <em>Automated</em> <br> Web Accessibility Solution <br> for ADA &amp; WCAG Compliance</h3>
						<ul class="home-hero-checkmarks">
							<li class="home-hero-checkmark">
							<i class="icon icon-checkmark icon-pack-icomoon ">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
									<title>checkmark</title>
									<path d="M13.5 2l-7.5 7.5-3.5-3.5-2.5 2.5 6 6 10-10z"></path>
								</svg>
							</i>
							<strong>Affordable.</strong> $49/month, free trial, no credit card required
							</li>
							<li class="home-hero-checkmark">
							<i class="icon icon-checkmark icon-pack-icomoon ">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
									<title>checkmark</title>
									<path d="M13.5 2l-7.5 7.5-3.5-3.5-2.5 2.5 6 6 10-10z"></path>
								</svg>
							</i>
							<strong>Effortless.</strong> Add a single line of code for 24/7 automated compliance
							</li>
							<li class="home-hero-checkmark">
							<i class="icon icon-checkmark icon-pack-icomoon ">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
									<title>checkmark</title>
									<path d="M13.5 2l-7.5 7.5-3.5-3.5-2.5 2.5 6 6 10-10z"></path>
								</svg>
							</i>
							<strong>Compliant.</strong> Accessibility statement and certificate of performance
							</li>
						</ul>
					</div>
				</div>
				<div class="hero-right">
					<div class="hero-right-inner">
						<div class="accessibe-logo">
							<img class="hero-cover-image" data-lazyload-ignore="true" src="<?php echo WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/accessibe-full-logo.svg'; ?>" alt="Blind person hero image">
						</div>
						<h4>Create A 30-Day Trial Account</h4>
						<form name="accesibe-registration" action="" method="post" class="accesibe-registration-form-fields">
							<div class="home-hero-cta">
								<div class="field-wrap">
									<label for="email"><?php _e( 'Your Email Address', 'wpcf7-redirect' ); ?></label>
									<input type="email" name="email" value="<?php echo get_option( 'admin_email' ); ?>" required  placeholder="example@example.com"/>
								</div>
								<div class="field-wrap">
									<label for="fullname"><?php _e( 'Your Full Name', 'wpcf7-redirect' ); ?></label>
									<input type="text" name="fullname" minlength="3" value="<?php echo $user->first_name . ' ' . $user->last_name; ?>" required placeholder="John Smith"/>
								</div>
								<div class="field-wrap">
									<label for="password"><?php _e( 'Your Password', 'wpcf7-redirect' ); ?></label>
									<input type="password" name="password" minlength="8" value="" required placeholder="<?php _e( '8+ Charachters', 'wpcf7-redirect' ); ?>"/>
								</div>

								<span class="loader-spinner-container spinner-light" data-loader="spinner">
									<span class="loader-spinner"></span></span>
								</button>
							</div>
							<div class="agreement">
								<div class="highlight">
									<input type="checkbox" name="tos" id="tos" checked="" data-activate-on-check="signup-button" required>
									<label for="tos"><?php _e( 'By signing up you agree to our', 'wpcf7-redirect' ); ?> <a href="https://accessibe.com/terms-of-service" target="_blank"><?php _e( 'Terms of Service', 'wpcf7-redirect' ); ?></a></label>
								</div>
							</div>
							<div class="field-wrap">
									<button type="submit" class="site-button js-active site-button-animate site-button-right-icon site-button-full" data-element="signup-button">
										<span class="align-middle">
											<?php _e( 'Create a Free Account!', 'wpcf7-redirect' ); ?> <span class="dashicons dashicons-arrow-right-alt2"></span>
										</span>
									</button>
							</div>
							<input type="hidden" name="activate-accesibe" value="1" />
							<div class="footer-wrap">
								<a class=" align-middle" href="<?php echo admin_url( '?deactivate=' . qs_get_plugin_display_name() ); ?>">
									<?php _e( 'Hide this special offer', 'wpcf7-redirect' ); ?>
								</a>
								<a href="<?php echo $this->get_scan_link(); ?>" class="scan-again"><?php _e( 'Scan my website again.', 'wpcf7-redirect' ); ?></a>
							</div>
						</form>
					</div>
				</div>

			</div>
		</form>
		<?php elseif ( $this->is_scan() ) : ?>
			<div class="qs-row">
				<div class="qs-col">
					<div class="scanning">
						Please wait a few seconds while we process your website accessibility report.
					</div>
					<div class="scanning-tip">
						<strong>Results guide:</strong> <strong>Compliant</strong> - You're all good. <strong>Semi/Not Compliant</strong> - We recommend you to install accessiBe
					</div>
				</div>
			</div>
			<?php $url = add_query_arg( 'accesibedomainscan', str_replace( 'http:', 'https:', home_url() ), 'https://querysol.com/scan' ); ?>
			<iframe src="<?php echo $url; ?>"  scrolling="no" style=" width: 100%; height: 100vh;  overflow: hidden;" ></iframe>
			<form  action="<?php echo $this->get_settings_url(); ?>" method="post" class="accesibe-settings-form-footer">
				<button type="submit" class="site-button js-active site-button-animate site-button-right-icon site-button-full" data-element="signup-button">
					<span class="align-middle">
						<?php _e( 'Create a Free Account!', 'wpcf7-redirect' ); ?> <span class="dashicons dashicons-arrow-right-alt2"></span>
					</span>
				</button>
				<input type="hidden" name="start-free-trial" value="<?php echo qs_get_plugin_display_name(); ?>" />
			</form>
			<div class="footer-wrap">
				<a class=" align-middle" href="<?php echo admin_url( '?deactivate=' . qs_get_plugin_display_name() ); ?>">
					<?php _e( 'Hide This Special Offer', 'wpcf7-redirect' ); ?>
				</a>
			</div>
		<?php else : ?>
			<form name="accesibe-registration" action="<?php echo $this->get_settings_url(); ?>" method="post" class="accesibe-settings-form">
				<div class="success-message">
					<h3>
						Thank you for installing accessibe!
					</h3>
					<div class="success-message-subtitle">
						The plugin is now active on your website.
					</div>
					<div class="success-content">
						If you want to customize the widget settings you can do it here: <br/>
						To deactivate the plugin you can <a class=" align-middle" href="<?php echo admin_url( '?deactivate=' . qs_get_plugin_display_name() ); ?>">Click Here.</a>
					</div>
				</div>
				<?php $this->get_settings_form(); ?>

				<div class="qs-row">
					<div class="qs-col">
						<button type="submit" class="site-button js-active site-button-animate site-button-right-icon site-button-full" data-element="signup-button">
							<span class="align-middle"><?php _e( 'Save', 'wpcf7-redirect' ); ?></span>
						</button>
					</div>
				</div>
				<input type="hidden" name="save_ext_settings" value="<?php echo qs_get_plugin_display_name(); ?>" />
			</form>
		<?php endif; ?>
</section>
