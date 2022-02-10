<?php defined('ABSPATH') or die("you do not have access to this page!"); ?>

<?php

$plugins = array(
	'ZIP' => array(
			'constant_free' => 'ZRDN_PLUGIN_BASENAME',
			'constant_premium' => 'ZRDN_PREMIUM',
			'website' => 'https://ziprecipes.net/premium/',
			'search' => 'zip+recipes+recipe+maker+really+simple+plugins+complianz',
			'url' => 'https://wordpress.org/plugins/zip-recipes/?src=complianz-plugin',
			'title' => 'Zip Recipes - '. __("Beautiful recipes optimized for Google", "complianz-gdpr"),
	),
	'RSSSL' => array(
			'constant_free' => 'rsssl_version',
			'constant_premium' => 'rsssl_pro_version',
			'website' => 'https://really-simple-ssl.com/premium/?src=complianz-plugin',
			'search' => 'really-simple-ssl%20rogier%20lankhorst&tab=search',
			'url' => 'https://wordpress.org/plugins/really-simple-ssl/',
			'title' => 'Really Simple SSL - '. __("Easily migrate your website to SSL", "complianz-gdpr"),
	),
	'COMPLIANZTC' => array(
			'constant_free' => 'cmplz_tc_version',
			'constant_premium' => 'cmplz_tc_version',
			'url' => 'https://wordpress.org/plugins/complianz-terms-conditions/',
			'website' => 'https://complianz.io?src=complianz-plugin',
			'search' => 'complianz+terms+conditions+stand-alone',
			'title' => 'Complianz - '. __("Terms and Conditions", "complianz-gdpr"),

	),
);
?>
<div>
	<?php foreach ($plugins as $id => $plugin) {
		$prefix = strtolower($id);
		?>
		<div class="cmplz-other-plugin cmplz-<?php echo $prefix?>">
			<div class="plugin-color">
				<div class="cmplz-bullet"></div>
			</div>
			<div class="plugin-text">
				<a href="<?php echo $plugin['url']?>" target="_blank"><?php echo $plugin['title']?></a>
			</div>
			<div class="plugin-status">
				<?php echo COMPLIANZ::$admin->get_status_link($plugin)?>
			</div>
		</div>
	<?php }?>
</div>
