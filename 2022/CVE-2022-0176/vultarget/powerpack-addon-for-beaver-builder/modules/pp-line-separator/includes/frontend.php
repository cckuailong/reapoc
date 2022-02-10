<div class="pp-line-separator-wrap">
	<?php if( $settings->line_separator == 'line_only' ) { ?>
		<div class="pp-line-separator-inner pp-line-only">
			<span class="pp-line-separator pp-line-only"></span>
		</div>
	<?php } ?>
	<?php if( $settings->line_separator == 'icon_image' ) { ?>
		<div class="pp-line-separator-inner pp-icon-image">
			<?php if( $settings->icon_image_select == 'icon' ) { ?>
				<div class="pp-line-separator pp-icon-wrap">
					<span class="pp-icon <?php echo $settings->separator_icon; ?>"></span>
				</div>
			<?php } else { ?>
				<div class="pp-line-separator pp-image-wrap">
					<img class="pp-icon-image pp-type-<?php echo $settings->icon_image_select; ?>" src="<?php echo wp_get_attachment_url( absint($settings->separator_image) ); ?>" alt="<?php echo pp_get_image_alt($settings->separator_image); ?>" />
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if( $settings->line_separator == 'line_with_icon' ) { ?>
		<div class="pp-line-separator-inner pp-line-icon <?php echo $settings->separator_alignment; ?>">
			<div class="pp-line-separator-inner pp-icon-image">
				<?php if( $settings->icon_image_select == 'icon' ) { ?>
					<div class="pp-line-separator pp-icon-wrap">
						<span class="pp-icon <?php echo $settings->separator_icon; ?>"></span>
					</div>
				<?php } else { ?>
					<div class="pp-line-separator pp-image-wrap">
						<img class="pp-icon-image" src="<?php echo wp_get_attachment_url( absint($settings->separator_image) ); ?>" alt="<?php echo pp_get_image_alt($settings->separator_image); ?>" />
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>
