<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="width: 107%;">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#progressBartab1" class="pluginops-tab2_link">Bar Options</a></li>
    <li><a href="#progressBartab2" class="pluginops-tab2_link">Style</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="progressBartab1" class="pluginops-tab2 active" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
	    <br>
		<br>
        <label>Title : </label>
        <input type="text" class="pbProgressBarTitle" id="pbProgressBarTitle">
        <br><br><hr><br>
        <label>Percentage : </label>
        <input type="number" class="pbProgressBarPrecentage" id="pbProgressBarPrecentage">
        <br><br><hr><br>
        <label>Bar Text : </label>
        <input type="text" class="pbProgressBarText" id="pbProgressBarText">
        <br><br><hr><br>
        <label>Display Precentage :</label>
        <select class="pbProgressBarDisplayPrecentage">
            <option value="%">Show</option>
            <option value="">Hide</option>
        </select>
        <br><br><hr><br><br>
	</div>
	<div id="progressBartab2" class="pluginops-tab2" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
		<br>
        <h4>Colors</h4>
        <label>Title Color :</label>
		<input type="text" class="color-picker_btn_two pbProgressBarTitleColor" id="pbProgressBarTitleColor" value=''>
		<br><br><hr><br>
		<label>Bar Text Color :</label>
		<input type="text" class="color-picker_btn_two pbProgressBarTextColor" id="pbProgressBarTextColor" value=''>
		<br><br><hr><br>
		<label>Bar Color : </label>
        <input type="text" class="color-picker_btn_two pbProgressBarColor" id="pbProgressBarColor" value=''>
        <br><br><hr><br>
		<label>Bar Background Color : </label>
        <input type="text" class="color-picker_btn_two pbProgressBarBgColor" id="pbProgressBarBgColor" value=''>
        <br>
        <hr>
        <br>
        <h4> Size </h4>
        <br>
        <label>Title Font Size : </label>
        <input type="number" class="pbProgressBarTitleSize">
        <br><br><hr><br>
        <label>Bar Height : </label>
        <input type="number" class="pbProgressBarHeight">
        <br><br><hr><br>
        <label>Bar Font Size : </label>
        <input type="number" class="pbProgressBarTextSize">
        <br><br><hr><br>
        <h4> Font Family </h4>
        <br>
        <br>
        <label>Select :</label>
        <input class="pbProgressBarTextFontFamily gFontSelectorulpb" id="pbProgressBarTextFontFamily">
        <br><br><br><hr><br><br><br><br><br><br><br>
	</div>
</div>
</div>