<div class="pbp_form" style=" background: #fff; padding:20px 5px 20px 15px; width: 100%;">
    <form id="imgWidgetOpsForm">
    <div class="pluginops-tabs2" style="width: 100%;">
      <ul class="pluginops-tab2-links">
        <li class="active"><a href="#imgWidgetTab1" class="pluginops-tab2_link">Image</a></li>
        <li><a href="#ImgWidgetTab2" class="pluginops-tab2_link">Caption</a></li>
        <!-- <li><a href="#ImgWidgetTab3" class="pluginops-tab2_link">Hover</a></li> -->
      </ul>
        <div class="pluginops-tab2-content" style="box-shadow:none;">
            <div id="imgWidgetTab1" class="pluginops-tab2 active">

                <br><br>

                <div class="PB_accordion" style="margin-left: -10px;">
                    <h4> Image Options </h4>
                    <div>
                        <div class="selectImagePreviewContainer">
                            <img src="#" class="selectImagePreview">
                        </div>
                        <input id="image_location1" type="text" class=" imgUrl ftr-img upload_image_button2" name='lpp_add_img_1' value=' ' placeholder='Insert Image URL here' data-optname="imgUrl" style="width:95%;" />
                        <label></label>
                        <input id="image_location1" type="button" class="upload_bg" data-id="2" value="SELECT IMAGE" style="width:95%;" />
                        <br><br><br><br><br><br><br><hr><br>

                        <label>Alt Text :</label>
                        <input type="text" class="imgAlt altTextField" data-optname="imgAlt">
                        <br><br><br><hr><br>

                        <label>Select Size :</label>
                        <select class="imgSize" id="imgSize" data-optname="imgSize">
                            <option value="">Select</option>
                            <option value="original">Original</option>
                            <option value="large">Large</option>
                            <option value="medium">Medium</option>
                            <option value="small">Small</option>
                            <option value="custom">Custom</option>
                        </select>
                        <br><br><hr><br>

                        <div style="display: none;" class="customImageSizeDiv">
                            <h3>Custom Image Size <span style="font-size: 12px;">(Pixels)</span></h3><br>
                            <div>
                                <label> Width
                                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                </label>
                                <div class="responsiveOps responsiveOptionsContainterLarge">
                                    <input type="number" class="imgSizeCustomWidth" id="imgSizeCustomWidth" style="width:18%;" data-optname="imgSizeCustomWidth">
                                </div>
                                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                    <input type="number" class="imgSizeCustomWidthTablet" id="imgSizeCustomWidthTablet" style="width:18%;" data-optname="imgSizeCustomWidthTablet">
                                </div>
                                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                    <input type="number" class="imgSizeCustomWidthMobile" id="imgSizeCustomWidthMobile" style="width:18%;" data-optname="imgSizeCustomWidthMobile">
                                </div>
                            </div>
                            <br><br><br><hr><br>

                            <div>
                                <label> Height
                                    <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                    <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                    <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                                </label>
                                <div class="responsiveOps responsiveOptionsContainterLarge">
                                    <input type="number" class="imgSizeCustomHeight" id="imgSizeCustomHeight" style="width:18%;" data-optname="imgSizeCustomHeight">
                                </div>
                                <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                    <input type="number" class="imgSizeCustomHeightTablet" id="imgSizeCustomHeightTablet" style="width:18%;" data-optname="imgSizeCustomHeightTablet">
                                </div>
                                <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                    <input type="number" class="imgSizeCustomHeightMobile" id="imgSizeCustomHeightMobile" style="width:18%;" data-optname="imgSizeCustomHeightMobile">
                                </div>
                            </div>
                            <br><br><br><hr><br>

                        </div>

                        <div>
                            <h4> Image Alignment 
                                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                            </h4>
                            <div class="responsiveOps responsiveOptionsContainterLarge">
                                <label></label>
                                <select class="imgAlignment" data-optname="imgAlignment">
                                    <option value="default">Default</option>
                                    <option value="left">Left</option>
                                    <option value="right">Right</option>
                                    <option value="center">Center</option>
                                </select>
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                <label></label>
                                <select class="imgAlignmentTablet" data-optname="imgAlignmentTablet">
                                    <option value="default">Default</option>
                                    <option value="left">Left</option>
                                    <option value="right">Right</option>
                                    <option value="center">Center</option>
                                </select>
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                <label></label>
                                <select class="imgAlignmentMobile" data-optname="imgAlignmentMobile">
                                    <option value="default">Default</option>
                                    <option value="left">Left</option>
                                    <option value="right">Right</option>
                                    <option value="center">Center</option>
                                </select>
                            </div>
                        </div>

                        <br><br><hr><br>
                        <label>Image Link :</label>
                        <input type="url" id="imgLink" class="imgLink" data-optname="imgLink">
                        <br><br><hr><br>

                        <label>Open Link :</label>
                        <select class="imgLinkOpen" data-optname="imgLinkOpen">
                            <option value="">Select</option>
                            <option value="_self">Same Tab</option>
                            <option value="_blank">New Tab</option>
                        </select>
                        <br><br><hr><br>

                    </div>

                    <h4>Image Border & Box Shadow</h4>
                    <div>
                        <div style="width: 100%;">
                            <br>
                            <p>Border Width </p>
                            <div>
                                <input type="number" name="iwbwt" class="padding_inline_inp linkedField  iwbwt " id="iwbwt" placeholder="Top" data-optname="iborderWidth.iwbwt">

                                <input type="number" name="iwbwr" class="padding_inline_inp linkedField  iwbwr " id="iwbwr" placeholder="Right" data-optname="iborderWidth.iwbwr">

                                <input type="number" name="iwbwb" class="padding_inline_inp linkedField  iwbwb " id="iwbwb" placeholder="Bottom" data-optname="iborderWidth.iwbwb">

                                <input type="number" name="iwbwl" class="padding_inline_inp linkedField  iwbwl " id="iwbwl" placeholder="Left" data-optname="iborderWidth.iwbwl">

                                <span class="linkfieldBtn linkBtn"> <i class="fa fa-link"></i> </span>
                            </div>
                            <br>
                            <br>
                            <br>
                            <label>Set Border Style: </label>
                            <select class="iwbs" data-optname="iwbs">
                                <option value="">Select</option>
                                <option value="solid">Solid</option>
                                <option value="dotted">Dotted</option>
                                <option value="dashed">Dashed</option>
                                <option value="double">Double</option>
                            </select>
                            <br>
                            <br>
                            <br>
                            <label>Set Border Color : </label>
                            <input type="text" id="iwbc" class="color-picker_btn_two iwbc" data-alpha='true' data-optname="iwbc">
                            <br>
                            <br>
                            <p>Corner Radius: </p>
                            <div>
                                <input type="number" name="iwbrt" class="padding_inline_inp linkedField  iwbrt " id="iwbrt" placeholder="Top Left" data-optname="iborderRadius.iwbrt">

                                <input type="number" name="iwbrr" class="padding_inline_inp linkedField  iwbrr " id="iwbrr" placeholder="Top Right" data-optname="iborderRadius.iwbrr">

                                <input type="number" name="iwbrb" class="padding_inline_inp linkedField  iwbrb " id="iwbrb" placeholder="Bottom Right" data-optname="iborderRadius.iwbrb">

                                <input type="number" name="iwbrl" class="padding_inline_inp linkedField  iwbrl " id="iwbrl" placeholder="Bottom Left" data-optname="iborderRadius.iwbrl">

                                <span class="linkfieldBtn linkBtn"> <i class="fa fa-link"></i> </span>
                            </div>
                            <br>
                            <br>
                            <hr>
                            <p>Box Shadow</p>
                            <br>
                            <label>Shadow Horizontal Position : </label>
                            <input type="number" class="iwbsh" data-optname="iwbsh"> px
                            <br>
                            <br>
                            <label>Shadow Vertcal Position : </label>
                            <input type="number" class="iwbsv" data-optname="iwbsv"> px
                            <br>
                            <br>
                            <label>Shadow Distance (Blur) : </label>
                            <input type="number" class="iwbsb" data-optname="iwbsb"> px
                            <br>
                            <br>
                            <br>
                            <label>Shadow Color : </label>
                            <input type="text" id="iwbsc" class="color-picker_btn_two iwbsc" data-alpha='true' data-optname="iwbsc">
                        </div>
                    </div>

                    <h4>Lightbox</h4>
                    <div>
                        <label>Lightbox :</label>
                        <select class="imgLightBox" id="imgLightBox" data-optname="imgLightBox">
                            <option value="">Select</option>
                            <option value="true">Enable</option>
                            <option value="false">Disable</option>
                        </select>
                    </div>
                </div>
                  
            </div>
            <div id="ImgWidgetTab2" class="pluginops-tab2">
                <br><br>
                <div class="PB_accordion" style="margin-left: -10px;">
                    
                    <h4>Text</h4>
                    <div>

                        <label>Display Caption </label>
                        <select class="imgwccdis" data-optname="imgwccdis" >
                            <option value="">Select</option>
                            <option value="always">Always</option>
                            <option value="hidden">Hidden</option>
                            <option value="on hover">On Hover</option>
                            <option value="on click">On Click</option>
                        </select>
                        <br><br><hr><br>

                        <label>Caption </label>
                        <br>
                        <textarea class="imgwcap" data-optname="imgwcap" style="width: 80%; height:110px;"></textarea>
                        <br><br><br><hr><br>
                        

                        <label>Text Color </label>
                        <input type="text" class="color-picker_btn_two imgwctc" id="imgwctc" data-optname="imgwctc" style="margin-left: 200px;">
                        <br><br><hr><br>

                        <label>Font family :</label>
                        <label></label>
                        <input class="imgwctff gFontSelectorulpb" id="imgwctff" data-optname="imgwctff">
                        <br><br><hr><br>


                        <div>
                            <label>Text Size
                                <span class="responsiveBtn rbt-l "> <i class="fa fa-desktop"></i> </span>
                                <span class="responsiveBtn rbt-m "> <i class="fa fa-tablet"></i> </span>
                                <span class="responsiveBtn rbt-s "> <i class="fa fa-mobile-phone"></i> </span>
                            </label>
                            <div class="responsiveOps responsiveOptionsContainterLarge">
                                <input type="number" class="imgwcts" data-optname='imgwcts' style="width:60px;">
                                <select class="imgwctsu" style="width:50px;" data-optname="imgwctsu">
                                    <option value="px">px</option>
                                    <option value="em">em</option>
                                    <option value="rem">rem</option>
                                    <option value="vh">vw</option>
                                </select>
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                <input type="number" class="imgwctsT" data-optname='imgwctsT' style="width:60px;">
                                <select class="imgwctsuT" style="width:50px;" data-optname="imgwctsuT">
                                    <option value="px">px</option>
                                    <option value="em">em</option>
                                    <option value="rem">rem</option>
                                    <option value="vh">vw</option>
                                </select>
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                <input type="number" class="imgwctsM" data-optname='imgwctsM' style="width:60px;">
                                <select class="imgwctsuM" style="width:50px;" data-optname="imgwctsuM">
                                    <option value="px">px</option>
                                    <option value="em">em</option>
                                    <option value="rem">rem</option>
                                    <option value="vh">vw</option>
                                </select>
                            </div>
                        </div>
                        <br><br><hr><br>

                        <div>
                            <label>Alignment (Horizontal)
                                <span class="responsiveBtn rbt-l "> <i class="fa fa-desktop"></i> </span>
                                <span class="responsiveBtn rbt-m "> <i class="fa fa-tablet"></i> </span>
                                <span class="responsiveBtn rbt-s "> <i class="fa fa-mobile-phone"></i> </span>
                            </label>
                            <div class="responsiveOps responsiveOptionsContainterLarge">
                                <select class="imgwcta" data-optname="imgwcta" >
                                    <option value="">Select</option>
                                    <option value="left">Left</option>
                                    <option value="center">Center</option>
                                    <option value="right">Right</option>
                                </select>
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                <select class="imgwctaT" data-optname="imgwctaT" >
                                    <option value="">Select</option>
                                    <option value="left">Left</option>
                                    <option value="center">Center</option>
                                    <option value="right">Right</option>
                                </select>
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                <select class="imgwctaM" data-optname="imgwctaM" >
                                    <option value="">Select</option>
                                    <option value="left">Left</option>
                                    <option value="center">Center</option>
                                    <option value="right">Right</option>
                                </select>
                            </div>
                        </div>
                        <br><br><hr><br>
                        <label>Alignment (Vertical) </label>
                        <select class="imgwctav" data-optname="imgwctav" >
                            <option value="center">Select</option>
                            <option value="flex-start">Top</option>
                            <option value="center">Middle</option>
                            <option value="flex-end">Bottom</option>
                        </select>
                        <br><br><hr><br>
                    </div>

                    <h4>Container</h4>
                    <div>

                        <div>
                            <label>Width
                                <span class="responsiveBtn rbt-l "> <i class="fa fa-desktop"></i> </span>
                                <span class="responsiveBtn rbt-m "> <i class="fa fa-tablet"></i> </span>
                                <span class="responsiveBtn rbt-s "> <i class="fa fa-mobile-phone"></i> </span>
                            </label>
                            <div class="responsiveOps responsiveOptionsContainterLarge">
                                <input type="number" class="imgwccw" data-optname='imgwccw' style="width:60px;">
                                <select class="imgwccwu" style="width:50px;" data-optname="imgwccwu">
                                    <option value="px">px</option>
                                    <option value="%">%</option>
                                    <option value="vh">vw</option>
                                </select>
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                                <input type="number" class="imgwccwT" data-optname='imgwccwT' style="width:60px;">
                                <select class="imgwccwuT" style="width:50px;" data-optname="imgwccwuT">
                                    <option value="px">px</option>
                                    <option value="%">%</option>
                                    <option value="vh">vw</option>
                                </select>
                            </div>
                            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                                <input type="number" class="imgwccwM" data-optname='imgwccwM' style="width:60px;">
                                <select class="imgwccwuM" style="width:50px;" data-optname="imgwccwuM">
                                    <option value="px">px</option>
                                    <option value="%">%</option>
                                    <option value="vh">vw</option>
                                </select>
                            </div>
                        </div>
                        <br><br><hr><br>

                        <label>Height </label>
                        <select class="imgwcch" data-optname="imgwcch" >
                            <option value="">Auto</option>
                            <option value="Fit Content">Fit Content</option>
                            <option value="100%">Cover (Full Height)</option>
                            <option value="70%">70%</option>
                            <option value="60%">60%</option>
                            <option value="50%">50%</option>
                            <option value="40%">40%</option>
                            <option value="30%">30%</option>
                            <option value="20%">20%</option>
                        </select>
                        <br><br><hr><br>

                        <label>Border Radius (px) </label>
                        <input type="number" class="imgwccbr" data-optname="imgwccbr" >
                        <br><br><hr><br>

                        <label>Alignment (Vertical) </label>
                        <select class="imgwccav" data-optname="imgwccav" >
                            <option value="">Select</option>
                            <option value="top">Top</option>
                            <option value="middle">Middle</option>
                            <option value="bottom">Bottom</option>
                        </select>
                        <br><br><hr><br>

                        <label>Alignment (Horizontal) </label>
                        <select class="imgwccah" data-optname="imgwccah" >
                            <option value="">Select</option>
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                        <br><br><hr><br>

                        <label>Background </label>
                        <input type="text" class="color-picker_btn_two imgwccbg" id="imgwccbg" data-optname="imgwccbg" >

                    </div>

                </div>

            </div>

            <div id="ImgWidgetTab3" class="pluginops-tab2">
                
            </div>



        </div>
    </div>
    </form>

                

</div>