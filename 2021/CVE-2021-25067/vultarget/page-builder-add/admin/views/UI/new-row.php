<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="lpp_modal new_row_div">
  <div class="lpp_modal_wrapper">
    <div class="edit_options_left">
        <h1 class="banner-h1">Row Options</h1>
        <label>Row Height :</label>
        <input type="text" name="row_height" id="new_row_height" placeholder="Set row height" class="edit_fields" value='200'>
        <br>
        <br>
        <label>Number of Columns :</label>
        <input type="number" name="number_of_columns" id="new_row_number_of_columns" placeholder="Number of columns in row" min="1" max="8"  class="edit_fields" value='1'>
        <br>
        <br>
        <label>Background Image :</label>
        <input id="image_location1" type="text" class=" new_rowBgImg upload_image_button2"  name='lpp_add_img_1' value='' placeholder='Insert Image URL here' />
        <input id="image_location1" type="button" class="upload_bg" data-id="2" value="Upload"  style="margin-left: 75px;" />
        <br>
        <br>
        <br>
        <br>
        <label>Background Color :</label>
        <input type="text" name="rowBgColor" class="color-picker new_rowBgColor" value='transparent'>
        <br>
        <br>
        <input type="hidden" name="rowMargin" class="new_rowMargin" value='0'>
        <br>
        <br>
        <input type="hidden" name="rowPadding" class="new_rowPadding" value='0'>
    </div>
  </div>
</div>