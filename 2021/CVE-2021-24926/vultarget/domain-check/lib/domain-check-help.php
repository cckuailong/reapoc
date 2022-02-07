<?php
class DomainCheckHelp {

	private static $data;
	private static $is_init = false;

	public static function init() {
		if (self::$is_init) {
			return;
		}

		//overview
		ob_start();
		?>
		Domain Check is a Wordpress plugin that lets you monitor and manage your domain names and SSL certificates from within your own Wordpress blog. You can easily search domain names, import your existing domains, check your SSL certificates, and set up domain expiration alerts and SSL expiration alerts. Domain Check runs on your blog and the lookups are all from your server. Every day the daily coupons from all the major domain registrars and hosting websites are collected and listed in the plugin so you no longer need to search to find a working coupon code when it is time to renew or you want a domain. Domain Check supports hundreds of major domain extensions and TLDs. Use Domain Check to track your domains across multiple registrars, montior domains for your business and clients, and be on top of expiration dates before they happen. Domain Check is 100% free.
		<?php
		self::$data['overview'] = ob_get_contents();
		ob_end_clean();

		//FAQ
		ob_start();
		?>
		<strong>
		Q: Does Domain Check let me change my DNS records?
		</strong>
		<br>
		A: No. Domain Check only allows you to search and monitor domain names it does not allow you to change anything about DNS or WHOIS.
		<br><br>
		<strong>
		Q: Does Domain Check let me change my MX records?
		</strong>
		<br>
		A: No. Domain Check only allows you to search and monitor domain names it does not allow you to change anything about MX, DNS, or WHOIS.
		<br><br>
		<strong>
		Q: Does Domain Check give people access to my GoDaddy / Bluehost / Hostmonster / etc. account?
		</strong>
		<br>
		A: No. Domain Check does not require any logins, passwords, or access to any of your domain registrar, SSL certificate provider, or hosting company.
		<br><br>
		<strong>
		Q: One of the coupon codes or deals I want does not work or expired, what should I do?
		</strong>
		<br>
		A: Be sure to go to the Coupons page and refresh the coupons to make sure you have the most up-to-date coupons available. If it still is available but the site is not giving you the coupon please contact their custom support directly.
		<br><br>
		<strong>
		Q: Domain Check does not support the domain extension I use, what should I do?
		</strong>
		<br>
		A: Domain Check is updated with new extensions and TLDs as they become available. Please keep up with new updates and your extension will become available.
		<br><br>
		<strong>
		Q: Does Domain Check track my domain searches and lookups and send them to a third party or make the public?
		</strong>
		<br>
		A: No. Domain Check does the opposite and allows you to do all your domain name searching privately from your own server and blog. Using Domain Check is a more secure way to search domain names and prevent domain name frontrunning.
		<br><br>
		<?php
		self::$data['page_faq'] = ob_get_contents();
		ob_end_clean();


		//dashboard
		ob_start();
		?>
		The dashboard is intended to give easy access to the major sections of Domain Check and also to offer a quick way to visually see what domains and SSL certificates are coming up for expiration and to quickly locate the coupons, coupon codes, and deals from your favorite sites before renewing. Use any of the search boxes to check for domains and search available domain names as click through to any domain or other section within Domain Check.
		<?php
		self::$data['page_dashboard'] = ob_get_contents();
		ob_end_clean();

		//page - your domains
		ob_start();
		?>
		Marking a domain as Owned will show it within the Your Domains section. This is used to easily filter out the domain names within Domain Check that you own. You can use this section to easily filter your own domains and add expiration notifications and domain expiration alerts to all of your domains. Searching a domain from the Your Domains section will automatically mark it as Owned.
		<?php
		self::$data['page_your_domains'] = ob_get_contents();
		ob_end_clean();

		//page - domain search
		ob_start();
		?>
		Domain Search is where you can search the availability of domain names and see a history of your past domain name searches. Search any of the available domain extensions and TLDs within Domain Check, mark domains as Owned or Taken and set a domain expiration notification for a domain. You can easily click from any search result to see other domains available with the name name but a different domain extension.
		<?php
		self::$data['page_domain_search'] = ob_get_contents();
		ob_end_clean();

		//page - domain watch
		ob_start();
		?>
		Domain Watch lets you see at a glance which domains you've set up domain expiration alerts for and allows you to view at a glance when your domains are expiring. You can see domains that are Owned or Taken allowing you to monitor other domains you may want backorder to keep track of expiration dates on.
		<?php
		self::$data['page_domain_watch'] = ob_get_contents();
		ob_end_clean();

		ob_start();
		?>
		SSL Check is where you can search to see the status of your SSL certificates and check if your current SSL certificates are valid. See all your previous SSL checks easily so you can keep checking all your certificates and mark any of them as needing SSL expirtation notification alerts. See at a glance which SSL certificates are valid and which sites are not secure.
		<?php
		self::$data['page_ssl_check'] = ob_get_contents();
		ob_end_clean();

		ob_start();
		?>
		SSL Expiration Alerts allow you to monitor your SSL certificates and send out SSL certificate expiration notifications and alerts. All of the SSL certificates and domains you are monitoring are seen in this SSL Expiration Alerts list and you can refresh or remove any SSL certificate at any time.
		<?php
		self::$data['page_ssl_watch'] = ob_get_contents();
		ob_end_clean();

		ob_start();
		?>
		Import / Export is how you get your domain names and SSL certificates in to Domain Check. You can use any CSV or XML outputs from your domain registrar or even just highlight you entire list of domains and copy and paste. Domain Check will find any domain names within the CSV, XML, or copy and pasted text and allow you to bulk import your domain names. You can also bulk import SSL certificate URLs and mark any bulk imports to set all domains or SSL certificates to have expiration alerts;
		<?php
		self::$data['page_import_export'] = ob_get_contents();
		ob_end_clean();

		ob_start();
		?>
		Like most plugins Domain Check allows you to adjust certain settings within the plugin to help you stay on top your domains and domain names.
		<?php
		self::$data['page_settings'] = ob_get_contents();
		ob_end_clean();

		ob_start();
		?>
		The Coupons and Deals section of the plugin is where you can view all of the coupons or deals from major domain registrars, SSL certificate providers, and hosting companies. Refresh the coupons to get the most up to date daily coupon codes.
		<?php
		self::$data['page_coupons'] = ob_get_contents();
		ob_end_clean();

		self::$is_init = true;
	}

	public static function get_help($name) {
		self::init();
		if (isset(self::$data[$name])) {
			return self::$data[$name];
		}
		return null;
	}
}