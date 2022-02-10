<?php
$enable_link = ( isset( $settings->enable_link ) && 'no' == $settings->enable_link ) ? false : true;
$alt = $settings->heading_title;

if ( isset( $settings->dual_heading ) && 'yes' == $settings->dual_heading && ! empty( $settings->heading_title2 ) ) { 
	$alt .= ' ' . $settings->heading_title2;
}
?>
<div class="pp-heading-content">
	<?php if ( 'top' == $settings->heading_separator_postion && 'no_spacer' != $settings->heading_separator && 'inline' != $settings->heading_separator ) { ?>
		<div class="pp-heading-separator <?php echo $settings->heading_separator; ?> pp-<?php echo $settings->heading_alignment; ?>">
			<?php if ( 'line_with_icon' == $settings->heading_separator ) { ?>
				<div class="pp-heading-separator-wrapper">
					<div class="pp-heading-separator-align">
						<div class="pp-heading-separator-icon">
							<?php if ( $settings->heading_font_icon_select && 'font_icon_select' == $settings->heading_icon_select ) { ?>
								<i class="<?php echo $settings->heading_font_icon_select; ?> pp-separator-font-icon"></i>
							<?php } ?>
							<?php if ( $settings->heading_custom_icon_select && 'custom_icon_select' == $settings->heading_icon_select ) { ?>
								<img class="heading-icon-image" src="<?php echo $settings->heading_custom_icon_select_src; ?>" alt="<?php echo $alt; ?>" />
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if ( 'icon_only' == $settings->heading_separator && 'font_icon_select' == $settings->heading_icon_select ) { ?>
				<div class="pp-heading-separator-wrapper">
					<div class="pp-heading-separator-align">
						<div class="pp-heading-separator-icon">
							<?php if ( $settings->heading_font_icon_select ) { ?>
								<i class="<?php echo $settings->heading_font_icon_select; ?> pp-separator-font-icon"></i>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ( 'line_only' == $settings->heading_separator ) { ?>
				<span class="pp-separator-line"></span>
			<?php } ?>

			<?php if ( 'icon_only' == $settings->heading_separator && 'custom_icon_select' == $settings->heading_icon_select ) { ?>
				<span class="separator-image">
					<img class="heading-icon-image" src="<?php echo $settings->heading_custom_icon_select_src; ?>" alt="<?php echo $alt; ?>" />
				</span>
			<?php } ?>

		</div>
	<?php } ?>
	<div class="pp-heading <?php if ( 'inline' == $settings->heading_separator ) { echo 'pp-separator-' . $settings->heading_separator; } ?> pp-<?php echo $settings->heading_alignment; ?>">

		<<?php echo $settings->heading_tag; ?> class="heading-title">

			<?php if ( $enable_link && ! empty( $settings->heading_link ) ) : ?>
				<a class="pp-heading-link"
					href="<?php echo $settings->heading_link; ?>"
					target="<?php echo $settings->heading_link_target; ?>"
					<?php echo ( isset( $settings->heading_link_nofollow ) && 'on' == $settings->heading_link_nofollow ) ? ' rel="nofollow"' : ''; ?>
					>
			<?php endif; ?>

			<span class="title-text pp-primary-title"><?php echo $settings->heading_title; ?></span>
			<?php if ( isset( $settings->dual_heading ) && 'yes' == $settings->dual_heading ) { ?>
				<?php if ( 'block' === $settings->heading_style && 'between' === $settings->heading_separator_postion && 'no_spacer' !== $settings->heading_separator && 'inline' !== $settings->heading_separator ) { ?>
					<div class="pp-heading-separator <?php echo $settings->heading_separator; ?> pp-<?php echo $settings->heading_alignment; ?>">
						<?php if ( 'line_with_icon' == $settings->heading_separator ) { ?>
							<div class="pp-heading-separator-wrapper">
								<div class="pp-heading-separator-align">
									<div class="pp-heading-separator-icon">
										<?php if ( $settings->heading_font_icon_select && 'font_icon_select' == $settings->heading_icon_select ) { ?>
											<i class="<?php echo $settings->heading_font_icon_select; ?> pp-separator-font-icon"></i>
										<?php } ?>
										<?php if ( $settings->heading_custom_icon_select && 'custom_icon_select' == $settings->heading_icon_select ) { ?>
											<img class="heading-icon-image" src="<?php echo $settings->heading_custom_icon_select_src; ?>" alt="<?php echo $alt; ?>" />
										<?php } ?>
									</div>
								</div>
							</div>
						<?php } ?>
						<?php if ( 'icon_only' == $settings->heading_separator && 'font_icon_select' == $settings->heading_icon_select ) { ?>
							<div class="pp-heading-separator-wrapper">
								<div class="pp-heading-separator-align">
									<div class="pp-heading-separator-icon">
										<?php if ( $settings->heading_font_icon_select ) { ?>
											<i class="<?php echo $settings->heading_font_icon_select; ?> pp-separator-font-icon"></i>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php } ?>

						<?php if ( 'line_only' == $settings->heading_separator ) { ?>
							<span class="pp-separator-line"></span>
						<?php } ?>

						<?php if ( 'icon_only' == $settings->heading_separator && 'custom_icon_select' == $settings->heading_icon_select ) { ?>
							<span class="separator-image">
								<img class="heading-icon-image" src="<?php echo $settings->heading_custom_icon_select_src; ?>" alt="<?php echo $alt; ?>" />
							</span>
						<?php } ?>

					</div>
				<?php } ?>
				<span class="title-text pp-secondary-title"><?php echo $settings->heading_title2; ?></span>
			<?php } ?>

			<?php if ( $enable_link && ! empty( $settings->heading_link ) ) : ?>
				</a>
			<?php endif; ?>

		</<?php echo $settings->heading_tag; ?>>

	</div>
	<?php if ( 'middle' == $settings->heading_separator_postion && 'no_spacer' != $settings->heading_separator && 'inline' != $settings->heading_separator ) { ?>
		<div class="pp-heading-separator <?php echo $settings->heading_separator; ?> pp-<?php echo $settings->heading_alignment; ?>">

			<?php if ( 'line_with_icon' == $settings->heading_separator ) { ?>
				<div class="pp-heading-separator-wrapper">
					<div class="pp-heading-separator-align">
						<div class="pp-heading-separator-icon">
							<?php if ( $settings->heading_font_icon_select && 'font_icon_select' == $settings->heading_icon_select ) { ?>
								<i class="<?php echo $settings->heading_font_icon_select; ?> pp-separator-font-icon"></i>
							<?php } ?>
							<?php if ( $settings->heading_custom_icon_select && 'custom_icon_select' == $settings->heading_icon_select ) { ?>
								<img class="heading-icon-image" src="<?php echo $settings->heading_custom_icon_select_src; ?>" alt="<?php echo $alt; ?>" />
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if ( 'icon_only' == $settings->heading_separator && 'font_icon_select' == $settings->heading_icon_select ) { ?>
				<div class="pp-heading-separator-wrapper">
					<div class="pp-heading-separator-align">
						<div class="pp-heading-separator-icon">
							<?php if ( $settings->heading_font_icon_select ) { ?>
								<i class="<?php echo $settings->heading_font_icon_select; ?> pp-separator-font-icon"></i>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ( 'line_only' == $settings->heading_separator ) { ?>
				<span class="pp-separator-line"></span>
			<?php } ?>

			<?php if ( 'icon_only' == $settings->heading_separator && 'custom_icon_select' == $settings->heading_icon_select ) { ?>
				<span class="separator-image">
					<img class="heading-icon-image" src="<?php echo $settings->heading_custom_icon_select_src; ?>" alt="<?php echo $alt; ?>" />
				</span>
			<?php } ?>

		</div>
	<?php } ?>
	<?php if ( isset( $settings->heading_sub_title ) && ! empty( $settings->heading_sub_title ) ) { ?>
		<div class="pp-sub-heading">
			<?php echo $settings->heading_sub_title; ?>
		</div>
	<?php } ?>

	<?php if ( 'bottom' == $settings->heading_separator_postion && 'no_spacer' != $settings->heading_separator && 'inline' != $settings->heading_separator ) { ?>
		<div class="pp-heading-separator <?php echo $settings->heading_separator; ?> pp-<?php echo $settings->heading_alignment; ?>">

			<?php if ( 'line_with_icon' == $settings->heading_separator ) { ?>
				<div class="pp-heading-separator-wrapper">
					<div class="pp-heading-separator-align">
						<div class="pp-heading-separator-icon">
							<?php if ( $settings->heading_font_icon_select && 'font_icon_select' == $settings->heading_icon_select ) { ?>
								<i class="<?php echo $settings->heading_font_icon_select; ?> pp-separator-font-icon"></i>
							<?php } ?>
							<?php if ( $settings->heading_custom_icon_select && 'custom_icon_select' == $settings->heading_icon_select ) { ?>
								<img class="heading-icon-image" src="<?php echo $settings->heading_custom_icon_select_src; ?>" alt="<?php echo $alt; ?>" />
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if ( 'icon_only' == $settings->heading_separator && 'font_icon_select' == $settings->heading_icon_select ) { ?>
				<div class="pp-heading-separator-wrapper">
					<div class="pp-heading-separator-align">
						<div class="pp-heading-separator-icon">
							<?php if ( $settings->heading_font_icon_select ) { ?>
								<i class="<?php echo $settings->heading_font_icon_select; ?> pp-separator-font-icon"></i>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ( 'line_only' == $settings->heading_separator ) { ?>
				<span class="pp-separator-line"></span>
			<?php } ?>

			<?php if ( 'icon_only' == $settings->heading_separator && 'custom_icon_select' == $settings->heading_icon_select ) { ?>
				<span class="separator-image">
					<img class="heading-icon-image" src="<?php echo $settings->heading_custom_icon_select_src; ?>" alt="<?php echo $alt; ?>" />
				</span>
			<?php } ?>

		</div>
	<?php } ?>
</div>
