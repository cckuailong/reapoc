<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="lpp_modal_row edit_row" style="background: #fff;">
  <div class="lpp_modal_wrapper_row">
  <div class="edit_options_left_row">
      <input type="hidden" class="currentEditingRow" value='' >
      <div class="pluginops-tabs2">
        <ul class="pluginops-tab2-links">
          <li style="margin: 0;"  class="active"><a style="font-size:12px; padding: 10px; text-align: center;" href="#tabRowOptions" class="pluginops-tab2_link"> <i class="fa fa-gears" style="font-size: 20px;"></i> <br> Row Options</a></li>
          <li style="margin: 0;"><a style="font-size:12px; padding: 10px; text-align: center;" href="#tabRowVideo" class="pluginops-tab2_link"> <i class="fab fa-youtube" style="font-size: 20px;"></i> <br> Background Video</a></li>
          <li style="margin: 0;"><a style="font-size:12px; padding: 10px; text-align: center;" href="#tabCustomCss" class="pluginops-tab2_link"> <i class="fa fa-code" style="font-size: 20px;"></i> <br> Custom CSS</a></li>
        </ul>
        <div class="pluginops-tab2-content">
          <div id="tabRowOptions" class="pluginops-tab2 active" style="min-height:400px;">
            <form id="rowOpsForm">
            <div class="pbp_form" style="width: 400px; margin: 10px;">
              <div>
                <label>Min Height  
                  <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                  <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                  <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                </label>
                <div class="responsiveOps responsiveOptionsContainterLarge">
                  <input type="number" name="row_height" id="row_height" placeholder="Set row height" class="edit_fields row_edit_fields" value='200' style="width:60px;" data-optname="rowHeight" >
                  <select class="row_height_unit row_edit_fields" style="width:50px;" data-optname="rowHeightUnit" >
                    <option value="px">px</option>
                    <option value="%">%</option>
                    <option value="vh">vh</option>
                  </select>
                </div>
                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                  <input type="number" name="rowHeightTablet" class="rowHeightTablet row_edit_fields" placeholder="Set row height" value='200' style="width:60px;" data-optname="rowHeightTablet">
                  <select class="rowHeightUnitTablet row_edit_fields" style="width:50px;" data-optname="rowHeightUnitTablet" >
                    <option value="px">px</option>
                    <option value="%">%</option>
                    <option value="vh">vh</option>
                  </select>
                </div>
                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                  <input type="number" name="rowHeightMobile" class="rowHeightMobile row_edit_fields" placeholder="Set row height" value='200' style="width:60px;" data-optname="rowHeightMobile" >
                  <select class="rowHeightUnitMobile row_edit_fields" style="width:50px;" data-optname="rowHeightUnitMobile" >
                    <option value="px">px</option>
                    <option value="%">%</option>
                    <option value="vh">vh</option>
                  </select>
                </div>
              </div>
              <br>
              <br>
              <br>
              <label>Container Width</label>
              <select data-optname="rowData.conType" class="edit_fields row_edit_fields">
                <option value="">Full Width</option>
                <option value="boxed">Boxed</option>
              </select>
              <br><br><br>
              <div class="boxedWidthOptionContainer" style="display: none;">
                <label>Container Width</label>
                <input type="number" data-optname="rowData.conWidth" class="edit_fields row_edit_fields">
                <br><br><br>
              </div>
              <label>Number of Columns :</label>
              <input type="number" name="number_of_columns" id="number_of_columns" placeholder="Number of columns in row" min="1" max="8"  class="edit_fields row_edit_fields" value='1' data-optname="columns" >
              <br>
              <br>
              <br>
              <hr><br>
              <div class="PB_accordion" style="margin-left: -10px;" >
                <h4>Background</h4>
                <div style="background: #fff;">
                  <div class="pluginops-tabs2">
                    <ul class="pluginops-tab2-links tabEditFields">
                      <li class="active"><a  href="#defaultRowBgOptions" class="pluginops-tab2_link">Default</a></li>
                      <li><a  href="#hoverRowBgOptions" class="pluginops-tab2_link">Hover</a></li>
                    </ul>
                    <div class="pluginops-tab2-content" style="box-shadow: none;">
                      <div id="defaultRowBgOptions" class="pluginops-tab2 active">
                        <br><br>
                        <div id="pluginops_input_tabs" class="popbinputTabsWrapper POPBInputNormalRow">
                          <p style="display: inline;"> Background Type </p>
                          <div class="iputTabNav">
                            <div class="popbNavItem" data-inptabID='content_popb_tab_1' title="Simple">
                              <label for="inputID1"> <i class="fa fa-paint-brush"></i></label>
                              <input type="radio" name="rowBackgroundType" id="inputID1" value='solid' class="rowBackgroundType rowBackgroundTypeSolid tabbedInputRadio row_edit_fields" data-optname="rowData.rowBackgroundType" >
                            </div>
                            <div class="popbNavItem" data-inptabID='content_popb_tab_2' title="Gradient">
                              <label for="inputID2 " class="GradientIcon"> <i class="fa fa-square"></i></label>
                              <input type="radio" name="rowBackgroundType" id="inputID2" class="rowBackgroundType rowBackgroundTypeGradient tabbedInputRadio row_edit_fields" value="gradient" data-optname="rowData.rowBackgroundType" >
                            </div>
                          </div>
                          <div class="popb_input_tabContent">
                            <div class="content_popb_tab_1 popb_tab_content">
                              <br><br><br>
                              <label>Color :</label>
                              <input type="text" name="rowBgColor" class="color-picker_btn_two rowBgColor row_edit_fields" data-alpha='true' id="rowBgColor" value='#fff' data-optname="rowData.bg_color" >
                              <br> <br><br>
                              <div>
                                <label> <span title="Background Image">Bg Image</span>
                                  <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>
                                  <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                  <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                </label>
                                <div class="responsiveOps responsiveOptionsContainterLarge">
                                  <input id="image_location1991" type="text" class="rowBgImg upload_image_button1991 row_edit_fields"  name='lpp_add_img_1' value='' placeholder='Insert Image URL here' data-optname="rowData.bg_img" > <br> <br>
                                  <label></label>
                                  <input id="image_location1991" type="button" class="upload_bg pb_upload_btn" data-id="1991" value="Upload"  style="" />
                                </div>
                                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;" >
                                  <input id="image_location1992" type="text" class="upload_image_button1992 row_edit_fields"  name='lpp_add_img_1' value='' placeholder='Insert Image URL here' data-optname="rowData.bg_imgT" > <br> <br>
                                  <label></label>
                                  <input id="image_location1992" type="button" class="upload_bg pb_upload_btn" data-id="1992" value="Upload"  style="" />
                                </div>
                                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;" >
                                  <input id="image_location1993" type="text" class="upload_image_button1993 row_edit_fields"  name='lpp_add_img_1' value='' placeholder='Insert Image URL here' data-optname="rowData.bg_imgM" > <br> <br>
                                  <label></label>
                                  <input id="image_location1993" type="button" class="upload_bg pb_upload_btn" data-id="1993" value="Upload"  style="" />
                                </div>
                              </div>
                              <br>
                              <br>
                              <br>
                              <br>
                              <div class="imageBackgroundOpsRow" style="display: none;">
                                <div>
                                  <label> Position 
                                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                  </label>
                                  <div class="responsiveOps responsiveOptionsContainterLarge">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.pos" >
                                      <option value="">Default</option>
                                      <option value="top left">Top Left</option>
                                      <option value="top center">Top Center</option>
                                      <option value="top right">Top Right</option>
                                      <option value="center left">Center Left</option>
                                      <option value="center center">Center Center</option>
                                      <option value="center right">Center Right</option>
                                      <option value="bottom left">Bottom Left</option>
                                      <option value="bottom center">Bottom Center</option>
                                      <option value="bottom right">Bottom Right</option>
                                      <option value="custom">Custom</option>
                                    </select>
                                  </div>
                                  <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.posT" >
                                      <option value="">Default</option>
                                      <option value="top left">Top Left</option>
                                      <option value="top center">Top Center</option>
                                      <option value="top right">Top Right</option>
                                      <option value="center left">Center Left</option>
                                      <option value="center center">Center Center</option>
                                      <option value="center right">Center Right</option>
                                      <option value="bottom left">Bottom Left</option>
                                      <option value="bottom center">Bottom Center</option>
                                      <option value="bottom right">Bottom Right</option>
                                      <option value="custom">Custom</option>
                                    </select>
                                  </div>
                                  <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.posM" >
                                      <option value="">Default</option>
                                      <option value="top left">Top Left</option>
                                      <option value="top center">Top Center</option>
                                      <option value="top right">Top Right</option>
                                      <option value="center left">Center Left</option>
                                      <option value="center center">Center Center</option>
                                      <option value="center right">Center Right</option>
                                      <option value="bottom left">Bottom Left</option>
                                      <option value="bottom center">Bottom Center</option>
                                      <option value="bottom right">Bottom Right</option>
                                      <option value="custom">Custom</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="rowBgImgCustomPositionDiv" style="display: none;">
                                  <br><br><br><br>
                                  <div>
                                    <label>X Position 
                                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                    </label>
                                    <div class="responsiveOps responsiveOptionsContainterLarge">
                                      <input type="number" class="row_edit_fields" data-optname="rowData.bgImgOps.xPos" style="width:60px;" >
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.xPosU" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                      <input type="number" class="row_edit_fields" data-optname="rowData.bgImgOps.xPosT" style="width:60px;" >
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.xPosUT" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                      <input type="number" class="row_edit_fields" data-optname="rowData.bgImgOps.xPosM" style="width:60px;">
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.xPosUM" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                  </div>
                                  <br><br><br><br>
                                  <div>
                                    <label>Y Position 
                                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                    </label>
                                    <div class="responsiveOps responsiveOptionsContainterLarge">
                                      <input type="number" class="row_edit_fields" data-optname="rowData.bgImgOps.yPos" style="width:60px;" >
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.yPosU" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                      <input type="number" class="row_edit_fields" data-optname="rowData.bgImgOps.yPosT" style="width:60px;" >
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.yPosUT" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                      <input type="number" class="row_edit_fields" data-optname="rowData.bgImgOps.yPosM" style="width:60px;" >
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.yPosUM" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                                <br><br><br><br>
                                <div>
                                  <label>Repeat 
                                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>
                                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                  </label>
                                  <div class="responsiveOps responsiveOptionsContainterLarge">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.rep" >
                                      <option value="default">Default</option>
                                      <option value="no-repeat">No-repeat</option>
                                      <option value="repeat">Repeat</option>
                                      <option value="repeat-x">Repeat-x</option>
                                      <option value="repeat-y">Repeat-y</option>
                                    </select>
                                  </div>
                                  <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.repT" >
                                      <option value="default">Default</option>
                                      <option value="no-repeat">No-repeat</option>
                                      <option value="repeat">Repeat</option>
                                      <option value="repeat-x">Repeat-x</option>
                                      <option value="repeat-y">Repeat-y</option>
                                    </select>
                                  </div>
                                  <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.repM" >
                                      <option value="default">Default</option>
                                      <option value="no-repeat">No-repeat</option>
                                      <option value="repeat">Repeat</option>
                                      <option value="repeat-x">Repeat-x</option>
                                      <option value="repeat-y">Repeat-y</option>
                                    </select>
                                  </div>
                                </div>
                                <br><br><br><br>
                                <div>
                                  <label>Size 
                                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                  </label>
                                  <div class="responsiveOps responsiveOptionsContainterLarge">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.size" >
                                      <option value="">Default</option>
                                      <option value="auto">Auto</option>
                                      <option value="cover">Cover</option>
                                      <option value="contain">Contain</option>
                                      <option value="custom">Custom</option>
                                    </select>
                                  </div>
                                  <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.sizeT" >
                                      <option value="">Default</option>
                                      <option value="auto">Auto</option>
                                      <option value="cover">Cover</option>
                                      <option value="contain">Contain</option>
                                      <option value="custom">Custom</option>
                                    </select>
                                  </div>
                                  <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.sizeM" >
                                      <option value="">Default</option>
                                      <option value="auto">Auto</option>
                                      <option value="cover">Cover</option>
                                      <option value="contain">Contain</option>
                                      <option value="custom">Custom</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="rowBgImgCustomSizeDiv" style="display: none;">
                                  <br><br><br><br>
                                  <div>
                                    <label>Width 
                                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                    </label>
                                    <div class="responsiveOps responsiveOptionsContainterLarge">
                                      <input class="row_edit_fields" data-optname="rowData.bgImgOps.cWid"  type="number" style="width:60px;" >
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.widU" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                      <input class="row_edit_fields" data-optname="rowData.bgImgOps.cWidT"  type="number" style="width:60px;" >
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.widUT" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                      <input class="row_edit_fields" data-optname="rowData.bgImgOps.cWidM"  type="number" style="width:60px;" >
                                      <select class="row_edit_fields" style="width:50px;" data-optname="rowData.bgImgOps.widUM" >
                                        <option value="px">px</option>
                                        <option value="%">%</option>
                                        <option value="vw">vw</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                                <br><br><br><br>
                                <div>
                                  <label> <span title="Fixed Background">Fixed Bg</span>
                                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                  </label>
                                  <div class="responsiveOps responsiveOptionsContainterLarge">
                                    <select class="rowBackgroundParallax row_edit_fields" id="rowBackgroundParallax" data-optname="rowData.rowBackgroundParallax" >
                                      <option value="">Select</option>
                                      <option value="true">Yes</option>
                                      <option value="false">No</option>
                                    </select>
                                  </div>
                                  <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.parlxT" >
                                      <option value="">Select</option>
                                      <option value="true">Yes</option>
                                      <option value="false">No</option>
                                    </select>
                                  </div>
                                  <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                    <select class="row_edit_fields" data-optname="rowData.bgImgOps.parlxM" >
                                      <option value="">Select</option>
                                      <option value="true">Yes</option>
                                      <option value="false">No</option>
                                    </select>
                                  </div>
                                </div>
                                
                              </div>

                            </div>
                            <div class="content_popb_tab_2 popb_tab_content">
                              <br><br><br>
                              <label>First Color </label>
                              <input type="text" name="rowGradientColorFirst" class="color-picker_btn_two rowGradientColorFirst row_edit_fields" data-alpha='true' id="rowGradientColorFirst" value='#fff' data-optname="rowData.rowGradient.rowGradientColorFirst" >
                              <p>Location</p>
                              <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='rowGradientLocationFirst'></div>
                              <input type="number" class="rowGradientLocationFirst row_edit_fields" data-optname="rowData.rowGradient.rowGradientLocationFirst" >
                              <br><br><hr>
                              <br><br>
                              <label>Second Color </label>
                              <input type="text" name="rowGradientColorSecond" class="color-picker_btn_two rowGradientColorSecond row_edit_fields" data-alpha='true' id="rowGradientColorSecond" value='#fff' data-optname="rowData.rowGradient.rowGradientColorSecond" >
                              <p>Location</p>
                              <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='rowGradientLocationSecond'></div>
                              <input type="number" class="rowGradientLocationSecond row_edit_fields" data-optname="rowData.rowGradient.rowGradientLocationSecond" >
                              <br><br>
                              <hr>
                              <br>
                              <br>
                              <label>Type </label>
                              <select class="rowGradientType row_edit_fields" data-optname="rowData.rowGradient.rowGradientType" >
                                <option value="linear">Linear</option>
                                <option value="radial">Radial</option>
                              </select>
                              <br>
                              <br>
                              <div class="radialInput" style="display: none;">
                                <br>
                                <label>Position </label>
                                <select class="rowGradientPosition row_edit_fields" data-optname="rowData.rowGradient.rowGradientPosition" >
                                  <option value="center center">Center Center</option>
                                  <option value="center left">Center Left</option>
                                  <option value="center right">Center Right</option>
                                  <option value="top center">Top Center</option>
                                  <option value="top left">Top Left</option>
                                  <option value="top right">Top Right</option>
                                  <option value="bottom center">Bottom Center</option>
                                  <option value="bottom left">Bottom Left</option>
                                  <option value="bottom right">Bottom Right</option>
                                </select>
                                <br><br>
                              </div>
                              <div class="linearInput" style="display: none;">
                              <p>Angle</p>
                                <div class="PoPbrangeSliderAngle PoPbnumberSlider" data-targetRangeInput='rowGradientAngle'></div>
                                <input type="number" class="rowGradientAngle row_edit_fields" data-optname="rowData.rowGradient.rowGradientAngle" >
                              </div>
                              <br>
                              <br>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id="hoverRowBgOptions" class="pluginops-tab2" >
                        <br><br>
                        <div id="pluginops_input_tabs" class="popbinputTabsWrapper POPBInputNormalRow POPBInputHoverRow">
                          <p style="display: inline;"> Background Type </p>
                          <div class="iputTabNav">
                            <div class="popbNavItem" data-inptabID='content_popb_tab_1' title="Simple">
                              <label for="inputIDHover1"> <i class="fa fa-paint-brush"></i></label>
                              <input type="radio" name="rowBackgroundTypeHover" id="inputIDHover1"  class="rowBackgroundTypeHover rowBackgroundTypeSolidHover tabbedInputRadio row_edit_fields" value='solid' data-optname="rowData.rowHoverOptions.rowBackgroundTypeHover" >
                            </div>
                            <div class="popbNavItem" data-inptabID='content_popb_tab_2' title="Gradient">
                              <label for="inputIDHover2 " class="GradientIcon"> <i class="fa fa-square"></i></label>
                              <input type="radio" name="rowBackgroundTypeHover" id="inputIDHover2" class="rowBackgroundTypeHover rowBackgroundTypeGradientHover tabbedInputRadio row_edit_fields" value="gradient" data-optname="rowData.rowHoverOptions.rowBackgroundTypeHover" >
                            </div>
                          </div>
                          <div class="popb_input_tabContent">
                            <div class="content_popb_tab_1 popb_tab_content">
                              <br><br>
                              <label>Color :</label>
                              <input type="text" name="rowBgColorHover" class="color-picker_btn_two rowBgColorHover row_edit_fields" data-alpha='true' id="rowBgColorHover" value='#fff' data-optname="rowData.rowHoverOptions.rowBgColorHover" >
                              <br>
                            </div>
                            <div class="content_popb_tab_2 popb_tab_content">
                              <br><br><br>
                              <label>First Color </label>
                              <input type="text" name="rowGradientColorFirstHover" class="color-picker_btn_two rowGradientColorFirstHover row_edit_fields" data-alpha='true' id="rowGradientColorFirstHover" value='#fff' data-optname="rowData.rowHoverOptions.rowGradientHover.rowGradientColorFirstHover" >
                              <p>Location</p>
                              <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='rowGradientLocationFirstHover'></div>
                              <input type="number" class="rowGradientLocationFirstHover row_edit_fields" data-optname="rowData.rowHoverOptions.rowGradientHover.rowGradientLocationFirstHover" >
                              <br><br><hr>
                              <br><br>
                              <label>Second Color </label>
                              <input type="text" name="rowGradientColorSecondHover" class="color-picker_btn_two rowGradientColorSecondHover row_edit_fields" data-alpha='true' id="rowGradientColorSecondHover" value='#fff' data-optname="rowData.rowHoverOptions.rowGradientHover.rowGradientColorSecondHover" >
                              <p>Location</p>
                              <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='rowGradientLocationSecondHover'></div>
                              <input type="number" class="rowGradientLocationSecondHover row_edit_fields" data-optname="rowData.rowHoverOptions.rowGradientHover.rowGradientLocationSecondHover" >
                              <br><br>
                              <hr>
                              <br>
                              <br>
                              <label>Type </label>
                              <select class="rowGradientTypeHover row_edit_fields" data-optname="rowData.rowHoverOptions.rowGradientHover.rowGradientTypeHover" >
                                <option value="linear">Linear</option>
                                <option value="radial">Radial</option>
                              </select>
                              <br>
                              <br>
                              <div class="radialInputHover" style="display: none;">
                                <br>
                                <label>Position </label>
                                <select class="rowGradientPositionHover row_edit_fields" data-optname="rowData.rowHoverOptions.rowGradientHover.rowGradientPositionHover" >
                                  <option value="center center">Center Center</option>
                                  <option value="center left">Center Left</option>
                                  <option value="center right">Center Right</option>
                                  <option value="top center">Top Center</option>
                                  <option value="top left">Top Left</option>
                                  <option value="top right">Top Right</option>
                                  <option value="bottom center">Bottom Center</option>
                                  <option value="bottom left">Bottom Left</option>
                                  <option value="bottom right">Bottom Right</option>
                                </select>
                                <br><br>
                              </div>
                              <div class="linearInputHover" style="display: none;">
                              <p>Angle</p>
                                <div class="PoPbrangeSliderAngle PoPbnumberSlider" data-targetRangeInput='rowGradientAngleHover'></div>
                                <input type="number" class="rowGradientAngleHover row_edit_fields" data-optname="rowData.rowHoverOptions.rowGradientHover.rowGradientAngleHover" >
                              </div>
                              <br>
                              <br>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <br>
                        <p>Transition Duration</p>
                        <div class="PoPbrangeSliderTransition PoPbnumberSlider" data-targetRangeInput='rowHoverTransitionDuration'></div>
                        <input type="number" class="rowHoverTransitionDuration row_edit_fields" data-optname="rowData.rowHoverOptions.rowHoverTransitionDuration" >
                        <br><br>
                      </div>
                    </div>
                  </div> 
                </div>
                <h4>Background Overlay</h4>
                <div style="width: 100%; background: #fff;">
                      <div id="defaultRowBgOverlayOptions">
                        <br><br>
                        <div id="pluginops_input_tabs" class="popbinputTabsWrapper POPBInputNormalRow POPBInputNormalRowOverlay">
                          <p style="display: inline;"> Overlay Type </p>
                          <div class="iputTabNav">
                            <div class="popbNavItem" data-inptabID='content_popb_tab_1' title="Simple">
                              <label for="inputID1"> <i class="fa fa-paint-brush"></i></label>
                              <input type="radio" name="rowOverlayBackgroundType" id="inputID1" value='solid' class="rowOverlayBackgroundType rowOverlayBackgroundTypeSolid tabbedInputRadio row_edit_fields" data-optname="rowData.rowOverlayBackgroundType" >
                            </div>
                            <div class="popbNavItem" data-inptabID='content_popb_tab_2' title="Gradient">
                              <label for="inputID2 " class="GradientIcon"> <i class="fa fa-square"></i></label>
                              <input type="radio" name="rowOverlayBackgroundType" id="inputID2" class="rowOverlayBackgroundType rowOverlayBackgroundTypeGradient tabbedInputRadio row_edit_fields" value="gradient" data-optname="rowData.rowOverlayBackgroundType" >
                            </div>
                          </div>
                          <div class="">
                            <div class="content_popb_tab_1 popb_tab_content">
                              <br><br><br>
                              <label>Color :</label>
                              <input type="text" name="rowBgOverlayColor" class="color-picker_btn_two rowBgOverlayColor row_edit_fields" data-alpha='true' id="rowBgOverlayColor" value='#fff' data-optname="rowData.rowBgOverlayColor" >
                              <br> <br>
                            </div>
                            <div class="content_popb_tab_2 popb_tab_content">
                              <br><br><br>
                              <label>First Color </label>
                              <input type="text" name="rowOverlayGradientColorFirst" class="color-picker_btn_two rowOverlayGradientColorFirst row_edit_fields" data-alpha='true' id="rowOverlayGradientColorFirst" value='#fff' data-optname="rowData.rowOverlayGradient.rowOverlayGradientColorFirst" >
                              <p>Location</p>
                              <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='rowOverlayGradientLocationFirst'></div>
                              <input type="number" class="rowOverlayGradientLocationFirst row_edit_fields" data-optname="rowData.rowOverlayGradient.rowOverlayGradientLocationFirst" >
                              <br><br><hr>
                              <br><br>
                              <label>Second Color </label>
                              <input type="text" name="rowOverlayGradientColorSecond" class="color-picker_btn_two rowOverlayGradientColorSecond row_edit_fields" data-alpha='true' id="rowOverlayGradientColorSecond" value='#fff' data-optname="rowData.rowOverlayGradient.rowOverlayGradientColorSecond" >
                              <p>Location</p>
                              <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='rowOverlayGradientLocationSecond'></div>
                              <input type="number" class="rowOverlayGradientLocationSecond row_edit_fields" data-optname="rowData.rowOverlayGradient.rowOverlayGradientLocationSecond" >
                              <br><br>
                              <hr>
                              <br>
                              <br>
                              <label>Type </label>
                              <select class="rowOverlayGradientType row_edit_fields" data-optname="rowData.rowOverlayGradient.rowOverlayGradientType" >
                                <option value="linear">Linear</option>
                                <option value="radial">Radial</option>
                              </select>
                              <br>
                              <br>
                              <div class="radialInput" style="display: none;">
                                <br>
                                <label>Position </label>
                                <select class="rowOverlayGradientPosition row_edit_fields" data-optname="rowData.rowOverlayGradient.rowOverlayGradientPosition" >
                                  <option value="center center">Center Center</option>
                                  <option value="center left">Center Left</option>
                                  <option value="center right">Center Right</option>
                                  <option value="top center">Top Center</option>
                                  <option value="top left">Top Left</option>
                                  <option value="top right">Top Right</option>
                                  <option value="bottom center">Bottom Center</option>
                                  <option value="bottom left">Bottom Left</option>
                                  <option value="bottom right">Bottom Right</option>
                                </select>
                                <br><br>
                              </div>
                              <div class="linearInput" style="display: none;">
                              <p>Angle</p>
                                <div class="PoPbrangeSliderAngle PoPbnumberSlider" data-targetRangeInput='rowOverlayGradientAngle'></div>
                                <input type="number" class="rowOverlayGradientAngle row_edit_fields" data-optname="rowData.rowOverlayGradient.rowOverlayGradientAngle" >
                              </div>
                              <br>
                              <br>
                            </div>
                          </div>
                        </div>
                      </div>
                </div>
                <h4>Background Shapes</h4>
                <div style="width: 100%; background: #fff;">
                  <div class="pluginops-tabs2">
                      <ul class="pluginops-tab2-links tabEditFields">
                        <li class="active"><a  href="#bgShapeTop" class="pluginops-tab2_link">Top</a></li>
                        <li><a  href="#bgShapeBottom" class="pluginops-tab2_link">Bottom</a></li>
                      </ul>
                      <div class="pluginops-tab2-content" style="box-shadow: none;">
                        <div id="bgShapeTop" class="pluginops-tab2 active">
                          <br>
                          <label>Shape Type </label>
                          <select class="rbgstType row_edit_fields" data-optname="rowData.bgSTop.rbgstType" >
                            <option value="none">none</option>
                            <option value="Mountains">Mountains</option>
                            <option value="Spikes">Spikes</option>
                            <option value="Pyramids">Pyramids</option>
                            <option value="Triangle">Triangle</option>
                            <option value="TriangleInvert">Triangle Inverted</option>
                            <option value="TriangleAssym">Triangle Asymmetrical</option>
                            <option value="TriangleAssymInvert">Triangle Asymmetrical Inverted</option>
                            <option value="Slope">Slope</option>
                            <option value="FanOpaque">Fan Opaque</option>
                            <option value="Curve">Curve</option>
                            <option value="CurveInvert">Curve Inverted</option>
                            <option value="Waves">Waves</option>
                            <option value="Arrow">Arrow</option>
                            <option value="ArrowInvert">Arrow Inverted</option>
                            <option value="Book">Book</option>
                            <option value="BookInvert">Book Inverted</option>
                            <option value="Clouds">Clouds</option>
                            <option value="Skyline">Skyline</option>
                          </select>
                          <br><br><br>
                          <label>Shape Color</label>
                          <input type="text" name="rbgstColor" class="color-picker_btn_two rbgstColor row_edit_fields" data-alpha='true' id="rbgstColor" value='#fff' data-optname="rowData.bgSTop.rbgstColor" >
                          <br><br><br>
                          <div>
                            <h4>Width   
                              <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                              <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                              <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                            </h4>
                            <div class="responsiveOps responsiveOptionsContainterLarge">
                              <label></label>
                              <input type="number" class="rbgstWidth row_edit_fields" min="100" max="300" data-optname="rowData.bgSTop.rbgstWidth" >
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                              <label></label>
                              <input type="number" class="rbgstWidtht row_edit_fields" min="100" max="300" data-optname="rowData.bgSTop.rbgstWidtht" >
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                              <label></label>
                              <input type="number" class="rbgstWidthm row_edit_fields" min="100" max="300" data-optname="rowData.bgSTop.rbgstWidthm" >
                            </div>
                          </div>
                          <br><br><br>
                          <div>
                            <h4>Height   
                              <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                              <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                              <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                            </h4>
                            <div class="responsiveOps responsiveOptionsContainterLarge">
                              <label></label>
                              <input type="number" class="rbgstHeight row_edit_fields" data-optname="rowData.bgSTop.rbgstHeight" >
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                              <label></label>
                              <input type="number" class="rbgstHeightt row_edit_fields" data-optname="rowData.bgSTop.rbgstHeightt" >
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                              <label></label>
                              <input type="number" class="rbgstHeightm row_edit_fields" data-optname="rowData.bgSTop.rbgstHeightm" >
                            </div>
                          </div>
                          <br><br><br>
                          <label>Flipped </label>
                          <select class="rbgstFlipped row_edit_fields" data-optname="rowData.bgSTop.rbgstFlipped" >
                            <option value="false">No</option>
                            <option value="true">Yes</option>
                          </select>
                          <br><br><br>
                          <label>Bring To Front </label>
                          <select class="rbgstFront row_edit_fields" data-optname="rowData.bgSTop.rbgstFront" >
                            <option value="false">No</option>
                            <option value="true">Yes</option>
                          </select>
                          <br><br><br>
                        </div>
                        <div id="bgShapeBottom" class="pluginops-tab2">
                          <br>
                          <label>Shape Type </label>
                          <select class="rbgsbType row_edit_fields" data-optname="rowData.bgSBottom.rbgsbType" >
                            <option value="none">none</option>
                            <option value="Mountains">Mountains</option>
                            <option value="Spikes">Spikes</option>
                            <option value="Pyramids">Pyramids</option>
                            <option value="Triangle">Triangle</option>
                            <option value="TriangleInvert">Triangle Inverted</option>
                            <option value="TriangleAssym">Triangle Asymmetrical</option>
                            <option value="TriangleAssymInvert">Triangle Asymmetrical Inverted</option>
                            <option value="Slope">Slope</option>
                            <option value="FanOpaque">Fan Opaque</option>
                            <option value="Curve">Curve</option>
                            <option value="CurveInvert">Curve Inverted</option>
                            <option value="Waves">Waves</option>
                            <option value="Arrow">Arrow</option>
                            <option value="ArrowInvert">Arrow Inverted</option>
                            <option value="Book">Book</option>
                            <option value="BookInvert">Book Inverted</option>
                            <option value="Clouds">Clouds</option>
                            <option value="Skyline">Skyline</option>
                          </select>
                          <br><br><br>
                          <label>Shape Color</label>
                          <input type="text" name="rbgsbColor" class="color-picker_btn_two rbgsbColor row_edit_fields" data-alpha='true' id="rbgsbColor" value='#fff' data-optname="rowData.bgSBottom.rbgsbColor" >
                          <br><br><br>
                          <div>
                            <h4>Width   
                              <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                              <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                              <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                            </h4>
                            <div class="responsiveOps responsiveOptionsContainterLarge">
                              <label></label>
                              <input type="number" class="rbgsbWidth row_edit_fields" min="100" max="300" data-optname="rowData.bgSBottom.rbgsbWidth" >
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                              <label></label>
                              <input type="number" class="rbgsbWidtht row_edit_fields" min="100" max="300" data-optname="rowData.bgSBottom.rbgsbWidtht" >
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                              <label></label>
                              <input type="number" class="rbgsbWidthm row_edit_fields" min="100" max="300" data-optname="rowData.bgSBottom.rbgsbWidthm" >
                            </div>
                          </div>
                          <br><br><br>
                          <div>
                            <h4>Height   
                              <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                              <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                              <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                            </h4>
                            <div class="responsiveOps responsiveOptionsContainterLarge">
                              <label></label>
                              <input type="number" class="rbgsbHeight row_edit_fields" data-optname="rowData.bgSBottom.rbgsbHeight" >
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                              <label></label>
                              <input type="number" class="rbgsbHeightt row_edit_fields" data-optname="rowData.bgSBottom.rbgsbHeightt" >
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                              <label></label>
                              <input type="number" class="rbgsbHeightm row_edit_fields" data-optname="rowData.bgSBottom.rbgsbHeightm" >
                            </div>
                          </div>
                          <br><br><br>
                          <label>Flipped </label>
                          <select class="rbgsbFlipped row_edit_fields" data-optname="rowData.bgSBottom.rbgsbFlipped" >
                            <option value="false">No</option>
                            <option value="true">Yes</option>
                          </select>
                          <br><br><br>
                          <label>Bring To Front </label>
                          <select class="rbgsbFront row_edit_fields" data-optname="rowData.bgSBottom.rbgsbFront" >
                            <option value="false">No</option>
                            <option value="true">Yes</option>
                          </select>
                          <br><br><br>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
              <br><hr><br>
              <div class="PB_accordion" style="margin-left: -10px;">
                <h4>Margins & Paddings</h4>
                <div style="background: #fff;" >
                  <h4>Row Margin   
                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                  </h4>
                  <div class="responsiveOps responsiveOptionsContainterLarge">
                    <input type="number" name="rowMarginTop" class="padding_inline_inp linkedField  rowMarginTop row_edit_fields" id="rowMarginTop"  placeholder="Top"  data-optname="rowData.margin.rowMarginTop" value="0">
                    
                    <input type="number" name="rowMarginBottom" class="padding_inline_inp linkedField  rowMarginBottom row_edit_fields" id="rowMarginBottom"  placeholder="Bottom" data-optname="rowData.margin.rowMarginBottom" value="0">
                    
                    <input type="number" name="rowMarginLeft" class="padding_inline_inp linkedField  rowMarginLeft row_edit_fields" id="rowMarginLeft"  placeholder="Left" data-optname="rowData.margin.rowMarginLeft" value="0">
                    
                    <input type="number" name="rowMarginRight" class="padding_inline_inp linkedField  rowMarginRight row_edit_fields" id="rowMarginRight"  placeholder="Right" data-optname="rowData.margin.rowMarginRight" value="0">

                    <span class="linkfieldBtn rowLinkBtn linkBtn" > <i class="fa fa-link"></i> </span>
                  </div>
                  <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <input type="number" name="rowMarginTopTablet" class="padding_inline_inp linkedField  rowMarginTopTablet row_edit_fields" id="rowMarginTopTablet"  placeholder="Top" data-optname="rowData.marginTablet.rMTT" value="0">
                    
                    <input type="number" name="rowMarginBottomTablet" class="padding_inline_inp linkedField  rowMarginBottomTablet row_edit_fields" id="rowMarginBottomTablet"  placeholder="Bottom" data-optname="rowData.marginTablet.rMBT" value="0">
                    
                    <input type="number" name="rowMarginLeftTablet" class="padding_inline_inp linkedField  rowMarginLeftTablet row_edit_fields" id="rowMarginLeftTablet"  placeholder="Left" data-optname="rowData.marginTablet.rMLT" value="0">
                    
                    <input type="number" name="rowMarginRightTablet" class="padding_inline_inp linkedField  rowMarginRightTablet row_edit_fields" id="rowMarginRightTablet"  placeholder="Right" data-optname="rowData.marginTablet.rMRT" value="0">

                    <span class="linkfieldBtn rowLinkBtn linkBtn" > <i class="fa fa-link"></i> </span>
                  </div>
                  <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <input type="number" name="rowMarginTopMobile" class="padding_inline_inp linkedField  rowMarginTopMobile row_edit_fields" id="rowMarginTopMobile"  placeholder="Top" data-optname="rowData.marginMobile.rMTM" value="0">
                    
                    <input type="number" name="rowMarginBottomMobile" class="padding_inline_inp linkedField  rowMarginBottomMobile row_edit_fields" id="rowMarginBottomMobile"  placeholder="Bottom" data-optname="rowData.marginMobile.rMBM" value="0">
                    
                    <input type="number" name="rowMarginLeftMobile" class="padding_inline_inp linkedField  rowMarginLeftMobile row_edit_fields" id="rowMarginLeftMobile"  placeholder="Left" data-optname="rowData.marginMobile.rMLM" value="0">
                    
                    <input type="number" name="rowMarginRightMobile" class="padding_inline_inp linkedField  rowMarginRightMobile row_edit_fields" id="rowMarginRightMobile"  placeholder="Right" data-optname="rowData.marginMobile.rMRM" value="0">

                    <span class="linkfieldBtn rowLinkBtn linkBtn" > <i class="fa fa-link"></i> </span>
                  </div>
                  <br>
                  <br>
                  <span class="ulp-note">The unit is percentage so set values accordingly.</span>
                  <br><br>
                  <h4>Row Padding
                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>
                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                  </h4>
                  <div class="responsiveOps responsiveOptionsContainterLarge">
                    <input type="number" name="rowPaddingTop" class="padding_inline_inp linkedField  rowPaddingTop row_edit_fields" id="rowPaddingTop"  placeholder="Top" data-optname="rowData.padding.rowPaddingTop" value="0">
                    
                    <input type="number" name="rowPaddingBottom" class="padding_inline_inp linkedField  rowPaddingBottom row_edit_fields" id="rowPaddingBottom"  placeholder="Bottom" data-optname="rowData.padding.rowPaddingBottom" value="0">
                    
                    <input type="number" name="rowPaddingLeft" class="padding_inline_inp linkedField  rowPaddingLeft row_edit_fields" id="rowPaddingLeft"  placeholder="Left" data-optname="rowData.padding.rowPaddingLeft" value="0">
                    
                    <input type="number" name="rowPaddingRight" class="padding_inline_inp linkedField  rowPaddingRight row_edit_fields" id="rowPaddingRight"  placeholder="Right" data-optname="rowData.padding.rowPaddingRight" value="0">

                    <span class="linkfieldBtn rowLinkBtn linkBtn" > <i class="fa fa-link"></i> </span>
                  </div>
                  <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                    <input type="number" name="rowPaddingTopTablet" class="padding_inline_inp linkedField  rowPaddingTopTablet row_edit_fields" id="rowPaddingTopTablet"  placeholder="Top" data-optname="rowData.paddingTablet.rPTT" value="1.5">
                    
                    <input type="number" name="rowPaddingBottomTablet" class="padding_inline_inp linkedField  rowPaddingBottomTablet row_edit_fields" id="rowPaddingBottomTablet"  placeholder="Bottom" data-optname="rowData.paddingTablet.rPBT" value="1.5">
                    
                    <input type="number" name="rowPaddingLeftTablet" class="padding_inline_inp linkedField  rowPaddingLeftTablet row_edit_fields" id="rowPaddingLeftTablet"  placeholder="Left" data-optname="rowData.paddingTablet.rPLT" value="1.5">
                    
                    <input type="number" name="rowPaddingRightTablet" class="padding_inline_inp linkedField  rowPaddingRightTablet row_edit_fields" id="rowPaddingRightTablet"  placeholder="Right" data-optname="rowData.paddingTablet.rPRT" value="1.5">

                    <span class="linkfieldBtn rowLinkBtn linkBtn" > <i class="fa fa-link"></i> </span>
                  </div>
                  <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                    <input type="number" name="rowPaddingTopMobile" class="padding_inline_inp linkedField  rowPaddingTopMobile row_edit_fields" id="rowPaddingTopMobile"  placeholder="Top" data-optname="rowData.paddingMobile.rPTM" value="1.5">
                    
                    <input type="number" name="rowPaddingBottomMobile" class="padding_inline_inp linkedField  rowPaddingBottomMobile row_edit_fields" id="rowPaddingBottomMobile"  placeholder="Bottom" data-optname="rowData.paddingMobile.rPBM" value="1.5">
                    
                    <input type="number" name="rowPaddingLeftMobile" class="padding_inline_inp linkedField  rowPaddingLeftMobile row_edit_fields" id="rowPaddingLeftMobile"  placeholder="Left" data-optname="rowData.paddingMobile.rPLM" value="1.5"> 
                    
                    <input type="number" name="rowPaddingRightMobile" class="padding_inline_inp linkedField  rowPaddingRightMobile row_edit_fields" id="rowPaddingRightMobile"  placeholder="Right" data-optname="rowData.paddingMobile.rPRM" value="1.5">

                    <span class="linkfieldBtn rowLinkBtn linkBtn" > <i class="fa fa-link"></i> </span>
                  </div>
                  <br>
                  <br>
                  <span class="ulp-note">The unit is percentage so set values accordingly.</span>
                </div>
                <h4>Row Display Options</h4>
                <div style="background: #fff;" >
                  <br>
                  <label>Custom Row Class : </label>
                  <input type="text" class="rowCustomClass row_edit_fields" data-optname="rowData.rowCustomClass" >
                  <br>
                  <br>  
                  <br>
                  <hr>
                  <br>
                  <div>
                    <h4>Hide Row  
                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>
                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                    </h4>
                    <div class="responsiveOps responsiveOptionsContainterLarge">
                      <label>DeskTop </label>
                      <select class="row_edit_fields rowHideOnDesktop" data-optname="rowData.rowHideOnDesktop" >
                        <option value="">Select</option>
                        <option value="show">Show</option>
                        <option value="hide">Hide</option>
                      </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <label>Tablet </label>
                      <select class="row_edit_fields rowHideOnTablet" data-optname="rowData.rowHideOnTablet" >
                        <option value="">Select</option>
                        <option value="show">Show</option>
                        <option value="hide">Hide</option>
                      </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <label>Mobile </label>
                      <select class="row_edit_fields rowHideOnMobile" data-optname="rowData.rowHideOnMobile" >
                        <option value="">Select</option>
                        <option value="show">Show</option>
                        <option value="hide">Hide</option>
                      </select>
                    </div>
                  </div>
                  <br>
                  <br>
                  <br>
                  <div>
                    <div class="rowCopyPasteButton rowCopyButton" title="Copy to clipboard" >
                      Copy Section Row Attrs
                    </div>
                    <br><hr><br>
                    <label for="">Paste copied Section Attrs</label>
                    <textarea class="pasteCopyAttrInput" style="width:200px"></textarea>
                    <br><br>
                    <div class="rowCopyPasteButton rowPasteButton" title="Click to load copied section" >
                      Load new section
                    </div>
                  </div>
                </div>
              </div>
                  
            </div>
            </form>
          </div>
          <div id="tabRowVideo" class="pluginops-tab2" style="min-height:400px;">
            <div class="pbp_form" style="margin: 10px; width: 400px;">
            <label>Background Video :</label> 
            <select class="row_edit_fields rowBgVideoEnable" data-optname="rowData.video.rowBgVideoEnable" >
              <option value="false">Disable</option>
              <option value="true">Enable</option>
            </select>
            <br>
            <br>
            <label>Loop</label> 
            <select class="row_edit_fields rowBgVideoLoop" data-optname="rowData.video.rowBgVideoLoop" >
              <option value="no">No</option>
              <option value="loop">Yes</option>
            </select>
            <br>
            <br>
            <label>Select Platform</label>
            <select class="row_edit_fields rowVideoType" data-optname="rowData.video.rowVideoType" >
              <option value="mp4">MP4</option>
              <!-- <option value="yt">Youtube</option> -->
            </select>
            <br>
            <br>
            <hr>
            <br>
            <div class=" bgrowmp4" style="display: block;">
              <label>Video (MP4) :</label>
              <input id="image_location9" type="text" class="row_edit_fields rowVideoMpfour upload_image_button9"  name='lpp_add_img_1' value='' placeholder='Insert Video URL here'  data-optname="rowData.video.rowVideoMpfour" > <br> <br>
              <label></label>
              <input id="image_location9" type="button" class="row_edit_fields upload_bg" data-id="9" value="Upload" />
              <br><br> <hr><br>
              <label>Video (WebM) :</label>
              <input id="image_location10" type="text" class="row_edit_fields rowVideoWebM upload_image_button10"  name='lpp_add_img_1' value='' placeholder='Insert Video URL here' data-optname="rowData.video.rowVideoWebM" > <br> <br>
              <label></label>
              <input id="image_location10" type="button" class="row_edit_fields upload_bg" data-id="10" value="Upload" />
              <br><br> <hr><br>
              <label>Video Thumbnail :</label>
              <input id="image_location11" type="text" class="row_edit_fields rowVideoThumb upload_image_button11"  name='lpp_add_img_1' value='' placeholder='Insert Image URL here' data-optname="rowData.video.rowVideoThumb" > <br> <br>
              <label></label>
              <input id="image_location11" type="button" class="row_edit_fields upload_bg" data-id="11" value="Upload" />
            </div>
            <div class=" bgrowyt" style="display: none;">
              <label>Youtube Video URL</label>
              <input type="url" class="row_edit_fields rowVideoYtUrl" data-optname="rowData.video.rowVideoYtUrl" >
            </div>
              
            <br>
            <br>
            <br>
            </div>
          </div>
          <div id="tabCustomCss" class="pluginops-tab2">
            <div class="pbp_form" style="width: 400px; margin: 10px;">
              <h3>Custom CSS</h3>
              <div style="height: 300px; margin-bottom: 50px;">
                <textarea  class="row_edit_fields rowCustomStyling" style="width: 90%; min-height: 280px;" data-optname="rowData.customStyling" > </textarea>
              </div>
              <!-- <h3>Custom JS</h3>
              <div style="height: 300px;">
                <textarea  class="row_edit_fields rowCustomJS" style="width: 90%; min-height: 280px;" data-optname="rowData.customJS" > </textarea>
              </div> -->
            </div>
          </div>
        </div>
      </div>
      <h3 class="nonPremUserNotice"> Tip You can edit your row at one place and changes will appear everywhere : <a href="https://pluginops.com/page-builder/?ref=GlobalRow" target="_blank"> Learn More </a></h3>
    </div>
  </div>
