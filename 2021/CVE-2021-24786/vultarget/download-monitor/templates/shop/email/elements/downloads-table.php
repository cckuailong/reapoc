<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var array $products */
?>
<?php if ( count( $products ) > 0 ) : ?>
	<?php foreach ( $products as $product ) : ?>
        <strong style="font-size: 1.2em;"><?php echo $product['label'] ?></strong><br/>
        <br/>
        <table cellpadding="0" cellspacing="0" border="0" class="dlm-downloads-table">
            <thead>
            <tr>
                <th class="dlm-th-name"><?php _e( "Download name", 'download-monitor' ); ?></th>
                <th class="dlm-th-version"><?php _e( "Version", 'download-monitor' ); ?></th>
                <th class="dlm-th-download-button">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
			<?php if ( count( $product['downloads'] ) > 0 ) : ?>
				<?php foreach ( $product['downloads'] as $item ) : ?>
                    <tr>
                        <td class="dlm-td-name"><?php echo $item['label']; ?></td>
                        <td class="dlm-td-version"><?php echo $item['version']; ?></td>
                        <td class="dlm-td-download-button"><?php echo $item['button']; ?></td>
                    </tr>
				<?php endforeach; ?>
			<?php endif; ?>
            </tbody>
        </table>
	<?php endforeach; ?>
<?php endif; ?>