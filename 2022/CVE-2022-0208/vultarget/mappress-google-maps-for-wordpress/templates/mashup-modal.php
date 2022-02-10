<?php
/**
 * Default template for displaying a mashup post in a modal dialog
 *
 */
?>

<div class="mapp-modal-featured">
	<?php the_post_thumbnail(); ?>
</div>

<article tabindex="0" class="mapp-modal-article mapp-modal-focus">
	<div class="mapp-modal-title">
		<?php the_title('<h1>', '</h1>' ); ?>
	</div>

	<div class="mapp-modal-body">
		<?php the_content(__('Continue reading', 'mappress-google-maps-for-wordpress') ); ?>
	</div>

	<div class="mapp-modal-meta">
		<div>
			<div><a href="<?php the_permalink(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></div>
			<div><?php printf(/* translators: %s: Author name. */ __( 'By %s' , 'mappress-google-maps-for-wordpress'), '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author_meta( 'display_name' ) ) . '</a>');?></div>
		</div>

		<div>
			<div><?php _e( 'In'  , 'mappress-google-maps-for-wordpress'); ?> <?php the_category( ', ' ); ?></div>
			<div><?php _e(' Tagged ' , 'mappress-google-maps-for-wordpress'); ?><?php the_tags( '', ', ', '' ); ?></div>
		</div>
	</div>
</article>

