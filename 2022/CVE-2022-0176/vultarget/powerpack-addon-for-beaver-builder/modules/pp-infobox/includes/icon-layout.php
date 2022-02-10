<div class="pp-icon-wrapper animated">
	<?php if ( $settings->icon_type == 'icon' ) { ?>
		<?php if ( ! empty( $settings->icon_select ) ) { ?>
			<div class="pp-infobox-icon">
				<div class="pp-infobox-icon-inner">
					<span class="pp-icon <?php echo $settings->icon_select; ?>"></span>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<?php if ( isset( $settings->image_select_src ) && ! empty( $settings->image_select_src ) ) { ?>
			<div class="pp-infobox-image">
				<img src="<?php echo $settings->image_select_src; ?>" alt="<?php echo $module->get_alt(); ?>" />
			</div>
		<?php } ?>
	<?php } ?>
</div>