<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<style>
	div.panel {padding: 20px 5px 10px 10px;}
	div.area {font-size:16px; text-align: center; line-height: 30px}
</style>

<div class="panel">
	<br/>
	<div class="area">
		<img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL."assets/img/logo-dpro-300x50.png"); ?>"  />
		<?php
			echo '<h2><i class="far fa-clock fa-sm"></i> ' .  esc_html__('This option is available in Duplicator Pro.', 'duplicator')  . '</h2>';
			esc_html_e('Create robust schedules that automatically create packages while you sleep.', 'duplicator');
			echo '<br/>';
			esc_html_e('Simply choose your storage location and when you want it to run.', 'duplicator');
		?>
	</div>
	<p style="text-align:center">
		<a href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_content=free_settings_schedule&utm_campaign=duplicator_pro" target="_blank" class="button button-primary button-large dup-check-it-btn" >
			<?php esc_html_e('Learn More', 'duplicator') ?>
		</a>
	</p>
</div>


