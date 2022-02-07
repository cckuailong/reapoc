<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var array $products */
?>
<?php if ( count( $products ) > 0 ) : ?>
	<?php foreach ( $products as $product ) : ?>
		-- <?php echo $product['label']; ?> -- <?php echo PHP_EOL; ?>
		<?php if ( count( $product['downloads'] ) > 0 ) : ?>
			<?php foreach ( $product['downloads'] as $item ) : ?>
				<?php echo $item['label']; ?> ( <?php echo $item['version']; ?> ): <?php echo $item['download_url'] . PHP_EOL; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
