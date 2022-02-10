<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php


$wooCommOpsDropDownDisabled = '
    <option value="addToCart">Add To Cart WooCommerce Product</option>
    <option value="addToCheckout">Add To Checkout WooCommerce Product</option>
';

$wooCommDisabled = '';
if (!function_exists('ulpb_available_pro_widgets')) {
  $wooCommDisabled = 'disabled';

  $wooCommOpsDropDownDisabled = '
    <option value="addToCart" disabled>Add To Cart WooCommerce Product (Pro Only)</option>
    <option value="addToCheckout" disabled>Add To Checkout WooCommerce Product (Pro Only)</option>
  ';
}


$args = array(
  'post_type'      => 'product',
  'posts_per_page' => 100,
);

$loop = new WP_Query( $args );

$wooCommerceProductDropdownList = "<option>Select Products</option>";
while ( $loop->have_posts() ) : $loop->the_post();
  global $product;
  $wooCommerceProductDropdownList = 
    $wooCommerceProductDropdownList.
    "<option value='".get_the_ID()."' > ".get_the_title()." </option>"
  ;
  endwhile;

wp_reset_query();

?>

<br><br>
<div class="pluginops-tabs2" style="width: 107%;">
                <ul class="pluginops-tab2-links">
                  <li class="active"><a href="#btntab1" class="pluginops-tab2_link">Button</a></li>
                  <li><a href="#btntab2" class="pluginops-tab2_link">Icon</a></li>
                </ul>
                <form id="widgetButtonOpsForm">
                  <div class="pluginops-tab2-content" style="box-shadow:none;">
                    <div id="btntab1" class="pluginops-tab2 active">
                      <div id="btn-gen" class="pbp_form" style="margin:0 0 0 0; background: #fff; padding:20px 10px 20px 25px; width: 99%;">
                        <br>
                        <br>
                        <label>Button Text :</label>
                        <input type="text" class="btnText" style="width: 80%;" placeholder="Button Text" data-optname="btnText">
                        <br><br><br><br><hr><br><br>
                        <label>On Click Action :</label>
                        <select class="btnClickAction" data-optname="btnClickAction">
                          <option value="openLink"> Open Link </option>
                          <option value="openPopUp"> Open PopUp </option>
                          <?php echo $wooCommOpsDropDownDisabled; ?>
                        </select>
                        <br><br><br><hr><br><br>
                        <div class="btnLinkOpsContainer">
                          <label>Button Link :</label>
                          <input type="URL" class="btnLink" placeholder="Link URL" data-optname="btnLink" style="width:80%;">
                          <br><br><br><br><hr><br>
                          <label>Open Link :</label>
                          <select class="btnBlankAttr" id="btnBlankAttr" data-optname="btnBlankAttr">
                            <option value="_self">Same Tab</option>
                            <option value="_blank">New Tab</option>
                          </select>
                          <br><br><hr><br>
                          <label>Link Attributes : <br> <span style="font-size: 9px;">(For developers)</span> </label>
                          <input type="text" class="btnCAction" style="width: 80%;" placeholder="Button Actions" data-optname="btnCAction">
                          <br><br><br><br><br><br><hr><br>
                        </div>
                        <div class="openPopUpOpsContainer" style="display: none;">

                          <?php
                            if ( post_type_exists('pluginops_forms') ) { ?>

                            <label>Select PopUp : </label>
                            <select class="btnWidgetPopUpId" id="btnWidgetPopUpId" data-optname="btnWidgetPopUpId">
                                <option value="Select">Choose...</option>
                                <?php 
                                  $ULP_pluginOps_Optins = array(
                                    'post_type' => 'pluginops_forms',
                                    'orderby' => 'date',
                                    'post_status'   => 'any',
                                    'posts_per_page'    => 100,
                                  );
                                  $ULP_pluginOps_Optins_posts = get_posts( $ULP_pluginOps_Optins );
                                  if (!is_array($ULP_pluginOps_Optins_posts)) {
                                    $ULP_pluginOps_Optins_posts = array();
                                  }
                                  foreach ($ULP_pluginOps_Optins_posts as  $thisPost) {
                                    $currentPostId = $thisPost->ID;
                                    $currentPostName = get_the_title($currentPostId);
                                    $currentPostLink = get_permalink($currentPostId);
                                    echo "<option value='$currentPostId' > $currentPostName </option>";
                                  }
                                ?>
                            </select>
                              
                            <?php } else { ?> 
                              <p style="background: #f0f0f0; color:#333; padding: 10px; max-width: 80%; font-size: 17px;">Please install the Optin Builder plugin to access PluginOps Optins and to add them in your Landing Page.<br> You can install it by clicking here : <a target="_blank" href="<?php echo admin_url('plugin-install.php?s=pluginops+&tab=search&type=term'); ?>"> Install Optin Builder</a></p>
                            <?php } ?>
                          <br><br><hr><br>
                        </div>
                        <div class="btnWooCommOpsContainer">
                          <label>Select Product :</label>
                          <select class="btnWooProdID" data-optname="btnWooProdID">
                            <?php echo $wooCommerceProductDropdownList; ?>
                          </select>
                          <br><br><br><br><hr><br>
                        </div>
                        <div>
                          <h4>Height 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </h4>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="btnHeight" data-optname="btnHeight" >px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                              <label></label>
                              <input type="number" class="btnHeightTablet" data-optname="btnHeightTablet" >px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="btnHeightMobile" data-optname="btnHeightMobile" >px
                          </div>
                        </div>
                        <br><br><hr><br>
                        <div>
                          <h4>Width 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </h4>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="btnWidthPercent" style="width:70px;" data-optname="btnWidthPercent">
                            <select style="width:70px;" class="btnWidthUnit" data-optname="btnWidthUnit">
                              <option value='%'>Percent</option>
                              <option value="px">Pixel</option>
                            </select>
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                              <label></label>
                              <input type="number" class="btnWidthPercentTablet" style="width:70px;" data-optname="btnWidthPercentTablet">
                              <select style="width:70px;" class="btnWidthUnitTablet" data-optname="btnWidthUnitTablet">
                                <option value='%'>Percent</option>
                                <option value="px">Pixel</option>
                              </select>
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="btnWidthPercentMobile" style="width:70px;" data-optname="btnWidthPercentMobile">
                            <select style="width:70px;" class="btnWidthUnitMobile" data-optname="btnWidthUnitMobile">
                              <option value='%'>Percent</option>
                              <option value="px">Pixel</option>
                            </select>
                          </div>
                        </div>
                        <br><br><hr><br>
                        <label>Background Color :</label>
                        <input type="text" class="color-picker_btn_two btnBgColor" id="btnBgColor" data-alpha='true' data-optname="btnBgColor">
                        <br><br><hr><br>
                        <label>Hover Background Color :</label>
                        <input type="text" class="color-picker_btn_two btnHoverBgColor" id="btnHoverBgColor" data-alpha='true' data-optname="btnHoverBgColor">
                        <br><br><hr><br>
                        <label>Button Text Color :</label>
                        <input type="text" class="color-picker_btn_two btnColor" id="btnColor" data-optname="btnTextColor">
                        <br><br><hr><br>
                        <label>Hover Text Color :</label>
                        <input type="text" class="color-picker_btn_two btnHoverTextColor" id="btnHoverTextColor" data-alpha='true' data-optname="btnHoverTextColor">
                        <br><br><hr><br>
                        <div>
                          <h4>Font size 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </h4>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <input type="number" class="btnFontSize" data-optname="btnFontSize">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                              <label></label>
                              <input type="number" class="btnFontSizeTablet" data-optname="btnFontSizeTablet">px
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <input type="number" class="btnFontSizeMobile" data-optname="btnFontSizeMobile">px
                          </div>
                        </div>
                        <br><br><hr><br>
                        <label>Border Width: </label>
                        <input type="number" class="btnBorderWidth" data-optname="btnBorderWidth">
                        <br><br><hr><br>
                        <label>Border Color: </label>
                        <input type="text" class="color-picker_btn_two btnBorderColor" id="btnBorderColor" data-optname="btnBorderColor">
                        <br><br><hr><br>
                        <label>Corner Radius: </label>
                        <input type="number" class="btnBorderRadius" max="100" min="0" data-optname="btnBorderRadius">
                        <br><br><hr><br>
                        <div>
                          <h4>Button Alignment 
                            <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                            <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                            <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                          </h4>
                          <div class="responsiveOps responsiveOptionsContainterLarge">
                            <label></label>
                            <select class="btnButtonAlignment" data-optname="btnButtonAlignment">
                              <option value="default">Default</option>
                              <option value="left">Left</option>
                              <option value="right">Right</option>
                              <option value="center">Center</option>
                            </select>
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                            <label></label>
                            <select class="btnButtonAlignmentTablet" data-optname="btnButtonAlignmentTablet">
                              <option value="default">Default</option>
                              <option value="left">Left</option>
                              <option value="right">Right</option>
                              <option value="center">Center</option>
                            </select>
                          </div>
                          <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                            <label></label>
                            <select class="btnButtonAlignmentMobile" data-optname="btnButtonAlignmentMobile">
                              <option value="default">Default</option>
                              <option value="left">Left</option>
                              <option value="right">Right</option>
                              <option value="center">Center</option>
                            </select>
                          </div>
                        </div>
                        <br><br><hr><br>
                        <label>Button Font :</label>
                        <input class="btnButtonFontFamily gFontSelectorulpb" id="btnButtonFontFamily" data-optname="btnButtonFontFamily">
                        
                        <br><br><hr><br><br><br><br><br><br><br><br><br><br><br>
                      </div>
                    </div>
                    <div id="btntab2" class="pluginops-tab2">
                      <div id="btn-gen" class="pbp_form" style="margin:0 0 0 0; background: #fff; padding:20px 10px 20px 25px; width: 99%;">
                        <label>Select Icon </label>
                        <input  data-placement="bottomRight" class="icp pbicp-auto btnSelectedIconpbicp-auto" value="" type="text" data-optname="btnSelectedIcon" />
                        <span class="input-group-addon btnSelectedIcon" style="font-size: 16px;"></span>
                        <br><br><hr><br>
                        <label>Icon Position </label>
                        <select class="btnIconPosition" data-optname="btnIconPosition">
                          <option value="before">Before Text</option>
                          <option value="after">After Text</option>
                        </select>
                        <br><br><hr><br>
                        <label>Icon Gap </label>
                        <input type="number" class="btnIconGap" data-optname="btnIconGap">px
                        <br><br><hr><br>
                        <label>Icon Hover Animation </label>
                        <select class="btnIconAnimation" data-optname="btnIconAnimation">
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
                      </div>
                    </div>
                  </div>
                </form>
</div>