<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#tabs_widget_cf1" class="pluginops-tab2_link">Items</a></li>
    <li><a href="#tabs_widget_cf2" class="pluginops-tab2_link">Style</a></li>
    <li><a href="#tabs_widget_cf3" class="pluginops-tab2_link">Icon</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="tabs_widget_cf1" class="pluginops-tab2 active" style="min-width: 380px;">
          <div class="btn btn-blue" id="addNewtabItem" > <span class="dashicons dashicons-plus-alt"></span> Add Item </div>
          <br>
          <br>
          <ul class="sortableAccordionWidget    tabItemsContainer"></ul>
	</div>
	<div id="tabs_widget_cf2" class="pluginops-tab2" style="background: #fff; padding:10px 0; width: 99%;">

        <div class="PB_accordion" style="margin-left: 5px; width:400px;">

            <h4> Title </h4>
            <div>
                
                <label> Color</label>
                <input type="text" class="color-picker_btn_two acctc" id="acctc" data-optname="tabTitle.acctc">
                <br><br><hr><br>

                <label>Active Color</label>
                <input type="text" class="color-picker_btn_two acctac" id="acctac" data-optname="tabTitle.acctac">
                <br><br><hr><br>

                <label> Background Color </label>
                <input type="text" class="color-picker_btn_two acctbg" id="acctbg" data-optname="tabTitle.acctbg">
                <br><br><hr><br>

                <label>Active Background Color </label>
                <input type="text" class="color-picker_btn_two acctabg" id="acctabg" data-optname="tabTitle.acctabg">
                <br><br><hr><br>


                <label>Horizontal Gap </label>
                <input type="number" data-optname="tabTitle.hgap" style="width:60px;" >
                <br><br><hr><br>

                <label>Vertical Gap </label>
                <input type="number" data-optname="tabTitle.vgap" style="width:60px;" >
                <br><br><hr><br>

                <p>Border Width </p>
                <div>
                    <input type="number" name="borwt" class="padding_inline_inp linkedField  borwt " id="borwt"  placeholder="Top" data-optname="tabTitle.borwt" >

                    <input type="number" name="borwr" class="padding_inline_inp linkedField  borwr " id="borwr"  placeholder="Right" data-optname="tabTitle.borwr" >
                            
                    <input type="number" name="borwb" class="padding_inline_inp linkedField  borwb " id="borwb"  placeholder="Bottom" data-optname="tabTitle.borwb" >
                            
                    <input type="number" name="borwl" class="padding_inline_inp linkedField  borwl " id="borwl"  placeholder="Left" data-optname="tabTitle.borwl" >
                        
                    <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                </div>
                <br><br><hr><br>

                <div>
                    <label>Font Size 
                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                    </label>
                    <div class="responsiveOps responsiveOptionsContainterLarge">
                        <input type="number" style="width:60px;" class="fsize" data-optname="tabTitle.typography.fsize">
                        <select class="fsizeu" style="width:50px;" data-optname="tabTitle.typography.fsizeu">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                        <input type="number" style="width:60px;" class="fsizeT" data-optname="tabTitle.typography.fsizeT">
                        <select class="fsizeuT" style="width:50px;" data-optname="tabTitle.typography.fsizeuT">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                        <input type="number" style="width:60px;" class="fsizeM" data-optname="tabTitle.typography.fsizeM">
                        <select class="fsizeuM" style="width:50px;" data-optname="tabTitle.typography.fsizeuM">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                </div>
                <br><br><hr><br>

                <div>
                    <label>Line Height (em)
                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                    </label>
                    <div class="responsiveOps responsiveOptionsContainterLarge">
                      <input type="number" class="flinh" data-optname="tabTitle.typography.flinh" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <input type="number" class="flinhT" data-optname="tabTitle.typography.flinhT" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <input type="number" class="flinhM" data-optname="tabTitle.typography.flinhM" style="width:60px;" >
                    </div>
                </div>
                <br><br><hr><br>

                <div>
                    <label>Text Spacing
                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                    </label>
                    <div class="responsiveOps responsiveOptionsContainterLarge">
                      <input type="number" class="fletsp" data-optname="tabTitle.typography.fletsp" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <input type="number" class="fletspT" data-optname="tabTitle.typography.fletspT" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <input type="number" class="fletspM" data-optname="tabTitle.typography.fletspM" style="width:60px;" >
                    </div>
                </div>
                <br><br><hr><br>

                <label>Font Weight</label>
                <select class="fwei" data-optname="tabTitle.typography.fwei" >
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
                <select class="ftrans" data-optname="tabTitle.typography.ftrans" >
                    <option value="none">Default</option>
                    <option value="uppercase">Uppercase</option>
                    <option value="lowercase">Lowercase</option>
                    <option value="capitalize">Capitalize</option>
                </select>
                <br><br><br><hr><br>
                <label>Font family </label>
                <input class="ffam gFontSelectorulpb" data-optname="tabTitle.typography.ffam">
                <br><br><br><hr><br><br><br>

            </div>

            <h4> Content </h4>
            <div>
                
                <label> Color</label>
                <input type="text" class="color-picker_btn_two acccc" id="acccc" data-optname="tabContent.acccc">
                <br><br><hr><br>

                <label> Background Color </label>
                <input type="text" class="color-picker_btn_two acccbg" id="acccbg" data-optname="tabContent.acccbg">
                <br><br><hr><br>

                <label>Horizontal Gap </label>
                <input type="number" data-optname="tabContent.hgap" style="width:60px;" >
                <br><br><hr><br>

                <label>Vertical Gap </label>
                <input type="number" data-optname="tabContent.vgap" style="width:60px;" >
                <br><br><hr><br>

                <p>Border Width </p>
                <div>
                    <input type="number" name="borwt" class="padding_inline_inp linkedField  borwt " id="borwt"  placeholder="Top" data-optname="tabContent.borwt" >

                    <input type="number" name="borwr" class="padding_inline_inp linkedField  borwr " id="borwr"  placeholder="Right" data-optname="tabContent.borwr" >
                            
                    <input type="number" name="borwb" class="padding_inline_inp linkedField  borwb " id="borwb"  placeholder="Bottom" data-optname="tabContent.borwb" >
                            
                    <input type="number" name="borwl" class="padding_inline_inp linkedField  borwl " id="borwl"  placeholder="Left" data-optname="tabContent.borwl" >
                        
                    <span class="linkfieldBtn linkBtn" > <i class="fa fa-link"></i> </span>
                </div>
                <br><br><hr><br>

                <div>
                    <label>Font Size 
                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                    </label>
                    <div class="responsiveOps responsiveOptionsContainterLarge">
                        <input type="number" style="width:60px;" class="fsize" data-optname="tabContent.typography.fsize">
                        <select class="fsizeu" style="width:50px;" data-optname="tabContent.typography.fsizeu">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                        <input type="number" style="width:60px;" class="fsizeT" data-optname="tabContent.typography.fsizeT">
                        <select class="fsizeuT" style="width:50px;" data-optname="tabContent.typography.fsizeuT">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                        <input type="number" style="width:60px;" class="fsizeM" data-optname="tabContent.typography.fsizeM">
                        <select class="fsizeuM" style="width:50px;" data-optname="tabContent.typography.fsizeuM">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                </div>
                <br><br><hr><br>

                <div>
                    <label>Line Height (em)
                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                    </label>
                    <div class="responsiveOps responsiveOptionsContainterLarge">
                      <input type="number" class="flinh" data-optname="tabContent.typography.flinh" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <input type="number" class="flinhT" data-optname="tabContent.typography.flinhT" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <input type="number" class="flinhM" data-optname="tabContent.typography.flinhM" style="width:60px;" >
                    </div>
                </div>
                <br><br><hr><br>

                <div>
                    <label>Text Spacing
                      <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                      <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                      <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
                    </label>
                    <div class="responsiveOps responsiveOptionsContainterLarge">
                      <input type="number" class="fletsp" data-optname="tabContent.typography.fletsp" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <input type="number" class="fletspT" data-optname="tabContent.typography.fletspT" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <input type="number" class="fletspM" data-optname="tabContent.typography.fletspM" style="width:60px;" >
                    </div>
                </div>
                <br><br><hr><br>

                <label>Font Weight</label>
                <select class="fwei" data-optname="tabContent.typography.fwei" >
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
                <select class="ftrans" data-optname="tabContent.typography.ftrans" >
                    <option value="none">Default</option>
                    <option value="uppercase">Uppercase</option>
                    <option value="lowercase">Lowercase</option>
                    <option value="capitalize">Capitalize</option>
                </select>
                <br><br><br><hr><br>
                <label>Font family </label>
                <input class="ffam gFontSelectorulpb" data-optname="tabContent.typography.ffam">
                <br><br><br><hr><br><br><br>

            </div>

            <h4> Container </h4>
            <div>
                
                <label>Set Border Style </label>
                <select class="tabIcon.accocbort" data-optname="tabSettings.accocbort">
                    <option value="solid">Solid</option>
                    <option value="dotted">Dotted</option>
                    <option value="dashed">Dashed</option>
                    <option value="double">Double</option>
                </select>
                <br><br><br><hr><br>

                <label>Border Color </label>
                <input type="text" id="accocborc" class="color-picker_btn_two accocborc" data-alpha='true' data-optname="tabSettings.accocborc">
                <br><br><br><hr><br>

                <label>Border Width </label>
                <input type="number" id="accocborw" class="accocborw" data-optname="tabSettings.accocborw"> px
                <br><br><br><hr><br>

            </div>


        </div>
	</div>
    <div id="tabs_widget_cf3" class="pluginops-tab2" style="background: #fff; padding:20px 10px; width: 99%;">

        <label>Position</label>
        <select data-optname="tabIcon.acciPos">
            <option value="before">Before Content</option>
            <option value="after">After Content</option>
        </select>
        <br><br><hr><br>

        <label>Horizontal Gap </label>
        <input type="number" data-optname="tabIcon.acciGap" style="width:60px;" >

        <br><br><hr><br>
    </div>
</div>
</div>

<style type="text/css">
    #tabs_widget_cf2 .font-select {
        width: 170px;
    }
</style>

