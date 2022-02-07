<?php

class DomainCheckAdminImportExport {
	
	public static function import_export() {
		global $wpdb;
		?>
		<div class="wrap">
			<h2>
				<a href="admin.php?page=domain-check" class="domain-check-link-icon">
					<img src="<?php echo plugins_url('/images/icons/color/circle-www2.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				</a>
				<img src="<?php echo plugins_url('/images/icons/color/data-transfer-upload.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				<img src="<?php echo plugins_url('/images/icons/color/data-transfer-download.svg', __FILE__); ?>" class="svg svg-icon-h1 svg-fill-gray">
				<span class="hidden-mobile">Domain Check - </span>Import / Export
			</h2>
			<?php DomainCheckAdminHeader::admin_header(); ?>
			<script type="text/javascript">
				function unique_domains(domain_array) {
					var unique_array = new Array();
					for (var i = 0; i < domain_array.length; i++ ) {
						var found_domain = false;
						for (var j = 0; j< unique_array.length;j++ ) {
							if (unique_array[j].toLowerCase() == domain_array[i].toLowerCase()) {
								found_domain = true;
								break;
							}
						}
						if (!found_domain) {
							unique_array.push(domain_array[i].toLowerCase());
						}
					}
					return unique_array;
				}

				function strip(arrayName) {
					var newArray = new Array();
					label:for (var i = 0; i < arrayName.length; i++ ) {
						//is there a list of reserved words that cannot be used for TLDs?
						if (arrayName[i].toLowerCase().match(/(pdf|php|html|htm|doc|txt|asp|aspx|cfm|sh|png|pdf|jpeg|jpg|gif|swf)$/)) {
							continue label;
						}
						newArray[newArray.length] = arrayName[i].toLowerCase();
					}
					return newArray;
				}

				function import_text_get_domains(import_data) {
					//a little sensitive but works...
					var regex_all_domains = eval("/([a-zA-Z0-9][-a-zA-Z0-9]*[a-zA-Z0-9]|[a-zA-Z0-9])\\.(([a-zA-Z]{2,4}|[a-zA-Z]{2,10}.[a-zA-Z0-9]{2,10}))(?![-0-9a-zA-Z])(?!\\.[a-zA-Z0-9])/gi");

					var import_data_domains = import_data.match(regex_all_domains);
					if (import_data_domains) {
						import_data_domains = unique_domains(strip(import_data_domains)).sort();
					} else {
						import_data_domains = null;
					}
					return import_data_domains;
				}

				var domains_to_search = null;
				var import_text_looping = false;
				var search_domain = null;
				function import_text_search() {
					var domain_arr = document.getElementById('found_domains').value.split("\n");
					if (domain_arr && domain_arr.length > 0 && !import_text_looping) {
						domains_to_search = domain_arr;
						domains_to_search.reverse();
						import_text_looping = true;
						import_text_loop({});
					}
				}

				function import_text_loop(data) {
					var plugins_url_icons = '<?php echo plugins_url('/images/icons/color/', __FILE__); ?>';
					if (!data.hasOwnProperty('error')) {
						if (data.hasOwnProperty('domain')) {
							var html_domain = data.domain.replace(/\./g, '-');
							var random = (Math.floor(Math.random() * 100)).toString();
							if (!jQuery('#force_ssl').prop('checked')) {
								var status_image = '<img src="'  + plugins_url_icons + 'circle-check.svg" class="svg svg-icon-table svg-fill-updated">';
								var status_text = 'Available!';
								switch (data.status) {
									case 0:
										status_text = 'Available!';
										status_image = '<img  id="' + html_domain + '-image-' + random + '" src="'  + plugins_url_icons + 'circle-check.svg" class="svg svg-icon-table svg-fill-update-updated" onload="paint_svg(\'' + html_domain + '-image-' + random + '\');">';
										break;
									case 1:
										status_text = 'Taken';
										status_image = '<img  id="' + html_domain + '-image-' + random + '" src="'  + plugins_url_icons + 'ban.svg" class="svg svg-icon-table svg-fill-error" onload="paint_svg(\'' + html_domain + '-image-' + random + '\');">';
										break;
									case 2:
										status_text = 'Owned';
										status_image = '<img  id="' + html_domain + '-image-' + random + '" src="'  + plugins_url_icons + 'flag.svg" class="svg svg-icon-table svg-fill-owned" onload="paint_svg(\'' + html_domain + '-image-' + random + '\');">';
										break;
								}
								var table_row = '<tr>' +
									'<td>' +
									'<a href="admin.php?page=domain-check-search&domain_check_search=' + data.domain + '">' +
									data.domain +
									'</td>' +
									'<td>' + status_image + status_text + '</td>' +
									'</tr>';
								jQuery('#import-text-results-table').append(table_row);
							} else {
								var status_image = '<img src="'  + plugins_url_icons + 'lock-unlocked.svg" class="svg svg-icon-table svg-fill-updated">';
								var status_text = 'Not Secure';
								switch (data.status) {
									case 0:
										status_text = 'Not Secure';
										status_image = '<img id="' + html_domain + '-ssl-image-' + random + '" src="'  + plugins_url_icons + 'lock-unlocked.svg" class="svg svg-icon-table svg-fill-error" onload="paint_svg(\'' + html_domain + '-ssl-image-' + random + '\');">';
										break;
									case 1:
										status_text = 'Secure';
										status_image = '<img id="' + html_domain + '-ssl-image-' + random + '" src="'  + plugins_url_icons + 'lock-locked.svg" class="svg svg-icon-table svg-fill-updated" onload="paint_svg(\'' + html_domain + '-ssl-image-' + random + '\');">';
										break;
								}
								var table_row = '<tr>' +
									'<td>' +
									'<a href="admin.php?page=domain-check-ssl-search&domain_check_ssl_search=' + data.domain + '">' +
									data.domain +
									'</td>' +
									'<td>' + status_image + status_text + '</td>' +
									'</tr>';
								jQuery('#import-text-results-table').append(table_row);

							}
						}
					} else {
						//error w/ last domain...
						if (!jQuery('#force_ssl').prop('checked')) {
							status_text = 'Taken';
							status_image = '<img src="'  + plugins_url_icons + 'ban.svg" class="svg svg-icon-table svg-fill-error">';
							var table_row = '<tr>' +
								'<td>' +
								'<a href="admin.php?page=domain-check-search&domain_check_search=' + data.error.domain + '">' +
								data.error.domain +
								'</td>' +
								'<td>' + status_image + status_text + '</td>' +
								'</tr>';
							jQuery('#import-text-results-table').append(table_row);
						} else {
							status_text = 'Not Secure';
							status_image = '<img src="'  + plugins_url_icons + 'lock-unlocked.svg" class="svg svg-icon-table svg-fill-error">';
							var table_row = '<tr>' +
								'<td>' +
								'<a href="admin.php?page=domain-check-ssl-search&domain_check_ssl_search=' + data.error.domain + '">' +
								data.error.domain +
								'</td>' +
								'<td>' + status_image + status_text + '</td>' +
								'</tr>';
							jQuery('#import-text-results-table').append(table_row);
						}

					}
					if (domains_to_search && domains_to_search.length > 0) {
						search_domain = domains_to_search.pop();
						var data_obj = {
							action:'domain_search',
							domain: search_domain
						}
						if (jQuery('#force_owned').prop('checked')) {
							data_obj.force_owned = 1;
						}
						if (jQuery('#force_ssl').prop('checked')) {
							data_obj.force_owned = 0;
							data_obj.force_ssl = 1;
						}
						if (jQuery('#force_watch').prop('checked')) {
							data_obj.force_watch = 1;
						}
						domain_check_ajax_call(data_obj, import_text_loop);
						return;
					}
					import_text_looping = false;
				}


				//puts the image on a canvas tag
				function import_text_file_handler(e) {
					var imgIdx = 0;
					var i = 0, len = this.files.length, img, reader, file;
					for ( ; i < len; i++ ) {
						file = this.files[i];
						//if (!!file.type.match(/image.*/)) {
							if ( window.FileReader ) {
								reader = new FileReader();
								reader.onload = function(event){
									var found_domains = import_text_get_domains(event.target.result);
									if (found_domains) {
										document.getElementById("found_domains").value = found_domains.join("\n");
									}
								}
								reader.readAsText(e.target.files[0]);
							}
						//}
					}
				}
				//setup the image uploader
				function import_text_file_init() {
					//for checking the file input for user uploads
					document.getElementById('domain_check_your_domains_import').addEventListener('change', import_text_file_handler, false);
				}

				function import_text_raw_init() {
					var found_data = import_text_get_domains(document.getElementById('import_text').value);
					if (found_data) {
						document.getElementById('found_domains').value = found_data.join("\n");
					}
				}

				function import_text_toggle_checkboxes(elem) {
					if (elem.id == 'force_owned') {
						if (jQuery('#force_owned').prop('checked')) {
							jQuery('#force_ssl').prop('checked', false);
						}
					}
					if (elem.id == 'force_ssl') {
						if (jQuery('#force_ssl').prop('checked')) {
							jQuery('#force_owned').prop('checked', false);
						}
					}
				}

			</script>
			<style type="text/css">
				.domain-check-import-left {
					max-width: 350px;
					min-width: 350px;
					min-height: 650px;
					display: inline-block;
					vertical-align: top;
					padding: 10px;
					margin: 10px;
					background-color: #ffffff;
				}
			</style>
			<div style="width: auto;">
				<div class="domain-check-import-left" style="min-height: 1px; max-width: 740px;">
					Use this tool to import your data and easily grab any domains from files, text lists, emails, or other documents. To get a list of your domains you should log in to your domain registrar and find the section that lists all of your domains. Highlight the entire page and copy and paste in to the first textbox. If you have multiple pages of domains you can also see if your domain registrar can export a CSV and you can use the file importer.
				</div>
				<div class="domain-check-import-left" style="min-height: 1px; max-width: 350px;">
					<table width="100%">
					<?php
					$coupons = DomainCheckCouponData::get_data();
					$coupon_site_counter = 0;

					$counter = 0;
					$coupons = array_keys($coupons);
					sort($coupons);
					foreach ($coupons as $coupon_site) {
						if (!($coupon_site_counter % 3)) {
							?><tr><?php
						}
						$homepage_link = DomainCheckLinks::homepage(site_url(), $coupon_site);
						?><td><strong><a href="<?php echo $homepage_link; ?>" target="_blank"><?php echo ucfirst($coupon_site); ?></a></strong></td><?php
						if (($coupon_site_counter % 3) == 2) {
							?></tr><?php
						}
						$counter++;
						$coupon_site_counter = $coupon_site_counter + 1;
					}
					?>
					</table>
				</div>
			</div>
			<h2>
				<img src="<?php echo plugins_url('/images/icons/color/data-transfer-upload.svg', __FILE__); ?>" class="svg svg-icon-h2 svg-fill-gray">
				Import
			</h2>
			<div class="domain-check-import-left">
				<h2>Step 1</h2>
				<div>
					<h3>Copy & Paste Any Text!</h3>
					<p class="p">
						Copy and paste any text in to here to find any domain names! Extract domain names from any text, HTML, email, and anything you can copy & paste!
					</p>
					<textarea id="import_text" style="width: 100%; height: 200px;" onclick="if (this.value == 'Copy and paste any text here and get the domains!') { this.value=''; }" onkeyup="import_text_raw_init();">Copy and paste any text here and get the domains!</textarea>
					<br>
					<input type="button" class="button-primary" value="Find Domains" onclick="import_text_raw_init();"/>
				</div>
				<div style="min-height: 30px; text-align: center;"></div>
				<h3>File Import</h3>
				<p class="p">
					Upload any CSV or XML file! If you have an XLS file please save your file as a CSV (comma delimitted) file and upload!
				</p>
				<div>
					<form action="" method="POST" enctype="multipart/form-data">
						<input type="file" name="domain_check_your_domains_import" id="domain_check_your_domains_import">
					</form>
				</div>
			</div><?php
			//spacer
			?><div class="domain-check-import-left">
				<h2>Step 2</h2>
				<h3>Domains to Import</h3>
				<div>
				<input type="checkbox" id="force_owned" onclick="import_text_toggle_checkboxes(this);" checked>&nbsp;-&nbsp;Import as Owned<br>
				<input type="checkbox" id="force_ssl" onchange="import_text_toggle_checkboxes(this);">&nbsp;-&nbsp;Do SSL Check<br>
				<input type="checkbox" id="force_watch" checked>&nbsp;-&nbsp;Watch All Domains<br>
				</div>
				<textarea id="found_domains" style="width: 100%; height: 350px;"></textarea>
				<br>
				<a href="#step3" class="button-primary" value="Search" onclick="import_text_search();">
					Start the Import!
				</a>
			</div><?php
			//spacer
			?><div class="domain-check-import-left">
				<a name="step3" />
				<h2>Step 3</h2>
				<h3>Domain Import Results</h3>
				<div id="import-text-results-wrapper" name="import-text-results-wrapper">
					<table id="import-text-results-table" name="import-text-results-table" style="width: 100%;"></table>
				</div>
			</div>
		</div>
		<?php
		DomainCheckAdminHeader::admin_header_nav();
		DomainCheckAdminHeader::footer();
		?>
		<script type="text/javascript">
			import_text_file_init();
		</script>
		<?php
	}

}