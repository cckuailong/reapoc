<?php
/**
 * The Template for displaying admin section field this plugin.
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
		<div class="tinvwl-inner">
			<?php if ( $label ) { ?>
				<div class="form-horizontal">
					<div class="form-group">
						<div class="control-label col-lg-6">
							<?php echo $label; // WPCS: xss ok. ?>
						</div>
						<div class="col-lg-6">
							<?php echo $field; // WPCS: xss ok. ?>
						</div>
					</div>
				</div>
			<?php } else {
				echo $field; // WPCS: xss ok.
			} ?>
		</div>
	</td>
	<?php if ( $show_helper && $show_field_desc ) : ?>
		<td class="tinvwl-info w-bg-grey">
			<?php if ( ! empty( $desc ) ) {
				self::view( 'section-infoblock', array( 'desc' => $desc ), '' );
			} ?>
		</td>
	<?php endif; ?>
</tr>
