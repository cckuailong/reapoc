<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<style>
	div.panel {padding: 20px 5px 10px 10px;}
	div.area {font-size:16px; text-align: center; line-height: 30px; width:500px; margin:auto}
	ul.li {padding:2px}
</style>

<div class="panel">
	<br/>
	<div class="area" style="width:400px">
		<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/logo-dpro-300x50.png"); ?>"  />
		<h2>
			<?php esc_html_e('Store your packages in multiple locations  with Duplicator Pro', 'duplicator') ?>
		</h2>
		<div style='text-align: left; margin:auto; width:200px'>
			<ul>
				<li><i class="fab fa-amazon"></i> <?php esc_html_e('Amazon S3', 'duplicator'); ?></li>
				<li><i class="fab fa-dropbox"></i> <?php esc_html_e(' Dropbox', 'duplicator'); ?></li>
				<li><i class="fab fa-google-drive"></i> <?php esc_html_e('Google Drive', 'duplicator'); ?></li>
				<li><i class="fa fa-cloud fa-sm"></i> <?php esc_html_e('One Drive', 'duplicator'); ?></li>
				<li><i class="fa fa-upload"></i> <?php esc_html_e('FTP &amp; SFTP', 'duplicator'); ?></li>
				<li><i class="far fa-folder-open"></i> <?php esc_html_e('Custom Directory', 'duplicator'); ?></li>
			</ul>
		</div>
		<?php
			_e('Set up a one-time storage location and automatically <br/> push the package to your destination.', 'duplicator');
		?>
	</div><br/>

	<p style="text-align:center">
		<a href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_settings_storage&utm_campaign=duplicator_pro" target="_blank" class="button button-primary button-large dup-check-it-btn" >
			<?php esc_html_e('Learn More', 'duplicator') ?>
		</a>
	</p>
</div>
