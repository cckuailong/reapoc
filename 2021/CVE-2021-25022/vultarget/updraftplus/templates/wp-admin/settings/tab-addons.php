<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

global $updraftplus_checkout_embed;
$tick = UPDRAFTPLUS_URL.'/images/updraft_tick.png';
$cross = UPDRAFTPLUS_URL.'/images/updraft_cross.png';
$freev = UPDRAFTPLUS_URL.'/images/updraft_freev.png';
$premv = UPDRAFTPLUS_URL.'/images/updraft_premv.png';

$checkout_embed_premium_attribute = '';

if ($updraftplus_checkout_embed) {
	$checkout_embed_premium_attribute = $updraftplus_checkout_embed->get_product('updraftpremium') ? 'data-embed-checkout="'.apply_filters('updraftplus_com_link', $updraftplus_checkout_embed->get_product('updraftpremium', UpdraftPlus_Options::admin_page_url().'?page=updraftplus&tab=addons')).'"' : '';
}

?>
<div class="updraft_premium">
	<section>
		<div class="updraft_premium_cta udpdraft__lifted">
			<div class="updraft_premium_cta__top">
				<div class="updraft_premium_cta__summary">
					<h2 id="premium-upgrade-header">UpdraftPlus <strong>Premium</strong></h2>
					<ul class="updraft_premium_description_list">
						<li><a target="_blank" href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/updraftplus-full-feature-list/");?>"><?php _e('Full feature list', 'updraftplus');?></a></li>
						<li><a target="_blank" href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/faq-category/general-and-pre-sales-questions/");?>"><?php _e('Pre-sales FAQs', 'updraftplus');?></a></li>
						<li><a target="_blank" href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/ask-a-pre-sales-question/");?>"><?php _e('Ask a pre-sales question', 'updraftplus');?></a></li>
						<li><a target="_blank" href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/support/");?>"><?php _e('Support', 'updraftplus');?></a></li>
						<li><a href="#other-plugins"><?php _e('Other great plugins', 'updraftplus');?></a></li>
						<li><a target="_blank" href="https://www.simbahosting.co.uk/s3/shop/"><?php _e('WooCommerce plugins', 'updraftplus');?></a></li>
					</ul>
				</div>
				<div class="updraft_premium_cta__action">
					<?php
					$user_bought_udp = isset($_REQUEST['updraftplus_product']) && 'updraftpremium' === $_REQUEST['updraftplus_product'] && isset($_REQUEST['status']) && 'complete' === $_REQUEST['status'];
					if (!$user_bought_udp) {
					?>
						<a aria-label="<?php echo sprintf(__('Get %s here', 'updraftplus'), 'UpdraftPlus Premium').'. '.__('Goes to the updraftplus.com checkout page', 'updraftplus'); ?>" target="_blank" class="button button-primary button-hero" href="<?php echo apply_filters('updraftplus_com_link', $updraftplus->get_url('shop_premium'));?>" <?php echo $checkout_embed_premium_attribute; ?>><?php _e('Get it here', 'updraftplus');?></a>
						<small><span class="dashicons dashicons-external dashicons-adapt-size"></span> <?php _e('Goes to updraftplus.com checkout page', 'updraftplus'); ?></small>
					<?php
					}
					?>
				</div>
			</div>
			<?php if (!$user_bought_udp) : ?>
				<div class="updraft_premium_cta__bottom">
					<p class="premium-upgrade-prompt">
						<?php _e('You are currently using the free version of UpdraftPlus.', 'updraftplus');?> <a target="_blank" href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/support/installing-updraftplus-premium-your-add-on/");?>"> <?php echo __('If you have purchased from UpdraftPlus.Com, then follow this link to the installation instructions (particularly step 1).', 'updraftplus');?></a>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="premium-upgrade-purchase-success" <?php if (!$user_bought_udp) echo 'style="display: none;"';?>>
		<h3><span class="dashicons dashicons-yes"></span><?php _e('You successfully purchased UpdraftPremium.', 'updraftplus');?></h3>
		<p><a target="_blank" href="<?php echo apply_filters('updraftplus_com_link', "https://updraftplus.com/support/installing-updraftplus-premium-your-add-on/");?>"> <?php echo __('Follow this link to the installation instructions (particularly step 1).', 'updraftplus');?></a></p>
	</section>

	<?php if (!$user_bought_udp) : ?>
		<section>
			<h2 class="updraft_feat_table__title">Features comparison</h2>
			<table class="updraft_feat_table udpdraft__lifted">
				<tbody>
				<tr class="updraft_feat_table__header">
					<td></td>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/ud-logo.png';?>" alt="UpdraftPlus" width="80" height="80">
						<?php _e('Free', 'updraftplus');?>
					</td>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/ud-logo.png';?>" alt="<?php esc_attr_e('UpdraftPlus Premium', 'updraftplus');?>" width="80" height="80">
						<?php _e('Premium', 'updraftplus');?>
					</th>
				</tr>
				<tr>
					<td></td>
					<td>
						<span class="installed updraft-yes"><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span> <?php _e('Installed', 'updraftplus');?></span>
					</td>
					<td>
						<a class="button button-primary" href="<?php esc_attr_e(apply_filters('updraftplus_com_link', $updraftplus->get_url('shop_premium')));?>" <?php echo $checkout_embed_premium_attribute; ?>><?php _e('Upgrade now', 'updraftplus');?></a>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/morestorage.png';?>" alt="<?php esc_attr_e('Remote storage', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Backup to remote storage locations', 'updraftplus');?></h4>
						<p><?php _e('To avoid server-wide risks, always backup to remote cloud storage. UpdraftPlus free includes Dropbox, Google Drive, Amazon S3, Rackspace and more.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/migrator.png';?>" alt="<?php esc_attr_e('Migrator', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Cloning and migration', 'updraftplus');?></h4>
						<p><?php _e('UpdraftPlus Migrator clones your WordPress site and moves it to a new domain directly and simply.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/anonymisation.png';?>" alt="<?php esc_attr_e('Anonymisation functions', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Anonymisation functions', 'updraftplus');?></h4>
						<p><?php _e('Anonymise personal data in your database backups.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/incremental.png';?>" alt="<?php esc_attr_e('Incremental backups', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Incremental backups', 'updraftplus');?></h4>
						<p><?php _e('Allows you to only backup changes to your files (such as a new image) that have been made to your site since the last backup.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/notices/support.png';?>" alt="<?php esc_attr_e('Support', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Fast, personal support', 'updraftplus');?></h4>
						<p><?php _e('Provides expert help and support from the developers whenever you need it.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/automaticbackup.png';?>" alt="<?php esc_attr_e('Pre-update backups', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Pre-update backups', 'updraftplus');?></h4>
						<p><?php _e('Automatically backs up your website before any updates to plugins, themes and WordPress core.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/morefiles.png';?>" alt="<?php esc_attr_e('Backup non-WordPress files and databases', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Backup non-WordPress files and databases', 'updraftplus');?></h4>
						<p><?php _e('Backup WordPress core and non-WP files and databases.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/multisite.png';?>" alt="<?php esc_attr_e('Network and multisite', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Network / multisite', 'updraftplus');?></h4>
						<p><?php _e('Backup WordPress multisites (i.e, networks), securely.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/fixtime.png';?>" alt="<?php esc_attr_e('Backup time and scheduling', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Backup time and scheduling', 'updraftplus');?></h4>
						<p><?php _e('Set exact times to create or delete backups.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/wp-cli.png';?>" alt="<?php esc_attr_e('WP CLI', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('WP-CLI support', 'updraftplus');?></h4>
						<p><?php _e('WP-CLI commands to take, list and delete backups.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/moredatabase.png';?>" alt="<?php esc_attr_e('More database options', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('More database options', 'updraftplus');?></h4>
						<p><?php _e('Encrypt your sensitive databases (e.g. customer information or passwords); Backup external databases too.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/morestorage.png';?>" alt="<?php esc_attr_e('Additional storage', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Additional and enhanced remote storage locations', 'updraftplus');?></h4>
						<p><?php _e('Get enhanced versions of the free remote storage options (Dropbox, Google Drive & S3) and even more remote storage options like OneDrive, SFTP, Azure, WebDAV and more with UpdraftPlus Premium.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/reporting.png';?>" alt="<?php esc_attr_e('Reporting', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Reporting', 'updraftplus');?></h4>
						<p><?php _e('Sophisticated reporting and emailing capabilities.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/noadverts.png';?>" alt="<?php esc_attr_e('No ads', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('No ads', 'updraftplus');?></h4>
						<p><?php _e('Tidy things up for clients and remove all adverts for our other products.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/importer.png';?>" alt="<?php esc_attr_e('Importer', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Importer', 'updraftplus');?></h4>
						<p><?php _e("Some backup plugins can't restore a backup, so Premium allows you to restore backups from other plugins.", 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/addons-images/lockadmin.png';?>" alt="<?php esc_attr_e('Lock settings', 'updraftplus');?>" width="80" height="80" class="udp-premium-image">
						<h4><?php _e('Lock settings', 'updraftplus');?></h4>
						<p><?php _e('Lock access to UpdraftPlus via a password so you choose which admin users can access backups.', 'updraftplus');?></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></span></p>
					</td>
					<td>
						<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span></p>
					</td>
				</tr>
				<tr>
					<td>
						<img src="<?php echo UPDRAFTPLUS_URL.'/images/updraft_vault_logo.png';?>" alt="<?php esc_attr_e('UpdraftVault', 'updraftplus');?>" width="100" height="100" class="udp-premium-image">
						<h4><?php _e('UpdraftVault storage', 'updraftplus');?></h4>
						<p>
							<?php _e('UpdraftPlus has its own embedded storage option, providing a zero-hassle way to download, store and manage all your backups from one place.', 'updraftplus');?>
							<a href="<?php esc_attr_e(apply_filters('updraftplus_com_link', 'https://updraftplus.com/landing/updraftvault'));?>"><?php _e('Premium / Find out more', 'updraftplus');?></a>
						</p>
						
					</td>
					<td>
						<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'updraftplus');?>"></span></span></p>
					</td>
					<td>
						<p><span class="updraft-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>">1 GB</span></p>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<span class="installed updraft-yes"><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'updraftplus');?>"></span> <?php _e('Installed', 'updraftplus');?></span>
					</td>
					<td>
						<p><a class="button button-primary" href="<?php esc_attr_e(apply_filters('updraftplus_com_link', $updraftplus->get_url('shop_premium')));?>" <?php echo $checkout_embed_premium_attribute; ?>><?php _e('Upgrade now', 'updraftplus');?></a></p>
					</td>
				</tr>
				</tbody>
			</table> 
		</section>
	<?php endif; ?>
	<section id="other-plugins">
		<h2><?php _e('More great plugins by the Updraft Team', 'updraftplus'); ?></h2>
		<div class="updraft-more-plugins">
			<div class="udp-box">
				<h3><img src="<?php echo UPDRAFTPLUS_URL; ?>/images/other-plugins/updraft-central.png" alt="UpdraftCentral"></h3>
				<p><?php _e('Manage multiple WordPress sites from one central dashboard', 'updraftplus'); ?></p>
				<a aria-label="UpdraftCentral. <?php echo __('Manage multiple WordPress sites from one central dashboard', 'updraftplus').'. '.__('Find out more', 'updraftplus'); ?>" target="_blank" href="https://updraftcentral.com/?utm_source=updraftplus&utm_medium=cross-sell&utm_campaign=addons-tab"><?php _e('Find out more', 'updraftplus'); ?></a>
			</div>
			<div class="udp-box">
				<h3><img src="<?php echo UPDRAFTPLUS_URL; ?>/images/other-plugins/wp-optimize.png" alt="WP Optimize"></h3>
				<p><?php _e('Keep your database fast and efficient', 'updraftplus'); ?></p>
				<a aria-label="WP Optimize. <?php echo __('Keep your database fast and efficient', 'updraftplus').'. '.__('Find out more', 'updraftplus'); ?>" target="_blank" href="https://getwpo.com/?utm_source=updraftplus&utm_medium=cross-sell&utm_campaign=addons-tab"><?php _e('Find out more', 'updraftplus'); ?></a>
			</div>
		</div>
	</section>	
</div>
