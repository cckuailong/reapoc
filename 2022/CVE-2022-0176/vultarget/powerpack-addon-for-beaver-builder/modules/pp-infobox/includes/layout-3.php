<div class="<?php echo $main_class; ?>">
	<div class="layout-<?php echo $layout; ?>-wrapper">
		<?php include $module->dir . 'includes/icon-layout.php'; ?>

		<div class="pp-heading-wrapper">

			<<?php echo $title_prefix_tag; ?> class="pp-infobox-title-prefix"><?php echo $settings->title_prefix; ?></<?php echo $title_prefix_tag; ?>>

			<?php if( $settings->pp_infobox_link_type == 'title' ) { ?>
				<a class="pp-more-link" href="<?php echo $settings->link; ?>" target="<?php echo $settings->link_target; ?>">
			<?php } ?>
				<div class="pp-infobox-title-wrapper">
					<<?php echo $settings->title_tag; ?> class="pp-infobox-title"><?php echo $settings->title; ?></<?php echo $settings->title_tag; ?>>
				</div>
			<?php if( $settings->pp_infobox_link_type == 'title' ) { ?>
				</a>
			<?php } ?>

			<div class="pp-infobox-description">
				<div class="pp-description-wrap">
					<?php echo $settings->description; ?>
				</div>
				<?php $module->render_link(); ?>
			</div>
		</div>
	</div>
</div>