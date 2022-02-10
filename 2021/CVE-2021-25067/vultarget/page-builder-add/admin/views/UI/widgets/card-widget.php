<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="width: 107%;">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#cardf1" class="pluginops-tab2_link">Icon & Text</a></li>
    <li><a href="#cardf2" class="pluginops-tab2_link">Styling</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="cardf1" class="tab active pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
		<br>
		<br>
		<label>Select Icon:  </label>
		<input data-placement="bottomRight" class="icp pbicp-auto pbSelectedCardIconValue" value="fa-archive" type="text" style="width: 280px !important; float:none;">
		<div class="CardPreview">
		<span class="input-group-addon pbSelectedCardIcon pbCardselIconStyles"></span>
		</div>
		<br style="clear: both;">
		<br><br><hr><br>
		<label>Card Headline : </label>
		<input type="text" class="pbCardTitle" style="width:300px; ">
		<br><br><br><br><hr><br>
		<label>Card Description : </label>
		<textarea class="pbCardDesc" style="width: 300px; height:150px; "></textarea>
		<br><br><hr><br>
	</div>
	<div id="cardf2" class="pluginops-tab2" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
		<h3>Icon</h3>
		<br>
		<label>Icon Size: </label>
		<input type="number" class="pbCardIconSize">
		<br><br><hr><br>
		<label> Icon Rotate: </label>
		<input type="number" class="pbCardIconRotation">
		<br><br><hr><br>
		<label> Icon Color :</label>
		<input type="text" class="color-picker_btn_two pbCardIconColor" id="pbCardIconColor">
		<br>
		<br>
		<hr>
		<br>
			<div>
                <h4>Headline Size 
                  <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                  <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                  <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </h4>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                    <label></label>
                  <input type="number" class="pbCardTitleSize"> px
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <label></label>
                  <input type="number" class="pbCardTitleSizeTablet"> px
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <label></label>
                  <input type="number" class="pbCardTitleSizeMobile"> px
                </div>
            </div>
		<br><br><hr><br>
		<label>Headline Color: </label>
		<input type="text" class="color-picker_btn_two pbCardTitleColor" id="pbCardTitleColor">
		<br><br><br><hr>
		<div>
                <h4>Description Size 
                  <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                  <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                  <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </h4>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                    <label></label>
                  <input type="number" class="pbCardDescSize"> px
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <label></label>
                  <input type="number" class="pbCardDescSizeTablet"> px
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <label></label>
                  <input type="number" class="pbCardDescSizeMobile"> px
                </div>
            </div>
		<br><br><hr><br>
		<label>Description Color: </label>
		<input type="text" class="color-picker_btn_two pbCardDescColor" id="pbCardDescColor">
		<br><br><hr><br>
	</div>
</div>
</div>

<style type="text/css">
	.pbicp-auto{
		width: 200px !important;
		font-size: 18px;
	}
	.popover-title > input{
		float: none;
		width:160px !important;
		margin: 0 auto !important;
		display: block !important;
	}
	.input-group-addon{
		font-size: 65px;
		margin-right : 20px;
		float: none;
	}
	.CardPreview{
		border-radius: 5px;
	    display: inline-block;
	    text-align: center;
	    margin-left: 10px;
	    width: 50px;
	}

	.CardPreview .input-group-addon {
		font-size: 30px !important;
	}
</style>