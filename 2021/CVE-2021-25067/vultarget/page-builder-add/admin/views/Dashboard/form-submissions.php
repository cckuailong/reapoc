<?php if ( ! defined( 'ABSPATH' ) ) exit;

if (function_exists('ulpb_available_pro_widgets') ) {
}else {
	?>
	<div  class="abTestNotice" style=""> 
	    <i class='fa fa-circle-o-notch'></i> 
	   	Did you know You can View, Download, Export & Sync your form submissions with your favorite  email marketing services :   
	    <a href='https://pluginops.com/page-builder/?ref=formSubmissions' target='_blank' style="padding: 5px 10px;"> Click here to order</a>
	</div>
	<?php
}


$args = array(
	'offset'           => 0,
	'posts_per_page'   => 100,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'ulpb_post',
	'post_status'      => array('publish','draft'),
);

$ulpb_pages = get_posts( $args );
$allLandingPagePosts = '';

$postId = ' ';
	
if (isset($_GET['selectedPostID'])) {
			
	$postId = sanitize_text_field( $_GET['selectedPostID'] );

}

if (!empty($ulpb_pages)) {

	foreach ($ulpb_pages as $ulpb_single_post) {

		$isSelected = '';
		if ($postId == $ulpb_single_post->ID) {
			$isSelected = 'selected';
		}

		$allLandingPagePosts = $allLandingPagePosts. " <option value='". $ulpb_single_post->ID. "' $isSelected > ". get_the_title($ulpb_single_post) ." </option> ";
	
	}
}

?>

<div style="background-color: #fff; padding: 10px 20px; border-radius: 7px; width: 60%; margin: 50px 100px;">

	<label style="margin-right: 20px;"> Select A Landing Page To View Form Submissions
	input</label>
	<select id="selectPostTypeFormSubmissions">
		<option value="none">None</option>
		
		<?php echo $allLandingPagePosts; ?>
	</select>

</div>

<?php
	
	if (function_exists('popb_formBuilder_database_renderFormDataTable')) {

		echo 
			
			"<div style='background:#fff; margin:25px 50px; padding:5px; border-radius:7px;'>".

				popb_formBuilder_database_renderFormDataTable($postId).

			"</div>"
		;

	}else{
		echo "<h1> Please get Form Builder Database extension to access all the submissions. </h1>";
	}

?>