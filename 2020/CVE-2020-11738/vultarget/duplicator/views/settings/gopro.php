<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
DUP_Util::hasCapability('export');

require_once(DUPLICATOR_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PLUGIN_PATH . '/views/inc.header.php');
?>
<style>
    /*================================================
    PAGE-SUPPORT:*/
	div.dup-pro-area {
		padding:10px 70px; max-width:750px; width:90%; margin:auto; text-align:center;
		background:#fff; border-radius:20px;
		box-shadow:inset 0px 0px 67px 20px rgba(241,241,241,1);
	}
	i.dup-gopro-help {color:#777 !important; margin-left:5px; font-size:14px; }
	td.group-header {background-color:#D5D5D5; color: #000; font-size: 20px; padding:7px !important; font-weight: bold}
    div.dup-compare-area {width:400px;  float:left; border:1px solid #dfdfdf; border-radius:4px; margin:10px; line-height:18px;box-shadow:0 8px 6px -6px #ccc;}
	div.feature {background:#fff; padding:15px; margin:2px; text-align:center; min-height:20px}
	div.feature a {font-size:18px; font-weight:bold;}
	div.dup-compare-area div.feature div.info {display:none; padding:7px 7px 5px 7px; font-style:italic; color:#555; font-size:14px}
	div.dup-gopro-header {text-align:center; margin:5px 0 15px 0; font-size:18px; line-height:30px}
	div.dup-gopro-header b {font-size:35px}
	button.dup-check-it-btn {box-shadow:5px 5px 5px 0px #999 !important; font-size:20px !important; height:45px !important;   padding:7px 30px 7px 30px !important;   color:white!important;  background-color: #3e8f3e!important; font-weight: bold!important;
    color: white;
    font-weight: bold;}

	#comparison-table { margin-top:25px; border-spacing:0px;  width:100%}
	#comparison-table th { color:#E21906;}
	#comparison-table td, #comparison-table th { font-size:1.2rem; padding:11px; }
	#comparison-table .feature-column { text-align:left; width:46%}
	#comparison-table .check-column { text-align:center; width:27% }
	#comparison-table tr:nth-child(2n+2) { background-color:#f6f6f6; }
	.button.button-large.dup-check-it-btn { line-height: 28px; }
</style>

<div class="dup-pro-area">
	<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/logo-dpro-300x50.png"); ?>"  />
	<div style="font-size:18px; font-style:italic; color:gray; border-bottom: 1px solid silver; padding-bottom:10px; margin-bottom: -30px">
		<?php esc_html_e('The simplicity of Duplicator', 'duplicator') ?>
		<?php esc_html_e('with power for everyone.', 'duplicator') ?>
	</div>

	<table id="comparison-table">
		<tr>
			<th class="feature-column"><?php esc_html_e('Feature', 'duplicator') ?></th>
			<th class="check-column"><?php esc_html_e('Free', 'duplicator') ?></th>
			<th class="check-column"><?php esc_html_e('Professional', 'duplicator') ?></th>
		</tr>
		<tr>
			<td class="feature-column"><?php esc_html_e('Backup Files & Database', 'duplicator') ?></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column"><?php esc_html_e('File Filters', 'duplicator') ?></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column"><?php esc_html_e('Database Table Filters', 'duplicator') ?></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column"><?php esc_html_e('Migration Wizard', 'duplicator') ?></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
        <tr>
			<td class="feature-column"><?php esc_html_e('Scheduled Backups', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column">
				<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/amazon-64.png") ?>" style='height:16px; width:16px'  />
				<?php esc_html_e('Amazon S3 Storage', 'duplicator') ?>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column">
				<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/dropbox-64.png"); ?>" style='height:16px; width:16px'  />
				<?php esc_html_e('Dropbox Storage ', 'duplicator') ?>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column">
				<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/google_drive_64px.png"); ?>" style='height:16px; width:16px'  />
				<?php esc_html_e('Google Drive Storage', 'duplicator') ?>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column">
				<img src="<?php echo DUPLICATOR_PLUGIN_URL ?>assets/img/onedrive-48px.png" style='height:16px; width:16px'  />
				<?php esc_html_e('Microsoft One Drive Storage', 'duplicator') ?>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
        		<tr>
			<td class="feature-column">
				<img src="<?php echo DUPLICATOR_PLUGIN_URL ?>assets/img/ftp-64.png" style='height:16px; width:16px'  />
				<?php esc_html_e('Remote FTP/SFTP Storage', 'duplicator') ?>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
        <tr>
            <td class="feature-column"><?php _e('Overwrite Live Site', 'duplicator') ?><sup>
					<i class="fa fa-question-circle dup-gopro-help"
						data-tooltip-title="<?php _e("Overwrite Existing Site", 'duplicator'); ?>"
                        data-tooltip="<?php _e('Overwrite a live site. Makes installing super-fast!', 'duplicator'); ?>"/></i></sup>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
            <td class="feature-column"><?php esc_html_e('Large Site Support', 'duplicator') ?><sup>
					<i class="fa fa-question-circle dup-gopro-help"
						data-tooltip-title="<?php esc_attr_e("Large Site Support", 'duplicator'); ?>"
                        data-tooltip="<?php esc_attr_e('Advanced archive engine processes multi-gig sites - even on stubborn budget hosts!', 'duplicator'); ?>"/></i></sup>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column"><?php esc_html_e('Multiple Archive Engines', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column"><?php esc_html_e('Server Throttling', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
        <tr>
			<td class="feature-column"><?php esc_html_e('Background Processing', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
        <tr>
            <td class="feature-column"><?php esc_html_e('Installer Passwords', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
        <tr>
			<td class="feature-column"><?php esc_html_e(' Regenerate Salts', 'duplicator') ?><sup>
					<i  class="fa fa-question-circle dup-gopro-help"
						data-tooltip-title="<?php esc_attr_e("Regenerate Salts", 'duplicator'); ?>"
                        data-tooltip="<?php esc_attr_e('Installer contains option to regenerate salts in the wp-config.php file.  This feature is only available with Freelancer, Business or Gold licenses.', 'duplicator'); ?>"/></i></sup>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
                <tr>
			<td class="feature-column"><?php esc_html_e('WP-Config Control Plus', 'duplicator') ?><sup>
					<i  class="fa fa-question-circle dup-gopro-help"
						data-tooltip-title="<?php esc_attr_e("WP-Config Control Plus", 'duplicator'); ?>"
                        data-tooltip="<?php esc_attr_e('Control many wp-config.php settings right from the installer!', 'duplicator'); ?>"/></i></sup>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column">
				<img src="<?php echo DUPLICATOR_PLUGIN_URL ?>assets/img/cpanel-48.png" style="width:16px; height:12px" />
				<?php esc_html_e('cPanel Database API', 'duplicator') ?>
				<sup>
					<i  class="fa fa-question-circle dup-gopro-help"
						data-tooltip-title="<?php esc_attr_e("cPanel", 'duplicator'); ?>"
                        data-tooltip="<?php esc_attr_e('Create the database and database user directly in the installer.  No need to browse to your host\'s cPanel application.', 'duplicator'); ?>"/></i></sup>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column"><?php esc_html_e('Multisite Network Migration', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
        <tr>
			<td class="feature-column"><?php esc_html_e('Multisite Subsite &gt; Standalone', 'duplicator') ?><sup>
					<i  class="fa fa-question-circle dup-gopro-help"
						data-tooltip-title="<?php esc_attr_e("Multisite", 'duplicator'); ?>"
                        data-tooltip="<?php esc_attr_e('Install an individual subsite from a Multisite as a standalone site.  This feature is only available with Business or Gold licenses.', 'duplicator'); ?>"/></i></sup>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>

   		<tr>
			<td class="feature-column"><?php esc_html_e('Custom Search & Replace', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>

		<tr>
			<td class="feature-column"><?php esc_html_e('Email Alerts', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>

		<tr>
			<td class="feature-column"><?php esc_html_e('Manual Transfers', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column">
				<?php esc_html_e('Active Customer Support', 'duplicator') ?>
				<sup><i  class="fa fa-question-circle dup-gopro-help"
						data-tooltip-title="<?php esc_attr_e("Support", 'duplicator'); ?>"
                        data-tooltip="<?php esc_attr_e('Pro users get top priority for any requests to our support desk.  In most cases responses will be answered in under 24 hours.', 'duplicator'); ?>"/></i></sup>
			</td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
		<tr>
			<td class="feature-column"><?php esc_html_e('Plus Many Other Features...', 'duplicator') ?></td>
			<td class="check-column"></td>
			<td class="check-column"><i class="fa fa-check"></i></td>
		</tr>
	</table>

	<br style="clear:both" />
	<p style="text-align:center">
		<button onclick="window.open('https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_go_pro&utm_campaign=duplicator_pro');" class="button button-large dup-check-it-btn" >
			<?php esc_html_e('Check It Out!', 'duplicator') ?>
		</button>
	</p>
	<br/><br/>
</div>
<br/><br/>
