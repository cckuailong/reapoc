<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="width: 99%; min-width: 400px;">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#iconList_cf1" class="pluginops-tab2_link">List Items</a></li>
    <li><a href="#iconList_cf2" class="pluginops-tab2_link">List Style</a></li>
  </ul>
<form id="iconListWidgetOpsForm">
<div class="pluginops-tab2-content" style="box-shadow:none;">
    <div id="iconList_cf1" class="pluginops-tab2 active">
          <div class="btn btn-blue" id="addNewIconList" > <span class="dashicons dashicons-plus-alt"></span> Add List Item </div>
          <br>
          <br>
          <ul class="sortableAccordionWidget  iconListItemsContainer" id="iconListItemsContainer">
          </ul>
    </div>
    <div id="iconList_cf2" class="pluginops-tab2">
        <div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
        <br>
        <label>Line Height</label>
        <input type="number" class="iconListLineHeight" data-optname='iconListLineHeight'>
        <br><br><hr><br>
        <label>Alignment</label>
        <select class="iconListAlignment" data-optname='iconListAlignment'>
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
        </select>
        <br>
        <br>
        <hr>
        <br>
        <div>
            <label>Icon Size
                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
            </label>
            <div class="responsiveOps responsiveOptionsContainterLarge">
                <input type="number" class="iconListIconSize" data-optname='iconListIconSize' >px
            </div>
            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                <input type="number" class="iconListIconSizeTablet" data-optname='iconListIconSizeTablet' >px
            </div>
            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                <input type="number" class="iconListIconSizeMobile" data-optname='iconListIconSizeMobile' >px
            </div>
        </div>
        <br><br><hr><br>
        <label>Icon Color</label>
        <input type="text" id="iconListIconColor" class="color-picker_btn_two iconListIconColor" data-optname='iconListIconColor' >
        <br>
        <br>
        <hr>
        <br>
        <div>
            <label>Text Size
                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
            </label>
            <div class="responsiveOps responsiveOptionsContainterLarge">
                <input type="number" class="iconListTextSize"  data-optname='iconListTextSize' >px
            </div>
            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                <input type="number" class="iconListTextSizeTablet"  data-optname=iconListTextSizeTablet' >px
            </div>
            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                <input type="number" class="iconListTextSizeMobile"  data-optname='iconListTextSizeMobile' >px
            </div>
        </div>
        <br><br><hr><br> 
        <div>
            <label>Text Indent
                <span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
                <span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
                <span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
            </label>
            <div class="responsiveOps responsiveOptionsContainterLarge">
                <input type="number" class="iconListTextIndent"  data-optname='iconListTextIndent' >px
            </div>
            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
                <input type="number" class="iconListTextIndentTablet"  data-optname='iconListTextIndentTablet' >px
            </div>
            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
                <input type="number" class="iconListTextIndentMobile"  data-optname='iconListTextIndentMobile' >px
            </div>
        </div>
        <br><br><hr><br>   
        <label>Text Color: </label>   
        <input type="text" id="iconListTextColor" class="color-picker_btn_two iconListTextColor"  data-optname='iconListTextColor' >
        <br><br><hr><br> 
        <label>Font Family :</label>
        <input class="iconListTextFontFamily gFontSelectorulpb" id="iconListTextFontFamily" data-optname='iconListTextFontFamily' >
        <br><br><hr><br><br><br><br><br><br><br>
        </div>
    </div>
</div>
</form>
</div>
<script type="text/javascript">
    (function($){
        var slideCountA = 1;
        jQuery('#addNewIconList').on('click',function(){
        var index = $(".iconListItemsContainer li").length;
        jQuery('.iconListItemsContainer').append(
            '<li> '+
                '<h3 class="handleHeader">List Item <span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> <span class="dashicons dashicons-admin-page slideDuplicateButton" style="float: right;" title="Duplicate"></span> </h3>'+
                '<div  class="accordContentHolder"> '+
                    '<label>List Text</label> '+
                    '<input type="text" class="iconListItemText" value="List Item" data-optname="iconListComplete.'+index+'.iconListItemText" > <br> <br> '+
                    '<label>Select Icon:  </label> '+
                    '<input  data-placement="bottomRight" class="icp pbIconListPicker iconListItemIcon" value="fa-archive" type="text" data-optname="iconListComplete.'+index+'.iconListItemIcon" /> <span class="input-group-addon" style="font-size: 16px;"></span> <br> <br> '+
                    '<label>Link : </label> '+
                    '<input type="url" class="iconListItemLink" data-optname="iconListComplete.'+index+'.iconListItemLink" > <br> <br> '+
                    '<label>Open Link in :</label> '+
                    '<select class="iconListItemLinkOpen" data-optname="iconListComplete.'+index+'.iconListItemLinkOpen" > <option value="_blank">New Tab</option> <option value="_self">Same Tab</option> </select> '+
                '</div> '+
            '</li>'
        );

        jQuery( '.iconListItemsContainer' ).accordion( "refresh" );

                        
        jQuery('.pbIconListPicker').iconpicker({ });
        jQuery('.pbIconListPicker').on('iconpickerSelected',function(event){
            $(this).val(event.iconpickerValue);
            $(this).trigger('change');
        });
        
        slideCountA++;
        
        pageBuilderApp.changedOpType = 'specific';
        pageBuilderApp.changedOpName = 'slideListEdit';
            
        var that = jQuery('.closeWidgetPopup').attr('data-CurrWidget');
        jQuery('div[data-saveCurrWidget="'+that+'"]').trigger('click');

        ColcurrentEditableRowID = jQuery('.ColcurrentEditableRowID').val();
        currentEditableColId = jQuery('.currentEditableColId').val();
        jQuery('section[rowid="'+ColcurrentEditableRowID+'"]').children('.ulpb_column_controls'+currentEditableColId).children('#editColumnSaveWidget').trigger('click');

        
    }); // CLICK function ends here.


        $(document).on( 'change','.iconListItemText', function(){
            if ($(this).val() == '') {
                
            } else{
                fieldLabel  = $(this).val().slice(0,30)+'...';
                $(this).parent().siblings('.handleHeader').html(fieldLabel + '<span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> <span class="dashicons dashicons-admin-page slideDuplicateButton" style="float: right;" title="Duplicate"></span> ');
                jQuery('.closeWidgetPopup').trigger('click');
            }
        });


    })(jQuery);
</script>

<style type="text/css">
    .handleHeader{
        margin:0;
        background:#F1F5F9;
        color: #737e89;
        cursor: move !important;
    }
    .slideRemoveButton{
        cursor: pointer;
        padding: 7px;
        border-radius: 100px;
        background: #d2dde9;
        text-align: center;
        margin-top: -5px;
    }
    .iconListItemsContainer div{
        background: #fff;
    }

    .iconpicker-container .iconpicker-popover{
        z-index: 9;
    }
</style>