<?php

class DomainCheckAdminYourDomains {

	public static $your_domains_obj = null;

	public static function your_domains() {
		global $wpdb;
		//add domain search box...
		//import domains CSV...
		//import domains XML...


		?>
		<div class="wrap">
			<h2>
				<a href="admin.php?page=domain-check" class="domain-check-link-icon">
					<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				</a>
				<img src="<?php echo plugins_url('/images/icons/color/flag.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-owned">
				<span class="hidden-mobile">Domain Check - </span>Your Domains
			</h2>
			<?php DomainCheckAdminHeader::admin_header(true, null, 'domain-check-your-domains'); ?>
			<?php self::your_domains_search_box(); ?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								self::$your_domains_obj->prepare_items();
								self::$your_domains_obj->display();
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
			<?php
			DomainCheckAdminHeader::admin_header_nav(null, 'domain-check-your-domains');
			DomainCheckAdminHeader::footer();
			?>
		</div>
		<?php
	}

	/**
	 * Screen options
	 */
	public static function your_domains_screen_option() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'Your Domains',
			'default' => 100,
			'option'  => 'domains_per_page'
		);

		add_screen_option( $option, $args );

		self::$your_domains_obj = new DomainCheck_Your_Domains_List();
	}

	public static function your_domains_search_box() {
		?>
		<script type="text/javascript">
			function domain_check_your_domains_search_click(evt) {
				document.getElementById('your-domains-search-box-form').submit();
			}
		</script>
		<form id="your-domains-search-box-form" action="" method="GET">
			<input type="text" name="domain_check_your_domains" id="domain_check_your_domains" class="domain-check-admin-search-input">
			<input type="hidden" name="page" value="domain-check-your-domains">
			<div type="button" class="button domain-check-admin-search-input-btn" onclick="domain_check_your_domains_search_click();">
				<img src="<?php echo plugins_url('/images/icons/color/flag.svg', __FILE__); ?>" class="svg svg-icon-h3 svg-fill-owned">
				<div style="display: inline-block;">Add to Your Domains</div>
			</div>
		</form>
		<?php
	}
	
}