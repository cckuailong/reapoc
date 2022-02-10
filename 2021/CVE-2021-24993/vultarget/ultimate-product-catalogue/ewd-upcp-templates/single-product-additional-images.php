<div class='ewd-upcp-single-product-thumbnails'>
	
	<?php foreach ( $this->product->get_all_images() as $count => $image ) { ?>
		
		<a class='ewd-upcp-thumbnail-anchor <?php echo ( ! empty( $image->video_key ) ? 'ewd-upcp-video-thumbnail' : '' ); ?> <?php echo $this->get_additional_images_lightbox_class(); ?>' href='<?php echo esc_attr( $image->url ); ?>' data-ulbsource='<?php echo esc_attr( $image->url ); ?>' data-ulbtitle='<?php echo esc_attr( $image->description ); ?>' data-ulbdescription='<?php echo esc_attr( $image->description ); ?>' data-video_key='<?php echo ( ! empty( $image->video_key ) ? $image->video_key  : '' ); ?>'>
			<img src='<?php echo esc_attr( $image->url ); ?>' class='ewd-upcp-single-product-thumbnail'>
		</a>

	<?php } ?>		

</div>