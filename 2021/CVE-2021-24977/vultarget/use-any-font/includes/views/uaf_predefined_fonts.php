<?php if ( ! defined( 'ABSPATH' ) ) exit;
$predefinedUplaodedFonts = uaf_get_uploaded_predefined_fonts();
?>

    <div class="uaf_search_bar">
        <form class="thickbox" action="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=uaf_predefined_font_interface" method="POST" id="predefind_search_form">
            <input type="search" value="" placeholder="Search Fonts" id="predefined_search_input" name="search" />        
            <input type="submit" value="Search" name="search-submit" class="button-primary" />        
        </form>
    </div>

    <div id="predefined_fonts_list" class="ready" data-page="1" data-totalPage = '1' data-randnumber="<?php echo rand(1111, 99999); ?>">
    </div>

    <div id="predefined_font_load_msg"></div>

<script type="text/javascript">
    function uaf_load_predefined_fonts(page = 1, category = 'all', search=''){
        var predefinedUplaodedFonts = <?php echo json_encode($predefinedUplaodedFonts); ?>;
        var randomNumber = jQuery('#predefined_fonts_list').attr('data-randnumber');
        jQuery('#predefined_font_load_msg').html('Loading...');
        jQuery.get("https://ultimatefont.com/useanyfont/list.php?&rand="+randomNumber+"&page="+page+"&category="+category+"&search="+search, function(response){
            var response = jQuery.parseJSON(response);            
            if (response.total_records > 0){                
                fontDatas    = response.fonts;
                jQuery.each(fontDatas, function(key,font) {
                    uaf_font_list_block(font, predefinedUplaodedFonts);
                });
                jQuery('#predefined_fonts_list').addClass('ready');
                jQuery('#predefined_fonts_list').attr('data-page',response.current_page);
                jQuery('#predefined_fonts_list').attr('data-totalPage',response.total_pages);
                jQuery('#predefined_font_load_msg').html('');
            } else {
                jQuery('#predefined_font_load_msg').html('No Font Found');
            }
        });
    }

    function uaf_font_list_block(font, predefinedUplaodedFonts){
        var fontListHTML    = '<div class="font_holder">';
        fontListHTML        += '<div class="font_meta">';
        fontListHTML        += '<div class="font_name">'+font.font_name+' <em>( '+font.font_sub_family+' )</em> </div>';
        if (predefinedUplaodedFonts.includes(font.id)){
            fontListHTML    += '<div class="add_font_link"><a href="javascript:void(0);" class="button-primary" disabled="disabled">Already Added</a></div>';
        } else {
            fontListHTML    += '<div class="add_font_link"><a onclick="uaf_add_loading_text(this);" href="admin.php?page=use-any-font&tab=font_upload&predefinedfontid='+font.id+'" class="button-primary">Add To Your Site</a></div>';
        }
        fontListHTML        += '</div>';
        fontListHTML        += '<div class="font_demo">';
        fontListHTML        += '<img style="max-width:100%; max-height:30px;" src="https://ultimatefont.com/files/images/'+font.font_demo_image+'" />';
        fontListHTML        += '</div>';
        fontListHTML        += '</div>';
        jQuery('#predefined_fonts_list').append(fontListHTML);
    }

    function uaf_add_loading_text(e){
        jQuery(e).html('Adding...');
    }

    jQuery(document).ready(function(){
        uaf_load_predefined_fonts();
        jQuery('#predefind_search_form').submit(function(e){
            jQuery('#predefined_fonts_list').html('');
            uaf_load_predefined_fonts('1', 'all', jQuery('#predefined_search_input').val());
            jQuery('#predefined_fonts_list').attr('data-page', '1');
            return false;
        });

        jQuery('#TB_ajaxContent').bind('scroll', function() {
            if (jQuery('#predefined_fonts_list').hasClass('ready')){            
                if(jQuery('#TB_ajaxContent').scrollTop() >= jQuery('#predefined_fonts_list').height() - jQuery('#TB_ajaxContent').height() - 50 ){
                    currentPage =    jQuery('#predefined_fonts_list').attr('data-page');
                    totalPage   =    jQuery('#predefined_fonts_list').attr('data-totalPage');
                    if (parseInt(currentPage) < parseInt(totalPage)){
                        newPage     =    parseInt(currentPage) + 1;
                        jQuery('#predefined_fonts_list').removeClass('ready');
                        uaf_load_predefined_fonts(newPage, 'all', jQuery('#predefined_search_input').val());
                    } else {
                        jQuery('#predefined_font_load_msg').html('No more fonts found');
                    }
                }
            }
        });
    });
</script>