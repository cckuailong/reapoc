<?php
/**
 * Detailed download output
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var DLM_Download $dlm_download */

?>
<aside class="download-box">

	<?php $dlm_download->the_image(); ?>

	<div
		class="download-count"><?php printf( _n( '1 download', '%d downloads', $dlm_download->get_download_count(), 'download-monitor' ), $dlm_download->get_download_count() ) ?></div>

	<div class="download-box-content">

		<h1><?php $dlm_download->the_title(); ?></h1>

		<?php $dlm_download->the_excerpt(); ?>

		<a class="download-button" title="<?php if ( $dlm_download->get_version()->has_version_number() ) {
			printf( __( 'Version %s', 'download-monitor' ), $dlm_download->get_version()->get_version_number() );
		} ?>" href="<?php $dlm_download->the_download_link(); ?>" rel="nofollow">
			<?php _e( 'Download File', 'download-monitor' ); ?>
			<small><?php echo $dlm_download->get_version()->get_filename(); ?> &ndash; <?php echo $dlm_download->get_version()->get_filesize_formatted(); ?></small>
		</a>

	</div>
</aside>


