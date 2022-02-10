jQuery(document).ready( function($) {

    function media_upload(button_class) {
        var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;
        $('body').on('click', button_class, function(e) {

            var button_id ='#'+$(this).attr('id');
            var self = $(button_id);
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $(button_id);
            var id = button.attr('id').replace('_button', '');
            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment){

                if ( _custom_media  ) {
                     var hb_attr =$('.'+id).attr('lfb_hb');
                   // $('.custom_media_id').val(attachment.id);
                    $('.'+id).val(attachment.url);
                    $('.'+id+'_image').attr('src',attachment.url).css('display','block');
                   if(hb_attr=='header'){
                    $('.leadform-show-form .lead-head').css({'background':'url('+attachment.url+')','display':'block'});
                   lfb_header_padding();
                    }else{
                    $('.leadform-show-form .lead-form-front').css('background-image','url('+attachment.url+')');
                    }
                } else {
                    return _orig_send_attachment.apply( button_id, [props, attachment] );
                }
            }
            wp.media.editor.open(button);
            lfb_save_changes();
                return false;
        });
    }
    media_upload('.lfb_custom_media_button.button');
});


function remove_image(t){
    if(t=='h'){
        jQuery('.lfb_custom_media_header_image').attr('src', '');
        jQuery('#lfb_header_image').val("");
        jQuery('.leadform-show-form .lead-head').css({'background':'none','display':'none'});
        } else if(t=='b'){
        jQuery('.lfb_custom_media_bg_image').attr('src', '');
        jQuery('#lfb_bg_image').val("");
        jQuery('.leadform-show-form .lead-form-front').css('background-image','none');
    
    }
    lfb_save_changes();
}
function lfb_save_changes(){
            jQuery("#saveColor, .saveColor").css({"background":'#000'});
            jQuery("#saveColor, .saveColor").val('Save Changes');
            jQuery( "#saveColor, .saveColor" ).prop( "disabled", false );
}

function lfb_header_padding(){
                     $header_padding = jQuery('#lfb_header_algmnt_tb').val();
                    if($header_padding ==0){
                        jQuery('.lead-head').css({"padding-top":'10%','padding-bottom':'10%'});
                        jQuery('#lfb_header_algmnt_tb').val(10);
                    }
}

