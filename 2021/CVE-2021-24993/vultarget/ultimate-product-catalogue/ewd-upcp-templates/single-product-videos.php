<div class='ewd-upcp-single-product-videos'>

	<?php foreach ( $this->product->get_videos() as $key => $video ) { ?>

		<?php /* <div class='ewd-upcp-single-product-video-description'>
			<?php echo esc_html( $video->description ); ?>
		</div> */ ?>

		<div class='ewd-upcp-single-video' data-video_key='<?php echo ( $key + 1 ); ?>'>

			<iframe width='300' height='225' src='<?php echo esc_attr( $video->embed_url ); ?>' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

		</div>

	<?php } ?>

</div>