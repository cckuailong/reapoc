<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="min-width: 380px;">
  <ul class="pluginops-tab2-links" >
    <li class="active" style="margin: 0;"><a href="#ims_cf1" class="pluginops-tab2_link">Images</a></li>
    <li style="margin: 0;"><a href="#ims_cf3" class="pluginops-tab2_link">Content Design</a></li>
    <li style="margin: 0;"><a href="#ims_cf2" class="pluginops-tab2_link">Slider Settings</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="ims_cf1" class="pluginops-tab2 active">
        <div class="pbp_form" style="background: #fff; padding:20px 0; width: 99%;" >
          <div class="btn btn-blue" id="addNewList" > <span class="dashicons dashicons-plus-alt"></span> Add Slide </div>
          <br>
          <br>
          <ul class="sortableAccordionWidget  sliderImageSlidesContainer">
          </ul>
        </div>
	</div>
	<div id="ims_cf2" class="pluginops-tab2" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
        <div>
            <h4>Slider Height
                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
            </h4>
            <div class="responsiveOps responsiveOptionsContainterLarge">
                <label></label>
                <input type="number" class="pbSliderHeight" data-optname='pbSliderHeight'>
                <select class="pbSliderHeightUnit" style="width:50px;" data-optname='pbSliderHeightUnit'>
                    <option value="px">px</option>
                    <option value="%">%</option>
                    <option value="vh">vh</option>
                </select>
            </div>
            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                <label></label>
                <input type="number" class="pbSliderHeightTablet" data-optname='pbSliderHeightTablet'>
                <select class="pbSliderHeightUnitTablet"  style="width:50px;" data-optname='pbSliderHeightUnitTablet'>
                    <option value="px">px</option>
                    <option value="%">%</option>
                    <option value="vh">vh</option>
                </select>
            </div>
            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                <label></label>
                <input type="number" class="pbSliderHeightMobile" data-optname='pbSliderHeightMobile'>
                <select class="pbSliderHeightUnitMobile"  style="width:50px;" data-optname='pbSliderHeightUnitMobile'>
                    <option value="px">px</option>
                    <option value="%">%</option>
                    <option value="vh">vh</option>
                </select>
            </div>
        </div>
        <br><br><hr><br>
        <label>AutoPlay</label>
        <select class="pbSliderAuto" data-optname='pbSliderAuto'>
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Slider Delay </label>
		<input type="number" class="pbSliderDelay" id="pbSliderDelay" value='' data-optname='pbSliderDelay'>
        <span> (In milliseconds) </span>
        <br><br><hr><br>
        <label>Bullet Navigation </label>
        <select class="pbSliderPager" data-optname='pbSliderPager'>
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Navigation Buttons </label>
        <select class="pbSliderNav" data-optname='pbSliderNav'>
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Random Order </label>
        <select class="pbSliderRandom" data-optname='pbSliderRandom'>
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Hover Pause </label>
        <select class="pbSliderPause" data-optname='pbSliderPause'>
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br><br><br>
	</div>
    <div id="ims_cf3" class="pluginops-tab2"> 
        <div class="pbp_form" style="background: #fff; padding:20px 0 20px 5px; width: 100%;" >
            <div class="PB_accordion">
                
                <h3 class="handleHeader widgetOpsAccordionHandle">Content Container </h3>
                <div class="accordContentHolder" style="background: #fff;">
                    <label>Content Background :</label>
                    <input type="text" class="color-picker_btn_two pbSliderContentBgColor" id="pbSliderContentBgColor" data-alpha='true' value='' data-optname='pbSliderContentBgColor' >
                    <br><br><hr><br>
                    <div>
                        <label>Content Width 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </label>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <input type="number" class="slideContentWidth" data-optname='slideContentWidth' style="width:60px;">
                            <select class="slideContentWUnit" style="width:50px;" data-optname="slideContentWUnit" >
                                <option value="px">px</option>
                                <option value="%">%</option>
                                <option value="vh">vw</option>
                            </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <input type="number" class="slideContentWidthT" data-optname='slideContentWidthT' style="width:60px;">
                            <select class="slideContentWUnitT" style="width:50px;" data-optname="slideContentWUnitT" >
                                <option value="px">px</option>
                                <option value="%">%</option>
                                <option value="vh">vw</option>
                            </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <input type="number" class="slideContentWidthM" data-optname='slideContentWidthM' style="width:60px;">
                            <select class="slideContentWUnitM" style="width:50px;" data-optname="slideContentWUnitM" >
                                <option value="px">px</option>
                                <option value="%">%</option>
                                <option value="vh">vw</option>
                            </select>
                        </div>
                    </div>
                    <br><br><hr><br>
                    <div>
                        <label>Horizontal Alignment 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </label>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <select class="slideContentAlignH" data-optname="slideContentAlignH" >
                                <option value="center">Center</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <select class="slideContentAlignHT" data-optname="slideContentAlignHT" >
                                <option value="center">Center</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <select class="slideContentAlignHM" data-optname="slideContentAlignHM" >
                                <option value="center">Center</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                    <br><br><hr><br>
                    <div>
                        <label>Vertical Alignment 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </label>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <select class="slideContentAlignV" data-optname="slideContentAlignV" >
                                <option value="middle">Middle</option>
                                <option value="top">Top</option>
                                <option value="bottom">Bottom</option>
                            </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <select class="slideContentAlignVT" data-optname="slideContentAlignVT" >
                                <option value="middle">Middle</option>
                                <option value="top">Top</option>
                                <option value="bottom">Bottom</option>
                            </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <select class="slideContentAlignVM" data-optname="slideContentAlignVM" >
                                <option value="middle">Middle</option>
                                <option value="top">Top</option>
                                <option value="bottom">Bottom</option>
                            </select>
                        </div>
                    </div>
                    <br><br><hr><br>
                    <div>
                        <label>Text Alignment 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </label>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <select class="slideContentAlign" data-optname="slideContentAlign" >
                                <option value="center">Center</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                                <option value="justify">Justified</option>
                            </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <select class="slideContentAlignT" data-optname="slideContentAlignT" >
                                <option value="center">Center</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                                <option value="justify">Justified</option>
                            </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <select class="slideContentAlignM" data-optname="slideContentAlignM" >
                                <option value="center">Center</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                                <option value="justify">Justified</option>
                            </select>
                        </div>
                    </div>
                </div>

                <h3 class="handleHeader widgetOpsAccordionHandle">Headline </h3>
                <div class="accordContentHolder" style="background: #fff;">
                    <br>
                    <label>Text Color :</label>
                    <input type="text" class="color-picker_btn_two slideHeadingColor" id="slideHeadingColor" data-alpha='true' value='#333333' data-optname='slideHeadingStyles.slideHeadingColor' >
                    <br><br><hr><br>
                    <div>
                        <h4>Text Size 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideHeadingSize" data-optname='slideHeadingStyles.slideHeadingSize'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideHeadingSizeTablet" data-optname='slideHeadingStyles.slideHeadingSizeTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideHeadingSizeMobile" data-optname='slideHeadingStyles.slideHeadingSizeMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <div>
                        <h4>Letter Spacing 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideHeadingLetterSpacing" data-optname='slideHeadingStyles.slideHeadingLetterSpacing'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideHeadingLetterSpacingTablet" data-optname='slideHeadingStyles.slideHeadingLetterSpacingTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideHeadingLetterSpacingMobile" data-optname='slideHeadingStyles.slideHeadingLetterSpacingMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <div>
                        <h4>Line Height
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideHeadingLineHeight" data-optname='slideHeadingStyles.slideHeadingLineHeight'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideHeadingLineHeightTablet" data-optname='slideHeadingStyles.slideHeadingLineHeightTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideHeadingLineHeightMobile" data-optname='slideHeadingStyles.slideHeadingLineHeightMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <label style="width: 160px;">Font family :</label>
                    <input class="slideHeadingFontFamily gFontSelectorulpb" id="slideHeadingFontFamily" data-optname='slideHeadingStyles.slideHeadingFontFamily'>
                    <br><br><hr><br>
                    <label for="slideHeadingBold" class="checkboxBtnLabel"> Bold </label>
                    <input type="checkbox" id="slideHeadingBold" class="slideHeadingBold popb_checkbox" data-optname='slideHeadingStyles.slideHeadingBold'>
                    <label for="slideHeadingItalic" class="checkboxBtnLabel"> Italic </label>
                    <input type="checkbox" id="slideHeadingItalic" class="slideHeadingItalic popb_checkbox" data-optname='slideHeadingStyles.slideHeadingItalic'>
                    <label for="slideHeadingUnderlined" class="checkboxBtnLabel"> Underlined </label>
                    <input type="checkbox" id="slideHeadingUnderlined" class="slideHeadingUnderlined popb_checkbox" data-optname='slideHeadingStyles.slideHeadingUnderlined'>
                </div>
                <h3 class="handleHeader widgetOpsAccordionHandle">Description </h3>
                <div class="accordContentHolder" style="background: #fff;">
                    <br>
                    <label>Text Color :</label>
                    <input type="text" class="color-picker_btn_two slideDescColor" id="slideDescColor" data-alpha='true' value='#333333' data-optname='slideDescStyles.slideDescColor'>
                    <br><br><hr><br>
                    <div>
                        <h4>Text Size 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideDescSize" data-optname='slideDescStyles.slideDescSize'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideDescSizeTablet" data-optname='slideDescStyles.slideDescSizeTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideDescSizeMobile" data-optname='slideDescStyles.slideDescSizeMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <div>
                        <h4>Letter Spacing
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideDescLetterSpacing" data-optname='slideDescStyles.slideDescLetterSpacing'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideDescLetterSpacingTablet" data-optname='slideDescStyles.slideDescLetterSpacingTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideDescLetterSpacingMobile" data-optname='slideDescStyles.slideDescLetterSpacingMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <div>
                        <h4>Line Height 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideDescLineHeight" data-optname='slideDescStyles.slideDescLineHeight'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideDescLineHeightTablet" data-optname='slideDescStyles.slideDescLineHeightTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideDescLineHeightMobile" data-optname='slideDescStyles.slideDescLineHeightMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <label style="width: 160px;">Font family :</label>
                    <input class="slideDescFontFamily gFontSelectorulpb" id="slideDescFontFamily" data-optname='slideDescStyles.slideDescFontFamily'>
                    <br><br><hr><br>
                    <label for="slideDescBold" class="checkboxBtnLabel"> Bold </label>
                    <input type="checkbox" id="slideDescBold" class="slideDescBold popb_checkbox" data-optname='slideDescStyles.slideDescBold'>
                    <label for="slideDescItalic" class="checkboxBtnLabel"> Italic </label>
                    <input type="checkbox" id="slideDescItalic" class="slideDescItalic popb_checkbox" data-optname='slideDescStyles.slideDescItalic'>
                    <label for="slideDescUnderlined" class="checkboxBtnLabel"> Underlined </label>
                    <input type="checkbox" id="slideDescUnderlined" class="slideDescUnderlined popb_checkbox" data-optname='slideDescStyles.slideDescUnderlined'>
                </div>
                <h3 class="handleHeader widgetOpsAccordionHandle">Button </h3>
                <div class="accordContentHolder" style="background: #fff;">
                    <br>
                    <div>
                        <h4>Button Height 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideButtonBtnHeight" data-optname='slideButtonStyles.slideButtonBtnHeight'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideButtonBtnHeightTablet" data-optname='slideButtonStyles.slideButtonBtnHeightTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideButtonBtnHeightMobile" data-optname='slideButtonStyles.slideButtonBtnHeightMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <div>
                        <h4>Button Width
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideButtonBtnWidth" data-optname='slideButtonStyles.slideButtonBtnWidth'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideButtonBtnWidthTablet" data-optname='slideButtonStyles.slideButtonBtnWidthTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideButtonBtnWidthMobile" data-optname='slideButtonStyles.slideButtonBtnWidthMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <label>Background Color :</label>
                    <input type="text" class="color-picker_btn_two slideButtonBtnBgColor" id="slideButtonBtnBgColor" value='#333333' data-alpha='true' data-optname='slideButtonStyles.slideButtonBtnBgColor'>
                    <br><br><hr><br>
                    <label>Hover BG Color :</label>
                    <input type="text" class="color-picker_btn_two slideButtonBtnHoverBgColor" id="slideButtonBtnHoverBgColor" data-alpha='true' value='#333333'  data-optname='slideButtonStyles.slideButtonBtnHoverBgColor'>
                    <br><br><hr><br>
                    <label>Hover Text Color :</label>
                    <input type="text" class="color-picker_btn_two slideButtonBtnHoverTextColor" id="slideButtonBtnHoverTextColor" data-alpha='true' value='#333333' data-optname='slideButtonStyles.slideButtonBtnHoverTextColor'>
                    <br><br><hr><br>
                    <label>Text Color :</label>
                    <input type="text" class="color-picker_btn_two slideButtonBtnColor" id="slideButtonBtnColor" data-alpha='true' data-optname='slideButtonStyles.slideButtonBtnColor'>
                    <br><br><hr><br>
                    <div>
                        <h4>Text size
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideButtonBtnFontSize" data-optname='slideButtonStyles.slideButtonBtnFontSize'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideButtonBtnFontSizeTablet" data-optname='slideButtonStyles.slideButtonBtnFontSizeTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideButtonBtnFontSizeMobile" data-optname='slideButtonStyles.slideButtonBtnFontSizeMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <label>Font family :</label>
                    <input class="slideButtonBtnFontFamily gFontSelectorulpb" id="slideButtonBtnFontFamily" data-optname='slideButtonStyles.slideButtonBtnFontFamily'>
                    <br><br><hr><br>
                    <div>
                        <h4>Letter Spacing
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="slideButtonBtnFontLetterSpacing" data-optname='slideButtonStyles.slideButtonBtnFontLetterSpacing'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="slideButtonBtnFontLetterSpacingTablet" data-optname='slideButtonStyles.slideButtonBtnFontLetterSpacingTablet'>px
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="slideButtonBtnFontLetterSpacingMobile" data-optname='slideButtonStyles.slideButtonBtnFontLetterSpacingMobile'>px
                        </div>
                    </div>
                    <br><br><hr><br>
                    <label>Border Width: </label>
                    <input type="number" class="slideButtonBtnBorderWidth" data-optname='slideButtonStyles.slideButtonBtnBorderWidth'>
                    <br><br><hr><br>
                    <label>Border Color: </label>
                    <input type="text" class="color-picker_btn_two slideButtonBtnBorderColor" id="slideButtonBtnBorderColor" value='#ffffff' data-optname='slideButtonStyles.slideButtonBtnBorderColor'>
                    <br><br><hr><br>
                    <label>Corner Radius: </label>
                    <input type="number" class="slideButtonBtnBorderRadius" max="100" min="0" data-optname='slideButtonStyles.slideButtonBtnBorderRadius'>
                    <br><br><hr><br>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    (function($){

        var slideCountA = 80;
        jQuery('#addNewList').on('click',function(){

        var index = $(".formFieldItemsContainer li").length;
        jQuery('.sliderImageSlidesContainer').append(
            '<li> '+
                '<h3 class="handleHeader widgetOpsAccordionHandle">Slide <span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> <span class="dashicons dashicons-admin-page slideDuplicateButton" style="float: right;" title="Duplicate"></span>  </h3>'+
                '<div class="accordContentHolder"> <br><br> '+
                    '<label>Slide Image :</label> '+
                    '<input id="image_location'+slideCountA+'" type="text" class="slideImgURL upload_image_button'+slideCountA+'" name="lpp_add_img_'+slideCountA+'" value="" placeholder="Insert Image URL here" style="width:40%;" data-optname="pbSliderContent.'+index+'.imageSlideUrl"> '+
                    '<label></label> <input id="image_location'+slideCountA+'" type="button" class="upload_bg_btn_imageSlider" data-id="'+slideCountA+'" value="Upload" /> <br> <br> <br> <br> <br> <hr> <br> <br> '+
                    '<input type="hidden" value="" class="isalt altTextField" data-optname="pbSliderContent.'+index+'.isalt">'+
                    '<input type="hidden" value="" class="istitle titleTextField" data-optname="pbSliderContent.'+index+'.istitle"> <br>'+

                    '<label>Slide Heading :</label>'+
                    '<input type="text" class="imageSlideHeading" value="" data-optname="pbSliderContent.'+index+'.imageSlideHeading" > <br> <br> <br>'+
                    '<label>Slide Description :</label>'+
                    '<textarea class="imageSlideDesc" value="" data-optname="pbSliderContent.'+index+'.imageSlideDesc" ></textarea> <br> <br> <br>'+
                    '<label>Slide Button Text :</label>'+
                    '<input type="text" class="imageSlideButtonText" value="" data-optname="pbSliderContent.'+index+'.imageSlideButtonText" > <br> <br> <br> '+
                    '<label>Slide Button URL :</label>'+
                    '<input type="url" class="imageSlideButtonURL" value="" data-optname="pbSliderContent.'+index+'.imageSlideButtonURL" > <br> <br> <br> '+
                '</div> '+
            '</li>'
        );

        pageBuilderApp.changedOpType = 'specific';
        pageBuilderApp.changedOpName = 'slideListEdit';
        
        var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
        jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

        ColcurrentEditableRowID = jQuery('.ColcurrentEditableRowID').val();
        currentEditableColId = jQuery('.currentEditableColId').val();
        jQuery('section[rowid="'+ColcurrentEditableRowID+'"]').children('.ulpb_column_controls'+currentEditableColId).children('#editColumnSaveWidget').trigger('click');

        slideCountA++;

    }); // CLICK function ends here.


    })(jQuery);
</script>

<style type="text/css">
    
</style>