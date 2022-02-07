<?php

class DomainCheckAdminCoupons {

	public static function coupons() {
		global $wpdb;

		if (isset($_GET['domain_check_coupons_site']) && DomainCheckCouponData::valid_site($_GET['domain_check_coupons_site'])) {
			self::coupons_site();
			return;
		}

		$coupons = null;
		if (isset($_GET['domain_check_coupons_search']) && $_GET['domain_check_coupons_search']) {
			$coupons = DomainCheckCouponData::search($_GET['domain_check_coupons_search']);
			$found = 0;
			foreach ($coupons as $coupon_site => $coupon_data) {
				if (isset($coupon_data['links']['link'])) {
					$found += count($coupon_data['links']['link']);
				}
			}

		}
		if (!$coupons) {
			$coupons = DomainCheckCouponData::get_data();
		}

		$coupon_last_updated = DomainCheckCouponData::last_updated();
		?>
		<div class="wrap">
			<h2>
				<a href="admin.php?page=domain-check" class="domain-check-link-icon">
					<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				</a>
				<img src="<?php echo plugins_url('/images/icons/color/055-price-tags.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-updated">
				<span class="hidden-mobile">Domain Check - </span>Coupons &amp; Deals
			</h2>
			<?php
			DomainCheckAdminHeader::admin_header();
			self::coupons_search_box();
			?>
			<?php
			if ($coupon_last_updated) {
				$updated_date = date('m-d-Y H:i:s', $coupon_last_updated);
				if ( date('m-d-Y', $coupon_last_updated) === date('m-d-Y') ) {
					$updated_date = 'Today!';
				}
			?>
				<h3>Coupons Last Updated: </strong> <?php echo $updated_date; ?></h3>
			<?php
			}
			?>
			<a href="admin.php?page=domain-check-coupons&domain_check_coupons_update=1" class="button">
				<img src="<?php echo plugins_url('/images/icons/color/303-loop2.svg', __FILE__); ?>" class="svg svg-icon-table svg-icon-table-links svg-fill-gray">
				Refresh Coupons
			</a>
			<?php
			foreach ($coupons as $coupon_site => $coupon_data) {
				?>
				<div style="clear:both;">
					<h3>
						<a href="admin.php?page=domain-check-coupons&domain_check_coupons_site=<?php echo $coupon_site; ?>">
						<?php echo ucfirst($coupon_site); ?>
						</a>
					</h3>
					<?php
					if (isset($coupon_data['links']['link']) && is_array($coupon_data['links']['link'])) {
						$coupon_ads = array();
						$text_ads = array();
						$img_ads = array();
						foreach ($coupon_data['links']['link'] as $coupon_link_idx => $coupon_link_data) {
							if (isset($coupon_link_data['link-type']) && $coupon_link_data['link-type']) {
								if ($coupon_link_data['link-type'] == 'Text Link') {
									if (isset($coupon_link_data['coupon-code'])
										&& ((is_array($coupon_link_data['coupon-code']) && count($coupon_link_data['coupon-code'])) || $coupon_link_data['coupon-code'])
									) {
										$coupon_ads[] = $coupon_link_idx;
									} else {
										$text_ads[] = $coupon_link_idx;
									}

								} else {
									$img_ads[] = $coupon_link_idx;
								}

							}
						}
					}
					?>
					<div class="coupon-ad-wrapper" style="width: 100%; float: left; display:block; clear: both;">
						<?php
						foreach ($coupon_ads as $coupon_link_idx) {
							$coupon_link_data = $coupon_data['links']['link'][$coupon_link_idx];
							?>
							<div class="domain-check-coupon-ad">
								<p style="text-align: left;">
									<strong>
										<a href="<?php echo $coupon_link_data['clickUrl']; ?>" target="_blank">
										<?php echo $coupon_link_data['link-code-html']; ?>
											</a>
									</strong>
								</p>

								<p style="text-align: center;">
									<div style="text-align: center;">
										<h3>Coupon Code: </h3>
									</div>
									<div style="text-align: center;">
										<a href="<?php echo $coupon_link_data['clickUrl']; ?>" target="_blank" style="background-color: #00AA00; color: #FFFFFF; font-size: 20px; margin: 10px; padding: 10px;">
											<strong>
												<?php echo $coupon_link_data['coupon-code']; ?>
											</strong>
										</a>
									</div>
								</p>
							</div>
						<?php } ?>
					</div>
					<div class="txt-ad-wrapper" style="width: 100%; float: left; display:inline-block;">
					<?php
					$limit = 10;
					$count = 0;
					shuffle($text_ads);
					foreach ($text_ads as $coupon_link_idx) {
						if ($count >= $limit) {
							break;
						}
						$coupon_link_data = $coupon_data['links']['link'][$coupon_link_idx];
						?>
						<div style="margin: 5px; padding: 5px; background-color: #ffffff; width: 40%; display: inline-block;" alt="<?php echo htmlentities($coupon_link_data['description']); ?>" title="<?php echo htmlentities($coupon_link_data['description']); ?>">
							<a href="<?php echo $coupon_link_data['clickUrl']; ?>" target="_blank">
							<?php
							echo $coupon_link_data['link-code-html'];
							?>
							</a>
						</div>
						<?php
						$count++;
					}
					?>
					</div>
				</div>
			<?php
			}
	}

