<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
<br>
    <div class="pluginops-tabs2">
        <ul class="pluginops-tab2-links tabEditFields">
            <li class="active" style="width: 150px !important;"><a href="#defaultIconWidgetOps" class="pluginops-tab2_link" style="width: 110px !important;">Default</a></li>
            <li class="" style="width: 150px !important;"><a href="#hoverIconWidgetOps" class="pluginops-tab2_link" style="width: 110px !important;">Hover</a></li>
        </ul>
        <div class="pluginops-tab2-content" style="box-shadow: none;">
			<div id="defaultIconWidgetOps" class="pluginops-tab2 active" style="display: block;">
				<br>
				<label>Select Icon:  </label>
				<input  data-placement="bottomRight" class="icp pbicp-auto" value="fa-archive" type="text" />
				<span class="input-group-addon pbSelectedIcon pbselIconStyles" style="font-size: 16px;"></span>
				<br><br><br><br><hr><br>
				<label>Icon Style :</label>
				<select class="pbIcStyle">
					<option value="none">Default</option>
					<option value="solid">Solid</option>
				</select>
				<br><br><hr><br>
				<label>Size: </label>
				<input type="number" class="pbIconSize">
				<br><br><hr><br>
				<label>Rotate: </label>
				<input type="number" class="pbIconRotation">
				<br><br><hr><br>
				<label>Color :</label>
				<input type="text" class="color-picker_btn_two pbIconColor" id="pbIconColor">
				<br><br><hr><br>
				<div class="iconStyleOps" style="display: none;">
					<label>Background Color </label>
					<input type="text" class="color-picker_btn_two pbIcBgC" id="pbIcBgC">
					<br><br><hr><br>
					<label>Border Color </label>
					<input type="text" class="color-picker_btn_two pbIcBC" id="pbIcBC">
					<br><br><hr><br>
					<label>Border Width </label>
					<input type="number" class="pbIcBW" id="pbIcBW">px
					<br><br><hr><br>
					<label> Rounded Edges </label>
					<input type="number" class="pbIcBR" id="pbIcBR">px
					<br><br><hr><br>
				</div>
				<label>Icon Link :</label>
				<input type="url" class="pbIconLink" id="pbIconLink">
				<br><br><hr><br>
				<label>Shadow Position (Vertical) : </label>
				<input type="number" class="pbIcSVP">
				<br><br><hr><br>
				<label>Shadow Position (Horizontal) : </label>
				<input type="number" class="pbIcSHP">
				<br><br><hr><br>
				<label>Shadow Blur : </label>
				<input type="number" class="pbIcSDB">
				<br><br><hr><br>
				<label>Shadow Color : </label>
				<input type="text" class="color-picker_btn_two pbIcSC" id="pbIcSC">
				<br><br><hr><br>

			</div>
			<div id="hoverIconWidgetOps" class="pluginops-tab2" style="display: none;">
				<label>Hover Color :</label>
				<input type="text" class="color-picker_btn_two pbIcHC" id="pbIcHC">
				<br><br><hr><br>
				<label>Hover Bg Color :</label>
				<input type="text" class="color-picker_btn_two pbIcHBgC" id="pbIcHBgC">
				<br><br><hr><br>
				<label>Icon Hover Animation </label>
                <select class="pbIcHAn">
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
                <br><br><hr><br>
			</div>
        </div>
	</div>


</div>