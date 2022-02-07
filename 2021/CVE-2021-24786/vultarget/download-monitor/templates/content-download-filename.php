<?php
/**
 * Default output for a download via the [download] shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var DLM_Download $dlm_download */
?>
<a class="download-link filetype-icon <?php echo 'filetype-' . $dlm_download->get_version()->get_filetype(); ?>"
   title="<?php if ( $dlm_download->get_version()->has_version_number() ) {
	   printf( __( 'Version %s', 'download-monitor' ), $dlm_download->get_version()->get_version_number() );
   } ?>" href="<?php $dlm_download->the_download_link(); ?>" rel="nofollow">
	<?php echo $dlm_download->get_version()->get_filename(); ?>
	(<?php printf( _n( '1 download', '%d downloads', $dlm_download->get_download_count(), 'download-monitor' ), $dlm_download->get_download_count() ) ?>)
</a>