<?php
/**
 * @package @TUTOR
 * @since v.1.0.0
 */
?>

<div class="wrap tutor-uninstall-wrap">
	<h2><?php _e('Uninstall Tutor', 'tutor'); ?></h2>
    <p class="desc"><?php _e('Just deactivate tutor plugin or completely uninstall and erase all of data saved before by tutor.', 'tutor'); ?></p>

    <div class="tutor-uninstall-btn-group">
        <?php $plugin_file = tutor()->basename; ?>

        <a href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin_file ) . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ); ?>" class="tutor-button button-warning"> Deactive </a>

        <a href="admin.php?action=uninstall_tutor_and_erase" class="tutor-button button-danger"><?php _e('Completely Uninstall and erase all data', 'tutor'); ?></a>
    </div>
</div>