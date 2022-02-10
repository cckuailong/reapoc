<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="width: 107%;">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#ps1" class="pluginops-tab2_link">Slider</a></li>
    <li><a href="#ps2" class="pluginops-tab2_link">Layout</a></li>
    <li><a href="#ps3" class="pluginops-tab2_link">Design</a></li>
    <li><a href="#ps4" class="pluginops-tab2_link">Content</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="ps1" class="pluginops-tab2 active">
		<div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
		<br>
        <label>AutoPlay</label>
		<select class="psAutoplay">
			<option value="true">Yes</option>
			<option value="false">No</option>
		</select>
		<br><br><hr><br>
		<label>Delay (Seconds) </label>
        <input type="number" class="psSlideDelay">
        <br><br><hr><br>
        <label>Loop </label>
        <select class="psSlideLoop">
			<option value="true">Yes</option>
			<option value="false">No</option>
		</select>
        <br><br><hr><br>
        <label>Transition </label>
        <select class="psSlideTransition">
			<option value="backSlide">Slide</option>
			<option value="fade">Fade</option>
		</select>
        <br><br><hr><br>
        <label>Number of Posts </label>
        <input type="number" class="psPostsNumber">
        <br><br><hr><br>
        <h3>Slider Controls</h3>
        <hr>
        <label>Show Dots </label>
        <select class="psDots">
			<option value="true">Show</option>
			<option value="false">Hide</option>
		</select>
        <br><br><hr><br>
        <label>Show Arrows </label>
        <select class="psArrows">
			<option value="true">Show</option>
			<option value="false">Hide</option>
		</select>
        <br><br><hr><br>
       </div>
	</div>
	<div id="ps2" class="pluginops-tab2">
		<div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
		<label>Featured Image</label>
		<select class="psFtrImage">
			<option value="initial">Show</option>
			<option value="none">Hide</option>
		</select>
		<br><br><hr><br>
		<label>Image Size</label>
		<select class="psFtrImageSize">
			<option value="thumbnail">Small</option>
			<option value="medium">Medium</option>
			<option value="large">Large</option>
			<option value="original">Original</option>
		</select>
		<br><br><hr><br>
		<h3>Content</h3>
		<hr>
		<br>
		<label>Post Excerpt</label>
		<select class="psExcerpt">
			<option value="initial">Show</option>
			<option value="none">Hide</option>
		</select>
		<br><br><hr><br>
		<label>Read more Link</label>
		<select class="psReadMore">
			<option value="initial">Show</option>
			<option value="none">Hide</option>
		</select>
		<br><br><hr><br>
		<label>More link Text </label>
        <input type="text" class="psMoreLinkText">
        <br><br><hr><br>
    	</div>
	</div>
	<div id="ps3" class="pluginops-tab2">
		<div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
		<label>Heading Size (px)</label>
        <input type="number" class="psHeadingSize">
        <br><br><hr><br>
        <label>Text Alignment</label>
		<select class="psTextAlignment">
			<option value="left">Left</option>
			<option value="center">Center</option>
			<option value="right">Right</option>
		</select>
		<br><br><hr><br>
		<h3>Colors</h3>
		<hr>
		<br>
		<label>Background Color :</label>
        <input type="text" class="color-picker_btn_two psBgColor" data-alpha='true' id="psBgColor">
        <br><br><hr><br>
        <label>Text Color :</label>
        <input type="text" class="color-picker_btn_two psTxtColor" id="psTxtColor">
        <br><br><hr><br>
        <label>Heading Color :</label>
        <input type="text" class="color-picker_btn_two psHeadingTxtColor" id="psHeadingTxtColor">
        <br><br><hr><br>
    	</div>
	</div>
	<div id="ps4" class="pluginops-tab2">
		<div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
		<label>Post Type</label>
        <select class="psPostType">
			<option value="post">Posts</option>
			<option value="page">Pages</option>
		</select>
		<br><br><hr><br>
		<label>Order By</label>
		<select class="psPostsOrderBy">
			<option value="date">Date</option>
			<option value="rand">Random</option>
			<option value="id">ID</option>
			<option value="name">Name</option>
			<option value="comment_count">Comment Count</option>
		</select>
		<br><br><hr><br>
		<label>Order</label>
		<select class="psPostsOrder">
			<option value="Descending">Descending</option>
			<option value="Ascending">Ascending</option>
		</select>
		<br><br><hr><br>
		<h3>Filter</h3>
		<hr>
		<br>
		<label>Filter By</label>
		<select class="psPostsFilterBy">
			<option value="none">None</option>
			<option value="true">Categories</option>
			<option value="false">Author Name</option>
		</select>
		<br><br><hr><br>
		<label>Filter Value :</label>
        <input type="text" class="psFilterValue">
        <br><br><hr><br>
    	</div>
	</div>
</div>
</div>
