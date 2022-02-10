<?php
$nofollow_1 = isset( $settings->button_1_link_nofollow ) && 'yes' === $settings->button_1_link_nofollow ? ' rel="nofollow"' : '';
$target_1   = isset( $settings->button_1_link_target ) ? ' target="' . $settings->button_1_link_target . '"' : '';
$nofollow_2 = isset( $settings->button_2_link_nofollow ) && 'yes' === $settings->button_2_link_nofollow ? ' rel="nofollow"' : '';
$target_2   = isset( $settings->button_2_link_target ) ? ' target="' . $settings->button_2_link_target . '"' : '';
$attr1      = '';
$attr2      = '';
if ( isset( $settings->enable_title_attr_1 ) && 'yes' === $settings->enable_title_attr_1 && ! empty( $settings->title_attr_1 ) ) {
	$attr1  = ' title="' . $settings->title_attr_1 . '"';
	$attr1 .= ' alt="' . $settings->title_attr_1 . '"';
}
if ( isset( $settings->enable_title_attr_2 ) && 'yes' === $settings->enable_title_attr_2 && ! empty( $settings->title_attr_2 ) ) {
	$attr2  = ' title="' . $settings->title_attr_2 . '"';
	$attr2 .= ' alt="' . $settings->title_attr_2 . '"';
}
?>
<div class="pp-dual-button-content clearfix">
	<div class="pp-dual-button-inner">
		<div class="pp-dual-button-1 pp-dual-button pp-button-effect-<?php echo $settings->button_1_effect; ?>">
			<a href="<?php echo $settings->button_1_link; ?>" class="pp-button <?php echo $settings->button_1_css_class; ?>" role="button"<?php echo $target_1; ?><?php echo $nofollow_1; ?><?php echo $attr1; ?> onclick="">
				<?php if ( 'left' === $settings->button_1_icon_aligment ) { ?>
					<?php if ( 'font_icon' === $settings->button_icon_select_1 && isset( $settings->button_font_icon_1 ) && ! empty( $settings->button_font_icon_1 ) ) { ?>
						<span class="pp-font-icon <?php echo $settings->button_font_icon_1; ?>"></span>
					<?php } ?>
					<?php if ( 'custom_icon' === $settings->button_icon_select_1 && isset( $settings->button_custom_icon_1 ) && ! empty( $settings->button_custom_icon_1 ) ) { ?>
						<img class="pp-custom-icon" src="<?php echo $settings->button_custom_icon_1_src; ?>" />
					<?php } ?>
				<?php } ?>
				<span class="pp-button-1-text"><?php echo $settings->button_1_title; ?></span>
				<?php if ( 'right' === $settings->button_1_icon_aligment ) { ?>
					<?php if ( 'font_icon' === $settings->button_icon_select_1 && isset( $settings->button_font_icon_1 ) && ! empty( $settings->button_font_icon_1 ) ) { ?>
						<span class="pp-font-icon <?php echo $settings->button_font_icon_1; ?>"></span>
					<?php } ?>
					<?php if ( 'custom_icon' === $settings->button_icon_select_1 && isset( $settings->button_custom_icon_1 ) && ! empty( $settings->button_custom_icon_1 ) ) { ?>
						<img class="pp-custom-icon" src="<?php echo $settings->button_custom_icon_1_src; ?>" />
					<?php } ?>
				<?php } ?>
			</a>
		</div>
		<div class="pp-spacer"></div>
		<div class="pp-dual-button-2 pp-dual-button pp-button-effect-<?php echo $settings->button_2_effect; ?>">
			<a href="<?php echo $settings->button_2_link; ?>" class="pp-button <?php echo $settings->button_2_css_class; ?>" role="button"<?php echo $target_2; ?><?php echo $nofollow_2; ?><?php echo $attr1; ?> onclick="">
				<?php if ( 'left' === $settings->button_2_icon_aligment ) { ?>
					<?php if ( 'font_icon' === $settings->button_icon_select_2 && isset( $settings->button_font_icon_2 ) && ! empty( $settings->button_font_icon_2 ) ) { ?>
						<span class="pp-font-icon <?php echo $settings->button_font_icon_2; ?>"></span>
					<?php } ?>
					<?php if ( 'custom_icon' === $settings->button_icon_select_2 && isset( $settings->button_custom_icon_2 ) && ! empty( $settings->button_custom_icon_2 ) ) { ?>
						<img class="pp-custom-icon" src="<?php echo $settings->button_custom_icon_2_src; ?>" />
					<?php } ?>
				<?php } ?>
				<span class="pp-button-2-text"><?php echo $settings->button_2_title; ?></span>
				<?php if ( 'right' === $settings->button_2_icon_aligment ) { ?>
					<?php if ( 'font_icon' === $settings->button_icon_select_2 && isset( $settings->button_font_icon_2 ) && ! empty( $settings->button_font_icon_2 ) ) { ?>
						<span class="pp-font-icon <?php echo $settings->button_font_icon_2; ?>"></span>
					<?php } ?>
					<?php if ( 'custom_icon' === $settings->button_icon_select_2 && isset( $settings->button_custom_icon_2 ) && ! empty( $settings->button_custom_icon_2 ) ) { ?>
						<img class="pp-custom-icon" src="<?php echo $settings->button_custom_icon_2_src; ?>" />
					<?php } ?>
				<?php } ?>
			</a>
		</div>
	</div>
</div>
