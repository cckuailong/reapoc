<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>


<div id="postbox-container-1" class="postbox-container animated slideOutRight" style="display: none;">
  <div id="side-sortables" class="meta-box-sortables ui-sortable">
  </div>
</div>



<div style="display: none;">
  <div id="sideOptions">
    <br>
    <h2 style="text-align: center;">Advanced Options</h2>
      <div id='extra_options' style="margin-left: 15px;">
          
        <div class="loadThemeWrapperContainer">
          <p id='label'>Load Theme Wrapper : </p>
            <label class="switch" id="loadThemeWrapper">
            <input type="checkbox" class="loadThemeWrapper" name='loadThemeWrapper' value='true' <?php checked( 'true', $loadThemeWrapper); ?>  isChecked="<?php echo($loadThemeWrapper); ?>">
            <div class="slider round"></div>
          </label>
          <br>
          <br>
          <br>
          <p></p>
        </div>

        <div class="advancedOpsContainer">
          <br>
          <div class="setasfrontpageopscontainer">
            <p id='label'>Set as Front Page : </p>
            <label class="switch" id="setFrontPage">
              <input type="checkbox" class="setFrontPage" name='is_front_page' value='true' <?php checked( 'true', $is_front_page); ?>  isChecked="<?php echo($is_front_page); ?>">
              <div class="slider round"></div>
            </label>
            <br>
            <br>
            <br>
            <p></p>
          </div>
          <p id='label'>Load wp_head :  </p>
          <label class="switch" id="loadWpHead">
            <input type="checkbox" class="loadWpHead" name='load_wphead' value='true' <?php checked( 'true', $loadWpHead); ?> isChecked="<?php echo($loadWpHead); ?>" >
            <div class="slider round"></div>
          </label>
          <br>
          <br>
          <p style="font-size:12px; font-style:italic;">This will load your theme header scripts.</p>
          <br>
          <p id='label'> Load wp_footer :  </p>
          <label class="switch" id="loadWpFooter">
            <input type="checkbox" class="loadWpFooter" name='load_wpfooter' value='true'  <?php checked( 'true', $loadWpFooter); ?>  isChecked="<?php echo($loadWpFooter); ?>" >
            <div class="slider round"></div>
          </label>
          <br>
          <br>
          <p style="font-size:12px; font-style:italic;">This will load your theme footer scripts.</p>
          <br><br>
          <div style="padding: 7px 0 8px 11px;background: #fff;border: 1px solid #D2D2D2;border-radius: 3px;width: 20%; min-width:250px;font-weight: bold; font-size:12px;">[pluginops_template template_id='<?php echo $postId; ?>']</div>
        </div>
        
      </div>
  </div>
  
</div>