	public static function coupons_site() {
		global $wpdb;

		$site = 'GoDaddy.com';
		if (isset($_GET['domain_check_coupons_site']) && DomainCheckCouponData::valid_site($_GET['domain_check_coupons_site'])) {
			$site = $_GET['domain_check_coupons_site'];
		}

		if (isset($_GET['coupon_search']) && $_GET['coupon_search']) {
			$coupons = DomainCheckCouponData::search($_GET['coupon_search']);
			$found = 0;
			foreach ($coupons as $coupon_site => $coupon_data) {
				if ($coupon_site == $site) {
					if (isset($coupon_data['links']['link'])) {
						$found += count($coupon_data['links']['link']);
					}
				}
			}
		} else {
			$coupons = DomainCheckCouponData::get_data();
		}
		foreach ($coupons as $coupon_site => $coupon_data) {
			if ($coupon_site != $site) {
				unset($coupons[$coupon_site]);
			}
		}

		DomainCheckAdminHeader::admin_header();
		?>
		<div class="wrap">
			<h2>
				<img src="<?php echo plugins_url('/images/icons/color/055-price-tags.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-updated">
				<?php echo $_GET['domain_check_coupons_site']; ?> Coupons
			</h2>
			<?php
			$coupon_last_updated = DomainCheckCouponData::last_updated();
			foreach ($coupons as $coupon_site => $coupon_data) {
				?>
				<div style="clear:both;">
				<h3><?php echo ucfirst($coupon_site); ?></h3>
					<?php if ($coupon_last_updated) { ?>
					<h4>Updated: <?php echo date('m-d-Y', $coupon_last_updated); ?></h4>
					<?php } ?>
				<?php
				if (isset($coupon_data['links']['link']) && is_array($coupon_data['links']['link'])) {
					$coupon_ads = array();
					$text_ads = array();
					$img_ads = array();
					foreach ($coupon_data['links']['link'] as $coupon_link_idx => $coupon_link_data) {
						if (isset($coupon_link_data['link-type']) && $coupon_link_data['link-type']) {
							if ($coupon_link_data['link-type'] == 'Text Link') {
								if (isset($coupon_link_data['coupon-code'])
									&& ((is_array($coupon_link_data['coupon-code']) && count($coupon_link_data['coupon-code'])) || $coupon_link_data['coupon-code'])) {
									$coupon_ads[] = $coupon_link_idx;
								} else {
									$text_ads[] = $coupon_link_idx;
								}

							} else {
								$img_ads[] = $coupon_link_idx;
							}

						}
					}

					?>
					<div class="coupon-ad-wrapper" style="width: 100%; float: left; display:block; clear: both;">
						<?php
						foreach ($coupon_ads as $coupon_link_idx) {
							$coupon_link_data = $coupon_data['links']['link'][$coupon_link_idx];
							?>
							<div class="domain-check-coupon-ad">
								<p style="text-align: left;">
									<strong>
										<a href="<?php echo $coupon_link_data['clickUrl']; ?>" target="_blank">
								<?php
								echo $coupon_link_data['link-code-html'];
								?>
								</a>
								<?php
								if (isset($coupon_link_data['coupon-code'])
									&& ((is_array($coupon_link_data['coupon-code']) && count($coupon_link_data['coupon-code'])) || $coupon_link_data['coupon-code'])) {
									?>
									</strong>
								</p>
								<p style="text-align: center;">
									<div style="text-align: center;">
									<h3>Coupon Code:<h3>
									</div>
									<div style="text-align: center;">
									<a href="<?php echo $coupon_link_data['clickUrl']; ?>" target="_blank" style="background-color: #00AA00; color: #FFFFFF; font-size: 20px; margin: 10px; padding: 10px;">
										<strong>
											<?php echo $coupon_link_data['coupon-code']; ?>
										</strong>
									</a>
									</div>
								</p>
								<?php
								}
								?>
							</div>
							<?php
						}
						?>
					</div>
					<div class="txt-ad-wrapper" style="width: 100%; float: left; display:inline-block;">
					<?php
					foreach ($text_ads as $coupon_link_idx) {
						$coupon_link_data = $coupon_data['links']['link'][$coupon_link_idx];
						?>
						<div style="margin: 5px; padding: 5px; background-color: #ffffff; width: 40%; display: inline-block;" alt="<?php echo htmlentities($coupon_link_data['description']); ?>" title="<?php echo htmlentities($coupon_link_data['description']); ?>">
							<a href="<?php echo $coupon_link_data['clickUrl']; ?>" target="_blank">
							<?php
							echo $coupon_link_data['link-code-html'];
							?>
							</a>
						</div>
						<?php
					}
					?>
					</div>
					<!--div class="img-ad-wrapper" style="float: left; width: 100%; display:inline-block; vertical-align: top;">
					<?php
					foreach ($img_ads as $coupon_link_idx) {
						$coupon_link_data = $coupon_data['links']['link'][$coupon_link_idx];
						?>
						<div class="domain-check-img-ad">
							<?php
							echo $coupon_link_data['link-code-html'];
							?>
						</div>
						<?php
					}
					?>
					</div-->
					<?php
				}
				?>
				</div>
				<?php
			}
			?>
			<?php
			DomainCheckAdminHeader::admin_header_nav();
			DomainCheckAdminHeader::footer();
			?>
		</div>
		<?php

	}