</div>








<div class="lpp_modal_row insert_Global_row">
  <div class="lpp_modal_wrapper_row">
    <div class="edit_options_left_row">
      <h1 class="banner-h1">Select Global Row</h1>
      <?php 
        $ULP_GlobalRow_args = array(
          'post_type' => 'ulpb_global_rows',
          'orderby' => 'date',
          'post_status'   => 'any',
          'posts_per_page'    => 100,
        );
        $ULPB_GlobalRow_posts = get_posts( $ULP_GlobalRow_args );

        echo "<br><br><br>
            <label style='margin-right:7%;'> Select a Global Row to Insert </label>
            <select class='selectGlobalRowToInsert' name='selectGlobalRowToInsert'>
            <option value=''  > Select Row </option>
        ";
        foreach ($ULPB_GlobalRow_posts as  $ulpost) {
          $currentPostId = $ulpost->ID;
          $currentPostName = get_the_title( $currentPostId);
          $currentPostLink = get_permalink($currentPostId);
          echo "<option value='$currentPostId' data-pagelink='$currentPostLink' > $currentPostName </option>";
        }

        echo "</select> 
        ";

      ?>
    </div>
    <div  class="addNewGlobalRowClosebutton" style="">
        <div ><span class="dashicons dashicons-arrow-left editSaveVisibleIcon" ></span></div><p></p><br>
    </div>
  </div>
