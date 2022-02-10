<?php
/**
 * The Template for displaying admin section this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$show_title = $show_names && $title;
?>
<section <?php echo $extra; // WPCS: xss ok. ?>>
	<table class="tinvwl-table w-info">
		<thead <?php echo( ! $show_title ? 'class="tinwl-empty"' : '' ); ?>>
		<tr>
			<th>
				<div class="tinvwl-inner">
					<?php if ( $show_title ) : ?>
						<h3><?php echo $title; // WPCS: xss ok. ?></h3>
					<?php endif; ?>
				</div>
			</th>
			<?php if ( $show_helper ) : ?>
				<th class="tinvwl-info w-bg-grey">
					<?php if ( ! empty( $desc ) ) {
						self::view( 'section-infoblock', array( 'desc' => $desc ), '' );
					} ?>
				</th>
			<?php endif; ?>
		</tr>
		</thead>
		<?php echo $groups; // WPCS: xss ok. ?>
		<tfoot>
		<tr>
			<td>
				<div class="tinvwl-inner"></div>
			</td>
			<?php if ( $show_helper ) : ?>
				<td class="w-bg-grey"></td>
			<?php endif; ?>
		</tr>
		</tfoot>
	</table>
</section>