	public static function coupons_init() {
		if (isset($_GET['domain_check_coupons_update'])) {
			if (DomainCheckCouponData::update()) {
				DomainCheckAdmin::admin_notices_add('Coupons updated!', 'updated', null, '055-price-tags');
			} else {
				DomainCheckAdmin::admin_notices_add('Coupon update failure.', 'error', null, '055-price-tags');
			}
		}
		if (isset($_GET['domain_check_coupons_search']) && $_GET['domain_check_coupons_search']) {
			$coupons = DomainCheckCouponData::search($_GET['domain_check_coupons_search']);
			$found = 0;
			foreach ($coupons as $coupon_site => $coupon_data) {
				if (isset($coupon_data['links']['link'])) {
					$found += count($coupon_data['links']['link']);
				}
			}
			if ($found) {
				$message = 'Success! Found ' . $found . ' Coupons for "' . htmlentities($_GET['domain_check_coupons_search']) . '"!';
				DomainCheckAdmin::admin_notices_add(
					$message,
					'updated',
					null,
					'055-price-tags'
				);
			} else {
				$message = 'No Coupons found for "' . htmlentities($_GET['domain_check_coupons_search']) . '"!';
				DomainCheckAdmin::admin_notices_add(
					$message,
					'error',
					null,
					'055-price-tags'
				);
			}
		}
	}

	public static function coupons_search_box($dashboard = false) {
		$css_class = 'domain-check-admin-search-input';
		if ( $dashboard ) {
			$css_class .= '-dashboard';
		}
		$css_class_button = $css_class . '-btn';
		?>
		<script type="text/javascript">
			function domain_check_coupon_search_click(evt) {
				document.getElementById('domain-check-coupon-search-box-form').submit();
			}
		</script>
		<form id="domain-check-coupon-search-box-form" action="" method="GET">
			<input type="text" name="domain_check_coupons_search" id="domain_check_coupons_search" class="<?php echo $css_class; ?>">
			<input type="hidden" name="page" value="domain-check-coupons">
			<?php if ( !$dashboard ) { ?>
			<div type="button" class="button domain-check-admin-search-input-btn" onclick="domain_check_coupon_search_click();">
				<img src="<?php echo plugins_url('/images/icons/color/055-price-tags.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-updated">
				<div style="display: inline-block;">Search Coupons</div>
			</div>
			<?php } else { ?>
			<input type="submit" class="button" value="Search Coupons" class="<?php echo $css_class_button; ?>" />
			<?php } ?>
		</form>
		<?php
	}
}