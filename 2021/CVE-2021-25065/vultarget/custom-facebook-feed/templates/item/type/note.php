<?php
/**
 * Custom Facebook Feed Item : Note Post Type
 * Displays the feed note post type!
 *
 * @version 2.19 Custom Facebook Feed by Smash Balloon
 *
 */
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$transient_name = 'cff_tle_' . $cff_post_id;
$transient_name = substr($transient_name, 0, 45);
if ( false !== ( $cff_note_json = get_transient( $transient_name ) ) ) {
	$cff_note_json = get_transient( $transient_name );

	//Interpret data with JSON
	$cff_note_obj = json_decode($cff_note_json);
	$cff_note_object = $cff_note_obj->attachments->data[0];

	$cff_note_title 		= isset($cff_note_object->title) ? htmlentities($cff_note_object->title, ENT_QUOTES, 'UTF-8')  : '';
	$cff_note_description 	= isset($cff_note_object->description) ? htmlentities($cff_note_object->description, ENT_QUOTES, 'UTF-8') : '';
	$cff_note_link 			= isset($cff_note_object->url) ? $cff_note_object->url : '';
	$cff_note_media_src 	= isset( $cff_note_object->media->image->src ) ? $cff_note_object->media->image->src : false;

} else {
	$attachment_data = '';
	if(isset($news->attachments->data[0])){
		$attachment_data = $news->attachments->data[0];
		$cff_note_title 		= isset($attachment_data->title) ? htmlentities($attachment_data->title, ENT_QUOTES, 'UTF-8') : '';
		$cff_note_description 	= isset($attachment_data->description) ? htmlentities($attachment_data->description, ENT_QUOTES, 'UTF-8') : '';
		$cff_note_link 			= isset($attachment_data->unshimmed_url) ? $attachment_data->unshimmed_url : '';
		$cff_note_media_src = '';
	}
}

?>
<span class="cff-details">
	<span class="cff-note-title"><?php echo $cff_note_title ?></span>
	<?php echo $cff_note_description ?>
</span>