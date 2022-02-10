<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="width: 100%;">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#TextWidgetTab1" class="pluginops-tab2_link">Text</a></li>
    <li><a href="#TextWidgetTab2" class="pluginops-tab2_link">Style</a></li>
  </ul>
<form id="pbtextwidgetops">
<div class="pluginops-tab2-content" style="box-shadow:none;">
    <div id="TextWidgetTab1" class="pluginops-tab2 active">
        <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
            <label>Text </label>
            <div style="width: 90%; min-height:205px;">
                <textarea style="width:100%; height: 205px;" id="widgetTextContent" class="widgetTextContent" data-optname="widgetTextContent"  ></textarea>
            </div>
            <br><br><hr><br>
            <label>HTML Tag</label>
            <select class="widgetTextTag" data-optname="widgetTextTag" >
                <option value="p">p</option>
                <option value="h1">H1</option>
                <option value="h2">H2</option>
                <option value="h3">H3</option>
                <option value="h4">H4</option>
                <option value="h5">H5</option>
                <option value="h6">H6</option>
                <option value="a">Link</option>
                <option value="div">div</option>
                <option value="span">span</option>
            </select>
            <br><br><hr><br>
            <div>
                <h4>Text Alignment
                  <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                  <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                  <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </h4>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                  <label></label>
                  <select class="widgetTextAlignment" data-optname="widgetTextAlignment" >
                    <option value="left">Left</option>
                    <option value="center">Center</option>
                    <option value="right">Right</option>
                    <option value="justified">Justified</option>
                  </select>
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <label></label>
                    <select class="widgetTextAlignmentTablet" data-optname="widgetTextAlignmentTablet" >
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                        <option value="justified">Justified</option>
                    </select>
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <label></label>
                    <select class="widgetTextAlignmentMobile" data-optname="widgetTextAlignmentMobile" >
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                        <option value="justified">Justified</option>
                    </select>
                </div>
            </div>
            <br><br><hr><br>
            <div class="linkOpsDiv" style="display: none;">
                <label>Link URL : </label>
                <input type="url" class="wtextLink" data-optname="wtextLink" style="width:95%;">
            </div>
            <br><br><hr><br>
       </div>
    </div>
    <div id="TextWidgetTab2" class="pluginops-tab2">
        <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
            <label>Text Color :</label>
            <input type="text" class="color-picker_btn_two widgetTextColor" id="widgetTextColor" value='#333333' data-optname="widgetTextColor">
            <br><br><br><hr>
            <div>
                <h4>Text Size 
                  <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                  <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                  <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </h4>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                    <label></label>
                  <input type="number" class="widgetTextSize" data-optname="widgetTextSize"> px
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <label></label>
                  <input type="number" class="widgetTextSizeTablet" data-optname="widgetTextSizeTablet"> px
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <label></label>
                  <input type="number" class="widgetTextSizeMobile" data-optname="widgetTextSizeMobile"> px
                </div>
            </div>
            <br><br><hr>
            <div>
                <h4>Text Line Height
                  <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                  <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                  <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </h4>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                  <label></label>
                  <input type="number" class="widgetTextLineHeight" data-optname="widgetTextLineHeight"> em
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <label></label>
                  <input type="number" class="widgetTextLineHeightTablet" data-optname="widgetTextLineHeightTablet"> em
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <label></label>
                  <input type="number" class="widgetTextLineHeightMobile" data-optname="widgetTextLineHeightMobile"> em
                </div>
            </div>
            <br><br><hr>
            <div>
                <h4>Text Spacing
                  <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                  <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                  <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </h4>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                  <label></label>
                  <input type="number" class="widgetTextSpacing" data-optname="widgetTextSpacing" > px
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <label></label>
                  <input type="number" class="widgetTextSpacingTablet" data-optname="widgetTextSpacingTablet" > px
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <label></label>
                  <input type="number" class="widgetTextSpacingMobile" data-optname="widgetTextSpacingMobile" > px
                </div>
            </div>
            <br><br><hr><br>
            <label>Font Weight</label>
            <select class="widgetTextWeight" data-optname="widgetTextWeight" >
                <option value="">Default</option>
                <option value="normal">Normal</option>
                <option value="bold">Bold</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="400">400</option>
                <option value="500">500</option>
                <option value="600">600</option>
                <option value="700">700</option>
                <option value="800">800</option>
                <option value="900">900</option>
            </select>
            <br><br><br><hr><br>
            <label>Text Transform</label>
            <select class="widgetTextTransform" data-optname="widgetTextTransform" >
                <option value="none">Default</option>
                <option value="uppercase">Uppercase</option>
                <option value="lowercase">Lowercase</option>
                <option value="capitalize">Capitalize</option>
            </select>
            <br><br><br><hr><br>
            <label>Font family :</label>
            <input class="widgetTextFamily gFontSelectorulpb" id="widgetTextFamily" data-optname="widgetTextFamily">
            <br><br><br><hr><br><br><br>
            <label for="widgetTextBold" class="checkboxBtnLabel"> Bold </label>
            <input type="checkbox" id="widgetTextBold" class="widgetTextBold popb_checkbox" data-optname="widgetTextBold">
            <label for="widgetTextItalic" class="checkboxBtnLabel"> Italic </label>
            <input type="checkbox" id="widgetTextItalic" class="widgetTextItalic popb_checkbox" data-optname="widgetTextItalic">
            <label for="widgetTextUnderlined" class="checkboxBtnLabel"> Underlined </label>
            <input type="checkbox" id="widgetTextUnderlined" class="widgetTextUnderlined popb_checkbox" data-optname="widgetTextUnderlined">
            <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        </div>
    </div>
</div>
</form>
</div>
