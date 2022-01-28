<?php
/**
 * Render extensions table
 */

defined( 'ABSPATH' ) || exit;

$extensions = $this->get_extensions();
?>

<div class="extensions-list">
	<div class="extensions">
		<?php if ( $extensions ) : ?>
			<?php foreach ( $extensions as $extension_slug => $extension ) : ?>
				<div class="extension">
					<?php echo $extension->get_action_promo(); ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
