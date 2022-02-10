<?php
/**
 * The Template for displaying admin section field HTML this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<tr <?php echo $extra_div; // WPCS: xss ok. ?>>
	<td>
		<?php echo $html; // WPCS: xss ok. ?>
	</td>
	<?php if ( $show_helper && $show_field_desc ) : ?>
		<td class="tinvwl-info w-bg-grey">
			<?php if ( ! empty( $desc ) ) {
				self::view( 'section-infoblock', array( 'desc' => $desc ), '' );
			} ?>
		</td>
	<?php endif; ?>
</tr>
