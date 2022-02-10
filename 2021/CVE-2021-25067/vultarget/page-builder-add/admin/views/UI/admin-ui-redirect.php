<?php  if ( ! defined( 'ABSPATH' ) ) exit; 
$redirectLink = admin_url()."edit.php?post_type=ulpb_post&page=page-builder-new-landing-page&thisPostID=".$post->ID;
?>

<script type="text/javascript">
	//location.href = '<?php echo "$redirectLink"; ?>';
</script>

<style type="text/css">
	.pb_fullScreenEditorButton {
		padding: 20px;
		color: rgb(255, 255, 255);
		background-color: rgb(2, 120, 177);
		margin: 100px auto 0px;
		text-align: center;
		max-width: 475px;
		font-size: 22px;
		cursor: pointer;
		border-bottom: 8px solid rgb(0, 79, 117);
		border-radius:10px;
		display: block !important;
		line-height: 1.4em;
	}
	.pb_fullScreenEditorButton:hover{
		background-color: #038ed2;
		border-bottom: 8px solid #016b9e;
		-webkit-transition: all .5s ease;
		   -moz-transition: all .5s ease;
		    -ms-transition: all .5s ease;
		     -o-transition: all .5s ease;
		        transition: all .5s ease;
	}

	#title, #title-prompt-text, .wp-heading-inline{
		display: none !important;
	}
</style>

<a href='<?php echo "$redirectLink"; ?>' style=" text-decoration: none; "> <div class="pb_fullScreenEditorButton" style=""> Click To Open Landing Page Editing Panel </div> </a>
<br><br><br><hr style="border-color: #3333338a; border-width:2px; border-radius: 50px; max-width: 50%;"><br>