<?php
/**
 * Download button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var DLM_Download $dlm_download */
?>
<p><a class="aligncenter download-button" href="<?php $dlm_download->the_download_link(); ?>" rel="nofollow">
		<?php printf( __( 'Download &ldquo;%s&rdquo;', 'download-monitor' ), $dlm_download->get_title() ); ?>
		<small><?php echo $dlm_download->get_version()->get_filename(); ?> &ndash; <?php printf( _n( 'Downloaded 1 time', 'Downloaded %d times', $dlm_download->get_download_count(), 'download-monitor' ), $dlm_download->get_download_count() ) ?> &ndash; <?php echo $dlm_download->get_version()->get_filesize_formatted(); ?></small>
	</a></p>