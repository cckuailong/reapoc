<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$memory_limit   = function_exists( 'wc_let_to_num' )?wc_let_to_num( WP_MEMORY_LIMIT ):woocommerce_let_to_num( WP_MEMORY_LIMIT );
$php_mem_limit  = function_exists( 'memory_get_usage' ) ? @ini_get( 'memory_limit' ) : '-';

$server_configs = apply_filters( 'wpo_wcpdf_server_configs' , array(
	'PHP version' => array(
		'required' => __( '7.1+ (7.4 or higher recommended)', 'woocommerce-pdf-invoices-packing-slips' ),
		'value'    => PHP_VERSION,
		'result'   => version_compare( PHP_VERSION, '7.1', '>' ),
	),
	'DOMDocument extension' => array(
		'required' => true,
		'value'    => phpversion('DOM'),
		'result'   => class_exists('DOMDocument'),
	),
	'MBString extension' => array(
		'required' => true,
		'value'    => phpversion('mbstring'),
		'result'   => function_exists('mb_send_mail'),
		'fallback' => __( 'Recommended, will use fallback functions', 'woocommerce-pdf-invoices-packing-slips' ),
	),
	'GD' => array(
		'required' => true,
		'value'    => phpversion('gd'),
		'result'   => function_exists('imagecreate'),
		'fallback' => __( 'Required if you have images in your documents', 'woocommerce-pdf-invoices-packing-slips' ),
	),
	// "PCRE" => array(
	// 	"required" => true,
	// 	"value"    => phpversion("pcre"),
	// 	"result"   => function_exists("preg_match") && @preg_match("/./u", "a"),
	// 	"failure"  => "PCRE is required with Unicode support (the \"u\" modifier)",
	// ),
	'Zlib' => array(
		'required' => __( 'To compress PDF documents', 'woocommerce-pdf-invoices-packing-slips' ),
		'value'    => phpversion('zlib'),
		'result'   => function_exists('gzcompress'),
		'fallback' => __( 'Recommended to compress PDF documents', 'woocommerce-pdf-invoices-packing-slips' ),
	),
	'opcache' => array(
		'required' => __( 'For better performances', 'woocommerce-pdf-invoices-packing-slips' ),
		'value'    => null,
		'result'   => false,
		'fallback' => __( 'Recommended for better performances', 'woocommerce-pdf-invoices-packing-slips' ),
	),
	'GMagick or IMagick' => array(
		'required' => __( 'Better with transparent PNG images', 'woocommerce-pdf-invoices-packing-slips' ),
		'value'    => null,
		'result'   => extension_loaded('gmagick') || extension_loaded('imagick'),
		'fallback' => __( 'Recommended for better performances', 'woocommerce-pdf-invoices-packing-slips' ),
	),
	'glob()' => array(
		'required' => __( 'Required to detect custom templates and to clear the temp folder periodically', 'woocommerce-pdf-invoices-packing-slips' ),
		'value'    => null,
		'result'   => function_exists('glob'),
		'fallback' => __( 'Check PHP disable_functions', 'woocommerce-pdf-invoices-packing-slips' ),
	),
	'WP Memory Limit' => array(
		/* translators: <a> tags */
		'required' => sprintf( __( 'Recommended: 128MB (more for plugin-heavy setups<br/>See: %1$sIncreasing the WordPress Memory Limit%2$s', 'woocommerce-pdf-invoices-packing-slips' ), '<a href="https://docs.woocommerce.com/document/increasing-the-wordpress-memory-limit/" target="_blank">', '</a>' ),
		'value'    => sprintf('WordPress: %s, PHP: %s', WP_MEMORY_LIMIT, $php_mem_limit ),
		'result'   => $memory_limit > 67108864,
	),
	'allow_url_fopen'	=> array (
		'required' => __( 'Allow remote stylesheets and images', 'woocommerce-pdf-invoices-packing-slips' ),
		'value'	   => null,
		'result'   => ini_get('allow_url_fopen'),			
		'fallback' => __( 'allow_url_fopen disabled', 'woocommerce-pdf-invoices-packing-slips' ),
	),
	'base64_decode'	=> array (
		'required' => __( 'To compress and decompress font data', 'woocommerce-pdf-invoices-packing-slips' ),
		'value'	   => null,	
		'result'   => function_exists('base64_decode'),			
		'fallback' => __( 'base64_decode disabled', 'woocommerce-pdf-invoices-packing-slips' ),
	),
) );

