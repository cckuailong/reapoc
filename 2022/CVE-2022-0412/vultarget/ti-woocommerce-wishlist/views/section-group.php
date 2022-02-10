<?php
/**
 * The Template for displaying admin section group this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<tbody <?php echo $extra; // WPCS: xss ok. ?>>
<tr class="tinvwl-bodies-border">
	<td>
		<div class="tinvwl-inner"></div>
	</td>
	<?php if ( $show_helper ) : ?>
		<td class="tinvwl-info w-bg-grey"></td>
	<?php endif; ?>
</tr>
<tr>
	<td>
		<div class="tinvwl-inner">
			<?php if ( $show_names && $title ) : ?>
				<h2><?php echo $title; // WPCS: xss ok. ?></h2>
			<?php endif; ?>
		</div>
	</td>
	<?php if ( $show_helper ) : ?>
		<td class="tinvwl-info w-bg-grey" <?php echo ( $desc ) ? 'rowspan="' . $fields_count . '"' : ''; // WPCS: xss ok. ?>>
			<?php if ( ! empty( $desc ) ) {
				self::view( 'section-infoblock', array( 'desc' => $desc ), '' );
			} ?>
		</td>
	<?php endif; ?>
</tr>
<?php echo $fields; // WPCS: xss ok. ?>
</tbody>
