<div class='ewd-upcp-catalog-overview'>
	
	<?php foreach ( $this->get_overview_items() as $overview_item ) { ?>

		<a href='<?php echo esc_attr( $overview_item->permalink ); ?>'>
		
			<div>
			
				<div class='ewd-upcp-overview-mode-image'>
					<img src='<?php echo esc_attr( $overview_item->image ); ?>' />
				</div>
		
				<div class='ewd-upcp-overview-mode-title'>
					<?php echo esc_html( $overview_item->title ); ?>
				</div>
		
			</div>

		</a>

	<?php } ?>

</div>