<?php
/**
 * Default output for a download via the [download] shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if( ! $dlm_download ) {
	return;
}

/** @var DLM_Download $dlm_download */
?>
<a class="download-link" title="<?php if ( $dlm_download->get_version()->has_version_number() ) {
	printf( __( 'Version %s', 'download-monitor' ), $dlm_download->get_version()->get_version_number() );
} ?>" href="<?php $dlm_download->the_download_link(); ?>" rel="nofollow">
	<?php $dlm_download->the_title(); ?>
	(<?php printf( _n( '1 download', '%d downloads', $dlm_download->get_download_count(), 'download-monitor' ), $dlm_download->get_download_count() ) ?>)
</a>