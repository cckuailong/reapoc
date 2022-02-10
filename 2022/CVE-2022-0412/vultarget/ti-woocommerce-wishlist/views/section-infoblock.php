<?php
/**
 * The Template for displaying admin section info block this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinvwl-info-wrap tinvwl-in-section">
	<div class="tinvwl-table">
		<span class="tinvwl-info-sign tinvwl-cell">
			<span>
				<i class="ftinvwl ftinvwl-info"></i>
			</span>
			<a class="tinvwl-help" href="javascript:void(0)" data-container="body" data-toggle="popover"
			   data-trigger="manual" data-placement="left" data-html="true" rel="nofollow">
				<i class="ftinvwl ftinvwl-info"></i>
			</a>
		</span>
		<span class="tinvwl-info-desc tinvwl-cell"><?php echo $desc; // WPCS: xss ok.  ?></span>
	</div>
</div>
