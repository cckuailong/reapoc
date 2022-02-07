<?php
class DomainCheckAdminDashboard {
	public static function dashboard()
	{
		global $wpdb;
		DomainCheckAdminHeader::admin_header(false);
		?>
				<div class="wrap">
					<h2>
						<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
						Domain Check - Dashboard
					</h2>
					<div class="domain-check-dasboard-wrap">
					<?php DomainCheckAdminHeader::admin_header_nav(null, 'domain-check'); ?>
					<div class="domain-check-admin-dashboard-search-box">
					<h3>
						<a href="<?php echo admin_url('admin.php?page=domain-check-search'); ?>">
						<img src="<?php echo plugins_url('/images/icons/color/magnifying-glass.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-gray">
						Domain Search
						</a>
					</h3>
					<?php
					DomainCheckAdminSearch::search_box(true);
					?>
						<h3>
							<a href="<?php echo admin_url('admin.php?page=domain-check-your-domains'); ?>">
							<img src="<?php echo plugins_url('/images/icons/color/flag.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-owned">
							Your Domains
							</a>
						</h3>
						<table style="width: 100%;">
						<?php
		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_domains WHERE status = 2 AND domain_expires > 0 ORDER BY domain_expires ASC LIMIT 0, 10';
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		if (count($result)) {
			foreach ($result as $item) {
				if (isset($item['domain_expires']) && $item['domain_expires']) {
					$expire_days_number = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
					$expire_days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0) . ' Days';
					$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
					$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
					if ($days_flat < 60) {
						$fill = 'gray';
						if ($days_flat < 30) {
							$fill = 'update-nag';
						}
						if ($days_flat < 10) {
							$fill = 'error';
						}
						if ($days_flat < 3) {
							$fill = 'red';
						}
						if ($expire_days_number < 0) {
							$expire_days = 'Expired';
						}
						$expire_days = '<img src="' . plugins_url('/images/icons/color/clock-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-' . $fill . '">' . $expire_days;
					}
				} else {
					$expire_days = 'n/a';
				}
				$mobile_class = '';
				if ( $expire_days === 'n/a' || $expire_days_number >= 60 ) {
					$mobile_class = ' hidden-mobile';
				}
				?>
				<tr class="domain-check-dasboard-table-tr<?php echo $mobile_class; ?>">
					<td>
						<strong>
							<a href="?page=domain-check-profile&domain=<?php echo $item['domain_url']; ?>"><?php echo $item['domain_url']; ?></a>
						</strong>
					</td>
					<td><?php
						echo $expire_days;
						?></td>
					<td><?php
						if (isset($item['domain_expires']) && $item['domain_expires']) {
							$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
							$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
							if ($days_flat < 60) {
								?>
								<a href="?page=domain-check&domain_check_search=<?php echo $item['domain_url']; ?>"
								   class="button">
									<img src="<?php echo plugins_url('/images/icons/color/303-loop2.svg', __FILE__); ?>"
										 class="svg svg-icon-table svg-icon-table-links svg-fill-gray">
								</a>
								<a class="button" href="<?php echo DomainCheckLinks::homepage($item['domain_url']); ?>"
								   target="_blank">
									Renew
								</a>
								<?php
							}
						}
						?></td>
				</tr>
				<?php
			}
		} else {

		}
		?>
						</table>
						<h3>
							<a href="<?php echo admin_url('admin.php?page=domain-check-watch'); ?>">
							<img src="<?php echo plugins_url('/images/icons/color/207-eye.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-gray">
							Watched Domains
							</a>
						</h3>
						<table style="width: 100%;">
						<?php
		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_domains WHERE domain_watch > 0 AND domain_expires > 0 ORDER BY domain_expires ASC LIMIT 0, 10';
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		if (count($result)) {
			foreach ($result as $item) {
				if (isset($item['domain_expires']) && $item['domain_expires']) {
					$expire_days_number = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
					$expire_days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0) . ' Days';
					$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
					$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
					if ($days_flat < 60) {
						$fill = 'gray';
						if ($days_flat < 30) {
							$fill = 'update-nag';
						}
						if ($days_flat < 10) {
							$fill = 'error';
						}
						if ($days_flat < 3) {
							$fill = 'red';
						}
						if ($expire_days_number < 0) {
							$expire_days = 'Expired';
						}
						$expire_days = '<img src="' . plugins_url('/images/icons/color/clock-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-' . $fill . '">' . $expire_days;
					}
				} else {
					$expire_days = 'n/a';
				}
				$mobile_class = '';
				if ( $expire_days === 'n/a' || $expire_days_number >= 60 ) {
					$mobile_class = ' hidden-mobile';
				}
				?>
				<tr class="domain-check-dasboard-table-tr<?php echo $mobile_class; ?>">
					<td>
						<strong>
							<a href="?page=domain-check-profile&domain=<?php echo $item['domain_url']; ?>"><?php echo $item['domain_url']; ?></a>
						</strong>
					</td>
					<td><?php echo $expire_days; ?></td>
					<td><?php
						if (isset($item['domain_expires']) && $item['domain_expires']) {
							$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
							$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
							if ($days_flat < 60) {
								?>
								<a href="?page=domain-check&domain_check_search=<?php echo $item['domain_url']; ?>"
								   class="button">
									<img src="<?php echo plugins_url('/images/icons/color/303-loop2.svg', __FILE__); ?>"
										 class="svg svg-icon-table svg-icon-table-links svg-fill-gray">
								</a>
								<a href="<?php echo DomainCheckLinks::homepage($item['domain_url']); ?>" class="button"
								   target="_blank">
									Renew
								</a>
								<?php
							}
						}
						?></td>
				</tr>
				<?php
			}
		} else {

		}
		?>
						</table>
					</div>
					<div class="domain-check-admin-dashboard-search-box">
						<h3>
							<a href="<?php echo admin_url('admin.php?page=domain-check-ssl-check'); ?>">
							<img src="<?php echo plugins_url('/images/icons/color/lock-locked-yellow.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-update-nag">
							SSL Check
							</a>
						</h3>
					<?php
		DomainCheckAdmin::ssl_search_box(true);
		?>
					<h3>
						<a href="<?php echo admin_url('admin.php?page=domain-check-ssl-watch'); ?>">
						<img src="<?php echo plugins_url('/images/icons/color/bell.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-gray">
						SSL Expiration Alerts
						</a>
					</h3>
					<table style="width: 100%;">
					<?php
		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl WHERE domain_watch > 0 ORDER BY domain_expires ASC LIMIT 0, 10';
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		if (count($result)) {
			foreach ($result as $item) {
				if (isset($item['domain_expires']) && $item['domain_expires']) {
					$expire_days_number = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
					$expire_days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0) . ' Days';
					$expire_days = '<span class="hidden-desktop"><br /></span>' . $expire_days;
					$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
					$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
					if ($days_flat < 60) {
						$fill = 'gray';
						if ($days_flat < 30) {
							$fill = 'update-nag';
						}
						if ($days_flat < 10) {
							$fill = 'error';
						}
						if ($days_flat < 3) {
							$fill = 'red';
						}
						if ($expire_days_number < 0) {
							$expire_days = 'Expired';
						}
						$expire_days = '<img src="' . plugins_url('/images/icons/color/clock-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-' . $fill . '">' . $expire_days;
					} else {
						$expire_days = '<img src="' . plugins_url('/images/icons/color/lock-locked-updated.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-updated">' . $expire_days;
					}
				} else {
					$expire_days = $expire_days = '<img src="' . plugins_url('/images/icons/color/lock-unlocked.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-error"> ' . 'Not Secure';
				}
				$mobile_class = '';
				if ( $expire_days === 'n/a' || $expire_days_number >= 60 ) {
					$mobile_class = ' hidden-mobile';
				}
				?>
				<tr class="domain-check-dasboard-table-tr<?php echo $mobile_class; ?>">
					<td>
						<strong>
							<a href="?page=domain-check-ssl-profile&domain=<?php echo $item['domain_url']; ?>">
								<?php echo $item['domain_url']; ?></a>
						</strong>
					</td>
					<td><?php echo $expire_days; ?></td>
					<td><?php
						if (isset($item['domain_expires']) && $item['domain_expires']) {
							$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
							$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
							if ($days_flat < 60) {
								?>
								<a href="?page=domain-check&domain_check_ssl_search=<?php echo $item['domain_url']; ?>"
								   class="button">
									<img src="<?php echo plugins_url('/images/icons/color/303-loop2.svg', __FILE__); ?>"
										 class="svg svg-icon-table svg-icon-table-links svg-fill-gray">
								</a>
								<a href="<?php echo DomainCheckLinks::ssl($item['domain_url']); ?>" class="button"
								   target="_blank">
									Renew
								</a>
								<?php
							}
						} else {
							?>
							<a href="?page=domain-check&domain_check_ssl_search=<?php echo $item['domain_url']; ?>"
							   class="button">
								<img src="<?php echo plugins_url('/images/icons/color/303-loop2.svg', __FILE__); ?>"
									 class="svg svg-icon-table svg-icon-table-links svg-fill-gray">
							</a>
							<a href="<?php echo DomainCheckLinks::ssl($item['domain_url']); ?>" class="button"
							   target="_blank">
								Fix
							</a>
							<?php
						}
						?></td>
				</tr>
				<?php
			}
		} else {

		}
		?>
					</table>
					<h3>
						<a href="<?php echo admin_url('admin.php?page=domain-check-ssl-watch'); ?>">
						<img src="<?php echo plugins_url('/images/icons/color/lock-locked-green.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-green">
						Recent SSL Checks
						</a>
					</h3>
					<table style="width: 100%;">
					<?php
		$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_ssl ORDER BY search_date DESC LIMIT 0, 10';
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		if (count($result)) {
			foreach ($result as $item) {
				if (isset($item['domain_expires']) && $item['domain_expires']) {
					$expire_days_number = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
					$expire_days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0) . ' Days';
					$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
					$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
					if ($days_flat < 60) {
						$fill = 'gray';
						if ($days_flat < 30) {
							$fill = 'update-nag';
						}
						if ($days_flat < 10) {
							$fill = 'error';
						}
						if ($days_flat < 3) {
							$fill = 'red';
						}
						if ($expire_days_number < 0) {
							$expire_days = 'Expired';
						}
						$expire_days = '<img src="' . plugins_url('/images/icons/color/clock-' . $fill . '.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-' . $fill . '">' . $expire_days;
					} else {
						$expire_days = '<img src="' . plugins_url('/images/icons/color/lock-locked-updated.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-updated">' . $expire_days;
					}

				} else {
					$expire_days = '<img src="' . plugins_url('/images/icons/color/lock-unlocked.svg', __FILE__) . '" class="svg svg-icon-table svg-fill-error">' . 'Not Secure';
				}
				$mobile_class = '';
				if ( $expire_days === 'n/a' || $expire_days_number >= 60 ) {
					$mobile_class = ' hidden-mobile';
				}
				?>
				<tr class="domain-check-dasboard-table-tr<?php echo $mobile_class; ?>">
					<td>
						<strong>
							<a href="?page=domain-check-ssl-profile&domain=<?php echo $item['domain_url']; ?>">
								<?php echo $item['domain_url']; ?></a>
						</strong>
					</td>
					<td><?php echo $expire_days; ?></td>
					<td><?php
						if (isset($item['domain_expires']) && $item['domain_expires']) {
							$days = number_format(($item['domain_expires'] - time()) / 60 / 60 / 24, 0);
							$days_flat = (int)floor(($item['domain_expires'] - time()) / 60 / 60 / 24);
							if ($days_flat < 60) {
								?>
								<a href="?page=domain-check&domain_check_ssl_search=<?php echo $item['domain_url']; ?>"
								   class="button">
									<img src="<?php echo plugins_url('/images/icons/color/303-loop2.svg', __FILE__); ?>"
										 class="svg svg-icon-table svg-icon-table-links svg-fill-gray">
								</a>
								<a href="<?php echo DomainCheckLinks::ssl($item['domain_url']); ?>" class="button"
								   target="_blank">
									Renew
								</a>
								<?php
							}
						} else {
							?>
							<a href="?page=domain-check&domain_check_ssl_search=<?php echo $item['domain_url']; ?>"
							   class="button">
								<img src="<?php echo plugins_url('/images/icons/color/303-loop2.svg', __FILE__); ?>"
									 class="svg svg-icon-table svg-icon-table-links svg-fill-gray">
							</a>
							<a href="<?php echo DomainCheckLinks::ssl($item['domain_url']); ?>" class="button"
							   target="_blank">
								Fix
							</a>
							<?php
						}
						?></td>
				</tr>
				<?php
			}
		} else {

		}
		?>
					</table>
					</div>
					<div class="domain-check-admin-dashboard-search-box">
						<h3>
							<a href="<?php echo admin_url('admin.php?page=domain-check-coupons'); ?>">
							<img src="<?php echo plugins_url('/images/icons/color/055-price-tags.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-green">
							Coupons &amp; Deals
							</a>
						</h3>
					<?php
		DomainCheckAdminCoupons::coupons_search_box(true);
		?>
		<?php
		$coupon_last_updated = DomainCheckCouponData::last_updated();
		if ($coupon_last_updated) {
			$updated_date = date('m-d-Y', $coupon_last_updated);
			if ( date('m-d-Y', $coupon_last_updated) === date('m-d-Y') ) {
				$updated_date = 'Today!';
			}
		} else {
			$updated_date = 'EXPIRED. Please Refresh.';
		}

		?>
					<h3>Coupons Updated: <?php echo $updated_date; ?></h3>
					<a href="admin.php?page=domain-check&domain_check_coupons_update=1" class="button">
						<img src="<?php echo plugins_url('/images/icons/color/303-loop2.svg', __FILE__); ?>" class="svg svg-icon-table svg-icon-table-links svg-fill-gray">
						Refresh Coupons
					</a>
						<style type="text/css">
							.dashboard-coupon-table {

							}
						</style>
					<?php
		$coupons = DomainCheckCouponData::get_data();
		$coupon_site_counter = 0;
		foreach ($coupons as $coupon_site => $coupon_data) {
			?>
					<h3>
						<a href="admin.php?page=domain-check-coupons&domain_check_coupons_site=<?php echo $coupon_site; ?>">
						<?php echo ucfirst($coupon_site); ?>
						<div style="float:right; display: inline-block; font-size: 12px;">More [&raquo;]</div>
						</a>
					</h3>
					<table id="dashboard-coupon-table-<?php echo $coupon_site_counter; ?>" class="dashboard-coupon-table">
					<?php
			if (count($coupon_data['links']['link'])) {
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

				$limit = 3;
				$count = 0;
				foreach ($coupon_ads as $coupon_link_idx) {
					$coupon_link_data = $coupon_data['links']['link'][$coupon_link_idx];
					if ($count >= $limit) {
						break;
					}
					?>
							<tr style="background-color: #FFFFFF; color: #FFFFFF;">
								<td style="overflow: hidden;">
							<div class="domain-check-coupon-ad domain-check-coupon-ad-dashboard">
									<strong>
									<a href="<?php echo $coupon_link_data['clickUrl']; ?>" target="_blank">
								<?php
					echo $coupon_link_data['link-code-html'];
					if (isset($coupon_link_data['coupon-code'])
						&& ((is_array($coupon_link_data['coupon-code']) && count($coupon_link_data['coupon-code'])) || $coupon_link_data['coupon-code'])
					) {
						?>
						</a>
						</strong>
						</div>
						</td>
						<td style="border:1px #000000 dashed; width: 20%;">
							<div style="text-align: center;">
								<a href="<?php echo $coupon_link_data['clickUrl']; ?>" style="color:#000000;"
								   target="_blank">
									<strong>
										<?php echo $coupon_link_data['coupon-code']; ?>
									</strong>
								</a>
							</div>
						</td>
						<?php
					}
					?>
								</div>
								</td>
							</tr>
							<?php
					$count++;
				}
				shuffle($text_ads);
				foreach ($text_ads as $coupon_link_idx) {
					if ($count >= $limit) {
						break;
					}
					$coupon_link_data = $coupon_data['links']['link'][$coupon_link_idx];
					?>
					<tr>
						<td colspan="1">
							<div alt="<?php echo htmlentities($coupon_link_data['description']); ?>"
								 title="<?php echo htmlentities($coupon_link_data['description']); ?>">
								<a href="<?php echo $coupon_link_data['clickUrl']; ?>" target="_blank">
									<?php
									echo $coupon_link_data['link-code-html'];
									?>
								</a>
							</div>
						</td>
					</tr>
					<?php
					$count++;
				}
				?>
						</div>
						<?php
			} else {

			}
			?>
					</table>
					<?php
		}
		$coupon_site_counter++;
		?>
				</div>
				</div>
			<?php
	}
}