if ( ( $xc = extension_loaded('xcache') ) || ( $apc = extension_loaded('apc') ) || ( $zop = extension_loaded('Zend OPcache') ) || ( $op = extension_loaded('opcache') ) ) {
	$server_configs['opcache']['result'] = true;
	$server_configs['opcache']['value'] = (
		$xc ? 'XCache '.phpversion('xcache') : (
			$apc ? 'APC '.phpversion('apc') : (
				$zop ? 'Zend OPCache '.phpversion('Zend OPcache') : 'PHP OPCache '.phpversion('opcache')
			)
		)
	);
}
if ( ( $gm = extension_loaded('gmagick') ) || ( $im = extension_loaded('imagick') ) ) {
	$server_configs['GMagick or IMagick']['value'] = ($im ? 'IMagick '.phpversion('imagick') : 'GMagick '.phpversion('gmagick'));
}

if( ! $server_configs['PHP version']['result'] ) {
	/* translators: <a> tags */
	$server_configs['PHP version']['required'] .= sprintf( __( '<br/>Download %1$sthis addon%2$s to enable backwards compatibility.', 'woocommerce-pdf-invoices-packing-slips' ), '<a href="https://docs.wpovernight.com/woocommerce-pdf-invoices-packing-slips/backwards-compatibility-with-php-5-6/" target="_blank">', '</a>' );
}

?>

