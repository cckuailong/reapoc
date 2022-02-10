<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="lpp_modal_2 columnWidgetPopup">
  <div class="lpp_modal_wrapper_widget">
  <div class="edit_form" style="width: 100% !important; overflow: hidden !important;">
      <div class="pluginops-tabs2" style="background: #fff;">
        <ul class="pluginops-tab2-links" style="background: #2fa8f9;">
          <li style="margin: 0;"  class="active"><a style="font-size:12px; padding: 10px 25px; text-align: center;" href="#tabBasicWidgetOptions" class="pluginops-tab2_link"> <i class="fa fa-puzzle-piece" style="font-size: 20px;"></i> <br>Widget</a></li>
          <li style="margin: 0;"><a style="font-size:12px; padding: 10px; text-align: center;" href="#tabAdvancedWidgetOptions" class="pluginops-tab2_link tabAdvancedWidgetOptionsTab"> <i class="fa fa-paint-brush tabAdvancedWidgetOptionsTab" style="font-size: 20px;"></i> <br> Advanced Options</a></li>
          <li style="margin: 0;"><a style="font-size:12px; padding: 10px; text-align: center;" href="#tabWidgetBlocks" class="pluginops-tab2_link"> <i class="fab fa-pagelines" style="font-size: 20px;"></i> <br> Widget Blocks</a></li>
        </ul>
        <div class="pluginops-tab2-content" style="box-shadow: none;">
          <div id="tabBasicWidgetOptions" class="pluginops-tab2 active" style="min-height:400px;">
            <div class="wdt-left" style="width: 100% !important;">
              <?php
                if (!function_exists('ulpb_available_pro_widgets') ) {
                  echo "<p class='premWidgetNotice' style='display:none;'><i>Note : </i> This is a pro widget and it will not work for free version users. </p>";
                }
              ?>
              <br>
              <div class="pbp-widgets wdt-editor" style="display:none">
                  <textarea  id="columnContent"  class="columnContent" data-optname='columnContent' name="columnContent" rows="10" style="width: 97%;"></textarea>
              </div>
              <div class="pbp-widgets wdt-accordion pbp_form" style="display:none">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-accordion.php'); ?>
              </div>
              <div class="pbp-widgets wdt-tabs pbp_form" style="display:none">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-tabs.php'); ?>
              </div>
              <div class="pbp-widgets wdt-img" style="display:none">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/image-widget.php'); ?>
              </div>
              <div class="pbp-widgets wdt-menu" style="display:none">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-menu.php'); ?>
              </div>
              <div class="pbp-widgets wdt-pb-liveText" style="display:none">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/live-text.php'); ?>
              </div>
              <div class="pbp-widgets wdt-btn-gen" style="display:none; ">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-button.php'); ?>
              </div>
              
              <div class="pbp-widgets wdt-pb-form pbp_form" style="display: none;">
                  <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-form.php'); ?>
              </div>
              <div class="pbp-widgets wdt-video pbp_video" style="display: none;">
                <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
                  <br>
                  <br>
                  <label>Video (MP4) :</label>
                  <input id="image_location7" type="text" class="videoMpfour upload_image_button7"  name='lpp_add_img_1' value='' placeholder='Insert Video URL here' style="width:40%;" />
                  <label></label>
                  <input id="image_location7" type="button" class="upload_bg" data-id="7" value="Upload" />
                  <br><br><br><br><hr><br>
                  <label>Video (WebM) :</label>
                  <input id="image_location6" type="text" class="videoWebM upload_image_button6"  name='lpp_add_img_6' value='' placeholder='Insert Video URL here' style="width:40%;" />
                  <label></label>
                  <input id="image_location6" type="button" class="upload_bg" data-id="6" value="Upload" />
                  <br><br><br><br><hr><br>
                  <label>Video Thumbnail :</label>
                  <input id="image_location8" type="text" class="videoThumb upload_image_button8"  name='lpp_add_img_1' value='' placeholder='Insert Image URL here' style="width:40%;" />
                  <label></label>
                  <input id="image_location8" type="button" class="upload_bg" data-id="8" value="Upload" />
                  <br><br><br><br><hr><br>
                  <label>Auto Play</label>
                  <select class="vidAutoPlay">
                    <option value="no">No</option>
                    <option value="autoplay">Yes</option>
                  </select>
                  <br><br><hr><br>
                  <label>Loop</label>
                  <select class="vidLoop">
                    <option value="no">No</option>
                    <option value="loop">Yes</option>
                  </select> 
                  <br><br><hr><br>
                  <label>Video Controls</label>
                  <select class="vidControls">
                    <option value="controls">Yes</option>
                    <option value="no">No</option>
                  </select> 
                </div>
              </div>
              <div class="pbp-widgets wdt-audio pbp_audio" style="display: none;">
                <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
                  <br>
                  <br>
                  <label>Audio (MP3) :</label>
                  <input id="image_location4" type="text" class="audioMpThree upload_image_button4"  name='lpp_add_img_1' value='' placeholder='Insert Audio URL here' style="width:40%;" />
                  <label></label>
                  <input id="image_location4" type="button" class="upload_bg" data-id="4" value="Upload" />
                  <br><br><br><br><hr><br>
                  <label>Audio (OGG) :</label>
                  <input id="image_location5" type="text" class="audioOgg upload_image_button5"  name='lpp_add_img_5' value='' placeholder='Insert Video URL here' style="width:40%;" />
                  <label></label>
                  <input id="image_location5" type="button" class="upload_bg" data-id="5" value="Upload" />
                  <br><br><br><br><hr><br>
                  <label>Auto Play</label>
                  <select class="audioAutoPlay">
                    <option value="no">No</option>
                    <option value="autoplay">Yes</option>
                  </select>
                  <br><br><hr><br>
                  <label>Loop</label>
                  <select class="audioLoop">
                    <option value="no">No</option>
                    <option value="loop">Yes</option>
                  </select> 
                  <br><br><hr><br>
                  <label>Audio Controls</label>
                  <select class="audioControls">
                    <option value="controls">Yes</option>
                    <option value="no">No</option>
                  </select> 
                </div>
              </div>
              <div class="pbp-widgets wdt-pbPostSlider pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/post-slider.php'); ?>
              </div>
              <div class="pbp-widgets wdt-icons pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/icons-widget.php'); ?>
              </div>
              <div class="pbp-widgets wdt-counter pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/counter-widget.php'); ?>
              </div>
              <div class="pbp-widgets wdt-card pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/card-widget.php'); ?>
              </div>
              <div class="pbp-widgets wdt-testimonial pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/testimonial-widget.php'); ?>
              </div>
              <div class="pbp-widgets wdt-shortcode pbp_form" style="display: none;">
                  <br>
                  <label>Enter Shortcode :</label>
                  <input type="text" class="shortCodeInput" id="shortCodeInput" style="width: 250px;">
                  <br>
                  <br>
                  <br>
                  <br>
                  <p style="font-size: 14px;"><i>Note :</i> Use onle one shortcode using multiple shortcodes in a single widget might cause errors. </p>
                  <br>
              </div>
              <div class="pbp-widgets wdt-countdown pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-countdown.php'); ?>
              </div>
              <div class="pbp-widgets wdt-imageSlider pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-slider.php'); ?>
              </div>
              <div class="pbp-widgets wdt-progressBar pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-progressBar.php'); ?>
              </div>
              <div class="pbp-widgets wdt-pricing pbp_form" style="display: none; width: ">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-pricing.php'); ?>
              </div>
              <div class="pbp-widgets wdt-imgCarousel pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-imgCarousel.php'); ?>
              </div>
              <div class="pbp-widgets wdt-wooCommerceProducts pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-wooCommerceProducts.php'); ?>
              </div>
              <div class="pbp-widgets wdt-iconList pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-iconList.php'); ?>
              </div>
              <div class="pbp-widgets wdt-formBuilder pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-formBuilder.php'); ?>
              </div>
              <div class="pbp-widgets wdt-spacer pbp_form" style="display: none;">
                <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">

                  <div>
                      <h4>Space 
                        <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                        <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                        <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                      </h4>
                      <div class="responsiveOps responsiveOptionsContainterLarge">
                          <label></label>
                        <input type="number" class="widgetVerticalSpaceValue"> px
                      </div>
                      <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                          <label></label>
                        <input type="number" class="widgetVerticalSpaceValueTablet"> px
                      </div>
                      <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                          <label></label>
                        <input type="number" class="widgetVerticalSpaceValueMobile"> px
                      </div>
                  </div>
                  <br>
                  <br>
                  <br>
                </div>
              </div>
              <div class="pbp-widgets wdt-breaker pbp_form" style="display: none;">
                <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
                  <label>Style :</label>
                  <select class="widgetBreakerStyle">
                    <option value="solid">Solid</option>
                    <option value="dotted">Dotted</option>
                    <option value="dashed">Dashed</option>
                    <option value="double">Double</option>
                  </select>
                  <br><br><hr><br>
                  <label> Weight : </label>
                  <input type="number" class="widgetBreakerWeight"  min="1" max="100">
                  <br><br><hr><br>
                  <label>Color :</label>
                  <input type="text" id="widgetBreakerColor" class="color-picker_btn_two widgetBreakerColor" data-alpha='true'  >
                  <br><br><hr><br>
                  <label> Width : </label>
                  <input type="number" class="widgetBreakerWidth" min="1" max="100">
                  <br><br><hr><br>
                  <label>Alignment :</label>
                  <select class="widgetBreakerAlignment">
                    <option value="left">Left</option>
                    <option value="center">Center</option>
                    <option value="right">Right</option>
                  </select>
                  <br><br><hr><br>
                  <label> Space : </label>
                  <input type="number" class="widgetBreakerSpacing">
                  <br><br><hr><br>
                </div>
              </div>
              <div class="pbp-widgets wdt-anchor pbp_form" style="display: none;">
                <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
                  <br>
                  <p style="font-size: 12px; color: #797878;">This id can be used in Link/URL field of a button or text in same page with # to make the link scroll to this element.</p>
                  <br>
                  <label for='wdtanchorid'>Anchor ID</label>
                  <input type="text" class="wdtanchorid" id="wdtanchorid">
                  <br>
                </div>
              </div>
              <div class="pbp-widgets wdt-text pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-text.php'); ?>
              </div>
              <div class="pbp-widgets wdt-embededVideo pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-embededVideo.php'); ?>
              </div>
              <div class="pbp-widgets wdt-testimonialSlider pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-testimonialCarousel.php'); ?>
              </div>
              <div class="pbp-widgets wdt-pluginopsOptins pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-pluginopsoptins.php'); ?>
              </div>
              <div class="pbp-widgets wdt-navMenu pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-navigation.php'); ?>
              </div>
              <div class="pbp-widgets wdt-gallery pbp_form" style="display: none;">
                <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widget-imageGallery.php'); ?>
              </div>
              <div class="pbp-widgets wdt-shareThis pbp_form" style="display: none;">
                <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
                  <br>
                  <br>
                  <label for='wdtstId'>Property ID</label>
                  <input type="text" class="wdtstId" id="wdtstId" data-optname="wdtstId" >
                  <br><br><br><hr><br>
                  <label for='wdtstbt'>Buttons Type</label>
                  <select class="wdtstbt" id="wdtstbt" data-optname="wdtstbt">
                    <option value="">Select</option>
                    <option value="SBI">Share Buttons (Inline)</option>
                    <option value="FBI">Follow Buttons (Inline)</option>
                    <option value="RBI">Reaction Buttons (Inline)</option>
                  </select>
                  <br><br><br><hr><br>
                  <a href="https://www.sharethis.com/support/faq/how-do-i-find-my-property-id/?ref=pluginops-page-builder" target="_blank" style="font-size: 14px;"> How to find 
                  Sharethis propery ID.</a>
                </div>
              </div>
            </div>
          </div>
          <div id="tabAdvancedWidgetOptions" class="pluginops-tab2" style="min-height:400px;">
            <div class="wdt-right" style="width: 100% !important;">
              <form id="widgetAdvOps">
              <div class="PB_accordion">
                <h4>Widget Background</h4>
                <div>
                  <div class="wdt-fields pbp_form" style="margin:10px 0 0 5px; width: 100%;">
                    <h4>Background</h4>
                    <div style="width:350px">
                        <div class="pluginops-tabs2">
                            <ul class="pluginops-tab2-links tabEditFields">
                                <li class="active"><a href="#defaultWidgBgOptions" class="pluginops-tab2_link">Default</a></li>
                                <li><a href="#hoverWidgBgOptions" class="pluginops-tab2_link">Hover</a></li>
                            </ul>
                            <div class="pluginops-tab2-content" style="box-shadow: none;">
                                <div id="defaultWidgBgOptions" class="pluginops-tab2 active">
                                    <br>
                                    <br>
                                    <div id="pluginops_input_tabs" class="popbinputTabsWrapper popbInputNormalwidg">
                                        <p style="display: inline;"> Background Type </p>
                                        <div class="iputTabNav">
                                            <div class="popbNavItem" data-inptabID='content_popb_tab_1' title="Simple">
                                                <label for="inputIDWidg1"> <i class="fa fa-paint-brush"></i></label>
                                                <input type="radio" name="widgBackgroundType" id="inputIDWidg1" value='solid' class="widgBackgroundType widgBackgroundTypeSolid widgOptionsFields tabbedInputRadio" data-optname="widgBackgroundType" > </div>
                                            <div class="popbNavItem" data-inptabID='content_popb_tab_2' title="Gradient">
                                                <label for="inputIDWidg2 " class="GradientIcon"> <i class="fa fa-square"></i></label>
                                                <input type="radio" name="widgBackgroundType" id="inputIDWidg2" class="widgBackgroundType widgBackgroundTypeGradient widgOptionsFields tabbedInputRadio" value="gradient" data-optname="widgBackgroundType" > </div>
                                        </div>
                                        <div class="popb_input_tabContent">
                                            <div class="content_popb_tab_1 popb_tab_content popb_widg_fields_container widgNSrCon">
                                              <br>
                                              <br>
                                              <br>
                                              <label>Color :</label>
                                              <input type="text" name="widgetBgColor" class="color-picker_btn_two widgetBgColor" data-alpha='true' id="widgetBgColor" data-optname="widgetBgColor" >
                                              <br>
                                              <br>
                                              <label>Image :</label>
                                              <input id="image_location1" type="text" class=" widgBgImg upload_image_button2994" name='lpp_add_img_1' value='' placeholder='Insert Image URL here' data-optname="widgBgImg" >
                                              <br>
                                              <br>
                                              <label></label>
                                              <input id="image_location1" type="button" class="upload_bg pb_upload_btn" data-id="2994" value="Upload" style="" />
                                              <br>
                                            </div>
                                            <div class="content_popb_tab_2 popb_tab_content popb_widg_fields_container widgNGrCon ">
                                              <br>
                                              <br>
                                              <br>
                                              <label>First Color </label>
                                              <input type="text" name="widgGradientColorFirst" class="color-picker_btn_two widgGradientColorFirst" data-alpha='true' id="widgGradientColorFirst" value='#fff' data-optname="widgGradient.widgGradientColorFirst" >
                                              <p>Location</p>
                                              <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='widgGradientLocationFirst'></div>
                                              <input type="number" class="widgGradientLocationFirst" data-optname="widgGradient.widgGradientLocationFirst" >
                                              <br>
                                              <br>
                                              <hr>
                                              <br>
                                              <br>
                                              <label>Second Color </label>
                                              <input type="text" name="widgGradientColorSecond" class="color-picker_btn_two widgGradientColorSecond" data-alpha='true' id="widgGradientColorSecond" value='#fff' data-optname="widgGradient.widgGradientColorSecond" >
                                              <p>Location</p>
                                              <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='widgGradientLocationSecond'></div>
                                              <input type="number" class="widgGradientLocationSecond" data-optname="widgGradient.widgGradientLocationSecond" >
                                              <br>
                                              <br>
                                              <hr>
                                              <br>
                                              <br>
                                              <label>Type </label>
                                              <select class="widgGradientType" data-optname="widgGradient.widgGradientType" >
                                                <option value="">Select</option>
                                                <option value="linear">Linear</option>
                                                <option value="radial">Radial</option>
                                              </select>
                                              <br>
                                              <br>
                                              <div class="radialInputWidg" style="display: none;">
                                                    <br>
                                                    <label>Position </label>
                                                    <select class="widgGradientPosition" data-optname="widgGradient.widgGradientPosition">
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
                                                    <br>
                                                    <br>
                                              </div>
                                              <div class="linearInputWidg" style="display: none;">
                                                    <p>Angle</p>
                                                    <div class="PoPbrangeSliderAngle PoPbnumberSlider" data-targetRangeInput='widgGradientAngle'></div>
                                                    <input type="number" class="widgGradientAngle" data-optname="widgGradient.widgGradientAngle" > </div>
                                              <br>
                                              <br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="hoverWidgBgOptions" class="pluginops-tab2">
                                    <br>
                                    <br>
                                    <div id="pluginops_input_tabs" class="popbinputTabsWrapper popbInputHoverwidg">
                                        <p style="display: inline;"> Background Type </p>
                                        <div class="iputTabNav">
                                            <div class="popbNavItem" data-inptabID='content_popb_tab_1' title="Simple">
                                                <label for="inputIDHover1"> <i class="fa fa-paint-brush"></i></label>
                                                <input type="radio" name="widgBackgroundTypeHover" id="inputIDHover1" class="widgBackgroundTypeHover widgBackgroundTypeSolidHover widgOptionsFields tabbedInputRadio" value='solid' data-optname="widgHoverOptions.widgBackgroundTypeHover" > </div>
                                            <div class="popbNavItem" data-inptabID='content_popb_tab_2' title="Gradient">
                                                <label for="inputIDHover2 " class="GradientIcon"> <i class="fa fa-square"></i></label>
                                                <input type="radio" name="widgBackgroundTypeHover" id="inputIDHover2" class="widgBackgroundTypeHover widgBackgroundTypeGradientHover  widgOptionsFields tabbedInputRadio" value="gradient" data-optname="widgHoverOptions.widgBackgroundTypeHover" > </div>
                                        </div>
                                        <div class="popb_input_tabContent">
                                            <div class="content_popb_tab_1 popb_tab_content popb_widg_fields_container widgHSrCon">
                                                <br>
                                                <br>
                                                <label>Color :</label>
                                                <input type="text" name="widgBgColorHover" class="color-picker_btn_two widgBgColorHover" data-alpha='true' id="widgBgColorHover" value='#fff' data-optname="widgHoverOptions.widgBgColorHover" >
                                                <br>
                                            </div>
                                            <div class="content_popb_tab_2 popb_tab_content popb_widg_fields_container widgHGrCon">
                                                <br>
                                                <br>
                                                <br>
                                                <label>First Color </label>
                                                <input type="text" name="widgGradientColorFirstHover" class="color-picker_btn_two widgGradientColorFirstHover" data-alpha='true' id="widgGradientColorFirstHover" value='#fff' data-optname="widgHoverOptions.widgGradientHover.widgGradientColorFirstHover" >
                                                <p>Location</p>
                                                <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='widgGradientLocationFirstHover'></div>
                                                <input type="number" class="widgGradientLocationFirstHover" data-optname="widgHoverOptions.widgGradientHover.widgGradientLocationFirstHover" >
                                                <br>
                                                <br>
                                                <hr>
                                                <br>
                                                <br>
                                                <label>Second Color </label>
                                                <input type="text" name="widgGradientColorSecondHover" class="color-picker_btn_two widgGradientColorSecondHover" data-alpha='true' id="widgGradientColorSecondHover" value='#fff' data-optname="widgHoverOptions.widgGradientHover.widgGradientColorSecondHover" >
                                                <p>Location</p>
                                                <div class="PoPbrangeSlider PoPbnumberSlider" data-targetRangeInput='widgGradientLocationSecondHover'></div>
                                                <input type="number" class="widgGradientLocationSecondHover" data-optname="widgHoverOptions.widgGradientHover.widgGradientLocationSecondHover" >
                                                <br>
                                                <br>
                                                <hr>
                                                <br>
                                                <br>
                                                <label>Type </label>
                                                <select class="widgGradientTypeHover" data-optname="widgHoverOptions.widgGradientHover.widgGradientTypeHover" >
                                                    <option value="">Select</option>
                                                    <option value="linear">Linear</option>
                                                    <option value="radial">Radial</option>
                                                </select>
                                                <br>
                                                <br>
                                                <div class="radialInputWidgHover" style="display: none;">
                                                    <br>
                                                    <label>Position </label>
                                                    <select class="widgGradientPositionHover" data-optname="widgHoverOptions.widgGradientHover.widgGradientPositionHover" >
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
                                                    <br>
                                                    <br>
                                                </div>
                                                <div class="linearInputWidgHover" style="display: none;">
                                                    <p>Angle</p>
                                                    <div class="PoPbrangeSliderAngle PoPbnumberSlider" data-targetRangeInput='widgGradientAngleHover'></div>
                                                    <input type="number" class="widgGradientAngleHover" data-optname="widgHoverOptions.widgGradientHover.widgGradientAngleHover" > </div>
                                                <br>
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <br>
                                    <label>Hover Animation :</label>
                                    <select class="widgetHoverAnimation" style="width: 70px" data-optname="widgHoverOptions.widgetHoverAnimation" >
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
                                      </optgroup>
                                      <optgroup label="Flippers">
                                        <option value="flip">flip</option>
                                        <option value="flipInX">flipInX</option>
                                        <option value="flipInY">flipInY</option>
                                      </optgroup>
                                    </select>
                                    <br>
                                    <br>
                                    <hr>
                                    <br>
                                    <p>Transition Duration</p>
                                    <div class="PoPbrangeSliderTransition PoPbnumberSlider" data-targetRangeInput='widgHoverTransitionDuration'></div>
                                    <input type="number" class="widgHoverTransitionDuration" data-optname="widgHoverOptions.widgHoverTransitionDuration" >
                                    <br>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>

                  </div>
                </div>
                <h4>Display Options</h4>
                <div>
                    <div class="wdt-fields pbp_form" style="width: 100%;">
                      <br><br>
                      <label>Widget Animation :</label>
                      <select class="widgetAnimation" style="width: 70px" data-optname="widgetAnimation" >
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
                        </optgroup>

                        <optgroup label="Bouncing Entrances">
                          <option value="bounceIn">bounceIn</option>
                          <option value="bounceInDown">bounceInDown</option>
                          <option value="bounceInLeft">bounceInLeft</option>
                          <option value="bounceInRight">bounceInRight</option>
                          <option value="bounceInUp">bounceInUp</option>
                        </optgroup>

                        <optgroup label="Fading Entrances">
                          <option value="fadeIn">fadeIn</option>
                          <option value="fadeInDown">fadeInDown</option>
                          <option value="fadeInDownBig">fadeInDownBig</option>
                          <option value="fadeInLeft">fadeInLeft</option>
                          <option value="fadeInLeftBig">fadeInLeftBig</option>
                          <option value="fadeInRight">fadeInRight</option>
                          <option value="fadeInRightBig">fadeInRightBig</option>
                          <option value="fadeInUp">fadeInUp</option>
                          <option value="fadeInUpBig">fadeInUpBig</option>
                        </optgroup>

                        <optgroup label="Flippers">
                          <option value="flip">flip</option>
                          <option value="flipInX">flipInX</option>
                          <option value="flipInY">flipInY</option>
                        </optgroup>

                        <optgroup label="Lightspeed">
                          <option value="lightSpeedIn">lightSpeedIn</option>
                        </optgroup>

                        <optgroup label="Rotating Entrances">
                          <option value="rotateIn">rotateIn</option>
                          <option value="rotateInDownLeft">rotateInDownLeft</option>
                          <option value="rotateInDownRight">rotateInDownRight</option>
                          <option value="rotateInUpLeft">rotateInUpLeft</option>
                          <option value="rotateInUpRight">rotateInUpRight</option>
                        </optgroup>

                        <optgroup label="Sliding Entrances">
                          <option value="slideInUp">slideInUp</option>
                          <option value="slideInDown">slideInDown</option>
                          <option value="slideInLeft">slideInLeft</option>
                          <option value="slideInRight">slideInRight</option>
                        </optgroup>
                        
                        <optgroup label="Zoom Entrances">
                          <option value="zoomIn">zoomIn</option>
                          <option value="zoomInDown">zoomInDown</option>
                          <option value="zoomInLeft">zoomInLeft</option>
                          <option value="zoomInRight">zoomInRight</option>
                          <option value="zoomInUp">zoomInUp</option>
                        </optgroup>
                        

                        <optgroup label="Specials">
                          <option value="rollIn">rollIn</option>
                        </optgroup>
                      </select>
                      <div class="widgetAnimateBtn">Animate</div>
                      <br><br><hr><br>
                      <div>
                          <label>Display Inline
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </label>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <select class="widgetIsInline" data-optname="widgetIsInline" >
                              <option value="">Select</option>
                              <option value="true">Yes</option>
                              <option value="false">No</option>
                            </select>
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <select class="widgetIsInlineTablet" data-optname="widgetIsInlineTablet" >
                              <option value="">Select</option>
                              <option value="true">Yes</option>
                              <option value="false">No</option>
                            </select>
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <select class="widgetIsInlineMobile" data-optname="widgetIsInlineMobile" >
                              <option value="">Select</option>
                              <option value="true">Yes</option>
                              <option value="false">No</option>
                            </select>
                          </div>
                      </div>
                      <br><br><hr><br>
                      <label>Custom Widget Class : </label>
                      <input type="text" class="widgetCustomClass" data-optname="widgetCustomClass" >
                      <div>
                        <br><br><hr><br>
                        <h4>Hide Widget  
                          <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>
                          <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                          <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </h4>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                          <label>Desktop </label>
                          <select class=" widgHideOnDesktop" data-optname="widgHideOnDesktop" >
                            <option value="">Select</option>
                            <option value="show">Show</option>
                            <option value="hide">Hide</option>
                          </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                          <label>Tablet </label>
                          <select class=" widgHideOnTablet" data-optname="widgHideOnTablet" >
                            <option value="">Select</option>
                            <option value="show">Show</option>
                            <option value="hide">Hide</option>
                          </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                          <label>Mobile </label>
                          <select class=" widgHideOnMobile" data-optname="widgHideOnMobile" >
                            <option value="">Select</option>
                            <option value="show">Show</option>
                            <option value="hide">Hide</option>
                          </select>
                        </div>
                      </div>
                    </div>
                </div>
                <h4>Margins & Paddings</h4>
                <div>
                    <div class="wdt-fields pbp_form" style="width: 100%;">
                      
                      <div>
                        <p>Widget Margin 
                          <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                          <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                          <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </p>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                          <input type="number" class="padding_inline_inp linkedField widgetMtop" placeholder="Top" data-optname="widgetMtop" value="0">
                      
                          <input type="number" class="padding_inline_inp linkedField widgetMbottom" placeholder="Bottom" data-optname="widgetMbottom" value="0">
                          
                          <input type="number" class="padding_inline_inp linkedField widgetMleft" placeholder="Left" data-optname="widgetMleft" value="0">
                          
                          <input type="number" class="padding_inline_inp linkedField widgetMright" placeholder="Right" data-optname="widgetMright" value="0">

                          <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                          <input type="number" name="widgetMTopTablet" class="padding_inline_inp linkedField  widgetMTopTablet " id="widgetMTopTablet"  placeholder="Top" data-optname="widgetMarginTablet.rMTT" value="0">
                          
                          <input type="number" name="widgetMBottomTablet" class="padding_inline_inp linkedField  widgetMBottomTablet " id="widgetMBottomTablet"  placeholder="Bottom" data-optname="widgetMarginTablet.rMBT" value="0">
                          
                          <input type="number" name="widgetMLeftTablet" class="padding_inline_inp linkedField  widgetMLeftTablet " id="widgetMLeftTablet"  placeholder="Left" data-optname="widgetMarginTablet.rMLT" value="0">
                          
                          <input type="number" name="widgetMRightTablet" class="padding_inline_inp linkedField  widgetMRightTablet " id="widgetMRightTablet"  placeholder="Right" data-optname="widgetMarginTablet.rMRT" value="0">

                          <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                          <input type="number" name="widgetMTopMobile" class="padding_inline_inp linkedField  widgetMTopMobile " id="widgetMTopMobile"  placeholder="Top" data-optname="widgetMarginMobile.rMTM" value="0">
                          
                          <input type="number" name="widgetMBottomMobile" class="padding_inline_inp linkedField  widgetMBottomMobile " id="widgetMBottomMobile"  placeholder="Bottom" data-optname="widgetMarginMobile.rMBM" value="0">
                          
                          <input type="number" name="widgetMLeftMobile" class="padding_inline_inp linkedField  widgetMLeftMobile " id="widgetMLeftMobile"  placeholder="Left" data-optname="widgetMarginMobile.rMLM" value="0">
                          
                          <input type="number" name="widgetMRightMobile" class="padding_inline_inp linkedField  widgetMRightMobile " id="widgetMRightMobile"  placeholder="Right" data-optname="widgetMarginMobile.rMRM" value="0">

                          <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                        </div>
                      </div>
                      <br>
                      <br>
                      <span class="ulp-note">The unit is percentage so set values accordingly.</span>
                      <br>
                      <br>  
                      <br>
                      <hr>
                      <br>
                      <div>
                        <p>Widget Padding
                          <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>
                          <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                          <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </p>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                          <input type="number" min="0" class="padding_inline_inp linkedField widgetPtop" placeholder="Top" data-optname="widgetPtop" value="0">
                      
                          <input type="number" min="0" class="padding_inline_inp linkedField widgetPbottom" placeholder="Bottom" data-optname="widgetPbottom" value="0">
                          
                          <input type="number" min="0" class="padding_inline_inp linkedField widgetPleft" placeholder="Left" data-optname="widgetPleft" value="0">
                          
                          <input type="number" min="0" class="padding_inline_inp linkedField widgetPright" placeholder="Right" data-optname="widgetPright" value="0">

                          <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                          <input type="number" name="widgetPTopTablet" min="0" class="padding_inline_inp linkedField  widgetPTopTablet " id="widgetPTopTablet"  placeholder="Top" data-optname="widgetPaddingTablet.rPTT" value="1.5">
                          
                          <input type="number" name="widgetPBottomTablet" min="0" class="padding_inline_inp linkedField  widgetPBottomTablet " id="widgetPBottomTablet"  placeholder="Bottom" data-optname="widgetPaddingTablet.rPBT" value="1.5">
                          
                          <input type="number" name="widgetPLeftTablet" min="0" class="padding_inline_inp linkedField  widgetPLeftTablet " id="widgetPLeftTablet"  placeholder="Left" data-optname="widgetPaddingTablet.rPLT" value="1.5">
                          
                          <input type="number" name="widgetPRightTablet" min="0" class="padding_inline_inp linkedField  widgetPRightTablet " id="widgetPRightTablet"  placeholder="Right" data-optname="widgetPaddingTablet.rPRT" value="1.5">

                          <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                          <input type="number" name="widgetPTopMobile" min="0" class="padding_inline_inp linkedField  widgetPTopMobile " id="widgetPTopMobile"  placeholder="Top" data-optname="widgetPaddingMobile.rPTM" value="1.5">
                          
                          <input type="number" name="widgetPBottomMobile" min="0" class="padding_inline_inp linkedField  widgetPBottomMobile " id="widgetPBottomMobile"  placeholder="Bottom" data-optname="widgetPaddingMobile.rPBM" value="1.5">
                          
                          <input type="number" name="widgetPLeftMobile" min="0" class="padding_inline_inp linkedField  widgetPLeftMobile " id="widgetPLeftMobile"  placeholder="Left" data-optname="widgetPaddingMobile.rPLM" value="1.5">
                          
                          <input type="number" name="widgetPRightMobile" min="0" class="padding_inline_inp linkedField  widgetPRightMobile " id="widgetPRightMobile"  placeholder="Right" data-optname="widgetPaddingMobile.rPRM" value="1.5">

                          <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                        </div>
                      </div>
                      <br>
                      <br>
                      <span class="ulp-note">The unit is percentage so set values accordingly.</span>
                      <br>
                      <br>  
                      <br>
                      <hr>
                      <br>

                    </div>
                </div>
                <h4>Border & Box Shadow</h4>
                <div>
                    <div class="wdt-fields pbp_form" style="width: 100%;">
                      <br>
                      <p>Border Width </p>
                      <div>
                        <input type="number" name="wbwt" class="padding_inline_inp linkedField  wbwt " id="wbwt"  placeholder="Top" data-optname="borderWidth.wbwt" >

                        <input type="number" name="wbwr" class="padding_inline_inp linkedField  wbwr " id="wbwr"  placeholder="Right" data-optname="borderWidth.wbwr" >
                            
                        <input type="number" name="wbwb" class="padding_inline_inp linkedField  wbwb " id="wbwb"  placeholder="Bottom" data-optname="borderWidth.wbwb" >
                            
                        <input type="number" name="wbwl" class="padding_inline_inp linkedField  wbwl " id="wbwl"  placeholder="Left" data-optname="borderWidth.wbwl" >
                        
                        <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                      </div>
                      <br>
                      <br><br>
                      <label>Set Border Style: </label>
                      <select class="widgetBorderStyle" data-optname="widgetBorderStyle">
                        <option value="">Select</option>
                        <option value="solid">Solid</option>
                        <option value="dotted">Dotted</option>
                        <option value="dashed">Dashed</option>
                        <option value="double">Double</option>
                      </select>
                      <br>
                      <br><br>
                      <label>Set Border Color : </label>
                      <input type="text" id="widgetBorderColor" class="color-picker_btn_two widgetBorderColor" data-alpha='true' data-optname="widgetBorderColor" >
                      <br>
                      <br>
                      <p>Corner Radius: </p>
                      <div>
                        <input type="number" name="wbrt" class="padding_inline_inp linkedField  wbrt " id="wbrt"  placeholder="Top Left"  data-optname="borderRadius.wbrt" >

                        <input type="number" name="wbrr" class="padding_inline_inp linkedField  wbrr " id="wbrr"  placeholder="Top Right" data-optname="borderRadius.wbrr" >

                        <input type="number" name="wbrb" class="padding_inline_inp linkedField  wbrb " id="wbrb"  placeholder="Bottom Right" data-optname="borderRadius.wbrb" >

                        <input type="number" name="wbrl" class="padding_inline_inp linkedField  wbrl " id="wbrl"  placeholder="Bottom Left" data-optname="borderRadius.wbrl" >
                        
                        <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                      </div>
                      <br>
                      <br>
                      <hr>
                      <p>Box Shadow</p>
                      <br>
                      <label>Shadow Horizontal Position : </label>
                      <input type="number" class="widgetBoxShadowH" data-optname="widgetBoxShadowH" > px
                      <br>
                      <br>
                      <label>Shadow Vertcal Position : </label>
                      <input type="number" class="widgetBoxShadowV" data-optname="widgetBoxShadowV" > px
                      <br>
                      <br>
                      <label>Shadow Distance (Blur) : </label>
                      <input type="number" class="widgetBoxShadowBlur" data-optname="widgetBoxShadowBlur" > px
                      <br>
                      <br>
                      <br>
                      <label>Shadow Color : </label>
                      <input type="text" id="widgetBoxShadowColor" class="color-picker_btn_two widgetBoxShadowColor" data-alpha='true'  data-optname="widgetBoxShadowColor" >
                    </div>
                </div>
                <h4>Width</h4>
                <div>
                  <div class="wdt-fields pbp_form" style="width: 100%;">
                    
                    <br>
                    <div>
                        <label>Width 
                          <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                          <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                          <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </label>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                          <select data-optname="wp.wpw" class='widgetWidthType wpw' >
                            <option value="">Default</option>
                            <option value="full">Full Width (100%) </option>
                            <option value="custom">Custom</option>
                          </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                          <select data-optname="wp.wpwt" class='widgetWidthType wpwt' >
                            <option value="">Default</option>
                            <option value="full">Full Width (100%) </option>
                            <option value="custom">Custom</option>
                          </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                          <select data-optname="wp.wpwm" class='widgetWidthType wpwm' >
                            <option value="">Default</option>
                            <option value="full">Full Width (100%) </option>
                            <option value="custom">Custom</option>
                          </select>
                        </div>
                    </div>

                    <div class="widget_width_ops_container" style="display:none;">
                        <br><br><br><hr><br>
                        <label>Custom Width
                          <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                          <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                          <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                        </label>
                        <div class="responsiveOps responsiveOptionsContainterLarge">
                          <input type="number" class="wpcw padding_inline_inp" placeholder="Widget Width" data-optname="wp.wpcw" value="0">
                          <select style="width:50px;" data-optname="wp.wpcwu" >
                            <option value="px">px</option>
                            <option value="%">%</option>
                            <option value="vh">vw</option>
                          </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                        <input type="number" class="wpcwt padding_inline_inp " placeholder="Widget Width" data-optname="wp.wpcwt" value="0">
                          <select style="width:50px;" data-optname="wp.wpcwut" >
                            <option value="px">px</option>
                            <option value="%">%</option>
                            <option value="vh">vw</option>
                          </select>
                        </div>
                        <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                          <input type="number" class="wpcwm padding_inline_inp " placeholder="Widget Width" data-optname="wp.wpcwm" value="0">
                          <select style="width:50px;" data-optname="wp.wpcwum" >
                            <option value="px">px</option>
                            <option value="%">%</option>
                            <option value="vh">vw</option>
                          </select>
                        </div>
                    </div>

                    <div class="widget_position_ops_container">

                    </div>
                    
                  </div>
                </div>
                <h4>Widget Custom CSS</h4>
                <div>
                  <label>Custom Styling (CSS) :</label>
                  <br>
                  <br>
                  <br>
                  <textarea  style="min-width: 250px; min-height: 250px;" class="widgetStyling" data-optname="widgetStyling" ></textarea>
                  <br>
                </div>
              </div>
              </form>
            </div>
          </div>
          <div id="tabWidgetBlocks" class="pluginops-tab2" style="min-height:400px;">
            <?php include(ULPB_PLUGIN_PATH.'admin/views/UI/widgets/widgetBlocks.php'); ?>
          </div>
          
        </div>
      </div> 
    

    <div class="btn closeWidgetPopup" style="display: none;"><span class="dashicons dashicons-yes closeWidgetPopupIcon"></span></div>
    </div>
    <br>
    <br>
    
  </div>
</div>


<div  class="editWidgetSaveButton" style="display: none;">
  <div id="editWidgetSaveVisible" data-col_id="column1">
    <span  class="dashicons dashicons-arrow-left editSaveVisibleIcon" data-col_id="column1"></span>
  </div>
</div>