</div>












<div class="lpp_modal_row pageops_modal">
  <div class="lpp_modal_wrapper_row">
    <div class="edit_options_left_row">
      <div class="pluginops-tabs2">
        <ul class="pluginops-tab2-links" style="background: #2fa8f9;">
          <li style="margin: 0;"  class="active"><a style="font-size:12px; padding: 10px; text-align: center;" href="#tabPageOptions" class="pluginops-tab2_link"> <i class="fa fa-gears" style="font-size: 20px;"></i> <br> Page Options</a></li>
          <li style="margin: 0;" class="colNewWidgetTabBtn"><a style="font-size:12px; padding: 10px; text-align: center;" href="#tabColumnWidgetsPageOps" class="pluginops-tab2_link"> <i class="fa  fa-plus-square" style="font-size: 20px;"></i> <br> New Widget</a></li>
          <li style="margin: 0;" class="customFontsTabBtn"><a style="font-size:12px; padding: 10px; text-align: center;" href="#tabCustomFonts" class="pluginops-tab2_link"> <i class="fas fa-font" style="font-size: 20px;"></i> <br>Custom Fonts</a></li>
        </ul>
        <div class="pluginops-tab2-content">
          <div id="tabPageOptions" class="pluginops-tab2 active" style="min-height:400px;">
            <div class="pluginops-tabs2">
              <ul class="pluginops-tab2-links" style="background: #2fa8f9;">
                <li style="margin: 0;" class="active"><a href="#bodyStyleTab" class="pluginops-tab2_link">Body Style</a></li>
                <li style="margin: 0;"><a href="#PoGlobalStylestab" class="pluginops-tab2_link">Global Styles</a></li>
                <li style="margin: 0;"><a href="#PocustomCSStab" class="pluginops-tab2_link">Custom CSS & JS</a></li>
                <!-- <li style="margin: 0;"><a href="#PocustomJStab" class="pluginops-tab2_link">Custom JS</a></li> -->
                <li style="margin: 0; display: none;"><a href="#PocustomFontstab" class="pluginops-tab2_link">Custom Fonts</a></li>
              </ul>
              <div class="pluginops-tab2-content" style="overflow: hidden; background: #fff;">
                <div id="bodyStyleTab" class="pluginops-tab2 active">
                  <div class="pbp_form" style="min-height: 400px;padding:20px;">

                    <div class="PB_accordion" style="margin-left: -10px;">
                      
                      <h4>Background</h4>
                      <div>
                        
                        <div>
                          <div class="pluginops-tabs2">
                            <ul class="pluginops-tab2-links tabEditFields" style="display: none;">
                              <li class="active"><a  href="#defaultbodyBgOptions" class="pluginops-tab2_link">Default</a></li>
                              <li><a  href="#hoverbodyBgOptions" class="pluginops-tab2_link">Hover</a></li>
                            </ul>
                            <div class="pluginops-tab2-content" style="box-shadow: none;">
                              <div id="defaultbodyBgOptions" class="pluginops-tab2 active">
                                <br><br>
                                <div id="pluginops_input_tabs" class="popbinputTabsWrapper POPBInputNormalbody">
                                  <p style="display: inline;"> Background Type </p>
                                  <div class="iputTabNav">
                                    <div class="popbNavItem" data-inptabID='content_popb_tab_1' title="Simple">
                                      <label for="inputID1"> <i class="fa fa-paint-brush"></i></label>
                                      <input type="radio" name="bodyBackgroundType" id="inputID1" value='solid' class="bodyBackgroundType bodyBackgroundTypeSolid tabbedInputRadio pageOpsField">
                                    </div>
                                    <div class="popbNavItem" data-inptabID='content_popb_tab_2' title="Gradient">
                                      <label for="inputID2 " class="GradientIcon"> <i class="fa fa-square"></i></label>
                                      <input type="radio" name="bodyBackgroundType" id="inputID2" class="bodyBackgroundType bodyBackgroundTypeGradient tabbedInputRadio pageOpsField" value="gradient">
                                    </div>
                                  </div>
                                  <div class="popb_input_tabContent">
                                    <div class="content_popb_tab_1 popb_tab_content">
                                      <br><br><br>
                                      <label>Color :</label>
                                      <input type="text" name="pageBgColor" class="color-picker_btn_two pageBgColor pageOpsField" data-alpha='true' id="pageBgColor" value='#fff'>
                                      <br> <br>
                                      <label>Image :</label>
                                      <input id="image_location_b" type="url" class=" pageBgImage upload_image_button0 pageOpsField"  name='lpp_add_img_0' value=' ' placeholder='Insert Image URL here' style="width:40%;" />
                                <label></label>
                                <input id="image_location_b" type="button" class="upload_bg0 pb_upload_btn" data-id="0" value="Upload"  />
                                      <br>
                                    </div>
                                    <div class="content_popb_tab_2 popb_tab_content">
                                      <br><br><br>
                                      <label>First Color </label>
                                      <input type="text" name="bodyGradientColorFirst" class="color-picker_btn_two bodyGradientColorFirst pageOpsField" data-alpha='true' id="bodyGradientColorFirst" value='#fff'>
                                      <p>Location</p>
                                      <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='bodyGradientLocationFirst'></div>
                                      <input type="number" class="bodyGradientLocationFirst pageOpsField">
                                      <br><br><hr>
                                      <br><br>
                                      <label>Second Color </label>
                                      <input type="text" name="bodyGradientColorSecond" class="color-picker_btn_two bodyGradientColorSecond pageOpsField" data-alpha='true' id="bodyGradientColorSecond" value='#fff'>
                                      <p>Location</p>
                                      <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='bodyGradientLocationSecond'></div>
                                      <input type="number" class="bodyGradientLocationSecond pageOpsField">
                                      <br><br>
                                      <hr>
                                      <br>
                                      <br>
                                      <label>Type </label>
                                      <select class="bodyGradientType pageOpsField">
                                        <option value="linear">Linear</option>
                                        <option value="radial">Radial</option>
                                      </select>
                                      <br>
                                      <br>
                                      <div class="bodyradialInput" style="">
                                        <br>
                                        <label>Position </label>
                                        <select class="bodyGradientPosition pageOpsField">
                                          <option value="center center">Center Center</option>
                                          <option value="center left">Center Left</option>
                                          <option value="center right">Center Right</option>
                                          <option value="top center">Top Center</option>
                                          <option value="top left">Top Left</option>
                                          <option value="top right">Top Right</option>
                                          <option value="bottom center">Bottom Center</option>
                                          <option value="bottom left">Bottom Left</option>
                                          <option value="bottom right">Bottom Right</option>
                                        </select>
                                        <br><br>
                                      </div>
                                      <div class="bodylinearInput" style="">
                                      <p>Angle</p>
                                        <div class="PoPbrangeSliderAngle PoPbnumberSlider" data-targetRangeInput='bodyGradientAngle'></div>
                                        <input type="number" class="bodyGradientAngle pageOpsField">
                                      </div>
                                      <br>
                                      <br>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div> 
                        </div>

                      </div>

                      <h4>Background Overlay</h4>
                      <div>
                          <div>
                              <div id="defaultbodyBgOptions">
                                <br>
                                <div id="pluginops_input_tabs" class="popbinputTabsWrapper POPBInputNormalbody">
                                  <p style="display: inline;"> Overlay Type </p>
                                  <div class="iputTabNav">
                                    <div class="popbNavItem" data-inptabID='content_popb_tab_1' title="Simple">
                                      <label for="inputID1"> <i class="fa fa-paint-brush"></i></label>
                                      <input type="radio" name="bodyOverlayBackgroundType" id="inputID1" value='solid' class="bodyOverlayBackgroundType bodyOverlayBackgroundTypeSolid tabbedInputRadio pageOpsField">
                                    </div>
                                    <div class="popbNavItem" data-inptabID='content_popb_tab_2' title="Gradient">
                                      <label for="inputID2 " class="GradientIcon"> <i class="fa fa-square"></i></label>
                                      <input type="radio" name="bodyOverlayBackgroundType" id="inputID2" class="bodyOverlayBackgroundType bodyOverlayBackgroundTypeGradient tabbedInputRadio pageOpsField" value="gradient">
                                    </div>
                                  </div>
                                  <div class="">
                                    <div class="content_popb_tab_1 popb_tab_content">
                                      <br><br><br>
                                      <label>Color :</label>
                                      <input type="text" name="bodyBgOverlayColor" class="color-picker_btn_two bodyBgOverlayColor pageOpsField" data-alpha='true' id="bodyBgOverlayColor" value='transparent'>
                                      <br> <br>
                                    </div>
                                    <div class="content_popb_tab_2 popb_tab_content">
                                      <br><br><br>
                                      <label>First Color </label>
                                      <input type="text" name="bodyOverlayGradientColorFirst" class="color-picker_btn_two bodyOverlayGradientColorFirst pageOpsField" data-alpha='true' id="bodyOverlayGradientColorFirst" value='#fff'>
                                      <p>Location</p>
                                      <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='bodyOverlayGradientLocationFirst'></div>
                                      <input type="number" class="bodyOverlayGradientLocationFirst pageOpsField">
                                      <br><br><hr>
                                      <br><br>
                                      <label>Second Color </label>
                                      <input type="text" name="bodyOverlayGradientColorSecond" class="color-picker_btn_two bodyOverlayGradientColorSecond pageOpsField" data-alpha='true' id="bodyOverlayGradientColorSecond" value='#fff'>
                                      <p>Location</p>
                                      <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='bodyOverlayGradientLocationSecond'></div>
                                      <input type="number" class="bodyOverlayGradientLocationSecond pageOpsField">
                                      <br><br>
                                      <hr>
                                      <br>
                                      <br>
                                      <label>Type </label>
                                      <select class="bodyOverlayGradientType pageOpsField">
                                        <option value="linear">Linear</option>
                                        <option value="radial">Radial</option>
                                      </select>
                                      <br>
                                      <br>
                                      <div class="radialInput" style="">
                                        <br>
                                        <label>Position </label>
                                        <select class="bodyOverlayGradientPosition pageOpsField">
                                          <option value="center center">Center Center</option>
                                          <option value="center left">Center Left</option>
                                          <option value="center right">Center Right</option>
                                          <option value="top center">Top Center</option>
                                          <option value="top left">Top Left</option>
                                          <option value="top right">Top Right</option>
                                          <option value="bottom center">Bottom Center</option>
                                          <option value="bottom left">Bottom Left</option>
                                          <option value="bottom right">Bottom Right</option>
                                        </select>
                                        <br><br>
                                      </div>
                                      <div class="linearInput" style="">
                                      <p>Angle</p>
                                        <div class="PoPbrangeSliderAngle PoPbnumberSlider" data-targetRangeInput='bodyOverlayGradientAngle'></div>
                                        <input type="number" class="bodyOverlayGradientAngle pageOpsField">
                                      </div>
                                      <br>
                                      <br>
                                    </div>
                                  </div>
                                </div>
                              </div>
                          </div>
                      </div>

                      <h4>Page Identity - (Logo etc..)</h4>
                      <div>
                        <label>Logo Image :</label>
                        <input id="image_location_b" type="url" class=" pageLogoUrl upload_image_button10 pageOpsField"  name='lpp_add_img_10' value=' ' placeholder='Insert Image URL here' style="width:40%;" />
                        <label></label>
                        <input id="image_location_b" type="button" class="upload_bg0 pb_upload_btn" data-id="10" value="Upload"  />
                        <br><br><br><br><br><br>
                        <label>FavIcon Image :</label>
                        <input id="image_location_b" type="url" class=" pageFavIconUrl upload_image_button9 pageOpsField"  name='lpp_add_img_9' value=' ' placeholder='Insert Image URL here' style="width:40%;" />
                        <label></label>
                        <input id="image_location_b" type="button" class="upload_bg0 pb_upload_btn" data-id="9" value="Upload"  />
                        <br><br><br><br><br><br>
                        <label>Featured Image :</label>
                        <input id="image_location_b" type="url" class=" pageSeofbOgImage upload_image_button9121 pageOpsField"  name='lpp_add_img_9121' value=' ' placeholder='Insert Image URL here' style="width:40%;" />
                        <label></label>
                        <input id="image_location_b" type="button" class="upload_bg0 pb_upload_btn" data-id="9121" value="Upload"  />
                        <br><br><br>
                      </div>

                      <h4>Body Padding</h4>
                      <div>
                        <div>
                          <h4>Body Padding    
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </h4>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <input type="number" name="pagePaddingTop" class=" pageOpsField padding_inline_inp pagePaddingTop" id="pagePaddingTop" value="0"  placeholder="Top">

                            <input type="number" name="pagePaddingBottom" class=" pageOpsField padding_inline_inp pagePaddingBottom" id="pagePaddingBottom"  value="0" placeholder="Botom">

                            <input type="number" name="pagePaddingLeft" class=" pageOpsField padding_inline_inp pagePaddingLeft" id="pagePaddingLeft"  value="0" placeholder="Left">
                            
                            <input type="number" name="pagePaddingRight" class=" pageOpsField padding_inline_inp pagePaddingRight" id="pagePaddingRight"  value="0" placeholder="Right">
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <input type="number" name="pagePaddingTopTablet" class=" pageOpsField padding_inline_inp  pagePaddingTopTablet " id="pagePaddingTopTablet"  placeholder="Top" >
                            
                            <input type="number" name="pagePaddingBottomTablet" class=" pageOpsField padding_inline_inp  pagePaddingBottomTablet " id="pagePaddingBottomTablet"  placeholder="Bottom">
                            
                            <input type="number" name="pagePaddingLeftTablet" class=" pageOpsField padding_inline_inp  pagePaddingLeftTablet " id="pagePaddingLeftTablet"  placeholder="Left">
                            
                            <input type="number" name="pagePaddingRightTablet" class=" pageOpsField padding_inline_inp  pagePaddingRightTablet " id="pagePaddingRightTablet"  placeholder="Right">
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <input type="number" name="pagePaddingTopMobile" class=" pageOpsField padding_inline_inp  pagePaddingTopMobile " id="pagePaddingTopMobile"  placeholder="Top" >
                            
                            <input type="number" name="pagePaddingBottomMobile" class=" pageOpsField padding_inline_inp  pagePaddingBottomMobile " id="pagePaddingBottomMobile"  placeholder="Bottom">
                            
                            <input type="number" name="pagePaddingLeftMobile" class=" pageOpsField padding_inline_inp  pagePaddingLeftMobile " id="pagePaddingLeftMobile"  placeholder="Left">
                            
                            <input type="number" name="pagePaddingRightMobile" class=" pageOpsField padding_inline_inp  pagePaddingRightMobile " id="pagePaddingRightMobile"  placeholder="Right">
                          </div>
                        </div>
                        <span class="ulp-note">The unit is percentage so set values accordingly.</span>
                        <br>
                        <br>
                        <br>
                      </div>

                      <h4>SEO</h4>
                      <div>
                        <label>Page Keywords <span class="text_small">(Separated with Commas)</span> :</label>
                        <input type="text" class="pageSeoKeywords" style="width:80%">
                        <br><br><br><br><br><hr><br><br>

                        <label> Short Page Description :</label>
                        <textarea class="pageSeoDescription" cols="40"></textarea>
                        <br><br><br><hr><br><br>

                        <label> Meta Tags <span style="font-size: 9px;"> (Enter custom meta tags here)</span></label>
                        <textarea class="pageSeoMetaTags" cols="40" rows="45"></textarea>
                        <br><br><br><br>

                      </div>

                    </div>
                    
                        
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                        
                  </div>
                </div>
                <div id="PocustomCSStab" class="pluginops-tab2">
                  <div class="pbp_form" style="min-height: 600px;padding:20px; width: 100%; min-width: 450px;">

                    <div class="PB_accordion" style="margin-left: -10px;">
                      <h4>Head Section Custom CSS <span style="font-size:10px;"> Without <\style> tags.</span> </h4>
                      <div>
                        <div style="height: 150px; margin-bottom: 150px; width: 390px;">
                          <textarea  class="pageOpsField POcustomCSS" style="width: 90%; min-height: 280px;"> </textarea>
                        </div>
                      </div>

                      <h4>Head Section Custom JS <span style="font-size:10px;"> Without <\script> tags.</span> </h4>
                      <div>
                        <div style="height: 150px; margin-bottom: 150px; width: 390px;">
                          <textarea  class="pageOpsField POcustomJS" style="width: 90%; min-height: 280px;"> </textarea>
                          
                        </div>
                      </div>
                        
                    </div>
                    
                  </div>
                </div>
                <div id="PocustomJStab" class="pluginops-tab2">
                  <div class="pbp_form" style="min-height: 400px;padding:20px; width: 100%; min-width: 450px;">
                        
                  </div>
                </div>
                <div id="PoGlobalStylestab" class="pluginops-tab2">
                  
                  <div class="pbp_form" style="min-height: 400px;padding:20px; width: 100%; min-width: 450px;">
                    <br>
                    <label>Enable Global Styles</label>
                    <select class="pageOpsField POPBDefaultsEnable">
                      <option value="false">Disable</option>
                      <option value="true">Enable</option>
                    </select>
                    <br><br><br><hr><br>

                    <div class="PB_accordion" style="margin-left: -10px;">
                      <h4> Global Font Sizes </h4>
                      <div style="width: 100%; background: #fff;" >
                        <h4>Global Font Sizes</h4>
                        <br><br>
                        <div>
                          <p>Heading Size (H1)   
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeHOne" id="typeSizeHOne">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeHOneTablet" id="typeSizeHOneTablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeHOneMobile" id="typeSizeHOneMobile">px
                          </div>
                        </div>
                        <br><br><br><hr>
                        <div>
                          <p>Sub Heading Size  (H2)
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeHTwo" id="typeSizeHTwo">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeHTwoTablet" id="typeSizeHTwoTablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeHTwoMobile" id="typeSizeHTwoMobile">px
                          </div>
                        </div>
                        <br><br><br><hr>
                        <div>
                          <p>Heading Size  (H3)
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH3" id="typeSizeH3">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH3Tablet" id="typeSizeH3Tablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH3Mobile" id="typeSizeH3Mobile">px
                          </div>
                        </div>
                        <br><br><br><hr>
                        <div>
                          <p>Heading Size  (H4)
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH4" id="typeSizeH4">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH4Tablet" id="typeSizeH4Tablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH4Mobile" id="typeSizeH4Mobile">px
                          </div>
                        </div>
                        <br><br><br><hr>
                        <div>
                          <p>Heading Size  (H5)
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH5" id="typeSizeH5">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH5Tablet" id="typeSizeH5Tablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH5Mobile" id="typeSizeH5Mobile">px
                          </div>
                        </div>
                        <br><br><br><hr>
                        <div>
                          <p>Heading Size  (H6)
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH6" id="typeSizeH6">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH6Tablet" id="typeSizeH6Tablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeH6Mobile" id="typeSizeH6Mobile">px
                          </div>
                        </div>
                        <br><br><br><hr>
                        <div>
                          <p>Paragraph Size
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeParagraph" id="typeSizeParagraph">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeParagraphTablet" id="typeSizeParagraphTablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeParagraphMobile" id="typeSizeParagraphMobile">px
                          </div>
                        </div>
                        <br><br><br><hr>
                        <div>
                          <p>Button Size
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeButton" id="typeSizeButton">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeButtonTablet" id="typeSizeButtonTablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeButtonMobile" id="typeSizeButtonMobile">px
                          </div>
                        </div>
                        <br><br><br><hr>
                        <div>
                          <p>Anchor Link Size
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </p>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeAnchorLink" id="typefaceAnchorLink">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeAnchorLinkTablet" id="typeSizeAnchorLinkTablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="pageOpsField typeSizeAnchorLinkMobile" id="typeSizeAnchorLinkMobile">px
                          </div>
                        </div>
                        <br><br><br><hr><br>
                        <br>
                        <br>
                      </div>
                      <h4> Global Font Family Options </h4>
                      <div style="width: 100%; background: #fff;" >
                        <label>H1 Font Family :</label>
                        <input class="pageOpsField typefaceHOne gFontSelectorulpb" id="typefaceHOne">
                        <br><br><br><hr><br>
                        <label>H2 Font Family :</label>
                        <input class="pageOpsField typefaceHTwo gFontSelectorulpb" id="typefaceHTwo">
                        <br><br><br><hr><br>
                        <label>H3 Font Family :</label>
                        <input class="pageOpsField typefaceH3 gFontSelectorulpb" id="typefaceHTwo">
                        <br><br><br><hr><br>
                        <label>H4 Font Family :</label>
                        <input class="pageOpsField typefaceH4 gFontSelectorulpb" id="typefaceHTwo">
                        <br><br><br><hr><br>
                        <label>H5 Font Family :</label>
                        <input class="pageOpsField typefaceH5 gFontSelectorulpb" id="typefaceHTwo">
                        <br><br><br><hr><br>
                        <label>H6 Font Family :</label>
                        <input class="pageOpsField typefaceH6 gFontSelectorulpb" id="typefaceHTwo">
                        <br><br><br><hr><br>
                        <label>H2 Font Family :</label>
                        <input class="pageOpsField typefaceHTwo gFontSelectorulpb" id="typefaceHTwo">
                        <br><br><br><hr><br>
                        <label>Paragraph Font :</label>
                        <input class="pageOpsField typefaceParagraph gFontSelectorulpb" id="typefaceParagraph">
                        <br><br><br><hr><br>
                        <label>Button Font :</label>
                        <input class="pageOpsField typefaceButton gFontSelectorulpb" id="typefaceButton">
                        <br><br><br><hr><br>
                        <label>Anchor Link Font :</label>
                        <input class="pageOpsField typefaceAnchorLink gFontSelectorulpb" id="typefaceAnchorLink">
                      </div>
                      
                    </div>
                    
                    <br><br><br><hr><br>
                        
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div id="tabColumnWidgetsPageOps" class="pluginops-tab2" style="min-height:550px;">
                <div class="edit_column_widgets">
                    <div class="pluginops-tabs2">
                      <ul class="pluginops-tab2-links">
                      </ul>
                        <div class="pluginops-tab2-content" style="padding:10px 0px 15px 15px; background: #fff; min-height: 210px;">
                            <input type="text" class="pbSearchWidget" placeholder="Search a widget" style="width: 90%;">
                            <?php
                                $proWidgetLoaded = false;
                                if (is_plugin_active( 'PluginOps-Extensions-Pack/extension-pack.php' )  ) {
                                    if (function_exists('ulpb_available_pro_widgets') ) {
                                      $proWidgetLoaded = true;
                                      ?>


                                    <div style="display: inline-block; width: 49%; float: left;">
                            
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-liveText"><i class="fa fa-edit"></i> <br>Text Editor</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-img"><i class="fa fa-picture-o"></i> <br> Image</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-navmenu"><i class="fa fa-navicon"></i> <br>Nav Builder</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-menu"><i class="fa fa-navicon"></i> <br> Menu</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-formBuilder"> <i class="fab fa-wpforms"></i> <br> Form Builder</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-video"> <i class="fa fa-video-camera"></i> <br>  Video</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-audio"> <i class="fa fa-file-audio-o"></i> <br>  Audio</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-anchor"> <i class="fa fa-anchor"></i> <br> Anchor </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-shortcode"> <i class="fa fa-code"></i> <br> ShortCode</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-break"> <i class="fa fa-ellipsis-h"></i> <br> Break </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-shareThis"><i class="fa fa-share"></i> <br> Share This </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-imageSlider"><i class="fa fa-file-image-o"></i> <br> Image Slider</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-pricing"><i class="fa fa-tags"></i> <br> Pricing</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-imgCarousel"><i class="fa fa-image"></i><i class="fa fa-image"></i>  <br> Image Carousel</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-iconList"> <i class="fa fa-list"></i> <br> Icon List</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-testimonialCarousel"><i class="fa fa-navicon"></i> <br> Testimonial Slider</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-popupClose"><i class="fa fa-remove"></i> <br> PopUp Close</div>

                                    </div>
                                    <div style="display: inline-block; width: 49%;">

                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-text"><i class="fa fa-text-width"></i> <br> Heading </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-btn-gen"><i class="fa fa-mouse-pointer"></i> <br> Button</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-gallery"><i class="fa fa-image"></i> <i class="fa fa-image"></i> <br>Image Gallery</div>
                                        <div class="widget POPB_widget wdt-draggable wdt_draggable_removed" data-type="wigt-pb-form"> <i class="fa fa-envelope-o"></i> <br> Subscribe Form</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-poOptins"> <i class="fa fa-puzzle-piece"></i> <br> <span style="font-size: 12px;">Pluginops Optin </span> </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-postSlider"><i class="fa fa-file-image-o"></i> <br> Posts Slider</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-embededVideo"> <i class="fab fa-youtube"></i> <br> Embed Video </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-accordion"> <i class="fas fa-chevron-down"></i> <br> Accordion </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-tabs"> <i class="fas fa-square"></i> <br> Tabs </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-icons"><i class="fab fa-fonticons"></i> <br> Icons</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-cards"><i class="fab fa-fonticons"></i> <br> Card</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-WYSIWYG"><i class="fa fa-file-text-o"></i> <br> HTML Editor</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-testimonial"><i class="fa fa fa-quote-left"></i> <br> Testimonial</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-spacer"> <i class="fa fa-arrows-v"></i> <br> Spacer </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-progressBar"><i class="fa fa-align-left"></i> <br> Progress Bar </div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-countdown"><i class="fa fa-sort-numeric-desc"></i> <br> Countdown</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-counter"> <i class="fa fa-sort-numeric-desc"></i> <br> Counter</div>
                                        <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-wooCommerceProducts"> <i class="fa fa-shopping-cart"></i> <br> WooCommerce Products</div>
                                        
                                    </div>

                                      <?php
                                    }
                                }
                                if($proWidgetLoaded == false) {
                                  
                                  ?>
                                      
                                    <div style="display: inline-block; width: 49%; float: left;">
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-liveText"><i class="fa fa-edit"></i> <br>Text Editor</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-img"><i class="fa fa-picture-o"></i> <br> Image</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-navmenu"><i class="fa fa-navicon"></i> <br>Nav Builder</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-menu"><i class="fa fa-navicon"></i> <br> Menu</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-formBuilder"> <i class="fab fa-wpforms"></i> <br> Form Builder</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-video"> <i class="fa fa-video-camera"></i> <br>  Video</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-audio"> <i class="fa fa-file-audio-o"></i> <br>  Audio</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-anchor"> <i class="fa fa-anchor"></i> <br> Anchor </div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-break"> <i class="fa fa-ellipsis-h"></i> <br> Break </div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-shareThis"><i class="fa fa-share"></i> <br> Share This </div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-shortcode"><i class="fa fa-code"></i> <br> ShortCode </div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-popupClose"><i class="fa fa-remove"></i> <br> PopUp Close</div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-imageSlider"><i class="fa fa-file-image-o"></i> <br> Image Slider <p>Pro Only</p></div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-pricing"><i class="fa fa-tags"></i> <br> Pricing <p>Pro Only</p></div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-imgCarousel"><i class="fa fa-image"></i><i class="fa fa-image"></i>  <br> Image Carousel <p>Pro Only</p></div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-countdown"><i class="fa fa-sort-numeric-desc"></i> <br> Countdown <p>Pro Only</p></div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-wooCommerceProducts"> <i class="fa fa-shopping-cart"></i> <br> WooCommerce Products <p>Pro Only</p></div>
                                    </div>
                                    <div style="display: inline-block; width: 49%;">
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-text"><i class="fa fa-text-width"></i> <br> Heading </div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-btn-gen"><i class="fa fa-mouse-pointer"></i> <br> Button</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-gallery"><i class="fa fa-image"></i> <i class="fa fa-image"></i> <br>Image Gallery</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-poOptins"> <i class="fa fa-puzzle-piece"></i> <br> <span style="font-size: 12px;">Pluginops Optin </span> </div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-postSlider"><i class="fa fa-file-image-o"></i> <br> Posts Slider</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-embededVideo"> <i class="fab fa-youtube"></i> <br> Embed Video </div>
                                      <div class="widget POPB_widget wdt-draggable ui-draggable ui-draggable-handle" data-type="wigt-pb-accordion" style="display: block;"> <i class="fas fa-chevron-down" aria-hidden="true"></i> <br> Accordion </div>
                                      <div class="widget POPB_widget wdt-draggable ui-draggable ui-draggable-handle" data-type="wigt-pb-tabs" style="display: block;"> <i class="fas fa-square" aria-hidden="true"></i> <br> Tabs </div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-icons"><i class="fab fa-fonticons"></i> <br> Icons</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-spacer"> <i class="fa fa-arrows-v"></i> <br> Spacer </div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-WYSIWYG"><i class="fa fa-file-text-o"></i> <br> HTML Editor</div>
                                      <div class="widget POPB_widget wdt-draggable" data-type="wigt-pb-iconList"> <i class="fa fa-list"></i> <br> Icon List</div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-testimonialCarousel"><i class="fa fa-quote-left"></i> <br> Testimonial Slider <p>Pro Only</p></div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-cards"><i class="fab fa-fonticons"></i> <br> Card <p>Pro Only</p></div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-testimonial"><i class="fa fa fa-quote-left"></i> <br> Testimonial <p>Pro Only</p></div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-progressBar"><i class="fa fa-align-left"></i> <br> Progress Bar <p>Pro Only</p></div>
                                      <div class="widget POPB_widget prem-widget" data-widgType="wigt-pb-counter"> <i class="fa fa-sort-numeric-desc"></i> <br> Counter <p>Pro Only</p></div>
                                                                         

                                    </div>

                                    <br><br><br><br><br><hr><br> <a href="https://pluginops.com/page-builder/?ref=widgets" target="_blank" class="premiumNoticeWidget" style="padding:5px 10px; text-decoration: none; font-size: 17px; text-align: center; color: #fff; background:#8BC34A; border-radius: 3px;"> Unlock All These Amazing Widgets </a> <br>
                                  <?php

                                }
                            ?> 

                            <div style="display: inline-block; width: 49%; float: left;"> </div>

                            <div style="display: inline-block; width: 49%;"> </div>
                        </div>
                    
                    </div>
                </div>
          </div>

          <div id="tabCustomFonts" class="pluginops-tab3" style="min-height:550px; display: none;">

            <div class="pbp_form" style="min-height: 400px;width:380px; margin-top: 20px;">
              
              <div class="btn btn-green" id="updateFonts" style="float:left;"> <span class="dashicons dashicons-cloud-upload"></span> Update Fonts </div>

              <div class="btn btn-blue" id="addNewCustomFont"> <span class="dashicons dashicons-plus-alt"></span> Add New Font </div>
              <br><br>
              <ul class="customFontsItemsContainer"></ul>
              <br>

              <div class="customFontsStylesContainer">
                
                <style></style>

              </div>

            </div>

          </div>
          
        </div>
      </div>

            



    </div>
  </div>
</div>

    <div  class="openPageOpsBtn" style="">
        <div ><span  class="dashicons dashicons-arrow-right editSaveVisibleIcon" ></span></div><p></p><br>
    </div>

    <div  class="closePageOpsBtn" style="z-index:9999; display: none; ">
        <div ><span class="dashicons dashicons-arrow-left editSaveVisibleIcon" ></span></div><p></p><br>
    </div>

    <div  class="SPopen-btn" style="">
        <div ><span  class="dashicons dashicons-arrow-left editSaveVisibleIcon" ></span></div><p></p><br>
    </div>

    <div  class="SPclose-btn" style="">
        <div ><span  class="dashicons dashicons-arrow-right editSaveVisibleIcon" ></span></div><p></p><br>
    </div>
    