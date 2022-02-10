<?php if ( ! defined( 'ABSPATH' ) ) exit;


$thisPostType = get_post_type( $postId );
$ULPB_args = array(
    'post_type' => 'ulpb_post',
    'orderby' => 'date',
    'post_status'   => 'any',
    'posts_per_page'    => 100,
);
$ULPB_PrevPosts = get_posts( $ULPB_args );
echo "<br> <p style='  text-indent: 50px;'> If you have already created a page and you love it so much that you want to use the same desgin on this page as well then select that page and click on Update Template button.</p>";
echo "<br><br><br> <form class='insertTemplateForm' >
		<label style='margin-right:7%;'> Select a Page to Insert as Template </label>
	 	<select class='selectPostToInsert' name='selectPostToInsert'>
";
foreach ($ULPB_PrevPosts as  $ulpost) {
	$currentPostId = $ulpost->ID;
	$currentPostName = get_the_title( $currentPostId);
	$currentPostLink = get_permalink($currentPostId);
	echo "<option value='$currentPostId' data-pagelink='$currentPostLink' > $currentPostName </option>";
}

echo "</select> 
<input type='hidden' value='$postId' name='pageToUpdate'>
<input type='hidden' value='$thisPostType' name='pageToUpdatePostType'>
</form>";

?>
<div class="insertTemplateFormSubmit btn-green large-btn">Update Template</div>
<br><br> <p class="upt_response"></p>
<div id="iframePreview" style="margin:3% 3% 3% 0%; background: #333; padding: 15px; min-height: 500px; min-width:100%;"></div>