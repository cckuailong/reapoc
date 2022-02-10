<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="width: 99%;">
  <ul class="pluginops-tab2-links">
    <li style="margin: 0;" class="active"><a href="#pricing_cf1" class="pluginops-tab2_link">Pricing Content</a></li>
    <li style="margin: 0;"><a href="#pricing_cf2" class="pluginops-tab2_link">Button</a></li>
    <li style="margin: 0;" ><a href="#pricing_cf3" class="pluginops-tab2_link">Style Options</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="pricing_cf1" class="pluginops-tab2 active" style="width: 99%; min-height: 890px;">
        <br>
        <br>
        <label> Header Text</label>
        <input type="text" class="pbPricingHeaderText" style="width: 200px;">
        <br><br><br><br><hr><br><br>
        <p>Pricing Content</p>
        <br>

        <textarea  id="pbPricingContent"  class="pbPricingContent" data-optname='pbPricingContent' name="pbPricingContent" rows="25" cols="35" style="width: 97%;"></textarea>

	</div>
  <div id="pricing_cf2" class="pluginops-tab2" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
        
        <div id="pricingbtn-gen" class="pbp_form">
          <div class="pbp_form" style="margin:0 0 0 0; background: #fff; padding:20px 10px 20px 25px; width: 99%;">
            <br>
            <br>
            <label>Button Text :</label>
            <input type="text" class="pricingbtnText" style="width: 250px;" placeholder="Button Text">
            <br>
            <br>
            <br>
            <br>
            <br>
            <label>Button Link :</label>
            <input type="URL" class="pricingbtnLink" placeholder="Link URL">
            <br>
            <br>
            <hr>
            <br>
            <label>Open Link :</label>
            <select class="pricingbtnBlankAttr" id="pricingbtnBlankAttr">
              <option value="_self">Same Tab</option>
              <option value="_blank">New Tab</option>
            </select>
            <br>
            <br>
            <hr>
            <br>
            <div>
              <h4>Height 
                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
              </h4>
              <div class="responsiveOps responsiveOptionsContainterLarge">
                <label></label>
                <input type="number" class="pricingbtnHeight">px</div>
              <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                <label></label>
                <input type="number" class="pricingbtnHeightTablet">px</div>
              <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                <label></label>
                <input type="number" class="pricingbtnHeightMobile">px</div>
            </div>
            <br>
            <br>
            <hr>
            <br>
            <div>
              <h4>Width 
                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
              </h4>
              <div class="responsiveOps responsiveOptionsContainterLarge">
                <label></label>
                <input type="number" class="pricingbtnWidth" style="width:70px;">
                <select style="width:70px;" class="pricingbtnWidthUnit">
                  <option value='%'>Percent</option>
                  <option value="px">Pixel</option>
                </select>
              </div>
              <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                <label></label>
                <input type="number" class="pricingbtnWidthTablet" style="width:70px;">
                <select style="width:70px;" class="pricingbtnWidthUnitTablet">
                  <option value='%'>Percent</option>
                  <option value="px">Pixel</option>
                </select>
              </div>
              <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                <label></label>
                <input type="number" class="pricingbtnWidthMobile" style="width:70px;">
                <select style="width:70px;" class="pricingbtnWidthUnitMobile">
                  <option value='%'>Percent</option>
                  <option value="px">Pixel</option>
                </select>
              </div>
            </div>
            <br>
            <br>
            <hr>
            <br>
            <label>Background Color :</label>
            <input type="text" class="color-picker_btn_two pricingbtnBgColor" id="pricingbtnBgColor" data-alpha='true'>
            <br>
            <br>
            <hr>
            <br>
            <label>Hover Background Color :</label>
            <input type="text" class="color-picker_btn_two pricingbtnHoverBgColor" id="pricingbtnHoverBgColor" data-alpha='true'>
            <br>
            <br>
            <hr>
            <br>
            <label>Button Text Color :</label>
            <input type="text" class="color-picker_btn_two pricingbtnTextColor" id="pricingbtnTextColor">
            <br>
            <br>
            <hr>
            <br>
            <label>Hover Text Color :</label>
            <input type="text" class="color-picker_btn_two pricingbtnHoverTextColor" data-alpha='true'>
            <br>
            <br>
            <hr>
            <br>
            <div>
              <h4>Font size 
                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
              </h4>
              <div class="responsiveOps responsiveOptionsContainterLarge">
                <label></label>
                <input type="number" class="pricingbtnFontSize">px</div>
              <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                <label></label>
                <input type="number" class="pricingbtnFontSizeTablet">px</div>
              <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                <label></label>
                <input type="number" class="pricingbtnFontSizeMobile">px</div>
            </div>
            <br>
            <br>
            <hr>
            <br>
            <label>Border Width:</label>
            <input type="number" class="pricingbtnBorderWidth">
            <br>
            <br>
            <hr>
            <br>
            <label>Border Color:</label>
            <input type="text" class="color-picker_btn_two pricingbtnBorderColor" id="pricingbtnBorderColor">
            <br>
            <br>
            <hr>
            <br>
            <label>Corner Radius:</label>
            <input type="number" class="pricingbtnBorderRadius" max="100" min="0">
            <br>
            <br>
            <hr>
            <br>
            <div>
              <h4>Button Alignment 
                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
              </h4>
              <div class="responsiveOps responsiveOptionsContainterLarge">
                <label></label>
                <select class="pricingbtnButtonAlignment">
                  <option value="default">Default</option>
                  <option value="left">Left</option>
                  <option value="right">Right</option>
                  <option value="center">Center</option>
                </select>
              </div>
              <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                <label></label>
                <select class="pricingbtnButtonAlignmentTablet">
                  <option value="default">Default</option>
                  <option value="left">Left</option>
                  <option value="right">Right</option>
                  <option value="center">Center</option>
                </select>
              </div>
              <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                <label></label>
                <select class="pricingbtnButtonAlignmentMobile">
                  <option value="default">Default</option>
                  <option value="left">Left</option>
                  <option value="right">Right</option>
                  <option value="center">Center</option>
                </select>
              </div>
            </div>
            <br>
            <br>
            <hr>
            <br>
            <label>Button Font :</label>
            <input class="pricingbtnButtonFontFamily gFontSelectorulpb" id="pricingbtnButtonFontFamily">
            <br>
            <br>
            <hr>
            <br>
            <br>
            <br>
            <label>Select Icon</label>
            <input data-placement="bottomRight" class="icp pbicp-auto pricingbtnSelectedIconpbicp-auto" value="" type="text" /> <span class="input-group-addon pricingbtnSelectedIcon" style="font-size: 16px;"></span>
            <br>
            <br>
            <hr>
            <br>
            <label>Icon Position</label>
            <select class="pricingbtnIconPosition">
              <option value="before">Before Text</option>
              <option value="after">After Text</option>
            </select>
            <br>
            <br>
            <hr>
            <br>
            <label>Icon Gap</label>
            <input type="number" class="pricingbtnIconGap">px
            <br>
            <br>
            <hr>
            <br>
            <label>Icon Hover Animation</label>
            <select class="pricingbtnIconAnimation">
              <option value="">None</option>
              <optgroup label="Attention Seekers">
                <option value="bounce">bounce</option>
                <option value="flash">flash</option>
                <option value="pulse">pulse</option>
                <option value="rubberBand">rubberBand</option>
                <option value="shake">shake</option>
                <option value="swing">swing</option>
                <option value="tada">tada</option>
                <option value="wobble">wobble</option>
                <option value="jello">jello</option>
                <option value="flip">flip</option>
              </optgroup>
            </select>
            <br>
            <br>
          </div>
        </div>
        <br>
        <br>
  </div>
	<div id="pricing_cf3" class="pluginops-tab2" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
        <label>Header Text Color</label>
        <input type="text" class="color-picker_btn_two pbPricingHeaderTextColor" id="pbPricingHeaderTextColor" >
        <br><br><hr><br>
        <label>Header Background Color</label>
        <input type="text" class="color-picker_btn_two pbPricingHeaderBgColor" id="pbPricingHeaderBgColor" >
        <br><br><hr><br>
        <label> Header Text Size</label>
        <input type="number" class="pbPricingHeaderTextSize">
        <br><br><hr><br>
        <label> Container Border Width</label>
        <input type="number" class="pbPricingBorderWidth">
        <br><br><hr><br>
        <label> Container Border Color</label>
        <input type="text" class="color-picker_btn_two pbPricingBorderColor" id="pbPricingBorderColor" value=''>
        <br><br><hr><br>
        <label> Button Section Background Color</label>
        <input type="text" class="color-picker_btn_two pbPricingButtonSectionBgColor" id="pbPricingButtonSectionBgColor" value=''>
        <br><br><hr><br><br>

	</div>
</div>
</div>