<h3 id="system"><?php _e( 'System Configuration', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>

<table cellspacing="1px" cellpadding="4px" style="background-color: white; padding: 5px; border: 1px solid #ccc;">
	<tr>
		<th align="left">&nbsp;</th>
		<th align="left"><?php _e( 'Required', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
		<th align="left"><?php _e( 'Present', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
	</tr>

	<?php foreach( $server_configs as $label => $server_config ) {
		if ( $server_config['result'] ) {
			$background = '#9e4';
			$color      = 'black';
		} elseif ( isset($server_config['fallback']) ) {
			$background = '#FCC612';
			$color      = 'black';
		} else {
			$background = '#f43';
			$color      = 'white';
		}
		?>
		<tr>
			<td class="title"><?php echo $label; ?></td>
			<td><?php echo ($server_config['required'] === true ? 'Yes' : $server_config['required']); ?></td>
			<td style="background-color:<?php echo $background; ?>; color:<?php echo $color; ?>">
				<?php
				echo $server_config['value'];
				if ($server_config['result'] && !$server_config['value']) echo 'Yes';
				if (!$server_config['result']) {
					if (isset($server_config['fallback'])) {
						echo '<div>No. '.$server_config['fallback'].'</div>';
					}
					if (isset($server_config['failure'])) {
						echo '<div>'.$server_config['failure'].'</div>';
					}
				}
				?>
			</td>
		</tr>
	<?php } ?>

</table>

<?php
	$status = array(
		'ok'     => __( 'Writable', 'woocommerce-pdf-invoices-packing-slips' ),
		'failed' => __( 'Not writable', 'woocommerce-pdf-invoices-packing-slips' ),
	);

	$permissions = array(
		'WCPDF_TEMP_DIR'		=> array (
			'description'		=> __( 'Central temporary plugin folder', 'woocommerce-pdf-invoices-packing-slips' ),
			'value'				=> WPO_WCPDF()->main->get_tmp_path(),
			'status'			=> is_writable( WPO_WCPDF()->main->get_tmp_path() ) ? 'ok' : 'failed',			
			'status_message'	=> is_writable( WPO_WCPDF()->main->get_tmp_path() ) ? $status['ok'] : $status['failed'],
		),
		'WCPDF_ATTACHMENT_DIR'		=> array (
			'description'		=> __( 'Temporary attachments folder', 'woocommerce-pdf-invoices-packing-slips' ),
			'value'				=> trailingslashit( WPO_WCPDF()->main->get_tmp_path( 'attachments' ) ),
			'status'			=> is_writable( WPO_WCPDF()->main->get_tmp_path( 'attachments' ) ) ? 'ok' : 'failed',			
			'status_message'	=> is_writable( WPO_WCPDF()->main->get_tmp_path( 'attachments' ) ) ? $status['ok'] : $status['failed'],
		),
		'DOMPDF_TEMP_DIR'		=> array (
			'description'		=> __( 'Temporary DOMPDF folder', 'woocommerce-pdf-invoices-packing-slips' ),
			'value'				=> trailingslashit(WPO_WCPDF()->main->get_tmp_path( 'dompdf' )),
			'status'			=> is_writable(WPO_WCPDF()->main->get_tmp_path( 'dompdf' )) ? 'ok' : 'failed',			
			'status_message'	=> is_writable(WPO_WCPDF()->main->get_tmp_path( 'dompdf' )) ? $status['ok'] : $status['failed'],
		),
		'DOMPDF_FONT_DIR'		=> array (
			'description'		=> __( 'DOMPDF fonts folder (needs to be writable for custom/remote fonts)', 'woocommerce-pdf-invoices-packing-slips' ),
			'value'				=> trailingslashit(WPO_WCPDF()->main->get_tmp_path( 'fonts' )),
			'status'			=> is_writable(WPO_WCPDF()->main->get_tmp_path( 'fonts' )) ? 'ok' : 'failed',			
			'status_message'	=> is_writable(WPO_WCPDF()->main->get_tmp_path( 'fonts' )) ? $status['ok'] : $status['failed'],
		),
	);

	$upload_dir  = wp_upload_dir();
	$upload_base = trailingslashit( $upload_dir['basedir'] );
?>
<br />
<h3 id="system"><?php _e( 'Write Permissions', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
<table cellspacing="1px" cellpadding="4px" style="background-color: white; padding: 5px; border: 1px solid #ccc;">
	<tr>
		<th align="left"><?php _e( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
		<th align="left"><?php _e( 'Value', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
		<th align="left"><?php _e( 'Status', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
	</tr>
	<?php
	foreach ( $permissions as $permission ) {
		if ( $permission['status'] == 'ok' ) {
			$background = '#9e4';
			$color      = 'black';
		} else {
			$background = '#f43';
			$color      = 'white';
		}
		?>
	<tr>
		<td><?php echo $permission['description']; ?></td>
		<td><?php echo str_replace( array('/','\\' ), array('/<wbr>','\\<wbr>' ), $permission['value'] ); ?></td>
		<td style="background-color:<?php echo $background; ?>; color:<?php echo $color; ?>"><?php echo $permission['status_message']; ?></td>
	</tr>

	<?php } ?>

</table>

<p>
	<?php
	/* translators: 1,2. directory paths, 3. UPLOADS, 4. wpo_wcpdf_tmp_path, 5. attachments, 6. dompdf, 7. fonts */
	printf( __( 'The central temp folder is %1$s. By default, this folder is created in the WordPress uploads folder (%2$s), which can be defined by setting %3$s in wp-config.php. Alternatively, you can control the specific folder for PDF invoices by using the %4$s filter. Make sure this folder is writable and that the subfolders %5$s, %6$s and %7$s are present (these will be created by the plugin if the central temp folder is writable).', 'woocommerce-pdf-invoices-packing-slips' ),
		'<code>'.WPO_WCPDF()->main->get_tmp_path().'</code>',
		'<code>'.$upload_base.'</code>',
		'<code>UPLOADS</code>',
		'<code>wpo_wcpdf_tmp_path</code>',
		'<code>attachments</code>',
		'<code>dompdf</code>',
		'<code>fonts</code>'
	);
	?>
</p>
	<?php
	/* translators: directory path */
	printf( __('If the temporary folders were not automatically created by the plugin, verify that all the font files (from %s) are copied to the fonts folder. Normally, this is fully automated, but if your server has strict security settings, this automated copying may have been prohibited. In that case, you also need to make sure these folders get synchronized on plugin updates!', 'woocommerce-pdf-invoices-packing-slips' ),
		'<code>'.WPO_WCPDF()->plugin_path() . "/vendor/dompdf/dompdf/lib/fonts/".'</code>'
	);
	?>
</p>