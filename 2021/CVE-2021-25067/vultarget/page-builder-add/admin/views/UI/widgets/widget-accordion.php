<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#accord_cf1" class="pluginops-tab2_link">Items</a></li>
    <li><a href="#accord_cf2" class="pluginops-tab2_link">Style</a></li>
    <li><a href="#accord_cf3" class="pluginops-tab2_link">Icon</a></li>
    <li><a href="#accord_cf4" class="pluginops-tab2_link">Settings</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="accord_cf1" class="pluginops-tab2 active" style="min-width: 380px;">
          <div class="btn btn-blue" id="addNewAccordionItem" > <span class="dashicons dashicons-plus-alt"></span> Add Item </div>
          <br>
          <br>
          <ul class="sortableAccordionWidget    accordionItemsContainer"></ul>
	</div>
	<div id="accord_cf2" class="pluginops-tab2" style="background: #fff; padding:10px 0; width: 99%;">

        <div class="PB_accordion" style="margin-left: 5px; width:400px;">

            <h4> Title </h4>
            <div>
                
                <label> Color</label>
                <input type="text" class="color-picker_btn_two acctc" id="acctc" data-optname="accordionTitle.acctc">
                <br><br><hr><br>

                <label>Active Color</label>
                <input type="text" class="color-picker_btn_two acctac" id="acctac" data-optname="accordionTitle.acctac">
                <br><br><hr><br>

                <label> Background Color </label>
                <input type="text" class="color-picker_btn_two acctbg" id="acctbg" data-optname="accordionTitle.acctbg">
                <br><br><hr><br>

                <label>Active Background Color </label>
                <input type="text" class="color-picker_btn_two acctabg" id="acctabg" data-optname="accordionTitle.acctabg">
                <br><br><hr><br>


                <label>Horizontal Gap </label>
                <input type="number" data-optname="accordionTitle.hgap" style="width:60px;" >
                <br><br><hr><br>

                <label>Vertical Gap </label>
                <input type="number" data-optname="accordionTitle.vgap" style="width:60px;" >
                <br><br><hr><br>

                <p>Border Width </p>
                <div>
                    <input type="number" name="borwt" class="padding_inline_inp linkedField  borwt " id="borwt"  placeholder="Top" data-optname="accordionTitle.borwt" >

                    <input type="number" name="borwr" class="padding_inline_inp linkedField  borwr " id="borwr"  placeholder="Right" data-optname="accordionTitle.borwr" >
                            
                    <input type="number" name="borwb" class="padding_inline_inp linkedField  borwb " id="borwb"  placeholder="Bottom" data-optname="accordionTitle.borwb" >
                            
                    <input type="number" name="borwl" class="padding_inline_inp linkedField  borwl " id="borwl"  placeholder="Left" data-optname="accordionTitle.borwl" >
                        
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
                        <input type="number" style="width:60px;" class="fsize" data-optname="accordionTitle.typography.fsize">
                        <select class="fsizeu" style="width:50px;" data-optname="accordionTitle.typography.fsizeu">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                        <input type="number" style="width:60px;" class="fsizeT" data-optname="accordionTitle.typography.fsizeT">
                        <select class="fsizeuT" style="width:50px;" data-optname="accordionTitle.typography.fsizeuT">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                        <input type="number" style="width:60px;" class="fsizeM" data-optname="accordionTitle.typography.fsizeM">
                        <select class="fsizeuM" style="width:50px;" data-optname="accordionTitle.typography.fsizeuM">
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
                      <input type="number" class="flinh" data-optname="accordionTitle.typography.flinh" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <input type="number" class="flinhT" data-optname="accordionTitle.typography.flinhT" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <input type="number" class="flinhM" data-optname="accordionTitle.typography.flinhM" style="width:60px;" >
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
                      <input type="number" class="fletsp" data-optname="accordionTitle.typography.fletsp" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <input type="number" class="fletspT" data-optname="accordionTitle.typography.fletspT" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <input type="number" class="fletspM" data-optname="accordionTitle.typography.fletspM" style="width:60px;" >
                    </div>
                </div>
                <br><br><hr><br>

                <label>Font Weight</label>
                <select class="fwei" data-optname="accordionTitle.typography.fwei" >
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
                <select class="ftrans" data-optname="accordionTitle.typography.ftrans" >
                    <option value="none">Default</option>
                    <option value="uppercase">Uppercase</option>
                    <option value="lowercase">Lowercase</option>
                    <option value="capitalize">Capitalize</option>
                </select>
                <br><br><br><hr><br>
                <label>Font family </label>
                <input class="ffam gFontSelectorulpb" data-optname="accordionTitle.typography.ffam">
                <br><br><br><hr><br><br><br>

            </div>

            <h4> Content </h4>
            <div>
                
                <label> Color</label>
                <input type="text" class="color-picker_btn_two acccc" id="acccc" data-optname="accordionContent.acccc">
                <br><br><hr><br>

                <label> Background Color </label>
                <input type="text" class="color-picker_btn_two acccbg" id="acccbg" data-optname="accordionContent.acccbg">
                <br><br><hr><br>

                <label>Horizontal Gap </label>
                <input type="number" data-optname="accordionContent.hgap" style="width:60px;" >
                <br><br><hr><br>

                <label>Vertical Gap </label>
                <input type="number" data-optname="accordionContent.vgap" style="width:60px;" >
                <br><br><hr><br>

                <p>Border Width </p>
                <div>
                    <input type="number" name="borwt" class="padding_inline_inp linkedField  borwt " id="borwt"  placeholder="Top" data-optname="accordionContent.borwt" >

                    <input type="number" name="borwr" class="padding_inline_inp linkedField  borwr " id="borwr"  placeholder="Right" data-optname="accordionContent.borwr" >
                            
                    <input type="number" name="borwb" class="padding_inline_inp linkedField  borwb " id="borwb"  placeholder="Bottom" data-optname="accordionContent.borwb" >
                            
                    <input type="number" name="borwl" class="padding_inline_inp linkedField  borwl " id="borwl"  placeholder="Left" data-optname="accordionContent.borwl" >
                        
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
                        <input type="number" style="width:60px;" class="fsize" data-optname="accordionContent.typography.fsize">
                        <select class="fsizeu" style="width:50px;" data-optname="accordionContent.typography.fsizeu">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                        <input type="number" style="width:60px;" class="fsizeT" data-optname="accordionContent.typography.fsizeT">
                        <select class="fsizeuT" style="width:50px;" data-optname="accordionContent.typography.fsizeuT">
                            <option value="px">px</option>
                            <option value="em">em</option>
                            <option value="rem">rem</option>
                            <option value="vh">vw</option>
                        </select>
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                        <input type="number" style="width:60px;" class="fsizeM" data-optname="accordionContent.typography.fsizeM">
                        <select class="fsizeuM" style="width:50px;" data-optname="accordionContent.typography.fsizeuM">
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
                      <input type="number" class="flinh" data-optname="accordionContent.typography.flinh" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <input type="number" class="flinhT" data-optname="accordionContent.typography.flinhT" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <input type="number" class="flinhM" data-optname="accordionContent.typography.flinhM" style="width:60px;" >
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
                      <input type="number" class="fletsp" data-optname="accordionContent.typography.fletsp" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                      <input type="number" class="fletspT" data-optname="accordionContent.typography.fletspT" style="width:60px;" >
                    </div>
                    <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                      <input type="number" class="fletspM" data-optname="accordionContent.typography.fletspM" style="width:60px;" >
                    </div>
                </div>
                <br><br><hr><br>

                <label>Font Weight</label>
                <select class="fwei" data-optname="accordionContent.typography.fwei" >
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
                <select class="ftrans" data-optname="accordionContent.typography.ftrans" >
                    <option value="none">Default</option>
                    <option value="uppercase">Uppercase</option>
                    <option value="lowercase">Lowercase</option>
                    <option value="capitalize">Capitalize</option>
                </select>
                <br><br><br><hr><br>
                <label>Font family </label>
                <input class="ffam gFontSelectorulpb" data-optname="accordionContent.typography.ffam">
                <br><br><br><hr><br><br><br>

            </div>

            <h4> Container </h4>
            <div>
                
                <label>Set Border Style </label>
                <select class="accordionIcon.accocbort" data-optname="accordionSettings.accocbort">
                    <option value="solid">Solid</option>
                    <option value="dotted">Dotted</option>
                    <option value="dashed">Dashed</option>
                    <option value="double">Double</option>
                </select>
                <br><br><br><hr><br>

                <label>Border Color </label>
                <input type="text" id="accocborc" class="color-picker_btn_two accocborc" data-alpha='true' data-optname="accordionSettings.accocborc">
                <br><br><br><hr><br>

                <label>Border Width </label>
                <input type="number" id="accocborw" class="accocborw" data-optname="accordionSettings.accocborw"> px
                <br><br><br><hr><br>

            </div>


        </div>
	</div>
    <div id="accord_cf3" class="pluginops-tab2" style="background: #fff; padding:20px 10px; width: 99%;">
        <div>
            <label>Icon <span style="font-size: 9px;">(When Closed)</span>  </label>
            <input  data-placement="bottomRight" class=" acciClosed pbicp-auto icp " value="fa-angle-down" type="text" data-optname="accordionIcon.acciClosed" />
            <span class="input-group-addon accordionSelectedIcon pbselIconStyles" style="font-size: 16px;"></span>
            <br><br><br><hr><br>
        </div>
            
        <div>
            <label>Active Icon <span style="font-size: 9px;">(When Open)</span>  </label>
            <input  data-placement="bottomRight" class="acciOpen pbicp-auto icp" value="fa-angle-up" type="text" data-optname="accordionIcon.acciOpen" />
            <span class="input-group-addon accordionSelectedIcon pbselIconStyles" style="font-size: 16px;"></span>
            <br><br><br><hr><br>  
        </div>
        

        <label>Alignment</label>
        <select data-optname="accordionIcon.acciAlign">
            <option value="none">Left</option>
            <option value="right">Right</option>
        </select>
        <br><br><hr><br>

        <label>Color</label>
        <input type="text" class="color-picker_btn_two" id="acciColor" data-optname="accordionIcon.acciColor" >
        <br><br><hr><br>

        <label>Active Color <span style="font-size: 9px;">(When Open)</span> </label>
        <input type="text" class="color-picker_btn_two" id="acciAColor" data-optname="accordionIcon.acciAColor" >
        <br><br><hr><br>

        <label>Horizontal Gap </label>
        <input type="number" data-optname="accordionIcon.acciGap" style="width:60px;" >

        <br><br><br>
    </div>
    <div id="accord_cf4" class="pluginops-tab2" style="background: #fff; padding:20px 10px; width: 99%;">
        <label>Height Style </label>
        <select data-optname="accordionSettings.accoHeight">
            <option value="auto">Auto</option>
            <option value="content">Content</option>
            <option value="fill">Fill</option>
        </select>
        <br><br><hr><br>

        <label>Active </label>
        <select data-optname="accordionSettings.accoActive">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
    </div>
</div>
</div>

<style type="text/css">
    #accord_cf2 .font-select {
        width: 170px;
    }
</style>

