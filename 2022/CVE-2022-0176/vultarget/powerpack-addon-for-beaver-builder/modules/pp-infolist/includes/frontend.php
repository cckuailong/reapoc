<?php
$title_tag = ( isset( $settings->title_tag ) ) ? $settings->title_tag : 'h3';
$number_items = count( $settings->list_items );
$layout = $settings->layouts;
?>
<div class="pp-infolist-wrap">
	<div class="pp-infolist layout-<?php echo $layout; ?>">
		<ul class="pp-list-items">
		<?php
		for ( $i = 0; $i < $number_items; $i++ ) {
			if ( ! is_object( $settings->list_items[ $i ] ) ) {
				continue;
			}
			$items = $settings->list_items[ $i ];
			$classes = '';
			if ( $items->icon_animation ) {
				$classes = $items->icon_animation;
			} else {
				$classes = '';
			}
		?>
			<li class="pp-list-item pp-list-item-<?php echo $i; ?>">
				<?php include $module->dir . 'includes/layout.php'; ?>
			</li>
		<?php } ?>
		</ul>
	</div>
</div>
