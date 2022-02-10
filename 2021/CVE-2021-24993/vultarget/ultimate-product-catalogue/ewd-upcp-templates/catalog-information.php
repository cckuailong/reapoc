<div class='ewd-upcp-catalog-information'>
	
	<?php if ( in_array( 'name', $this->get_option( 'show-catalog-information' ) ) ) { ?>
		
		<div class='ewd-upcp-catalog-information-name'>

			<h3>
				<?php echo esc_html( $this->catalog->post_title ); ?>
			</h3>

		</div>

	<?php } ?>

	<?php if ( in_array( 'description', $this->get_option( 'show-catalog-information' ) ) ) { ?>
	
		<div class='ewd-upcp-catalog-information-description'>
			<?php echo do_shortcode( $this->catalog->post_content ); ?>
		</div>
	
	<?php } ?>

</div>