<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pbp_form" style=" background: #fff; padding:20px 10px 20px 25px; width: 99%;">
    <label for="ftr-menu-style-1" style="width: 90%;"> Style 1
        <img src="<?php echo ULPB_PLUGIN_URL.'/images/menu/menu-style-1.png'; ?>" class='menu-select' loading="lazy" > </label>
    <input type="radio" style="width: 15px;" class="ftr-menu-style" id='ftr-menu-style-1' name="ftr-menu-select-style" value='menu-style-1'>
    <br><br><br><br>
    <label for="ftr-menu-style-2" style="width: 90%;"> Style 2 (Sticky)
        <img src="<?php echo ULPB_PLUGIN_URL.'/images/menu/menu-style-2.png'; ?>" class='menu-select' loading="lazy" > </label>
    <input type="radio" style="width: 15px;" class="ftr-menu-style" id='ftr-menu-style-2' name="ftr-menu-select-style" value='menu-style-2'>
    <br><br><br><br>
    <label for="ftr-menu-style-4" style="width: 90%;"> Style 3
        <img src="<?php echo ULPB_PLUGIN_URL.'/images/menu/menu-style-4.png'; ?>" class='menu-select' loading="lazy" > </label>
    <input type="radio" style="width: 15px;" class="ftr-menu-style" id='ftr-menu-style-4' name="ftr-menu-select-style" value='menu-style-4'>
    <br><br><br><br>
    <label for="ftr-menu-style-3" style="width: 99%;"> Style 4 (Without Logo)
        <img src="<?php echo ULPB_PLUGIN_URL.'/images/menu/menu-style-3.png'; ?>" class='menu-select' loading="lazy" > </label>
    <input type="radio" style="width: 15px;" class="ftr-menu-style" id='ftr-menu-style-3' name="ftr-menu-select-style" value='menu-style-3'>
    <br><br><br><br><br>
    <hr><br>
    <label>Select Menu :</label>
    <select name="ftr-menu-select" id="ftr-menu-select">
        <option value="Select">Choose</option>
        <?php $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 
        if (!is_array($menus)) {
            $menus = array();
        }
        foreach($menus as $menu){ echo "<option value='$menu->name'>$menu->name</option>"; } ?> 
    </select>
    <br>
    <br>
    <hr>
    <br>
    <label>Font Color :</label>
    <input type="text" class="color-picker_btn_two ftr-menu-color" id="ftr-menu-color" value='#333333'>
    <br>
    <br>
    <hr>
    <br>
    <label>Hover Color :</label>
    <input type="text" class="color-picker_btn_two pbMenuFontHoverColor" id="pbMenuFontHoverColor" value='#333333'>
    <br>
    <br>
    <hr>
    <br>
    <label>Hover Background Color :</label>
    <input type="text" class="color-picker_btn_two pbMenuFontHoverBgColor" id="pbMenuFontHoverBgColor" value='#333333'>
    <br>
    <br>
    <hr>
    <br>
    <label>Font Size</label>
    <input type="number" class="pbMenuFontSize" value="16">px
    <br>
    <br>
    <hr>
    <br>
    <label>Menu Font Family:</label>
    <input class="pbMenuFontFamily gFontSelectorulpb" id="pbMenuFontFamily">

    <br><br><br><hr><br><br><br><br><br><br><br><br><br><br>
</div>