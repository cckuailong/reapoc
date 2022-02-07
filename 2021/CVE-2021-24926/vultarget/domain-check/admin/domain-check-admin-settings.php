<?php

class DomainCheckAdminSettings {
	public static function settings() {

		if (function_exists('get_option')) {
			$admin_email = get_option('admin_email');
			if (get_option(DomainCheckConfig::OPTIONS_PREFIX . 'email_primary_email')) {
				$admin_email = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'email_primary_email');
			}
			if ($admin_email) {
				$emails[strtolower($admin_email)] = array(
					'owned' => array(),
					'taken' => array(),
					'ssl'	=> array()
				);
			}
			$blog_name = get_option('blogname');
			$site_url = get_option('site_url');
		}
		$domain_autosuggest_enabled = true;

		$email_additional_emails = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'email_additional_emails');
		if (is_array($email_additional_emails) && count($email_additional_emails)) {
			$email_additional_emails = implode("\n", $email_additional_emails);
		} else {
			$email_additional_emails = '';
		}

		$domain_extension_favorites = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'domain_extension_favorites');
		if (is_array($domain_extension_favorites) && count($domain_extension_favorites)) {
			foreach ($domain_extension_favorites as $fav_idx => $fav) {
				$domain_extension_favorites[$fav_idx] = '.' . $fav;
			}
			$domain_extension_favorites = implode("\n", $domain_extension_favorites);

		} else {
			$domain_extension_favorites = DomainCheckConfig::$options[DomainCheckConfig::OPTIONS_PREFIX . 'settings']['domain_extension_favorites'];
			foreach ($domain_extension_favorites as $fav_idx => $fav) {
				$domain_extension_favorites[$fav_idx] = '.' . $fav;
			}
			$domain_extension_favorites = implode("\n", $domain_extension_favorites);
			$domain_extension_favorites = '';
		}

		$coupons_primary_site = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'coupons_primary_site');
		if (!$coupons_primary_site) {
			$coupons_primary_site = 'GoDaddy';
		}

		?>
		<script type="text/javascript">

		</script>
		<div class="wrap">
			<h2>
				<a href="admin.php?page=domain-check" class="domain-check-link-icon">
					<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				</a>
				<img src="<?php echo plugins_url('/images/icons/color/cog.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				<span class="hidden-mobile">Domain Check - </span>Settings
			</h2>
			<?php DomainCheckAdminHeader::admin_header(); ?>
			<div class="setting-wrapper">
				<div class="setting-div">
					<h3>Additional Email Addresses</h3>
					<p class="p">
						These email addresses will automatically be added to any alerts for domains, SSL certificates, or hosting. These email addresses will receive alert emails and can also be individually removed for alerts. Use this to add multiple people, email inboxes, or ticket support systems to alerts. One email address per line.
					</p>
					<div class="setting-input">
						<textarea id="email-additional-emails" class="domain-check-text-input domain-check-profile-settings-textarea" style="width: 100%; height: 200px;"><?php echo $email_additional_emails; ?></textarea>
					</div>
					<br>
					<a class="button" onclick="update_setting('email_additional_emails');">
						Update Additional Emails
					</a>
				</div>
				<div class="setting-div">
					<h3>Primary Email Address</h3>
					<p class="p">
						This is the default email used for all alerts. If you have a separate Technical Contact you may want to set them to this email address.
					</p>
					<input type="text" id="email-primary-email" value="<?php echo $admin_email; ?>">
					<br><br>
					<a class="button" onclick="update_setting('email_primary_email');">
						Update Primary Email Address
					</a>
				</div>
				<div class="setting-div">
					<div class="setting-label">
						<h3>Favorite Domain Extensions:</h3>
						<p class="p">
							These extensions will automatically be searched or listed with your domain search results. The default list contains some of the current most popular domain extensions. One extension per line.
						</p>
					</div>
					<div class="setting-input">
						<textarea id="domain-extension-favorites" rows="10" class="domain-check-text-input domain-check-profile-settings-textarea" style="width: 100%;"><?php echo $domain_extension_favorites; ?></textarea>
						<br><br>
						<a class="button" onclick="update_setting('domain_extension_favorites');">
							Update Favorite Extensions
						</a>
					</div>
				</div>
				<div class="setting-div" style="display: none;">
					<div class="setting-label">
						<h3>Domain Autosuggest:</h3>
						<p class="p">
							Autosuggest is when you search a domain using the search box and your favorite domain extensions are automatically searched on your domain search results page. If you turn Domain Autosuggest off you will still be able to easily click to search your domain name with your favorite domain extensions.
						</p>
					</div>
					<div class="setting-input">
						<input type="checkbox" id="domain-autosuggest-enabled"<?php echo ($domain_autosuggest_enabled) ? ' checked' : ''; ?>>&nbsp; Domain Autosuggest: <?php echo ($domain_autosuggest_enabled) ? 'Enabled' : 'Disabled'; ?>
					</div>
					<a class="button">
						Update Autosuggest
					</a>
				</div>
				<div class="setting-div">
					<div class="setting-input">
						<h3>Primary Coupon Site</h3>
						<p class="p">
							This should be set to the registrar you regularly use or the site you most. Your coupon site controls links in domain searches, domain expiration alerts, email links, and dashboard links for easy clicking.
						</p>
						<select id="coupons-primary-site">
						<?php
						$coupon_sites = array_keys(DomainCheckCouponData::get_data());
						foreach ($coupon_sites as $coupon_site) {
							$selected = '';
							$tmp_coupon_site = strtolower(trim($coupon_site));
							$tmp_coupons_primary_site = strtolower(trim($coupons_primary_site));
							if ($tmp_coupon_site == $tmp_coupons_primary_site) {
								$selected = ' selected';
							}
							?>
						<option value="<?php echo $coupon_site; ?>"<?php echo $selected; ?>><?php echo $coupon_site; ?></option>
						<?php } ?>
						</select>
						<a class="button"  onclick="update_setting('coupons_primary_site');">
							Update Primary Coupon Site
						</a>
					</div>
				</div>

				<div class="setting-div" style="display: none;">
					<div class="setting-label">
						<h4>Favorite Domain Registrars:</h4>
						<p class="p">
							Choose which domain registrar is your primary registrar and list your favorite registrars to get coupons for the sites you use. Your primary domain registrar controls links in domain searches, domain expiration alerts, email links, and dashboard links. The primary domain registrar is set to GoDaddy by default.
						</p>
					</div>
						<div class="setting-input">
						<h4>All Favorite Registrars</h4>
						<p class="p">
							Most people and organizations have domains at multiple registrars and use many different domain registration sites. Check all the domain registrars listed below that you use to see more coupons, deals, and links for your favorite domain registrars.
						</p>
						<a class="button">
							Update Favorite Registrars
						</a>
					</div>
				</div>
				<div class="setting-div" style="display: none;">
					<div class="setting-label">
						<h3>Coupon Updates:</h3>
						<p class="p"></p>
					</div>
					<div class="setting-input">
						<input type="checkbox" id="domain-autosuggest-enabled"<?php echo ($domain_autosuggest_enabled) ? ' checked' : ''; ?>>&nbsp; Domain Autosuggest: <?php echo ($domain_autosuggest_enabled) ? 'Enabled' : 'Disabled'; ?>
					</div>
					<a class="button">
						Update Autosuggest
					</a>
				</div>
			</div>
			<?php
			DomainCheckAdminHeader::admin_header_nav();
			DomainCheckAdminHeader::footer();
			?>
		</div>

		<?php
	}

}