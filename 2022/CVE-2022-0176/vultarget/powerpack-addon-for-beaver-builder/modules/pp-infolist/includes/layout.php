<?php if ( 'box' === $items->link_type ) { ?>
	<a class="pp-more-link" href="<?php echo $items->link; ?>" target="<?php echo $items->link_target; ?>">
<?php } ?>
<div class="pp-icon-wrapper animated <?php echo $classes; ?>">
	<div class="pp-infolist-icon">
		<div class="pp-infolist-icon-inner">
			<?php if ( $items->icon_type == 'icon' ) { ?>
				<span class="pp-icon <?php echo $items->icon_select; ?>"></span>
			<?php } else { ?>
				<?php if ( isset( $items->image_select_src ) && ! empty( $items->image_select_src ) ) { ?>
				<img src="<?php echo $items->image_select_src; ?>" alt="<?php echo get_the_title( absint( $items->image_select ) ); ?>" />
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>
<div class="pp-heading-wrapper">
	<div class="pp-infolist-title">
		<?php if ( $items->link_type == 'title' ) { ?>
			<a class="pp-more-link" href="<?php echo $items->link; ?>" target="<?php echo $items->link_target; ?>">
		<?php } ?>
		<<?php echo $title_tag; ?> class="pp-infolist-title-text"><?php echo $items->title; ?></<?php echo $title_tag; ?>>
		<?php if ( $items->link_type == 'title' ) { ?>
			</a>
		<?php } ?>
	</div>
	<div class="pp-infolist-description">
		<?php echo $items->description; ?>
		<?php if ( $items->link_type == 'read_more' ) { ?>
			<a class="pp-more-link" href="<?php echo $items->link; ?>" target="<?php echo $items->link_target; ?>"><?php echo $items->read_more_text; ?></a>
		<?php } ?>
	</div>
</div>

<div class="pp-list-connector"></div>
<?php if ( 'box' === $items->link_type ) { ?>
	</a>
<?php } ?>