<?php
/**
 * Custom Facebook Feed Item : Item Container
 * This is the item container
 *
 * @version 2.19 Custom Facebook Feed by Smash Balloon
 *
 */
$name = isset($news->from->name) ? $news->from->name : false;

$post_item_attr = $this_class->get_item_attributes($cff_post_type, $cff_album, $cff_post_bg_color_check, $cff_post_style, $cff_box_shadow, $name, $cff_post_id);

?>
<div <?php echo $post_item_attr['id'] ?> <?php echo $post_item_attr['class'] ?> <?php echo $post_item_attr['style'] ?>>
	<?php
		if( $cff_show_author )
			echo $cff_author;
		if ( $cff_show_date && $cff_date_position == 'above' )
			echo $cff_date;
		if( $cff_show_text || $cff_show_desc )
			echo $cff_post_text;
		if( $cff_show_shared_links )
			echo $cff_shared_link;
		if ( (!$cff_show_author && $cff_date_position == 'author') || $cff_show_date && $cff_date_position == 'below' )
			echo $cff_date;
		if( $cff_show_media_link )
			echo $cff_media_link;
		if( $cff_show_link )
			echo $cff_link;
	?>
</div>



<?php
